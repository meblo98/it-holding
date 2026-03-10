<nav x-data="{ open: false }" class="relative z-50">
    <!-- Top Utility Bar -->
    <div class="bg-navy-700 text-white/80 py-2 text-xs border-b border-white/10 hidden sm:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div>Bienvenue sur {{ config('app.name') }} - Votre partenaire IT au Sénégal</div>
            <div class="flex items-center gap-6">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    +221 77 351 87 16
                </span>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-gold-400 transition-colors">Besoin d'aide ?</a>
                    <a href="#" class="hover:text-gold-400 transition-colors">Suivre ma commande</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="bg-navy-600 border-b border-navy-500 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-8">
            <!-- Logo -->
            <div class="shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('logo.jpeg') }}" alt="Logo" class="h-12 w-auto object-contain rounded">
                    <span class="text-white font-bold text-2xl tracking-tighter hidden lg:block uppercase">{{ config('app.name') }}</span>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="flex-grow max-w-2xl hidden md:block">
                <form action="{{ route('shop.index') }}" method="GET" class="relative group">
                    <input type="search" name="q" placeholder="Rechercher des produits..." value="{{ request('q') }}"
                        class="w-full bg-white text-navy-900 px-5 py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-gold-500 transition-all shadow-inner">
                    <button type="submit" class="absolute right-0 top-0 h-full px-6 bg-gold-500 text-navy-900 rounded-r-md hover:bg-gold-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                    </button>
                </form>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4 text-white">
                <a href="{{ route('shop.cart') }}" class="relative p-2 hover:bg-navy-500 rounded-full transition-colors group">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @php
                        $cart = Session::get('cart', []);
                        $cartCount = array_sum(array_column($cart, 'quantity'));
                    @endphp
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-gold-500 text-navy-900 text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full group-hover:scale-110 transition-transform">{{ $cartCount }}</span>
                    @endif
                </a>
                
                <div class="h-8 w-px bg-white/10 mx-2 hidden sm:block"></div>

                @auth
                    <div x-data="{ userOpen: false }" class="relative">
                        <button @click="userOpen = !userOpen" class="flex items-center gap-2 hover:text-gold-400 transition-colors py-2 focus:outline-none">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="text-sm font-medium hidden lg:block">{{ Auth::user()->name }}</span>
                        </button>
                        <div x-show="userOpen" @click.away="userOpen = false" x-transition class="absolute right-0 mt-2 w-48 bg-white text-navy-900 rounded-md shadow-xl py-1 z-50">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Tableau de bord</a>
                            @if (auth()->user()->is_admin ?? false)
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Admin</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">@csrf <button type="submit" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">Déconnexion</button></form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2 hover:text-gold-400 transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span class="text-sm font-medium hidden sm:block">Connexion</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <div class="bg-white border-b border-gray-100 py-3 shadow-sm hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <!-- Categories Dropdown (Mock) -->
                <div x-data="{ catOpen: false }" class="relative">
                    <button @click="catOpen = !catOpen" class="bg-gray-100 hover:bg-gray-200 text-navy-900 px-4 py-2 rounded-md font-semibold flex items-center gap-3 transition-colors focus:outline-none">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        Toutes les catégories
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{'rotate-180': catOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <!-- Mock categories -->
                    <div x-show="catOpen" @click.away="catOpen = false" x-transition class="absolute left-0 mt-2 w-64 bg-white border border-gray-100 shadow-xl rounded-md z-50 py-2">
                        <a href="#" class="block px-4 py-2 text-sm text-navy-700 hover:bg-gold-50 hover:text-gold-700">Ordinateurs & Laptops</a>
                        <a href="#" class="block px-4 py-2 text-sm text-navy-700 hover:bg-gold-50 hover:text-gold-700">Composants PC</a>
                        <a href="#" class="block px-4 py-2 text-sm text-navy-700 hover:bg-gold-50 hover:text-gold-700">Périphériques</a>
                        <a href="#" class="block px-4 py-2 text-sm text-navy-700 hover:bg-gold-50 hover:text-gold-700">Logiciels & SaaS</a>
                        <a href="#" class="block px-4 py-2 text-sm text-navy-700 hover:bg-gold-50 hover:text-gold-700">Réseaux & Serveurs</a>
                    </div>
                </div>

                <!-- Nav Links -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-xs font-bold tracking-wider {{ request()->routeIs('home') ? 'text-gold-600 underline decoration-2 underline-offset-8' : 'text-navy-700 hover:text-gold-500' }} transition-colors uppercase">ACCUEIL</a>
                    <a href="{{ route('shop.index') }}" class="text-xs font-bold tracking-wider {{ request()->routeIs('shop.*') ? 'text-gold-600 underline decoration-2 underline-offset-8' : 'text-navy-700 hover:text-gold-500' }} transition-colors uppercase">BOUTIQUE</a>
                    <a href="{{ route('services.index') }}" class="text-xs font-bold tracking-wider {{ request()->routeIs('services.*') ? 'text-gold-600 underline decoration-2 underline-offset-8' : 'text-navy-700 hover:text-gold-500' }} transition-colors uppercase">NOS SERVICES</a>
                    <a href="{{ route('portfolio.index') }}" class="text-xs font-bold tracking-wider {{ request()->routeIs('portfolio.*') ? 'text-gold-600 underline decoration-2 underline-offset-8' : 'text-navy-700 hover:text-gold-500' }} transition-colors uppercase">PORTFOLIO</a>
                    <a href="{{ route('blog.index') }}" class="text-xs font-bold tracking-wider {{ request()->routeIs('blog.*') ? 'text-gold-600 underline decoration-2 underline-offset-8' : 'text-navy-700 hover:text-gold-500' }} transition-colors uppercase">BLOG</a>
                    <a href="{{ route('about') }}" class="text-xs font-bold tracking-wider {{ request()->routeIs('about') ? 'text-gold-600 underline decoration-2 underline-offset-8' : 'text-navy-700 hover:text-gold-500' }} transition-colors uppercase uppercase">À PROPOS</a>
                    <a href="{{ route('contact.index') }}" class="text-xs font-bold tracking-wider {{ request()->routeIs('contact.*') ? 'text-gold-600 underline decoration-2 underline-offset-8' : 'text-navy-700 hover:text-gold-500' }} transition-colors uppercase uppercase">CONTACT</a>
                </div>
            </div>
            
            <div class="text-sm font-bold text-navy-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="hidden lg:inline">SUPPORT:</span> +221 77 351 87 16
            </div>
        </div>
    </div>

    <!-- Mobile Header -->
    <div class="md:hidden bg-navy-600 py-3 px-4 flex items-center justify-between border-t border-navy-500">
        <button @click="open = !open" class="text-white hover:text-gold-400 focus:outline-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="{{ route('home') }}">
            <img src="{{ asset('logo.jpeg') }}" alt="Logo" class="h-10 w-auto rounded object-contain">
        </a>
        <div class="flex items-center gap-4">
            <a href="{{ route('shop.cart') }}" class="relative text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @if ($cartCount > 0)
                    <span class="absolute -top-1 -right-1 bg-gold-500 text-navy-900 text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- Mobile Menu Drawer -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-0 z-50 md:hidden overflow-y-auto bg-white" style="display: none;">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-navy-600 text-white">
            <span class="font-bold">MENU</span>
            <button @click="open = false" class="text-white">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex flex-col py-4">
            <a href="{{ route('home') }}" class="px-6 py-4 text-navy-900 font-bold border-b border-gray-50 {{ request()->routeIs('home') ? 'bg-gold-50 text-gold-700' : '' }}">ACCUEIL</a>
            <a href="{{ route('shop.index') }}" class="px-6 py-4 text-navy-900 font-bold border-b border-gray-50 {{ request()->routeIs('shop.*') ? 'bg-gold-50 text-gold-700' : '' }}">BOUTIQUE</a>
            <a href="{{ route('services.index') }}" class="px-6 py-4 text-navy-900 font-bold border-b border-gray-50 {{ request()->routeIs('services.*') ? 'bg-gold-50 text-gold-700' : '' }}">NOS SERVICES</a>
            <a href="{{ route('portfolio.index') }}" class="px-6 py-4 text-navy-900 font-bold border-b border-gray-50 {{ request()->routeIs('portfolio.*') ? 'bg-gold-50 text-gold-700' : '' }}">PORTFOLIO</a>
            <a href="{{ route('blog.index') }}" class="px-6 py-4 text-navy-900 font-bold border-b border-gray-50 {{ request()->routeIs('blog.*') ? 'bg-gold-50 text-gold-700' : '' }}">BLOG</a>
            <a href="{{ route('about') }}" class="px-6 py-4 text-navy-900 font-bold border-b border-gray-50 {{ request()->routeIs('about') ? 'bg-gold-50 text-gold-700' : '' }}">À PROPOS</a>
            <a href="{{ route('contact.index') }}" class="px-6 py-4 text-navy-900 font-bold border-b border-gray-50 {{ request()->routeIs('contact.*') ? 'bg-gold-50 text-gold-700' : '' }}">CONTACT</a>
            
            <div class="px-6 py-8 mt-4 bg-gray-50">
                <p class="text-xs text-gray-500 mb-2">BESOIN D'AIDE ?</p>
                <p class="font-bold text-navy-900">+221 33 823 45 67</p>
                <p class="text-sm text-navy-700">contact@itholding.sn</p>
            </div>
        </div>
    </div>
</nav>
