@extends('layouts.app')

@section('title', 'Mon Épargne - ' . config('app.name'))

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
                <span class="text-navy-900 font-bold uppercase tracking-wider italic">Mon Épargne</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            @include('layouts.client_sidebar')

            <!-- Main Content -->
            <main class="flex-1">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-lg flex items-center gap-3 text-green-600 text-xs font-bold uppercase tracking-widest italic">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-lg flex items-center gap-3 text-red-600 text-xs font-bold uppercase tracking-widest italic">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                        <h3 class="text-sm font-black text-navy-900 uppercase tracking-tighter italic">Mes Épargnes Produits & Services</h3>
                        <span class="bg-gold-100 text-gold-800 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full italic">
                            {{ $savingPlans->total() }} Plan(s) d'Épargne
                        </span>
                    </div>

                    <div class="p-8">
                        @if($client)
                        <div class="mb-8 bg-navy-900 rounded-2xl p-6 text-white flex flex-col md:flex-row justify-between items-center gap-6">
                            <div>
                                <span class="text-[10px] font-black text-gold-500 uppercase tracking-wider block mb-1">Portefeuille Client</span>
                                <h4 class="text-3xl font-black italic">{{ number_format($client->wallet_balance, 0, ',', ' ') }} FCFA</h4>
                            </div>
                            <div class="text-sm text-gray-300 max-w-md font-medium text-center md:text-right">
                                Utilisez le solde de votre portefeuille pour effectuer des dépôts instantanés sur vos plans d'épargne.
                            </div>
                        </div>
                        @endif

                        @forelse($savingPlans as $plan)
                            <div class="border border-gray-100 rounded-2xl p-6 mb-6 hover:border-gold-500 transition-all">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                                    <div>
                                        <span class="inline-block px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-wider mb-2 {{ $plan->product_id ? 'bg-navy-100 text-navy-800' : 'bg-gold-100 text-gold-800' }}">
                                            {{ $plan->product_id ? 'Produit' : 'Service' }}
                                        </span>
                                        <h4 class="text-base font-black text-navy-900 uppercase tracking-tight italic">
                                            {{ $plan->product_id ? $plan->product->name : $plan->service->title }}
                                        </h4>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mt-1">
                                            Lancé le {{ $plan->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($plan->status === 'active')
                                            <span class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider italic">Actif</span>
                                        @elseif($plan->status === 'completed')
                                            <span class="bg-green-50 text-green-700 border border-green-200 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider italic">Complété & Livré</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-500 border border-gray-200 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider italic">Retiré</span>
                                        @endif

                                        <a href="{{ route('dashboard.savings.show', $plan->id) }}" class="inline-flex items-center gap-2 bg-navy-900 text-white hover:bg-gold-500 hover:text-navy-900 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all">
                                            Gérer l'épargne
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs font-bold text-gray-500">
                                        <span>Progression ({{ round($plan->progressPercent, 1) }}%)</span>
                                        <span class="text-navy-900">{{ number_format($plan->current_amount, 0, ',', ' ') }} / {{ number_format($plan->target_amount, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-navy-800 to-gold-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $plan->progressPercent) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-20">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-xs text-gray-400 font-bold italic mb-6">Vous n'avez pas encore de plan d'épargne en cours.</p>
                                <div class="flex justify-center gap-4">
                                    <a href="{{ route('shop.index') }}" class="inline-flex items-center bg-navy-900 text-white hover:bg-gold-500 hover:text-navy-900 px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all">Parcourir la boutique</a>
                                    <a href="{{ route('services.index') }}" class="inline-flex items-center border border-navy-900 text-navy-900 hover:bg-navy-900 hover:text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all">Nos Services</a>
                                </div>
                            </div>
                        @endforelse

                        @if($savingPlans->count() > 0)
                            <div class="mt-8">
                                {{ $savingPlans->links('pagination::tailwind') }}
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
