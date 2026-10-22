@extends('layouts.app')
@section('title', 'Devenir partenaire - ' . config('app.name'))

@section('content')
@php
    $descriptions = [
        'commercial' => ['Vous prospectez et vendez nos produits et services à vos clients.', 'Suivi de vos prospects, de vos ventes et de vos commissions.'],
        'apporteur'  => ['Vous nous recommandez un client ou un projet, nous nous occupons de la suite.', 'Vous suivez le statut de chaque recommandation et votre commission.'],
        'revendeur'  => ['Vous revendez nos produits à votre clientèle ou sur vos réseaux.', 'Liens de partage personnels et outils pour vos publications.'],
    ];
    $field = 'w-full border border-navy-300 rounded-md py-3 px-3.5 text-base text-black bg-white focus:ring-2 focus:ring-gold-500 focus:border-navy-600 placeholder-gray-500';
    $label = 'block text-sm font-semibold text-navy-900 mb-1.5';
    $err = 'mt-1 text-sm text-red-700';
@endphp
<div class="bg-navy-50 py-12 px-4">
    <div class="max-w-2xl mx-auto bg-white border border-navy-100 rounded-lg p-6 sm:p-10">
        <p class="text-sm font-semibold text-gold-600 mb-2">Programme partenaire</p>
        <h1 class="text-3xl font-semibold text-navy-900 tracking-tight">Créer un compte partenaire</h1>
        <p class="mt-3 text-navy-600">Choisissez d’abord votre profil. Votre espace de travail et vos outils en dépendent. Une commission vous est versée sur les ventes issues de votre lien.</p>

        <form action="{{ route('partner.register') }}" method="POST" class="mt-8 space-y-8"
              x-data="{ type: @js(old('partner_type', request('type', ''))) }">
            @csrf

            <!-- 1. Profil -->
            <fieldset>
                <legend class="text-lg font-semibold text-navy-900 mb-3">1. Votre profil</legend>
                <div class="space-y-3">
                    @foreach($partnerTypes as $key => $name)
                        <label class="block cursor-pointer">
                            <input type="radio" name="partner_type" value="{{ $key }}" x-model="type" class="sr-only peer" required>
                            <span class="block border-2 border-navy-200 rounded-lg p-4 peer-checked:border-navy-900 peer-checked:bg-gold-50 peer-focus-visible:ring-2 peer-focus-visible:ring-navy-600 hover:border-navy-400 transition-colors">
                                <span class="block text-lg font-semibold text-navy-900">{{ $name }}</span>
                                <span class="block mt-1 text-navy-700">{{ $descriptions[$key][0] ?? '' }}</span>
                                <span class="block mt-1 text-sm text-navy-500">{{ $descriptions[$key][1] ?? '' }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('partner_type')<p class="{{ $err }}">{{ $message }}</p>@enderror
            </fieldset>

            <!-- 2. Coordonnées -->
            <fieldset class="space-y-5">
                <legend class="text-lg font-semibold text-navy-900 mb-3">2. Vos informations</legend>

                <div>
                    <label for="name" class="{{ $label }}">Nom complet</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Ex : Mamadou Diallo" class="{{ $field }}">
                    @error('name')<p class="{{ $err }}">{{ $message }}</p>@enderror
                </div>

                <div x-show="type === 'revendeur'" x-cloak>
                    <label for="shop_name" class="{{ $label }}">Nom de votre boutique ou de votre page <span class="font-normal text-navy-500">(facultatif)</span></label>
                    <input type="text" name="shop_name" id="shop_name" value="{{ old('shop_name') }}" placeholder="Ex : Diallo Informatique" class="{{ $field }}">
                    @error('shop_name')<p class="{{ $err }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="{{ $label }}">Adresse e-mail</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Ex : mamadou@example.com" class="{{ $field }}">
                    @error('email')<p class="{{ $err }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="{{ $label }}">Téléphone (WhatsApp)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required autocomplete="tel" placeholder="Ex : +221 77 000 00 00" class="{{ $field }}">
                    @error('phone')<p class="{{ $err }}">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="username" class="{{ $label }}">Identifiant de votre lien</label>
                    <div class="flex items-stretch">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-navy-300 bg-navy-50 text-navy-700">/partner/</span>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required autocomplete="username" placeholder="mamadou123" class="{{ $field }} rounded-l-none">
                    </div>
                    <p class="mt-1 text-sm text-navy-500">Lettres et chiffres uniquement. Il servira à votre lien de recommandation.</p>
                    @error('username')<p class="{{ $err }}">{{ $message }}</p>@enderror
                </div>
            </fieldset>

            <!-- 3. Mot de passe -->
            <fieldset class="space-y-5">
                <legend class="text-lg font-semibold text-navy-900 mb-3">3. Votre mot de passe</legend>
                <div>
                    <label for="password" class="{{ $label }}">Mot de passe</label>
                    <input type="password" name="password" id="password" required autocomplete="new-password" class="{{ $field }}">
                    @error('password')<p class="{{ $err }}">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="{{ $label }}">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="{{ $field }}">
                </div>
            </fieldset>

            <div>
                <button type="submit" class="btn-primary-gold w-full justify-center py-3.5 text-base">Envoyer ma demande</button>
                <p class="mt-3 text-sm text-navy-500">Votre compte est activé immédiatement : vous accédez à votre espace dès l’inscription.</p>
            </div>
        </form>

        <p class="mt-8 pt-6 border-t border-navy-100 text-center text-navy-600">
            Vous avez déjà un compte ? <a href="{{ route('login') }}" class="font-semibold text-navy-900 underline">Se connecter</a>
        </p>
    </div>
</div>
@endsection
