@extends('layouts.admin')

@section('title', 'Nouveau Fournisseur')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Ajouter un Fournisseur</h1>
        <p class="text-sm text-gray-500 mt-1">Créez une fiche fournisseur pour suivre vos contrats et livraisons.</p>
    </div>
    <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none transition">
        Retour à la liste
    </a>
</div>

<div class="max-w-3xl bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100 p-6">
    <form action="{{ route('admin.suppliers.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700">Nom de l'Entreprise / Raison Sociale *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Ex: SENEGAL TECH DISTRI" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Person -->
                <div>
                    <label for="contact_person" class="block text-sm font-semibold text-gray-700">Nom du Contact Principal</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" placeholder="Ex: M. Jean Diop" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    @error('contact_person')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700">Téléphone de contact</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Ex: +221 77 XXX XX XX" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700">Adresse Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Ex: contact@fournisseur.sn" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-semibold text-gray-700">Adresse Physique / Siège Social</label>
                    <textarea name="address" id="address" rows="3" placeholder="Ex: Rue 10, Zone Industrielle, Dakar, Sénégal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gold-500 focus:border-gold-500 sm:text-sm">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-navy-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-navy-700 transition">
                    Enregistrer le Fournisseur
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
