@extends('layouts.app')

@section('title', 'Gestion de mon Épargne - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('home') }}" class="hover:text-navy-900 flex items-center gap-1">
                    <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('dashboard') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('dashboard.savings') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Mon Épargne</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold uppercase tracking-wider italic">Détails de mon Épargne</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            <aside class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <nav class="flex flex-col">
                        @php
                            $navItems = [
                                ['name' => 'Tableau de bord', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>', 'route' => 'dashboard'],
                                ['name' => 'Historique des commandes', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'route' => 'dashboard.orders'],
                                ['name' => 'Suivi de commande', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'route' => 'dashboard.track'],
                                ['name' => 'Mes Garanties', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>', 'route' => 'dashboard.warranties'],
                                ['name' => 'Mon Épargne', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'route' => 'dashboard.savings'],
                                ['name' => 'Panier', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>', 'route' => 'shop.cart'],
                                ['name' => 'Liste de souhaits', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>', 'route' => '#'],
                                ['name' => 'Cartes & Adresses', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>', 'route' => '#'],
                                ['name' => 'Paramètres', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'route' => 'dashboard.settings'],
                            ];
                        @endphp
                        @foreach($navItems as $nav)
                        <a href="{{ $nav['route'] != '#' ? route($nav['route']) : '#' }}" class="px-6 py-4 flex items-center gap-3 text-sm font-bold uppercase tracking-tight italic transition-all {{ request()->routeIs($nav['route']) ? 'bg-gold-500 text-navy-900' : 'text-gray-400 hover:text-navy-900 hover:bg-gray-50 border-b border-gray-50' }}">
                            {!! $nav['icon'] !!}
                            {{ $nav['name'] }}
                        </a>
                        @endforeach
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-6 py-4 flex items-center gap-3 text-sm font-bold uppercase tracking-tight italic text-red-500 hover:bg-red-50 transition-all text-left">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Déconnexion
                            </button>
                        </form>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 space-y-8">
                @if(session('success'))
                    <div class="p-4 bg-green-50 border border-green-100 rounded-lg flex items-center gap-3 text-green-600 text-xs font-bold uppercase tracking-widest italic">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 bg-red-50 border border-red-100 rounded-lg flex items-center gap-3 text-red-600 text-xs font-bold uppercase tracking-widest italic">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Plan details card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                        <h3 class="text-sm font-black text-navy-900 uppercase tracking-tighter italic">Détails du Plan d'Épargne</h3>
                        <span class="inline-block px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider border {{ $savingPlan->status === 'active' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($savingPlan->status === 'completed' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200') }} italic">
                            {{ $savingPlan->status === 'active' ? 'Actif' : ($savingPlan->status === 'completed' ? 'Complété' : 'Retiré') }}
                        </span>
                    </div>

                    <div class="p-8">
                        <div class="flex flex-col md:flex-row justify-between gap-6 mb-8">
                            <div class="flex items-center gap-6">
                                @if($savingPlan->product)
                                    <div class="w-24 h-24 bg-gray-50 border border-gray-100 rounded-2xl p-3 flex items-center justify-center flex-shrink-0">
                                        @if($savingPlan->product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $savingPlan->product->images->first()->image_path) }}" class="object-contain max-h-full">
                                        @else
                                            <img src="{{ asset('assets/logo.png') }}" class="object-contain max-h-full opacity-30">
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black text-navy-500 uppercase tracking-widest block mb-1">Produit à obtenir</span>
                                        <h4 class="text-xl font-black text-navy-900 uppercase tracking-tight italic">{{ $savingPlan->product->name }}</h4>
                                        <p class="text-xs text-gray-400 font-bold mt-1">
                                            Objectif: <span class="text-navy-900">{{ number_format($savingPlan->target_amount, 0, ',', ' ') }} FCFA</span>
                                        </p>
                                    </div>
                                @elseif($savingPlan->service)
                                    <div class="w-24 h-24 bg-navy-900 rounded-2xl flex items-center justify-center text-gold-500 flex-shrink-0">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13a9.001 9.001 0 01-9 9 9 9 0 01-9-9m9 9a9 9 0 019-9m-9 9V12m0 0a9 9 0 019-9m-9 9H3"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black text-gold-500 uppercase tracking-widest block mb-1">Service à obtenir</span>
                                        <h4 class="text-xl font-black text-navy-900 uppercase tracking-tight italic">{{ $savingPlan->service->title }}</h4>
                                        <p class="text-xs text-gray-400 font-bold mt-1">
                                            Objectif: <span class="text-navy-900">{{ number_format($savingPlan->target_amount, 0, ',', ' ') }} FCFA</span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div class="text-left md:text-right">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Montant Épargné</span>
                                <h4 class="text-3xl font-black text-gold-600 italic">{{ number_format($savingPlan->current_amount, 0, ',', ' ') }} FCFA</h4>
                                @if($savingPlan->status === 'active')
                                    <span class="text-[10px] font-bold text-gray-400 block mt-1">
                                        Restant: <span class="text-navy-900">{{ number_format($savingPlan->target_amount - $savingPlan->current_amount, 0, ',', ' ') }} FCFA</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between text-xs font-bold text-gray-500">
                                <span>Progression ({{ round($savingPlan->progressPercent, 1) }}%)</span>
                                <span>Lancement: {{ $savingPlan->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
                                <div class="bg-gradient-to-r from-navy-800 to-gold-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $savingPlan->progressPercent) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($savingPlan->status === 'active')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Deposit Form -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden md:col-span-2">
                            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/30">
                                <h4 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Faire un Dépôt</h4>
                            </div>
                            <form action="{{ route('dashboard.savings.deposit', $savingPlan->id) }}" method="POST" class="p-8 space-y-6">
                                @csrf
                                <div>
                                    <label for="amount" class="block text-xs font-black text-navy-900 uppercase tracking-widest mb-2 italic">Montant à déposer (FCFA)</label>
                                    <div class="relative">
                                        <input type="number" name="amount" id="amount" min="100" max="{{ $savingPlan->target_amount - $savingPlan->current_amount }}" class="w-full pl-6 pr-16 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-navy-900 focus:outline-none focus:border-gold-500 transition-all" required>
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400">FCFA</div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-navy-900 uppercase tracking-widest mb-3 italic">Moyen de Paiement</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="payment_method" value="wallet" checked class="peer sr-only">
                                            <div class="flex flex-col items-center justify-center p-4 border border-gray-100 rounded-xl hover:border-gold-500 peer-checked:border-gold-500 peer-checked:bg-gold-50/50 transition-all text-center">
                                                <svg class="w-6 h-6 text-navy-900 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="text-[10px] font-black text-navy-900 uppercase tracking-tight">Portefeuille</span>
                                                <span class="text-[8px] font-bold text-gray-400 mt-0.5">Solde: {{ number_format($client->wallet_balance, 0, ',', ' ') }} F</span>
                                            </div>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="payment_method" value="wave" class="peer sr-only">
                                            <div class="flex flex-col items-center justify-center p-4 border border-gray-100 rounded-xl hover:border-gold-500 peer-checked:border-gold-500 peer-checked:bg-gold-50/50 transition-all text-center">
                                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white font-black text-[10px] flex items-center justify-center mb-1.5">W</div>
                                                <span class="text-[10px] font-black text-navy-900 uppercase tracking-tight">Wave</span>
                                                <span class="text-[8px] font-bold text-gray-400 mt-0.5">Mobile Money</span>
                                            </div>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="payment_method" value="orange_money" class="peer sr-only">
                                            <div class="flex flex-col items-center justify-center p-4 border border-gray-100 rounded-xl hover:border-gold-500 peer-checked:border-gold-500 peer-checked:bg-gold-50/50 transition-all text-center">
                                                <div class="w-6 h-6 rounded-full bg-orange-500 text-white font-black text-[10px] flex items-center justify-center mb-1.5">OM</div>
                                                <span class="text-[10px] font-black text-navy-900 uppercase tracking-tight">Orange Money</span>
                                                <span class="text-[8px] font-bold text-gray-400 mt-0.5">Mobile Money</span>
                                            </div>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="payment_method" value="card" class="peer sr-only">
                                            <div class="flex flex-col items-center justify-center p-4 border border-gray-100 rounded-xl hover:border-gold-500 peer-checked:border-gold-500 peer-checked:bg-gold-50/50 transition-all text-center">
                                                <svg class="w-6 h-6 text-navy-900 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span class="text-[10px] font-black text-navy-900 uppercase tracking-tight">Carte Bancaire</span>
                                                <span class="text-[8px] font-bold text-gray-400 mt-0.5">Visa / Mastercard</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="w-full bg-navy-900 hover:bg-gold-500 text-white hover:text-navy-900 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                    Valider le Dépôt
                                </button>
                            </form>
                        </div>

                        <!-- Withdraw/Cancel Plan -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between">
                            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/30">
                                <h4 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Retrait Anticipé</h4>
                            </div>
                            <div class="p-8 flex-1 flex flex-col justify-between space-y-6">
                                <div class="space-y-3">
                                    <p class="text-xs text-gray-500 leading-relaxed font-medium">
                                        Vous avez la possibilité de récupérer l'intégralité des fonds accumulés sur ce plan d'épargne.
                                    </p>
                                    <div class="bg-red-50 text-red-700 border border-red-100 rounded-xl p-4 text-[11px] font-bold italic space-y-2">
                                        <p class="font-black uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Attention
                                        </p>
                                        <p>
                                            Le retrait de votre argent entraînera l'annulation immédiate et définitive de ce plan d'épargne. Les fonds seront transférés sur votre portefeuille client.
                                        </p>
                                    </div>
                                </div>

                                <form action="{{ route('dashboard.savings.withdraw', $savingPlan->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir retirer les fonds et annuler ce plan d\'épargne ?');">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                        Retirer mes {{ number_format($savingPlan->current_amount, 0, ',', ' ') }} F
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Transaction Ledger -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/30">
                        <h4 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Historique des Transactions</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-8 py-4 border-b border-gray-100 italic">ID Transaction</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Date & Heure</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Moyen</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Type</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Montant</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($savingPlan->transactions as $transaction)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-4 text-[11px] font-mono font-bold text-navy-900">
                                            #{{ $transaction->id }}
                                        </td>
                                        <td class="px-8 py-4 text-[11px] font-bold text-gray-500">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-8 py-4 text-[11px] font-bold text-navy-900 uppercase">
                                            {{ $transaction->payment_method ?: 'Portefeuille' }}
                                        </td>
                                        <td class="px-8 py-4">
                                            @if($transaction->type === 'deposit')
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-green-50 text-green-700 border border-green-200 italic">Dépôt</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-red-50 text-red-700 border border-red-200 italic">Retrait</span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-4 text-[11px] font-black italic {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-10 text-center text-xs text-gray-400 font-bold italic">
                                            Aucune transaction enregistrée pour ce plan d'épargne.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
