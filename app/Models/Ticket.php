<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'client_id', 'warranty_id', 'product_id', 'assigned_to',
        'client_name', 'client_phone', 'client_email',
        'product_name', 'serial_number',
        'title', 'description', 'status', 'priority', 'type',
        'diagnosis', 'intervention_notes', 'parts_used',
        'repair_cost', 'covered_by_warranty',
        'opened_at', 'resolved_at', 'closed_at', 'scheduled_date', 'notes',
    ];

    protected $casts = [
        'repair_cost'          => 'decimal:2',
        'covered_by_warranty'  => 'boolean',
        'opened_at'            => 'datetime',
        'resolved_at'          => 'datetime',
        'closed_at'            => 'datetime',
        'scheduled_date'       => 'date',
    ];

    // ── Relations ───────────────────────────────────────────────────────────
    public function client()      { return $this->belongsTo(Client::class); }
    public function warranty()    { return $this->belongsTo(Warranty::class); }
    public function product()     { return $this->belongsTo(Product::class); }
    public function technician()  { return $this->belongsTo(User::class, 'assigned_to'); }
    public function attachments() { return $this->hasMany(TicketAttachment::class); }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopeOpen($q)        { return $q->where('status', 'open'); }
    public function scopeInProgress($q)  { return $q->whereIn('status', ['diagnosed', 'in_progress', 'waiting_parts']); }
    public function scopeResolved($q)    { return $q->whereIn('status', ['resolved', 'closed']); }

    // ── Static helpers ───────────────────────────────────────────────────────
    public static function generateNumber(): string
    {
        $count = self::whereYear('created_at', date('Y'))->count() + 1;
        return 'SAV-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function statusConfig(string $status): array
    {
        return match($status) {
            'open'          => ['label' => 'Ouvert',           'classes' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'diagnosed'     => ['label' => 'Diagnostiqué',     'classes' => 'bg-purple-50 text-purple-700 border-purple-200'],
            'in_progress'   => ['label' => 'En cours',         'classes' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'waiting_parts' => ['label' => 'Attente pièces',   'classes' => 'bg-orange-50 text-orange-700 border-orange-200'],
            'resolved'      => ['label' => 'Résolu',           'classes' => 'bg-green-50 text-green-700 border-green-200'],
            'closed'        => ['label' => 'Clôturé',          'classes' => 'bg-gray-100 text-gray-600 border-gray-200'],
            'cancelled'     => ['label' => 'Annulé',           'classes' => 'bg-red-50 text-red-600 border-red-200'],
            default         => ['label' => ucfirst($status),   'classes' => 'bg-gray-100 text-gray-600 border-gray-200'],
        };
    }

    public static function priorityConfig(string $priority): array
    {
        return match($priority) {
            'urgent' => ['label' => '🔴 Urgent',   'classes' => 'text-red-700'],
            'high'   => ['label' => '🟠 Élevé',    'classes' => 'text-orange-600'],
            'normal' => ['label' => '🟡 Normal',   'classes' => 'text-amber-600'],
            'low'    => ['label' => '🟢 Faible',   'classes' => 'text-green-600'],
            default  => ['label' => ucfirst($priority), 'classes' => 'text-gray-500'],
        };
    }
}
