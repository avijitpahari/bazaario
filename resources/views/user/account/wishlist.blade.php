@extends('layouts.user')

@section('title', 'My Wishlist & Saved Finds — Bazaario')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-8" 
     x-data="wishlistManager()">

    {{-- ── 1. TELEMETRY & HERO HEADER ── --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-amber-950 text-white p-6 sm:p-8 md:p-10 shadow-xl border border-slate-700/60">
        <!-- Ambient Glow -->
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-rose-500/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex flex-col gap-2 max-w-xl">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 text-xs font-mono font-bold tracking-wider uppercase border border-rose-400/30">
                        <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                        <span>Curated Wishlist</span>
                    </span>
                    @if($wishlist->sharing_token)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white/10 text-slate-300 text-[11px] font-mono border border-white/15">
                            <span class="material-symbols-outlined text-[13px] text-emerald-400">public</span>
                            <span>Publicly Shareable</span>
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-4xl font-black font-['Outfit'] tracking-tight text-white mt-1">
                    My Saved Finds
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    Keep track of artisanal goods, APMC farm harvest consignments, and specialty produce. Move to cart anytime before stock runs out.
                </p>
            </div>

            <!-- Telemetry Metrics Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 xl:grid-cols-4 gap-3 bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/10 shrink-0">
                <div class="flex flex-col">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-mono">Total Saved</span>
                    <span class="text-xl sm:text-2xl font-bold font-['Outfit'] text-white mt-0.5" x-text="totalCount">{{ $totalCount }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-mono">Total Value</span>
                    <span class="text-xl sm:text-2xl font-bold font-['Outfit'] text-amber-400 mt-0.5">₹{{ number_format($totalValue, 2) }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-mono">In Stock</span>
                    <span class="text-xl sm:text-2xl font-bold font-['Outfit'] text-emerald-400 mt-0.5">{{ $inStockCount }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-mono">Out of Stock</span>
                    <span class="text-xl sm:text-2xl font-bold font-['Outfit'] text-rose-400 mt-0.5">{{ $outOfStockCount }}</span>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-3 pt-6 mt-6 border-t border-white/10">
            <div class="flex flex-wrap items-center gap-2.5">
                @if($inStockCount > 0)
                    <form action="{{ route('user.wishlist.move-all-to-cart') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm transition-all shadow-md flex items-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                            <span>Move All In-Stock to Cart ({{ $inStockCount }})</span>
                        </button>
                    </form>
                @endif

                <button type="button" 
                        @click="openShareModal()"
                        class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm border border-white/15 transition-all flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">share</span>
                    <span>Share Wishlist</span>
                </button>

                @if($totalCount > 0)
                    <form action="{{ route('user.wishlist.clear') }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to clear your entire wishlist? This cannot be undone.');">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-200 border border-rose-500/30 font-semibold text-xs sm:text-sm transition-all flex items-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">delete_sweep</span>
                            <span>Clear All</span>
                        </button>
                    </form>
                @endif
            </div>

            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs text-amber-300 hover:text-amber-200 font-semibold transition-colors">
                <span>Explore Catalog</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>

    {{-- ── 2. SEARCH, CATEGORY FILTER & SORT TOOLBAR ── --}}
    @if($totalCount > 0 || !empty($search) || request('category_id'))
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form action="{{ route('user.wishlist.index') }}" method="GET" class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Search Input -->
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="q" value="{{ $search }}" placeholder="Search saved items or stores..."
                       class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
                @if(!empty($search))
                    <a href="{{ route('user.wishlist.index', request()->except('q')) }}" 
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 material-symbols-outlined text-sm">close</a>
                @endif
            </div>

            <!-- Category Filter Dropdown -->
            @if($savedCategories->isNotEmpty())
            <div class="shrink-0">
                <select name="category_id" onchange="this.form.submit()"
                        class="w-full sm:w-auto px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all cursor-pointer">
                    <option value="">All Categories ({{ $totalCount }})</option>
                    @foreach($savedCategories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Sort Dropdown -->
            <div class="shrink-0">
                <select name="sort" onchange="this.form.submit()"
                        class="w-full sm:w-auto px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all cursor-pointer">
                    <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Recently Saved</option>
                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="stock" {{ $sort === 'stock' ? 'selected' : '' }}>In Stock First</option>
                    <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name: A to Z</option>
                </select>
            </div>
        </form>

        <!-- Showing Result Counter -->
        <div class="text-xs text-slate-500 font-mono shrink-0 self-end md:self-center">
            Showing <strong class="text-slate-900">{{ $items->count() }}</strong> of {{ $totalCount }} item(s)
        </div>
    </div>
    @endif

    {{-- ── 3. WISHLIST ITEMS GRID ── --}}
    @if($items->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($items as $item)
            @php
                $product = $item->product;
                $img = $product?->primaryImage?->url ?? $product?->images->first()?->url;
                $inStock = ($product?->stock ?? 0) > 0;
                $isLowStock = $inStock && ($product?->stock ?? 0) <= 10;
            @endphp
            <div class="group relative flex flex-col rounded-3xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-xl hover:border-amber-400/80 transition-all duration-300 overflow-hidden"
                 id="wishlist-card-{{ $item->id }}"
                 x-data="{ noteOpen: false, noteText: @json($item->note ?? ''), isSavingNote: false }">

                <!-- Image Container with Overlays -->
                <div class="relative w-full aspect-square overflow-hidden bg-slate-100">
                    <a href="{{ route('products.show', $product?->slug ?? $product?->id) }}" class="block w-full h-full">
                        @if($img)
                            <img alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" 
                                 src="{{ $img }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                                <span class="material-symbols-outlined text-[48px]">image</span>
                            </div>
                        @endif
                    </a>

                    <!-- Stock Status Badge Overlay (Top Left) -->
                    <div class="absolute top-3 left-3 flex flex-col gap-1 pointer-events-none">
                        @if(!$inStock)
                            <span class="px-2.5 py-1 rounded-full bg-rose-600/95 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm backdrop-blur-xs flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                <span>Out of Stock</span>
                            </span>
                        @elseif($isLowStock)
                            <span class="px-2.5 py-1 rounded-full bg-amber-500/95 text-slate-950 text-[10px] font-bold uppercase tracking-wider shadow-sm backdrop-blur-xs flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-950 animate-ping"></span>
                                <span>Only {{ $product->stock }} Left</span>
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-emerald-600/90 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm backdrop-blur-xs flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-200"></span>
                                <span>In Stock</span>
                            </span>
                        @endif
                    </div>

                    <!-- Quick Remove Heart Button (Top Right) -->
                    <button type="button" 
                            @click="removeItem({{ $item->id }}, '{{ addslashes($product->name) }}')"
                            title="Remove from Wishlist" 
                            class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-rose-500 hover:text-rose-600 backdrop-blur-md flex items-center justify-center transition-all shadow-md active:scale-90 cursor-pointer">
                        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    </button>

                    <!-- Category Pill (Bottom Left) -->
                    @if($product->category)
                        <div class="absolute bottom-3 left-3 pointer-events-none">
                            <span class="px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-mono font-medium">
                                {{ $product->category->name }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Card Content -->
                <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between gap-3 bg-white">
                    <div class="flex flex-col gap-1">
                        <!-- Merchant / Artisan Store Name -->
                        @if($product->seller)
                            <span class="text-[11px] text-slate-500 font-mono flex items-center gap-1 truncate">
                                <span class="material-symbols-outlined text-[13px] text-amber-600">storefront</span>
                                <span class="truncate">{{ $product->seller->sellerProfile->shop_name ?? $product->seller->name }}</span>
                            </span>
                        @endif

                        <!-- Product Title -->
                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-amber-700 transition-colors line-clamp-2 leading-snug">
                            <a href="{{ route('products.show', $product?->slug ?? $product?->id) }}">
                                {{ $product->name }}
                            </a>
                        </h3>

                        <!-- Optional Personal Note Pill -->
                        <div class="mt-1">
                            <template x-if="noteText">
                                <div class="p-2 rounded-xl bg-amber-50/70 border border-amber-200 text-amber-900 text-xs flex items-start justify-between gap-2">
                                    <div class="flex items-start gap-1 min-w-0">
                                        <span class="material-symbols-outlined text-amber-600 text-[14px] mt-0.5 shrink-0">edit_note</span>
                                        <span class="text-[11px] font-medium leading-tight truncate" x-text="noteText"></span>
                                    </div>
                                    <button type="button" @click="noteOpen = !noteOpen" class="text-amber-700 hover:text-amber-900 text-[10px] font-bold uppercase underline shrink-0 cursor-pointer">Edit</button>
                                </div>
                            </template>
                            <template x-if="!noteText">
                                <button type="button" @click="noteOpen = !noteOpen" class="text-[11px] text-slate-400 hover:text-amber-600 flex items-center gap-1 transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[14px]">add</span>
                                    <span>Add personal note</span>
                                </button>
                            </template>

                            <!-- Inline Note Editor -->
                            <div x-show="noteOpen" x-cloak class="mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-2">
                                <input type="text" x-model="noteText" placeholder="e.g. For Diwali gift / Check with Rahul..." 
                                       maxlength="255"
                                       class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-amber-500">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="noteOpen = false" class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-600 hover:bg-slate-200">Cancel</button>
                                    <button type="button" 
                                            @click="saveNote({{ $item->id }}, noteText); noteOpen = false;"
                                            class="px-3 py-1 rounded-md text-[11px] font-bold bg-slate-900 text-amber-400 hover:bg-slate-800">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price & Move to Cart Button Bar -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-mono text-slate-400">Price</span>
                            <span class="text-base sm:text-lg font-black font-['Outfit'] text-slate-900">
                                ₹{{ number_format($product->price ?? 0, 2) }}
                            </span>
                        </div>

                        <div>
                            @if($inStock)
                                <button type="button" 
                                        @click="moveToCart({{ $item->id }}, '{{ addslashes($product->name) }}')"
                                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-all shadow-xs flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px] text-amber-400">add_shopping_cart</span>
                                    <span>Move to Cart</span>
                                </button>
                            @else
                                <button type="button" disabled
                                        class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-400 font-semibold text-xs cursor-not-allowed">
                                    Out of Stock
                                </button>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        @endforeach
    </div>
    @else
    {{-- ── EMPTY STATE ── --}}
    <div class="p-8 sm:p-14 rounded-3xl bg-white border border-slate-200/90 shadow-xs flex flex-col items-center justify-center text-center gap-4">
        <div class="w-20 h-20 rounded-full bg-rose-50 text-rose-500 border border-rose-200/60 flex items-center justify-center">
            <span class="material-symbols-outlined text-4xl">favorite_border</span>
        </div>
        <div class="max-w-md">
            <h2 class="text-xl sm:text-2xl font-black font-['Outfit'] text-slate-900">
                @if(!empty($search))
                    No saved items match "{{ $search }}"
                @else
                    Your wishlist is waiting for treasures!
                @endif
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                @if(!empty($search))
                    Try searching for another keyword or clear the search filter to view all your saved items.
                @else
                    Discover verified master artisans, direct APMC farm harvests, and organic goods. Click the heart icon anywhere to save items here.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-3 mt-2">
            @if(!empty($search) || request('category_id'))
                <a href="{{ route('user.wishlist.index') }}" 
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs sm:text-sm transition-colors">
                    Clear Filters
                </a>
            @endif
            <a href="{{ route('products.index') }}" 
               class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm transition-all shadow-md flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">explore</span>
                <span>Discover Verified Products</span>
            </a>
        </div>
    </div>
    @endif

    {{-- ── 4. RECOMMENDED DISCOVERY SECTION (Warm Modernist Showcase) ── --}}
    @if($recommendations->isNotEmpty())
    <div class="mt-4 flex flex-col gap-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <div>
                <span class="text-[11px] font-mono uppercase font-bold text-amber-700 tracking-wider">Curated Discovery</span>
                <h2 class="text-lg sm:text-xl font-bold font-['Outfit'] text-slate-900">Popular Farm &amp; Artisan Specials</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-600 hover:text-amber-700 flex items-center gap-1">
                <span>View All</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($recommendations as $rec)
                @php
                    $recImg = $rec->primaryImage?->url ?? $rec->images->first()?->url;
                @endphp
                <div class="group relative flex flex-col rounded-2xl bg-white border border-slate-200 shadow-2xs hover:shadow-md transition-all p-3">
                    <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-slate-100 mb-3">
                        <a href="{{ route('products.show', $rec->slug ?? $rec->id) }}">
                            @if($recImg)
                                <img src="{{ $recImg }}" alt="{{ $rec->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-3xl">image</span>
                                </div>
                            @endif
                        </a>
                        <!-- Add to Wishlist Heart Button -->
                        <button type="button" 
                                onclick="window.bazaarioToggleWishlist({{ $rec->id }}, this)"
                                data-wishlist-product="{{ $rec->id }}"
                                title="Save to Wishlist"
                                class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-700 hover:text-rose-500 backdrop-blur-md flex items-center justify-center shadow-xs transition-all active:scale-90 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">favorite_border</span>
                        </button>
                    </div>
                    <div class="flex flex-col flex-1 justify-between gap-2">
                        <div>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $rec->seller?->sellerProfile?->shop_name ?? 'Bazaario Verified' }}</span>
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-amber-700 line-clamp-1">
                                <a href="{{ route('products.show', $rec->slug ?? $rec->id) }}">{{ $rec->name }}</a>
                            </h4>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-sm font-bold font-['Outfit'] text-slate-900">₹{{ number_format($rec->price, 2) }}</span>
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $rec->id }}">
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-[11px] transition-colors flex items-center gap-1 shadow-2xs">
                                    <span class="material-symbols-outlined text-[14px]">add_shopping_cart</span>
                                    <span>Add</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── 5. SHARE WISHLIST MODAL ── --}}
    <div x-cloak x-show="shareModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200"
             @click.outside="shareModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5 text-slate-900">
                    <span class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center material-symbols-outlined text-2xl">share</span>
                    <div>
                        <h3 class="text-lg font-bold font-['Outfit'] text-slate-900">Share Your Wishlist</h3>
                        <p class="text-xs text-slate-500">Anyone with this link can view your curated collection</p>
                    </div>
                </div>
                <button type="button" @click="shareModalOpen = false" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="py-5 flex flex-col gap-4">
                <!-- Share Link Box -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5 font-mono">
                        Public Shareable Link
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly :value="shareUrl" 
                               class="flex-1 px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-800 select-all focus:outline-none">
                        <button type="button" 
                                @click="copyShareLink()"
                                class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-bold text-xs transition-colors flex items-center gap-1.5 shrink-0 shadow-xs cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]" x-text="copied ? 'check' : 'content_copy'"></span>
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                </div>

                <!-- Instant Share Buttons -->
                <div>
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2 font-mono">Quick Share</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- WhatsApp -->
                        <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent('Check out my saved finds on Bazaario: ' + shareUrl)" 
                           target="_blank" rel="noopener noreferrer"
                           class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-xs flex items-center gap-1.5 transition-colors">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">chat</span>
                            <span>WhatsApp</span>
                        </a>
                        <!-- Twitter / X -->
                        <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent('Curated treasures on @Bazaario: ') + '&url=' + encodeURIComponent(shareUrl)" 
                           target="_blank" rel="noopener noreferrer"
                           class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-300 font-bold text-xs flex items-center gap-1.5 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">send</span>
                            <span>Twitter / X</span>
                        </a>
                        <!-- Email -->
                        <a :href="'mailto:?subject=' + encodeURIComponent('My Bazaario Wishlist') + '&body=' + encodeURIComponent('Take a look at items I saved on Bazaario: ' + shareUrl)"
                           class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 font-bold text-xs flex items-center gap-1.5 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">mail</span>
                            <span>Email</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                <button type="button" @click="shareModalOpen = false" 
                        class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs">
                    Done
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function wishlistManager() {
    return {
        totalCount: {{ $totalCount }},
        shareModalOpen: false,
        shareUrl: @json($wishlist->sharing_token ? route('wishlist.public', $wishlist->sharing_token) : ''),
        copied: false,

        async openShareModal() {
            if (!this.shareUrl) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch("{{ route('user.wishlist.share') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token || ''
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.shareUrl = data.share_url;
                    }
                } catch(e) {
                    console.error(e);
                }
            }
            this.shareModalOpen = true;
        },

        copyShareLink() {
            if (!this.shareUrl) return;
            navigator.clipboard.writeText(this.shareUrl).then(() => {
                this.copied = true;
                if (typeof showToast === 'function') {
                    showToast('Wishlist link copied to clipboard!', 'success');
                }
                setTimeout(() => { this.copied = false; }, 2500);
            });
        },

        async removeItem(itemId, productName) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/user/wishlist/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const card = document.getElementById(`wishlist-card-${itemId}`);
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    }
                    this.totalCount = data.count;
                    window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.count } }));
                    if (typeof showToast === 'function') {
                        showToast(`Removed "${productName}" from wishlist.`, 'info');
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        async moveToCart(itemId, productName) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/user/wishlist/${itemId}/move-to-cart`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const card = document.getElementById(`wishlist-card-${itemId}`);
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    }
                    this.totalCount = data.wishlist_count;
                    window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlist_count } }));
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                    if (typeof showToast === 'function') {
                        showToast(`Moved "${productName}" to your cart!`, 'success');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Could not move item to cart.', 'error');
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        async saveNote(itemId, note) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/user/wishlist/${itemId}/note`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify({ note: note })
                });
                const data = await res.json();
                if (data.success && typeof showToast === 'function') {
                    showToast('Personal note saved.', 'success');
                }
            } catch (e) {
                console.error(e);
            }
        }
    };
}

