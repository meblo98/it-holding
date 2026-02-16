@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-4">Bienvenue, {{ Auth::user()->name }} !</h1>
                <p>Vous êtes connecté à votre espace client.</p>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Example Card 1 -->
                    <div class="bg-gray-50 p-4 rounded shadow">
                        <h3 class="font-bold text-lg">Mes Commandes</h3>
                        <p class="text-gray-600 mt-2">Suivez l'état de vos commandes récentes.</p>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 mt-3 inline-block font-medium">Voir les commandes &rarr;</a>
                    </div>
                    
                    <!-- Example Card 2 -->
                    <div class="bg-gray-50 p-4 rounded shadow">
                        <h3 class="font-bold text-lg">Mes Devis</h3>
                        <p class="text-gray-600 mt-2">Consultez vos demandes de devis en cours.</p>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 mt-3 inline-block font-medium">Voir les devis &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
