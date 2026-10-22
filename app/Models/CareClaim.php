<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareClaim extends Model
{
    /** Les 12 étapes du suivi (cahier des charges §8.5). */
    public const FLOW = [
        'received', 'checking', 'awaiting_device', 'diagnosing', 'quote_preparing', 'quote_pending',
        'coverage_approved', 'repairing', 'quality_check', 'ready', 'returned', 'closed',
    ];

    public const LABELS = [
        'received'          => 'Demande reçue',
        'checking'          => 'Vérification de l’abonnement',
        'awaiting_device'   => 'Appareil à déposer ou à collecter',
        'diagnosing'        => 'Diagnostic en cours',
        'quote_preparing'   => 'Devis en préparation',
        'quote_pending'     => 'Devis à approuver',
        'coverage_approved' => 'Prise en charge validée',
        'coverage_refused'  => 'Prise en charge refusée',
        'repairing'         => 'Réparation en cours',
        'quality_check'     => 'Contrôle qualité',
        'ready'             => 'Appareil prêt',
        'returned'          => 'Appareil restitué',
        'closed'            => 'Dossier clôturé',
        'refused'           => 'Demande refusée',
        'cancelled'         => 'Demande annulée',
    ];

    /** Statuts terminaux : le dossier n'est plus ouvert. */
    public const TERMINAL = ['closed', 'cancelled', 'refused', 'coverage_refused'];

    /** Passages autorisés à l'équipe (les étapes suivantes seront ajoutées avec le devis). */
    public const TRANSITIONS = [
        'received'        => ['checking'],
        'checking'        => ['awaiting_device'],
        'awaiting_device' => ['diagnosing'],
    ];

    /** Le client peut annuler tant que l'appareil n'a pas été diagnostiqué. */
    public const CLIENT_CANCELLABLE = ['received', 'checking', 'awaiting_device'];

    protected $fillable = [
        'number', 'care_subscription_id', 'user_id', 'symptoms', 'description', 'started_at',
        'drop_mode', 'contact_phone', 'photos', 'video_path', 'status', 'refusal_reason',
    ];

    protected $casts = [
        'started_at' => 'date',
        'photos'     => 'array',
    ];

    public function subscription() { return $this->belongsTo(CareSubscription::class, 'care_subscription_id'); }
    public function user()         { return $this->belongsTo(User::class); }
    public function events()       { return $this->hasMany(CareClaimEvent::class)->orderBy('created_at')->orderBy('id'); }

    public function scopeOpen($q)
    {
        return $q->whereNotIn('status', self::TERMINAL);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }

    public function getIsOpenAttribute(): bool
    {
        return !in_array($this->status, self::TERMINAL, true);
    }

    public function getCanCancelAttribute(): bool
    {
        return in_array($this->status, self::CLIENT_CANCELLABLE, true);
    }

    public function getDropLabelAttribute(): string
    {
        return $this->drop_mode === 'collecte' ? 'Collecte de l’appareil' : 'Dépôt à l’atelier';
    }

    public function logEvent(string $status, ?string $note = null, ?int $userId = null): CareClaimEvent
    {
        return $this->events()->create([
            'status'     => $status,
            'note'       => $note,
            'user_id'    => $userId,
            'created_at' => now(),
        ]);
    }

    public static function generateNumber(): string
    {
        $count = self::whereYear('created_at', date('Y'))->count() + 1;

        return 'REP-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
