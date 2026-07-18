<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeliveryNoteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', 'all'); // 'all', 'envoi', 'reception'
        $status = $request->input('status');

        $deliveryNotes = DeliveryNote::with(['supplier', 'order', 'invoice', 'client'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('number', 'like', "%{$search}%")
                      ->orWhere('supplier_name', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->when($type !== 'all', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15);

        return view('admin.delivery_notes.index', compact('deliveryNotes', 'search', 'type', 'status'));
    }

    public function create(Request $request)
    {
        $nextNumber = 'BL-' . date('Y') . '-' . str_pad(DeliveryNote::count() + 1, 4, '0', STR_PAD_LEFT);
        $products = Product::where('active', true)->get(['id', 'name', 'price', 'purchase_price']);
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        $clients = \App\Models\Client::orderBy('company_name')->orderBy('last_name')->get();

        $prefilled = null;

        if ($request->has('order_id')) {
            $order = \App\Models\Order::with('items.product')->find($request->input('order_id'));
            if ($order) {
                $prefilled = [
                    'type' => 'envoi',
                    'client_id' => $order->client_id,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'customer_address' => $order->customer_address,
                    'order_id' => $order->id,
                    'invoice_id' => null,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'purchase_price' => $item->price ?? 0.00,
                        ];
                    })->toArray()
                ];
            }
        } elseif ($request->has('invoice_id')) {
            $invoice = \App\Models\Invoice::with('items.product')->find($request->input('invoice_id'));
            if ($invoice) {
                $prefilled = [
                    'type' => 'envoi',
                    'client_id' => $invoice->client_id,
                    'customer_name' => $invoice->client_name,
                    'customer_phone' => $invoice->client_phone,
                    'customer_address' => $invoice->client_address,
                    'order_id' => null,
                    'invoice_id' => $invoice->id,
                    'items' => $invoice->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'purchase_price' => $item->unit_price ?? 0.00,
                        ];
                    })->toArray()
                ];
            }
        }

        return view('admin.delivery_notes.create', compact('nextNumber', 'products', 'suppliers', 'clients', 'prefilled'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number'            => 'required|unique:delivery_notes,number',
            'type'              => 'required|in:reception,envoi',
            'status'            => 'required|string',
            'delivery_date'     => 'required|date',
            'notes'             => 'nullable|string',
            
            // Conditional Supplier validation
            'supplier_id'       => 'required_if:type,reception|nullable|exists:suppliers,id',
            'supplier_name'     => 'nullable|string|max:255',
            
            // Conditional Customer validation
            'client_id'         => 'nullable|exists:clients,id',
            'customer_name'     => 'required_if:type,envoi|nullable|string|max:255',
            'customer_phone'    => 'nullable|string|max:50',
            'customer_address'  => 'nullable|string|max:1000',
            
            // Relations
            'order_id'          => 'nullable|integer',
            'invoice_id'        => 'nullable|integer',

            // Items
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|exists:products,id',
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ]);

        $deliveryNote = DB::transaction(function () use ($validated) {
            $totalPurchaseAmount = 0;
            
            $supplierId = $validated['supplier_id'] ?? null;
            $supplierName = $validated['supplier_name'] ?? null;
            if ($supplierId) {
                $supplier = \App\Models\Supplier::find($supplierId);
                if ($supplier) {
                    $supplierName = $supplier->name;
                }
            }

            // 1. Create the delivery note
            $deliveryNote = DeliveryNote::create([
                'number'            => $validated['number'],
                'type'              => $validated['type'],
                'status'            => $validated['status'],
                'delivery_date'     => $validated['delivery_date'],
                'notes'             => $validated['notes'] ?? null,
                'supplier_id'       => $supplierId,
                'supplier_name'     => $supplierName,
                'client_id'         => $validated['client_id'] ?? null,
                'customer_name'     => $validated['customer_name'] ?? null,
                'customer_phone'    => $validated['customer_phone'] ?? null,
                'customer_address'  => $validated['customer_address'] ?? null,
                'order_id'          => $validated['order_id'] ?? null,
                'invoice_id'        => $validated['invoice_id'] ?? null,
                'total_purchase_amount' => 0.00,
            ]);

            // 2. Loop over items and build them
            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                $quantity = floatval($itemData['quantity']);
                $purchasePrice = floatval($itemData['purchase_price']);
                $totalPrice = $quantity * $purchasePrice;

                $totalPurchaseAmount += $totalPrice;

                $deliveryNote->items()->create([
                    'product_id'     => $product->id,
                    'product_name'   => $product->name,
                    'quantity'       => $quantity,
                    'purchase_price' => $purchasePrice,
                    'total_price'    => $totalPrice,
                ]);

                // 3. Stock Impact
                $this->applyStockImpact($deliveryNote, $product, $quantity, $purchasePrice);
            }

            $deliveryNote->update([
                'total_purchase_amount' => $totalPurchaseAmount
            ]);

            return $deliveryNote;
        });

        return redirect()->route('admin.delivery-notes.show', $deliveryNote->id)
            ->with('success', 'Bon de livraison enregistré avec succès.');
    }

    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['items.product', 'supplier', 'order', 'invoice']);
        return view('admin.delivery_notes.show', compact('deliveryNote'));
    }

    public function print(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['items.product', 'supplier', 'order', 'invoice']);
        return view('admin.delivery_notes.show', compact('deliveryNote'))->with('print', true);
    }

    public function edit(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load('items.product');
        $products = Product::where('active', true)->get(['id', 'name', 'price', 'purchase_price']);
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        $clients = \App\Models\Client::orderBy('company_name')->orderBy('last_name')->get();

        return view('admin.delivery_notes.edit', compact('deliveryNote', 'products', 'suppliers', 'clients'));
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        $validated = $request->validate([
            'status'            => 'required|string',
            'delivery_date'     => 'required|date',
            'notes'             => 'nullable|string',
            
            // Conditional Supplier validation
            'supplier_id'       => 'required_if:type,reception|nullable|exists:suppliers,id',
            'supplier_name'     => 'nullable|string|max:255',
            
            // Conditional Customer validation
            'client_id'         => 'nullable|exists:clients,id',
            'customer_name'     => 'required_if:type,envoi|nullable|string|max:255',
            'customer_phone'    => 'nullable|string|max:50',
            'customer_address'  => 'nullable|string|max:1000',

            // Items
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|exists:products,id',
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($deliveryNote, $validated) {
            // 1. Revert previous stock impacts
            $this->revertStockImpact($deliveryNote);

            // 2. Clear old items
            $deliveryNote->items()->delete();

            $supplierId = $validated['supplier_id'] ?? null;
            $supplierName = $validated['supplier_name'] ?? null;
            if ($supplierId) {
                $supplier = \App\Models\Supplier::find($supplierId);
                if ($supplier) {
                    $supplierName = $supplier->name;
                }
            }

            // 3. Update Delivery Note properties
            $deliveryNote->update([
                'status'            => $validated['status'],
                'delivery_date'     => $validated['delivery_date'],
                'notes'             => $validated['notes'] ?? null,
                'supplier_id'       => $supplierId,
                'supplier_name'     => $supplierName,
                'client_id'         => $validated['client_id'] ?? null,
                'customer_name'     => $validated['customer_name'] ?? null,
                'customer_phone'    => $validated['customer_phone'] ?? null,
                'customer_address'  => $validated['customer_address'] ?? null,
            ]);

            $totalPurchaseAmount = 0;

            // 4. Create new items and apply stock impacts
            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                $quantity = floatval($itemData['quantity']);
                $purchasePrice = floatval($itemData['purchase_price']);
                $totalPrice = $quantity * $purchasePrice;

                $totalPurchaseAmount += $totalPrice;

                $deliveryNote->items()->create([
                    'product_id'     => $product->id,
                    'product_name'   => $product->name,
                    'quantity'       => $quantity,
                    'purchase_price' => $purchasePrice,
                    'total_price'    => $totalPrice,
                ]);

                $this->applyStockImpact($deliveryNote, $product, $quantity, $purchasePrice);
            }

            $deliveryNote->update([
                'total_purchase_amount' => $totalPurchaseAmount
            ]);
        });

        return redirect()->route('admin.delivery-notes.show', $deliveryNote->id)
            ->with('success', 'Bon de livraison mis à jour avec succès.');
    }

    public function destroy(DeliveryNote $deliveryNote)
    {
        DB::transaction(function () use ($deliveryNote) {
            // Revert stock impact
            $this->revertStockImpact($deliveryNote);

            // Delete Note & Items (cascade or manual delete)
            $deliveryNote->items()->delete();
            $deliveryNote->delete();
        });

        return redirect()->route('admin.delivery-notes.index')
            ->with('success', 'Bon de livraison supprimé et stocks réajustés.');
    }

    // ==================== HELPER METHODS ====================

    private function applyStockImpact(DeliveryNote $deliveryNote, Product $product, float $quantity, float $purchasePrice)
    {
        $status = $deliveryNote->status;
        $type = $deliveryNote->type;

        // Stock impact trigger states
        if ($type === 'reception' && $status === 'received') {
            $product->increment('stock', $quantity);
            $product->update(['purchase_price' => $purchasePrice]);

            StockMovement::create([
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'type'       => 'in',
                'source'     => "Bon de livraison #{$deliveryNote->number}",
                'notes'      => "Réception de marchandises (Entrée fournisseur)",
                'user_id'    => Auth::id(),
            ]);
        } elseif ($type === 'envoi' && ($status === 'shipped' || $status === 'delivered')) {
            $product->decrement('stock', $quantity);

            StockMovement::create([
                'product_id' => $product->id,
                'quantity'   => -$quantity,
                'type'       => 'out',
                'source'     => "Bon de livraison #{$deliveryNote->number}",
                'notes'      => "Expédition de marchandises (Sortie client)",
                'user_id'    => Auth::id(),
            ]);
        }
    }

    private function revertStockImpact(DeliveryNote $deliveryNote)
    {
        $status = $deliveryNote->status;
        $type = $deliveryNote->type;

        if ($type === 'reception' && $status === 'received') {
            // Decrement what was incremented
            foreach ($deliveryNote->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->decrement('stock', $item->quantity);

                        StockMovement::create([
                            'product_id' => $product->id,
                            'quantity'   => -$item->quantity,
                            'type'       => 'out',
                            'source'     => "Annulation Bon #{$deliveryNote->number}",
                            'notes'      => "Réajustement automatique suite à mise à jour/suppression",
                            'user_id'    => Auth::id(),
                        ]);
                    }
                }
            }
        } elseif ($type === 'envoi' && ($status === 'shipped' || $status === 'delivered')) {
            // Increment what was decremented
            foreach ($deliveryNote->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);

                        StockMovement::create([
                            'product_id' => $product->id,
                            'quantity'   => $item->quantity,
                            'type'       => 'in',
                            'source'     => "Annulation Bon #{$deliveryNote->number}",
                            'notes'      => "Réajustement automatique suite à mise à jour/suppression",
                            'user_id'    => Auth::id(),
                        ]);
                    }
                }
            }
        }
    }
}
