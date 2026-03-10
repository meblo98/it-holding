@extends('layouts.app')

@section('title', $post->title . ' - ' . config('app.name'))

@section('content')
<div class="bg-white min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-gray-50 border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center">
                <a href="{{ route('home') }}" class="hover:text-navy-900 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('blog.index') }}" class="hover:text-navy-900 transition-colors">Blog</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold truncate">{{ $post->title }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Article Content -->
            <article class="flex-1 overflow-hidden">
                <!-- Large Post Header Image -->
                <div class="relative aspect-video lg:aspect-[21/9] rounded-2xl overflow-hidden mb-12 shadow-2xl group">
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                    @else
                        <div class="w-full h-full bg-gray-50 flex items-center justify-center text-navy-100">
                            <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-900/60 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <span class="bg-gold-500 text-navy-900 text-[10px] font-black underline-offset-4 uppercase tracking-[0.2em] px-3 py-1.5 rounded-sm italic">{{ $post->category ?? 'Actualités' }}</span>
                    </div>
                </div>

                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4 italic">
                    <span class="flex items-center gap-2 text-navy-900"><svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Autheur: Admin IT Holding</span>
                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> {{ $post->published_at ? $post->published_at->format('d M, Y') : '' }}</span>
                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg> 12 Commentaires</span>
                </div>

                <h1 class="text-3xl lg:text-4xl font-black text-navy-950 uppercase tracking-tighter italic leading-tight mb-10">
                    {{ $post->title }}
                </h1>

                <!-- Share Grid -->
                <div class="flex flex-wrap items-center gap-4 mb-10 border-y border-gray-100 py-6">
                    <span class="text-[10px] font-black text-navy-900 uppercase tracking-widest italic mr-4">Partager:</span>
                    <button class="w-10 h-10 bg-[#3b5998] text-white rounded-full flex items-center justify-center hover:scale-110 transition-transform"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.597 0 0 .597 0 1.326v21.348C0 23.403.597 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.597 1.323-1.326V1.326C24 .597 23.403 0 22.675 0z"/></svg></button>
                    <button class="w-10 h-10 bg-[#1da1f2] text-white rounded-full flex items-center justify-center hover:scale-110 transition-transform"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></button>
                    <button class="w-10 h-10 bg-[#0077b5] text-white rounded-full flex items-center justify-center hover:scale-110 transition-transform"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451c.979 0 1.774-.773 1.774-1.729V1.729C24 .774 23.204 0 22.222 0z"/></svg></button>
                </div>

                <!-- Article Content -->
                <div class="prose prose-lg prose-navy max-w-none text-gray-500 italic leading-relaxed mb-20 font-medium">
                    {!! nl2br(e($post->content)) !!}
                </div>

                <!-- Quote Mockup style -->
                <div class="bg-navy-50 border-l-4 border-gold-500 p-8 rounded-r-xl mb-20">
                    <svg class="w-10 h-10 text-gold-200 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.896 14.913 16 16.017 16H19.017C19.569 16 20.017 15.552 20.017 15V9C20.017 8.448 19.569 8 19.017 8H16.017C14.913 8 14.017 7.104 14.017 6V3L22.017 3V12C22.017 16.971 17.988 21 13.017 21H14.017ZM1.017 21L1.017 18C1.017 16.896 1.913 16 3.017 16H6.017C6.569 16 7.017 15.552 7.017 15V9C7.017 8.448 6.569 8 6.017 8H3.017C1.913 8 1.017 7.104 1.017 6V3L9.017 3V12C9.017 16.971 4.988 21 0.017 21H1.017Z"/></svg>
                    <p class="text-xl font-black text-navy-900 leading-snug uppercase tracking-tight italic">
                        "La technologie ne doit pas seulement être puissante, elle doit être accessible et au service de l'excellence opérationnelle."
                    </p>
                </div>

                <!-- Secondary Images (Mockup style) -->
                <div class="grid grid-cols-2 gap-6 mb-20">
                    <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80" class="rounded-xl shadow-lg border border-gray-100">
                    <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=600&q=80" class="rounded-xl shadow-lg border border-gray-100">
                </div>

                <!-- Comments Section -->
                <section class="border-t border-gray-100 pt-20">
                    <h3 class="text-2xl font-black text-navy-950 uppercase tracking-tighter italic mb-12">Laisser un Commentaire</h3>
                    
                    <form action="#" class="space-y-6 mb-20">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nom Complet</label>
                                <input type="text" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/50">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Adresse Email</label>
                                <input type="email" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/50">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Message</label>
                            <textarea rows="5" class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/50" placeholder="Votre commentaire ici..."></textarea>
                        </div>
                        <button type="submit" class="btn-primary-gold px-12 py-4 uppercase tracking-[0.2em] text-[10px] shadow-lg shadow-gold-500/20">Publier le Commentaire</button>
                    </form>

                    <h3 class="text-xl font-black text-navy-950 uppercase tracking-tighter italic mb-10">Commentaires (3)</h3>
                    <div class="space-y-10">
                        @foreach([
                            ['user' => 'Aminata Ndiaye', 'date' => 'Il y a 2 jours', 'msg' => 'Très bon article ! Les conseils sur la maintenance préventive sont essentiels.'],
                            ['user' => 'Cheikh Fall', 'date' => 'La semaine dernière', 'msg' => 'Merci pour ces éclairages technologiques.'],
                            ['user' => 'Binte Sarr', 'date' => 'Récemment', 'msg' => 'Superbe design, le contenu est très instructif.']
                        ] as $cmt)
                            <div class="flex gap-6 pb-10 border-b border-gray-50">
                                <img src="https://i.pravatar.cc/100?u={{ $cmt['user'] }}" class="w-12 h-12 rounded-full border-2 border-gold-400 p-0.5 grayscale">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-3">
                                        <h4 class="text-xs font-black text-navy-900 uppercase italic">{{ $cmt['user'] }}</h4>
                                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $cmt['date'] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 italic">{{ $cmt['msg'] }}</p>
                                    <button class="text-[9px] font-black text-gold-600 uppercase tracking-widest hover:underline mt-2">Répondre</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </article>

            <!-- Sidebar -->
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-12">
                <!-- Search -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 italic">Rechercher</h3>
                    <div class="relative">
                        <input type="text" placeholder="Titre de l'article..." class="w-full border-gray-200 rounded-lg py-3 px-4 text-sm focus:ring-gold-500 focus:border-gold-500 bg-gray-50/50">
                        <svg class="w-5 h-5 absolute right-3 top-2.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <!-- Categories -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 italic border-b border-gray-100 pb-2">Catégories</h3>
                    <ul class="space-y-4">
                        @foreach($categories as $cat)
                        <li>
                            <a href="#" class="flex items-center justify-between group transition-colors">
                                <span class="text-sm font-bold text-gray-500 group-hover:text-navy-900 italic capitalize font-medium">{{ $cat }}</span>
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-gold-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Latest Blog -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 italic border-b border-gray-100 pb-2">Dernières Actualités</h3>
                    <div class="space-y-6">
                        @foreach($latestPosts as $lp)
                        <a href="{{ route('blog.show', $lp->slug) }}" class="flex gap-4 group">
                            <div class="w-20 h-16 bg-gray-50 rounded overflow-hidden flex-shrink-0">
                                @if($lp->image)
                                    <img src="{{ asset('storage/' . $lp->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-[10px] font-bold text-navy-900 line-clamp-2 italic mb-1 group-hover:text-gold-600 leading-tight transition-colors">{{ $lp->title }}</h4>
                                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $lp->published_at ? $lp->published_at->format('d M, Y') : 'Date inconnue' }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Gallery -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 italic border-b border-gray-100 pb-2">Galerie</h3>
                    <div class="grid grid-cols-4 gap-2">
                        @for($i=1; $i<=8; $i++)
                            <div class="aspect-square bg-gray-50 rounded-sm overflow-hidden border border-gray-100 group cursor-pointer shadow-sm">
                                <img src="https://picsum.photos/100/100?random={{ $i + 10 }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform opacity-60 group-hover:opacity-100">
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Popular Tags -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 italic border-b border-gray-100 pb-2">Mots-clés</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Hardware', 'Service', 'Promo', 'Dakar', 'Support', 'Tech', 'Networking'] as $tag)
                            <span class="px-3 py-1 bg-gray-50 text-[10px] font-bold text-navy-800 rounded border border-gray-100 hover:border-gold-300 transition-all cursor-pointer uppercase tracking-tight">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
