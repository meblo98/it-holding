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
        'is_preorder',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'options' => 'array',
        'is_preorder' => 'boolean',
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
                $duration = ($orderItem->product && $orderItem->product->warranty_duration_months !== null)
                    ? $orderItem->product->warranty_duration_months
                    : 12;

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
                    'expiry_date'     => \Carbon\Carbon::parse($order->created_at ?: now())->addMonths($duration)->format('Y-m-d'),
                    'duration_months' => $duration,
                    'type'            => 'standard',
                    'status'          => 'active',
                    'coverage_notes'  => "Garantie standard de " . ($duration % 12 === 0 ? ($duration / 12) . " an(s)" : $duration . " mois") . " générée automatiquement suite à la commande #" . $order->id,
                    'notes'           => "Générée automatiquement suite à la commande #" . $order->id,
                ]);
            }
        });
    }
}
