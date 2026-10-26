<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourcingRequest extends Model
{
    public const LABELS = [
        'open'           => 'En cours',
        'awarded'        => 'Offre retenue',
        'failed'         => 'Aucune offre',
        'needs_location' => 'Position à renseigner',
        'cancelled'      => 'Annulée',
    ];

    protected $fillable = ['order_id', 'status', 'deadline_at', 'awarded_offer_id', 'awarded_at', 'award_mode', 'notes'];

    protected $casts = [
        'deadline_at' => 'datetime',
        'awarded_at'  => 'datetime',
    ];

    public function order()        { return $this->belongsTo(Order::class); }
    public function offers()       { return $this->hasMany(SupplierOffer::class); }
    public function awardedOffer() { return $this->belongsTo(SupplierOffer::class, 'awarded_offer_id'); }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }
}
