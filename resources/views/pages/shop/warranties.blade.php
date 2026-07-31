@extends('layouts.app')

@section('title', 'Mes Garanties - ' . config('app.name'))

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
                <span class="text-navy-900 font-bold uppercase tracking-wider italic">Mes Garanties</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            @include('layouts.client_sidebar')

            <!-- Main Content -->
            <main class="flex-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                        <h3 class="text-sm font-black text-navy-900 uppercase tracking-tighter italic">Mes Garanties</h3>
                        <span class="bg-gold-100 text-gold-800 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full italic">
                            {{ $warranties->total() }} Produit(s) Sous Garantie
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-8 py-4 border-b border-gray-100 italic">N° Garantie / Produit</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">N° de Série</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Type / Durée</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Fin de Garantie</th>
                                    <th class="px-8 py-4 border-b border-gray-100 italic">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($warranties as $warranty)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="text-[11px] font-black text-navy-900 italic mb-0.5">
                                            {{ $warranty->number }}
                                        </div>
                                        <div class="text-[11px] font-bold text-gray-500">
                                            {{ $warranty->product_name ?? ($warranty->product ? $warranty->product->name : 'N/A') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-[11px] font-mono font-bold text-navy-900 italic">
                                        {{ $warranty->serial_number ?: 'N/A' }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-[11px] font-bold text-navy-900 block">
                                            {{ \App\Models\Warranty::typeLabel($warranty->type) }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider italic">
                                            {{ $warranty->duration_months }} Mois
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="text-[11px] font-bold text-navy-900">
                                            {{ $warranty->expiry_date ? $warranty->expiry_date->format('d/m/Y') : 'N/A' }}
                                        </div>
                                        @if($warranty->status === 'active' && $warranty->expiry_date)
                                            <div class="text-[9px] text-gray-400 font-black uppercase tracking-tight">
                                                Restant: {{ $warranty->days_remaining }} Jours
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5">
                                        @php
                                            $config = \App\Models\Warranty::statusConfig($warranty->status);
                                        @endphp
                                        <span class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider border {{ $config['classes'] }} italic">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-200">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            </div>
                                            <p class="text-xs text-gray-400 font-bold italic">Aucune garantie active ou enregistrée pour vos achats.</p>
                                            <a href="{{ route('shop.index') }}" class="btn-primary-gold px-6 py-2 text-[10px] uppercase tracking-widest">Commencer vos achats</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($warranties->count() > 0)
                    <div class="px-8 py-6 border-t border-gray-50 flex items-center justify-center">
                        {{ $warranties->links('pagination::tailwind') }}
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
