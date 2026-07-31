@extends('layouts.app')

@section('title', 'Détails du Ticket ' . $ticket->number . ' - ' . config('app.name'))

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
                <a href="{{ route('dashboard.tickets') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Support / SAV</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold uppercase tracking-wider italic">Détails du ticket</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            @include('layouts.client_sidebar')

            <!-- Main Content -->
            <main class="flex-1 space-y-8">
                <!-- Ticket Header Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50/30">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('dashboard.tickets') }}" class="text-gray-400 hover:text-navy-900 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            </a>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-black text-navy-900 uppercase tracking-tighter italic">{{ $ticket->number }}</h3>
                                    @php $statusCfg = \App\Models\Ticket::statusConfig($ticket->status); @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider border {{ $statusCfg['classes'] }} italic">
                                        {{ $statusCfg['label'] }}
                                    </span>
                                    @php $pCfg = \App\Models\Ticket::priorityConfig($ticket->priority); @endphp
                                    <span class="inline-flex text-[9px] font-black uppercase tracking-wider italic {{ $pCfg['classes'] }}">
                                        {{ $pCfg['label'] }}
                                    </span>
                                </div>
                                <h1 class="text-base font-black text-navy-900 uppercase tracking-tight italic mt-1">{{ $ticket->title }}</h1>
                            </div>
                        </div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider text-left md:text-right">
                            Créé le {{ $ticket->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>

                    <!-- Progress Stepper -->
                    @php
                        $steps = ['open' => 'Ouvert', 'diagnosed' => 'Diagnostiqué', 'in_progress' => 'En cours', 'resolved' => 'Résolu', 'closed' => 'Clôturé'];
                        $statusOrder = array_keys($steps);
                        $currentIdx = array_search($ticket->status, $statusOrder);
                        if ($currentIdx === false) {
                            $currentIdx = 0; // Default fallback (e.g. cancelled)
                        }
                    @endphp
                    <div class="p-8 border-b border-gray-50">
                        <div class="relative">
                            <div class="flex items-center justify-between mb-2">
                                @foreach($steps as $s => $label)
                                    @php 
                                        $idx = array_search($s, $statusOrder); 
                                        $done = $idx <= $currentIdx && $ticket->status !== 'cancelled';
                                        $active = $ticket->status === $s;
                                    @endphp
                                    <div class="flex flex-col items-center relative z-10">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors duration-500 {{ $active ? 'bg-gold-500 border-gold-500 text-navy-900' : ($done ? 'bg-navy-900 border-navy-900 text-white' : 'bg-white border-gray-100 text-gray-200') }}">
                                            @if($done && !$active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <span class="text-xs font-black">{{ $idx + 1 }}</span>
                                            @endif
                                        </div>
                                        <span class="mt-2 text-[9px] font-black uppercase tracking-widest italic text-center {{ $active ? 'text-gold-600' : ($done ? 'text-navy-900' : 'text-gray-300') }}">{{ $label }}</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Connecting Line -->
                            @if($ticket->status !== 'cancelled')
                                <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-100 -z-10 rounded-full">
                                    <div class="h-full bg-navy-900 transition-all duration-1000 ease-in-out" style="width: {{ ($currentIdx / (count($steps) - 1)) * 100 }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Details & Attachments & Report -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Problem Description -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-4">
                            <h4 class="text-xs font-black text-navy-900 uppercase tracking-widest italic border-b border-gray-50 pb-3">Description du problème</h4>
                            <p class="text-xs text-gray-600 font-medium leading-relaxed whitespace-pre-line">
                                {{ $ticket->description }}
                            </p>
                        </div>

                        <!-- Attachments -->
                        @if($ticket->attachments->isNotEmpty())
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-4">
                                <h4 class="text-xs font-black text-navy-900 uppercase tracking-widest italic border-b border-gray-50 pb-3">Pièces jointes</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    @foreach($ticket->attachments as $att)
                                        @if($att->type === 'image')
                                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="group relative rounded-xl overflow-hidden border border-gray-100 hover:border-gold-500 transition-all">
                                                <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->file_name }}" class="w-full h-32 object-cover group-hover:scale-105 transition duration-300">
                                                <div class="absolute inset-0 bg-navy-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-black uppercase tracking-wider">Agrandir</div>
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="flex flex-col justify-between p-4 border border-gray-100 rounded-xl hover:border-gold-500 text-left transition-all">
                                                <div class="text-2xl mb-2">📄</div>
                                                <div class="text-[10px] font-black text-navy-900 uppercase tracking-tight line-clamp-1 mb-0.5">{{ $att->file_name }}</div>
                                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Document PDF / Fichier</div>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Technician Diagnostic & Intervention Report -->
                        @if($ticket->diagnosis || $ticket->intervention_notes || $ticket->parts_used)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-6">
                                <h4 class="text-xs font-black text-navy-900 uppercase tracking-widest italic border-b border-gray-50 pb-3">Rapport d'intervention technique</h4>
                                
                                @if($ticket->diagnosis)
                                    <div class="space-y-2">
                                        <h5 class="text-[10px] font-black text-purple-600 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            Diagnostic Technique
                                        </h5>
                                        <p class="text-xs text-gray-600 font-medium leading-relaxed bg-purple-50/30 p-4 border border-purple-100/50 rounded-xl whitespace-pre-line">
                                            {{ $ticket->diagnosis }}
                                        </p>
                                    </div>
                                @endif

                                @if($ticket->intervention_notes)
                                    <div class="space-y-2">
                                        <h5 class="text-[10px] font-black text-blue-600 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Travaux d'intervention effectués
                                        </h5>
                                        <p class="text-xs text-gray-600 font-medium leading-relaxed bg-blue-50/30 p-4 border border-blue-100/50 rounded-xl whitespace-pre-line">
                                            {{ $ticket->intervention_notes }}
                                        </p>
                                    </div>
                                @endif

                                @if($ticket->parts_used)
                                    <div class="space-y-2">
                                        <h5 class="text-[10px] font-black text-amber-600 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            Pièces et composants remplacés
                                        </h5>
                                        <p class="text-xs text-gray-600 font-medium leading-relaxed bg-amber-50/30 p-4 border border-amber-100/50 rounded-xl whitespace-pre-line">
                                            {{ $ticket->parts_used }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Right: Info Details & Product details -->
                    <div class="space-y-8">
                        <!-- Info Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-4">
                            <h4 class="text-xs font-black text-navy-900 uppercase tracking-widest italic border-b border-gray-50 pb-3">Informations générales</h4>
                            <div class="space-y-3 text-xs">
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-gray-400 font-bold">Nature</span>
                                    <span class="text-navy-900 font-black uppercase italic">
                                        @switch($ticket->type)
                                            @case('repair') Réparation @break
                                            @case('installation') Installation @break
                                            @case('maintenance') Maintenance @break
                                            @case('advice') Conseil @break
                                            @case('warranty_claim') Garantie @break
                                            @default {{ $ticket->type }}
                                        @endswitch
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-gray-400 font-bold">Technicien</span>
                                    <span class="text-navy-900 font-black uppercase italic">
                                        {{ $ticket->technician?->name ?? 'En attente d\'attribution' }}
                                    </span>
                                </div>
                                @if($ticket->scheduled_date)
                                    <div class="flex justify-between items-center py-1">
                                        <span class="text-gray-400 font-bold">Date d'intervention</span>
                                        <span class="text-blue-600 font-black uppercase italic">
                                            📅 {{ $ticket->scheduled_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                @endif
                                @if($ticket->repair_cost > 0)
                                    <div class="flex justify-between items-center py-1 border-t border-gray-50 pt-3">
                                        <span class="text-gray-400 font-bold">Coût facturé</span>
                                        <span class="text-navy-900 font-black italic">
                                            {{ number_format($ticket->repair_cost, 0, ',', ' ') }} FCFA
                                        </span>
                                    </div>
                                @endif
                                @if($ticket->covered_by_warranty)
                                    <div class="bg-green-50 text-green-700 border border-green-100 rounded-xl p-3 text-[10px] font-black uppercase tracking-wider text-center italic mt-2">
                                        🛡️ Pris en charge par la Garantie
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Product details card -->
                        @if($ticket->product_name || $ticket->serial_number)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-4">
                                <h4 class="text-xs font-black text-navy-900 uppercase tracking-widest italic border-b border-gray-50 pb-3">Matériel concerné</h4>
                                <div class="space-y-3 text-xs">
                                    @if($ticket->product_name)
                                        <div class="py-1">
                                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider block mb-0.5">Appareil</span>
                                            <span class="text-navy-900 font-black uppercase italic">{{ $ticket->product_name }}</span>
                                        </div>
                                    @endif
                                    @if($ticket->serial_number)
                                        <div class="py-1">
                                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider block mb-0.5">Numéro de série (S/N)</span>
                                            <span class="text-navy-900 font-mono font-bold">{{ $ticket->serial_number }}</span>
                                        </div>
                                    @endif
                                    @if($ticket->warranty)
                                        <div class="py-1 border-t border-gray-50 pt-3">
                                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider block mb-1">Contrat de Garantie lié</span>
                                            <a href="{{ route('dashboard.warranties') }}" class="text-gold-600 font-black uppercase tracking-tight hover:underline flex items-center gap-1.5">
                                                🛡️ {{ $ticket->warranty->number }}
                                            </a>
                                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider block mt-1">Expire le {{ $ticket->warranty->expiry_date ? $ticket->warranty->expiry_date->format('d/m/Y') : 'N/A' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Help Notice -->
                        <div class="bg-navy-900 text-white rounded-xl p-8 space-y-4">
                            <h4 class="text-xs font-black text-gold-500 uppercase tracking-widest italic">Besoin d'assistance ?</h4>
                            <p class="text-[10px] text-gray-300 font-medium leading-relaxed">
                                Notre SAV traite vos demandes dans les meilleurs délais. Si vous souhaitez apporter des détails complémentaires, veuillez contacter notre support direct.
                            </p>
                            <div class="text-[11px] font-black italic text-gold-500 pt-2 flex flex-col gap-1.5">
                                <span>📞 +221 77 351 87 16</span>
                                <span>✉️ support@itholdingsn.com</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
