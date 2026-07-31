<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Product::with('images')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(10)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Generate a unique slug
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Product::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'purchase_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'available_at' => 'nullable|date',
            'warranty_duration_months' => 'required|integer|min:0|max:120',
            'blackfriday' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'condition' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'wholesale_qty' => 'nullable|integer|min:2',
            'wholesale_discount_rate' => 'nullable|numeric|min:0|max:100',
            'wholesale_discount_limit' => 'nullable|numeric|min:0',
        ]);

        $validated['slug'] = $this->generateUniqueSlug($request->name);
        $validated['active'] = $request->boolean('active');
        $validated['blackfriday'] = $request->boolean('blackfriday');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product = Product::create($validated);

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $file) {
                    if (!$file->isValid()) continue;
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Product images upload error (store): ' . $e->getMessage());
                return redirect()->back()->with('error', 'Erreur lors de l\'upload des images.')->withInput();
            }
        }
        return redirect()->route('admin.products.index')->with('success', 'Produit créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'purchase_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'available_at' => 'nullable|date',
            'warranty_duration_months' => 'required|integer|min:0|max:120',
            'blackfriday' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'condition' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'wholesale_qty' => 'nullable|integer|min:2',
            'wholesale_discount_rate' => 'nullable|numeric|min:0|max:100',
            'wholesale_discount_limit' => 'nullable|numeric|min:0',
        ]);

        // Only regenerate slug if the name has changed, to preserve existing URLs (SEO)
        if ($request->name !== $product->name) {
            $validated['slug'] = $this->generateUniqueSlug($request->name, $product->id);
        }
        $validated['active'] = $request->boolean('active');
        $validated['blackfriday'] = $request->boolean('blackfriday');


        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);

        // Handle new multiple images upload
        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $file) {
                    if (!$file->isValid()) continue;
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Product images upload error (update): ' . $e->getMessage());
                return redirect()->back()->with('error', 'Erreur lors de l\'upload des images.')->withInput();
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);


        // delete main image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // delete related images files
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produit supprimé avec succès.');
    }

    /**
     * Remove a single product image.
     */
    public function destroyImage(string $productId, string $imageId)
    {
        $product = Product::findOrFail($productId);
        $image = ProductImage::where('product_id', $product->id)->where('id', $imageId)->firstOrFail();

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return redirect()->back()->with('success', 'Image supprimée.');
    }
}
