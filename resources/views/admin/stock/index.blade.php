@extends('layouts.admin')

@section('title', 'Gestion de Stock & Inventaire')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Gestion de Stock</h1>
        <p class="text-sm text-gray-500 mt-1">Valorisez vos inventaires, surveillez les ruptures et ajustez vos stocks en un clic.</p>
    </div>
    
    <!-- Tab Switches -->
    <div class="flex bg-gray-250 p-1 rounded-lg border border-gray-300">
        <a href="{{ route('admin.stock.index', ['tab' => 'status']) }}" class="px-4 py-2 text-sm font-semibold rounded-md transition {{ $tab === 'status' ? 'bg-navy-600 text-white shadow' : 'text-gray-600 hover:text-navy-600' }}">
            État du Stock
        </a>
        <a href="{{ route('admin.stock.index', ['tab' => 'movements']) }}" class="px-4 py-2 text-sm font-semibold rounded-md transition {{ $tab === 'movements' ? 'bg-navy-600 text-white shadow' : 'text-gray-600 hover:text-navy-600' }}">
            Historique des Mouvements
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-sm">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded shadow-sm">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<!-- Key Metrics Dashboard Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Purchase Valuation -->
    <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white rounded-lg p-5 shadow-sm">
        <p class="text-xs text-navy-200 uppercase font-bold tracking-wider">Valorisation (Achat H.T.)</p>
        <h3 class="text-2xl font-black mt-1">{{ number_format($totalPurchaseValue, 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></h3>
        <div class="text-[10px] text-navy-200 mt-2 bg-navy-500 bg-opacity-35 py-1 px-2 rounded w-fit">
            Coût d'acquisition global
        </div>
    </div>

    <!-- Sales Valuation -->
    <div class="bg-gradient-to-br from-gold-600 to-gold-700 text-white rounded-lg p-5 shadow-sm">
        <p class="text-xs text-gold-100 uppercase font-bold tracking-wider">Valorisation (Vente)</p>
        <h3 class="text-2xl font-black mt-1">{{ number_format($totalSalesValue, 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></h3>
        <div class="text-[10px] text-gold-100 mt-2 bg-gold-800 bg-opacity-20 py-1 px-2 rounded w-fit">
            Chiffre d'affaires potentiel
        </div>
    </div>

    <!-- Out of Stock Alert -->
    <div class="bg-white border {{ $outOfStockCount > 0 ? 'border-red-300 bg-red-50/20' : 'border-gray-150' }} rounded-lg p-5 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Produits en Rupture</p>
            <h3 class="text-3xl font-black mt-1 text-gray-900">{{ $outOfStockCount }}</h3>
        </div>
        <div class="p-3 rounded-full {{ $outOfStockCount > 0 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white border {{ $lowStockCount > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-gray-150' }} rounded-lg p-5 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Stock Critique (≤ 5)</p>
            <h3 class="text-3xl font-black mt-1 text-gray-900">{{ $lowStockCount }}</h3>
        </div>
        <div class="p-3 rounded-full {{ $lowStockCount > 0 ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-400' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>
</div>

<!-- ==================== TAB 1 : ETAT DU STOCK ==================== -->
@if($tab === 'status')
<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
    <!-- Filter Panel -->
    <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Search bar -->
        <form action="{{ route('admin.stock.index') }}" method="GET" class="w-full md:w-96 flex">
            <input type="hidden" name="tab" value="status">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="text" name="search" placeholder="Rechercher un produit par son nom..." value="{{ $search }}" class="block w-full border-gray-300 rounded-l-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
            <button type="submit" class="bg-navy-600 text-white px-4 rounded-r-md hover:bg-navy-700 transition flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>

        <!-- Category Filters -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.stock.index', ['tab' => 'status', 'search' => $search]) }}" class="px-3 py-1.5 text-xs font-bold rounded {{ empty($filter) ? 'bg-navy-100 text-navy-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Tous les produits
            </a>
            <a href="{{ route('admin.stock.index', ['tab' => 'status', 'filter' => 'out', 'search' => $search]) }}" class="px-3 py-1.5 text-xs font-bold rounded {{ $filter === 'out' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                En rupture (0)
            </a>
            <a href="{{ route('admin.stock.index', ['tab' => 'status', 'filter' => 'low', 'search' => $search]) }}" class="px-3 py-1.5 text-xs font-bold rounded {{ $filter === 'low' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Stock critique (≤ 5)
            </a>
            <a href="{{ route('admin.stock.index', ['tab' => 'status', 'filter' => 'sufficient', 'search' => $search]) }}" class="px-3 py-1.5 text-xs font-bold rounded {{ $filter === 'sufficient' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                En stock (> 5)
            </a>
        </div>
    </div>

    @if($products->isEmpty())
    <div class="p-12 text-center text-gray-500">
        Aucun produit ne correspond à ces critères de recherche ou de filtre.
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">P. Achat H.T.</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">P. Vente</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jauge de Stock</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actuel</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" x-data="{ editingProductId: null }">
                @foreach($products as $product)
                <tr class="hover:bg-gray-55 transition">
                    <!-- Product Details -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
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
                                <img src="{{ $thumb }}" class="w-10 h-10 object-cover rounded border" alt="{{ $product->name }}">
                            @else
                                <div class="w-10 h-10 bg-gray-100 flex items-center justify-center border rounded text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900 max-w-xs truncate">{{ $product->name }}</span>
                                <span class="text-xs text-gray-400">Catégorie: {{ $product->category->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- Purchase Price -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 font-medium">
                        {{ number_format($product->purchase_price ?? 0, 0, ',', ' ') }} FCFA
                    </td>

                    <!-- Sales Price -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-navy-950 font-bold">
                        @if($product->promo_price && $product->promo_price > 0 && $product->promo_price < $product->price)
                            <div class="flex flex-col items-end">
                                <span class="text-navy-600 font-bold">{{ number_format($product->promo_price, 0, ',', ' ') }} FCFA</span>
                                <span class="text-[10px] text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @else
                            {{ number_format($product->price, 0, ',', ' ') }} FCFA
                        @endif
                    </td>

                    <!-- Visual Stock Gauge -->
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if($product->stock <= 0)
                                <div class="w-full max-w-[120px] bg-gray-200 h-2.5 rounded-full overflow-hidden">
                                    <div class="bg-red-600 h-2.5 rounded-full" style="width: 100%"></div>
                                </div>
                                <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full uppercase tracking-wider">Rupture</span>
                            @elseif($product->stock <= 5)
                                <div class="w-full max-w-[120px] bg-gray-200 h-2.5 rounded-full overflow-hidden">
                                    <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ ($product->stock / 20) * 100 }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full uppercase tracking-wider">Faible</span>
                            @else
                                <div class="w-full max-w-[120px] bg-gray-200 h-2.5 rounded-full overflow-hidden">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ min(($product->stock / 50) * 100, 100) }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full uppercase tracking-wider">Correct</span>
                            @endif
                        </div>
                    </td>

                    <!-- Stock Count -->
                    <td class="px-6 py-4 whitespace-nowrap text-right text-md font-extrabold text-navy-950">
                        {{ number_format($product->stock, 0) }}
                    </td>

                    <!-- Actions / Inline Editing form -->
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button type="button" @click="editingProductId = (editingProductId === {{ $product->id }} ? null : {{ $product->id }})" class="text-gold-600 hover:text-gold-900 bg-gold-50 hover:bg-gold-100 px-3 py-1 rounded transition">
                            Ajuster
                        </button>
                    </td>
                </tr>

                <!-- Collapsible editing drawer/row -->
                <tr x-show="editingProductId === {{ $product->id }}" x-cloak class="bg-navy-50/20 border border-gold-500/10" x-data="{ type: 'set', qty: '{{ $product->stock }}' }">
                    <td colspan="6" class="px-6 py-4">
                        <form action="{{ route('admin.stock.adjust', $product->id) }}" method="POST" class="flex flex-wrap md:flex-nowrap items-end justify-between gap-4 p-4 border border-gold-500/20 rounded-md bg-white shadow-sm">
                            @csrf
                            <div class="flex items-center gap-4 flex-1 min-w-[300px]">
                                <!-- Action type -->
                                <div class="w-36">
                                    <label class="admin-label">Méthode</label>
                                    <select name="type" x-model="type" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-xs">
                                        <option value="set">Fixer le stock à</option>
                                        <option value="add">Ajouter (+/-)</option>
                                    </select>
                                </div>

                                <!-- Quantity input -->
                                <div class="w-32">
                                    <label class="admin-label">Quantité</label>
                                    <input type="number" name="quantity" x-model="qty" required class="block w-full border-gray-300 rounded-md shadow-sm sm:text-xs text-center font-bold text-navy-900">
                                </div>

                                <!-- Note input -->
                                <div class="flex-1">
                                    <label class="admin-label">Motif / Commentaire</label>
                                    <input type="text" name="notes" placeholder="Ex: Inventaire physique mai 2026..." class="block w-full border-gray-300 rounded-md shadow-sm sm:text-xs">
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="button" @click="editingProductId = null" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded transition">
                                    Annuler
                                </button>
                                <button type="submit" class="px-4 py-2 bg-navy-600 hover:bg-navy-700 text-white text-xs font-bold rounded transition shadow">
                                    Valider l'Ajustement
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endif

<!-- ==================== TAB 2 : AUDIT LOGS / HISTORIQUE ==================== -->
@if($tab === 'movements')
<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-md font-bold text-navy-600 uppercase tracking-wider">Journal d'audit des stocks</h3>
        <span class="text-xs text-gray-500">Mouvements en temps réel</span>
    </div>

    @if($movements->isEmpty())
    <div class="p-12 text-center text-gray-500 text-sm">
        Aucun mouvement de stock n'a été enregistré pour le moment.
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date / Heure</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source du mouvement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes / Observations</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Opérateur</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($movements as $m)
                <tr class="hover:bg-gray-50 transition text-sm">
                    <!-- Date -->
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-semibold">
                        {{ $m->created_at->format('d/m/Y H:i:s') }}
                    </td>

                    <!-- Product -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-bold text-navy-950">{{ $m->product->name ?? 'Produit supprimé' }}</div>
                    </td>

                    <!-- Quantity -->
                    <td class="px-6 py-4 whitespace-nowrap text-center font-extrabold">
                        @if($m->quantity > 0)
                            <span class="text-green-600">+{{ $m->quantity }}</span>
                        @else
                            <span class="text-red-600">{{ $m->quantity }}</span>
                        @endif
                    </td>

                    <!-- Type Badge -->
                    <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-bold uppercase">
                        @if($m->type === 'in')
                            <span class="px-2 py-0.5 rounded-full bg-green-50 border border-green-200 text-green-700">Entrée</span>
                        @elseif($m->type === 'out')
                            <span class="px-2 py-0.5 rounded-full bg-red-50 border border-red-200 text-red-700">Sortie</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-blue-700">Ajustement</span>
                        @endif
                    </td>

                    <!-- Source -->
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-medium">
                        {{ $m->source }}
                    </td>

                    <!-- Notes -->
                    <td class="px-6 py-4 text-gray-500 max-w-xs truncate" title="{{ $m->notes }}">
                        {{ $m->notes ?? 'N/A' }}
                    </td>

                    <!-- Operator -->
                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs text-gray-600 font-semibold">
                        {{ $m->user->name ?? 'Système' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $movements->links() }}
    </div>
    @endif
</div>
@endif
@endsection
