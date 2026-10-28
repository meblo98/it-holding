<?php

namespace App\Services;

use App\Models\CourierProfile;
use App\Models\Delivery;
use App\Models\SourcingRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Attribution automatique de la livraison au livreur disponible le plus proche du point de retrait.
 */
class DeliveryService
{
    /** Crée la livraison dès qu'une offre fournisseur est retenue, puis cherche un livreur. */
    public static function createForAward(SourcingRequest $request): ?Delivery
    {
        $order = $request->order;
        $offer = $request->awardedOffer()->with('supplier')->first();

        if (!$offer || Delivery::where('order_id', $order->id)->exists()) {
            return null;
        }

        $supplier = $offer->supplier;

        $delivery = Delivery::create([
            'order_id'          => $order->id,
            'supplier_offer_id' => $offer->id,
            'status'            => 'pending',
            'pickup_lat'        => $supplier->latitude,
            'pickup_lng'        => $supplier->longitude,
            'pickup_label'      => trim($supplier->name . ($supplier->address ? ' — ' . $supplier->address : '')),
            'dropoff_lat'       => $order->delivery_lat,
            'dropoff_lng'       => $order->delivery_lng,
            'declined_by'       => [],
        ]);

        self::assignCourier($delivery, true);

        return $delivery;
    }

    /** Propose la course au meilleur livreur disponible. @return bool true si un livreur a été trouvé */
    public static function assignCourier(Delivery $delivery, bool $alertIfNone = false): bool
    {
        if (!in_array($delivery->status, ['pending', 'offered'], true)) {
            return false;
        }

        $refLat = $delivery->pickup_lat ?? $delivery->dropoff_lat;
        $refLng = $delivery->pickup_lng ?? $delivery->dropoff_lng;
        $declined = $delivery->declined_by ?? [];
        $maxActive = (int) config('delivery.max_active_per_courier', 2);
        $fresh = now()->subHours((int) config('delivery.courier_location_fresh_hours', 12));

        $candidates = collect();

        if ($refLat !== null && $refLng !== null) {
            $candidates = CourierProfile::with('user')
                ->where('is_available', true)
                ->whereNotNull('latitude')
                ->where('location_updated_at', '>=', $fresh)
                ->get()
                ->filter(fn ($p) => $p->user && $p->user->isLivreur() && !in_array($p->user_id, $declined, true))
                ->map(fn ($p) => [
                    'profile' => $p,
                    'dist'    => GeoDistance::km($refLat, $refLng, $p->latitude, $p->longitude),
                    'active'  => Delivery::where('courier_id', $p->user_id)->whereIn('status', Delivery::ACTIVE)->count(),
                ])
                ->filter(fn ($c) => $c['dist'] <= $c['profile']->radius_km && $c['active'] < $maxActive)
                ->sort(fn ($a, $b) => [$a['dist'], $a['active']] <=> [$b['dist'], $b['active']])
                ->values();
        }

        if ($candidates->isEmpty()) {
            $delivery->update(['status' => 'pending', 'courier_id' => null, 'offered_at' => null, 'offer_expires_at' => null, 'distance_km' => null]);
            if ($alertIfNone) {
                self::alertAdmin("⚠️ Livraison commande #{$delivery->order_id} : aucun livreur disponible dans la zone. Nouvelle tentative automatique chaque minute.");
            }

            return false;
        }

        $best = $candidates->first();
        $delivery->update([
            'status'           => 'offered',
            'courier_id'       => $best['profile']->user_id,
            'offered_at'       => now(),
            'offer_expires_at' => now()->addMinutes((int) config('delivery.courier_offer_minutes', 10)),
            'distance_km'      => $best['dist'],
        ]);

        self::notifyCourier($delivery, $best['profile']->user);

        return true;
    }

    public static function accept(Delivery $delivery, int $userId): bool
    {
        if ($delivery->courier_id !== $userId || $delivery->status !== 'offered' || ($delivery->offer_expires_at && $delivery->offer_expires_at->isPast())) {
            return false;
        }

        $delivery->update(['status' => 'accepted', 'accepted_at' => now(), 'offer_expires_at' => null]);
        self::alertAdmin("🏍️ Livraison commande #{$delivery->order_id} acceptée par " . ($delivery->courier->name ?? 'un livreur') . '.');

        return true;
    }

