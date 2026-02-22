@extends('layouts.app')

@section('title', 'Contact - ' . config('app.name'))

@section('content')
    <div class="bg-white py-16 px-4 overflow-hidden sm:px-6 lg:px-8 lg:py-24">
        <div class="relative max-w-xl mx-auto">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Contactez-nous</h2>
                <p class="mt-4 text-lg leading-6 text-gray-500">
                    Une question ? Un projet ? Nous sommes à votre écoute.
                </p>
            </div>

            @if (session('success'))
                <div class="mt-8 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mt-12">
                <form action="{{ route('contact.store') }}" method="POST"
                    class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    @csrf
                    <div class="sm:col-span-2">
                        <x-form-input name="name" label="Nom complet" required />
                    </div>

                    <div class="sm:col-span-2">
                        <x-form-input name="email" label="Email" type="email" required />
                    </div>

                    <div class="sm:col-span-2">
                        <x-form-input name="subject" label="Sujet" value="{{ $subject ?? '' }}" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-form-textarea name="message" label="Message" rows="4" required />
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
