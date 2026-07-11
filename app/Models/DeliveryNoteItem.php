<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_note_id',
        'product_id',
        'product_name',
        'quantity',
        'purchase_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
