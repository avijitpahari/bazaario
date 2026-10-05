@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use App\Models\Category;

    // On public pages, only delegate to nav-user if a real user/seller is logged in.
    // Admin guard must NOT trigger nav-user on public pages.
    $currentUser = Auth::guard('user')->user() ?? Auth::guard('seller')->user();

    if ($currentUser && !defined('NAV_DELEGATING')) {
        define('NAV_DELEGATING', true);
        echo view('components.nav-user', get_defined_vars())->render();
        return;
    }

    $currentRoute = Route::currentRouteName();

    $navCategories = Cache::store('file')->remember('nav_categories_list', 600, function () {
        return Category::where('status', 'active')->take(6)->get();
    });

    $cartCount = 0;
    if ($currentUser) {
        $userCart = \App\Models\Cart::where('user_id', $currentUser->id)->with('items')->first();
        if ($userCart) {
            $cartCount = (int) $userCart->items->sum('quantity');
        }
    }
    if ($cartCount === 0) {
        $cartCount = count(session('cart', []));
    }
    $wishlistCount = count(session('wishlist', []));
@endphp

<!-- ── STICKY CUSTOMER NAVBAR CONTAINER ── -->
<div x-data="{ 
        categoriesOpen: false, 
        accountOpen: false, 
        notificationsOpen: false, 
        cartOpen: false,
        searchFocus: false,
        mobileMenuOpen: false 
    }" 
    class="sticky top-0 z-50 font-sans text-slate-900 selection:bg-amber-100 px-4 sm:px-6 pt-3 pb-2">

    <!-- ── LEVEL 1: MAIN DESKTOP NAVBAR ── -->
    <div class="max-w-7xl mx-auto bg-white rounded-full border border-slate-200/80 shadow-md px-4 sm:px-6 h-14 flex items-center justify-between gap-4">

        <!-- 1. BRAND LOGO -->
        <div class="flex items-center gap-6 shrink-0">
            <!-- Mobile Hamburger Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-1.5 text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>

            <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                <img src="{{ asset('images/bazaario-logo.png') }}" alt="Bazaario" class="h-8 sm:h-9 w-auto object-contain transition-transform group-hover:scale-105">
            </a>
        </div>

        <!-- 2. MAIN NAVIGATION LINKS -->
        @php
            $isOnShop = in_array($currentRoute, ['products.index', 'products.show', 'products.category', 'user.wishlist.index']) && request()->path() !== '/';
            $isOnAuctions = in_array($currentRoute, ['auctions.index', 'auctions', 'user.bids', 'bids']) || request('sale_type') === 'auction';
            $isOnDeals = $currentRoute === 'products.index' && request('filter') === 'deals';
        @endphp
        <nav class="hidden lg:flex items-center gap-1.5 text-sm font-semibold text-slate-700">
            <!-- Home → Products Catalog -->
            <a href="{{ route('products.index') }}" 
               class="px-3.5 py-1.5 rounded-full transition-all {{ $isOnShop ? 'bg-slate-900 text-white font-bold' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                Shop
            </a>

            <!-- Categories Mega Dropdown -->
            <div class="relative" @mouseenter="categoriesOpen = true" @mouseleave="categoriesOpen = false">
                <button @click="categoriesOpen = !categoriesOpen" 
                        class="px-3.5 py-1.5 flex items-center gap-0.5 rounded-full transition-all hover:bg-slate-100 hover:text-slate-900 cursor-pointer">
                    <span>Categories</span>
                    <span class="material-symbols-outlined text-base transition-transform duration-200" :class="categoriesOpen ? 'rotate-180' : ''">expand_more</span>
                </button>

                <!-- Mega Dropdown Panel (P36: Category slug routes) -->
                <div x-cloak x-show="categoriesOpen"
                     x-transition:enter="transition ease-out duration-150 transform"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100 transform"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute top-full mt-2 left-0 w-[540px] max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 z-50 grid grid-cols-3 gap-6">
                    
                    <div>
                        <h4 class="font-mono text-[11px] font-bold uppercase text-slate-400 tracking-wider mb-2.5 pb-1 border-b border-slate-100">Electronics</h4>
                        <ul class="space-y-1.5 text-xs text-slate-600">
                            <li><a href="{{ route('products.index', ['category' => 'electronics']) }}" class="hover:text-amber-600 transition-colors">All Electronics</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'electronics']) }}" class="hover:text-amber-600 transition-colors">Smartphones & Audio</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'electronics']) }}" class="hover:text-amber-600 transition-colors">Laptops & Gadgets</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'electronics']) }}" class="hover:text-amber-600 transition-colors">Tech Accessories</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-mono text-[11px] font-bold uppercase text-slate-400 tracking-wider mb-2.5 pb-1 border-b border-slate-100">Fashion</h4>
                        <ul class="space-y-1.5 text-xs text-slate-600">
                            <li><a href="{{ route('products.index', ['category' => 'fashion']) }}" class="hover:text-amber-600 transition-colors">All Fashion</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'fashion']) }}" class="hover:text-amber-600 transition-colors">Men's Wear</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'fashion']) }}" class="hover:text-amber-600 transition-colors">Women's Wear</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'fashion']) }}" class="hover:text-amber-600 transition-colors">Footwear & Bags</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-mono text-[11px] font-bold uppercase text-slate-400 tracking-wider mb-2.5 pb-1 border-b border-slate-100">Craft & Home</h4>
                        <ul class="space-y-1.5 text-xs text-slate-600">
                            <li><a href="{{ route('products.index', ['category' => 'artisan-craft']) }}" class="hover:text-amber-600 transition-colors">Handmade Crafts</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'home-living']) }}" class="hover:text-amber-600 transition-colors">Home & Living</a></li>
                            <li><a href="{{ route('products.index', ['category' => 'collectibles']) }}" class="hover:text-amber-600 transition-colors">Rare Collectibles</a></li>
                            <li><a href="{{ route('products.index') }}" class="font-bold text-amber-600 hover:underline flex items-center gap-0.5 mt-2">
                                <span>Browse All</span> <span>→</span>
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Auctions (Distinct Live Identity) -->
            <a href="{{ route('auctions.index') }}" 
               class="px-3.5 py-1.5 rounded-full flex items-center gap-1.5 font-bold transition-all {{ $isOnAuctions ? 'bg-slate-900 text-white' : 'text-slate-900 hover:bg-slate-100' }}">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                </span>
                <span>⚡ Auctions</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-extrabold uppercase bg-rose-100 text-rose-700">LIVE</span>
            </a>

            <!-- Deals -->
            <a href="{{ route('products.index', ['filter' => 'deals']) }}" 
               class="px-3.5 py-1.5 rounded-full transition-all {{ $isOnDeals ? 'bg-slate-900 text-white font-bold' : 'hover:bg-slate-100 hover:text-amber-600' }}">
                Deals
            </a>
        </nav>

        <!-- 3. PROMINENT SEARCH BAR WITH FOCUS DROPDOWN (P29 Parity) -->
        <div class="flex-1 max-w-md mx-2 relative hidden md:block" @click.outside="searchFocus = false">
            <form action="{{ route('products.index') }}" method="GET" class="relative">
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-xl pointer-events-none">search</span>
                    <input type="text" 
                           name="search"
                           @focus="searchFocus = true"
                           placeholder="Search products, brands & categories..."
                           class="w-full bg-slate-100 hover:bg-slate-100/80 focus:bg-white text-slate-900 text-xs sm:text-sm rounded-full pl-10 pr-4 py-2 border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-all shadow-inner">
                </div>
            </form>

            <!-- Search Focus Dropdown -->
            <div x-cloak x-show="searchFocus" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 z-50 text-xs">
                
                <div class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Popular Categories</div>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('products.index', ['category' => 'electronics']) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-100 font-semibold text-slate-800 flex items-center gap-1.5">
                        <span>⚡ Electronics</span>
                    </a>
                    <a href="{{ route('products.index', ['category' => 'fashion']) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-100 font-semibold text-slate-800 flex items-center gap-1.5">
                        <span>👗 Fashion</span>
                    </a>
                    <a href="{{ route('products.index', ['category' => 'artisan-craft']) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-100 font-semibold text-slate-800 flex items-center gap-1.5">
                        <span>🏺 Crafts</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. CUSTOMER ACTION ICONS (Wishlist, Notifications, Cart, Profile) -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">

            <!-- Wishlist ♡ -->
            <a href="{{ route('user.wishlist.index') }}" 
               class="relative p-2 text-slate-700 hover:text-red-500 rounded-full hover:bg-slate-100 transition-colors"
               aria-label="Wishlist">
                <span class="material-symbols-outlined text-2xl">favorite</span>
                @if($wishlistCount > 0)
                    <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white rounded-full text-[10px] font-bold flex items-center justify-center shadow-xs">
                        {{ $wishlistCount }}
                    </span>
                @endif
            </a>

            <!-- Cart Icon (Touch & Desktop Friendly) -->
            <div class="relative"
                 @mouseenter="if (!('ontouchstart' in window || navigator.maxTouchPoints > 0)) cartOpen = true" 
                 @mouseleave="if (!('ontouchstart' in window || navigator.maxTouchPoints > 0)) cartOpen = false" 
                 @click.outside="cartOpen = false">
                <a href="{{ route('cart.index') }}" 
                   @click="if ('ontouchstart' in window || navigator.maxTouchPoints > 0) { if (!cartOpen) { $event.preventDefault(); cartOpen = true; } }"
                   class="relative p-2 text-slate-700 hover:text-slate-900 rounded-full hover:bg-slate-100 transition-colors flex items-center"
                   aria-label="Cart">
                    <span class="material-symbols-outlined text-2xl">shopping_cart</span>
                    @if($cartCount > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-slate-900 text-amber-400 rounded-full text-[10px] font-bold flex items-center justify-center shadow-xs">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Customer Profile 👤 / Account Dropdown -->
            <div class="relative" @click.outside="accountOpen = false">
                @if($currentUser)
                    <button @click="accountOpen = !accountOpen" 
                            class="flex items-center gap-2 p-1 pl-2 pr-3 bg-slate-100 hover:bg-slate-200 rounded-full transition-colors border border-slate-200/80">
                        @if($currentUser->profile_image)
                            <img src="{{ Storage::url($currentUser->profile_image) }}" alt="{{ $currentUser->name }}" class="w-7 h-7 rounded-full object-cover">
                        @else
                            <div class="w-7 h-7 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(mb_substr($currentUser->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="font-semibold text-xs text-slate-900 max-w-[90px] truncate hidden sm:inline">
                            {{ $currentUser->name }}
                        </span>
                        <span class="material-symbols-outlined text-base text-slate-500">expand_more</span>
                    </button>

                    <!-- Account Dropdown Menu — Full Panel -->
                    <div x-cloak x-show="accountOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-full mt-2 right-0 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 text-xs font-medium max-h-[calc(100vh-85px)] flex flex-col overflow-hidden">

                        {{-- Sticky User Identity Header --}}
                        <div class="px-4 py-2.5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/80 backdrop-blur-xs shrink-0">
                            @if($currentUser->profile_image)
                                <img src="{{ Storage::url($currentUser->profile_image) }}" alt="{{ $currentUser->name }}" class="w-9 h-9 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-9 h-9 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(mb_substr($currentUser->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 text-xs truncate leading-tight">{{ $currentUser->name }}</div>
                                <div class="text-slate-400 font-mono text-[10px] truncate leading-tight mt-0.5">{{ $currentUser->email }}</div>
                            </div>
                        </div>

                        {{-- Scrollable Content Body --}}
                        <div class="overflow-y-auto flex-1 py-1 divide-y divide-slate-100">
                            {{-- Dashboard --}}
                            @php
                                $dashRoute = match($currentUser->role) {
                                    'admin' => route('admin.dashboard'),
                                    'seller' => route('seller.dashboard'),
                                    default => route('user.dashboard'),
                                };
                            @endphp
                            <div class="py-1">
                                <a href="{{ $dashRoute }}" class="px-3.5 py-1.5 hover:bg-amber-50 text-slate-700 hover:text-amber-700 flex items-center gap-2.5 font-semibold transition-colors">
                                    <span class="material-symbols-outlined text-base text-amber-500">dashboard</span>
                                    <span>My Dashboard</span>
                                </a>
                            </div>

                            {{-- Group: Shopping --}}
                            <div class="py-1">
                                <div class="px-3.5 pt-1 pb-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-400 font-mono">Shopping</div>
                                <a href="{{ route('user.orders.index') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">package_2</span>
                                    <span>My Orders</span>
                                </a>
                                <a href="{{ route('user.wishlist.index') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">favorite</span>
                                    <span>Wishlist</span>
                                </a>
                                <a href="{{ route('cart.index') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">shopping_cart</span>
                                    <span>Cart</span>
                                </a>
                                <a href="{{ route('user.returns') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">assignment_return</span>
                                    <span>Returns</span>
                                </a>
                                <a href="{{ route('user.invoices') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">receipt_long</span>
                                    <span>Invoices</span>
                                </a>
                                <a href="{{ route('user.coupons') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">local_offer</span>
                                    <span>My Coupons</span>
                                </a>
                            </div>

                            {{-- Group: Auctions & Discovery --}}
                            <div class="py-1">
                                <div class="px-3.5 pt-1 pb-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-400 font-mono">Auctions & Discovery</div>
                                <a href="{{ route('user.bids') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">gavel</span>
                                    <span>My Bids</span>
                                </a>
                                <a href="{{ route('user.auctions') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">bolt</span>
                                    <span>My Auctions</span>
                                </a>
                                <a href="{{ route('user.compare') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">compare</span>
                                    <span>Compare Products</span>
                                </a>
                                <a href="{{ route('user.recommendations') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">recommend</span>
                                    <span>Recommendations</span>
                                </a>
                            </div>

                            {{-- Group: Account --}}
                            <div class="py-1">
                                <div class="px-3.5 pt-1 pb-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-400 font-mono">Account</div>
                                <a href="{{ route('user.profile') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">person</span>
                                    <span>My Profile</span>
                                </a>
                                <a href="{{ route('user.reviews.index') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">rate_review</span>
                                    <span>My Reviews</span>
                                </a>
                                <a href="{{ route('user.addresses.index') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">location_on</span>
                                    <span>Addresses</span>
                                </a>
                                <a href="{{ route('user.notifications.index') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">notifications</span>
                                    <span>Notifications</span>
                                </a>
                                <a href="{{ route('user.settings') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">settings</span>
                                    <span>Settings</span>
                                </a>
                                <a href="{{ route('user.security') }}" class="px-3.5 py-1.5 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base text-slate-400">shield</span>
                                    <span>Security</span>
                                </a>
                            </div>
                        </div>

                        {{-- Sticky Logout Footer --}}
                        <div class="border-t border-slate-100 bg-white shrink-0">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-3.5 py-2 hover:bg-red-50 text-red-600 font-semibold flex items-center gap-2.5 transition-colors">
                                    <span class="material-symbols-outlined text-base">logout</span>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-bold py-2 px-4 rounded-full transition-all flex items-center justify-center {{ $currentRoute === 'login' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100 font-semibold' }}">
                            Sign in
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-bold py-2 px-5 sm:px-6 rounded-full transition-all flex items-center justify-center {{ $currentRoute === 'register' ? 'bg-amber-500 text-slate-950 shadow-md' : ($currentRoute === 'login' ? 'text-slate-700 hover:text-slate-900 hover:bg-slate-100 font-semibold' : 'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-md active:scale-95') }}">
                            Sign up
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- ── MOBILE SLIDE-OVER DRAWER MENU ── -->
    <div x-cloak x-show="mobileMenuOpen" 
         class="fixed inset-0 z-50 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        
        <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <div class="fixed inset-y-0 left-0 w-80 bg-white shadow-2xl flex flex-col justify-between z-10 p-5 overflow-y-auto">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/bazaario-logo.png') }}" alt="Bazaario" class="h-7 w-auto object-contain">
                    </a>
                    <button @click="mobileMenuOpen = false" class="p-1 text-slate-400 hover:text-slate-700">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                </div>

                <!-- Navigation Links -->
                <div class="py-4 space-y-1 font-semibold text-sm">
                    <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-xl {{ $isOnShop ? 'bg-slate-900 text-white' : 'hover:bg-slate-100 text-slate-900' }}">Home</a>
                    <a href="{{ route('auctions.index') }}" class="block px-3 py-2 rounded-xl {{ $isOnAuctions ? 'bg-slate-900 text-white' : 'hover:bg-rose-50 text-rose-700' }} flex items-center gap-2">
                        <span>⚡ Live Auctions</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-rose-200 text-rose-800">LIVE</span>
                    </a>
                    <a href="{{ route('products.index', ['filter' => 'deals']) }}" class="block px-3 py-2 rounded-xl {{ $isOnDeals ? 'bg-slate-900 text-white' : 'hover:bg-slate-100 text-slate-900' }}">Today's Deals</a>
                    <a href="{{ url('/seller/register') }}" class="block px-3 py-2 rounded-xl bg-amber-50 text-amber-900 font-bold">Sell on Bazaario →</a>
                </div>
            </div>

            <!-- Footer Action inside Drawer -->
            <div class="pt-4 border-t border-slate-100">
                @if($currentUser)
                    <a href="{{ route('user.dashboard') }}" class="w-full py-2.5 text-center bg-slate-900 text-white font-bold rounded-xl block">
                        Go to Dashboard
                    </a>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="py-2.5 text-center font-bold rounded-xl transition-all {{ $currentRoute === 'login' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'border border-slate-200 text-slate-800' }}">Sign in</a>
                        <a href="{{ route('register') }}" class="py-2.5 text-center font-bold rounded-xl transition-all {{ $currentRoute === 'register' ? 'bg-amber-500 text-slate-950 shadow-xs' : ($currentRoute === 'login' ? 'border border-slate-200 text-slate-800' : 'bg-amber-500 text-slate-950') }}">Get started</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ── MOBILE BOTTOM FIXED NAVIGATION BAR ── -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 py-2 px-4 flex items-center justify-around text-slate-600 text-[10px] font-semibold">
    <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 {{ $currentRoute === 'home' ? 'text-slate-900 font-bold' : '' }}">
        <span class="material-symbols-outlined text-xl">home</span>
        <span>Home</span>
    </a>
    <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 {{ $currentRoute === 'products.index' ? 'text-slate-900 font-bold' : '' }}">
        <span class="material-symbols-outlined text-xl">search</span>
        <span>Search</span>
    </a>
    <a href="{{ route('auctions.index') }}" class="flex flex-col items-center gap-0.5 {{ $isOnAuctions ? 'text-slate-900 font-bold' : 'text-rose-600 font-bold' }}">
        <span class="material-symbols-outlined text-xl">bolt</span>
        <span>Auctions</span>
    </a>
    <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-0.5 relative {{ $currentRoute === 'cart.index' ? 'text-slate-900 font-bold' : '' }}">
        <span class="material-symbols-outlined text-xl">shopping_cart</span>
        <span>Cart</span>
        @if($cartCount > 0)
            <span class="absolute -top-1 right-2 w-3.5 h-3.5 bg-slate-900 text-amber-400 rounded-full text-[9px] font-bold flex items-center justify-center">
                {{ $cartCount }}
            </span>
        @endif
    </a>
    <a href="{{ $currentUser ? route('user.dashboard') : route('login') }}" class="flex flex-col items-center gap-0.5">
        <span class="material-symbols-outlined text-xl">person</span>
        <span>Account</span>
    </a>
</div>