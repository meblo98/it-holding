@extends('layouts.app')

@section('title', 'Assistant IA Commercial - ' . config('app.name'))

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
                <span class="text-navy-900 font-bold uppercase tracking-wider">Assistant IA Commercial</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            @include('layouts.client_sidebar')

            <!-- Main Partner CRM Content -->
            <main class="flex-1 min-w-0 space-y-8">
                <!-- Sub navigation tabs -->
                <div class="flex overflow-x-auto border-b border-gray-200 bg-white rounded-xl p-2 shadow-sm gap-2 scrollbar-none whitespace-nowrap">
                    <a href="{{ route('dashboard.partner') }}" class="flex-shrink-0 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>📊</span> Tableau de bord
                    </a>
                    <a href="{{ route('dashboard.partner.crm') }}" class="flex-shrink-0 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>👥</span> CRM & Prospects
                    </a>
                    <a href="{{ route('dashboard.partner.assistant') }}" class="flex-shrink-0 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors bg-navy-900 text-white flex items-center gap-2">
                        <span>🤖</span> Assistant IA
                    </a>
                    <a href="{{ route('dashboard.partner.marketing') }}" class="flex-shrink-0 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors text-gray-500 hover:text-navy-900 hover:bg-gray-50 flex items-center gap-2">
                        <span>📢</span> Studio Marketing
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Generation Form -->
                    <div class="bg-white rounded-xl border border-gray-150 p-6 shadow-sm space-y-6 self-start">
                        <div>
                            <h2 class="text-sm font-black text-navy-900 uppercase tracking-wider">Paramètres de génération</h2>
                            <p class="text-[11px] text-gray-400 italic">Configurez l'IA pour obtenir un contenu sur-mesure.</p>
                        </div>

                        <form id="assistant-form" class="space-y-4">
                            @csrf
                            <!-- Product Selection -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Produit à promouvoir</label>
                                <select name="product_id" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none bg-white">
                                    <option value="">-- Aucun produit particulier --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ number_format($product->price, 0, ',', ' ') }} F)</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Objective Selection -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Objectif Commercial</label>
                                <select name="objective" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none bg-white">
                                    <option value="sell">Rédiger un argumentaire de vente direct</option>
                                    <option value="inform">Donner des conseils informatifs / Astuces</option>
                                    <option value="promote">Créer une publication de promotion produit</option>
                                    <option value="discount">Annoncer une promotion avec mon code (-5%)</option>
                                    <option value="lead">Générer des prospects (curiosité)</option>
                                    <option value="objection">Répondre à une objection client</option>
                                    <option value="whatsapp_followup">Rédiger un message de relance WhatsApp</option>
                                </select>
                            </div>

                            <!-- Social Network Selection -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Format / Réseau Cible</label>
                                <select name="network" class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none bg-white">
                                    <option value="whatsapp">💬 WhatsApp (Court, Emojis, Direct)</option>
                                    <option value="facebook">📘 Facebook (Engageant, Emojis, Long)</option>
                                    <option value="instagram">📸 Instagram (Phrases courtes, Hashtags)</option>
                                    <option value="linkedin">💼 LinkedIn (Professionnel, B2B)</option>
                                    <option value="tiktok">🎵 TikTok / Reel (Script vidéo 30s)</option>
                                    <option value="email">✉️ E-mail Commercial (Objet + Corps)</option>
                                </select>
                            </div>

                            <!-- Extra instructions -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-navy-900 uppercase tracking-wider block">Instructions complémentaires *</label>
                                <textarea name="message" rows="4" required class="w-full text-xs border border-gray-200 rounded-lg p-2.5 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none" placeholder="ex: Rédige un message pour convaincre des PME de s'équiper avec ce routeur pour stabiliser leur connexion internet."></textarea>
                            </div>

                            <button type="submit" id="submit-btn" class="w-full bg-navy-900 text-white hover:bg-gold-500 hover:text-navy-900 text-[10px] font-black uppercase tracking-widest py-3 rounded-lg shadow-md transition-all flex items-center justify-center gap-2">
                                <span>🚀</span> Générer avec l'IA
                            </button>
                        </form>
                    </div>

                    <!-- Right: Chat Workspace -->
                    <div class="lg:col-span-2 flex flex-col h-[550px] bg-white rounded-xl border border-gray-150 overflow-hidden shadow-sm">
                        <!-- Chat Header -->
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-ping"></span>
                                <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest italic">Mon Studio de Rédaction Commerciale</h3>
                            </div>
                            <button onclick="clearChat()" class="text-[10px] font-bold text-gray-400 hover:text-navy-900 transition-colors uppercase">
                                Effacer la discussion
                            </button>
                        </div>

                        <!-- Chat Messages Container -->
                        <div id="chat-container" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50/50">
                            <!-- Welcome bubble -->
                            <div class="flex gap-3 max-w-[85%]">
                                <div class="w-8 h-8 rounded-full bg-navy-900 text-gold-500 flex items-center justify-center flex-shrink-0 font-bold text-xs shadow">
                                    🤖
                                </div>
                                <div class="bg-white rounded-r-xl rounded-bl-xl p-4 border border-gray-100 shadow-sm space-y-2">
                                    <p class="text-xs text-navy-900 leading-relaxed font-medium">
                                        Bonjour <strong>{{ $user->name }}</strong> ! Je suis votre assistant commercial personnel. 
                                    </p>
                                    <p class="text-[11px] text-gray-500 leading-relaxed italic">
                                        Sélectionnez un produit à promouvoir ou configurez vos objectifs à gauche, puis cliquez sur <strong>Générer avec l'IA</strong>. Je vais rédiger des publications et argumentaires percutants intégrant automatiquement votre lien d'affiliation personnel !
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat input placeholder or inline quick help -->
                        <div class="p-3 bg-gray-50 border-t border-gray-100 text-center text-[9px] font-bold text-gray-400 uppercase tracking-wider">
                            Alimenté par l'Intelligence Artificielle de Google Gemini
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('assistant-form');
    const submitBtn = document.getElementById('submit-btn');
    const chatContainer = document.getElementById('chat-container');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const userMsg = form.querySelector('textarea[name="message"]').value.trim();

        if (!userMsg) return;

        // Disable button & show spinner
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>⏳</span> Génération en cours...';

        // Append User message to Chat
        appendUserMessage(userMsg);

        // Append Loading AI bubble
        const loadingId = appendLoadingBubble();

        try {
            const response = await fetch('{{ route("dashboard.partner.assistant.chat") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            // Remove loading bubble
            document.getElementById(loadingId).remove();

            if (data.success) {
                appendAiMessage(data.reply);
            } else {
                appendAiMessage('❌ Une erreur est survenue : ' + (data.message || 'Erreur inconnue.'));
            }
        } catch (err) {
            console.error(err);
            // Remove loading bubble
            document.getElementById(loadingId).remove();
            appendAiMessage('❌ Impossible de joindre le serveur. Veuillez vérifier votre connexion.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>🚀</span> Générer avec l\'IA';
        }
    });

    function appendUserMessage(text) {
        const bubble = document.createElement('div');
        bubble.className = 'flex gap-3 max-w-[85%] ml-auto justify-end';
        bubble.innerHTML = `
            <div class="bg-navy-900 text-white rounded-l-xl rounded-br-xl p-4 shadow-sm">
                <p class="text-xs leading-relaxed">${escapeHtml(text)}</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-gold-500 text-navy-900 flex items-center justify-center flex-shrink-0 font-bold text-xs shadow">
                👤
            </div>
        `;
        chatContainer.appendChild(bubble);
        scrollToBottom();
    }

    function appendLoadingBubble() {
        const id = 'loading-' + Date.now();
        const bubble = document.createElement('div');
        bubble.id = id;
        bubble.className = 'flex gap-3 max-w-[85%]';
        bubble.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-navy-900 text-gold-500 flex items-center justify-center flex-shrink-0 font-bold text-xs shadow">
                🤖
            </div>
            <div class="bg-white rounded-r-xl rounded-bl-xl p-4 border border-gray-100 shadow-sm flex items-center gap-1.5">
                <div class="w-2 h-2 bg-navy-900 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-navy-900 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                <div class="w-2 h-2 bg-navy-900 rounded-full animate-bounce [animation-delay:0.4s]"></div>
            </div>
        `;
        chatContainer.appendChild(bubble);
        scrollToBottom();
        return id;
    }

    function appendAiMessage(text) {
        const bubble = document.createElement('div');
        bubble.className = 'flex gap-3 max-w-[85%]';
        
        // Format text with simple markdown conversions
        let formattedText = escapeHtml(text)
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');

        const copyTextId = 'copy-' + Date.now();

        bubble.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-navy-900 text-gold-500 flex items-center justify-center flex-shrink-0 font-bold text-xs shadow">
                🤖
            </div>
            <div class="bg-white rounded-r-xl rounded-bl-xl p-4 border border-gray-100 shadow-sm space-y-3 flex-1">
                <div class="text-xs text-navy-900 leading-relaxed" id="${copyTextId}">${formattedText}</div>
                <div class="flex justify-end pt-2 border-t border-gray-50">
                    <button onclick="copyGeneratedText('${copyTextId}')" class="text-[9px] font-black text-navy-900 bg-gold-500 hover:bg-navy-900 hover:text-white transition-all uppercase tracking-widest px-3 py-1.5 rounded flex items-center gap-1 shadow-sm">
                        📋 Copier le texte
                    </button>
                </div>
            </div>
        `;
        chatContainer.appendChild(bubble);
        scrollToBottom();
    }

    function copyGeneratedText(elementId) {
        // We read text content, replace <br> with newlines
        const el = document.getElementById(elementId);
        const text = el.innerText || el.textContent;
        navigator.clipboard.writeText(text).then(function() {
            alert('Texte commercial copié dans le presse-papiers !');
        }, function(err) {
            console.error('Erreur de copie : ', err);
        });
    }

    function clearChat() {
        if (confirm('Voulez-vous effacer toute la discussion ?')) {
            chatContainer.innerHTML = `
                <div class="flex gap-3 max-w-[85%]">
                    <div class="w-8 h-8 rounded-full bg-navy-900 text-gold-500 flex items-center justify-center flex-shrink-0 font-bold text-xs shadow">
                        🤖
                    </div>
                    <div class="bg-white rounded-r-xl rounded-bl-xl p-4 border border-gray-100 shadow-sm space-y-2">
                        <p class="text-xs text-navy-900 leading-relaxed font-medium">
                            Discussion effacée. Comment puis-je vous aider aujourd'hui ?
                        </p>
                    </div>
                </div>
            `;
        }
    }

    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function escapeHtml(string) {
        return String(string).replace(/[&<>"']/g, function (s) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[s];
        });
    }
</script>
@endsection
