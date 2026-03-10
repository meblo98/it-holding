@extends('layouts.app')

@section('title', 'Boutique Informatique - ' . config('app.name'))

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
                <span class="text-navy-900 font-bold">Boutique</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gold-600 font-medium truncate">Matériel Informatique</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-64 flex-shrink-0 space-y-8">
                <!-- Category Filter -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 italic">Catégories</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('shop.index') }}" class="flex items-center gap-3 text-sm {{ !request('category_id') ? 'text-gold-600 font-bold' : 'text-gray-500 hover:text-navy-900' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ !request('category_id') ? 'bg-gold-500' : 'bg-transparent border border-gray-300' }}"></div>
                                Tous les Produits
                            </a>
                        </li>
                        @foreach ($categories ?? [] as $cat)
                            <li>
                                <a href="{{ route('shop.index', ['category_id' => $cat->id]) }}" class="flex items-center gap-3 text-sm {{ request('category_id') == $cat->id ? 'text-gold-600 font-bold' : 'text-gray-500 hover:text-navy-900' }}">
                                    <div class="w-1.5 h-1.5 rounded-full {{ request('category_id') == $cat->id ? 'bg-gold-500' : 'bg-transparent border border-gray-300' }}"></div>
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Price Range (Mock) -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 italic">Gamme de Prix</h3>
                    <div class="space-y-4">
                        <div class="h-1 bg-gray-100 rounded-full relative">
                            <div class="absolute inset-y-0 left-0 right-1/4 bg-gold-400 rounded-full"></div>
                            <div class="absolute -top-1.5 left-0 w-4 h-4 bg-white border-2 border-gold-500 rounded-full shadow-sm"></div>
                            <div class="absolute -top-1.5 right-1/4 w-4 h-4 bg-white border-2 border-gold-500 rounded-full shadow-sm"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" placeholder="Min" class="w-full border-gray-200 rounded text-xs py-1.5 px-2 bg-gray-50/50">
                            <input type="text" placeholder="Max" class="w-full border-gray-200 rounded text-xs py-1.5 px-2 bg-gray-50/50">
                        </div>
                        <ul class="space-y-2 text-xs text-gray-500">
                            <li><label class="flex items-center gap-2 cursor-pointer hover:text-navy-900"><input type="radio" name="price" class="text-gold-500 focus:ring-gold-500"> Tout afficher</label></li>
                            <li><label class="flex items-center gap-2 cursor-pointer hover:text-navy-900"><input type="radio" name="price" class="text-gold-500 focus:ring-gold-500"> Moins de 50.000 CFA</label></li>
                            <li><label class="flex items-center gap-2 cursor-pointer hover:text-navy-900"><input type="radio" name="price" class="text-gold-500 focus:ring-gold-500"> 50.000 - 200.000 CFA</label></li>
                            <li><label class="flex items-center gap-2 cursor-pointer hover:text-navy-900"><input type="radio" name="price" class="text-gold-500 focus:ring-gold-500"> Plus de 500.000 CFA</label></li>
                        </ul>
                    </div>
                </div>

                <!-- Popular Brands -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 italic">Marques Populaires</h3>
                    <div class="grid grid-cols-2 gap-y-2">
                        @foreach ($brands ?? [] as $b)
                            <label class="flex items-center gap-2 text-xs text-gray-500 cursor-pointer hover:text-navy-900">
                                <input type="checkbox" name="brand_id[]" value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'checked' : '' }} class="rounded text-gold-500 focus:ring-gold-500 h-3.5 w-3.5 border-gray-300">
                                {{ $b->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Tags -->
                <div>
                    <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 italic">Mots-clés</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-gray-50 text-[10px] font-bold text-navy-800 rounded border border-gray-100 hover:border-gold-300 transition-all cursor-pointer">GAMING</span>
                        <span class="px-3 py-1 bg-gray-50 text-[10px] font-bold text-navy-800 rounded border border-gray-100 hover:border-gold-300 transition-all cursor-pointer">OFFICE</span>
                        <span class="px-3 py-1 bg-gray-50 text-[10px] font-bold text-navy-800 rounded border border-gray-100 hover:border-gold-300 transition-all cursor-pointer">IPHONE</span>
                        <span class="px-3 py-1 bg-navy-900 text-[10px] font-bold text-white rounded cursor-pointer">MATÉRIEL</span>
                        <span class="px-3 py-1 bg-gray-50 text-[10px] font-bold text-navy-800 rounded border border-gray-100 hover:border-gold-300 transition-all cursor-pointer">SSD</span>
                        <span class="px-3 py-1 bg-gray-50 text-[10px] font-bold text-navy-800 rounded border border-gray-100 hover:border-gold-300 transition-all cursor-pointer">TABLETTE</span>
                    </div>
                </div>

                <!-- Promo Banner -->
                <div class="relative rounded-xl overflow-hidden group">
                    <img src="https://images.unsplash.com/photo-1510282135024-94e497ef445x?auto=format&fit=crop&w=400&q=80" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-900 via-navy-900/40 to-transparent p-6 flex flex-col justify-end text-center">
                        <span class="text-gold-500 text-[10px] font-black uppercase tracking-[0.3em] mb-2">Offre Limitée</span>
                        <h4 class="text-white font-black italic uppercase text-lg leading-tight mb-4">Nouvel Apple MacBook Pro</h4>
                        <p class="text-gray-300 text-xs mb-6 italic">Performance sans compromis pour les professionnels.</p>
                        <a href="#" class="btn-primary-gold py-2.5 text-[10px] uppercase tracking-widest mx-auto flex items-center gap-2">
                            Acheter Maintenant
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1">
                <!-- Header Filters -->
                <div class="bg-gray-50/50 border border-gray-100 rounded-xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 mb-8">
                    <div class="relative w-full md:w-96">
                        <input type="text" placeholder="Rechercher un produit..." class="w-full border-gray-200 rounded-lg py-2.5 pl-4 pr-10 text-sm focus:ring-gold-500 focus:border-gold-500">
                        <svg class="w-5 h-5 absolute right-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-widest italic">Trier par:</span>
                        <select class="border-gray-200 rounded-lg py-2 px-4 focus:ring-gold-500 focus:border-gold-500 text-navy-900 font-bold">
                            <option>Plus populaires</option>
                            <option>Prix croissant</option>
                            <option>Prix décroissant</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filters -->
                <div class="flex flex-wrap items-center gap-3 mb-8">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">Filtres actifs:</span>
                    <div class="flex items-center gap-2 bg-navy-50 border border-navy-100 px-3 py-1 rounded text-[10px] font-bold text-navy-900 group">
                        Matériel Informatique
                        <button class="hover:text-red-500 transition-colors"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <span class="text-[10px] font-bold text-navy-900 italic ml-auto"><span class="text-gold-600 font-black">{{ $products->total() }}</span> Produits trouvés</span>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($products as $product)
                        <div class="product-card group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 relative">
                            <!-- Discount Badge -->
                            @if($product->promo_price && $product->promo_price < $product->price)
                                <div class="absolute top-4 left-4 z-10 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded uppercase italic">PROMO</div>
                            @endif
                            @if($product->blackfriday)
                                <div class="absolute top-4 right-4 z-10 bg-navy-900 text-gold-400 text-[10px] font-black px-2 py-1 rounded uppercase tracking-tighter italic">BLACK FRIDAY</div>
                            @endif

                            <div class="relative bg-gray-50/50 p-6 overflow-hidden">
                                @php
                                    $rawPath = $product->image ?: $product->images->first()->path ?? null;
                                    $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                                    $imgUrl = ($imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)) 
                                        ? '/storage/' . ltrim($imgPath, '/') 
                                        : ($rawPath ?: asset('logo.jpeg'));
                                @endphp
                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-full h-48 object-contain mix-blend-multiply group-hover:scale-110 transition-transform duration-500">
                                
                                <!-- Hover Actions -->
                                <div class="absolute inset-0 bg-navy-900/60 flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <form action="{{ route('shop.addToCart', $product->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-10 h-10 bg-gold-500 text-navy-900 rounded-full flex items-center justify-center hover:bg-white transition-colors" title="Ajouter au Panier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('shop.show', $product->slug) }}" class="w-10 h-10 bg-white text-navy-900 rounded-full flex items-center justify-center hover:bg-gold-500 transition-colors" title="Détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </a>
                                </div>
                            </div>

                            <div class="p-5">
                                <div class="flex items-center gap-1 mb-2">
                                    @for($i = 0; $i < 5; $i++)
                                        <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                    <span class="text-[10px] text-gray-300 font-bold ml-1">(4.8)</span>
                                </div>
                                <h3 class="text-xs font-bold text-navy-900 group-hover:text-gold-600 transition-colors line-clamp-2 mb-3 h-8 italic">
                                    <a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                
                                <div class="flex items-center gap-3">
                                    @if($product->promo_price && $product->promo_price < $product->price)
                                        <span class="text-sm font-black text-navy-950">{{ number_format($product->promo_price, 0, ',', ' ') }} <span class="text-[10px]">CFA</span></span>
                                        <span class="text-xs font-bold text-gray-300 line-through italic">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-[8px]">CFA</span></span>
                                    @else
                                        <span class="text-sm font-black text-navy-950">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-[10px]">CFA</span></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a2 2 0 00-1.96 1.414l-.724 2.17a2 2 0 001.077 2.423l1.091.546a2 2 0 011.139 1.438l.192 1.154a2 2 0 001.99 1.66h2.828a2 2 0 001.99-1.66l.192-1.154a2 2 0 011.139-1.438l1.091-.546a2 2 0 001.077-2.423l-.724-2.17a2 2 0 00-1.96-1.414l-2.387.477a2 2 0 00-1.022.547zM10 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-navy-900 italic uppercase">Aucun produit trouvé</h3>
                            <p class="text-sm text-gray-400 mt-2">Essayez d'ajuster vos filtres pour trouver ce que vous cherchez.</p>
                            <a href="{{ route('shop.index') }}" class="mt-8 inline-block btn-primary-gold px-8 py-3 uppercase tracking-widest text-[10px]">Réinitialiser les Filtres</a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    {{ $products->links() }}
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
