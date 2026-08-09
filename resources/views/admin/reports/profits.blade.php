@extends('layouts.admin')

@section('title', 'Rapport des Bénéfices')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-gold-600 uppercase tracking-widest hover:underline flex items-center gap-1 mb-2">
            ← Retour au Centre de Rapports
        </a>
        <h1 class="text-3xl font-black text-navy-900 tracking-tight uppercase">Rapport des Bénéfices</h1>
    </div>
    <a href="{{ route('admin.reports.export', ['type' => 'profits', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-4 py-2 bg-gold-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gold-700 transition">
        Exporter en CSV
    </a>
</div>

<!-- Date Filter Form -->
<div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm mb-8">
    <form method="GET" action="{{ route('admin.reports.profits') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Date de Début</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Date de Fin</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div class="flex gap-4">
            <button type="submit" class="flex-grow inline-flex justify-center items-center px-4 py-2.5 bg-navy-900 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-navy-800 transition">
                Filtrer
            </button>
            <a href="{{ route('admin.reports.profits') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-200 text-gray-500 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-50 transition">
                Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- Profit KPIs Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Chiffre d'Affaires Net</span>
        <h3 class="text-2xl font-black text-navy-900">{{ number_format($revenue, 0, ',', ' ') }} FCFA</h3>
        <p class="text-xs text-gray-500 mt-1">Revenus net après déduction des commissions partenaires ({{ number_format($commissions, 0, ',', ' ') }} FCFA déduits).</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Coût d'Achat des Marchandises</span>
        <h3 class="text-2xl font-black text-gray-700">{{ number_format($cogs, 0, ',', ' ') }} FCFA</h3>
        <p class="text-xs text-gray-500 mt-1">Valeur totale d'achat du stock vendu.</p>
    </div>

    <div class="bg-navy-900 text-white rounded-xl p-6 shadow-sm">
        <span class="text-xs font-bold text-gold-400 uppercase tracking-widest block mb-2">Bénéfice Brut (Net)</span>
        <h3 class="text-2xl font-black text-gold-500">{{ number_format($grossProfit, 0, ',', ' ') }} FCFA</h3>
        <p class="text-xs text-gray-400 mt-1">Revenus restants après coût de revient et commissions.</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Marge Commerciale</span>
        <h3 class="text-2xl font-black text-green-600">{{ number_format($margin, 2) }}%</h3>
        <p class="text-xs text-gray-500 mt-1">Ratio bénéfice brut / chiffre d'affaires net.</p>
    </div>
</div>

<!-- Visual Explanation Card -->
<div class="bg-gold-50/50 border border-gold-200 rounded-xl p-6">
    <div class="flex items-start gap-4">
        <div class="p-3 bg-gold-100 rounded-lg text-gold-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h4 class="text-sm font-bold text-navy-900 uppercase tracking-wider mb-1">Comment le bénéfice est calculé</h4>
            <p class="text-xs text-navy-950 leading-relaxed font-medium">
                Le bénéfice brut est basé uniquement sur les commandes dont le paiement a été validé/réglé (`payment_status = paid`).
                Le coût d'achat des marchandises vendues (COGS) utilise le snapshot du prix d'achat (`purchase_price`) enregistré dans l'article de commande au moment de la vente pour garantir l'exactitude historique, indépendamment des variations futures des prix d'achat chez les fournisseurs.
                De plus, les commissions reversées aux partenaires / revendeurs affiliés sont automatiquement déduites du chiffre d'affaires net et du bénéfice brut pour refléter la rentabilité réelle.
            </p>
        </div>
    </div>
</div>
@endsection
