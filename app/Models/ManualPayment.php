<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualPayment extends Model
{
    public const LABELS = [
        'awaiting'  => 'Paiement à effectuer',
        'submitted' => 'À vérifier',
        'confirmed' => 'Confirmé',
        'rejected'  => 'Refusé',
    ];

    protected $fillable = [
        'payable_type', 'payable_id', 'user_id', 'method', 'amount', 'reference', 'proof_path', 'status',
        'submitted_at', 'reviewed_by', 'reviewed_at', 'rejection_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public function payable()  { return $this->morphTo(); }
    public function user()     { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }

    public function getPayableLabelAttribute(): string
    {
        $p = $this->payable;

        return match (true) {
            $p instanceof Order            => 'Commande #' . $p->id,
            $p instanceof CareSubscription => 'Abonnement Care+ ' . $p->number . ' (' . ucfirst($p->plan) . ')',
            default                        => 'Paiement #' . $this->id,
        };
    }
}
