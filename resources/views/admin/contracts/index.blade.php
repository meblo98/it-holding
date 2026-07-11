@extends('layouts.admin')
@section('title', 'Contrats de Maintenance')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Contrats de Maintenance</h1>
        <p class="text-sm text-gray-500 mt-0.5">Gestion des contrats d'assistance et de maintenance.</p>
    </div>
    <a href="{{ route('admin.contracts.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau Contrat
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

{{-- KPI CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-green-100 p-4">
        <p class="text-xs font-bold text-green-400 uppercase mb-1">Actifs</p>
        <p class="text-2xl font-black text-green-700">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-amber-100 p-4">
        <p class="text-xs font-bold text-amber-400 uppercase mb-1">Expirant ≤ 30j</p>
        <p class="text-2xl font-black text-amber-700">{{ $stats['expiring_soon'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Total</p>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-navy-100 p-4">
        <p class="text-xs font-bold text-navy-400 uppercase mb-1">CA Contrats actifs</p>
        <p class="text-lg font-black text-navy-900">{{ number_format($stats['revenue'], 0, ',', ' ') }} <span class="text-xs font-semibold text-gray-400">FCFA</span></p>
    </div>
</div>

{{-- STATUS TABS --}}
<div class="flex flex-wrap gap-2 mb-5">
    @foreach(['' => 'Tous', 'active' => 'Actifs', 'expiring' => 'Expirant bientôt', 'expired' => 'Expirés', 'draft' => 'Brouillons', 'cancelled' => 'Annulés'] as $s => $l)
    <a href="{{ route('admin.contracts.index', $s ? ['status' => $s] : []) }}"
       class="px-4 py-1.5 rounded-full text-xs font-bold border transition {{ $status === $s ? 'bg-navy-600 text-white border-navy-600' : 'bg-white text-gray-600 border-gray-300 hover:border-navy-400' }}">
        {{ $l }}
    </a>
    @endforeach
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex gap-2">
        <form action="{{ route('admin.contracts.index') }}" method="GET" class="flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="N° contrat, nom client..."
                   class="border-gray-300 rounded-md shadow-sm text-sm w-72 focus:ring-gold-500 focus:border-gold-500">
            <button type="submit" class="bg-navy-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-navy-700 transition">Rechercher</button>
        </form>
    </div>

    @if($contracts->isEmpty())
    <div class="p-12 text-center">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-gray-500 font-medium">Aucun contrat trouvé</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contrat</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Interventions</th>
                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($contracts as $contract)
                @php $cfg = \App\Models\MaintenanceContract::statusConfig($contract->status); @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.contracts.show', $contract->id) }}" class="text-sm font-bold text-navy-600 hover:underline">{{ $contract->number }}</a>
                        <div class="text-xs text-gray-400">SLA : {{ $contract->response_time_hours }}h</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $contract->client_name }}</div>
                        @if($contract->client_phone) <div class="text-xs text-gray-400">{{ $contract->client_phone }}</div> @endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ \App\Models\MaintenanceContract::typeLabel($contract->type) }}</span>
                    </td>
                    <td class="px-5 py-4 text-center text-sm">
                        <span class="font-bold {{ $contract->interventions_remaining === 0 ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $contract->interventions_used }} / {{ $contract->interventions_included }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right font-bold text-gray-900 text-sm">{{ number_format($contract->price, 0, ',', ' ') }} FCFA</td>
                    <td class="px-5 py-4 text-sm {{ $contract->days_remaining <= 30 && $contract->status === 'active' ? 'text-amber-600 font-bold' : 'text-gray-500' }}">
                        {{ $contract->end_date->format('d/m/Y') }}
                        @if($contract->status === 'active' && $contract->days_remaining <= 30)
                            <div class="text-[10px] text-amber-500">⚠️ {{ $contract->days_remaining }}j restants</div>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $cfg['classes'] }}">{{ $cfg['label'] }}</span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.contracts.show', $contract->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Voir</a>
                            <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="text-xs font-bold text-gold-600 bg-gold-50 hover:bg-gold-100 px-2.5 py-1 rounded transition">Modifier</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $contracts->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
