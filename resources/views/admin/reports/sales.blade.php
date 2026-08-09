@extends('layouts.admin')

@section('title', 'Rapport des Ventes')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-gold-600 uppercase tracking-widest hover:underline flex items-center gap-1 mb-2">
            ← Retour au Centre de Rapports
        </a>
        <h1 class="text-3xl font-black text-navy-900 tracking-tight uppercase">Rapport des Ventes</h1>
    </div>
    <a href="{{ route('admin.reports.export', ['type' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-4 py-2 bg-gold-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gold-700 transition">
        Exporter en CSV
    </a>
</div>

<!-- Filters -->
<div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm mb-8">
    <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
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
            <a href="{{ route('admin.reports.sales') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-200 text-gray-500 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-50 transition">
                Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- Performance Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-navy-900 text-white rounded-xl p-6">
        <span class="text-xs font-bold text-gold-400 uppercase tracking-widest">Revenus des Ventes (Net)</span>
        <h3 class="text-3xl font-black mt-2">{{ number_format($totalSales, 0, ',', ' ') }} FCFA</h3>
        <p class="text-xs text-gray-400 mt-2">Net après déduction des commissions partenaires ({{ number_format($commissions, 0, ',', ' ') }} FCFA déduits).</p>
    </div>
    <div class="bg-gray-50 border border-gray-100 rounded-xl p-6">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Articles Commandés</span>
        <h3 class="text-3xl font-black text-navy-900 mt-2">{{ number_format($totalQty) }} unités</h3>
        <p class="text-xs text-gray-500 mt-2">Volume total d'équipements vendus sur la période.</p>
    </div>
</div>

<!-- Table -->
<div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Facture</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produit / Configuration</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Quantité</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Prix Unit. (FCFA)</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total (FCFA)</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-navy-600">
                        #{{ $item->order_id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-navy-950 font-medium">
                        {{ $item->order->customer_name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="font-bold text-navy-900">{{ $item->product->name ?? 'Produit retiré' }}</div>
                        @if(!empty($item->options))
                            <div class="mt-1 text-xs text-gray-500 font-medium space-y-0.5">
                                @foreach($item->options as $opt)
                                    <span class="block">• {{ $opt['name'] }} : {{ $opt['value'] }} (+{{ number_format($opt['price'], 0, ',', ' ') }} F)</span>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-navy-900">
                        {{ $item->quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                        {{ number_format($item->price, 0, ',', ' ') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-navy-900">
                        {{ number_format($item->quantity * $item->price, 0, ',', ' ') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                        {{ $item->created_at->format('d/m/Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">Aucune vente enregistrée sur cette période.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
