<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerProspect extends Model
{
    protected $fillable = [
        'partner_id',
        'name',
        'phone',
        'email',
        'company',
        'need',
        'budget',
        'status',
        'notes',
        'next_action_at',
        'next_action_description',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'next_action_at' => 'datetime',
    ];

    /**
     * Get the partner that owns the prospect.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }
}
