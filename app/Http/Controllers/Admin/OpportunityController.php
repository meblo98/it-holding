<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerProspect;
use App\Services\CommissionPayout;
use App\Services\TaxEngine;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    /**
     * List all business opportunities (apporteurs d'affaires), with a filter
     * for those flagged as duplicates awaiting arbitration (doc §32).
     */
    public function index(Request $request)
    {
        $query = PartnerProspect::opportunities()->with(['partner']);

        if ($request->boolean('flagged')) {
            $query->where('duplicate_status', 'flagged');
        }

        $opportunities = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $flaggedCount = PartnerProspect::opportunities()->where('duplicate_status', 'flagged')->count();

        return view('admin.opportunities.index', compact('opportunities', 'flaggedCount'));
    }

    public function show(PartnerProspect $opportunity)
    {
        abort_unless($opportunity->entry_type === 'opportunity', 404);

        $opportunity->load(['partner', 'duplicateOf.partner', 'duplicates.partner']);

        return view('admin.opportunities.show', compact('opportunity'));
    }

    /**
     * Arbitrate a duplicate group: attribute the opportunity to one partner and
     * clear the flag on every entry in the group, keeping a trace of the decision
     * on each one (doc §32: "la première déclaration valide peut être prioritaire
     * ... l'administrateur arbitre").
     */
    public function arbitrate(Request $request, PartnerProspect $opportunity)
    {
        abort_unless($opportunity->entry_type === 'opportunity', 404);

        $validated = $request->validate([
            'winner_partner_id' => 'required|exists:users,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $root = $opportunity->duplicate_of_id ? $opportunity->duplicateOf : $opportunity;
        $group = collect([$root])->merge($root->duplicates)->unique('id');

        foreach ($group as $entry) {
            $history = $entry->arbitration_history ?? [];
            $history[] = [
                'decision' => (int) $entry->partner_id === (int) $validated['winner_partner_id'] ? 'attribuée' : 'rejetée',
                'partner_id' => $entry->partner_id,
                'admin_id' => auth()->id(),
                'note' => $validated['note'] ?? null,
                'at' => now()->toDateTimeString(),
            ];
            $entry->update([
                'duplicate_status' => 'cleared',
                'arbitration_history' => $history,
            ]);
        }

        return back()->with('success', "Arbitrage enregistré : l'opportunité a été attribuée, l'historique est conservé sur chaque déclaration.");
    }

    /**
     * Update the opportunity's outcome (status, final contract amount) and its
     * apporteur commission rule (doc §33-36) — never a fixed rate baked into code.
     */
    public function update(Request $request, PartnerProspect $opportunity)
    {
        abort_unless($opportunity->entry_type === 'opportunity', 404);

        $validated = $request->validate([
            'status' => 'required|in:new,contacted,interested,proposal_sent,negotiating,won,lost',
            'contract_amount' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|in:percent,fixed',
            'commission_value' => 'nullable|numeric|min:0',
        ]);

        // Une commission déjà versée au portefeuille ne se modifie plus.
        if ($opportunity->credited_at) {
            return back()->with('error', 'Commission déjà versée au portefeuille de l\'apporteur le ' . $opportunity->credited_at->format('d/m/Y') . ' : cette opportunité ne peut plus être modifiée.');
        }

        $opportunity->fill($validated);

        // Doc §21-22, §64 : la commission apporteur ne se finalise pas tant que
        // le contrat en vigueur pour sa catégorie n'a pas été accepté.
        $partner = $opportunity->partner;
        $contractBlocked = $partner && !$partner->hasAcceptedCurrentContract();

        if ($contractBlocked) {
            $opportunity->commission_amount = null;
            $opportunity->tax_rule_id = null;
            $opportunity->withholding_amount = 0;
            $opportunity->net_amount = null;
        } else {
            // Aperçu brut / retenue / net, recalculé à chaque enregistrement
            // tant que la commission n'est pas versée (moteur fiscal, doc §34-37).
            $opportunity->commission_amount = $opportunity->computeCommission();

            if ($opportunity->commission_amount !== null) {
                $tax = app(TaxEngine::class)->calculate(
                    (float) $opportunity->commission_amount,
                    'commission_apporteur',
                    $partner?->professionalProfile?->beneficiary_type ?? 'individual'
                );
                $opportunity->tax_rule_id = $tax['rule']?->id;
                $opportunity->withholding_amount = $tax['withholding_amount'];
                $opportunity->net_amount = $tax['net_amount'];
            } else {
                $opportunity->tax_rule_id = null;
                $opportunity->withholding_amount = 0;
                $opportunity->net_amount = null;
            }
        }

        $opportunity->save();

        $message = 'Opportunité mise à jour.';
        if ($contractBlocked) {
            $message .= " Commission non calculée : {$partner->name} n'a pas encore accepté son contrat (elle sera versée automatiquement dès son acceptation).";
        } elseif ($opportunity->status === 'won' && app(CommissionPayout::class)->payOpportunity($opportunity) === 'paid') {
            $message .= ' Commission de ' . number_format($opportunity->net_amount, 0, ',', ' ') . ' FCFA nets versée au portefeuille de l\'apporteur.';
        }

        return back()->with('success', $message);
    }
}
