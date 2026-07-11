@extends('layouts.admin')
@section('title', $contract->number)
@section('content')

@php $cfg = \App\Models\MaintenanceContract::statusConfig($contract->status); @endphp

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.contracts.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $contract->number }}</h1>
                <span class="px-3 py-0.5 rounded-full text-sm font-bold border {{ $cfg['classes'] }}">{{ $cfg['label'] }}</span>
            </div>
            <p class="text-sm text-gray-500">{{ \App\Models\MaintenanceContract::typeLabel($contract->type) }} — {{ $contract->client_name }}</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="inline-flex items-center px-4 py-2 bg-gold-500 text-navy-900 font-bold rounded-md text-sm hover:bg-gold-600 transition shadow gap-2">Modifier</a>
        <form action="{{ route('admin.contracts.destroy', $contract->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce contrat ?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-md text-sm hover:bg-red-700 transition shadow">Supprimer</button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

{{-- EXPIRY ALERT --}}
@if($contract->status === 'active' && $contract->days_remaining <= 30)
<div class="mb-5 bg-amber-50 border-l-4 border-amber-500 p-4 rounded text-amber-800 text-sm font-medium">
    ⚠️ Ce contrat expire dans <strong>{{ $contract->days_remaining }} jour(s)</strong> — le {{ $contract->end_date->format('d/m/Y') }}.
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- CLIENT & DATES --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Client & Période</h2>
            <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                <div><span class="text-gray-400 block text-xs uppercase font-bold">Client</span><span class="font-bold text-gray-900">{{ $contract->client_name }}</span></div>
                @if($contract->client_phone)<div><span class="text-gray-400 block text-xs uppercase font-bold">Téléphone</span><span>{{ $contract->client_phone }}</span></div>@endif
                @if($contract->client_address)<div class="col-span-2"><span class="text-gray-400 block text-xs uppercase font-bold">Adresse</span><span>{{ $contract->client_address }}</span></div>@endif
                <div><span class="text-gray-400 block text-xs uppercase font-bold">Début</span><span class="font-bold">{{ $contract->start_date->format('d/m/Y') }}</span></div>
                <div><span class="text-gray-400 block text-xs uppercase font-bold">Fin</span><span class="font-bold {{ $contract->days_remaining <= 30 ? 'text-amber-600' : '' }}">{{ $contract->end_date->format('d/m/Y') }}</span></div>
            </div>
        </div>

        {{-- INTERVENTIONS GAUGE --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Suivi des Interventions</h2>
            @php
                $pct = $contract->interventions_included > 0 ? min(100, ($contract->interventions_used / $contract->interventions_included) * 100) : 0;
                $barColor = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-green-500');
            @endphp
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700">{{ $contract->interventions_used }} / {{ $contract->interventions_included }} utilisées</span>
                <span class="text-sm font-bold {{ $contract->interventions_remaining === 0 ? 'text-red-600' : 'text-green-700' }}">{{ $contract->interventions_remaining }} restantes</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="{{ $barColor }} h-3 rounded-full transition-all" style="width: {{ $pct }}%"></div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                SLA : réponse garantie en <strong>{{ $contract->response_time_hours }}h</strong>
            </div>
        </div>

        {{-- SCOPE --}}
        @if($contract->scope)
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Périmètre / Scope</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $contract->scope }}</p>
        </div>
        @endif
    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-5">
        {{-- Financial --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-3 text-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Facturation</h3>
            <div class="flex justify-between"><span class="text-gray-500">Type</span><span class="font-bold text-blue-700">{{ \App\Models\MaintenanceContract::typeLabel($contract->type) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Périodicité</span><span class="font-semibold capitalize">{{ ['monthly'=>'Mensuelle','quarterly'=>'Trimestrielle','annual'=>'Annuelle'][$contract->billing_period] ?? $contract->billing_period }}</span></div>
            <div class="flex justify-between border-t pt-3"><span class="text-gray-500">Montant total</span><span class="font-black text-gray-900">{{ number_format($contract->price, 0, ',', ' ') }} FCFA</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Montant payé</span><span class="font-bold text-green-700">{{ number_format($contract->amount_paid, 0, ',', ' ') }} FCFA</span></div>
            @if($contract->amount_remaining > 0)
            <div class="flex justify-between"><span class="text-gray-500">Reste à payer</span><span class="font-bold text-red-600">{{ number_format($contract->amount_remaining, 0, ',', ' ') }} FCFA</span></div>
            @endif
            @php $pstatus = match($contract->payment_status){ 'paid'=>['label'=>'Payé','class'=>'bg-green-50 text-green-700'], 'partial'=>['label'=>'Partiel','class'=>'bg-amber-50 text-amber-700'], default=>['label'=>'En attente','class'=>'bg-red-50 text-red-700'] }; @endphp
            <div class="text-center mt-2 py-1.5 rounded-md {{ $pstatus['class'] }} text-xs font-bold">{{ $pstatus['label'] }}</div>
        </div>

        {{-- Days remaining --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-xs font-bold text-gray-400 uppercase mb-2">Jours restants</p>
            <p class="text-3xl font-black {{ $contract->days_remaining <= 30 ? 'text-amber-600' : 'text-green-700' }}">{{ $contract->days_remaining }}</p>
            <p class="text-xs text-gray-400 mt-1">jusqu'au {{ $contract->end_date->format('d/m/Y') }}</p>
        </div>

        @if($contract->notes)
        <div class="bg-amber-50 rounded-lg border border-amber-100 p-4">
            <h3 class="text-xs font-bold text-amber-600 uppercase mb-2">Notes</h3>
            <p class="text-sm text-gray-700">{{ $contract->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
