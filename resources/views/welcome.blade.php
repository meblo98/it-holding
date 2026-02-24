@extends('layouts.app')

@section('content')
    <!-- Hero Section - Kanje Style -->
    <section class="bg-gradient-to-br from-gold-400 via-gold-500 to-navy-500 relative overflow-hidden">
        <div class="max-w-7xl mx-auto py-20 px-4 sm:py-28 sm:px-6 lg:px-8">
            <div class="relative z-10">
                <!-- Promo Badge -->
                <div
                    class="inline-flex items-center bg-navy-700 text-white px-6 py-2 rounded-full text-sm font-bold uppercase mb-6">
                    <span class="bg-gold-500 text-navy-900 px-3 py-1 rounded-full mr-3 text-xs">Promo</span>
                    Offres Exceptionnelles
                </div>

                <h1 class="text-4xl tracking-tight font-extrabold text-navy-900 sm:text-5xl md:text-6xl">
                    <span class="block text-white">Bienvenue chez</span>
                    <span class="block text-navy-800 mt-2">{{ config('app.name') }}</span>
                </h1>
                <p class="mt-6 max-w-2xl text-lg text-navy-700 sm:text-xl">
                    Votre partenaire de confiance pour tous vos besoins en services informatiques, maintenance, numérisation
                    et plus encore.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('services.index') }}"
                        class="inline-flex items-center px-8 py-4 border-2 border-navy-800 text-base font-bold rounded-lg text-navy-900 bg-white hover:bg-navy-50 transition-all duration-200 shadow-xl hover:shadow-2xl">
                        Nos Services
                    </a>
                    <a href="{{ route('contact.index') }}"
                        class="inline-flex items-center px-8 py-4 border-2 border-white text-base font-bold rounded-lg text-white bg-navy-700 hover:bg-navy-800 transition-all duration-200">
                        Contactez-nous
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Preview - Kanje Style -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-navy-900">Nos Solutions</h2>
                <p class="mt-4 text-lg text-gray-600">Découvrez comment nous pouvons vous aider à propulser votre
                    entreprise.</p>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Service 1 -->
                <div
                    class="bg-white border-2 border-gray-100 hover:border-gold-400 overflow-hidden rounded-xl transition-all duration-200 hover:shadow-lg">
                    <div class="px-6 py-8">
                        <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-navy-900">Services Numériques</h3>
                        <p class="mt-3 text-sm text-gray-600">Développement web, mobile et transformation digitale.</p>
                    </div>
                </div>

                <!-- Service 2 -->
                <div
                    class="bg-white border-2 border-gray-100 hover:border-gold-400 overflow-hidden rounded-xl transition-all duration-200 hover:shadow-lg">
                    <div class="px-6 py-8">
                        <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-navy-900">Maintenance & Réparation</h3>
                        <p class="mt-3 text-sm text-gray-600">Assistance technique pour vos équipements informatiques.</p>
                    </div>
                </div>

                <!-- Service 3 -->
                <div
                    class="bg-white border-2 border-gray-100 hover:border-gold-400 overflow-hidden rounded-xl transition-all duration-200 hover:shadow-lg">
                    <div class="px-6 py-8">
                        <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-navy-900">Vidéosurveillance</h3>
                        <p class="mt-3 text-sm text-gray-600">Sécurisez vos locaux avec nos solutions de contrôle d'accès.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    @if ($products->count() > 0)
        <section class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-extrabold text-navy-900">Nos Produits</h2>
                    <p class="mt-4 text-lg text-gray-600">Découvrez notre sélection de produits de qualité</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <a href="{{ route('shop.show', $product->slug) }}"
                            class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden relative">
                            <!-- Badge Nouveau -->
                            @if ($product->created_at->diffInDays(now()) < 30)
                                <span
                                    class="absolute top-3 left-3 z-10 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-md uppercase">
                                    Nouveau
                                </span>
                            @endif

                            <div class="bg-gray-50 p-6">
                                @if ($product->image)
                                    @php
                                        // Prefer product.image, fallback to first product image from images relation
                                        $imgPath = $product->image ?: $product->images->first()->path ?? null;
                                        if (
                                            $imgPath &&
                                            \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)
                                        ) {
                                            $imgUrl = \Illuminate\Support\Facades\Storage::disk('public')->url(
                                                $imgPath,
                                            );
                                        } elseif ($imgPath && filter_var($imgPath, FILTER_VALIDATE_URL)) {
                                            $imgUrl = $imgPath;
                                        } else {
                                            $imgUrl = $imgPath ? asset('storage/' . ltrim($imgPath, '/')) : null;
                                        }
                                    @endphp
                                    <div class="w-full h-48 sm:h-56 lg:h-48 rounded-lg overflow-hidden bg-white">
                                        @if ($imgUrl)
                                            <img loading="lazy" src="{{ $imgUrl }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div
                                        class="w-full h-48 sm:h-56 lg:h-48 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3
                                    class="text-sm font-medium text-navy-600 hover:text-gold-500 transition-colors duration-200 line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-2xl font-bold text-gray-900">
                                        {{ number_format($product->price, 0, ',', ' ') }} <span class="text-sm">FCFA</span>
                                    </span>
                                </div>
                                @if ($product->stock > 0)
                                    <span class="inline-block mt-2 text-xs font-semibold text-green-600">
                                        En stock
                                    </span>
                                @else
                                    <span class="inline-block mt-2 text-xs font-semibold text-red-600">
                                        Rupture de stock
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12 text-center">
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center px-8 py-4 border-2 border-navy-700 text-base font-bold rounded-lg text-navy-900 bg-white hover:bg-navy-50 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Voir tous les produits
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    @endif
@endsection
