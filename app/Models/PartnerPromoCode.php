<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerPromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'code',
        'discount_percent',
        'commission_percent',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'promo_code_id');
    }

    public function commissions()
    {
        return $this->hasMany(PartnerCommission::class, 'promo_code_id');
    }
}
