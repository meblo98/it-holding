<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'supplier_name',
        'supplier_id',
        'delivery_date',
        'notes',
        'total_purchase_amount',
        'type',
        'status',
        'customer_name',
        'customer_phone',
        'customer_address',
        'order_id',
        'invoice_id',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_purchase_amount' => 'decimal:2',
        'supplier_id' => 'integer',
        'order_id' => 'integer',
        'invoice_id' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
