@extends('layouts.app')

@section('title', 'Nos Services - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb & Header -->
    <div class="bg-white border-b border-gray-100 py-4 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center mb-2">
                <a href="{{ route('home') }}" class="hover:text-navy-900">Accueil</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gold-600 font-medium">Nos Services</span>
            </nav>
            <h1 class="text-2xl font-black text-navy-900 uppercase tracking-tighter italic">Solutions Experts IT</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Sidebar -->
            <aside class="hidden lg:block lg:col-span-3 space-y-8">
                <!-- Search -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Rechercher</h3>
                    <div class="relative">
                        <input type="text" placeholder="Quel service ?" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-gold-500">
                        <svg class="w-4 h-4 text-gray-400 absolute right-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <!-- Latest Blog Posts -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Actualités IT</h3>
                    <div class="space-y-4">
                        @foreach($latestPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="group flex gap-3 items-center">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex-shrink-0 overflow-hidden">
                                @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h14a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-[10px] font-bold text-navy-900 line-clamp-2 group-hover:text-gold-600 transition-colors uppercase">{{ $post->title }}</h4>
                                <span class="text-[9px] text-gray-400 uppercase tracking-tighter">{{ $post->created_at->format('d M Y') }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Contact Promo -->
                <div class="bg-navy-900 rounded-xl p-6 text-white relative overflow-hidden group">
                    <div class="relative z-10">
                        <h3 class="text-xl font-black italic uppercase tracking-tighter mb-2">Besoin d'un Conseil ?</h3>
                        <p class="text-[10px] text-gray-300 mb-4 leading-relaxed font-medium uppercase tracking-widest">Nos experts sont à votre disposition pour vous accompagner.</p>
                        <a href="{{ route('contact.index') }}" class="inline-flex items-center gap-2 bg-gold-500 text-navy-900 px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-gold-400 transition-colors">
                            Parlons-en
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                    <svg class="w-32 h-32 text-white/5 absolute -bottom-8 -right-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="lg:col-span-9">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($services as $service)
                        <div class="group bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <!-- Service Header / Image -->
                            <div class="relative h-56 overflow-hidden">
                                @if($service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-navy-900 flex items-center justify-center relative overflow-hidden">
                                        <!-- Decorative elements -->
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-gold-500/10 rounded-full blur-3xl -mr-16 -mt-16"></div>
                                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gold-500/10 rounded-full blur-3xl -ml-16 -mb-16"></div>
                                        
                                        <div class="bg-gold-500/10 p-6 rounded-2xl border border-gold-500/20">
                                            @php
                                                $icons = [
                                                    'code' => '<svg class="w-12 h-12 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>',
                                                    'cloud' => '<svg class="w-12 h-12 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>',
                                                    'shield-check' => '<svg class="w-12 h-12 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                                                    'database' => '<svg class="w-12 h-12 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>',
                                                    'refresh' => '<svg class="w-12 h-12 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
                                                    'support' => '<svg class="w-12 h-12 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
                                                ];
                                            @endphp
                                            {!! $icons[$service->icon] ?? $icons['code'] !!}
                                        </div>
                                    </div>
                                @endif
                                <div class="absolute top-4 left-4">
                                    <span class="bg-navy-900/80 backdrop-blur-md text-gold-500 text-[9px] font-bold px-3 py-1.5 rounded-full uppercase tracking-widest border border-gold-500/30">Service Actif</span>
                                </div>
                            </div>

                            <!-- Service Content -->
                            <div class="p-8">
                                <h2 class="text-xl font-black text-navy-900 uppercase tracking-tighter italic mb-4 group-hover:text-gold-600 transition-colors">{{ $service->title }}</h2>
                                <p class="text-xs text-gray-500 leading-relaxed mb-6 font-medium line-clamp-3">
                                    {{ $service->description }}
                                </p>
                                
                                <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                                    <a href="{{ route('services.show', $service->slug) }}" class="inline-flex items-center gap-2 text-[10px] font-black text-navy-900 uppercase tracking-widest group-hover:gap-4 transition-all">
                                        Découvrir l'offre
                                        <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="inline-flex p-4 rounded-full bg-gray-50 mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-navy-900 uppercase tracking-tight italic">Aucun service trouvé</h3>
                            <p class="text-xs text-gray-400 mt-2 uppercase tracking-widest">Revenez bientôt pour plus de solutions.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
