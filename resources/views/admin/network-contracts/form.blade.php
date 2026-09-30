@extends('layouts.admin')
@section('title', $contract->exists ? 'Modifier le contrat' : 'Nouveau contrat')
@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.network-contracts.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $contract->exists ? 'Modifier : ' . $contract->title : 'Nouveau contrat' }}</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ $contract->exists ? route('admin.network-contracts.update', $contract->id) : route('admin.network-contracts.store') }}" method="POST" class="max-w-3xl">
    @csrf
    @if($contract->exists) @method('PUT') @endif
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-5">
        <div>
            <label class="admin-label">Titre</label>
            <input type="text" name="title" value="{{ old('title', $contract->title) }}" required class="admin-input" placeholder="Ex: Contrat Partenaire Commercial IT Holding">
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="admin-label">Catégorie</label>
                <select name="type" required class="admin-select">
                    @foreach($partnerTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $contract->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label">Version</label>
                <input type="text" name="version" value="{{ old('version', $contract->version ?? '1.0') }}" required class="admin-input">
            </div>
            <div>
                <label class="admin-label">Statut</label>
                <select name="status" required class="admin-select">
                    @foreach(\App\Models\Contract::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $contract->status ?? 'draft') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="admin-label">En vigueur à partir du (optionnel)</label>
            <input type="date" name="effective_from" value="{{ old('effective_from', optional($contract->effective_from)->format('Y-m-d')) }}" class="admin-input max-w-xs">
        </div>

        <div>
            <label class="admin-label">Contenu du contrat</label>
            <textarea name="content" rows="18" required class="admin-textarea font-mono text-xs" placeholder="Identité des parties, objet, mission, rémunération, délais, obligations, confidentialité, propriété intellectuelle, responsabilité, conditions de résiliation, conditions de paiement, règles fiscales applicables...">{{ old('content', $contract->content) }}</textarea>
            <p class="admin-hint">Texte affiché tel quel au partenaire. Passer ce contrat en "En vigueur" pour qu'il devienne opposable, puis en cas d'évolution créer une nouvelle version plutôt que d'éditer celle-ci une fois acceptée.</p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-gold-600 text-white font-bold px-6 py-2.5 rounded-md hover:bg-gold-700 transition text-sm">Enregistrer</button>
            <a href="{{ route('admin.network-contracts.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 text-sm">Annuler</a>
        </div>
    </div>
</form>
@endsection
