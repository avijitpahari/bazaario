<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600&amp;family=Space+Grotesk:wght@500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

    <title>
        @yield('title', 'Bazaario — Shop Smart, Live Better')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'amber-action': '#F5A623',
                        'slate-authority': '#0F172A',
                    }
                }
            }
        }
    </script>



    @stack('styles')
</head>

<body class="bg-canvas-ivory font-body-regular text-body-regular text-on-surface">

    @include('components.navbar')

    <main class="w-full pt-20 bg-canvas-ivory min-h-screen flex flex-col justify-center">
        @yield('content')
    </main>

    @include('components.footer')

    @stack('scripts')
</body>

</html>