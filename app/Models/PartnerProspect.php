<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerProspect extends Model
{
    protected $fillable = [
        'partner_id',
        'name',
        'phone',
        'email',
        'company',
        'need',
        'budget',
        'status',
        'notes',
        'next_action_at',
        'next_action_description',
        // Opportunité apporteur d'affaires (doc §4, §30-33)
        'entry_type',
        'opportunity_ref',
        'duplicate_of_id',
        'duplicate_status',
        'contract_amount',
        'commission_type',
        'commission_value',
        'commission_amount',
        'arbitration_history',
        'tax_rule_id',
        'withholding_amount',
        'net_amount',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'contract_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'withholding_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'next_action_at' => 'datetime',
        'arbitration_history' => 'array',
    ];

    /**
     * Get the partner that owns the prospect.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    /**
     * The original opportunity this one was flagged as a duplicate of.
     */
    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'duplicate_of_id');
    }

    /**
     * Other opportunities that were flagged as duplicates of this one.
     */
    public function duplicates(): HasMany
    {
        return $this->hasMany(self::class, 'duplicate_of_id');
    }

    public function taxRule(): BelongsTo
    {
        return $this->belongsTo(TaxRule::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopeStandardProspects($q)
    {
        return $q->where('entry_type', 'prospect');
    }

    public function scopeOpportunities($q)
    {
        return $q->where('entry_type', 'opportunity');
    }

    /**
     * Generate the next sequential opportunity ID, e.g. ITH-OPP-000154.
     */
    public static function generateOpportunityRef(): string
    {
        $count = self::whereNotNull('opportunity_ref')->count() + 1;
        do {
            $ref = 'ITH-OPP-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            $count++;
        } while (self::where('opportunity_ref', $ref)->exists());

        return $ref;
    }

    /**
     * Find an existing, still-open opportunity matching this phone, email or
     * company (doc §32: règle d'attribution / anti-doublon). Only matches against
     * "root" entries (not already flagged as someone else's duplicate).
     */
    public static function findDuplicateOpportunity(?string $phone, ?string $email, ?string $company, ?int $excludeId = null): ?self
    {
        if (!$phone && !$email && !$company) {
            return null;
        }

        return self::opportunities()
            ->whereNull('duplicate_of_id')
            ->where('status', '!=', 'lost')
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($phone, $email, $company) {
                if ($phone) {
                    $q->orWhere('phone', $phone);
                }
                if ($email) {
                    $q->orWhereRaw('LOWER(email) = ?', [strtolower($email)]);
                }
                if ($company) {
                    $q->orWhereRaw('LOWER(company) = ?', [strtolower($company)]);
                }
            })
            ->orderBy('created_at')
            ->first();
    }

    /**
     * Compute the apporteur commission from contract_amount / commission_type /
     * commission_value, without persisting it (doc §33-34).
     */
    public function computeCommission(): ?float
    {
        if ($this->commission_type === 'percent') {
            if ($this->contract_amount === null || $this->commission_value === null) {
                return null;
            }
            return round(((float) $this->contract_amount) * ((float) $this->commission_value) / 100, 2);
        }

        if ($this->commission_type === 'fixed') {
            return $this->commission_value !== null ? (float) $this->commission_value : null;
        }

        return null;
    }
}
