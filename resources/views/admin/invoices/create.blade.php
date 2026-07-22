@extends('layouts.admin')

@section('title', 'Nouvelle Facture')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <h1 class="text-3xl font-bold text-gray-900">Créer une Facture</h1>
    <a href="{{ route('admin.invoices.index') }}" class="text-navy-600 hover:text-navy-900 underline font-medium">Retour à la liste</a>
</div>

<form action="{{ route('admin.invoices.store') }}" method="POST" x-data="{ 
    items: [{ product_id: null, description: '', quantity: 1, unit_price: 0, save_to_catalog: false, catalog_type: 'product', source: 'catalog' }],
    catalog: {{ Js::from($catalog) }},
    applyTax: false,
    addItem() { this.items.push({ product_id: null, description: '', quantity: 1, unit_price: 0, save_to_catalog: false, catalog_type: 'product', source: 'catalog' }) },
    removeItem(index) { this.items.splice(index, 1) },
    onSelectChange(index, event) {
        const value = event.target.value;
        const item = this.items[index];
        if (value === 'new') {
            item.source = 'manual';
            item.description = '';
            item.product_id = null;
            item.unit_price = 0;
            item.save_to_catalog = true;
        } else {
            const match = this.catalog.find(c => c.name === value);
            if (match) {
                item.source = 'catalog';
                item.description = match.name;
                item.product_id = match.type === 'product' ? match.id : null;
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
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Échéance</label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
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
                                    <div x-show="item.source === 'catalog'" class="mt-1">
                                        <select @change="onSelectChange(index, $event)" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                                            <option value="">-- Sélectionner un article --</option>
                                            <template x-for="c in catalog" :key="c.name">
                                                <option :value="c.name" :selected="item.description === c.name" x-text="`${c.name} (${c.type === 'product' ? 'Produit' : 'Service'})`"></option>
                                            </template>
                                            <option value="new" class="text-gold-600 font-bold">+ Nouveau / Autre article</option>
                                        </select>
                                        <!-- Hidden input to submit description and product_id -->
                                        <input type="hidden" :name="`items[${index}][description]`" x-model="item.description">
                                        <input type="hidden" :name="`items[${index}][product_id]`" x-model="item.product_id">
                                    </div>

                                    <!-- Manual Input -->
                                    <div x-show="item.source === 'manual'" class="mt-1 flex gap-2">
                                        <input type="text" :name="`items[${index}][description]`" x-model="item.description" placeholder="Description de l'article..." required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                                        <input type="hidden" :name="`items[${index}][product_id]`" x-model="item.product_id">
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
                        <label for="number" class="block text-sm font-medium text-gray-700">Numéro de Facture</label>
                        <input type="text" name="number" id="number" value="{{ old('number', $nextNumber) }}" readonly class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm font-bold text-navy-600">
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Mode de Paiement</label>
                        <select name="payment_method" id="payment_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                            <option value="">-- Non spécifié --</option>
                            <option value="espece" {{ old('payment_method') == 'espece' ? 'selected' : '' }}>Espèces (Cash)</option>
                            <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement Bancaire</option>
                            <option value="orange_money" {{ old('payment_method') == 'orange_money' ? 'selected' : '' }}>Orange Money</option>
                            <option value="wave" {{ old('payment_method') == 'wave' ? 'selected' : '' }}>Wave</option>
                            <option value="free_money" {{ old('payment_method') == 'free_money' ? 'selected' : '' }}>Free Money</option>
                        </select>
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
