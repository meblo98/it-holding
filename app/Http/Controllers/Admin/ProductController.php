<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JsonException;

class ProductController extends Controller
{
    private const IMPORT_FIELDS = [
        'name',
        'slug',
        'description',
        'price',
        'purchase_price',
        'stock',
        'available_at',
        'warranty_duration_months',
        'warranty_coverage_percent',
        'warranty_coverage_scope',
        'active',
        'is_pack',
        'promo_price',
        'blackfriday',
        'category_id',
        'brand_id',
        'condition',
        'wholesale_qty',
        'wholesale_discount_rate',
        'wholesale_discount_limit',
        'specs',
        'pack_items',
    ];

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

    public function export(string $format)
    {
        $products = Product::with('packItems.product')->orderBy('id')->get();
        $rows = $products->map(fn (Product $product) => $this->exportRow($product));

        if ($format === 'json') {
            return response()->json(['products' => $rows])
                ->withHeaders(['Content-Disposition' => 'attachment; filename="produits.json"']);
        }

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, self::IMPORT_FIELDS, ';', '"', '');

            foreach ($rows as $row) {
                fputcsv($output, array_map(function ($field) use ($row) {
                    $value = $row[$field] ?? null;
                    if (is_bool($value)) {
                        return $value ? '1' : '0';
                    }
                    return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value;
                }, self::IMPORT_FIELDS), ';', '"', '');
            }

