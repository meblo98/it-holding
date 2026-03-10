@extends('layouts.app')

@section('title', 'Tableau de bord - ' . config('app.name'))

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
                <span class="text-navy-900 font-bold uppercase tracking-wider">Tableau de bord</span>
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
                            <button type="submit" class="w-full px-6 py-4 flex items-center gap-3 text-sm font-bold uppercase tracking-tight italic text-red-500 hover:bg-red-50 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Déconnexion
                            </button>
                        </form>
                    </nav>
                </div>
            </aside>

            <!-- Main Dashboard Content -->
            <main class="flex-1 space-y-8">
                <!-- Welcome Section -->
                <div>
                    <h2 class="text-xl font-black text-navy-900 uppercase tracking-tighter italic mb-2">Bonjour, {{ $user->name }}</h2>
                    <p class="text-xs text-gray-400 italic font-medium leading-relaxed">
                        Depuis votre tableau de bord, vous pouvez facilement consulter l'historique de vos <a href="{{ route('dashboard.orders') }}" class="text-gold-600 underline">commandes récentes</a>, gérer vos <a href="{{ route('dashboard.settings') }}" class="text-gold-600 underline">adresses</a> de livraison et modifier votre <a href="{{ route('dashboard.settings') }}" class="text-gold-600 underline">mot de passe</a>.
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-[#EAF6FE] rounded-lg p-6 flex items-center gap-6 border border-[#D5EEFF]">
                        <div class="w-14 h-14 bg-white rounded flex items-center justify-center text-[#2DA5F3] shadow-inner">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div>
                            <span class="text-2xl font-black text-navy-900 block tracking-tighter">{{ str_pad($totalOrders, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest italic">Total Commandes</span>
                        </div>
                    </div>
                    <div class="bg-[#FFF3EB] rounded-lg p-6 flex items-center gap-6 border border-[#FFE7D6]">
                        <div class="w-14 h-14 bg-white rounded flex items-center justify-center text-[#FA8232] shadow-inner">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-2xl font-black text-navy-900 block tracking-tighter">{{ str_pad($pendingOrders, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest italic">Commandes En cours</span>
                        </div>
                    </div>
                    <div class="bg-[#EAF7E9] rounded-lg p-6 flex items-center gap-6 border border-[#D3EFD1]">
                        <div class="w-14 h-14 bg-white rounded flex items-center justify-center text-[#2DB324] shadow-inner">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-2xl font-black text-navy-900 block tracking-tighter">{{ str_pad($completedOrders, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest italic">Commandes Terminées</span>
                        </div>
                    </div>
                </div>

                <!-- Account Info & Billing -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Info Compte -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                            <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Informations du Compte</h3>
                        </div>
                        <div class="p-6 flex items-start gap-4">
                            <img src="https://i.pravatar.cc/150?u={{ $user->id }}" class="w-16 h-16 rounded-full border border-gray-100 shadow-sm">
                            <div class="space-y-1">
                                <p class="text-sm font-black text-navy-900 uppercase italic">{{ $user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider italic">{{ $user->city ?? 'Dakar' }}, {{ $user->country ?? 'Sénégal' }}</p>
                                <p class="text-[11px] text-navy-700 font-medium mt-2"><b>Email:</b> {{ $user->email }}</p>
                                <p class="text-[11px] text-navy-700 font-medium"><b>Tel:</b> {{ $user->phone ?? '+221 77 000 00 00' }}</p>
                                <a href="{{ route('dashboard.settings') }}" class="inline-block mt-4 text-[10px] font-black text-gold-600 uppercase tracking-widest hover:underline">Modifier le Profil</a>
                            </div>
                        </div>
                    </div>

                    <!-- Adresse de Facturation -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                            <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Adresse de Facturation</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-1">
                                <p class="text-sm font-black text-navy-900 uppercase italic">{{ $user->billing_first_name }} {{ $user->billing_last_name }}</p>
                                <p class="text-[11px] text-gray-500 font-medium leading-relaxed italic">
                                    {{ $user->billing_address ?? 'Aucune adresse enregistrée' }}<br>
                                    {{ $user->billing_zip }} {{ $user->billing_city }} - {{ $user->country }}
                                </p>
                                <p class="text-[11px] text-navy-700 font-medium mt-2"><b>Email:</b> {{ $user->email }}</p>
                                <p class="text-[11px] text-navy-700 font-medium"><b>Tel:</b> {{ $user->phone }}</p>
                                <a href="{{ route('dashboard.settings') }}" class="inline-block mt-4 text-[10px] font-black text-gold-600 uppercase tracking-widest hover:underline">Gérer les Adresses</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                        <h3 class="text-sm font-black text-navy-900 uppercase tracking-tighter italic">Commandes Récentes</h3>
                        <a href="{{ route('dashboard.orders') }}" class="text-[10px] font-black text-gold-600 uppercase tracking-widest hover:underline flex items-center gap-2">
                            Tout voir
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-8 py-4 border-b border-gray-100 italic">ID Commande</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Statut</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Date</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Total</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-4 text-[11px] font-bold text-navy-900 italic">#{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-8 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-orange-100 text-orange-600',
                                                'processing' => 'bg-indigo-100 text-indigo-600',
                                                'shipped' => 'bg-blue-100 text-blue-600',
                                                'delivered' => 'bg-green-100 text-green-600',
                                                'canceled' => 'bg-red-100 text-red-600',
                                                'completed' => 'bg-green-100 text-green-600',
                                            ];
                                            $col = $statusColors[strtolower($order->status)] ?? 'bg-gray-100 text-gray-600';
                                        @endphp
                                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded {{ $col }} italic">
                                            {{ strtoupper($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-4 text-[11px] font-bold text-gray-500">{{ $order->created_at->format('d M, Y H:i') }}</td>
                                    <td class="px-8 py-4 text-[11px] font-black text-navy-900 italic">{{ number_format($order->total_amount, 0, ',', ' ') }} CFA</td>
                                    <td class="px-8 py-4">
                                        <a href="{{ route('dashboard.orders.show', $order->id) }}" class="text-[10px] font-black text-gold-600 uppercase tracking-widest hover:underline flex items-center gap-2 group-hover:translate-x-1 transition-transform">
                                            Détails
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-10 text-center text-xs text-gray-400 font-bold italic">Aucune commande trouvée.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Browsing History (Placeholder with latest products) -->
                <div>
                    <h2 class="text-xl font-black text-navy-900 uppercase tracking-tighter italic mb-8">Historique de Navigation</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($browsingHistory as $prod)
                        <div class="bg-white border border-gray-100 rounded-lg p-4 group cursor-pointer shadow-sm hover:shadow-md transition-shadow">
                            <div class="aspect-square bg-gray-50 rounded mb-4 overflow-hidden p-2">
                                <img src="{{ $prod->image ?: ($prod->images->first()->path ?? asset('logo.jpeg')) }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform">
                            </div>
                            <h4 class="text-[10px] font-bold text-navy-900 line-clamp-2 italic h-8 leading-tight group-hover:text-gold-600">{{ $prod->name }}</h4>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs font-black text-gold-600 tracking-tight">{{ number_format($prod->price, 0, ',', ' ') }} <span class="text-[8px]">CFA</span></span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
