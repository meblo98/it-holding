@extends('layouts.app')

@section('title', 'Souscrire - Garantie ' . $plan['label'] . ' - ' . config('app.name'))

@section('content')
@php $fmt = fn ($n) => number_format($n, 0, ',', ' ') . ' FCFA'; @endphp
<section class="bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <a href="{{ route('care.landing') }}#formules" class="text-sm text-navy-500 hover:text-navy-900">← Retour aux formules</a>
        <h1 class="mt-4 text-3xl font-semibold text-navy-900 tracking-tight">Souscrire à la formule {{ $plan['label'] }}</h1>

        <div class="mt-8 border border-navy-100 rounded-md p-6">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-lg font-semibold text-navy-900">{{ $plan['label'] }} · {{ $duration }} mois</h2>
                <p class="text-2xl font-semibold text-navy-900">{{ $fmt($plan['price']) }}</p>
            </div>
            <ul class="mt-4 space-y-2 text-sm text-navy-600">
                <li>{{ $plan['coverage_rate'] }} % des frais de réparation admissibles pris en charge</li>
                <li>Plafond par réparation : {{ $fmt($plan['per_repair_cap']) }}</li>
                <li>Plafond sur 12 mois : {{ $fmt($plan['per_year_cap']) }}</li>
                <li>Traitement : {{ $plan['priority'] }}</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('care.subscribe.store', $planKey) }}" class="mt-8 space-y-6">
            @csrf

            <div>
                <label for="phone" class="block text-sm font-medium text-navy-900">Numéro de téléphone</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required inputmode="tel"
                       placeholder="77 000 00 00" class="mt-2 w-full rounded-md border border-navy-200 px-3 py-2.5">
                <p class="mt-1 text-xs text-navy-400">Compte : {{ $user->name }} ({{ $user->email }})</p>
                @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <p class="text-sm font-medium text-navy-900">Moyens de paiement</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span class="px-3 py-1.5 rounded-md border border-navy-200 text-sm text-navy-900">Wave</span>
                    <span class="px-3 py-1.5 rounded-md border border-navy-200 text-sm text-navy-900">Orange Money</span>
                    <span class="px-3 py-1.5 rounded-md border border-navy-200 text-sm text-navy-900">Carte bancaire</span>
                </div>
                <p class="mt-2 text-xs text-navy-400">Vous choisirez votre moyen de paiement sur la page sécurisée de paiement, après avoir cliqué sur « Payer ».</p>
            </div>

            <div>
                <label class="flex items-start gap-3 text-sm text-navy-600">
                    <input type="checkbox" name="accept_terms" value="1" class="mt-1" {{ old('accept_terms') ? 'checked' : '' }}>
                    <span>J’ai lu et j’accepte les <a href="{{ route('care.landing') }}#couverture" target="_blank" class="underline text-navy-900">conditions de couverture et les exclusions</a>. Je comprends qu’il s’agit d’une garantie commerciale de réparation, et non d’une assurance.</span>
                </label>
                @error('accept_terms')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn-primary-gold w-full justify-center">Payer {{ $fmt($plan['price']) }}</button>
            <p class="text-xs text-navy-400">La protection est activée dès la confirmation du paiement. Vous enregistrerez ensuite votre appareil depuis votre espace.</p>
        </form>
    </div>
</section>
@endsection
