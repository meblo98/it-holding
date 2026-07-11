@extends('layouts.admin')
@section('title', 'Nouveau Client')
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.clients.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Nouveau Client</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.clients.store') }}" method="POST"
      x-data="{ isPro: {{ old('is_professional') ? 'true' : 'false' }} }">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- TYPE --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <h2 class="admin-section-title">Type de client</h2>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_professional" value="0" x-model="isPro" :value="false" @change="isPro=false" {{ !old('is_professional') ? 'checked' : '' }} class="text-navy-600">
                        <span class="text-sm font-semibold text-gray-700">👤 Particulier</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_professional" value="1" x-model="isPro" :value="true" @change="isPro=true" {{ old('is_professional') ? 'checked' : '' }} class="text-blue-600">
                        <span class="text-sm font-semibold text-gray-700">🏢 Professionnel / Entreprise</span>
                    </label>
                </div>
            </div>

            {{-- IDENTITY --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Identité</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required class="admin-input">
                    </div>
                </div>

                <div x-show="isPro" x-cloak class="grid grid-cols-3 gap-4">
                    <div class="col-span-3">
                        <label class="admin-label">Raison sociale / Entreprise</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">RCCM</label>
                        <input type="text" name="rccm" value="{{ old('rccm') }}" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">NINEA</label>
                        <input type="text" name="ninea" value="{{ old('ninea') }}" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">Secteur d'activité</label>
                        <input type="text" name="sector" value="{{ old('sector') }}" class="admin-input">
                    </div>
                </div>
            </div>

            {{-- CONTACT --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Contact</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">Téléphone principal</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">Téléphone secondaire</label>
                        <input type="text" name="phone2" value="{{ old('phone2') }}" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">Ville</label>
                        <input type="text" name="city" value="{{ old('city', 'Dakar') }}" class="admin-input">
                    </div>
                </div>
                <div>
                    <label class="admin-label">Adresse complète</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="admin-input">
                </div>
            </div>

            {{-- NOTES --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Notes internes</label>
                <textarea name="notes" rows="3" class="admin-textarea">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="space-y-6">
            {{-- Professional credit --}}
            <div x-show="isPro" x-cloak class="bg-white rounded-lg shadow-sm border border-blue-100 p-5 space-y-4">
                <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wider">Crédit Professionnel</h2>
                <div>
                    <label class="admin-label">Plafond crédit (FCFA)</label>
                    <input type="number" name="credit_limit" value="{{ old('credit_limit', 0) }}" min="0" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Conditions de paiement</label>
                    <select name="payment_terms" class="admin-select">
                        <option value="">— Comptant —</option>
                        <option value="semaine" {{ old('payment_terms') === 'semaine' ? 'selected' : '' }}>Chaque semaine</option>
                        <option value="15j" {{ old('payment_terms') === '15j' ? 'selected' : '' }}>Tous les 15 jours</option>
                        <option value="mois" {{ old('payment_terms') === 'mois' ? 'selected' : '' }}>Chaque mois</option>
                        <option value="trimestre" {{ old('payment_terms') === 'trimestre' ? 'selected' : '' }}>Chaque trimestre</option>
                    </select>
                </div>
            </div>

            {{-- Submit --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gradient-to-r from-navy-600 to-navy-700 text-white font-bold py-3 rounded-lg hover:from-navy-700 hover:to-navy-800 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer le client
                </button>
                <a href="{{ route('admin.clients.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
