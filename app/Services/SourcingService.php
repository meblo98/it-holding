<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SourcingRequest;
use App\Models\Supplier;
use App\Models\SupplierOffer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Appel d'offres automatique : une commande confirmée est proposée aux fournisseurs actifs de la zone,
 * puis la meilleure offre est retenue selon le score (prix, délai, proximité, fiabilité).
 */
class SourcingService
{
    /** Démarre l'appel d'offres d'une commande confirmée (idempotent). */
    public static function startForOrder(Order $order): ?SourcingRequest
    {
        $order = $order->fresh();
        if (!$order || in_array($order->status, ['cancelled', 'canceled'], true)) {
            return null;
        }

        $confirmed = $order->payment_method === 'cod'
            || $order->payment_status === 'paid'
            || $order->status === 'processing';

        if (!$confirmed || $order->items()->count() === 0) {
            return null;
        }

        if (SourcingRequest::where('order_id', $order->id)->exists()) {
            return null;
        }

        try {
            $request = SourcingRequest::create([
                'order_id'    => $order->id,
                'status'      => 'open',
                'deadline_at' => now()->addMinutes((int) config('delivery.sourcing_minutes', 15)),
            ]);
        } catch (QueryException $e) {
            return null; // déjà créée par un autre déclenchement
        }

        self::launch($request, $order);

        return $request;
    }

    /** Localise la commande puis invite les fournisseurs. */
    private static function launch(SourcingRequest $request, Order $order): void
    {
        if (!self::locate($order)) {
            $request->update(['status' => 'needs_location', 'notes' => 'Position de livraison introuvable : à renseigner.']);
            self::alertAdmin("⚠️ Commande #{$order->id} : position de livraison introuvable. Renseignez-la dans Appels d'offres pour lancer la recherche.");

            return;
        }

        $invited = self::invite($request, $order);

        if ($invited === 0) {
            $request->update(['status' => 'failed', 'notes' => 'Aucun fournisseur actif dans la zone de livraison.']);
            self::alertAdmin("⚠️ Commande #{$order->id} : aucun fournisseur actif dans la zone. Ajustez les fournisseurs puis relancez.");
        }
    }

    /** GPS du client, sinon position approximative de la ville trouvée dans l'adresse. */
    private static function locate(Order $order): bool
    {
        if ($order->delivery_lat !== null && $order->delivery_lng !== null) {
            return true;
        }

        $haystack = Str::slug(Str::ascii((string) $order->customer_address));
        foreach (config('delivery.cities', []) as $key => [$lat, $lng]) {
            if ($key !== '' && Str::contains($haystack, $key)) {
                $order->forceFill(['delivery_lat' => $lat, 'delivery_lng' => $lng, 'delivery_source' => 'city'])->saveQuietly();

                return true;
            }
        }

        return false;
    }

    public static function cityLabel(Order $order): string
    {
        $haystack = Str::slug(Str::ascii((string) $order->customer_address));
        foreach (array_keys(config('delivery.cities', [])) as $key) {
            if (Str::contains($haystack, $key)) {
                return Str::title(str_replace('-', ' ', $key));
            }
        }

        return 'Zone de livraison';
    }

    private static function invite(SourcingRequest $request, Order $order): int
    {
        $count = 0;
        $links = [];

        foreach (Supplier::active()->get() as $supplier) {
            $distance = null;
            if ($supplier->latitude !== null && $supplier->longitude !== null) {
                $distance = GeoDistance::km($order->delivery_lat, $order->delivery_lng, $supplier->latitude, $supplier->longitude);
                $radius = $supplier->service_radius_km ?: (int) config('delivery.default_radius_km', 30);
                if ($distance > $radius) {
                    continue;
                }
            }

            $offer = SupplierOffer::create([
                'sourcing_request_id' => $request->id,
                'supplier_id'         => $supplier->id,
                'token'               => Str::random(40),
                'status'              => 'invited',
                'distance_km'         => $distance,
                'invited_at'          => now(),
            ]);
            $count++;

            $url = route('supplier.offer.show', $offer->token);
            $message = "IT Holding : nouvelle demande d'offre (commande #{$order->id}). Répondez avant "
                . $request->deadline_at->format('H:i') . " : {$url}";

            $emailed = false;
            if ($supplier->email) {
                try {
                    Mail::raw($message, fn ($m) => $m->to($supplier->email)->subject('Demande d\'offre IT Holding #' . $order->id));
                    $emailed = true;
                } catch (\Throwable $e) {
                    Log::warning('Supplier offer email failed: ' . $e->getMessage());
                }
            }

            $links[] = ($emailed ? '✉️' : '📲') . " {$supplier->name} ({$supplier->phone}) : {$url}";
        }

        if ($count > 0) {
            self::alertAdmin("📦 Commande #{$order->id} : demande d'offre envoyée à {$count} fournisseur(s) jusqu'à "
                . $request->deadline_at->format('H:i') . ".\n" . implode("\n", $links) . "\n(📲 = à transmettre par WhatsApp, pas d'e-mail)");
        }

        return $count;
    }

    /** Le fournisseur propose un prix et un délai. */
    public static function recordOffer(SupplierOffer $offer, int $price, int $delayHours, ?string $note): bool
    {
        $request = $offer->request;
        if (!self::isOpen($request)) {
            return false;
        }

        $offer->update([
            'status'       => 'offered',
            'price'        => $price,
            'delay_hours'  => $delayHours,
            'note'         => $note,
            'responded_at' => now(),
        ]);

        self::maybeEvaluate($request);

        return true;
    }

    public static function recordDecline(SupplierOffer $offer): bool
    {
        $request = $offer->request;
        if (!self::isOpen($request)) {
            return false;
        }

        $offer->update(['status' => 'declined', 'responded_at' => now()]);
        self::maybeEvaluate($request);

        return true;
    }

