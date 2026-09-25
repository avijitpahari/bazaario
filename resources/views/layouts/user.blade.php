<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <title>@yield('title', 'Bazaario — Shop Smart, Live Better')</title>

    <!-- Compiled Tailwind CSS & App JS via Vite + Production Fallback -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-dim": "#dbdad5",
                        "card-white": "#FFFFFF",
                        "on-error-container": "#93000a",
                        "on-tertiary-fixed-variant": "#633f00",
                        "primary-alpha-70": "rgba(15, 23, 42, 0.70)",
                        "outline": "#76777d",
                        "on-tertiary-fixed": "#291800",
                        "on-primary-fixed-variant": "#3f465c",
                        "on-secondary-fixed-variant": "#005320",
                        "slate-authority": "#0F172A",
                        "inverse-on-surface": "#f2f1ec",
                        "tertiary": "#000000",
                        "tertiary-fixed": "#ffddb4",
                        "error": "#ba1a1a",
                        "tertiary-fixed-dim": "#ffb955",
                        "surface-variant": "#e4e2de",
                        "on-tertiary-container": "#b57700",
                        "secondary-fixed": "#7ffc97",
                        "ivory-alpha-70": "rgba(255, 253, 248, 0.70)",
                        "on-secondary": "#ffffff",
                        "on-primary": "#ffffff",
                        "amber-action": "#F5A623",
                        "on-primary-container": "#7c839b",
                        "on-secondary-container": "#007230",
                        "surface-container-low": "#f5f3ee",
                        "surface-container": "#efeee9",
                        "tertiary-container": "#291800",
                        "inverse-primary": "#bec6e0",
                        "secondary-container": "#7cf994",
                        "on-secondary-fixed": "#002109",
                        "surface-container-highest": "#e4e2de",
                        "on-background": "#1b1c19",
                        "primary-alpha-20": "rgba(15, 23, 42, 0.20)",
                        "canvas-ivory": "#FFFDF8",
                        "error-container": "#ffdad6",
                        "primary-container": "#131b2e",
                        "on-surface": "#1b1c19",
                        "surface-tint": "#565e74",
                        "on-error": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "outline-variant": "#c6c6cd",
                        "on-primary-fixed": "#131b2e",
                        "background": "#fbf9f4",
                        "status-green": "#16A34A",
                        "primary": "#000000",
                        "on-tertiary": "#ffffff",
                        "secondary-fixed-dim": "#62df7d",
                        "surface-bright": "#fbf9f4",
                        "primary-fixed-dim": "#bec6e0",
                        "primary-fixed": "#dae2fd",
                        "inverse-surface": "#30312e",
                        "primary-alpha-10": "rgba(15, 23, 42, 0.10)",
                        "on-surface-variant": "#45464d",
                        "surface": "#fbf9f4",
                        "surface-container-high": "#eae8e3",
                        "secondary": "#006e2d"
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"
                    },
                    spacing: {
                        "gutter-md": "1.5rem", "section-final-bottom": "4rem", "gutter-sm": "1rem",
                        "section-hero-top": "4rem", "container-max": "72rem", "section-hero-bottom": "2.5rem",
                        "card-padding": "1.25rem", "gutter-lg": "2.5rem", "gutter-xs": "0.5rem",
                        "section-interior-y": "3.5rem", "gutter-xl": "3.5rem", "banner-padding": "2.5rem"
                    },
                    fontFamily: {
                        "label-eyebrow": ["JetBrains Mono"], "body-regular": ["Inter"],
                        "display-hero": ["Space Grotesk"], "title-card": ["Space Grotesk"],
                        "label-micro": ["JetBrains Mono"], "body-small": ["Inter"],
                        "button-text": ["Inter"], "body-lead": ["Inter"],
                        "headline-section": ["Space Grotesk"], "display-hero-mobile": ["Space Grotesk"]
                    },
                    fontSize: {
                        "label-eyebrow": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "500" }],
                        "body-regular": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "display-hero": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "title-card": ["18px", { "lineHeight": "24px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "label-micro": ["10px", { "lineHeight": "14px", "letterSpacing": "0.025em", "fontWeight": "500" }],
                        "body-small": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "button-text": ["16px", { "lineHeight": "24px", "fontWeight": "600" }],
                        "body-lead": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                        "headline-section": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700" }],
                        "display-hero-mobile": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700" }]
                    }
                }
            }
        };
    </script>

    <style>
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
        }
        ::-webkit-scrollbar { display: none; }
    </style>

    @stack('styles')
</head>

<body class="bg-canvas-ivory text-on-surface antialiased selection:bg-amber-action selection:text-slate-authority min-h-screen flex flex-col">

    {{-- Global customer navbar (floating pill style) --}}
    @include('components.nav-user', ['currentRoute' => Route::currentRouteName()])

    {{-- Main content area --}}

    <main class="w-full pt-0 bg-canvas-ivory flex-1 flex flex-col">
        {{-- Flash messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="fixed top-20 right-4 z-50 max-w-sm bg-status-green text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 font-body-small text-body-small">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                {{ session('success') }}
                <button @click="show = false" class="ml-auto"><span class="material-symbols-outlined text-[16px]">close</span></button>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="fixed top-24 right-4 z-50 max-w-sm bg-error text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 font-body-small text-body-small">
                <span class="material-symbols-outlined text-[18px]">error</span>
                {{ session('error') }}
                <button @click="show = false" class="ml-auto"><span class="material-symbols-outlined text-[16px]">close</span></button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <x-footer />

    @stack('scripts')

    {{-- Alpine.js for interactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
