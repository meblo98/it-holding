<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('images')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|max:2048',
            'active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['active'] = $request->boolean('active');

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
        return view('admin.products.edit', compact('product'));
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
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|max:2048',
            'active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['active'] = $request->boolean('active');


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
