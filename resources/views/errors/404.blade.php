@extends('layouts.app')

@section('title', '404 - Page Non Trouvée')

@section('content')
<div class="bg-white min-h-screen py-20 px-4 flex flex-col items-center justify-center text-center">
    <!-- Error Illustration -->
    <div class="relative mb-12">
        <div class="w-64 h-64 lg:w-80 lg:h-80 mx-auto opacity-10 blur-3xl bg-navy-900 rounded-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
        <div class="relative">
            <!-- Mock Illustration using SVG -->
            <svg class="w-64 h-64 lg:w-80 lg:h-80 mx-auto text-navy-100" viewBox="0 0 500 500" fill="none">
                <circle cx="250" cy="250" r="200" stroke="currentColor" stroke-width="2" stroke-dasharray="20 20"/>
                <path d="M150 150L350 350M350 150L150 350" stroke="currentColor" stroke-width="20" stroke-linecap="round"/>
                <rect x="200" y="240" width="100" height="20" rx="10" fill="#EAB308" class="animate-pulse"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-7xl lg:text-9xl font-black text-navy-900 italic tracking-tighter opacity-20">404</span>
            </div>
        </div>
    </div>

    <!-- Error Text -->
    <h1 class="text-3xl lg:text-4xl font-black text-navy-900 uppercase tracking-tighter italic mb-4">404, Page non trouvée</h1>
    <p class="max-w-md mx-auto text-gray-400 text-sm lg:text-base leading-relaxed mb-12 italic">
        Oups ! Il semble que la page que vous recherchez n'existe pas ou a été déplacée. 
        Vérifiez l'URL ou retournez à l'accueil.
    </p>

    <!-- Navigation Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button onclick="window.history.back()" class="btn-primary-gold px-12 py-4 uppercase tracking-[0.2em] text-[10px] flex items-center justify-center gap-3 group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7"/></svg>
            RETOURNER
        </button>
        <a href="{{ route('home') }}" class="border-2 border-navy-900 text-navy-900 px-12 py-4 rounded-md font-bold uppercase tracking-[0.2em] text-[10px] hover:bg-navy-900 hover:text-white transition-all flex items-center justify-center gap-3 group">
            PAGE D'ACCUEIL
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </a>
    </div>
    
    <!-- Support Link -->
    <p class="mt-16 text-[10px] text-gray-300 uppercase tracking-widest font-bold">
        Besoin d'aide ? <a href="{{ route('contact.index') }}" class="text-gold-600 hover:underline">Contactez le Support</a>
    </p>
</div>
@endsection
