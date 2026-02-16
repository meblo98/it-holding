@extends('layouts.app')

@section('title', 'Nos Services - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-base font-semibold tracking-wide text-indigo-600 uppercase">Ce que nous faisons</h2>
            <p class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">Nos Services</p>
            <p class="max-w-xl mt-5 mx-auto text-xl text-gray-500">Des solutions complètes adaptées à vos besoins professionnels.</p>
        </div>

        <div class="mt-12 grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($services as $service)
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden bg-white hover:shadow-xl transition-shadow duration-300">
                    <div class="flex-shrink-0">
                        @if($service->image)
                            <img class="h-48 w-full object-cover" src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}">
                        @else
                            <div class="h-48 w-full bg-indigo-100 flex items-center justify-center text-indigo-500">
                                <svg class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <a href="{{ route('services.show', $service->slug) }}" class="block mt-2">
                                <p class="text-xl font-semibold text-gray-900">{{ $service->title }}</p>
                                <p class="mt-3 text-base text-gray-500">
                                    {{ Str::limit($service->description, 100) }}
                                </p>
                            </a>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('services.show', $service->slug) }}" class="text-indigo-600 hover:text-indigo-500 font-medium">
                                En savoir plus &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 py-12">
                    <p>Aucun service disponible pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
