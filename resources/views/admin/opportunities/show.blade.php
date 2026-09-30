@extends('layouts.admin')
@section('title', 'Opportunité ' . $opportunity->opportunity_ref)
@section('content')

@php
    $root = $opportunity->duplicate_of_id ? $opportunity->duplicateOf : $opportunity;
    $group = collect([$root])->merge($root->duplicates)->unique('id');
@endphp

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.opportunities.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 font-mono">{{ $opportunity->opportunity_ref }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $opportunity->name }} — déclarée par {{ $opportunity->partner?->name ?? '—' }}</p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Prospect details --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide mb-4">Détail de l'opportunité</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-xs text-gray-400 block">Société</span><span class="font-bold text-gray-900">{{ $opportunity->company ?: '—' }}</span></div>
                <div><span class="text-xs text-gray-400 block">Téléphone</span><span class="font-bold text-gray-900">{{ $opportunity->phone ?: '—' }}</span></div>
                <div><span class="text-xs text-gray-400 block">Email</span><span class="font-bold text-gray-900">{{ $opportunity->email ?: '—' }}</span></div>
                <div><span class="text-xs text-gray-400 block">Budget estimé</span><span class="font-bold text-gray-900">{{ $opportunity->budget ? number_format($opportunity->budget, 0, ',', ' ') . ' FCFA' : '—' }}</span></div>
                <div class="col-span-2"><span class="text-xs text-gray-400 block">Besoin</span><span class="text-gray-800">{{ $opportunity->need ?: '—' }}</span></div>
                @if($opportunity->notes)
                <div class="col-span-2"><span class="text-xs text-gray-400 block">Notes</span><span class="text-gray-800">{{ $opportunity->notes }}</span></div>
                @endif
            </div>
        </div>

        {{-- Duplicate group / arbitration --}}
        @if($group->count() > 1)
        <div class="bg-white rounded-lg shadow-sm border border-red-200 p-6">
            <h2 class="text-sm font-black text-red-700 uppercase tracking-wide mb-1">🚩 Conflit d'attribution</h2>
            <p class="text-xs text-gray-500 mb-4">Plusieurs apporteurs ont déclaré ce même prospect (téléphone, email ou société identiques). Choisissez à qui attribuer l'opportunité.</p>

            <div class="space-y-2 mb-4">
                @foreach($group as $entry)
                <div class="flex items-center justify-between border border-gray-100 rounded-md p-3 {{ $entry->id === $opportunity->id ? 'bg-navy-50/40 border-navy-200' : '' }}">
                    <div>
                        <span class="text-xs font-mono font-bold text-navy-900">{{ $entry->opportunity_ref }}</span>
                        <span class="text-sm text-gray-700 ml-2">{{ $entry->partner?->name ?? '—' }}</span>
                        <span class="text-xs text-gray-400 ml-2">déclarée le {{ $entry->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    @if($entry->duplicate_status === 'flagged')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">En attente</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Arbitrée</span>
                    @endif
                </div>
                @endforeach
            </div>

            @if($group->contains('duplicate_status', 'flagged'))
            <form action="{{ route('admin.opportunities.arbitrate', $opportunity->id) }}" method="POST" class="border-t border-gray-100 pt-4 space-y-3">
                @csrf
                <div>
                    <label class="admin-label">Attribuer l'opportunité à</label>
                    <select name="winner_partner_id" required class="admin-select">
                        @foreach($group->unique('partner_id') as $entry)
                            <option value="{{ $entry->partner_id }}" {{ $entry->id === $root->id ? 'selected' : '' }}>
                                {{ $entry->partner?->name ?? '—' }} ({{ $entry->opportunity_ref }}{{ $entry->id === $root->id ? ' — 1ère déclaration' : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">Note d'arbitrage (optionnel)</label>
                    <textarea name="note" rows="2" class="admin-input" placeholder="Justification de la décision..."></textarea>
                </div>
                <button type="submit" class="bg-red-600 text-white font-bold px-5 py-2 rounded-md hover:bg-red-700 transition text-sm">Trancher le conflit</button>
            </form>
            @endif
        </div>
        @endif

        {{-- Arbitration history --}}
        @if(!empty($opportunity->arbitration_history))
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide mb-4">Historique d'arbitrage</h2>
            <ul class="space-y-2 text-xs text-gray-600">
                @foreach($opportunity->arbitration_history as $h)
                <li class="border-l-2 {{ $h['decision'] === 'attribuée' ? 'border-green-400' : 'border-gray-300' }} pl-3">
                    <span class="font-bold {{ $h['decision'] === 'attribuée' ? 'text-green-700' : 'text-gray-500' }}">{{ ucfirst($h['decision']) }}</span>
                    — {{ $h['at'] }}
                    @if(!empty($h['note']))<div class="italic text-gray-500 mt-0.5">« {{ $h['note'] }} »</div>@endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        {{-- Status & commission --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide mb-4">Suivi & commission apporteur</h2>
            <form action="{{ route('admin.opportunities.update', $opportunity->id) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="admin-label">Statut</label>
                    <select name="status" class="admin-select">
                        @foreach(['new'=>'Nouveau','contacted'=>'Contacté','interested'=>'Intéressé','proposal_sent'=>'Devis envoyé','negotiating'=>'En négociation','won'=>'Gagné','lost'=>'Perdu'] as $key => $label)
                            <option value="{{ $key }}" {{ $opportunity->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">Montant final du contrat (FCFA)</label>
                    <input type="number" step="0.01" name="contract_amount" value="{{ old('contract_amount', $opportunity->contract_amount) }}" class="admin-input" placeholder="Rempli quand l'affaire est conclue">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="admin-label">Type de commission</label>
                        <select name="commission_type" class="admin-select">
                            <option value="">—</option>
                            <option value="percent" {{ $opportunity->commission_type === 'percent' ? 'selected' : '' }}>% du contrat</option>
                            <option value="fixed" {{ $opportunity->commission_type === 'fixed' ? 'selected' : '' }}>Montant fixe</option>
                        </select>
                    </div>
                    <div>
                        <label class="admin-label">Valeur</label>
                        <input type="number" step="0.01" name="commission_value" value="{{ old('commission_value', $opportunity->commission_value) }}" class="admin-input" placeholder="ex: 2">
                    </div>
                </div>
                <p class="text-[11px] text-gray-400">Barème saisi au cas par cas selon le type d'affaire — aucun taux n'est figé dans le code (doc §33-36).</p>

                @if($opportunity->commission_amount)
                <div class="bg-gold-50 border border-gold-200 rounded-md p-3 text-sm">
                    <span class="text-xs text-gold-700 font-bold uppercase block">Commission calculée</span>
                    <span class="text-lg font-black text-gold-800">{{ number_format($opportunity->commission_amount, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif

                <button type="submit" class="w-full bg-navy-600 text-white font-bold px-5 py-2.5 rounded-md hover:bg-navy-700 transition text-sm">Enregistrer</button>
            </form>
        </div>
    </div>
</div>
@endsection
