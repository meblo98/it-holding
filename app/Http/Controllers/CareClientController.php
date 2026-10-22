<?php

namespace App\Http\Controllers;

use App\Models\CareSubscription;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CareClientController extends Controller
{
    private const CATEGORIES = [
        'laptop'  => 'Ordinateur portable',
        'desktop' => 'Ordinateur fixe',
    ];

    /** Abonnements appartenant au client connecté. */
    private function mine()
    {
        $user = Auth::user();
        $clientId = Client::where('user_id', $user->id)->value('id');

        return CareSubscription::query()->where(function ($q) use ($user, $clientId) {
            $q->where('user_id', $user->id);
            if ($clientId) {
                $q->orWhere('client_id', $clientId);
            }
        });
    }

    public function index()
    {
        return view('pages.shop.care-espace', [
            'subscriptions' => $this->mine()->with('claims')->latest()->get(),
            'plans'         => config('care.plans'),
        ]);
    }

    public function deviceForm(int $care)
    {
        $subscription = $this->mine()->findOrFail($care);
        abort_unless($subscription->can_register_device, 403, 'Cet abonnement ne permet pas d’enregistrer un appareil pour le moment.');

        return view('pages.shop.care-device', [
            'subscription' => $subscription,
            'categories'   => self::CATEGORIES,
        ]);
    }

    public function storeDevice(Request $request, int $care)
    {
        $subscription = $this->mine()->findOrFail($care);
        abort_unless($subscription->can_register_device, 403);

        $data = $request->validate([
            'device_category'   => ['required', 'in:' . implode(',', array_keys(self::CATEGORIES))],
            'device_brand'      => ['required', 'string', 'max:80'],
            'device_model'      => ['required', 'string', 'max:120'],
            'serial_number'     => ['required', 'string', 'max:100'],
            'purchase_date'     => ['nullable', 'date', 'before_or_equal:today'],
            'initial_condition' => ['required', 'string', 'min:10', 'max:1000'],
            'photos'            => ['required', 'array', 'min:2', 'max:4'],
            'photos.*'          => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'invoice'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'declaration'       => ['accepted'],
        ], [
            'photos.required'      => 'Ajoutez au moins 2 photos de l’appareil.',
            'photos.min'           => 'Ajoutez au moins 2 photos de l’appareil.',
            'photos.max'           => 'Vous pouvez ajouter 4 photos au maximum.',
            'photos.*.image'       => 'Chaque photo doit être une image.',
            'photos.*.max'         => 'Chaque photo ne doit pas dépasser 4 Mo.',
            'invoice.max'          => 'La facture ne doit pas dépasser 4 Mo.',
            'initial_condition.min' => 'Décrivez l’état de l’appareil (au moins 10 caractères).',
            'declaration.accepted' => 'Vous devez certifier l’exactitude de ces informations.',
        ]);

        $serial = strtoupper(trim($data['serial_number']));

        $already = CareSubscription::where('id', '!=', $subscription->id)
            ->whereRaw('UPPER(serial_number) = ?', [$serial])
            ->whereIn('device_status', ['pending', 'approved'])
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($already) {
            return back()->withInput()->withErrors(['serial_number' => 'Cet appareil est déjà enregistré sous un autre abonnement.']);
        }

        // Remplacement d'une précédente soumission refusée
        $this->deleteDeviceFiles($subscription);

        $dir = "care-devices/{$subscription->id}";
        $photos = [];
        foreach ($request->file('photos') as $photo) {
            $photos[] = Storage::disk('local')->putFileAs($dir, $photo, Str::uuid() . '.' . $photo->extension());
        }

        $invoice = null;
        if ($request->hasFile('invoice')) {
            $file = $request->file('invoice');
            $invoice = Storage::disk('local')->putFileAs($dir, $file, 'facture-' . Str::uuid() . '.' . $file->extension());
        }

        $subscription->update([
            'device_category'         => $data['device_category'],
            'device_brand'            => $data['device_brand'],
            'device_model'            => $data['device_model'],
            'product_name'            => trim($data['device_brand'] . ' ' . $data['device_model']),
            'serial_number'           => $serial,
            'purchase_date'           => $data['purchase_date'] ?? null,
            'initial_condition'       => $data['initial_condition'],
            'device_photos'           => $photos,
            'invoice_path'            => $invoice,
            'device_status'           => 'pending',
            'device_rejection_reason' => null,
            'device_submitted_at'     => now(),
            'device_reviewed_at'      => null,
        ]);

        return redirect()->route('dashboard.care')
            ->with('success', 'Appareil enregistré. Notre équipe vérifie son admissibilité, puis votre certificat sera disponible.');
    }

    /** Photo ou facture : accessible uniquement à son propriétaire. */
    public function file(int $care, string $type, int $index = 0)
    {
        $subscription = $this->mine()->findOrFail($care);

        $path = $type === 'invoice'
            ? $subscription->invoice_path
            : ($subscription->device_photos[$index] ?? null);

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }

    public function certificate(int $care)
    {
        $subscription = $this->mine()->findOrFail($care);
        abort_unless($subscription->has_certificate, 403, 'Le certificat sera disponible après la validation de votre appareil.');

        $token = $subscription->ensurePublicToken();
        if (!$subscription->certificate_issued_at) {
            $subscription->update(['certificate_issued_at' => now()]);
        }

        $verifyUrl = route('care.verify', $token);

        return view('pages.shop.care-certificate', [
            'subscription' => $subscription,
            'plan'         => config('care.plans.' . $subscription->plan),
            'verifyUrl'    => $verifyUrl,
            'qrUrl'        => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($verifyUrl),
        ]);
    }

    /** Page publique ouverte par le QR code : aucune donnée personnelle. */
    public function verify(string $token)
    {
        $subscription = CareSubscription::where('public_token', $token)->first();

        return view('pages.care-verify', ['subscription' => $subscription]);
    }

    private function deleteDeviceFiles(CareSubscription $subscription): void
    {
        foreach ((array) $subscription->device_photos as $path) {
            Storage::disk('local')->delete($path);
        }
        if ($subscription->invoice_path) {
            Storage::disk('local')->delete($subscription->invoice_path);
        }
    }
}
