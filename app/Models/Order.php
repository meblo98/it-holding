<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'partner_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'promo_code_id',
        'discount_amount',
        'tax_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'client_id' => 'integer',
        'partner_id' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function promoCode()
    {
        return $this->belongsTo(PartnerPromoCode::class, 'promo_code_id');
    }

    public function commissions()
    {
        return $this->hasMany(PartnerCommission::class, 'order_id');
    }

    public function hasPreorder()
    {
        return $this->items()->where('is_preorder', true)->exists();
    }
}
