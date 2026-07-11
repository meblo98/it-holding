@extends('layouts.admin')
@section('title', 'Nouvel Abonnement CARE+')
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.care.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Nouvel Abonnement IT HOLDING CARE+</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.care.store') }}" method="POST" x-data="{
    plan: '{{ old('plan', 'standard') }}',
    plans: {
        standard:   { priority: true, repairDiscount: true, repairPct: 20, partsDiscount: false, partsPct: 0, home: false },
        premium:    { priority: true, repairDiscount: true, repairPct: 30, partsDiscount: true, partsPct: 15, home: false },
        enterprise: { priority: true, repairDiscount: true, repairPct: 40, partsDiscount: true, partsPct: 25, home: true },
    },
    get current() { return this.plans[this.plan] || this.plans.standard; }
}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- PLAN SELECTOR --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <h2 class="admin-section-title">Choisir un Plan</h2>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer" @click="plan = 'standard'">
                        <input type="radio" name="plan" value="standard" class="sr-only" {{ old('plan','standard')==='standard'?'checked':'' }}>
                        <div class="border-2 rounded-lg p-4 text-center transition" :class="plan==='standard' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300'">
                            <div class="text-2xl mb-1">🛡️</div>
                            <div class="text-sm font-black text-gray-900">Standard</div>
                            <div class="text-xs text-gray-500 mt-1">Assistance prioritaire + réduction réparation 20%</div>
                        </div>
                    </label>
                    <label class="cursor-pointer" @click="plan = 'premium'">
                        <input type="radio" name="plan" value="premium" class="sr-only" {{ old('plan')==='premium'?'checked':'' }}>
                        <div class="border-2 rounded-lg p-4 text-center transition" :class="plan==='premium' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300'">
                            <div class="text-2xl mb-1">⭐</div>
                            <div class="text-sm font-black text-gray-900">Premium</div>
                            <div class="text-xs text-gray-500 mt-1">Réduction 30% réparation + 15% pièces</div>
                        </div>
                    </label>
                    <label class="cursor-pointer" @click="plan = 'enterprise'">
                        <input type="radio" name="plan" value="enterprise" class="sr-only" {{ old('plan')==='enterprise'?'checked':'' }}>
                        <div class="border-2 rounded-lg p-4 text-center transition" :class="plan==='enterprise' ? 'border-amber-500 bg-amber-50' : 'border-gray-200 hover:border-amber-300'">
                            <div class="text-2xl mb-1">🏆</div>
                            <div class="text-sm font-black text-gray-900">Entreprise</div>
                            <div class="text-xs text-gray-500 mt-1">Tout Premium + 40% réparation + intervention à domicile</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- AVANTAGES AUTO --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-3">
                <h2 class="admin-section-title">Avantages du plan</h2>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="has_priority_support" value="1" class="admin-check" :checked="current.priority" {{ old('has_priority_support') ? 'checked' : '' }}>
                        <span class="font-medium text-gray-700">Assistance prioritaire</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="has_home_service" value="1" class="admin-check" :checked="current.home" {{ old('has_home_service') ? 'checked' : '' }}>
                        <span class="font-medium text-gray-700">Intervention à domicile</span>
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="has_repair_discount" value="1" class="admin-check" :checked="current.repairDiscount" {{ old('has_repair_discount','1') ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-700">Réduction réparation</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="repair_discount_pct" :value="current.repairPct" value="{{ old('repair_discount_pct', 20) }}" min="0" max="100" class="w-20 border-gray-300 rounded-md shadow-sm text-sm">
                            <span class="text-sm text-gray-500">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="has_parts_discount" value="1" class="admin-check" :checked="current.partsDiscount" {{ old('has_parts_discount') ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-700">Réduction pièces</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="parts_discount_pct" :value="current.partsPct" value="{{ old('parts_discount_pct', 0) }}" min="0" max="100" class="w-20 border-gray-300 rounded-md shadow-sm text-sm">
                            <span class="text-sm text-gray-500">%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CLIENT & PRODUIT --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Client & Produit</h2>
                <div>
                    <label class="admin-label">Client CRM</label>
                    <select name="client_id" class="admin-select">
                        <option value="">— Saisie manuelle —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" {{ old('client_id')==$c->id?'selected':'' }}>{{ $c->display_name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Nom client *</label><input type="text" name="client_name" value="{{ old('client_name') }}" required class="admin-input"></div>
                    <div><label class="admin-label">Téléphone</label><input type="text" name="client_phone" value="{{ old('client_phone') }}" class="admin-input"></div>
                </div>
                <div>
                    <label class="admin-label">Garantie liée</label>
                    <select name="warranty_id" class="admin-select">
                        <option value="">— Aucune —</option>
                        @foreach($warranties as $w)<option value="{{ $w->id }}" {{ old('warranty_id')==$w->id?'selected':'' }}>{{ $w->number }} — {{ $w->product_name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Produit couvert *</label><input type="text" name="product_name" value="{{ old('product_name') }}" required class="admin-input"></div>
                    <div><label class="admin-label">N° de série</label><input type="text" name="serial_number" value="{{ old('serial_number') }}" class="admin-input font-mono"></div>
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Durée & Prix</h2>
                <div><label class="admin-label">Date de début *</label><input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="admin-input"></div>
                <div><label class="admin-label">Durée (mois) *</label>
                    <select name="duration_months" required class="admin-select">
                        @foreach([6,12,24,36] as $m)<option value="{{ $m }}" {{ old('duration_months',12)==$m?'selected':'' }}>{{ $m }} mois</option>@endforeach
                    </select>
                </div>
                <div><label class="admin-label">Prix abonnement (FCFA) *</label><input type="number" name="price" value="{{ old('price') }}" min="0" required class="admin-input"></div>
                <div><label class="admin-label">Statut</label>
                    <select name="status" required class="admin-select">
                        <option value="active" selected>Actif</option>
                        <option value="suspended">Suspendu</option>
                    </select>
                </div>
                <div><label class="admin-label">Paiement</label>
                    <select name="payment_status" required class="admin-select">
                        <option value="pending">En attente</option>
                        <option value="paid">Payé</option>
                    </select>
                </div>
                <div><label class="admin-label">Notes</label><textarea name="notes" rows="2" class="admin-textarea">{{ old('notes') }}</textarea></div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-navy-700 text-white font-bold py-3 rounded-lg hover:opacity-90 transition flex items-center justify-center gap-2">
                    🛡️ Créer l'abonnement CARE+
                </button>
                <a href="{{ route('admin.care.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
