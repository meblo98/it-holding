<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $totalOrders = $user->orders()->count();
        $pendingOrders = $user->orders()->whereIn('status', ['pending', 'processing', 'in_progress', 'processing'])->count();
        $completedOrders = $user->orders()->whereIn('status', ['completed', 'delivered'])->count();
        
        $recentOrders = $user->orders()->latest()->take(7)->get();
        
        // Mock browsing history with latest products
        $browsingHistory = Product::where('active', true)->latest()->take(4)->get();
        
        return view('dashboard', compact(
            'user', 
            'totalOrders', 
            'pendingOrders', 
            'completedOrders', 
            'recentOrders',
            'browsingHistory'
        ));
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()->latest()->paginate(10);
        
        return view('pages.shop.orders', compact('user', 'orders'));
    }

    public function showOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product']);
        
        return view('pages.shop.order-details', compact('order'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('pages.shop.settings', compact('user'));
    }

    public function trackOrder()
    {
        $user = Auth::user();
        return view('pages.shop.track-order', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'display_name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
        ]);

        $user->update([
            'name' => $request->display_name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
        ]);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function updateAddress(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'billing_first_name' => 'nullable|string|max:255',
            'billing_last_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_zip' => 'nullable|string|max:255',
            'shipping_first_name' => 'nullable|string|max:255',
            'shipping_last_name' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_zip' => 'nullable|string|max:255',
        ]);

        $user->update($data);

        return back()->with('success', 'Adresses mises à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }
}
