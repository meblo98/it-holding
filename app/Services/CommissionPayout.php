<?php

namespace App\Services;

use App\Models\PartnerCommission;
use App\Models\PartnerProspect;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Chaîne de versement des commissions (doc §37) :
 * contrat accepté ? → retenue à la source (TaxEngine) → crédit net au
 * portefeuille pro (ProWallet). Utilisée à la fin d'une commande, au gain
 * d'une opportunité, et au déblocage après acceptation du contrat.
 */
class CommissionPayout
{
    public function __construct(private TaxEngine $taxEngine, private ProWallet $wallet)
    {
    }

    /**
     * @return string 'paid' ou 'blocked'
     */
    public function payOrderCommission(PartnerCommission $commission): string
    {
        $partner = $commission->partner;

        if (!$partner) {
            $commission->update(['status' => 'paid']);

            return 'paid';
        }

        if (!$partner->hasAcceptedCurrentContract()) {
            $commission->update(['status' => 'blocked_no_contract']);

            return 'blocked';
        }

        DB::transaction(function () use ($commission, $partner) {
            $tax = $this->taxEngine->calculate(
                (float) $commission->commission_amount,
                'commission_vente',
                $partner->professionalProfile?->beneficiary_type ?? 'individual'
            );

            $commission->update([
                'status' => 'paid',
                'tax_rule_id' => $tax['rule']?->id,
                'withholding_amount' => $tax['withholding_amount'],
                'net_amount' => $tax['net_amount'],
            ]);

            $desc = "Commission de la commande #{$commission->order_id}"
                . ($commission->promoCode ? " (code {$commission->promoCode->code})" : ' (lien direct)');

            $this->wallet->credit($partner, 'commission_vente', $commission->id, (float) $commission->commission_amount,
                $tax['withholding_amount'], $tax['net_amount'], $tax['rule']?->id, $desc);
        });

        return 'paid';
    }

    /**
     * Verse la commission d'une opportunité gagnée (doc §33), une seule fois.
     *
     * @return string 'paid', 'blocked' ou 'not_ready'
     */
    public function payOpportunity(PartnerProspect $opportunity): string
    {
        if ($opportunity->credited_at || $opportunity->status !== 'won') {
            return 'not_ready';
        }

        $partner = $opportunity->partner;
        if (!$partner || !$partner->hasAcceptedCurrentContract()) {
            return 'blocked';
        }

        $gross = $opportunity->computeCommission();
        if (!$gross || $gross <= 0) {
            return 'not_ready';
        }

        DB::transaction(function () use ($opportunity, $partner, $gross) {
            $tax = $this->taxEngine->calculate($gross, 'commission_apporteur', $partner->professionalProfile?->beneficiary_type ?? 'individual');

            $opportunity->update([
                'commission_amount' => $gross,
                'tax_rule_id' => $tax['rule']?->id,
                'withholding_amount' => $tax['withholding_amount'],
                'net_amount' => $tax['net_amount'],
                'credited_at' => now(),
            ]);

            $this->wallet->credit($partner, 'commission_apporteur', $opportunity->id, $gross,
                $tax['withholding_amount'], $tax['net_amount'], $tax['rule']?->id,
                "Commission apporteur — opportunité {$opportunity->opportunity_ref}");
        });

        return 'paid';
    }

    /**
     * Après acceptation du contrat : verse tout ce qui était bloqué faute de contrat.
     *
     * @return int nombre de commissions versées
     */
    public function releaseBlockedFor(User $user): int
    {
        if (!$user->hasAcceptedCurrentContract()) {
            return 0;
        }

        $released = 0;

        PartnerCommission::where('partner_id', $user->id)->where('status', 'blocked_no_contract')->get()
            ->each(function (PartnerCommission $c) use (&$released) {
                if ($this->payOrderCommission($c) === 'paid') {
                    $released++;
                }
            });

        PartnerProspect::opportunities()->where('partner_id', $user->id)->where('status', 'won')
            ->whereNull('credited_at')->whereNotNull('commission_type')->get()
            ->each(function (PartnerProspect $o) use (&$released) {
                if ($this->payOpportunity($o) === 'paid') {
                    $released++;
                }
            });

        return $released;
    }
}
