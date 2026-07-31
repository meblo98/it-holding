@extends('layouts.app')

@section('title', 'Support & SAV - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('home') }}" class="hover:text-navy-900 flex items-center gap-1">
                    <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('dashboard') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold uppercase tracking-wider italic">Support / SAV</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            @include('layouts.client_sidebar')

            <!-- Main Content -->
            <main class="flex-1">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-lg flex items-center gap-3 text-green-600 text-xs font-bold uppercase tracking-widest italic">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50/30">
                        <div>
                            <h3 class="text-sm font-black text-navy-900 uppercase tracking-tighter italic">Support Technique & SAV</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Gérez vos demandes d'assistance, réparations et garanties</p>
                        </div>
                        <a href="{{ route('dashboard.tickets.create') }}" class="inline-flex items-center gap-2 bg-navy-900 hover:bg-gold-500 text-white hover:text-navy-900 px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Ouvrir un ticket
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Ticket / Objet</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Type</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Priorité</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Technicien</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Statut</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Créé le</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($tickets as $ticket)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="text-[11px] font-black text-navy-900 italic mb-0.5">
                                            {{ $ticket->number }}
                                        </div>
                                        <div class="text-[11px] font-bold text-gray-500 line-clamp-1 max-w-[200px]">
                                            {{ $ticket->title }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-[11px] font-bold text-navy-900 italic">
                                        @switch($ticket->type)
                                            @case('repair')
                                                Réparation
                                                @break
                                            @case('installation')
                                                Installation
                                                @break
                                            @case('maintenance')
                                                Maintenance
                                                @break
                                            @case('advice')
                                                Conseil / Info
                                                @break
                                            @case('warranty_claim')
                                                Réclamation Garantie
                                                @break
                                            @default
                                                {{ $ticket->type }}
                                        @endswitch
                                    </td>
                                    <td class="px-8 py-5 text-[11px] font-bold italic">
                                        @php $pCfg = \App\Models\Ticket::priorityConfig($ticket->priority); @endphp
                                        <span class="{{ $pCfg['classes'] }}">
                                            {{ $pCfg['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-[11px] font-bold text-gray-500">
                                        {{ $ticket->technician?->name ?? 'Non assigné' }}
                                    </td>
                                    <td class="px-8 py-5">
                                        @php $statusCfg = \App\Models\Ticket::statusConfig($ticket->status); @endphp
                                        <span class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider border {{ $statusCfg['classes'] }} italic">
                                            {{ $statusCfg['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-[11px] font-bold text-gray-400">
                                        {{ $ticket->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <a href="{{ route('dashboard.tickets.show', $ticket->id) }}" class="inline-flex items-center gap-1 bg-navy-900 hover:bg-gold-500 text-white hover:text-navy-900 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all">
                                            Détails
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            </div>
                                            <p class="text-xs text-gray-400 font-bold italic">Vous n'avez ouvert aucune demande d'assistance technique ou SAV.</p>
                                            <a href="{{ route('dashboard.tickets.create') }}" class="inline-flex items-center gap-2 bg-navy-900 hover:bg-gold-500 text-white hover:text-navy-900 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all">
                                                Ouvrir votre premier ticket
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($tickets->hasPages())
                        <div class="px-8 py-5 border-t border-gray-50 bg-gray-50/20">
                            {{ $tickets->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
