@extends('layouts.admin')
@section('title', $contract->title)
@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.network-contracts.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $contract->title }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ \App\Models\User::PARTNER_TYPES[$contract->type] ?? ($contract->type === 'all' ? 'Toutes catégories' : $contract->type) }} — v{{ $contract->version }} — {{ \App\Models\Contract::STATUSES[$contract->status] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide mb-4">Contenu</h2>
            <div class="text-sm text-gray-700 whitespace-pre-line font-mono leading-relaxed max-h-[600px] overflow-y-auto border border-gray-100 rounded p-4 bg-gray-50/50">{{ $contract->content }}</div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-700 uppercase tracking-wide mb-4">Preuves d'acceptation ({{ $contract->acceptances->count() }})</h2>
            @if($contract->acceptances->isEmpty())
                <p class="text-xs text-gray-400 italic">Aucune acceptation pour le moment.</p>
            @else
                <ul class="space-y-3 text-xs">
                    @foreach($contract->acceptances as $acc)
                    <li class="border-b border-gray-50 pb-3">
                        <div class="font-bold text-gray-900">{{ $acc->user?->name ?? '—' }}</div>
                        <div class="text-gray-500">{{ $acc->accepted_at->format('d/m/Y à H:i') }}</div>
                        <div class="text-gray-400 font-mono">{{ $acc->ip_address }}</div>
                        <div class="text-gray-300 font-mono truncate" title="{{ $acc->document_hash }}">hash: {{ Str::limit($acc->document_hash, 16, '…') }}</div>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
