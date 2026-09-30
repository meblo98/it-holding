<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne du registre du portefeuille professionnel (doc §37-38).
 * Immuable : une correction se fait par une ligne compensatoire.
 */
class ProWalletEntry extends Model
{
    protected $fillable = [
        'user_id',
        'direction',
        'source_type',
        'source_id',
        'gross_amount',
        'withholding_amount',
        'amount',
        'tax_rule_id',
        'description',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'withholding_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public const SOURCE_LABELS = [
        'commission_vente'              => 'Commission vente',
        'commission_apporteur'          => "Commission apporteur d'affaires",
        'mission'                       => 'Mission',
        'withdrawal'                    => 'Retrait',
        'withdrawal_refund'             => 'Retrait refusé — recrédit',
        'shop_credit_transfer'          => 'Transfert en crédit boutique',
        'legacy_shop_credit_commission' => 'Versé en crédit boutique',
        'legacy_shop_credit_mission'    => 'Versé en crédit boutique',
    ];

    // Sources pour lesquelles une attestation de retenue peut exister.
    public const CERTIFIABLE_SOURCES = [
        'commission_vente' => 'commission',
        'commission_apporteur' => 'opportunity',
        'mission' => 'mission',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taxRule(): BelongsTo
    {
        return $this->belongsTo(TaxRule::class);
    }

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCE_LABELS[$this->source_type] ?? $this->source_type;
    }

    public function isCredit(): bool
    {
        return $this->direction === 'credit';
    }

    public function getSignedAmountAttribute(): float
    {
        return $this->isCredit() ? (float) $this->amount : -(float) $this->amount;
    }
}
