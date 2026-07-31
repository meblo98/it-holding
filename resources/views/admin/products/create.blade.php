@extends('layouts.admin')

@section('title', 'Nouveau Produit - Admin')

@section('content')
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Ajouter un Produit</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Ajoutez un nouveau produit à votre boutique.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom du produit</label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name') }}"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    required>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700">Prix d'achat Fournisseur (FCFA)</label>
                                <input type="number" name="purchase_price" id="purchase_price" min="0" step="0.01"
                                    value="{{ old('purchase_price') }}"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('purchase_price') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    required>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="price" class="block text-sm font-medium text-gray-700">Prix de vente (FCFA)</label>
                                <input type="number" name="price" id="price" min="0" step="0.01"
                                    value="{{ old('price') }}"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('price') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    required>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="promo_price" class="block text-sm font-medium text-gray-700">Prix promo (FCFA)</label>
                                <input type="number" name="promo_price" id="promo_price" min="0" step="0.01"
                                    value="{{ old('promo_price') }}"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('promo_price') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="number" name="stock" id="stock" min="0"
                                    value="{{ old('stock') }}"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('stock') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    required>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="available_at" class="block text-sm font-medium text-gray-700">Date de disponibilité</label>
                                <input type="date" name="available_at" id="available_at"
                                    value="{{ old('available_at') }}"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('available_at') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                <p class="text-xs text-gray-500 mt-1">À définir si le stock est épuisé.</p>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="warranty_duration_months" class="block text-sm font-medium text-gray-700">Garantie (Mois)</label>
                                <input type="number" name="warranty_duration_months" id="warranty_duration_months" min="0" max="120"
                                    value="{{ old('warranty_duration_months', 12) }}"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('warranty_duration_months') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    required>
                                <p class="text-xs text-gray-500 mt-1">Durée de garantie standard (ex: 12 pour 1 an).</p>
                            </div>

                            <div class="col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <div class="mt-1">
                                    <textarea id="description" name="description" rows="5"
                                        class="shadow-sm focus:ring-gold-500 focus:border-gold-500 block w-full sm:text-sm border border-gray-300 rounded-md @error('description') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                        required>{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="col-span-6 flex items-center">
                                <div class="flex items-center h-5">
                                    <input id="blackfriday" name="blackfriday" type="checkbox" value="1"
                                        {{ old('blackfriday') ? 'checked' : '' }}
                                        class="focus:ring-gold-500 h-4 w-4 text-navy-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="blackfriday" class="font-medium text-gray-700">Black Friday</label>
                                    <p class="text-gray-500">Cocher si le produit fait partie des offres Black Friday.</p>
                                </div>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                                <select name="category_id" id="category_id"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('category_id') border-red-500 @enderror">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="brand_id" class="block text-sm font-medium text-gray-700">Marque</label>
                                <select name="brand_id" id="brand_id"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('brand_id') border-red-500 @enderror">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="condition" class="block text-sm font-medium text-gray-700">État</label>
                                <select name="condition" id="condition"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('condition') border-red-500 @enderror">
                                    <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>Neuf</option>
                                    <option value="reconditioned" {{ old('condition') == 'reconditioned' ? 'selected' : '' }}>Reconditionné</option>
                                    <option value="second_hand" {{ old('condition') == 'second_hand' ? 'selected' : '' }}>Seconde main</option>
                                </select>
                            </div>

                            <!-- Section Achat en Gros -->
                            <div class="col-span-6 border-t border-gray-200 pt-5">
                                <h4 class="text-md font-semibold text-navy-900 uppercase tracking-wider mb-1">Achat en Gros (Volume Discount)</h4>
                                <p class="text-xs text-gray-500">Configurez les avantages tarifaires d'achat en volume pour ce produit.</p>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="wholesale_qty" class="block text-sm font-medium text-gray-700">Qté minimale de gros</label>
                                <input type="number" name="wholesale_qty" id="wholesale_qty" min="2"
                                    value="{{ old('wholesale_qty', 5) }}"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('wholesale_qty') border-red-500 @enderror"
                                    required>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="wholesale_discount_rate" class="block text-sm font-medium text-gray-700">Taux de réduction (%)</label>
                                <input type="number" name="wholesale_discount_rate" id="wholesale_discount_rate" min="0" max="100" step="0.01"
                                    value="{{ old('wholesale_discount_rate', 10.00) }}"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('wholesale_discount_rate') border-red-500 @enderror"
                                    required>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label for="wholesale_discount_limit" class="block text-sm font-medium text-gray-700">Plafond de réduction (FCFA/unité)</label>
                                <input type="number" name="wholesale_discount_limit" id="wholesale_discount_limit" min="0" step="0.01"
                                    value="{{ old('wholesale_discount_limit') }}"
                                    placeholder="Aucune limite"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('wholesale_discount_limit') border-red-500 @enderror">
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Images du produit</label>
                                <div
                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="images"
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-navy-600 hover:text-gold-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-gold-500">
                                                <span>Télécharger des fichiers</span>
                                                <input id="images" name="images[]" type="file" class="sr-only"
                                                    accept="image/*" multiple onchange="validateImages(event)">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF — maximum 10 fichiers, 2MB chacun
                                        </p>
                                        <p id="image-error" class="text-xs text-red-600 mt-2 hidden"></p>
                                    </div>
                                </div>
                                <div id="preview-container"
                                    class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4"></div>
                            </div>

                            <!-- Fiche Technique PDF -->
                            <div class="col-span-6">
                                <label for="fiche_technique" class="block text-sm font-medium text-gray-700">Fiche Technique (PDF / Image)</label>
                                <input type="file" name="fiche_technique" id="fiche_technique" accept=".pdf,image/*"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="text-xs text-gray-500 mt-1">Fichier PDF ou image de la fiche technique (Max: 5MB).</p>
                            </div>

                            <!-- Caractéristiques Techniques -->
                            <div class="col-span-6 border-t border-gray-200 pt-5" x-data="{ 
                                rows: [],
                                init() {
                                    this.addRow();
                                },
                                addRow() {
                                    this.rows.push({ key: '', value: '' });
                                },
                                removeRow(index) {
                                    this.rows.splice(index, 1);
                                    if (this.rows.length === 0) {
                                        this.addRow();
                                    }
                                }
                            }">
                                <h4 class="text-md font-semibold text-navy-900 uppercase tracking-wider mb-1">Caractéristiques Techniques</h4>
                                <p class="text-xs text-gray-500 mb-4">Ajoutez des détails techniques (ex: Couleur, Processeur, RAM, Dimensions...).</p>
                                
                                <div class="space-y-3">
                                    <template x-for="(row, index) in rows" :key="index">
                                        <div class="flex gap-4 items-center">
                                            <div class="flex-grow grid grid-cols-2 gap-4">
                                                <div>
                                                    <input type="text" :name="'specs[' + index + '][key]'" x-model="row.key" placeholder="Nom (ex: Couleur)"
                                                        class="focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                </div>
                                                <div>
                                                    <input type="text" :name="'specs[' + index + '][value]'" x-model="row.value" placeholder="Valeur (ex: Noir)"
                                                        class="focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                </div>
                                            </div>
                                            <button type="button" @click="removeRow(index)" class="inline-flex items-center p-1.5 border border-transparent rounded-md text-red-600 hover:bg-red-50 focus:outline-none transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                
                                <button type="button" @click="addRow()" class="mt-3 inline-flex items-center px-3 py-1.5 border border-dashed border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                                    + Ajouter une caractéristique
                                </button>
                            </div>

                            <div class="col-span-6 flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="active" name="active" type="checkbox" value="1"
                                        class="focus:ring-gold-500 h-4 w-4 text-navy-600 border-gray-300 rounded"
                                        checked>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="active" class="font-medium text-gray-700">Actif</label>
                                    <p class="text-gray-500">Rendre ce produit visible sur la boutique.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.products.index') }}"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 mr-2">
                            Annuler
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500">
                            Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validateImages(event) {
            const files = event.target.files;
            const maxSize = 2 * 1024 * 1024; // 2MB en bytes
            const maxFiles = 10;
            const errorElement = document.getElementById('image-error');
            const previewContainer = document.getElementById('preview-container');
            let errorMessage = '';

            if (files.length > maxFiles) {
                errorMessage = `Vous ne pouvez pas télécharger plus de ${maxFiles} fichiers.`;
            } else {
                for (let file of files) {
                    if (file.size > maxSize) {
                        errorMessage = `Le fichier "${file.name}" dépasse 2MB.`;
                        break;
                    }
                    if (!file.type.match('image.*')) {
                        errorMessage = `Le fichier "${file.name}" n'est pas une image valide.`;
                        break;
                    }
                }
            }

            if (errorMessage) {
                errorElement.textContent = errorMessage;
                errorElement.classList.remove('hidden');
                event.target.value = '';
                previewContainer.innerHTML = '';
            } else {
                errorElement.classList.add('hidden');
                errorElement.textContent = '';
                displayImagePreviews(files, previewContainer);
            }
        }

        function displayImagePreviews(files, container) {
            container.innerHTML = '';
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}" class="w-full h-32 object-cover rounded-lg shadow-sm">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <p class="text-white text-xs text-center px-2 truncate">${file.name}</p>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 truncate">${file.name}</p>
                    `;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
