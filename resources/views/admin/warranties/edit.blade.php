@extends('layouts.admin')
@section('title', 'Modifier Garantie ' . $warranty->number)
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.warranties.show', $warranty->id) }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Modifier : {{ $warranty->number }}</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.warranties.update', $warranty->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Produit</h2>
                <div>
                    <label class="admin-label">Produit du catalogue</label>
                    <select name="product_id" class="admin-select">
                        <option value="">— Aucun —</option>
                        @foreach($products as $p)<option value="{{ $p->id }}" {{ $warranty->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach
                    </select>
                </div>
                <div><label class="admin-label">Nom produit *</label><input type="text" name="product_name" value="{{ old('product_name', $warranty->product_name) }}" required class="admin-input"></div>
                <div><label class="admin-label">N° de Série</label><input type="text" name="serial_number" value="{{ old('serial_number', $warranty->serial_number) }}" class="admin-input font-mono"></div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Client</h2>
                <div>
                    <label class="admin-label">Client CRM</label>
                    <select name="client_id" class="admin-select">
                        <option value="">— Aucun —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" {{ $warranty->client_id == $c->id ? 'selected' : '' }}>{{ $c->display_name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Nom *</label><input type="text" name="client_name" value="{{ old('client_name', $warranty->client_name) }}" required class="admin-input"></div>
                    <div><label class="admin-label">Téléphone</label><input type="text" name="client_phone" value="{{ old('client_phone', $warranty->client_phone) }}" class="admin-input"></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Couverture</h2>
                <div><label class="admin-label">Ce qui est couvert</label><textarea name="coverage_notes" rows="2" class="admin-textarea">{{ old('coverage_notes', $warranty->coverage_notes) }}</textarea></div>
                <div><label class="admin-label">Exclusions</label><textarea name="exclusions" rows="2" class="admin-textarea">{{ old('exclusions', $warranty->exclusions) }}</textarea></div>
                <div><label class="admin-label">Notes</label><textarea name="notes" rows="2" class="admin-textarea">{{ old('notes', $warranty->notes) }}</textarea></div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Paramètres</h2>
                <div><label class="admin-label">Type</label>
                    <select name="type" required class="admin-select">
                        <option value="standard" {{ $warranty->type==='standard'?'selected':'' }}>Standard</option>
                        <option value="extended" {{ $warranty->type==='extended'?'selected':'' }}>Étendue</option>
                        <option value="care_plus" {{ $warranty->type==='care_plus'?'selected':'' }}>IT HOLDING CARE+</option>
                    </select>
                </div>
                <div><label class="admin-label">Durée (mois)</label>
                    <select name="duration_months" required class="admin-select">
                        @foreach([3,6,12,18,24,36,48,60] as $m)
                        <option value="{{ $m }}" {{ $warranty->duration_months==$m?'selected':'' }}>{{ $m }} mois</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="admin-label">Date d'achat</label><input type="date" name="purchase_date" value="{{ old('purchase_date', $warranty->purchase_date->format('Y-m-d')) }}" required class="admin-input"></div>
                <div><label class="admin-label">Statut</label>
                    <select name="status" required class="admin-select">
                        <option value="active" {{ $warranty->status==='active'?'selected':'' }}>Active</option>
                        <option value="expired" {{ $warranty->status==='expired'?'selected':'' }}>Expirée</option>
                        <option value="claimed" {{ $warranty->status==='claimed'?'selected':'' }}>Réclamée</option>
                        <option value="void" {{ $warranty->status==='void'?'selected':'' }}>Annulée</option>
                    </select>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gold-500 text-navy-900 font-bold py-3 rounded-lg hover:bg-gold-600 transition">Enregistrer</button>
                <a href="{{ route('admin.warranties.show', $warranty->id) }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
