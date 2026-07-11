@extends('layouts.admin')
@section('title', 'Nouveau Contrat de Maintenance')
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.contracts.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Nouveau Contrat de Maintenance</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.contracts.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- CLIENT --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Client</h2>
                <div><label class="admin-label">Client CRM</label>
                    <select name="client_id" class="admin-select">
                        <option value="">— Saisie manuelle —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" {{ old('client_id')==$c->id?'selected':'' }}>{{ $c->display_name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Nom/Entreprise *</label><input type="text" name="client_name" value="{{ old('client_name') }}" required class="admin-input"></div>
                    <div><label class="admin-label">Téléphone</label><input type="text" name="client_phone" value="{{ old('client_phone') }}" class="admin-input"></div>
                </div>
                <div><label class="admin-label">Adresse</label><input type="text" name="client_address" value="{{ old('client_address') }}" class="admin-input"></div>
            </div>

            {{-- CONTRACT TERMS --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Termes du Contrat</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Date de début *</label><input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="admin-input"></div>
                    <div><label class="admin-label">Date de fin *</label><input type="date" name="end_date" value="{{ old('end_date') }}" required class="admin-input"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Nb. interventions incluses *</label><input type="number" name="interventions_included" value="{{ old('interventions_included', 12) }}" min="0" required class="admin-input"></div>
                    <div><label class="admin-label">SLA délai réponse (heures) *</label><input type="number" name="response_time_hours" value="{{ old('response_time_hours', 24) }}" min="1" required class="admin-input"></div>
                </div>
                <div><label class="admin-label">Périmètre / Scope</label>
                    <textarea name="scope" rows="3" placeholder="Matériels et logiciels couverts, sites concernés..." class="admin-textarea">{{ old('scope') }}</textarea>
                </div>
                <div><label class="admin-label">Notes</label><textarea name="notes" rows="2" class="admin-textarea">{{ old('notes') }}</textarea></div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Type & Facturation</h2>
                <div><label class="admin-label">Type de contrat *</label>
                    <select name="type" required class="admin-select">
                        <option value="basic" {{ old('type')==='basic'?'selected':'' }}>Basique</option>
                        <option value="standard" {{ old('type','standard')==='standard'?'selected':'' }}>Standard</option>
                        <option value="premium" {{ old('type')==='premium'?'selected':'' }}>Premium</option>
                        <option value="custom" {{ old('type')==='custom'?'selected':'' }}>Sur Mesure</option>
                    </select>
                </div>
                <div><label class="admin-label">Périodicité facturation *</label>
                    <select name="billing_period" required class="admin-select">
                        <option value="monthly" {{ old('billing_period')==='monthly'?'selected':'' }}>Mensuelle</option>
                        <option value="quarterly" {{ old('billing_period')==='quarterly'?'selected':'' }}>Trimestrielle</option>
                        <option value="annual" {{ old('billing_period','annual')==='annual'?'selected':'' }}>Annuelle</option>
                    </select>
                </div>
                <div><label class="admin-label">Montant total (FCFA) *</label><input type="number" name="price" value="{{ old('price') }}" min="0" required class="admin-input"></div>
                <div><label class="admin-label">Statut</label>
                    <select name="status" required class="admin-select">
                        <option value="draft">Brouillon</option>
                        <option value="active" selected>Actif</option>
                    </select>
                </div>
                <div><label class="admin-label">Paiement</label>
                    <select name="payment_status" required class="admin-select">
                        <option value="pending" selected>En attente</option>
                        <option value="partial">Partiel</option>
                        <option value="paid">Payé</option>
                    </select>
                </div>
                <div><label class="admin-label">Montant payé (FCFA)</label><input type="number" name="amount_paid" value="{{ old('amount_paid', 0) }}" min="0" class="admin-input"></div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gradient-to-r from-navy-600 to-navy-700 text-white font-bold py-3 rounded-lg hover:from-navy-700 hover:to-navy-800 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Créer le Contrat
                </button>
                <a href="{{ route('admin.contracts.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
