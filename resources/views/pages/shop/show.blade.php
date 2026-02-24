@extends('layouts.app')

@section('title', $product->name . ' - Boutique')

@section('content')
    <div class="bg-gradient-to-br from-gray-50 to-white min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            @if (session('success'))
                <div class="mb-8 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm animate-fade-in">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-8 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm animate-fade-in">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modern Product Card -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <div class="lg:grid lg:grid-cols-5 lg:gap-8">
                    <!-- Breadcrumb -->
                    <div class="col-span-full px-8 pt-6">
                        <nav class="text-sm text-gray-500">
                            <a href="{{ route('shop.index') }}" class="hover:underline">Boutique</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('shop.index', ['category' => $product->category ?? 'all']) }}"
                                class="hover:underline">{{ $product->category ?? 'Produits' }}</a>
                            <span class="mx-2">/</span>
                            <span class="text-gray-700">{{ $product->name }}</span>
                        </nav>
                    </div>
                    <!-- Compact Image Section / Gallery -->
                    <div class="lg:col-span-2 bg-gradient-to-br from-gray-50 to-gray-100 p-8">
                        <div class="w-full max-w-md mx-auto">
                            @if ($product->images->count())
                                @php
                                    $rawPath = $product->image ?: $product->images->first()->path ?? null;
                                    $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                                    if (
                                        $imgPath &&
                                        \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)
                                    ) {
                                        $mainUrl = '/storage/' . ltrim($imgPath, '/');
                                    } elseif ($rawPath && filter_var($rawPath, FILTER_VALIDATE_URL)) {
                                        $mainUrl = $rawPath;
                                    } else {
                                        $mainUrl = $imgPath ? asset('storage/' . ltrim($imgPath, '/')) : null;
                                    }
                                @endphp

                                <!-- Main Image -->
                                <div class="rounded-2xl overflow-hidden shadow-lg bg-white">
                                    <img id="main-image" loading="lazy" src="{{ $mainUrl }}" alt="{{ $product->name }}"
                                        class="w-full h-96 object-contain transition-opacity duration-300">
                                </div>

                                <!-- Thumbnail Gallery -->
                                @if ($product->images->count() > 1)
                                    <div class="mt-4 flex items-center gap-2 overflow-x-auto pb-2">
                                        @foreach ($product->images as $index => $img)
                                            @php
                                                $rawThumb = $img->path;
                                                $tPath = $rawThumb
                                                    ? preg_replace('#^(/?storage/)#', '', $rawThumb)
                                                    : null;
                                                if (
                                                    $tPath &&
                                                    \Illuminate\Support\Facades\Storage::disk('public')->exists($tPath)
                                                ) {
                                                    $thumbUrl = '/storage/' . ltrim($tPath, '/');
                                                } elseif ($rawThumb && filter_var($rawThumb, FILTER_VALIDATE_URL)) {
                                                    $thumbUrl = $rawThumb;
                                                } else {
                                                    $thumbUrl = $tPath ? asset('storage/' . ltrim($tPath, '/')) : null;
                                                }
                                            @endphp
                                            <button type="button"
                                                class="gallery-thumb flex-shrink-0 border-2 rounded-lg {{ $index === 0 ? 'border-indigo-500' : 'border-gray-300' }} hover:border-indigo-400 transition-colors focus:outline-none"
                                                data-image="{{ $thumbUrl }}" onclick="changeMainImage(this)">
                                                <img loading="lazy" src="{{ $thumbUrl }}"
                                                    alt="Image {{ $index + 1 }}"
                                                    class="w-16 h-16 rounded-lg object-cover">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div
                                    class="aspect-square bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center shadow-lg">
                                    <div class="text-center">
                                        <svg class="mx-auto h-20 w-20 text-indigo-300" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="mt-3 text-indigo-400 font-medium text-sm">Image du produit</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info Section -->
                    <div class="lg:col-span-3 p-8 lg:p-12">
                        <!-- Product Title -->
                        <div class="mb-6">
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                            <div class="h-1 w-20 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full"></div>
                        </div>

                        <!-- Price and Stock -->
                        <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-200">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Prix</p>
                                <p
                                    class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                    {{ number_format($product->price, 0, ',', ' ') }} <span class="text-2xl">FCFA</span>
                                </p>
                            </div>

                            @if ($product->stock > 0)
                                <div class="text-right">
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border border-green-200">
                                        <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ $product->stock }} en stock
                                    </span>
                                </div>
                            @else
                                <div class="text-right">
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-50 text-red-700 border border-red-200">
                                        <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Rupture de stock
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Description + Specifications tabs -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                                <div class="text-sm text-gray-500">Référence: {{ $product->sku ?? 'N/A' }}</div>
                            </div>

                            <div class="border-b mb-4">
                                <nav class="flex -mb-px space-x-6" aria-label="Tabs">
                                    <button type="button"
                                        class="tab-btn pb-3 border-b-2 border-indigo-600 text-indigo-600 font-medium"
                                        data-target="desc">Description</button>
                                    <button type="button"
                                        class="tab-btn pb-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800"
                                        data-target="specs">Spécifications</button>
                                    <button type="button"
                                        class="tab-btn pb-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800"
                                        data-target="reviews">Avis</button>
                                </nav>
                            </div>

                            <div id="tab-desc" class="tab-content">
                                <div class="prose prose-sm text-gray-600 leading-relaxed">
                                    <p>{{ $product->description }}</p>
                                </div>
                            </div>

                            <div id="tab-specs" class="tab-content hidden">
                                @if (!empty($product->specs))
                                    <ul class="list-disc pl-5 text-gray-700">
                                        @foreach ($product->specs as $key => $val)
                                            <li><strong class="text-gray-900">{{ $key }}:</strong>
                                                {{ $val }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-600">Aucune spécification détaillée disponible.</p>
                                @endif
                            </div>

                            <div id="tab-reviews" class="tab-content hidden">
                                <p class="text-gray-600">Les avis seront disponibles prochainement.</p>
                            </div>
                        </div>

                        <form action="{{ route('shop.addToCart', $product->id) }}" method="POST"
                            class="mt-10 lg:sticky lg:top-28">
                            @csrf

                            @if ($product->stock > 0)
                                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                                    <div class="flex-shrink-0 w-full sm:w-auto">
                                        <label for="quantity"
                                            class="block text-sm font-semibold text-gray-900 mb-3">Quantité</label>
                                        <div class="flex items-center space-x-3">
                                            <button type="button" onclick="decrementQuantity()"
                                                class="inline-flex items-center justify-center w-12 h-12 border-2 border-gray-300 rounded-lg text-gray-600 hover:border-indigo-500 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 12H4" />
                                                </svg>
                                            </button>

                                            <input type="number" id="quantity" name="quantity" value="1"
                                                min="1" max="{{ $product->stock }}"
                                                class="w-20 text-center text-xl font-semibold border-2 border-gray-300 rounded-lg py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">

                                            <button type="button" onclick="incrementQuantity({{ $product->stock }})"
                                                class="inline-flex items-center justify-center w-12 h-12 border-2 border-gray-300 rounded-lg text-gray-600 hover:border-indigo-500 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <span class="text-sm text-gray-600">Partager:</span>
                                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($product->name) }}&url={{ urlencode(request()->fullUrl()) }}"
                                                target="_blank" class="text-blue-500 hover:underline text-sm">Twitter</a>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                                                target="_blank" class="text-blue-700 hover:underline text-sm">Facebook</a>
                                        </div>

                                        <button type="submit"
                                            class="w-full sm:flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-xl shadow-lg py-3 px-6 flex items-center justify-center text-base font-semibold text-white hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-105 transition-all duration-200 h-12">
                                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Ajouter au panier
                                        </button>
                                    </div>
                                </div>
                            @else
                                <button type="button" disabled
                                    class="w-full bg-gray-300 border border-transparent rounded-xl py-4 px-8 flex items-center justify-center text-lg font-semibold text-gray-500 cursor-not-allowed">
                                    Produit indisponible
                                </button>
                            @endif
                        </form>

                        <!-- Delivery Information Card -->
                        <div
                            class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-100">
                            <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Garanties & Livraison
                            </h3>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="ml-3 text-sm text-gray-700 font-medium">Livraison rapide disponible</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="ml-3 text-sm text-gray-700 font-medium">Retour gratuit sous 30 jours</p>
                                </div>
                                <div class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="ml-3 text-sm text-gray-700 font-medium">Garantie qualité certifiée</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function incrementQuantity(max) {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            if (currentValue < max) {
                input.value = currentValue + 1;
            }
        }

        function decrementQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }

        // Gallery image switcher
        function changeMainImage(button) {
            const imageUrl = button.getAttribute('data-image');
            const mainImage = document.getElementById('main-image');
            if (mainImage && imageUrl) {
                mainImage.src = imageUrl;

                // Update active thumbnail border
                document.querySelectorAll('.gallery-thumb').forEach(thumb => {
                    thumb.classList.remove('border-indigo-500');
                    thumb.classList.add('border-gray-300');
                });
                button.classList.remove('border-gray-300');
                button.classList.add('border-indigo-500');
            }
        }

        // Tabs for product details
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    buttons.forEach(b => b.classList.remove('border-indigo-600',
                        'text-indigo-600'));
                    buttons.forEach(b => b.classList.add('border-transparent', 'text-gray-600'));

                    this.classList.remove('border-transparent');
                    this.classList.remove('text-gray-600');
                    this.classList.add('border-indigo-600', 'text-indigo-600');

                    const target = this.getAttribute('data-target');
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.add(
                        'hidden'));
                    const el = document.getElementById('tab-' + target.replace(/s$/, 's'));
                    if (el) el.classList.remove('hidden');
                });
            });
        });
    </script>
@endsection
