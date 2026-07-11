@extends('layouts.admin')
@section('title', 'SAV & Tickets')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">SAV & Tickets</h1>
        <p class="text-sm text-gray-500 mt-0.5">Suivi des demandes d'intervention et de réparation.</p>
    </div>
    <a href="{{ route('admin.tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau Ticket
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

{{-- KPI CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-4">
        <p class="text-xs font-bold text-blue-400 uppercase mb-1">Ouverts</p>
        <p class="text-2xl font-black text-blue-700">{{ $stats['open'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-amber-100 p-4">
        <p class="text-xs font-bold text-amber-400 uppercase mb-1">En cours</p>
        <p class="text-2xl font-black text-amber-700">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-green-100 p-4">
        <p class="text-xs font-bold text-green-400 uppercase mb-1">Résolus</p>
        <p class="text-2xl font-black text-green-700">{{ $stats['resolved'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Total</p>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
    </div>
</div>

{{-- FILTERS --}}
<div class="bg-white shadow-sm rounded-lg border border-gray-100 mb-5 p-4 flex flex-wrap gap-3 items-center">
    <form action="{{ route('admin.tickets.index') }}" method="GET" class="flex flex-wrap gap-2 w-full">
        <input type="text" name="search" value="{{ $search }}" placeholder="N° ticket, titre, client..."
               class="border-gray-300 rounded-md shadow-sm text-sm flex-1 min-w-[200px] focus:ring-gold-500 focus:border-gold-500">

        <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Tous statuts</option>
            @foreach(['open'=>'Ouvert','diagnosed'=>'Diagnostiqué','in_progress'=>'En cours','waiting_parts'=>'Attente pièces','resolved'=>'Résolu','closed'=>'Clôturé'] as $s => $l)
            <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>

        <select name="priority" class="border-gray-300 rounded-md shadow-sm text-sm">
            <option value="">Toutes priorités</option>
            @foreach(['urgent'=>'🔴 Urgent','high'=>'🟠 Élevé','normal'=>'🟡 Normal','low'=>'🟢 Faible'] as $p => $l)
            <option value="{{ $p }}" {{ $priority === $p ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>

        <button type="submit" class="bg-navy-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-navy-700 transition">Filtrer</button>
        <a href="{{ route('admin.tickets.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm">Réinit.</a>
    </form>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    @if($tickets->isEmpty())
    <div class="p-12 text-center">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        <p class="text-gray-500 font-medium">Aucun ticket trouvé</p>
        <a href="{{ route('admin.tickets.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition">Créer un ticket</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technicien</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Priorité</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($tickets as $ticket)
                @php
                    $statusCfg   = \App\Models\Ticket::statusConfig($ticket->status);
                    $priorityCfg = \App\Models\Ticket::priorityConfig($ticket->priority);
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="text-sm font-bold text-navy-600 hover:underline">{{ $ticket->number }}</a>
                        <div class="text-xs text-gray-500 mt-0.5 truncate max-w-[200px]">{{ $ticket->title }}</div>
                        <div class="text-xs text-gray-400">{{ ucfirst($ticket->type) }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-sm font-semibold text-gray-800">{{ $ticket->client_name }}</div>
                        @if($ticket->client_phone) <div class="text-xs text-gray-400">{{ $ticket->client_phone }}</div> @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600">
                        {{ $ticket->technician?->name ?? '—' }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="text-xs font-bold {{ $priorityCfg['classes'] }}">{{ $priorityCfg['label'] }}</span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusCfg['classes'] }}">{{ $statusCfg['label'] }}</span>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-500">
                        {{ $ticket->opened_at?->format('d/m/Y') ?? $ticket->created_at->format('d/m/Y') }}
                        @if($ticket->scheduled_date)
                            <div class="text-blue-500">📅 {{ $ticket->scheduled_date->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Voir</a>
                            <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="text-xs font-bold text-gold-600 bg-gold-50 hover:bg-gold-100 px-2.5 py-1 rounded transition">Modifier</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $tickets->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