            fclose($output);
        }, 'produits.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:json,csv,txt',
        ]);

        $rows = $this->readImportFile($request);
        if (count($rows) < 1 || count($rows) > 1000) {
            throw ValidationException::withMessages([
                'file' => 'Le fichier doit contenir entre 1 et 1 000 produits.',
            ]);
        }

        $validatedRows = [];
        $errors = [];
        $seenSlugs = [];

        foreach ($rows as $index => $row) {
            $line = $index + 1;
            if (!is_array($row) || array_is_list($row)) {
                $errors["products.{$line}"] = "La ligne {$line} doit être un objet produit.";
                continue;
            }

            foreach ($row as $field => $value) {
                if ($value === '') {
                    $row[$field] = null;
                }
            }

            foreach (['specs', 'pack_items'] as $jsonField) {
                if (isset($row[$jsonField]) && is_string($row[$jsonField]) && $row[$jsonField] !== '') {
                    try {
                        $row[$jsonField] = json_decode($row[$jsonField], true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException) {
                        $errors["products.{$line}.{$jsonField}"] = "Le champ {$jsonField} de la ligne {$line} doit contenir un JSON valide.";
                    }
                } elseif (($row[$jsonField] ?? null) === '') {
                    $row[$jsonField] = null;
                }
            }

            $validator = Validator::make($row, [
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'purchase_price' => 'required|numeric|min:0',
                'stock' => 'nullable|integer|min:0',
                'available_at' => 'nullable|date',
                'warranty_duration_months' => 'nullable|integer|min:0|max:120',
                'warranty_coverage_percent' => 'nullable|integer|in:25,50,75',
                'warranty_coverage_scope' => 'nullable|string|max:120',
                'active' => 'nullable|boolean',
                'is_pack' => 'nullable|boolean',
                'promo_price' => 'nullable|numeric|min:0',
                'blackfriday' => 'nullable|boolean',
                'category_id' => 'nullable|integer|exists:categories,id',
                'brand_id' => 'nullable|integer|exists:brands,id',
                'condition' => 'nullable|string|max:255',
                'wholesale_qty' => 'nullable|integer|min:2',
                'wholesale_discount_rate' => 'nullable|numeric|min:0|max:100',
                'wholesale_discount_limit' => 'nullable|numeric|min:0',
                'specs' => 'nullable|array',
                'pack_items' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors["products.{$line}"] = "Ligne {$line} : {$message}";
                }
                continue;
            }

            $row = $validator->validated();
            foreach ($row['pack_items'] ?? [] as $itemIndex => $item) {
                if (!is_array($item)) {
                    $errors["products.{$line}.pack_items.{$itemIndex}"] = "Composition invalide à la ligne {$line}.";
                    continue;
                }

                $itemValidator = Validator::make($item, [
                    'product_slug' => 'required|string|max:255',
                    'quantity' => 'required|integer|min:1',
                ]);
                if ($itemValidator->fails()) {
                    $errors["products.{$line}.pack_items.{$itemIndex}"] = "Composition invalide à la ligne {$line}.";
                }
            }

            if (!empty($row['slug'])) {
                if (isset($seenSlugs[$row['slug']])) {
                    $errors["products.{$line}.slug"] = "Le slug « {$row['slug']} » apparaît plusieurs fois dans le fichier.";
                }
                $seenSlugs[$row['slug']] = true;
            }
            $validatedRows[] = $row;
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($validatedRows) {
            $imported = [];

            foreach ($validatedRows as $row) {
                $slug = $row['slug'] ?? $this->generateUniqueSlug($row['name']);
                $attributes = collect($row)->only(self::IMPORT_FIELDS)->except(['slug', 'pack_items'])->all();
                $attributes['slug'] = $slug;

                $product = Product::where('slug', $slug)->first();
                if (!$product && !array_key_exists('warranty_duration_months', $attributes)) {
                    $attributes['warranty_duration_months'] = 12;
                }

                $isPack = filter_var($attributes['is_pack'] ?? false, FILTER_VALIDATE_BOOLEAN);
                if ($isPack) {
                    $attributes['stock'] = 0;
                }

                $product ??= new Product();
                $product->fill($attributes);
                $product->save();
                $imported[] = [$product, $row];
            }

            foreach ($imported as [$product, $row]) {
                if (!array_key_exists('pack_items', $row) && !array_key_exists('is_pack', $row)) {
                    continue;
                }

                if (!$product->is_pack) {
                    $product->packItems()->delete();
                    continue;
                }

                if (!array_key_exists('pack_items', $row)) {
                    continue;
                }

                $product->packItems()->delete();
                foreach ($row['pack_items'] ?? [] as $item) {
                    $component = Product::where('slug', $item['product_slug'])->first();
                    if (!$component || $component->is_pack || $component->id === $product->id) {
                        throw ValidationException::withMessages([
                            'file' => "Produit « {$product->name} » : composant « {$item['product_slug']} » introuvable ou invalide.",
                        ]);
                    }

                    $product->packItems()->create([
                        'product_id' => $component->id,
                        'quantity' => $item['quantity'],
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', count($validatedRows) . ' produit(s) importé(s) ou mis à jour avec succès.');
    }

    private function exportRow(Product $product): array
    {
        $row = $product->only(array_diff(self::IMPORT_FIELDS, ['pack_items']));
        $row['pack_items'] = $product->packItems->map(fn ($item) => [
            'product_slug' => $item->product?->slug,
            'quantity' => $item->quantity,
        ])->all();

        return $row;
    }

    private function readImportFile(Request $request): array
    {
        $file = $request->file('file');
        $contents = file_get_contents($file->getRealPath());
        if ($contents === false) {
            throw ValidationException::withMessages(['file' => 'Impossible de lire le fichier importé.']);
        }

        if (strtolower($file->getClientOriginalExtension()) === 'json') {
            try {
                $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                throw ValidationException::withMessages(['file' => 'Le fichier JSON est invalide.']);
            }

            if (is_array($decoded) && isset($decoded['products'])) {
                if (!is_array($decoded['products']) || !array_is_list($decoded['products'])) {
                    throw ValidationException::withMessages([
                        'file' => 'La propriété « products » doit contenir un tableau de produits.',
                    ]);
                }
                return $decoded['products'];
            }
            if (is_array($decoded) && array_is_list($decoded)) {
                return $decoded;
            }

            throw ValidationException::withMessages([
                'file' => 'Le fichier JSON doit contenir un tableau de produits ou une propriété « products ».',
            ]);
        }

        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            throw ValidationException::withMessages(['file' => 'Impossible de lire le fichier importé.']);
        }

        $headerLine = fgets($handle);
        if ($headerLine === false) {
            fclose($handle);
            return [];
        }
        $headerLine = preg_replace('/^\xEF\xBB\xBF/', '', $headerLine);
        $semicolonCount = count(str_getcsv($headerLine, ';'));
        $commaCount = count(str_getcsv($headerLine, ','));
        $delimiter = $semicolonCount >= $commaCount ? ';' : ',';
        rewind($handle);

        $headers = fgetcsv($handle, 0, $delimiter, '"', '');
        if (isset($headers[0])) {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]);
        }
        $headers = array_map(fn ($header) => trim((string) $header), $headers ?: []);
        if (!$headers || !in_array('name', $headers, true)) {
            fclose($handle);
            throw ValidationException::withMessages([
                'file' => 'Le tableau doit contenir une colonne « name ».',
            ]);
        }

        $rows = [];
        while (($values = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
            if (count(array_filter($values, fn ($value) => $value !== null && $value !== '')) === 0) {
                continue;
            }
            $rows[] = array_combine(
                array_slice($headers, 0, count($values)),
                array_slice(array_pad($values, count($headers), null), 0, count($headers))
            );
        }
        fclose($handle);

        return $rows;
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $allProducts = Product::where('is_pack', false)->orderBy('name')->get();
        return view('admin.products.create', compact('categories', 'brands', 'allProducts'));
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'purchase_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'stock' => 'required_without:is_pack|nullable|integer|min:0',
            'available_at' => 'nullable|date',
            'warranty_duration_months' => 'required|integer|min:0|max:120',
            'warranty_coverage_percent' => 'nullable|integer|in:25,50,75',
            'warranty_coverage_scope' => 'nullable|string|max:120',
            'blackfriday' => 'boolean',
            'is_pack' => 'boolean',
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
            'specs' => 'nullable|array',
            'fiche_technique' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pack_items' => 'nullable|array',
            'pack_items.*.product_id' => 'required_with:pack_items|exists:products,id',
            'pack_items.*.quantity' => 'required_with:pack_items|integer|min:1',
        ]);

        $validated['slug'] = $this->generateUniqueSlug($request->name);
        $validated['active'] = $request->boolean('active');
        $validated['blackfriday'] = $request->boolean('blackfriday');
        $validated['is_pack'] = $request->boolean('is_pack');
        $validated['stock'] = $validated['is_pack'] ? 0 : intval($request->input('stock', 0));

        // Transform specs
        $rawSpecs = $request->input('specs', []);
        $specs = [];
        if (is_array($rawSpecs)) {
            foreach ($rawSpecs as $item) {
                if (!empty($item['key']) && !empty($item['value'])) {
                    $specs[$item['key']] = $item['value'];
                }
            }
        }
        $validated['specs'] = !empty($specs) ? $specs : null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        if ($request->hasFile('fiche_technique')) {
            $path = $request->file('fiche_technique')->store('products/tech_sheets', 'public');
            $validated['fiche_technique'] = $path;
        }

        $product = Product::create($validated);

        // Handle pack items
        if ($product->is_pack && !empty($request->input('pack_items'))) {
            foreach ($request->input('pack_items') as $item) {
                if (!empty($item['product_id']) && !empty($item['quantity'])) {
                    \App\Models\PackItem::create([
                        'pack_id' => $product->id,
                        'product_id' => $item['product_id'],
                        'quantity' => intval($item['quantity']),
                    ]);
                }
            }
        }

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

    public function edit(string $id)
    {
        $product = Product::with('packItems.product')->findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        $allProducts = Product::where('is_pack', false)->where('id', '!=', $product->id)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'allProducts'));
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
            'stock' => 'required_without:is_pack|nullable|integer|min:0',
            'available_at' => 'nullable|date',
            'warranty_duration_months' => 'required|integer|min:0|max:120',
            'warranty_coverage_percent' => 'nullable|integer|in:25,50,75',
            'warranty_coverage_scope' => 'nullable|string|max:120',
            'blackfriday' => 'boolean',
            'is_pack' => 'boolean',
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
            'specs' => 'nullable|array',
            'fiche_technique' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pack_items' => 'nullable|array',
            'pack_items.*.product_id' => 'required_with:pack_items|exists:products,id',
            'pack_items.*.quantity' => 'required_with:pack_items|integer|min:1',
        ]);

        // Ensure specs can be cleared / updated
        $rawSpecs = $request->input('specs', []);
        $specs = [];
        if (is_array($rawSpecs)) {
            foreach ($rawSpecs as $item) {
                if (!empty($item['key']) && !empty($item['value'])) {
                    $specs[$item['key']] = $item['value'];
                }
            }
        }
        $validated['specs'] = !empty($specs) ? $specs : null;

        // Only regenerate slug if the name has changed, to preserve existing URLs (SEO)
        if ($request->name !== $product->name) {
            $validated['slug'] = $this->generateUniqueSlug($request->name, $product->id);
        }
        $validated['active'] = $request->boolean('active');
        $validated['blackfriday'] = $request->boolean('blackfriday');
        $validated['is_pack'] = $request->boolean('is_pack');
        $validated['stock'] = $validated['is_pack'] ? 0 : intval($request->input('stock', 0));


        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        if ($request->hasFile('fiche_technique')) {
            if ($product->fiche_technique) {
                Storage::disk('public')->delete($product->fiche_technique);
            }
            $path = $request->file('fiche_technique')->store('products/tech_sheets', 'public');
            $validated['fiche_technique'] = $path;
        }

        $product->update($validated);

        // Handle pack items update
        if ($product->is_pack) {
            $product->packItems()->delete();
            if (!empty($request->input('pack_items'))) {
                foreach ($request->input('pack_items') as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity'])) {
                        \App\Models\PackItem::create([
                            'pack_id' => $product->id,
                            'product_id' => $item['product_id'],
                            'quantity' => intval($item['quantity']),
                        ]);
                    }
                }
            }
        } else {
            $product->packItems()->delete();
        }

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

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(string $id)
    {
        $product = Product::with(['images', 'options', 'packItems'])->findOrFail($id);

        $newProduct = $product->replicate();
        
        $newProduct->name = $product->name . ' (Copie)';
        $newProduct->slug = $this->generateUniqueSlug($newProduct->name);

        // Handle physical file duplication for main image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            try {
                $oldPath = $product->image;
                $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = 'products/' . Str::random(40) . '.' . $extension;
                Storage::disk('public')->copy($oldPath, $newPath);
                $newProduct->image = $newPath;
            } catch (\Exception $e) {
                \Log::error('Error copying product main image during duplication: ' . $e->getMessage());
            }
        }

        // Handle physical file duplication for tech sheet
        if ($product->fiche_technique && Storage::disk('public')->exists($product->fiche_technique)) {
            try {
                $oldPath = $product->fiche_technique;
                $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = 'products/tech_sheets/' . Str::random(40) . '.' . $extension;
                Storage::disk('public')->copy($oldPath, $newPath);
                $newProduct->fiche_technique = $newPath;
            } catch (\Exception $e) {
                \Log::error('Error copying product tech sheet during duplication: ' . $e->getMessage());
            }
        }

        $newProduct->save();

        // Duplicate multiple images
        foreach ($product->images as $img) {
            if ($img->path && Storage::disk('public')->exists($img->path)) {
                try {
                    $oldPath = $img->path;
                    $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                    $newPath = 'products/' . Str::random(40) . '.' . $extension;
                    Storage::disk('public')->copy($oldPath, $newPath);
                    
                    $newProduct->images()->create([
                        'path' => $newPath,
                        'sort_order' => $img->sort_order,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error copying product gallery image during duplication: ' . $e->getMessage());
                }
            }
        }

        // Duplicate options
        foreach ($product->options as $option) {
            $newProduct->options()->create([
                'name' => $option->name,
                'value' => $option->value,
                'price' => $option->price,
            ]);
        }

        // Duplicate pack items
        if ($product->is_pack) {
            foreach ($product->packItems as $item) {
                \App\Models\PackItem::create([
                    'pack_id' => $newProduct->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }

        return redirect()->route('admin.products.edit', $newProduct->id)
            ->with('success', 'Produit dupliqué avec succès. Vous pouvez maintenant modifier cette copie.');
    }
}
