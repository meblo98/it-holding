@extends('layouts.app')

@section('title', 'Boutique Informatique - ' . config('app.name'))

@section('content')
@php
    $hasFilters = request()->anyFilled(['category_id', 'brand_id', 'condition', 'price_min', 'price_max']);
    $keep = request()->except(['page']);
    $chip = 'inline-flex items-center gap-2 bg-navy-50 border border-navy-200 px-3 py-1.5 rounded-full text-sm text-navy-900';
    $closeIcon = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
    $field = 'w-full border border-navy-300 rounded-md text-base text-black bg-white py-2.5 px-3 focus:ring-2 focus:ring-gold-500 focus:border-navy-600 placeholder-gray-500';
    $conditionLabel = fn ($c) => match ($c) { 'new' => 'Neuf', 'reconditioned' => 'Reconditionné', 'second_hand' => 'Seconde main', default => ucfirst(str_replace('_', ' ', $c)) };
@endphp
<div class="bg-white">
    <!-- Fil d'Ariane -->
    <div class="bg-navy-50 border-b border-navy-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-sm text-navy-500 gap-2 items-center" aria-label="Fil d'Ariane">
                <a href="{{ route('home') }}" class="hover:text-navy-900">Accueil</a>
                <span aria-hidden="true">/</span>
                <span class="text-navy-900 font-semibold">Boutique</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl sm:text-3xl font-semibold text-navy-900 tracking-tight mb-6">Boutique</h1>

        <div x-data="{ showFilters: false }" class="flex flex-col lg:flex-row gap-8">

            <!-- Bouton filtres (mobile) -->
            <button @click="showFilters = !showFilters" type="button"
                class="lg:hidden w-full flex items-center justify-between bg-white border border-navy-300 rounded-md px-4 py-3 text-base font-semibold text-navy-900">
                <span class="flex items-center gap-2">
                    Catégories et filtres
                    @if($hasFilters)<span class="w-2 h-2 rounded-full bg-gold-500"></span>@endif
                </span>
                <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': showFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Colonne de gauche -->
            <aside :class="showFilters ? 'block' : 'hidden'" class="lg:block w-full lg:w-72 flex-shrink-0 space-y-8">

                <!-- Catégories -->
                <div>
                    <h2 class="text-base font-semibold text-navy-900 mb-3">Catégories</h2>
                    <ul class="border border-navy-200 rounded-md divide-y divide-navy-100 bg-white">
                        <li>
                            <a href="{{ route('shop.index', request()->except(['category_id', 'page'])) }}"
                               class="block px-4 py-3 text-base {{ request('category_id') ? 'text-navy-700 hover:bg-navy-50' : 'bg-navy-900 text-white font-semibold rounded-t-md' }}">
                                Toutes les catégories
                            </a>
                        </li>
                        @foreach ($categories ?? [] as $cat)
                            @php
                                $isActiveRoot = request('category_id') == $cat->id;
                                $childActive = $cat->children->contains('id', (int) request('category_id'));
                            @endphp
                            <li x-data="{ open: {{ ($isActiveRoot || $childActive) ? 'true' : 'false' }} }">
                                <div class="flex items-stretch {{ $isActiveRoot ? 'bg-gold-100' : '' }}">
                                    <a href="{{ route('shop.index', $keep + ['category_id' => $cat->id]) }}"
                                       class="flex-1 px-4 py-3 text-base {{ $isActiveRoot ? 'font-semibold text-navy-900' : 'text-navy-800 hover:bg-navy-50' }}">
                                        {{ $cat->name }}
                                    </a>
                                    @if($cat->children->count())
                                        <button type="button" @click="open = !open" class="px-4 text-navy-600 hover:bg-navy-50" aria-label="Afficher les sous-catégories de {{ $cat->name }}">
                                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    @endif
                                </div>
                                @if($cat->children->count())
                                    <ul x-show="open" x-cloak class="bg-navy-50 py-1">
                                        @foreach($cat->children as $child)
                                            <li>
                                                <a href="{{ route('shop.index', $keep + ['category_id' => $child->id]) }}"
                                                   class="block pl-8 pr-4 py-2 text-[15px] {{ request('category_id') == $child->id ? 'font-semibold text-navy-900 border-l-4 border-gold-500 pl-7' : 'text-navy-700 hover:text-navy-900' }}">
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Filtres -->
                <form action="{{ route('shop.index') }}" method="GET" class="space-y-6">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    @if(request('blackfriday'))<input type="hidden" name="blackfriday" value="{{ request('blackfriday') }}">@endif
                    @if(request('category_id'))<input type="hidden" name="category_id" value="{{ request('category_id') }}">@endif
                    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                    <h2 class="text-base font-semibold text-navy-900">Filtrer</h2>

                    @if(isset($conditions) && $conditions->count() > 0)
                    <div>
                        <label for="f-condition" class="block text-sm font-semibold text-navy-900 mb-2">État</label>
                        <select id="f-condition" name="condition" class="{{ $field }}">
                            <option value="">Tous les états</option>
                            @foreach ($conditions as $cond)
                                <option value="{{ $cond }}" {{ request('condition') == $cond ? 'selected' : '' }}>{{ $conditionLabel($cond) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div>
                        <span class="block text-sm font-semibold text-navy-900 mb-2">Prix (FCFA)</span>
                        <div class="flex items-center gap-2">
                            <input type="number" min="0" name="price_min" value="{{ request('price_min') }}" placeholder="Min" aria-label="Prix minimum" class="{{ $field }}">
                            <span class="text-navy-500">à</span>
                            <input type="number" min="0" name="price_max" value="{{ request('price_max') }}" placeholder="Max" aria-label="Prix maximum" class="{{ $field }}">
                        </div>
                    </div>

                    @if(count($brands ?? []))
                    <fieldset>
                        <legend class="text-sm font-semibold text-navy-900 mb-2">Marques</legend>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2.5">
                            @foreach ($brands as $b)
                                <label class="flex items-center gap-2 text-[15px] text-navy-800 cursor-pointer">
                                    <input type="checkbox" name="brand_id[]" value="{{ $b->id }}" {{ in_array($b->id, (array) request('brand_id', [])) ? 'checked' : '' }} class="rounded text-navy-900 focus:ring-gold-500 h-5 w-5 border-navy-300">
                                    {{ $b->name }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    @endif

                    <div class="space-y-3">
                        <button type="submit" class="w-full btn-primary-gold justify-center py-3 text-base">Appliquer les filtres</button>
                        @if($hasFilters)
                            <a href="{{ route('shop.index', request()->only(['search'])) }}" class="block text-center text-sm font-semibold text-navy-600 hover:text-navy-900 underline">Réinitialiser les filtres</a>
                        @endif
                    </div>
                </form>

                <!-- Dernier arrivage / bon plan -->
                @if(isset($promoProduct))
                @php
                    $promoRawPath = $promoProduct->image ?: $promoProduct->images->first()->path ?? null;
                    $promoImgPath = $promoRawPath ? preg_replace('#^(/?storage/)#', '', $promoRawPath) : null;
                    $promoImgUrl = ($promoImgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($promoImgPath))
                        ? '/storage/' . ltrim($promoImgPath, '/')
                        : ($promoRawPath ?: asset('logo.jpeg'));
                @endphp
                <div class="rounded-lg bg-gold-500 text-navy-900 p-5">
                    <span class="inline-block bg-navy-900 text-white text-sm font-semibold px-3 py-1 rounded-full mb-4">
                        {{ $promoProduct->promo_price ? 'Bon plan' : 'Dernier arrivage' }}
                    </span>
                    <div class="bg-white rounded-md p-4 mb-4">
                        <img src="{{ $promoImgUrl }}" alt="{{ $promoProduct->name }}" class="w-full h-44 object-contain">
                    </div>
                    <h3 class="font-semibold text-lg leading-snug line-clamp-2 mb-2">{{ $promoProduct->name }}</h3>
                    <p class="text-base font-bold mb-4">
                        @if($promoProduct->promo_price && $promoProduct->promo_price < $promoProduct->price)
                            {{ number_format($promoProduct->promo_price, 0, ',', ' ') }} CFA
                            <span class="text-sm font-medium line-through opacity-70 ml-2">{{ number_format($promoProduct->price, 0, ',', ' ') }} CFA</span>
                        @else
                            {{ number_format($promoProduct->price, 0, ',', ' ') }} CFA
                        @endif
                    </p>
                    <a href="{{ route('shop.show', $promoProduct->slug) }}" class="block text-center bg-navy-900 text-white font-semibold py-3 rounded-md hover:bg-navy-700 transition-colors">Voir le produit</a>
                </div>
                @endif
            </aside>

            <!-- Contenu -->
            <main class="flex-1 min-w-0">
                <!-- Recherche et tri -->
                <form action="{{ route('shop.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 mb-6">
                    @foreach(request()->only(['category_id', 'condition', 'blackfriday', 'price_min', 'price_max']) as $k => $v)
                        @if($v !== null && $v !== '')<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
                    @endforeach
                    @foreach((array) request('brand_id', []) as $bid)
                        <input type="hidden" name="brand_id[]" value="{{ $bid }}">
                    @endforeach
                    <div class="relative flex-1">
                        <label for="shop-search" class="sr-only">Rechercher un produit</label>
                        <input id="shop-search" type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit" class="{{ $field }} pr-12 py-3">
                        <button type="submit" aria-label="Lancer la recherche" class="absolute right-0 top-0 h-full px-4 text-navy-700 hover:text-navy-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="shop-sort" class="text-sm font-semibold text-navy-900 whitespace-nowrap">Trier par</label>
                        <select id="shop-sort" name="sort" onchange="this.form.submit()" class="{{ $field }} py-3 md:w-52">
                            <option value="">Pertinence</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Nouveautés</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        </select>
                    </div>
                </form>

                <!-- Filtres actifs -->
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    @if(request('category_id') && ($selectedCat = \App\Models\Category::find(request('category_id'))))
                        <span class="{{ $chip }}">{{ $selectedCat->name }}
                            <a href="{{ route('shop.index', request()->except(['category_id', 'page'])) }}" aria-label="Retirer la catégorie" class="hover:text-red-600">{!! $closeIcon !!}</a>
                        </span>
                    @endif
                    @if(request('condition'))
                        <span class="{{ $chip }}">{{ $conditionLabel(request('condition')) }}
                            <a href="{{ request()->fullUrlWithQuery(['condition' => null, 'page' => null]) }}" aria-label="Retirer l'état" class="hover:text-red-600">{!! $closeIcon !!}</a>
                        </span>
                    @endif
                    @if(request('search'))
                        <span class="{{ $chip }}">« {{ request('search') }} »
                            <a href="{{ route('shop.index', request()->except(['search', 'page'])) }}" aria-label="Effacer la recherche" class="hover:text-red-600">{!! $closeIcon !!}</a>
                        </span>
                    @endif
                    @if(request('price_min') || request('price_max'))
                        <span class="{{ $chip }}">{{ request('price_min') ? number_format(request('price_min'), 0, ',', ' ') : '0' }} – {{ request('price_max') ? number_format(request('price_max'), 0, ',', ' ') : '∞' }} CFA
                            <a href="{{ route('shop.index', request()->except(['price_min', 'price_max', 'page'])) }}" aria-label="Retirer le prix" class="hover:text-red-600">{!! $closeIcon !!}</a>
                        </span>
                    @endif
                    @foreach((array) request('brand_id', []) as $bid)
                        @php $activeBrand = ($brands ?? collect())->firstWhere('id', (int) $bid); @endphp
                        @if($activeBrand)
                            <span class="{{ $chip }}">{{ $activeBrand->name }}
                                <a href="{{ route('shop.index', request()->except(['brand_id', 'page']) + ['brand_id' => array_values(array_diff((array) request('brand_id', []), [$bid]))]) }}" aria-label="Retirer la marque" class="hover:text-red-600">{!! $closeIcon !!}</a>
                            </span>
                        @endif
                    @endforeach
                    <span class="text-sm text-navy-700 sm:ml-auto"><strong class="text-navy-900">{{ $products->total() }}</strong> produit{{ $products->total() > 1 ? 's' : '' }}</span>
                </div>

                <!-- Produits -->
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
                    @forelse($products as $product)
                        @php
                            $rawPath = $product->image ?: $product->images->first()->path ?? null;
                            $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                            $imgUrl = ($imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath))
                                ? '/storage/' . ltrim($imgPath, '/')
                                : ($rawPath ?: asset('logo.jpeg'));
                            $onPromo = $product->promo_price && $product->promo_price < $product->price;
                        @endphp
                        <div class="group bg-white border border-navy-100 rounded-lg overflow-hidden flex flex-col">
                            <a href="{{ route('shop.show', $product->slug) }}" class="relative block bg-navy-50 p-4">
                                @if($product->isPreorderable())
                                    <span class="absolute top-2 left-2 z-10 bg-gold-500 text-navy-900 text-xs font-semibold px-2 py-1 rounded">Précommande</span>
                                @elseif($onPromo)
                                    <span class="absolute top-2 left-2 z-10 bg-navy-900 text-white text-xs font-semibold px-2 py-1 rounded">Promo</span>
                                @endif
                                @if($product->blackfriday)
                                    <span class="absolute top-2 right-2 z-10 bg-navy-900 text-gold-400 text-xs font-semibold px-2 py-1 rounded">Black Friday</span>
                                @endif
                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-full h-32 sm:h-40 object-contain mix-blend-multiply">
                            </a>

                            <div class="p-3 sm:p-4 flex flex-col flex-1">
                                <h3 class="text-[15px] font-medium text-navy-900 line-clamp-2 min-h-[2.75rem] mb-2 leading-snug">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="hover:text-gold-600">{{ $product->name }}</a>
                                </h3>

                                <div class="mb-3">
                                    @if($onPromo)
                                        <span class="text-lg font-semibold text-navy-900">{{ number_format($product->promo_price, 0, ',', ' ') }} <span class="text-xs font-medium">CFA</span></span>
                                        <span class="block text-sm text-navy-400 line-through">{{ number_format($product->price, 0, ',', ' ') }} CFA</span>
                                    @else
                                        <span class="text-lg font-semibold text-navy-900">{{ number_format($product->price, 0, ',', ' ') }} <span class="text-xs font-medium">CFA</span></span>
                                    @endif
                                    @if($product->isPreorderable() && $product->available_at)
                                        <span class="block text-sm text-gold-700">Disponible le {{ $product->available_at->format('d/m/Y') }}</span>
                                    @endif
                                </div>

                                <form action="{{ route('shop.addToCart', $product->id) }}" method="POST" class="mt-auto">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full bg-gold-500 text-navy-900 font-semibold py-2.5 rounded-md hover:bg-gold-400 transition-colors text-[15px]">
                                        {{ $product->isPreorderable() ? 'Précommander' : 'Ajouter au panier' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <h3 class="text-xl font-semibold text-navy-900">Aucun produit trouvé</h3>
                            <p class="text-base text-navy-500 mt-2">Essayez de modifier vos filtres ou votre recherche.</p>
                            <a href="{{ route('shop.index') }}" class="mt-6 inline-block btn-primary-gold">Réinitialiser les filtres</a>
                        </div>
                    @endforelse
                </div>

                <div class="mt-10 flex justify-center">
                    {{ $products->links() }}
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
