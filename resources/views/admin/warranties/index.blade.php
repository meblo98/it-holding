@extends('layouts.admin')
@section('title', 'Garanties')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Garanties Produits</h1>
        <p class="text-sm text-gray-500 mt-0.5">Suivi des garanties et IT HOLDING CARE+.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.warranties.scanner') }}" class="inline-flex items-center px-4 py-2 bg-gold-500 text-navy-900 rounded-md font-bold text-sm hover:bg-gold-600 transition shadow-sm gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 11v1m4-6h-1m-11 0h1m13-4a2 2 0 00-2-2h-3M7 5a2 2 0 00-2 2v3m14 4v3a2 2 0 01-2 2h-3M7 19a2 2 0 01-2-2v-3" />
            </svg>
            Scanner QR Code
        </a>
        <a href="{{ route('admin.warranties.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle Garantie
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

{{-- KPI CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-green-100 p-4">
        <p class="text-xs font-bold text-green-400 uppercase mb-1">Actives</p>
        <p class="text-2xl font-black text-green-700">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-amber-100 p-4">
        <p class="text-xs font-bold text-amber-400 uppercase mb-1">Expirant ≤ 30j</p>
        <p class="text-2xl font-black text-amber-700">{{ $stats['expiring_soon'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-red-100 p-4">
        <p class="text-xs font-bold text-red-400 uppercase mb-1">Expirées</p>
        <p class="text-2xl font-black text-red-700">{{ $stats['expired'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Total</p>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
    </div>
</div>

{{-- STATUS FILTERS --}}
<div class="flex flex-wrap gap-2 mb-5">
    <a href="{{ route('admin.warranties.index') }}" class="px-4 py-1.5 rounded-full text-xs font-bold border transition {{ !$status ? 'bg-navy-600 text-white border-navy-600' : 'bg-white text-gray-600 border-gray-300 hover:border-navy-400' }}">Tous</a>
    @foreach(['active'=>'Actives','expiring'=>'Expirant bientôt','expired'=>'Expirées','claimed'=>'Réclamées'] as $s => $l)
    <a href="{{ route('admin.warranties.index', ['status' => $s]) }}" class="px-4 py-1.5 rounded-full text-xs font-bold border transition {{ $status === $s ? 'bg-navy-600 text-white border-navy-600' : 'bg-white text-gray-600 border-gray-300 hover:border-navy-400' }}">{{ $l }}</a>
    @endforeach
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex gap-2">
        <form action="{{ route('admin.warranties.index') }}" method="GET" class="flex gap-2 w-full max-w-lg">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="N° garantie, client, produit, N° série..."
                   class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-gold-500 focus:border-gold-500">
            <button type="submit" class="bg-navy-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-navy-700 transition">Rechercher</button>
        </form>
    </div>

    @if($warranties->isEmpty())
    <div class="p-12 text-center">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <p class="text-gray-500 font-medium">Aucune garantie trouvée</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Garantie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($warranties as $w)
                @php
                    $cfg = \App\Models\Warranty::statusConfig($w->status);
                    $daysLeft = $w->days_remaining;
                    $expClass = $w->status === 'active' && $daysLeft <= 30 ? 'text-amber-600 font-bold' : 'text-gray-500';
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.warranties.show', $w->id) }}" class="text-sm font-bold text-navy-600 hover:underline">{{ $w->number }}</a>
                        @if($w->serial_number)
                            <div class="text-xs text-gray-400 font-mono">S/N: {{ $w->serial_number }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $w->product_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $w->client_name }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($w->type === 'care_plus')
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">CARE+</span>
                        @elseif($w->type === 'extended')
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Étendue</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">Standard</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm {{ $expClass }}">
                        {{ $w->expiry_date->format('d/m/Y') }}
                        @if($w->status === 'active' && $daysLeft <= 30 && $daysLeft > 0)
                            <div class="text-[10px] text-amber-500">⚠️ {{ $daysLeft }}j restants</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $cfg['classes'] }}">{{ $cfg['label'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.warranties.show', $w->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Voir</a>
                            <a href="{{ route('admin.warranties.edit', $w->id) }}" class="text-xs font-bold text-gold-600 bg-gold-50 hover:bg-gold-100 px-2.5 py-1 rounded transition">Modifier</a>
                            <form action="{{ route('admin.warranties.destroy', $w->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette garantie ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded transition">Suppr.</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $warranties->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
