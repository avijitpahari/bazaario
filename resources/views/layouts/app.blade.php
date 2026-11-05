<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <title>
        @yield('title', \App\Models\SiteSetting::getSiteName() . ' — ' . \App\Models\SiteSetting::getTagline())
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])



    @stack('styles')
</head>

<body class="bg-canvas-ivory font-body-regular text-body-regular text-on-surface">

    <x-toast />

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

    @include('components.nav')

    <main class="w-full pt-20 bg-canvas-ivory min-h-screen flex flex-col justify-center">
        @yield('content')
    </main>

    @include('components.footer')

    <!-- Global Floating Live Support Chat Widget -->
    <x-support-chat-widget />

    @stack('scripts')
</body>

</html>