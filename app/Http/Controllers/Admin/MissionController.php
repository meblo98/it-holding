<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Mission;
use App\Models\MissionApplication;
use App\Services\MissionMatcher;
use App\Services\ProWallet;
use App\Services\TaxEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MissionController extends Controller
{
    public function index(Request $request)
    {
        $missions = Mission::withCount([
                'applications',
                'applications as team_count' => fn ($q) => $q->whereIn('status', MissionApplication::TEAM_STATUSES),
            ])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.missions.index', compact('missions'));
    }

    public function create()
    {
        return view('admin.missions.form', ['mission' => new Mission(['positions' => 1, 'status' => 'draft']), 'clients' => $this->clients()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['reference'] = Mission::generateReference();
        $data['created_by'] = auth()->id();

        $mission = Mission::create($data);

        return redirect()->route('admin.missions.show', $mission->id)->with('success', "Mission {$mission->reference} créée.");
    }

    public function show(Mission $mission)
    {
        $mission->load(['client', 'applications.user.professionalProfile']);
        $suggestions = app(MissionMatcher::class)->suggest($mission);

        return view('admin.missions.show', compact('mission', 'suggestions'));
    }

    public function edit(Mission $mission)
    {
        return view('admin.missions.form', ['mission' => $mission, 'clients' => $this->clients()]);
    }

    public function update(Request $request, Mission $mission)
    {
        $mission->update($this->validated($request));

        return redirect()->route('admin.missions.show', $mission->id)->with('success', 'Mission mise à jour.');
    }

    /**
     * Fait avancer une candidature dans son parcours (doc §16), avec
     * historique. Le passage à « Payé » applique le contrôle de contrat et
     * le moteur fiscal, puis crédite le portefeuille — une seule fois.
     */
    public function updateApplication(Request $request, MissionApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(MissionApplication::STATUSES)),
            'agreed_amount' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:2000',
            'note' => 'nullable|string|max:500',
        ]);

        if ($application->status === 'paid') {
            return back()->with('error', 'Cette candidature est déjà payée : son statut ne peut plus changer.');
        }

        $newStatus = $validated['status'];
        $agreedAmount = $validated['agreed_amount'] ?? $application->agreed_amount;
        $user = $application->user;

        if ($newStatus === 'paid') {
            if (!$agreedAmount || $agreedAmount <= 0) {
                return back()->with('error', 'Renseignez le montant convenu avant de passer la candidature en « Payé ».');
            }
            if (!$user->hasAcceptedCurrentContract()) {
                return back()->with('error', "Paiement impossible : {$user->name} n'a pas encore accepté le contrat en vigueur pour sa catégorie.");
            }
        }

        DB::transaction(function () use ($application, $validated, $newStatus, $agreedAmount, $user) {
            $history = $application->status_history ?? [];
            if ($newStatus !== $application->status) {
                $history[] = [
                    'from' => $application->status,
                    'to' => $newStatus,
                    'by' => auth()->user()->name,
                    'note' => $validated['note'] ?? null,
                    'at' => now()->toDateTimeString(),
                ];
            }

            $application->fill([
                'status' => $newStatus,
                'agreed_amount' => $agreedAmount,
                'admin_notes' => $validated['admin_notes'] ?? $application->admin_notes,
                'status_history' => $history,
            ]);

            if ($newStatus === 'paid') {
                $beneficiaryType = $user->professionalProfile?->beneficiary_type ?? 'individual';
                $tax = app(TaxEngine::class)->calculate((float) $agreedAmount, 'mission_freelance', $beneficiaryType);

                $application->fill([
                    'tax_rule_id' => $tax['rule']?->id,
                    'withholding_amount' => $tax['withholding_amount'],
                    'net_amount' => $tax['net_amount'],
                    'paid_at' => now(),
                ]);

                app(ProWallet::class)->credit($user, 'mission', $application->id, (float) $agreedAmount,
                    $tax['withholding_amount'], $tax['net_amount'], $tax['rule']?->id,
                    "Mission {$application->mission->reference} — {$application->mission->title}");
            }

            $application->save();

            // Doc §18 : dès qu'un professionnel est retenu, la mission devient un projet.
            $mission = $application->mission;
            if ($application->isTeamMember() && !$mission->project_ref) {
                $mission->update(['project_ref' => Mission::generateProjectRef()]);
            }
        });

        return back()->with('success', 'Candidature mise à jour.');
    }

    public function downloadApplicationDocument(MissionApplication $application)
    {
        abort_unless($application->document_path && Storage::disk('local')->exists($application->document_path), 404);

        return Storage::disk('local')->download($application->document_path, $application->document_name);
    }

    private function clients()
    {
        return Client::orderBy('company_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'company_name']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'city' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string|max:100',
            'required_skills' => 'nullable|string|max:500',
            'equipment' => 'nullable|string|max:2000',
            'start_date' => 'nullable|date',
            'apply_until' => 'nullable|date',
            'positions' => 'required|integer|min:1|max:500',
            'status' => 'required|in:' . implode(',', array_keys(Mission::STATUSES)),
            'client_id' => 'nullable|exists:clients,id',
            'target_types' => 'nullable|array',
            'target_types.*' => 'in:' . implode(',', Mission::ELIGIBLE_PARTNER_TYPES),
        ]);

        $skills = array_values(array_filter(array_map('trim', explode(',', $data['required_skills'] ?? ''))));
        $data['required_skills'] = $skills ?: null;

        // Aucune case ou toutes cochées = mission ouverte aux deux catégories.
        $targets = array_values(array_unique($data['target_types'] ?? []));
        $data['target_types'] = ($targets && count($targets) < count(Mission::ELIGIBLE_PARTNER_TYPES)) ? $targets : null;

        return $data;
    }
}
