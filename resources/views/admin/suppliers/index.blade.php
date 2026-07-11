@extends('layouts.admin')

@section('title', 'Gestion des Fournisseurs')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Fournisseurs</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez vos grossistes et suivez les volumes d'achat globaux enregistrés.</p>
    </div>
    <a href="{{ route('admin.suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-semibold text-sm hover:bg-navy-700 transition shadow-sm hover:shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nouveau Fournisseur
    </a>
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

<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100 mb-6">
    @if($suppliers->isEmpty())
    <div class="p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun fournisseur enregistré</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter votre premier fournisseur pour y lier vos bons de livraison.</p>
        <div class="mt-6">
            <a href="{{ route('admin.suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-semibold text-sm hover:bg-navy-700 transition shadow-sm">
                Ajouter un Fournisseur
            </a>
        </div>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur / Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordonnées</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Livraisons</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Achat</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($suppliers as $supplier)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <a href="{{ route('admin.suppliers.show', $supplier->id) }}" class="text-navy-600 hover:text-navy-900 font-bold underline text-sm">
                                {{ $supplier->name }}
                            </a>
                            @if($supplier->contact_person)
                            <span class="text-xs text-gray-400 mt-0.5">Contact: {{ $supplier->contact_person }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <div class="flex flex-col gap-0.5">
                            @if($supplier->phone)
                            <span class="flex items-center text-xs text-navy-950 font-medium">
                                <svg class="w-3 h-3 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $supplier->phone }}
                            </span>
                            @endif
                            @if($supplier->email)
                            <span class="flex items-center text-[11px] text-gray-400">
                                <svg class="w-3 h-3 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ $supplier->email }}
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-navy-50 text-navy-800">
                            {{ $supplier->delivery_notes_count }} BL(s)
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-navy-900 font-bold">
                        {{ number_format($supplier->total_purchase_amount, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="{{ route('admin.suppliers.show', $supplier->id) }}" class="text-navy-600 hover:text-navy-900 bg-navy-50 hover:bg-navy-100 px-3 py-1 rounded transition">
                            Fiche
                        </a>
                        <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="text-gold-600 hover:text-gold-900 bg-gold-50 hover:bg-gold-100 px-3 py-1 rounded transition">
                            Modifier
                        </a>
                        <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ? Les bons de livraison associés conserveront l\'historique, mais seront désassociés.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>
@endsection
