<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionTask extends Model
{
    protected $fillable = [
        'mission_id',
        'title',
        'description',
        'assigned_to',
        'status',
        'due_date',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public const STATUSES = [
        'todo'        => 'À faire',
        'in_progress' => 'En cours',
        'done'        => 'Terminée',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
