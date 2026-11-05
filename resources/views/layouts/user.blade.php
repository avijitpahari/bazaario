<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    <title>@yield('title', \App\Models\SiteSetting::getSiteName() . ' — ' . \App\Models\SiteSetting::getTagline())</title>

    <!-- Compiled Tailwind CSS & App JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
        }
        ::-webkit-scrollbar { display: none; }
    </style>

    @stack('styles')
</head>

<body class="bg-canvas-ivory text-on-surface antialiased selection:bg-amber-action selection:text-slate-authority min-h-screen flex flex-col">

    <!-- Global Toast Notifications & Alert Interceptor -->
    <x-toast />

    <!-- Full-Page CDN & Assets Preloader -->
    <x-preloader />

    <!-- Global Slim Top Progress Bar -->
    <div id="global-page-loader" class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 z-[9999] opacity-0 pointer-events-none transition-opacity duration-150"></div>
    <script>
        window.addEventListener('beforeunload', () => {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.classList.remove('opacity-0');
                loader.classList.add('animate-pulse');
            }
        });
    </script>

    {{-- Global customer navbar (floating pill style) --}}
    @include('components.nav-user', ['currentRoute' => Route::currentRouteName()])

    {{-- Main content area --}}

    <main class="w-full pt-0 bg-canvas-ivory flex-1 flex flex-col">
        @yield('content')
    </main>

    {{-- Footer --}}
    <x-footer />

    <!-- Global Floating Live Support Chat Widget -->
    <x-support-chat-widget />

    @stack('scripts')

    {{-- Admin Impersonation Banner --}}
    <x-impersonation-bar />
</body>

</html>
