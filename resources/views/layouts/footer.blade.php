<footer class="bg-navy-900 text-white/70 pt-14 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <div class="space-y-5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="bg-white rounded p-1 inline-flex"><img src="{{ asset('logo.jpeg') }}" alt="Logo {{ config('app.name') }}" class="h-10 w-auto object-contain"></span>
                    <span class="text-white font-semibold text-xl tracking-tight">{{ config('app.name') }}</span>
                </a>
                <p class="text-sm leading-relaxed max-w-xs">
                    Matériel informatique, réseau, vidéosurveillance et maintenance pour les entreprises et les particuliers au Sénégal.
                </p>
                <div class="flex items-center gap-4">
                    <a href="#" aria-label="Instagram" class="text-white/50 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" aria-label="Facebook" class="text-white/50 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-4 text-sm">Boutique</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('shop.index') }}" class="hover:text-white transition-colors">Tous les produits</a></li>
                    <li><a href="{{ route('shop.cart') }}" class="hover:text-white transition-colors">Mon panier</a></li>
                    <li><a href="{{ route('dashboard.track') }}" class="hover:text-white transition-colors">Suivre ma commande</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-4 text-sm">Entreprise</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">À propos</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">Services</a></li>
                    <li><a href="{{ route('care.landing') }}" class="hover:text-white transition-colors">Garantie &amp; Protection</a></li>
                    <li><a href="{{ route('portfolio.index') }}" class="hover:text-white transition-colors">Portfolio</a></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog</a></li>
                    <li><a href="{{ route('partner.landing') }}" class="hover:text-white transition-colors">Devenir partenaire</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-4 text-sm">Nous joindre</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="tel:+221773518716" class="hover:text-white transition-colors">+221 77 351 87 16</a></li>
                    <li><a href="mailto:contact@itholding.sn" class="hover:text-white transition-colors">contact@itholding.sn</a></li>
                    <li><a href="{{ route('contact.index') }}" class="inline-block mt-2 bg-gold-500 text-navy-900 font-semibold px-4 py-2 rounded-md hover:bg-gold-400 transition-colors">Écrire à l'équipe</a></li>
                </ul>
            </div>
        </div>

        <div class="pt-6 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-3 text-xs">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <div class="flex items-center gap-5">
                <a href="#" class="hover:text-white transition-colors">Conditions générales</a>
                <a href="#" class="hover:text-white transition-colors">Confidentialité</a>
                <a href="#" class="hover:text-white transition-colors">Paiement</a>
            </div>
        </div>
    </div>
</footer>
