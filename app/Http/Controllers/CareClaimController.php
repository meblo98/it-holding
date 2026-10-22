<?php

namespace App\Http\Controllers;

use App\Models\CareClaim;
use App\Models\CareSubscription;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CareClaimController extends Controller
{
    private function mySubscription(int $id): CareSubscription
    {
        $user = Auth::user();
        $clientId = Client::where('user_id', $user->id)->value('id');

        return CareSubscription::where('id', $id)
            ->where(function ($q) use ($user, $clientId) {
                $q->where('user_id', $user->id);
                if ($clientId) {
                    $q->orWhere('client_id', $clientId);
                }
            })
            ->firstOrFail();
    }

    private function myClaim(int $id): CareClaim
    {
        return CareClaim::where('id', $id)->where('user_id', Auth::id())->with(['subscription', 'events'])->firstOrFail();
    }

    public function form(int $care)
    {
        $subscription = $this->mySubscription($care);

        if (!$subscription->can_declare_claim) {
            return redirect()->route('dashboard.care')
                ->with('error', 'Vous ne pouvez pas déclarer de panne pour cet abonnement : certificat non délivré, abonnement expiré, ou une demande est déjà en cours.');
        }

        return view('pages.shop.care-claim-form', ['subscription' => $subscription]);
    }

    public function store(Request $request, int $care)
    {
        $subscription = $this->mySubscription($care);
        abort_unless($subscription->can_declare_claim, 403);

        $data = $request->validate([
            'symptoms'      => ['required', 'string', 'max:200'],
            'description'   => ['required', 'string', 'min:10', 'max:2000'],
            'started_at'    => ['nullable', 'date', 'before_or_equal:today'],
            'drop_mode'     => ['required', 'in:depot,collecte'],
            'contact_phone' => ['required', 'string', 'min:9', 'max:30'],
            'photos'        => ['nullable', 'array', 'max:4'],
            'photos.*'      => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'video'         => ['nullable', 'file', 'mimes:mp4,mov,webm', 'max:20480'],
            'declaration'   => ['accepted'],
        ], [
            'symptoms.required'    => 'Indiquez le symptôme principal.',
            'description.required' => 'Décrivez la panne.',
            'description.min'      => 'Décrivez la panne (au moins 10 caractères).',
            'photos.max'           => 'Vous pouvez ajouter 4 photos au maximum.',
            'photos.*.max'         => 'Chaque photo ne doit pas dépasser 4 Mo.',
            'video.max'            => 'La vidéo ne doit pas dépasser 20 Mo.',
            'declaration.accepted' => 'Vous devez certifier l’exactitude de ces informations.',
        ]);

        $claim = CareClaim::create([
            'number'               => CareClaim::generateNumber(),
            'care_subscription_id' => $subscription->id,
            'user_id'              => Auth::id(),
            'symptoms'             => $data['symptoms'],
            'description'          => $data['description'],
            'started_at'           => $data['started_at'] ?? null,
            'drop_mode'            => $data['drop_mode'],
            'contact_phone'        => $data['contact_phone'],
            'status'               => 'received',
        ]);

        $dir = "care-claims/{$claim->id}";
        $photos = [];
        foreach ((array) $request->file('photos', []) as $photo) {
            $photos[] = Storage::disk('local')->putFileAs($dir, $photo, Str::uuid() . '.' . $photo->extension());
        }
        $video = null;
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $video = Storage::disk('local')->putFileAs($dir, $file, 'video-' . Str::uuid() . '.' . $file->extension());
        }
        $claim->update(['photos' => $photos, 'video_path' => $video]);

        $claim->logEvent('received', 'Demande déclarée par le client.', Auth::id());

        return redirect()->route('dashboard.care.claim.show', $claim->id)
            ->with('success', 'Votre demande ' . $claim->number . ' a bien été reçue. Vous pouvez suivre son avancement ici.');
    }

    public function show(int $claim)
    {
        return view('pages.shop.care-claim-show', ['claim' => $this->myClaim($claim)]);
    }

    public function cancel(int $claim)
    {
        $claim = $this->myClaim($claim);
        abort_unless($claim->can_cancel, 403, 'Cette demande ne peut plus être annulée.');

        $claim->update(['status' => 'cancelled']);
        $claim->logEvent('cancelled', 'Demande annulée par le client.', Auth::id());

        return redirect()->route('dashboard.care')->with('success', 'Votre demande a été annulée.');
    }

    public function file(int $claim, string $type, int $index = 0)
    {
        $claim = $this->myClaim($claim);
        $path = $type === 'video' ? $claim->video_path : ($claim->photos[$index] ?? null);
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }
}
