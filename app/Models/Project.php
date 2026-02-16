<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'client',
        'completion_date',
        'technologies',
        'url',
        'image',
        'gallery',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'technologies' => 'array',
        'gallery' => 'array',
    ];
}
