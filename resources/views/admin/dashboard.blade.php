@extends('layouts.admin')

@section('title', 'Tableau de Bord')

@section('content')
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Tableau de Bord</h1>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Services</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['services'] }}</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Projets Portfolio</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['projects'] }}</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Articles Blog</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['posts'] }}</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Produits</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['products'] }}</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Commandes</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['orders'] }}</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Messages Contact</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['contacts'] }}</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-gold-500">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Devis</dt>
                    <dd class="mt-1 text-3xl font-semibold text-navy-600">{{ $stats['quotes'] }}</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-navy-500">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Factures</dt>
                    <dd class="mt-1 text-3xl font-semibold text-navy-600">{{ $stats['invoices'] }}</dd>
                </div>
            </div>
        </div>

        <!-- Section Rentabilité et Bénéfices -->
        <div class="mt-8 bg-white overflow-hidden shadow sm:rounded-lg border-t-4 border-gold-500">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-50 gap-2">
                <div>
                    <h3 class="text-lg leading-6 font-bold text-navy-900 uppercase tracking-tight flex items-center gap-2">
                        <svg class="h-6 w-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Analyse de Rentabilité & Bénéfices
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Données calculées en temps réel à partir des commandes validées et des factures payées/envoyées.</p>
                </div>
                <span class="px-3 py-1 bg-navy-600 text-white rounded-full text-xs font-bold">Marge Globale : {{ number_format($financials['margin'], 1) }} %</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                <!-- Chiffre d'Affaires -->
                <div class="p-6">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-widest">Chiffre d'Affaires (C.A.)</dt>
                    <dd class="mt-2 text-3xl font-black text-navy-900">{{ number_format($financials['revenue'], 0, ',', ' ') }} <span class="text-sm font-semibold">FCFA</span></dd>
                    <div class="mt-4 flex flex-col gap-1 text-xs text-gray-500">
                        <div class="flex justify-between">
                            <span>Boutique (Brut) :</span>
                            <span class="font-bold text-gray-700">{{ number_format($financials['orders']['gross_revenue'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if(isset($financials['commissions']) && $financials['commissions'] > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Commissions :</span>
                            <span class="font-bold">-{{ number_format($financials['commissions'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span>Factures :</span>
                            <span class="font-bold text-gray-700">{{ number_format($financials['invoices']['revenue'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>

                <!-- Coût d'Achat -->
                <div class="p-6">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-widest font-semibold">Coût d'Achat Total</dt>
                    <dd class="mt-2 text-3xl font-black text-slate-500">{{ number_format($financials['cost'], 0, ',', ' ') }} <span class="text-sm font-semibold">FCFA</span></dd>
                    <div class="mt-4 flex flex-col gap-1 text-xs text-gray-500">
                        <div class="flex justify-between">
                            <span>Boutique :</span>
                            <span class="font-bold text-gray-700">{{ number_format($financials['orders']['cost'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Factures :</span>
                            <span class="font-bold text-gray-700">{{ number_format($financials['invoices']['cost'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>

                <!-- Bénéfice Net -->
                <div class="p-6 bg-amber-50/20">
                    <dt class="text-xs font-bold text-amber-600 uppercase tracking-widest">Bénéfice Net (Profit)</dt>
                    <dd class="mt-2 text-3xl font-black text-gold-600">{{ number_format($financials['profit'], 0, ',', ' ') }} <span class="text-sm font-semibold">FCFA</span></dd>
                    <div class="mt-4 flex flex-col gap-1 text-xs text-gray-500">
                        <div class="flex justify-between">
                            <span>Boutique :</span>
                            <span class="font-bold text-gold-600">{{ number_format($financials['orders']['profit'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Factures :</span>
                            <span class="font-bold text-gold-600">{{ number_format($financials['invoices']['profit'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>

                <!-- Marge Moyenne -->
                <div class="p-6">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-widest">Marge Commerciale</dt>
                    <dd class="mt-2 text-3xl font-black text-green-600">{{ number_format($financials['margin'], 1) }} %</dd>
                    <p class="text-xs text-gray-400 mt-4 leading-relaxed">
                        Chaque vente génère en moyenne un bénéfice net de <span class="font-bold text-navy-600">{{ number_format($financials['margin'], 1) }} %</span> après déduction du coût d'achat fournisseur.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section Graphiques Financiers -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Graphique Évolution Mensuelle (Line Chart) -->
            <div class="bg-white overflow-hidden shadow sm:rounded-lg border border-gray-100 p-6 lg:col-span-2">
                <h3 class="text-lg leading-6 font-bold text-navy-900 uppercase tracking-tight mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Évolution Mensuelle du C.A. & des Bénéfices
                </h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="monthlyFinancialsChart"></canvas>
                </div>
            </div>

            <!-- Graphique Répartition (Doughnut Chart) -->
            <div class="bg-white overflow-hidden shadow sm:rounded-lg border border-gray-100 p-6 lg:col-span-1">
                <h3 class="text-lg leading-6 font-bold text-navy-900 uppercase tracking-tight mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    Origine des Ventes
                </h3>
                <div class="relative flex items-center justify-center" style="height: 300px;">
                    <canvas id="salesDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Latest Contacts -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Derniers Messages</h3>
                </div>
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($latestContacts as $contact)
                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-navy-600 truncate">{{ $contact->name }}</p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $contact->type }}</p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        {{ Str::limit($contact->subject, 30) }} - {{ Str::limit($contact->message, 50) }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <p>{{ $contact->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 text-center text-gray-500">Aucun message reçue.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Latest Orders -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Dernières Commandes</h3>
                </div>
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($latestOrders as $order)
                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-navy-600 truncate">#{{ $order->id }} -
                                    {{ $order->customer_name }}</p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $order->status }}</p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <p>{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 text-center text-gray-500">Aucune commande récente.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <!-- Management Links -->
        <div class="mt-8">
            <h2 class="text-xl leading-6 font-medium text-gray-900 mb-4">Gestion du Contenu</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Services -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Services
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            Gérer les offres
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('admin.services.index') }}"
                                class="font-medium text-navy-600 hover:text-gold-600">
                                Voir tout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Portfolio -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Portfolio
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            Gérer les projets
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('admin.projects.index') }}"
                                class="font-medium text-navy-600 hover:text-gold-600">
                                Voir tout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Blog -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Blog
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            Gérer les articles
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('admin.posts.index') }}"
                                class="font-medium text-navy-600 hover:text-gold-600">
                                Voir tout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Boutique
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            Gérer les produits
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('admin.products.index') }}"
                                class="font-medium text-navy-600 hover:text-gold-600">
                                Voir tout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21H5a2 2 0 01-2-2V9.414a1 1 0 010-1.414l7-7a1 1 0 011.414 0l7 7a1 1 0 010 1.414V19a2 2 0 01-2 2h-2m-5-10l3 3m0 0l3-3m-3 3V8" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Catégories
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            Gérer les catégories
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('admin.categories.index') }}"
                                class="font-medium text-navy-600 hover:text-gold-600">
                                Voir tout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Brands -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21H5a2 2 0 01-2-2V9.414a1 1 0 010-1.414l7-7a1 1 0 011.414 0l7 7a1 1 0 010 1.414V19a2 2 0 01-2 2h-2m-5-10l3 3m0 0l3-3m-3 3V8" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Marques
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            Gérer les marques
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('admin.brands.index') }}"
                                class="font-medium text-navy-600 hover:text-gold-600">
                                Voir tout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Orders -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Commandes
                                    </dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">
                                            Gérer les commandes
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('admin.orders.index') }}"
                                class="font-medium text-navy-600 hover:text-gold-600">
                                Voir tout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Line Chart: Monthly Revenue & Profit
            const ctxMonthly = document.getElementById('monthlyFinancialsChart').getContext('2d');
            
            // Create nice gradients for the charts
            const revGradient = ctxMonthly.createLinearGradient(0, 0, 0, 300);
            revGradient.addColorStop(0, 'rgba(22, 38, 70, 0.2)');
            revGradient.addColorStop(1, 'rgba(22, 38, 70, 0.0)');
            
            const prfGradient = ctxMonthly.createLinearGradient(0, 0, 0, 300);
            prfGradient.addColorStop(0, 'rgba(202, 138, 4, 0.2)');
            prfGradient.addColorStop(1, 'rgba(202, 138, 4, 0.0)');

            const monthlyChart = new Chart(ctxMonthly, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [
                        {
                            label: "Chiffre d'Affaires",
                            data: {!! json_encode($chartData['revenue']) !!},
                            borderColor: 'rgba(22, 38, 70, 1)',
                            backgroundColor: revGradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(22, 38, 70, 1)',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: 'rgba(22, 38, 70, 1)',
                            pointHoverBorderColor: '#fff',
                        },
                        {
                            label: 'Bénéfice Net',
                            data: {!! json_encode($chartData['profit']) !!},
                            borderColor: 'rgba(202, 138, 4, 1)',
                            backgroundColor: prfGradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(202, 138, 4, 1)',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: 'rgba(202, 138, 4, 1)',
                            pointHoverBorderColor: '#fff',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    weight: 'bold',
                                    family: 'sans-serif'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR').format(value) + ' F';
                                },
                                font: {
                                    family: 'sans-serif'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'sans-serif'
                                }
                            }
                        }
                    }
                }
            });

            // 2. Doughnut Chart: Origin of Sales
            const ctxDistribution = document.getElementById('salesDistributionChart').getContext('2d');
            const distributionChart = new Chart(ctxDistribution, {
                type: 'doughnut',
                data: {
                    labels: ['Boutique en ligne', 'Factures directes'],
                    datasets: [{
                        data: [
                            {{ $financials['orders']['revenue'] }},
                            {{ $financials['invoices']['revenue'] }}
                        ],
                        backgroundColor: [
                            'rgba(22, 38, 70, 0.85)', // navy
                            'rgba(202, 138, 4, 0.85)'   // gold
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    weight: 'bold',
                                    family: 'sans-serif'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    label += new Intl.NumberFormat('fr-FR').format(value) + ' FCFA (' + percentage + '%)';
                                    return label;
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        });
    </script>
@endsection
