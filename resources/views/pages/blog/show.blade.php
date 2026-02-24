@extends('layouts.app')

@section('title', $post->title . ' - Blog')

@section('content')
    <div class="relative py-16 bg-gray-50 overflow-hidden">
        <div class="hidden lg:block lg:absolute lg:inset-y-0 lg:h-full lg:w-full">
            <div class="relative h-full text-lg max-w-prose mx-auto" aria-hidden="true">
                <svg class="absolute top-12 left-full transform translate-x-32" width="404" height="384" fill="none"
                    viewBox="0 0 404 384">
                    <defs>
                        <pattern id="74b3fd99-0a6f-4271-bef2-e80eeafdf357" x="0" y="0" width="20" height="20"
                            patternUnits="userSpaceOnUse">
                            <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor" />
                        </pattern>
                    </defs>
                    <rect width="404" height="384" fill="url(#74b3fd99-0a6f-4271-bef2-e80eeafdf357)" />
                </svg>
            </div>
        </div>
        <div class="relative px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                <nav class="text-sm text-gray-600 mb-4">
                    <a href="{{ route('blog.index') }}" class="hover:underline">Blog</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-800">{{ Str::limit($post->title, 60) }}</span>
                </nav>

                <header class="text-center mb-6">
                    <span
                        class="inline-block text-xs font-semibold text-indigo-700 uppercase tracking-wide bg-indigo-100 border border-indigo-200 px-3 py-1 rounded-full">{{ $post->category ?? 'Article' }}</span>
                    <h1 class="mt-4 text-3xl leading-9 font-extrabold text-gray-900 sm:text-4xl">{{ $post->title }}</h1>

                    @php
                        $words = str_word_count(strip_tags($post->content ?? ''));
                        $minutes = max(1, (int) ceil($words / 200));
                    @endphp

                    <div class="mt-3 flex items-center justify-center space-x-4 text-sm text-gray-600">
                        <div>Publié le {{ $post->published_at ? $post->published_at->format('d/m/Y') : 'Récemment' }}</div>
                        <div>•</div>
                        <div>{{ $minutes }} min de lecture</div>
                    </div>

                    <div class="mt-4 flex items-center justify-center space-x-3">
                        <a href="javascript:window.print()" class="text-sm text-gray-600 hover:text-gray-900">Imprimer</a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->fullUrl()) }}"
                            target="_blank" rel="noopener" class="text-sm text-indigo-600 hover:underline">Partager</a>
                    </div>
                </header>

                @if ($post->image)
                    <figure class="mb-6">
                        <img class="w-full lg:w-64 xl:w-80 rounded-xl shadow-lg object-cover lg:float-left lg:mr-6 lg:mb-4"
                            src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                    </figure>
                @endif

                <article class="prose prose-lg prose-neutral mx-auto text-gray-800">
                    {!! nl2br(e($post->content)) !!}
                </article>

                <footer class="mt-12 border-t border-gray-200 pt-8">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">Partagez cet article si vous l'avez aimé.</div>
                        <div class="space-x-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                                target="_blank" rel="noopener" class="text-sm text-blue-600 hover:underline">Facebook</a>
                            <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}"
                                target="_blank" rel="noopener" class="text-sm text-green-600 hover:underline">WhatsApp</a>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Commentaires</h3>
                        <div class="bg-white border border-gray-100 rounded-lg p-6 text-sm text-gray-700">Les commentaires
                            seront disponibles
                            prochainement. Si vous avez une question, contactez-nous via la page <a
                                href="{{ route('contact.index') }}" class="text-indigo-600 hover:underline">Contact</a>.
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
@endsection
