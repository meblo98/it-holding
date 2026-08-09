@extends('layouts.admin')

@section('title', 'Modifier Service - Admin')

@section('content')
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Modifier le Service</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Mettez à jour les informations du service.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            
                            <!-- Titre -->
                            <div class="col-span-6 sm:col-span-4">
                                <label for="title" class="block text-sm font-medium text-gray-700">Titre du service <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="title" 
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 px-3 @error('title') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                    value="{{ old('title', $service->title) }}" required>
                                @error('title')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prix -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="price" class="block text-sm font-medium text-gray-700">Prix du service (FCFA)</label>
                                <input type="number" name="price" id="price" min="0" step="0.01" 
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 px-3 @error('price') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                    placeholder="Ex: 50000" value="{{ old('price', $service->price) }}">
                                @error('price')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Icône -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="icon" class="block text-sm font-medium text-gray-700">Icône du service</label>
                                <select name="icon" id="icon" 
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2.5 px-3 @error('icon') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                    <option value="code" {{ old('icon', $service->icon) == 'code' ? 'selected' : '' }}>Code / Développement (code)</option>
                                    <option value="cloud" {{ old('icon', $service->icon) == 'cloud' ? 'selected' : '' }}>Cloud (cloud)</option>
                                    <option value="shield-check" {{ old('icon', $service->icon) == 'shield-check' ? 'selected' : '' }}>Sécurité (shield-check)</option>
                                    <option value="database" {{ old('icon', $service->icon) == 'database' ? 'selected' : '' }}>Base de données (database)</option>
                                    <option value="refresh" {{ old('icon', $service->icon) == 'refresh' ? 'selected' : '' }}>Maintenance (refresh)</option>
                                    <option value="support" {{ old('icon', $service->icon) == 'support' ? 'selected' : '' }}>Support client (support)</option>
                                </select>
                                @error('icon')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description courte -->
                            <div class="col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description courte <span class="text-red-500">*</span></label>
                                <input type="text" name="description" id="description" 
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 px-3 @error('description') border-red-500 @enderror" 
                                    value="{{ old('description', $service->description) }}" required>
                                @error('description')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contenu détaillé -->
                            <div class="col-span-6">
                                <label for="content" class="block text-sm font-medium text-gray-700">Contenu détaillé <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <textarea id="content" name="content" rows="6" 
                                        class="shadow-sm focus:ring-gold-500 focus:border-gold-500 block w-full sm:text-sm border border-gray-300 rounded-md p-3 @error('content') border-red-500 @enderror" 
                                        required>{{ old('content', $service->content) }}</textarea>
                                </div>
                                @error('content')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Image du service -->
                            <div class="col-span-6" x-data="{ 
                                imagePreview: null,
                                fileName: '',
                                fileSize: '',
                                errorMessage: '',
                                initPreview(event) {
                                    const file = event.target.files[0];
                                    if (!file) return;
                                    
                                    if (file.size > 2 * 1024 * 1024) {
                                        this.errorMessage = 'Le fichier dépasse la taille maximale de 2Mo.';
                                        this.imagePreview = null;
                                        event.target.value = '';
                                        return;
                                    }
                                    if (!file.type.match('image.*')) {
                                        this.errorMessage = 'Le fichier doit être une image (PNG, JPG, GIF).';
                                        this.imagePreview = null;
                                        event.target.value = '';
                                        return;
                                    }
                                    
                                    this.errorMessage = '';
                                    this.fileName = file.name;
                                    this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' Mo';
                                    
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        this.imagePreview = e.target.result;
                                    };
                                    reader.readAsDataURL(file);
                                },
                                clearPreview() {
                                    this.imagePreview = null;
                                    this.fileName = '';
                                    this.fileSize = '';
                                    this.errorMessage = '';
                                    document.getElementById('image').value = '';
                                }
                            }">
                                <label class="block text-sm font-medium text-gray-700">Image du service</label>
                                
                                <!-- Current Image display (hidden when new file is pre-visualized) -->
                                @if($service->image)
                                    <div class="mt-2 mb-2" x-show="!imagePreview">
                                        <img src="{{ asset('storage/' . $service->image) }}" alt="Image actuelle" class="h-32 w-auto rounded-md shadow-sm object-cover">
                                        <p class="text-xs text-gray-500 mt-1">Image actuelle</p>
                                    </div>
                                @endif

                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gold-500 transition-colors duration-200"
                                     :class="errorMessage ? 'border-red-300 bg-red-50/10' : 'border-gray-300'">
                                    
                                    <div class="space-y-1 text-center" x-show="!imagePreview">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-navy-600 hover:text-gold-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-gold-500 px-2">
                                                <span>{{ $service->image ? 'Changer l\'image' : 'Télécharger un fichier' }}</span>
                                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" @change="initPreview($event)">
                                            </label>
                                            <p class="pl-1">ou glisser-déposer</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 2MB</p>
                                    </div>

                                    <!-- Image Preview Mode -->
                                    <div class="w-full flex flex-col items-center space-y-2" x-show="imagePreview" x-cloak>
                                        <div class="relative">
                                            <img :src="imagePreview" class="h-32 w-auto rounded-md shadow-sm object-cover">
                                            <button type="button" @click="clearPreview()" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 shadow-md transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-600 font-semibold" x-text="fileName"></p>
                                        <p class="text-[10px] text-gray-400" x-text="fileSize"></p>
                                    </div>
                                </div>
                                
                                <p class="text-xs text-red-600 mt-2" x-show="errorMessage" x-text="errorMessage" x-cloak></p>
                                @error('image')
                                    <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Statut Actif -->
                            <div class="col-span-6 flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="active" name="active" type="checkbox" value="1" class="focus:ring-gold-500 h-4 w-4 text-navy-600 border-gray-300 rounded" {{ $service->active ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="active" class="font-medium text-gray-700">Actif</label>
                                    <p class="text-gray-500">Rendre ce service visible sur le site public.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.services.index') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500 mr-3 border-gray-300">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500 transition-colors duration-200">
                            Mettre à jour
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
