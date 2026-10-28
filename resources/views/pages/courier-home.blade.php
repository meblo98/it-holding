@extends('layouts.app')

@section('title', 'Espace livreur - ' . config('app.name'))

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="max-w-lg mx-auto px-4 py-10">
        <p class="text-xs font-bold tracking-widest text-gold-600 uppercase">IT Holding Services</p>
        <h1 class="mt-1 text-2xl font-semibold text-navy-900">Espace livreur</h1>
        <p class="text-sm text-gray-500">Bonjour {{ auth()->user()->name }}. Gardez votre position à jour pour recevoir les courses proches de vous.</p>

        @foreach (['success', 'error'] as $flash)
            @if (session($flash))
                <div class="mt-4 rounded-lg border px-4 py-3 text-sm {{ $flash === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }}">{{ session($flash) }}</div>
            @endif
        @endforeach


        {{-- Courses proposées --}}
        @foreach ($offered as $d)
            <div class="mt-6 bg-white rounded-xl border-2 border-gold-500 shadow-sm p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold text-navy-900">Nouvelle course · commande #{{ $d->order_id }}</p>
                    <span class="text-xs text-gray-500">à accepter avant {{ $d->offer_expires_at?->format('H:i') }}</span>
                </div>
                <p class="text-sm text-gray-600">Point de retrait : <strong>{{ $d->pickup_label ?: 'à confirmer' }}</strong> · à {{ number_format($d->distance_km ?? 0, 1) }} km de vous</p>
                <p class="text-sm text-gray-600">{{ $d->order->items->sum('quantity') }} article(s)</p>
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('courier.accept', $d->id) }}" class="flex-1">@csrf
                        <button class="w-full px-4 py-3 rounded-lg bg-green-600 text-white font-semibold text-sm">Accepter</button></form>
                    <form method="POST" action="{{ route('courier.decline', $d->id) }}" class="flex-1">@csrf
                        <button class="w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-700 font-semibold text-sm">Refuser</button></form>
                </div>
            </div>
        @endforeach

        {{-- Courses en cours --}}
        @foreach ($active as $d)
            <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold text-navy-900">Course en cours · commande #{{ $d->order_id }}</p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">{{ $d->status === 'accepted' ? 'À récupérer' : 'En route vers le client' }}</span>
                </div>
                <div class="text-sm text-gray-700 space-y-1">
                    <p><span class="text-gray-400">Retrait :</span> {{ $d->pickup_label ?: '—' }}
                        @if ($d->pickup_maps_url)<a href="{{ $d->pickup_maps_url }}" target="_blank" class="underline text-navy-900">Itinéraire</a>@endif</p>
                    <p><span class="text-gray-400">Client :</span> {{ $d->order->customer_name }} · <a href="tel:{{ $d->order->customer_phone }}" class="underline">{{ $d->order->customer_phone }}</a></p>
                    <p><span class="text-gray-400">Adresse :</span> {{ $d->order->customer_address }}
                        @if ($d->maps_url)<a href="{{ $d->maps_url }}" target="_blank" class="underline text-navy-900">Itinéraire</a>@endif</p>
                    <p class="text-gray-400">Articles :
                        @foreach ($d->order->items as $item)<span class="text-gray-700">{{ $item->product?->name ?? 'Produit' }} ×{{ $item->quantity }}@if(!$loop->last), @endif</span>@endforeach
                    </p>
                    @if ($d->order->payment_method === 'cod')
                        <p class="font-semibold text-red-700">Paiement à la livraison : encaisser {{ number_format($d->order->total_amount, 0, ',', ' ') }} FCFA</p>
                    @endif
                </div>
                @if ($d->status === 'accepted')
                    <form method="POST" action="{{ route('courier.pickedup', $d->id) }}">@csrf
                        <button class="w-full btn-primary-gold justify-center">J'ai récupéré le colis</button></form>
                @else
                    <form method="POST" action="{{ route('courier.delivered', $d->id) }}" onsubmit="return confirm('Confirmer la livraison au client ?')">@csrf
                        <button class="w-full px-4 py-3 rounded-lg bg-green-600 text-white font-semibold text-sm">Colis livré</button></form>
                @endif
            </div>
        @endforeach

        <form method="POST" action="{{ route('courier.update') }}" class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5"
              x-data="{ state: 'idle',
                        locate() {
                            if (!navigator.geolocation) { this.state = 'unsupported'; return; }
                            this.state = 'loading';
                            navigator.geolocation.getCurrentPosition(
                                p => { $refs.lat.value = p.coords.latitude.toFixed(7); $refs.lng.value = p.coords.longitude.toFixed(7); this.state = 'ok'; },
                                () => { this.state = 'denied'; },
                                { enableHighAccuracy: true, timeout: 15000 }
                            );
                        } }">
            @csrf
            <input type="hidden" name="latitude" x-ref="lat">
            <input type="hidden" name="longitude" x-ref="lng">

            <div>
                <p class="text-sm font-medium text-navy-900">Ma position</p>
                <p class="text-xs text-gray-500 mt-1">
                    @if ($profile->has_location)
                        Dernière mise à jour : {{ $profile->location_updated_at?->diffForHumans() }}
                    @else
                        Aucune position enregistrée.
                    @endif
                </p>
                <button type="button" @click="locate()" class="mt-3 w-full px-4 py-3 rounded-lg border border-navy-900 text-navy-900 font-semibold text-sm">
                    <span x-show="state !== 'loading'">Utiliser ma position actuelle</span>
                    <span x-show="state === 'loading'">Recherche de la position…</span>
                </button>
                <p x-show="state === 'ok'" class="mt-2 text-sm text-green-700">Position récupérée. Enregistrez pour la valider.</p>
                <p x-show="state === 'denied'" class="mt-2 text-sm text-red-600">Position refusée. Autorisez la localisation dans votre navigateur puis réessayez.</p>
                <p x-show="state === 'unsupported'" class="mt-2 text-sm text-red-600">Ce navigateur ne gère pas la localisation.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-navy-900">Rayon de livraison</label>
                <select name="radius_km" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                    @foreach ([5, 10, 15, 25, 40] as $km)
                        <option value="{{ $km }}" @selected(old('radius_km', $profile->radius_km) == $km)>{{ $km }} km autour de moi</option>
                    @endforeach
                </select>
            </div>

            <label class="flex items-center gap-3 text-sm text-navy-900">
                <input type="checkbox" name="is_available" value="1" @checked(old('is_available', $profile->is_available))>
                <span class="font-medium">Je suis disponible pour de nouvelles courses</span>
            </label>

            <button type="submit" class="btn-primary-gold w-full justify-center">Enregistrer</button>
        </form>


        @if ($history->isNotEmpty())
            <div class="mt-6 bg-white rounded-xl border border-gray-100 p-6">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Dernières livraisons</p>
                <ul class="divide-y divide-gray-100 text-sm">
                    @foreach ($history as $d)
                        <li class="py-2 flex justify-between"><span>Commande #{{ $d->order_id }}</span><span class="text-gray-500">{{ $d->delivered_at?->format('d/m H:i') }}</span></li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
</section>
@endsection
