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
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="title" class="block text-sm font-medium text-gray-700">Titre du service <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="title" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 px-3" value="{{ old('title', $service->title) }}" required>
                                    </div>

                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="description" class="block text-sm font-medium text-gray-700">Description courte <span class="text-red-500">*</span></label>
                                        <input type="text" name="description" id="description" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 px-3" value="{{ old('description', $service->description) }}" required>
                                    </div>
                                    
                                    <div class="col-span-6">
                                        <label for="content" class="block text-sm font-medium text-gray-700">Contenu détaillé <span class="text-red-500">*</span></label>
                                        <div class="mt-1">
                                            <textarea id="content" name="content" rows="5" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md p-3" required>{{ old('content', $service->content) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="icon" class="block text-sm font-medium text-gray-700">Classe d'icône (FontAwesome/Heroicons)</label>
                                        <input type="text" name="icon" id="icon" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 px-3" value="{{ old('icon', $service->icon) }}">
                                    </div>

                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">Image de couverture</label>
                                        @if($service->image)
                                            <div class="mt-2 mb-2">
                                                <img src="{{ asset('storage/' . $service->image) }}" alt="Image actuelle" class="h-32 w-auto rounded-md shadow-sm">
                                                <p class="text-xs text-gray-500 mt-1">Image actuelle</p>
                                            </div>
                                        @endif
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors duration-200">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600 justify-center">
                                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-2">
                                                        <span>Changer le fichier</span>
                                                        <input id="image" name="image" type="file" class="sr-only">
                                                    </label>
                                                    <p class="pl-1">ou glisser-déposer</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 2MB</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-6 flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="active" name="active" type="checkbox" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" {{ $service->active ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="active" class="font-medium text-gray-700">Actif</label>
                                            <p class="text-gray-500">Rendre ce service visible sur le site public.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                <a href="{{ route('admin.services.index') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3 border-gray-300">
                                    Annuler
                                </a>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    Mettre à jour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
@endsection
