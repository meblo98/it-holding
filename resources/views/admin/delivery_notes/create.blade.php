@extends('layouts.admin')

@section('title', 'Nouveau Bon de Livraison')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.delivery-notes.index') }}" class="text-gray-400 hover:text-gray-700 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Nouveau Bon de Livraison</h1>
        <p class="text-sm text-gray-500 mt-0.5">Envoi client ou réception fournisseur</p>
    </div>
</div>

@if($errors->any())
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded shadow-sm">
    <p class="font-bold text-red-800 mb-1">Veuillez corriger les erreurs suivantes :</p>
    <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.delivery-notes.store') }}" method="POST"
      x-data="{
        blType: '{{ $prefilled['type'] ?? 'reception' }}',
        statusOptions: {
            reception: [{ value: 'draft', label: 'Brouillon (Stock non impacté)' }, { value: 'received', label: 'Reçu (Stock incrémenté)' }],
            envoi:     [{ value: 'draft', label: 'Brouillon (Stock non impacté)' }, { value: 'pending', label: 'En attente d\'enlèvement' }, { value: 'shipped', label: 'Expédié (Stock décrémenté)' }, { value: 'delivered', label: 'Livré (Stock décrémenté)' }]
        },
        items: {{ json_encode($prefilled['items'] ?? [['product_id'=>'','quantity'=>1,'purchase_price'=>0]]) }},
        products: {{ $products->toJson() }},
        addItem() { this.items.push({ product_id: '', quantity: 1, purchase_price: 0 }) },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1) },
        getProduct(id) { return this.products.find(p => p.id == id) },
        onProductChange(i) {
            const p = this.getProduct(this.items[i].product_id);
            if (p) this.items[i].purchase_price = (this.blType === 'reception') ? (p.purchase_price ?? 0) : (p.price ?? 0);
        },
        onClientChange(event) {
            const opt = event.target.options[event.target.selectedIndex];
            if (opt.value) {
                document.getElementById('client_id').value = opt.value;
                document.getElementById('customer_name').value = opt.dataset.name || '';
                document.getElementById('customer_phone').value = opt.dataset.phone || '';
                document.getElementById('customer_address').value = opt.dataset.address || '';
            } else {
                document.getElementById('client_id').value = '';
            }
        },
        totalAmount() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity)||0) * (parseFloat(item.purchase_price)||0), 0);
        }
      }"
      x-init="$watch('blType', value => {
          items.forEach((item, i) => {
              const p = getProduct(item.product_id);
              if (p) {
                  item.purchase_price = (value === 'reception') ? (p.purchase_price ?? 0) : (p.price ?? 0);
              }
          });
      })">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ========== LEFT COLUMN : FORM ========== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TYPE SELECTOR CARD --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h2 class="admin-section-title">Type de Bon de Livraison</h2>

                <div class="grid grid-cols-2 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="reception" x-model="blType" class="sr-only peer">
                        <div class="border-2 rounded-lg p-4 text-center transition peer-checked:border-green-500 peer-checked:bg-green-50 border-gray-200 hover:border-green-300">
                            <svg class="w-8 h-8 mx-auto mb-2 text-green-600 peer-checked:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                            </svg>
                            <p class="font-bold text-gray-800">Réception</p>
                            <p class="text-xs text-gray-500 mt-0.5">Marchandise reçue d'un fournisseur</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="envoi" x-model="blType" class="sr-only peer">
                        <div class="border-2 rounded-lg p-4 text-center transition peer-checked:border-blue-500 peer-checked:bg-blue-50 border-gray-200 hover:border-blue-300">
                            <svg class="w-8 h-8 mx-auto mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            <p class="font-bold text-gray-800">Envoi</p>
                            <p class="text-xs text-gray-500 mt-0.5">Expédition de marchandise à un client</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- IDENTIFICATION CARD --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="admin-section-title">Identification</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">N° du Bon <span class="text-red-500">*</span></label>
                        <input type="text" name="number" value="{{ old('number', $nextNumber) }}" required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm font-mono">
                    </div>
                    <div>
                        <label class="admin-label">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                </div>

                {{-- STATUT ADAPTÉ --}}
                <div>
                    <label class="admin-label">Statut <span class="text-red-500">*</span></label>
                    <select name="status" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                        <template x-for="opt in statusOptions[blType]" :key="opt.value">
                            <option :value="opt.value" :selected="opt.value === '{{ old('status', 'received') }}'" x-text="opt.label"></option>
                        </template>
                    </select>
                    <p class="mt-1 text-xs text-gray-400 italic">⚠️ Le stock est impacté seulement aux statuts "Reçu", "Expédié" ou "Livré".</p>
                </div>
            </div>

            {{-- CONDITIONAL SUPPLIER/CLIENT BLOCK --}}
            {{-- SUPPLIER --}}
            <div x-show="blType === 'reception'" x-cloak class="bg-white rounded-lg shadow-sm border border-green-100 p-6 space-y-4">
                <h2 class="text-sm font-bold text-green-700 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                    Fournisseur
                </h2>
                <div>
                    <label class="admin-label">Sélectionner un Fournisseur</label>
                    <select name="supplier_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                        <option value="">— Choisir un fournisseur —</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">Ou saisir manuellement</label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" placeholder="Nom du fournisseur..."
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                </div>
            </div>

            {{-- CUSTOMER --}}
            <div x-show="blType === 'envoi'" x-cloak class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 space-y-4">
                <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    Destinataire (Client)
                </h2>
                
                <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id', $prefilled['client_id'] ?? '') }}">
                
                @if($prefilled)
                    <input type="hidden" name="order_id" value="{{ $prefilled['order_id'] }}">
                    <input type="hidden" name="invoice_id" value="{{ $prefilled['invoice_id'] }}">
                @endif

                <div>
                    <label for="select_client" class="admin-label">Sélectionner un Client Existant</label>
                    <select id="select_client" @change="onClientChange($event)" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                        <option value="">-- Saisie manuelle / Autre --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ (old('client_id', $prefilled['client_id'] ?? '') == $c->id) ? 'selected' : '' }} data-name="{{ $c->display_name }}" data-phone="{{ $c->phone }}" data-address="{{ $c->address }}">{{ $c->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">Nom du client <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $prefilled['customer_name'] ?? '') }}" required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="admin-label">Téléphone</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', $prefilled['customer_phone'] ?? '') }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label class="admin-label">Adresse de livraison</label>
                    <input type="text" name="customer_address" id="customer_address" value="{{ old('customer_address', $prefilled['customer_address'] ?? '') }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                </div>
            </div>

            {{-- ITEMS TABLE --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="admin-section-title">Produits / Articles</h2>
                    <button type="button" @click="addItem()" class="text-xs font-bold text-navy-600 hover:text-navy-900 bg-navy-50 hover:bg-navy-100 px-3 py-1.5 rounded transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter un article
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, i) in items" :key="i">
                        <div class="grid grid-cols-12 gap-2 items-start bg-gray-50 p-3 rounded-lg border border-gray-100">
                            {{-- Product select --}}
                            <div class="col-span-5">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Produit</label>
                                <select :name="`items[${i}][product_id]`" x-model="item.product_id" @change="onProductChange(i)" required
                                        class="block w-full border-gray-300 rounded-md shadow-sm text-xs">
                                    <option value="">— Sélectionner —</option>
                                    <template x-for="p in products" :key="p.id">
                                        <option :value="p.id" x-text="p.name" :selected="item.product_id == p.id"></option>
                                    </template>
                                </select>
                            </div>
                            {{-- Quantity --}}
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Qté</label>
                                <input type="number" :name="`items[${i}][quantity]`" x-model="item.quantity" min="0.01" step="0.01" required
                                       class="block w-full border-gray-300 rounded-md shadow-sm text-xs text-center font-bold">
                            </div>
                            {{-- Price --}}
                            <div class="col-span-4">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1" x-text="blType === 'reception' ? 'P.U. Achat (FCFA)' : 'P.U. Vente (FCFA)'"></label>
                                <input type="number" :name="`items[${i}][purchase_price]`" x-model="item.purchase_price" min="0" step="1" required
                                       class="block w-full border-gray-300 rounded-md shadow-sm text-xs text-right font-bold">
                            </div>
                            {{-- Remove button --}}
                            <div class="col-span-1 flex items-end pb-0.5">
                                <button type="button" @click="removeItem(i)" class="w-full flex items-center justify-center text-red-400 hover:text-red-700 transition p-2 rounded hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- NOTES --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Notes / Observations</label>
                <textarea name="notes" rows="3" placeholder="Commentaire interne sur ce bon de livraison..."
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- ========== RIGHT COLUMN : SUMMARY + SUBMIT ========== --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 sticky top-6">
                <h2 class="admin-section-title">Récapitulatif</h2>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Type de bon</span>
                        <span class="font-bold text-navy-800 capitalize" x-text="blType === 'envoi' ? '📦 Envoi Client' : '📥 Réception Fournisseur'"></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Nombre d'articles</span>
                        <span class="font-bold text-navy-800" x-text="items.length"></span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-700">Montant Total</span>
                        <span class="text-lg font-black text-navy-900" x-text="totalAmount().toLocaleString('fr-FR', {minimumFractionDigits:0}) + ' FCFA'"></span>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-navy-600 to-navy-700 text-white font-bold py-3 px-4 rounded-lg shadow hover:from-navy-700 hover:to-navy-800 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer le Bon
                </button>
                <a href="{{ route('admin.delivery-notes.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
