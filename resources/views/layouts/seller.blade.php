@php
    $impersonating = session('impersonating');
    if ($impersonating && ($impersonating['panel'] ?? '') === 'seller' && Auth::guard('admin')->check()) {
        $sellerUser = \App\Models\User::find($impersonating['user_id']);
    } else {
        $sellerUser = Auth::guard('seller')->user();
        if (!$sellerUser && Auth::user()?->role === 'seller') {
            $sellerUser = Auth::user();
        }
    }
    // Strict seller validation to prevent customer data leakage (P40)
    if ($sellerUser && $sellerUser->role !== 'seller' && !($impersonating && Auth::guard('admin')->check())) {
        $sellerUser = null;
    }
    $sellerProfile = $sellerUser?->sellerProfile;

    // View variables fallback if set by caller controller
    if (!$sellerProfile && isset($profile) && $profile instanceof \App\Models\SellerProfile) {
        $sellerProfile = $profile;
    }
    if (!$sellerUser && isset($seller) && $seller instanceof \App\Models\User) {
        $sellerUser = $seller;
    }
    if (!$sellerProfile && $sellerUser?->sellerProfile) {
        $sellerProfile = $sellerUser->sellerProfile;
    }
    if (!$sellerUser && $sellerProfile?->user) {
        $sellerUser = $sellerProfile->user;
    }

    // Resolve seller avatar or shop logo URL robustly
    $rawAvatar = $sellerProfile?->logo_path 
        ?? $sellerUser?->profile_image 
        ?? (isset($profile) ? $profile?->logo_path : null) 
        ?? (isset($seller) ? $seller?->profile_image : null);

    $sellerAvatarUrl = null;
    if (!empty($rawAvatar)) {
        if (str_starts_with($rawAvatar, 'http://') || str_starts_with($rawAvatar, 'https://')) {
            $sellerAvatarUrl = $rawAvatar;
        } elseif (str_starts_with($rawAvatar, '/storage/')) {
            $sellerAvatarUrl = asset(ltrim($rawAvatar, '/'));
        } else {
            $sellerAvatarUrl = asset('storage/' . ltrim($rawAvatar, '/'));
        }
    }

    // Dynamic unread notifications count (P18)
    $sellerUnreadNotificationsCount = 0;
    if ($sellerUser) {
        try {
            $sellerUnreadNotificationsCount = (int) \Illuminate\Support\Facades\DB::table('notifications')
                ->where('user_id', $sellerUser->id)
                ->whereNull('read_at')
                ->count();
        } catch (\Throwable $e) {
            $sellerUnreadNotificationsCount = 0;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Seller Workspace — ' . \App\Models\SiteSetting::getSiteName())</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Compiled Tailwind CSS & Bundled Alpine.js via Vite -->
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
            background-color: #F8FAFC;
            color: #0F172A;
            font-family: var(--font-sans);
            letter-spacing: -0.011em;
            line-height: 1.5;
        }

        /* Modernist Headings & Metric Display */
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

        /* Numerical & Monospace Precision */
        .font-mono, code, kbd, samp {
            font-family: var(--font-mono) !important;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
        }

        /* KPI figures and currency amounts */
        .font-headline-lg.font-mono,
        .text-headline-lg.font-mono,
        .font-headline-md.font-mono {
            font-feature-settings: 'tnum' 1, 'zero' 1;
            letter-spacing: -0.03em !important;
        }

        /* Custom Scrollbar for Sleek Aesthetics */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #CBD5E1;
        }

        /* Inputs & Form Elements */
        input, select, textarea, button {
            font-family: inherit;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        .material-symbols-outlined.fill-1 {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 20;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F8FAFC] font-sans antialiased min-h-screen text-slate-900 selection:bg-amber-100 selection:text-amber-900" x-data="{ mobileSidebarOpen: false }">

    <!-- Full-Page CDN & Assets Preloader -->
    <x-preloader />

    <!-- Global Slim Top Progress Bar -->
    <div id="global-page-loader" class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 z-[9999] opacity-0 pointer-events-none transition-opacity duration-150 print:hidden"></div>
    <script>
        window.addEventListener('beforeunload', () => {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.classList.remove('opacity-0');
                loader.classList.add('animate-pulse');
            }
        });
    </script>

    <!-- MOBILE BACKDROP OVERLAY -->
    <div x-cloak x-show="mobileSidebarOpen" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 lg:hidden print:hidden"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false"></div>

    <!-- SIDEBAR: FIXED DESKTOP & RESPONSIVE MOBILE SLIDE-OVER DRAWER -->
    <aside class="fixed left-0 top-0 h-screen w-72 bg-white z-50 flex flex-col justify-between py-4 px-3.5 shadow-[1px_0_16px_rgba(15,23,42,0.03)] border-r border-slate-200/80 transition-transform duration-300 ease-in-out lg:translate-x-0 print:hidden"
           :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        
        <div class="flex flex-col gap-3 min-h-0 flex-1">
            <!-- Brand Logo Header & Mobile Close -->
            <div class="flex items-center justify-between px-1.5 pb-1">
                <x-brand-logo :link="route('seller.dashboard')" badge="MERCHANT" />
                <button type="button" @click="mobileSidebarOpen = false" class="lg:hidden p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-colors" aria-label="Close sidebar drawer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Merchant Store Quick Card -->
            <div class="p-3 bg-gradient-to-br from-slate-50 via-amber-50/20 to-slate-50/60 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl overflow-hidden bg-gradient-to-tr from-amber-500 to-amber-600 text-slate-950 font-heading font-extrabold text-sm flex items-center justify-center shrink-0 shadow-xs ring-2 ring-white">
                        @if($sellerAvatarUrl)
                            <img src="{{ $sellerAvatarUrl }}" 
                                 alt="{{ $sellerProfile?->shop_name ?? $sellerUser?->name ?? 'Shop Logo' }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                            <span class="hidden">{{ strtoupper(substr($sellerProfile?->shop_name ?? $sellerUser?->name ?? 'S', 0, 1)) }}</span>
                        @else
                            <span>{{ strtoupper(substr($sellerProfile?->shop_name ?? $sellerUser?->name ?? 'S', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col min-w-0">
                        <div class="flex items-center gap-1">
                            <span class="text-xs font-heading font-bold text-slate-900 truncate">
                                {{ $sellerProfile?->shop_name ?? $sellerUser?->name ?? 'My Farm Store' }}
                            </span>
                            <span class="material-symbols-outlined text-[14px] text-emerald-600 fill-1 shrink-0" title="Verified Merchant">verified</span>
                        </div>
                        <span class="text-[10px] font-mono text-slate-500 truncate">
                            {{ $sellerProfile?->seller_type ?? 'Farmer' }} • {{ $sellerProfile?->city ?? 'Hyperlocal' }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('shop') }}" target="_blank" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-100/60 rounded-lg transition-colors shrink-0" title="View Public Store">
                    <span class="material-symbols-outlined text-[17px]">open_in_new</span>
                </a>
            </div>

            <!-- Sidebar Navigation Links with Groups -->
            <nav class="flex flex-col gap-1 px-0.5 overflow-y-auto custom-scrollbar flex-1 pr-1" x-data="{ productsOpen: {{ request()->routeIs('seller.products.*') ? 'true' : 'false' }}, auctionsOpen: {{ request()->routeIs('seller.auctions.*') ? 'true' : 'false' }} }">
                
                <!-- SECTION 1: OVERVIEW -->
                <div class="px-2.5 pt-2 pb-1 text-[10px] font-mono uppercase tracking-wider font-bold text-slate-400">
                    Operations
                </div>

                <!-- Dashboard -->
                <a href="{{ route('seller.dashboard') }}" 
                   class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs font-semibold {{ request()->routeIs('seller.dashboard') ? 'bg-amber-500/10 text-amber-900 border-l-4 border-amber-500 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('seller.dashboard') ? 'bg-amber-500/20 text-amber-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <span class="material-symbols-outlined text-[17px] {{ request()->routeIs('seller.dashboard') ? 'fill-1' : '' }}">dashboard</span>
                        </span>
                        <span class="font-heading">Dashboard</span>
                    </div>
                </a>

                <!-- Store Analytics -->
                <a href="{{ route('seller.analytics.index') }}" 
                   class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs font-semibold {{ request()->routeIs('seller.analytics.*') ? 'bg-amber-500/10 text-amber-900 border-l-4 border-amber-500 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('seller.analytics.*') ? 'bg-amber-500/20 text-amber-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <span class="material-symbols-outlined text-[17px] {{ request()->routeIs('seller.analytics.*') ? 'fill-1' : '' }}">analytics</span>
                        </span>
                        <span class="font-heading">Analytics</span>
                    </div>
                </a>

                <!-- Orders -->
                <a href="{{ route('seller.orders.index') }}" 
                   class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs font-semibold {{ request()->routeIs('seller.orders.*') ? 'bg-amber-500/10 text-amber-900 border-l-4 border-amber-500 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('seller.orders.*') ? 'bg-amber-500/20 text-amber-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <span class="material-symbols-outlined text-[17px] {{ request()->routeIs('seller.orders.*') ? 'fill-1' : '' }}">inventory_2</span>
                        </span>
                        <span class="font-heading">Orders</span>
                    </div>
                    <span class="font-mono text-[10px] px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-bold">Live</span>
                </a>

                <!-- SECTION 2: CATALOG & AUCTIONS -->
                <div class="px-2.5 pt-3 pb-1 text-[10px] font-mono uppercase tracking-wider font-bold text-slate-400">
                    Catalog &amp; Sales
                </div>

                <!-- Products Group (Collapsible) -->
                <div class="flex flex-col gap-0.5">
                    <button type="button" @click="productsOpen = !productsOpen" class="w-full group flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 transition-all">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('seller.products.*') ? 'bg-amber-500/20 text-amber-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                                <span class="material-symbols-outlined text-[17px]">shopping_bag</span>
                            </span>
                            <span class="font-heading">Products</span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': productsOpen }">expand_more</span>
                    </button>
                    <div x-show="productsOpen" x-collapse class="pl-10 pr-1 flex flex-col gap-0.5 py-1 text-xs">
                        <a href="{{ route('seller.products.index') }}" class="px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('seller.products.index') ? 'text-amber-800 font-bold bg-amber-50/80' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('seller.products.index') ? 'bg-amber-500' : 'bg-slate-300' }}"></span>
                            <span>All Products</span>
                        </a>
                        <a href="{{ route('seller.products.create') }}" class="px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('seller.products.create') ? 'text-amber-800 font-bold bg-amber-50/80' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('seller.products.create') ? 'bg-amber-500' : 'bg-slate-300' }}"></span>
                            <span>Add Product</span>
                        </a>
                        <a href="{{ route('seller.products.inventory') }}" class="px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('seller.products.inventory') ? 'text-amber-800 font-bold bg-amber-50/80' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('seller.products.inventory') ? 'bg-amber-500' : 'bg-slate-300' }}"></span>
                            <span>Inventory Stock</span>
                        </a>
                    </div>
                </div>

                <!-- Auctions Group (Collapsible) -->
                <div class="flex flex-col gap-0.5">
                    <button type="button" @click="auctionsOpen = !auctionsOpen" class="w-full group flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 transition-all">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('seller.auctions.*') ? 'bg-amber-500/20 text-amber-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                                <span class="material-symbols-outlined text-[17px]">gavel</span>
                            </span>
                            <span class="font-heading">Auctions</span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': auctionsOpen }">expand_more</span>
                    </button>
                    <div x-show="auctionsOpen" x-collapse class="pl-10 pr-1 flex flex-col gap-0.5 py-1 text-xs">
                        <a href="{{ route('seller.auctions.index') }}" class="px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('seller.auctions.index') ? 'text-amber-800 font-bold bg-amber-50/80' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('seller.auctions.index') ? 'bg-amber-500' : 'bg-slate-300' }}"></span>
                            <span>My Auctions</span>
                        </a>
                        <a href="{{ route('seller.auctions.create') }}" class="px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('seller.auctions.create') ? 'text-amber-800 font-bold bg-amber-50/80' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('seller.auctions.create') ? 'bg-amber-500' : 'bg-slate-300' }}"></span>
                            <span>Create Auction</span>
                        </a>
                        <a href="{{ route('seller.auctions.live') }}" class="px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('seller.auctions.live') ? 'text-amber-800 font-bold bg-amber-50/80' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('seller.auctions.live') ? 'bg-amber-500' : 'bg-slate-300' }}"></span>
                            <span>Live Pulse</span>
                        </a>
                        <a href="{{ route('seller.auctions.history') }}" class="px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-2 {{ request()->routeIs('seller.auctions.history') ? 'text-amber-800 font-bold bg-amber-50/80' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('seller.auctions.history') ? 'bg-amber-500' : 'bg-slate-300' }}"></span>
                            <span>Auction History</span>
                        </a>
                    </div>
                </div>

                <!-- SECTION 3: FINANCES & SUPPORT -->
                <div class="px-2.5 pt-3 pb-1 text-[10px] font-mono uppercase tracking-wider font-bold text-slate-400">
                    Finances &amp; Support
                </div>

                <!-- Payouts -->
                <a href="{{ route('seller.payouts.index') }}" 
                   class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs font-semibold {{ request()->routeIs('seller.payouts.*') ? 'bg-amber-500/10 text-amber-900 border-l-4 border-amber-500 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('seller.payouts.*') ? 'bg-amber-500/20 text-amber-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <span class="material-symbols-outlined text-[17px] {{ request()->routeIs('seller.payouts.*') ? 'fill-1' : '' }}">account_balance_wallet</span>
                        </span>
                        <span class="font-heading">Payouts</span>
                    </div>
                    <span class="font-mono text-[10px] px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200/60 font-bold">Ledger</span>
                </a>

                <!-- Customer Inquiries & Support -->
                <a href="{{ route('seller.support.index') }}" 
                   class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs font-semibold {{ request()->routeIs('seller.support.*') ? 'bg-amber-500/10 text-amber-900 border-l-4 border-amber-500 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('seller.support.*') ? 'bg-amber-500/20 text-amber-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <span class="material-symbols-outlined text-[17px] {{ request()->routeIs('seller.support.*') ? 'fill-1' : '' }}">support_agent</span>
                        </span>
                        <span class="font-heading">Inquiries</span>
                    </div>
                    @php
                        $pendingInquiriesCount = \App\Models\SupportTicket::where('seller_id', Auth::guard('seller')->id() ?? Auth::id())->whereIn('status', ['open', 'waiting_seller', 'escalated'])->count();
                    @endphp
                    @if($pendingInquiriesCount > 0)
                        <span class="font-mono text-[10px] px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-bold shadow-xs">{{ $pendingInquiriesCount }}</span>
                    @endif
                </a>

                <!-- SECTION 4: STORE SETTINGS -->
                <div class="px-2.5 pt-3 pb-1 text-[10px] font-mono uppercase tracking-wider font-bold text-slate-400">
                    Configuration
                </div>

                <!-- Shop Profile -->
                <a href="{{ route('seller.account.profile') }}" 
                   class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs font-semibold {{ request()->routeIs('seller.account.profile') ? 'bg-amber-500/10 text-amber-900 border-l-4 border-amber-500 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('seller.account.profile') ? 'bg-amber-500/20 text-amber-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <span class="material-symbols-outlined text-[17px] {{ request()->routeIs('seller.account.profile') ? 'fill-1' : '' }}">storefront</span>
                        </span>
                        <span class="font-heading">Shop Profile</span>
                    </div>
                </a>

                <!-- Settings -->
                <a href="{{ route('seller.account.settings') }}" 
                   class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs font-semibold {{ request()->routeIs('seller.account.settings') ? 'bg-amber-500/10 text-amber-900 border-l-4 border-amber-500 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('seller.account.settings') ? 'bg-amber-500/20 text-amber-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <span class="material-symbols-outlined text-[17px] {{ request()->routeIs('seller.account.settings') ? 'fill-1' : '' }}">settings</span>
                        </span>
                        <span class="font-heading">Settings</span>
                    </div>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom Actions -->
        <div class="flex flex-col gap-1 px-1 pt-3 border-t border-slate-200/80 text-xs">
            <a href="{{ route('pages.how-it-works') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 transition-all font-medium">
                <span class="material-symbols-outlined text-[18px] text-slate-400">help</span>
                <span>Help &amp; Knowledge Base</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-red-600 hover:bg-red-50 hover:text-red-700 transition-all font-semibold font-heading">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN WRAPPER (Offset by Sidebar) -->
    <div class="pl-0 lg:pl-72 print:pl-0">
        <!-- FIXED TOP HEADER -->
        <header class="fixed top-0 left-0 lg:left-72 right-0 h-16 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 shadow-[0_1px_3px_rgba(15,23,42,0.02)] z-40 flex items-center justify-between px-4 sm:px-6 lg:px-8 print:hidden">
            <!-- Mobile Hamburger Toggle (< 1024px) -->
            <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden p-2 -ml-1 mr-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors shrink-0" aria-label="Open sidebar drawer">
                <span class="material-symbols-outlined text-[24px]">menu</span>
            </button>

            <!-- Global Search with Dynamic Platform Shortcut -->
            @php
                $sellerCurrentRoute = request()->route()?->getName() ?? '';
                $sellerSearchRoute = route('seller.products.index');
                if (str_starts_with($sellerCurrentRoute, 'seller.orders')) {
                    $sellerSearchRoute = route('seller.orders.index');
                } elseif (str_starts_with($sellerCurrentRoute, 'seller.payouts')) {
                    $sellerSearchRoute = route('seller.payouts.index');
                }
            @endphp
            <form action="{{ $sellerSearchRoute }}" method="GET" class="flex items-center flex-1 max-w-md">
                <div class="flex items-center w-full px-3 py-1.5 bg-slate-50/80 hover:bg-white focus-within:bg-white rounded-xl shadow-xs border border-slate-200/80 focus-within:border-amber-500 focus-within:ring-2 focus-within:ring-amber-500/20 transition">
                    <span class="material-symbols-outlined text-slate-400 text-[18px] mr-2 shrink-0">search</span>
                    <input id="seller-global-search" type="text" name="search" value="{{ request('search') }}" class="w-full bg-transparent text-xs text-slate-800 placeholder:text-slate-400 outline-none font-sans min-w-0" placeholder="Search orders, products, auctions, payouts...">
                    <div class="flex items-center gap-1.5 shrink-0 ml-1.5">
                        <kbd id="seller-search-shortcut" 
                             onclick="document.getElementById('seller-global-search')?.focus()"
                             class="hidden sm:inline-flex items-center px-1.5 py-0.5 font-mono text-[10px] font-bold text-slate-500 bg-white border border-slate-200/90 rounded-md shadow-2xs cursor-pointer select-none hover:border-slate-300 hover:text-slate-700 transition-colors" 
                             title="Shortcut (Ctrl + K)">Ctrl K</kbd>
                        <button type="submit" class="p-1 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition-colors" title="Execute Search">
                            <span class="material-symbols-outlined text-[17px]">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Header Right Profile Controls -->
            <div class="flex items-center gap-3 sm:gap-4">
                
                <!-- Quick Actions -->
                <div class="hidden sm:flex items-center gap-2">
                    <a href="{{ route('shop') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100/80 hover:bg-slate-200/80 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition-colors">
                        <span class="material-symbols-outlined text-[15px] text-slate-500">storefront</span>
                        <span>View Shop</span>
                    </a>
                    <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-xs font-bold font-heading rounded-xl shadow-xs transition-all">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        <span>Add Product</span>
                    </a>
                </div>

                <!-- Notifications -->
                <a href="{{ route('seller.account.notifications') }}" class="relative p-2 rounded-xl hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors inline-block" title="Notifications">
                    <span class="material-symbols-outlined text-[21px]">notifications</span>
                    @if($sellerUnreadNotificationsCount > 0)
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-500 rounded-full ring-2 ring-white animate-pulse"></span>
                    @endif
                </a>

                <!-- Profile Pill -->
                <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200">
                    <div class="w-8 h-8 rounded-xl overflow-hidden bg-gradient-to-tr from-slate-900 to-slate-800 text-amber-400 flex items-center justify-center font-heading font-bold text-xs shadow-xs ring-1 ring-slate-200 shrink-0">
                        @if($sellerAvatarUrl)
                            <img src="{{ $sellerAvatarUrl }}" 
                                 alt="{{ $sellerProfile?->shop_name ?? $sellerUser?->name ?? 'Shop Logo' }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                            <span class="hidden">{{ strtoupper(substr($sellerProfile?->shop_name ?? $sellerUser?->name ?? 'S', 0, 1)) }}</span>
                        @else
                            <span>{{ strtoupper(substr($sellerProfile?->shop_name ?? $sellerUser?->name ?? 'S', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="hidden md:flex flex-col text-left">
                        <div class="flex items-center gap-1">
                            <span class="text-xs font-heading font-bold text-slate-900 truncate max-w-[130px]">
                                {{ $sellerProfile?->shop_name ?? $sellerUser?->name ?? 'My Shop' }}
                            </span>
                        </div>
                        <span class="font-mono text-[10px] text-emerald-600 font-semibold flex items-center gap-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Active Seller
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- FLASH TOAST ALERTS & GLOBAL ALERT OVERRIDE -->
        <x-toast />

        <!-- MAIN OPERATING CANVAS -->
        <main class="relative pt-24 pb-16 bg-[#F8FAFC] min-h-screen w-full px-4 sm:px-6 lg:px-8 print:pt-0 print:pb-0 print:px-0 print:bg-white print:min-h-0">
            <div class="max-w-[1400px] mx-auto print:max-w-none print:w-full">
                <x-seller.compliance-notice :profile="$sellerProfile" />
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Adapt keyboard shortcut badge for Mac vs Windows/Linux
            var isMac = typeof navigator !== 'undefined' && /Mac|iPhone|iPad|iPod/i.test(navigator.userAgent || navigator.platform || '');
            var badge = document.getElementById('seller-search-shortcut');
            if (badge && isMac) {
                badge.textContent = '⌘K';
                badge.title = 'Shortcut (⌘ + K)';
            }

            // Global shortcut listener: Ctrl+K / Cmd+K focuses search
            window.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
                    e.preventDefault();
                    var searchInput = document.getElementById('seller-global-search');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
            });
        });
    </script>

    @stack('scripts')

    {{-- Admin Impersonation Banner --}}
    <x-impersonation-bar />

    <!-- Global Floating Live Support Chat Widget -->
    <x-support-chat-widget />
</body>
</html>
