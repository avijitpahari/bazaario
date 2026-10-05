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
    <title>@yield('title', 'Seller Workspace — Bazaario')</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Compiled Tailwind CSS & Bundled Alpine.js via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-color: #fbf9f4;
            color: #1b1c19;
            font-family: 'Inter', sans-serif;
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
<body class="bg-surface font-sans antialiased min-h-screen" x-data="{ mobileSidebarOpen: false }">

    <!-- MOBILE BACKDROP OVERLAY -->
    <div x-cloak x-show="mobileSidebarOpen" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false"></div>

    <!-- SIDEBAR: FIXED DESKTOP & RESPONSIVE MOBILE SLIDE-OVER DRAWER -->
    <aside class="fixed left-0 top-0 h-screen w-72 bg-surface-container-low z-50 flex flex-col justify-between py-4 px-3 shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-r border-[#E2DFD7]/60 transition-transform duration-300 ease-in-out lg:translate-x-0"
           :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="flex flex-col gap-4">
            <!-- Brand Logo Header & Mobile Close -->
            <div class="flex items-center justify-between px-1">
                <a href="{{ route('seller.dashboard') }}" class="px-2 py-2 flex items-center gap-3 group">
                    <div class="w-9 h-9 bg-primary rounded-[12px] flex items-center justify-center text-brand-amber font-heading font-bold text-xl shadow-sm transition-transform group-hover:scale-105">
                        B
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading text-lg font-bold tracking-tight text-primary leading-tight">BAZAARIO</span>
                        <span class="font-mono text-[10px] tracking-wider uppercase text-secondary font-bold">SELLER CENTER</span>
                    </div>
                </a>
                <button type="button" @click="mobileSidebarOpen = false" class="lg:hidden p-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-xl transition-colors" aria-label="Close sidebar drawer">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="flex flex-col gap-1 px-1 overflow-y-auto max-h-[calc(100vh-260px)]" x-data="{ productsOpen: {{ request()->routeIs('seller.products.*') ? 'true' : 'false' }}, auctionsOpen: {{ request()->routeIs('seller.auctions.*') ? 'true' : 'false' }} }">
                
                <!-- Dashboard -->
                <a href="{{ route('seller.dashboard') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-[12px] transition-all text-sm {{ request()->routeIs('seller.dashboard') ? 'bg-secondary-container text-on-secondary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('seller.dashboard') ? 'fill-1' : '' }}">dashboard</span>
                        <span class="font-heading">Dashboard</span>
                    </div>
                </a>

                <!-- Orders -->
                <a href="{{ route('seller.orders.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-[12px] transition-all text-sm {{ request()->routeIs('seller.orders.*') ? 'bg-secondary-container text-on-secondary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('seller.orders.*') ? 'fill-1' : '' }}">inventory_2</span>
                        <span class="font-heading">Orders</span>
                    </div>
                    <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-surface-container-highest text-on-surface font-semibold">Live</span>
                </a>

                <!-- Products Group (Collapsible) -->
                <div class="flex flex-col gap-0.5">
                    <button type="button" @click="productsOpen = !productsOpen" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[12px] text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                            <span class="font-heading">Products</span>
                        </div>
                        <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="{ 'rotate-180': productsOpen }">expand_more</span>
                    </button>
                    <div x-show="productsOpen" x-collapse class="pl-8 pr-1 flex flex-col gap-1 py-1 space-y-0.5 text-xs">
                        <a href="{{ route('seller.products.index') }}" class="px-3 py-2 rounded-lg font-medium text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors {{ request()->routeIs('seller.products.index') ? 'text-secondary font-bold bg-surface-container' : '' }}">
                            All Products
                        </a>
                        <a href="{{ route('seller.products.create') }}" class="px-3 py-2 rounded-lg font-medium text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors {{ request()->routeIs('seller.products.create') ? 'text-secondary font-bold bg-surface-container' : '' }}">
                            Add Product
                        </a>
                        <a href="{{ route('seller.products.inventory') }}" class="px-3 py-2 rounded-lg font-medium text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors {{ request()->routeIs('seller.products.inventory') ? 'text-secondary font-bold bg-surface-container' : '' }}">
                            Inventory Stock
                        </a>
                    </div>
                </div>

                <!-- Auctions Group (Collapsible) -->
                <div class="flex flex-col gap-0.5">
                    <button type="button" @click="auctionsOpen = !auctionsOpen" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[12px] text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px]">gavel</span>
                            <span class="font-heading">Auctions</span>
                        </div>
                        <span class="material-symbols-outlined text-[18px] transition-transform duration-200" :class="{ 'rotate-180': auctionsOpen }">expand_more</span>
                    </button>
                    <div x-show="auctionsOpen" x-collapse class="pl-8 pr-1 flex flex-col gap-1 py-1 space-y-0.5 text-xs">
                        <a href="{{ route('seller.auctions.index') }}" class="px-3 py-2 rounded-lg font-medium text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors {{ request()->routeIs('seller.auctions.index') ? 'text-secondary font-bold bg-surface-container' : '' }}">
                            My Auctions
                        </a>
                        <a href="{{ route('seller.auctions.create') }}" class="px-3 py-2 rounded-lg font-medium text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors {{ request()->routeIs('seller.auctions.create') ? 'text-secondary font-bold bg-surface-container' : '' }}">
                            Create Auction
                        </a>
                        <a href="{{ route('seller.auctions.live') }}" class="px-3 py-2 rounded-lg font-medium text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors {{ request()->routeIs('seller.auctions.live') ? 'text-secondary font-bold bg-surface-container' : '' }}">
                            Live Pulse
                        </a>
                        <a href="{{ route('seller.auctions.history') }}" class="px-3 py-2 rounded-lg font-medium text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors {{ request()->routeIs('seller.auctions.history') ? 'text-secondary font-bold bg-surface-container' : '' }}">
                            Auction History
                        </a>
                    </div>
                </div>

                <!-- Payouts -->
                <a href="{{ route('seller.payouts.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-[12px] transition-all text-sm {{ request()->routeIs('seller.payouts.*') ? 'bg-secondary-container text-on-secondary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('seller.payouts.*') ? 'fill-1' : '' }}">account_balance_wallet</span>
                        <span class="font-heading">Payouts</span>
                    </div>
                    <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-surface-container-highest text-secondary font-bold">Ledger</span>
                </a>

                <!-- Shop Profile -->
                <a href="{{ route('seller.account.profile') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-[12px] transition-all text-sm {{ request()->routeIs('seller.account.profile') ? 'bg-secondary-container text-on-secondary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('seller.account.profile') ? 'fill-1' : '' }}">storefront</span>
                        <span class="font-heading">Shop Profile</span>
                    </div>
                </a>

                <!-- Settings -->
                <a href="{{ route('seller.account.settings') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-[12px] transition-all text-sm {{ request()->routeIs('seller.account.settings') ? 'bg-secondary-container text-on-secondary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('seller.account.settings') ? 'fill-1' : '' }}">settings</span>
                        <span class="font-heading">Settings</span>
                    </div>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom Actions -->
        <div class="flex flex-col gap-1 px-1 pt-3 border-t border-surface-container-highest text-xs">
            <a href="{{ Route::has('docs.how-it-works') ? route('docs.how-it-works') : (Route::has('docs.become-a-seller') ? route('docs.become-a-seller') : url('/')) }}" class="flex items-center gap-2 px-3 py-2 rounded-[10px] text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all">
                <span class="material-symbols-outlined text-[18px]">help</span>
                <span class="font-medium">Help &amp; Support</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-[10px] text-error hover:bg-error-container hover:text-on-error-container transition-all">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span class="font-medium font-heading">Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN WRAPPER (Offset by Sidebar) -->
    <div class="pl-0 lg:pl-72">
        <!-- FIXED TOP HEADER -->
        <header class="fixed top-0 left-0 lg:left-72 right-0 h-16 bg-surface/85 backdrop-blur-xl border-b border-[#E2DFD7]/60 shadow-[0_1px_8px_rgba(0,0,0,0.02)] z-40 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Mobile Hamburger Toggle (< 1024px) -->
            <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden p-2 -ml-1 mr-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-xl transition-colors shrink-0" aria-label="Open sidebar drawer">
                <span class="material-symbols-outlined text-[24px]">menu</span>
            </button>

            <!-- Global Search with ⌘K -->
            <form action="{{ route('seller.products.index') }}" method="GET" class="flex items-center flex-1 max-w-lg">
                <div class="flex items-center w-full px-3.5 py-2 bg-surface-container-lowest rounded-[12px] shadow-[0_1px_4px_rgba(0,0,0,0.02)] border border-surface-container-highest focus-within:border-brand-amber transition">
                    <span class="material-symbols-outlined text-on-surface-variant text-[20px] mr-2">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-transparent text-xs text-on-surface placeholder:text-on-surface-variant outline-none font-sans" placeholder="Search orders, products, auctions, payouts...">
                    <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 font-mono text-[10px] bg-surface-container text-on-surface-variant rounded border border-surface-container-high">⌘K</kbd>
                </div>
            </form>

            <!-- Header Right Profile Controls -->
            <div class="flex items-center gap-4">
                <!-- Notifications (P18, P27: Wired with dynamic badge count) -->
                <a href="{{ route('seller.account.notifications') }}" class="relative p-2 rounded-[10px] hover:bg-surface-container-high text-on-surface-variant transition-colors inline-block" title="Notifications">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    @if($sellerUnreadNotificationsCount > 0)
                        <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 bg-brand-amber text-primary rounded-full text-[10px] font-mono font-bold flex items-center justify-center ring-2 ring-white shadow-xs">
                            {{ $sellerUnreadNotificationsCount > 9 ? '9+' : $sellerUnreadNotificationsCount }}
                        </span>
                    @endif
                </a>

                <!-- Profile Pill -->
                <div class="flex items-center gap-3 pl-4 border-l border-surface-container-highest">
                    <div class="w-8 h-8 rounded-full bg-primary text-brand-amber flex items-center justify-center font-heading font-bold text-sm shadow-sm">
                        {{ strtoupper(substr($sellerProfile?->shop_name ?? $sellerUser?->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="flex flex-col text-left">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-heading font-bold text-on-surface truncate max-w-[140px]">
                                {{ $sellerProfile?->shop_name ?? $sellerUser?->name ?? 'My Shop' }}
                            </span>
                            <span class="material-symbols-outlined text-[15px] text-on-tertiary-container fill-1" title="Verified Merchant">check_circle</span>
                        </div>
                        <div class="flex items-center gap-1 text-[11px] text-on-surface-variant">
                            <span>{{ $sellerProfile?->seller_type ?? 'Farmer' }}</span>
                            <span class="text-outline-variant font-bold">•</span>
                            <span class="font-mono text-[10px] uppercase text-on-tertiary-container font-semibold">Verified</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- FLASH TOAST ALERTS -->
        <div class="fixed top-20 right-8 z-50 flex flex-col gap-2 max-w-md w-full pointer-events-none" x-data="{ showSuccess: {{ session('success') ? 'true' : 'false' }}, showError: {{ session('error') ? 'true' : 'false' }}, showWarning: {{ session('warning') ? 'true' : 'false' }}, showInfo: {{ session('info') ? 'true' : 'false' }} }">
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

        <!-- MAIN OPERATING CANVAS -->
        <main class="relative pt-16 bg-surface min-h-screen w-full px-6 sm:px-8 py-8">
            <div class="max-w-[1400px] mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')

    {{-- Admin Impersonation Banner --}}
    <x-impersonation-bar />
</body>
</html>
