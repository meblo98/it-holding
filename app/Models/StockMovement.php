<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'source',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'quantity'   => 'integer',
        'user_id'    => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
