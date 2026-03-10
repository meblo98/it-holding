<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('active', true)->where('stock', '>', 0);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        if ($request->filled('blackfriday')) {
            $query->where('blackfriday', true);
        }

        $products = $query->with('images', 'category', 'brand')->paginate(12)->withQueryString();

        // filters list
        $categories = Category::whereHas('products', function ($q) {
            $q->where('active', true)->where('stock', '>', 0);
        })->get();
        $brands = Brand::whereHas('products', function ($q) {
            $q->where('active', true)->where('stock', '>', 0);
        })->get();
        $conditions = Product::where('active', true)->where('stock', '>', 0)->whereNotNull('condition')->distinct()->pluck('condition');

        return view('pages.shop.index', compact('products', 'categories', 'brands', 'conditions'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->where('active', true)->with('images', 'category', 'brand')->firstOrFail();
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
            // determine effective price (promo or normal)
            $effectivePrice = $product->promo_price && $product->promo_price > 0 && $product->promo_price < $product->price
                ? $product->promo_price
                : $product->price;

            $cart[$id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $effectivePrice,
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
            'payment_method' => 'required|string', // Allow any for now, but logical check below
        ]);

        // calculate total
        $total = 0;
        foreach ($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        // create order within transaction
        DB::beginTransaction();
        try {
            $fullAddress = $validated['address'] . ', ' . $validated['city'] . ' ' . $validated['zip'] . ', ' . $validated['country'];
            
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'] ?? null,
                'customer_address' => $fullAddress,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'], // Store whatever was picked
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
