@extends('layouts.admin')

@section('title', 'Gestion des Projets Portfolio - Admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Portfolio</h1>
                <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Nouveau Projet
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($projects as $project)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($project->image)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $project->image) }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold">
                                                    {{ substr($project->title, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-indigo-600 truncate">
                                                {{ $project->title }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $project->client ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.projects.edit', $project->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Editer</a>
                                        <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium bg-transparent border-0 cursor-pointer">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                            Aucun projet dans le portfolio.
                        </li>
                    @endforelse
                </ul>
            </div>
            
            <div class="mt-4">
                {{ $projects->links() }}
@endsection
