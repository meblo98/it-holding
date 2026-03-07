@extends('layouts.app')

@section('content')
    <!-- Hero Section - Premium Institutional Design -->
    <section x-data="{ loaded: false }" @load="loaded = true"
        class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-navy-900 min-h-[90vh] flex items-center">

        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Gradient Orbs Animation -->
            <div
                class="absolute -top-40 -right-40 w-96 h-96 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-0">
            </div>
            <div
                class="absolute -bottom-40 -left-40 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute top-1/2 left-1/2 w-96 h-96 bg-violet-500 rounded-full mix-blend-multiply filter blur-3xl opacity-25 animate-blob animation-delay-4000">
            </div>

            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>

            <!-- Floating IT Elements -->
            <!-- Computer Icon -->
            <div class="absolute top-20 left-10 animate-float opacity-70" style="animation-duration: 6s;">
                <svg class="w-16 h-16 text-cyan-400 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <!-- Server Icon -->
            <div class="absolute bottom-32 right-20 animate-float opacity-80"
                style="animation-duration: 8s; animation-delay: 1s;">
                <svg class="w-20 h-20 text-emerald-400 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
            </div>

            <!-- Code Brackets -->
            <div class="absolute top-1/3 right-10 animate-float opacity-75"
                style="animation-duration: 7s; animation-delay: 2s;">
                <svg class="w-12 h-12 text-rose-400 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
            </div>

            <!-- Database Icon -->
            <div class="absolute bottom-20 left-20 animate-float opacity-85"
                style="animation-duration: 9s; animation-delay: 3s;">
                <svg class="w-14 h-14 text-violet-400 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                </svg>
            </div>

            <!-- Network Icon -->
            <div class="absolute top-2/3 left-1/4 animate-float opacity-90"
                style="animation-duration: 10s; animation-delay: 4s;">
                <svg class="w-18 h-18 text-blue-400 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>

            <!-- Shopping Cart Icon -->
            <div class="absolute top-1/4 left-1/5 animate-cart-bounce opacity-75"
                style="animation-duration: 8s; animation-delay: 5s;">
                <svg class="w-14 h-14 text-orange-400 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13l-1.1 5M7 13l1.1-5m8.9 5L17 8m2 5v5a2 2 0 01-2 2H9a2 2 0 01-2-2v-5m8-5V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2.01" />
                </svg>
            </div>

            <!-- Product Box Icon -->
            <div class="absolute bottom-1/4 right-1/4 animate-float opacity-80"
                style="animation-duration: 9s; animation-delay: 6s;">
                <svg class="w-16 h-16 text-amber-400 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>

            <!-- Price Tag Icon -->
            <div class="absolute top-1/2 right-1/6 animate-float opacity-85"
                style="animation-duration: 7s; animation-delay: 7s;">
                <svg class="w-12 h-12 text-lime-400 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>

            <!-- Credit Card Icon -->
            <div class="absolute bottom-1/6 left-1/6 animate-float opacity-70"
                style="animation-duration: 10s; animation-delay: 8s;">
                <svg class="w-15 h-15 text-teal-400 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>

            <!-- Discount Badge -->
            <div class="absolute top-3/4 left-3/4 animate-discount-pulse opacity-90"
                style="animation-duration: 6s; animation-delay: 9s;">
                <div class="relative">
                    <svg class="w-14 h-14 text-red-400 drop-shadow-lg" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M9 14l6-6m-5.5.5h.01m4.99 5h.01m-.01-5.5h.01M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold text-red-300">%</span>
                    </div>
                </div>
            </div>

            <!-- Coin/Money Icon -->
            <div class="absolute top-1/6 right-1/3 animate-coin-spin opacity-85"
                style="animation-duration: 8s; animation-delay: 10s;">
                <svg class="w-10 h-10 text-yellow-300 drop-shadow-lg" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Floating Particles -->
            <div class="absolute inset-0">
                <div class="absolute top-1/4 left-1/3 w-3 h-3 bg-cyan-400 rounded-full animate-ping opacity-90 shadow-lg shadow-cyan-400/50"
                    style="animation-duration: 3s;"></div>
                <div class="absolute top-3/4 right-1/3 w-2 h-2 bg-emerald-400 rounded-full animate-ping opacity-85 shadow-lg shadow-emerald-400/50"
                    style="animation-duration: 4s; animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-2/3 w-2.5 h-2.5 bg-rose-400 rounded-full animate-ping opacity-80 shadow-lg shadow-rose-400/50"
                    style="animation-duration: 5s; animation-delay: 2s;"></div>
                <div class="absolute bottom-1/4 left-1/4 w-2 h-2 bg-violet-400 rounded-full animate-ping opacity-95 shadow-lg shadow-violet-400/50"
                    style="animation-duration: 2.5s; animation-delay: 0.5s;"></div>
                <div class="absolute top-1/6 right-1/4 w-3 h-3 bg-blue-400 rounded-full animate-ping opacity-75 shadow-lg shadow-blue-400/50"
                    style="animation-duration: 6s; animation-delay: 3s;"></div>
                <div class="absolute bottom-1/3 right-1/2 w-1.5 h-1.5 bg-yellow-400 rounded-full animate-ping opacity-85 shadow-lg shadow-yellow-400/50"
                    style="animation-duration: 4.5s; animation-delay: 1.5s;"></div>
                <div class="absolute top-2/3 left-1/2 w-2 h-2 bg-pink-400 rounded-full animate-ping opacity-80 shadow-lg shadow-pink-400/50"
                    style="animation-duration: 3.5s; animation-delay: 2.5s;"></div>
                <!-- Shopping-themed particles -->
                <div class="absolute top-1/3 right-1/2 w-2 h-2 bg-orange-400 rounded-full animate-ping opacity-88 shadow-lg shadow-orange-400/50"
                    style="animation-duration: 4s; animation-delay: 3.5s;"></div>
                <div class="absolute bottom-1/2 left-1/2 w-1.8 h-1.8 bg-amber-400 rounded-full animate-ping opacity-82 shadow-lg shadow-amber-400/50"
                    style="animation-duration: 3.8s; animation-delay: 4s;"></div>
                <div class="absolute top-1/5 left-1/4 w-2.2 h-2.2 bg-lime-400 rounded-full animate-ping opacity-90 shadow-lg shadow-lime-400/50"
                    style="animation-duration: 5.2s; animation-delay: 4.5s;"></div>
                <div class="absolute bottom-1/5 right-1/3 w-1.6 h-1.6 bg-teal-400 rounded-full animate-ping opacity-86 shadow-lg shadow-teal-400/50"
                    style="animation-duration: 4.2s; animation-delay: 5s;"></div>
                <div class="absolute top-1/2 right-1/5 w-2.4 h-2.4 bg-red-400 rounded-full animate-ping opacity-92 shadow-lg shadow-red-400/50"
                    style="animation-duration: 3.6s; animation-delay: 5.5s;"></div>
            </div>
        </div>

        <!-- Animated Code Lines -->
        <div class="absolute top-1/4 right-1/4 opacity-70">
            <div class="text-cyan-300 text-sm font-mono font-bold animate-data-flow drop-shadow-lg"
                style="animation-duration: 4s;">
                <span class="typing text-cyan-200">function optimize(){</span>
            </div>
        </div>
        <div class="absolute bottom-1/3 left-1/3 opacity-65">
            <div class="text-emerald-300 text-sm font-mono font-bold animate-data-flow drop-shadow-lg"
                style="animation-duration: 5s; animation-delay: 1s;">
                <span class="text-emerald-200">const data = fetch('/api')</span>
            </div>
        </div>
        <div class="absolute top-2/3 right-1/3 opacity-75">
            <div class="text-rose-300 text-sm font-mono font-bold animate-data-flow drop-shadow-lg"
                style="animation-duration: 3.5s; animation-delay: 2s;">
                <span class="text-rose-200">return response.json()</span>
            </div>
        </div>
        <!-- Shopping-themed code lines -->
        <div class="absolute bottom-1/4 right-1/5 opacity-68">
            <div class="text-orange-300 text-xs font-mono font-bold animate-data-flow drop-shadow-lg"
                style="animation-duration: 4.5s; animation-delay: 3s;">
                <span class="text-orange-200">cart.addItem(product)</span>
            </div>
        </div>
        <div class="absolute top-1/3 left-1/6 opacity-72">
            <div class="text-amber-300 text-xs font-mono font-bold animate-price-glow drop-shadow-lg"
                style="animation-duration: 5.5s; animation-delay: 4s;">
                <span class="text-amber-200">price: 25000 FCFA</span>
            </div>
        </div>
        <div class="absolute bottom-1/6 left-2/3 opacity-70">
            <div class="text-lime-300 text-xs font-mono font-bold animate-data-flow drop-shadow-lg"
                style="animation-duration: 4.2s; animation-delay: 5s;">
                <span class="text-lime-200">order.checkout()</span>
            </div>
        </div>
        </defs>

        <!-- Connection lines -->
        <path d="M100,200 Q300,150 500,200" stroke="url(#connectionGradient)" stroke-width="3" fill="none"
            class="animate-circuit-flow drop-shadow-sm" />
        <path d="M800,300 Q600,250 400,300" stroke="url(#connectionGradient2)" stroke-width="3" fill="none"
            class="animate-circuit-flow drop-shadow-sm" style="animation-delay: 1s;" />
        <path d="M200,400 Q400,350 600,400" stroke="url(#connectionGradient3)" stroke-width="3" fill="none"
            class="animate-circuit-flow drop-shadow-sm" style="animation-delay: 2s;" />

        <!-- Data packets -->
        <circle cx="100" cy="200" r="4" fill="#06b6d4" class="animate-data-flow drop-shadow-lg"
            style="animation-duration: 3s;" />
        <circle cx="800" cy="300" r="4" fill="#10b981" class="animate-data-flow drop-shadow-lg"
            style="animation-duration: 3s; animation-delay: 1s;" />
        <circle cx="200" cy="400" r="4" fill="#f97316" class="animate-data-flow drop-shadow-lg"
            style="animation-duration: 3s; animation-delay: 2s;" />
        </svg>

        <!-- Circuit Pattern Overlay -->
        <div class="absolute inset-0 opacity-3">
            <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="circuit" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <path d="M 10,0 L 10,10 M 0,10 L 20,10" stroke="rgba(255,193,7,0.1)" stroke-width="0.5"
                            fill="none" />
                        <circle cx="10" cy="10" r="1" fill="rgba(255,193,7,0.05)" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#circuit)" />
            </svg>
        </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-32 relative z-10 w-full">
            <div class="max-w-3xl">
                <!-- Animated IT Icons Row -->
                <div x-show="loaded" x-transition:enter="transition ease-out duration-1000 transform"
                    x-transition:enter-start="opacity-0 -translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex justify-center gap-8 mb-8">

                    <!-- Desktop Computer -->
                    <div class="group flex flex-col items-center">
                        <div
                            class="p-3 bg-cyan-400/20 rounded-xl group-hover:bg-cyan-400/30 transition-all duration-300 animate-pulse shadow-lg shadow-cyan-400/20">
                            <svg class="w-8 h-8 text-cyan-300 group-hover:text-cyan-200 transition-colors duration-300 drop-shadow-sm"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span
                            class="text-xs text-cyan-300 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-semibold">Ordinateurs</span>
                    </div>

                    <!-- Server -->
                    <div class="group flex flex-col items-center">
                        <div class="p-3 bg-emerald-400/20 rounded-xl group-hover:bg-emerald-400/30 transition-all duration-300 animate-pulse shadow-lg shadow-emerald-400/20"
                            style="animation-delay: 0.5s;">
                            <svg class="w-8 h-8 text-emerald-300 group-hover:text-emerald-200 transition-colors duration-300 drop-shadow-sm"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                        </div>
                        <span
                            class="text-xs text-emerald-300 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-semibold">Serveurs</span>
                    </div>

                    <!-- Code -->
                    <div class="group flex flex-col items-center">
                        <div class="p-3 bg-rose-400/20 rounded-xl group-hover:bg-rose-400/30 transition-all duration-300 animate-pulse shadow-lg shadow-rose-400/20"
                            style="animation-delay: 1s;">
                            <svg class="w-8 h-8 text-rose-300 group-hover:text-rose-200 transition-colors duration-300 drop-shadow-sm"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <span
                            class="text-xs text-rose-300 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-semibold">Développement</span>
                    </div>

                    <!-- Network -->
                    <div class="group flex flex-col items-center">
                        <div class="p-3 bg-blue-400/20 rounded-xl group-hover:bg-blue-400/30 transition-all duration-300 animate-pulse shadow-lg shadow-blue-400/20"
                            style="animation-delay: 1.5s;">
                            <svg class="w-8 h-8 text-blue-300 group-hover:text-blue-200 transition-colors duration-300 drop-shadow-sm"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <span
                            class="text-xs text-blue-300 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-semibold">Réseaux</span>
                    </div>

                    <!-- Security -->
                    <div class="group flex flex-col items-center">
                        <div class="p-3 bg-violet-400/20 rounded-xl group-hover:bg-violet-400/30 transition-all duration-300 animate-pulse shadow-lg shadow-violet-400/20"
                            style="animation-delay: 2s;">
                            <svg class="w-8 h-8 text-violet-300 group-hover:text-violet-200 transition-colors duration-300 drop-shadow-sm"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <span
                            class="text-xs text-violet-300 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-semibold">Sécurité</span>
                    </div>

                    <!-- Shop/Commerce -->
                    <div class="group flex flex-col items-center">
                        <div class="p-3 bg-orange-400/20 rounded-xl group-hover:bg-orange-400/30 transition-all duration-300 animate-pulse shadow-lg shadow-orange-400/20"
                            style="animation-delay: 2.5s;">
                            <svg class="w-8 h-8 text-orange-300 group-hover:text-orange-200 transition-colors duration-300 drop-shadow-sm"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span
                            class="text-xs text-orange-300 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-semibold">Boutique</span>
                    </div>
                </div>

                <!-- Animated Promo Badge -->
                <div x-show="loaded" x-transition:enter="transition ease-out duration-500 transform"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="inline-flex items-center gap-2 mb-8">
                    <span
                        class="inline-block w-2 h-2 bg-cyan-400 rounded-full animate-pulse shadow-lg shadow-cyan-400/50"></span>
                    <span class="text-cyan-300 text-sm font-semibold tracking-wider uppercase drop-shadow-sm">Bienvenue à
                        nos côtés</span>
                </div>

                <!-- Main Heading with Stagger Animation -->
                <div class="mb-6 overflow-hidden">
                    <h1 x-show="loaded" x-transition:enter="transition ease-out duration-700 transform"
                        x-transition:enter-start="opacity-0 translate-y-8"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="text-5xl sm:text-6xl md:text-7xl font-black text-white leading-tight tracking-tight">
                        <span class="block">Excellence</span>
                        <span class="block">
                            <span class="relative">
                                <span
                                    class="absolute inset-0 bg-gradient-to-r from-gold-400 to-gold-600 rounded-lg opacity-20 blur"></span>
                                <span
                                    class="relative bg-gradient-to-r from-gold-300 to-gold-500 bg-clip-text text-transparent">Informatique</span>
                            </span>
                        </span>
                    </h1>
                </div>

                <!-- Subtitle with smooth reveal -->
                <p x-show="loaded" x-transition:enter="transition ease-out duration-700 transform"
                    x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                    style="transition-delay: 200ms"
                    class="text-lg sm:text-xl text-gray-300 leading-relaxed max-w-2xl mb-8">
                    Partenaire de confiance depuis des années, nous offrons des solutions informatiques intégrées,
                    de la maintenance à la numérisation, avec un engagement inébranlable envers l'excellence et
                    l'innovation.
                </p>

                <!-- Animated CTA Buttons -->
                <div x-show="loaded" x-transition:enter="transition ease-out duration-700 transform"
                    x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                    style="transition-delay: 400ms" class="flex flex-col sm:flex-row gap-4 pt-4">

                    <!-- Primary CTA -->
                    <a href="{{ route('services.index') }}"
                        class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-lg overflow-hidden rounded-xl transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                        <!-- Background gradient -->
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-gold-400 to-gold-600 transition-all duration-300"></span>
                        <!-- Hover overlay -->
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-gold-500 to-gold-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        <!-- Text -->
                        <span class="relative flex items-center gap-2 text-slate-900">
                            Découvrir nos services
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </span>
                    </a>

                    <!-- Secondary CTA -->
                    <a href="{{ route('contact.index') }}"
                        class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-lg overflow-hidden rounded-xl transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border-2 border-gold-400">
                        <!-- Background -->
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-gold-400/10 to-transparent group-hover:from-gold-400/20 transition-all duration-300"></span>
                        <!-- Text -->
                        <span
                            class="relative flex items-center gap-2 text-gold-300 group-hover:text-gold-200 transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Nous contacter
                        </span>
                    </a>
                </div>

                <!-- Trust indicators -->
                <div x-show="loaded" x-transition:enter="transition ease-out duration-700 transform"
                    x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                    style="transition-delay: 600ms"
                    class="mt-12 pt-8 border-t border-gold-400/20 flex flex-wrap gap-6 sm:gap-8">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gold-400/10">
                                <svg class="h-6 w-6 text-gold-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Certifié depuis</p>
                            <p class="text-lg font-semibold text-white">2015</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gold-400/10">
                                <svg class="h-6 w-6 text-gold-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Clients satisfaits</p>
                            <p class="text-lg font-semibold text-white">500+</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gold-400/10">
                                <svg class="h-6 w-6 text-gold-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Support 24/7</p>
                            <p class="text-lg font-semibold text-white">Toujours là</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div x-show="loaded" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" style="transition-delay: 800ms"
            class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20">
            <div class="flex flex-col items-center gap-2">
                <span class="text-sm text-gray-400">Voir plus</span>
                <div class="animate-bounce">
                    <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Add CSS Animations -->
    <style>
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes codeGlow {

            0%,
            100% {
                opacity: 0.6;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }
        }

        @keyframes circuitFlow {
            0% {
                stroke-dashoffset: 1000;
            }

            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes dataFlow {
            0% {
                transform: translateX(-100%);
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        @keyframes serverBlink {

            0%,
            100% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-code-glow {
            animation: codeGlow 3s ease-in-out infinite;
        }

        .animate-circuit-flow {
            animation: circuitFlow 4s linear infinite;
        }

        .animate-data-flow {
            animation: dataFlow 2s linear infinite;
        }

        .animate-server-blink {
            animation: serverBlink 2s ease-in-out infinite;
        }

        .animation-delay-0 {
            animation-delay: 0s;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        .bg-grid-pattern {
            background-image:
                linear-gradient(rgba(255, 193, 7, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 193, 7, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        /* Enhanced hover effects */
        .it-icon:hover {
            transform: scale(1.1) rotate(5deg);
            transition: all 0.3s ease;
        }

        /* Typing animation for code elements */
        .typing::after {
            content: '|';
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            50% {
                opacity: 1;
            }

            51%,
            100% {
                opacity: 0;
            }
        }

        /* Particle system */
        .particle {
            position: absolute;
            border-radius: 50%;
            animation: particleFloat 8s ease-in-out infinite;
        }

        @keyframes particleFloat {

            0%,
            100% {
                transform: translateY(0px) translateX(0px);
                opacity: 0.3;
            }

            25% {
                transform: translateY(-20px) translateX(10px);
                opacity: 0.8;
            }

            50% {
                transform: translateY(-10px) translateX(-10px);
                opacity: 0.6;
            }

            75% {
                transform: translateY(-30px) translateX(5px);
                opacity: 0.4;
            }
        }

        /* Commerce-specific animations */
        @keyframes coinSpin {
            0% {
                transform: rotateY(0deg);
            }

            100% {
                transform: rotateY(360deg);
            }
        }

        @keyframes cartBounce {

            0%,
            100% {
                transform: translateY(0px) scale(1);
            }

            50% {
                transform: translateY(-10px) scale(1.05);
            }
        }

        @keyframes priceGlow {

            0%,
            100% {
                text-shadow: 0 0 5px rgba(132, 204, 22, 0.5);
            }

            50% {
                text-shadow: 0 0 20px rgba(132, 204, 22, 0.8), 0 0 30px rgba(132, 204, 22, 0.6);
            }
        }

        @keyframes discountPulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            50% {
                transform: scale(1.1);
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
        }

        .animate-coin-spin {
            animation: coinSpin 3s linear infinite;
        }

        .animate-cart-bounce {
            animation: cartBounce 2s ease-in-out infinite;
        }

        .animate-price-glow {
            animation: priceGlow 2s ease-in-out infinite;
        }

        .animate-discount-pulse {
            animation: discountPulse 2s infinite;
        }
    </style>

    <!-- Services Preview - Kanje Style -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-navy-900">Nos Solutions</h2>
                <p class="mt-4 text-lg text-gray-600">Découvrez comment nous pouvons vous aider à propulser votre
                    entreprise.</p>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Service 1 -->
                <div
                    class="bg-white border-2 border-gray-100 hover:border-gold-400 overflow-hidden rounded-xl transition-all duration-200 hover:shadow-lg">
                    <div class="px-6 py-8">
                        <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-navy-900">Services Numériques</h3>
                        <p class="mt-3 text-sm text-gray-600">Développement web, mobile et transformation digitale.</p>
                    </div>
                </div>

                <!-- Service 2 -->
                <div
                    class="bg-white border-2 border-gray-100 hover:border-gold-400 overflow-hidden rounded-xl transition-all duration-200 hover:shadow-lg">
                    <div class="px-6 py-8">
                        <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-navy-900">Maintenance & Réparation</h3>
                        <p class="mt-3 text-sm text-gray-600">Assistance technique pour vos équipements informatiques.</p>
                    </div>
                </div>

                <!-- Service 3 -->
                <div
                    class="bg-white border-2 border-gray-100 hover:border-gold-400 overflow-hidden rounded-xl transition-all duration-200 hover:shadow-lg">
                    <div class="px-6 py-8">
                        <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-navy-900">Vidéosurveillance</h3>
                        <p class="mt-3 text-sm text-gray-600">Sécurisez vos locaux avec nos solutions de contrôle d'accès.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    @if ($products->count() > 0)
        <section class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-extrabold text-navy-900">Nos Produits</h2>
                    <p class="mt-4 text-lg text-gray-600">Découvrez notre sélection de produits de qualité</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <a href="{{ route('shop.show', $product->slug) }}"
                            class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden relative">
                            <!-- Badge Nouveau -->
                            @if ($product->created_at->diffInDays(now()) < 30)
                                <span
                                    class="absolute top-3 left-3 z-10 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-md uppercase">
                                    Nouveau
                                </span>
                            @endif

                            <div class="bg-gray-50 p-6">
                                @php
                                    $rawPath = $product->image ?: $product->images->first()->path ?? null;
                                    $imgPath = $rawPath ? preg_replace('#^(/?storage/)#', '', $rawPath) : null;
                                    if (
                                        $imgPath &&
                                        \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)
                                    ) {
                                        // Use root-relative path so host/port of current request is preserved
                                        $imgUrl = '/storage/' . ltrim($imgPath, '/');
                                    } elseif ($rawPath && filter_var($rawPath, FILTER_VALIDATE_URL)) {
                                        $imgUrl = $rawPath;
                                    } else {
                                        $imgUrl = $imgPath ? asset('storage/' . ltrim($imgPath, '/')) : null;
                                    }
                                @endphp

                                @if (config('app.debug'))
                                    <!-- IMGURL: {{ $imgUrl ?? 'null' }} -->
                                @endif
                                @if ($imgUrl)
                                    <div class="w-full h-48 sm:h-56 lg:h-48 rounded-lg overflow-hidden bg-white">
                                        <img loading="lazy" src="{{ $imgUrl }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                @else
                                    <div
                                        class="w-full h-48 sm:h-56 lg:h-48 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3
                                    class="text-sm font-medium text-navy-600 hover:text-gold-500 transition-colors duration-200 line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-2xl font-bold text-gray-900">
                                        {{ number_format($product->price, 0, ',', ' ') }} <span class="text-sm">FCFA</span>
                                    </span>
                                </div>
                                @if ($product->stock > 0)
                                    <span class="inline-block mt-2 text-xs font-semibold text-green-600">
                                        En stock
                                    </span>
                                @else
                                    <span class="inline-block mt-2 text-xs font-semibold text-red-600">
                                        Rupture de stock
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12 text-center">
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center px-8 py-4 border-2 border-navy-700 text-base font-bold rounded-lg text-navy-900 bg-white hover:bg-navy-50 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Voir tous les produits
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    @endif
@endsection
