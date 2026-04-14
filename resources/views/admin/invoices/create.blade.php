@extends('layouts.admin')

@section('title', 'Nouvelle Facture')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <h1 class="text-3xl font-bold text-gray-900">Créer une Facture</h1>
    <a href="{{ route('admin.invoices.index') }}" class="text-navy-600 hover:text-navy-900 underline font-medium">Retour à la liste</a>
</div>

<form action="{{ route('admin.invoices.store') }}" method="POST" x-data="{ 
    items: [{ description: '', quantity: 1, unit_price: 0 }],
    addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0 }) },
    removeItem(index) { this.items.splice(index, 1) },
    get grandTotal() { return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0)), 0); }
}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-navy-600 border-b pb-2">Informations Client</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="client_name" class="block text-sm font-medium text-gray-700">Nom du Client / Entreprise *</label>
                        <input type="text" name="client_name" id="client_name" required value="{{ old('client_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="client_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="client_email" id="client_email" value="{{ old('client_email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="client_phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="text" name="client_phone" id="client_phone" value="{{ old('client_phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Échéance</label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label for="client_address" class="block text-sm font-medium text-gray-700">Adresse</label>
                        <textarea name="client_address" id="client_address" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">{{ old('client_address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between mb-4 border-b pb-2">
                    <h2 class="text-xl font-bold text-navy-600">Articles / Services</h2>
                    <button type="button" @click="addItem" class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Ajouter
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex flex-wrap md:flex-nowrap gap-4 items-end bg-gray-50 p-4 rounded-md relative group">
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-xs font-medium text-gray-500 uppercase">Description</label>
                                <input type="text" :name="`items[${index}][description]`" x-model="item.description" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                            </div>
                            <div class="w-24">
                                <label class="block text-xs font-medium text-gray-500 uppercase">Qté</label>
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" min="0.01" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm text-center">
                            </div>
                            <div class="w-32">
                                <label class="block text-xs font-medium text-gray-500 uppercase">Prix Unitaire</label>
                                <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price" min="0" step="1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm text-right">
                            </div>
                            <div class="w-32">
                                <label class="block text-xs font-medium text-gray-500 uppercase">Total</label>
                                <div class="mt-1 p-2 text-right bg-white border border-gray-300 rounded-md shadow-sm sm:text-sm font-bold text-navy-600">
                                    <span x-text="new Intl.NumberFormat('fr-FR').format(item.quantity * item.unit_price)"></span>
                                </div>
                            </div>
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="mb-1 p-2 text-red-600 hover:bg-red-50 rounded-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-navy-600">Résumé</h2>
                <div class="space-y-3">
                    <div>
                        <label for="number" class="block text-sm font-medium text-gray-700">Numéro de Facture</label>
                        <input type="text" name="number" id="number" value="{{ old('number', $nextNumber) }}" readonly class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm font-bold text-navy-600">
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-medium text-gray-700">TOTAL</span>
                            <span class="font-bold text-navy-600 text-2xl">
                                <span x-text="new Intl.NumberFormat('fr-FR').format(grandTotal)"></span> FCFA
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-2">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-navy-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-navy-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Enregistrer la Facture
                    </button>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-xl font-bold mb-2 text-navy-600">Notes</h2>
                <textarea name="notes" id="notes" rows="4" placeholder="Conditions de paiement, RIB, etc..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>
</form>
@endsection
