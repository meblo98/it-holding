@extends('layouts.admin')
@section('title', 'Modifier ' . $ticket->number)
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->number }} — Modifier</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- TICKET --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Ticket</h2>
                <div><label class="admin-label">Titre *</label><input type="text" name="title" value="{{ old('title', $ticket->title) }}" required class="admin-input"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Type</label>
                        <select name="type" required class="admin-select">
                            @foreach(['repair'=>'Réparation','installation'=>'Installation','maintenance'=>'Maintenance','advice'=>'Conseil','warranty_claim'=>'Réclamation Garantie'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $ticket->type===$val?'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="admin-label">Priorité</label>
                        <select name="priority" required class="admin-select">
                            @foreach(['low'=>'Faible','normal'=>'Normal','high'=>'Élevé','urgent'=>'Urgent'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $ticket->priority===$val?'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div><label class="admin-label">Description *</label><textarea name="description" rows="3" required class="admin-textarea">{{ old('description', $ticket->description) }}</textarea></div>
                <div><label class="admin-label">Ajouter des photos</label><input type="file" name="attachments[]" multiple accept="image/*,.pdf" class="admin-file"></div>
            </div>

            {{-- CLIENT --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Client</h2>
                <div><label class="admin-label">Client CRM</label>
                    <select name="client_id" class="admin-select">
                        <option value="">— Aucun —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" {{ $ticket->client_id==$c->id?'selected':'' }}>{{ $c->display_name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Nom *</label><input type="text" name="client_name" value="{{ old('client_name', $ticket->client_name) }}" required class="admin-input"></div>
                    <div><label class="admin-label">Téléphone</label><input type="text" name="client_phone" value="{{ old('client_phone', $ticket->client_phone) }}" class="admin-input"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Produit</label><input type="text" name="product_name" value="{{ old('product_name', $ticket->product_name) }}" class="admin-input"></div>
                    <div><label class="admin-label">N° Série</label><input type="text" name="serial_number" value="{{ old('serial_number', $ticket->serial_number) }}" class="admin-input font-mono"></div>
                </div>
                <div class="flex items-center gap-2"><input type="checkbox" name="covered_by_warranty" value="1" id="cwv" class="admin-check" {{ $ticket->covered_by_warranty ? 'checked' : '' }}><label for="cwv" class="text-sm font-medium text-gray-700">Couvert par garantie</label></div>
            </div>

            {{-- TECHNICIAN REPORT --}}
            <div class="bg-white rounded-lg shadow-sm border border-purple-100 p-5 space-y-4">
                <h2 class="text-sm font-bold text-purple-700 uppercase tracking-wider">Rapport Technicien</h2>
                <div><label class="admin-label">Diagnostic</label><textarea name="diagnosis" rows="3" class="admin-textarea">{{ old('diagnosis', $ticket->diagnosis) }}</textarea></div>
                <div><label class="admin-label">Notes d'intervention</label><textarea name="intervention_notes" rows="3" class="admin-textarea">{{ old('intervention_notes', $ticket->intervention_notes) }}</textarea></div>
                <div><label class="admin-label">Pièces utilisées</label><textarea name="parts_used" rows="2" class="admin-textarea">{{ old('parts_used', $ticket->parts_used) }}</textarea></div>
                <div><label class="admin-label">Coût de réparation (FCFA)</label><input type="number" name="repair_cost" value="{{ old('repair_cost', $ticket->repair_cost) }}" min="0" class="admin-input"></div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Statut & Planification</h2>
                <div><label class="admin-label">Statut</label>
                    <select name="status" required class="admin-select">
                        @foreach(['open'=>'Ouvert','diagnosed'=>'Diagnostiqué','in_progress'=>'En cours','waiting_parts'=>'Attente pièces','resolved'=>'Résolu','closed'=>'Clôturé','cancelled'=>'Annulé'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $ticket->status===$val?'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="admin-label">Technicien</label>
                    <select name="assigned_to" class="admin-select">
                        <option value="">— Non assigné —</option>
                        @foreach($technicians as $t)<option value="{{ $t->id }}" {{ $ticket->assigned_to==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
                    </select>
                </div>
                <div><label class="admin-label">Date intervention</label><input type="date" name="scheduled_date" value="{{ old('scheduled_date', $ticket->scheduled_date?->format('Y-m-d')) }}" class="admin-input"></div>
                <div><label class="admin-label">Notes</label><textarea name="notes" rows="2" class="admin-textarea">{{ old('notes', $ticket->notes) }}</textarea></div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gold-500 text-navy-900 font-bold py-3 rounded-lg hover:bg-gold-600 transition">Enregistrer</button>
                <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
