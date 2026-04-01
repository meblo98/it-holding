@extends('layouts.admin')

@section('title', 'Nouvelle Catégorie - Admin')

@section('content')
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    @if(request('parent_id') && $parents->find(request('parent_id')))
                        Ajouter une Sous-catégorie
                    @else
                        Ajouter une Catégorie
                    @endif
                </h3>
                <p class="mt-1 text-sm text-gray-600">
                    @if(request('parent_id') && $parents->find(request('parent_id')))
                        Sous-catégorie de <strong>{{ $parents->find(request('parent_id'))->name }}</strong>.
                    @else
                        Créez une catégorie principale ou une sous-catégorie.
                    @endif
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">

                            <div class="col-span-6">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom de la catégorie</label>
                                <input type="text" name="name" id="name"
                                    class="mt-1 focus:ring-gold-500 focus:border-gold-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="parent_id" class="block text-sm font-medium text-gray-700">Catégorie parente</label>
                                <select name="parent_id" id="parent_id"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gold-500 focus:border-gold-500 sm:text-sm">
                                    <option value="">— Aucune (catégorie principale) —</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id', request('parent_id')) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="mt-1 shadow-sm focus:ring-gold-500 focus:border-gold-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description') }}</textarea>
                            </div>

                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.categories.index') }}"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 mr-2">
                            Annuler
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-navy-600 hover:bg-navy-700">
                            Créer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
