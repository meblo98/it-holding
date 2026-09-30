<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = [
        'type',
        'version',
        'title',
        'content',
        'status',
        'effective_from',
        'created_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
    ];

    public const STATUSES = [
        'draft'    => 'Brouillon',
        'active'   => 'En vigueur',
        'archived' => 'Archivé',
    ];

    public function acceptances(): HasMany
    {
        return $this->hasMany(ContractAcceptance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    /**
     * Once a contract has at least one acceptance, its content must never
     * change again — new terms require a new version (doc §64).
     */
    public function isLocked(): bool
    {
        return $this->status !== 'draft' || $this->acceptances()->exists();
    }

    /**
     * The contract currently in force for a given partner_type, falling back
     * to a generic "all" contract if no type-specific one is active.
     */
    public static function currentFor(string $partnerType): ?self
    {
        return static::active()->where('type', $partnerType)->latest('effective_from')->first()
            ?? static::active()->where('type', 'all')->latest('effective_from')->first();
    }

    public function contentHash(): string
    {
        return hash('sha256', $this->content);
    }
}
