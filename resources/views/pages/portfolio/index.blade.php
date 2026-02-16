@extends('layouts.app')

@section('title', 'Portfolio - ' . config('app.name'))

@section('content')
<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-base font-semibold tracking-wide text-indigo-600 uppercase">Nos Réalisations</h2>
            <p class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">Portfolio</p>
            <p class="max-w-xl mt-5 mx-auto text-xl text-gray-500">Découvrez nos projets récents et notre expertise.</p>
        </div>

        <div class="mt-12 grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none">
            @forelse($projects as $project)
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                    <div class="flex-shrink-0">
                        @if($project->image)
                            <img class="h-48 w-full object-cover" src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}">
                        @else
                            <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <span>Image du projet</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-indigo-600">
                                {{ $project->client ?? 'Client confidentiel' }}
                            </p>
                            <a href="{{ route('portfolio.show', $project->slug) }}" class="block mt-2">
                                <p class="text-xl font-semibold text-gray-900">
                                    {{ $project->title }}
                                </p>
                                <p class="mt-3 text-base text-gray-500">
                                    {{ Str::limit($project->description, 100) }}
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 py-12">
                     <p>Aucun projet à afficher pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
