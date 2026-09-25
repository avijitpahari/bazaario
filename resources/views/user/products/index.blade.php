@php
    use Illuminate\Support\Str;

    $fallbackProducts = [
        [
            'id' => 1,
            'name' => 'Handcrafted Vintage Leather Messenger Bag',
            'slug' => 'handcrafted-leather-messenger-bag',
            'category' => 'Artisan & Handmade Crafts',
            'seller' => 'Heritage Leather Crafters',
            'sellerVerified' => true,
            'price' => 4299,
            'originalPrice' => 5499,
            'discountPercent' => 22,
            'rating' => 4.9,
            'reviewCount' => 142,
            'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&auto=format&fit=crop&q=80',
            'badge' => 'Bestseller',
            'badgeClass' => 'bg-amber-500 text-slate-950 font-bold',
            'inStock' => true,
            'description' => 'Full-grain vegetable-tanned leather handcrafted by master artisans in Rajasthan.'
        ],
        [
            'id' => 2,
            'name' => 'Lumik V87 Pro Wireless Mechanical Keyboard',
            'slug' => 'lumik-v87-pro-wireless-keyboard',
            'category' => 'Electronics & Gadgets',
            'seller' => 'TechForge Systems',
            'sellerVerified' => true,
            'price' => 6499,
            'originalPrice' => 7999,
            'discountPercent' => 18,
            'rating' => 4.8,
            'reviewCount' => 215,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600&auto=format&fit=crop&q=80',
            'badge' => 'Verified',
            'badgeClass' => 'bg-emerald-600 text-white',
            'inStock' => true,
            'description' => 'Gasket-mounted hot-swappable switches with tri-mode Bluetooth 5.2 and 4000mAh battery.'
        ],
        [
            'id' => 3,
            'name' => 'Hand-thrown Ceramic Coffee Dripper & Mug Set',
            'slug' => 'hand-thrown-ceramic-coffee-set',
            'category' => 'Home & Living',
            'seller' => 'Clay & Kiln Studio',
            'sellerVerified' => true,
            'price' => 1850,
            'originalPrice' => 2200,
            'discountPercent' => 15,
            'rating' => 4.9,
            'reviewCount' => 88,
            'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&auto=format&fit=crop&q=80',
            'badge' => 'Handmade',
            'badgeClass' => 'bg-slate-900 text-amber-400',
            'inStock' => true,
            'description' => 'Artisan stoneware glazed with natural minerals. Oven and dishwasher safe.'
        ],
        [
            'id' => 4,
            'name' => 'Acoustic Pro Active Noise-Cancelling Headphones',
            'slug' => 'acoustic-pro-anc-headphones',
            'category' => 'Electronics & Gadgets',
            'seller' => 'SoundSphere India',
            'sellerVerified' => true,
            'price' => 12499,
            'originalPrice' => 15999,
            'discountPercent' => 21,
            'rating' => 4.7,
            'reviewCount' => 512,
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80',
            'badge' => 'Trending',
            'badgeClass' => 'bg-rose-600 text-white',
            'inStock' => true,
            'description' => 'Hybrid active noise cancellation with 45-hour playback and high-resolution spatial audio.'
        ],
        [
            'id' => 5,
            'name' => 'Pure Organic Mulberry Silk Scarf (Indigo Dye)',
            'slug' => 'organic-mulberry-silk-scarf',
            'category' => 'Fashion & Apparel',
            'seller' => 'Varanasi Weavers Guild',
            'sellerVerified' => true,
            'price' => 2499,
            'originalPrice' => 3200,
            'discountPercent' => 22,
            'rating' => 4.9,
            'reviewCount' => 120,
            'image' => 'https://images.unsplash.com/photo-1601924994987-69e26d50dc26?w=600&auto=format&fit=crop&q=80',
            'badge' => 'Artisan',
            'badgeClass' => 'bg-indigo-600 text-white',
            'inStock' => true,
            'description' => '100% natural mulberry silk hand-dyed with organic indigo vats in Varanasi.'
        ],
        [
            'id' => 6,
            'name' => 'Vintage Brass Desk Compass & Sundial (1940s Replica)',
            'slug' => 'vintage-brass-desk-compass',
            'category' => 'Rare Collectibles & Antiques',
            'seller' => 'Nautical Antiquities',
            'sellerVerified' => false,
            'price' => 3199,
            'originalPrice' => 3999,
            'discountPercent' => 20,
            'rating' => 4.6,
            'reviewCount' => 64,
            'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80',
            'badge' => 'Rare Find',
            'badgeClass' => 'bg-amber-700 text-white',
            'inStock' => true,
            'description' => 'Solid brass casting with antique patina finish and hand-stitched leather storage case.'
        ],
        [
            'id' => 7,
            'name' => 'Handbound Leather Journal with Deckle Edge Cotton Paper',
            'slug' => 'handbound-leather-journal-deckle-paper',
            'category' => 'Books & Fine Stationery',
            'seller' => 'Scriptorium Arts',
            'sellerVerified' => true,
            'price' => 1299,
            'originalPrice' => 1699,
            'discountPercent' => 23,
            'rating' => 4.8,
            'reviewCount' => 194,
            'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80',
            'badge' => 'Eco Friendly',
            'badgeClass' => 'bg-emerald-700 text-white',
            'inStock' => true,
            'description' => '240 pages of tree-free handmade recycled cotton paper suitable for fountain pens.'
        ],
        [
            'id' => 8,
            'name' => 'Nordic Minimalist Oak Wood Table Lamp',
            'slug' => 'nordic-minimalist-oak-table-lamp',
            'category' => 'Home & Living',
            'seller' => 'Lumina Studio',
            'sellerVerified' => true,
            'price' => 2899,
            'originalPrice' => 3499,
            'discountPercent' => 17,
            'rating' => 4.7,
            'reviewCount' => 76,
            'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=600&auto=format&fit=crop&q=80',
            'badge' => 'Sustainable',
            'badgeClass' => 'bg-teal-700 text-white',
            'inStock' => true,
            'description' => 'FSC-certified solid white oak with warm dimmable LED and linen drum shade.'
        ],
        [
            'id' => 9,
            'name' => 'Minimalist Titanium Automatic Chronograph Watch',
            'slug' => 'minimalist-titanium-chronograph-watch',
            'category' => 'Fashion & Apparel',
            'seller' => 'Chronos Horology',
            'sellerVerified' => true,
            'price' => 18999,
            'originalPrice' => 24000,
            'discountPercent' => 20,
            'rating' => 4.9,
            'reviewCount' => 287,
            'image' => 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?w=600&auto=format&fit=crop&q=80',
            'badge' => 'Premium',
            'badgeClass' => 'bg-slate-950 text-amber-400 font-bold',
            'inStock' => true,
            'description' => 'Grade 5 titanium case, sapphire crystal glass with anti-reflective coating and NH35 movement.'
        ]
    ];

    $dbProds = $dbProducts ?? collect();
    $formattedProducts = $dbProds->map(function($p) {
        $primaryImg = $p->primaryImage ? $p->primaryImage->image_path : ($p->images->first() ? $p->images->first()->image_path : 'images/products/leather_bag_1.jpg');
        $imgUrl = Str::startsWith($primaryImg, ['http://', 'https://']) ? $primaryImg : asset($primaryImg);
        
        return [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug,
            'category' => $p->category ? $p->category->name : 'General',
            'seller' => $p->seller && $p->seller->sellerProfile ? $p->seller->sellerProfile->shop_name : ($p->seller ? $p->seller->name : 'Bazaario Verified'),
            'sellerVerified' => true,
            'price' => (float)$p->price,
            'originalPrice' => round((float)$p->price * 1.25),
            'discountPercent' => 20,
            'rating' => (float)($p->average_rating ?: 4.8),
            'reviewCount' => (int)($p->total_reviews ?: 120),
            'image' => $imgUrl,
            'badge' => $p->price > 10000 ? 'Premium' : ($p->average_rating >= 4.9 ? 'Bestseller' : 'Verified'),
            'badgeClass' => $p->price > 10000 ? 'bg-slate-950 text-amber-400 font-bold' : ($p->average_rating >= 4.9 ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-emerald-600 text-white'),
            'inStock' => $p->stock > 0,
            'description' => $p->short_description ?: $p->description,
        ];
    })->values();

    $finalProducts = $formattedProducts->isNotEmpty() ? $formattedProducts : $fallbackProducts;
    $categoryList = collect(['All Categories'])->merge(($dbCategories ?? collect())->pluck('name'))->values();
    if ($categoryList->count() <= 1) {
        $categoryList = collect(['All Categories', 'Artisan & Handmade Crafts', 'Electronics & Gadgets', 'Fashion & Apparel', 'Home & Living', 'Rare Collectibles & Antiques', 'Books & Fine Stationery']);
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <title>Shop Marketplace — Bazaario</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols Outlined -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Compiled Tailwind CSS & App JS via Vite + Production Fallback -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        * { -webkit-tap-highlight-color: transparent; }
    </style>
</head>

<body class="bg-[#FFFDF8] font-sans text-slate-800 antialiased min-h-screen flex flex-col selection:bg-amber-500 selection:text-slate-950"
      x-data="productCatalog()"
      x-init="initCatalog()">

    <!-- Global Page Initial Preloader -->
    <div x-show="pageLoading" 
         x-transition:leave="transition ease-out duration-500 opacity-0 scale-95"
         class="fixed inset-0 z-[110] bg-[#FFFDF8]/95 backdrop-blur-xl flex flex-col items-center justify-center p-4">
        <div class="relative flex flex-col items-center">
            <!-- Animated Spinner Rings -->
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 rounded-full border-4 border-amber-500/20"></div>
                <div class="absolute inset-0 rounded-full border-4 border-amber-500 border-t-transparent animate-spin"></div>
                <div class="absolute inset-2 rounded-full border-4 border-slate-900 border-b-transparent animate-spin" style="animation-duration: 1.5s; animation-direction: reverse;"></div>
            </div>
            <!-- Bazaario Brand Text -->
            <div class="mt-4 flex items-center gap-2 font-display text-lg font-bold text-slate-900 tracking-tight">
                <span>Bazaario</span>
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            </div>
            <p class="text-xs font-mono text-slate-500 mt-1">Loading marketplace items...</p>
        </div>
    </div>

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
    <main class="flex-1 w-full pt-3 sm:pt-5 pb-24 md:pb-16">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb & Top Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 sm:pb-6 border-b border-slate-200/80">
                <div>
                    <nav class="flex items-center gap-1.5 text-[11px] sm:text-xs font-mono text-slate-500 mb-1">
                        <a href="{{ url('/') }}" class="hover:text-amber-600 transition-colors">Home</a>
                        <span>/</span>
                    </nav>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <h1 class="text-xl sm:text-3xl font-display font-bold text-slate-900 tracking-tight">Marketplace Catalog</h1>
                        <span class="text-[10px] sm:text-xs font-mono font-medium px-2 py-0.5 sm:py-1 bg-slate-200/70 text-slate-700 rounded-full"
                              x-text="filteredProducts.length + ' items'"></span>
                    </div>
                </div>

                <!-- Right Trust Strip & AI Query Assistant Bar -->
                <div class="flex items-center gap-2 shrink-0">
                    <div class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-mono font-semibold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Escrow Secured
                    </div>
                    
                    <button @click="showAiModal = true" 
                            class="inline-flex items-center gap-1.5 bg-slate-900 text-amber-400 hover:text-amber-300 hover:bg-slate-800 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-mono font-medium transition-all shadow-sm active:scale-95">
                        <span class="font-bold">✦</span>
                        <span>AI Finder</span>
                    </button>
                </div>
            </div>

            <!-- Level 0: Interactive Search & Controls Bar -->
            <div class="mt-3 sm:mt-6 bg-white rounded-2xl p-2.5 sm:p-4 shadow-sm border border-slate-200/80 flex flex-col gap-2.5">
                <!-- Search Box -->
                <div class="relative w-full">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg sm:text-xl pointer-events-none">search</span>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Search products, brands, or verified sellers..."
                           class="w-full pl-9 sm:pl-10 pr-9 sm:pr-10 py-2 sm:py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition-all">
                    <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1">
                        <span class="material-symbols-outlined text-base sm:text-lg">close</span>
                    </button>
                </div>

                <!-- Sort & Filter Strip -->
                <div class="flex items-center justify-between gap-2 pt-1 border-t sm:border-t-0 border-slate-100">
                    <!-- Filter Trigger Button (Visible on Desktop & Mobile) -->
                    <button @click="mobileFiltersOpen = !mobileFiltersOpen" 
                            class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-slate-900 text-white hover:bg-slate-800 rounded-xl text-xs font-semibold active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-base">tune</span>
                        <span>Filter Panel</span>
                        <span x-show="activeFilterCount > 0" 
                              class="w-4 h-4 rounded-full bg-amber-500 text-slate-950 font-mono text-[10px] flex items-center justify-center font-bold" 
                              x-text="activeFilterCount"></span>
                    </button>

                    <!-- Sort By Selector -->
                    <div class="relative flex-1 sm:flex-initial" x-data="{ sortOpen: false }">
                        <button @click="sortOpen = !sortOpen" 
                                class="w-full sm:w-auto inline-flex items-center justify-between sm:justify-start gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 border border-slate-200 text-xs font-medium text-slate-800 transition-colors">
                            <span class="material-symbols-outlined text-base">sort</span>
                            <span class="truncate max-w-[120px] sm:max-w-none" x-text="sortLabels[sortBy]"></span>
                            <span class="material-symbols-outlined text-sm">expand_more</span>
                        </button>
                        <div x-cloak x-show="sortOpen" @click.away="sortOpen = false" 
                             class="absolute right-0 sm:right-0 mt-1.5 w-48 sm:w-52 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-30">
                            <button @click="sortBy = 'featured'; sortOpen = false" class="w-full text-left px-3 sm:px-4 py-2 text-xs hover:bg-slate-50 flex items-center justify-between" :class="sortBy === 'featured' ? 'font-bold text-slate-900 bg-slate-100/70' : 'text-slate-600'">
                                <span>Featured</span>
                                <span x-show="sortBy === 'featured'" class="material-symbols-outlined text-sm text-amber-500">check</span>
                            </button>
                            <button @click="sortBy = 'price_low'; sortOpen = false" class="w-full text-left px-3 sm:px-4 py-2 text-xs hover:bg-slate-50 flex items-center justify-between" :class="sortBy === 'price_low' ? 'font-bold text-slate-900 bg-slate-100/70' : 'text-slate-600'">
                                <span>Price: Low to High</span>
                                <span x-show="sortBy === 'price_low'" class="material-symbols-outlined text-sm text-amber-500">check</span>
                            </button>
                            <button @click="sortBy = 'price_high'; sortOpen = false" class="w-full text-left px-3 sm:px-4 py-2 text-xs hover:bg-slate-50 flex items-center justify-between" :class="sortBy === 'price_high' ? 'font-bold text-slate-900 bg-slate-100/70' : 'text-slate-600'">
                                <span>Price: High to Low</span>
                                <span x-show="sortBy === 'price_high'" class="material-symbols-outlined text-sm text-amber-500">check</span>
                            </button>
                            <button @click="sortBy = 'rating'; sortOpen = false" class="w-full text-left px-3 sm:px-4 py-2 text-xs hover:bg-slate-50 flex items-center justify-between" :class="sortBy === 'rating' ? 'font-bold text-slate-900 bg-slate-100/70' : 'text-slate-600'">
                                <span>Top Customer Rating</span>
                                <span x-show="sortBy === 'rating'" class="material-symbols-outlined text-sm text-amber-500">check</span>
                            </button>
                        </div>
                    </div>

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

            <!-- Level 1: Category Pill Tags Filter Bar (Scrollable on Mobile) -->
            <div class="mt-3 sm:mt-4 flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar pb-1 -mx-3 px-3 sm:mx-0 sm:px-0">
                <template x-for="cat in categories" :key="cat">
                    <button @click="selectedCategory = cat" 
                            :class="selectedCategory === cat ? 'bg-slate-900 text-white shadow-xs font-semibold' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/90'"
                            class="px-3 sm:px-4 py-1.5 rounded-full text-xs whitespace-nowrap transition-all flex items-center gap-1 shrink-0 active:scale-95">
                        <span x-text="cat"></span>
                        <span class="text-[10px] opacity-70 font-mono" x-text="getCategoryCount(cat)"></span>
                    </button>
                </template>
            </div>

            <!-- Level 2: Main Grid Full Width with 6 Columns -->
            <div class="mt-3 sm:mt-6 w-full">
                
                <!-- Zero Results State -->
                <div x-show="filteredProducts.length === 0" 
                     class="bg-white rounded-2xl p-8 sm:p-12 text-center border border-slate-200/80 shadow-xs">
                    <span class="material-symbols-outlined text-4xl sm:text-5xl text-slate-300 mb-3">sentiment_dissatisfied</span>
                    <h3 class="text-base sm:text-lg font-display font-bold text-slate-900 mb-1">No products match your criteria</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mb-5 max-w-md mx-auto">Try widening your price range, searching different keywords, or resetting your filter criteria.</p>
                    <button @click="resetFilters()" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition-colors">
                        Clear All Filters
                    </button>
                </div>

                    <!-- Product Grid (2 cols mobile, 3 tablet, 4 desktop md, 6 desktop xl) -->
                    <div x-show="filteredProducts.length > 0" 
                         :class="viewMode === 'grid' ? 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-2.5 sm:gap-3' : 'space-y-3 sm:space-y-4'">
                        
                        <template x-for="product in filteredProducts.slice(0, displayLimit)" :key="product.id">
                            <div>
                                <!-- ── GRID CARD FORMAT ── -->
                                <div x-show="viewMode === 'grid'" 
                                     class="group bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-xl hover:border-slate-300 transition-all duration-300 flex flex-col relative h-full">
                                    
                                    <!-- Product Image & Badges (Compact 4:3 Aspect Ratio) -->
                                    <div class="relative aspect-[4/3] w-full bg-slate-100 overflow-hidden">
                                        <a :href="'/product/' + product.slug" class="block w-full h-full">
                                            <img :src="product.image" 
                                                 :alt="product.name" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                 loading="lazy"
                                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';">
                                        </a>

                                        <!-- Top Left Single Badge -->
                                        <div class="absolute top-2 left-2 z-10">
                                            <template x-if="product.badge">
                                                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-mono font-bold uppercase tracking-wider shadow-xs backdrop-blur-md"
                                                      :class="product.badgeClass || 'bg-slate-900 text-white'"
                                                      x-text="product.badge"></span>
                                            </template>
                                        </div>

                                        <!-- Top Right Wishlist Button -->
                                        <button @click="toggleWishlist(product)" 
                                                class="absolute top-2 right-2 p-1.5 rounded-full bg-white/90 backdrop-blur-md text-slate-700 hover:text-red-500 hover:bg-white transition-all duration-200 shadow-xs z-10"
                                                :class="{'text-red-500': isInWishlist(product.id)}"
                                                aria-label="Wishlist">
                                            <span class="material-symbols-outlined text-sm sm:text-base leading-none block fill-current">
                                                <span x-text="isInWishlist(product.id) ? 'favorite' : 'favorite'"></span>
                                            </span>
                                        </button>
                                    </div>

                                    <!-- Content Container (Padding & Layout) -->
                                    <div class="p-2.5 sm:p-3 flex-1 flex flex-col justify-between">
                                        <div>
                                            <!-- Sub-header Row: Category & Stock Pill -->
                                            <div class="flex items-center justify-between gap-1 mb-1">
                                                <span class="text-[9px] sm:text-[10px] font-mono font-medium text-slate-400 uppercase tracking-wider truncate" x-text="product.category"></span>
                                                
                                                <!-- In Stock Indicator -->
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 shrink-0">
                                                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    <span>In Stock</span>
                                                </span>
                                            </div>

                                            <!-- Product Title (Clean 2-line clamp) -->
                                            <h3 class="font-display font-bold text-slate-900 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-amber-600 transition-colors">
                                                <a :href="'/product/' + product.slug" x-text="product.name"></a>
                                            </h3>

                                            <!-- Seller & Rating Row (Clean wrap) -->
                                            <div class="flex items-center justify-between gap-1 mt-1 text-[10px] sm:text-xs">
                                                <div class="flex items-center gap-0.5 text-slate-600 truncate">
                                                    <span class="truncate max-w-[80px] sm:max-w-none" x-text="product.seller"></span>
                                                    <template x-if="product.sellerVerified">
                                                        <span class="material-symbols-outlined text-blue-600 text-[12px] sm:text-xs shrink-0" title="Verified Seller">verified</span>
                                                    </template>
                                                </div>
                                                <div class="flex items-center text-amber-500 font-bold shrink-0">
                                                    <span>★</span>
                                                    <span class="text-slate-800 ml-0.5" x-text="product.rating.toFixed(1)"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Price & Cart CTA -->
                                        <div class="mt-1 pt-1.5 border-t border-slate-100 flex items-center justify-between gap-1">
                                            <div>
                                                <div class="flex items-baseline gap-1">
                                                    <span class="text-xs sm:text-sm font-mono font-bold text-slate-900" x-text="'₹' + Number(product.price).toLocaleString('en-IN')"></span>
                                                    <template x-if="product.discountPercent > 0">
                                                        <span class="text-[10px] font-mono text-slate-400 line-through" x-text="'₹' + Number(product.originalPrice).toLocaleString('en-IN')"></span>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Cart Button -->
                                            <button @click="addToCart(product)" 
                                                    :disabled="addingToCartId === product.id"
                                                    class="p-1 sm:px-2.5 sm:py-1.5 bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white rounded-xl text-[11px] font-semibold transition-all duration-200 flex items-center justify-center gap-1 shadow-xs active:scale-90 shrink-0 disabled:opacity-75"
                                                    aria-label="Add to cart">
                                                <template x-if="addingToCartId === product.id">
                                                    <svg class="animate-spin h-3.5 w-3.5 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </template>
                                                <template x-if="addingToCartId !== product.id">
                                                    <span class="material-symbols-outlined text-sm">shopping_cart</span>
                                                </template>
                                                <span class="hidden sm:inline text-xs" x-text="addingToCartId === product.id ? 'Adding...' : 'Add'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- ── LIST ROW FORMAT ── -->
                                <div x-show="viewMode === 'list'" 
                                     class="bg-white rounded-2xl border border-slate-200/90 p-3 sm:p-4 shadow-xs hover:shadow-md transition-all flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 justify-between">
                                    <div class="flex items-center gap-3 w-full sm:w-auto">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-slate-100 shrink-0 overflow-hidden relative">
                                            <img :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] font-mono text-slate-500 mb-0.5">
                                                <span class="uppercase text-slate-400 truncate" x-text="product.category"></span>
                                                <span>•</span>
                                                <span class="text-slate-700 font-sans truncate" x-text="product.seller"></span>
                                            </div>
                                            <h3 class="font-display font-bold text-slate-900 text-xs sm:text-sm line-clamp-1 hover:text-amber-600 transition-colors">
                                                <a :href="'/product/' + product.slug" x-text="product.name"></a>
                                            </h3>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-amber-500 text-xs font-bold">★ <span class="text-slate-700" x-text="product.rating.toFixed(1)"></span></span>
                                                <span class="text-[10px] font-mono text-slate-400" x-text="'(' + product.reviewsCount + ' reviews)'"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto border-t sm:border-t-0 border-slate-100 pt-2 sm:pt-0">
                                        <div>
                                            <span class="text-sm sm:text-base font-mono font-bold text-slate-900" x-text="'₹' + Number(product.price).toLocaleString('en-IN')"></span>
                                            <template x-if="product.discountPercent > 0">
                                                <span class="text-xs font-mono text-slate-400 line-through block" x-text="'₹' + Number(product.originalPrice).toLocaleString('en-IN')"></span>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button @click="openQuickView(product)" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors" title="Quick View">
                                                <span class="material-symbols-outlined text-base">visibility</span>
                                            </button>
                                            <button @click="addToCart(product)" 
                                                    :disabled="addingToCartId === product.id"
                                                    class="px-4 py-2 bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white rounded-xl text-xs font-semibold transition-all flex items-center gap-1 disabled:opacity-75">
                                                <template x-if="addingToCartId === product.id">
                                                    <svg class="animate-spin h-3.5 w-3.5 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </template>
                                                <template x-if="addingToCartId !== product.id">
                                                    <span class="material-symbols-outlined text-base">shopping_cart</span>
                                                </template>
                                                <span x-text="addingToCartId === product.id ? 'Adding...' : 'Add'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Load More Products Button -->
                    <div class="mt-10 flex flex-col items-center justify-center gap-3 pt-6 border-t border-slate-200/80">
                        <button x-show="displayLimit < filteredProducts.length"
                                @click="displayLimit += 12"
                                class="px-8 py-3.5 rounded-full bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-bold text-xs sm:text-sm shadow-md hover:shadow-xl transition-all duration-200 flex items-center gap-2 active:scale-95 group">
                            <span>Load More Products</span>
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-y-0.5 transition-transform">expand_more</span>
                        </button>
                        <span class="text-[11px] font-mono text-slate-500">
                            Showing <span class="font-bold text-slate-900" x-text="Math.min(displayLimit, filteredProducts.length)"></span> of <span class="font-bold text-slate-900" x-text="filteredProducts.length"></span> curated items
                        </span>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <!-- ── SLIDE-OVER FILTERS DRAWER (LEFT SIDE) ── -->
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
                    <h3 class="font-display font-bold text-slate-900 text-base">Filters & Refinement</h3>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="resetFilters()" class="text-xs font-mono text-amber-600 font-semibold underline">
                        Reset All
                    </button>
                    <button @click="mobileFiltersOpen = false" class="p-1 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            </div>

            <!-- Scrollable Filter Options -->
            <div class="p-5 overflow-y-auto space-y-6 flex-1 text-xs">
                
                <!-- Category Selector -->
                <div>
                    <label class="font-mono font-bold text-slate-800 uppercase tracking-wider block mb-2">Category</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <template x-for="cat in categories" :key="cat">
                            <button @click="selectedCategory = cat" 
                                    class="p-2 rounded-xl text-left font-medium transition-all flex items-center justify-between border"
                                    :class="selectedCategory === cat ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200/80'">
                                <span class="truncate" x-text="cat"></span>
                                <span x-show="selectedCategory === cat" class="material-symbols-outlined text-xs text-amber-400">check</span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <label class="font-mono font-bold text-slate-800 uppercase tracking-wider">Max Budget</label>
                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg text-xs" x-text="'₹' + Number(maxPriceFilter).toLocaleString('en-IN')"></span>
                    </div>
                    <input type="range" 
                           min="500" 
                           max="1000000" 
                           step="2500" 
                           x-model="maxPriceFilter" 
                           class="w-full accent-slate-900 h-2 bg-slate-200 rounded-lg cursor-pointer">
                    <div class="flex justify-between font-mono text-[10px] text-slate-400 mt-1">
                        <span>₹500</span>
                        <span>₹5,00,000</span>
                        <span>₹10,00,000+</span>
                    </div>
                </div>

                <!-- Minimum Rating -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="font-mono font-bold text-slate-800 uppercase tracking-wider block mb-2">Customer Rating</label>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="r in [4, 3, 2]" :key="r">
                            <button @click="minRating = r" 
                                    class="p-2.5 rounded-xl border flex items-center gap-1.5 transition-all"
                                    :class="minRating === r ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200'">
                                <span class="text-amber-400">★</span>
                                <span class="font-bold" x-text="r + '.0+'"></span>
                            </button>
                        </template>
                        <button @click="minRating = 0" 
                                class="p-2.5 rounded-xl border flex items-center justify-center transition-all"
                                :class="minRating === 0 ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200'">
                            <span>All Ratings</span>
                        </button>
                    </div>
                </div>

                <!-- Toggles -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80 cursor-pointer">
                        <span class="font-medium text-slate-800 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-blue-600 text-base">verified</span>
                            Verified Sellers Only
                        </span>
                        <input type="checkbox" x-model="verifiedOnly" class="w-4 h-4 rounded text-slate-900 accent-slate-900 focus:ring-0">
                    </label>
                    <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80 cursor-pointer">
                        <span class="font-medium text-slate-800 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-emerald-600 text-base">inventory_2</span>
                            In Stock Ready to Ship
                        </span>
                        <input type="checkbox" x-model="inStockOnly" class="w-4 h-4 rounded text-slate-900 accent-slate-900 focus:ring-0">
                    </label>
                </div>

            </div>

            <!-- Drawer Bottom Apply Button -->
            <div class="p-4 border-t border-slate-100 bg-white shrink-0">
                <button @click="mobileFiltersOpen = false" 
                        class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg">
                    <span>Show Results</span>
                    <span class="font-mono bg-amber-500 text-slate-950 px-2 py-0.5 rounded-full text-[10px]" x-text="'(' + filteredProducts.length + ' found)'"></span>
                </button>
            </div>

        </div>
    </div>

    <!-- ── QUICK VIEW MODAL (Mobile Optimized) ── -->
    <div x-cloak x-show="quickViewModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="quickViewModal = false" 
             class="bg-white rounded-t-3xl sm:rounded-3xl max-w-2xl w-full p-5 sm:p-8 shadow-2xl relative border border-slate-200 max-h-[85vh] overflow-y-auto"
             x-show="quickViewModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-full sm:scale-95 sm:translate-y-0 opacity-0"
             x-transition:enter-end="translate-y-0 sm:scale-100 sm:translate-y-0 opacity-100">
            
            <button @click="quickViewModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800 p-1.5 rounded-full bg-slate-100 hover:bg-slate-200 z-10">
                <span class="material-symbols-outlined text-lg sm:text-xl">close</span>
            </button>

            <template x-if="selectedProduct">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-8 items-center">
                    <!-- Image -->
                    <div class="aspect-square bg-slate-100 rounded-2xl overflow-hidden relative">
                        <img :src="selectedProduct.image" :alt="selectedProduct.name" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 px-2.5 py-0.5 bg-slate-900/90 text-white rounded-full text-[10px] font-mono font-bold uppercase" x-text="selectedProduct.category"></span>
                    </div>

                    <!-- Details -->
                    <div class="flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1.5 text-xs font-mono text-slate-500 mb-1">
                                <span x-text="selectedProduct.seller"></span>
                                <template x-if="selectedProduct.sellerVerified">
                                    <span class="material-symbols-outlined text-blue-600 text-sm" title="Verified Seller">verified</span>
                                </template>
                            </div>

                            <h2 class="text-base sm:text-xl font-display font-bold text-slate-900 leading-tight" x-text="selectedProduct.name"></h2>

                            <!-- Stars -->
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="flex items-center text-amber-400 text-xs">
                                    <span class="material-symbols-outlined text-sm">star</span>
                                    <span class="text-xs font-bold text-slate-900 ml-1" x-text="selectedProduct.rating"></span>
                                </div>
                                <span class="text-xs font-mono text-slate-400" x-text="'(' + selectedProduct.reviewCount + ' reviews)'"></span>
                            </div>

                            <!-- Price -->
                            <div class="mt-3 flex items-baseline gap-2">
                                <span class="text-xl sm:text-2xl font-mono font-bold text-slate-900" x-text="'₹' + Number(selectedProduct.price).toLocaleString('en-IN')"></span>
                                <template x-if="selectedProduct.originalPrice">
                                    <span class="text-xs font-mono text-slate-400 line-through" x-text="'₹' + Number(selectedProduct.originalPrice).toLocaleString('en-IN')"></span>
                                </template>
                            </div>

                            <p class="text-xs text-slate-600 mt-2 leading-relaxed line-clamp-3" x-text="selectedProduct.description || 'Authentic verified item backed by Bazaario Buyer Protection. Instant dispatch within 24 hours.'"></p>

                            <!-- Quantity Selector -->
                            <div class="mt-4 flex items-center gap-3">
                                <span class="text-xs font-mono font-semibold text-slate-700">Qty:</span>
                                <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden">
                                    <button @click="if(quickViewQty > 1) quickViewQty--" class="px-3 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold">-</button>
                                    <span class="px-3 py-1 font-mono text-xs font-bold" x-text="quickViewQty"></span>
                                    <button @click="quickViewQty++" class="px-3 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal CTA Buttons -->
                        <div class="mt-6 flex flex-row gap-2">
                            <button @click="addToCart(selectedProduct, quickViewQty); quickViewModal = false;" 
                                    class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-base">shopping_cart</span>
                                <span>Add to Cart</span>
                            </button>
                            <a :href="'/product/' + selectedProduct.slug" 
                               class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold text-center transition-colors">
                                Details
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ── AI SEARCH HELPER MODAL (Mobile Optimized) ── -->
    <div x-cloak x-show="showAiModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.away="showAiModal = false" class="bg-white rounded-t-3xl sm:rounded-3xl max-w-lg w-full p-5 sm:p-8 shadow-2xl relative border border-slate-200">
            <button @click="showAiModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800 p-1">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
            <div class="flex items-center gap-2 mb-1.5 text-amber-500 font-bold">
                <span class="text-xl">✦</span>
                <span class="font-display font-bold text-slate-900 text-base">Bazaario AI Smart Assistant</span>
            </div>
            <p class="text-xs text-slate-500 mb-4">Tap any shopping prompt below to instantly discover verified products:</p>
            
            <div class="space-y-2 mb-3">
                <button @click="searchQuery = 'Leather'; showAiModal = false" class="w-full text-left p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs text-slate-700 flex items-center justify-between transition-colors">
                    <span>"Handcrafted genuine leather bags under ₹5,000"</span>
                    <span class="material-symbols-outlined text-sm text-slate-400">arrow_forward</span>
                </button>
                <button @click="searchQuery = 'Keyboard'; showAiModal = false" class="w-full text-left p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs text-slate-700 flex items-center justify-between transition-colors">
                    <span>"Mechanical keyboards with wireless Bluetooth"</span>
                    <span class="material-symbols-outlined text-sm text-slate-400">arrow_forward</span>
                </button>
                <button @click="searchQuery = 'Artisan'; showAiModal = false" class="w-full text-left p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs text-slate-700 flex items-center justify-between transition-colors">
                    <span>"Top-rated handmade artisan ceramics & decor"</span>
                    <span class="material-symbols-outlined text-sm text-slate-400">arrow_forward</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ── NATIVE MOBILE BOTTOM NAVIGATION DOCK (App-like Experience) ── -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-slate-200/90 px-3 py-2.5 shadow-[0_-8px_30px_rgba(0,0,0,0.08)] flex items-center justify-around text-[10px] font-sans font-medium text-slate-600">
        <a href="{{ url('/') }}" class="flex flex-col items-center gap-0.5 py-0.5 px-2 rounded-xl transition-colors hover:text-slate-900">
            <span class="material-symbols-outlined text-[22px]">home</span>
            <span>Home</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 py-0.5 px-2 rounded-xl text-slate-900 font-bold">
            <span class="material-symbols-outlined text-[22px] text-amber-500 fill-current">storefront</span>
            <span>Shop</span>
        </a>
        <a href="{{ url('/user/account/wishlist') }}" class="flex flex-col items-center gap-0.5 py-0.5 px-2 rounded-xl transition-colors hover:text-slate-900 relative">
            <span class="material-symbols-outlined text-[22px]">favorite</span>
            <span>Wishlist</span>
            <span x-show="wishlist.length > 0" class="absolute top-0 right-2 w-3.5 h-3.5 bg-rose-500 text-white rounded-full font-mono text-[9px] flex items-center justify-center font-bold" x-text="wishlist.length"></span>
        </a>
        <a href="{{ url('/cart') }}" class="flex flex-col items-center gap-0.5 py-0.5 px-2 rounded-xl transition-colors hover:text-slate-900 relative">
            <span class="material-symbols-outlined text-[22px]">shopping_cart</span>
            <span>Cart</span>
        </a>
        <a href="{{ url('/account') }}" class="flex flex-col items-center gap-0.5 py-0.5 px-2 rounded-xl transition-colors hover:text-slate-900">
            <span class="material-symbols-outlined text-[22px]">account_circle</span>
            <span>Account</span>
        </a>
    </nav>

    <!-- Global Footer -->
    <x-footer />

    <!-- Alpine.js Catalog Logic -->
    <script>
        function productCatalog() {
            return {
                pageLoading: true,
                searchQuery: '',
                selectedCategory: 'All Categories',
                sortBy: 'featured',
                viewMode: 'grid',
                maxPriceFilter: 1000000,
                minRating: 0,
                verifiedOnly: false,
                inStockOnly: false,
                mobileFiltersOpen: false,
                quickViewModal: false,
                showAiModal: false,
                selectedProduct: null,
                quickViewQty: 1,
                wishlist: [],
                
                toast: {
                    visible: false,
                    message: '',
                    type: 'cart',
                    icon: 'shopping_cart'
                },

                sortLabels: {
                    featured: 'Featured',
                    price_low: 'Price: Low-High',
                    price_high: 'Price: High-Low',
                    rating: 'Top Rated'
                },

                categories: {!! json_encode($categoryList) !!},

                products: {!! json_encode($finalProducts) !!},
                displayLimit: 12,

                initCatalog() {
                    try {
                        const urlParams = new URLSearchParams(window.location.search);
                        const searchParam = urlParams.get('search');
                        if (searchParam) {
                            this.searchQuery = searchParam;
                        }
                        const saved = localStorage.getItem('bazaario_wishlist');
                        if (saved) this.wishlist = JSON.parse(saved);
                    } catch(e) {}
                    setTimeout(() => { this.pageLoading = false; }, 350);
                },


                get activeFilterCount() {
                    let count = 0;
                    if (this.selectedCategory !== 'All Categories') count++;
                    if (this.maxPriceFilter < 1000000) count++;
                    if (this.minRating > 0) count++;
                    if (this.verifiedOnly) count++;
                    if (this.inStockOnly) count++;
                    return count;
                },

                get filteredProducts() {
                    return this.products.filter(p => {
                        if (this.selectedCategory !== 'All Categories' && p.category !== this.selectedCategory) return false;
                        if (this.searchQuery.trim() !== '') {
                            const q = this.searchQuery.toLowerCase();
                            const matchTitle = p.name.toLowerCase().includes(q);
                            const matchSeller = p.seller.toLowerCase().includes(q);
                            const matchCat = p.category.toLowerCase().includes(q);
                            if (!matchTitle && !matchSeller && !matchCat) return false;
                        }
                        if (p.price > Number(this.maxPriceFilter)) return false;
                        if (this.minRating > 0 && p.rating < this.minRating) return false;
                        if (this.verifiedOnly && !p.sellerVerified) return false;
                        if (this.inStockOnly && !p.inStock) return false;
                        return true;
                    }).sort((a, b) => {
                        if (this.sortBy === 'price_low') return a.price - b.price;
                        if (this.sortBy === 'price_high') return b.price - a.price;
                        if (this.sortBy === 'rating') return b.rating - a.rating;
                        return 0;
                    });
                },

                getCategoryCount(cat) {
                    if (cat === 'All Categories') return '(' + this.products.length + ')';
                    const count = this.products.filter(p => p.category === cat).length;
                    return '(' + count + ')';
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.selectedCategory = 'All Categories';
                    this.sortBy = 'featured';
                    this.maxPriceFilter = 1000000;
                    this.minRating = 0;
                    this.verifiedOnly = false;
                    this.inStockOnly = false;
                },

                isWishlisted(id) {
                    return this.wishlist.includes(id);
                },

                isInWishlist(id) {
                    return this.isWishlisted(id);
                },

                toggleWishlist(product) {
                    if (this.isWishlisted(product.id)) {
                        this.wishlist = this.wishlist.filter(item => item !== product.id);
                        this.showToast('Removed from Wishlist', 'wishlist', 'favorite_border');
                    } else {
                        this.wishlist.push(product.id);
                        this.showToast('Saved to Wishlist!', 'wishlist', 'favorite');
                    }
                    try {
                        localStorage.setItem('bazaario_wishlist', JSON.stringify(this.wishlist));
                    } catch(e) {}
                },

                addingToCartId: null,

                addToCart(product, qty = 1) {
                    this.addingToCartId = product.id;
                    setTimeout(() => {
                        this.showToast(`Added to Cart: ${product.name.substring(0, 20)}...`, 'cart', 'shopping_cart');
                        this.addingToCartId = null;
                    }, 400);
                },


                openQuickView(product) {
                    this.selectedProduct = product;
                    this.quickViewQty = 1;
                    this.quickViewModal = true;
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