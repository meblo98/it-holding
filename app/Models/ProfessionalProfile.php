<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalProfile extends Model
{
    protected $fillable = [
        'user_id',
        'pro_id',
        'city',
        'skills',
        'languages',
        'availability',
        'verification_level',
        'bio',
        'beneficiary_type', // individual, company — utilisé par le moteur fiscal (TaxEngine)
        'ninea',
    ];

    public const BENEFICIARY_TYPES = [
        'individual' => 'Personne physique',
        'company'    => 'Personne morale',
    ];

    protected $casts = [
        'skills' => 'array',
        'languages' => 'array',
        'verification_level' => 'integer',
    ];

    // Niveaux de vérification, voir cahier des charges §13
    public const VERIFICATION_LEVELS = [
        1 => 'Téléphone & email vérifiés',
        2 => 'Pièce d\'identité vérifiée',
        3 => 'Informations professionnelles vérifiées',
        4 => 'Diplômes / certifications vérifiés',
        5 => 'Vérification approfondie IT Holding',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getVerificationLabelAttribute(): string
    {
        return self::VERIFICATION_LEVELS[$this->verification_level] ?? self::VERIFICATION_LEVELS[1];
    }

    /**
     * Generate the next sequential professional ID, e.g. ITH-PRO-000125.
     */
    public static function generateProId(): string
    {
        $count = self::whereNotNull('pro_id')->count() + 1;
        do {
            $proId = 'ITH-PRO-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            $count++;
        } while (self::where('pro_id', $proId)->exists());

        return $proId;
    }
}
