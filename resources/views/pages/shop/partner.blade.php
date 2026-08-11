@extends('layouts.app')

@section('title', 'Espace Partenaire - ' . config('app.name'))

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
                <span class="text-navy-900 font-bold uppercase tracking-wider">Espace Partenaire</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            @include('layouts.client_sidebar')

            <!-- Main Partner Content -->
            <main class="flex-1 space-y-8">
                <!-- Session Alerts -->
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic">
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-xs font-bold italic">
                    {{ session('error') }}
                </div>
                @endif

                @if($user->role !== 'partner')
                    <!-- Non-partner client page: Promotion / Join Program -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-navy-900 text-white p-8 relative overflow-hidden">
                            <span class="text-gold-500 text-xs font-bold uppercase tracking-widest">IT Holding Partner</span>
                            <h2 class="text-2xl font-black italic uppercase tracking-wider mt-2 mb-4">Devenez Partenaire et gagnez de l'argent !</h2>
                            <p class="text-xs text-gray-300 max-w-xl leading-relaxed italic">
                                Rejoignez notre réseau commercial digital. Recommandez nos produits et services informatique, et touchez des commissions automatiques sur chaque vente réussie.
                            </p>
                        </div>
                        <div class="p-8 space-y-6">
                            <h3 class="text-sm font-black uppercase tracking-wider text-navy-900 border-b pb-2">Pourquoi devenir partenaire ?</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-navy-700 font-bold italic">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gold-500 text-navy-900 flex items-center justify-center flex-shrink-0 font-black">✓</div>
                                    <div>
                                        <p class="text-navy-900 uppercase">Jusqu'à 20% de commission</p>
                                        <p class="text-gray-400 font-medium">Touchez un pourcentage attractif sur chaque produit et service informatique vendu.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gold-500 text-navy-900 flex items-center justify-center flex-shrink-0 font-black">✓</div>
                                    <div>
                                        <p class="text-navy-900 uppercase">Zéro investissement</p>
                                        <p class="text-gray-400 font-medium">Vous n'avez pas besoin de stock, de logistique, ni de boutique. Nous gérons tout.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gold-500 text-navy-900 flex items-center justify-center flex-shrink-0 font-black">✓</div>
                                    <div>
                                        <p class="text-navy-900 uppercase">Lien & Code Unique</p>
                                        <p class="text-gray-400 font-medium">Partagez vos liens uniques ou codes promos sur WhatsApp, Facebook ou en direct.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gold-500 text-navy-900 flex items-center justify-center flex-shrink-0 font-black">✓</div>
                                    <div>
                                        <p class="text-navy-900 uppercase">Suivi en temps réel</p>
                                        <p class="text-gray-400 font-medium">Suivez vos ventes, vos commissions en attente et demandez vos retraits en 1 clic.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="pt-6 border-t flex justify-center">
                                <form action="{{ route('dashboard.partner.apply') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-primary-gold px-8 py-3.5 text-xs font-black uppercase tracking-widest shadow-md">
                                        Activer mon espace partenaire
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif($user->partner_status === 'pending')
                    <!-- Pending candidate -->
                    <div class="bg-white rounded-xl shadow-sm border border-yellow-100 p-10 text-center space-y-6">
                        <div class="w-16 h-16 bg-yellow-50 text-yellow-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                            <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-base font-black text-navy-900 uppercase tracking-widest italic">Candidature en cours d'examen</h3>
                            <p class="text-xs text-navy-700 leading-relaxed italic max-w-md mx-auto">
                                Votre demande d'adhésion au programme <strong>IT Holding Partner</strong> a bien été enregistrée et est actuellement en cours d'évaluation par notre équipe commerciale.
                            </p>
                            <p class="text-xs text-gray-400 italic">
                                Vous recevrez une notification par e-mail ou WhatsApp dès que votre compte sera activé. Merci pour votre patience !
                            </p>
                        </div>
                    </div>
                @elseif($user->partner_status === 'rejected')
                    <!-- Rejected candidate -->
                    <div class="bg-white rounded-xl shadow-sm border border-red-100 p-10 text-center space-y-6">
                        <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2 2 2m0-4l-2 2-2-2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-base font-black text-navy-900 uppercase tracking-widest italic">Candidature non retenue</h3>
                            <p class="text-xs text-navy-700 leading-relaxed italic max-w-md mx-auto">
                                Nous regrettons de vous informer que votre candidature pour rejoindre le programme <strong>IT Holding Partner</strong> n'a pas été acceptée pour le moment.
                            </p>
                            <p class="text-xs text-gray-400 italic">
                                Si vous avez des questions ou souhaitez soumettre de nouveaux éléments, vous pouvez contacter notre support client.
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Approved Partner Dashboard (Original Active Screen) -->
                    <!-- Sub navigation tabs -->
                    <div class="flex flex-wrap border-b border-gray-200 bg-white rounded-xl p-2 shadow-sm gap-2 mb-6">
                        <a href="{{ route('dashboard.partner') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors bg-navy-900 text-white flex items-center gap-2">
                            <span>📊</span> Tableau de bord
                        </a>
                        <a href="{{ route('dashboard.partner.crm') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                            <span>👥</span> CRM & Prospects
                        </a>
                        <a href="{{ route('dashboard.partner.assistant') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                            <span>🤖</span> Assistant IA
                        </a>
                        <a href="{{ route('dashboard.partner.marketing') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                            <span>📢</span> Studio Marketing
                        </a>
                    </div>

                    <!-- Welcome Section -->
                    <div class="bg-navy-900 text-white rounded-2xl p-8 relative overflow-hidden shadow-lg border-b-4 border-gold-500">
                        <div class="absolute right-0 top-0 opacity-10 translate-x-10 -translate-y-10">
                            <svg class="w-80 h-80 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M13 7H7v6h6V7z"/><path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 110-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="relative z-10 space-y-4">
                            <span class="bg-gold-500 text-navy-900 text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded">Partenariat Privilégié</span>
                            <h2 class="text-2xl lg:text-3xl font-black uppercase italic tracking-tight">Programme d'Affiliation & Codes Promo</h2>
                            <p class="text-xs text-gray-300 max-w-xl font-medium leading-relaxed italic">
                                Partagez votre code promo unique ou votre lien commercial personnel avec votre audience, vos amis ou vos clients. Pour chaque achat effectué via votre lien ou avec votre code : vos contacts bénéficient de <b>5% de réduction immédiate</b> (avec code promo), et vous recevez une commission de <b>10% du montant de leur commande</b> directement versée sur votre portefeuille.
                            </p>
                            
                            <div class="bg-navy-800/80 backdrop-blur rounded-xl p-4 mt-6 border border-navy-700/50 max-w-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="space-y-1 text-center sm:text-left flex-1 min-w-0 w-full">
                                    <span class="text-[9px] font-bold text-gold-500 uppercase tracking-widest block">Votre Lien Commercial Personnel</span>
                                    <div class="text-xs font-mono select-all text-white break-all truncate" id="referral-url" title="{{ url('/partner/' . ($user->username ?: $user->partner_code)) }}">
                                        {{ url('/partner/' . ($user->username ?: $user->partner_code)) }}
                                    </div>
                                </div>
                                <button onclick="copyReferralLink()" class="flex-shrink-0 w-full sm:w-auto text-[10px] font-black text-navy-900 bg-gold-500 hover:bg-white hover:text-navy-900 transition-colors uppercase tracking-widest px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                    Copier le Lien
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 bg-green-50 rounded flex items-center justify-center text-green-600 shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="text-xl font-black text-navy-900 block tracking-tighter">{{ number_format($totalEarned, 0, ',', ' ') }} FCFA</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest italic">Commissions Validées</span>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 bg-orange-50 rounded flex items-center justify-center text-orange-500 shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="text-xl font-black text-navy-900 block tracking-tighter">{{ number_format($totalPending, 0, ',', ' ') }} FCFA</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest italic">Commissions En attente</span>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 bg-gold-50 rounded flex items-center justify-center text-gold-600 shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="text-xl font-black text-navy-900 block tracking-tighter">{{ number_format($client ? $client->wallet_balance : 0, 0, ',', ' ') }} FCFA</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest italic">Solde de votre Portefeuille</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left: My Promo Codes list and Creation form -->
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                                    <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Mes Codes Promo</h3>
                                </div>
                                <div class="p-6 space-y-6">
                                    @forelse($promoCodes as $promo)
                                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-100 space-y-3 relative group">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm font-black text-navy-900 bg-white border border-gray-200 px-3 py-1.5 rounded tracking-widest uppercase cursor-pointer hover:border-gold-500 transition-colors" onclick="copyToClipboard('{{ $promo->code }}')" title="Cliquez pour copier le code">
                                                    {{ $promo->code }}
                                                </span>
                                                <span class="text-[9px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded uppercase tracking-wider">Actif</span>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Réduction acheteur</p>
                                                <p class="text-xs font-black text-navy-900">{{ number_format($promo->discount_percent, 0) }}%</p>
                                            </div>
                                        </div>

                                        <div class="border-t border-gray-200/50 pt-3 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                            <div class="text-[11px] font-bold text-navy-700 italic">
                                                Votre commission : <span class="text-gold-600 font-black">{{ number_format($promo->commission_percent, 0) }}%</span> de chaque vente
                                            </div>
                                            <button onclick="copyToClipboard('{{ $promo->code }}')" class="text-[9px] font-black text-navy-900 bg-gold-500 px-3 py-2 rounded uppercase tracking-widest hover:bg-navy-900 hover:text-white transition-all flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                                Copier le Code
                                            </button>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-6 text-xs text-gray-400 font-bold italic">
                                        Vous n'avez pas encore généré de code promo partenaire. Utilisez le formulaire ci-contre pour en créer un.
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Commissions History Table -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                                    <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Historique des Ventes & Commissions</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                <th class="px-6 py-3.5 border-b border-gray-100 italic">Commande</th>
                                                <th class="px-6 py-3.5 border-b border-gray-100 italic">Code Utilisé</th>
                                                <th class="px-6 py-3.5 border-b border-gray-100 italic">Montant Vente</th>
                                                <th class="px-6 py-3.5 border-b border-gray-100 italic">Ma Commission</th>
                                                <th class="px-6 py-3.5 border-b border-gray-100 italic">Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @forelse($commissions as $comm)
                                            <tr class="hover:bg-gray-50/30 transition-colors text-[11px] font-bold text-navy-700">
                                                <td class="px-6 py-4 text-navy-900">#{{ str_pad($comm->order_id, 8, '0', STR_PAD_LEFT) }}</td>
                                                <td class="px-6 py-4"><span class="bg-gray-100 px-2 py-0.5 rounded font-mono">{{ $comm->promoCode->code }}</span></td>
                                                <td class="px-6 py-4">{{ number_format($comm->order_amount, 0, ',', ' ') }} CFA</td>
                                                <td class="px-6 py-4 text-gold-600 font-black">{{ number_format($comm->commission_amount, 0, ',', ' ') }} CFA</td>
                                                <td class="px-6 py-4">
                                                    @if($comm->status === 'paid')
                                                    <span class="text-[9px] font-black bg-green-100 text-green-700 px-2 py-1 rounded uppercase tracking-widest italic">Validé</span>
                                                    @elseif($comm->status === 'cancelled')
                                                    <span class="text-[9px] font-black bg-red-100 text-red-700 px-2 py-1 rounded uppercase tracking-widest italic">Annulé</span>
                                                    @else
                                                    <span class="text-[9px] font-black bg-orange-100 text-orange-700 px-2 py-1 rounded uppercase tracking-widest italic">En attente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-10 text-center text-xs text-gray-400 font-bold italic">
                                                    Aucune vente enregistrée avec vos codes promo pour le moment.
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if($commissions->count() > 0)
                                <div class="px-6 py-4 border-t border-gray-50">
                                    {{ $commissions->links() }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right sidebar: Generate code form and guidelines -->
                        <div class="space-y-6">
                            <!-- Create code form -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                                    <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Créer un Code Promo</h3>
                                </div>
                                <div class="p-6">
                                    <form action="{{ route('dashboard.partner.promo.generate') }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label for="code" class="block text-[10px] font-black text-navy-900 uppercase tracking-wider italic mb-2">Code personnalisé</label>
                                            <input type="text" id="code" name="code" value="{{ old('code') }}" required
                                                placeholder="Ex: PROMO10, NOMDEPARTENAIRE"
                                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-xs font-bold text-navy-900 placeholder-gray-400 focus:bg-white focus:border-gold-500 focus:ring-0 uppercase tracking-widest transition-all">
                                            @error('code')
                                            <p class="text-[10px] text-red-500 font-bold italic mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="bg-navy-50/50 rounded-lg p-4 border border-navy-100 text-[10px] space-y-1.5 font-bold text-navy-700 italic">
                                            <p class="text-navy-900 uppercase tracking-wide">Valeurs par défaut :</p>
                                            <p>• Réduction acheteur : <span class="text-gold-600 font-black">5%</span></p>
                                            <p>• Commission partenaire : <span class="text-gold-600 font-black">10%</span></p>
                                        </div>

                                        <button type="submit" class="w-full bg-navy-900 text-white hover:bg-gold-500 hover:text-navy-900 text-[10px] font-black uppercase tracking-widest py-3 rounded-lg shadow-md transition-all">
                                            Générer mon Code
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Guidelines card -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                                    <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Comment ça marche ?</h3>
                                </div>
                                <div class="p-6 space-y-4 text-xs font-medium text-navy-700 leading-relaxed italic">
                                    <div class="flex gap-3">
                                        <div class="w-6 h-6 bg-gold-500 text-navy-900 font-black rounded-full flex items-center justify-center flex-shrink-0">1</div>
                                        <p>Créez votre code promo personnalisé ci-dessus (par exemple <b>SUPER10</b>).</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="w-6 h-6 bg-gold-500 text-navy-900 font-black rounded-full flex items-center justify-center flex-shrink-0">2</div>
                                        <p>Partagez ce code avec vos amis, clients ou sur vos réseaux sociaux.</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="w-6 h-6 bg-gold-500 text-navy-900 font-black rounded-full flex items-center justify-center flex-shrink-0">3</div>
                                        <p>Vos contacts l'insèrent dans leur panier et profitent de 5% de remise immédiate.</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="w-6 h-6 bg-gold-500 text-navy-900 font-black rounded-full flex items-center justify-center flex-shrink-0">4</div>
                                        <p>Une fois la commande de vos contacts validée (status <b>Complétée</b>), votre commission de 10% est créditée dans votre portefeuille.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Code promo "' + text + '" copié dans le presse-papiers !');
        }, function(err) {
            console.error('Erreur de copie : ', err);
        });
    }

    function copyReferralLink() {
        const link = document.getElementById('referral-url').innerText.trim();
        navigator.clipboard.writeText(link).then(function() {
            alert('Votre lien d\'affiliation personnel a été copié dans le presse-papiers !');
        }, function(err) {
            console.error('Erreur de copie : ', err);
        });
    }
</script>
@endsection
