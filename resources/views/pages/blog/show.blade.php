@extends('layouts.app')

@section('title', $post->title . ' - Blog')

@section('content')
<div class="relative py-16 bg-white overflow-hidden">
    <div class="hidden lg:block lg:absolute lg:inset-y-0 lg:h-full lg:w-full">
        <div class="relative h-full text-lg max-w-prose mx-auto" aria-hidden="true">
            <svg class="absolute top-12 left-full transform translate-x-32" width="404" height="384" fill="none" viewBox="0 0 404 384">
                <defs>
                    <pattern id="74b3fd99-0a6f-4271-bef2-e80eeafdf357" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="404" height="384" fill="url(#74b3fd99-0a6f-4271-bef2-e80eeafdf357)" />
            </svg>
        </div>
    </div>
    <div class="relative px-4 sm:px-6 lg:px-8">
        <div class="text-lg max-w-prose mx-auto">
            <h1>
                <span class="block text-base text-center text-indigo-600 font-semibold tracking-wide uppercase">{{ $post->category ?? 'Article' }}</span>
                <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">{{ $post->title }}</span>
            </h1>
            <p class="mt-8 text-xl text-gray-500 leading-8">
               Publié le {{ $post->published_at ? $post->published_at->format('d/m/Y') : 'Récemment' }}
            </p>
            @if($post->image)
                <img class="w-full rounded-lg shadow-lg mt-6 mb-6" src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
            @endif
        </div>
        <div class="mt-6 prose prose-indigo prose-lg text-gray-500 mx-auto">
            {!! nl2br(e($post->content)) !!}
        </div>
    </div>
</div>
@endsection
