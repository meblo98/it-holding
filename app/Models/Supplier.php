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
    ];

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
