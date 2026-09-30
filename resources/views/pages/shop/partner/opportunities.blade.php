@extends('layouts.app')

@section('title', 'Opportunités - ' . config('app.name'))

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
                <a href="{{ route('dashboard.partner') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Espace Partenaire</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold uppercase tracking-wider">Opportunités</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('layouts.client_sidebar')

            <main class="flex-1 min-w-0 space-y-8">
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('success') }}</div>
                @endif
                @if(session('warning'))
                <div class="bg-orange-50 border border-orange-200 text-orange-800 px-4 py-3 rounded-lg text-xs font-bold italic">⚠️ {{ session('warning') }}</div>
                @endif
                @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('error') }}</div>
                @endif

                <!-- Sub navigation tabs -->
                @include('pages.shop.partner._tabs', ['active' => 'opportunities'])

                <!-- Intro -->
                <div class="bg-navy-900 text-white rounded-2xl p-8 relative overflow-hidden shadow-lg border-b-4 border-gold-500">
                    <div class="relative z-10 space-y-3">
                        <span class="bg-gold-500 text-navy-900 text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded">Apporteur d'affaires</span>
                        <h2 class="text-2xl lg:text-3xl font-black uppercase italic tracking-tight">Déclarez une opportunité</h2>
                        <p class="text-xs text-gray-300 max-w-2xl font-medium leading-relaxed italic">
                            Une opportunité, c'est une mise en relation ponctuelle — pas une prospection continue : vous mettez IT Holding en relation avec un besoin identifié (un marché, un client, une entreprise) et IT Holding se charge de conclure l'affaire. Chaque déclaration reçoit un numéro de dossier officiel et reste protégée : si un autre apporteur déclare le même prospect, le système le signale et IT Holding arbitre.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- New opportunity form -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                                <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Nouvelle opportunité</h3>
                            </div>
                            <form action="{{ route('dashboard.partner.opportunities.store') }}" method="POST" class="p-6 space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nom du contact / entreprise *</label>
                                    <input type="text" name="name" required value="{{ old('name') }}" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="Ex: Entreprise X">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Société</label>
                                    <input type="text" name="company" value="{{ old('company') }}" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Téléphone</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                                    </div>
                                </div>
                                <p class="text-[9px] text-gray-400 -mt-2">Renseignez au moins un téléphone, un email ou une société.</p>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Besoin identifié</label>
                                    <textarea name="need" rows="3" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="Ex: 30 ordinateurs pour renouvellement de parc">{{ old('need') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Budget estimatif (FCFA)</label>
                                    <input type="number" name="budget" value="{{ old('budget') }}" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: 40000000">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Notes</label>
                                    <textarea name="notes" rows="2" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">{{ old('notes') }}</textarea>
                                </div>
                                <button type="submit" class="w-full btn-primary-gold py-3 text-[10px] uppercase tracking-[0.2em]">Enregistrer l'opportunité</button>
                            </form>
                        </div>
                    </div>

                    <!-- Opportunities list -->
                    <div class="lg:col-span-2 space-y-4">
                        @forelse($opportunities as $opp)
                        <div class="bg-white rounded-xl shadow-sm border {{ $opp->duplicate_status === 'flagged' ? 'border-orange-200' : 'border-gray-100' }} p-5">
                            <div class="flex items-start justify-between gap-3 flex-wrap">
                                <div>
                                    <span class="text-[10px] font-mono font-black text-navy-900 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded">{{ $opp->opportunity_ref }}</span>
                                    <h4 class="text-sm font-black text-navy-900 mt-2">{{ $opp->name }}</h4>
                                    <p class="text-xs text-gray-400">{{ $opp->company ?: '—' }} · {{ $opp->phone ?: $opp->email ?: '—' }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded
                                        {{ $opp->status === 'won' ? 'bg-green-50 text-green-700 border border-green-200' :
                                           ($opp->status === 'lost' ? 'bg-red-50 text-red-700 border border-red-200' :
                                           'bg-gray-100 text-gray-600 border border-gray-200') }}">
                                        {{ ['new'=>'Nouveau','contacted'=>'Contacté','interested'=>'Intéressé','proposal_sent'=>'Devis envoyé','negotiating'=>'Négociation','won'=>'Gagné','lost'=>'Perdu'][$opp->status] ?? ucfirst($opp->status) }}
                                    </span>
                                    @if($opp->duplicate_status === 'flagged')
                                        <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-orange-50 text-orange-700 border border-orange-200">🚩 En arbitrage</span>
                                    @endif
                                </div>
                            </div>
                            @if($opp->need)
                            <p class="text-xs text-gray-500 mt-3 italic">{{ $opp->need }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                                <span class="text-xs font-bold text-navy-900">{{ $opp->budget ? number_format($opp->budget, 0, ',', ' ') . ' FCFA (estimé)' : '—' }}</span>
                                @if($opp->commission_amount)
                                    <span class="text-xs font-black text-gold-600">{{ number_format($opp->commission_amount, 0, ',', ' ') }} FCFA de commission</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center text-xs text-gray-400 italic">
                            Vous n'avez pas encore déclaré d'opportunité. Utilisez le formulaire pour mettre IT Holding en relation avec un prospect.
                        </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
