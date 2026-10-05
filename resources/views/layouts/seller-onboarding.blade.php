<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Seller Portal Onboarding — Bazaario')</title>

    <!-- Google Fonts: Space Grotesk, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Theme Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#FFFDF8',
                            slate: '#0F172A',
                            amber: '#F5A623',
                            'amber-dark': '#D98205',
                            green: '#16A34A',
                            muted: '#45464D',
                            outline: '#E2DFD7',
                            surface: '#FFFFFF',
                            card: '#FFFFFF',
                            subtle: '#FAF8F2'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    borderRadius: {
                        'custom': '14px',
                        '14': '14px',
                    },
                    boxShadow: {
                        'subtle': '0 2px 10px rgba(15, 23, 42, 0.04)',
                        'card': '0 4px 20px -2px rgba(15, 23, 42, 0.06)',
                        'modal': '0 25px 50px -12px rgba(15, 23, 42, 0.25)',
                    }
                }
            }
        };
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background-color: #FFFDF8;
            color: #0F172A;
            font-family: 'Inter', sans-serif;
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

    <!-- TOP HEADER / NAV -->
    <header class="border-b border-[#EAE6DC] bg-white/95 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <!-- Brand Vector Asset -->
                    <div class="w-10 h-10 bg-brand-slate rounded-[14px] flex items-center justify-center text-brand-amber font-bold font-heading text-2xl shadow-sm transition-transform group-hover:scale-105">
                        B
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-bold text-xl tracking-tight text-brand-slate">BAZAARIO</span>
                            <span class="bg-[#F5A623]/15 text-[#B45309] font-mono font-bold text-[10px] tracking-wider px-2 py-0.5 rounded-[6px] border border-[#F5A623]/30">SELLER</span>
                        </div>
                        <p class="text-[11px] text-brand-muted hidden sm:block">Smarter Shopping. Local Sellers. Live Auctions.</p>
                    </div>
                </a>
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

    <!-- FLASH TOAST ALERTS -->
    <div class="fixed top-24 right-4 z-50 flex flex-col gap-2 max-w-md w-full pointer-events-none px-4" x-data="{ showSuccess: {{ session('success') ? 'true' : 'false' }}, showError: {{ session('error') ? 'true' : 'false' }}, showWarning: {{ session('warning') ? 'true' : 'false' }}, showInfo: {{ session('info') ? 'true' : 'false' }} }">
        @if(session('success'))
            <div x-show="showSuccess" x-init="setTimeout(() => showSuccess = false, 5000)" class="pointer-events-auto bg-white border border-brand-green/30 rounded-[14px] p-4 shadow-card flex items-start gap-3 transition">
                <span class="material-symbols-outlined text-brand-green text-[22px] shrink-0">check_circle</span>
                <div class="flex-1 text-xs">
                    <p class="font-heading font-bold text-brand-slate">Success</p>
                    <p class="text-brand-muted mt-0.5">{{ session('success') }}</p>
                </div>
                <button type="button" @click="showSuccess = false" class="text-brand-muted hover:text-brand-slate">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-show="showError" x-init="setTimeout(() => showError = false, 6000)" class="pointer-events-auto bg-white border border-red-200 rounded-[14px] p-4 shadow-card flex items-start gap-3 transition">
                <span class="material-symbols-outlined text-red-600 text-[22px] shrink-0">error</span>
                <div class="flex-1 text-xs">
                    <p class="font-heading font-bold text-brand-slate">Notice</p>
                    <p class="text-brand-muted mt-0.5">{{ session('error') }}</p>
                </div>
                <button type="button" @click="showError = false" class="text-brand-muted hover:text-brand-slate">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>
        @endif

        @if(session('warning'))
            <div x-show="showWarning" x-init="setTimeout(() => showWarning = false, 5000)" class="pointer-events-auto bg-white border border-amber-300 rounded-[14px] p-4 shadow-card flex items-start gap-3 transition">
                <span class="material-symbols-outlined text-brand-amber text-[22px] shrink-0">warning</span>
                <div class="flex-1 text-xs">
                    <p class="font-heading font-bold text-brand-slate">Attention</p>
                    <p class="text-brand-muted mt-0.5">{{ session('warning') }}</p>
                </div>
                <button type="button" @click="showWarning = false" class="text-brand-muted hover:text-brand-slate">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div x-show="showInfo" x-init="setTimeout(() => showInfo = false, 5000)" class="pointer-events-auto bg-white border border-blue-200 rounded-[14px] p-4 shadow-card flex items-start gap-3 transition">
                <span class="material-symbols-outlined text-blue-600 text-[22px] shrink-0">info</span>
                <div class="flex-1 text-xs">
                    <p class="font-heading font-bold text-brand-slate">Information</p>
                    <p class="text-brand-muted mt-0.5">{{ session('info') }}</p>
                </div>
                <button type="button" @click="showInfo = false" class="text-brand-muted hover:text-brand-slate">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>
        @endif
    </div>

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
                        Email us at <a href="mailto:support@bazaario.in" class="text-brand-amber font-mono font-bold hover:underline">support@bazaario.in</a> or reach our merchant concierge on WhatsApp at <strong>+91 98765 43210</strong>.
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
                <span class="font-heading font-bold text-brand-slate">BAZAARIO</span>
                <span>&copy; {{ date('Y') }} All rights reserved.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="hover:text-brand-slate transition-colors">Marketplace</a>
                <a href="{{ route('docs.fees-and-commission') }}" class="hover:text-brand-slate transition-colors">Pricing &amp; Commissions</a>
                <a href="{{ Route::has('docs.how-it-works') ? route('docs.how-it-works') : (Route::has('docs.become-a-seller') ? route('docs.become-a-seller') : url('/')) }}" class="hover:text-brand-slate transition-colors">How It Works</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
