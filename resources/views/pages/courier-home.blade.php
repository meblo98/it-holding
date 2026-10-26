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

        <p class="mt-4 text-xs text-gray-400">Les courses qui vous sont proposées apparaîtront ici dès la prochaine étape de la livraison automatique.</p>
    </div>
</section>
@endsection
