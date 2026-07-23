<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'order_id',
        'promo_code_id',
        'order_amount',
        'commission_amount',
        'status', // pending, paid, cancelled
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function promoCode()
    {
        return $this->belongsTo(PartnerPromoCode::class, 'promo_code_id');
    }
}
