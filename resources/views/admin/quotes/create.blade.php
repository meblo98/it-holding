@extends('layouts.admin')

@section('title', 'Nouveau Devis')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <h1 class="text-3xl font-bold text-gray-900">Créer un Devis</h1>
    <a href="{{ route('admin.quotes.index') }}" class="text-navy-600 hover:text-navy-900 underline font-medium">Retour à la liste</a>
</div>

<form action="{{ route('admin.quotes.store') }}" method="POST" x-data="{ 
    items: [{ description: '', quantity: 1, unit_price: 0, save_to_catalog: false, catalog_type: 'product', source: 'catalog' }],
    catalog: {{ Js::from($catalog) }},
    applyTax: false,
    addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0, save_to_catalog: false, catalog_type: 'product', source: 'catalog' }) },
    removeItem(index) { this.items.splice(index, 1) },
    onSelectChange(index, event) {
        const value = event.target.value;
        const item = this.items[index];
        if (value === 'new') {
            item.source = 'manual';
            item.description = '';
            item.unit_price = 0;
            item.save_to_catalog = true;
        } else {
            const match = this.catalog.find(c => c.name === value);
            if (match) {
                item.source = 'catalog';
                item.description = match.name;
                item.unit_price = match.price;
                item.catalog_type = match.type;
                item.save_to_catalog = false;
            }
        }
    },
    onClientChange(event) {
        const opt = event.target.options[event.target.selectedIndex];
        if (opt.value) {
            this.$refs.clientId.value = opt.value;
            this.$refs.clientName.value = opt.dataset.name || '';
            this.$refs.clientEmail.value = opt.dataset.email || '';
            this.$refs.clientPhone.value = opt.dataset.phone || '';
            this.$refs.clientAddress.value = opt.dataset.address || '';
        } else {
            this.$refs.clientId.value = '';
        }
    },
    get grandTotal() { return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0)), 0); }
}">
    @csrf
    <input type="hidden" name="client_id" x-ref="clientId" value="{{ old('client_id') }}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-navy-600 border-b pb-2">Informations Client</h2>
                
                <div class="mb-4">
                    <label for="select_client" class="block text-sm font-medium text-gray-700">Sélectionner un Client Existant</label>
                    <select id="select_client" @change="onClientChange($event)" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                        <option value="">-- Saisie manuelle --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" data-name="{{ $c->display_name }}" data-email="{{ $c->email }}" data-phone="{{ $c->phone }}" data-address="{{ $c->address }}">{{ $c->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="client_name" class="block text-sm font-medium text-gray-700">Nom du Client / Entreprise *</label>
                        <input type="text" name="client_name" id="client_name" x-ref="clientName" required value="{{ old('client_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="client_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="client_email" id="client_email" x-ref="clientEmail" value="{{ old('client_email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="client_phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="text" name="client_phone" id="client_phone" x-ref="clientPhone" value="{{ old('client_phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="valid_until" class="block text-sm font-medium text-gray-700">Valide jusqu'au</label>
                        <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', now()->addDays(30)->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label for="client_address" class="block text-sm font-medium text-gray-700">Adresse</label>
                        <textarea name="client_address" id="client_address" x-ref="clientAddress" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">{{ old('client_address') }}</textarea>
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
                        <div class="bg-gray-50 p-4 rounded-md relative group">
                            <div class="flex flex-wrap md:flex-nowrap gap-4 items-end">
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Article / Service</label>
                                    
                                    <!-- Selection Dropdown -->
                                    <div x-show="item.source === 'catalog'" class="mt-1" x-data="{ open: false, searchQuery: '' }" @click.away="open = false">
                                        <!-- Trigger Button -->
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                                            <span class="block truncate text-gray-700" x-text="item.description || '-- Sélectionner un article --'"></span>
                                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>

                                        <!-- Dropdown Panel -->
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-hidden flex flex-col sm:text-sm"
                                             style="display: none;">
                                            
                                            <!-- Search Input inside Dropdown -->
                                            <div class="p-2 border-b border-gray-100">
                                                <input type="text" 
                                                       placeholder="Rechercher..." 
                                                       x-model="searchQuery" 
                                                       @keydown.enter.prevent
                                                       class="block w-full border border-gray-300 rounded-md py-1 px-2.5 text-xs focus:outline-none focus:ring-1 focus:ring-gold-500 focus:border-gold-500">
                                            </div>
                                            
                                            <!-- Options List -->
                                            <div class="overflow-y-auto max-h-48 divide-y divide-gray-50">
                                                <!-- Catalog items -->
                                                <template x-for="c in catalog.filter(cat => !searchQuery || cat.name.toLowerCase().includes(searchQuery.toLowerCase()))" :key="c.name">
                                                    <button type="button"
                                                            @click="
                                                                item.source = 'catalog';
                                                                item.description = c.name;
                                                                item.unit_price = c.price;
                                                                item.catalog_type = c.type;
                                                                item.save_to_catalog = false;
                                                                open = false;
                                                                searchQuery = '';
                                                            "
                                                            class="w-full text-left cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gold-500 hover:text-navy-950 text-gray-900 transition duration-150">
                                                        <span class="block truncate font-medium" x-text="c.name"></span>
                                                        <span class="block text-xs text-gray-500" x-text="`${c.type === 'product' ? 'Produit' : 'Service'} — ${new Intl.NumberFormat('fr-FR').format(c.price)} FCFA`"></span>
                                                    </button>
                                                </template>
                                                
                                                <!-- Custom/New Option -->
                                                <button type="button"
                                                        @click="
                                                            item.source = 'manual';
                                                            item.description = searchQuery;
                                                            item.unit_price = 0;
                                                            item.save_to_catalog = true;
                                                            open = false;
                                                            searchQuery = '';
                                                        "
                                                        class="w-full text-left cursor-default select-none relative py-2.5 pl-3 pr-9 bg-gray-50 text-gold-600 hover:bg-navy-600 hover:text-white font-bold transition duration-150 border-t">
                                                    <span x-text="searchQuery ? `+ Créer : ${searchQuery}` : '+ Nouveau / Autre article'"></span>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Hidden input to submit description -->
                                        <input type="hidden" :name="`items[${index}][description]`" x-model="item.description">
                                    </div>

                                    <!-- Manual Input -->
                                    <div x-show="item.source === 'manual'" class="mt-1 flex gap-2">
                                        <input type="text" :name="`items[${index}][description]`" x-model="item.description" placeholder="Description de l'article..." required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                                        <button type="button" @click="item.source = 'catalog'" class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded hover:bg-gray-300">Annuler</button>
                                    </div>
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
                            
                            <!-- Save to catalog option -->
                            <div class="mt-2 flex items-center gap-4 text-xs border-t pt-2" x-show="item.source === 'manual' || !catalog.find(c => c.name === item.description)">
                                <label class="flex items-center text-gray-600">
                                    <input type="checkbox" :name="`items[${index}][save_to_catalog]`" x-model="item.save_to_catalog" class="rounded border-gray-300 text-gold-600 focus:ring-gold-500 mr-2">
                                    Enregistrer dans le catalogue
                                </label>
                                <div x-show="item.save_to_catalog" class="flex items-center gap-2">
                                    <span class="text-gray-400">Type:</span>
                                    <select :name="`items[${index}][catalog_type]`" x-model="item.catalog_type" class="text-xs border-gray-300 rounded-md focus:ring-gold-500 focus:border-gold-500 py-0 px-2 h-6">
                                        <option value="product">Produit</option>
                                        <option value="service">Service</option>
                                    </select>
                                </div>
                            </div>
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
                        <label for="number" class="block text-sm font-medium text-gray-700">Numéro de Devis</label>
                        <input type="text" name="number" id="number" value="{{ old('number', $nextNumber) }}" readonly class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm font-bold text-navy-600">
                    </div>
                    <div class="border-t pt-3 space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700 select-none cursor-pointer">
                            <input type="checkbox" x-model="applyTax" class="rounded border-gray-300 text-gold-600 focus:ring-gold-500 mr-2">
                            Appliquer la TVA (18%)
                        </label>
                        <input type="hidden" name="tax_amount" :value="applyTax ? Math.round(grandTotal * 0.18) : 0">
                        
                        <div class="flex justify-between text-sm text-gray-500 pt-1" x-show="applyTax">
                            <span>Sous-total HT :</span>
                            <span class="font-medium"><span x-text="new Intl.NumberFormat('fr-FR').format(grandTotal)"></span> FCFA</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500" x-show="applyTax">
                            <span>TVA (18%) :</span>
                            <span class="font-medium"><span x-text="new Intl.NumberFormat('fr-FR').format(Math.round(grandTotal * 0.18))"></span> FCFA</span>
                        </div>
                        
                        <div class="flex justify-between items-center border-t pt-2 mt-2">
                            <span class="font-bold text-gray-700 text-sm uppercase" x-text="applyTax ? 'TOTAL TTC' : 'TOTAL'">TOTAL</span>
                            <span class="font-bold text-navy-600 text-2xl">
                                <span x-text="new Intl.NumberFormat('fr-FR').format(applyTax ? grandTotal + Math.round(grandTotal * 0.18) : grandTotal)"></span> FCFA
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-2">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-navy-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-navy-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Enregistrer le Devis
                    </button>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-xl font-bold mb-2 text-navy-600">Notes</h2>
                <textarea name="notes" id="notes" rows="4" placeholder="Conditions de paiement, délais de livraison, etc..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>
</form>
@endsection
