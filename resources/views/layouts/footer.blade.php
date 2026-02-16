<footer class="bg-gray-800 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <h3 class="text-lg font-bold">{{ config('app.name') }}</h3>
                <p class="text-sm text-gray-400">Votre partenaire informatique de confiance au Sénégal.</p>
            </div>
            <div class="flex space-x-4">
                <a href="#" class="text-gray-400 hover:text-white transition">Mentions Légales</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Politique de Confidentialité</a>
            </div>
        </div>
        <div class="mt-8 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
        </div>
    </div>
</footer>
