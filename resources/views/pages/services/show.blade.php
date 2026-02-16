@extends('layouts.app')

@section('title', $service->title . ' - ' . config('app.name'))

@section('content')
<div class="bg-white overflow-hidden">
    <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="hidden lg:block bg-gray-50 absolute top-0 bottom-0 left-3/4 w-screen"></div>
        <div class="mx-auto text-base max-w-prose lg:grid lg:grid-cols-2 lg:gap-8 lg:max-w-none">
            <div>
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Service</h2>
                <h3 class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    {{ $service->title }}
                </h3>
            </div>
        </div>
        <div class="mt-8 lg:grid lg:grid-cols-2 lg:gap-8">
            <div class="relative lg:row-start-1 lg:col-start-2">
                <svg class="hidden lg:block absolute top-0 right-0 -mt-20 -mr-20 block h-42 w-42 text-gray-100" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polygon points="50,0 100,0 50,100 0,100" />
                </svg>
                <div class="relative text-base mx-auto max-w-prose lg:max-w-none">
                    <figure>
                        <div class="aspect-w-12 aspect-h-7 lg:aspect-none">
                            @if($service->image)
                                <img class="rounded-lg shadow-lg object-cover object-center" src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" width="1184" height="1376">
                            @else
                                <div class="bg-gray-200 rounded-lg h-64 w-full flex items-center justify-center text-gray-400">
                                    <span>Image du service</span>
                                </div>
                            @endif
                        </div>
                    </figure>
                </div>
            </div>
            <div class="mt-8 lg:mt-0">
                <div class="text-base max-w-prose mx-auto lg:max-w-none text-gray-500">
                    <p class="text-lg">
                        {{ $service->description }}
                    </p>
                </div>
                <div class="mt-5 prose prose-indigo text-gray-500 mx-auto lg:max-w-none lg:row-start-1 lg:col-start-1">
                    {!! nl2br(e($service->content)) !!}
                </div>
                
                <div class="mt-10">
                    <a href="{{ route('contact.index', ['subject' => 'Devis pour ' . $service->title]) }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Demander un devis
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
