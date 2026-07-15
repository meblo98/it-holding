@extends('layouts.admin')

@section('title', 'Centre de Rapports & Statistiques')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-navy-900 tracking-tight uppercase">Rapports & Statistiques</h1>
    <p class="text-sm text-gray-500 mt-1">Générez des rapports précis sur l'activité financière, technique et logistique de IT-HOLDING.</p>
</div>

<!-- Quick Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Ventes (Volume)</span>
            <span class="p-2 rounded-lg bg-gold-50 text-gold-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </span>
        </div>
        <h3 class="text-2xl font-black text-navy-900">{{ number_format($salesCount) }}</h3>
        <p class="text-xs text-gray-400 mt-1">Commandes totales passées</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Chiffre d'Affaires</span>
            <span class="p-2 rounded-lg bg-green-50 text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>
        <h3 class="text-2xl font-black text-navy-900">{{ number_format($totalCA, 0, ',', ' ') }} <span class="text-xs">FCFA</span></h3>
        <p class="text-xs text-gray-400 mt-1">Factures réglées</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Valeur du Stock (PV)</span>
            <span class="p-2 rounded-lg bg-blue-50 text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V5.414"/></svg>
            </span>
        </div>
        <h3 class="text-2xl font-black text-navy-900">{{ number_format($stockValuation, 0, ',', ' ') }} <span class="text-xs">FCFA</span></h3>
        <p class="text-xs text-gray-400 mt-1">Valeur au prix de vente</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">SAV (Tickets Actifs)</span>
            <span class="p-2 rounded-lg bg-red-50 text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </span>
        </div>
        <h3 class="text-2xl font-black text-navy-900">{{ number_format($openTicketsCount) }}</h3>
        <p class="text-xs text-gray-400 mt-1">Tickets de support ouverts</p>
    </div>
</div>

<!-- Report Navigation Modules Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- Sales Report -->
    <a href="{{ route('admin.reports.sales') }}" class="group block bg-white border border-gray-100 rounded-2xl p-8 hover:shadow-xl hover:border-gold-300 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-gold-50 text-gold-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h2 class="text-lg font-bold text-navy-900 group-hover:text-gold-600 transition-colors uppercase tracking-tight">Rapport des Ventes</h2>
        <p class="text-xs text-gray-400 mt-2 leading-relaxed">Suivi des ventes par période, catégories de produits, et clients. Comprend l'historique complet des articles commandés.</p>
    </a>

    <!-- Stock Valuation & Rotations -->
    <a href="{{ route('admin.reports.stocks') }}" class="group block bg-white border border-gray-100 rounded-2xl p-8 hover:shadow-xl hover:border-gold-300 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V5.414"/></svg>
        </div>
        <h2 class="text-lg font-bold text-navy-900 group-hover:text-gold-600 transition-colors uppercase tracking-tight">Rapport des Stocks</h2>
        <p class="text-xs text-gray-400 mt-2 leading-relaxed">Valorisation complète de vos stocks (achat vs vente), suivi des rotations, alertes de stocks bas et ruptures imminentes.</p>
    </a>

    <!-- Profits & Cost Analysis -->
    <a href="{{ route('admin.reports.profits') }}" class="group block bg-white border border-gray-100 rounded-2xl p-8 hover:shadow-xl hover:border-gold-300 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2 class="text-lg font-bold text-navy-900 group-hover:text-gold-600 transition-colors uppercase tracking-tight">Rapport des Bénéfices</h2>
        <p class="text-xs text-gray-400 mt-2 leading-relaxed">Analyse des marges commerciales et bénéfices nets. Rapprochement du coût de revient et du chiffre d'affaires.</p>
    </a>

    <!-- Supplier Analytics -->
    <a href="{{ route('admin.reports.suppliers') }}" class="group block bg-white border border-gray-100 rounded-2xl p-8 hover:shadow-xl hover:border-gold-300 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <h2 class="text-lg font-bold text-navy-900 group-hover:text-gold-600 transition-colors uppercase tracking-tight">Rapport Fournisseurs</h2>
        <p class="text-xs text-gray-400 mt-2 leading-relaxed">Statistiques sur les volumes de livraison, montant total acheté par marque et catalogue produit fournisseur.</p>
    </a>

    <!-- SAV & Warranties -->
    <a href="{{ route('admin.reports.sav') }}" class="group block bg-white border border-gray-100 rounded-2xl p-8 hover:shadow-xl hover:border-gold-300 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04c0 4.835 1.355 9.347 3.718 13.191A11.96 11.96 0 0012 21.481c2.901 0 5.537-.94 7.653-2.545a11.959 11.959 0 013.718-13.191z"/></svg>
        </div>
        <h2 class="text-lg font-bold text-navy-900 group-hover:text-gold-600 transition-colors uppercase tracking-tight">Garanties & Support SAV</h2>
        <p class="text-xs text-gray-400 mt-2 leading-relaxed">Suivi des garanties actives, taux de panne des équipements et volumes de tickets d'assistance résolus.</p>
    </a>
</div>
@endsection
