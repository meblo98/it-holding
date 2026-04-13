@extends('layouts.app')

@section('title', 'À Propos - IT-Holding Sénégal')
@section('meta_description', 'Découvrez IT-Holding, votre expert en solutions technologiques au Sénégal depuis 2015. Notre mission est d\'accompagner les entreprises dans leur transformation numérique.')
@section('meta_keywords', 'expertise informatique Sénégal, IT-Holding histoire, solutions technologiques Dakar, entreprise informatique Sénégal')

@section('content')
<div class="bg-white min-h-screen">
    <!-- Breadcrumb (Optional but good for consistency) -->
    <div class="bg-gray-50 border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center">
                <a href="{{ route('home') }}" class="hover:text-navy-900 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold">À Propos</span>
            </nav>
        </div>
    </div>

    <!-- Who We Are Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="flex-1 space-y-8">
                <div>
                    <span class="bg-gold-500 text-navy-900 text-[10px] font-black uppercase tracking-[0.3em] px-3 py-1.5 rounded-sm">QUI SOMMES-NOUS</span>
                    <h1 class="text-4xl lg:text-5xl font-black text-navy-900 uppercase tracking-tighter italic mt-6 leading-tight">
                        IT HOLDING - Le plus grand hub technologique du Sénégal.
                    </h1>
                    <p class="text-gray-500 text-sm lg:text-base leading-relaxed mt-6 italic">
                        IT HOLDING SERVICES est votre partenaire de confiance pour toutes vos solutions numériques, matérielles et de sécurité. Nous combinons expertise locale et standards internationaux pour propulser votre croissance.
                    </p>
                </div>
                
                <ul class="space-y-4">
                    @foreach([
                        'Service client disponible 24/7 pour nos contrats premium.',
                        'Plus de 50 experts dédiés à votre satisfaction.',
                        'Présence physique et accompagnement sur tout le territoire.',
                        'Plus de 1 000 références de produits informatiques en stock.'
                    ] as $item)
                        <li class="flex items-center gap-3 text-sm font-bold text-navy-800 italic">
                            <div class="w-5 h-5 bg-green-100 text-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="flex-1 relative">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-gold-50 rounded-full blur-3xl opacity-60"></div>
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-navy-50 rounded-full blur-3xl opacity-60"></div>
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                    class="relative rounded-2xl shadow-2xl border-4 border-white object-cover w-full h-[500px]">
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <!-- <section class="bg-gray-50/50 py-20 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-black text-navy-900 uppercase tracking-tighter italic mb-16">Membres de l'Équipe Dirigeante</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @foreach([
                    ['name' => 'Amadou Fall', 'role' => 'Directeur Général', 'img' => 'https://i.pravatar.cc/150?u=1'],
                    ['name' => 'Binte Diop', 'role' => 'Directrice Technique', 'img' => 'https://i.pravatar.cc/150?u=2'],
                    ['name' => 'Cheikh Ndiaye', 'role' => 'Chef de Produit', 'img' => 'https://i.pravatar.cc/150?u=3'],
                    ['name' => 'Fatou Sarr', 'role' => 'Responsable Clientèle', 'img' => 'https://i.pravatar.cc/150?u=4'],
                    ['name' => 'Moussa Gueye', 'role' => 'Expert Réseaux', 'img' => 'https://i.pravatar.cc/150?u=5'],
                    ['name' => 'Khady Diallo', 'role' => 'Directrice Marketing', 'img' => 'https://i.pravatar.cc/150?u=6'],
                    ['name' => 'Omar Sy', 'role' => 'Ingénieur Système', 'img' => 'https://i.pravatar.cc/150?u=7'],
                    ['name' => 'Awa Kane', 'role' => 'Développeur Senior', 'img' => 'https://i.pravatar.cc/150?u=8']
                ] as $member)
                    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow group">
                        <img src="{{ $member['img'] }}" class="w-16 h-16 rounded-full mx-auto mb-4 border-2 border-gold-500 p-0.5 grayscale group-hover:grayscale-0 transition-all">
                        <h3 class="text-sm font-black text-navy-900 uppercase italic">{{ $member['name'] }}</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">{{ $member['role'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section> -->

    <!-- Promotion Section with "Video" look -->
    <!-- <section class="relative py-32 lg:py-48 overflow-hidden">
        <img src="https://images.unsplash.com/photo-1510282135024-94e497ef445x?auto=format&fit=crop&w=1920&q=80" 
            class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-navy-900/40"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
            <button class="w-20 h-20 bg-gold-500 text-navy-900 rounded-full flex items-center justify-center shadow-2xl mb-10 hover:scale-110 transition-transform group">
                <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M4.5 3a.5.5 0 00-.5.5v13a.5.5 0 00.74.434l11-6.5a.5.5 0 000-.868l-11-6.5a.5.5 0 00-.24-.034z"/></svg>
            </button>
            <h2 class="text-3xl lg:text-5xl font-black text-white uppercase tracking-tighter italic mb-6 shadow-navy-900/50">Votre Hub Informatique de Confiance</h2>
            <p class="max-w-2xl text-gray-200 text-sm lg:text-base italic leading-relaxed shadow-sm">
                Découvrez comment IT HOLDING transforme l'infrastructure technologique des entreprises à travers le Sénégal.
            </p>
        </div>
    </section> -->

    <!-- Bottom Product Clusters -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- Cluster: FLASH SALE -->
            <!-- <div>
                <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 italic border-b border-gray-100 pb-2">VENTES FLASH DU JOUR</h3>
                <div class="space-y-6">
                    @foreach($products->take(3) as $product)
                    <div class="flex gap-4 group cursor-pointer">
                        <div class="w-16 h-16 bg-gray-50 rounded border border-gray-100 p-2 flex-shrink-0">
                             <img src="{{ $product->image ?: ($product->images->first()->path ?? asset('logo.jpeg')) }}" class="max-h-full max-w-full object-contain mx-auto group-hover:scale-110 transition-transform">
                        </div>
                        <div>
                            <h4 class="text-[10px] font-bold text-navy-900 line-clamp-2 italic mb-1 group-hover:text-gold-600">{{ $product->name }}</h4>
                            <span class="text-xs font-black text-gold-600">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-[8px]">CFA</span></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div> -->

            <!-- Cluster: BEST SELLERS -->
            <!-- <div>
                <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 italic border-b border-gray-100 pb-2">MEILLEURES VENTES</h3>
                <div class="space-y-6">
                    @foreach($products->skip(3)->take(3) as $product)
                    <div class="flex gap-4 group cursor-pointer">
                        <div class="w-16 h-16 bg-gray-50 rounded border border-gray-100 p-2 flex-shrink-0">
                             <img src="{{ $product->image ?: ($product->images->first()->path ?? asset('logo.jpeg')) }}" class="max-h-full max-w-full object-contain mx-auto group-hover:scale-110 transition-transform">
                        </div>
                        <div>
                            <h4 class="text-[10px] font-bold text-navy-900 line-clamp-2 italic mb-1 group-hover:text-gold-600">{{ $product->name }}</h4>
                            <span class="text-xs font-black text-gold-600">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-[8px]">CFA</span></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div> -->

            <!-- Cluster: TOP RATED -->
            <!-- <div>
                <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 italic border-b border-gray-100 pb-2">MIEUX NOTÉS</h3>
                <div class="space-y-6">
                    @foreach($products->skip(6)->take(3) as $product)
                    <div class="flex gap-4 group cursor-pointer">
                        <div class="w-16 h-16 bg-gray-50 rounded border border-gray-100 p-2 flex-shrink-0">
                             <img src="{{ $product->image ?: ($product->images->first()->path ?? asset('logo.jpeg')) }}" class="max-h-full max-w-full object-contain mx-auto group-hover:scale-110 transition-transform">
                        </div>
                        <div>
                            <h4 class="text-[10px] font-bold text-navy-900 line-clamp-2 italic mb-1 group-hover:text-gold-600">{{ $product->name }}</h4>
                            <span class="text-xs font-black text-gold-600">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-[8px]">CFA</span></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div> -->

            <!-- Cluster: NEW ARRIVAL -->
            <!-- <div>
                <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 italic border-b border-gray-100 pb-2">NOUVEAUTÉS</h3>
                <div class="space-y-6">
                    @foreach($products->skip(9)->take(3) as $product)
                    <div class="flex gap-4 group cursor-pointer">
                        <div class="w-16 h-16 bg-gray-50 rounded border border-gray-100 p-2 flex-shrink-0">
                             <img src="{{ $product->image ?: ($product->images->first()->path ?? asset('logo.jpeg')) }}" class="max-h-full max-w-full object-contain mx-auto group-hover:scale-110 transition-transform">
                        </div>
                        <div>
                            <h4 class="text-[10px] font-bold text-navy-900 line-clamp-2 italic mb-1 group-hover:text-gold-600">{{ $product->name }}</h4>
                            <span class="text-xs font-black text-gold-600">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-[8px]">CFA</span></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div> -->
        </div>
    </section>

    <!-- Newsletter Bar (Often on About pages in early conversion templates) -->
    <section class="bg-navy-900 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center justify-between gap-10">
            <div class="text-center lg:text-left">
                <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter">Inscrivez-vous à notre Newsletter</h2>
                <p class="text-gray-400 text-sm mt-2 font-medium">Recevez nos meilleures offres et actualités technologiques.</p>
            </div>
            <div class="flex-1 max-w-lg w-full">
                <form action="#" class="flex gap-2">
                    <input type="email" placeholder="Votre adresse email" class="flex-1 bg-white/10 border-white/20 rounded-lg py-3 px-4 text-white text-sm focus:ring-gold-500 focus:border-gold-500 placeholder-white/30">
                    <button type="submit" class="btn-primary-gold px-8 py-3 text-[10px] font-black uppercase tracking-widest">S'abonner</button>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
