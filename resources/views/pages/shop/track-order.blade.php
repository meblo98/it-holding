@extends('layouts.app')

@section('title', 'Suivi de commande - ' . config('app.name'))

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
                <span class="text-navy-900 font-bold uppercase tracking-wider italic">Suivi de commande</span>
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
                                ['name' => 'Panier', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>', 'route' => 'shop.cart'],
                                ['name' => 'Liste de souhaits', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>', 'route' => '#'],
                                ['name' => 'Cartes & Adresses', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>', 'route' => '#'],
                                ['name' => 'Paramètres', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', 'route' => 'dashboard.settings'],
                            ];
                        @endphp
                        @foreach($navItems as $item)
                        <a href="{{ $item['route'] != '#' ? route($item['route']) : '#' }}" class="px-6 py-4 flex items-center gap-3 text-sm font-bold uppercase tracking-tight italic transition-all {{ request()->routeIs($item['route']) ? 'bg-gold-500 text-navy-900' : 'text-gray-400 hover:text-navy-900 hover:bg-gray-50 border-b border-gray-50' }}">
                            {!! $item['icon'] !!}
                            {{ $item['name'] }}
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
            <main class="flex-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50">
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Suivre une commande</h3>
                    </div>
                    <div class="p-8 italic leading-relaxed">
                        <p class="text-xs text-gray-500 mb-8 max-w-xl">
                            Pour suivre votre commande, veuillez saisir votre ID de commande dans la case ci-dessous et appuyer sur le bouton "Suivre". Celui-ci vous a été communiqué sur votre reçu et dans l'e-mail de confirmation que vous auriez dû recevoir.
                        </p>
                        <form action="#" method="GET" class="space-y-6 max-w-xl">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">ID de Commande</label>
                                    <input type="text" name="order_id" placeholder="ID de la commande" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 outline-none transition-all italic">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Email de Facturation</label>
                                    <input type="email" name="order_email" value="{{ $user->email }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-lg text-xs font-bold text-navy-900 focus:ring-1 focus:ring-gold-500 outline-none transition-all italic">
                                </div>
                            </div>
                            <div class="flex items-start gap-4 p-4 bg-orange-50 border border-orange-100 rounded-lg">
                                <svg class="w-5 h-5 text-orange-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-[10px] text-orange-700 font-bold uppercase tracking-tight">Veuillez noter que le suivi en temps réel peut prendre jusqu'à 24h après l'expédition.</p>
                            </div>
                            <button type="submit" class="btn-primary-gold px-10 py-3 text-[10px] font-black uppercase tracking-widest transition-all">
                                Suivre la commande
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Recent Order Quick Status -->
                @php
                    $latestOrder = $user->orders()->latest()->first();
                @endphp
                @if($latestOrder)
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50">
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Statut de votre dernière commande</h3>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-8">
                            <div class="space-y-1">
                                <p class="text-xs font-black text-navy-900 uppercase italic">Commande #{{ str_pad($latestOrder->id, 8, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest italic">{{ $latestOrder->created_at->format('d M, Y') }}</p>
                            </div>
                            <span class="px-4 py-2 bg-gold-100 text-gold-700 text-[10px] font-black uppercase tracking-widest rounded-full animate-pulse italic">
                                {{ strtoupper($latestOrder->status) }}
                            </span>
                        </div>
                        <a href="{{ route('dashboard.orders.show', $latestOrder->id) }}" class="text-[10px] font-black text-gold-600 uppercase tracking-widest hover:underline flex items-center gap-2">
                            VOIR LES DÉTAILS COMPLETS
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection
