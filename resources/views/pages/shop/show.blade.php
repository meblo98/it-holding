@extends('layouts.app')

@section('title', $product->name . ' - Boutique')

@section('content')
<div class="bg-white">
    <div class="pt-6">
        <!-- Image gallery -->
        <div class="mt-6 max-w-2xl mx-auto sm:px-6 lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:gap-x-8">
            <div class="hidden aspect-w-3 aspect-h-4 rounded-lg overflow-hidden lg:block">
                 @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-center object-cover">
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                        <span>Image Produit</span>
                    </div>
                @endif
            </div>
            
            <!-- Product info -->
            <div class="max-w-2xl mx-auto pt-10 pb-16 px-4 sm:px-6 lg:max-w-7xl lg:pt-16 lg:pb-24 lg:px-8 lg:grid lg:grid-cols-1 lg:grid-rows-[auto,auto,1fr] lg:gap-x-8">
                <div class="lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
                    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">{{ $product->name }}</h1>
                </div>

                <!-- Options -->
                <div class="mt-4 lg:mt-0 lg:row-span-3">
                    <h2 class="sr-only">Information produit</h2>
                    <p class="text-3xl text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>

                    <div class="mt-6">
                        <h3 class="sr-only">Description</h3>
                        <div class="text-base text-gray-900 space-y-6">
                            <p>{{ $product->description }}</p>
                        </div>
                    </div>

                    <form class="mt-10" action="{{ url('/shop/add-to-cart/' . $product->id) }}" method="GET">
                        @csrf
                        <button type="submit" class="mt-10 w-full bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ajouter au panier
                        </button>
                    </form>
                </div>

                <div class="py-10 lg:pt-6 lg:pb-16 lg:col-start-1 lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
                    <!-- Description and details -->
                    <div>
                        <h3 class="sr-only">Détails</h3>
                        <div class="space-y-6">
                            <p class="text-base text-gray-900">{{ $product->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
