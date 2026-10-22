<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerAiImage extends Model
{
    protected $fillable = ['user_id', 'type', 'prompt', 'product_id', 'path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
