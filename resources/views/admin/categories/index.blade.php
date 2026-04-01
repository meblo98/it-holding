@extends('layouts.admin')

@section('title', 'Catégories - Admin')

@section('content')
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Catégories</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Gérez les catégories et sous-catégories de produits.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <a href="{{ route('admin.categories.create') }}"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500 mb-4">
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

            @if (session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm">
                    <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse ($categories as $category)
                        {{-- Catégorie parente --}}
                        <li>
                            <div class="px-4 py-4 flex items-center justify-between sm:px-6">
                                <div>
                                    <p class="text-sm font-semibold text-navy-600">{{ $category->name }}</p>
                                    @if ($category->description)
                                        <p class="text-sm text-gray-500">{{ Str::limit($category->description, 80) }}</p>
                                    @endif
                                    @if ($category->children->count())
                                        <p class="text-xs text-gray-400 mt-1">{{ $category->children->count() }} sous-catégorie(s)</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}"
                                        class="text-green-600 hover:text-green-800 text-xs font-medium border border-green-200 px-2 py-1 rounded hover:bg-green-50">
                                        + Sous-catégorie
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="text-navy-600 hover:text-navy-900 text-sm font-medium">Éditer</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                        style="display: inline;"
                                        onsubmit="return confirm('Supprimer cette catégorie ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">Supprimer</button>
                                    </form>
                                </div>
                            </div>

                            {{-- Sous-catégories --}}
                            @if ($category->children->count())
                                <ul class="border-t border-gray-100 bg-gray-50">
                                    @foreach ($category->children as $child)
                                        <li>
                                            <div class="px-4 py-3 flex items-center justify-between sm:px-6 pl-10">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-400 text-xs">└</span>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-700">{{ $child->name }}</p>
                                                        @if ($child->description)
                                                            <p class="text-xs text-gray-500">{{ Str::limit($child->description, 60) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('admin.categories.edit', $child) }}"
                                                        class="text-navy-600 hover:text-navy-900 text-xs font-medium">Éditer</a>
                                                    <form method="POST" action="{{ route('admin.categories.destroy', $child) }}"
                                                        style="display: inline;"
                                                        onsubmit="return confirm('Supprimer cette sous-catégorie ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900 text-xs font-medium">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
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
