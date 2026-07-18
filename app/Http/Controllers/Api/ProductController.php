<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('active', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->with(['category', 'brand'])->paginate(15);

        $products->getCollection()->transform(function ($product) {
            return $this->formatProduct($product);
        });

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'images', 'options'])->findOrFail($id);

        return response()->json([
            'product' => $this->formatProduct($product, true)
        ]);
    }

    private function formatProduct($product, $includeDetails = false)
    {
        $imageUrl = $product->image 
            ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image)) 
            : asset('images/logo.png');

        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => $product->price,
            'promo_price' => $product->promo_price,
            'stock' => $product->stock,
            'image_url' => $imageUrl,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ] : null,
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
            ] : null,
            'condition' => $product->condition,
            'wholesale_qty' => $product->wholesale_qty,
            'wholesale_discount_rate' => $product->wholesale_discount_rate,
        ];

        if ($includeDetails) {
            $data['gallery'] = $product->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'url' => str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path),
                ];
            });

            $data['options'] = $product->options->map(function ($opt) {
                return [
                    'id' => $opt->id,
                    'name' => $opt->option_name,
                    'values' => is_array($opt->option_values) ? $opt->option_values : json_decode($opt->option_values, true),
                    'price' => $opt->price_modifier,
                ];
            });
        }

        return $data;
    }
}
