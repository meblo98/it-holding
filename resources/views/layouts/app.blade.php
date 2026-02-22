<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

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
                        // Lighter gray palette for less harsh UI
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
                        },
                        // Brighter, more vibrant red palette (closer to Tailwind defaults)
                        red: {
                            50: '#fff5f5',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                        // Keep legacy color names (indigo/blue/green/yellow) mapped to red palette
                        indigo: {
                            50: '#fff5f5',
                            100: '#fed7d7',
                            200: '#feb2b2',
                            300: '#fc8181',
                            400: '#f56565',
                            500: '#e53935',
                            600: '#c62828',
                            700: '#b71c1c',
                            800: '#8e0000',
                            900: '#650000',
                        },
                        blue: {
                            50: '#fff5f5',
                            100: '#fed7d7',
                            200: '#feb2b2',
                            300: '#fc8181',
                            400: '#f56565',
                            500: '#e53935',
                            600: '#c62828',
                            700: '#b71c1c',
                            800: '#8e0000',
                            900: '#650000',
                        },
                        green: {
                            50: '#fff5f5',
                            100: '#fed7d7',
                            200: '#feb2b2',
                            300: '#fc8181',
                            400: '#f56565',
                            500: '#e53935',
                            600: '#c62828',
                            700: '#b71c1c',
                            800: '#8e0000',
                            900: '#650000',
                        },
                        yellow: {
                            50: '#fff5f5',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
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
