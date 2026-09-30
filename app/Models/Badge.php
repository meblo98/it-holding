<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'color',
        'level',
        'validity_months',
        'active',
    ];

    protected $casts = [
        'level' => 'integer',
        'validity_months' => 'integer',
        'active' => 'boolean',
    ];

    // Classes complètes (pas de concaténation) pour que Tailwind CDN les détecte.
    public const COLOR_CLASSES = [
        'green'  => 'bg-green-50 text-green-700 border-green-200',
        'blue'   => 'bg-blue-50 text-blue-700 border-blue-200',
        'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
        'orange' => 'bg-orange-50 text-orange-700 border-orange-200',
        'yellow' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'amber'  => 'bg-amber-50 text-amber-700 border-amber-200',
        'red'    => 'bg-red-50 text-red-700 border-red-200',
        'gray'   => 'bg-gray-50 text-gray-700 border-gray-200',
    ];

    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    public function getColorClassesAttribute(): string
    {
        return self::COLOR_CLASSES[$this->color] ?? self::COLOR_CLASSES['gray'];
    }
}
