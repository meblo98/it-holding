@extends('layouts.admin')
@section('title', 'IT HOLDING CARE+')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">IT HOLDING CARE+</h1>
        <p class="text-sm text-gray-500 mt-0.5">Abonnements garantie prolongée et assistance prioritaire.</p>
    </div>
    <a href="{{ route('admin.care.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-navy-700 text-white rounded-md font-bold text-sm hover:opacity-90 transition shadow-sm gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvel Abonnement
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

{{-- KPI CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-lg shadow-sm p-4 text-white">
        <p class="text-xs font-bold text-purple-200 uppercase mb-1">Abonnés actifs</p>
        <p class="text-2xl font-black">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-amber-100 p-4">
        <p class="text-xs font-bold text-amber-400 uppercase mb-1">Expirant ≤ 30j</p>
        <p class="text-2xl font-black text-amber-700">{{ $stats['expiring_soon'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Total</p>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-4">
        <p class="text-xs font-bold text-purple-400 uppercase mb-1">Revenus abonnements</p>
        <p class="text-lg font-black text-purple-900">{{ number_format($stats['revenue'], 0, ',', ' ') }} <span class="text-xs font-semibold text-gray-400">FCFA</span></p>
    </div>
</div>

{{-- PLAN TABS --}}
<div class="flex flex-wrap gap-2 mb-5">
    <a href="{{ route('admin.care.index') }}" class="px-4 py-1.5 rounded-full text-xs font-bold border transition {{ !$plan ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-600 border-gray-300 hover:border-purple-400' }}">Tous les plans</a>
    <a href="{{ route('admin.care.index', ['plan' => 'standard']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold border transition {{ $plan==='standard' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400' }}">🛡️ Standard</a>
    <a href="{{ route('admin.care.index', ['plan' => 'premium']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold border transition {{ $plan==='premium' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-600 border-gray-300 hover:border-purple-400' }}">⭐ Premium</a>
    <a href="{{ route('admin.care.index', ['plan' => 'enterprise']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold border transition {{ $plan==='enterprise' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-gray-600 border-gray-300 hover:border-amber-400' }}">🏆 Entreprise</a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <form action="{{ route('admin.care.index') }}" method="GET" class="flex gap-2">
            <input type="hidden" name="plan" value="{{ $plan }}"><input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="N° abonnement, client, produit..."
                   class="border-gray-300 rounded-md shadow-sm text-sm flex-1 max-w-sm focus:ring-purple-500 focus:border-purple-500">
            <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm">
                <option value="">Tous statuts</option>
                @foreach(['active'=>'Actif','expired'=>'Expiré','cancelled'=>'Annulé','suspended'=>'Suspendu'] as $s => $l)
                <option value="{{ $s }}" {{ $status===$s?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-purple-700 transition">Filtrer</button>
        </form>
    </div>

    @if($subscriptions->isEmpty())
    <div class="p-12 text-center">
        <div class="text-5xl mb-3">🛡️</div>
        <p class="text-gray-500 font-medium">Aucun abonnement CARE+ trouvé</p>
        <a href="{{ route('admin.care.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md font-bold text-sm hover:bg-purple-700 transition">Créer un abonnement</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client / Produit</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avantages</th>
                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($subscriptions as $sub)
                @php
                    $planCfg   = \App\Models\CareSubscription::planConfig($sub->plan);
                    $statusCfg = \App\Models\CareSubscription::statusConfig($sub->status);
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.care.show', $sub->id) }}" class="text-sm font-bold text-navy-600 hover:underline">{{ $sub->number }}</a>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $sub->client_name }}</div>
                        <div class="text-xs text-gray-500">{{ $sub->product_name }}</div>
                        @if($sub->serial_number)<div class="text-xs text-gray-400 font-mono">S/N: {{ $sub->serial_number }}</div>@endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $planCfg['classes'] }}">{{ $planCfg['icon'] }} {{ $planCfg['label'] }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex flex-wrap gap-1">
                            @if($sub->has_priority_support) <span class="text-xs bg-green-50 text-green-700 px-1.5 py-0.5 rounded">Prioritaire</span> @endif
                            @if($sub->has_repair_discount) <span class="text-xs bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded">-{{ $sub->repair_discount_pct }}% répa.</span> @endif
                            @if($sub->has_home_service) <span class="text-xs bg-purple-50 text-purple-700 px-1.5 py-0.5 rounded">À domicile</span> @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 text-right font-bold text-gray-900 text-sm">{{ number_format($sub->price, 0, ',', ' ') }} FCFA</td>
                    <td class="px-5 py-4 text-sm {{ $sub->days_remaining <= 30 && $sub->status === 'active' ? 'text-amber-600 font-bold' : 'text-gray-500' }}">
                        {{ $sub->end_date->format('d/m/Y') }}
                        @if($sub->status === 'active' && $sub->days_remaining <= 30)
                            <div class="text-[10px] text-amber-500">⚠️ {{ $sub->days_remaining }}j</div>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusCfg['classes'] }}">{{ $statusCfg['label'] }}</span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.care.show', $sub->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Voir</a>
                            <a href="{{ route('admin.care.edit', $sub->id) }}" class="text-xs font-bold text-gold-600 bg-gold-50 hover:bg-gold-100 px-2.5 py-1 rounded transition">Modifier</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $subscriptions->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
