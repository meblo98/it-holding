@extends('layouts.admin')

@section('title', 'Gestion de la Boutique - Admin')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Boutique</h1>
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto items-stretch sm:items-center">
            <form action="{{ route('admin.products.index') }}" method="GET" class="relative flex-grow sm:flex-grow-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit..." 
                    class="w-full sm:w-64 border-gray-300 rounded-md shadow-sm pl-10 pr-10 py-2 text-sm focus:ring-gold-500 focus:border-gold-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.products.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" title="Effacer la recherche">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500">
                Nouveau Produit
            </a>
        </div>
    </div>

    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Import / export du catalogue</h2>
                <p class="mt-1 text-xs text-gray-500">
                    Formats JSON et CSV (tableur, séparateur « ; » et colonnes identiques à l’export).
                    Un produit portant le même slug sera mis à jour.
                    Les images et fiches techniques ne sont pas incluses.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.products.export', 'json') }}"
                    class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Exporter JSON
                </a>
                <a href="{{ route('admin.products.export', 'csv') }}"
                    class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Exporter CSV
                </a>
                <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data"
                    class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    @csrf
                    <input type="file" name="file" accept=".json,.csv,.txt,application/json,text/csv"
                        required
                        class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-md bg-navy-600 px-4 py-2 text-sm font-medium text-white hover:bg-navy-700">
                        Importer
                    </button>
                </form>
            </div>
        </div>
        @if ($errors->any())
            <div class="mt-3 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" role="alert">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
                                    @php
                                        $rawPath = $product->image ?: $product->images->first()->path ?? null;
                                        $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                                        if (
                                            $imgPath &&
                                            \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)
                                        ) {
                                            $thumb = '/storage/' . ltrim($imgPath, '/');
                                        } elseif ($rawPath && filter_var($rawPath, FILTER_VALIDATE_URL)) {
                                            $thumb = $rawPath;
                                        } else {
                                            $thumb = $imgPath ? asset('storage/' . ltrim($imgPath, '/')) : null;
                                        }
                                    @endphp

                                    @if ($thumb)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $thumb }}"
                                            alt="">
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600 font-bold">
                                            {{ substr($product->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-navy-600 truncate flex items-center gap-2">
                                        {{ $product->name }}
                                        @if($product->is_pack)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                                                Pack
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Achat: {{ number_format($product->purchase_price, 0, ',', ' ') }} FCFA | Vente: {{ number_format($product->price, 0, ',', ' ') }} FCFA | Stock: {{ $product->stock }} | Garantie: {{ $product->warranty_duration_months ?? 12 }} mois
                                        @if($product->available_at)
                                            | <span class="text-amber-600 font-medium">Disponible le: {{ $product->available_at->format('d/m/Y') }} (Précommande)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->active ? 'Actif' : 'Inactif' }}
                                </span>
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="text-navy-600 hover:text-navy-900 p-2 rounded hover:bg-gray-50"
                                    title="Éditer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536M4 20l4-1 9.293-9.293a1 1 0 00-1.414-1.414L6.586 17.586 5 19l-1 1z" />
                                    </svg>
                                    <span class="sr-only">Éditer</span>
                                </a>
                                <form action="{{ route('admin.products.duplicate', $product->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir dupliquer ce produit ?');">
                                    @csrf
                                    <button type="submit"
                                        class="text-navy-600 hover:text-navy-900 p-2 rounded hover:bg-gray-50"
                                        title="Dupliquer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                        </svg>
                                        <span class="sr-only">Dupliquer</span>
                                    </button>
                                </form>
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
    </div>
@endsection
