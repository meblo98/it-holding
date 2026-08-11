<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingAsset extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'category',
    ];
}
