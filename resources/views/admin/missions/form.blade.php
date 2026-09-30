@extends('layouts.admin')
@section('title', $mission->exists ? 'Modifier la mission' : 'Nouvelle mission')
@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ $mission->exists ? route('admin.missions.show', $mission->id) : route('admin.missions.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $mission->exists ? 'Modifier : ' . $mission->title : 'Nouvelle mission' }}</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ $mission->exists ? route('admin.missions.update', $mission->id) : route('admin.missions.store') }}" method="POST" class="max-w-3xl">
    @csrf
    @if($mission->exists) @method('PUT') @endif
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-5">
        <div>
            <label class="admin-label">Titre</label>
            <input type="text" name="title" value="{{ old('title', $mission->title) }}" required class="admin-input" placeholder="Ex: Installation de 20 caméras à Dakar">
        </div>

        <div>
            <label class="admin-label">Description</label>
            <textarea name="description" rows="6" required class="admin-textarea" placeholder="Contexte, périmètre, livrables attendus...">{{ old('description', $mission->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">Ville</label>
                <input type="text" name="city" value="{{ old('city', $mission->city) }}" class="admin-input" placeholder="Dakar">
            </div>
            <div>
                <label class="admin-label">Lieu précis</label>
                <input type="text" name="location" value="{{ old('location', $mission->location) }}" class="admin-input" placeholder="Adresse, site client...">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="admin-label">Budget (FCFA)</label>
                <input type="number" step="1" min="0" name="budget" value="{{ old('budget', $mission->budget) }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">Durée</label>
                <input type="text" name="duration" value="{{ old('duration', $mission->duration) }}" class="admin-input" placeholder="3 jours">
            </div>
            <div>
                <label class="admin-label">Personnes nécessaires</label>
                <input type="number" min="1" name="positions" value="{{ old('positions', $mission->positions) }}" required class="admin-input">
            </div>
        </div>

        <div>
            <label class="admin-label">Compétences requises (séparées par des virgules)</label>
            <input type="text" name="required_skills" value="{{ old('required_skills', implode(', ', $mission->required_skills ?? [])) }}" class="admin-input" placeholder="Vidéosurveillance, Réseau Cat6">
            <p class="admin-hint">Utilisées pour suggérer automatiquement les profils les plus adaptés.</p>
        </div>

        <div>
            <label class="admin-label">Ouverte à</label>
            @php $targets = old('target_types', $mission->target_types ?? []); @endphp
            <div class="flex gap-6 mt-1">
                @foreach(['freelance' => 'Freelances', 'prestataire' => 'Prestataires / structures'] as $key => $label)
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="target_types[]" value="{{ $key }}" class="admin-check" {{ in_array($key, $targets, true) ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            <p class="admin-hint">Aucune case cochée = ouverte aux freelances et aux prestataires.</p>
        </div>

        <div>
            <label class="admin-label">Matériel</label>
            <textarea name="equipment" rows="2" class="admin-textarea" placeholder="Matériel fourni par IT Holding / à prévoir par le prestataire">{{ old('equipment', $mission->equipment) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">Date de début</label>
                <input type="date" name="start_date" value="{{ old('start_date', optional($mission->start_date)->format('Y-m-d')) }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">Candidatures jusqu'au</label>
                <input type="date" name="apply_until" value="{{ old('apply_until', optional($mission->apply_until)->format('Y-m-d')) }}" class="admin-input">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">Client (optionnel)</label>
                <select name="client_id" class="admin-select">
                    <option value="">— Mission interne IT Holding —</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ (int) old('client_id', $mission->client_id) === $c->id ? 'selected' : '' }}>{{ $c->company_name ?: trim($c->first_name . ' ' . $c->last_name) }}</option>
                    @endforeach
                </select>
                <p class="admin-hint">Jamais affiché aux freelances.</p>
            </div>
            <div>
                <label class="admin-label">Statut</label>
                <select name="status" required class="admin-select">
                    @foreach(\App\Models\Mission::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $mission->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="admin-hint">« Ouverte » publie la mission auprès des freelances et prestataires.</p>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-gold-600 text-white font-bold px-6 py-2.5 rounded-md hover:bg-gold-700 transition text-sm">Enregistrer</button>
        </div>
    </div>
</form>
@endsection
