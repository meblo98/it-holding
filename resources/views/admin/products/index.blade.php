@extends('layouts.admin')

@section('title', 'Gestion de la Boutique - Admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Boutique</h1>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Nouveau Produit
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($product->image)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $product->image) }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold">
                                                    {{ substr($product->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-indigo-600 truncate">
                                                {{ $product->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ number_format($product->price, 0, ',', ' ') }} FCFA | Stock: {{ $product->stock }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->active ? 'Actif' : 'Inactif' }}
                                        </span>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Editer</a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium bg-transparent border-0 cursor-pointer">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                            Aucun produit en boutique.
                        </li>
                    @endforelse
                </ul>
            </div>
            
            <div class="mt-4">
                {{ $products->links() }}
@endsection
