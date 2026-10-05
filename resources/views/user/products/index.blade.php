@php
    use Illuminate\Support\Str;

    $categoryList = collect(['All Categories'])->merge(($categories ?? collect())->pluck('name'))->values();
    if ($categoryList->count() <= 1) {
        $categoryList = collect(['All Categories', 'Artisan & Handmade Crafts', 'Electronics & Gadgets', 'Fashion & Apparel', 'Home & Living', 'Rare Collectibles & Antiques', 'Books & Fine Stationery']);
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Marketplace Catalog — Bazaario</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Compiled Tailwind CSS & App JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        * { -webkit-tap-highlight-color: transparent; }
        body {
            background-color: #FFFDF8;
            background-image: 
                radial-gradient(circle at 12% 10%, rgba(245, 166, 35, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 88% 18%, rgba(15, 23, 42, 0.03) 0%, transparent 40%);
            background-attachment: fixed;
        }
    </style>
</head>

<body class="bg-[#FFFDF8] font-sans text-slate-800 antialiased min-h-screen flex flex-col selection:bg-amber-500 selection:text-slate-950"
      x-data="productCatalog()">

    <!-- Include Global Customer Top Navbar -->
    @include('components.nav-user')

    <!-- Toast Notification Banner -->
    <div x-cloak x-show="toast.visible" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
         class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 left-4 sm:left-auto z-50 flex items-center gap-3 px-4 sm:px-5 py-3 rounded-2xl shadow-2xl border text-xs sm:text-sm font-medium"
         :class="toast.type === 'cart' ? 'bg-slate-900 text-white border-slate-800' : 'bg-amber-500 text-slate-950 border-amber-400'">
        <span class="material-symbols-outlined text-lg sm:text-xl shrink-0" x-text="toast.icon"></span>
        <span class="truncate" x-text="toast.message"></span>
    </div>

    <!-- Main Container -->
    <main class="flex-1 w-full pt-20 pb-24 md:pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb & Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 sm:pb-6 border-b border-slate-200/80">
                <div>
                    <nav class="flex items-center gap-1.5 text-xs font-mono text-slate-500 mb-1">
                        <a href="{{ url('/') }}" class="hover:text-amber-600 transition-colors">Home</a>
                        <span>/</span>
                        <span class="text-slate-900 font-semibold">Catalog</span>
                    </nav>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <h1 class="text-2xl sm:text-3xl font-display font-bold text-slate-900 tracking-tight">Marketplace Catalog</h1>
                        <span class="text-xs font-mono font-bold px-2.5 py-0.5 bg-slate-900 text-white rounded-full">
                            {{ $products->total() }} items
                        </span>
                    </div>
                </div>

                <!-- Right Trust Strip -->
                <div class="flex items-center gap-2 shrink-0">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-mono font-semibold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        100% Escrow Secured
                    </div>
                </div>
            </div>

            <!-- Search Results Feedback Banner (Feature 22) -->
            @if(!empty($search))
                <div class="mt-4 flex items-center justify-between p-3.5 bg-amber-50 rounded-2xl border border-amber-200/80 text-xs">
                    <div class="flex items-center gap-2 text-slate-800">
                        <span class="material-symbols-outlined text-amber-600 text-lg">search</span>
                        <span>Showing <strong>{{ $products->total() }}</strong> matching results for "<strong>{{ $search }}</strong>"</span>
                    </div>
                    <a href="{{ route('products.index', request()->except(['search', 'page'])) }}" class="text-amber-900 hover:text-amber-950 font-bold underline flex items-center gap-1">
                        <span>Clear search</span>
                        <span class="material-symbols-outlined text-xs">close</span>
                    </a>
                </div>
            @endif

            <!-- Active Filters Summary Pills -->
            @php
                $catFilter = is_array(request('category')) ? (request('category')[0] ?? null) : request('category');
                $hasActiveFilters = ($catFilter && $catFilter !== 'all' && $catFilter !== 'All Categories') || request('min_price') || request('max_price') || request('min_rating') || request('radius') || request('verified_only') || request('in_stock_only');
            @endphp
            @if($hasActiveFilters)
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="font-mono text-slate-400 font-bold uppercase text-[10px]">Active Filters:</span>
                    @if($catFilter && $catFilter !== 'all' && $catFilter !== 'All Categories')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200">
                            <span>Category: {{ $catFilter }}</span>
                            <a href="{{ route('products.index', request()->except(['category', 'page'])) }}" class="hover:text-red-500">&times;</a>
                        </span>
                    @endif
                    @if(request('min_price') || request('max_price'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200 font-mono">
                            <span>Price: ₹{{ request('min_price', 0) }} - ₹{{ request('max_price', 'Any') }}</span>
                            <a href="{{ route('products.index', request()->except(['min_price', 'max_price', 'page'])) }}" class="hover:text-red-500">&times;</a>
                        </span>
                    @endif
                    @if(request('min_rating'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200">
                            <span>Rating: {{ request('min_rating') }}★+</span>
                            <a href="{{ route('products.index', request()->except(['min_rating', 'page'])) }}" class="hover:text-red-500">&times;</a>
                        </span>
                    @endif
                    @if(request('radius'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200 font-mono">
                            <span>Radius: {{ request('radius') }} km</span>
                            <a href="{{ route('products.index', request()->except(['radius', 'page'])) }}" class="hover:text-red-500">&times;</a>
                        </span>
                    @endif
                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-800 underline ml-1">
                        Reset All
                    </a>
                </div>
            @endif

            <!-- Level 0: Interactive Search & Controls Bar -->
            <div class="mt-4 bg-white rounded-2xl p-3 sm:p-4 shadow-xs border border-slate-200/80 flex flex-col gap-3">
                <!-- Search Form (Feature 21 & Feature 22) -->
                <form action="{{ route('products.index') }}" method="GET" class="relative w-full flex items-center">
                    @if($catFilter && $catFilter !== 'all') <input type="hidden" name="category" value="{{ $catFilter }}"> @endif
                    @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
                    @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
                    @if(request('min_rating')) <input type="hidden" name="min_rating" value="{{ request('min_rating') }}"> @endif
                    @if(request('radius')) <input type="hidden" name="radius" value="{{ request('radius') }}"> @endif
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg sm:text-xl pointer-events-none">search</span>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search products, descriptions, brands, or verified sellers..."
                           class="w-full pl-9 sm:pl-10 pr-24 py-2 sm:py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition-all">
                    
                    <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white rounded-lg text-xs font-semibold transition-colors">
                        Search
                    </button>
                </form>

                <!-- Sort & Filter Strip -->
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                    <!-- Filter Trigger Button -->
                    <button @click="mobileFiltersOpen = !mobileFiltersOpen" 
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-slate-900 text-white hover:bg-slate-800 rounded-xl text-xs font-semibold active:scale-95 transition-all shadow-xs">
                        <span class="material-symbols-outlined text-base">tune</span>
                        <span>Filter Options</span>
                        @if($hasActiveFilters)
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        @endif
                    </button>

                    <!-- Sort By Selector (Feature 23) -->
                    <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-1.5">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if($catFilter && $catFilter !== 'all') <input type="hidden" name="category" value="{{ $catFilter }}"> @endif
                        @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
                        @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
                        @if(request('min_rating')) <input type="hidden" name="min_rating" value="{{ request('min_rating') }}"> @endif
                        @if(request('radius')) <input type="hidden" name="radius" value="{{ request('radius') }}"> @endif

                        <div class="relative">
                            <select name="sort" 
                                    onchange="this.form.submit()" 
                                    class="appearance-none bg-slate-100 hover:bg-slate-200/80 border border-slate-200 rounded-xl pl-3 pr-8 py-2 text-xs font-semibold text-slate-800 cursor-pointer focus:outline-none focus:ring-1 focus:ring-slate-900">
                                <option value="newest" {{ request('sort', $sortBy) === 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                                <option value="price_low" {{ request('sort', $sortBy) === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort', $sortBy) === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="rating" {{ request('sort', $sortBy) === 'rating' ? 'selected' : '' }}>Top Customer Rating</option>
                                <option value="popular" {{ request('sort', $sortBy) === 'popular' ? 'selected' : '' }}>Most Popular</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">expand_more</span>
                        </div>
                    </form>

                    <!-- View Mode Grid/List Toggle -->
                    <div class="flex items-center bg-slate-100 p-0.5 rounded-xl border border-slate-200 shrink-0">
                        <button @click="viewMode = 'grid'" 
                                :class="viewMode === 'grid' ? 'bg-white shadow-xs text-slate-900 font-semibold' : 'text-slate-500 hover:text-slate-800'"
                                class="p-1.5 rounded-lg transition-all" title="Grid View">
                            <span class="material-symbols-outlined text-lg">grid_view</span>
                        </button>
                        <button @click="viewMode = 'list'" 
                                :class="viewMode === 'list' ? 'bg-white shadow-xs text-slate-900 font-semibold' : 'text-slate-500 hover:text-slate-800'"
                                class="p-1.5 rounded-lg transition-all" title="List View">
                            <span class="material-symbols-outlined text-lg">view_list</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Level 1: Category Pill Tags Filter Bar (Feature 17) -->
            <div class="mt-4 flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 -mx-3 px-3 sm:mx-0 sm:px-0">
                <a href="{{ route('products.index', array_merge(request()->except(['category', 'page']), ['category' => 'all'])) }}"
                   class="px-3.5 sm:px-4 py-1.5 rounded-full text-xs whitespace-nowrap transition-all flex items-center gap-1 shrink-0 {{ ($selectedCategory ?? 'All Categories') === 'All Categories' || ($selectedCategory ?? '') === 'all' ? 'bg-slate-900 text-white shadow-xs font-semibold' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/90' }}">
                    <span>All Categories</span>
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('products.index', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}"
                       class="px-3.5 sm:px-4 py-1.5 rounded-full text-xs whitespace-nowrap transition-all flex items-center gap-1 shrink-0 {{ ($selectedCategory ?? '') === $cat->slug || ($selectedCategory ?? '') === $cat->name ? 'bg-slate-900 text-white shadow-xs font-semibold' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/90' }}">
                        <span>{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Level 2: Main Catalog Grid (Feature 16, 21, 22) -->
            <div class="mt-6 w-full">
                @if($products->isEmpty())
                    <!-- Zero Results State (Feature 22) -->
                    <div class="bg-white rounded-3xl p-8 sm:p-14 text-center border border-slate-200/80 shadow-xs">
                        <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">sentiment_dissatisfied</span>
                        <h3 class="text-lg sm:text-xl font-display font-bold text-slate-900 mb-2">No products match your criteria</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mb-6 max-w-md mx-auto">
                            @if(!empty($search))
                                We couldn't find any products matching "<strong>{{ e($search) }}</strong>". Try checking for typos or removing filter constraints.
                            @else
                                Try expanding your distance radius, adjusting price boundaries, or clearing filter tags.
                            @endif
                        </p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-2xl text-xs font-semibold hover:bg-amber-500 hover:text-slate-950 transition-colors shadow-sm">
                            <span>Clear All Filters</span>
                            <span class="material-symbols-outlined text-sm">refresh</span>
                        </a>
                    </div>
                @else
                    <!-- Product Grid Cards (Feature 16) -->
                    <div :class="viewMode === 'grid' ? 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3' : 'space-y-4'">
                        @foreach($products as $product)
                            @php
                                $primaryImg = $product->primaryImage ? $product->primaryImage->image_path : ($product->images->first() ? $product->images->first()->image_path : null);
                                $imgUrl = $primaryImg ? (Str::startsWith($primaryImg, ['http://', 'https://']) ? $primaryImg : asset($primaryImg)) : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';
                                $sellerProfile = $product->seller ? $product->seller->sellerProfile : null;
                                $sellerName = $sellerProfile ? $sellerProfile->shop_name : ($product->seller ? $product->seller->name : 'Bazaario Artisan');
                                $ratingDisplay = $product->average_rating ? number_format($product->average_rating, 1) : 'New';
                                $reviewsDisplay = ($product->total_reviews ?? 0) . ' reviews';
                                $originalPrice = round($product->price * 1.25);
                                $badge = $product->price > 10000 ? 'Premium' : (($product->average_rating && $product->average_rating >= 4.8) ? 'Top Pick' : 'Verified');
                                $badgeClass = $product->price > 10000 ? 'bg-slate-950 text-amber-400 font-bold' : (($product->average_rating && $product->average_rating >= 4.8) ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-emerald-600 text-white');
                            @endphp

                            <!-- Grid Mode Card -->
                            <div x-show="viewMode === 'grid'" 
                                 class="group bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-xl hover:border-slate-300 transition-all duration-300 flex flex-col justify-between relative h-full">
                                
                                <div>
                                    <!-- Product Image & Badge -->
                                    <div class="relative aspect-[4/3] w-full bg-slate-100 overflow-hidden">
                                        <a href="{{ route('products.show', ['slug' => $product->slug]) }}" class="block w-full h-full">
                                            <img src="{{ $imgUrl }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                                 loading="lazy"
                                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';">
                                        </a>

                                        <div class="absolute top-2 left-2 z-10">
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold uppercase tracking-wider shadow-xs backdrop-blur-md {{ $badgeClass }}">
                                                {{ $badge }}
                                            </span>
                                        </div>

                                        <button @click="toggleWishlist({{ $product->id }}, '{{ addslashes($product->name) }}')" 
                                                class="absolute top-2 right-2 p-1.5 rounded-full bg-white/90 backdrop-blur-md text-slate-700 hover:text-red-500 hover:bg-white transition-all shadow-xs z-10"
                                                :class="{'text-red-500': isInWishlist({{ $product->id }})}"
                                                aria-label="Wishlist">
                                            <span class="material-symbols-outlined text-sm leading-none block">favorite</span>
                                        </button>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-2.5 sm:p-3">
                                        <div class="flex items-center justify-between gap-1 mb-1">
                                            <span class="text-[9px] sm:text-[10px] font-mono font-medium text-slate-400 uppercase tracking-wider truncate max-w-[90px] sm:max-w-none">
                                                {{ $product->category ? $product->category->name : 'General' }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 shrink-0">
                                                <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>In Stock</span>
                                            </span>
                                        </div>

                                        <h3 class="font-display font-bold text-slate-900 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-amber-600 transition-colors">
                                            <a href="{{ route('products.show', ['slug' => $product->slug]) }}">{{ $product->name }}</a>
                                        </h3>

                                        <div class="flex items-center justify-between gap-1 mt-1 text-[10px] sm:text-xs">
                                            <div class="flex items-center gap-0.5 text-slate-600 truncate min-w-0">
                                                <span class="truncate max-w-[85px] sm:max-w-[110px]">{{ $sellerName }}</span>
                                                <span class="material-symbols-outlined text-blue-600 text-[12px] shrink-0" title="Verified Seller">verified</span>
                                            </div>
                                            <div class="flex items-center text-amber-500 font-bold shrink-0">
                                                <span class="text-xs">★</span>
                                                <span class="text-slate-800 ml-0.5">{{ $product->average_rating ? number_format($product->average_rating, 1) : 'New' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Footer: Price & Actions -->
                                <div class="p-2.5 sm:p-3 pt-0">
                                    <div class="pt-2 border-t border-slate-100 flex flex-col gap-1.5">
                                        <div class="flex items-baseline justify-between gap-1">
                                            <div class="flex items-baseline gap-1 truncate">
                                                <span class="text-xs sm:text-sm font-mono font-bold text-slate-900">₹{{ number_format($product->price, 2) }}</span>
                                                @if($originalPrice > $product->price)
                                                    <span class="text-[10px] font-mono text-slate-400 line-through">₹{{ number_format($originalPrice, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1.5 w-full">
                                            <form action="{{ route('cart.store') }}" method="POST" class="shrink-0">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-semibold transition-all flex items-center justify-center shadow-2xs active:scale-95" title="Add to Cart">
                                                    <span class="material-symbols-outlined text-sm">shopping_cart</span>
                                                </button>
                                            </form>
                                            <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="buy_now" value="1">
                                                <button type="submit" class="w-full py-1.5 px-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-[11px] transition-all flex items-center justify-center gap-1 shadow-2xs active:scale-95 whitespace-nowrap" title="Buy Now">
                                                    <span class="material-symbols-outlined text-xs shrink-0">bolt</span>
                                                    <span class="font-bold tracking-tight">Buy Now</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- List Mode Row -->
                            <div x-show="viewMode === 'list'" 
                                 class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs hover:shadow-md transition-all flex flex-col sm:flex-row items-start sm:items-center gap-4 justify-between">
                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    <div class="w-20 h-20 rounded-xl bg-slate-100 shrink-0 overflow-hidden relative">
                                        <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5 text-xs font-mono text-slate-500 mb-0.5">
                                            <span class="uppercase text-slate-400 truncate">{{ $product->category ? $product->category->name : 'General' }}</span>
                                            <span>•</span>
                                            <span class="text-slate-700 font-sans truncate font-medium">{{ $sellerName }}</span>
                                        </div>
                                        <h3 class="font-display font-bold text-slate-900 text-sm line-clamp-1 hover:text-amber-600 transition-colors">
                                            <a href="{{ route('products.show', ['slug' => $product->slug]) }}">{{ $product->name }}</a>
                                        </h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-amber-500 text-xs font-bold">★ <span class="text-slate-700 font-normal">{{ $product->average_rating ? number_format($product->average_rating, 1) : 'New' }}</span></span>
                                            <span class="text-[10px] font-mono text-slate-400">({{ ($product->total_reviews ?? 0) . ' reviews' }})</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto border-t sm:border-t-0 border-slate-100 pt-2 sm:pt-0">
                                    <div>
                                        <span class="text-base font-mono font-bold text-slate-900">₹{{ number_format($product->price, 2) }}</span>
                                        @if($originalPrice > $product->price)
                                            <span class="text-xs font-mono text-slate-400 line-through block">₹{{ number_format($originalPrice, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-semibold transition-all flex items-center gap-1 shadow-2xs">
                                                <span class="material-symbols-outlined text-base">shopping_cart</span>
                                                <span>Add</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="buy_now" value="1">
                                            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs transition-all flex items-center gap-1 shadow-xs active:scale-95">
                                                <span class="material-symbols-outlined text-base">bolt</span>
                                                <span>Buy Now</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Server-Side Pagination Links (Feature 16) -->
                    <div class="mt-10 flex flex-col items-center justify-center gap-3 pt-6 border-t border-slate-200/80">
                        <div>
                            {{ $products->links() }}
                        </div>
                        <span class="text-xs font-mono text-slate-500">
                            Showing <strong class="text-slate-900">{{ $products->firstItem() ?? 0 }}</strong> to <strong class="text-slate-900">{{ $products->lastItem() ?? 0 }}</strong> of <strong class="text-slate-900">{{ $products->total() }}</strong> curated marketplace items
                        </span>
                    </div>
                @endif
            </div>

        </div>
    </main>

    <!-- ── SLIDE-OVER FILTERS DRAWER (Features 17, 18, 19, 20) ── -->
    <div x-cloak x-show="mobileFiltersOpen" 
         class="fixed inset-0 z-50 overflow-hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div @click="mobileFiltersOpen = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs"></div>

        <!-- Drawer Content Container (Slides from left) -->
        <div class="fixed inset-y-0 left-0 w-80 sm:w-96 bg-white shadow-2xl flex flex-col overflow-hidden z-10"
             x-show="mobileFiltersOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            
            <!-- Handle & Header -->
            <div class="p-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-700 text-xl">tune</span>
                    <h3 class="font-display font-bold text-slate-900 text-base">Filter Marketplace Items</h3>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('products.index') }}" class="text-xs font-mono text-amber-600 font-semibold underline">
                        Reset All
                    </a>
                    <button @click="mobileFiltersOpen = false" class="p-1 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            </div>

            <!-- Scrollable Filter Form (Submits to Server) -->
            <form action="{{ route('products.index') }}" method="GET" class="flex flex-col flex-1 overflow-hidden">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="p-5 overflow-y-auto space-y-6 flex-1 text-xs">
                    
                    <!-- Feature 17: Category Filter -->
                    <div>
                        <label class="font-mono font-bold text-slate-800 uppercase tracking-wider block mb-2">Category</label>
                        <select name="category" class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 font-medium text-xs focus:ring-1 focus:ring-slate-900">
                            <option value="all">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ (request('category') === $cat->slug || ($selectedCategory ?? '') === $cat->slug) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Feature 18: Price Range Filter (Min & Max Bounds) -->
                    <div class="pt-4 border-t border-slate-100">
                        <label class="font-mono font-bold text-slate-800 uppercase tracking-wider block mb-2">Price Range (₹ INR)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] font-mono text-slate-400 block mb-1">Min Price</span>
                                <input type="number" 
                                       name="min_price" 
                                       value="{{ request('min_price') }}" 
                                       placeholder="₹ 0" 
                                       min="0"
                                       class="w-full p-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono text-slate-800 focus:ring-1 focus:ring-slate-900">
                            </div>
                            <div>
                                <span class="text-[10px] font-mono text-slate-400 block mb-1">Max Price</span>
                                <input type="number" 
                                       name="max_price" 
                                       value="{{ request('max_price') }}" 
                                       placeholder="₹ Max" 
                                       min="0"
                                       class="w-full p-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono text-slate-800 focus:ring-1 focus:ring-slate-900">
                            </div>
                        </div>
                    </div>

                    <!-- Feature 19: Minimum Seller Rating Filter -->
                    <div class="pt-4 border-t border-slate-100">
                        <label class="font-mono font-bold text-slate-800 uppercase tracking-wider block mb-2">Minimum Seller Rating</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach([4 => '4.0+ Stars', 3 => '3.0+ Stars', 2 => '2.0+ Stars'] as $rVal => $rLabel)
                                <label class="p-2.5 rounded-xl border flex items-center gap-1.5 cursor-pointer transition-all {{ request('min_rating') == $rVal ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                    <input type="radio" name="min_rating" value="{{ $rVal }}" {{ request('min_rating') == $rVal ? 'checked' : '' }} class="sr-only">
                                    <span class="text-amber-400">★</span>
                                    <span class="font-bold">{{ $rLabel }}</span>
                                </label>
                            @endforeach
                            <label class="p-2.5 rounded-xl border flex items-center justify-center cursor-pointer transition-all {{ !request('min_rating') ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                <input type="radio" name="min_rating" value="" {{ !request('min_rating') ? 'checked' : '' }} class="sr-only">
                                <span>All Ratings</span>
                            </label>
                        </div>
                    </div>

                    <!-- Feature 20: Filter by Distance / Radius from Sellers -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-mono font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm text-amber-600">near_me</span>
                                <span>Proximity Radius</span>
                            </label>
                            <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-[11px]" x-text="radiusValue > 0 ? radiusValue + ' km' : 'All Regions'"></span>
                        </div>
                        <input type="range" 
                               name="radius" 
                               min="0" 
                               max="200" 
                               step="10" 
                               x-model="radiusValue"
                               class="w-full accent-slate-900 h-2 bg-slate-200 rounded-lg cursor-pointer">
                        <div class="flex justify-between font-mono text-[10px] text-slate-400 mt-1">
                            <span>All Regions</span>
                            <span>50 km</span>
                            <span>100 km</span>
                            <span>200 km</span>
                        </div>
                        <input type="hidden" name="lat" value="{{ request('lat', 22.572646) }}">
                        <input type="hidden" name="lng" value="{{ request('lng', 88.363895) }}">
                    </div>

                    <!-- Additional Toggles -->
                    <div class="pt-4 border-t border-slate-100 space-y-2.5">
                        <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80 cursor-pointer">
                            <span class="font-medium text-slate-800 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-blue-600 text-base">verified</span>
                                Verified Sellers Only
                            </span>
                            <input type="checkbox" name="verified_only" value="1" {{ request('verified_only') ? 'checked' : '' }} class="w-4 h-4 rounded text-slate-900 accent-slate-900 focus:ring-0">
                        </label>
                        <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80 cursor-pointer">
                            <span class="font-medium text-slate-800 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-emerald-600 text-base">inventory_2</span>
                                In Stock Ready to Ship
                            </span>
                            <input type="checkbox" name="in_stock_only" value="1" {{ request('in_stock_only') ? 'checked' : '' }} class="w-4 h-4 rounded text-slate-900 accent-slate-900 focus:ring-0">
                        </label>
                    </div>

                </div>

                <!-- Drawer Bottom Apply Button -->
                <div class="p-4 border-t border-slate-100 bg-white shrink-0">
                    <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg active:scale-95 transition-all">
                        <span>Apply Filters &amp; View Catalog</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Global Footer -->
    <x-footer />

    <!-- Alpine.js Catalog State Handler -->
    <script>
        function productCatalog() {
            return {
                viewMode: 'grid',
                mobileFiltersOpen: false,
                radiusValue: {{ (int)request('radius', 0) }},
                wishlist: [],
                toast: {
                    visible: false,
                    message: '',
                    type: 'cart',
                    icon: 'shopping_cart'
                },

                init() {
                    try {
                        const dbWishlist = @json(auth()->guard('user')->check() && auth()->guard('user')->user()->wishlist ? auth()->guard('user')->user()->wishlist->items->pluck('product_id') : []);
                        if (dbWishlist && dbWishlist.length > 0) {
                            this.wishlist = dbWishlist;
                        } else {
                            const saved = localStorage.getItem('bazaario_wishlist');
                            if (saved) this.wishlist = JSON.parse(saved);
                        }
                    } catch(e) {}
                },

                isInWishlist(id) {
                    return this.wishlist.includes(id);
                },

                toggleWishlist(id, name) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('user.wishlist.store') }}';

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    const productIdInput = document.createElement('input');
                    productIdInput.type = 'hidden';
                    productIdInput.name = 'product_id';
                    productIdInput.value = id;
                    form.appendChild(productIdInput);

                    document.body.appendChild(form);
                    form.submit();
                },

                showToast(msg, type = 'cart', icon = 'check') {
                    this.toast.message = msg;
                    this.toast.type = type;
                    this.toast.icon = icon;
                    this.toast.visible = true;
                    setTimeout(() => {
                        this.toast.visible = false;
                    }, 3000);
                }
            };
        }
    </script>
</body>
</html>