// Global Bazaario Wishlist Toggle Function for all product cards & detail pages
window.bazaarioToggleWishlist = async function(productId, btnElement = null) {
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (btnElement) {
            btnElement.classList.add('scale-125');
            setTimeout(() => btnElement.classList.remove('scale-125'), 200);
        }

        const res = await fetch("{{ route('wishlist.toggle') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token || ''
            },
            body: JSON.stringify({ product_id: productId })
        });

        if (!res.ok) {
            if (res.status === 401) {
                window.location.href = "{{ route('login') }}";
                return;
            }
            throw new Error('Failed to toggle wishlist');
        }

        const data = await res.json();
        if (data.success) {
            window.dispatchEvent(new CustomEvent('wishlist-updated', { 
                detail: { count: data.count, in_wishlist: data.in_wishlist, product_id: productId } 
            }));

            // Sync all matching heart buttons on page
            document.querySelectorAll(`[data-wishlist-product="${productId}"]`).forEach(btn => {
                const icon = btn.querySelector('.material-symbols-outlined') || btn;
                if (data.in_wishlist) {
                    btn.classList.add('text-rose-500');
                    if (icon) {
                        icon.textContent = 'favorite';
                        icon.style.fontVariationSettings = "'FILL' 1";
                    }
                } else {
                    btn.classList.remove('text-rose-500');
                    if (icon) {
                        icon.textContent = 'favorite_border';
                        icon.style.fontVariationSettings = "'FILL' 0";
                    }
                }
            });

            if (typeof showToast === 'function') {
                showToast(data.message, data.in_wishlist ? 'success' : 'info');
            }
        }
    } catch (e) {
        console.error(e);
        if (typeof showToast === 'function') {
            showToast('Unable to update wishlist. Please try again.', 'error');
        }
    }
};
</script>
@endsection
