<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'amount',
        'method',
        'account_details',
        'status',
        'payment_reference',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public const METHODS = [
        'wave'          => 'Wave',
        'orange_money'  => 'Orange Money',
        'free_money'    => 'Free Money',
        'bank_transfer' => 'Virement bancaire',
    ];

    public const STATUSES = [
        'requested' => 'Demandé',
        'approved'  => 'Approuvé',
        'paid'      => 'Versé',
        'rejected'  => 'Refusé',
    ];

    public const STATUS_CLASSES = [
        'requested' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'approved'  => 'bg-blue-50 text-blue-700 border-blue-200',
        'paid'      => 'bg-green-50 text-green-700 border-green-200',
        'rejected'  => 'bg-red-50 text-red-700 border-red-200',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusClassesAttribute(): string
    {
        return self::STATUS_CLASSES[$this->status] ?? self::STATUS_CLASSES['requested'];
    }

    public static function generateReference(): string
    {
        $count = self::count() + 1;
        do {
            $ref = 'ITH-RET-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            $count++;
        } while (self::where('reference', $ref)->exists());

        return $ref;
    }
}
