<?php

namespace App\Services;

use App\Models\CourierProfile;
use App\Models\Delivery;
use App\Models\PurchaseOrder;
use App\Models\SourcingRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            'delivery_token'    => Str::random(40),
            'delivery_code'     => sprintf('%06d', random_int(0, 999999)),
        ]);

        // Bon de commande du fournisseur retenu (QR code + code de remise).
        $bon = PurchaseOrder::create([
            'number'            => PurchaseOrder::generateNumber(),
            'order_id'          => $order->id,
            'supplier_id'       => $supplier->id,
            'supplier_offer_id' => $offer->id,
            'supplier_token'    => Str::random(40),
            'pickup_token'      => Str::random(40),
            'pickup_code'       => sprintf('%06d', random_int(0, 999999)),
            'agreed_price'      => (int) $offer->price,
            'delay_hours'       => $offer->delay_hours,
            'status'            => 'issued',
        ]);
        self::notifySupplierOfBon($bon);

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

    /**
     * Remise du colis au livreur : par scan du QR du bon de commande ($method = 'qr')
     * ou par le code donné par le fournisseur ($method = 'code'). Retourne un message d'erreur, ou null si tout va bien.
     */
    public static function confirmPickup(Delivery $delivery, int $userId, string $method, ?string $code = null): ?string
    {
        if ($delivery->courier_id !== $userId || $delivery->status !== 'accepted') {
            return 'Cette course n’est pas en attente de récupération.';
        }

        $bon = PurchaseOrder::where('order_id', $delivery->order_id)->first();
        if (!$bon) {
            return 'Bon de commande introuvable : contactez IT Holding.';
        }

        if ($method === 'code') {
            if ($bon->pickup_attempts >= 5) {
                return 'Trop de tentatives. Contactez IT Holding pour valider la remise.';
            }
            if (!hash_equals($bon->pickup_code, trim((string) $code))) {
                $bon->increment('pickup_attempts');

                return 'Code incorrect.';
            }
        }

        self::applyPickup($delivery, $bon, $method);

        return null;
    }

    /** Livraison au client : scan du QR du client ou code qu'il donne. */
    public static function confirmDelivery(Delivery $delivery, int $userId, string $method, ?string $code = null): ?string
    {
        if ($delivery->courier_id !== $userId || $delivery->status !== 'picked_up') {
            return 'Cette course n’est pas en cours de livraison.';
        }

        if ($method === 'code') {
            if ($delivery->dropoff_attempts >= 5) {
                return 'Trop de tentatives. Contactez IT Holding pour valider la livraison.';
            }
            if (!$delivery->delivery_code || !hash_equals($delivery->delivery_code, trim((string) $code))) {
                $delivery->increment('dropoff_attempts');

                return 'Code incorrect.';
            }
        }

        self::applyDelivery($delivery, $method);

        return null;
    }

    private static function applyPickup(Delivery $delivery, ?PurchaseOrder $bon, string $method): void
    {
        DB::transaction(function () use ($delivery, $bon, $method) {
            $bon?->update(['status' => 'handed_over', 'handed_over_at' => now()]);
            $delivery->update(['status' => 'picked_up', 'picked_up_at' => now(), 'pickup_method' => $method]);
        });
    }

    private static function applyDelivery(Delivery $delivery, string $method): void
    {
        $delivery->update([
            'status'         => 'delivered',
            'delivered_at'   => now(),
            'dropoff_method' => $method,
            'courier_fee'    => self::computeFee($delivery),
        ]);

        self::alertAdmin("✅ Commande #{$delivery->order_id} livrée (validation : {$method}). Pensez à la marquer « terminée » dans les commandes.");
    }

    /** Validation manuelle par l'admin (code perdu, téléphone en panne…). */
    public static function adminForce(Delivery $delivery, string $step): void
    {
        if ($step === 'pickup') {
            abort_unless($delivery->status === 'accepted', 422);
            self::applyPickup($delivery, PurchaseOrder::where('order_id', $delivery->order_id)->first(), 'admin');
        } else {
            abort_unless(in_array($delivery->status, ['accepted', 'picked_up'], true), 422);
            self::applyDelivery($delivery, 'admin');
        }
    }

    /** Revenu du livreur pour une course : forfait + prix au km (config/delivery.php), arrondi à 50 FCFA. */
    public static function computeFee(Delivery $delivery): int
    {
        $cfg = config('delivery.courier_fee', []);

        $km = ($delivery->pickup_lat !== null && $delivery->dropoff_lat !== null)
            ? GeoDistance::km($delivery->pickup_lat, $delivery->pickup_lng, $delivery->dropoff_lat, $delivery->dropoff_lng)
            : (float) ($delivery->distance_km ?? 0);

        $fee = (int) (round((($cfg['base'] ?? 1000) + ($cfg['per_km'] ?? 100) * $km) / 50) * 50);

        return max((int) ($cfg['min'] ?? 0), min((int) ($cfg['max'] ?? PHP_INT_MAX), $fee));
    }

    private static function notifySupplierOfBon(PurchaseOrder $bon): void
    {
        $supplier = $bon->supplier;
        $url = route('purchase.show', $bon->supplier_token);
        $message = "IT Holding : votre offre est retenue ! Bon de commande {$bon->number} — préparez la commande. "
            . "Un livreur viendra la récupérer. Ouvrez le bon (QR code et code de remise) : {$url}";

        $sent = false;
        if ($supplier->email) {
            try {
                Mail::raw($message, fn ($m) => $m->to($supplier->email)->subject('Bon de commande ' . $bon->number));
                $sent = true;
            } catch (\Throwable $e) {
                Log::warning('Purchase order email failed: ' . $e->getMessage());
            }
        }
        if ($supplier->phone && WhatsAppService::send($supplier->phone, $message)) {
            $sent = true;
        }

        self::alertAdmin(($sent ? '📄' : '📲') . " Bon {$bon->number} ({$supplier->name}) : {$url}"
            . ($sent ? '' : "\n(à transmettre au fournisseur par WhatsApp)"));
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
