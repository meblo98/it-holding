<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionApplication extends Model
{
    protected $fillable = [
        'mission_id',
        'user_id',
        'proposal',
        'proposed_rate',
        'proposed_delay',
        'experience',
        'references',
        'document_path',
        'document_name',
        'status',
        'status_history',
        'admin_notes',
        'agreed_amount',
        'tax_rule_id',
        'withholding_amount',
        'net_amount',
        'paid_at',
    ];

    protected $casts = [
        'proposed_rate' => 'decimal:2',
        'agreed_amount' => 'decimal:2',
        'withholding_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'status_history' => 'array',
        'paid_at' => 'datetime',
    ];

    // Parcours d'une candidature (doc §16), dans l'ordre.
    public const STATUSES = [
        'applied'     => 'Candidature',
        'shortlisted' => 'Présélection',
        'interview'   => 'Entretien',
        'selected'    => 'Sélectionné',
        'contracted'  => 'Contrat',
        'in_progress' => 'En cours',
        'completed'   => 'Terminé',
        'validated'   => 'Validé',
        'paid'        => 'Payé',
        'rejected'    => 'Non retenue',
    ];

    public const TEAM_STATUSES = ['selected', 'contracted', 'in_progress', 'completed', 'validated', 'paid'];

    public const STATUS_CLASSES = [
        'applied'     => 'bg-gray-100 text-gray-700 border-gray-200',
        'shortlisted' => 'bg-blue-50 text-blue-700 border-blue-200',
        'interview'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'selected'    => 'bg-purple-50 text-purple-700 border-purple-200',
        'contracted'  => 'bg-purple-50 text-purple-700 border-purple-200',
        'in_progress' => 'bg-amber-50 text-amber-700 border-amber-200',
        'completed'   => 'bg-teal-50 text-teal-700 border-teal-200',
        'validated'   => 'bg-green-50 text-green-700 border-green-200',
        'paid'        => 'bg-green-100 text-green-800 border-green-300',
        'rejected'    => 'bg-red-50 text-red-700 border-red-200',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taxRule(): BelongsTo
    {
        return $this->belongsTo(TaxRule::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusClassesAttribute(): string
    {
        return self::STATUS_CLASSES[$this->status] ?? self::STATUS_CLASSES['applied'];
    }

    public function isTeamMember(): bool
    {
        return in_array($this->status, self::TEAM_STATUSES, true);
    }
}
