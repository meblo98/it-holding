@extends('layouts.admin')
@section('title', 'Nouvelle Garantie')
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.warranties.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Nouvelle Garantie</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.warranties.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- PRODUCT --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Produit Couvert</h2>
                <div>
                    <label class="admin-label">Produit du catalogue</label>
                    <select name="product_id" class="admin-select">
                        <option value="">— Sélectionner dans le catalogue —</option>
                        @foreach($products as $p)<option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">Nom produit (saisi manuellement) <span class="text-red-500">*</span></label>
                    <input type="text" name="product_name" value="{{ old('product_name') }}" required placeholder="Ex: HP ProBook 450 G10" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">N° de Série</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="Ex: 5CD32XXYZ" class="admin-input font-mono">
                </div>
            </div>

            {{-- CLIENT --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Client</h2>
                <div>
                    <label class="admin-label">Client CRM</label>
                    <select name="client_id" class="admin-select">
                        <option value="">— Sélectionner un client —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->display_name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">Nom client <span class="text-red-500">*</span></label>
                        <input type="text" name="client_name" value="{{ old('client_name') }}" required class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">Téléphone</label>
                        <input type="text" name="client_phone" value="{{ old('client_phone') }}" class="admin-input">
                    </div>
                </div>
                <div>
                    <label class="admin-label">Facture liée</label>
                    <select name="invoice_id" class="admin-select">
                        <option value="">— Aucune facture —</option>
                        @foreach($invoices as $inv)<option value="{{ $inv->id }}" {{ old('invoice_id') == $inv->id ? 'selected' : '' }}>{{ $inv->number }} — {{ $inv->client_name }}</option>@endforeach
                    </select>
                </div>
            </div>

            {{-- COVERAGE --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Couverture</h2>
                <div>
                    <label class="admin-label">Ce qui est couvert</label>
                    <textarea name="coverage_notes" rows="2" placeholder="Ex: Pannes matérielles, remplacement pièces défectueuses..." class="admin-textarea">{{ old('coverage_notes') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">Exclusions</label>
                    <textarea name="exclusions" rows="2" placeholder="Ex: Écran cassé par choc, usure normale de la batterie..." class="admin-textarea">{{ old('exclusions') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">Notes internes</label>
                    <textarea name="notes" rows="2" class="admin-textarea">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Paramètres Garantie</h2>
                <div>
                    <label class="admin-label">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="admin-select">
                        <option value="standard" {{ old('type','standard')==='standard'?'selected':'' }}>Standard</option>
                        <option value="extended" {{ old('type')==='extended'?'selected':'' }}>Étendue</option>
                        <option value="care_plus" {{ old('type')==='care_plus'?'selected':'' }}>IT HOLDING CARE+</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label">Durée (mois) <span class="text-red-500">*</span></label>
                    <select name="duration_months" required class="admin-select">
                        @foreach([3,6,12,18,24,36,48,60] as $m)
                        <option value="{{ $m }}" {{ old('duration_months',12)==$m?'selected':'' }}>{{ $m }} mois{{ $m === 12 ? ' (1 an)' : ($m === 24 ? ' (2 ans)' : '') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">Date d'achat <span class="text-red-500">*</span></label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Statut <span class="text-red-500">*</span></label>
                    <select name="status" required class="admin-select">
                        <option value="active" selected>Active</option>
                        <option value="claimed">Réclamée</option>
                        <option value="void">Annulée</option>
                    </select>
                </div>
                <p class="text-xs text-gray-400 italic">⚙️ La date d'expiration est calculée automatiquement.</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gradient-to-r from-navy-600 to-navy-700 text-white font-bold py-3 rounded-lg hover:from-navy-700 hover:to-navy-800 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Créer la Garantie
                </button>
                <a href="{{ route('admin.warranties.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
