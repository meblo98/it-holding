<?php

namespace App\Services;

use App\Models\MissionApplication;
use App\Models\PartnerCommission;
use App\Models\PartnerProspect;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Données d'une attestation de retenue à la source (doc §34, §36), quelle
 * que soit la source : commission vente, commission apporteur ou mission.
 */
class WithholdingCertificate
{
    public const TYPES = ['commission', 'opportunity', 'mission'];

    /**
     * @return array{ref: string, beneficiary: \App\Models\User, nature: string, gross: float, withholding: float, net: float, rule: ?\App\Models\TaxRule, date: \Illuminate\Support\Carbon}
     */
    public function resolve(string $type, int $id): array
    {
        $data = match ($type) {
            'commission' => $this->fromCommission(PartnerCommission::with(['partner.professionalProfile', 'taxRule'])->findOrFail($id)),
            'opportunity' => $this->fromOpportunity(PartnerProspect::opportunities()->with(['partner.professionalProfile', 'taxRule'])->findOrFail($id)),
            'mission' => $this->fromMission(MissionApplication::with(['user.professionalProfile', 'taxRule', 'mission'])->findOrFail($id)),
            default => throw new ModelNotFoundException(),
        };

        if ($data['withholding'] <= 0 || !$data['beneficiary']) {
            throw new ModelNotFoundException();
        }

        return $data;
    }

    private function fromCommission(PartnerCommission $c): array
    {
        return [
            'ref' => 'COMM-' . str_pad($c->id, 6, '0', STR_PAD_LEFT),
            'beneficiary' => $c->partner,
            'nature' => 'Commission vente (partenaire commercial) — commande #' . $c->order_id,
            'gross' => (float) $c->commission_amount,
            'withholding' => (float) $c->withholding_amount,
            'net' => (float) $c->net_amount,
            'rule' => $c->taxRule,
            'date' => $c->updated_at,
        ];
    }

    private function fromOpportunity(PartnerProspect $o): array
    {
        return [
            'ref' => $o->opportunity_ref,
            'beneficiary' => $o->partner,
            'nature' => "Commission apporteur d'affaires — opportunité " . $o->opportunity_ref,
            'gross' => (float) $o->commission_amount,
            'withholding' => (float) $o->withholding_amount,
            'net' => (float) $o->net_amount,
            'rule' => $o->taxRule,
            'date' => $o->credited_at ?? $o->updated_at,
        ];
    }

    private function fromMission(MissionApplication $a): array
    {
        return [
            'ref' => $a->mission->reference . '-' . $a->id,
            'beneficiary' => $a->user,
            'nature' => 'Prestation — mission ' . $a->mission->reference . ' : ' . $a->mission->title,
            'gross' => (float) $a->agreed_amount,
            'withholding' => (float) $a->withholding_amount,
            'net' => (float) $a->net_amount,
            'rule' => $a->taxRule,
            'date' => $a->paid_at ?? $a->updated_at,
        ];
    }
}
