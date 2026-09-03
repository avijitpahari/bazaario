@extends('layouts.app')

@section('title', 'Explore Marketplace Products — Bazaario')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    filterOpen: true,
    priceRange: 150,
    selectedRating: 0,
    selectedCategory: 'all',
    sortBy: 'featured',
    viewMode: 'grid',
    hasResults: true
}">

    <!-- Page Header & Title -->
    <div class="flex flex-col md:flex-row md:items-end justify-between pb-6 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <span class="text-slate-900 font-semibold">Marketplace Catalog</span>
            </div>
            <h1 class="text-3xl font-bold font-heading text-slate-900">All Marketplace Products</h1>
            <p class="text-sm text-slate-500 mt-1">Showing 24 of 1,480 unique handcrafted and curated items</p>
        </div>

        <!-- Sort and Layout Controls -->
        <div class="flex items-center gap-3">
            <button @click="filterOpen = !filterOpen" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-sm transition">
                <i class="fa-solid fa-sliders text-indigo-600"></i>
                <span x-text="filterOpen ? 'Hide Filters' : 'Show Filters'"></span>
            </button>

            <div class="relative" x-data="{ sortDropdown: false }">
                <button @click="sortDropdown = !sortDropdown" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-sm transition">
                    <i class="fa-solid fa-arrow-down-short-wide text-slate-400"></i>
                    <span>Sort: <span class="text-indigo-600 capitalize" x-text="sortBy"></span></span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                </button>
                <div x-show="sortDropdown" @click.outside="sortDropdown = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-30 text-xs font-medium text-slate-700">
                    <button @click="sortBy = 'featured'; sortDropdown = false" class="w-full text-left px-4 py-2 hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between">Featured First</button>
                    <button @click="sortBy = 'price-low'; sortDropdown = false" class="w-full text-left px-4 py-2 hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between">Price: Low to High</button>
                    <button @click="sortBy = 'price-high'; sortDropdown = false" class="w-full text-left px-4 py-2 hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between">Price: High to Low</button>
                    <button @click="sortBy = 'rating'; sortDropdown = false" class="w-full text-left px-4 py-2 hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between">Highest Rated Stalls</button>
                    <button @click="sortBy = 'newest'; sortDropdown = false" class="w-full text-left px-4 py-2 hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-between">Newest Arrivals</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Collapsible Horizontal Filter Bar -->
    <div x-show="filterOpen" x-transition.duration.300ms class="mt-6 p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
        
        <!-- Category Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 shrink-0 mr-1">Category:</span>
            <button @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap">
                All Categories (1,480)
            </button>
            <button @click="selectedCategory = 'artisan'" :class="selectedCategory === 'artisan' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap">
                Artisan & Crafts (420)
            </button>
            <button @click="selectedCategory = 'electronics'" :class="selectedCategory === 'electronics' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap">
                Electronics & Gadgets (310)
            </button>
            <button @click="selectedCategory = 'apparel'" :class="selectedCategory === 'apparel' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap">
                Fashion & Apparel (390)
            </button>
            <button @click="selectedCategory = 'home'" :class="selectedCategory === 'home' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap">
                Home & Living (260)
            </button>
            <button @click="selectedCategory = 'organic'" :class="selectedCategory === 'organic' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition whitespace-nowrap">
                Organic & Gourmet (100)
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-3 border-t border-slate-100">
            <!-- Price Range Filter -->
            <div>
                <div class="flex justify-between items-center text-xs font-semibold text-slate-700 mb-1.5">
                    <span>Max Price:</span>
                    <span class="text-indigo-600 font-bold text-sm">$<span x-text="priceRange"></span></span>
                </div>
                <input type="range" min="10" max="500" step="5" x-model="priceRange" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                    <span>$10</span>
                    <span>$250</span>
                    <span>$500+</span>
                </div>
            </div>

            <!-- Seller Rating Filter -->
            <div>
                <label class="text-xs font-semibold text-slate-700 block mb-1.5">Seller Trust Score:</label>
                <div class="flex items-center gap-1.5">
                    <button @click="selectedRating = 0" :class="selectedRating === 0 ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600'" class="px-2.5 py-1 text-xs font-semibold rounded-lg border hover:border-slate-300">Any</button>
                    <button @click="selectedRating = 4" :class="selectedRating === 4 ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600'" class="px-2.5 py-1 text-xs font-semibold rounded-lg border hover:border-slate-300 flex items-center gap-1">4.0+ <i class="fa-solid fa-star text-amber-500 text-[10px]"></i></button>
                    <button @click="selectedRating = 45" :class="selectedRating === 45 ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600'" class="px-2.5 py-1 text-xs font-semibold rounded-lg border hover:border-slate-300 flex items-center gap-1">4.5+ <i class="fa-solid fa-star text-amber-500 text-[10px]"></i></button>
                    <button @click="selectedRating = 48" :class="selectedRating === 48 ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600'" class="px-2.5 py-1 text-xs font-semibold rounded-lg border hover:border-slate-300 flex items-center gap-1">Top 99%</button>
                </div>
            </div>

            <!-- Fast shipping toggle & reset -->
            <div class="flex items-center justify-between md:justify-end gap-3 pt-2 md:pt-0">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                    <span>Verified Stalls Only</span>
                </label>
                <button @click="priceRange = 500; selectedRating = 0; selectedCategory = 'all'" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold underline">
                    Reset
                </button>
            </div>
        </div>

    </div>

    <!-- Active Filters Display -->
    <div class="flex items-center gap-2 mt-4 text-xs text-slate-500">
        <span>Active Filters:</span>
        <span class="inline-flex items-center gap-1 bg-white border border-slate-200 px-2.5 py-1 rounded-full text-slate-700 font-medium">
            Category: <strong x-text="selectedCategory"></strong>
        </span>
        <span class="inline-flex items-center gap-1 bg-white border border-slate-200 px-2.5 py-1 rounded-full text-slate-700 font-medium">
            Price &le; $<span x-text="priceRange"></span>
        </span>
    </div>

    <!-- Masonry-Style Product Grid -->
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-start">
        
        <!-- Product Card 1 (Bestseller) -->
        <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
            <div class="relative h-64 overflow-hidden bg-slate-100">
                <a href="{{ route('products.show', 'handcrafted-leather-messenger-bag') }}">
                    <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=600&q=80" alt="Leather Bag" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </a>
                <!-- Badges -->
                <div class="absolute top-3 left-3 flex flex-col gap-1">
                    <span class="bg-amber-500 text-slate-950 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full shadow">Bestseller</span>
                    <span class="bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Only 3 left</span>
                </div>
                <!-- Wishlist button -->
                <button class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur hover:bg-white text-slate-600 hover:text-rose-500 shadow-md flex items-center justify-center transition">
                    <i class="fa-regular fa-heart text-sm"></i>
                </button>
            </div>

            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <!-- Seller Tag -->
                    <div class="flex items-center justify-between text-xs mb-2">
                        <a href="{{ route('stall.show', 'nordic-leather') }}" class="font-bold text-amber-600 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-store text-[10px]"></i> Nordic Leather Atelier
                        </a>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">99% Trust</span>
                    </div>

                    <a href="{{ route('products.show', 'handcrafted-leather-messenger-bag') }}" class="font-heading font-bold text-base text-slate-900 group-hover:text-indigo-600 line-clamp-2 transition">
                        Full-Grain Cognac Leather Messenger Briefcase
                    </a>
                    
                    <div class="flex items-center gap-1 text-xs text-amber-500 mt-1">
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <span class="text-slate-400 text-[11px] font-medium ml-1">(128)</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 line-through">$189.00</span>
                        <div class="text-lg font-black text-slate-900 font-heading">$149.00</div>
                    </div>
                    <a href="{{ route('cart.index') }}" class="p-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white transition shadow-sm" title="Add to Cart">
                        <i class="fa-solid fa-cart-plus text-base"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Card 2 (Taller Card for Masonry Feel) -->
        <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
            <div class="relative h-80 overflow-hidden bg-slate-100">
                <a href="{{ route('products.show', 'acoustic-audiophile-over-ear-headphones') }}">
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=600&q=80" alt="Headphones" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </a>
                <div class="absolute top-3 left-3 flex flex-col gap-1">
                    <span class="bg-indigo-600 text-white text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full shadow">Free Shipping</span>
                </div>
                <button class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur hover:bg-white text-slate-600 hover:text-rose-500 shadow-md flex items-center justify-center transition">
                    <i class="fa-regular fa-heart text-sm"></i>
                </button>
            </div>

            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs mb-2">
                        <a href="{{ route('stall.show', 'audiocraft') }}" class="font-bold text-amber-600 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-store text-[10px]"></i> AudioCraft Studios
                        </a>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">98% Trust</span>
                    </div>

                    <a href="{{ route('products.show', 'acoustic-audiophile-over-ear-headphones') }}" class="font-heading font-bold text-base text-slate-900 group-hover:text-indigo-600 line-clamp-2 transition">
                        Aura Hi-Fi Studio Wireless Noise-Cancelling Headphones
                    </a>
                    
                    <div class="flex items-center gap-1 text-xs text-amber-500 mt-1">
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star-half-stroke text-[11px]"></i>
                        <span class="text-slate-400 text-[11px] font-medium ml-1">(94)</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-lg font-black text-slate-900 font-heading">$219.00</div>
                    </div>
                    <a href="{{ route('cart.index') }}" class="p-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white transition shadow-sm" title="Add to Cart">
                        <i class="fa-solid fa-cart-plus text-base"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Card 3 -->
        <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
            <div class="relative h-60 overflow-hidden bg-slate-100">
                <a href="{{ route('products.show', 'hand-thrown-ceramic-matcha-bowl') }}">
                    <img src="https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=600&q=80" alt="Ceramic Bowl" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </a>
                <div class="absolute top-3 left-3 flex flex-col gap-1">
                    <span class="bg-emerald-600 text-white text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full shadow">Handmade</span>
                </div>
                <button class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur hover:bg-white text-slate-600 hover:text-rose-500 shadow-md flex items-center justify-center transition">
                    <i class="fa-regular fa-heart text-sm"></i>
                </button>
            </div>

            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs mb-2">
                        <a href="{{ route('stall.show', 'kyoto-clay') }}" class="font-bold text-amber-600 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-store text-[10px]"></i> Kyoto Clay Collective
                        </a>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">100% Trust</span>
                    </div>

                    <a href="{{ route('products.show', 'hand-thrown-ceramic-matcha-bowl') }}" class="font-heading font-bold text-base text-slate-900 group-hover:text-indigo-600 line-clamp-2 transition">
                        Wabi-Sabi Stoneware Glazed Matcha & Tea Bowl
                    </a>
                    
                    <div class="flex items-center gap-1 text-xs text-amber-500 mt-1">
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <span class="text-slate-400 text-[11px] font-medium ml-1">(52)</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-lg font-black text-slate-900 font-heading">$38.00</div>
                    </div>
                    <a href="{{ route('cart.index') }}" class="p-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white transition shadow-sm" title="Add to Cart">
                        <i class="fa-solid fa-cart-plus text-base"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Card 4 -->
        <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
            <div class="relative h-72 overflow-hidden bg-slate-100">
                <a href="{{ route('products.show', 'solid-walnut-desk-organizer') }}">
                    <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=600&q=80" alt="Desk Organizer" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </a>
                <div class="absolute top-3 left-3 flex flex-col gap-1">
                    <span class="bg-amber-500 text-slate-950 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full shadow">Trending</span>
                    <span class="bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Only 2 left</span>
                </div>
                <button class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur hover:bg-white text-slate-600 hover:text-rose-500 shadow-md flex items-center justify-center transition">
                    <i class="fa-regular fa-heart text-sm"></i>
                </button>
            </div>

            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs mb-2">
                        <a href="{{ route('stall.show', 'timber-grain') }}" class="font-bold text-amber-600 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-store text-[10px]"></i> Timber & Grain
                        </a>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">99% Trust</span>
                    </div>

                    <a href="{{ route('products.show', 'solid-walnut-desk-organizer') }}" class="font-heading font-bold text-base text-slate-900 group-hover:text-indigo-600 line-clamp-2 transition">
                        Solid American Walnut Minimalist Desk Tray
                    </a>
                    
                    <div class="flex items-center gap-1 text-xs text-amber-500 mt-1">
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <i class="fa-solid fa-star text-[11px]"></i>
                        <span class="text-slate-400 text-[11px] font-medium ml-1">(68)</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 line-through">$85.00</span>
                        <div class="text-lg font-black text-slate-900 font-heading">$64.00</div>
                    </div>
                    <a href="{{ route('cart.index') }}" class="p-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white transition shadow-sm" title="Add to Cart">
                        <i class="fa-solid fa-cart-plus text-base"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Infinite Scroll / Load More Action -->
    <div class="mt-14 text-center">
        <button class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 text-sm font-bold shadow-sm hover:border-slate-400 transition">
            <i class="fa-solid fa-arrows-rotate text-indigo-600"></i> Load More Marketplace Items
        </button>
        <p class="text-xs text-slate-400 mt-2">Showing 4 of 1,480 products from 450+ verified stalls</p>
    </div>

</div>
@endsection
