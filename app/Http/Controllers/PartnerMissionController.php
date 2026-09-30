<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerMissionController extends Controller
{
    /**
     * Missions publiées et candidatures du professionnel (doc §14-16).
     * Réservé aux catégories freelance et prestataire.
     */
    protected function checkEligible()
    {
        $user = Auth::user();
        abort_unless($user->isPartner(), 403, 'Accès réservé aux partenaires approuvés.');
        abort_unless(in_array($user->partner_type, Mission::ELIGIBLE_PARTNER_TYPES, true), 403, 'Les missions sont réservées aux freelances et prestataires du réseau.');

        return $user;
    }

    public function index()
    {
        $user = $this->checkEligible();

        $openMissions = Mission::where('status', 'open')
            ->targeting($user->partner_type)
            ->where(fn ($q) => $q->whereNull('apply_until')->orWhere('apply_until', '>=', now()->toDateString()))
            ->latest()
            ->get();

        $myApplications = $user->missionApplications()->with('mission')->latest()->get();
        $appliedMissionIds = $myApplications->pluck('mission_id')->all();

        return view('pages.shop.partner.missions', compact('user', 'openMissions', 'myApplications', 'appliedMissionIds'));
    }

    public function show(Mission $mission)
    {
        $user = $this->checkEligible();

        $application = $user->missionApplications()->where('mission_id', $mission->id)->first();

        // Brouillon, mission annulée ou réservée à une autre catégorie :
        // visible uniquement par ceux qui y ont déjà candidaté.
        $visible = in_array($mission->status, ['open', 'closed', 'in_progress', 'completed'], true)
            && $mission->targetsType($user->partner_type);
        abort_unless($visible || $application, 404);

        return view('pages.shop.partner.mission', compact('user', 'mission', 'application'));
    }

    public function apply(Request $request, Mission $mission)
    {
        $user = $this->checkEligible();

        if (!$mission->isOpenForApplications()) {
            return back()->with('error', "Cette mission n'accepte plus de candidatures.");
        }
        abort_unless($mission->targetsType($user->partner_type), 404);
        if ($user->missionApplications()->where('mission_id', $mission->id)->exists()) {
            return back()->with('error', 'Vous avez déjà candidaté à cette mission.');
        }

        $validated = $request->validate([
            'proposal' => 'required|string|max:5000',
            'proposed_rate' => 'nullable|numeric|min:0',
            'proposed_delay' => 'nullable|string|max:100',
            'experience' => 'nullable|string|max:3000',
            'references' => 'nullable|string|max:2000',
            'document' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $validated['document_path'] = $file->store("mission-applications/{$mission->id}", 'local');
            $validated['document_name'] = $file->getClientOriginalName();
        }
        unset($validated['document']);

        $user->missionApplications()->create($validated + [
            'mission_id' => $mission->id,
            'status' => 'applied',
            'status_history' => [['from' => null, 'to' => 'applied', 'by' => $user->name, 'note' => null, 'at' => now()->toDateTimeString()]],
        ]);

        return redirect()->route('dashboard.partner.missions.show', $mission->id)->with('success', 'Candidature envoyée. IT Holding vous tiendra informé de son avancement.');
    }
}
