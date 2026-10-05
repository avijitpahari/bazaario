<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Platform') — Bazaario Warm Modernist</title>

    <!-- Google Fonts: Plus Jakarta Sans + Inter + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <!-- Tailwind CSS with Warm Modernist Admin Configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#835500",
                        "primary-container": "#f5a623",
                        "on-primary-container": "#644000",
                        "on-primary-fixed": "#291800",
                        "secondary": "#565e74",
                        "secondary-container": "#dae2fd",
                        "tertiary": "#006e2d",
                        "tertiary-container": "#50ce6f",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "background": "#faf8ff",
                        "surface": "#faf8ff",
                        "surface-dim": "#dad9e2",
                        "surface-bright": "#faf8ff",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f4f3fb",
                        "surface-container": "#eeedf6",
                        "surface-container-high": "#e8e7f0",
                        "surface-container-highest": "#e2e2ea",
                        "on-surface": "#1a1b21",
                        "on-surface-variant": "#524534",
                        "outline": "#857462",
                        "outline-variant": "#d7c3ae",
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.875rem",
                        "2xl": "1rem",
                        "full": "9999px"
                    },
                    spacing: {
                        "space-xs": "0.25rem",
                        "space-sm": "0.5rem",
                        "space-md": "1rem",
                        "space-lg": "1.25rem",
                        "space-xl": "1.5rem",
                        "gutter": "1.25rem",
                        "gutter-desktop": "1.5rem",
                    },
                    fontFamily: {
                        "display": ["'Plus Jakarta Sans'", "sans-serif"],
                        "headline-lg": ["'Plus Jakarta Sans'", "sans-serif"],
                        "headline-md": ["'Plus Jakarta Sans'", "sans-serif"],
                        "headline-sm": ["'Plus Jakarta Sans'", "sans-serif"],
                        "body-lg": ["Inter", "sans-serif"],
                        "body-md": ["Inter", "sans-serif"],
                        "body-sm": ["Inter", "sans-serif"],
                        "label-lg": ["'JetBrains Mono'", "monospace"],
                        "label-md": ["'JetBrains Mono'", "monospace"],
                        "label-sm": ["'JetBrains Mono'", "monospace"],
                    }
                }
            }
        };
    </script>
    <!-- Alpine.js for Mobile Drawer & Reactive UI -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

    <!-- Mobile Drawer Overlay Backdrop -->
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 md:hidden transition-opacity"></div>

    <!-- ── AUTHORITATIVE DEEP SLATE SIDEBAR (260px) ── -->
    <aside class="fixed left-0 top-0 h-full w-[260px] bg-[#0F172A] z-50 flex flex-col justify-between border-r border-[#1E293B] -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out"
           :class="{ 'translate-x-0': mobileMenuOpen, '-translate-x-full': !mobileMenuOpen }">
        <div class="flex flex-col">
            <!-- Brand / Logo Area -->
            <div class="p-space-lg border-b border-[#1E293B] flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 210 44" fill="none" class="h-8 w-auto">
                        <rect width="36" height="36" y="4" rx="10" fill="#F5A623"/>
                        <path d="M12 14H24C26.2 14 28 15.8 28 18C28 20.2 26.2 22 24 22M12 22H25C27.2 22 29 23.8 29 26C29 28.2 27.2 30 25 30H12V14Z" stroke="#0F172A" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="27" cy="11" r="3" fill="#0F172A"/>
                        <text x="46" y="29" font-family="'Space Grotesk', sans-serif" font-weight="800" font-size="22" letter-spacing="1.5" fill="#FFFFFF">BAZAARIO</text>
                        <rect x="46" y="34" width="28" height="2" rx="1" fill="#F5A623"/>
                    </svg>
                </a>
                <button @click="mobileMenuOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
                <p class="mt-2 font-body-sm text-[11px] text-[#94A3B8] leading-tight">Smarter Shopping. Local Sellers. Live Auctions.</p>
            </div>

            <!-- Navigation Links -->
            @php
                $currentRoute = Route::currentRouteName() ?? '';
            @endphp
            <nav class="flex flex-col py-space-md px-space-sm gap-0.5 text-xs font-medium">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ $currentRoute === 'admin.dashboard' ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px]">grid_view</span>
                        <span>Dashboard</span>
                    </div>
                </a>

                <!-- Sellers & Approvals Group -->
                @php
                    $isSellersActive = str_starts_with($currentRoute, 'admin.sellers');
                @endphp
                <div class="flex flex-col">
                    <a href="{{ route('admin.sellers.index') }}" 
                       class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ $currentRoute === 'admin.sellers.index' ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">storefront</span>
                            <span>Sellers</span>
                        </div>
                    </a>
                    <div class="flex flex-col pl-9 pr-space-xs gap-0.5 mt-0.5">
                        <a href="{{ route('admin.sellers.index') }}" 
                           class="flex items-center px-space-sm py-1.5 rounded transition-all text-[11px] {{ $currentRoute === 'admin.sellers.index' ? 'text-primary-container font-semibold' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                            All Sellers
                        </a>
                        <a href="{{ route('admin.sellers.approvals') }}" 
                           class="flex items-center justify-between px-space-sm py-1.5 rounded transition-all text-[11px] {{ $currentRoute === 'admin.sellers.approvals' ? 'text-primary-container font-semibold' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
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
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.products') ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                        <span>Products</span>
                    </div>
                </a>

                <!-- Categories -->
                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.categories') ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px]">category</span>
                        <span>Categories</span>
                    </div>
                </a>

                <!-- Orders -->
                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.orders') ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
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
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.auctions') ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px]">bolt</span>
                        <span>Live Auctions</span>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </a>

                <!-- Payouts -->
                <a href="{{ route('admin.payouts.index') }}" 
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.payouts') ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                        <span>Payouts</span>
                    </div>
                </a>

                <!-- Disputes -->
                <a href="{{ route('admin.disputes.index') }}" 
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.disputes') ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px]">balance</span>
                        <span>Disputes</span>
                    </div>
                </a>

                <!-- Customers -->
                <a href="{{ route('admin.customers.index') }}" 
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.customers') ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px]">badge</span>
                        <span>Customers</span>
                    </div>
                </a>

                <!-- Coupons -->
                <a href="{{ route('admin.coupons.index') }}" 
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.coupons') ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px]">local_offer</span>
                        <span>Coupons</span>
                    </div>
                </a>

                <!-- AI Center -->
                <a href="{{ route('admin.settings.ai') }}" 
                   class="flex items-center justify-between px-space-md py-2.5 rounded-lg transition-all {{ str_starts_with($currentRoute, 'admin.settings') ? 'bg-[#1E293B] text-primary-container font-headline-sm border-l-4 border-primary-container pl-3' : 'text-[#94A3B8] hover:text-white hover:bg-[#1E293B]' }}">
                    <div class="flex items-center gap-space-sm">
                        <span class="material-symbols-outlined text-[20px]">psychology</span>
                        <span>AI Engine Ops</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 font-label-sm text-[9px]">v2.5</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom Footer -->
        <div class="p-space-sm border-t border-[#1E293B] flex flex-col gap-2">
            <!-- System Health Pill -->
            <div class="flex items-center justify-between px-space-md py-2 rounded-lg bg-[#0B1120]">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#16A34A] animate-pulse"></span>
                    <span class="font-label-sm text-[11px] text-[#94A3B8]">Cluster Health</span>
                </div>
                <span class="font-label-sm text-[11px] text-[#16A34A] font-bold">99.98%</span>
            </div>

            <!-- Admin Profile & Logout -->
            @php
                $adminUser = Auth::guard('admin')->user();
                $adminName = $adminUser ? $adminUser->name : 'Rajesh Mukherjee';
                $adminInitial = strtoupper(mb_substr($adminName, 0, 2));
            @endphp
            <div class="flex items-center justify-between p-2 rounded-xl bg-[#1E293B]/70">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-primary-container text-[#0F172A] flex items-center justify-center font-display font-bold text-xs shrink-0">
                        {{ $adminInitial }}
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-body-sm text-xs text-white font-medium truncate leading-tight">{{ $adminName }}</span>
                        <span class="font-label-sm text-[10px] text-[#94A3B8] truncate leading-tight mt-0.5">Chief Administrator</span>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[#94A3B8] hover:text-red-400 transition-colors p-1" title="Logout">
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
        <main class="w-full pt-[68px] min-h-screen bg-[#FFFDF8] px-4 md:px-space-xl py-4 md:py-space-xl">
            <!-- Flash Notification Messages -->
            @if(session('success'))
                <div id="flash-success" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 flex items-center justify-between shadow-2xs transition-all animate-fadeIn">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-600 text-[22px]">check_circle</span>
                        <div>
                            <span class="font-headline-sm text-xs font-bold block">Action Completed</span>
                            <span class="text-xs text-emerald-800">{{ session('success') }}</span>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('flash-success').remove()" class="text-emerald-700 hover:text-emerald-900 p-1 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="flash-error" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-900 flex items-center justify-between shadow-2xs transition-all animate-fadeIn">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-rose-600 text-[22px]">error</span>
                        <div>
                            <span class="font-headline-sm text-xs font-bold block">Error Encountered</span>
                            <span class="text-xs text-rose-800">{{ session('error') }}</span>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('flash-error').remove()" class="text-rose-700 hover:text-rose-900 p-1 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div id="flash-errors" class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 flex items-start justify-between shadow-2xs transition-all animate-fadeIn">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 text-[22px] mt-0.5">warning</span>
                        <div>
                            <span class="font-headline-sm text-xs font-bold block mb-1">Validation Requirements</span>
                            <ul class="list-disc list-inside text-xs text-amber-800 space-y-0.5 font-body-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('flash-errors').remove()" class="text-amber-700 hover:text-amber-900 p-1 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
