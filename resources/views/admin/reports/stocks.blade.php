@extends('layouts.admin')

@section('title', 'Rapport des Stocks')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-gold-600 uppercase tracking-widest hover:underline flex items-center gap-1 mb-2">
            ← Retour au Centre de Rapports
        </a>
        <h1 class="text-3xl font-black text-navy-900 tracking-tight uppercase">Rapport des Stocks</h1>
    </div>
    <a href="{{ route('admin.reports.export', ['type' => 'stocks']) }}" class="inline-flex items-center px-4 py-2 bg-gold-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gold-700 transition">
        Exporter en CSV
    </a>
</div>

<!-- Performance Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-navy-900 text-white rounded-xl p-6">
        <span class="text-xs font-bold text-gold-400 uppercase tracking-widest">Valeur Stock (Prix de Vente)</span>
        <h3 class="text-2xl font-black mt-2">{{ number_format($totalValuation, 0, ',', ' ') }} FCFA</h3>
        <p class="text-xs text-gray-400 mt-2">Valorisation totale estimée à la revente brute.</p>
    </div>
    <div class="bg-gray-50 border border-gray-100 rounded-xl p-6">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Valeur Stock (Coût d'Achat)</span>
        <h3 class="text-2xl font-black text-navy-900 mt-2">{{ number_format($totalPurchaseValuation, 0, ',', ' ') }} FCFA</h3>
        <p class="text-xs text-gray-500 mt-2">Investissement total engagé dans le stock actuel.</p>
    </div>
    <div class="bg-gray-50 border border-gray-100 rounded-xl p-6">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Quantité Totale en Stock</span>
        <h3 class="text-2xl font-black text-navy-900 mt-2">{{ number_format($totalStock) }} unités</h3>
        <p class="text-xs text-gray-500 mt-2">Nombre total d'articles physiques en inventaire.</p>
    </div>
</div>

<!-- Low Stock Warnings -->
@if($lowStockProducts->count() > 0)
<div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8">
    <div class="flex items-center gap-2 mb-4 text-red-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span class="font-bold uppercase tracking-wider text-xs">Alertes Stocks Bas / Ruptures (Stock &le; 5)</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($lowStockProducts as $product)
        <div class="bg-white p-4 rounded-lg border border-red-100 flex justify-between items-center shadow-sm">
            <div>
                <span class="text-xs font-bold text-navy-900 block truncate max-w-[200px]">{{ $product->name }}</span>
                <span class="text-[10px] text-gray-400 uppercase tracking-wider">Ref: #IT-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <span class="px-2.5 py-1 rounded text-xs font-black {{ $product->stock == 0 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                {{ $product->stock }} Restant(s)
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Full Inventory Table -->
<h2 class="text-lg font-bold text-navy-900 mb-4 uppercase tracking-tight">Inventaire Complet & Valorisation</h2>
<div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produit</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Prix Achat (FCFA)</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Prix Vente (FCFA)</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Valeur Achat Total</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Valeur Vente Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($products as $p)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-navy-600 font-bold">
                        #{{ $p->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-navy-950">
                        {{ $p->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold">
                        <span class="px-2.5 py-1 rounded {{ $p->stock == 0 ? 'bg-red-100 text-red-700' : ($p->stock <= 5 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                            {{ $p->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                        {{ number_format($p->purchase_price ?: 0, 0, ',', ' ') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 font-semibold">
                        {{ number_format($p->price, 0, ',', ' ') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 font-medium">
                        {{ number_format($p->stock * ($p->purchase_price ?: 0), 0, ',', ' ') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-navy-900 font-bold">
                        {{ number_format($p->stock * $p->price, 0, ',', ' ') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
