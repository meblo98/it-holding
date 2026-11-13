<?php

namespace App\Http\Controllers;

use App\Models\CourierProfile;
use App\Models\Delivery;
use App\Models\PurchaseOrder;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierController extends Controller
{
    private function profile(): CourierProfile
    {
        $user = Auth::user();
        abort_unless($user && $user->isLivreur(), 403, 'Espace réservé aux livreurs.');

        return CourierProfile::firstOrCreate(['user_id' => $user->id]);
    }

    private function earnedQuery()
    {
        return Delivery::where('courier_id', Auth::id())->where('status', 'delivered')->whereNotNull('courier_fee');
    }

    public function home()
    {
        $profile = $this->profile();
        $id = Auth::id();

        $base = fn () => Delivery::with(['order.items.product'])->where('courier_id', $id);

        return view('pages.courier-home', [
            'profile'    => $profile,
            'offered'    => $base()->where('status', 'offered')->latest('offered_at')->get(),
            'active'     => $base()->whereIn('status', ['accepted', 'picked_up'])->latest('accepted_at')->get(),
            'history'    => $base()->where('status', 'delivered')->latest('delivered_at')->take(10)->get(),
            'todayTotal' => (int) $this->earnedQuery()->where('delivered_at', '>=', now()->startOfDay())->sum('courier_fee'),
        ]);
    }

    public function earnings()
    {
        $this->profile();

        $sum = fn ($q) => (int) $q->sum('courier_fee');

        return view('pages.courier-earnings', [
            'today'   => $sum($this->earnedQuery()->where('delivered_at', '>=', now()->startOfDay())),
            'week'    => $sum($this->earnedQuery()->where('delivered_at', '>=', now()->startOfWeek())),
            'month'   => $sum($this->earnedQuery()->where('delivered_at', '>=', now()->startOfMonth())),
            'total'   => $sum($this->earnedQuery()),
            'unpaid'  => $sum($this->earnedQuery()->whereNull('fee_paid_at')),
            'count'   => $this->earnedQuery()->count(),
            'rows'    => $this->earnedQuery()->latest('delivered_at')->take(50)->get(),
        ]);
    }

    public function update(Request $request)
    {
        $profile = $this->profile();

        $data = $request->validate([
            'latitude'     => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'    => ['nullable', 'numeric', 'between:-180,180'],
            'radius_km'    => ['required', 'integer', 'in:5,10,15,25,40'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $update = [
            'radius_km'    => $data['radius_km'],
            'is_available' => $request->boolean('is_available'),
        ];

        if (isset($data['latitude'], $data['longitude'])) {
            $update['latitude'] = $data['latitude'];
            $update['longitude'] = $data['longitude'];
            $update['location_updated_at'] = now();
        }

        if ($update['is_available'] && !$profile->has_location && !isset($update['latitude'])) {
            return back()->with('error', 'Partagez d’abord votre position pour vous déclarer disponible.');
        }

        $profile->update($update);

        return back()->with('success', $update['is_available']
            ? 'Vous êtes disponible. Les courses proches de vous vous seront proposées.'
            : 'Vos informations sont enregistrées. Vous êtes indisponible.');
    }

    public function accept(Delivery $delivery)
    {
        $this->profile();
        $ok = DeliveryService::accept($delivery, Auth::id());

        return back()->with($ok ? 'success' : 'error', $ok ? 'Course acceptée. Rendez-vous chez le fournisseur.' : 'Cette course n’est plus disponible.');
    }

    public function decline(Delivery $delivery)
    {
        $this->profile();
        $ok = DeliveryService::decline($delivery, Auth::id());

        return back()->with($ok ? 'success' : 'error', $ok ? 'Course refusée. Elle est proposée à un autre livreur.' : 'Action impossible.');
    }

    /** Retrait chez le fournisseur : code donné par le fournisseur. */
    public function pickupCode(Request $request, Delivery $delivery)
    {
        $this->profile();
        $request->validate(['code' => ['required', 'digits:6']], ['code.digits' => 'Le code comporte 6 chiffres.']);

        $error = DeliveryService::confirmPickup($delivery, Auth::id(), 'code', $request->input('code'));

        return back()->with($error ? 'error' : 'success', $error ?? 'Remise validée. Bonne route vers le client !');
    }

    /** Page ouverte en scannant le QR du bon de commande. */
    public function pickupScan(string $token)
    {
        $this->profile();
        $bon = PurchaseOrder::where('pickup_token', $token)->with('supplier')->firstOrFail();
        $delivery = Delivery::where('order_id', $bon->order_id)->firstOrFail();

        return view('pages.courier-scan', [
            'mode'     => 'pickup',
            'delivery' => $delivery,
            'title'    => 'Retrait chez ' . $bon->supplier->name,
            'detail'   => 'Bon ' . $bon->number,
            'valid'    => $delivery->courier_id === Auth::id() && $delivery->status === 'accepted',
            'action'   => route('courier.pickup.confirm', $token),
            'button'   => 'Confirmer : j’ai reçu le colis',
        ]);
    }

    public function pickupConfirm(string $token)
    {
        $this->profile();
        $bon = PurchaseOrder::where('pickup_token', $token)->firstOrFail();
        $delivery = Delivery::where('order_id', $bon->order_id)->firstOrFail();

        $error = DeliveryService::confirmPickup($delivery, Auth::id(), 'qr');

        return redirect()->route('courier.home')->with($error ? 'error' : 'success', $error ?? 'Remise validée. Bonne route vers le client !');
    }

    /** Livraison : code donné par le client. */
    public function dropoffCode(Request $request, Delivery $delivery)
    {
        $this->profile();
        $request->validate(['code' => ['required', 'digits:6']], ['code.digits' => 'Le code comporte 6 chiffres.']);

        $error = DeliveryService::confirmDelivery($delivery, Auth::id(), 'code', $request->input('code'));

        return back()->with($error ? 'error' : 'success', $error ?? 'Livraison confirmée. Merci !');
    }

    /** Page ouverte en scannant le QR présenté par le client. */
    public function dropoffScan(string $token)
    {
        $this->profile();
        $delivery = Delivery::where('delivery_token', $token)->with('order')->firstOrFail();

        return view('pages.courier-scan', [
            'mode'     => 'dropoff',
            'delivery' => $delivery,
            'title'    => 'Livraison commande #' . $delivery->order_id,
            'detail'   => 'Client : ' . $delivery->order->customer_name,
            'valid'    => $delivery->courier_id === Auth::id() && $delivery->status === 'picked_up',
            'action'   => route('courier.dropoff.confirm', $token),
            'button'   => 'Confirmer : colis remis au client',
        ]);
    }

    public function dropoffConfirm(string $token)
    {
        $this->profile();
        $delivery = Delivery::where('delivery_token', $token)->firstOrFail();

        $error = DeliveryService::confirmDelivery($delivery, Auth::id(), 'qr');

        return redirect()->route('courier.home')->with($error ? 'error' : 'success', $error ?? 'Livraison confirmée. Votre revenu est enregistré.');
    }
}
