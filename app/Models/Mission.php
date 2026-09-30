<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    protected $fillable = [
        'reference',
        'project_ref',
        'title',
        'description',
        'city',
        'location',
        'budget',
        'duration',
        'required_skills',
        'equipment',
        'start_date',
        'apply_until',
        'positions',
        'target_types',
        'status',
        'client_id',
        'created_by',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'required_skills' => 'array',
        'target_types' => 'array',
        'start_date' => 'date',
        'apply_until' => 'date',
        'positions' => 'integer',
    ];

    public const STATUSES = [
        'draft'       => 'Brouillon',
        'open'        => 'Ouverte aux candidatures',
        'closed'      => 'Candidatures closes',
        'in_progress' => 'En cours',
        'completed'   => 'Terminée',
        'cancelled'   => 'Annulée',
    ];

    // Catégories du réseau autorisées à postuler (doc §6-7).
    public const ELIGIBLE_PARTNER_TYPES = ['freelance', 'prestataire'];

    public function applications(): HasMany
    {
        return $this->hasMany(MissionApplication::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(MissionTask::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MissionMessage::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Candidatures retenues = équipe du projet.
     */
    public function teamApplications(): HasMany
    {
        return $this->applications()->whereIn('status', MissionApplication::TEAM_STATUSES);
    }

    /**
     * Catégories ciblées par la mission (doc §6-7) : vide = freelances et prestataires.
     */
    public function targetTypes(): array
    {
        return $this->target_types ?: self::ELIGIBLE_PARTNER_TYPES;
    }

    public function targetsType(?string $partnerType): bool
    {
        return in_array($partnerType, $this->targetTypes(), true);
    }

    public function getTargetLabelAttribute(): string
    {
        $types = $this->targetTypes();
        if (count($types) === count(self::ELIGIBLE_PARTNER_TYPES)) {
            return 'Freelances & prestataires';
        }

        return $types === ['prestataire'] ? 'Prestataires uniquement' : 'Freelances uniquement';
    }

    public function scopeTargeting($q, string $partnerType)
    {
        return $q->where(fn ($q) => $q->whereNull('target_types')->orWhereJsonContains('target_types', $partnerType));
    }

    public function isOpenForApplications(): bool
    {
        return $this->status === 'open' && (!$this->apply_until || !$this->apply_until->isPast() || $this->apply_until->isToday());
    }

    /**
     * Espace projet accessible à l'équipe retenue (et au staff), doc §18-20.
     */
    public function isTeamMember(User $user): bool
    {
        return $this->teamApplications()->where('user_id', $user->id)->exists();
    }

    public function hasWorkspace(): bool
    {
        return $this->project_ref !== null;
    }

    public static function generateReference(): string
    {
        $count = self::count() + 1;
        do {
            $ref = 'ITH-MIS-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            $count++;
        } while (self::where('reference', $ref)->exists());

        return $ref;
    }

    public static function generateProjectRef(): string
    {
        $year = date('Y');
        $count = self::where('project_ref', 'like', "ITH-{$year}-%")->count() + 1;
        do {
            $ref = "ITH-{$year}-" . str_pad($count, 5, '0', STR_PAD_LEFT);
            $count++;
        } while (self::where('project_ref', $ref)->exists());

        return $ref;
    }
}
