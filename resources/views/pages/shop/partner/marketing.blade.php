@extends('layouts.app')

@section('title', 'Studio Marketing Partenaire - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen" x-data="{ mainTab: 'poster', scheduleTitle: '', scheduleContent: '', scheduleProductId: '' }">
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
            <main class="flex-1 min-w-0 space-y-8">
                <!-- Sub navigation tabs -->
                <div class="flex overflow-x-auto border-b border-gray-200 bg-white rounded-xl p-2 shadow-sm gap-2 scrollbar-none whitespace-nowrap">
                    <a href="{{ route('dashboard.partner') }}" class="flex-shrink-0 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>📊</span> Tableau de bord
                    </a>
                    <a href="{{ route('dashboard.partner.crm') }}" class="flex-shrink-0 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>👥</span> CRM & Prospects
                    </a>
                    <a href="{{ route('dashboard.partner.assistant') }}" class="flex-shrink-0 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>🤖</span> Assistant IA
                    </a>
                    <a href="{{ route('dashboard.partner.marketing') }}" class="flex-shrink-0 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors bg-navy-900 text-white flex items-center gap-2">
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
                <div class="flex overflow-x-auto border-b border-gray-200 bg-white rounded-xl p-2 shadow-sm gap-2 scrollbar-none whitespace-nowrap">
                    <button @click="mainTab = 'poster'" :class="mainTab === 'poster' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="flex-shrink-0 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
                        🎨 Affiches
                    </button>
                    <button @click="mainTab = 'catalog'" :class="mainTab === 'catalog' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="flex-shrink-0 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
                        📋 Catalogue PDF
                    </button>
                    <button @click="mainTab = 'scheduler'" :class="mainTab === 'scheduler' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="flex-shrink-0 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
                        📅 Planificateur Réseaux
                    </button>
                    <button @click="mainTab = 'video'" :class="mainTab === 'video' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="flex-shrink-0 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
                        🎬 Créateur Vidéo IA
                    </button>
                    <button @click="mainTab = 'library'" :class="mainTab === 'library' ? 'bg-navy-900 text-white' : 'text-gray-500 hover:bg-gray-50'" class="flex-shrink-0 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5">
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
                    useAIImage: false,
                    aiImageUrl: '',
                    aiImageLoading: false,
                    aiImagePrompt: '',
                    updateProduct() {
                        this.selectedProduct = this.products.find(p => p.id == this.selectedProductId) || null;
                        this.useAIImage = false;
                        this.aiImageUrl = '';
                        if(this.selectedProduct) {
                            this.customPrice = this.selectedProduct.price;
                        } else {
                            this.customPrice = '';
                        }
                    },
                    async generateAIImage() {
                        if (!this.selectedProductId) {
                            alert('Veuillez d\'abord sélectionner un produit.');
                            return;
                        }
                        this.aiImageLoading = true;
                        this.aiImageUrl = '';
                        try {
                            const response = await fetch('{{ route("dashboard.partner.marketing.poster.ai") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    product_id: this.selectedProductId
                                })
                            });
                            const data = await response.json();
                            if (data.success) {
                                this.aiImageUrl = '{{ route("dashboard.partner.marketing.proxy.image") }}?url=' + encodeURIComponent(data.image_url);
                                this.aiImagePrompt = data.prompt;
                                this.useAIImage = true;
                            } else {
                                alert(data.message || 'La génération de l\'image a échoué.');
                            }
                        } catch (err) {
                            console.error(err);
                            alert('Erreur lors de la génération de l\'image.');
                        } finally {
                            this.aiImageLoading = false;
                        }
                    },
                    sendToPlanner() {
                        this.$parent.scheduleTitle = 'Promo - ' + this.selectedProduct.name;
                        this.$parent.scheduleContent = '🔥 Offre Spéciale chez IT Holding !\n\nDécouvrez le ' + this.selectedProduct.name + ' au tarif exclusif de ' + Number(this.customPrice).toLocaleString('fr-FR') + ' FCFA !\n\n👉 Commandez directement avec mon lien de recommandation :\n' + '{{ route('home') }}' + '?ref=' + this.partnerCode + '\n\n💡 Utilisez mon code promo *' + this.partnerCode + '* pour bénéficier de 5% de réduction supplémentaire !';
                        this.$parent.scheduleProductId = this.selectedProductId;
                        this.$parent.mainTab = 'scheduler';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
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

                            <div class="space-y-1 pt-1" x-show="selectedProduct">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Type d'image</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button @click="useAIImage = false" :class="!useAIImage ? 'bg-navy-900 text-white' : 'bg-gray-100 hover:bg-gray-200 text-navy-900'" class="text-[9px] font-black uppercase py-2 px-3 rounded-lg transition-all text-center">
                                        📷 Photo Produit
                                    </button>
                                    <button @click="useAIImage = true; if(!aiImageUrl) generateAIImage()" :class="useAIImage ? 'bg-gold-500 text-navy-900 font-bold' : 'bg-gray-100 hover:bg-gray-200 text-navy-900'" class="text-[9px] font-black uppercase py-2 px-3 rounded-lg transition-all text-center flex items-center justify-center gap-1">
                                        <span x-show="aiImageLoading" class="inline-block w-2.5 h-2.5 border-2 border-navy-950 border-t-transparent rounded-full animate-spin"></span>
                                        <span x-text="aiImageLoading ? 'IA...' : '✨ Générer par IA'"></span>
                                    </button>
                                </div>
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

                            <!-- Social Network Hub (visible only if product is selected) -->
                            <div x-show="selectedProduct" class="border-t border-gray-150 pt-4 space-y-3 mt-4" x-transition>
                                <h4 class="text-[10px] font-black text-navy-900 uppercase tracking-widest flex items-center gap-1.5">
                                    <span>📢</span> Hub de Diffusion Réseaux
                                </h4>
                                <p class="text-[10px] text-gray-400 italic">
                                    Partagez ou planifiez ce visuel sur vos réseaux sociaux.
                                </p>
                                
                                <div class="grid grid-cols-2 gap-2">
                                    <!-- WhatsApp -->
                                    <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent('🔥 Offre Spéciale chez IT Holding ! Découvrez le ' + selectedProduct.name + ' au prix spécial de ' + Number(customPrice).toLocaleString('fr-FR') + ' F ! Utilisez mon Code Promo *' + partnerCode + '* pour obtenir 5% de réduction immédiate. Commandez ici : ' + '{{ route('home') }}' + '?ref=' + partnerCode)" 
                                       target="_blank" 
                                       class="px-2.5 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-[9px] font-bold text-center flex items-center justify-center gap-1 transition-colors shadow-sm">
                                        💬 WhatsApp
                                    </a>
                                    <!-- Facebook -->
                                    <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent('{{ route('home') }}' + '?ref=' + partnerCode)" 
                                       target="_blank" 
                                       class="px-2.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[9px] font-bold text-center flex items-center justify-center gap-1 transition-colors shadow-sm">
                                        📘 Facebook
                                    </a>
                                    <!-- LinkedIn -->
                                    <a :href="'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent('{{ route('home') }}' + '?ref=' + partnerCode)" 
                                       target="_blank" 
                                       class="px-2.5 py-2 bg-blue-800 hover:bg-blue-900 text-white rounded-lg text-[9px] font-bold text-center flex items-center justify-center gap-1 transition-colors shadow-sm">
                                        💼 LinkedIn
                                    </a>
                                    <!-- X / Twitter -->
                                    <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent('🔥 Offre Spéciale chez IT Holding ! Découvrez le ' + selectedProduct.name + ' au prix de ' + Number(customPrice).toLocaleString('fr-FR') + ' F ! Code Promo: ' + partnerCode) + '&url=' + encodeURIComponent('{{ route('home') }}' + '?ref=' + partnerCode)" 
                                       target="_blank" 
                                       class="px-2.5 py-2 bg-slate-900 hover:bg-black text-white rounded-lg text-[9px] font-bold text-center flex items-center justify-center gap-1 transition-colors shadow-sm">
                                        🐦 X / Twitter
                                    </a>
                                </div>

                                <button @click="sendToPlanner()" class="w-full bg-navy-900 hover:bg-gold-500 hover:text-navy-900 text-white text-[9px] font-black uppercase tracking-widest py-3 rounded-lg shadow-sm transition-all flex items-center justify-center gap-1.5 mt-2">
                                    📅 Planifier dans le Calendrier
                                </button>
                            </div>
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
                                        <!-- Case 1: AI Generated Image -->
                                        <template x-if="useAIImage && aiImageUrl">
                                            <img :src="aiImageUrl" class="max-w-full max-h-full object-cover rounded animate-fade-in" crossOrigin="anonymous">
                                        </template>
                                        <!-- Case 2: Loading State for AI Image -->
                                        <template x-if="useAIImage && aiImageLoading">
                                            <div class="flex flex-col items-center justify-center space-y-1.5">
                                                <div class="w-5 h-5 border-2 border-gold-500 border-t-transparent rounded-full animate-spin"></div>
                                                <span class="text-[8px] text-gray-400 animate-pulse uppercase font-black">IA en cours...</span>
                                            </div>
                                        </template>
                                        <!-- Case 3: Standard Product Photo -->
                                        <template x-if="!useAIImage && selectedProduct && (selectedProduct.image || (selectedProduct.images && selectedProduct.images.length > 0))">
                                            <img :src="'/storage/' + (selectedProduct.image || selectedProduct.images[0].path)" class="max-w-full max-h-full object-contain mix-blend-normal">
                                        </template>
                                        <!-- Case 4: No image or no product -->
                                        <template x-if="(!useAIImage && !selectedProduct) || (!useAIImage && !selectedProduct.image && (!selectedProduct.images || selectedProduct.images.length === 0))">
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
                                    <input type="text" name="title" x-model="scheduleTitle" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: Relance Routeur Cisco">
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Texte de publication *</label>
                                    <textarea name="content" x-model="scheduleContent" required rows="4" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="Contenu de la publication (intégrez votre lien d'affiliation...)"></textarea>
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
                                    <select name="product_id" x-model="scheduleProductId" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white focus:ring-1 focus:ring-gold-500 outline-none">
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
                    selectedProduct: null,
                    tone: 'energetic',
                    duration: '30',
                    instructions: '',
                    loading: false,
                    scenes: [],
                    speechSynth: window.speechSynthesis,
                    speechUtterance: null,
                    isPlayingPreview: false,
                    currentPreviewScene: 0,
                    updateProduct() {
                        this.selectedProduct = this.products.find(p => p.id == this.selectedProductId) || null;
                    },
                    async generateScript() {
                        if (!this.selectedProductId) {
                            alert('Veuillez choisir un produit.');
                            return;
                        }
                        this.loading = true;
                        this.scenes = [];
                        this.stopPreview();
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
                    },
                    startPreview() {
                        if (this.scenes.length === 0) return;
                        this.isPlayingPreview = true;
                        this.currentPreviewScene = 0;
                        this.playScene(0);
                    },
                    playScene(index) {
                        if (index < 0 || index >= this.scenes.length) {
                            this.stopPreview();
                            return;
                        }
                        this.currentPreviewScene = index;
                        if (this.speechSynth.speaking) {
                            this.speechSynth.cancel();
                        }
                        const scene = this.scenes[index];
                        this.speechUtterance = new SpeechSynthesisUtterance(scene.voiceover);
                        this.speechUtterance.lang = 'fr-FR';
                        this.speechUtterance.onend = () => {
                            if (this.isPlayingPreview && this.currentPreviewScene === index) {
                                this.playScene(index + 1);
                            }
                        };
                        this.speechSynth.speak(this.speechUtterance);
                    },
                    stopPreview() {
                        this.isPlayingPreview = false;
                        this.speechSynth.cancel();
                        this.currentPreviewScene = 0;
                    },
                    copyScriptToClipboard() {
                        if (this.scenes.length === 0) return;
                        let text = 'STORYBOARD DE CAMPAGNE VIDÉO\n\n';
                        this.scenes.forEach(s => {
                            text += 'SCÈNE ' + s.num + '\n';
                            text += '• Visuel : ' + s.visual + '\n';
                            text += '• Voix Off : ' + s.voiceover + '\n\n';
                        });
                        navigator.clipboard.writeText(text).then(() => {
                            alert('Storyboard copié dans le presse-papiers ! Collez-le dans CapCut pour monter votre vidéo.');
                        }).catch(err => {
                            console.error('Erreur lors de la copie', err);
                        });
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
                                <select x-model="selectedProductId" @change="updateProduct()" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 bg-white focus:ring-1 focus:ring-gold-500 outline-none">
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
                            <div class="flex flex-wrap gap-2 items-center justify-between border-b border-gray-100 pb-2">
                                <h3 class="text-xs font-black text-navy-900 uppercase tracking-wider">Aperçu du Storyboard</h3>
                                <div class="flex gap-2">
                                    <template x-if="scenes.length > 0">
                                        <div class="flex gap-2">
                                            <button @click="startPreview()" class="text-[9px] font-black text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded transition-colors uppercase flex items-center gap-1 shadow-sm">
                                                ▶️ Simuler Vidéo
                                            </button>
                                            <button @click="copyScriptToClipboard()" class="text-[9px] font-black text-navy-900 bg-gold-100 hover:bg-gold-200 px-3 py-1 rounded transition-colors uppercase flex items-center gap-1">
                                                📋 Copier pour CapCut
                                            </button>
                                        </div>
                                    </template>
                                    <button @click="stopSpeak()" class="text-[9px] font-black text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition-colors uppercase">
                                        ⏹️ Arrêter l'audio
                                    </button>
                                </div>
                            </div>

                            <!-- Simulated Video Player -->
                            <div x-show="isPlayingPreview" class="bg-slate-950 rounded-xl overflow-hidden shadow-2xl relative border-2 border-gold-500/20 max-w-[320px] mx-auto flex flex-col justify-between p-4 h-[420px] transition-all duration-300" x-transition>
                                <!-- Player Header -->
                                <div class="flex justify-between items-center z-10 text-white">
                                    <span class="text-[8px] font-black tracking-widest text-gold-400">LECTEUR SIMULATEUR IA (OMNI)</span>
                                    <button @click="stopPreview()" class="text-[10px] text-gray-400 hover:text-white font-black bg-white/10 hover:bg-white/20 px-2 py-0.5 rounded transition-colors">✕ Fermer</button>
                                </div>

                                <!-- Dynamic Visual Area -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center select-none overflow-hidden">
                                    <!-- Zoom/Pan Visual Background -->
                                    <div class="absolute inset-0 bg-gradient-to-b from-slate-900 via-navy-950 to-slate-900 opacity-60"></div>
                                    
                                    <!-- Product image with transition -->
                                    <div class="w-32 h-32 rounded-xl bg-white/10 flex items-center justify-center p-3 relative overflow-hidden shadow-inner backdrop-blur-sm border border-white/10 z-10 transition-transform duration-1000 transform scale-110">
                                        <template x-if="selectedProduct && (selectedProduct.image || (selectedProduct.images && selectedProduct.images.length > 0))">
                                            <img :src="'/storage/' + (selectedProduct.image || selectedProduct.images[0].path)" class="max-w-full max-h-full object-contain">
                                        </template>
                                        <template x-if="!selectedProduct || (!selectedProduct.image && (!selectedProduct.images || selectedProduct.images.length === 0))">
                                            <span class="text-4xl">📦</span>
                                        </template>
                                    </div>
                                    
                                    <!-- Visual instructions overview -->
                                    <p class="text-[9px] text-gold-400 font-black uppercase tracking-widest mt-4 z-10 max-w-[200px]" x-text="'Scène ' + (currentPreviewScene + 1)"></p>
                                    <p class="text-[10px] text-white italic leading-relaxed mt-1 z-10 max-w-[240px] font-medium" x-text="scenes[currentPreviewScene] ? scenes[currentPreviewScene].visual : ''"></p>
                                </div>

                                <!-- Captions / Voiceover subtitles overlay -->
                                <div class="z-10 bg-black/75 backdrop-blur-md border border-white/10 p-3 rounded-lg text-center space-y-2 mt-auto">
                                    <p class="text-xs text-white leading-relaxed font-black" x-text="scenes[currentPreviewScene] ? scenes[currentPreviewScene].voiceover : ''"></p>
                                    
                                    <!-- Playback Controls inside player -->
                                    <div class="flex justify-center items-center gap-3 pt-1">
                                        <button @click="playScene(Math.max(0, currentPreviewScene - 1))" class="text-xs hover:text-gold-500 text-gray-300">⏮️</button>
                                        <button @click="stopPreview()" class="text-xs hover:text-red-500 text-gray-300">⏹️</button>
                                        <button @click="playScene(Math.min(scenes.length - 1, currentPreviewScene + 1))" class="text-xs hover:text-gold-500 text-gray-300">⏭️</button>
                                    </div>
                                </div>
                            </div>

                            <!-- CapCut Integration Helper -->
                            <template x-if="scenes.length > 0">
                                <div class="bg-gradient-to-r from-navy-900 to-slate-900 border border-gold-500/30 rounded-xl p-4 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-md">
                                    <div class="space-y-1">
                                        <h4 class="text-xs font-black text-gold-400 uppercase tracking-wider flex items-center gap-1.5">
                                            🎬 Monter votre vidéo avec CapCut
                                        </h4>
                                        <p class="text-[10px] text-gray-300 leading-relaxed max-w-md">
                                            Copiez ce script et utilisez l'éditeur en ligne de <strong>CapCut</strong> pour assembler vos scènes, générer des sous-titres automatiques et appliquer une voix off professionnelle.
                                        </p>
                                    </div>
                                    <a href="https://www.capcut.com/editor" target="_blank" class="bg-gold-500 hover:bg-white text-navy-950 text-[9px] font-black uppercase tracking-wider px-3.5 py-2.5 rounded-lg transition-colors flex items-center gap-1.5 self-stretch md:self-auto justify-center">
                                        Créer sur CapCut ↗
                                    </a>
                                </div>
                            </template>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" referrerpolicy="no-referrer"></script>
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
