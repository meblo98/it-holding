@extends('layouts.app')

@section('title', 'CRM Partenaire - ' . config('app.name'))

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
                <a href="{{ route('dashboard.partner') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Espace Partenaire</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold uppercase tracking-wider">CRM & Prospects</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            @include('layouts.client_sidebar')

            <!-- Main Partner CRM Content -->
            <main class="flex-1 space-y-8" x-data="{ 
                openCreateModal: false, 
                openEditModal: false,
                currentProspect: {
                    id: '',
                    name: '',
                    phone: '',
                    email: '',
                    company: '',
                    need: '',
                    budget: '',
                    status: 'new',
                    notes: '',
                    next_action_at: '',
                    next_action_description: ''
                },
                openEdit(prospect) {
                    this.currentProspect = { ...prospect };
                    // Format next_action_at for input datetime-local
                    if (this.currentProspect.next_action_at) {
                        this.currentProspect.next_action_at = this.currentProspect.next_action_at.substring(0, 16);
                    }
                    this.openEditModal = true;
                }
            }">
                <!-- Sub navigation tabs -->
                <div class="flex flex-wrap border-b border-gray-200 bg-white rounded-xl p-2 shadow-sm gap-2">
                    <a href="{{ route('dashboard.partner') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>📊</span> Tableau de bord
                    </a>
                    <a href="{{ route('dashboard.partner.crm') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors bg-navy-900 text-white flex items-center gap-2">
                        <span>👥</span> CRM & Prospects
                    </a>
                    <a href="{{ route('dashboard.partner.assistant') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>🤖</span> Assistant IA
                    </a>
                    <a href="{{ route('dashboard.partner.marketing') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>📢</span> Studio Marketing
                    </a>
                </div>

                <!-- Session Alerts -->
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic">
                    {{ session('success') }}
                </div>
                @endif

                <!-- CRM Header & Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-1">CA Gagné</span>
                        <span class="text-lg font-black text-navy-900 block tracking-tighter">{{ number_format($totalCA, 0, ',', ' ') }} FCFA</span>
                        <span class="text-[9px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded inline-block mt-2">{{ $wonCount }} ventes conclues</span>
                    </div>
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Valeur du Pipeline</span>
                        <span class="text-lg font-black text-navy-900 block tracking-tighter">{{ number_format($pipelineValue, 0, ',', ' ') }} FCFA</span>
                        <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded inline-block mt-2">Opportunités actives</span>
                    </div>
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Total Prospects</span>
                        <span class="text-lg font-black text-navy-900 block tracking-tighter">{{ $prospectsCount }}</span>
                        <span class="text-[9px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded inline-block mt-2">Portefeuille global</span>
                    </div>
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex flex-col justify-between">
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Prochaine action</span>
                            @if($upcomingActions->isNotEmpty())
                                <span class="text-[10px] font-black text-navy-900 block truncate" title="{{ $upcomingActions->first()->next_action_description }}">
                                    {{ $upcomingActions->first()->next_action_description }}
                                </span>
                                <span class="text-[9px] font-bold text-orange-600 mt-1 block">
                                    {{ $upcomingActions->first()->next_action_at->format('d/m H:i') }} ({{ $upcomingActions->first()->name }})
                                </span>
                            @else
                                <span class="text-[10px] text-gray-400 font-bold block italic mt-1">Aucune action planifiée</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action buttons and Upcoming alerts -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-black text-navy-900 uppercase tracking-tight italic">Mon Pipeline Commercial</h2>
                        <p class="text-xs text-gray-400 italic">Cliquez sur un prospect pour mettre à jour sa fiche ou modifier son statut.</p>
                    </div>
                    <button @click="openCreateModal = true" class="text-xs font-black text-navy-900 bg-gold-500 hover:bg-navy-900 hover:text-white transition-colors uppercase tracking-widest px-5 py-3 rounded-lg flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter un prospect
                    </button>
                </div>

                <!-- Kanban Board -->
                <div class="overflow-x-auto flex gap-4 pb-6 select-none -mx-4 px-4 md:mx-0 md:px-0">
                    @foreach($stages as $key => $stage)
                        <div class="flex-shrink-0 w-80 bg-white border border-gray-200/60 rounded-xl p-4 flex flex-col h-[600px] shadow-sm">
                            <!-- Stage Header -->
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md {{ $stage['color'] }} border">
                                        {{ $stage['label'] }}
                                    </span>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold">{{ $stage['prospects']->count() }}</span>
                            </div>

                            <!-- Cards Container -->
                            <div class="flex-1 overflow-y-auto space-y-3 pr-1 scrollbar-thin">
                                @forelse($stage['prospects'] as $prospect)
                                    <div @click="openEdit({{ json_encode($prospect) }})" class="bg-gray-50 hover:bg-white rounded-lg p-4 border border-gray-100 hover:border-gold-500 hover:shadow-md cursor-pointer transition-all space-y-3 group">
                                        <div class="flex justify-between items-start">
                                            <h4 class="text-xs font-black text-navy-900 group-hover:text-gold-600 transition-colors truncate max-w-[80%]">{{ $prospect->name }}</h4>
                                            @if($prospect->budget)
                                                <span class="text-[10px] font-black text-navy-900 flex-shrink-0">{{ number_format($prospect->budget, 0, ',', ' ') }} F</span>
                                            @endif
                                        </div>

                                        @if($prospect->company)
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider -mt-1">{{ $prospect->company }}</p>
                                        @endif

                                        @if($prospect->need)
                                            <p class="text-[10px] text-navy-800 italic line-clamp-2 leading-relaxed bg-white/40 p-2 rounded border border-gray-50">{{ $prospect->need }}</p>
                                        @endif

                                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-[9px] font-bold text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                {{ $prospect->phone ?: 'N/A' }}
                                            </span>
                                            @if($prospect->next_action_at)
                                                <span class="flex items-center gap-1 {{ $prospect->next_action_at->isPast() && $prospect->status !== 'won' && $prospect->status !== 'lost' ? 'text-red-600 animate-pulse' : 'text-orange-600' }}">
                                                    📅 {{ $prospect->next_action_at->format('d/m') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10 text-[10px] text-gray-400 font-bold italic">
                                        Aucun prospect
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ==================== CREATE MODAL ==================== -->
                <div x-show="openCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-900/60 backdrop-blur-sm" x-cloak>
                    <div @click.away="openCreateModal = false" class="bg-white rounded-xl shadow-xl border border-gray-150 w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Créer un nouveau prospect</h3>
                            <button @click="openCreateModal = false" class="text-gray-400 hover:text-navy-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form action="{{ route('dashboard.partner.crm.store') }}" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Nom du prospect *</label>
                                    <input type="text" name="name" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: Abdoulaye Ndiaye">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Entreprise</label>
                                    <input type="text" name="company" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: ABC Services">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Téléphone</label>
                                    <input type="text" name="phone" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: 771234567">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Email</label>
                                    <input type="email" name="email" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: client@gmail.com">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Budget Estimé (FCFA)</label>
                                    <input type="number" name="budget" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: 1500000">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Statut *</label>
                                    <select name="status" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none bg-white">
                                        <option value="new">Nouveau</option>
                                        <option value="contacted">Contacté</option>
                                        <option value="interested">Intéressé</option>
                                        <option value="proposal_sent">Devis envoyé</option>
                                        <option value="negotiating">En négociation</option>
                                        <option value="won">Gagné</option>
                                        <option value="lost">Perdu</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Besoin / Demande</label>
                                <textarea name="need" rows="2" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: Recherche 5 PC portables bureautiques..."></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Prochaine action (Date & Heure)</label>
                                    <input type="datetime-local" name="next_action_at" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Description de l'action</label>
                                    <input type="text" name="next_action_description" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: Appeler pour relance devis">
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Notes / Historique</label>
                                <textarea name="notes" rows="3" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="Ajouter des notes internes sur la discussion..."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-navy-900 text-white hover:bg-gold-500 hover:text-navy-900 text-[10px] font-black uppercase tracking-widest py-3 rounded-lg shadow-md transition-all">
                                Enregistrer le prospect
                            </button>
                        </form>
                    </div>
                </div>

                <!-- ==================== EDIT/SHOW MODAL ==================== -->
                <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-900/60 backdrop-blur-sm" x-cloak>
                    <div @click.away="openEditModal = false" class="bg-white rounded-xl shadow-xl border border-gray-150 w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Modifier la fiche prospect</h3>
                            <button @click="openEditModal = false" class="text-gray-400 hover:text-navy-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <!-- Actions panel inside show modal (WhatsApp, Call, SMS) -->
                        <div class="px-6 py-3 bg-indigo-50/50 border-b border-gray-100 flex items-center gap-2 justify-center">
                            <a :href="'https://wa.me/221' + currentProspect.phone" target="_blank" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded text-[10px] font-bold flex items-center gap-1.5 shadow-sm transition-colors">
                                💬 WhatsApp
                            </a>
                            <a :href="'tel:' + currentProspect.phone" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-[10px] font-bold flex items-center gap-1.5 shadow-sm transition-colors">
                                📞 Appeler
                            </a>
                            <a :href="'mailto:' + currentProspect.email" class="px-3 py-1.5 bg-navy-900 hover:bg-gold-500 hover:text-navy-900 text-white rounded text-[10px] font-bold flex items-center gap-1.5 shadow-sm transition-colors">
                                ✉️ Email
                            </a>
                        </div>

                        <form :action="'{{ route('dashboard.partner.crm.store') }}/' + currentProspect.id" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Nom du prospect *</label>
                                    <input type="text" name="name" required x-model="currentProspect.name" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Entreprise</label>
                                    <input type="text" name="company" x-model="currentProspect.company" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Téléphone</label>
                                    <input type="text" name="phone" x-model="currentProspect.phone" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Email</label>
                                    <input type="email" name="email" x-model="currentProspect.email" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Budget Estimé (FCFA)</label>
                                    <input type="number" name="budget" x-model="currentProspect.budget" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Statut *</label>
                                    <select name="status" x-model="currentProspect.status" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none bg-white">
                                        <option value="new">Nouveau</option>
                                        <option value="contacted">Contacté</option>
                                        <option value="interested">Intéressé</option>
                                        <option value="proposal_sent">Devis envoyé</option>
                                        <option value="negotiating">En négociation</option>
                                        <option value="won">Gagné</option>
                                        <option value="lost">Perdu</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Besoin / Demande</label>
                                <textarea name="need" rows="2" x-model="currentProspect.need" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Prochaine action (Date & Heure)</label>
                                    <input type="datetime-local" name="next_action_at" x-model="currentProspect.next_action_at" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Description de l'action</label>
                                    <input type="text" name="next_action_description" x-model="currentProspect.next_action_description" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Notes / Historique</label>
                                <textarea name="notes" rows="3" x-model="currentProspect.notes" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none"></textarea>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button type="submit" class="flex-1 bg-navy-900 text-white hover:bg-gold-500 hover:text-navy-900 text-[10px] font-black uppercase tracking-widest py-3 rounded-lg shadow-md transition-all">
                                    Mettre à jour
                                </button>
                        </form>
                        
                        <form :action="'{{ route('dashboard.partner.crm.store') }}/' + currentProspect.id" method="POST" onsubmit="return confirm('Confirmez-vous la suppression définitive de ce prospect ?')" class="px-6 pb-6">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-650 hover:bg-red-750 text-white text-[10px] font-black uppercase tracking-widest py-3 rounded-lg shadow-sm transition-all">
                                Supprimer le prospect
                            </button>
                        </form>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>
@endsection
