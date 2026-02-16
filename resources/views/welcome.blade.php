@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block">Bienvenue chez</span>
                <span class="block text-indigo-600">{{ config('app.name') }}</span>
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                Votre partenaire de confiance pour tous vos besoins en services informatiques, maintenance, numérisation et plus encore.
            </p>
            <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                <div class="rounded-md shadow">
                    <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                        Nos Services
                    </a>
                </div>
                <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                    <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                        Contactez-nous
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Preview -->
    <section class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">Nos Solutions</h2>
                <p class="mt-4 text-lg text-gray-500">Découvrez comment nous pouvons vous aider à propulser votre entreprise.</p>
            </div>
            
            <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Service 1 -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Services Numériques</h3>
                        <p class="mt-2 text-sm text-gray-500">Développement web, mobile et transformation digitale.</p>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Maintenance & Réparation</h3>
                        <p class="mt-2 text-sm text-gray-500">Assistance technique pour vos équipements informatiques.</p>
                    </div>
                </div>

                <!-- Service 3 -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Vidéosurveillance</h3>
                        <p class="mt-2 text-sm text-gray-500">Sécurisez vos locaux avec nos solutions de contrôle d'accès.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
