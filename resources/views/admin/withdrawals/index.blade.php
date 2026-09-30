@extends('layouts.admin')
@section('title', 'Retraits des professionnels')
@section('content')
@php $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA'; @endphp

<div class="mb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Retraits des professionnels</h1>
        <p class="text-sm text-gray-500 mt-0.5">Demandes de versement depuis les portefeuilles pro. Le montant est réservé dès la demande ; un refus le recrédite automatiquement.</p>
    </div>
    <form action="{{ route('admin.withdrawals.export') }}" method="GET" class="flex items-end gap-2 bg-white border border-gray-100 rounded-lg p-3 shadow-sm">
        <div>
            <label class="admin-label">Du</label>
            <input type="date" name="from" required value="{{ now()->startOfMonth()->toDateString() }}" class="admin-input">
        </div>
        <div>
            <label class="admin-label">Au</label>
            <input type="date" name="to" required value="{{ now()->toDateString() }}" class="admin-input">
        </div>
        <button type="submit" class="bg-navy-600 text-white font-bold px-4 py-2 rounded-md hover:bg-navy-700 transition text-sm whitespace-nowrap">Export comptable (CSV)</button>
    </form>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="flex gap-1 border-b border-gray-200 mb-5 overflow-x-auto">
    @foreach(['requested' => 'À traiter', 'approved' => 'Approuvés, à verser', 'paid' => 'Versés', 'rejected' => 'Refusés', 'all' => 'Tous'] as $key => $label)
        <a href="{{ route('admin.withdrawals.index', ['status' => $key]) }}" class="px-4 py-2.5 text-sm font-semibold border-b-2 whitespace-nowrap -mb-px {{ $status === $key ? 'border-navy-600 text-navy-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ $label }}
            @if($key !== 'all' && isset($counts[$key]))
                <span class="ml-1 text-xs text-gray-400">{{ $counts[$key]->n }} · {{ $fcfa($counts[$key]->total) }}</span>
            @endif
        </a>
    @endforeach
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    @if($withdrawals->isEmpty())
        <div class="p-10 text-center text-gray-400 text-sm">Aucune demande.</div>
    @else
    <div class="divide-y divide-gray-100">
        @foreach($withdrawals as $w)
        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4" x-data="{ mode: null }">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-sm font-bold text-gray-900">{{ $w->reference }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $w->status_classes }}">{{ $w->status_label }}</span>
                    <span class="text-lg font-black text-gray-900">{{ $fcfa($w->amount) }}</span>
                </div>
                <div class="text-sm text-gray-600 mt-1">
                    <a href="{{ route('admin.professionals.show', $w->user_id) }}" class="font-bold hover:text-gold-700">{{ $w->user->name }}</a>
                    @if($w->user->professionalProfile?->pro_id)<span class="font-mono text-xs text-gray-400">{{ $w->user->professionalProfile->pro_id }}</span>@endif
                </div>
                <div class="text-xs text-gray-500 mt-0.5">{{ $w->method_label }} → <span class="font-mono">{{ $w->account_details }}</span> · demandé le {{ $w->created_at->format('d/m/Y H:i') }}</div>
                @if($w->processed_at)
                    <div class="text-xs text-gray-400 mt-0.5">Traité le {{ $w->processed_at->format('d/m/Y') }} par {{ $w->processor?->name ?? '—' }}@if($w->payment_reference) · réf. {{ $w->payment_reference }}@endif @if($w->admin_note) · « {{ $w->admin_note }} »@endif</div>
                @endif
            </div>

            @if(in_array($w->status, ['requested', 'approved']))
            <div class="shrink-0 space-y-2 lg:text-right">
                <div class="flex gap-2 lg:justify-end">
                    @if($w->status === 'requested')
                        <form action="{{ route('admin.withdrawals.approve', $w->id) }}" method="POST">@csrf
                            <button type="submit" class="text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded">Approuver</button>
                        </form>
                    @endif
                    <button type="button" @click="mode = mode === 'paid' ? null : 'paid'" class="text-xs font-bold text-green-700 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded">Marquer versé</button>
                    <button type="button" @click="mode = mode === 'reject' ? null : 'reject'" class="text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded">Refuser</button>
                </div>
                <form x-show="mode === 'paid'" x-cloak action="{{ route('admin.withdrawals.paid', $w->id) }}" method="POST" class="flex gap-2">@csrf
                    <input type="text" name="payment_reference" required class="admin-input" placeholder="Réf. transaction {{ $w->method_label }}">
                    <button type="submit" class="bg-green-600 text-white text-xs font-bold px-3 rounded">Confirmer</button>
                </form>
                <form x-show="mode === 'reject'" x-cloak action="{{ route('admin.withdrawals.reject', $w->id) }}" method="POST" class="flex gap-2">@csrf
                    <input type="text" name="admin_note" required class="admin-input" placeholder="Motif (visible par le professionnel)">
                    <button type="submit" class="bg-red-600 text-white text-xs font-bold px-3 rounded">Refuser</button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    <div class="p-4 border-t border-gray-100">{{ $withdrawals->links() }}</div>
    @endif
</div>
@endsection
