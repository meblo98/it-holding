@extends('layouts.admin')

@section('title', 'Rapport Fournisseurs')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-gold-600 uppercase tracking-widest hover:underline flex items-center gap-1 mb-2">
        ← Retour au Centre de Rapports
    </a>
    <h1 class="text-3xl font-black text-navy-900 tracking-tight uppercase">Rapport Fournisseurs</h1>
    <p class="text-sm text-gray-500 mt-1">Analyse du catalogue produit et de la valeur des marchandises fournies par chaque partenaire.</p>
</div>

<!-- Table -->
<div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fournisseur</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Contact Principal</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Téléphone / Email</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Nb. Produits Référencés</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Valeur Estimée Stock (FCFA)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($supplierData as $data)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-navy-900 block">{{ $data['supplier']->company_name }}</span>
                        @if($data['supplier']->code)
                        <span class="text-[9px] text-gold-600 font-bold uppercase tracking-widest">{{ $data['supplier']->code }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-navy-950 font-medium">
                        {{ $data['supplier']->contact_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $data['supplier']->phone }}</div>
                        <div class="text-xs">{{ $data['supplier']->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-navy-900">
                        {{ $data['products_count'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-navy-900">
                        {{ number_format($data['stock_value'], 0, ',', ' ') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Aucun fournisseur enregistré dans la base de données.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
