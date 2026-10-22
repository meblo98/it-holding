<?php

namespace App\Http\Controllers;

use App\Models\CareSubscription;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CareCheckoutController extends Controller
{
    public function show(string $plan)
    {
        $p = $this->planOrFail($plan);

        return view('pages.care-subscribe', [
            'planKey'  => $plan,
            'plan'     => $p,
            'user'     => Auth::user(),
            'duration' => (int) config('care.duration_months', 12),
        ]);
    }

    public function store(Request $request, string $plan)
    {
        $p = $this->planOrFail($plan);

        $data = $request->validate([
            'phone'        => ['required', 'string', 'min:9', 'max:20'],
            'accept_terms' => ['accepted'],
        ], [
            'phone.required'  => 'Le numéro de téléphone est obligatoire.',
            'phone.min'       => 'Le numéro de téléphone est trop court.',
            'accept_terms.accepted' => 'Vous devez accepter les conditions de la garantie pour continuer.',
        ]);

        $user   = Auth::user();
        $months = (int) config('care.duration_months', 12);

        $subscription = CareSubscription::create([
            'number'               => CareSubscription::generateNumber(),
            'user_id'              => $user->id,
            'client_id'            => optional(Client::where('user_id', $user->id)->first())->id,
            'client_name'          => $user->name,
            'client_phone'         => $data['phone'],
            'product_name'         => 'À enregistrer',
            'plan'                 => $plan,
            'start_date'           => today(),
            'end_date'             => today()->addMonths($months),
            'duration_months'      => $months,
            'price'                => $p['price'],
            'has_priority_support' => $plan !== 'basique',
            'has_repair_discount'  => true,
            'repair_discount_pct'  => $p['labor_discount'],
            'has_parts_discount'   => true,
            'parts_discount_pct'   => $p['parts_discount'],
            'has_home_service'     => false,
            'status'               => 'pending',
            'payment_status'       => 'pending',
            'payment_method'       => 'bictorys',
            'amount_paid'          => 0,
            'notes'                => 'Souscription en ligne',
        ]);

        try {
            $checkoutUrl = $this->createBictorysCharge($subscription, $user->email);
        } catch (\Throwable $e) {
            Log::error('Care subscription payment initiation failed: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
            ]);
            $subscription->update(['status' => 'cancelled', 'payment_status' => 'failed']);

            return back()->withInput()->with('error', 'Le paiement en ligne est momentanément indisponible. Réessayez dans quelques instants ou contactez-nous.');
        }

        return redirect()->away($checkoutUrl);
    }

    /** Retour du client après le paiement. L'activation n'est faite que par le webhook. */
    public function success(Request $request)
    {
        $subscription = CareSubscription::findOrFail((int) $request->query('subscription'));
        abort_unless($subscription->user_id === Auth::id(), 403);

        return view('pages.care-success', ['subscription' => $subscription]);
    }

    public function error(Request $request)
    {
        $subscription = CareSubscription::findOrFail((int) $request->query('subscription'));
        abort_unless($subscription->user_id === Auth::id(), 403);

        if ($subscription->payment_status === 'pending') {
            $subscription->update(['status' => 'cancelled', 'payment_status' => 'failed']);
        }

        return redirect()->route('care.subscribe', $subscription->plan)
            ->with('error', 'Le paiement a échoué ou a été annulé. Vous pouvez réessayer.');
    }

    /**
     * Appelé par ShopController::bictorysWebhook() quand la référence commence par « CARE- ».
     * La signature du webhook y est déjà vérifiée.
     */
    public function webhook(array $payload)
    {
        $reference = (string) ($payload['merchantReference'] ?? $payload['paymentReference'] ?? '');
        $subscription = CareSubscription::find((int) substr($reference, 5));

        if (!$subscription) {
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        $status = $payload['status'] ?? '';
        $event  = $payload['event'] ?? '';
        $isSuccessful = ($status === 'succeeded' || $status === 'successful' || $event === 'charge.successful');

        if ($isSuccessful) {
            $payloadAmount = $payload['amount'] ?? null;
            if ($payloadAmount !== null && (int) $payloadAmount !== (int) round((float) $subscription->price)) {
                Log::warning("Bictorys webhook amount mismatch for care subscription #{$subscription->id}", [
                    'expected' => $subscription->price,
                    'received' => $payloadAmount,
                ]);
                return response()->json(['error' => 'Amount mismatch'], 422);
            }

            if ($subscription->payment_status !== 'paid') {
                $months = (int) ($subscription->duration_months ?: 12);
                $subscription->update([
                    'payment_status' => 'paid',
                    'status'         => 'active',
                    'amount_paid'    => $subscription->price,
                    'paid_at'        => now(),
                    'start_date'     => today(),
                    'end_date'       => today()->addMonths($months),
                ]);
                Log::info("Care subscription #{$subscription->id} paid via Bictorys webhook.");
            }
        } elseif ($status === 'failed' || $event === 'charge.failed') {
            if ($subscription->payment_status !== 'paid') {
                $subscription->update(['status' => 'cancelled', 'payment_status' => 'failed']);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function planOrFail(string $plan): array
    {
        $plans = config('care.plans', []);
        abort_unless(isset($plans[$plan]), 404);

        return $plans[$plan];
    }

    private function chargesEndpoint(): string
    {
        $baseUrl = rtrim(config('services.bictorys.base_url'), '/');
        if (!str_contains($baseUrl, '/pay/v1')) {
            $baseUrl .= '/pay/v1';
        }

        return $baseUrl . '/charges';
    }

    private function createBictorysCharge(CareSubscription $subscription, ?string $email): string
    {
        $reference = 'CARE-' . $subscription->id;

        $response = Http::withHeaders([
            'X-Api-Key'    => config('services.bictorys.key'),
            'Content-Type' => 'application/json',
        ])->post($this->chargesEndpoint(), [
            'amount'            => (int) $subscription->price,
            'currency'          => 'XOF',
            'paymentReference'  => $reference,
            'merchantReference' => $reference,
            'successRedirectUrl' => URL::signedRoute('care.success', ['subscription' => $subscription->id]),
            'errorRedirectUrl'   => URL::signedRoute('care.error', ['subscription' => $subscription->id]),
            'customerObject'    => [
                'name'  => $subscription->client_name,
                'phone' => $subscription->client_phone,
                'email' => $email,
            ],
        ]);

        $redirectUrl = $response->successful()
            ? ($response->json('link') ?? $response->json('redirectUrl'))
            : null;

        if (!$redirectUrl) {
            throw new \Exception('Bictorys payment session creation failed: ' . $response->body());
        }

        return $redirectUrl;
    }
}
