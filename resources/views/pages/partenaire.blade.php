@extends('layouts.app')

@section('title', 'Devenir partenaire - ' . config('app.name'))
@section('meta_description', 'Devenez partenaire IT-Holding : partagez nos produits et services avec votre réseau et touchez une commission sur chaque vente issue de votre lien.')

@section('content')
@php
    $steps = [
        ['Créez votre compte partenaire', 'Remplissez le formulaire d’inscription partenaire.'],
        ['Accédez à votre espace', 'Votre compte est activé dès l’inscription. Vous accédez directement à votre espace et acceptez le contrat de partenariat.'],
        ['Partagez vos liens', 'Chaque produit a votre lien personnel. Partagez-le sur Facebook, sur WhatsApp ou en direct.'],
        ['Touchez vos commissions', 'Les ventes issues de votre lien sont suivies automatiquement. Retrouvez vos gains dans votre portefeuille.'],
    ];
    $tools = [
        ['Lien et code promo personnels', 'Votre identifiant est intégré à chaque lien que vous partagez.'],
        ['Suivi des commissions', 'Vos ventes, vos gains et votre portefeuille au même endroit.'],
        ['Catalogue et visuels', 'Un catalogue PDF et des publications prêtes à partager.'],
        ['Suivi de vos prospects', 'Gardez la trace de vos contacts et de leurs demandes.'],
        ['Missions', 'Des missions proposées aux partenaires, avec un espace de suivi.'],
        ['Assistant commercial', 'Un assistant pour préparer vos messages et répondre aux objections.'],
    ];
@endphp

<!-- Introduction -->
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
        <p class="text-sm font-semibold text-gold-600 mb-3">Programme partenaire</p>
        <h1 class="text-4xl lg:text-5xl font-semibold text-navy-900 leading-tight tracking-tight max-w-3xl">
            Recommandez IT-Holding et touchez une commission sur chaque vente.
        </h1>
        <p class="mt-6 text-lg text-navy-500 max-w-2xl leading-relaxed">
            Vous avez un réseau, une page ou des clients ? Partagez nos produits et services informatiques avec votre lien personnel. Chaque vente qui en découle vous rapporte une commission.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            @guest
                <a href="{{ route('partner.register') }}" class="btn-primary-gold">Devenir partenaire</a>
                <a href="{{ route('login') }}" class="btn-outline-navy">J’ai déjà un compte</a>
            @else
                <a href="{{ route('dashboard.partner') }}" class="btn-primary-gold">Accéder à mon espace partenaire</a>
            @endguest
        </div>
    </div>
</section>

<!-- Fonctionnement -->
<section class="bg-navy-50 border-y border-navy-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-navy-900 tracking-tight mb-10">Comment ça marche</h2>
        <ol class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            @foreach ($steps as $i => [$title, $text])
                <li class="border-t-2 border-navy-900 pt-5">
                    <span class="text-sm font-semibold text-gold-600">Étape {{ $i + 1 }}</span>
                    <h3 class="mt-1 text-lg font-semibold text-navy-900">{{ $title }}</h3>
                    <p class="mt-2 text-navy-600 leading-relaxed">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

<!-- Espace partenaire -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-navy-900 tracking-tight">Ce que vous trouvez dans votre espace</h2>
        <p class="mt-3 text-navy-500 max-w-2xl">Tout ce qu’il faut pour recommander, suivre vos ventes et suivre vos gains.</p>
        <dl class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
            @foreach ($tools as [$title, $text])
                <div class="border-b border-navy-100 pb-6">
                    <dt class="text-lg font-semibold text-navy-900">{{ $title }}</dt>
                    <dd class="mt-1 text-navy-600 leading-relaxed">{{ $text }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>

<!-- Appel final -->
<section class="bg-navy-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-semibold tracking-tight max-w-xl">Prêt à commencer ?</h2>
        <p class="mt-3 text-white/70 max-w-xl">Une question avant de vous inscrire ? Écrivez-nous, nous vous répondons.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            @guest
                <a href="{{ route('partner.register') }}" class="btn-primary-gold">Devenir partenaire</a>
            @else
                <a href="{{ route('dashboard.partner') }}" class="btn-primary-gold">Mon espace partenaire</a>
            @endguest
            <a href="{{ route('contact.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-md border border-white/30 font-semibold hover:bg-white/10 transition-colors">Nous écrire</a>
        </div>
    </div>
</section>
@endsection
