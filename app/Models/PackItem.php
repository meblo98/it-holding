<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_id',
        'product_id',
        'quantity',
    ];

    public function pack()
    {
        return $this->belongsTo(Product::class, 'pack_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
