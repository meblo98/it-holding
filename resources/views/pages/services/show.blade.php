@extends('layouts.app')

@section('title', $service->title . ' - ' . config('app.name'))

@section('content')
<div class="bg-white min-h-screen">
    <!-- Premium Header / Hero -->
    <div class="relative py-16 bg-navy-900 overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gold-500/5 -skew-x-12 transform origin-top-right"></div>
        <div class="absolute bottom-0 left-0 w-1/4 h-1/2 bg-white/5 rounded-full blur-3xl -ml-20 -mb-20"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <nav class="flex text-xs text-gray-400 gap-2 items-center mb-6">
                <a href="{{ route('home') }}" class="hover:text-gold-500 transition-colors uppercase font-bold tracking-widest">Accueil</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('services.index') }}" class="hover:text-gold-500 transition-colors uppercase font-bold tracking-widest">Nos Services</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gold-500 font-bold uppercase tracking-widest">{{ $service->title }}</span>
            </nav>

            <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
                <div>
                    <span class="inline-block bg-gold-500 text-navy-900 text-[10px] font-black px-4 py-1.5 rounded-full mb-6 uppercase tracking-[0.2em]">Solution Expert</span>
                    <h1 class="text-4xl lg:text-6xl font-black text-white uppercase tracking-tighter italic leading-none mb-6">
                        {{ $service->title }}
                    </h1>
                    <p class="text-lg text-gray-300 font-medium leading-relaxed max-w-xl mb-8">
                        {{ $service->description }}
                    </p>
                    <a href="{{ route('contact.index', ['subject' => 'Devis : ' . $service->title]) }}" class="inline-flex items-center gap-4 bg-gold-500 text-navy-900 px-8 py-4 rounded-xl font-black uppercase tracking-widest hover:bg-gold-400 transition-all hover:gap-6 group">
                        Demander une étude personnalisée
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
                <div class="hidden lg:block relative">
                    <div class="aspect-square bg-white shadow-2xl rounded-3xl overflow-hidden border-8 border-navy-800 rotate-3 p-4 group">
                        @if($service->image)
                            <img src="{{ asset('storage/' . $service->image) }}" class="w-full h-full object-cover rounded-2xl group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-full bg-navy-900 rounded-2xl flex items-center justify-center p-12">
                                <div class="p-8 rounded-full bg-gold-500/10 border-4 border-gold-500/20">
                                    @php
                                        $icons = [
                                            'code' => '<svg class="w-24 h-24 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>',
                                            'cloud' => '<svg class="w-24 h-24 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>',
                                            'shield-check' => '<svg class="w-24 h-24 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                                            'database' => '<svg class="w-24 h-24 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>',
                                            'refresh' => '<svg class="w-24 h-24 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
                                            'support' => '<svg class="w-24 h-24 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
                                        ];
                                    @endphp
                                    {!! $icons[$service->icon] ?? $icons['code'] !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="lg:grid lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-8">
                <article class="prose prose-lg prose-navy max-w-none">
                    <h2 class="text-3xl font-black text-navy-900 uppercase tracking-tighter italic mb-8 border-b-2 border-gold-500 pb-4 inline-block">Détails de l'expertise</h2>
                    <div class="text-gray-600 leading-loose space-y-6 text-justify">
                        {!! nl2br(e($service->content)) !!}
                    </div>

                    <div class="mt-12 bg-gray-50 rounded-3xl p-8 border border-gray-100 flex flex-col md:flex-row items-center gap-8 not-prose">
                        <div class="bg-navy-900 p-4 rounded-2xl flex-shrink-0">
                            <svg class="w-12 h-12 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-navy-900 uppercase tracking-tighter italic mb-2">Résultats Garantis</h3>
                            <p class="text-sm text-gray-500 font-medium">Nous nous engageons sur la qualité et les délais de chaque réalisation.</p>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <aside class="lg:col-span-4 mt-12 lg:mt-0 space-y-12">
                <!-- Related Services -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                    <h3 class="text-lg font-black text-navy-900 uppercase tracking-tighter italic mb-6 border-l-4 border-gold-500 pl-4">Autres Services</h3>
                    <div class="space-y-4">
                        @foreach($otherServices as $other)
                        <a href="{{ route('services.show', $other->slug) }}" class="group block bg-white p-4 rounded-xl border border-transparent hover:border-gold-500 transition-all shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-navy-900/5 flex items-center justify-center text-navy-900 group-hover:bg-navy-900 group-hover:text-gold-500 transition-all">
                                    @php
                                        $smallIcons = [
                                            'code' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>',
                                            'cloud' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>',
                                            'shield-check' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                                            'database' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>',
                                            'refresh' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
                                            'support' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
                                        ];
                                    @endphp
                                    {!! $smallIcons[$other->icon] ?? $smallIcons['code'] !!}
                                </div>
                                <span class="text-[10px] font-black text-navy-900 uppercase tracking-widest">{{ $other->title }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Blog Recap -->
                <div class="bg-navy-900 rounded-3xl p-8 text-white">
                    <h3 class="text-lg font-black uppercase tracking-tighter italic mb-8 border-l-4 border-gold-500 pl-4">Dernières Actualités</h3>
                    <div class="space-y-6 text-sm">
                        @foreach($latestPosts as $post)
                            <a href="{{ route('blog.show', $post->slug) }}" class="group flex flex-col gap-2">
                                <h4 class="text-[11px] font-bold text-gray-100 group-hover:text-gold-500 transition-colors uppercase leading-tight">{{ $post->title }}</h4>
                                <span class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">{{ $post->created_at->format('d/m/Y') }}</span>
                            </a>
                            @if(!$loop->last) <div class="h-px bg-white/5"></div> @endif
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
