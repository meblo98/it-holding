<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierOffer extends Model
{
    public const LABELS = [
        'invited'  => 'Invité',
        'offered'  => 'Offre reçue',
        'declined' => 'Décliné',
        'selected' => 'Retenue',
        'rejected' => 'Non retenue',
        'expired'  => 'Sans réponse',
    ];

    protected $fillable = [
        'sourcing_request_id', 'supplier_id', 'token', 'status', 'price', 'delay_hours', 'note',
        'distance_km', 'score', 'invited_at', 'responded_at',
    ];

    protected $casts = [
        'distance_km'  => 'float',
        'score'        => 'float',
        'invited_at'   => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function request()  { return $this->belongsTo(SourcingRequest::class, 'sourcing_request_id'); }
    public function supplier() { return $this->belongsTo(Supplier::class); }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }
}