    public static function decline(Delivery $delivery, int $userId): bool
    {
        if ($delivery->courier_id !== $userId || $delivery->status !== 'offered') {
            return false;
        }

        self::release($delivery);
        self::assignCourier($delivery->fresh(), true);

        return true;
    }

    public static function markPickedUp(Delivery $delivery, int $userId): bool
    {
        if ($delivery->courier_id !== $userId || $delivery->status !== 'accepted') {
            return false;
        }

        $delivery->update(['status' => 'picked_up', 'picked_up_at' => now()]);

        return true;
    }

    public static function markDelivered(Delivery $delivery, int $userId): bool
    {
        if ($delivery->courier_id !== $userId || $delivery->status !== 'picked_up') {
            return false;
        }

        $delivery->update(['status' => 'delivered', 'delivered_at' => now()]);
        self::alertAdmin("✅ Commande #{$delivery->order_id} livrée. Pensez à la marquer « terminée » dans les commandes.");

        return true;
    }

    /** L'admin désigne lui-même le livreur : la course est directement acceptée. */
    public static function adminAssign(Delivery $delivery, User $courier): void
    {
        abort_unless($courier->isLivreur(), 422);
        abort_unless(in_array($delivery->status, ['pending', 'offered', 'accepted'], true), 422);

        $delivery->update([
            'status'           => 'accepted',
            'courier_id'       => $courier->id,
            'accepted_at'      => now(),
            'offered_at'       => $delivery->offered_at ?? now(),
            'offer_expires_at' => null,
        ]);

        self::notifyCourier($delivery, $courier, 'Une course vous a été affectée par IT Holding.');
    }

    /** L'admin relance la recherche automatique en écartant le livreur actuel. */
    public static function adminReassign(Delivery $delivery): bool
    {
        abort_unless(in_array($delivery->status, ['pending', 'offered', 'accepted'], true), 422);

        self::release($delivery);

        return self::assignCourier($delivery->fresh(), false);
    }

    public static function adminCancel(Delivery $delivery): void
    {
        abort_unless(in_array($delivery->status, ['pending', 'offered', 'accepted'], true), 422);
        $delivery->update(['status' => 'cancelled', 'courier_id' => null, 'offer_expires_at' => null]);
    }

    /** Chaque minute : propositions expirées -> livreur suivant ; livraisons en attente -> nouvelle recherche. */
    public static function tick(): int
    {
        $count = 0;

        $expired = Delivery::where('status', 'offered')->where('offer_expires_at', '<=', now())->get();
        foreach ($expired as $delivery) {
            self::release($delivery);
            self::assignCourier($delivery->fresh(), true);
            $count++;
        }

        foreach (Delivery::where('status', 'pending')->get() as $delivery) {
            if (self::assignCourier($delivery, false)) {
                $count++;
            }
        }

        return $count;
    }

    /** Écarte le livreur actuel de cette course. */
    private static function release(Delivery $delivery): void
    {
        $declined = $delivery->declined_by ?? [];
        if ($delivery->courier_id) {
            $declined[] = $delivery->courier_id;
        }

        $delivery->update([
            'status'           => 'pending',
            'courier_id'       => null,
            'declined_by'      => array_values(array_unique($declined)),
            'offered_at'       => null,
            'offer_expires_at' => null,
            'distance_km'      => null,
        ]);
    }

    private static function notifyCourier(Delivery $delivery, ?User $courier, ?string $text = null): void
    {
        if (!$courier) {
            return;
        }

        $message = $text ?? ('IT Holding : nouvelle course proposée (' . number_format($delivery->distance_km ?? 0, 1) . ' km du point de retrait). '
            . 'Répondez avant ' . $delivery->offer_expires_at?->format('H:i') . ' : ' . route('courier.home'));

        if ($courier->email) {
            try {
                Mail::raw($message, fn ($m) => $m->to($courier->email)->subject('Nouvelle course IT Holding'));
            } catch (\Throwable $e) {
                Log::warning('Courier email failed: ' . $e->getMessage());
            }
        }

        self::alertAdmin("📲 Course commande #{$delivery->order_id} proposée à {$courier->name}"
            . ($courier->phone ? " ({$courier->phone})" : '') . '.');
    }

    private static function alertAdmin(string $message): void
    {
        try {
            WhatsAppService::sendToAdmin($message);
        } catch (\Throwable $e) {
            Log::warning('Delivery admin alert failed: ' . $e->getMessage());
        }
    }
}
