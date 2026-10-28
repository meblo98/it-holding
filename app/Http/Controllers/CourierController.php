<?php

namespace App\Http\Controllers;

use App\Models\CourierProfile;
use App\Models\Delivery;
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

    public function home()
    {
        $profile = $this->profile();
        $id = Auth::id();

        $base = fn () => Delivery::with(['order.items.product'])->where('courier_id', $id);

        return view('pages.courier-home', [
            'profile' => $profile,
            'offered' => $base()->where('status', 'offered')->latest('offered_at')->get(),
            'active'  => $base()->whereIn('status', ['accepted', 'picked_up'])->latest('accepted_at')->get(),
            'history' => $base()->where('status', 'delivered')->latest('delivered_at')->take(10)->get(),
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

        return back()->with($ok ? 'success' : 'error', $ok ? 'Course acceptée. Rendez-vous au point de retrait.' : 'Cette course n’est plus disponible.');
    }

    public function decline(Delivery $delivery)
    {
        $this->profile();
        $ok = DeliveryService::decline($delivery, Auth::id());

        return back()->with($ok ? 'success' : 'error', $ok ? 'Course refusée. Elle est proposée à un autre livreur.' : 'Action impossible.');
    }

    public function pickedUp(Delivery $delivery)
    {
        $this->profile();
        $ok = DeliveryService::markPickedUp($delivery, Auth::id());

        return back()->with($ok ? 'success' : 'error', $ok ? 'Colis récupéré. Bonne route !' : 'Action impossible.');
    }

    public function delivered(Delivery $delivery)
    {
        $this->profile();
        $ok = DeliveryService::markDelivered($delivery, Auth::id());

        return back()->with($ok ? 'success' : 'error', $ok ? 'Livraison confirmée. Merci !' : 'Action impossible.');
    }
}
