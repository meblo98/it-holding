<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        foreach($cart as $id => $details) {
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

        if(isset($cart[$id])) {
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
        
        if(isset($cart[$id])) {
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
            foreach($cart as $details) {
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
        if(isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Produit retiré du panier.');
    }
}

