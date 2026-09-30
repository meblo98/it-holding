<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Affaire du pipeline commercial central (doc §43).
 */
class CrmDeal extends Model
{
    protected $fillable = [
        'reference',
        'title',
        'client_id',
        'contact_name',
        'contact_phone',
        'contact_email',
        'company',
        'need',
        'source',
        'partner_id',
        'partner_prospect_id',
        'stage',
        'amount',
        'quote_id',
        'owner_id',
        'next_action_at',
        'next_action_note',
        'lost_reason',
        'closed_at',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'next_action_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Pipeline du §43, dans l'ordre, plus « Perdu ».
    public const STAGES = [
        'new'         => 'Nouveau',
        'contacted'   => 'Contacté',
        'qualified'   => 'Qualifié',
        'quote'       => 'Devis',
        'negotiation' => 'Négociation',
        'won'         => 'Gagné',
        'delivery'    => 'Livraison',
        'loyalty'     => 'Fidélisation',
        'lost'        => 'Perdu',
    ];

    // Affaires encore à conclure (valeur du pipeline).
    public const OPEN_STAGES = ['new', 'contacted', 'qualified', 'quote', 'negotiation'];

    // Affaires conclues (gagnées puis suivies).
    public const WON_STAGES = ['won', 'delivery', 'loyalty'];

    public const STAGE_CLASSES = [
        'new'         => 'bg-gray-100 text-gray-700 border-gray-200',
        'contacted'   => 'bg-blue-50 text-blue-700 border-blue-200',
        'qualified'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'quote'       => 'bg-purple-50 text-purple-700 border-purple-200',
        'negotiation' => 'bg-amber-50 text-amber-700 border-amber-200',
        'won'         => 'bg-green-50 text-green-700 border-green-200',
        'delivery'    => 'bg-teal-50 text-teal-700 border-teal-200',
        'loyalty'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'lost'        => 'bg-red-50 text-red-700 border-red-200',
    ];

    public const SOURCES = [
        'website'    => 'Site web',
        'partner'    => 'Partenaire commercial',
        'apporteur'  => "Apporteur d'affaires",
        'commercial' => 'Prospection commerciale',
        'referral'   => 'Recommandation client',
        'event'      => 'Salon / événement',
        'other'      => 'Autre',
    ];

    public const ACTIVITY_TYPES = [
        'note'     => 'Note',
        'call'     => 'Appel',
        'email'    => 'Email',
        'whatsapp' => 'WhatsApp',
        'meeting'  => 'Rendez-vous',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function partnerProspect(): BelongsTo
    {
        return $this->belongsTo(PartnerProspect::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class);
    }

    public function scopeOpen($q)
    {
        return $q->whereIn('stage', self::OPEN_STAGES);
    }

    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }

    public function getStageClassesAttribute(): string
    {
        return self::STAGE_CLASSES[$this->stage] ?? self::STAGE_CLASSES['new'];
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->client) {
            return $this->client->company_name ?: trim($this->client->first_name . ' ' . $this->client->last_name);
        }

        return $this->company ?: ($this->contact_name ?: '—');
    }

    public function isFollowUpLate(): bool
    {
        return $this->next_action_at && $this->next_action_at->isPast() && !in_array($this->stage, ['lost', 'loyalty'], true);
    }

    public static function generateReference(): string
    {
        $count = self::count() + 1;
        do {
            $ref = 'ITH-CRM-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            $count++;
        } while (self::where('reference', $ref)->exists());

        return $ref;
    }
}
