<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierProfile;
use App\Models\Delivery;
use App\Models\User;
use App\Services\DeliveryService;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $deliveries = Delivery::with(['order', 'courier'])
            ->when($status === 'active', fn ($q) => $q->whereIn('status', ['pending', 'offered', 'accepted', 'picked_up']))
            ->when($status && $status !== 'active', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'pending'   => Delivery::where('status', 'pending')->count(),
            'active'    => Delivery::whereIn('status', ['offered', 'accepted', 'picked_up'])->count(),
            'delivered' => Delivery::where('status', 'delivered')->count(),
        ];

        $couriers = CourierProfile::with('user')->where('is_available', true)->get();

        return view('admin.deliveries-index', compact('deliveries', 'stats', 'status', 'couriers'));
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['order.items.product', 'courier', 'offer.supplier']);

        $couriers = User::where('role', 'livreur')->orderBy('name')->get();
        $profiles = CourierProfile::whereIn('user_id', $couriers->pluck('id'))->get()->keyBy('user_id');

        return view('admin.deliveries-show', compact('delivery', 'couriers', 'profiles'));
    }

    public function assign(Request $request, Delivery $delivery)
    {
        $data = $request->validate(['courier_id' => 'required|exists:users,id']);
        DeliveryService::adminAssign($delivery, User::findOrFail($data['courier_id']));

        return back()->with('success', 'Livreur affecté.');
    }

    public function reassign(Delivery $delivery)
    {
        $found = DeliveryService::adminReassign($delivery);

        return back()->with($found ? 'success' : 'error', $found ? 'Course proposée à un autre livreur.' : 'Aucun autre livreur disponible pour le moment. Nouvelle tentative automatique chaque minute.');
    }

    public function cancel(Delivery $delivery)
    {
        DeliveryService::adminCancel($delivery);

        return back()->with('success', 'Livraison annulée.');
    }
}
