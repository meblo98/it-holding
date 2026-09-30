<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CrmDeal;
use App\Models\PartnerProspect;
use App\Models\Quote;
use App\Models\User;
use App\Services\SalesAssistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CrmDealController extends Controller
{
    /**
     * Pipeline commercial central (doc §43).
     */
    public function index(Request $request)
    {
        $ownerFilter = $request->input('owner', 'all');

        $deals = CrmDeal::with(['client', 'owner', 'partner'])
            ->when($ownerFilter === 'mine', fn ($q) => $q->where('owner_id', auth()->id()))
            ->when(is_numeric($ownerFilter), fn ($q) => $q->where('owner_id', (int) $ownerFilter))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('company', 'like', $term)
                    ->orWhere('contact_name', 'like', $term)->orWhere('reference', 'like', $term));
            })
            ->where(fn ($q) => $q->where('stage', '!=', 'lost')->orWhere('updated_at', '>=', now()->subDays(30)))
            ->orderByRaw('next_action_at IS NULL, next_action_at')
            ->get()
            ->groupBy('stage');

        $stats = [
            'pipeline' => CrmDeal::open()->sum('amount'),
            'open' => CrmDeal::open()->count(),
            'won_month' => CrmDeal::whereIn('stage', CrmDeal::WON_STAGES)->where('closed_at', '>=', now()->startOfMonth())->sum('amount'),
            'late' => CrmDeal::whereNotNull('next_action_at')->where('next_action_at', '<', now())->whereNotIn('stage', ['lost', 'loyalty'])->count(),
        ];

        $followUps = CrmDeal::with('owner')
            ->whereNotNull('next_action_at')
            ->where('next_action_at', '<', now()->endOfDay())
            ->whereNotIn('stage', ['lost', 'loyalty'])
            ->when($ownerFilter === 'mine', fn ($q) => $q->where('owner_id', auth()->id()))
            ->orderBy('next_action_at')
            ->limit(10)->get();

        return view('admin.crm.index', [
            'deals' => $deals,
            'stats' => $stats,
            'followUps' => $followUps,
            'owners' => $this->staff(),
            'ownerFilter' => $ownerFilter,
        ]);
    }

    public function create(Request $request)
    {
        $deal = new CrmDeal(['stage' => 'new', 'source' => 'commercial', 'owner_id' => auth()->id(), 'client_id' => $request->integer('client_id') ?: null]);

        return view('admin.crm.form', ['deal' => $deal, 'clients' => $this->clients(), 'owners' => $this->staff()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $deal = DB::transaction(function () use ($data) {
            $deal = CrmDeal::create($data + ['reference' => CrmDeal::generateReference(), 'created_by' => auth()->id()]);
            $this->log($deal, 'stage_change', 'Affaire créée à l\'étape « ' . $deal->stage_label . ' ».');

            return $deal;
        });

        return redirect()->route('admin.crm.show', $deal->id)->with('success', "Affaire {$deal->reference} créée.");
    }

    public function show(CrmDeal $deal)
    {
        $deal->load(['client', 'owner', 'partner', 'partnerProspect', 'quote', 'activities' => fn ($q) => $q->with('user')->latest()]);

        $quotes = Quote::query()
            ->when($deal->client_id, fn ($q) => $q->where('client_id', $deal->client_id))
            ->latest()->limit(20)->get(['id', 'number', 'client_name', 'total_amount', 'status']);

        return view('admin.crm.show', ['deal' => $deal, 'quotes' => $quotes, 'owners' => $this->staff()]);
    }

    public function edit(CrmDeal $deal)
    {
        return view('admin.crm.form', ['deal' => $deal, 'clients' => $this->clients(), 'owners' => $this->staff()]);
    }

    public function update(Request $request, CrmDeal $deal)
    {
        $data = $this->validated($request);
        $oldStage = $deal->stage;

        DB::transaction(function () use ($deal, $data, $oldStage) {
            $deal->update($data + $this->closingFields($data['stage'], $deal));
            if ($oldStage !== $deal->stage) {
                $this->log($deal, 'stage_change', 'Étape : ' . CrmDeal::STAGES[$oldStage] . ' → ' . $deal->stage_label . ($deal->stage === 'lost' ? " ({$deal->lost_reason})" : '') . '.');
            }
        });

        return redirect()->route('admin.crm.show', $deal->id)->with('success', 'Affaire mise à jour.');
    }

    /**
     * Changement d'étape rapide depuis le pipeline ou la fiche.
     */
    public function moveStage(Request $request, CrmDeal $deal)
    {
        $validated = $request->validate([
            'stage' => 'required|in:' . implode(',', array_keys(CrmDeal::STAGES)),
            'lost_reason' => 'nullable|required_if:stage,lost|string|max:255',
        ], ['lost_reason.required_if' => 'Indiquez pourquoi l\'affaire est perdue.']);

        if ($validated['stage'] !== $deal->stage) {
            $old = $deal->stage;
            DB::transaction(function () use ($deal, $validated, $old) {
                $deal->update(['stage' => $validated['stage'], 'lost_reason' => $validated['lost_reason'] ?? $deal->lost_reason] + $this->closingFields($validated['stage'], $deal));
                $this->log($deal, 'stage_change', 'Étape : ' . CrmDeal::STAGES[$old] . ' → ' . $deal->stage_label . ($deal->stage === 'lost' ? " ({$deal->lost_reason})" : '') . '.');
            });
        }

        return back()->with('success', "{$deal->reference} : étape « {$deal->stage_label} ».");
    }

    /**
     * Journal des échanges et prochaine relance.
     */
    public function storeActivity(Request $request, CrmDeal $deal)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(CrmDeal::ACTIVITY_TYPES)),
            'body' => 'required|string|max:5000',
            'next_action_at' => 'nullable|date',
            'next_action_note' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($deal, $validated) {
            $this->log($deal, $validated['type'], $validated['body']);
            $deal->update([
                'next_action_at' => $validated['next_action_at'] ?? null,
                'next_action_note' => $validated['next_action_note'] ?? null,
            ]);
        });

        return back()->with('success', 'Échange enregistré.');
    }

    public function linkQuote(Request $request, CrmDeal $deal)
    {
        $validated = $request->validate(['quote_id' => 'required|exists:quotes,id']);
        $quote = Quote::find($validated['quote_id']);

        $deal->update(['quote_id' => $quote->id, 'amount' => $deal->amount ?: $quote->total_amount]);
        $this->log($deal, 'quote', "Devis {$quote->number} associé (" . number_format($quote->total_amount, 0, ',', ' ') . ' FCFA).');

        return back()->with('success', "Devis {$quote->number} associé à l'affaire.");
    }

    /**
     * Importe dans le pipeline un prospect ou une opportunité déclaré par un
     * partenaire ou un apporteur (doc §77 : le partenaire ne possède pas les
     * clients, tout remonte au système central).
     */
    public function importPartnerProspect(PartnerProspect $prospect)
    {
        $existing = CrmDeal::where('partner_prospect_id', $prospect->id)->first();
        if ($existing) {
            return redirect()->route('admin.crm.show', $existing->id)->with('success', 'Ce prospect est déjà suivi dans le pipeline.');
        }

        $isOpportunity = $prospect->entry_type === 'opportunity';

        $deal = DB::transaction(function () use ($prospect, $isOpportunity) {
            $deal = CrmDeal::create([
                'reference' => CrmDeal::generateReference(),
                'title' => $isOpportunity ? "Opportunité {$prospect->opportunity_ref}" : "Prospect de {$prospect->partner?->name}",
                'contact_name' => $prospect->name,
                'contact_phone' => $prospect->phone,
                'contact_email' => $prospect->email,
                'company' => $prospect->company,
                'need' => $prospect->need,
                'amount' => $prospect->contract_amount ?? $prospect->budget,
                'source' => $isOpportunity ? 'apporteur' : 'partner',
                'partner_id' => $prospect->partner_id,
                'partner_prospect_id' => $prospect->id,
                'stage' => 'new',
                'owner_id' => auth()->id(),
                'created_by' => auth()->id(),
            ]);

            $this->log($deal, 'stage_change', 'Importé depuis ' . ($isOpportunity ? "l'opportunité {$prospect->opportunity_ref}" : 'le CRM du partenaire') . " déclaré(e) par {$prospect->partner?->name}.");

            return $deal;
        });

        return redirect()->route('admin.crm.show', $deal->id)->with('success', "Affaire {$deal->reference} créée à partir du prospect partenaire.");
    }

    /**
     * Vue centrale des prospects déclarés par tout le réseau (doc §43, §77).
     */
    public function partnerProspects(Request $request)
    {
        $prospects = PartnerProspect::with('partner')
            ->when($request->input('type') === 'opportunity', fn ($q) => $q->opportunities())
            ->when($request->input('type') === 'prospect', fn ($q) => $q->standardProspects())
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('company', 'like', $term)->orWhere('phone', 'like', $term));
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $imported = CrmDeal::whereIn('partner_prospect_id', $prospects->pluck('id'))->pluck('id', 'partner_prospect_id');

        return view('admin.crm.partner-prospects', compact('prospects', 'imported'));
    }

    // ── Assistant IA sur une affaire (doc §44) ─────────────────────────────

    public function aiFollowUp(Request $request, CrmDeal $deal, SalesAssistant $assistant)
    {
        $channel = $request->validate(['channel' => 'required|in:whatsapp,email'])['channel'];

        return $this->runAi($deal, fn () => $assistant->followUp($deal, $channel), 'Relance ' . ($channel === 'email' ? 'e-mail' : 'WhatsApp') . ' proposée');
    }

    public function aiAnalyze(CrmDeal $deal, SalesAssistant $assistant)
    {
        return $this->runAi($deal, fn () => $assistant->analyzeDeal($deal), 'Analyse de l\'opportunité');
    }

    public function aiQuote(CrmDeal $deal, SalesAssistant $assistant)
    {
        try {
            $result = $assistant->quoteFromNeed($deal, auth()->user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if (!$result['quote']) {
            $msg = 'Aucun produit ou service du catalogue ne correspond au besoin : aucun devis créé.';
            if ($result['unmatched']) {
                $msg .= ' Besoins identifiés : ' . implode(' ; ', $result['unmatched']) . '.';
            }

            return back()->with('error', $msg);
        }

        $quote = $result['quote'];
        $deal->update(['quote_id' => $quote->id, 'amount' => $deal->amount ?: $quote->total_amount]);
        $this->log($deal, 'quote', "Brouillon de devis {$quote->number} préparé par l'assistant IA (" . number_format($quote->total_amount, 0, ',', ' ') . ' FCFA HT, prix du catalogue).'
            . ($result['unmatched'] ? ' À chiffrer manuellement : ' . implode(' ; ', $result['unmatched']) . '.' : ''));

        return back()->with('success', "Brouillon de devis {$quote->number} créé avec les prix du catalogue. Vérifiez-le (TVA, remises, conditions) avant de l'envoyer.");
    }

    private function runAi(CrmDeal $deal, callable $call, string $label)
    {
        try {
            $text = $call();
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->log($deal, 'ai', "{$label} :\n{$text}");

        return back()->with('success', "{$label} : voir le journal ci-dessous.")->with('ai_result', $text);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function closingFields(string $stage, CrmDeal $deal): array
    {
        if (in_array($stage, ['won', 'lost'], true) && !$deal->closed_at) {
            return ['closed_at' => now()];
        }
        if (in_array($stage, CrmDeal::OPEN_STAGES, true)) {
            return ['closed_at' => null];
        }

        return [];
    }

    private function log(CrmDeal $deal, string $type, string $body): void
    {
        $deal->activities()->create(['user_id' => auth()->id(), 'type' => $type, 'body' => $body]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'need' => 'nullable|string|max:5000',
            'source' => 'required|in:' . implode(',', array_keys(CrmDeal::SOURCES)),
            'stage' => 'required|in:' . implode(',', array_keys(CrmDeal::STAGES)),
            'amount' => 'nullable|numeric|min:0',
            'owner_id' => 'nullable|exists:users,id',
            'next_action_at' => 'nullable|date',
            'next_action_note' => 'nullable|string|max:255',
            'lost_reason' => 'nullable|required_if:stage,lost|string|max:255',
        ], ['lost_reason.required_if' => 'Indiquez pourquoi l\'affaire est perdue.']);
    }

    private function clients()
    {
        return Client::orderBy('company_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'company_name']);
    }

    private function staff()
    {
        return User::where(fn ($q) => $q->where('is_admin', true)->orWhereIn('role', ['admin', 'dg', 'commercial']))
            ->orderBy('name')->get(['id', 'name']);
    }
}
