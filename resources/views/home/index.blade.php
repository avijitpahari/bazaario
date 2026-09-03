@extends('layouts.app')

@section('title', 'Bazaario — General Multi-Vendor Marketplace | Discover Independent Stalls')

@section('content')
<div class="space-y-16 lg:space-y-24 pb-12">

    <!-- 1. Hero Section -->
    <section class="relative overflow-hidden pt-8 pb-16 lg:pt-16 lg:pb-24 bg-gradient-to-b from-indigo-50/70 via-slate-50 to-slate-50">
        <!-- Abstract Glow Backgrounds -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-gradient-to-tr from-indigo-300/30 via-amber-200/20 to-purple-300/20 blur-3xl pointer-events-none rounded-full -z-10"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-indigo-100 shadow-sm text-xs font-bold text-indigo-700">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Unified Multi-Vendor Cart • Direct Dispatch
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight font-heading leading-[1.1]">
                        The open market for <br class="hidden sm:inline">
                        <span class="bg-gradient-to-r from-indigo-600 via-indigo-800 to-amber-600 bg-clip-text text-transparent">authentic goods</span> & independent stalls.
                    </h1>
                    
                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed">
                        Shop directly from hundreds of vetted craftsmen, independent boutique owners, and curated specialty brands in one single seamless checkout.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('products.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-base shadow-xl shadow-indigo-600/25 hover:shadow-indigo-600/40 hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fa-solid fa-compass"></i> Browse the Market
                        </a>
                        <a href="{{ route('seller.register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-4 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-bold text-base border border-slate-200 shadow-sm hover:border-slate-300 transition-all duration-200">
                            <i class="fa-solid fa-store text-amber-500"></i> Open Your Own Stall
                        </a>
                    </div>

                    <!-- Trust Stats Bar -->
                    <div class="pt-6 grid grid-cols-3 gap-6 border-t border-slate-200/80 max-w-lg mx-auto lg:mx-0">
                        <div>
                            <div class="text-2xl font-black font-heading text-slate-900">450+</div>
                            <div class="text-xs text-slate-500 font-medium">Verified Stalls</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black font-heading text-slate-900">28k+</div>
                            <div class="text-xs text-slate-500 font-medium">Unique Items</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black font-heading text-amber-600 flex items-center gap-1 justify-center lg:justify-start">
                                4.9 <i class="fa-solid fa-star text-xs"></i>
                            </div>
                            <div class="text-xs text-slate-500 font-medium">Trust Score Avg</div>
                        </div>
                    </div>

                </div>

                <!-- Hero Visual / Live Spotlight Card -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Main Card -->
                        <div class="relative bg-white rounded-3xl p-5 shadow-2xl border border-slate-100 ring-1 ring-slate-900/5">
                            <div class="relative h-64 rounded-2xl overflow-hidden bg-slate-100 mb-4 group">
                                <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80" alt="Spotlight Product" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur-md text-white text-[11px] font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                                    <i class="fa-solid fa-fire text-amber-400"></i> Stall of the Week
                                </div>
                                <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur text-slate-900 font-extrabold text-sm px-3 py-1 rounded-xl shadow">
                                    $149.00
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" class="w-10 h-10 rounded-xl object-cover ring-2 ring-indigo-500/20" alt="Stall owner">
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <h4 class="font-bold text-sm text-slate-900">Nordic Leather Atelier</h4>
                                            <i class="fa-solid fa-circle-check text-indigo-600 text-xs"></i>
                                        </div>
                                        <p class="text-xs text-slate-500">Handmade Goods • 99.2% Trust</p>
                                    </div>
                                </div>
                                <a href="{{ route('stall.show', 'nordic-leather') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-xs font-bold text-slate-700 transition">
                                    Visit Stall
                                </a>
                            </div>
                        </div>

                        <!-- Floating Live Order Card -->
                        <div class="absolute -bottom-6 -left-6 bg-white/95 backdrop-blur-md rounded-2xl p-3.5 shadow-xl border border-slate-100 flex items-center gap-3 hidden sm:flex animate-bounce duration-1000">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <div class="text-xs">
                                <p class="font-bold text-slate-900">Order Dispatched</p>
                                <p class="text-slate-500 text-[11px]">Just shipped from <strong class="text-slate-700">Kyoto Ceramics</strong></p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. Live Activity Strip -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-4 shadow-xl border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Live Activity</span>
                <span class="text-slate-500">|</span>
                <div class="text-xs text-slate-300 flex items-center gap-2 overflow-hidden whitespace-nowrap">
                    <span class="font-medium"><i class="fa-solid fa-store text-amber-400"></i> New Stall: <strong>Aura Fragrances</strong> just joined</span>
                    <span class="text-slate-600">•</span>
                    <span class="font-medium"><i class="fa-solid fa-bag-shopping text-indigo-400"></i> Order placed for <strong>Mechanical Keycaps</strong> (San Francisco)</span>
                </div>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-bold text-amber-400 hover:text-amber-300 flex items-center gap-1 shrink-0">
                View All Live Marketplace Items <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </section>

    <!-- 3. Stall Mosaic (4 Featured Seller Cards with Signature Layout) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
            <div>
                <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-600 mb-1">
                    <i class="fa-solid fa-store"></i> Curated Stalls
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900">Featured Stall Mosaic</h2>
                <p class="text-sm text-slate-500 mt-1">Discover the passionate creators behind our top-rated independent marketplace storefronts.</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 hover:text-indigo-700">
                Explore All Stalls <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <!-- Large Spotlight Card 1 (Span 7) -->
            <div class="md:col-span-7 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative h-64 rounded-2xl overflow-hidden mb-5">
                        <img src="https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=1000&q=80" alt="Artisan Timber" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between text-white">
                            <div>
                                <span class="bg-amber-500 text-slate-950 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded">Featured Master Stall</span>
                                <h3 class="text-xl font-bold font-heading mt-1">Timber & Grain Workshop</h3>
                                <p class="text-xs text-slate-300">Custom Walnut furniture, organizers & home decor</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-xl bg-white/20 backdrop-blur text-xs font-bold">124 Products</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-4 text-xs text-slate-600">
                            <span class="flex items-center gap-1 font-bold text-emerald-600"><i class="fa-solid fa-shield-check"></i> 99.4% Trust Score</span>
                            <span>•</span>
                            <span><i class="fa-solid fa-box text-slate-400"></i> 1,420 Sales</span>
                            <span>•</span>
                            <span><i class="fa-solid fa-location-dot text-slate-400"></i> Oregon, USA</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Free 48h dispatch on orders over $50</span>
                    <a href="{{ route('stall.show', 'timber-grain') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold transition">
                        Visit Stall <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2 (Span 5) -->
            <div class="md:col-span-5 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative h-44 rounded-2xl overflow-hidden mb-5">
                        <img src="https://images.unsplash.com/photo-1550009158-9ebf69173e03?auto=format&fit=crop&w=800&q=80" alt="Tech Sphere" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                            Electronics & Audio
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-slate-900">AudioCraft Studios</h3>
                    <p class="text-xs text-slate-500 mt-1">Audiophile headphones, DACs, and custom braided cables.</p>
                    <div class="flex items-center gap-3 mt-3 text-xs text-slate-600">
                        <span class="font-bold text-amber-600 flex items-center gap-1"><i class="fa-solid fa-star text-[10px]"></i> 4.9 (840 reviews)</span>
                        <span>•</span>
                        <span class="text-emerald-600 font-semibold">Verified Stall</span>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-700">From $35.00</span>
                    <a href="{{ route('stall.show', 'audiocraft') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition">
                        View Storefront
                    </a>
                </div>
            </div>

            <!-- Card 3 (Span 5) -->
            <div class="md:col-span-5 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative h-44 rounded-2xl overflow-hidden mb-5">
                        <img src="https://images.unsplash.com/photo-1523293182086-7651a899d37f?auto=format&fit=crop&w=800&q=80" alt="Ceramics" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                            Home & Ceramics
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-slate-900">Kyoto Clay Collective</h3>
                    <p class="text-xs text-slate-500 mt-1">Hand-thrown stoneware mugs, matcha bowls, and minimalist vases.</p>
                    <div class="flex items-center gap-3 mt-3 text-xs text-slate-600">
                        <span class="font-bold text-amber-600 flex items-center gap-1"><i class="fa-solid fa-star text-[10px]"></i> 5.0 (310 reviews)</span>
                        <span>•</span>
                        <span class="text-emerald-600 font-semibold">Top Rated</span>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-700">From $28.00</span>
                    <a href="{{ route('stall.show', 'kyoto-clay') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition">
                        View Storefront
                    </a>
                </div>
            </div>

            <!-- Card 4 (Span 7) -->
            <div class="md:col-span-7 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative h-64 rounded-2xl overflow-hidden mb-5">
                        <img src="https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=1000&q=80" alt="Organic Apparel" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between text-white">
                            <div>
                                <span class="bg-indigo-500 text-white text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded">Sustainable Wear</span>
                                <h3 class="text-xl font-bold font-heading mt-1">Solstice Linen & Wool</h3>
                                <p class="text-xs text-slate-300">100% Organic European linen apparel & handcrafted knitwear</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-xl bg-white/20 backdrop-blur text-xs font-bold">89 Products</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-4 text-xs text-slate-600">
                            <span class="flex items-center gap-1 font-bold text-emerald-600"><i class="fa-solid fa-shield-check"></i> 98.9% Trust Score</span>
                            <span>•</span>
                            <span><i class="fa-solid fa-box text-slate-400"></i> 2,190 Orders</span>
                            <span>•</span>
                            <span><i class="fa-solid fa-seedling text-emerald-600"></i> Eco-Certified</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Ships directly from Lisbon atelier</span>
                    <a href="{{ route('stall.show', 'solstice-linen') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold transition">
                        Visit Stall <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- 4. Category Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900">Shop by Category</h2>
            <p class="text-sm text-slate-500 mt-2">Explore thousands of products categorized across verified stalls.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            
            <a href="{{ route('category.show', 'artisan-crafts') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-indigo-400 hover:shadow-lg text-center group transition duration-200">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition duration-200">
                    <i class="fa-solid fa-paintbrush"></i>
                </div>
                <h3 class="font-bold text-sm text-slate-800 mt-3 group-hover:text-indigo-600">Artisan & Crafts</h3>
                <span class="text-[11px] text-slate-400">2,410 items</span>
            </a>

            <a href="{{ route('category.show', 'electronics') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-indigo-400 hover:shadow-lg text-center group transition duration-200">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition duration-200">
                    <i class="fa-solid fa-headphones"></i>
                </div>
                <h3 class="font-bold text-sm text-slate-800 mt-3 group-hover:text-indigo-600">Electronics & Gear</h3>
                <span class="text-[11px] text-slate-400">1,830 items</span>
            </a>

            <a href="{{ route('category.show', 'apparel') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-indigo-400 hover:shadow-lg text-center group transition duration-200">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-rose-500 group-hover:text-white transition duration-200">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <h3 class="font-bold text-sm text-slate-800 mt-3 group-hover:text-indigo-600">Fashion & Wear</h3>
                <span class="text-[11px] text-slate-400">4,120 items</span>
            </a>

            <a href="{{ route('category.show', 'home-living') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-indigo-400 hover:shadow-lg text-center group transition duration-200">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition duration-200">
                    <i class="fa-solid fa-couch"></i>
                </div>
                <h3 class="font-bold text-sm text-slate-800 mt-3 group-hover:text-indigo-600">Home & Living</h3>
                <span class="text-[11px] text-slate-400">3,290 items</span>
            </a>

            <a href="{{ route('category.show', 'organic-gourmet') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-indigo-400 hover:shadow-lg text-center group transition duration-200">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-orange-500 group-hover:text-white transition duration-200">
                    <i class="fa-solid fa-lemon"></i>
                </div>
                <h3 class="font-bold text-sm text-slate-800 mt-3 group-hover:text-indigo-600">Gourmet & Organics</h3>
                <span class="text-[11px] text-slate-400">980 items</span>
            </a>

            <a href="{{ route('products.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-indigo-400 hover:shadow-lg text-center group transition duration-200">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition duration-200">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <h3 class="font-bold text-sm text-slate-800 mt-3 group-hover:text-indigo-600">All Collections</h3>
                <span class="text-[11px] text-indigo-600 font-semibold">View All &rarr;</span>
            </a>

        </div>
    </section>

    <!-- 5. Seller CTA Section (3-Step Explainer) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 text-white p-8 sm:p-12 lg:p-16 relative overflow-hidden border border-slate-800 shadow-2xl">
            <!-- Glow circle -->
            <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-3xl mb-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold mb-3 border border-amber-500/30">
                    <i class="fa-solid fa-shop"></i> Sell on Bazaario
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold font-heading">Start your own digital stall in 3 simple steps</h2>
                <p class="text-slate-300 text-sm sm:text-base mt-2 leading-relaxed">
                    We empower independent merchants with full storefront autonomy, automated order routing, transparent commission rates, and instant customer trust.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                
                <!-- Step 1 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 font-black text-lg flex items-center justify-center font-heading">
                        1
                    </div>
                    <h3 class="text-lg font-bold font-heading text-white">Create Your Stall Profile</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Sign up, submit your shop name, logo, and seller verification details for fast-track admin approval.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500 text-white font-black text-lg flex items-center justify-center font-heading">
                        2
                    </div>
                    <h3 class="text-lg font-bold font-heading text-white">List Your Products</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Upload high-res images, set prices and inventory levels, and define your custom dispatch policies.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-slate-950 font-black text-lg flex items-center justify-center font-heading">
                        3
                    </div>
                    <h3 class="text-lg font-bold font-heading text-white">Receive Orders & Payouts</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Pack and ship directly to buyers. Track earnings with our transparent 8.5% platform fee and automated payouts.
                    </p>
                </div>

            </div>

            <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-6 pt-8 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <div class="flex -space-x-2">
                        <img class="w-9 h-9 rounded-full ring-2 ring-slate-900 object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Seller">
                        <img class="w-9 h-9 rounded-full ring-2 ring-slate-900 object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="Seller">
                        <img class="w-9 h-9 rounded-full ring-2 ring-slate-900 object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80" alt="Seller">
                    </div>
                    <span class="text-xs text-slate-300">Join <strong>450+</strong> registered merchants making sales today</span>
                </div>

                <a href="{{ route('seller.register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-xl shadow-amber-500/20 transition">
                    <i class="fa-solid fa-store"></i> Register as a Seller Now
                </a>
            </div>

        </div>
    </section>

</div>
@endsection
