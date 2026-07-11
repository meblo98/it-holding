@extends('layouts.admin')
@section('title', 'Modifier ' . $care->number)
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.care.show', $care->id) }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Modifier CARE+ : {{ $care->number }}</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.care.update', $care->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Plan</h2>
                <div class="grid grid-cols-3 gap-3 text-center">
                    @foreach(['standard'=>['🛡️','Standard'],'premium'=>['⭐','Premium'],'enterprise'=>['🏆','Entreprise']] as $val => [$icon, $lbl])
                    <label class="cursor-pointer"><input type="radio" name="plan" value="{{ $val }}" class="sr-only" {{ $care->plan===$val?'checked':'' }}>
                        <div class="border-2 rounded-lg p-3 text-sm transition {{ $care->plan===$val ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300' }}">{{ $icon }} {{ $lbl }}</div>
                    </label>
                    @endforeach
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="has_priority_support" value="1" class="admin-check" {{ $care->has_priority_support?'checked':'' }}><span>Assistance prioritaire</span></label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="has_home_service" value="1" class="admin-check" {{ $care->has_home_service?'checked':'' }}><span>À domicile</span></label>
                    <div class="flex items-center gap-2 text-sm"><label class="flex items-center gap-1"><input type="checkbox" name="has_repair_discount" value="1" class="admin-check" {{ $care->has_repair_discount?'checked':'' }}><span>Réduction répa.</span></label><input type="number" name="repair_discount_pct" value="{{ $care->repair_discount_pct }}" min="0" max="100" class="w-16 border-gray-300 rounded-md shadow-sm text-sm"><span class="text-gray-400">%</span></div>
                    <div class="flex items-center gap-2 text-sm"><label class="flex items-center gap-1"><input type="checkbox" name="has_parts_discount" value="1" class="admin-check" {{ $care->has_parts_discount?'checked':'' }}><span>Réduction pièces</span></label><input type="number" name="parts_discount_pct" value="{{ $care->parts_discount_pct }}" min="0" max="100" class="w-16 border-gray-300 rounded-md shadow-sm text-sm"><span class="text-gray-400">%</span></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Client & Produit</h2>
                <div><label class="admin-label">Client CRM</label>
                    <select name="client_id" class="admin-select">
                        <option value="">— Aucun —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" {{ $care->client_id==$c->id?'selected':'' }}>{{ $c->display_name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Nom *</label><input type="text" name="client_name" value="{{ old('client_name', $care->client_name) }}" required class="admin-input"></div>
                    <div><label class="admin-label">Téléphone</label><input type="text" name="client_phone" value="{{ old('client_phone', $care->client_phone) }}" class="admin-input"></div>
                    <div><label class="admin-label">Produit *</label><input type="text" name="product_name" value="{{ old('product_name', $care->product_name) }}" required class="admin-input"></div>
                    <div><label class="admin-label">N° Série</label><input type="text" name="serial_number" value="{{ old('serial_number', $care->serial_number) }}" class="admin-input font-mono"></div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Durée & Prix</h2>
                <div><label class="admin-label">Date début *</label><input type="date" name="start_date" value="{{ old('start_date', $care->start_date->format('Y-m-d')) }}" required class="admin-input"></div>
                <div><label class="admin-label">Durée (mois) *</label>
                    <select name="duration_months" required class="admin-select">
                        @foreach([6,12,24,36] as $m)<option value="{{ $m }}" {{ $care->duration_months==$m?'selected':'' }}>{{ $m }} mois</option>@endforeach
                    </select>
                </div>
                <div><label class="admin-label">Prix (FCFA) *</label><input type="number" name="price" value="{{ old('price', $care->price) }}" min="0" required class="admin-input"></div>
                <div><label class="admin-label">Statut</label>
                    <select name="status" required class="admin-select">
                        @foreach(['active'=>'Actif','expired'=>'Expiré','cancelled'=>'Annulé','suspended'=>'Suspendu'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $care->status===$val?'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="admin-label">Paiement</label>
                    <select name="payment_status" required class="admin-select">
                        @foreach(['pending'=>'En attente','paid'=>'Payé'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $care->payment_status===$val?'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="admin-label">Notes</label><textarea name="notes" rows="2" class="admin-textarea">{{ old('notes', $care->notes) }}</textarea></div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gold-500 text-navy-900 font-bold py-3 rounded-lg hover:bg-gold-600 transition">Enregistrer</button>
                <a href="{{ route('admin.care.show', $care->id) }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
