<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'client_id', 'client_name', 'client_phone', 'client_address',
        'type', 'start_date', 'end_date', 'price', 'billing_period',
        'interventions_included', 'interventions_used', 'response_time_hours', 'scope',
        'status', 'payment_status', 'amount_paid', 'notes',
    ];

    protected $casts = [
        'start_date'             => 'date',
        'end_date'               => 'date',
        'price'                  => 'decimal:2',
        'amount_paid'            => 'decimal:2',
        'interventions_included' => 'integer',
        'interventions_used'     => 'integer',
        'response_time_hours'    => 'integer',
    ];

    // ── Relations ───────────────────────────────────────────────────────────
    public function client() { return $this->belongsTo(Client::class); }

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

    public function getInterventionsRemainingAttribute(): int
    {
        return max(0, $this->interventions_included - $this->interventions_used);
    }

    public function getAmountRemainingAttribute(): float
    {
        return max(0, $this->price - $this->amount_paid);
    }

    // ── Static helpers ───────────────────────────────────────────────────────
    public static function generateNumber(): string
    {
        $count = self::whereYear('created_at', date('Y'))->count() + 1;
        return 'CONT-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function statusConfig(string $status): array
    {
        return match($status) {
            'draft'     => ['label' => 'Brouillon',  'classes' => 'bg-gray-100 text-gray-600 border-gray-200'],
            'active'    => ['label' => 'Actif',       'classes' => 'bg-green-50 text-green-700 border-green-200'],
            'expired'   => ['label' => 'Expiré',      'classes' => 'bg-red-50 text-red-700 border-red-200'],
            'cancelled' => ['label' => 'Annulé',      'classes' => 'bg-gray-100 text-gray-500 border-gray-200'],
            'suspended' => ['label' => 'Suspendu',    'classes' => 'bg-orange-50 text-orange-700 border-orange-200'],
            default     => ['label' => ucfirst($status), 'classes' => 'bg-gray-100 text-gray-600 border-gray-200'],
        };
    }

    public static function typeLabel(string $type): string
    {
        return match($type) {
            'basic'    => 'Basique',
            'standard' => 'Standard',
            'premium'  => 'Premium',
            'custom'   => 'Sur Mesure',
            default    => ucfirst($type),
        };
    }
}
