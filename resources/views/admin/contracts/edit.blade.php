@extends('layouts.admin')
@section('title', 'Modifier ' . $contract->number)
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.contracts.show', $contract->id) }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Modifier : {{ $contract->number }}</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.contracts.update', $contract->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Client</h2>
                <div><label class="admin-label">Client CRM</label>
                    <select name="client_id" class="admin-select">
                        <option value="">— Aucun —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" {{ $contract->client_id==$c->id?'selected':'' }}>{{ $c->display_name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Nom *</label><input type="text" name="client_name" value="{{ old('client_name', $contract->client_name) }}" required class="admin-input"></div>
                    <div><label class="admin-label">Téléphone</label><input type="text" name="client_phone" value="{{ old('client_phone', $contract->client_phone) }}" class="admin-input"></div>
                </div>
                <div><label class="admin-label">Adresse</label><input type="text" name="client_address" value="{{ old('client_address', $contract->client_address) }}" class="admin-input"></div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Termes</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Début *</label><input type="date" name="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required class="admin-input"></div>
                    <div><label class="admin-label">Fin *</label><input type="date" name="end_date" value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}" required class="admin-input"></div>
                    <div><label class="admin-label">Interventions incluses *</label><input type="number" name="interventions_included" value="{{ old('interventions_included', $contract->interventions_included) }}" min="0" required class="admin-input"></div>
                    <div><label class="admin-label">Interventions utilisées</label><input type="number" name="interventions_used" value="{{ old('interventions_used', $contract->interventions_used) }}" min="0" class="admin-input"></div>
                    <div><label class="admin-label">SLA (heures) *</label><input type="number" name="response_time_hours" value="{{ old('response_time_hours', $contract->response_time_hours) }}" min="1" required class="admin-input"></div>
                </div>
                <div><label class="admin-label">Scope</label><textarea name="scope" rows="3" class="admin-textarea">{{ old('scope', $contract->scope) }}</textarea></div>
                <div><label class="admin-label">Notes</label><textarea name="notes" rows="2" class="admin-textarea">{{ old('notes', $contract->notes) }}</textarea></div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Paramètres</h2>
                <div><label class="admin-label">Type</label>
                    <select name="type" required class="admin-select">
                        @foreach(['basic'=>'Basique','standard'=>'Standard','premium'=>'Premium','custom'=>'Sur Mesure'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $contract->type===$val?'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="admin-label">Facturation</label>
                    <select name="billing_period" required class="admin-select">
                        @foreach(['monthly'=>'Mensuelle','quarterly'=>'Trimestrielle','annual'=>'Annuelle'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $contract->billing_period===$val?'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="admin-label">Montant (FCFA) *</label><input type="number" name="price" value="{{ old('price', $contract->price) }}" min="0" required class="admin-input"></div>
                <div><label class="admin-label">Statut</label>
                    <select name="status" required class="admin-select">
                        @foreach(['draft'=>'Brouillon','active'=>'Actif','expired'=>'Expiré','cancelled'=>'Annulé','suspended'=>'Suspendu'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $contract->status===$val?'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="admin-label">Paiement</label>
                    <select name="payment_status" required class="admin-select">
                        @foreach(['pending'=>'En attente','partial'=>'Partiel','paid'=>'Payé'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $contract->payment_status===$val?'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="admin-label">Montant payé (FCFA)</label><input type="number" name="amount_paid" value="{{ old('amount_paid', $contract->amount_paid) }}" min="0" class="admin-input"></div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gold-500 text-navy-900 font-bold py-3 rounded-lg hover:bg-gold-600 transition">Enregistrer</button>
                <a href="{{ route('admin.contracts.show', $contract->id) }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
