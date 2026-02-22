<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::where('active', true)->where('stock', '>', 0)->paginate(12);
        return view('pages.shop.index', compact('products'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->where('active', true)->firstOrFail();
        return view('pages.shop.show', compact('product'));
    }

    public function cart()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        foreach ($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }
        return view('pages.shop.cart', compact('cart', 'total'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validate quantity
        $quantity = $request->input('quantity', 1);
        if ($quantity > $product->stock) {
            return redirect()->back()->with('error', 'Stock insuffisant. Seulement ' . $product->stock . ' disponible(s).');
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $newQuantity = $cart[$id]['quantity'] + $quantity;
            if ($newQuantity > $product->stock) {
                return redirect()->back()->with('error', 'Stock insuffisant. Seulement ' . $product->stock . ' disponible(s).');
            }
            $cart[$id]['quantity'] = $newQuantity;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "image" => $product->image,
                "slug" => $product->slug
            ];
        }

        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Produit ajouté au panier !');
    }

    public function updateCart(Request $request)
    {
        $cart = Session::get('cart', []);
        $id = $request->input('id');
        $quantity = $request->input('quantity', 1);

        if (isset($cart[$id])) {
            $product = Product::findOrFail($id);

            if ($quantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant'
                ], 400);
            }

            if ($quantity <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['quantity'] = $quantity;
            }

            Session::put('cart', $cart);

            // Calculate new total
            $total = 0;
            foreach ($cart as $details) {
                $total += $details['price'] * $details['quantity'];
            }

            return response()->json([
                'success' => true,
                'total' => number_format($total, 0, ',', ' ') . ' FCFA',
                'cartCount' => array_sum(array_column($cart, 'quantity'))
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    public function removeFromCart($id)
    {
        $cart = Session::get('cart');
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Produit retiré du panier.');
    }

    /**
     * Show checkout form
     */
    public function checkout()
    {
        $cart = Session::get('cart', []);
        if (count($cart) === 0) {
            return redirect()->route('shop.index')->with('error', 'Votre panier est vide.');
        }
        $total = 0;
        foreach ($cart as $details) {
            $total += $details['price'] * $details['quantity'];
        }
        return view('pages.shop.checkout', compact('cart', 'total'));
    }

    /**
     * Place order (Cash on Delivery)
     */
    public function placeOrder(Request $request)
    {
        $cart = Session::get('cart', []);
        if (count($cart) === 0) {
            return redirect()->route('shop.index')->with('error', 'Votre panier est vide.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'required|string|max:1000',
            'payment_method' => 'required|in:cod',
        ]);

        // calculate total
        $total = 0;
        foreach ($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        // create order within transaction
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'],
                'total_amount' => $total,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'],
            ]);

            foreach ($cart as $id => $details) {
                $product = Product::findOrFail($id);

                // prevent ordering more than stock
                $quantity = min($details['quantity'], $product->stock);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $quantity,
                    'price' => $details['price'],
                ]);

                // reduce stock
                $product->decrement('stock', $quantity);
            }

            DB::commit();

            // clear cart
            Session::forget('cart');

            return redirect()->route('shop.thanks', $order->id)->with('success', 'Commande passée. Paiement à la livraison.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la création de la commande.');
        }
    }

    /**
     * Thank you / order confirmation
     */
    public function thanks($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        return view('pages.shop.thanks', compact('order'));
    }
}
