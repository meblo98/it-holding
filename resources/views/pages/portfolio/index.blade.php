@extends('layouts.app')

@section('title', 'Portfolio - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb & Header -->
    <div class="bg-white border-b border-gray-100 py-4 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center mb-2">
                <a href="{{ route('home') }}" class="hover:text-navy-900">Accueil</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gold-600 font-medium">Portfolio</span>
            </nav>
            <h1 class="text-2xl font-black text-navy-900 uppercase tracking-tighter italic">Nos Réalisations</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Sidebar -->
            <aside class="hidden lg:block lg:col-span-3 space-y-8">
                <!-- Project Stats -->
                <div class="bg-navy-900 p-6 rounded-xl shadow-sm text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-xs font-bold text-gold-500 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Notre Impact</h3>
                        <div class="space-y-4">
                            <div>
                                <span class="block text-2xl font-black italic uppercase tracking-tighter">50+</span>
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Projets Livrés</span>
                            </div>
                            <div class="h-px bg-white/5"></div>
                            <div>
                                <span class="block text-2xl font-black italic uppercase tracking-tighter">15+</span>
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Technologies Maîtrisées</span>
                            </div>
                        </div>
                    </div>
                    <svg class="w-24 h-24 text-white/5 absolute -bottom-4 -right-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/><path d="M7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
                </div>

                <!-- Latest Blog Posts -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Derniers Articles</h3>
                    <div class="space-y-4">
                        @foreach($latestPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="group flex flex-col gap-1">
                            <h4 class="text-[10px] font-bold text-navy-900 line-clamp-2 group-hover:text-gold-600 transition-colors uppercase leading-tight">{{ $post->title }}</h4>
                            <span class="text-[8px] text-gray-400 uppercase font-bold">{{ $post->created_at->format('d M Y') }}</span>
                        </a>
                        @if(!$loop->last) <div class="h-px bg-gray-50 mt-1"></div> @endif
                        @endforeach
                    </div>
                </div>

                <!-- Expertise Section -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Expertises</h3>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $expertises = ['Cloud', 'E-commerce', 'Fintech', 'SaaS', 'IoT', 'Mobile'];
                        @endphp
                        @foreach($expertises as $exp)
                            <span class="px-3 py-1 bg-gray-50 text-[9px] font-bold text-gray-500 rounded-full border border-gray-100 uppercase tracking-wider">{{ $exp }}</span>
                        @endforeach
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="lg:col-span-9">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($projects as $project)
                        <a href="{{ route('portfolio.show', $project->slug) }}" class="group block bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                            <!-- Project Image -->
                            <div class="relative h-64 overflow-hidden">
                                @if($project->image)
                                    <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                @else
                                    <div class="w-full h-full bg-navy-900 flex items-center justify-center relative overflow-hidden">
                                        <!-- Decorative Elements -->
                                        <div class="absolute inset-0 bg-gradient-to-tr from-navy-900 via-navy-900 to-navy-800"></div>
                                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-gold-500/10 rounded-full blur-3xl"></div>
                                        
                                        <div class="relative z-10 flex flex-col items-center">
                                            <div class="w-16 h-16 bg-white/5 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/10 mb-4 group-hover:rotate-12 transition-transform">
                                                <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            </div>
                                            <span class="text-[10px] font-black text-gold-500 uppercase tracking-[0.3em] opacity-50">{{ $project->client ?? 'PROJET IT' }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Hover Overlay Tags -->
                                <div class="absolute inset-0 bg-navy-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                                    <span class="bg-gold-500 text-navy-900 font-black text-[10px] px-6 py-3 rounded-full uppercase tracking-widest transform translate-y-4 group-hover:translate-y-0 transition-transform">Voir l'étude de cas</span>
                                </div>
                            </div>

                            <!-- Project Content -->
                            <div class="p-8">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-[9px] font-black text-gold-600 uppercase tracking-widest">{{ $project->client ?? 'Client confidentiel' }}</span>
                                    @if($project->completion_date)
                                        <span class="text-[9px] text-gray-400 font-bold uppercase">{{ $project->completion_date->format('Y') }}</span>
                                    @endif
                                </div>
                                <h2 class="text-xl font-black text-navy-900 uppercase tracking-tighter italic mb-4 group-hover:text-gold-600 transition-colors">{{ $project->title }}</h2>
                                <p class="text-[11px] text-gray-500 leading-relaxed line-clamp-2 mb-6 font-medium">
                                    {{ $project->description }}
                                </p>
                                
                                <!-- Tech Stack -->
                                <div class="flex flex-wrap gap-2 pt-6 border-t border-gray-50">
                                    @if($project->technologies)
                                        @foreach(array_slice($project->technologies, 0, 3) as $tech)
                                            <span class="px-2 py-1 bg-gray-50 text-[8px] font-bold text-navy-800 rounded uppercase tracking-wider border border-gray-100 group-hover:border-gold-500 transition-colors">{{ $tech }}</span>
                                        @endforeach
                                        @if(count($project->technologies) > 3)
                                            <span class="px-2 py-1 bg-gray-50 text-[8px] font-bold text-gray-400 rounded uppercase tracking-wider border border-gray-100">+{{ count($project->technologies) - 3 }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="inline-flex p-4 rounded-full bg-gray-50 mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-navy-900 uppercase tracking-tight italic">Portfolio en cours de mise à jour</h3>
                            <p class="text-xs text-gray-400 mt-2 uppercase tracking-widest">Nos dernières réalisations arrivent très bientôt.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
