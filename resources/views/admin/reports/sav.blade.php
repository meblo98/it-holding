@extends('layouts.admin')

@section('title', 'Rapport Garanties & SAV')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-gold-600 uppercase tracking-widest hover:underline flex items-center gap-1 mb-2">
        ← Retour au Centre de Rapports
    </a>
    <h1 class="text-3xl font-black text-navy-900 tracking-tight uppercase">Garanties & Support SAV</h1>
    <p class="text-sm text-gray-500 mt-1">Suivi de la satisfaction clients, états de pannes, garanties actives et résolution de tickets.</p>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Garanties Actives</span>
        <h3 class="text-2xl font-black text-green-600">{{ number_format($activeWarranties) }}</h3>
        <p class="text-xs text-gray-500 mt-1">Équipements couverts actuellement.</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Garanties Expirées</span>
        <h3 class="text-2xl font-black text-gray-400">{{ number_format($expiredWarranties) }}</h3>
        <p class="text-xs text-gray-500 mt-1">Contrats de garantie échus.</p>
    </div>

    <div class="bg-navy-900 text-white rounded-xl p-6 shadow-sm">
        <span class="text-xs font-bold text-gold-400 uppercase tracking-widest block mb-2">Tickets Ouverts / En Cours</span>
        <h3 class="text-2xl font-black text-gold-500">{{ number_format($openTickets) }}</h3>
        <p class="text-xs text-gray-400 mt-1">En attente de résolution.</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Tickets Résolus</span>
        <h3 class="text-2xl font-black text-navy-900">{{ number_format($resolvedTickets) }}</h3>
        <p class="text-xs text-gray-500 mt-1">Dossiers clôturés avec succès.</p>
    </div>
</div>

<!-- Recent Tickets Table -->
<h2 class="text-lg font-bold text-navy-900 mb-4 uppercase tracking-tight">Tickets d'Assistance Récents</h2>
<div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ticket</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sujet</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Priorité</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Date de Création</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-navy-600">
                        #{{ $ticket->code ?: $ticket->id }}
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-950 font-medium">
                        {{ $ticket->subject }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $ticket->client->first_name ?? '' }} {{ $ticket->client->last_name ?? ($ticket->client->company_name ?? 'Client Externe') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($ticket->priority == 'high' || $ticket->priority == 'critical') bg-red-100 text-red-800 
                            @elseif($ticket->priority == 'medium') bg-orange-100 text-orange-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($ticket->status == 'resolved' || $ticket->status == 'closed') bg-green-100 text-green-800 
                            @elseif($ticket->status == 'pending') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">Aucun ticket d'assistance enregistré.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
