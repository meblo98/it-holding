@extends('layouts.app')

@section('title', 'Détails de la Commande #' . str_pad($order->id, 8, '0', STR_PAD_LEFT) . ' - ' . config('app.name'))

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
                <a href="{{ route('dashboard.orders') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider italic">Historique des commandes</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold uppercase tracking-wider">Détail commande</span>
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
                    <!-- Header -->
                    <div class="px-8 py-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50/30">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <a href="{{ route('dashboard.orders') }}" class="text-gray-400 hover:text-navy-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                </a>
                                <h3 class="text-sm font-black text-navy-900 uppercase tracking-tighter italic">Détails de la commande</h3>
                            </div>
                            <p class="text-[11px] font-bold text-gray-500 italic">ID: #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }} • {{ $order->items->count() }} Produits • Passée le {{ $order->created_at->format('d M, Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xl font-black text-gold-600 italic tracking-tighter">{{ number_format($order->total_amount, 0, ',', ' ') }} CFA</span>
                            <a href="#" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline flex items-center gap-1">
                                Laisser un avis
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="p-8 border-b border-gray-50">
                        <div class="relative pt-1">
                            <div class="flex items-center justify-between mb-8">
                                @php
                                    $steps = [
                                        ['name' => 'Commande passée', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'status' => 'completed'],
                                        ['name' => 'Emballage', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'status' => 'completed'],
                                        ['name' => 'En route', 'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1m-6 0a1 1 0 001-1H9', 'status' => 'active'],
                                        ['name' => 'Livrée', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'status' => 'pending'],
                                    ];
                                    
                                    // Map order status to progress
                                    $statusMap = [
                                        'pending' => 0,
                                        'processing' => 1,
                                        'shipped' => 2,
                                        'delivered' => 3,
                                        'completed' => 3,
                                    ];
                                    $currentStep = $statusMap[strtolower($order->status)] ?? 0;
                                @endphp

                                @foreach($steps as $index => $step)
                                <div class="flex flex-col items-center relative z-10">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-colors duration-500 {{ $index <= $currentStep ? 'bg-gold-500 border-gold-500 text-navy-900' : 'bg-white border-gray-100 text-gray-200' }}">
                                        @if($index < $currentStep)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                                        @endif
                                    </div>
                                    <span class="mt-2 text-[10px] font-black uppercase tracking-widest italic text-center {{ $index <= $currentStep ? 'text-navy-900' : 'text-gray-300' }}">{{ $step['name'] }}</span>
                                </div>
                                @endforeach

                                <!-- Progress Lines -->
                                <div class="absolute top-5 left-0 right-0 h-1 bg-gray-100 -z-0 rounded-full">
                                    <div class="h-full bg-gold-500 transition-all duration-1000 ease-in-out" style="width: {{ ($currentStep / 3) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Activity -->
                    <div class="p-8 border-b border-gray-50">
                        <h4 class="text-[12px] font-black text-navy-900 uppercase tracking-tighter italic mb-6">Activité de la commande</h4>
                        <div class="space-y-6 relative before:absolute before:left-5 before:top-2 before:bottom-2 before:w-px before:bg-gray-100">
                            @php
                                $activities = [
                                    ['title' => 'Votre commande a été confirmée.', 'date' => $order->created_at->format('d M, Y à H:i'), 'icon' => 'bg-blue-50 text-blue-500', 'svg' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                    ['title' => 'Votre commande est en cours de traitement.', 'date' => $order->created_at->addHours(2)->format('d M, Y à H:i'), 'icon' => 'bg-indigo-50 text-indigo-500', 'svg' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                                ];
                                if($currentStep >= 2) {
                                     $activities[] = ['title' => 'Votre colis a quitté l\'entrepôt.', 'date' => $order->updated_at->format('d M, Y à H:i'), 'icon' => 'bg-orange-50 text-orange-500', 'svg' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z'];
                                }
                                if($currentStep >= 3) {
                                     $activities[] = ['title' => 'Votre commande a été livrée. Merci de votre confiance !', 'date' => $order->updated_at->format('d M, Y à H:i'), 'icon' => 'bg-green-50 text-green-500', 'svg' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'];
                                }
                                $activities = array_reverse($activities);
                            @endphp

                            @foreach($activities as $act)
                            <div class="flex items-start gap-4 relative">
                                <div class="w-10 h-10 rounded-lg {{ $act['icon'] }} flex flex-shrink-0 items-center justify-center z-10 ring-4 ring-white shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $act['svg'] }}"/></svg>
                                </div>
                                <div class="pt-2">
                                    <p class="text-[11px] font-black text-navy-900 uppercase italic tracking-tight">{{ $act['title'] }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold italic">{{ $act['date'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Produits ({{ $order->items->count() }})</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Prix</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Quantité</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic text-right">Sous-total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gray-50 rounded p-1 flex-shrink-0">
                                                <img src="{{ $item->product->image ?: ($item->product->images->first()->path ?? asset('logo.jpeg')) }}" class="w-full h-full object-contain">
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-black text-navy-900 line-clamp-1 italic">{{ $item->product->name }}</p>
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">{{ $item->product->category->name ?? 'Catégorie' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-[11px] font-bold text-navy-900 italic">{{ number_format($item->price, 0, ',', ' ') }} CFA</td>
                                    <td class="px-8 py-5 text-[11px] font-bold text-gray-500 italic">x{{ $item->quantity }}</td>
                                    <td class="px-8 py-5 text-[11px] font-black text-navy-900 text-right italic">{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} CFA</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Addresses Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
                    <!-- Billing Address -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-4">
                        <h4 class="text-[12px] font-black text-navy-900 uppercase tracking-tighter italic border-b border-gray-50 pb-4">Adresse de facturation</h4>
                        <div class="space-y-1">
                            <p class="text-[11px] font-black text-navy-700 uppercase italic">{{ $order->customer_name }}</p>
                            <p class="text-[10px] text-gray-500 font-bold leading-loose italic">
                                {{ $order->address }}<br>
                                {{ $order->city }}, {{ $order->postal_code }} - Sénégal
                            </p>
                            <p class="text-[10px] text-navy-900 font-black mt-4 italic">Email: <span class="text-gray-500 font-bold uppercase">{{ $order->email }}</span></p>
                            <p class="text-[10px] text-navy-900 font-black italic">Tel: <span class="text-gray-500 font-bold uppercase">{{ $order->phone }}</span></p>
                        </div>
                    </div>

                    <!-- Shipping Address (Same for now) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-4">
                        <h4 class="text-[12px] font-black text-navy-900 uppercase tracking-tighter italic border-b border-gray-50 pb-4">Adresse de livraison</h4>
                        <div class="space-y-1">
                            <p class="text-[11px] font-black text-navy-700 uppercase italic">{{ $order->customer_name }}</p>
                            <p class="text-[10px] text-gray-500 font-bold leading-loose italic">
                                {{ $order->address }}<br>
                                {{ $order->city }}, {{ $order->postal_code }} - Sénégal
                            </p>
                            <p class="text-[10px] text-navy-900 font-black mt-4 italic">Email: <span class="text-gray-500 font-bold uppercase">{{ $order->email }}</span></p>
                            <p class="text-[10px] text-navy-900 font-black italic">Tel: <span class="text-gray-500 font-bold uppercase">{{ $order->phone }}</span></p>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-4 font-bold italic">
                        <h4 class="text-[12px] font-black text-navy-900 uppercase tracking-tighter italic border-b border-gray-50 pb-4">Notes de commande</h4>
                        <p class="text-[10px] text-gray-400 leading-relaxed">
                            {{ $order->notes ?: 'Aucune note particulière pour cette commande. Elle sera traitée selon nos protocoles standards de livraison rapide.' }}
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
