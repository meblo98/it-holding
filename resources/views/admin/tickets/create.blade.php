@extends('layouts.admin')
@section('title', 'Nouveau Ticket SAV')
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.tickets.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <h1 class="text-2xl font-bold text-gray-900">Nouveau Ticket SAV</h1>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded"><ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form action="{{ route('admin.tickets.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- TICKET INFO --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Informations du ticket</h2>
                <div>
                    <label class="admin-label">Titre / Objet <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Ex: Écran ne s'allume plus, PC lent..." class="admin-input">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="admin-select">
                            <option value="repair" {{ old('type','repair')==='repair'?'selected':'' }}>🔧 Réparation</option>
                            <option value="installation" {{ old('type')==='installation'?'selected':'' }}>🖥️ Installation</option>
                            <option value="maintenance" {{ old('type')==='maintenance'?'selected':'' }}>⚙️ Maintenance</option>
                            <option value="advice" {{ old('type')==='advice'?'selected':'' }}>💡 Conseil</option>
                            <option value="warranty_claim" {{ old('type')==='warranty_claim'?'selected':'' }}>🛡️ Réclamation Garantie</option>
                        </select>
                    </div>
                    <div>
                        <label class="admin-label">Priorité <span class="text-red-500">*</span></label>
                        <select name="priority" required class="admin-select">
                            <option value="low" {{ old('priority')==='low'?'selected':'' }}>🟢 Faible</option>
                            <option value="normal" {{ old('priority','normal')==='normal'?'selected':'' }}>🟡 Normal</option>
                            <option value="high" {{ old('priority')==='high'?'selected':'' }}>🟠 Élevé</option>
                            <option value="urgent" {{ old('priority')==='urgent'?'selected':'' }}>🔴 Urgent</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="admin-label">Description du problème <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required placeholder="Décrivez le problème en détail..." class="admin-textarea">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="admin-label">Photos / Pièces jointes</label>
                    <input type="file" name="attachments[]" multiple accept="image/*,.pdf" class="admin-file">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP, PDF — Max 5Mo par fichier</p>
                </div>
            </div>

            {{-- CLIENT --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Client</h2>
                <div>
                    <label class="admin-label">Client CRM</label>
                    <select name="client_id" class="admin-select">
                        <option value="">— Sélectionner —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" {{ old('client_id')==$c->id?'selected':'' }}>{{ $c->display_name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Nom *</label><input type="text" name="client_name" value="{{ old('client_name') }}" required class="admin-input"></div>
                    <div><label class="admin-label">Téléphone</label><input type="text" name="client_phone" value="{{ old('client_phone') }}" class="admin-input"></div>
                    <div class="col-span-2"><label class="admin-label">Email</label><input type="email" name="client_email" value="{{ old('client_email') }}" class="admin-input"></div>
                </div>
            </div>

            {{-- PRODUCT --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Produit concerné</h2>
                <div>
                    <label class="admin-label">Garantie liée</label>
                    <select name="warranty_id" class="admin-select">
                        <option value="">— Aucune garantie —</option>
                        @foreach($warranties as $w)<option value="{{ $w->id }}" {{ old('warranty_id')==$w->id?'selected':'' }}>{{ $w->number }} — {{ $w->product_name }} ({{ $w->client_name }})</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="admin-label">Nom du produit</label><input type="text" name="product_name" value="{{ old('product_name') }}" class="admin-input"></div>
                    <div><label class="admin-label">N° de série</label><input type="text" name="serial_number" value="{{ old('serial_number') }}" class="admin-input font-mono"></div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="covered_by_warranty" value="1" id="covered_by_warranty" class="admin-check" {{ old('covered_by_warranty') ? 'checked' : '' }}>
                    <label for="covered_by_warranty" class="text-sm text-gray-700 font-medium">Ce ticket est couvert par la garantie</label>
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 space-y-4">
                <h2 class="admin-section-title">Planification</h2>
                <div>
                    <label class="admin-label">Statut</label>
                    <select name="status" required class="admin-select">
                        <option value="open" selected>Ouvert</option>
                        <option value="diagnosed">Diagnostiqué</option>
                        <option value="in_progress">En cours</option>
                        <option value="waiting_parts">Attente pièces</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label">Technicien assigné</label>
                    <select name="assigned_to" class="admin-select">
                        <option value="">— Non assigné —</option>
                        @foreach($technicians as $t)<option value="{{ $t->id }}" {{ old('assigned_to')==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-label">Date d'intervention prévue</label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">Notes internes</label>
                    <textarea name="notes" rows="3" class="admin-textarea">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <button type="submit" class="w-full bg-gradient-to-r from-navy-600 to-navy-700 text-white font-bold py-3 rounded-lg hover:from-navy-700 hover:to-navy-800 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Ouvrir le ticket
                </button>
                <a href="{{ route('admin.tickets.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
