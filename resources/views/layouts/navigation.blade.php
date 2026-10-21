@php
    $navCategories = \App\Models\Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
    $cart = Session::get('cart', []);
    $cartCount = array_sum(array_column($cart, 'quantity'));
    $navLinks = [
        ['Accueil', route('home'), request()->routeIs('home')],
        ['Boutique', route('shop.index'), request()->routeIs('shop.*')],
        ['Services', route('services.index'), request()->routeIs('services.*')],
        ['Care+', route('care.landing'), request()->routeIs('care.*')],
        ['Portfolio', route('portfolio.index'), request()->routeIs('portfolio.*')],
        ['Blog', route('blog.index'), request()->routeIs('blog.*')],
        ['À propos', route('about'), request()->routeIs('about')],
        ['Contact', route('contact.index'), request()->routeIs('contact.*')],
    ];
@endphp
<nav x-data="{ open: false }" class="relative z-50 bg-white border-b border-navy-100">

    <!-- Ligne 1 : logo, recherche, panier, compte (bureau) -->
    <div class="hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                <img src="{{ asset('logo.jpeg') }}" alt="Logo {{ config('app.name') }}" class="h-12 w-auto object-contain">
                <span class="hidden lg:block text-navy-900 font-semibold text-xl tracking-tight">{{ config('app.name') }}</span>
            </a>

            <form action="{{ route('shop.index') }}" method="GET" class="flex-grow max-w-xl">
                <label for="nav-search" class="sr-only">Rechercher un produit</label>
                <div class="relative">
                    <input id="nav-search" type="search" name="search" placeholder="Rechercher un produit" value="{{ request('search') }}"
                        class="w-full bg-navy-50 text-navy-900 pl-4 pr-12 py-2.5 rounded-md focus:outline-none focus:bg-white">
                    <button type="submit" aria-label="Lancer la recherche" class="absolute right-0 top-0 h-full px-4 text-navy-600 hover:text-navy-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                    </button>
                </div>
            </form>

            <div class="flex items-center gap-5 text-navy-900">
                <a href="{{ route('shop.cart') }}" class="relative p-1 hover:text-gold-600 transition-colors" aria-label="Panier">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1.5 -right-2 bg-gold-500 text-navy-900 text-[10px] font-bold min-w-[1.25rem] h-5 px-1 flex items-center justify-center rounded-full">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    <div x-data="{ userOpen: false }" class="relative">
                        <button @click="userOpen = !userOpen" class="flex items-center gap-2 hover:text-gold-600 transition-colors py-1 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="text-sm font-medium hidden lg:block">{{ Auth::user()->name }}</span>
                        </button>
                        <div x-show="userOpen" @click.away="userOpen = false" x-transition class="absolute right-0 mt-2 w-48 bg-white text-navy-900 rounded-md border border-navy-100 shadow-lg py-1 z-50">
                            @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-navy-50">Administration</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-navy-50">Tableau de bord</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">@csrf <button type="submit" class="block w-full text-left px-4 py-2 text-sm hover:bg-navy-50">Déconnexion</button></form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2 hover:text-gold-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span class="text-sm font-medium hidden sm:block">Connexion</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Ligne 2 : catégories, pages, contact (bureau) -->
    <div class="hidden md:block border-t border-navy-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <div x-data="{ catOpen: false }" class="relative">
                    <button @click="catOpen = !catOpen" class="flex items-center gap-2 py-3 text-sm font-semibold text-navy-900 hover:text-gold-600 transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        Catégories
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': catOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="catOpen" @click.away="catOpen = false" x-transition class="absolute left-0 mt-0 w-64 bg-white border border-navy-100 shadow-lg rounded-md z-50 py-2">
                        @forelse($navCategories as $cat)
                            <div class="group/cat relative">
                                <a href="{{ route('shop.index', ['category_id' => $cat->id]) }}" class="flex items-center justify-between px-4 py-2 text-sm text-navy-800 hover:bg-navy-50 font-medium transition-colors">
                                    {{ $cat->name }}
                                    @if($cat->children->count())
                                        <svg class="w-4 h-4 text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    @endif
                                </a>
                                @if($cat->children->count())
                                    <div class="hidden group-hover/cat:block absolute left-full top-0 ml-1 w-64 bg-white border border-navy-100 shadow-lg rounded-md py-2 z-50">
                                        @foreach($cat->children as $child)
                                            <a href="{{ route('shop.index', ['category_id' => $child->id]) }}" class="block px-4 py-2 text-sm text-navy-800 hover:bg-navy-50 transition-colors">
                                                {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <a href="{{ route('shop.index') }}" class="block px-4 py-2 text-sm text-navy-400">Tous les produits</a>
                        @endforelse
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    @foreach ($navLinks as [$label, $url, $active])
                        <a href="{{ $url }}" class="py-3 text-sm {{ $active ? 'font-semibold text-navy-900 border-b-2 border-gold-500' : 'font-medium text-navy-500 hover:text-navy-900 border-b-2 border-transparent' }} transition-colors">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-5 text-sm text-navy-500">
                <a href="{{ route('dashboard.track') }}" class="hover:text-navy-900 transition-colors hidden lg:inline">Suivre ma commande</a>
                <a href="tel:+221773518716" class="font-semibold text-navy-900">+221 77 351 87 16</a>
            </div>
        </div>
    </div>

    <!-- En-tête mobile -->
    <div class="md:hidden py-3 px-4 flex items-center justify-between">
        <button @click="open = !open" class="text-navy-900 focus:outline-none" aria-label="Ouvrir le menu">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="{{ route('home') }}">
            <img src="{{ asset('logo.jpeg') }}" alt="Logo {{ config('app.name') }}" class="h-10 w-auto object-contain">
        </a>
        <a href="{{ route('shop.cart') }}" class="relative text-navy-900" aria-label="Panier">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            @if ($cartCount > 0)
                <span class="absolute -top-1 -right-2 bg-gold-500 text-navy-900 text-[10px] font-bold min-w-[1rem] h-4 px-1 flex items-center justify-center rounded-full">{{ $cartCount }}</span>
            @endif
        </a>
    </div>

    <!-- Menu mobile -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-0 z-50 md:hidden overflow-y-auto bg-white" style="display: none;">
        <div class="px-4 py-3 border-b border-navy-100 flex items-center justify-between">
            <span class="font-semibold text-navy-900">Menu</span>
            <button @click="open = false" class="text-navy-900" aria-label="Fermer le menu">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('shop.index') }}" method="GET" class="px-4 py-3 border-b border-navy-100">
            <label for="mobile-search" class="sr-only">Rechercher un produit</label>
            <input id="mobile-search" type="search" name="search" placeholder="Rechercher un produit" value="{{ request('search') }}" class="w-full bg-navy-50 text-navy-900 px-4 py-2.5 rounded-md focus:outline-none">
        </form>

        <div class="flex flex-col">
            @foreach ($navLinks as [$label, $url, $active])
                <a href="{{ $url }}" class="px-6 py-4 border-b border-navy-50 {{ $active ? 'font-semibold text-navy-900 bg-navy-50' : 'font-medium text-navy-700' }}">{{ $label }}</a>
                @if ($label === 'Boutique')
                    <div x-data="{ mobileCatOpen: false }" class="border-b border-navy-50">
                        <button @click="mobileCatOpen = !mobileCatOpen" class="w-full flex items-center justify-between px-6 py-4 font-medium text-navy-700 focus:outline-none">
                            <span>Catégories</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': mobileCatOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="mobileCatOpen" x-transition class="bg-navy-50 py-2">
                            <a href="{{ route('shop.index') }}" class="block px-8 py-2 text-sm text-navy-700">Tous les produits</a>
                            @foreach($navCategories as $cat)
                                <a href="{{ route('shop.index', ['category_id' => $cat->id]) }}" class="block px-8 py-2 text-sm text-navy-800 font-medium">{{ $cat->name }}</a>
                                @foreach($cat->children as $child)
                                    <a href="{{ route('shop.index', ['category_id' => $child->id]) }}" class="block px-12 py-1.5 text-xs text-navy-500">{{ $child->name }}</a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="px-6 py-6 mt-2 bg-navy-50 text-sm">
                <a href="{{ route('dashboard.track') }}" class="block text-navy-700 mb-4">Suivre ma commande</a>
                <p class="text-navy-500 mb-1">Une question ?</p>
                <a href="tel:+221773518716" class="font-semibold text-navy-900">+221 77 351 87 16</a>
                <p class="text-navy-700">contact@itholding.sn</p>
            </div>
        </div>
    </div>
</nav>
