<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'number', 'order_id', 'supplier_id', 'supplier_offer_id', 'supplier_token', 'pickup_token', 'pickup_code',
        'agreed_price', 'delay_hours', 'status', 'handed_over_at', 'pickup_attempts',
    ];

    protected $casts = ['handed_over_at' => 'datetime'];

    public function order()    { return $this->belongsTo(Order::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function offer()    { return $this->belongsTo(SupplierOffer::class, 'supplier_offer_id'); }

    public static function generateNumber(): string
    {
        $count = self::whereYear('created_at', date('Y'))->count() + 1;

        return 'BC-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /** Ce que le livreur scanne : ouvre la validation de retrait. */
    public function getScanUrlAttribute(): string
    {
        return route('courier.pickup.scan', $this->pickup_token);
    }

    public function getQrImageUrlAttribute(): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&data=' . urlencode($this->scan_url);
    }
}
