<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $category ? $category->name . ' — Bazaario Catalog' : 'All Products — Bazaario Catalog' }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <!-- Compiled Tailwind CSS & App JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { display: none; }
        body {
            background-color: #FFFDF8;
            background-image: 
                radial-gradient(circle at 12% 10%, rgba(245, 166, 35, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 88% 18%, rgba(15, 23, 42, 0.03) 0%, transparent 40%);
            background-attachment: fixed;
        }
    </style>
</head>

<body class="bg-[#FFFDF8] text-slate-800 font-sans antialiased min-h-screen relative selection:bg-amber-500 selection:text-slate-950">
    <!-- Ambient 3D Volumetric Mesh & Glow Blobs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 left-1/4 w-[520px] h-[520px] rounded-full bg-gradient-to-br from-amber-400/20 via-orange-300/15 to-transparent blur-3xl opacity-70 transform -rotate-12"></div>
        <div class="absolute top-1/3 -right-28 w-[480px] h-[480px] rounded-full bg-gradient-to-bl from-purple-400/15 via-indigo-300/10 to-transparent blur-3xl opacity-60"></div>
        <div class="absolute bottom-1/4 -left-20 w-[420px] h-[420px] rounded-full bg-gradient-to-tr from-amber-300/15 via-emerald-200/10 to-transparent blur-3xl opacity-50"></div>
    </div>

    <!-- CUSTOMER NAVBAR -->
    @include('components.nav')

    <main class="w-full pt-20 relative z-10 pb-16">
        <div class="w-full max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-col gap-6">
            
            <!-- Top Breadcrumb & Micro-Trust Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                <nav class="flex items-center gap-2 bg-white/70 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-slate-200/80 shadow-xs">
                    <a class="hover:text-amber-600 transition-colors flex items-center gap-1" href="{{ route('home') }}">
                        <span class="material-symbols-outlined text-sm">home</span>
                        <span>Home</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <a class="hover:text-amber-600 transition-colors" href="{{ route('products.index') }}">Catalog</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-bold">{{ $category ? $category->name : 'All Products' }}</span>
                </nav>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 backdrop-blur-md text-emerald-800 border border-emerald-500/25 shadow-xs font-mono text-[11px] font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="uppercase tracking-wider">BAZAARIO ESCROW SECURED</span>
                </div>
            </div>

            <!-- Category Hero Banner with 3D Dimensional Glass Atmosphere -->
            <section class="relative w-full rounded-3xl bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 text-white overflow-hidden p-6 md:p-10 border border-white/10 shadow-2xl backdrop-blur-2xl">
                <!-- Glowing Ambient Iridescent Accents -->
                <div class="absolute -right-16 -top-16 w-[420px] h-[420px] rounded-full bg-gradient-to-bl from-amber-500/25 to-purple-500/15 blur-3xl pointer-events-none"></div>
                <div class="absolute left-1/3 -bottom-24 w-80 h-80 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>

                <div class="relative z-10 grid md:grid-cols-12 gap-8 items-center">
                    <div class="md:col-span-8 flex flex-col gap-3">
                        <div class="inline-flex items-center gap-2 self-start bg-white/10 backdrop-blur-xl px-3.5 py-1.5 rounded-full border border-white/20 text-amber-400 font-mono text-[11px] uppercase tracking-wider font-bold shadow-inner">
                            <span class="material-symbols-outlined text-sm">verified</span>
                            <span>Verified Marketplace Taxonomy</span>
                        </div>
                        <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-white tracking-tight uppercase">
                            {{ $category ? $category->name : 'ALL CATEGORIES' }}
                        </h1>
                        <p class="text-sm sm:text-base text-slate-300 max-w-2xl leading-relaxed">
                            {{ $category && $category->description ? $category->description : 'Explore authentic, handcrafted products from verified local makers and specialty merchants across India.' }}
                        </p>
                        <div class="flex flex-wrap items-center gap-3 pt-2 font-mono text-xs">
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 backdrop-blur-md">
                                <span class="material-symbols-outlined text-amber-400 text-base">inventory_2</span>
                                <span class="text-white font-semibold">{{ $products->total() }} Active Listings</span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 backdrop-blur-md">
                                <span class="material-symbols-outlined text-emerald-400 text-base">shield</span>
                                <span class="text-white font-semibold">100% Escrow Protected</span>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-4 flex items-center justify-center md:justify-end">
                        <div class="p-6 rounded-3xl bg-white/10 backdrop-blur-2xl border border-white/20 shadow-xl text-center w-full max-w-xs">
                            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center mx-auto mb-3">
                                <span class="material-symbols-outlined text-3xl">category</span>
                            </div>
                            <div class="font-display font-bold text-lg text-white mb-1">{{ $category ? $category->name : 'Catalog' }}</div>
                            <p class="text-xs text-slate-400">Directly from independent regional workshops.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Subcategory Quick Filter Pill Bar -->
            <div class="w-full bg-white/70 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-2 shadow-2xs">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth">
                    <a href="{{ route('category.show', ['slug' => 'all']) }}" 
                       class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl font-semibold text-xs transition-all {{ ($slug ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80' }}">
                        <span class="material-symbols-outlined text-sm">grid_view</span>
                        <span>All</span>
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('category.show', ['slug' => $cat->slug]) }}" 
                           class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl font-semibold text-xs transition-all {{ ($slug ?? '') === $cat->slug ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/80' }}">
                            <span>{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Catalog Filter & Sorting Bar -->
            <form action="{{ route('category.show', ['slug' => $slug]) }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2 border-b border-slate-200">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <h2 class="font-display font-bold text-xl text-slate-900 tracking-tight">
                        {{ $category ? $category->name : 'All Catalog Items' }}
                    </h2>
                    <span class="font-mono text-xs font-bold bg-slate-900 text-white px-2.5 py-0.5 rounded-full">
                        {{ $products->total() }} Products
                    </span>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Compact Search -->
                    <div class="relative flex-1 sm:w-64">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                        <input name="search" 
                               value="{{ request('search') }}"
                               class="w-full bg-white border border-slate-200/90 rounded-xl pl-9 pr-3 py-2 text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-amber-500 shadow-2xs"
                               placeholder="Search in this category..." 
                               type="text" />
                    </div>

                    <!-- Sort Select -->
                    <div class="relative">
                        <select name="sort" 
                                onchange="this.form.submit()"
                                class="appearance-none bg-white border border-slate-200/90 rounded-xl pl-3.5 pr-8 py-2 text-xs font-semibold text-slate-800 hover:border-amber-500 cursor-pointer focus:outline-none focus:border-amber-500 shadow-2xs">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Highest Rating</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-sm">expand_more</span>
                    </div>
                </div>
            </form>

            <!-- Dynamic Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($products as $product)
                    @php
                        $primaryImg = $product->primaryImage ? $product->primaryImage->image_path : ($product->images->first() ? $product->images->first()->image_path : null);
                        $imgUrl = $primaryImg ? (Illuminate\Support\Str::startsWith($primaryImg, ['http://', 'https://']) ? $primaryImg : asset($primaryImg)) : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';
                        $sellerName = $product->seller && $product->seller->sellerProfile ? $product->seller->sellerProfile->shop_name : ($product->seller ? $product->seller->name : 'Bazaario Artisan');
                        $ratingDisplay = $product->average_rating ? number_format($product->average_rating, 1) : 'New';
                        $reviewsDisplay = ($product->total_reviews ?? 0) . ' reviews';
                        $originalPrice = round($product->price * 1.25);
                    @endphp
                    <article class="group bg-white rounded-3xl border border-slate-200/90 p-4 flex flex-col justify-between hover:border-amber-500/60 hover:-translate-y-1.5 shadow-xs hover:shadow-xl transition-all duration-300 relative overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-500 to-amber-300 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div>
                            <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-slate-100 mb-3.5 flex items-center justify-center shadow-inner">
                                <a href="{{ route('products.show', ['slug' => $product->slug]) }}" class="w-full h-full block">
                                    <img alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500" 
                                         src="{{ $imgUrl }}" 
                                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';">
                                </a>
                                <div class="absolute top-2.5 left-2.5">
                                    <span class="font-mono text-[9px] uppercase font-bold bg-slate-900/90 backdrop-blur-md text-amber-400 px-2 py-0.5 rounded-md tracking-wider shadow-sm">
                                        {{ $product->price > 10000 ? 'Premium' : (($product->average_rating && $product->average_rating >= 4.8) ? 'Top Pick' : 'Verified') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="font-mono text-[10px] uppercase text-amber-600 font-bold tracking-wider truncate">
                                    {{ $product->category ? $product->category->name : 'General' }}
                                </span>
                                <div class="flex items-center gap-1 text-[11px] font-mono text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full shrink-0">
                                    <span class="text-amber-500 font-bold">★</span>
                                    <span class="text-slate-900 font-bold">{{ $product->average_rating ? number_format($product->average_rating, 1) : 'New' }}</span>
                                    <span class="text-slate-400 text-[10px]">({{ ($product->total_reviews ?? 0) . ' reviews' }})</span>
                                </div>
                            </div>

                            <h3 class="font-display font-bold text-slate-900 text-sm line-clamp-1 group-hover:text-amber-600 transition-colors">
                                <a href="{{ route('products.show', ['slug' => $product->slug]) }}">{{ $product->name }}</a>
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-1 mt-0.5 font-sans">
                                {{ $product->short_description ?: $product->description }}
                            </p>
                            <p class="text-[11px] font-mono text-slate-400 mt-1 truncate">
                                By <span class="text-slate-700 font-medium">{{ $sellerName }}</span>
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span class="font-mono font-bold text-base text-slate-900">₹{{ number_format($product->price, 2) }}</span>
                                @if($originalPrice > $product->price)
                                    <span class="font-mono text-xs line-through text-slate-400">₹{{ number_format($originalPrice, 2) }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button class="w-full bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-semibold text-xs py-2 px-3 rounded-xl flex items-center justify-center gap-1.5 transition-all shadow-sm active:scale-95" type="submit">
                                        <span class="material-symbols-outlined text-sm">shopping_cart</span>
                                        <span>Add to Cart</span>
                                    </button>
                                </form>
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="buy_now" value="1">
                                    <button class="p-2 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl transition-all shadow-sm active:scale-95" type="submit" title="Buy Now">
                                        <span class="material-symbols-outlined text-sm">bolt</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-1 sm:col-span-2 lg:col-span-4 bg-white rounded-3xl p-12 text-center border border-slate-200/90 shadow-xs">
                        <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">inventory_2</span>
                        <h3 class="font-display font-bold text-slate-900 text-lg mb-1">No products found in this category</h3>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">Explore our other curated categories or check back soon as regional artisans upload new collections.</p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-amber-500 hover:text-slate-950 transition-colors">
                            <span>Browse All Products</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Server-Side Pagination Links -->
            <div class="mt-8 flex justify-center">
                {{ $products->links() }}
            </div>

            <!-- Category Escrow Guarantee Banner -->
            <section class="w-full bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 relative overflow-hidden border border-white/10 shadow-xl backdrop-blur-2xl mt-6">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 text-amber-400 font-mono text-[11px] uppercase font-bold tracking-wider mb-2 bg-white/10 px-3 py-1 rounded-full border border-white/15">
                            <span class="material-symbols-outlined text-sm">verified_user</span>
                            Authenticity &amp; Escrow Guarantee
                        </div>
                        <h3 class="font-display font-bold text-xl sm:text-2xl text-white tracking-tight">
                            Bazaario Decentralized Escrow Assurance
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed font-sans">
                            Every purchase is held safely in smart escrow. Merchant payouts are released on a T+2 schedule only after courier delivery confirmation and your 7-day inspection window.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('pages.how-it-works') }}" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm px-6 py-3 rounded-2xl transition-all shadow-md inline-flex items-center gap-2">
                            <span>Learn How Escrow Works</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <!-- Global Footer -->
    <x-footer />
</body>
</html>