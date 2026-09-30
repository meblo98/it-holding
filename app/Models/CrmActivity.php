<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmActivity extends Model
{
    protected $fillable = [
        'crm_deal_id',
        'user_id',
        'type',
        'body',
    ];

    public const TYPE_LABELS = [
        'note'         => 'Note',
        'call'         => 'Appel',
        'email'        => 'Email',
        'whatsapp'     => 'WhatsApp',
        'meeting'      => 'Rendez-vous',
        'stage_change' => 'Étape',
        'ai'           => 'Assistant IA',
        'quote'        => 'Devis',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(CrmDeal::class, 'crm_deal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    /**
     * Rend le Markdown d'une réponse IA en HTML sûr : tout HTML brut est
     * supprimé et les liens dangereux (javascript:) sont neutralisés.
     */
    public static function safeMarkdown(string $text): string
    {
        return \Illuminate\Support\Str::markdown($text, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
