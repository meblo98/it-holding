<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerCommission;
use App\Models\PartnerProspect;
use App\Models\TaxRule;
use Illuminate\Http\Request;

class TaxRuleController extends Controller
{
    /**
     * List of configurable tax rules (doc §35 : TAX ENGINE). Réservé, via le
     * système de permissions par rôle existant, au module "tax_engine" —
     * typiquement accordé au rôle comptable.
     */
    public function index()
    {
        $rules = TaxRule::orderByDesc('active')->orderBy('name')->get();

        return view('admin.tax-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.tax-rules.form', ['rule' => new TaxRule()]);
    }

    public function store(Request $request)
    {
        TaxRule::create($this->validated($request));

        return redirect()->route('admin.tax-rules.index')->with('success', 'Règle fiscale créée.');
    }

    public function edit(TaxRule $taxRule)
    {
        return view('admin.tax-rules.form', ['rule' => $taxRule]);
    }

    public function update(Request $request, TaxRule $taxRule)
    {
        $taxRule->update($this->validated($request));

        return redirect()->route('admin.tax-rules.index')->with('success', 'Règle fiscale mise à jour — appliquée dès le prochain calcul, sans déploiement.');
    }

    public function destroy(TaxRule $taxRule)
    {
        $taxRule->delete();

        return redirect()->route('admin.tax-rules.index')->with('success', 'Règle fiscale supprimée.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|size:2',
            'beneficiary_type' => 'required|in:' . implode(',', array_keys(TaxRule::BENEFICIARY_TYPES)),
            'prestation_nature' => 'required|in:' . implode(',', array_keys(TaxRule::PRESTATION_NATURES)),
            'rate' => 'required|numeric|min:0|max:100',
            'threshold_amount' => 'nullable|numeric|min:0',
            'is_exempt' => 'nullable|boolean',
            'tax_regime' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['is_exempt'] = $request->boolean('is_exempt');
        $data['active'] = $request->boolean('active');

        return $data;
    }

    /**
     * Justificatifs de retenue (doc §34, §36) : liste des commissions et
     * opportunités pour lesquelles une retenue a été appliquée, avec attestation
     * imprimable/exportable pour le bénéficiaire.
     */
    public function withholdings(Request $request)
    {
        $commissions = PartnerCommission::with(['partner', 'taxRule'])
            ->where('withholding_amount', '>', 0)
            ->when($request->filled('partner_id'), fn ($q) => $q->where('partner_id', $request->partner_id))
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn ($c) => (object) [
                'type' => 'commission',
                'id' => $c->id,
                'ref' => 'COMM-' . str_pad($c->id, 6, '0', STR_PAD_LEFT),
                'partner' => $c->partner,
                'gross' => $c->commission_amount,
                'withholding' => $c->withholding_amount,
                'net' => $c->net_amount,
                'tax_rule' => $c->taxRule,
                'date' => $c->updated_at,
            ]);

        $opportunities = PartnerProspect::opportunities()->with(['partner', 'taxRule'])
            ->where('withholding_amount', '>', 0)
            ->when($request->filled('partner_id'), fn ($q) => $q->where('partner_id', $request->partner_id))
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn ($o) => (object) [
                'type' => 'opportunity',
                'id' => $o->id,
                'ref' => $o->opportunity_ref,
                'partner' => $o->partner,
                'gross' => $o->commission_amount,
                'withholding' => $o->withholding_amount,
                'net' => $o->net_amount,
                'tax_rule' => $o->taxRule,
                'date' => $o->updated_at,
            ]);

        $items = $commissions->merge($opportunities)->sortByDesc('date')->values();

        return view('admin.tax-rules.withholdings', compact('items'));
    }

    /**
     * Printable withholding certificate for one beneficiary payment.
     */
    public function certificate(string $type, int $id)
    {
        abort_unless(in_array($type, ['commission', 'opportunity']), 404);

        if ($type === 'commission') {
            $entry = PartnerCommission::with(['partner.professionalProfile', 'taxRule'])->findOrFail($id);
            $ref = 'COMM-' . str_pad($entry->id, 6, '0', STR_PAD_LEFT);
        } else {
            $entry = PartnerProspect::opportunities()->with(['partner.professionalProfile', 'taxRule'])->findOrFail($id);
            $ref = $entry->opportunity_ref;
        }

        abort_if((float) $entry->withholding_amount <= 0, 404);

        return view('admin.tax-rules.certificate', compact('entry', 'ref', 'type'));
    }
}
