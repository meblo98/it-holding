@extends('layouts.admin')
@section('title', 'Opportunités & Apporteurs')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Opportunités & Apporteurs d'affaires</h1>
        <p class="text-sm text-gray-500 mt-0.5">Dossiers d'opportunité déclarés par les apporteurs — distincts du pipeline de vente courant.</p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

{{-- KPI CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Total Opportunités</p>
        <p class="text-2xl font-black text-navy-900">{{ $opportunities->total() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-red-100 p-4">
        <p class="text-xs font-bold text-red-400 uppercase mb-1">En attente d'arbitrage</p>
        <p class="text-2xl font-black text-red-700">{{ $flaggedCount }}</p>
    </div>
</div>

{{-- FILTER TABS --}}
<div class="flex gap-1 border-b border-gray-200 mb-5">
    <a href="{{ route('admin.opportunities.index') }}" class="px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px {{ !request('flagged') ? 'border-navy-600 text-navy-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">Toutes</a>
    <a href="{{ route('admin.opportunities.index', ['flagged' => 1]) }}" class="px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px {{ request('flagged') ? 'border-red-500 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">🚩 En attente d'arbitrage</a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    @if($opportunities->isEmpty())
    <div class="p-10 text-center text-gray-400 text-sm">Aucune opportunité pour le moment.</div>
    @else
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prospect</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Apporteur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget estimé</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doublon</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($opportunities as $opp)
            <tr class="hover:bg-gray-50 transition {{ $opp->duplicate_status === 'flagged' ? 'bg-red-50/40' : '' }}">
                <td class="px-6 py-4 text-xs font-mono font-bold text-navy-900">{{ $opp->opportunity_ref }}</td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $opp->name }}</div>
                    <div class="text-xs text-gray-500">{{ $opp->company ?: '—' }} · {{ $opp->phone ?: $opp->email ?: '—' }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $opp->partner?->name ?? '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $opp->budget ? number_format($opp->budget, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">{{ ucfirst($opp->status) }}</span>
                </td>
                <td class="px-6 py-4">
                    @if($opp->duplicate_status === 'flagged')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">🚩 En attente</span>
                    @elseif($opp->duplicate_status === 'cleared')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Arbitrée</span>
                    @else
                        <span class="text-[10px] text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.opportunities.show', $opp->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Voir</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $opportunities->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
