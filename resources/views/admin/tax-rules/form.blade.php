@extends('layouts.admin')
@section('title', $rule->exists ? 'Modifier la règle fiscale' : 'Nouvelle règle fiscale')
@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.tax-rules.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $rule->exists ? 'Modifier : ' . $rule->name : 'Nouvelle règle fiscale' }}</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ $rule->exists ? route('admin.tax-rules.update', $rule->id) : route('admin.tax-rules.store') }}" method="POST" class="max-w-2xl">
    @csrf
    @if($rule->exists) @method('PUT') @endif
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-5">
        <div>
            <label class="admin-label">Nom de la règle</label>
            <input type="text" name="name" value="{{ old('name', $rule->name) }}" required class="admin-input" placeholder="Ex: Retenue BRS - prestations de services">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">Pays (code ISO 2 lettres)</label>
                <input type="text" name="country" value="{{ old('country', $rule->country ?? 'SN') }}" maxlength="2" required class="admin-input uppercase">
            </div>
            <div>
                <label class="admin-label">Régime fiscal (information libre)</label>
                <input type="text" name="tax_regime" value="{{ old('tax_regime', $rule->tax_regime) }}" class="admin-input" placeholder="Ex: régime réel">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">Type de bénéficiaire</label>
                <select name="beneficiary_type" required class="admin-select">
                    @foreach(\App\Models\TaxRule::BENEFICIARY_TYPES as $key => $label)
                        <option value="{{ $key }}" {{ old('beneficiary_type', $rule->beneficiary_type ?? 'all') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label">Nature de la prestation</label>
                <select name="prestation_nature" required class="admin-select">
                    @foreach(\App\Models\TaxRule::PRESTATION_NATURES as $key => $label)
                        <option value="{{ $key }}" {{ old('prestation_nature', $rule->prestation_nature ?? 'all') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4 space-y-4">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_exempt" id="is_exempt" value="1" {{ old('is_exempt', $rule->is_exempt) ? 'checked' : '' }} class="rounded border-gray-300">
                <label for="is_exempt" class="text-sm font-medium text-gray-700">Exonération explicite (aucune retenue, quel que soit le montant)</label>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="admin-label">Taux de retenue (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="rate" value="{{ old('rate', $rule->rate ?? 0) }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Seuil d'application (FCFA)</label>
                    <input type="number" step="0.01" min="0" name="threshold_amount" value="{{ old('threshold_amount', $rule->threshold_amount) }}" class="admin-input" placeholder="Ex: 25000 — laisser vide si aucun seuil">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">En vigueur à partir du</label>
                <input type="date" name="effective_from" value="{{ old('effective_from', optional($rule->effective_from)->format('Y-m-d')) }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">Jusqu'au (optionnel)</label>
                <input type="date" name="effective_to" value="{{ old('effective_to', optional($rule->effective_to)->format('Y-m-d')) }}" class="admin-input">
            </div>
        </div>

        <div>
            <label class="admin-label">Notes / référence légale</label>
            <textarea name="notes" rows="3" class="admin-input" placeholder="Ex: CGI Sénégal, retenue à la source sur prestations à partir de 25 000 FCFA — validé par le comptable le ...">{{ old('notes', $rule->notes) }}</textarea>
        </div>

        <div class="flex items-center gap-2 border-t border-gray-100 pt-4">
            <input type="checkbox" name="active" id="active" value="1" {{ old('active', $rule->exists ? $rule->active : true) ? 'checked' : '' }} class="rounded border-gray-300">
            <label for="active" class="text-sm font-medium text-gray-700">Règle active</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-gold-600 text-white font-bold px-6 py-2.5 rounded-md hover:bg-gold-700 transition text-sm">Enregistrer</button>
            <a href="{{ route('admin.tax-rules.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 text-sm">Annuler</a>
        </div>
    </div>
</form>
@endsection
