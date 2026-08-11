@extends('layouts.app')

@section('title', 'Studio Marketing Partenaire - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen" x-data="{ mainTab: 'poster' }">
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
                <span class="text-navy-900 font-bold uppercase tracking-wider">Studio Marketing</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            @include('layouts.client_sidebar')

            <!-- Main Marketing Content -->
            <main class="flex-1 space-y-8">
                <!-- Sub navigation tabs -->
                <div class="flex flex-wrap border-b border-gray-200 bg-white rounded-xl p-2 shadow-sm gap-2">
                    <a href="{{ route('dashboard.partner') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>📊</span> Tableau de bord
                    </a>
                    <a href="{{ route('dashboard.partner.crm') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>👥</span> CRM & Prospects
                    </a>
                    <a href="{{ route('dashboard.partner.assistant') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>🤖</span> Assistant IA
                    </a>
                    <a href="{{ route('dashboard.partner.marketing') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors bg-navy-900 text-white flex items-center gap-2">
                        <span>📢</span> Studio Marketing
                    </a>
                </div>

                <!-- Session Alerts -->
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Unified Studio Sub-Tabs -->
                <div class="flex flex-wrap border-b border-gray-200 bg-white rounded-xl p-2 shadow-sm gap-2">
                    <button @click="mainTab = 'poster'" :class="mainTab === 'poster' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
                        🎨 Affiches
                    </button>
                    <button @click="mainTab = 'catalog'" :class="mainTab === 'catalog' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
                        📋 Catalogue PDF
                    </button>
                    <button @click="mainTab = 'scheduler'" :class="mainTab === 'scheduler' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
                        📅 Planificateur Réseaux
                    </button>
                    <button @click="mainTab = 'video'" :class="mainTab === 'video' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
                        🎬 Créateur Vidéo IA
                    </button>
                    <button @click="mainTab = 'library'" :class="mainTab === 'library' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
                        📦 Bibliothèque Assets
                    </button>
                </div>

                <!-- TAB 1: POSTER GENERATOR -->
                <div x-show="mainTab === 'poster'" class="bg-white rounded-xl border border-gray-150 p-6 lg:p-8 shadow-sm space-y-6" x-data="{
                    products: {{ json_encode($products) }},
                    selectedProductId: '',
                    selectedProduct: null,
                    headline: 'Offre Exceptionnelle !',
                    theme: 'navy-gold',
                    customPrice: '',
                    showAffiliation: true,
                    showPhone: true,
                    partnerCode: '{{ $user->partner_code }}',
                    partnerPhone: '{{ $user->phone ?: '77 000 00 00' }}',
                    updateProduct() {
                        this.selectedProduct = this.products.find(p => p.id == this.selectedProductId) || null;
                        if(this.selectedProduct) {
                            this.customPrice = this.selectedProduct.price;
                        } else {
                            this.customPrice = '';
                        }
                    }
                }" x-cloak>
                    <div class="border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">🎨 Générateur d'Affiches Publicitaires</h2>
                        <p class="text-xs text-gray-400 italic">Personnalisez et téléchargez instantanément un visuel professionnel pour vos réseaux sociaux.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <!-- Controls (Left) -->
                        <div class="lg:col-span-5 space-y-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">1. Choisir le Produit</label>
                                <select x-model="selectedProductId" @change="updateProduct()" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white outline-none focus:ring-1 focus:ring-gold-500">
                                    <option value="">-- Sélectionnez un produit --</option>
                                    <template x-for="p in products" :key="p.id">
                                        <option :value="p.id" x-text="p.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">2. Thème Visuel</label>
                                <select x-model="theme" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white outline-none focus:ring-1 focus:ring-gold-500">
                                    <option value="navy-gold">🌌 Nuit de Luxe (Bleu Marine & Or)</option>
                                    <option value="cyber-orange">🔥 Cyber Contrast (Gris Foncé & Orange)</option>
                                    <option value="clean-minimal">❄️ Minimaliste (Dégradé Doux & Blanc)</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">3. Titre de l'Affiche</label>
                                <input type="text" x-model="headline" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:ring-1 focus:ring-gold-500">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">4. Prix affiché (FCFA)</label>
                                <input type="number" x-model="customPrice" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:ring-1 focus:ring-gold-500">
                            </div>

                            <div class="space-y-2 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="showAffiliation" class="rounded text-gold-500 focus:ring-gold-500">
                                    <span class="text-xs font-bold text-gray-600">Afficher mon code promo</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="showPhone" class="rounded text-gold-500 focus:ring-gold-500">
                                    <span class="text-xs font-bold text-gray-600">Afficher mon numéro WhatsApp</span>
                                </label>
                            </div>

                            <button @click="downloadPoster()" :disabled="!selectedProduct" class="w-full bg-gold-500 hover:bg-navy-900 hover:text-white disabled:bg-gray-200 disabled:text-gray-400 text-navy-900 text-[10px] font-black uppercase tracking-widest py-3.5 rounded-lg shadow transition-colors flex items-center justify-center gap-2 mt-4">
                                📥 Télécharger le visuel (PNG)
                            </button>
                        </div>

                        <!-- Live Preview (Right) -->
                        <div class="lg:col-span-7 flex justify-center items-center bg-gray-50 border border-gray-150 rounded-xl p-6 overflow-hidden">
                            <div id="poster-preview" class="w-[360px] h-[360px] relative rounded-lg overflow-hidden shadow-2xl flex flex-col justify-between p-6 select-none"
                                 :class="{
                                     'bg-gradient-to-br from-slate-950 via-navy-900 to-slate-950 text-white border-2 border-gold-500/30': theme === 'navy-gold',
                                     'bg-gradient-to-br from-neutral-900 to-neutral-850 text-white border-2 border-orange-500/30': theme === 'cyber-orange',
                                     'bg-gradient-to-br from-sky-50 via-white to-blue-50 text-gray-900 border-2 border-gray-200': theme === 'clean-minimal'
                                 }">
                                <div class="absolute inset-0 opacity-15 pointer-events-none mix-blend-overlay"
                                     :class="{
                                         'bg-[radial-gradient(circle_at_center,rgba(245,158,11,0.2),transparent_60%)]': theme === 'navy-gold',
                                         'bg-[radial-gradient(circle_at_center,rgba(249,115,22,0.25),transparent_60%)]': theme === 'cyber-orange',
                                         'bg-[radial-gradient(circle_at_center,rgba(56,189,248,0.2),transparent_60%)]': theme === 'clean-minimal'
                                     }"></div>

                                <div class="flex justify-between items-center z-10">
                                    <span class="text-[9px] font-black uppercase tracking-widest"
                                          :class="theme === 'clean-minimal' ? 'text-navy-900' : 'text-gold-400'">
                                          IT HOLDING
                                    </span>
                                    <span class="text-[8px] font-bold px-2 py-0.5 rounded-full uppercase"
                                          :class="{
                                              'bg-gold-500 text-navy-900': theme === 'navy-gold',
                                              'bg-orange-500 text-white': theme === 'cyber-orange',
                                              'bg-navy-900 text-white': theme === 'clean-minimal'
                                          }">
                                          Partenaire Agréé
                                    </span>
                                </div>

                                <div class="my-auto flex flex-col items-center justify-center z-10 text-center space-y-3">
                                    <h3 class="text-xs font-black uppercase tracking-wider"
                                        :class="{
                                            'text-gold-400': theme === 'navy-gold',
                                            'text-orange-500': theme === 'cyber-orange',
                                            'text-navy-900': theme === 'clean-minimal'
                                        }" x-text="headline"></h3>

                                    <div class="w-32 h-32 rounded-lg bg-white/10 flex items-center justify-center p-2 relative overflow-hidden shadow-inner backdrop-blur-sm border border-white/10">
                                        <template x-if="selectedProduct && selectedProduct.image">
                                            <img :src="'/storage/' + selectedProduct.image" class="max-w-full max-h-full object-contain mix-blend-normal">
                                        </template>
                                        <template x-if="!selectedProduct || !selectedProduct.image">
                                            <span class="text-xl">📦</span>
                                        </template>
                                    </div>

                                    <h4 class="text-sm font-black tracking-tight"
                                        :class="theme === 'clean-minimal' ? 'text-navy-900' : 'text-white'"
                                        x-text="selectedProduct ? selectedProduct.name : 'Nom du Produit'"></h4>
                                </div>

                                <div class="flex justify-between items-end border-t pt-3 z-10"
                                     :class="theme === 'clean-minimal' ? 'border-gray-200' : 'border-white/10'">
                                    
                                    <div class="text-left">
                                        <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wider block">Tarif spécial</span>
                                        <span class="text-lg font-black tracking-tighter"
                                              :class="{
                                                  'text-gold-400': theme === 'navy-gold',
                                                  'text-orange-500': theme === 'cyber-orange',
                                                  'text-navy-900': theme === 'clean-minimal'
                                              }"
                                              x-text="customPrice ? Number(customPrice).toLocaleString('fr-FR') + ' F' : '-- F'"></span>
                                    </div>

                                    <div class="text-right space-y-1">
                                        <template x-if="showAffiliation && partnerCode">
                                            <span class="text-[7px] font-bold px-2 py-0.5 rounded border uppercase block"
                                                  :class="{
                                                      'border-gold-500/40 text-gold-400 bg-gold-500/10': theme === 'navy-gold',
                                                      'border-orange-500/40 text-orange-500 bg-orange-500/10': theme === 'cyber-orange',
                                                      'border-navy-900/40 text-navy-900 bg-navy-900/5': theme === 'clean-minimal'
                                                  }">
                                                Code -5% : <span class="font-black" x-text="partnerCode"></span>
                                            </span>
                                        </template>
                                        <template x-if="showPhone">
                                            <span class="text-[8px] font-black block"
                                                  :class="theme === 'clean-minimal' ? 'text-gray-600' : 'text-gray-300'">
                                                🟢 WhatsApp : <span x-text="partnerPhone"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: CATALOG GENERATOR -->
                <div x-show="mainTab === 'catalog'" class="bg-white rounded-xl border border-gray-150 p-6 lg:p-8 shadow-sm space-y-6" x-cloak>
                    <div class="border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">📋 Générateur de Catalogue PDF Personnalisé</h2>
                        <p class="text-xs text-gray-400 italic">Sélectionnez les produits que vous souhaitez inclure dans un mini-catalogue PDF personnalisé à votre nom.</p>
                    </div>

                    <form action="{{ route('dashboard.partner.marketing.catalog') }}" method="POST" target="_blank" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Titre personnalisé du catalogue (Optionnel)</label>
                                <input type="text" name="catalog_title" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 outline-none focus:ring-1 focus:ring-gold-500" placeholder="ex: Recommandations pour Cabinet Médical X">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Sélectionnez les produits (au moins 1) *</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-[300px] overflow-y-auto pr-2 border border-gray-100 p-3 rounded-lg bg-gray-50/50">
                                @forelse($products as $product)
                                    <label class="flex items-center gap-3 p-2 bg-white rounded-md border border-gray-100 hover:border-gold-500/50 cursor-pointer select-none transition-colors">
                                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="rounded text-gold-500 focus:ring-gold-500">
                                        <div class="text-left">
                                            <span class="text-xs font-bold text-navy-900 block truncate max-w-[180px]">{{ $product->name }}</span>
                                            <span class="text-[10px] text-gray-500">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </label>
                                @empty
                                    <div class="col-span-full text-center text-xs text-gray-400 italic py-6">
                                        Aucun produit disponible.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <button type="submit" class="bg-navy-900 text-white hover:bg-gold-500 hover:text-navy-900 text-[10px] font-black uppercase tracking-widest py-3 px-6 rounded-lg shadow-md transition-all flex items-center gap-2">
                            <span>📄</span> Générer le catalogue (PDF)
                        </button>
                    </form>
                </div>

                <!-- TAB 3: SCHEDULER & EDITORIAL CALENDAR -->
                <div x-show="mainTab === 'scheduler'" class="bg-white rounded-xl border border-gray-150 p-6 lg:p-8 shadow-sm space-y-8" x-cloak>
                    <div class="border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">📅 Planificateur Réseaux & Calendrier</h2>
                        <p class="text-xs text-gray-400 italic">Planifiez vos posts promotionnels sur Facebook, Instagram, LinkedIn ou WhatsApp et gérez votre planning.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <!-- Scheduling Form (Left) -->
                        <div class="lg:col-span-5 bg-gray-50/50 border border-gray-150 p-5 rounded-xl space-y-4">
                            <h3 class="text-xs font-black text-navy-900 uppercase tracking-wider border-b border-gray-100 pb-2">Planifier un Post</h3>
                            <form action="{{ route('dashboard.partner.marketing.posts.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Titre Interne *</label>
                                    <input type="text" name="title" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: Relance Routeur Cisco">
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Texte de publication *</label>
                                    <textarea name="content" required rows="4" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="Contenu de la publication (intégrez votre lien d'affiliation...)"></textarea>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Plateformes Cibles *</label>
                                    <div class="flex flex-wrap gap-2 pt-1">
                                        <label class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold cursor-pointer hover:border-gold-500">
                                            <input type="checkbox" name="platforms[]" value="facebook" class="rounded text-gold-500">
                                            Facebook
                                        </label>
                                        <label class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold cursor-pointer hover:border-gold-500">
                                            <input type="checkbox" name="platforms[]" value="instagram" class="rounded text-gold-500">
                                            Instagram
                                        </label>
                                        <label class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold cursor-pointer hover:border-gold-500">
                                            <input type="checkbox" name="platforms[]" value="linkedin" class="rounded text-gold-500">
                                            LinkedIn
                                        </label>
                                        <label class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold cursor-pointer hover:border-gold-500">
                                            <input type="checkbox" name="platforms[]" value="whatsapp" class="rounded text-gold-500">
                                            WhatsApp
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Date & Heure programmées *</label>
                                    <input type="datetime-local" name="scheduled_at" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:ring-1 focus:ring-gold-500 outline-none">
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Produit Associé (Optionnel)</label>
                                    <select name="product_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white focus:ring-1 focus:ring-gold-500 outline-none">
                                        <option value="">-- Aucun --</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="w-full bg-navy-900 text-white hover:bg-gold-500 hover:text-navy-900 text-[10px] font-black uppercase tracking-widest py-3 rounded-lg shadow-md transition-all">
                                    📅 Programmer le post
                                </button>
                            </form>
                        </div>

                        <!-- Calendar and Posts list (Right) -->
                        <div class="lg:col-span-7 space-y-6">
                            <!-- Visual Editorial Calendar for current 7 days -->
                            <div class="bg-gray-50 border border-gray-150 rounded-xl p-4 space-y-3">
                                <h4 class="text-[10px] font-black text-navy-900 uppercase tracking-widest">Aperçu Hebdomadaire</h4>
                                <div class="grid grid-cols-7 gap-2">
                                    @for($i = 0; $i < 7; $i++)
                                        @php
                                            $day = now()->addDays($i);
                                            $postsForDay = $scheduledPosts->filter(function($p) use ($day) {
                                                return $p->scheduled_at->isSameDay($day);
                                            });
                                        @endphp
                                        <div class="bg-white border border-gray-200 rounded-lg p-2 flex flex-col items-center min-h-[70px] shadow-sm">
                                            <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $day->translatedFormat('D') }}</span>
                                            <span class="text-xs font-black text-navy-900">{{ $day->format('d') }}</span>
                                            @if($postsForDay->isNotEmpty())
                                                <span class="w-2.5 h-2.5 bg-gold-500 rounded-full mt-1.5 block animate-pulse" title="{{ $postsForDay->count() }} post(s) programmé(s)"></span>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <!-- List of Scheduled Posts -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-black text-navy-900 uppercase tracking-wider">Publications planifiées</h4>
                                <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                                    @forelse($scheduledPosts as $post)
                                        <div class="bg-white border border-gray-150 rounded-lg p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                                            <div class="space-y-1.5 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-black text-navy-900">{{ $post->title }}</span>
                                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded {{ $post->status === 'published' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-orange-50 text-orange-700 border border-orange-200' }}">
                                                        {{ $post->status === 'published' ? 'Publié' : 'En attente' }}
                                                    </span>
                                                </div>
                                                <p class="text-[10px] text-gray-500 truncate max-w-[400px]">{{ $post->content }}</p>
                                                <div class="flex flex-wrap items-center gap-2 text-[9px] font-bold text-gray-400">
                                                    <span>📅 {{ $post->scheduled_at->format('d/m/Y H:i') }}</span>
                                                    <span>•</span>
                                                    <span class="flex gap-1">
                                                        @foreach($post->platforms as $plat)
                                                            <span class="px-1.5 py-0.5 bg-gray-100 rounded text-navy-900 uppercase text-[8px] font-black">{{ $plat }}</span>
                                                        @endforeach
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2">
                                                <!-- Share Button (e.g. WhatsApp status) -->
                                                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->content) }}" target="_blank" class="px-2.5 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-md text-[9px] font-bold shadow-sm transition-colors">
                                                    💬 Partager
                                                </a>

                                                <form action="{{ route('dashboard.partner.marketing.posts.publish', $post->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 bg-navy-900 hover:bg-gold-500 hover:text-navy-900 text-white rounded-md text-[9px] font-bold shadow-sm transition-colors">
                                                        ✓ Publié
                                                    </button>
                                                </form>

                                                <form action="{{ route('dashboard.partner.marketing.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Supprimer ce post ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-6 text-xs text-gray-400 italic">Aucune publication programmée.</div>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- TAB 4: IA VIDEO SCRIPT STORYBOARD -->
                <div x-show="mainTab === 'video'" class="bg-white rounded-xl border border-gray-150 p-6 lg:p-8 shadow-sm space-y-6" x-data="{
                    products: {{ json_encode($products) }},
                    selectedProductId: '',
                    tone: 'energetic',
                    duration: '30',
                    instructions: '',
                    loading: false,
                    scenes: [],
                    speechSynth: window.speechSynthesis,
                    speechUtterance: null,
                    async generateScript() {
                        if (!this.selectedProductId) {
                            alert('Veuillez choisir un produit.');
                            return;
                        }
                        this.loading = true;
                        this.scenes = [];
                        try {
                            const response = await fetch('{{ route("dashboard.partner.marketing.video") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    product_id: this.selectedProductId,
                                    tone: this.tone,
                                    duration: this.duration,
                                    instructions: this.instructions
                                })
                            });
                            const data = await response.json();
                            if (data.success) {
                                this.scenes = data.scenes;
                            } else {
                                alert(data.message || 'Une erreur est survenue.');
                            }
                        } catch (err) {
                            console.error(err);
                            alert('Impossible de générer le script vidéo.');
                        } finally {
                            this.loading = false;
                        }
                    },
                    speakText(text) {
                        if (this.speechSynth.speaking) {
                            this.speechSynth.cancel();
                        }
                        this.speechUtterance = new SpeechSynthesisUtterance(text);
                        this.speechUtterance.lang = 'fr-FR';
                        this.speechSynth.speak(this.speechUtterance);
                    },
                    stopSpeak() {
                        this.speechSynth.cancel();
                    }
                }" x-cloak>
                    <div class="border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">🎬 Créateur Vidéo IA & Storyboard Vocal</h2>
                        <p class="text-xs text-gray-400 italic">Générez un storyboard vidéo complet pour vos Tiktok/Reels et écoutez le rendu vocal grâce à notre synthétiseur de voix IA.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <!-- Controls Form -->
                        <div class="lg:col-span-5 space-y-4 bg-gray-50/50 border border-gray-150 p-5 rounded-xl self-start">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Produit à mettre en scène *</label>
                                <select x-model="selectedProductId" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white focus:ring-1 focus:ring-gold-500 outline-none">
                                    <option value="">-- Sélectionnez un produit --</option>
                                    <template x-for="p in products" :key="p.id">
                                        <option :value="p.id" x-text="p.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Ton de la Voix Off *</label>
                                <select x-model="tone" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white focus:ring-1 focus:ring-gold-500 outline-none">
                                    <option value="energetic">🔥 Ultra Dynamique / TikTok</option>
                                    <option value="friendly">😊 Recommandation Amicale</option>
                                    <option value="corporate">💼 Corporate / Professionnel</option>
                                    <option value="urgent">⏰ Urgent / Offre Limitée</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Durée ciblée *</label>
                                <select x-model="duration" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white focus:ring-1 focus:ring-gold-500 outline-none">
                                    <option value="15">15 Secondes (Très court)</option>
                                    <option value="30">30 Secondes (Reel/TikTok)</option>
                                    <option value="60">60 Secondes (Présentation complète)</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Consignes spécifiques</label>
                                <textarea x-model="instructions" rows="3" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: Mentionne que la livraison est gratuite à Dakar..."></textarea>
                            </div>

                            <button @click="generateScript()" :disabled="loading" class="w-full bg-gold-500 hover:bg-navy-900 hover:text-white disabled:bg-gray-200 text-navy-900 text-[10px] font-black uppercase tracking-widest py-3 rounded-lg shadow-md transition-all flex items-center justify-center gap-2">
                                <span x-show="loading">⏳ Génération en cours...</span>
                                <span x-show="!loading">🎬 Générer le Storyboard</span>
                            </button>
                        </div>

                        <!-- Storyboard Output -->
                        <div class="lg:col-span-7 space-y-4">
                            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                <h3 class="text-xs font-black text-navy-900 uppercase tracking-wider">Aperçu du Storyboard</h3>
                                <button @click="stopSpeak()" class="text-[9px] font-black text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition-colors uppercase">
                                    ⏹️ Arrêter l'audio
                                </button>
                            </div>

                            <!-- Welcome state / Loader -->
                            <template x-if="loading">
                                <div class="flex flex-col items-center justify-center py-20 space-y-3">
                                    <div class="w-10 h-10 border-4 border-gold-500 border-t-transparent rounded-full animate-spin"></div>
                                    <p class="text-xs text-gray-400 font-bold italic animate-pulse">L'IA rédige votre script et structure le visuel...</p>
                                </div>
                            </template>

                            <template x-if="!loading && scenes.length === 0">
                                <div class="text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-200 text-xs text-gray-400 italic">
                                    Sélectionnez les paramètres à gauche puis lancez la génération.
                                </div>
                            </template>

                            <!-- Storyboard Grid -->
                            <template x-if="!loading && scenes.length > 0">
                                <div class="space-y-4">
                                    <template x-for="scene in scenes" :key="scene.num">
                                        <div class="bg-white border border-gray-150 rounded-xl p-4 shadow-sm flex flex-col md:flex-row gap-4 hover:border-gold-500 transition-colors">
                                            <div class="w-12 h-12 bg-navy-900 text-gold-500 rounded-lg flex flex-col items-center justify-center flex-shrink-0">
                                                <span class="text-[9px] font-bold uppercase">Scène</span>
                                                <span class="text-lg font-black" x-text="scene.num"></span>
                                            </div>
                                            <div class="space-y-3 flex-1">
                                                <div>
                                                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wider block">👁️ Visuel suggéré</span>
                                                    <p class="text-[10px] text-navy-900 leading-relaxed font-bold" x-text="scene.visual"></p>
                                                </div>
                                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 relative">
                                                    <span class="text-[8px] font-bold text-gold-600 uppercase tracking-wider block">🎙️ Voix Off</span>
                                                    <p class="text-xs text-gray-600 leading-relaxed italic pr-8" x-text="scene.voiceover"></p>
                                                    <button @click="speakText(scene.voiceover)" class="absolute right-3 top-3 text-xs bg-white hover:bg-gold-500 hover:text-navy-900 shadow-sm border border-gray-150 p-1.5 rounded-full transition-colors" title="Écouter la voix off">
                                                        🔊
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: RESOURCE LIBRARY (ASSETS) -->
                <div x-show="mainTab === 'library'" class="bg-white rounded-xl border border-gray-150 p-6 lg:p-8 shadow-sm space-y-6" x-cloak>
                    <div class="border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-black text-navy-900 uppercase tracking-tight italic">📦 Bibliothèque d'Assets Marketing</h2>
                        <p class="text-xs text-gray-400 italic">Téléchargez les ressources prêtes à l'emploi partagées par l'administration IT Holding.</p>
                    </div>

                    <div x-data="{ activeTab: 'image' }" class="space-y-6">
                        <div class="flex flex-wrap border-b border-gray-100 gap-2 pb-2">
                            <button @click="activeTab = 'image'" :class="activeTab === 'image' ? 'border-b-2 border-gold-500 text-navy-900 font-black' : 'text-gray-400'" class="text-[10px] uppercase tracking-wider py-2 px-3 font-bold transition-all flex items-center gap-1.5">
                                🖼️ Photos & Affiches
                            </button>
                            <button @click="activeTab = 'pdf'" :class="activeTab === 'pdf' ? 'border-b-2 border-gold-500 text-navy-900 font-black' : 'text-gray-400'" class="text-[10px] uppercase tracking-wider py-2 px-3 font-bold transition-all flex items-center gap-1.5">
                                📄 Flyers & PDF
                            </button>
                            <button @click="activeTab = 'document'" :class="activeTab === 'document' ? 'border-b-2 border-gold-500 text-navy-900 font-black' : 'text-gray-400'" class="text-[10px] uppercase tracking-wider py-2 px-3 font-bold transition-all flex items-center gap-1.5">
                                📝 Fiches & Textes
                            </button>
                            <button @click="activeTab = 'template'" :class="activeTab === 'template' ? 'border-b-2 border-gold-500 text-navy-900 font-black' : 'text-gray-400'" class="text-[10px] uppercase tracking-wider py-2 px-3 font-bold transition-all flex items-center gap-1.5">
                                🎨 Gabarits
                            </button>
                        </div>

                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            <!-- Image Tab -->
                            <div x-show="activeTab === 'image'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-cloak>
                                @forelse($groupedAssets['image'] as $asset)
                                    <div class="bg-white rounded-lg border border-gray-100 p-4 shadow-sm space-y-3 flex flex-col justify-between">
                                        <div class="space-y-1.5">
                                            <h4 class="text-xs font-bold text-navy-900">{{ $asset->title }}</h4>
                                            @if($asset->description)
                                                <p class="text-[10px] text-gray-500 leading-relaxed">{{ $asset->description }}</p>
                                            @endif
                                        </div>
                                        <a href="{{ asset('storage/' . $asset->file_path) }}" download class="w-full text-center py-2 bg-navy-50 hover:bg-gold-500 hover:text-navy-900 text-[10px] text-navy-900 font-black uppercase tracking-widest rounded-md shadow-sm transition-all">
                                            💾 Télécharger
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-xs text-gray-400 italic py-6">Aucune image partagée.</div>
                                @endforelse
                            </div>

                            <!-- PDF Tab -->
                            <div x-show="activeTab === 'pdf'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-cloak>
                                @forelse($groupedAssets['pdf'] as $asset)
                                    <div class="bg-white rounded-lg border border-gray-100 p-4 shadow-sm space-y-3 flex flex-col justify-between">
                                        <div class="space-y-1.5">
                                            <h4 class="text-xs font-bold text-navy-900">{{ $asset->title }}</h4>
                                            @if($asset->description)
                                                <p class="text-[10px] text-gray-500 leading-relaxed">{{ $asset->description }}</p>
                                            @endif
                                        </div>
                                        <a href="{{ asset('storage/' . $asset->file_path) }}" download class="w-full text-center py-2 bg-navy-50 hover:bg-gold-500 hover:text-navy-900 text-[10px] text-navy-900 font-black uppercase tracking-widest rounded-md shadow-sm transition-all">
                                            💾 Télécharger
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-xs text-gray-400 italic py-6">Aucun PDF disponible.</div>
                                @endforelse
                            </div>

                            <!-- Document Tab -->
                            <div x-show="activeTab === 'document'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-cloak>
                                @forelse($groupedAssets['document'] as $asset)
                                    <div class="bg-white rounded-lg border border-gray-150 p-4 shadow-sm space-y-3 flex flex-col justify-between">
                                        <div class="space-y-1.5">
                                            <h4 class="text-xs font-bold text-navy-900">{{ $asset->title }}</h4>
                                            @if($asset->description)
                                                <p class="text-[10px] text-gray-500 leading-relaxed">{{ $asset->description }}</p>
                                            @endif
                                        </div>
                                        <a href="{{ asset('storage/' . $asset->file_path) }}" download class="w-full text-center py-2 bg-navy-50 hover:bg-gold-500 hover:text-navy-900 text-[10px] text-navy-900 font-black uppercase tracking-widest rounded-md shadow-sm transition-all">
                                            💾 Télécharger
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-xs text-gray-400 italic py-6">Aucun texte / document disponible.</div>
                                @endforelse
                            </div>

                            <!-- Template Tab -->
                            <div x-show="activeTab === 'template'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-cloak>
                                @forelse($groupedAssets['template'] as $asset)
                                    <div class="bg-white rounded-lg border border-gray-100 p-4 shadow-sm space-y-3 flex flex-col justify-between">
                                        <div class="space-y-1.5">
                                            <h4 class="text-xs font-bold text-navy-900">{{ $asset->title }}</h4>
                                            @if($asset->description)
                                                <p class="text-[10px] text-gray-500 leading-relaxed">{{ $asset->description }}</p>
                                            @endif
                                        </div>
                                        <a href="{{ asset('storage/' . $asset->file_path) }}" download class="w-full text-center py-2 bg-navy-50 hover:bg-gold-500 hover:text-navy-900 text-[10px] text-navy-900 font-black uppercase tracking-widest rounded-md shadow-sm transition-all">
                                            💾 Télécharger
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-xs text-gray-400 italic py-6">Aucun gabarit partagé.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>

<!-- html2canvas to convert div to png inside user browser -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfYLjYEXyyp8AM5tcJHUROms53S35UEMgiTXv7YRT5bGHC9WpfM1hCji5V85gWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function downloadPoster() {
        const preview = document.getElementById('poster-preview');
        if (!preview) return;

        html2canvas(preview, {
            scale: 2, 
            useCORS: true,
            backgroundColor: null
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'affiche_produit_' + Date.now() + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }).catch(err => {
            console.error('Error during poster render: ', err);
            alert('Impossible de générer le fichier image. Veuillez réessayer.');
        });
    }
</script>
@endsection
