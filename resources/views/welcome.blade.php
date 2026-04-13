@extends('layouts.app')

@section('title', 'Expertise IT & Solutions Numériques')
@section('meta_description', 'IT-Holding est le leader des solutions IT au Sénégal. Maintenance informatique, infrastructure réseau, vidéosurveillance et vente de matériel de haute qualité.')
@section('meta_keywords', 'informatique Sénégal, maintenance ordinateur Dakar, vidéosurveillance IP, réseau entreprise, IT-Holding Sénégal')

@section('content')
    <!-- Clicon Style Hero Section -->
    <section class="bg-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Slider -->
                <div class="lg:col-span-2 relative overflow-hidden rounded-xl bg-white shadow-sm h-[400px] lg:h-[500px]">
                    <div class="relative h-full flex items-center p-8 lg:p-12">
                        <div class="max-w-md z-10">
                            <span class="text-navy-600 font-bold text-sm tracking-wider uppercase mb-2 block">DÉCOUVREZ NOS SOLUTIONS IT</span>
                            <h1 class="text-4xl lg:text-5xl font-black text-navy-900 leading-tight mb-4">
                                L'Excellence <br>
                                <span class="text-gold-600 uppercase">Informatique</span>
                            </h1>
                            <p class="text-gray-600 mb-8 max-w-sm">Partenaire de confiance depuis 2015 pour toutes vos solutions numériques et matérielles.</p>
                            <a href="{{ route('services.index') }}" class="btn-primary-gold w-fit">
                                DÉCOUVRIR NOS SERVICES
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                        <!-- Mock Hero Image -->
                        <div class="absolute right-0 bottom-0 w-2/3 lg:w-1/2 h-full flex items-end justify-end pointer-events-none opacity-20 lg:opacity-100">
                             <div class="bg-gradient-to-l from-white via-transparent to-transparent absolute inset-0 z-10 lg:hidden"></div>
                             <img src="{{ asset('logo.jpeg') }}" alt="Hero Product" class="h-4/5 w-auto object-contain p-8 transform hover:scale-105 transition-transform duration-700">
                        </div>
                    </div>
                </div>

                <!-- Side Promos -->
                <div class="flex flex-col gap-6">
                    <div class="bg-navy-900 rounded-xl p-8 flex-1 relative overflow-hidden group">
                        <div class="relative z-10">
                            <span class="text-gold-400 font-bold text-xs mb-2 block">OFFRE SPÉCIALE</span>
                            <h3 class="text-white font-bold text-xl mb-4">Maintenance <br>Préventive</h3>
                            <a href="{{ route('contact.index') }}" class="text-white font-bold text-sm flex items-center gap-2 group-hover:text-gold-400 transition-colors">
                                EN SAVOIR PLUS
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-gold-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    </div>
                    <div class="bg-gold-50 border border-gold-100 rounded-xl p-8 flex-1 relative overflow-hidden group">
                        <div class="relative z-10">
                            <span class="text-navy-600 font-bold text-xs mb-2 block">NOUVEAU</span>
                            <h3 class="text-navy-900 font-bold text-xl mb-4">Solutions <br>Cloud Cloud</h3>
                            <a href="{{ route('services.index') }}" class="text-navy-900 font-bold text-sm flex items-center gap-2 group-hover:translate-x-1 transition-transform">
                                DÉCOURIR
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Feature Bar -->
    <section class="bg-white border-b border-gray-100 py-0 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-4 divide-x divide-gray-100">
                <div class="trust-card">
                    <div class="bg-gray-50 p-3 rounded-lg text-navy-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-navy-900 uppercase">Livraison Rapide</p>
                        <p class="text-xs text-gray-500">Expédition sous 24/48h</p>
                    </div>
                </div>
                <div class="trust-card">
                    <div class="bg-gray-50 p-3 rounded-lg text-navy-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-navy-900 uppercase">Garantie Qualité</p>
                        <p class="text-xs text-gray-500">Produits certifiés</p>
                    </div>
                </div>
                <div class="trust-card">
                    <div class="bg-gray-50 p-3 rounded-lg text-navy-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-navy-900 uppercase">Paiement Sécurisé</p>
                        <p class="text-xs text-gray-500">Transaction 100% sûre</p>
                    </div>
                </div>
                <div class="trust-card">
                    <div class="bg-gray-50 p-3 rounded-lg text-navy-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-navy-900 uppercase">Support 24/7</p>
                        <p class="text-xs text-gray-500">Experts à votre écoute</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Promo Banner / Specialized Service -->
    @php
        $promoProduct = $products->where('price', '>', 50000)->first() ?: $products->first();
    @endphp
    @if($promoProduct)
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-navy-900 rounded-2xl flex flex-col lg:flex-row items-center overflow-hidden">
                <div class="lg:w-1/2 p-8 lg:p-16 text-center lg:text-left">
                    <span class="bg-gold-500 text-navy-900 px-4 py-1 rounded-full text-xs font-bold mb-6 inline-block">BONS PLANS DU JOUR</span>
                    <h2 class="text-3xl lg:text-5xl font-black text-white mb-6 leading-tight">
                        {{ $promoProduct->name }}
                    </h2>
                    <p class="text-white/60 mb-8 max-w-md mx-auto lg:mx-0">
                        Performance exceptionnelle et fiabilité garantie pour tous vos travaux professionnels au Sénégal.
                    </p>
                    <div class="flex items-center justify-center lg:justify-start gap-6">
                        <span class="text-gold-400 text-3xl font-bold">{{ number_format($promoProduct->price, 0, ',', ' ') }} FCFA</span>
                        <a href="{{ route('shop.show', $promoProduct->slug) }}" class="btn-primary-gold">COMMANDER MAINTENANT</a>
                    </div>
                </div>
                <div class="lg:w-1/2 bg-navy-800/50 w-full h-full flex items-center justify-center p-8">
                     @php
                        $rawPath = $promoProduct->image ?: $promoProduct->images->first()->path ?? null;
                        $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                        $imgUrl = $imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath) 
                                ? '/storage/' . ltrim($imgPath, '/') 
                                : ($rawPath && filter_var($rawPath, FILTER_VALIDATE_URL) ? $rawPath : asset('logo.jpeg'));
                    @endphp
                    <img src="{{ $imgUrl }}" alt="{{ $promoProduct->name }}" class="max-h-80 w-auto object-contain transform hover:scale-110 transition-transform duration-500 drop-shadow-[0_20px_50px_rgba(255,193,7,0.3)]">
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Product Grid Section -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-2xl font-bold text-navy-900 border-l-4 border-gold-500 pl-4 uppercase tracking-tighter">Nos Produits Phares</h2>
                <a href="{{ route('shop.index') }}" class="text-navy-600 hover:text-gold-600 font-bold text-sm flex items-center gap-2 group transition-colors">
                    VOIR TOUT LE CATALOGUE
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white border border-gray-100 rounded-lg p-4 group hover:shadow-xl hover:border-gold-200 transition-all duration-300 relative">
                        <a href="{{ route('shop.show', $product->slug) }}" class="block">
                            <!-- Badge Nouveau -->
                            @if ($product->created_at->diffInDays(now()) < 30)
                                <span class="absolute top-2 left-2 z-10 bg-navy-900 text-white text-[10px] font-bold px-2 py-1 rounded">Nouveau</span>
                            @endif

                            <!-- Image Container -->
                            <div class="relative aspect-square mb-4 bg-gray-50 rounded-md overflow-hidden flex items-center justify-center p-4">
                                @php
                                    $rawPath = $product->image ?: $product->images->first()->path ?? null;
                                    $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                                    $imgUrl = $imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath) 
                                            ? '/storage/' . ltrim($imgPath, '/') 
                                            : ($rawPath && filter_var($rawPath, FILTER_VALIDATE_URL) ? $rawPath : asset('logo.jpeg'));
                                @endphp
                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform duration-500">
                                
                                <!-- Quick Actions Overlay -->
                                <div class="absolute inset-0 bg-navy-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <button class="w-10 h-10 bg-white text-navy-900 rounded-full flex items-center justify-center hover:bg-gold-500 transition-colors shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </button>
                                    <button class="w-10 h-10 bg-white text-navy-900 rounded-full flex items-center justify-center hover:bg-gold-500 transition-colors shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="space-y-2">
                                <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">IT HOLDING</span>
                                <h3 class="text-sm font-medium text-navy-900 line-clamp-2 h-10 group-hover:text-gold-600 transition-colors">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-black text-navy-900">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-[10px]">CFA</span></span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="bg-gray-50 py-16 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-black text-navy-900 mb-4 uppercase tracking-tighter italic">L'Expertise IT à Votre Service</h2>
            <p class="text-gray-500 mb-12 max-w-2xl mx-auto">Plus qu'une boutique, nous sommes vos ingénieurs conseils pour toutes vos problématiques technologiques.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-sm border-b-4 border-gold-500 hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gold-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h4 class="font-bold text-navy-900 mb-2">MAINTENANCE EXPERTE</h4>
                    <p class="text-sm text-gray-500">Diagnostic précis et réparation rapide de vos parcs informatiques.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm border-b-4 border-navy-600 hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-navy-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h4 class="font-bold text-navy-900 mb-2">INFRASTRUCTURE</h4>
                    <p class="text-sm text-gray-500">Installation de réseaux performants et sécurisés pour votre entreprise.</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-sm border-b-4 border-gold-500 hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gold-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <h4 class="font-bold text-navy-900 mb-2">VIDÉOSURVEILLANCE</h4>
                    <p class="text-sm text-gray-500">Protection intelligente de vos biens avec les dernières technologies IP.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final Styles Additions -->
    <style>
        .btn-primary-gold {
            background-color: var(--color-gold-500);
            color: var(--color-navy-900);
            padding: 0.75rem 2rem;
            border-radius: 0.375rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px 0 rgba(255, 193, 7, 0.39);
        }
        .btn-primary-gold:hover {
            background-color: var(--color-gold-600);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.5);
        }
        .trust-card {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }
        .trust-card:hover {
            background-color: #f8fafc;
        }
    </style>
@endsection
