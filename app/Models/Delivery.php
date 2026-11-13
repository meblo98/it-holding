<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    public const LABELS = [
        'pending'   => 'En attente de livreur',
        'offered'   => 'Proposée à un livreur',
        'accepted'  => 'Livreur assigné',
        'picked_up' => 'En route vers le client',
        'delivered' => 'Livrée',
        'cancelled' => 'Annulée',
    ];

    /** Statuts qui comptent comme une course en cours pour un livreur. */
    public const ACTIVE = ['offered', 'accepted', 'picked_up'];

    protected $fillable = [
        'order_id', 'supplier_offer_id', 'courier_id', 'status', 'pickup_lat', 'pickup_lng', 'pickup_label',
        'dropoff_lat', 'dropoff_lng', 'distance_km', 'declined_by', 'offered_at', 'offer_expires_at',
        'accepted_at', 'picked_up_at', 'delivered_at', 'notes',
        'delivery_token', 'delivery_code', 'dropoff_attempts', 'pickup_method', 'dropoff_method', 'courier_fee', 'fee_paid_at',
    ];

    protected $casts = [
        'pickup_lat'       => 'float',
        'pickup_lng'       => 'float',
        'dropoff_lat'      => 'float',
        'dropoff_lng'      => 'float',
        'distance_km'      => 'float',
        'declined_by'      => 'array',
        'offered_at'       => 'datetime',
        'offer_expires_at' => 'datetime',
        'accepted_at'      => 'datetime',
        'picked_up_at'     => 'datetime',
        'delivered_at'     => 'datetime',
        'fee_paid_at'      => 'datetime',
    ];

    public function order()   { return $this->belongsTo(Order::class); }
    public function offer()   { return $this->belongsTo(SupplierOffer::class, 'supplier_offer_id'); }
    public function courier() { return $this->belongsTo(User::class, 'courier_id'); }

    public function purchaseOrder() { return $this->hasOne(PurchaseOrder::class, 'order_id', 'order_id'); }

    /** Ce que le livreur scanne chez le client. */
    public function getDropoffScanUrlAttribute(): ?string
    {
        return $this->delivery_token ? route('courier.dropoff.scan', $this->delivery_token) : null;
    }

    public function getDropoffQrUrlAttribute(): ?string
    {
        return $this->dropoff_scan_url
            ? 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&data=' . urlencode($this->dropoff_scan_url)
            : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }

    public function getMapsUrlAttribute(): ?string
    {
        return $this->dropoff_lat !== null
            ? 'https://www.google.com/maps?q=' . $this->dropoff_lat . ',' . $this->dropoff_lng
            : null;
    }

    public function getPickupMapsUrlAttribute(): ?string
    {
        return $this->pickup_lat !== null
            ? 'https://www.google.com/maps?q=' . $this->pickup_lat . ',' . $this->pickup_lng
            : null;
    }
}
