@extends('layouts.admin')

@section('title', 'Modifier la Ressource Marketing - Admin')

@section('content')
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Modifier la Ressource</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Modifiez le titre, la catégorie ou remplacez le fichier de la ressource.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.marketing-assets.update', $marketingAsset) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">

                            <div class="col-span-6">
                                <label for="title" class="block text-sm font-medium text-gray-700">Titre de la ressource *</label>
                                <input type="text" name="title" id="title"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    value="{{ old('title', $marketingAsset->title) }}" required>
                                @error('title')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="category" class="block text-sm font-medium text-gray-700">Catégorie *</label>
                                <select name="category" id="category"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                                    <option value="image" {{ old('category', $marketingAsset->category) == 'image' ? 'selected' : '' }}>🖼️ Image / Photo Produit</option>
                                    <option value="pdf" {{ old('category', $marketingAsset->category) == 'pdf' ? 'selected' : '' }}>📄 Document PDF / Flyer</option>
                                    <option value="document" {{ old('category', $marketingAsset->category) == 'document' ? 'selected' : '' }}>📝 Texte de vente / Argumentaire</option>
                                    <option value="template" {{ old('category', $marketingAsset->category) == 'template' ? 'selected' : '' }}>🎨 Gabarit Canva / Modèle</option>
                                    <option value="other" {{ old('category', $marketingAsset->category) == 'other' ? 'selected' : '' }}>📦 Autre</option>
                                </select>
                                @error('category')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Fichier actuel</label>
                                <a href="{{ asset('storage/' . $marketingAsset->file_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline block mt-1">
                                    Voir le fichier : {{ basename($marketingAsset->file_path) }}
                                </a>
                            </div>

                            <div class="col-span-6">
                                <label for="file" class="block text-sm font-medium text-gray-700">Remplacer le fichier (optionnel)</label>
                                <input type="file" name="file" id="file"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-navy-50 file:text-navy-700 hover:file:bg-navy-100">
                                @error('file')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description / Instructions</label>
                                <textarea id="description" name="description" rows="4"
                                    class="mt-1 shadow-sm focus:ring-gold-500 focus:border-gold-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $marketingAsset->description) }}</textarea>
                            </div>

                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.marketing-assets.index') }}"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 mr-2">
                            Annuler
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-navy-600 hover:bg-navy-700">
                            Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
