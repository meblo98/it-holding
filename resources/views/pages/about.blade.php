@extends('layouts.app')

@section('title', 'À Propos - IT-Holding Sénégal')
@section('meta_description', 'Découvrez IT-Holding, votre expert en solutions technologiques au Sénégal. Notre mission est d\'accompagner les entreprises dans leur transformation numérique.')
@section('meta_keywords', 'expertise informatique Sénégal, IT-Holding histoire, solutions technologiques Dakar, entreprise informatique Sénégal')

@section('content')
<div class="bg-white min-h-screen font-sans">
    <!-- Breadcrumb -->
    <div class="bg-gray-50 border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center">
                <a href="{{ route('home') }}" class="hover:text-navy-900 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold">À Propos</span>
            </nav>
        </div>
    </div>

    <!-- Section 1: Hero Section (Dark gradient with stats) -->
    <section class="relative bg-gradient-to-br from-navy-700 via-navy-800 to-navy-950 text-white overflow-hidden py-16 lg:py-24">
        <!-- Abstract background glow elements -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-navy-500/20 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-6">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        À propos d'<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-gold-600">IT Holding</span> Services
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-300 max-w-xl font-medium leading-relaxed">
                        L'écosystème qui connecte la technologie, les compétences et les opportunités.
                    </p>
                    <div class="flex flex-wrap gap-3 text-xs sm:text-sm font-semibold text-gray-400">
                        <span class="flex items-center gap-1.5 bg-white/5 px-3 py-1.5 rounded-full border border-white/10">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Informatique
                        </span>
                        <span class="flex items-center gap-1.5 bg-white/5 px-3 py-1.5 rounded-full border border-white/10">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Numérique
                        </span>
                        <span class="flex items-center gap-1.5 bg-white/5 px-3 py-1.5 rounded-full border border-white/10">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Innovation
                        </span>
                        <span class="flex items-center gap-1.5 bg-white/5 px-3 py-1.5 rounded-full border border-white/10">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Partenariats
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-4 pt-2">
                        <a href="#vision" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-lg shadow-sm text-navy-900 bg-gold-500 hover:bg-gold-600 transition-colors">
                            Découvrir notre vision
                        </a>
                        <a href="{{ route('contact.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-white hover:bg-white/10 text-sm font-bold rounded-lg text-white transition-colors">
                            Nous contacter
                        </a>
                    </div>
                </div>

                <!-- Right image -->
                <div class="lg:col-span-5 relative">
                    <div class="relative rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/40 via-transparent to-transparent z-10"></div>
                        <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=800&q=80" 
                             alt="Collaborateurs IT Holding" class="w-full h-80 lg:h-96 object-cover transform hover:scale-105 transition-transform duration-700">
                    </div>
                </div>
            </div>

            <!-- Stats overlay cards row -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach([
                    ['num' => '100+', 'label' => 'Projets réalisés', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-gold-500 bg-gold-500/10'],
                    ['num' => '50+', 'label' => 'Partenaires', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'text-gold-500 bg-gold-500/10'],
                    ['num' => '30+', 'label' => 'Experts & Freelances', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'text-gold-500 bg-gold-500/10'],
                    ['num' => '100%', 'label' => 'Engagement', 'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-gold-500 bg-gold-500/10']
                ] as $stat)
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-5 hover:border-gold-500/30 hover:bg-white/10 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $stat['color'] }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white group-hover:text-gold-400 transition-colors">{{ $stat['num'] }}</div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $stat['label'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section 2: Notre Histoire & Notre Modèle -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
            <!-- Left: Notre Histoire -->
            <div class="lg:col-span-5 space-y-6">
                <span class="bg-gold-100 text-gold-800 text-[10px] font-black uppercase tracking-[0.2em] px-3 py-1.5 rounded-sm">NOTRE PARCOURS</span>
                <h2 class="text-3xl font-black text-navy-900 uppercase italic tracking-tight">Notre Histoire</h2>
                <div class="space-y-4 text-gray-600 text-sm leading-relaxed">
                    <p>
                        <strong class="text-navy-900 font-semibold">IT Holding Services</strong> est née d'une idée simple : regrouper tous les domaines de l'informatique et du numérique pour offrir aux entreprises, institutions et particuliers des solutions complètes, innovantes et adaptées à leurs besoins.
                    </p>
                    <p>
                        Aujourd'hui, nous travaillons en freelance et en réseau, en collaborant avec de nombreux partenaires, fournisseurs nationaux et internationaux, ainsi qu'avec de jeunes entrepreneurs locaux talentueux et passionnés.
                    </p>
                </div>
            </div>

            <!-- Right: Notre Modèle (Diagram) -->
            <div class="lg:col-span-7 space-y-6">
                <span class="bg-navy-100 text-navy-800 text-[10px] font-black uppercase tracking-[0.2em] px-3 py-1.5 rounded-sm">NOTRE APPROCHE</span>
                <h2 class="text-3xl font-black text-navy-900 uppercase italic tracking-tight">Notre Modèle</h2>
                
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 shadow-inner">
                    <!-- Top Row of Flow -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
                        <!-- Step 1 -->
                        <div class="flex-1 w-full bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center text-center shadow-sm">
                            <div class="w-10 h-10 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-navy-900 uppercase">Vous avez un besoin</span>
                        </div>
                        
                        <!-- Arrow -->
                        <div class="shrink-0 text-gold-500 rotate-90 sm:rotate-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                        
                        <!-- Step 2 -->
                        <div class="flex-1 w-full bg-navy-600 border-2 border-gold-500 rounded-xl p-4 flex flex-col items-center text-center shadow-md relative overflow-hidden">
                            <div class="absolute -right-6 -bottom-6 w-16 h-16 bg-white/5 rounded-full"></div>
                            <div class="w-10 h-10 rounded-full bg-gold-500 text-navy-900 flex items-center justify-center mb-2 font-black text-sm">
                                IT
                            </div>
                            <span class="text-xs font-bold text-white uppercase">IT Holding Services</span>
                        </div>
                        
                        <!-- Arrow -->
                        <div class="shrink-0 text-gold-500 rotate-90 sm:rotate-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                        
                        <!-- Step 3 -->
                        <div class="flex-1 w-full bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center text-center shadow-sm">
                            <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-navy-900 uppercase">Une solution adaptée</span>
                        </div>
                    </div>
                    
                    <!-- Connector line -->
                    <div class="w-full flex justify-center mb-4">
                        <div class="w-0.5 h-6 bg-gold-500"></div>
                    </div>
                    
                    <!-- Bottom Grid representing the partners/experts/suppliers -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach([
                            ['name' => 'Fournisseurs nationaux', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                            ['name' => 'Fournisseurs internationaux', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a3 3 0 013 3V1.05M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['name' => 'Freelances & Experts', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                            ['name' => 'Jeunes entrepreneurs', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                            ['name' => 'Techniciens & Ingénieurs', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                            ['name' => 'Partenaires spécialisés', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z']
                        ] as $item)
                            <div class="bg-white border border-gray-150 hover:border-gold-400 rounded-xl p-3 flex flex-col items-center text-center shadow-sm hover:shadow transition-all group">
                                <div class="w-8 h-8 rounded-full bg-navy-50 text-navy-600 group-hover:bg-gold-50 group-hover:text-gold-600 flex items-center justify-center mb-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                </div>
                                <span class="text-[10px] font-bold text-navy-800 tracking-tight leading-tight group-hover:text-gold-600 transition-colors">{{ $item['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Nos Domaines d'Expertise -->
    <section class="bg-gray-50/50 border-y border-gray-100 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-16">
                <span class="bg-gold-100 text-gold-800 text-[10px] font-black uppercase tracking-[0.2em] px-3 py-1.5 rounded-sm">NOTRE SAVOIR-FAIRE</span>
                <h2 class="text-3xl font-black text-navy-900 uppercase italic tracking-tight mt-4">Nos domaines d'expertise</h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach([
                    [
                        'title' => 'Informatique',
                        'desc' => 'Vente de matériel, Maintenance & Support',
                        'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
                    ],
                    [
                        'title' => 'Réseaux & Télécoms',
                        'desc' => 'Câblage, Wi-Fi, Fibre, Routage, VPN',
                        'icon' => 'M4.9 19.1C1 15.2 1 8.8 4.9 4.9M19.1 4.9c3.9 3.9 3.9 10.3 0 14.2M7.7 16.3C5.4 14 5.4 10 7.7 7.7M16.3 7.7c2.3 2.3 2.3 6.3 0 8.6M12 14a2 2 0 100-4 2 2 0 000 4z'
                    ],
                    [
                        'title' => 'Cybersécurité',
                        'desc' => 'Protection des données, Réseaux & Systèmes',
                        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
                    ],
                    [
                        'title' => 'Vidéosurveillance',
                        'desc' => 'Caméras IP, Contrôle d\'accès, Sécurité',
                        'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'
                    ],
                    [
                        'title' => 'Développement',
                        'desc' => 'Sites web, Applications, Logiciels sur mesure',
                        'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'
                    ],
                    [
                        'title' => 'Intelligence Artificielle',
                        'desc' => 'Automatisation, IA, Agents intelligents',
                        'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'
                    ],
                    [
                        'title' => 'E-commerce',
                        'desc' => 'Boutiques en ligne, Marketplaces',
                        'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'
                    ],
                    [
                        'title' => 'Marketing Digital',
                        'desc' => 'Réseaux sociaux, Publicité, Stratégie',
                        'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'
                    ],
                    [
                        'title' => 'Design & Création',
                        'desc' => 'Graphisme, Branding, UI/UX, Visuels',
                        'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'
                    ],
                    [
                        'title' => 'IoT & Smart Solutions',
                        'desc' => 'Objets connectés, Domotique, Capteurs',
                        'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.364l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'
                    ],
                    [
                        'title' => 'Cloud & Infrastructures',
                        'desc' => 'Serveurs, Cloud, Sauvegarde, Virtualisation',
                        'icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'
                    ],
                    [
                        'title' => 'Formation',
                        'desc' => 'Formations pratiques, Transfert de compétences',
                        'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222'
                    ]
                ] as $expert)
                    <div class="bg-white border border-gray-150 rounded-xl p-6 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300 flex flex-col items-center text-center group">
                        <div class="w-12 h-12 bg-navy-50 text-navy-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gold-500 group-hover:text-navy-900 transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $expert['icon'] }}"/></svg>
                        </div>
                        <h3 class="font-bold text-navy-900 text-sm sm:text-base uppercase tracking-tight mb-2 group-hover:text-gold-600 transition-colors duration-300">{{ $expert['title'] }}</h3>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $expert['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section 4: Vision, Mission, Ambitions, Engagement -->
    <section id="vision" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- NOTRE VISION -->
            <div class="bg-white border border-gray-150 rounded-xl p-6 shadow-sm flex flex-col h-full hover:border-gold-500 transition-colors">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3 class="font-black text-navy-900 text-sm uppercase tracking-wider italic">Notre Vision</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Devenir un écosystème technologique de référence au Sénégal et en Afrique de l'Ouest, capable de connecter les talents, les entreprises et les technologies pour construire une Afrique plus numérique, innovante et compétitive.
                </p>
            </div>

            <!-- NOTRE MISSION -->
            <div class="bg-white border border-gray-150 rounded-xl p-6 shadow-sm flex flex-col h-full hover:border-gold-500 transition-colors">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 0A5 5 0 118 8.002a5 5 0 017.072 1.17M18.364 5.636a9 9 0 11-12.728 0m12.728 0A9 9 0 015.636 18.364"/></svg>
                    </div>
                    <h3 class="font-black text-navy-900 text-sm uppercase tracking-wider italic">Notre Mission</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Connecter chaque besoin de nos clients aux bonnes compétences, aux bonnes technologies et aux bons partenaires afin de fournir des solutions fiables, innovantes et adaptées, tout en créant de la valeur.
                </p>
            </div>

            <!-- NOS AMBITIONS -->
            <div class="bg-white border border-gray-150 rounded-xl p-6 shadow-sm flex flex-col h-full hover:border-gold-500 transition-colors">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="font-black text-navy-900 text-sm uppercase tracking-wider italic">Nos Ambitions</h3>
                </div>
                <ul class="space-y-2 text-xs text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Développer notre réseau de partenaires
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Créer des opportunités pour les jeunes
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Accompagner la transformation numérique
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Étendre notre présence en Afrique
                    </li>
                </ul>
            </div>

            <!-- NOTRE ENGAGEMENT -->
            <div class="bg-white border border-gray-150 rounded-xl p-6 shadow-sm flex flex-col h-full hover:border-gold-500 transition-colors">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gold-100 text-gold-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-black text-navy-900 text-sm uppercase tracking-wider italic flex items-center gap-1.5">
                        Notre Engagement <span class="text-base">🇸🇳</span>
                    </h3>
                </div>
                <p class="text-xs text-gray-500 font-bold mb-2">Nous croyons au potentiel du Sénégal et de sa jeunesse :</p>
                <ul class="space-y-2 text-xs text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Former et accompagner les talents
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Collaborer avec les acteurs locaux
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gold-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Innover pour répondre aux défis
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Section 5: Quote / Partnership Block (Dark navy background) -->
    <section class="bg-gradient-to-r from-navy-800 to-navy-950 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/5 via-transparent to-transparent opacity-30"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Icon -->
                <div class="lg:col-span-2 flex justify-center">
                    <div class="w-20 h-20 rounded-full border-2 border-gold-500/30 flex items-center justify-center bg-white/5 relative">
                        <div class="absolute inset-2 rounded-full border border-gold-500/10 animate-ping"></div>
                        <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>

                <!-- Quote text -->
                <div class="lg:col-span-5 text-center lg:text-left">
                    <p class="text-xl sm:text-2xl font-black italic text-gray-100 leading-relaxed relative">
                        <span class="text-5xl text-gold-500 font-serif absolute -top-6 -left-4 opacity-35">“</span>
                        Nous ne prétendons pas tout faire seuls. Nous réunissons les bonnes personnes autour des bons projets.
                    </p>
                </div>

                <!-- Descriptive text -->
                <div class="lg:col-span-5 text-center lg:text-left">
                    <p class="text-sm text-gray-300 leading-relaxed border-t lg:border-t-0 lg:border-l border-white/10 pt-6 lg:pt-0 lg:pl-8">
                        Grâce à notre réseau de partenaires, de freelances, d'experts et de fournisseurs, nous sommes capables de répondre à des projets de toutes tailles et de tous secteurs. <strong class="text-gold-400 font-semibold">Votre objectif est notre priorité.</strong>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 6: CTA / Contact Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="bg-gray-50 border border-gray-100 rounded-3xl p-8 lg:p-12 relative overflow-hidden shadow-sm">
            <div class="absolute top-0 right-0 w-80 h-80 bg-gold-500/5 rounded-full blur-3xl"></div>
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <!-- Left Laptop Workspace Image -->
                <div class="lg:col-span-3 hidden lg:block">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80" 
                         alt="IT Workspace" class="rounded-2xl shadow-md border border-white">
                </div>

                <!-- Center Text + Action -->
                <div class="lg:col-span-6 text-center space-y-6">
                    <h3 class="text-2xl lg:text-3xl font-black text-navy-900 uppercase italic">
                        Vous avez un projet ?<br>
                        <span class="text-gold-600 font-sans normal-case not-italic">Parlons-en.</span>
                    </h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto leading-relaxed">
                        Notre écosystème de compétences et de partenaires nous permet de construire la solution adaptée à votre besoin.
                    </p>
                    <div>
                        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 justify-center px-8 py-3.5 border border-transparent text-sm font-bold rounded-lg shadow-sm text-navy-900 bg-gold-500 hover:bg-gold-600 transition-all transform hover:scale-[1.02]">
                            Démarrer un projet
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Right Discussion Image -->
                <div class="lg:col-span-3 hidden lg:block">
                    <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=600&q=80" 
                         alt="Partners discussion" class="rounded-2xl shadow-md border border-white">
                </div>
            </div>
        </div>

        <!-- Micro-footer bottom values bar -->
        <div class="bg-navy-700 rounded-xl mt-8 py-4 px-6 text-white/80">
            <div class="flex flex-wrap items-center justify-center gap-6 md:gap-12 text-[10px] sm:text-xs font-black uppercase tracking-widest text-center">
                <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Écoute & Conseil</span>
                <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Solutions sur mesure</span>
                <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Qualité & Fiabilité</span>
                <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Innovation continue</span>
                <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Accompagnement durable</span>
            </div>
        </div>
    </section>
</div>
@endsection
