<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    protected $fillable = [
        'name',
        'country',
        'beneficiary_type',
        'prestation_nature',
        'rate',
        'threshold_amount',
        'is_exempt',
        'tax_regime',
        'active',
        'effective_from',
        'effective_to',
        'notes',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'threshold_amount' => 'decimal:2',
        'is_exempt' => 'boolean',
        'active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public const BENEFICIARY_TYPES = [
        'all'        => 'Tous bénéficiaires',
        'individual' => 'Personne physique',
        'company'    => 'Personne morale',
    ];

    public const PRESTATION_NATURES = [
        'all'                  => 'Toutes prestations',
        'commission_vente'     => 'Commission vente (partenaire commercial)',
        'commission_apporteur' => "Commission apporteur d'affaires",
        'mission_freelance'    => 'Prestation freelance / mission',
    ];

    public function scopeActive($q)
    {
        return $q->where('active', true);
    }
}
