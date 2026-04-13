<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) | IT-Holding Sénégal</title>
    
    <!-- Primary Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'IT-Holding est votre partenaire de confiance au Sénégal pour toutes vos solutions numériques : maintenance informatique, infrastructure réseau, vidéosurveillance et vente de matériel.')">
    <meta name="keywords" content="@yield('meta_keywords', 'IT-Holding, maintenance informatique Sénégal, infrastructure réseau Dakar, vidéosurveillance IP, vente matériel informatique, solutions numériques Sénégal')">
    <meta name="author" content="IT-Holding">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', config('app.name')) | IT-Holding Sénégal">
    <meta property="og:description" content="@yield('meta_description', 'IT-Holding est votre partenaire de confiance au Sénégal pour toutes vos solutions numériques.')">
    <meta property="og:image" content="@yield('og_image', asset('logo.jpeg'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', config('app.name')) | IT-Holding Sénégal">
    <meta property="twitter:description" content="@yield('meta_description', 'IT-Holding est votre partenaire de confiance au Sénégal pour toutes vos solutions numériques.')">
    <meta property="twitter:image" content="@yield('og_image', asset('logo.jpeg'))">

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "IT-Holding",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('logo.jpeg') }}",
        "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "+221 XX XXX XX XX",
            "contactType": "customer service",
            "areaServed": "SN",
            "availableLanguage": "French"
        },
        "sameAs": [
            "https://www.facebook.com/itholding",
            "https://www.linkedin.com/company/itholding",
            "https://twitter.com/itholding"
        ]
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "url": "{{ url('/') }}",
        "name": "IT-Holding Sénégal",
        "potentialAction": {
            "@@type": "SearchAction",
            "target": "{{ url('/shop?q={search_term_string}') }}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            50: '#f1f5f9',
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
                            50: '#fffbeb',
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
                        gray: {
                            50: '#fafafa',
                            100: '#f4f4f5',
                            200: '#e6e7eb',
                            300: '#d1d5db',
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Dark Mode Script -->
    <script>
        // Initialize dark mode from localStorage before page renders
        if (localStorage.getItem('darkMode') === 'true' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        // Toggle dark mode function
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('darkMode', isDark);
        }
    </script>

    <!-- Alpine.js (CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Cross-browser fixes for text gradients and color rendering (improves Firefox) */
        :root {
            color-scheme: light dark;
        }

        /* Ensure background-clip:text works in Firefox/Chrome and text becomes transparent to show gradient */
        .bg-clip-text,
        .text-gradient {
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
        }

        /* Ensure SVG icons inherit color consistently */
        svg {
            color: inherit;
        }
    </style>
    @stack('styles')
</head>

<body
    class="antialiased bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-red-400 flex flex-col min-h-screen transition-colors duration-200">

    @include('layouts.navigation')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('layouts.footer')

    @stack('scripts')
</body>

</html>
