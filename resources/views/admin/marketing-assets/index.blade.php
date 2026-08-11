@extends('layouts.admin')

@section('title', 'Ressources Marketing - Admin')

@section('content')
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Ressources Marketing</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Déposez et gérez des ressources marketing (images, flyers, fiches techniques PDF, argumentaires) destinées à être téléchargées par vos partenaires.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <a href="{{ route('admin.marketing-assets.create') }}"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500 mb-4">
                Ajouter une ressource
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
                    @forelse ($assets as $asset)
                        <li>
                            <div class="px-4 py-4 flex items-center justify-between sm:px-6">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-gray-100 text-gray-800 border">
                                            @if($asset->category === 'image') 🖼️ Image
                                            @elseif($asset->category === 'pdf') 📄 PDF
                                            @elseif($asset->category === 'document') 📝 Doc
                                            @elseif($asset->category === 'template') 🎨 Gabarit
                                            @else 📦 Autre
                                            @endif
                                        </span>
                                        <p class="text-sm font-semibold text-navy-600">{{ $asset->title }}</p>
                                    </div>
                                    @if ($asset->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($asset->description, 80) }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium border border-blue-200 px-2 py-1 rounded hover:bg-blue-50">
                                        👁️ Voir
                                    </a>
                                    <a href="{{ route('admin.marketing-assets.edit', $asset) }}"
                                        class="text-navy-600 hover:text-navy-900 text-sm font-medium">Éditer</a>
                                    <form method="POST" action="{{ route('admin.marketing-assets.destroy', $asset) }}"
                                        style="display: inline;"
                                        onsubmit="return confirm('Supprimer cette ressource ?');">
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
                            Aucune ressource marketing trouvée.
                        </li>
                    @endforelse
                </ul>
            </div>

            @if ($assets->count())
                <div class="mt-4">
                    {{ $assets->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
