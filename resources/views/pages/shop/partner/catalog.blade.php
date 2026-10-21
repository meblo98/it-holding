@extends('layouts.app')

@section('title', 'Catalogue partenaire - ' . config('app.name'))

@section('content')
<div class="bg-navy-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('layouts.client_sidebar')

            <main class="flex-1 min-w-0 space-y-6">
                @include('pages.shop.partner._tabs', ['active' => 'catalog'])

                <div>
                    <h1 class="text-2xl font-semibold text-navy-900 tracking-tight">Catalogue</h1>
                    <p class="mt-1 text-navy-600">Choisissez un produit et partagez-le : votre lien de parrainage est déjà intégré à chaque bouton.</p>
                </div>

                <!-- Recherche -->
                <form method="GET" action="{{ route('dashboard.partner.catalog') }}" class="flex flex-col sm:flex-row gap-3">
                    <label for="cat-search" class="sr-only">Rechercher un produit</label>
                    <input id="cat-search" type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit"
                           class="flex-1 border border-navy-300 rounded-md py-3 px-3.5 text-base text-black bg-white placeholder-gray-500 focus:ring-2 focus:ring-gold-500 focus:border-navy-600">
                    <label for="cat-category" class="sr-only">Catégorie</label>
                    <select id="cat-category" name="category_id" onchange="this.form.submit()"
                            class="border border-navy-300 rounded-md py-3 px-3.5 text-base text-black bg-white focus:ring-2 focus:ring-gold-500 focus:border-navy-600 sm:w-64">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary-gold justify-center">Rechercher</button>
                </form>

                <p class="text-sm text-navy-700"><strong class="text-navy-900">{{ $products->total() }}</strong> produit{{ $products->total() > 1 ? 's' : '' }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @forelse($products as $product)
                        @php
                            $rawPath = $product->image ?: $product->images->first()->path ?? null;
                            $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                            $imgUrl = ($imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath))
                                ? '/storage/' . ltrim($imgPath, '/')
                                : ($rawPath ?: asset('logo.jpeg'));
                            $price = ($product->promo_price && $product->promo_price < $product->price) ? $product->promo_price : $product->price;
                            $shareUrl = route('shop.show', $product->slug) . '?ref=' . urlencode($refCode);
                        @endphp
                        <article x-data="{ copied: false }" class="bg-white border border-navy-100 rounded-lg overflow-hidden flex flex-col">
                            <a href="{{ route('shop.show', $product->slug) }}" class="block bg-navy-50 p-4">
                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-full h-44 object-contain mix-blend-multiply">
                            </a>
                            <div class="p-4 flex flex-col flex-1">
                                <h2 class="text-base font-medium text-navy-900 line-clamp-2 min-h-[3rem]">{{ $product->name }}</h2>
                                <p class="mt-1 text-lg font-semibold text-navy-900">{{ number_format($price, 0, ',', ' ') }} <span class="text-xs font-medium">CFA</span></p>

                                <div class="mt-4 grid grid-cols-2 gap-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="btn-primary-gold justify-center col-span-2 py-2.5">Partager sur Facebook</a>
                                    <a href="https://wa.me/?text={{ urlencode($product->name . ' : ' . $shareUrl) }}" target="_blank" rel="noopener" class="btn-outline-navy justify-center py-2.5">WhatsApp</a>
                                    <button type="button" @click="navigator.clipboard.writeText('{{ $shareUrl }}'); copied = true; setTimeout(() => copied = false, 2000)" class="btn-outline-navy justify-center py-2.5" x-text="copied ? 'Lien copié' : 'Copier le lien'">Copier le lien</button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full bg-white border border-navy-100 rounded-lg p-10 text-center">
                            <h2 class="text-lg font-semibold text-navy-900">Aucun produit trouvé</h2>
                            <p class="mt-1 text-navy-600">Essayez une autre recherche ou une autre catégorie.</p>
                        </div>
                    @endforelse
                </div>

                <div class="flex justify-center">{{ $products->links() }}</div>
            </main>
        </div>
    </div>
</div>
@endsection
