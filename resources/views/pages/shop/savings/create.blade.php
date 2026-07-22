@extends('layouts.app')

@section('title', 'Lancer une Épargne - ' . config('app.name'))

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
                <span class="text-navy-900 font-bold uppercase tracking-wider italic">Créer un plan</span>
            </nav>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-lg flex items-center gap-3 text-red-600 text-xs font-bold uppercase tracking-widest italic">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                <h3 class="text-sm font-black text-navy-900 uppercase tracking-tighter italic">Commencer un Plan d'Épargne</h3>
                <a href="{{ route('dashboard.savings') }}" class="text-[10px] text-gray-400 font-black uppercase tracking-widest hover:text-navy-900">Retour</a>
            </div>

            <form action="{{ route('dashboard.savings.store') }}" method="POST" class="p-8">
                @csrf

                @if($product)
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="flex items-center gap-6 mb-8 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="w-20 h-20 bg-white rounded-xl overflow-hidden border border-gray-100 p-2 flex-shrink-0 flex items-center justify-center">
                            @if($product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" class="object-contain max-h-full">
                            @else
                                <img src="{{ asset('assets/logo.png') }}" class="object-contain max-h-full opacity-30">
                            @endif
                        </div>
                        <div>
                            <span class="text-[9px] font-black text-navy-500 uppercase tracking-widest">Épargne Produit</span>
                            <h4 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">{{ $product->name }}</h4>
                            <span class="text-sm font-bold text-gold-600 block mt-1">{{ number_format($targetAmount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                @elseif($service)
                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                    <div class="flex items-center gap-6 mb-8 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="w-20 h-20 bg-navy-900 rounded-xl flex items-center justify-center text-gold-500 flex-shrink-0">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13a9.001 9.001 0 01-9 9 9 9 0 01-9-9m9 9a9 9 0 019-9m-9 9V12m0 0a9 9 0 019-9m-9 9H3"/></svg>
                        </div>
                        <div>
                            <span class="text-[9px] font-black text-gold-500 uppercase tracking-widest">Épargne Service</span>
                            <h4 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">{{ $service->title }}</h4>
                            <span class="text-sm font-bold text-gold-600 block mt-1">{{ number_format($targetAmount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                @endif

                <div class="mb-6">
                    <label for="initial_deposit" class="block text-xs font-black text-navy-900 uppercase tracking-widest mb-2 italic">Dépôt Initial (FCFA)</label>
                    <div class="relative">
                        <input type="number" name="initial_deposit" id="initial_deposit" value="0" min="0" max="{{ $targetAmount }}" class="w-full pl-6 pr-16 py-3.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-navy-900 focus:outline-none focus:border-gold-500 transition-all" required>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400">FCFA</div>
                    </div>
                    <span class="text-[10px] text-gray-400 font-medium mt-1.5 block">Saisissez 0 si vous souhaitez commencer le plan sans faire de dépôt immédiatement.</span>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-black text-navy-900 uppercase tracking-widest mb-3 italic">Moyen de Paiement pour le dépôt</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="wallet" checked class="peer sr-only">
                            <div class="flex flex-col items-center justify-center p-4 border border-gray-100 rounded-xl hover:border-gold-500 peer-checked:border-gold-500 peer-checked:bg-gold-50/50 transition-all text-center">
                                <svg class="w-6 h-6 text-navy-900 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-[10px] font-black text-navy-900 uppercase tracking-tight">Portefeuille</span>
                                <span class="text-[8px] font-bold text-gray-400 block mt-0.5">Solde: {{ number_format($client->wallet_balance, 0, ',', ' ') }} F</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="wave" class="peer sr-only">
                            <div class="flex flex-col items-center justify-center p-4 border border-gray-100 rounded-xl hover:border-gold-500 peer-checked:border-gold-500 peer-checked:bg-gold-50/50 transition-all text-center">
                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white font-black text-xs flex items-center justify-center mb-2">W</div>
                                <span class="text-[10px] font-black text-navy-900 uppercase tracking-tight">Wave</span>
                                <span class="text-[8px] font-bold text-gray-400 block mt-0.5">Mobile Money</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="orange_money" class="peer sr-only">
                            <div class="flex flex-col items-center justify-center p-4 border border-gray-100 rounded-xl hover:border-gold-500 peer-checked:border-gold-500 peer-checked:bg-gold-50/50 transition-all text-center">
                                <div class="w-6 h-6 rounded-full bg-orange-500 text-white font-black text-xs flex items-center justify-center mb-2">OM</div>
                                <span class="text-[10px] font-black text-navy-900 uppercase tracking-tight">Orange Money</span>
                                <span class="text-[8px] font-bold text-gray-400 block mt-0.5">Mobile Money</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="card" class="peer sr-only">
                            <div class="flex flex-col items-center justify-center p-4 border border-gray-100 rounded-xl hover:border-gold-500 peer-checked:border-gold-500 peer-checked:bg-gold-50/50 transition-all text-center">
                                <svg class="w-6 h-6 text-navy-900 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[10px] font-black text-navy-900 uppercase tracking-tight">Carte Bancaire</span>
                                <span class="text-[8px] font-bold text-gray-400 block mt-0.5">Visa / Mastercard</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-gold-50 rounded-2xl p-6 mb-8 border border-gold-100 text-xs text-navy-900 space-y-3 font-medium">
                    <h5 class="font-black uppercase tracking-wider text-gold-800 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Comment fonctionne l'Épargne ?
                    </h5>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Dès que vous validez ce plan d'épargne, vous pouvez effectuer des dépôts à votre rythme.</li>
                        <li>Une fois la somme totale de <strong>{{ number_format($targetAmount, 0, ',', ' ') }} FCFA</strong> atteinte, la commande du produit ou du service est automatiquement initiée et livrée.</li>
                        <li>Vous pouvez décider de <strong>retirer votre argent à tout moment</strong> avant que l'objectif ne soit atteint. Les fonds épargnés seront intégralement recrédités sur votre portefeuille client.</li>
                    </ul>
                </div>

                <button type="submit" class="w-full bg-navy-900 hover:bg-gold-500 text-white hover:text-navy-900 py-4 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Confirmer et lancer l'Épargne
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
