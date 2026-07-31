@extends('layouts.app')

@section('title', 'Ouvrir un Ticket Support - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('home') }}" class="hover:text-navy-900 flex items-center gap-1">
                    <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Accueil
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('dashboard') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('dashboard.tickets') }}" class="hover:text-navy-900 transition-colors uppercase tracking-wider">Support / SAV</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-navy-900 font-bold uppercase tracking-wider italic">Nouveau Ticket</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Navigation -->
            @include('layouts.client_sidebar')

            <!-- Main Content -->
            <main class="flex-1">
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-lg flex items-center gap-3 text-red-600 text-xs font-bold uppercase tracking-widest italic">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center gap-3 bg-gray-50/30">
                        <a href="{{ route('dashboard.tickets') }}" class="text-gray-400 hover:text-navy-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        </a>
                        <div>
                            <h3 class="text-sm font-black text-navy-900 uppercase tracking-tighter italic">Ouvrir un nouveau ticket</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">Expliquez votre problème avec précision pour une prise en charge rapide</p>
                        </div>
                    </div>

                    <form action="{{ route('dashboard.tickets.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-[10px] font-black text-navy-900 uppercase tracking-widest mb-2 italic">Nature de la demande *</label>
                                <select name="type" id="type" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-navy-900 focus:outline-none focus:border-gold-500 transition-all" required>
                                    <option value="repair" {{ old('type') == 'repair' ? 'selected' : '' }}>🔧 Réparation de matériel</option>
                                    <option value="installation" {{ old('type') == 'installation' ? 'selected' : '' }}>🔌 Installation / Configuration</option>
                                    <option value="maintenance" {{ old('type') == 'maintenance' ? 'selected' : '' }}>🧹 Maintenance préventive</option>
                                    <option value="warranty_claim" {{ old('type') == 'warranty_claim' ? 'selected' : '' }}>🛡️ Réclamation sous garantie</option>
                                    <option value="advice" {{ old('type') == 'advice' ? 'selected' : '' }}>💬 Conseil ou demande d'information</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-[10px] font-bold mt-1 uppercase italic">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priorité -->
                            <div>
                                <label for="priority" class="block text-[10px] font-black text-navy-900 uppercase tracking-widest mb-2 italic">Niveau d'urgence *</label>
                                <select name="priority" id="priority" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-navy-900 focus:outline-none focus:border-gold-500 transition-all" required>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🟢 Faible (Question, conseil...)</option>
                                    <option value="normal" {{ old('priority') == 'normal' || !old('priority') ? 'selected' : '' }}>🟡 Normal (Fonctionnement dégradé)</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🟠 Élevé (Matériel inutilisable)</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>🔴 Urgent (Bloquant / Panne totale)</option>
                                </select>
                                @error('priority')
                                    <p class="text-red-500 text-[10px] font-bold mt-1 uppercase italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Sélection de la garantie (si existante) -->
                        <div>
                            <label for="warranty_id" class="block text-[10px] font-black text-navy-900 uppercase tracking-widest mb-2 italic">Associer un produit sous garantie (Optionnel)</label>
                            <select name="warranty_id" id="warranty_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-navy-900 focus:outline-none focus:border-gold-500 transition-all">
                                <option value="" data-product-name="" data-serial-number="">-- Sélectionner un produit sous garantie --</option>
                                @foreach($warranties as $warranty)
                                    <option value="{{ $warranty->id }}" 
                                            {{ old('warranty_id') == $warranty->id ? 'selected' : '' }}
                                            data-product-name="{{ $warranty->product_name ?? ($warranty->product ? $warranty->product->name : '') }}"
                                            data-serial-number="{{ $warranty->serial_number }}">
                                        🛡️ {{ $warranty->number }} - {{ $warranty->product_name ?? ($warranty->product ? $warranty->product->name : 'Produit') }} (S/N: {{ $warranty->serial_number ?: 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">Sélectionner une garantie remplira automatiquement le nom et le numéro de série du produit.</p>
                            @error('warranty_id')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom du produit -->
                            <div>
                                <label for="product_name" class="block text-[10px] font-black text-navy-900 uppercase tracking-widest mb-2 italic">Nom du produit / Modèle</label>
                                <input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}" placeholder="Ex: HP ProBook 450 G8" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-navy-900 focus:outline-none focus:border-gold-500 transition-all">
                                @error('product_name')
                                    <p class="text-red-500 text-[10px] font-bold mt-1 uppercase italic">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Numéro de série -->
                            <div>
                                <label for="serial_number" class="block text-[10px] font-black text-navy-900 uppercase tracking-widest mb-2 italic">Numéro de série (S/N)</label>
                                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" placeholder="Ex: CND12345XYZ" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-navy-900 focus:outline-none focus:border-gold-500 transition-all">
                                @error('serial_number')
                                    <p class="text-red-500 text-[10px] font-bold mt-1 uppercase italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Titre de la demande -->
                        <div>
                            <label for="title" class="block text-[10px] font-black text-navy-900 uppercase tracking-widest mb-2 italic">Objet du ticket *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Ex: Problème d'allumage après mise à jour" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-navy-900 focus:outline-none focus:border-gold-500 transition-all" required>
                            @error('title')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-[10px] font-black text-navy-900 uppercase tracking-widest mb-2 italic">Description détaillée du problème *</label>
                            <textarea name="description" id="description" rows="6" placeholder="Veuillez décrire le problème rencontré avec le plus de précisions possible (circonstances, messages d'erreur, étapes pour reproduire le bug...)" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl text-xs font-medium text-navy-900 focus:outline-none focus:border-gold-500 transition-all" required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pièces Jointes -->
                        <div>
                            <label class="block text-[10px] font-black text-navy-900 uppercase tracking-widest mb-2 italic">Pièces jointes (Photos, Captures d'écran, PDF... Max 5Mo par fichier)</label>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-gold-500 transition-colors bg-gray-50/50">
                                <input type="file" name="attachments[]" id="attachments" class="hidden" multiple accept="image/*,application/pdf">
                                <label for="attachments" class="cursor-pointer flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-xs text-navy-900 font-black uppercase tracking-tight">Sélectionner des fichiers</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase" id="file-indicator">Aucun fichier sélectionné</span>
                                </label>
                            </div>
                            @error('attachments')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase italic">{{ $message }}</p>
                            @enderror
                            @error('attachments.*')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4 pt-4">
                            <a href="{{ route('dashboard.tickets') }}" class="inline-flex items-center justify-center border border-gray-200 hover:border-navy-900 text-gray-500 hover:text-navy-900 px-6 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                Annuler
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center bg-navy-900 hover:bg-gold-500 text-white hover:text-navy-900 px-8 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                                Soumettre le ticket
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const warrantySelect = document.getElementById('warranty_id');
        const productNameInput = document.getElementById('product_name');
        const serialNumberInput = document.getElementById('serial_number');
        const attachmentsInput = document.getElementById('attachments');
        const fileIndicator = document.getElementById('file-indicator');

        // Handle auto-fill when selecting warranty
        warrantySelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const productName = selectedOption.getAttribute('data-product-name') || '';
            const serialNumber = selectedOption.getAttribute('data-serial-number') || '';

            productNameInput.value = productName;
            serialNumberInput.value = serialNumber;

            if (this.value !== '') {
                productNameInput.setAttribute('readonly', 'true');
                serialNumberInput.setAttribute('readonly', 'true');
                productNameInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                serialNumberInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                productNameInput.removeAttribute('readonly');
                serialNumberInput.removeAttribute('readonly');
                productNameInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                serialNumberInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        });

        // Trigger change on load if value is pre-selected
        if (warrantySelect.value !== '') {
            warrantySelect.dispatchEvent(new Event('change'));
        }

        // Handle attachment file selection indicator
        attachmentsInput.addEventListener('change', function () {
            const fileCount = this.files.length;
            if (fileCount > 0) {
                fileIndicator.textContent = fileCount === 1 ? '1 fichier sélectionné' : fileCount + ' fichiers sélectionnés';
                fileIndicator.classList.remove('text-gray-400');
                fileIndicator.classList.add('text-gold-600', 'font-black');
            } else {
                fileIndicator.textContent = 'Aucun fichier sélectionné';
                fileIndicator.classList.remove('text-gold-600', 'font-black');
                fileIndicator.classList.add('text-gray-400');
            }
        });
    });
</script>
@endsection
