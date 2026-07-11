@extends('layouts.admin')
@section('title', 'Modifier ' . $client->display_name)
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.clients.show', $client->id) }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Modifier : {{ $client->display_name }}</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.clients.update', $client->id) }}" method="POST"
      x-data="{ isPro: {{ $client->is_professional ? 'true' : 'false' }} }">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <h2 class="admin-section-title">Type</h2>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="is_professional" value="0" @change="isPro=false" {{ !$client->is_professional ? 'checked' : '' }} class="text-navy-600"><span class="text-sm font-semibold">👤 Particulier</span></label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="is_professional" value="1" @change="isPro=true" {{ $client->is_professional ? 'checked' : '' }} class="text-blue-600"><span class="text-sm font-semibold">🏢 Professionnel</span></label>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Identité</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Prénom *</label><input type="text" name="first_name" value="{{ old('first_name', $client->first_name) }}" required class="admin-input"></div>
                    <div><label class="admin-label">Nom *</label><input type="text" name="last_name" value="{{ old('last_name', $client->last_name) }}" required class="admin-input"></div>
                </div>
                <div x-show="isPro" x-cloak class="grid grid-cols-3 gap-4">
                    <div class="col-span-3"><label class="admin-label">Entreprise</label><input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" class="admin-input"></div>
                    <div><label class="admin-label">RCCM</label><input type="text" name="rccm" value="{{ old('rccm', $client->rccm) }}" class="admin-input"></div>
                    <div><label class="admin-label">NINEA</label><input type="text" name="ninea" value="{{ old('ninea', $client->ninea) }}" class="admin-input"></div>
                    <div><label class="admin-label">Secteur</label><input type="text" name="sector" value="{{ old('sector', $client->sector) }}" class="admin-input"></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Contact</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Email</label><input type="email" name="email" value="{{ old('email', $client->email) }}" class="admin-input"></div>
                    <div><label class="admin-label">Téléphone</label><input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="admin-input"></div>
                    <div><label class="admin-label">Tél. secondaire</label><input type="text" name="phone2" value="{{ old('phone2', $client->phone2) }}" class="admin-input"></div>
                    <div><label class="admin-label">Ville</label><input type="text" name="city" value="{{ old('city', $client->city) }}" class="admin-input"></div>
                </div>
                <div><label class="admin-label">Adresse</label><input type="text" name="address" value="{{ old('address', $client->address) }}" class="admin-input"></div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Notes</label>
                <textarea name="notes" rows="3" class="admin-textarea">{{ old('notes', $client->notes) }}</textarea>
            </div>
        </div>

        <div class="space-y-6">
            <div x-show="isPro" x-cloak class="bg-white rounded-lg shadow-sm border border-blue-100 p-5 space-y-4">
                <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wider">Crédit Professionnel</h2>
                <div><label class="admin-label">Plafond crédit (FCFA)</label><input type="number" name="credit_limit" value="{{ old('credit_limit', $client->credit_limit) }}" min="0" class="admin-input"></div>
                <div><label class="admin-label">Conditions paiement</label>
                    <select name="payment_terms" class="admin-select">
                        <option value="">— Comptant —</option>
                        @foreach(['semaine'=>'Hebdomadaire','15j'=>'Bi-mensuel','mois'=>'Mensuel','trimestre'=>'Trimestriel'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $client->payment_terms === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gold-500 text-navy-900 font-bold py-3 rounded-lg hover:bg-gold-600 transition">Enregistrer les modifications</button>
                <a href="{{ route('admin.clients.show', $client->id) }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
