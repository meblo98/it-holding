@extends('layouts.admin')
@section('title', $ticket->number . ' — ' . $ticket->title)
@section('content')

@php $statusCfg = \App\Models\Ticket::statusConfig($ticket->status); @endphp

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.tickets.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->number }}</h1>
                <span class="px-3 py-0.5 rounded-full text-sm font-bold border {{ $statusCfg['classes'] }}">{{ $statusCfg['label'] }}</span>
                @php $pCfg = \App\Models\Ticket::priorityConfig($ticket->priority); @endphp
                <span class="text-sm font-bold {{ $pCfg['classes'] }}">{{ $pCfg['label'] }}</span>
            </div>
            <p class="text-sm text-gray-600 mt-0.5">{{ $ticket->title }}</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="inline-flex items-center px-4 py-2 bg-gold-500 text-navy-900 font-bold rounded-md text-sm hover:bg-gold-600 transition shadow gap-2">Modifier</a>
        <form action="{{ route('admin.tickets.destroy', $ticket->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce ticket ?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-md text-sm hover:bg-red-700 transition shadow">Supprimer</button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

{{-- STATUS STEPPER --}}
@php
    $steps = ['open' => 'Ouvert', 'diagnosed' => 'Diagnostiqué', 'in_progress' => 'En cours', 'resolved' => 'Résolu', 'closed' => 'Clôturé'];
    $statusOrder = array_keys($steps);
    $currentIdx  = array_search($ticket->status, $statusOrder) ?? 0;
@endphp
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 mb-6">
    <div class="flex items-center justify-between">
        @foreach($steps as $s => $label)
        @php $idx = array_search($s, $statusOrder); $done = $idx <= $currentIdx; @endphp
        <div class="flex-1 flex flex-col items-center relative">
            @if(!$loop->first)
            <div class="absolute top-3.5 -left-1/2 right-1/2 h-0.5 {{ $done ? 'bg-green-400' : 'bg-gray-200' }}"></div>
            @endif
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black z-10 relative {{ $done ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                @if($done && $idx < $currentIdx)✓@else{{ $idx + 1 }}@endif
            </div>
            <span class="text-xs text-center mt-1.5 {{ $done ? 'text-green-700 font-bold' : 'text-gray-400' }}">{{ $label }}</span>
        </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- LEFT --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- DESCRIPTION --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Description du problème</h2>
            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $ticket->description }}</p>
        </div>

        {{-- PHOTOS --}}
        @if($ticket->attachments->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Pièces jointes / Photos</h2>
            <div class="grid grid-cols-3 md:grid-cols-4 gap-3">
                @foreach($ticket->attachments as $att)
                @if($att->type === 'image')
                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="group relative rounded-lg overflow-hidden border border-gray-200 hover:border-navy-400 transition">
                    <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->file_name }}" class="w-full h-24 object-cover group-hover:scale-105 transition duration-200">
                </a>
                @else
                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:border-navy-400 text-xs text-navy-600 transition">
                    📄 {{ $att->file_name }}
                </a>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- TECHNICIAN REPORT --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rapport Technicien</h2>

            @if($ticket->diagnosis)
            <div>
                <p class="text-xs font-bold text-purple-600 uppercase mb-1">🔍 Diagnostic</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $ticket->diagnosis }}</p>
            </div>
            @endif

            @if($ticket->intervention_notes)
            <div>
                <p class="text-xs font-bold text-blue-600 uppercase mb-1">🔧 Intervention</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $ticket->intervention_notes }}</p>
            </div>
            @endif

            @if($ticket->parts_used)
            <div>
                <p class="text-xs font-bold text-amber-600 uppercase mb-1">📦 Pièces utilisées</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $ticket->parts_used }}</p>
            </div>
            @endif

            @if(!$ticket->diagnosis && !$ticket->intervention_notes)
            <p class="text-sm text-gray-400 italic">Aucun rapport technicien renseigné.</p>
            @endif
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="space-y-5">
        {{-- CLIENT --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Client</h3>
            <div class="space-y-2 text-sm">
                @if($ticket->client)
                    <a href="{{ route('admin.clients.show', $ticket->client->id) }}" class="font-bold text-navy-700 hover:underline text-base">{{ $ticket->client_name }}</a>
                @else
                    <p class="font-bold text-gray-900">{{ $ticket->client_name }}</p>
                @endif
                @if($ticket->client_phone) <p class="text-gray-600">{{ $ticket->client_phone }}</p> @endif
                @if($ticket->client_email) <p class="text-gray-500 text-xs">{{ $ticket->client_email }}</p> @endif
            </div>
        </div>

        {{-- PRODUCT --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Produit</h3>
            <div class="space-y-2 text-sm">
                @if($ticket->product_name)
                    <p class="font-bold text-gray-900">{{ $ticket->product_name }}</p>
                    @if($ticket->serial_number) <p class="text-xs font-mono text-gray-500">S/N: {{ $ticket->serial_number }}</p> @endif
                @else
                    <p class="text-gray-400 italic">Non renseigné</p>
                @endif
                @if($ticket->warranty)
                <div class="mt-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.warranties.show', $ticket->warranty->id) }}" class="text-xs font-bold text-green-600 hover:underline">🛡️ Garantie {{ $ticket->warranty->number }}</a>
                    <p class="text-xs text-gray-400">Expire le {{ $ticket->warranty->expiry_date->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- DETAILS --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-3 text-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informations</h3>
            <div class="flex justify-between"><span class="text-gray-500">Type</span><span class="font-semibold capitalize">{{ str_replace('_', ' ', $ticket->type) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Technicien</span><span class="font-semibold">{{ $ticket->technician?->name ?? '—' }}</span></div>
            @if($ticket->scheduled_date)
            <div class="flex justify-between"><span class="text-gray-500">Date prévue</span><span class="font-semibold text-blue-600">📅 {{ $ticket->scheduled_date->format('d/m/Y') }}</span></div>
            @endif
            @if($ticket->repair_cost !== null)
            <div class="flex justify-between border-t pt-3"><span class="text-gray-500">Coût réparation</span><span class="font-black text-gray-900">{{ number_format($ticket->repair_cost, 0, ',', ' ') }} FCFA</span></div>
            @endif
            @if($ticket->covered_by_warranty)
            <div class="bg-green-50 rounded p-2 text-xs font-bold text-green-700 text-center">✅ Couvert par garantie</div>
            @endif
        </div>

        {{-- TIMELINE --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-2 text-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Historique</h3>
            @if($ticket->opened_at) <div class="flex justify-between"><span class="text-gray-500">Ouvert</span><span class="text-gray-700">{{ $ticket->opened_at->format('d/m/Y H:i') }}</span></div> @endif
            @if($ticket->resolved_at) <div class="flex justify-between"><span class="text-gray-500">Résolu</span><span class="text-green-700 font-semibold">{{ $ticket->resolved_at->format('d/m/Y H:i') }}</span></div> @endif
            @if($ticket->closed_at) <div class="flex justify-between"><span class="text-gray-500">Clôturé</span><span class="text-gray-600">{{ $ticket->closed_at->format('d/m/Y H:i') }}</span></div> @endif
        </div>
    </div>
</div>
@endsection
