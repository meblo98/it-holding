@extends('layouts.admin')
@section('title', $deal->exists ? 'Modifier l\'affaire' : 'Nouvelle affaire')
@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ $deal->exists ? route('admin.crm.show', $deal->id) : route('admin.crm.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $deal->exists ? 'Modifier : ' . $deal->title : 'Nouvelle affaire' }}</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ $deal->exists ? route('admin.crm.update', $deal->id) : route('admin.crm.store') }}" method="POST" class="max-w-3xl" x-data="{ stage: @js(old('stage', $deal->stage)) }">
    @csrf
    @if($deal->exists) @method('PUT') @endif
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-5">
        <div>
            <label class="admin-label">Intitulé de l'affaire</label>
            <input type="text" name="title" value="{{ old('title', $deal->title) }}" required class="admin-input" placeholder="Ex: Renouvellement du parc informatique — 30 postes">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="admin-label">Client existant (optionnel)</label>
                <select name="client_id" class="admin-select">
                    <option value="">— Nouveau prospect —</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ (int) old('client_id', $deal->client_id) === $c->id ? 'selected' : '' }}>{{ $c->company_name ?: trim($c->first_name . ' ' . $c->last_name) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label">Société</label>
                <input type="text" name="company" value="{{ old('company', $deal->company) }}" class="admin-input">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="admin-label">Contact</label>
                <input type="text" name="contact_name" value="{{ old('contact_name', $deal->contact_name) }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">Téléphone</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $deal->contact_phone) }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">Email</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $deal->contact_email) }}" class="admin-input">
            </div>
        </div>

        <div>
            <label class="admin-label">Besoin du client</label>
            <textarea name="need" rows="4" class="admin-textarea" placeholder="Ex: 30 ordinateurs portables pour l'administration, 2 imprimantes réseau, installation comprise">{{ old('need', $deal->need) }}</textarea>
            <p class="admin-hint">Plus le besoin est précis, meilleures seront l'analyse et le brouillon de devis de l'assistant IA.</p>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="admin-label">Étape</label>
                <select name="stage" x-model="stage" class="admin-select">
                    @foreach(\App\Models\CrmDeal::STAGES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label">Montant estimé (FCFA)</label>
                <input type="number" min="0" step="1" name="amount" value="{{ old('amount', $deal->amount) }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">Source</label>
                <select name="source" class="admin-select">
                    @foreach(\App\Models\CrmDeal::SOURCES as $key => $label)
                        <option value="{{ $key }}" {{ old('source', $deal->source) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div x-show="stage === 'lost'" x-cloak>
            <label class="admin-label">Motif de perte</label>
            <input type="text" name="lost_reason" value="{{ old('lost_reason', $deal->lost_reason) }}" class="admin-input" placeholder="Prix, délai, concurrent, projet abandonné…">
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="admin-label">Commercial en charge</label>
                <select name="owner_id" class="admin-select">
                    <option value="">— Non attribuée —</option>
                    @foreach($owners as $o)
                        <option value="{{ $o->id }}" {{ (int) old('owner_id', $deal->owner_id) === $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label">Prochaine relance</label>
                <input type="datetime-local" name="next_action_at" value="{{ old('next_action_at', optional($deal->next_action_at)->format('Y-m-d\TH:i')) }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">Action prévue</label>
                <input type="text" name="next_action_note" value="{{ old('next_action_note', $deal->next_action_note) }}" class="admin-input" placeholder="Rappeler pour le devis">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-gold-600 text-white font-bold px-6 py-2.5 rounded-md hover:bg-gold-700 transition text-sm">Enregistrer</button>
        </div>
    </div>
</form>
@endsection
