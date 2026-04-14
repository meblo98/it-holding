<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - IT Holding Services')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            50:  '#f1f5f9',
                            100: '#e2e8f0',
                            200: '#cbd5e1',
                            300: '#94a3b8',
                            400: '#64748b',
                            500: '#1e293b',
                            600: '#0f172a',
                            700: '#020617',
                            800: '#000000',
                            900: '#000000',
                        },
                        gold: {
                            50:  '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        },
                    },
                }
            }
        }
    </script>
    <!-- Alpine.js (CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
        <!-- Sidebar -->
        <aside class="flex-shrink-0 w-64 flex flex-col border-r border-navy-700 bg-navy-500 text-white transition-all duration-300 transform fixed md:relative z-30 h-full"
               :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'md:translate-x-0': true}">
            <div class="flex items-center justify-center h-16 bg-navy-600 shadow-md border-b border-gold-500/20">
                <span class="text-xl font-bold uppercase tracking-wider text-gold-400">IT Holding Admin</span>
            </div>
            
            <div class="flex flex-col flex-1 overflow-y-auto">
                <nav class="flex-1 px-2 py-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-navy-600 text-gold-400' : 'text-gray-300 hover:bg-navy-600 hover:text-gold-400' }}">
                        <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.dashboard') ? 'text-gold-400' : 'text-gray-400 group-hover:text-gold-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.services.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.services*') ? 'bg-navy-600 text-gold-400' : 'text-gray-300 hover:bg-navy-600 hover:text-gold-400' }}">
                        <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.services*') ? 'text-gold-400' : 'text-gray-400 group-hover:text-gold-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Services
                    </a>

                    <a href="{{ route('admin.projects.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.projects*') ? 'bg-navy-600 text-gold-400' : 'text-gray-300 hover:bg-navy-600 hover:text-gold-400' }}">
                        <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.projects*') ? 'text-gold-400' : 'text-gray-400 group-hover:text-gold-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Portfolio
                    </a>

                    <a href="{{ route('admin.posts.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.posts*') ? 'bg-navy-600 text-gold-400' : 'text-gray-300 hover:bg-navy-600 hover:text-gold-400' }}">
                        <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.posts*') ? 'text-gold-400' : 'text-gray-400 group-hover:text-gold-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        Blog
                    </a>

                    <a href="{{ route('admin.products.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.products*') ? 'bg-navy-600 text-gold-400' : 'text-gray-300 hover:bg-navy-600 hover:text-gold-400' }}">
                        <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.products*') ? 'text-gold-400' : 'text-gray-400 group-hover:text-gold-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Boutique
                    </a>

                    <a href="{{ route('admin.quotes.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.quotes*') ? 'bg-navy-600 text-gold-400' : 'text-gray-300 hover:bg-navy-600 hover:text-gold-400' }}">
                        <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.quotes*') ? 'text-gold-400' : 'text-gray-400 group-hover:text-gold-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Devis
                    </a>

                    <a href="{{ route('admin.invoices.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.invoices*') ? 'bg-navy-600 text-gold-400' : 'text-gray-300 hover:bg-navy-600 hover:text-gold-400' }}">
                        <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.invoices*') ? 'text-gold-400' : 'text-gray-400 group-hover:text-gold-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Factures
                    </a>

                    <a href="{{ route('admin.orders.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.orders*') ? 'bg-navy-600 text-gold-400' : 'text-gray-300 hover:bg-navy-600 hover:text-gold-400' }}">
                        <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.orders*') ? 'text-gold-400' : 'text-gray-400 group-hover:text-gold-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Commandes
                    </a>
                </nav>
            </div>
            
            <div class="p-4 border-t border-navy-500">
                <a href="{{ url('/') }}" class="flex items-center text-gray-300 hover:text-gold-400">
                    <svg class="mr-3 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                   Retour au site
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="flex items-center justify-between px-6 py-4 bg-white shadow-sm border-b border-gray-200">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none md:hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <h2 class="text-xl font-semibold text-gray-800">
                    @yield('title')
                </h2>

                <div class="flex items-center" x-data="{ open: false }">
                    <div class="relative ml-3">
                        <div>
                            <button @click="open = !open" type="button" class="max-w-xs bg-white flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Ouvrir le menu utilisateur</span>
                                <span class="mr-3 text-navy-600 font-medium">{{ Auth::user()->name }}</span>
                                <div class="h-8 w-8 rounded-full bg-gold-100 flex items-center justify-center text-navy-600 font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </button>
                        </div>

                        <div x-show="open" 
                             @click.away="open = false" 
                             x-cloak
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Se déconnecter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
        
        <!-- Overlay for mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" x-cloak></div>
    </div>
</body>
</html>
