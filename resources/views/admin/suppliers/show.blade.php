@extends('layouts.admin')

@section('title', 'Fournisseur ' . $supplier->name)

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $supplier->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Fiche profil fournisseur et historique des achats.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
            Retour
        </a>
        <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="inline-flex items-center px-4 py-2 bg-gold-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gold-700 transition">
            Modifier Coordonnées
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Supplier Details card -->
    <div class="lg:col-span-1 bg-white shadow-sm rounded-lg p-6 border border-gray-100 h-fit">
        <h2 class="text-lg font-bold text-navy-600 uppercase tracking-wider mb-4 border-b pb-2">Coordonnées</h2>
        <div class="space-y-4">
            <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Entreprise</span>
                <span class="text-sm font-semibold text-gray-900">{{ $supplier->name }}</span>
            </div>
            @if($supplier->contact_person)
            <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Interlocuteur</span>
                <span class="text-sm text-gray-800">{{ $supplier->contact_person }}</span>
            </div>
            @endif
            @if($supplier->phone)
            <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Téléphone</span>
                <span class="text-sm text-gray-800 flex items-center mt-0.5">
                    <svg class="w-4 h-4 mr-2 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    {{ $supplier->phone }}
                </span>
            </div>
            @endif
            @if($supplier->email)
            <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Email</span>
                <span class="text-sm text-gray-800 flex items-center mt-0.5">
                    <svg class="w-4 h-4 mr-2 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ $supplier->email }}
                </span>
            </div>
            @endif
            @if($supplier->address)
            <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Adresse</span>
                <span class="text-sm text-gray-700 whitespace-pre-line mt-1 block bg-gray-50 p-3 rounded border">{{ $supplier->address }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Purchases overview and history -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Dashboard Key Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white rounded-lg p-6 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-navy-200 font-bold uppercase tracking-wider">Volume total d'achat</p>
                        <h3 class="text-3xl font-black mt-2">{{ number_format($supplier->total_purchase_amount, 0, ',', ' ') }} <span class="text-lg font-medium">FCFA</span></h3>
                    </div>
                    <div class="bg-navy-500 bg-opacity-30 p-3 rounded-full">
                        <svg class="w-8 h-8 text-navy-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gold-600 to-gold-700 text-white rounded-lg p-6 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gold-100 font-bold uppercase tracking-wider">Livraisons Reçues</p>
                        <h3 class="text-3xl font-black mt-2">{{ $supplier->deliveryNotes->count() }} <span class="text-lg font-medium">BL(s)</span></h3>
                    </div>
                    <div class="bg-gold-500 bg-opacity-30 p-3 rounded-full">
                        <svg class="w-8 h-8 text-gold-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V15a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h.586a1 1 0 00.707-.293l1.414-1.414A1 1 0 0019 14V6a1 1 0 00-1-1h-5"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Notes list -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-md font-bold text-navy-600 uppercase tracking-wider">Bons de livraison associés</h3>
                <span class="text-xs text-gray-500">{{ $supplier->deliveryNotes->count() }} enregistrements</span>
            </div>

            @if($supplier->deliveryNotes->isEmpty())
            <div class="p-8 text-center text-gray-500 text-sm">
                Aucun bon de livraison associé à ce fournisseur pour le moment.
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Bon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Achat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($supplier->deliveryNotes as $note)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.delivery-notes.show', $note->id) }}" class="text-navy-600 hover:text-navy-950 font-bold underline">
                                    {{ $note->number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $note->delivery_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                {{ $note->notes ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-navy-600">
                                {{ number_format($note->total_purchase_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold">
                                <a href="{{ route('admin.delivery-notes.show', $note->id) }}" class="text-navy-600 hover:text-navy-900 bg-navy-50 px-2.5 py-1 rounded">
                                    Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
