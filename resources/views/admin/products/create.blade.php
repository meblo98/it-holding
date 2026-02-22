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
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="price" class="block text-sm font-medium text-gray-700">Prix (FCFA)</label>
                                <input type="number" name="price" id="price" min="0" step="0.01"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="number" name="stock" id="stock" min="0"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>

                            <div class="col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <div class="mt-1">
                                    <textarea id="description" name="description" rows="5"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                        required></textarea>
                                </div>
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
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Télécharger des fichiers</span>
                                                <input id="images" name="images[]" type="file" class="sr-only"
                                                    accept="image/*" multiple onchange="validateImages(event)">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF — maximum 10 fichiers, 2MB chacun</p>
                                        <p id="image-error" class="text-xs text-red-600 mt-2 hidden"></p>
                                    </div>
                                </div>
                                <div id="preview-container"
                                    class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4"></div>
                            </div>

                            <div class="col-span-6 flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="active" name="active" type="checkbox" value="1"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
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
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
