@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Créer un nouveau compte
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ou
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    connectez-vous à votre compte existant
                </a>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" action="{{ route('register') }}" method="POST">
                    @csrf

                    <x-form-input name="name" label="Nom complet" required />

                    <x-form-input name="email" label="Adresse Email" type="email" required />

                    <x-form-input name="password" label="Mot de passe" type="password" required />

                    <x-form-input name="password_confirmation" label="Confirmer le mot de passe" type="password" required />

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            S'inscrire
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
