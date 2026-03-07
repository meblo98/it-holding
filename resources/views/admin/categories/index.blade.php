@extends('layouts.admin')

@section('title', 'Catégories - Admin')

@section('content')
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Catégories</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Gérez les catégories de produits.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <a href="{{ route('admin.categories.create') }}"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mb-4">
                Ajouter une catégorie
            </a>

            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse ($categories as $category)
                        <li>
                            <div class="px-4 py-4 flex items-center justify-between sm:px-6">
                                <div>
                                    <p class="text-sm font-medium text-indigo-600">{{ $category->name }}</p>
                                    @if ($category->description)
                                        <p class="text-sm text-gray-600">{{ Str::limit($category->description, 80) }}</p>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Éditer</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                        style="display: inline;"
                                        onsubmit="return confirm('Êtes-vous sûr ? Les produits associés gardent leur catégorie.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-center text-gray-600">
                            Aucune catégorie trouvée.
                        </li>
                    @endforelse
                </ul>
            </div>

            @if ($categories->count())
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
