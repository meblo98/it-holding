@extends('layouts.app')

@section('title', 'Blog & Actualités - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-base font-semibold tracking-wide text-indigo-600 uppercase">Actualités</h2>
            <p class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">Le Blog</p>
            <p class="max-w-xl mt-5 mx-auto text-xl text-gray-500">Restez informé des dernières innovations et de la vie de notre entreprise.</p>
        </div>

        <div class="mt-12 grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none">
            @forelse($posts as $post)
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                    <div class="flex-shrink-0">
                        @if($post->image)
                            <img class="h-48 w-full object-cover" src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                        @else
                            <div class="h-48 w-full bg-indigo-100 flex items-center justify-center text-indigo-500">
                                <span>Image article</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-indigo-600">
                                <a href="#" class="hover:underline">
                                    {{ $post->category ?? 'Général' }}
                                </a>
                            </p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="block mt-2">
                                <p class="text-xl font-semibold text-gray-900">
                                    {{ $post->title }}
                                </p>
                                <p class="mt-3 text-base text-gray-500">
                                    {{ Str::limit(strip_tags($post->content), 100) }}
                                </p>
                            </a>
                        </div>
                        <div class="mt-6 flex items-center">
                            <div class="flex-shrink-0">
                                <span class="sr-only">Auteur</span>
                            </div>
                            <div class="ml-0">
                                <p class="text-sm font-medium text-gray-900">
                                    IT Holding Team
                                </p>
                                <div class="flex space-x-1 text-sm text-gray-500">
                                    <time datetime="{{ $post->published_at }}">
                                        {{ $post->published_at ? $post->published_at->format('d M Y') : '' }}
                                    </time>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 py-12">
                     <p>Aucun article publié pour le moment.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
