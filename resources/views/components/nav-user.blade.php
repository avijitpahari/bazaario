@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $currentRoute = Route::currentRouteName();
    $currentUser = Auth::guard('user')->user() ?? Auth::guard('seller')->user() ?? Auth::guard('admin')->user() ?? Auth::user();

    // Determine dashboard link based on role
    $dashboardUrl = url('/');
    if ($currentUser) {
        if ($currentUser->role === 'admin') {
            $dashboardUrl = Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard');
        } elseif ($currentUser->role === 'seller') {
            $dashboardUrl = Route::has('seller.dashboard') ? route('seller.dashboard') : url('/seller/dashboard');
        } else {
            $dashboardUrl = Route::has('user.dashboard') ? route('user.dashboard') : url('/user/dashboard');
        }
    }

    // Avatar image resolution
    $avatarUrl = null;
    if ($currentUser && !empty($currentUser->profile_image)) {
        $avatarUrl = Str::startsWith($currentUser->profile_image, ['http://', 'https://']) 
            ? $currentUser->profile_image 
            : Storage::url($currentUser->profile_image);
    }
@endphp

<!-- Customer Top Navbar Header Dock -->
<header class="fixed top-2 sm:top-5 left-0 w-full z-50 flex justify-center px-2 sm:px-6 pointer-events-none" 
    x-data="{ 
        mobileMenuOpen: false, 
        profileDropdownOpen: false, 
        notificationsOpen: false, 
        categoriesOpen: false
    }" 
    @click.away="profileDropdownOpen = false; notificationsOpen = false; categoriesOpen = false">
    
    <div class="pointer-events-auto max-w-7xl mx-auto w-full relative">
        <div class="bg-white/95 backdrop-blur-xl border border-slate-200/90 rounded-2xl sm:rounded-full px-3 sm:px-6 py-2 sm:py-2.5 shadow-[0_12px_40px_-8px_rgba(15,23,42,0.12),0_2px_8px_-2px_rgba(15,23,42,0.06)] flex items-center justify-between gap-2 sm:gap-4 transition-all duration-200 ring-1 ring-slate-900/5">

            <!-- ── LEFT: Logo & Main Navigation Links ── -->
            <div class="flex items-center gap-2 sm:gap-4 lg:gap-6 shrink-0">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-1.5 sm:gap-2 group shrink-0 transition-transform active:scale-95 pr-1 sm:pr-4 sm:border-r border-slate-200/80">
                    <img src="{{ asset('images/bazaario-logo.png') }}" 
                         alt="Bazaario" 
                         class="h-7 sm:h-9 w-auto object-contain transition-transform group-hover:scale-105"
                         onerror="this.onerror=null; this.src='https://placehold.co/120x36/0f172a/f5a623?text=BAZAARIO';">
                </a>

                <!-- Desktop Navigation Links (Home, Shop, Categories, Auctions) -->
                <nav class="hidden md:flex items-center gap-1 bg-slate-100/80 p-1 rounded-full border border-slate-200/60 font-sans text-xs">
                    <!-- Home -->
                    <a href="{{ url('/') }}" 
                       class="px-3.5 py-1.5 rounded-full font-semibold transition-all duration-150 {{ request()->is('/') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white/80' }}">
                        Home
                    </a>

                    <!-- Shop -->
                    <a href="{{ Route::has('products.index') ? route('products.index') : url('/products') }}" 
                       class="px-3.5 py-1.5 rounded-full font-semibold transition-all duration-150 {{ request()->is('products*') || request()->is('shop*') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white/80' }}">
                        Shop
                    </a>

                    <!-- Categories with Dropdown Menu -->
                    <div class="relative" @mouseenter="categoriesOpen = true" @mouseleave="categoriesOpen = false">
                        <button type="button" 
                                @click="categoriesOpen = !categoriesOpen"
                                class="px-3.5 py-1.5 rounded-full font-semibold text-slate-700 hover:text-slate-900 hover:bg-white/80 transition-all duration-150 flex items-center gap-1">
                            <span>Categories</span>
                            <span class="material-symbols-outlined text-[15px] transition-transform duration-200" :class="categoriesOpen ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <!-- Categories Dropdown Popover -->
                        <div x-show="categoriesOpen" 
                             x-transition:enter="transition ease-out duration-150" 
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95" 
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
                             x-transition:leave="transition ease-in duration-100" 
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute left-0 mt-2 w-64 bg-white/95 backdrop-blur-xl rounded-2xl border border-slate-200/90 shadow-2xl p-2 z-50 grid grid-cols-1 gap-1 text-xs"
                             style="display: none;">
                            <a href="{{ route('category.show', 'fashion') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50/80 text-slate-700 hover:text-amber-900 font-medium transition-colors">
                                <span class="w-7 h-7 rounded-lg bg-amber-100/70 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">checkroom</span>
                                </span>
                                <div>
                                    <div class="font-bold text-slate-900">Fashion & Apparel</div>
                                    <div class="text-[10px] text-slate-400">Clothing, shoes & bags</div>
                                </div>
                            </a>
                            <a href="{{ route('category.show', 'electronics') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50/80 text-slate-700 hover:text-amber-900 font-medium transition-colors">
                                <span class="w-7 h-7 rounded-lg bg-indigo-100/70 text-indigo-700 flex items-center justify-center text-sm font-bold shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">headphones</span>
                                </span>
                                <div>
                                    <div class="font-bold text-slate-900">Tech & Electronics</div>
                                    <div class="text-[10px] text-slate-400">Audio, gadgets & gear</div>
                                </div>
                            </a>
                            <a href="{{ route('category.show', 'artisan-craft') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50/80 text-slate-700 hover:text-amber-900 font-medium transition-colors">
                                <span class="w-7 h-7 rounded-lg bg-emerald-100/70 text-emerald-700 flex items-center justify-center text-sm font-bold shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">palette</span>
                                </span>
                                <div>
                                    <div class="font-bold text-slate-900">Artisan & Handmade</div>
                                    <div class="text-[10px] text-slate-400">Crafts, pottery & decor</div>
                                </div>
                            </a>
                            <a href="{{ route('category.show', 'collectibles') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50/80 text-slate-700 hover:text-amber-900 font-medium transition-colors">
                                <span class="w-7 h-7 rounded-lg bg-purple-100/70 text-purple-700 flex items-center justify-center text-sm font-bold shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">stars</span>
                                </span>
                                <div>
                                    <div class="font-bold text-slate-900">Collectibles & Rare</div>
                                    <div class="text-[10px] text-slate-400">Vintage & limited runs</div>
                                </div>
                            </a>
                            <div class="pt-1 mt-1 border-t border-slate-100">
                                <a href="{{ route('products.index') }}" class="flex items-center justify-between px-3 py-1.5 text-[11px] font-bold text-amber-600 hover:text-amber-700">
                                    <span>Browse All Categories</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Auctions -->
                    <a href="{{ url('/auctions') }}" 
                       class="px-3.5 py-1.5 rounded-full font-semibold transition-all duration-150 flex items-center gap-1.5 {{ request()->is('auctions*') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white/80' }}">
                        <span>Auctions</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    </a>
                </nav>
            </div>

            <!-- ── CENTER: Quick Search Bar (Desktop) ── -->
            <div class="flex-1 max-w-xs md:max-w-sm lg:max-w-md hidden sm:block">
                <form action="{{ route('products.index') }}" method="GET" class="relative group">
                    <div class="relative flex items-center bg-slate-100/90 border border-slate-200/80 focus-within:border-amber-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-amber-500/20 rounded-full transition-all duration-200 overflow-hidden shadow-inner">
                        <span class="material-symbols-outlined text-slate-400 group-focus-within:text-amber-600 pl-3.5 text-[18px] pointer-events-none">
                            search
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search products, brands, sellers..." 
                               class="w-full bg-transparent border-0 py-1.5 pl-2 pr-8 text-xs text-slate-800 placeholder:text-slate-400 focus:ring-0 focus:outline-none font-sans">
                        <button type="submit" 
                                class="absolute right-1 w-6 h-6 rounded-full bg-slate-200 hover:bg-amber-500 hover:text-slate-900 text-slate-600 flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ── RIGHT: User Actions & Profile / Guest Actions ── -->
            <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">

                @if($currentUser)
                    {{-- ────────────────────────────────────────── --}}
                    {{-- ── LOGGED-IN USER NAVBAR (Normal / Seller / Admin) ── --}}
                    {{-- ────────────────────────────────────────── --}}

                    <!-- Wishlist Button (Hidden on Mobile to keep top bar ultra clean) -->
                    <a href="{{ url('/user/account/wishlist') }}" 
                       title="My Wishlist" 
                       class="hidden sm:flex relative w-9 h-9 rounded-full items-center justify-center text-slate-700 hover:text-rose-600 hover:bg-rose-50 border border-slate-200/70 bg-slate-50/80 transition-all active:scale-95 group">
                        <span class="material-symbols-outlined text-[19px] transition-transform group-hover:scale-110">favorite</span>
                        <span class="sr-only">Wishlist</span>
                    </a>

                    <!-- Cart Button (Visible everywhere with badge) -->
                    <a href="{{ url('/cart') }}" 
                       title="Shopping Cart" 
                       class="relative w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-slate-700 hover:text-slate-900 hover:bg-amber-50 border border-slate-200/70 bg-slate-50/80 transition-all active:scale-95 group">
                        <span class="material-symbols-outlined text-[18px] sm:text-[19px] transition-transform group-hover:scale-110">shopping_cart</span>
                        <!-- Cart Items Pill Badge -->
                        <span class="absolute -top-1 -right-1 min-w-[16px] h-[16px] sm:min-w-[17px] sm:h-[17px] px-1 rounded-full bg-amber-500 text-slate-950 font-mono text-[9px] sm:text-[10px] font-extrabold flex items-center justify-center shadow-xs">
                            {{ session('cart_count', 2) }}
                        </span>
                        <span class="sr-only">Cart</span>
                    </a>

                    <!-- Notifications Dropdown (Hidden on mobile) -->
                    <div class="relative hidden sm:block">
                        <button type="button" 
                                @click="notificationsOpen = !notificationsOpen; profileDropdownOpen = false" 
                                title="Notifications" 
                                class="relative w-9 h-9 rounded-full flex items-center justify-center text-slate-700 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/70 bg-slate-50/80 transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[19px]">notifications</span>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
                        </button>

                        <!-- Notification Dropdown Popover -->
                        <div x-show="notificationsOpen" 
                             x-transition:enter="transition ease-out duration-150" 
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95" 
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
                             x-transition:leave="transition ease-in duration-100" 
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95" 
                             class="absolute right-0 mt-2 w-80 bg-white/95 backdrop-blur-xl rounded-2xl border border-slate-200/90 shadow-2xl p-3 z-50 text-xs"
                             style="display: none;">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2">
                                <span class="font-bold text-slate-900 font-sans">Notifications</span>
                                <a href="{{ url('/user/account/notifications') }}" class="text-[11px] text-amber-600 font-bold hover:underline">View all</a>
                            </div>
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                <div class="p-2.5 rounded-xl bg-amber-50/60 border border-amber-100 flex items-start gap-2.5">
                                    <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-900 text-[11px]">Order #BZ-ESC-90821 Escrow Secured</p>
                                        <p class="text-[10px] text-slate-500">Seller is preparing your package.</p>
                                        <span class="text-[9px] text-slate-400 mt-1 block">Just now</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Account Dropdown Pill (Desktop) -->
                    <div class="relative hidden md:block">
                        <button type="button" 
                                @click="profileDropdownOpen = !profileDropdownOpen; notificationsOpen = false" 
                                class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-full border border-slate-200/90 bg-white shadow-xs hover:border-amber-400/60 transition-all cursor-pointer group active:scale-95">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" 
                                     alt="{{ $currentUser->name }}" 
                                     class="w-7 h-7 rounded-full object-cover ring-1 ring-amber-400/50 shadow-inner">
                            @else
                                <div class="w-7 h-7 rounded-full bg-slate-900 text-amber-400 font-sans text-xs font-bold flex items-center justify-center ring-1 ring-amber-400/40 shadow-xs">
                                    {{ strtoupper(mb_substr($currentUser->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <span class="text-xs font-bold text-slate-800 tracking-tight max-w-[100px] truncate">
                                {{ $currentUser->name }}
                            </span>
                            <span class="material-symbols-outlined text-[16px] text-slate-400 transition-transform duration-200" :class="profileDropdownOpen ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <div x-show="profileDropdownOpen" 
                             x-transition:enter="transition ease-out duration-150" 
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95" 
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
                             x-transition:leave="transition ease-in duration-100" 
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95" 
                             class="absolute right-0 mt-2 w-56 bg-white/95 backdrop-blur-xl rounded-2xl border border-slate-200/90 shadow-2xl p-2 z-50 text-xs"
                             style="display: none;">
                            <div class="px-3 py-2 border-b border-slate-100 mb-1">
                                <span class="text-[10px] font-mono text-slate-400 uppercase">Signed in as</span>
                                <div class="font-bold text-slate-900 truncate">{{ $currentUser->email }}</div>
                            </div>
                            <div class="space-y-0.5">
                                <a href="{{ $dashboardUrl }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100/80 font-semibold transition-colors">
                                    <span class="material-symbols-outlined text-[17px] text-amber-600">dashboard</span>
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ url('/account') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100/80 font-medium transition-colors">
                                    <span class="material-symbols-outlined text-[17px] text-slate-500">package_2</span>
                                    <span>Orders & Escrow</span>
                                </a>
                                <a href="{{ url('/user/account/wishlist') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100/80 font-medium transition-colors">
                                    <span class="material-symbols-outlined text-[17px] text-slate-500">favorite</span>
                                    <span>Saved Wishlist</span>
                                </a>
                                <a href="{{ url('/user/account/profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100/80 font-medium transition-colors">
                                    <span class="material-symbols-outlined text-[17px] text-slate-500">manage_accounts</span>
                                    <span>Settings</span>
                                </a>
                            </div>
                            <div class="my-1.5 border-t border-slate-100"></div>
                            <div class="px-1">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 font-bold transition-colors text-left">
                                        <span class="material-symbols-outlined text-[17px]">logout</span>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- ────────────────────────────────────────── --}}
                    {{-- ── GUEST USER NAVBAR (Login & Register) ── --}}
                    {{-- ────────────────────────────────────────── --}}

                    <!-- Sign In Link -->
                    <a href="{{ route('login') }}" 
                       class="text-xs font-semibold px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-full transition-colors text-slate-700 hover:text-slate-900 hover:bg-slate-100">
                        Sign in
                    </a>

                    <!-- Get Started / Register Button -->
                    <a href="{{ route('register') }}" 
                       class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-3 sm:px-5 py-1.5 sm:py-2 rounded-full transition-all shadow-md shadow-amber-500/20 active:scale-95 flex items-center gap-1">
                        <span>Join</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>

                @endif

                <!-- Mobile Hamburger Menu Button (visible on mobile screens) -->
                <button type="button" 
                        @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-slate-700 hover:bg-slate-100 border border-slate-200/80 bg-white"
                        aria-label="Toggle Mobile Menu">
                    <span class="material-symbols-outlined text-[19px]" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                </button>

            </div>

        </div>

        <!-- ── MOBILE SLIDE-DOWN DRAWER MENU ── -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 -translate-y-4 scale-98" 
             x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
             x-transition:leave-end="opacity-0 -translate-y-4 scale-98" 
             class="md:hidden mt-2 bg-white/95 backdrop-blur-2xl border border-slate-200/90 rounded-3xl shadow-2xl p-4 space-y-4 text-xs z-50 font-sans"
             style="display: none;">

            <!-- Mobile Search Bar -->
            <form action="{{ route('products.index') }}" method="GET" class="relative">
                <div class="flex items-center bg-slate-100 border border-slate-200 rounded-full px-3 py-1.5">
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">search</span>
                    <input type="text" name="search" placeholder="Search Bazaario..." class="w-full bg-transparent border-0 py-1 px-2 text-xs focus:ring-0 focus:outline-none">
                </div>
            </form>

            <!-- Navigation Links -->
            <div class="grid grid-cols-2 gap-2 text-slate-800 font-semibold">
                <a href="{{ url('/') }}" class="flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50 hover:bg-amber-50 transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-amber-600">home</span>
                    <span>Home</span>
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50 hover:bg-amber-50 transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-amber-600">storefront</span>
                    <span>Shop</span>
                </a>
                <a href="{{ route('category.show', 'all') }}" class="flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50 hover:bg-amber-50 transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-amber-600">category</span>
                    <span>Categories</span>
                </a>
                <a href="{{ url('/auctions') }}" class="flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50 hover:bg-amber-50 transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-amber-600">gavel</span>
                    <span>Auctions</span>
                </a>
            </div>

            @if($currentUser)
                <!-- Logged In Mobile Section -->
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-slate-900 text-amber-400 font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(mb_substr($currentUser->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block text-xs">{{ $currentUser->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $currentUser->email }}</span>
                            </div>
                        </div>
                        <a href="{{ $dashboardUrl }}" class="px-3 py-1 rounded-full bg-slate-900 text-white font-bold text-[11px]">Dashboard</a>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2 pt-2">
                        <a href="{{ url('/cart') }}" class="flex flex-col items-center justify-center p-2 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 text-[11px] font-bold">
                            <span class="material-symbols-outlined text-[18px] text-slate-600 mb-0.5">shopping_cart</span>
                            <span>Cart</span>
                        </a>
                        <a href="{{ url('/user/account/wishlist') }}" class="flex flex-col items-center justify-center p-2 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 text-[11px] font-bold">
                            <span class="material-symbols-outlined text-[18px] text-rose-500 mb-0.5">favorite</span>
                            <span>Wishlist</span>
                        </a>
                        <a href="{{ url('/user/account/notifications') }}" class="flex flex-col items-center justify-center p-2 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 text-[11px] font-bold">
                            <span class="material-symbols-outlined text-[18px] text-amber-600 mb-0.5">notifications</span>
                            <span>Alerts</span>
                        </a>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full py-2.5 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">logout</span>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            @else
                <!-- Guest Mobile Section -->
                <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs text-center">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs text-center shadow-md shadow-amber-500/20">
                        Get Started
                    </a>
                </div>
            @endif

        </div>

    </div>
</header>

@once
    <!-- Material Symbols & AlpineJS Assets for Interactive Navbar -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endonce