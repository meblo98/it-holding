<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    protected $fillable = [
        'number',
        'user_id',
        'badge_id',
        'issued_at',
        'expires_at',
        'status',
        'issued_by',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Active and not past its expiry date.
     */
    public function scopeValid($q)
    {
        return $q->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString()));
    }

    public function isValid(): bool
    {
        return $this->status === 'active' && (!$this->expires_at || !$this->expires_at->isPast() || $this->expires_at->isToday());
    }

    /**
     * Human status for display: valide / expiré / révoqué.
     */
    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'revoked') {
            return 'revoked';
        }

        return $this->isValid() ? 'valid' : 'expired';
    }

    public static function generateNumber(): string
    {
        $count = self::count() + 1;
        do {
            $number = 'ITH-BDG-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            $count++;
        } while (self::where('number', $number)->exists());

        return $number;
    }
}
