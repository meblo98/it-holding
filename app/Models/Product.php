<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'specs',
        'fiche_technique',
        'price',
        'purchase_price',
        'stock',
        'available_at',
        'warranty_duration_months',
        'image',
        'active',
        'promo_price',
        'blackfriday',
        'category_id',
        'brand_id',
        'condition',
        'wholesale_qty',
        'wholesale_discount_rate',
        'wholesale_discount_limit',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'stock' => 'integer',
        'available_at' => 'date',
        'warranty_duration_months' => 'integer',
        'active' => 'boolean',
        'blackfriday' => 'boolean',
        'wholesale_qty' => 'integer',
        'wholesale_discount_rate' => 'decimal:2',
        'wholesale_discount_limit' => 'decimal:2',
        'specs' => 'array',
    ];

    /**
     * Compute wholesale dynamic unit price based on item quantity
     */
    public static function calculateWholesalePrice(Product $product, int $quantity)
    {
        $basePrice = $product->promo_price && $product->promo_price > 0 && $product->promo_price < $product->price
            ? $product->promo_price
            : $product->price;

        $wholesaleQty = $product->wholesale_qty ?? 5;
        if ($quantity >= $wholesaleQty) {
            $discountRate = $product->wholesale_discount_rate ?? 10.00;
            $rawDiscount = $basePrice * ($discountRate / 100);

            if ($product->wholesale_discount_limit !== null && $product->wholesale_discount_limit > 0) {
                $rawDiscount = min($rawDiscount, $product->wholesale_discount_limit);
            }

            return max(0.00, $basePrice - $rawDiscount);
        }

        return $basePrice;
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function isPreorderable()
    {
        return $this->stock <= 0 && !is_null($this->available_at);
    }
}
