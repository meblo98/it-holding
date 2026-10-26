<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'latitude',
        'longitude',
        'service_radius_km',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'latitude'          => 'float',
        'longitude'         => 'float',
        'service_radius_km' => 'integer',
        'rating'            => 'float',
        'is_active'         => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }

    /**
     * Get the total purchase amount from this supplier.
     */
    public function getTotalPurchaseAmountAttribute()
    {
        return $this->deliveryNotes()->sum('total_purchase_amount');
    }
}
