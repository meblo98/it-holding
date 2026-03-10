@extends('layouts.app')

@section('title', 'Blog & Actualités - ' . config('app.name'))

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
                <span class="text-navy-900 font-bold">Blog</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-20">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Sidebar -->
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-12">
                <!-- Search Blog -->
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
                                <span class="text-sm font-bold text-gray-500 group-hover:text-navy-900 italic capitalize">{{ $cat }}</span>
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

                <!-- Gallery (Mockup style) -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-6 italic border-b border-gray-100 pb-2">Galerie</h3>
                    <div class="grid grid-cols-4 gap-2">
                        @for($i=1; $i<=8; $i++)
                            <div class="aspect-square bg-gray-50 rounded-sm overflow-hidden border border-gray-100 group cursor-pointer shadow-sm">
                                <img src="https://picsum.photos/100/100?random={{ $i }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform opacity-60 group-hover:opacity-100">
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

            <!-- Main Blog Grid -->
            <main class="flex-1">
                <!-- Blog Header -->
                <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-4">
                    <h2 class="text-xl font-black text-navy-900 uppercase tracking-tighter italic"><span class="text-gold-500">Fil</span> d'Actualités</h2>
                    <div class="flex items-center gap-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">
                        Trier par:
                        <select class="border-none bg-transparent text-navy-900 font-black focus:ring-0 cursor-pointer">
                            <option>Plus récents</option>
                            <option>Plus anciens</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($posts as $post)
                        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 group">
                            <!-- Image Container -->
                            <div class="relative aspect-video overflow-hidden">
                                @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                @else
                                    <div class="w-full h-full bg-gray-50 flex items-center justify-center text-navy-100 group-hover:scale-110 transition-transform duration-700">
                                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="absolute top-4 left-4 bg-navy-900 text-gold-400 text-[9px] font-black px-2 py-1 rounded uppercase italic tracking-tighter">{{ $post->category ?? 'General' }}</div>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <div class="flex items-center gap-4 text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-4 italic">
                                    <span class="flex items-center gap-1.5 text-navy-800"><svg class="w-3 h-3 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> IT Holding Team</span>
                                    <span class="flex items-center gap-1.5"><svg class="w-3 h-3 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> {{ $post->published_at ? $post->published_at->format('d M, Y') : '' }}</span>
                                </div>
                                <h3 class="text-base font-black text-navy-950 italic group-hover:text-gold-600 transition-colors uppercase leading-snug mb-4">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                <p class="text-gray-400 text-xs italic leading-relaxed mb-6 line-clamp-3">
                                    {{ Str::limit(strip_tags($post->content), 120) }}
                                </p>
                                <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center gap-2 text-[10px] font-black text-gold-600 uppercase tracking-[0.2em] group/btn">
                                    Lire l'article
                                    <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center bg-gray-50/50 rounded-xl border border-gray-100 border-dashed">
                             <p class="text-sm text-gray-400 italic font-bold">Aucun article publié pour le moment.</p>
                             <a href="{{ route('home') }}" class="mt-6 inline-block btn-primary-gold px-8 py-3 uppercase tracking-widest text-[10px]">Accueil</a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    {{ $posts->links() }}
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
