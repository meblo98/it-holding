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
        <div x-data="{ showFilters: false }" class="flex flex-col lg:flex-row gap-8">
            <!-- Mobile Filters Toggle -->
            <button @click="showFilters = !showFilters" type="button"
                class="lg:hidden w-full flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-xs font-bold text-navy-900 uppercase tracking-widest">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 01.8 1.6l-6.3 8.4v5.6a1 1 0 01-1.45.9l-4-2A1 1 0 019 16.8V13L2.7 5.6A1 1 0 013 4z"/></svg>
                    Filtres
                    @if(request()->anyFilled(['category_id', 'brand_id', 'condition', 'price_min', 'price_max']))
                        <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span>
                    @endif
                </span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': showFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Sidebar Filters -->
            <aside :class="showFilters ? 'block' : 'hidden'" class="lg:block w-full lg:w-64 flex-shrink-0 space-y-8">
                <form action="{{ route('shop.index') }}" method="GET" class="space-y-8">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    @if(request('blackfriday'))<input type="hidden" name="blackfriday" value="{{ request('blackfriday') }}">@endif

                    <!-- Category Filter (dropdown with subcategories) -->
                    <div>
                        <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 italic">Catégorie</h3>
                        <select name="category_id" class="w-full border-gray-200 rounded-lg text-sm py-2.5 px-3 focus:ring-gold-500 focus:border-gold-500 bg-white">
                            <option value="">Toutes les catégories</option>
                            @foreach ($categories ?? [] as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @foreach ($cat->children as $child)
                                    <option value="{{ $child->id }}" {{ request('category_id') == $child->id ? 'selected' : '' }}>&nbsp;&nbsp;&nbsp;└ {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <!-- Condition Filter -->
                    @if(isset($conditions) && $conditions->count() > 0)
                    <div>
                        <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 italic">État</h3>
                        <select name="condition" class="w-full border-gray-200 rounded-lg text-sm py-2.5 px-3 focus:ring-gold-500 focus:border-gold-500 bg-white">
                            <option value="">Tous les états</option>
                            @foreach ($conditions as $cond)
                                <option value="{{ $cond }}" {{ request('condition') == $cond ? 'selected' : '' }}>
                                    @switch($cond)
                                        @case('new') Neuf @break
                                        @case('reconditioned') Reconditionné @break
                                        @case('second_hand') Seconde main @break
                                        @default {{ ucfirst(str_replace('_', ' ', $cond)) }}
                                    @endswitch
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Price Range -->
                    <div>
                        <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 italic">Gamme de Prix</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" min="0" name="price_min" value="{{ request('price_min') }}" placeholder="Min" class="w-full border-gray-200 rounded text-xs py-1.5 px-2 bg-gray-50/50 focus:ring-gold-500 focus:border-gold-500">
                            <span class="text-gray-300 text-xs">–</span>
                            <input type="number" min="0" name="price_max" value="{{ request('price_max') }}" placeholder="Max" class="w-full border-gray-200 rounded text-xs py-1.5 px-2 bg-gray-50/50 focus:ring-gold-500 focus:border-gold-500">
                        </div>
                    </div>

                    <!-- Popular Brands -->
                    <div>
                        <h3 class="text-xs font-bold text-navy-900 uppercase tracking-widest mb-4 italic">Marques Populaires</h3>
                        <div class="grid grid-cols-2 gap-y-2">
                            @foreach ($brands ?? [] as $b)
                                <label class="flex items-center gap-2 text-xs text-gray-500 cursor-pointer hover:text-navy-900">
                                    <input type="checkbox" name="brand_id[]" value="{{ $b->id }}" {{ in_array($b->id, (array) request('brand_id', [])) ? 'checked' : '' }} class="rounded text-gold-500 focus:ring-gold-500 h-3.5 w-3.5 border-gray-300">
                                    {{ $b->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-2">
                        <button type="submit" class="w-full btn-primary-gold py-3 text-[10px] uppercase tracking-widest flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Rechercher
                        </button>
                        @if(request()->anyFilled(['category_id', 'brand_id', 'condition', 'price_min', 'price_max']))
                            <a href="{{ route('shop.index', request()->only('search')) }}" class="block text-center text-[10px] font-bold text-gray-400 hover:text-red-500 uppercase tracking-widest">Réinitialiser les filtres</a>
                        @endif
                    </div>
                </form>

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

                <!-- Bon Plan Banner -->
                @if(isset($promoProduct))
                <div class="relative rounded-xl overflow-hidden group ring-2 ring-red-500/70">
                    @php
                        $promoRawPath = $promoProduct->image ?: $promoProduct->images->first()->path ?? null;
                        $promoImgPath = $promoRawPath ? preg_replace('#^(/?storage/)#', '', $promoRawPath) : null;
                        $promoImgUrl = ($promoImgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($promoImgPath)) 
                            ? '/storage/' . ltrim($promoImgPath, '/') 
                            : ($promoRawPath ?: asset('logo.jpeg'));
                    @endphp
                    <img src="{{ $promoImgUrl }}" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-red-700 via-red-700/75 to-red-700/10 p-6 flex flex-col justify-end items-center text-center">
                        <span class="bg-white text-red-600 text-[10px] font-black uppercase tracking-[0.2em] mb-3 px-3 py-1 rounded-full">
                            {{ $promoProduct->promo_price ? 'Bon Plan' : 'Dernier Arrivage' }}
                        </span>
                        <h4 class="text-white font-black italic uppercase text-sm leading-tight mb-2 line-clamp-2">
                            {{ $promoProduct->name }}
                        </h4>
                        <p class="text-red-50 text-[10px] mb-4 italic line-clamp-2">
                            {{ strip_tags($promoProduct->description) }}
                        </p>
                        <div class="text-white font-bold text-sm mb-4">
                            @if($promoProduct->promo_price && $promoProduct->promo_price < $promoProduct->price)
                                <span class="text-white text-base">{{ number_format($promoProduct->promo_price, 0, ',', ' ') }} CFA</span>
                                <span class="text-xs line-through text-red-200 ml-2">{{ number_format($promoProduct->price, 0, ',', ' ') }} CFA</span>
                            @else
                                <span class="text-white text-base">{{ number_format($promoProduct->price, 0, ',', ' ') }} CFA</span>
                            @endif
                        </div>
                        <a href="{{ route('shop.show', $promoProduct->slug) }}" class="bg-white text-red-600 hover:bg-red-50 font-black py-2 px-6 rounded-lg text-[10px] uppercase tracking-widest flex items-center gap-2 transition-colors w-fit">
                            Acheter Maintenant
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
                @endif
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1">
                <!-- Header Filters -->
                <div class="bg-gray-50/50 border border-gray-100 rounded-xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 mb-8">
                    <form action="{{ route('shop.index') }}" method="GET" class="relative w-full md:w-96">
                        @if(request('category_id'))
                            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                        @endif
                        @foreach((array) request('brand_id', []) as $bid)
                            <input type="hidden" name="brand_id[]" value="{{ $bid }}">
                        @endforeach
                        @if(request('condition'))
                            <input type="hidden" name="condition" value="{{ request('condition') }}">
                        @endif
                        @if(request('blackfriday'))
                            <input type="hidden" name="blackfriday" value="{{ request('blackfriday') }}">
                        @endif
                        @if(request('price_min'))
                            <input type="hidden" name="price_min" value="{{ request('price_min') }}">
                        @endif
                        @if(request('price_max'))
                            <input type="hidden" name="price_max" value="{{ request('price_max') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit..." class="w-full border-gray-200 rounded-lg py-2.5 pl-4 pr-12 text-sm focus:ring-gold-500 focus:border-gold-500">
                        <div class="absolute right-3 top-2.5 flex items-center gap-2">
                            @if(request('search'))
                                <a href="{{ route('shop.index', request()->except('search')) }}" class="text-gray-400 hover:text-red-500" title="Effacer la recherche">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                            <button type="submit" class="text-gray-400 hover:text-navy-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </button>
                        </div>
                    </form>
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
                    @if(request('category_id'))
                        @php
                            $selectedCat = \App\Models\Category::find(request('category_id'));
                        @endphp
                        @if($selectedCat)
                        <div class="flex items-center gap-2 bg-navy-50 border border-navy-100 px-3 py-1 rounded text-[10px] font-bold text-navy-900 group">
                            Catégorie: {{ $selectedCat->name }}
                            <a href="{{ route('shop.index', request()->except('category_id')) }}" class="hover:text-red-500 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="flex items-center gap-2 bg-navy-50 border border-navy-100 px-3 py-1 rounded text-[10px] font-bold text-navy-900 group">
                            Matériel Informatique
                            <a href="{{ route('shop.index') }}" class="hover:text-red-500 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </div>
                    @endif
                    
                    @if(request('condition'))
                    <div class="flex items-center gap-2 bg-navy-50 border border-navy-100 px-3 py-1 rounded text-[10px] font-bold text-navy-900 group">
                        État: 
                        @switch(request('condition'))
                            @case('new') Neuf @break
                            @case('reconditioned') Reconditionné @break
                            @case('second_hand') Seconde main @break
                            @default {{ ucfirst(str_replace('_', ' ', request('condition'))) }}
                        @endswitch
                        <a href="{{ request()->fullUrlWithQuery(['condition' => null]) }}" class="hover:text-red-500 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </div>
                    @endif

                    @if(request('search'))
                    <div class="flex items-center gap-2 bg-navy-50 border border-navy-100 px-3 py-1 rounded text-[10px] font-bold text-navy-900 group">
                        Recherche: "{{ request('search') }}"
                        <a href="{{ route('shop.index', request()->except('search')) }}" class="hover:text-red-500 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </div>
                    @endif

                    @if(request('price_min') || request('price_max'))
                    <div class="flex items-center gap-2 bg-navy-50 border border-navy-100 px-3 py-1 rounded text-[10px] font-bold text-navy-900 group">
                        Prix: {{ request('price_min') ? number_format(request('price_min'), 0, ',', ' ') . ' CFA' : '0' }} – {{ request('price_max') ? number_format(request('price_max'), 0, ',', ' ') . ' CFA' : '∞' }}
                        <a href="{{ route('shop.index', request()->except(['price_min', 'price_max'])) }}" class="hover:text-red-500 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </div>
                    @endif

                    @foreach((array) request('brand_id', []) as $bid)
                        @php $activeBrand = ($brands ?? collect())->firstWhere('id', (int) $bid); @endphp
                        @if($activeBrand)
                        <div class="flex items-center gap-2 bg-navy-50 border border-navy-100 px-3 py-1 rounded text-[10px] font-bold text-navy-900 group">
                            Marque: {{ $activeBrand->name }}
                            <a href="{{ route('shop.index', request()->except('brand_id') + ['brand_id' => array_values(array_diff((array) request('brand_id', []), [$bid]))]) }}" class="hover:text-red-500 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </div>
                        @endif
                    @endforeach

                    <span class="text-[10px] font-bold text-navy-900 italic ml-auto"><span class="text-gold-600 font-black">{{ $products->total() }}</span> Produits trouvés</span>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-5">
                    @forelse($products as $product)
                        <div class="product-card group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 relative">
                            <!-- Discount/Preorder Badge -->
                            @if($product->isPreorderable())
                                <div class="absolute top-4 left-4 z-10 bg-amber-500 text-white text-[10px] font-black px-2 py-1 rounded uppercase italic">PRÉCOMMANDE</div>
                            @elseif($product->promo_price && $product->promo_price < $product->price)
                                <div class="absolute top-4 left-4 z-10 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded uppercase italic">PROMO</div>
                            @endif
                            @if($product->blackfriday)
                                <div class="absolute top-4 right-4 z-10 bg-navy-900 text-gold-400 text-[10px] font-black px-2 py-1 rounded uppercase tracking-tighter italic">BLACK FRIDAY</div>
                            @endif

                            <div class="relative bg-gray-50/50 p-3 sm:p-4 lg:p-5 overflow-hidden">
                                @php
                                    $rawPath = $product->image ?: $product->images->first()->path ?? null;
                                    $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                                    $imgUrl = ($imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath))
                                        ? '/storage/' . ltrim($imgPath, '/')
                                        : ($rawPath ?: asset('logo.jpeg'));
                                @endphp
                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-full h-24 sm:h-32 lg:h-32 object-contain mix-blend-multiply group-hover:scale-110 transition-transform duration-500">
                                
                                <!-- Hover Actions -->
                                <div class="absolute inset-0 bg-navy-900/60 flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <form action="{{ route('shop.addToCart', $product->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-10 h-10 {{ $product->isPreorderable() ? 'bg-amber-500' : 'bg-gold-500' }} text-navy-900 rounded-full flex items-center justify-center hover:bg-white transition-colors" title="{{ $product->isPreorderable() ? 'Précommander' : 'Ajouter au Panier' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('shop.show', $product->slug) }}" class="w-10 h-10 bg-white text-navy-900 rounded-full flex items-center justify-center hover:bg-gold-500 transition-colors" title="Détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </a>
                                </div>
                            </div>

                            <div class="p-3 sm:p-4">
                                <div class="flex items-center gap-1 mb-1.5">
                                    @for($i = 0; $i < 5; $i++)
                                        <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                    <span class="text-[10px] text-gray-300 font-bold ml-1">(4.8)</span>
                                </div>
                                <h3 class="text-xs font-bold text-navy-900 group-hover:text-gold-600 transition-colors line-clamp-2 mb-2 h-8 italic">
                                    <a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>

                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($product->promo_price && $product->promo_price < $product->price)
                                            <span class="text-sm font-black text-navy-950">{{ number_format($product->promo_price, 0, ',', ' ') }} <span class="text-[10px]">CFA</span></span>
                                            <span class="text-xs font-bold text-gray-300 line-through italic">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-[8px]">CFA</span></span>
                                        @else
                                            <span class="text-sm font-black text-navy-950">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-[10px]">CFA</span></span>
                                        @endif
                                    </div>
                                    @if($product->isPreorderable() && $product->available_at)
                                        <span class="text-[9px] bg-amber-50 text-amber-700 border border-amber-200 px-1.5 py-0.5 rounded font-bold w-fit">
                                            Dispo: {{ $product->available_at->format('d/m/Y') }}
                                        </span>
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
