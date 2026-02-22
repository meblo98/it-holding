@extends('layouts.admin')

@section('title', 'Gestion de la Boutique - Admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Boutique</h1>
        <a href="{{ route('admin.products.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Nouveau Produit
        </a>
    </div>

    @if (session('success'))
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
                                    @if ($product->images->count())
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="{{ asset('storage/' . $product->images->first()->path) }}" alt="">
                                    @elseif($product->image)
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="{{ asset('storage/' . $product->image) }}" alt="">
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold">
                                            {{ substr($product->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-indigo-600 truncate">
                                        {{ $product->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ number_format($product->price, 0, ',', ' ') }} FCFA | Stock:
                                        {{ $product->stock }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->active ? 'Actif' : 'Inactif' }}
                                </span>
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 p-2 rounded hover:bg-gray-50"
                                    title="Éditer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536M4 20l4-1 9.293-9.293a1 1 0 00-1.414-1.414L6.586 17.586 5 19l-1 1z" />
                                    </svg>
                                    <span class="sr-only">Éditer</span>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 p-2 rounded hover:bg-red-50"
                                        title="Supprimer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                                        </svg>
                                        <span class="sr-only">Supprimer</span>
                                    </button>
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
