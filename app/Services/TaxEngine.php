<?php

namespace App\Services;

use App\Models\TaxRule;
use Carbon\CarbonInterface;

class TaxEngine
{
    /**
     * Compute the withholding tax (retenue à la source) applicable to a gross
     * amount, driven entirely by configurable App\Models\TaxRule rows.
     *
     * Doc §34-36 : le CGI sénégalais prévoit notamment une retenue de 5% sur
     * certaines prestations à partir de 25 000 FCFA, mais ces conditions
     * (bénéficiaire, nature de la prestation, seuil, taux) doivent rester
     * modifiables par le comptable/fiscaliste sans toucher au code — donc
     * aucun taux n'est codé en dur ici : tout vient de la table tax_rules.
     *
     * @param  float  $grossAmount  Montant HT sur lequel appliquer la retenue.
     * @param  string  $prestationNature  ex: commission_vente, commission_apporteur, mission_freelance.
     * @param  string  $beneficiaryType  individual ou company.
     * @param  string  $country  Code pays ISO 2 lettres (défaut SN).
     * @return array{rule: ?TaxRule, withholding_amount: float, net_amount: float, applied: bool}
     */
    public function calculate(
        float $grossAmount,
        string $prestationNature,
        string $beneficiaryType = 'individual',
        string $country = 'SN',
        ?CarbonInterface $date = null
    ): array {
        $date = $date ?: now();

        $rule = TaxRule::active()
            ->where('country', $country)
            ->whereIn('beneficiary_type', [$beneficiaryType, 'all'])
            ->whereIn('prestation_nature', [$prestationNature, 'all'])
            ->where(fn ($q) => $q->whereNull('effective_from')->orWhere('effective_from', '<=', $date))
            ->where(fn ($q) => $q->whereNull('effective_to')->orWhere('effective_to', '>=', $date))
            // Préférer la règle la plus spécifique (bénéficiaire/prestation exacts avant les règles génériques "all")
            ->orderByRaw("(beneficiary_type = ?) desc", [$beneficiaryType])
            ->orderByRaw("(prestation_nature = ?) desc", [$prestationNature])
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first();

        if (!$rule || $rule->is_exempt) {
            return $this->noWithholding($rule, $grossAmount);
        }

        if ($rule->threshold_amount !== null && $grossAmount < (float) $rule->threshold_amount) {
            return $this->noWithholding($rule, $grossAmount);
        }

        $withholding = round($grossAmount * (float) $rule->rate / 100, 2);

        return [
            'rule' => $rule,
            'withholding_amount' => $withholding,
            'net_amount' => round($grossAmount - $withholding, 2),
            'applied' => true,
        ];
    }

    private function noWithholding(?TaxRule $rule, float $grossAmount): array
    {
        return [
            'rule' => $rule,
            'withholding_amount' => 0.0,
            'net_amount' => $grossAmount,
            'applied' => false,
        ];
    }
}
