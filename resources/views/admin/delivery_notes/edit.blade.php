@extends('layouts.admin')

@section('title', 'Modifier Bon de Livraison')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.delivery-notes.show', $deliveryNote->id) }}" class="text-gray-400 hover:text-gray-700 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Modifier : {{ $deliveryNote->number }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">⚠️ La modification réajuste automatiquement les stocks.</p>
    </div>
</div>

@if($errors->any())
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded shadow-sm">
    <p class="font-bold text-red-800 mb-1">Veuillez corriger les erreurs suivantes :</p>
    <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.delivery-notes.update', $deliveryNote->id) }}" method="POST"
      x-data="{
        blType: '{{ $deliveryNote->type }}',
        statusOptions: {
            reception: [{ value: 'draft', label: 'Brouillon (Stock non impacté)' }, { value: 'received', label: 'Reçu (Stock incrémenté)' }],
            envoi:     [{ value: 'draft', label: 'Brouillon (Stock non impacté)' }, { value: 'pending', label: 'En attente' }, { value: 'shipped', label: 'Expédié (Stock décrémenté)' }, { value: 'delivered', label: 'Livré (Stock décrémenté)' }]
        },
        items: {{ json_encode($deliveryNote->items->map(fn($i) => ['product_id' => $i->product_id, 'quantity' => $i->quantity, 'purchase_price' => $i->purchase_price])) }},
        products: {{ $products->toJson() }},
        addItem() { this.items.push({ product_id: '', quantity: 1, purchase_price: 0 }) },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1) },
        onProductChange(i) {
            const p = this.products.find(p => p.id == this.items[i].product_id);
            if (p) this.items[i].purchase_price = p.purchase_price ?? 0;
        },
        totalAmount() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity)||0) * (parseFloat(item.purchase_price)||0), 0);
        }
      }">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- TYPE (readonly badge) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                @if($deliveryNote->type === 'envoi')
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-bold bg-blue-50 border border-blue-200 text-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        Envoi Client
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-bold bg-green-50 border border-green-200 text-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                        Réception Fournisseur
                    </span>
                @endif
                <span class="text-sm text-gray-500">Le type du bon ne peut pas être modifié après création.</span>
            </div>

            {{-- IDENTIFICATION --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="admin-section-title">Identification & Statut</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">N° du Bon</label>
                        <input type="text" value="{{ $deliveryNote->number }}" readonly
                               class="block w-full border-gray-200 bg-gray-50 rounded-md shadow-sm sm:text-sm font-mono text-gray-500">
                    </div>
                    <div>
                        <label class="admin-label">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="delivery_date" value="{{ old('delivery_date', $deliveryNote->delivery_date->format('Y-m-d')) }}" required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label class="admin-label">Changer de statut <span class="text-red-500">*</span></label>
                    <select name="status" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                        <template x-for="opt in statusOptions[blType]" :key="opt.value">
                            <option :value="opt.value" :selected="opt.value === '{{ $deliveryNote->status }}'" x-text="opt.label"></option>
                        </template>
                    </select>
                    <p class="mt-1 text-xs text-red-500 font-medium">⚠️ Changer de statut recalcule automatiquement le stock (annulation + réapplication).</p>
                </div>
            </div>

            {{-- SUPPLIER --}}
            @if($deliveryNote->type === 'reception')
            <div class="bg-white rounded-lg shadow-sm border border-green-100 p-6 space-y-4">
                <h2 class="text-sm font-bold text-green-700 uppercase tracking-wider">Fournisseur</h2>
                <div>
                    <label class="admin-label">Sélectionner un fournisseur</label>
                    <select name="supplier_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                        <option value="">— Choisir —</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ $deliveryNote->supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">Ou saisir manuellement</label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name', $deliveryNote->supplier_name) }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                </div>
            </div>
            @endif

            {{-- CUSTOMER --}}
            @if($deliveryNote->type === 'envoi')
            <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 space-y-4">
                <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wider">Destinataire (Client)</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">Nom du client</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $deliveryNote->customer_name) }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="admin-label">Téléphone</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', $deliveryNote->customer_phone) }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label class="admin-label">Adresse</label>
                    <input type="text" name="customer_address" value="{{ old('customer_address', $deliveryNote->customer_address) }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                </div>
            </div>
            @endif

            {{-- ITEMS --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="admin-section-title">Articles</h2>
                    <button type="button" @click="addItem()" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-3 py-1.5 rounded transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter
                    </button>
                </div>
                <div class="space-y-3">
                    <template x-for="(item, i) in items" :key="i">
                        <div class="grid grid-cols-12 gap-2 items-start bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <div class="col-span-5">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Produit</label>
                                <select :name="`items[${i}][product_id]`" x-model="item.product_id" @change="onProductChange(i)" required class="block w-full border-gray-300 rounded-md shadow-sm text-xs">
                                    <option value="">— Sélectionner —</option>
                                    <template x-for="p in products" :key="p.id">
                                        <option :value="p.id" x-text="p.name" :selected="item.product_id == p.id"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Qté</label>
                                <input type="number" :name="`items[${i}][quantity]`" x-model="item.quantity" min="0.01" step="0.01" required class="block w-full border-gray-300 rounded-md shadow-sm text-xs text-center font-bold">
                            </div>
                            <div class="col-span-4">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Prix unitaire (FCFA)</label>
                                <input type="number" :name="`items[${i}][purchase_price]`" x-model="item.purchase_price" min="0" step="1" required class="block w-full border-gray-300 rounded-md shadow-sm text-xs text-right font-bold">
                            </div>
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
                <textarea name="notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">{{ old('notes', $deliveryNote->notes) }}</textarea>
            </div>
        </div>

        {{-- SUMMARY SIDEBAR --}}
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 sticky top-6 space-y-4">
                <h2 class="admin-section-title">Récapitulatif</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Articles</span>
                        <span class="font-bold" x-text="items.length"></span>
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="font-bold text-gray-700">Total</span>
                        <span class="text-lg font-black text-navy-900" x-text="totalAmount().toLocaleString('fr-FR', {minimumFractionDigits:0}) + ' FCFA'"></span>
                    </div>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-gold-600 to-gold-700 text-white font-bold py-3 px-4 rounded-lg shadow hover:from-gold-700 hover:to-gold-800 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Sauvegarder les modifications
                </button>
                <a href="{{ route('admin.delivery-notes.show', $deliveryNote->id) }}" class="block text-center text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
