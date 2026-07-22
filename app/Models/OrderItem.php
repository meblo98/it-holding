<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'purchase_price',
        'options',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'options' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::created(function ($orderItem) {
            $order = $orderItem->order;
            if ($order) {
                \App\Models\Warranty::create([
                    'number'          => \App\Models\Warranty::generateNumber(),
                    'client_id'       => $order->client_id,
                    'product_id'      => $orderItem->product_id,
                    'order_id'        => $order->id,
                    'product_name'    => $orderItem->product ? $orderItem->product->name : ('Produit #' . $orderItem->product_id),
                    'serial_number'   => null,
                    'client_name'     => $order->customer_name,
                    'client_phone'    => $order->customer_phone,
                    'purchase_date'   => $order->created_at ?: now(),
                    'expiry_date'     => \Carbon\Carbon::parse($order->created_at ?: now())->addMonths(12)->format('Y-m-d'),
                    'duration_months' => 12,
                    'type'            => 'standard',
                    'status'          => 'active',
                    'coverage_notes'  => "Garantie standard de 1 an générée automatiquement suite à la commande #" . $order->id,
                    'notes'           => "Générée automatiquement suite à la commande #" . $order->id,
                ]);
            }
        });
    }
}
