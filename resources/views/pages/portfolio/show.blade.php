@extends('layouts.app')

@section('title', $project->title . ' - Portfolio')

@section('content')
<div class="bg-white min-h-screen">
    <!-- Project Header -->
    <div class="bg-navy-900 py-16 lg:py-24 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" fill="none" viewBox="0 0 400 400"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#grid)"/></svg>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <nav class="flex text-xs text-gray-400 gap-2 items-center mb-6">
                <a href="{{ route('home') }}" class="hover:text-gold-500 transition-colors uppercase font-bold tracking-widest">Accueil</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('portfolio.index') }}" class="hover:text-gold-500 transition-colors uppercase font-bold tracking-widest">Portfolio</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gold-500 font-bold uppercase tracking-widest">{{ $project->title }}</span>
            </nav>

            <div class="max-w-3xl">
                <span class="inline-block bg-gold-500 text-navy-900 text-[10px] font-black px-4 py-1.5 rounded-full mb-6 uppercase tracking-[0.2em]">Étude de Cas</span>
                <h1 class="text-4xl lg:text-7xl font-black text-white uppercase tracking-tighter italic leading-none mb-8">{{ $project->title }}</h1>
                <div class="flex flex-wrap gap-8">
                    @if($project->client)
                    <div>
                        <span class="block text-[9px] text-gray-500 font-black uppercase tracking-widest mb-1">Client</span>
                        <span class="text-white font-bold uppercase text-sm italic">{{ $project->client }}</span>
                    </div>
                    @endif
                    @if($project->completion_date)
                    <div>
                        <span class="block text-[9px] text-gray-500 font-black uppercase tracking-widest mb-1">Date</span>
                        <span class="text-white font-bold uppercase text-sm italic">{{ $project->completion_date->format('M Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="lg:grid lg:grid-cols-12 lg:gap-16">
            <!-- Left Side: Description & Content -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-gray-100 mb-12">
                    @if($project->image)
                        <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}" class="w-full h-auto">
                    @else
                        <div class="w-full aspect-video bg-navy-900 flex items-center justify-center relative">
                            <div class="p-12 rounded-full bg-gold-500/10 border border-gold-500/20">
                                <svg class="w-20 h-20 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="prose prose-lg prose-navy max-w-none">
                    <h2 class="text-3xl font-black text-navy-900 uppercase tracking-tighter italic mb-8 border-b-2 border-gold-500 pb-4 inline-block text-justify">Présentation du Projet</h2>
                    <p class="text-gray-600 leading-loose text-justify mb-12">
                        {{ $project->description }}
                    </p>

                    @if($project->technologies)
                    <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 not-prose">
                        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-6 border-l-4 border-gold-500 pl-4 text-justify">Technologies utilisées</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($project->technologies as $tech)
                                <span class="bg-white px-4 py-2 rounded-lg border border-gray-100 text-[10px] font-black text-navy-900 uppercase tracking-widest shadow-sm hover:border-gold-500 transition-colors">{{ $tech }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Side: Sidebar -->
            <aside class="lg:col-span-4 mt-12 lg:mt-0 space-y-12">
                <!-- Action Card -->
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl overflow-hidden relative">
                    <h3 class="text-xl font-black text-navy-900 uppercase tracking-tighter italic mb-4">Prêt à lancer votre projet ?</h3>
                    <p class="text-xs text-gray-400 mb-8 leading-relaxed uppercase font-bold tracking-widest">Faites comme {{ $project->client ?? 'nos clients' }} et choisissez l'excellence IT.</p>
                    <a href="{{ route('contact.index') }}" class="w-full flex items-center justify-center gap-4 bg-navy-900 text-white py-4 rounded-xl font-black uppercase tracking-widest hover:bg-navy-800 transition-all group">
                        Démarrer Maintenant
                        <svg class="w-5 h-5 text-gold-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Related Projects -->
                <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100">
                    <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-6 border-l-4 border-gold-500 pl-4">Autres Réalisations</h3>
                    <div class="space-y-6">
                        @foreach($otherProjects as $other)
                        <a href="{{ route('portfolio.show', $other->slug) }}" class="group block">
                            <div class="flex gap-4 items-center">
                                <div class="w-16 h-16 rounded-xl bg-navy-900 overflow-hidden flex-shrink-0 flex items-center justify-center p-2">
                                    @if($other->image)
                                        <img src="{{ asset('storage/' . $other->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                    @else
                                        <svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <span class="block text-[8px] font-black text-gold-600 uppercase tracking-widest mb-1">{{ $other->client ?? 'PROJET IT' }}</span>
                                    <h4 class="text-[10px] font-black text-navy-900 uppercase tracking-tight italic group-hover:text-gold-500 transition-colors leading-tight">{{ $other->title }}</h4>
                                </div>
                            </div>
                        </a>
                        @if(!$loop->last) <div class="h-px bg-gray-200"></div> @endif
                        @endforeach
                    </div>
                </div>

                <!-- Contact Info Badge -->
                <div class="bg-gold-500 rounded-3xl p-8 flex items-center gap-6">
                    <div class="bg-navy-900 p-3 rounded-full text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <span class="block text-[9px] font-black text-navy-900 uppercase tracking-widest mb-1">Téléphone</span>
                        <span class="text-sm font-black text-navy-900">+221 33 800 00 00</span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
