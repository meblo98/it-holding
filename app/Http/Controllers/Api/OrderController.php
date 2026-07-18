<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::with(['items.product']);

        if ($user->role === 'client') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(15);

        $orders->getCollection()->transform(function ($order) {
            return $this->formatOrder($order);
        });

        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::with(['items.product', 'client'])->findOrFail($id);

        if ($user->role === 'client' && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return response()->json([
            'order' => $this->formatOrder($order, true)
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.options' => 'nullable|array',
            'payment_method' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'required|email|max:255',
            'customer_address' => 'required|string',
        ]);

        $orderItemsData = [];
        $totalAmount = 0.00;

        try {
            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);

                if ($product->stock < $itemData['quantity']) {
                    return response()->json([
                        'message' => "Le stock pour le produit '{$product->name}' est insuffisant."
                    ], 422);
                }

                // Compute price based on wholesale rules if applicable
                $unitPrice = Product::calculateWholesalePrice($product, $itemData['quantity']);
                $itemTotal = $unitPrice * $itemData['quantity'];
                $totalAmount += $itemTotal;

                // Decrement stock
                $product->decrement('stock', $itemData['quantity']);

                // Record stock movement
                $product->stockMovements()->create([
                    'type' => 'out',
                    'quantity' => $itemData['quantity'],
                    'reference' => 'Vente (App Mobile)',
                    'description' => 'Achat via application mobile.',
                    'user_id' => $user->id,
                ]);

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $unitPrice,
                    'purchase_price' => $product->purchase_price ?? 0.00,
                    'options' => $itemData['options'] ?? null,
                ];
            }

            // Determine if there is an associated Client profile
            $client = $user->client;
            
            $order = Order::create([
                'user_id' => $user->id,
                'client_id' => $client ? $client->id : null,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);

            foreach ($orderItemsData as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            return response()->json([
                'message' => 'Commande passée avec succès.',
                'order' => $this->formatOrder($order->load('items.product'))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création de la commande.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function formatOrder($order, $includeItems = false)
    {
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number ?? 'CMD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'customer_address' => $order->customer_address,
            'total_amount' => $order->total_amount,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'created_at' => $order->created_at->toIso8601String(),
        ];

        if ($includeItems || $order->relationLoaded('items')) {
            $data['items'] = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product ? $item->product->name : 'Produit inconnu',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'options' => $item->options,
                    'total' => $item->price * $item->quantity,
                ];
            });
        }

        return $data;
    }
}
