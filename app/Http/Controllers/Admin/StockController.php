<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'status'); // 'status' or 'movements'
        $search = $request->input('search');
        $filter = $request->input('filter'); // 'low', 'out', 'sufficient'

        // 1. Calculate global stats
        $allProducts = Product::all();
        
        $totalPurchaseValue = $allProducts->sum(function ($p) {
            return ($p->stock ?? 0) * ($p->purchase_price ?? 0);
        });

        $totalSalesValue = $allProducts->sum(function ($p) {
            $price = $p->promo_price && $p->promo_price > 0 && $p->promo_price < $p->price
                ? $p->promo_price
                : $p->price;
            return ($p->stock ?? 0) * $price;
        });

        $outOfStockCount = $allProducts->where('stock', 0)->count();
        $lowStockCount = $allProducts->filter(function ($p) {
            return $p->stock > 0 && $p->stock <= 5;
        })->count();

        // 2. Fetch paginated products based on query
        $products = Product::with(['category', 'images'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($filter === 'low', function ($query) {
                $query->where('stock', '>', 0)->where('stock', '<=', 5);
            })
            ->when($filter === 'out', function ($query) {
                $query->where('stock', 0);
            })
            ->when($filter === 'sufficient', function ($query) {
                $query->where('stock', '>', 5);
            })
            ->orderBy('stock', 'asc') // Critical stock first
            ->paginate(15);

        // 3. Fetch paginated movements
        $movements = StockMovement::with(['product', 'user'])
            ->latest()
            ->paginate(25);

        return view('admin.stock.index', compact(
            'tab', 'search', 'filter', 'products', 'movements',
            'totalPurchaseValue', 'totalSalesValue', 'outOfStockCount', 'lowStockCount'
        ));
    }

    public function adjust(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type'     => 'required|in:set,add', // set: exact stock count, add: add/subtract from stock
            'notes'    => 'nullable|string|max:255',
        ]);

        $oldStock = $product->stock;
        
        if ($validated['type'] === 'set') {
            $newStock = intval($validated['quantity']);
            $difference = $newStock - $oldStock;
        } else {
            $difference = intval($validated['quantity']);
            $newStock = $oldStock + $difference;
        }

        // Prevent negative final stock
        if ($newStock < 0) {
            return redirect()->back()->with('error', 'Le stock final ne peut pas être négatif.');
        }

        if ($difference !== 0) {
            DB::transaction(function () use ($product, $newStock, $difference, $validated) {
                $product->update([
                    'stock' => $newStock
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'quantity'   => $difference,
                    'type'       => 'adjustment',
                    'source'     => 'Ajustement manuel',
                    'notes'      => $validated['notes'] ?? 'Mise à jour manuelle des stocks',
                    'user_id'    => Auth::id(),
                ]);
            });

            return redirect()->back()->with('success', "Stock de '{$product->name}' mis à jour avec succès ({$oldStock} -> {$newStock}).");
        }

        return redirect()->back()->with('success', 'Aucune modification du stock requise.');
    }
}
