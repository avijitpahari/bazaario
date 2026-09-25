@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use App\Models\Category;

    $currentRoute = Route::currentRouteName();
    $currentUser = Auth::guard('user')->user() ?? Auth::guard('admin')->user() ?? Auth::guard('seller')->user() ?? Auth::user();
    
    $navCategories = Cache::store('file')->remember('nav_categories_list', 600, function () {
        return Category::where('status', 'active')->take(6)->get();
    });

    $cartCount = count(session('cart', []));
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
            $isOnShop = in_array($currentRoute, ['products.index', 'products.show', 'products.category', 'user.wishlist.index']);
            $isOnAuctions = $currentRoute === 'products.index' && request('sale_type') === 'auction';
            $isOnDeals = $currentRoute === 'products.index' && request('filter') === 'deals';
        @endphp
        <nav class="hidden lg:flex items-center gap-1.5 text-sm font-semibold text-slate-700">
            <!-- Home → Products Catalog -->
            <a href="{{ route('products.index') }}" 
               class="px-3.5 py-1.5 rounded-full transition-all {{ $isOnShop ? 'bg-slate-900 text-white font-bold' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                Home
            </a>

            <!-- Categories Mega Dropdown -->
            <div class="relative" @mouseenter="categoriesOpen = true" @mouseleave="categoriesOpen = false">
                <button @click="categoriesOpen = !categoriesOpen" 
                        class="px-3.5 py-1.5 flex items-center gap-0.5 rounded-full transition-all hover:bg-slate-100 hover:text-slate-900 cursor-pointer">
                    <span>Categories</span>
                    <span class="material-symbols-outlined text-base transition-transform duration-200" :class="categoriesOpen ? 'rotate-180' : ''">expand_more</span>
                </button>

                <!-- Mega Dropdown Panel -->
                <div x-cloak x-show="categoriesOpen"
                     x-transition:enter="transition ease-out duration-150 transform"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100 transform"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute top-full mt-2 left-0 w-[540px] bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 z-50 grid grid-cols-3 gap-6">
                    
                    <div>
                        <h4 class="font-mono text-[11px] font-bold uppercase text-slate-400 tracking-wider mb-2.5 pb-1 border-b border-slate-100">Electronics</h4>
                        <ul class="space-y-1.5 text-xs text-slate-600">
                            <li><a href="{{ route('products.index', ['search' => 'Smartphone']) }}" class="hover:text-amber-600 transition-colors">Smartphones</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Laptop']) }}" class="hover:text-amber-600 transition-colors">Laptops</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Headphones']) }}" class="hover:text-amber-600 transition-colors">Headphones</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Camera']) }}" class="hover:text-amber-600 transition-colors">Cameras</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-mono text-[11px] font-bold uppercase text-slate-400 tracking-wider mb-2.5 pb-1 border-b border-slate-100">Fashion</h4>
                        <ul class="space-y-1.5 text-xs text-slate-600">
                            <li><a href="{{ route('products.index', ['search' => 'Men']) }}" class="hover:text-amber-600 transition-colors">Men's Wear</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Women']) }}" class="hover:text-amber-600 transition-colors">Women's Wear</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Shoes']) }}" class="hover:text-amber-600 transition-colors">Shoes & Sneakers</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Leather']) }}" class="hover:text-amber-600 transition-colors">Leather Goods</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-mono text-[11px] font-bold uppercase text-slate-400 tracking-wider mb-2.5 pb-1 border-b border-slate-100">Artisan & Home</h4>
                        <ul class="space-y-1.5 text-xs text-slate-600">
                            <li><a href="{{ route('products.index', ['search' => 'Ceramic']) }}" class="hover:text-amber-600 transition-colors">Handmade Ceramics</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Furniture']) }}" class="hover:text-amber-600 transition-colors">Modern Furniture</a></li>
                            <li><a href="{{ route('products.index', ['search' => 'Pottery']) }}" class="hover:text-amber-600 transition-colors">Traditional Pottery</a></li>
                            <li><a href="{{ route('products.index') }}" class="font-bold text-amber-600 hover:underline flex items-center gap-0.5 mt-2">
                                <span>Browse All</span> <span>→</span>
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Auctions (Distinct Live Identity) -->
            <a href="{{ route('products.index', ['sale_type' => 'auction']) }}" 
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

        <!-- 3. PROMINENT SEARCH BAR WITH FOCUS DROPDOWN -->
        <div class="flex-1 max-w-md mx-2 relative" @click.outside="searchFocus = false">
            <form action="{{ route('products.index') }}" method="GET" class="relative">
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-xl pointer-events-none">search</span>
                    <input type="text" 
                           name="search"
                           @focus="searchFocus = true"
                           placeholder="Search products, brands & categories..."
                           class="w-full bg-slate-100 hover:bg-slate-100/80 focus:bg-white text-slate-900 text-xs sm:text-sm rounded-full pl-10 pr-4 py-2.5 border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-all shadow-inner">
                </div>
            </form>

            <!-- Search Focus Dropdown -->
            <div x-cloak x-show="searchFocus" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 z-50 text-xs">
                
                <div class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Recent Searches</div>
                <div class="flex flex-wrap gap-1.5 mb-4">
                    <a href="{{ route('products.index', ['search' => 'Headphones']) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-amber-100 rounded-full text-slate-700 transition-colors flex items-center gap-1">
                        <span>↻ headphones</span>
                    </a>
                    <a href="{{ route('products.index', ['search' => 'Keyboard']) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-amber-100 rounded-full text-slate-700 transition-colors flex items-center gap-1">
                        <span>↻ mechanical keyboard</span>
                    </a>
                </div>

                <div class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Popular Categories</div>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('products.index', ['search' => 'Headphones']) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-100 font-semibold text-slate-800 flex items-center gap-1.5">
                        <span>🎧 Headphones</span>
                    </a>
                    <a href="{{ route('products.index', ['search' => 'Laptop']) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-100 font-semibold text-slate-800 flex items-center gap-1.5">
                        <span>💻 Laptops</span>
                    </a>
                    <a href="{{ route('products.index', ['search' => 'Phone']) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-100 font-semibold text-slate-800 flex items-center gap-1.5">
                        <span>📱 Phones</span>
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

            <!-- Notifications 🔔 -->
            <div class="relative" @click.outside="notificationsOpen = false">
                <button @click="notificationsOpen = !notificationsOpen" 
                        class="relative p-2 text-slate-700 hover:text-slate-900 rounded-full hover:bg-slate-100 transition-colors"
                        aria-label="Notifications">
                    <span class="material-symbols-outlined text-2xl">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-amber-500 rounded-full ring-2 ring-white"></span>
                </button>

                <!-- Notifications Popover -->
                <div x-cloak x-show="notificationsOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute top-full right-0 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 py-3 z-50 text-xs">
                    
                    <div class="px-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span class="font-display font-bold text-slate-900 text-sm">Notifications</span>
                        <span class="text-[10px] font-mono text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded font-bold">3 New</span>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
                        <a href="{{ route('user.orders.index') }}" class="p-3 block hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-2 text-blue-600 font-semibold mb-0.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <span>Order Shipped</span>
                            </div>
                            <p class="text-slate-600 line-clamp-1">Your item BZ-104 has been dispatched by seller.</p>
                            <span class="text-[10px] font-mono text-slate-400 mt-1 block">2 minutes ago</span>
                        </a>

                        <a href="{{ route('products.index', ['sale_type' => 'auction']) }}" class="p-3 block hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-2 text-amber-600 font-semibold mb-0.5">
                                <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                                <span>New Auction Bid</span>
                            </div>
                            <p class="text-slate-600 line-clamp-1">Someone outbid your bid on Vintage Leica M3.</p>
                            <span class="text-[10px] font-mono text-slate-400 mt-1 block">1 hour ago</span>
                        </a>
                    </div>

                    <div class="pt-2 px-3 border-t border-slate-100 text-center">
                        <a href="{{ route('user.notifications.index') }}" class="text-xs font-bold text-slate-900 hover:text-amber-600">
                            View all notifications →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cart 🛒 with Mini-Cart Popover -->
            <div class="relative" 
                 @mouseenter="cartOpen = true" 
                 @mouseleave="cartOpen = false" 
                 @click.outside="cartOpen = false">
                <a href="{{ route('cart.index') }}" 
                   class="relative p-2 text-slate-700 hover:text-slate-900 rounded-full hover:bg-slate-100 transition-colors flex items-center"
                   aria-label="Cart">
                    <span class="material-symbols-outlined text-2xl">shopping_cart</span>
                    @if($cartCount > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-slate-900 text-amber-400 rounded-full text-[10px] font-bold flex items-center justify-center shadow-xs">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <!-- Mini Cart Popover -->
                <div x-cloak x-show="cartOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute top-full right-0 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 z-50 text-xs">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="font-display font-bold text-slate-900 text-sm">Your Cart ({{ $cartCount }})</span>
                        <a href="{{ route('cart.index') }}" class="text-[11px] font-semibold text-amber-600 hover:underline">View Full Cart</a>
                    </div>

                    @if($cartCount > 0)
                        <div class="py-3 space-y-3 max-h-56 overflow-y-auto">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 shrink-0 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=200&auto=format&fit=crop&q=80" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="font-semibold text-slate-900 truncate">Handcrafted Leather Bag</h5>
                                    <span class="text-slate-500 font-mono text-[11px]">₹2,499 × 1</span>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <div class="flex items-center justify-between font-bold text-slate-900 text-sm">
                                <span>Subtotal</span>
                                <span class="font-mono">₹2,499</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('cart.index') }}" class="py-2 text-center bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-semibold transition-colors">
                                    View Cart
                                </a>
                                <a href="{{ route('checkout.index') }}" class="py-2 text-center bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl font-bold transition-colors">
                                    Checkout
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="py-6 text-center text-slate-500">
                            <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1">shopping_bag</span>
                            <span>Your cart is empty</span>
                        </div>
                    @endif
                </div>
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
                         class="absolute top-full mt-2 right-0 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-50 text-xs font-medium max-h-[80vh] overflow-y-auto">

                        {{-- User Identity Header --}}
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-3">
                            @if($currentUser->profile_image)
                                <img src="{{ Storage::url($currentUser->profile_image) }}" alt="{{ $currentUser->name }}" class="w-10 h-10 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(mb_substr($currentUser->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 text-sm truncate">{{ $currentUser->name }}</div>
                                <div class="text-slate-400 font-mono text-[11px] truncate">{{ $currentUser->email }}</div>
                            </div>
                        </div>

                        {{-- Dashboard --}}
                        @php
                            $dashRoute = match($currentUser->role) {
                                'admin' => route('admin.dashboard'),
                                'seller' => route('seller.dashboard'),
                                default => route('user.dashboard'),
                            };
                        @endphp
                        <div class="py-1">
                            <a href="{{ $dashRoute }}" class="px-4 py-2 hover:bg-amber-50 text-slate-700 hover:text-amber-700 flex items-center gap-2.5 font-semibold">
                                <span class="material-symbols-outlined text-base text-amber-500">dashboard</span>
                                <span>My Dashboard</span>
                            </a>
                        </div>

                        {{-- Group: Shopping --}}
                        <div class="border-t border-slate-100 pt-1 pb-1">
                            <div class="px-4 pt-1.5 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Shopping</div>
                            <a href="{{ route('user.orders.index') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">package_2</span>
                                <span>My Orders</span>
                            </a>
                            <a href="{{ route('user.wishlist.index') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">favorite</span>
                                <span>Wishlist</span>
                            </a>
                            <a href="{{ route('cart.index') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">shopping_cart</span>
                                <span>Cart</span>
                            </a>
                            <a href="{{ route('user.returns') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">assignment_return</span>
                                <span>Returns</span>
                            </a>
                            <a href="{{ route('user.invoices') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">receipt_long</span>
                                <span>Invoices</span>
                            </a>
                            <a href="{{ route('user.coupons') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">local_offer</span>
                                <span>My Coupons</span>
                            </a>
                        </div>

                        {{-- Group: Auctions & Discovery --}}
                        <div class="border-t border-slate-100 pt-1 pb-1">
                            <div class="px-4 pt-1.5 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Auctions & Discovery</div>
                            <a href="{{ route('user.bids') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">gavel</span>
                                <span>My Bids</span>
                            </a>
                            <a href="{{ route('user.auctions') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">bolt</span>
                                <span>My Auctions</span>
                            </a>
                            <a href="{{ route('user.compare') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">compare</span>
                                <span>Compare Products</span>
                            </a>
                            <a href="{{ route('user.recommendations') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">recommend</span>
                                <span>Recommendations</span>
                            </a>
                        </div>

                        {{-- Group: Account --}}
                        <div class="border-t border-slate-100 pt-1 pb-1">
                            <div class="px-4 pt-1.5 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Account</div>
                            <a href="{{ route('user.profile') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">person</span>
                                <span>My Profile</span>
                            </a>
                            <a href="{{ route('user.reviews.index') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">rate_review</span>
                                <span>My Reviews</span>
                            </a>
                            <a href="{{ route('user.addresses.index') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">location_on</span>
                                <span>Addresses</span>
                            </a>
                            <a href="{{ route('user.notifications.index') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">notifications</span>
                                <span>Notifications</span>
                            </a>
                            <a href="{{ route('user.settings') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">settings</span>
                                <span>Settings</span>
                            </a>
                            <a href="{{ route('user.security') }}" class="px-4 py-2 hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-slate-400">shield</span>
                                <span>Security</span>
                            </a>
                        </div>

                        {{-- Logout --}}
                        <div class="pt-1 border-t border-slate-100">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 hover:bg-red-50 text-red-600 font-semibold flex items-center gap-2.5">
                                    <span class="material-symbols-outlined text-base">logout</span>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="text-xs font-bold py-2 rounded-full transition-all {{ $currentRoute === 'login' ? 'bg-amber-500 text-slate-950 shadow-xs px-4' : 'text-slate-700 hover:text-slate-900 font-semibold px-3' }}">
                            Sign in
                        </a>
                        <a href="{{ route('register') }}" class="text-xs font-bold py-2 rounded-full transition-all {{ $currentRoute === 'register' ? 'bg-amber-500 text-slate-950 shadow-xs px-4' : ($currentRoute === 'login' ? 'text-slate-700 hover:text-slate-900 font-semibold px-3' : 'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-xs px-4') }}">
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
                    <a href="{{ route('products.index', ['sale_type' => 'auction']) }}" class="block px-3 py-2 rounded-xl {{ $isOnAuctions ? 'bg-slate-900 text-white' : 'hover:bg-rose-50 text-rose-700' }} flex items-center gap-2">
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
    <a href="{{ route('products.index', ['sale_type' => 'auction']) }}" class="flex flex-col items-center gap-0.5 text-rose-600 font-bold">
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