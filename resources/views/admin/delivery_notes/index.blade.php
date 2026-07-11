@extends('layouts.admin')

@section('title', 'Bons de Livraison')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Bons de Livraison</h1>
        <p class="text-sm text-gray-500 mt-1">Suivez les envois clients et les réceptions fournisseurs en temps réel.</p>
    </div>
    <a href="{{ route('admin.delivery-notes.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-semibold text-sm hover:bg-navy-700 transition shadow-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nouveau Bon de Livraison
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-sm">
    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded shadow-sm">
    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
</div>
@endif

{{-- TYPE TABS --}}
<div class="flex border-b border-gray-200 mb-6 gap-1">
    <a href="{{ route('admin.delivery-notes.index', ['type' => 'all', 'search' => $search]) }}"
       class="px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px {{ $type === 'all' ? 'border-navy-600 text-navy-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        Tous
    </a>
    <a href="{{ route('admin.delivery-notes.index', ['type' => 'envoi', 'search' => $search]) }}"
       class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px {{ $type === 'envoi' ? 'border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
        Envois Clients
    </a>
    <a href="{{ route('admin.delivery-notes.index', ['type' => 'reception', 'search' => $search]) }}"
       class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px {{ $type === 'reception' ? 'border-green-500 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
        Réceptions Fournisseurs
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
    {{-- FILTER PANEL --}}
    <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row gap-3 items-center justify-between">
        <form action="{{ route('admin.delivery-notes.index') }}" method="GET" class="w-full md:w-96 flex">
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="text" name="search" placeholder="Rechercher par N°, fournisseur, client..." value="{{ $search }}"
                   class="block w-full border-gray-300 rounded-l-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
            <button type="submit" class="bg-navy-600 text-white px-4 rounded-r-md hover:bg-navy-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>

        {{-- Status filter chips --}}
        <div class="flex flex-wrap gap-2">
            @php
                $statusOptions = $type === 'reception'
                    ? ['received' => ['label' => 'Reçu', 'color' => 'green'], 'draft' => ['label' => 'Brouillon', 'color' => 'gray']]
                    : ($type === 'envoi'
                        ? ['draft' => ['label' => 'Brouillon', 'color' => 'gray'], 'pending' => ['label' => 'En attente', 'color' => 'amber'], 'shipped' => ['label' => 'Expédié', 'color' => 'blue'], 'delivered' => ['label' => 'Livré', 'color' => 'green']]
                        : ['draft' => ['label' => 'Brouillon', 'color' => 'gray'], 'pending' => ['label' => 'En attente', 'color' => 'amber'], 'shipped' => ['label' => 'Expédié', 'color' => 'blue'], 'delivered' => ['label' => 'Livré', 'color' => 'green'], 'received' => ['label' => 'Reçu', 'color' => 'green']]);
            @endphp
            <a href="{{ route('admin.delivery-notes.index', ['type' => $type, 'search' => $search]) }}"
               class="px-3 py-1 text-xs font-bold rounded {{ empty($status) ? 'bg-navy-100 text-navy-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Tous statuts
            </a>
            @foreach($statusOptions as $key => $opt)
            <a href="{{ route('admin.delivery-notes.index', ['type' => $type, 'status' => $key, 'search' => $search]) }}"
               class="px-3 py-1 text-xs font-bold rounded {{ $status === $key ? 'bg-'.$opt['color'].'-100 text-'.$opt['color'].'-800 border border-'.$opt['color'].'-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $opt['label'] }}
            </a>
            @endforeach
        </div>
    </div>

    @if($deliveryNotes->isEmpty())
    <div class="p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V15a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h.586a1 1 0 00.707-.293l1.414-1.414A1 1 0 0019 14V6a1 1 0 00-1-1h-5" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun bon de livraison trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">Modifiez vos critères ou créez un nouveau bon.</p>
        <div class="mt-6">
            <a href="{{ route('admin.delivery-notes.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-semibold text-sm hover:bg-navy-700 transition shadow-sm">
                Créer un Bon de Livraison
            </a>
        </div>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Bon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contrepartie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($deliveryNotes as $note)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.delivery-notes.show', $note->id) }}" class="text-navy-600 hover:text-navy-900 font-bold underline">
                            {{ $note->number }}
                        </a>
                        @if($note->order_id)
                            <div class="text-[10px] text-gray-400 mt-0.5">Commande #{{ $note->order_id }}</div>
                        @elseif($note->invoice_id)
                            <div class="text-[10px] text-gray-400 mt-0.5">Facture #{{ $note->invoice_id }}</div>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($note->type === 'envoi')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 border border-blue-200 text-blue-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                Envoi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 border border-green-200 text-green-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                                Réception
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                        @if($note->type === 'reception')
                            @if($note->supplier)
                                <a href="{{ route('admin.suppliers.show', $note->supplier->id) }}" class="text-navy-600 hover:underline">{{ $note->supplier_name }}</a>
                            @else
                                {{ $note->supplier_name ?? '—' }}
                            @endif
                        @else
                            <div>{{ $note->customer_name ?? '—' }}</div>
                            @if($note->customer_phone)
                                <div class="text-xs text-gray-400">{{ $note->customer_phone }}</div>
                            @endif
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $note->delivery_date->format('d/m/Y') }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @php
                            $statusConfig = match($note->status) {
                                'draft'     => ['label' => 'Brouillon',   'classes' => 'bg-gray-100 text-gray-700 border-gray-200'],
                                'pending'   => ['label' => 'En attente',  'classes' => 'bg-amber-50 text-amber-700 border-amber-200'],
                                'shipped'   => ['label' => 'Expédié',     'classes' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                'delivered' => ['label' => 'Livré',       'classes' => 'bg-green-50 text-green-700 border-green-200'],
                                'received'  => ['label' => 'Reçu',        'classes' => 'bg-green-50 text-green-700 border-green-200'],
                                default     => ['label' => ucfirst($note->status), 'classes' => 'bg-gray-100 text-gray-700 border-gray-200'],
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusConfig['classes'] }}">
                            {{ $statusConfig['label'] }}
                        </span>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-navy-900 font-bold">
                        {{ number_format($note->total_purchase_amount, 0, ',', ' ') }} FCFA
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.delivery-notes.show', $note->id) }}" class="text-navy-600 hover:text-navy-900 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition text-xs font-bold">Voir</a>
                            <a href="{{ route('admin.delivery-notes.edit', $note->id) }}" class="text-gold-600 hover:text-gold-900 bg-gold-50 hover:bg-gold-100 px-2.5 py-1 rounded transition text-xs font-bold">Modifier</a>
                            <a href="{{ route('admin.delivery-notes.print', $note->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-2.5 py-1 rounded transition text-xs font-bold">Imprimer</a>
                            <form action="{{ route('admin.delivery-notes.destroy', $note->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Supprimer ce bon et réajuster les stocks ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded transition text-xs font-bold">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $deliveryNotes->appends(['type' => $type, 'search' => $search, 'status' => $status])->links() }}
    </div>
    @endif
</div>
@endsection
