@extends('layouts.admin')
@section('title', 'Pipeline commercial')
@section('content')
@php $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA'; @endphp

<div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pipeline commercial</h1>
        <p class="text-sm text-gray-500 mt-0.5">Toutes les affaires d'IT Holding, du premier contact à la fidélisation.</p>
    </div>
    <a href="{{ route('admin.crm.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle affaire
    </a>
</div>

@include('admin.crm._nav', ['active' => 'pipeline'])

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Pipeline en cours</p>
        <p class="text-xl font-black text-navy-900">{{ $fcfa($stats['pipeline']) }}</p>
        <p class="text-xs text-gray-400">{{ $stats['open'] }} affaire(s) ouverte(s)</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-green-100 p-4">
        <p class="text-xs font-bold text-green-500 uppercase mb-1">Gagné ce mois</p>
        <p class="text-xl font-black text-green-700">{{ $fcfa($stats['won_month']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border {{ $stats['late'] ? 'border-red-200' : 'border-gray-100' }} p-4 col-span-2">
        <p class="text-xs font-bold {{ $stats['late'] ? 'text-red-500' : 'text-gray-400' }} uppercase mb-1">Relances en retard</p>
        <p class="text-xl font-black {{ $stats['late'] ? 'text-red-700' : 'text-gray-700' }}">{{ $stats['late'] }}</p>
    </div>
</div>

@if($followUps->isNotEmpty())
<div class="bg-white rounded-lg shadow-sm border border-amber-200 mb-6">
    <div class="px-5 py-3 border-b border-amber-100 text-xs font-black text-amber-700 uppercase tracking-wide">Relances du jour et en retard</div>
    <ul class="divide-y divide-gray-50">
        @foreach($followUps as $f)
        <li class="px-5 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-sm">
            <a href="{{ route('admin.crm.show', $f->id) }}" class="font-bold text-gray-900 hover:text-gold-700">{{ $f->title }} <span class="font-normal text-gray-500">— {{ $f->display_name }}</span></a>
            <span class="text-xs {{ $f->isFollowUpLate() ? 'text-red-600 font-bold' : 'text-amber-700' }}">
                {{ $f->next_action_at->format('d/m H:i') }} · {{ $f->next_action_note ?: 'Relance' }} · {{ $f->owner?->name ?? 'non attribuée' }}
            </span>
        </li>
        @endforeach
    </ul>
</div>
@endif

<form method="GET" action="{{ route('admin.crm.index') }}" class="flex flex-wrap gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher une affaire, un contact, une société…" class="admin-input max-w-sm">
    <select name="owner" class="admin-select max-w-xs" onchange="this.form.submit()">
        <option value="all" {{ $ownerFilter === 'all' ? 'selected' : '' }}>Tous les commerciaux</option>
        <option value="mine" {{ $ownerFilter === 'mine' ? 'selected' : '' }}>Mes affaires</option>
        @foreach($owners as $o)
            <option value="{{ $o->id }}" {{ (string) $ownerFilter === (string) $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-navy-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-navy-700">Filtrer</button>
</form>

{{-- Pipeline (doc §43) --}}
<div class="flex gap-4 overflow-x-auto pb-4">
    @foreach(\App\Models\CrmDeal::STAGES as $stage => $label)
        @php $column = $deals->get($stage, collect()); @endphp
        <div class="w-72 shrink-0 bg-gray-50 rounded-lg border border-gray-200 flex flex-col max-h-[70vh]">
            <div class="px-3 py-2.5 border-b border-gray-200 flex items-center justify-between">
                <span class="text-xs font-black uppercase tracking-wide {{ $stage === 'lost' ? 'text-red-600' : 'text-gray-700' }}">{{ $label }}</span>
                <span class="text-[11px] text-gray-400">{{ $column->count() }} · {{ number_format($column->sum('amount'), 0, ',', ' ') }}</span>
            </div>
            <div class="p-2 space-y-2 overflow-y-auto">
                @forelse($column as $deal)
                    <div class="bg-white rounded-md border {{ $deal->isFollowUpLate() ? 'border-red-300' : 'border-gray-200' }} p-3 shadow-sm">
                        <a href="{{ route('admin.crm.show', $deal->id) }}" class="block text-sm font-bold text-gray-900 hover:text-gold-700 leading-tight">{{ $deal->title }}</a>
                        <div class="text-xs text-gray-500 mt-0.5 truncate">{{ $deal->display_name }}</div>
                        <div class="flex items-center justify-between mt-2 text-[11px]">
                            <span class="font-bold text-navy-900">{{ $deal->amount ? $fcfa($deal->amount) : '—' }}</span>
                            <span class="text-gray-400">{{ $deal->owner?->name ?? 'non attribuée' }}</span>
                        </div>
                        @if($deal->partner)
                            <div class="text-[10px] text-purple-600 mt-1">via {{ $deal->partner->name }}</div>
                        @endif
                        @if($deal->next_action_at && $stage !== 'lost')
                            <div class="text-[10px] mt-1 {{ $deal->isFollowUpLate() ? 'text-red-600 font-bold' : 'text-gray-400' }}">⏰ {{ $deal->next_action_at->format('d/m H:i') }}</div>
                        @endif
                        @if(!in_array($stage, ['lost', 'loyalty'], true))
                            @php $keys = array_keys(\App\Models\CrmDeal::STAGES); $next = $keys[array_search($stage, $keys) + 1] ?? null; @endphp
                            @if($next && $next !== 'lost')
                                <form action="{{ route('admin.crm.stage', $deal->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="stage" value="{{ $next }}">
                                    <button type="submit" class="w-full text-[10px] font-bold text-navy-700 bg-navy-50 hover:bg-navy-100 rounded py-1">→ {{ \App\Models\CrmDeal::STAGES[$next] }}</button>
                                </form>
                            @endif
                        @endif
                    </div>
                @empty
                    <p class="text-[11px] text-gray-400 italic text-center py-4">—</p>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection
