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
        $cart = Session::get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Produit ajouté au panier !');
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
