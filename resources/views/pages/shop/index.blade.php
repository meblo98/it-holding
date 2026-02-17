@extends('layouts.app')

@section('title', 'Boutique - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-navy-900">Nos Produits</h2>
            <p class="mt-4 text-lg text-gray-600">Découvrez notre sélection complète de produits</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($products as $product)
                <a href="{{ route('shop.show', $product->slug) }}" class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden relative">
                    <!-- Badge Nouveau -->
                    @if($product->created_at->diffInDays(now()) < 30)
                    <span class="absolute top-3 left-3 z-10 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-md uppercase">
                        Nouveau
                    </span>
                    @endif
                    
                    <div class="bg-gray-50 p-6">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-contain group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-navy-600 hover:text-gold-500 transition-colors duration-200 line-clamp-2">
                            {{ $product->name }}
                        </h3>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-900">
                                {{ number_format($product->price, 0, ',', ' ') }} <span class="text-sm">FCFA</span>
                            </span>
                        </div>
                        @if($product->stock > 0)
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
            @empty
                <div class="col-span-full text-center bg-white rounded-xl shadow-sm p-12">
                     <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="mt-4 text-gray-600">Aucun produit disponible pour le moment.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-12">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
