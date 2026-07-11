<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'company_name', 'rccm', 'ninea', 'sector',
        'email', 'phone', 'phone2', 'address', 'city', 'country',
        'is_professional', 'credit_limit', 'current_balance', 'wallet_balance',
        'payment_terms', 'user_id', 'notes',
    ];

    protected $casts = [
        'is_professional' => 'boolean',
        'credit_limit'    => 'decimal:2',
        'current_balance' => 'decimal:2',
        'wallet_balance'  => 'decimal:2',
    ];

    // ── Full name accessor ──────────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->company_name
            ? $this->company_name . ' (' . $this->full_name . ')'
            : $this->full_name;
    }

    // ── Relations ───────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}
