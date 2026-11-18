@extends('layouts.app')

@section('title', 'Sécurité & paiements - ' . config('app.name'))
@section('meta_description', 'Comment IT Holding Services protège vos paiements, vos données et vos livraisons.')

@section('content')
@php
    $blocks = [
        'Vos paiements' => [
            'Carte bancaire et Orange Money : le paiement se fait sur la page sécurisée de notre partenaire de paiement. Notre site ne voit ni ne conserve votre numéro de carte.',
            'Wave : vous payez dans l’application Wave. Nous vérifions chaque paiement (montant et référence de transaction) avant d’activer votre commande ou votre abonnement.',
            'Une commande ou un abonnement n’est jamais validé tant que le paiement n’est pas confirmé.',
            'La confirmation d’un paiement par carte ou Orange Money est contrôlée (montant et référence) avant l’activation.',
        ],
        'Vos données' => [
            'La connexion à notre site est chiffrée (HTTPS).',
            'Vos mots de passe ne sont jamais stockés en clair.',
            'Vos photos d’appareils, factures et captures de paiement sont privées : seuls vous et l’équipe autorisée peuvent les consulter.',
            'Les accès de notre équipe sont limités par rôle : chacun ne voit que ce dont il a besoin.',
        ],
        'Vos livraisons' => [
            'Le fournisseur remet la commande au livreur uniquement contre un code ou un QR code du bon de commande.',
            'À la livraison, la commande est validée par un code ou un QR code que vous seul possédez : ne le donnez qu’en main propre.',
        ],
    ];
@endphp

<section class="bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
        <p class="text-sm font-semibold text-gold-600 mb-3">IT Holding Services</p>
        <h1 class="text-4xl font-semibold text-navy-900 leading-tight tracking-tight">Sécurité &amp; paiements</h1>
        <p class="mt-5 text-lg text-navy-500 leading-relaxed">Voici concrètement ce que nous faisons pour protéger vos paiements, vos données et vos livraisons.</p>

        <div class="mt-10 space-y-10">
            @foreach ($blocks as $title => $items)
                <div>
                    <h2 class="text-xl font-semibold text-navy-900">{{ $title }}</h2>
                    <ul class="mt-4 space-y-3 text-navy-600">
                        @foreach ($items as $item)
                            <li class="flex gap-3"><span class="mt-1 text-green-700">✓</span><span>{{ $item }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div class="rounded-lg border border-amber-200 bg-amber-50 p-5 text-amber-900">
                <h2 class="font-semibold">Restez vigilant</h2>
                <p class="mt-2 text-sm">Nous ne vous demanderons jamais votre code secret (PIN) Wave ou Orange Money, ni un code reçu par SMS, par message ou par téléphone. En cas de doute sur un message, contactez-nous avant de payer.</p>
            </div>
        </div>

        <a href="{{ route('contact.index') }}" class="mt-10 inline-flex btn-primary-gold">Une question ? Contactez-nous</a>
    </div>
</section>
@endsection
