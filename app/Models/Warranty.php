<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Warranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'client_id', 'product_id', 'invoice_id', 'order_id',
        'product_name', 'serial_number', 'client_name', 'client_phone',
        'purchase_date', 'expiry_date', 'duration_months',
        'type', 'status', 'coverage_notes', 'exclusions', 'notes',
    ];

    protected $casts = [
        'purchase_date'   => 'date',
        'expiry_date'     => 'date',
        'duration_months' => 'integer',
    ];

    // ── Relations ───────────────────────────────────────────────────────────
    public function client()   { return $this->belongsTo(Client::class); }
    public function product()  { return $this->belongsTo(Product::class); }
    public function invoice()  { return $this->belongsTo(Invoice::class); }
    public function order()    { return $this->belongsTo(Order::class); }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopeActive($q)         { return $q->where('status', 'active'); }
    public function scopeExpired($q)        { return $q->where('status', 'expired'); }
    public function scopeExpiringSoon($q, $days = 30)
    {
        return $q->where('status', 'active')
                 ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    // ── Accessors ────────────────────────────────────────────────────────────
    public function getDaysRemainingAttribute(): int
    {
        return max(0, (int) now()->diffInDays($this->expiry_date, false));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date->isPast();
    }

    // ── Auto-generate warranty number ─────────────────────────────────────
    public static function generateNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        do {
            $number = 'GAR-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            $count++;
        } while (self::where('number', $number)->exists());
        return $number;
    }

    // ── Type labels ──────────────────────────────────────────────────────────
    public static function typeLabel(string $type): string
    {
        return match($type) {
            'standard'  => 'Standard',
            'extended'  => 'Étendue',
            'care_plus' => 'IT HOLDING CARE+',
            default     => ucfirst($type),
        };
    }

    // ── Status badge config ───────────────────────────────────────────────
    public static function statusConfig(string $status): array
    {
        return match($status) {
            'active'  => ['label' => 'Active',   'classes' => 'bg-green-50 text-green-700 border-green-200'],
            'expired' => ['label' => 'Expirée',  'classes' => 'bg-red-50 text-red-700 border-red-200'],
            'claimed' => ['label' => 'Réclamée', 'classes' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'void'    => ['label' => 'Annulée',  'classes' => 'bg-gray-100 text-gray-600 border-gray-200'],
            default   => ['label' => ucfirst($status), 'classes' => 'bg-gray-100 text-gray-600 border-gray-200'],
        };
    }
}
