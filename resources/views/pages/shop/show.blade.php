@extends('layouts.app')

@section('title', $product->name . ' - IT-Holding Boutique')
@section('meta_description', Str::limit(strip_tags($product->description), 160))
@section('meta_keywords', $product->name . ', ' . ($product->category->name ?? '') . ', hardware Sénégal, prix informatique Dakar')

@section('og_type', 'product')
@section('og_image', $product->image ? asset('storage/' . $product->image) : asset('logo.jpeg'))

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
                <a href="{{ route('shop.index') }}" class="hover:text-navy-900">Boutique</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @if ($product->category)
                    <a href="{{ route('shop.index', ['category_id' => $product->category->id]) }}" class="hover:text-navy-900">{{ $product->category->name }}</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @endif
                <span class="text-gold-600 font-medium truncate">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if (session('success'))
            <div class="mb-6 bg-green-50 text-green-700 p-4 rounded-lg flex items-center gap-3 border border-green-100">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            <!-- Left: Gallery -->
            <div x-data="{ mainImage: '{{ $product->image ? (preg_match('#^/?storage/#', $product->image) ? $product->image : '/storage/'.ltrim($product->image, '/')) : asset('logo.jpeg') }}' }" class="space-y-6">
                <!-- Main Image -->
                <div class="aspect-square bg-gray-50 rounded-lg overflow-hidden border border-gray-100 flex items-center justify-center p-8 group relative">
                    <img :src="mainImage" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain transform group-hover:scale-105 transition-transform duration-500">
                </div>

                <!-- Thumbnails Gallery -->
                @if($product->images->count() > 0)
                <div class="relative px-8">
                    <div class="flex items-center gap-4 overflow-x-auto py-2">
                        @foreach($product->images as $img)
                            @php
                                $tPath = preg_match('#^/?storage/#', $img->path) ? $img->path : '/storage/'.ltrim($img->path, '/');
                            @endphp
                            <button @click="mainImage = '{{ $tPath }}'" 
                                class="flex-shrink-0 w-20 h-20 border-2 rounded-lg p-2 bg-white transition-all overflow-hidden"
                                :class="mainImage === '{{ $tPath }}' ? 'border-gold-500' : 'border-gray-100 hover:border-gold-200'">
                                <img src="{{ $tPath }}" class="w-full h-full object-contain">
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right: Product Info -->
            <div class="flex flex-col">
                <!-- Rating & Feedback -->
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex text-gold-500">
                        @for($i=0; $i<5; $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="text-xs font-bold text-navy-900 uppercase tracking-widest">4.7 Star Rating</span>
                    <span class="text-xs text-gray-400 whitespace-nowrap">(2,350 Retours utilisateurs)</span>
                </div>

                <!-- Title -->
                <h1 class="text-2xl lg:text-3xl font-bold text-navy-900 mb-6 leading-tight">{{ $product->name }}</h1>

                <!-- Meta Details -->
                <div class="grid grid-cols-2 gap-y-4 mb-8 text-sm">
                    <div>
                        <span class="text-gray-400 block mb-1">REF:</span>
                        <span class="font-bold text-navy-900">{{ $product->sku ?: 'IT-'.str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Disponibilité:</span>
                        <span class="font-bold text-green-600">{{ $product->stock > 0 ? 'En stock' : 'Rupture de stock' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Marque:</span>
                        <span class="font-bold text-navy-900">{{ $product->brand->name ?? 'IT-HOLDING' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Catégorie:</span>
                        <span class="font-bold text-navy-900">{{ $product->category->name ?? 'Informatique' }}</span>
                    </div>
                </div>

                <!-- Price -->
                <div class="flex items-center gap-4 mb-10">
                    <span class="text-3xl font-black text-gold-500">{{ number_format($product->promo_price ?: $product->price, 0, ',', ' ') }} <span class="text-sm font-bold">CFA</span></span>
                    @if($product->promo_price && $product->promo_price < $product->price)
                        <span class="text-xl text-gray-300 line-through">{{ number_format($product->price, 0, ',', ' ') }} CFA</span>
                        @php
                            $discount = round((($product->price - $product->promo_price) / $product->price) * 100);
                        @endphp
                        <span class="bg-gold-100 text-gold-600 px-3 py-1 rounded text-xs font-black">{{ $discount }}% OFF</span>
                    @endif
                </div>

                <!-- Variants (Mock) -->
                @if($product->category && in_array(strtolower($product->category->name), ['ordinateur', 'ordinateur portable', 'pc', 'laptop', 'macbook']))
                <div class="grid grid-cols-2 gap-6 mb-10">
                    <div class="space-y-3">
                        <label class="text-sm font-bold text-navy-900 uppercase tracking-tighter italic">Mémoire Vive (RAM)</label>
                        <select class="w-full border-gray-200 rounded-lg text-sm focus:ring-gold-500 focus:border-gold-500">
                            <option>8GB unified memory</option>
                            <option>16GB unified memory</option>
                            <option>32GB unified memory</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="text-sm font-bold text-navy-900 uppercase tracking-tighter italic">Stockage (SSD)</label>
                        <select class="w-full border-gray-200 rounded-lg text-sm focus:ring-gold-500 focus:border-gold-500">
                            <option>256GB SSD Storage</option>
                            <option>512GB SSD Storage</option>
                            <option>1TB SSD Storage</option>
                        </select>
                    </div>
                </div>
                @endif

                <!-- Add to Cart -->
                <form action="{{ route('shop.addToCart', $product->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="flex items-center gap-4">
                        <div x-data="{ qty: 1 }" class="flex items-center border-2 border-gray-100 rounded-lg p-1 bg-gray-50">
                            <button type="button" @click="if(qty > 1) qty--" class="w-10 h-10 flex items-center justify-center text-navy-900 hover:bg-white rounded transition-colors">-</button>
                            <input type="number" name="quantity" x-model="qty" class="w-12 text-center border-none bg-transparent font-bold focus:ring-0 text-navy-900" min="1" max="{{ $product->stock }}">
                            <button type="button" @click="if(qty < {{ $product->stock }}) qty++" class="w-10 h-10 flex items-center justify-center text-navy-900 hover:bg-white rounded transition-colors">+</button>
                        </div>
                        <button type="submit" class="flex-grow btn-primary-gold h-12 uppercase tracking-widest text-xs flex items-center justify-center gap-4 disabled:opacity-50 disabled:cursor-not-allowed" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            Ajouter au Panier
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </button>
                        <button type="button" class="w-12 h-12 border-2 border-gray-100 rounded-lg flex items-center justify-center text-navy-900 hover:border-gold-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
                    </div>
                </form>

                <!-- Features Badges -->
                <div class="mt-auto pt-8 border-t border-gray-50 flex items-center gap-6">
                    <span class="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                        <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04c0 4.835 1.355 9.347 3.718 13.191A11.96 11.96 0 0012 21.481c2.901 0 5.537-.94 7.653-2.545a11.959 11.959 0 013.718-13.191z"/></svg>
                        Paiement Sécurisé
                    </span>
                    <span class="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                        <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        Expédition 24H
                    </span>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div x-data="{ tab: 'description' }" class="border border-gray-100 rounded-2xl overflow-hidden mb-16">
            <div class="flex bg-gray-50 border-b border-gray-100 overflow-x-auto">
                <button @click="tab = 'description'" :class="tab === 'description' ? 'bg-white border-b-2 border-gold-500 text-navy-900' : 'text-gray-400 hover:text-navy-900'" class="px-8 py-5 text-xs font-bold uppercase tracking-widest transition-all">Description de l'Article</button>
                <button @click="tab = 'specs'" :class="tab === 'specs' ? 'bg-white border-b-2 border-gold-500 text-navy-900' : 'text-gray-400 hover:text-navy-900'" class="px-8 py-5 text-xs font-bold uppercase tracking-widest transition-all">Fiche Technique</button>
                <button @click="tab = 'shipping'" :class="tab === 'shipping' ? 'bg-white border-b-2 border-gold-500 text-navy-900' : 'text-gray-400 hover:text-navy-900'" class="px-8 py-5 text-xs font-bold uppercase tracking-widest transition-all">Livraison & Garanties</button>
                <button @click="tab = 'reviews'" :class="tab === 'reviews' ? 'bg-white border-b-2 border-gold-500 text-navy-900' : 'text-gray-400 hover:text-navy-900'" class="px-8 py-5 text-xs font-bold uppercase tracking-widest transition-all">Avis Clients</button>
            </div>
            <div class="p-8 lg:p-12">
                <div x-show="tab === 'description'" x-transition class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <div class="lg:col-span-2 prose prose-navy max-w-none text-sm leading-8 text-gray-500">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>
                <div x-show="tab === 'specs'" x-transition class="max-w-3xl">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100 italic">
                            @if(is_array($product->specs) || is_object($product->specs))
                                @foreach($product->specs as $key => $value)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 font-bold text-navy-900 uppercase tracking-tighter w-1/3">{{ $key }}</td>
                                    <td class="py-4 text-gray-500">{{ $value }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Related Products Grid -->
        <div class="space-y-8">
            <h3 class="text-xl font-bold text-navy-900 uppercase tracking-tighter italic">Recommandations pour vous</h3>
            @php
                $relatedProducts = \App\Models\Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->take(4)->get();
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                <div class="bg-white border border-gray-100 rounded-lg p-4 group hover:shadow-xl hover:border-gold-200 transition-all duration-300">
                    <a href="{{ route('shop.show', $rel->slug) }}" class="block">
                        <div class="relative aspect-square mb-4 bg-gray-50 rounded-md overflow-hidden flex items-center justify-center p-4">
                            @php
                                $rPath = $rel->image ?: $rel->images->first()->path ?? null;
                                $rImgUrl = $rPath ? (preg_match('#^/?storage/#', $rPath) ? $rPath : '/storage/'.ltrim($rPath, '/')) : asset('logo.jpeg');
                            @endphp
                            <img src="{{ $rImgUrl }}" alt="{{ $rel->name }}" class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-navy-900 line-clamp-2 h-10 group-hover:text-gold-600 transition-colors">
                                {{ $rel->name }}
                            </h3>
                            <span class="text-lg font-black text-navy-900">{{ number_format($rel->price, 0, ',', ' ') }} <span class="text-[10px]">CFA</span></span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
