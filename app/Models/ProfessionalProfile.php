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
        // Structure (prestataires, doc §7)
        'company_name',
        'rccm',
        'legal_representative',
        'team_size',
        'website',
    ];

    // Champs d'identité légale : les modifier invalide une vérification pro déjà faite.
    public const IDENTITY_FIELDS = ['company_name', 'ninea', 'rccm'];

    public function isStructure(): bool
    {
        return filled($this->company_name);
    }

    /**
     * Règles des champs de profil modifiables par le professionnel lui-même
     * comme par l'admin. Le niveau de vérification et le statut fiscal
     * restent réservés à l'admin.
     */
    public static function editableRules(): array
    {
        return [
            'city' => 'nullable|string|max:100',
            'availability' => 'nullable|in:available,busy,unavailable',
            'skills' => 'nullable|string|max:500',
            'languages' => 'nullable|string|max:200',
            'bio' => 'nullable|string|max:2000',
            'ninea' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'rccm' => 'nullable|string|max:100',
            'legal_representative' => 'nullable|string|max:255',
            'team_size' => 'nullable|integer|min:1|max:10000',
            'website' => 'nullable|url|max:255',
        ];
    }

    /**
     * Transforme les listes saisies « a, b, c » en tableaux.
     */
    public static function normalizeInput(array $data): array
    {
        foreach (['skills', 'languages'] as $key) {
            if (array_key_exists($key, $data)) {
                $value = $data[$key];
                $items = $value === null ? [] : array_values(array_filter(array_map('trim', explode(',', $value))));
                $data[$key] = $items ?: null;
            }
        }

        return $data;
    }

    public const BENEFICIARY_TYPES = [
        'individual' => 'Personne physique',
        'company'    => 'Personne morale',
    ];

    protected $casts = [
        'skills' => 'array',
        'languages' => 'array',
        'verification_level' => 'integer',
        'team_size' => 'integer',
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
