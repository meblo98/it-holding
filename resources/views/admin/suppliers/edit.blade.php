@extends('layouts.admin')

@section('title', 'Modifier le Fournisseur ' . $supplier->name)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Modifier le Fournisseur</h1>
        <p class="text-sm text-gray-500 mt-1">Mettez à jour les coordonnées de {{ $supplier->name }}.</p>
    </div>
    <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none transition">
        Retour à la liste
    </a>
</div>

<div class="max-w-3xl bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100 p-6">
    <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700">Nom de l'Entreprise / Raison Sociale *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $supplier->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Person -->
                <div>
                    <label for="contact_person" class="block text-sm font-semibold text-gray-700">Nom du Contact Principal</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    @error('contact_person')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Livraison automatique : zone et fiabilité -->
                <div class="border-t border-gray-200 pt-4 space-y-4"
                     x-data="{ state: 'idle', locate() {
                        if (!navigator.geolocation) { this.state = 'unsupported'; return; }
                        this.state = 'loading';
                        navigator.geolocation.getCurrentPosition(
                            p => { $refs.lat.value = p.coords.latitude.toFixed(7); $refs.lng.value = p.coords.longitude.toFixed(7); this.state = 'ok'; },
                            () => { this.state = 'denied'; },
                            { enableHighAccuracy: true, timeout: 15000 }
                        );
                     } }">
                    <p class="text-sm font-bold text-gray-700">Livraison automatique</p>
                    <p class="text-xs text-gray-500">Position du fournisseur et rayon dans lequel il peut répondre aux demandes. Utilisée pour la proximité.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Latitude</label>
                            <input type="number" step="any" name="latitude" x-ref="lat" value="{{ old('latitude', $supplier->latitude) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                            @error('latitude')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Longitude</label>
                            <input type="number" step="any" name="longitude" x-ref="lng" value="{{ old('longitude', $supplier->longitude) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                            @error('longitude')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <button type="button" @click="locate()" class="text-sm font-semibold text-navy-700 underline">Utiliser ma position actuelle</button>
                    <span x-show="state === 'ok'" class="text-xs text-green-700">Position récupérée.</span>
                    <span x-show="state === 'denied'" class="text-xs text-red-600">Localisation refusée.</span>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Rayon de service (km)</label>
                            <input type="number" name="service_radius_km" min="1" max="500" value="{{ old('service_radius_km', $supplier->service_radius_km) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Fiabilité (0 à 5)</label>
                            <input type="number" name="rating" min="0" max="5" step="0.5" value="{{ old('rating', $supplier->rating) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                        Fournisseur actif (reçoit les demandes d'offre)
                    </label>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700">Téléphone de contact</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700">Adresse Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $supplier->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-semibold text-gray-700">Adresse Physique / Siège Social</label>
                    <textarea name="address" id="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">{{ old('address', $supplier->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-navy-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-navy-700 transition">
                    Mettre à jour
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
