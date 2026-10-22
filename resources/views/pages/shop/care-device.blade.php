@extends('layouts.app')

@section('title', 'Enregistrer mon appareil - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('layouts.client_sidebar')

            <main class="flex-1">
                <a href="{{ route('dashboard.care') }}" class="text-sm text-gray-500 hover:text-navy-900">← Mon Care+</a>
                <h1 class="mt-3 text-xl font-semibold text-navy-900">Enregistrer mon appareil</h1>
                <p class="text-sm text-gray-500">Abonnement {{ $subscription->number }} · formule {{ ucfirst($subscription->plan) }}. Notre équipe vérifie ensuite l'admissibilité avant de délivrer votre certificat.</p>

                @if ($subscription->device_status === 'rejected' && $subscription->device_rejection_reason)
                    <p class="mt-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">Votre précédente demande a été refusée : {{ $subscription->device_rejection_reason }}</p>
                @endif

                <form method="POST" action="{{ route('dashboard.care.device.store', $subscription->id) }}" enctype="multipart/form-data" class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-navy-900">Type d'appareil</label>
                        <select name="device_category" required class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}" @selected(old('device_category') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('device_category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-navy-900">Marque</label>
                            <input name="device_brand" value="{{ old('device_brand') }}" required maxlength="80" placeholder="HP, Dell, Lenovo…" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                            @error('device_brand')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-navy-900">Modèle</label>
                            <input name="device_model" value="{{ old('device_model') }}" required maxlength="120" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                            @error('device_model')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-navy-900">Numéro de série</label>
                            <input name="serial_number" value="{{ old('serial_number') }}" required maxlength="100" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                            @error('serial_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-navy-900">Date d'achat (si connue)</label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" max="{{ now()->toDateString() }}" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">
                            @error('purchase_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-900">État actuel de l'appareil</label>
                        <textarea name="initial_condition" rows="4" required maxlength="1000" placeholder="Fonctionne normalement ? Rayures, défauts existants, pannes passées…" class="mt-2 w-full rounded-md border border-gray-200 px-3 py-2.5">{{ old('initial_condition') }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Déclarez honnêtement les défauts existants : les pannes antérieures non déclarées ne sont pas couvertes.</p>
                        @error('initial_condition')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-900">Photos de l'appareil (2 à 4)</label>
                        <input type="file" name="photos[]" multiple required accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm">
                        <p class="mt-1 text-xs text-gray-400">Face avant, écran allumé, étiquette du numéro de série. 4 Mo maximum par photo.</p>
                        @error('photos')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        @error('photos.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-900">Facture ou preuve d'achat (facultatif)</label>
                        <input type="file" name="invoice" accept="image/jpeg,image/png,application/pdf" class="mt-2 block w-full text-sm">
                        @error('invoice')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="flex items-start gap-3 text-sm text-gray-600">
                            <input type="checkbox" name="declaration" value="1" class="mt-1" {{ old('declaration') ? 'checked' : '' }}>
                            <span>Je certifie que ces informations sont exactes et que l'appareil m'appartient.</span>
                        </label>
                        @error('declaration')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-primary-gold w-full justify-center">Envoyer pour vérification</button>
                </form>
            </main>
        </div>
    </div>
</div>
@endsection
