@extends('layouts.app')

@section('title', 'À Propos - ' . config('app.name'))

@section('content')
<div class="bg-white">
    <!-- Hero section -->
    <div class="relative bg-indigo-800">
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-1.2.1&auto=format&fit=crop&w=2850&q=80" alt="Work">
            <div class="absolute inset-0 bg-indigo-800 mix-blend-multiply" aria-hidden="true"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">À Propos de Nous</h1>
            <p class="mt-6 text-xl text-indigo-100 max-w-3xl">
                IT HOLDING SERVICES est votre partenaire privilégié pour tous vos besoins technologiques au Sénégal.
            </p>
        </div>
    </div>

    <!-- Mission section -->
    <div class="py-16 bg-gray-50 overflow-hidden lg:py-24">
        <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
            <div class="relative">
                <h2 class="text-center text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Notre Mission
                </h2>
                <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
                    Nous nous engageons à fournir des solutions informatiques innovantes et fiables pour accompagner la croissance de nos clients. Que ce soit pour des services numériques, de la maintenance, ou de la vidéosurveillance, nous visons l'excellence.
                </p>
            </div>

            <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="relative">
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">
                        Nos Valeurs
                    </h3>
                    <p class="mt-3 text-lg text-gray-500">
                        Intégrité, Innovation, et Service Client sont au cœur de notre démarche.
                    </p>

                    <dl class="mt-10 space-y-10">
                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                    <!-- Heroicon name: globe-alt -->
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                </div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Expertise Locale & Globale</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500">
                                Ancrés au Sénégal mais connectés aux standards internationaux.
                            </dd>
                        </div>
                        
                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                   <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Réactivité</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500">
                                Intervention rapide pour minimiser vos temps d'arrêt.
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="mt-10 -mx-4 relative lg:mt-0" aria-hidden="true">
                    <img class="relative mx-auto" width="490" src="https://tailwindui.com/img/features/feature-example-2.png" alt="">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