    public static function isOpen(SourcingRequest $request): bool
    {
        return $request->status === 'open' && $request->deadline_at && $request->deadline_at->isFuture();
    }

    /** Dès que tous les fournisseurs invités ont répondu, on tranche sans attendre la fin du délai. */
    private static function maybeEvaluate(SourcingRequest $request): void
    {
        if (!$request->offers()->where('status', 'invited')->exists()) {
            self::evaluate($request, true);
        }
    }

    /** Choisit la meilleure offre. $force ignore le délai restant. */
    public static function evaluate(SourcingRequest $request, bool $force = false): void
    {
        $winner = null;

        DB::transaction(function () use ($request, $force, &$winner) {
            $request = SourcingRequest::lockForUpdate()->find($request->id);
            if (!$request || $request->status !== 'open') {
                return;
            }
            if (!$force && $request->deadline_at->isFuture() && $request->offers()->where('status', 'invited')->exists()) {
                return;
            }

            $request->offers()->where('status', 'invited')->update(['status' => 'expired']);
            $offered = $request->offers()->with('supplier')->where('status', 'offered')->get();

            if ($offered->isEmpty()) {
                $request->update(['status' => 'failed', 'notes' => 'Aucune offre reçue dans le délai.']);

                return;
            }

            $scores = self::score($offered);
            foreach ($offered as $offer) {
                $offer->update(['score' => $scores[$offer->id]]);
            }

            $winner = $offered->sort(function ($a, $b) use ($scores) {
                return [$scores[$b->id], $a->price] <=> [$scores[$a->id], $b->price];
            })->first();

            $request->offers()->where('status', 'offered')->where('id', '!=', $winner->id)->update(['status' => 'rejected']);
            $winner->update(['status' => 'selected']);
            $request->update([
                'status'           => 'awarded',
                'awarded_offer_id' => $winner->id,
                'awarded_at'       => now(),
                'award_mode'       => 'auto',
            ]);
        });

        $request->refresh();
        if ($request->status === 'awarded') {
            self::afterAward($request);
        } elseif ($request->status === 'failed') {
            self::alertAdmin("⚠️ Commande #{$request->order_id} : aucune offre reçue. Relancez l'appel d'offres ou contactez un fournisseur.");
        }
    }

    /** L'admin retient lui-même une offre (cas particulier). */
    public static function awardManually(SourcingRequest $request, SupplierOffer $offer): void
    {
        abort_unless($offer->sourcing_request_id === $request->id && $offer->status === 'offered', 422);
        abort_unless($request->status === 'open', 422);

        DB::transaction(function () use ($request, $offer) {
            $request->offers()->where('status', 'invited')->update(['status' => 'expired']);
            $request->offers()->where('status', 'offered')->where('id', '!=', $offer->id)->update(['status' => 'rejected']);
            $offer->update(['status' => 'selected']);
            $request->update(['status' => 'awarded', 'awarded_offer_id' => $offer->id, 'awarded_at' => now(), 'award_mode' => 'manual']);
        });

        self::afterAward($request->fresh());
    }

    /** Relance complète (aucune offre, position corrigée…). */
    public static function relaunch(SourcingRequest $request): void
    {
        abort_unless(in_array($request->status, ['failed', 'needs_location', 'cancelled'], true), 422);

        $request->offers()->delete();
        $request->update([
            'status'           => 'open',
            'awarded_offer_id' => null,
            'awarded_at'       => null,
            'award_mode'       => null,
            'notes'            => null,
            'deadline_at'      => now()->addMinutes((int) config('delivery.sourcing_minutes', 15)),
        ]);

        self::launch($request, $request->order);
    }

    /** Ferme les appels d'offres dont le délai est dépassé (appelé chaque minute). */
    public static function tick(): int
    {
        $due = SourcingRequest::where('status', 'open')->where('deadline_at', '<=', now())->get();
        foreach ($due as $request) {
            self::evaluate($request, true);
        }

        return $due->count();
    }

    /** Point d'accroche de l'étape suivante : attribution de la livraison à un livreur. */
    private static function afterAward(SourcingRequest $request): void
    {
        $offer = $request->awardedOffer()->with('supplier')->first();
        self::alertAdmin("✅ Commande #{$request->order_id} : offre retenue de {$offer->supplier->name} ("
            . number_format($offer->price, 0, ',', ' ') . " FCFA, {$offer->delay_hours} h, score {$offer->score}/100).");
    }

    /** Scores sur 100 selon les poids de config/delivery.php. @return array<int,float> offre => score */
    private static function score($offers): array
    {
        $w = config('delivery.weights');
        $minPrice = max(1, (int) $offers->min('price'));
        $minDelay = max(1, (int) $offers->min('delay_hours'));
        $minDist = $offers->whereNotNull('distance_km')->min('distance_km');

        $scores = [];
        foreach ($offers as $offer) {
            $price = $minPrice / max(1, (int) $offer->price);
            $delay = $minDelay / max(1, (int) $offer->delay_hours);
            $prox = $offer->distance_km === null ? 0.5 : (($minDist ?? 0) + 1) / ($offer->distance_km + 1);
            $rel = max(0, min(5, (float) $offer->supplier->rating)) / 5;

            $scores[$offer->id] = round(100 * ($w['price'] * $price + $w['delay'] * $delay + $w['proximity'] * $prox + $w['reliability'] * $rel), 2);
        }

        return $scores;
    }

    private static function alertAdmin(string $message): void
    {
        try {
            WhatsAppService::sendToAdmin($message);
        } catch (\Throwable $e) {
            Log::warning('Sourcing admin alert failed: ' . $e->getMessage());
        }
    }
}
