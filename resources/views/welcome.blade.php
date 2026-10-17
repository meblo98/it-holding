@extends('layouts.app')

@section('title', 'Expertise IT & Solutions Numériques')
@section('meta_description', 'IT-Holding est le leader des solutions IT au Sénégal. Maintenance informatique, infrastructure réseau, vidéosurveillance et vente de matériel de haute qualité.')
@section('meta_keywords', 'informatique Sénégal, maintenance ordinateur Dakar, vidéosurveillance IP, réseau entreprise, IT-Holding Sénégal')

@section('content')
@php
    $promoProduct = $products->where('price', '>', 50000)->first() ?: $products->first();
@endphp

<!-- Accueil -->
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20 grid grid-cols-1 lg:grid-cols-5 gap-12 items-center">
        <div class="lg:col-span-3">
            <h1 class="text-4xl lg:text-5xl font-semibold text-navy-900 leading-tight tracking-tight max-w-2xl">
                Le matériel, le réseau et la maintenance informatique de votre entreprise, au même endroit.
            </h1>
            <p class="mt-6 text-lg text-navy-500 max-w-xl leading-relaxed">
                Nous vendons du matériel, installons vos réseaux et caméras, et assurons le suivi de votre parc informatique au Sénégal.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('shop.index') }}" class="btn-primary-gold">Voir la boutique</a>
                <a href="{{ route('contact.index') }}" class="btn-outline-navy">Demander un devis</a>
            </div>
        </div>

        <div class="lg:col-span-2">
            @if($promoProduct)
                @php
                    $rawPath = $promoProduct->image ?: $promoProduct->images->first()->path ?? null;
                    $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                    $imgUrl = $imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)
                        ? '/storage/' . ltrim($imgPath, '/')
                        : ($rawPath && filter_var($rawPath, FILTER_VALIDATE_URL) ? $rawPath : asset('logo.jpeg'));
                @endphp
                <a href="{{ route('shop.show', $promoProduct->slug) }}" class="block border border-navy-100 rounded-lg bg-navy-50 p-6 group">
                    <div class="aspect-[4/3] bg-white rounded flex items-center justify-center p-6 mb-5">
                        <img src="{{ $imgUrl }}" alt="{{ $promoProduct->name }}" class="max-h-full max-w-full object-contain">
                    </div>
                    <p class="text-sm text-navy-500 mb-1">À la une en boutique</p>
                    <h2 class="text-lg font-semibold text-navy-900 group-hover:text-gold-600 transition-colors">{{ $promoProduct->name }}</h2>
                    <p class="mt-1 text-navy-900 font-semibold">{{ number_format($promoProduct->price, 0, ',', ' ') }} FCFA</p>
                </a>
            @else
                <div class="border border-navy-100 rounded-lg bg-white p-10 flex items-center justify-center">
                    <img src="{{ asset('logo.jpeg') }}" alt="Logo {{ config('app.name') }}" class="max-h-56 w-auto object-contain">
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Engagements -->
<section class="bg-navy-50 border-y border-navy-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-5 text-sm">
        <div>
            <p class="font-semibold text-navy-900">Livraison rapide</p>
            <p class="text-navy-500">Expédition sous 24 à 48 h</p>
        </div>
        <div>
            <p class="font-semibold text-navy-900">Garantie</p>
            <p class="text-navy-500">Produits certifiés</p>
        </div>
        <div>
            <p class="font-semibold text-navy-900">Paiement sécurisé</p>
            <p class="text-navy-500">Transactions protégées</p>
        </div>
        <div>
            <p class="font-semibold text-navy-900">Support</p>
            <p class="text-navy-500">Une équipe à votre écoute</p>
        </div>
    </div>
</section>

<!-- Produits -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8 gap-4">
            <h2 class="text-2xl font-semibold text-navy-900 tracking-tight">Nos produits phares</h2>
            <a href="{{ route('shop.index') }}" class="text-sm font-semibold text-navy-600 hover:text-gold-600 transition-colors whitespace-nowrap">Voir tout le catalogue</a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-5 gap-y-8">
            @foreach ($products as $product)
                @php
                    $rawPath = $product->image ?: $product->images->first()->path ?? null;
                    $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                    $imgUrl = $imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)
                        ? '/storage/' . ltrim($imgPath, '/')
                        : ($rawPath && filter_var($rawPath, FILTER_VALIDATE_URL) ? $rawPath : asset('logo.jpeg'));
                @endphp
                <a href="{{ route('shop.show', $product->slug) }}" class="group block">
                    <div class="relative aspect-square mb-3 bg-navy-50 border border-navy-100 rounded-md flex items-center justify-center p-4">
                        @if ($product->created_at->diffInDays(now()) < 30)
                            <span class="absolute top-2 left-2 bg-navy-900 text-white text-xs font-medium px-2 py-0.5 rounded">Nouveau</span>
                        @endif
                        <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain">
                    </div>
                    <h3 class="text-sm font-medium text-navy-900 line-clamp-2 min-h-[2.5rem] group-hover:text-gold-600 transition-colors">{{ $product->name }}</h3>
                    <p class="mt-1 font-semibold text-navy-900">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-xs font-medium text-navy-500">FCFA</span></p>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Services -->
<section class="bg-navy-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-semibold tracking-tight max-w-xl">Plus qu'une boutique : une équipe technique.</h2>
        <p class="mt-4 text-white/70 max-w-2xl">Nous accompagnons les entreprises de l'installation au suivi, pour que leur informatique fonctionne sans interruption.</p>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="border-t border-white/20 pt-5">
                <h3 class="font-semibold text-lg mb-2">Maintenance</h3>
                <p class="text-white/70 text-sm leading-relaxed">Diagnostic précis et réparation rapide de vos parcs informatiques.</p>
            </div>
            <div class="border-t border-white/20 pt-5">
                <h3 class="font-semibold text-lg mb-2">Infrastructure réseau</h3>
                <p class="text-white/70 text-sm leading-relaxed">Installation de réseaux performants et sécurisés pour votre entreprise.</p>
            </div>
            <div class="border-t border-white/20 pt-5">
                <h3 class="font-semibold text-lg mb-2">Vidéosurveillance</h3>
                <p class="text-white/70 text-sm leading-relaxed">Protection de vos locaux avec des caméras IP et un suivi à distance.</p>
            </div>
        </div>

        <div class="mt-12 flex flex-wrap gap-3">
            <a href="{{ route('services.index') }}" class="btn-primary-gold">Voir tous les services</a>
            <a href="{{ route('contact.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-md border border-white/30 font-semibold hover:bg-white/10 transition-colors">Nous écrire</a>
        </div>
    </div>
</section>
@endsection
