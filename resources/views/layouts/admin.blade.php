<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Platform') — {{ \App\Models\SiteSetting::getSiteName() }} Warm Modernist</title>

    <!-- Google Fonts: Plus Jakarta Sans + Outfit + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>

    <!-- Compiled Vite Assets (Tailwind CSS v4 & Bundled Local Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f0f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#FFFDF8] font-body-md text-on-surface antialiased selection:bg-amber-100 selection:text-amber-900" x-data="{ mobileMenuOpen: false }">

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

    <!-- Mobile Drawer Overlay Backdrop -->
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 md:hidden transition-opacity"></div>

    <!-- ── AUTHORITATIVE DEEP SLATE SIDEBAR (260px) ── -->
    <aside class="fixed left-0 top-0 h-full w-[260px] bg-[#0F172A] z-50 flex flex-col justify-between border-r border-[#1E293B] -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out"
           :class="{ 'translate-x-0': mobileMenuOpen, '-translate-x-full': !mobileMenuOpen }">
        <div class="flex flex-col flex-1 min-h-0 overflow-y-auto overflow-x-hidden">
            <!-- Brand / Logo Area -->
            <div class="p-5 border-b border-[#1E293B] shrink-0">
                <div class="flex items-center justify-between">
                    <x-brand-logo theme="dark" :link="route('admin.dashboard')" badge="ADMIN" :showTagline="true" />
                    <button @click="mobileMenuOpen = false" class="md:hidden text-slate-400 hover:text-white p-1 ml-2">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
            </div>

            <!-- Navigation Links -->
            @php
                $currentRoute = Route::currentRouteName() ?? '';
            @endphp
            <nav class="flex flex-col py-3 px-3 gap-1 text-xs font-medium">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ $currentRoute === 'admin.dashboard' ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">grid_view</span>
                        <span>Dashboard</span>
                    </div>
                </a>

                <!-- Site Analytics -->
                <a href="{{ route('admin.analytics.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.analytics') ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">analytics</span>
                        <span>Analytics</span>
                    </div>
                </a>

                <!-- Sellers & Approvals Group -->
                @php
                    $isSellersActive = str_starts_with($currentRoute, 'admin.sellers');
                @endphp
                <div class="flex flex-col">
                    <a href="{{ route('admin.sellers.index') }}" 
                       class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ $currentRoute === 'admin.sellers.index' ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px]">storefront</span>
                            <span>Sellers</span>
                        </div>
                    </a>
                    <div class="flex flex-col pl-9 pr-1 gap-1 mt-1">
                        <a href="{{ route('admin.sellers.index') }}" 
                           class="flex items-center px-2 py-1.5 rounded-md transition-all text-[11px] {{ $currentRoute === 'admin.sellers.index' ? 'text-amber-400 font-bold bg-amber-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            All Sellers
                        </a>
                        <a href="{{ route('admin.sellers.approvals') }}" 
                           class="flex items-center justify-between px-2 py-1.5 rounded-md transition-all text-[11px] {{ $currentRoute === 'admin.sellers.approvals' ? 'text-amber-400 font-bold bg-amber-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <span>Seller Approval</span>
                            @php $pendingKycCount = \App\Models\SellerProfile::where('status', 'pending')->count(); @endphp
                            @if($pendingKycCount > 0)
                                <span class="px-1.5 py-0.5 rounded-full bg-primary-container text-on-primary-container font-mono text-[10px] font-bold">{{ $pendingKycCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Products -->
                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.products') ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                        <span>Products</span>
                    </div>
                </a>

                <!-- Categories -->
                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.categories') ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">category</span>
                        <span>Categories</span>
                    </div>
                </a>

                <!-- Orders -->
                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.orders') ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                        <span>Orders</span>
                    </div>
                    @php $liveOrdersCount = \App\Models\Order::whereIn('order_status', ['pending', 'processing'])->count(); @endphp
                    @if($liveOrdersCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full bg-[#334155] text-surface-container-lowest font-mono text-[10px]">{{ $liveOrdersCount }}</span>
                    @endif
                </a>

                <!-- Live Auctions -->
                <a href="{{ route('admin.auctions.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.auctions') ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">bolt</span>
                        <span>Live Auctions</span>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </a>

                <!-- Payouts -->
                <a href="{{ route('admin.payouts.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.payouts') ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                        <span>Payouts</span>
                    </div>
                </a>

                <!-- Disputes -->
                <a href="{{ route('admin.disputes.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.disputes') ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">balance</span>
                        <span>Disputes</span>
                    </div>
                </a>

                <!-- Customers -->
                <a href="{{ route('admin.customers.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.customers') ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">badge</span>
                        <span>Customers</span>
                    </div>
                </a>

                <!-- Coupons -->
                <a href="{{ route('admin.coupons.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.coupons') ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">local_offer</span>
                        <span>Coupons</span>
                    </div>
                </a>

                <!-- Platform Settings -->
                <a href="{{ route('admin.settings.index') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ ($currentRoute === 'admin.settings.index' || str_starts_with($currentRoute, 'admin.settings.')) && $currentRoute !== 'admin.settings.ai' ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">settings</span>
                        <span>Platform Settings</span>
                    </div>
                </a>

                <!-- AI Center -->
                <a href="{{ route('admin.settings.ai') }}" 
                   class="flex items-center justify-between py-2.5 rounded-lg transition-all {{ $currentRoute === 'admin.settings.ai' ? 'bg-amber-500/15 text-amber-400 font-bold border-l-4 border-amber-400 pl-3 pr-3 rounded-r-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 px-3' }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]">psychology</span>
                        <span>AI Engine Ops</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-mono text-[9px] font-bold">v2.5</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom Footer -->
        <div class="p-4 border-t border-[#1E293B] flex flex-col gap-2 shrink-0 bg-[#0F172A]">
            <!-- System Health Pill -->
            <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-[#0B1120] border border-slate-800/60">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[11px] text-slate-400 font-medium">Cluster Health</span>
                </div>
                <span class="text-[11px] text-emerald-400 font-bold font-mono">99.98%</span>
            </div>

            <!-- Admin Profile & Logout -->
            @php
                $adminUser = Auth::guard('admin')->user();
                $adminName = $adminUser ? $adminUser->name : 'Rajesh Mukherjee';
                $adminInitial = strtoupper(mb_substr($adminName, 0, 2));
            @endphp
            <div class="flex items-center justify-between p-2 rounded-xl bg-[#1E293B]/70 border border-slate-800/40">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-amber-400 text-slate-900 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                        {{ $adminInitial }}
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs text-white font-medium truncate leading-tight">{{ $adminName }}</span>
                        <span class="text-[10px] text-slate-400 truncate leading-tight mt-0.5">Chief Administrator</span>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors p-1" title="Logout">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ── MAIN WRAPPER ── -->
    <div class="pl-0 md:pl-[260px]">
        <!-- ── STICKY TOP HEADER (68px) ── -->
        <header class="fixed top-0 left-0 md:left-[260px] right-0 h-[68px] bg-white/90 backdrop-blur-md z-40 border-b border-[rgba(15,23,42,0.08)] flex items-center justify-between px-4 md:px-space-xl">
            <!-- Mobile Hamburger Button & Working Search Bar -->
            <div class="flex items-center gap-2.5 w-full max-w-lg">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 shrink-0 focus:outline-none" aria-label="Toggle Navigation">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
                @php
                    $searchRoute = route('admin.products.index');
                    if (request()->routeIs('admin.sellers.*')) {
                        $searchRoute = route('admin.sellers.index');
                    } elseif (request()->routeIs('admin.orders.*')) {
                        $searchRoute = route('admin.orders.index');
                    } elseif (request()->routeIs('admin.customers.*')) {
                        $searchRoute = route('admin.customers.index');
                    }
                @endphp
                <form action="{{ $searchRoute }}" method="GET" class="relative w-full flex items-center">
                    <span class="material-symbols-outlined absolute left-3 text-[20px] text-slate-400 pointer-events-none">search</span>
                    <input name="search" 
                           value="{{ request('search') }}"
                           class="w-full pl-10 pr-12 py-2 bg-white border border-[rgba(15,23,42,0.15)] rounded-xl font-body-sm text-xs text-[#0F172A] placeholder:text-slate-400 focus:outline-none focus:border-[#0F172A] transition-all" 
                           placeholder="Search products, sellers, orders, SKUs..." 
                           type="text"/>
                    <button type="submit" class="absolute right-2 p-1 text-slate-400 hover:text-[#0F172A] transition-colors" title="Execute Search">
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </form>
            </div>

            <!-- Top Header Actions -->
            <div class="flex items-center gap-2 sm:gap-space-md ml-2 shrink-0">
                <!-- Live Auctions Indicator -->
                <a href="{{ route('admin.auctions.index') }}" class="hidden lg:flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/60 hover:bg-emerald-100 transition-colors">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="font-label-sm text-[11px] font-semibold text-emerald-700">Live Auctions</span>
                </a>

                <!-- Notifications -->
                <button class="relative p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#F5A623] ring-2 ring-white"></span>
                </button>

                <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                <!-- Admin Profile Pill & Top Logout -->
                <div class="flex items-center gap-2.5 pl-1 shrink-0">
                    <div class="w-8 h-8 rounded-full bg-[#0F172A] text-[#F5A623] flex items-center justify-center font-display font-bold text-xs ring-1 ring-slate-200 shrink-0">
                        {{ $adminInitial }}
                    </div>
                    <div class="hidden xl:flex flex-col text-left">
                        <span class="font-body-sm text-xs font-bold text-[#0F172A] leading-tight">{{ $adminName }}</span>
                        <span class="font-label-sm text-[10px] text-slate-500 leading-tight">Chief Administrator</span>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Logout">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- ── CONTENT CANVAS ── -->
        <main class="w-full min-h-screen bg-[#FFFDF8] pt-24 pb-16 px-4 md:px-8 lg:px-10">
            @yield('content')
        </main>
    </div>

    <!-- Admin Impersonation Persistent Banner -->
    <x-impersonation-bar />

    @stack('scripts')
</body>
</html>
