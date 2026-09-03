<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Bazaario — General Multi-Vendor Marketplace')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS (CDN fallback for instant preview & full utility coverage) -->
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
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                        bazaar: {
                            amber: '#f59e0b',
                            terracotta: '#ea580c',
                            emerald: '#10b981',
                            slate: '#0f172a',
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
        .glass-nav {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 flex flex-col min-h-screen antialiased selection:bg-indigo-500 selection:text-white" x-data="{ mobileMenu: false, lang: 'EN', cartCount: 3 }">

    <!-- Top Live Announcement Strip -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Live Market
                </span>
                <span class="hidden sm:inline">Over <strong>450+</strong> independent verified stalls shipping across the nation</span>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <a href="{{ route('seller.register') }}" class="text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-store"></i> Open your own stall
                </a>
                <span class="text-slate-600">|</span>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="hover:text-white flex items-center gap-1 focus:outline-none">
                        <i class="fa-solid fa-globe text-slate-400"></i> <span x-text="lang"></span> <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-1 w-28 bg-white text-slate-800 shadow-xl rounded-lg py-1 border border-slate-100 z-50">
                        <button @click="lang = 'EN'; open = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 flex items-center justify-between">English <i x-show="lang === 'EN'" class="fa-solid fa-check text-indigo-600 text-xs"></i></button>
                        <button @click="lang = 'ES'; open = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 flex items-center justify-between">Español <i x-show="lang === 'ES'" class="fa-solid fa-check text-indigo-600 text-xs"></i></button>
                        <button @click="lang = 'FR'; open = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 flex items-center justify-between">Français <i x-show="lang === 'FR'" class="fa-solid fa-check text-indigo-600 text-xs"></i></button>
                        <button @click="lang = 'HI'; open = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 flex items-center justify-between">हिन्दी <i x-show="lang === 'HI'" class="fa-solid fa-check text-indigo-600 text-xs"></i></button>
                    </div>
                </div>
                <span class="text-slate-600">|</span>
                <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-white transition flex items-center gap-1 text-[11px]">
                    <i class="fa-solid fa-shield-halved"></i> Admin Portal
                </a>
            </div>
        </div>
    </div>

    <!-- Sticky Navigation Header -->
    <header class="sticky top-0 z-40 glass-nav border-b border-slate-200/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3 shrink-0">
                    <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
                        <i class="fa-solid fa-bars-staggered text-xl"></i>
                    </button>
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-amber-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-200">
                            <i class="fa-solid fa-bag-shopping text-lg"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold tracking-tight font-heading bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-700 bg-clip-text text-transparent">Bazaario</span>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-600 -mt-1 font-sans">Multi-Vendor Bazaar</span>
                        </div>
                    </a>
                </div>

                <!-- Global Search & Category Filter -->
                <div class="hidden md:flex flex-1 max-w-2xl mx-4">
                    <form action="{{ route('products.index') }}" method="GET" class="w-full flex items-center rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-slate-300 focus-within:border-indigo-500 focus-within:ring-4 focus-within:ring-indigo-500/10 transition-all overflow-hidden">
                        <div class="relative pl-3 pr-2 border-r border-slate-200 bg-slate-50 text-slate-600" x-data="{ catOpen: false, selectedCat: 'All Categories' }">
                            <button type="button" @click="catOpen = !catOpen" class="py-2.5 text-xs font-semibold flex items-center gap-1.5 focus:outline-none">
                                <span x-text="selectedCat" class="truncate max-w-[110px]"></span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>
                            <div x-show="catOpen" @click.outside="catOpen = false" x-cloak class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-50 text-xs font-medium text-slate-700">
                                <button type="button" @click="selectedCat = 'All Categories'; catOpen = false" class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-600">All Categories</button>
                                <button type="button" @click="selectedCat = 'Artisan & Crafts'; catOpen = false" class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-600">Artisan & Crafts</button>
                                <button type="button" @click="selectedCat = 'Electronics & Gear'; catOpen = false" class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-600">Electronics & Gear</button>
                                <button type="button" @click="selectedCat = 'Apparel & Wear'; catOpen = false" class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-600">Apparel & Wear</button>
                                <button type="button" @click="selectedCat = 'Home & Living'; catOpen = false" class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-600">Home & Living</button>
                                <button type="button" @click="selectedCat = 'Organic & Gourmet'; catOpen = false" class="w-full text-left px-3 py-2 hover:bg-indigo-50 hover:text-indigo-600">Organic & Gourmet</button>
                            </div>
                        </div>
                        <div class="relative flex-1 flex items-center">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3.5 text-sm"></i>
                            <input type="text" name="q" placeholder="Search 10,000+ unique products, stalls, artisans..." class="w-full py-2.5 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 bg-transparent border-0 focus:outline-none focus:ring-0">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-5 py-3 transition-colors flex items-center gap-1.5">
                            Search
                        </button>
                    </form>
                </div>

                <!-- Right Action Icons & Account -->
                <div class="flex items-center gap-3">
                    
                    <!-- Browse Products link -->
                    <a href="{{ route('products.index') }}" class="hidden lg:inline-flex items-center gap-1.5 text-sm font-semibold text-slate-700 hover:text-indigo-600 px-3 py-2 rounded-xl hover:bg-slate-100 transition">
                        <i class="fa-solid fa-compass text-indigo-500"></i> Explore Stalls
                    </a>

                    <!-- Seller Portal Quick Link -->
                    <a href="{{ route('seller.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/80 px-3.5 py-2 rounded-xl transition">
                        <i class="fa-solid fa-store text-amber-600"></i> Seller Hub
                    </a>

                    <!-- Cart Drawer/Link -->
                    <a href="{{ route('cart.index') }}" class="relative p-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-indigo-600 hover:border-indigo-300 shadow-sm transition flex items-center">
                        <i class="fa-solid fa-cart-shopping text-lg"></i>
                        <span class="absolute -top-1.5 -right-1.5 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-md shadow-indigo-500/30" x-text="cartCount">3</span>
                    </a>

                    <!-- Account Menu Dropdown -->
                    <div class="relative" x-data="{ accountOpen: false }">
                        <button @click="accountOpen = !accountOpen" class="flex items-center gap-2 p-1.5 pl-2 rounded-xl border border-slate-200 bg-white hover:border-slate-300 shadow-sm focus:outline-none transition">
                            <img class="w-8 h-8 rounded-lg object-cover ring-2 ring-indigo-500/20" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Avatar">
                            <span class="hidden xl:inline text-xs font-semibold text-slate-800">Sarah M.</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 mr-1"></i>
                        </button>
                        
                        <div x-show="accountOpen" @click.outside="accountOpen = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 text-xs font-medium text-slate-700 divide-y divide-slate-100">
                            <div class="px-4 py-2.5">
                                <p class="text-[11px] text-slate-500 font-normal">Signed in as</p>
                                <p class="text-sm font-bold text-slate-900 truncate">sarah.miller@example.com</p>
                                <span class="inline-flex mt-1 items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700">
                                    <i class="fa-solid fa-user-check"></i> Customer Account
                                </span>
                            </div>
                            <div class="py-1.5">
                                <a href="{{ route('account.index') }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 hover:text-indigo-600">
                                    <i class="fa-regular fa-user text-slate-400 w-4"></i> My Profile & Addresses
                                </a>
                                <a href="{{ route('account.orders') }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 hover:text-indigo-600">
                                    <i class="fa-solid fa-box text-slate-400 w-4"></i> Orders & Tracking
                                    <span class="ml-auto bg-indigo-100 text-indigo-700 text-[10px] font-bold px-1.5 py-0.2 rounded-full">2 active</span>
                                </a>
                                <a href="{{ route('cart.index') }}" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 hover:text-indigo-600">
                                    <i class="fa-solid fa-bag-shopping text-slate-400 w-4"></i> Saved Cart Items
                                </a>
                            </div>
                            <div class="py-1.5 bg-slate-50/60">
                                <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-amber-700 hover:bg-amber-50">
                                    <i class="fa-solid fa-chart-line text-amber-600 w-4"></i> Seller Dashboard
                                </a>
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-600 hover:bg-slate-100">
                                    <i class="fa-solid fa-gear text-slate-400 w-4"></i> Platform Admin
                                </a>
                            </div>
                            <div class="py-1.5">
                                <a href="{{ route('login') }}" class="flex items-center gap-2.5 px-4 py-2 text-red-600 hover:bg-red-50">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-red-400 w-4"></i> Log Out
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenu" x-cloak class="lg:hidden bg-white border-t border-slate-200 px-4 py-4 space-y-3">
            <form action="{{ route('products.index') }}" method="GET" class="relative">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3.5 top-3 text-sm"></i>
                <input type="text" placeholder="Search products, stalls..." class="w-full py-2.5 pl-10 pr-4 text-sm rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-500">
            </form>
            <div class="grid grid-cols-2 gap-2 text-xs font-semibold pt-2">
                <a href="{{ route('products.index') }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-compass text-indigo-500"></i> All Products
                </a>
                <a href="{{ route('seller.register') }}" class="p-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 flex items-center gap-2">
                    <i class="fa-solid fa-store text-amber-600"></i> Open Stall
                </a>
                <a href="{{ route('account.orders') }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-box text-indigo-500"></i> Track Orders
                </a>
                <a href="{{ route('seller.dashboard') }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-shop text-indigo-500"></i> Seller Panel
                </a>
            </div>
        </div>
    </header>

    <!-- Main Dynamic Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-slate-800">
                
                <!-- Brand Info -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-amber-400 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                        <span class="text-2xl font-bold font-heading text-white">Bazaario</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-sm">
                        The modern open multi-vendor marketplace connecting passionate independent artisans, verified brands, and trusted stalls with buyers worldwide.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <span class="w-8 h-8 rounded-full bg-slate-800 hover:bg-indigo-600 flex items-center justify-center text-slate-400 hover:text-white transition cursor-pointer"><i class="fa-brands fa-x-twitter"></i></span>
                        <span class="w-8 h-8 rounded-full bg-slate-800 hover:bg-indigo-600 flex items-center justify-center text-slate-400 hover:text-white transition cursor-pointer"><i class="fa-brands fa-instagram"></i></span>
                        <span class="w-8 h-8 rounded-full bg-slate-800 hover:bg-indigo-600 flex items-center justify-center text-slate-400 hover:text-white transition cursor-pointer"><i class="fa-brands fa-facebook"></i></span>
                        <span class="w-8 h-8 rounded-full bg-slate-800 hover:bg-indigo-600 flex items-center justify-center text-slate-400 hover:text-white transition cursor-pointer"><i class="fa-brands fa-github"></i></span>
                    </div>
                </div>

                <!-- Shop Links -->
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-white mb-4 font-heading">Shop Bazaario</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('products.index') }}" class="hover:text-indigo-400 transition">All Product Catalog</a></li>
                        <li><a href="{{ route('category.show', 'artisan-crafts') }}" class="hover:text-indigo-400 transition">Artisan & Handmade</a></li>
                        <li><a href="{{ route('category.show', 'electronics') }}" class="hover:text-indigo-400 transition">Electronics & Gadgets</a></li>
                        <li><a href="{{ route('category.show', 'apparel') }}" class="hover:text-indigo-400 transition">Fashion & Apparel</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-indigo-400 transition">Featured Weekly Stalls</a></li>
                    </ul>
                </div>

                <!-- Sell on Bazaario -->
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-white mb-4 font-heading">Sell with Us</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('seller.register') }}" class="text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-sparkles text-xs"></i> Open a Verified Stall</a></li>
                        <li><a href="{{ route('seller.dashboard') }}" class="hover:text-indigo-400 transition">Seller Dashboard</a></li>
                        <li><a href="{{ route('seller.payouts.index') }}" class="hover:text-indigo-400 transition">Commission & Payouts</a></li>
                        <li><a href="{{ route('seller.products.create') }}" class="hover:text-indigo-400 transition">Listing Guidelines</a></li>
                        <li><a href="{{ route('seller.onboarding.index') }}" class="hover:text-indigo-400 transition">Seller Handbook</a></li>
                    </ul>
                </div>

                <!-- Company & Trust -->
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-white mb-4 font-heading">Trust & Security</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('account.orders') }}" class="hover:text-indigo-400 transition">Order Protection</a></li>
                        <li><a href="{{ route('maintenance') }}" class="hover:text-indigo-400 transition">Trust Score System</a></li>
                        <li><a href="{{ route('maintenance') }}" class="hover:text-indigo-400 transition">Return & Refund Policy</a></li>
                        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400 transition">Platform Governance</a></li>
                        <li><a href="{{ route('maintenance') }}" class="hover:text-indigo-400 transition">Privacy & Terms</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Subfooter -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Bazaario Marketplace Inc. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1 text-slate-400"><i class="fa-solid fa-shield-check text-emerald-400"></i> 100% Buyer Protected</span>
                    <span class="flex items-center gap-1 text-slate-400"><i class="fa-solid fa-truck-fast text-indigo-400"></i> Multi-Seller Direct Dispatch</span>
                    <span class="flex items-center gap-1 text-slate-400"><i class="fa-solid fa-money-bill-wave text-amber-400"></i> Cash on Delivery Enabled</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
