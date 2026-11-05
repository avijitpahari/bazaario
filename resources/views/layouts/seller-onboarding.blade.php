<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Seller Portal Onboarding — ' . \App\Models\SiteSetting::getSiteName())</title>

    <!-- Google Fonts: Outfit, Plus Jakarta Sans, JetBrains Mono, Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Compiled Vite Assets (Tailwind CSS v4 & Bundled Local Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-heading: 'Outfit', 'Plus Jakarta Sans', sans-serif;
            --font-display: 'Outfit', 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
        }

        html {
            font-family: var(--font-sans);
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-feature-settings: 'cv02' 1, 'cv03' 1, 'cv04' 1, 'cv11' 1;
        }

        body {
            background-color: #FFFDF8;
            color: #0F172A;
            font-family: var(--font-sans);
            letter-spacing: -0.012em;
            line-height: 1.5;
        }

        h1, h2, h3, h4, h5, h6, .font-heading, .font-display {
            font-family: var(--font-heading) !important;
            letter-spacing: -0.028em;
            font-feature-settings: 'cv02' 1, 'ss01' 1;
        }

        h1, .text-headline-lg {
            letter-spacing: -0.035em !important;
            font-weight: 800;
        }

        h2, .text-headline-md {
            letter-spacing: -0.028em !important;
            font-weight: 700;
        }

        h3, h4, .text-headline-sm {
            letter-spacing: -0.02em !important;
            font-weight: 700;
        }

        .font-mono, code, kbd, samp {
            font-family: var(--font-mono) !important;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
        }

        input, select, textarea, button {
            font-family: inherit;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        .material-symbols-outlined.fill-1 {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-brand-bg flex flex-col selection:bg-brand-amber selection:text-brand-slate" x-data="{ helpModalOpen: false }">

    <!-- Full-Page CDN & Assets Preloader -->
    <x-preloader />

    <!-- TOP HEADER / NAV -->
    <header class="border-b border-[#EAE6DC] bg-white/95 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <x-brand-logo :link="url('/')" badge="SELLER" />
            </div>

            <!-- Header Controls -->
            <div class="flex items-center gap-3">
                @auth('seller')
                    <div class="hidden sm:flex items-center gap-2 text-xs font-mono text-brand-muted bg-brand-subtle px-3 py-1.5 rounded-[14px] border border-brand-outline">
                        <span class="w-2 h-2 rounded-full bg-brand-green"></span>
                        <span class="truncate max-w-[140px]">{{ Auth::guard('seller')->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-brand-muted hover:text-red-600 transition-colors py-2 px-3">
                            Sign Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-medium text-brand-muted hover:text-brand-slate transition-colors py-2 px-3">
                        Sign In
                    </a>
                @endauth

                <!-- Seller Help Trigger Button -->
                <button type="button" @click="helpModalOpen = true" class="text-xs font-medium bg-brand-subtle hover:bg-brand-outline/40 text-brand-slate border border-brand-outline px-3.5 py-2 rounded-[14px] flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px] text-brand-amber">help</span>
                    <span>Seller Help</span>
                </button>
            </div>
        </div>
    </header>

    <!-- FLASH TOAST ALERTS & GLOBAL ALERT OVERRIDE -->
    <x-toast />

    <!-- MAIN ONBOARDING CONTAINER -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        @yield('content')
    </main>

    <!-- SELLER HELP MODAL -->
    <div x-show="helpModalOpen" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-50 bg-brand-slate/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;" 
         @keydown.escape.window="helpModalOpen = false" 
         @click.self="helpModalOpen = false">
        
        <div class="bg-white border border-brand-outline rounded-[14px] max-w-lg w-full p-6 sm:p-8 shadow-modal transform transition-all space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-brand-outline">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-amber-100 text-brand-amber flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">help</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-heading font-bold text-brand-slate">Seller Onboarding Help</h3>
                        <p class="text-xs text-brand-muted">Guidelines & Merchant Concierge</p>
                    </div>
                </div>
                <button type="button" @click="helpModalOpen = false" class="text-brand-muted hover:text-brand-slate p-1 rounded-lg">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- FAQs -->
            <div class="space-y-4 text-xs text-brand-slate">
                <div class="p-3 bg-brand-subtle rounded-[10px] border border-brand-outline">
                    <p class="font-heading font-bold text-brand-slate">How does verification work?</p>
                    <p class="text-brand-muted mt-1 leading-relaxed">
                        After submitting your details and GPS coordinates, our compliance team reviews your information within 24 to 48 hours before activating live marketplace access.
                    </p>
                </div>
                <div class="p-3 bg-brand-subtle rounded-[10px] border border-brand-outline">
                    <p class="font-heading font-bold text-brand-slate">What are the commission rates?</p>
                    <p class="text-brand-muted mt-1 leading-relaxed">
                        All newly registered sellers receive <strong>0% platform commission for the first 90 days</strong>. Following the promotional period, a transparent 10% rate applies.
                    </p>
                </div>
                <div class="p-3 bg-brand-subtle rounded-[10px] border border-brand-outline">
                    <p class="font-heading font-bold text-brand-slate">Need live support or coordinate help?</p>
                    <p class="text-brand-muted mt-1 leading-relaxed">
                        Email us at <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'support@bazaario.in') }}" class="text-brand-amber font-mono font-bold hover:text-amber-600 transition-colors">{{ \App\Models\SiteSetting::get('contact_email', 'support@bazaario.in') }}</a> or reach our merchant concierge on WhatsApp / Phone at <strong>{{ \App\Models\SiteSetting::get('contact_phone', '+91 800-BAZAARIO') }}</strong>.
                    </p>
                </div>
            </div>

            <div class="pt-2">
                <button type="button" @click="helpModalOpen = false" class="w-full py-3 bg-brand-slate text-white font-heading font-semibold text-xs rounded-[14px] hover:bg-slate-800 transition">
                    Got It, Return to Onboarding
                </button>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="border-t border-[#EAE6DC] bg-white py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-brand-muted">
            <div class="flex items-center gap-2">
                <span class="font-heading font-bold text-brand-slate uppercase">{{ \App\Models\SiteSetting::getSiteName() }}</span>
                <span>&copy; {{ date('Y') }} All rights reserved.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="hover:text-brand-slate transition-colors">Marketplace</a>
                <a href="{{ route('docs.fees-and-commission') }}" class="hover:text-brand-slate transition-colors">Pricing &amp; Commissions</a>
                <a href="{{ route('pages.how-it-works') }}" class="hover:text-brand-slate transition-colors">How It Works</a>
            </div>
        </div>
    </footer>

    <!-- Global Floating Live Support Chat Widget -->
    <x-support-chat-widget />

    @stack('scripts')
</body>
</html>
