<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Seller Portal — Bazaario')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        seller: {
                            gold: '#f59e0b',
                            dark: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-800" x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

        <!-- Left Navigation Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">
            
            <!-- Sidebar Header / Brand -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800">
                <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 font-black text-xl shadow-lg shadow-amber-500/20">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <div class="font-heading font-bold text-lg tracking-tight text-white flex items-center gap-1.5">
                            Bazaario
                            <span class="text-[10px] uppercase font-sans tracking-widest font-extrabold bg-amber-500 text-slate-950 px-1.5 py-0.2 rounded">Stall</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Seller Merchant Center</p>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Stall Identity Mini-Card -->
            <div class="p-4 mx-4 my-4 rounded-2xl bg-slate-800/80 border border-slate-700/60 flex items-center gap-3">
                <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=120&q=80" alt="Stall Logo" class="w-11 h-11 rounded-xl object-cover ring-2 ring-amber-500/40">
                <div class="overflow-hidden">
                    <div class="flex items-center gap-1.5">
                        <h4 class="text-xs font-bold text-white truncate">Artisan & Timber</h4>
                        <i class="fa-solid fa-circle-check text-amber-400 text-xs" title="Verified Stall"></i>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> 98.4% Trust
                        </span>
                        <span class="text-[10px] text-slate-400">Active</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto">
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-2 mb-1">Stall Operations</p>
                
                <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('seller.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center text-base"></i> Dashboard
                </a>

                <a href="{{ route('seller.products.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('seller.products.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-boxes-stacked w-5 text-center text-base"></i> Products
                    </div>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('seller.products.*') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-300' }}">48</span>
                </a>

                <a href="{{ route('seller.orders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('seller.orders.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-clipboard-list w-5 text-center text-base"></i> Orders
                    </div>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400">5 new</span>
                </a>

                <a href="{{ route('seller.payouts.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('seller.payouts.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-wallet w-5 text-center text-base"></i> Payouts & Balance
                </a>

                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-1">Storefront & Settings</p>

                <a href="{{ route('stall.show', 'artisan-timber') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center text-base text-amber-400"></i> View Live Stall
                </a>

                <a href="{{ route('seller.settings') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('seller.settings') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-sliders w-5 text-center text-base"></i> Stall Settings
                </a>
            </nav>

            <!-- Bottom User & Exit -->
            <div class="p-4 border-t border-slate-800 space-y-2">
                <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition">
                    <i class="fa-solid fa-shop"></i> Buyer Marketplace
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Topbar -->
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 z-10">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-bold font-heading text-slate-900">@yield('page-title', 'Seller Dashboard')</h1>
                        <p class="text-xs text-slate-500 hidden sm:block">Manage your catalog, fulfill buyer orders, and monitor stall payouts</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    
                    <!-- Quick Add Product CTA -->
                    <a href="{{ route('seller.products.create') }}" class="hidden sm:inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-indigo-600/20 transition">
                        <i class="fa-solid fa-plus"></i> Add New Product
                    </a>

                    <!-- Notification Bell -->
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 relative">
                            <i class="fa-regular fa-bell text-base"></i>
                            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-amber-500 rounded-full ring-2 ring-white"></span>
                        </button>
                        <div x-show="notifOpen" @click.outside="notifOpen = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 p-4 z-50 text-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="font-bold text-slate-900">Stall Notifications</span>
                                <span class="text-[10px] text-indigo-600 font-semibold cursor-pointer">Mark read</span>
                            </div>
                            <div class="space-y-3 pt-3">
                                <div class="flex gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-cart-check"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">New order #BZR-98241 received</p>
                                        <p class="text-slate-400 text-[10px]">2 handcrafted wooden lamps • 12 mins ago</p>
                                    </div>
                                </div>
                                <div class="flex gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">Low stock: Rustic Walnut Desk</p>
                                        <p class="text-slate-400 text-[10px]">Only 2 items left in inventory • 1 hour ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                        <img class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-200" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80" alt="Seller">
                        <div class="hidden md:block text-left text-xs">
                            <span class="font-bold text-slate-900 block leading-tight">Marcus Vance</span>
                            <span class="text-[10px] text-slate-500">Shop Owner</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Content Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
