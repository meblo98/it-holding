@extends('layouts.app')

@section('title', 'Boutique - ' . config('app.name'))

@section('content')
<div class="bg-white">
    <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
        <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Nos Produits</h2>

        <div class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
            @forelse($products as $product)
                <div class="group relative">
                    <div class="w-full min-h-80 bg-gray-200 aspect-w-1 aspect-h-1 rounded-md overflow-hidden group-hover:opacity-75 lg:h-80 lg:aspect-none">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-center object-cover lg:w-full lg:h-full">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <span>Image Produit</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 flex justify-between">
                        <div>
                            <h3 class="text-sm text-gray-700">
                                <a href="{{ route('shop.show', $product->slug) }}">
                                    <span aria-hidden="true" class="absolute inset-0"></span>
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</p>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 py-12">
                     <p>Aucun produit disponible pour le moment.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
