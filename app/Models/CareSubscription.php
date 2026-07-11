<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'client_id', 'warranty_id',
        'client_name', 'client_phone', 'product_name', 'serial_number',
        'plan', 'start_date', 'end_date', 'duration_months', 'price',
        'has_priority_support', 'has_repair_discount', 'repair_discount_pct',
        'has_parts_discount', 'parts_discount_pct', 'has_home_service',
        'status', 'payment_status', 'amount_paid', 'notes',
    ];

    protected $casts = [
        'start_date'           => 'date',
        'end_date'             => 'date',
        'price'                => 'decimal:2',
        'amount_paid'          => 'decimal:2',
        'has_priority_support' => 'boolean',
        'has_repair_discount'  => 'boolean',
        'has_parts_discount'   => 'boolean',
        'has_home_service'     => 'boolean',
        'duration_months'      => 'integer',
        'repair_discount_pct'  => 'integer',
        'parts_discount_pct'   => 'integer',
    ];

    // ── Relations ───────────────────────────────────────────────────────────
    public function client()  { return $this->belongsTo(Client::class); }
    public function warranty() { return $this->belongsTo(Warranty::class); }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopeActive($q)          { return $q->where('status', 'active'); }
    public function scopeExpiringSoon($q, $days = 30)
    {
        return $q->where('status', 'active')
                 ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }

    // ── Accessors ─────────────────────────────────────────────────────────
    public function getDaysRemainingAttribute(): int
    {
        return max(0, (int) now()->diffInDays($this->end_date, false));
    }

    // ── Static helpers ───────────────────────────────────────────────────────
    public static function generateNumber(): string
    {
        $count = self::whereYear('created_at', date('Y'))->count() + 1;
        return 'CARE-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function planConfig(string $plan): array
    {
        return match($plan) {
            'standard'   => ['label' => 'Standard',   'classes' => 'bg-blue-50 text-blue-700 border-blue-200',   'icon' => '🛡️'],
            'premium'    => ['label' => 'Premium',     'classes' => 'bg-purple-50 text-purple-700 border-purple-200', 'icon' => '⭐'],
            'enterprise' => ['label' => 'Entreprise',  'classes' => 'bg-gold-50 text-gold-700 border-gold-200',   'icon' => '🏆'],
            default      => ['label' => ucfirst($plan), 'classes' => 'bg-gray-100 text-gray-600 border-gray-200', 'icon' => '📋'],
        };
    }

    public static function statusConfig(string $status): array
    {
        return match($status) {
            'active'    => ['label' => 'Actif',     'classes' => 'bg-green-50 text-green-700 border-green-200'],
            'expired'   => ['label' => 'Expiré',    'classes' => 'bg-red-50 text-red-700 border-red-200'],
            'cancelled' => ['label' => 'Annulé',    'classes' => 'bg-gray-100 text-gray-500 border-gray-200'],
            'suspended' => ['label' => 'Suspendu',  'classes' => 'bg-orange-50 text-orange-700 border-orange-200'],
            default     => ['label' => ucfirst($status), 'classes' => 'bg-gray-100 text-gray-600 border-gray-200'],
        };
    }
}
