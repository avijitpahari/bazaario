<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Bazaario — Shop Smarter. Sell Bigger.</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Compiled Tailwind CSS & App JS via Vite + Production Fallback -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">

    <style>
        body {
            background-color: #FFFDF8;
            background-image:
                radial-gradient(at 0% 0%, rgba(245, 166, 35, 0.14) 0px, transparent 48%),
                radial-gradient(at 100% 10%, rgba(245, 166, 35, 0.10) 0px, transparent 45%),
                radial-gradient(at 50% 50%, rgba(254, 243, 199, 0.35) 0px, transparent 65%),
                radial-gradient(at 90% 90%, rgba(15, 23, 42, 0.04) 0px, transparent 40%);
            background-attachment: fixed;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 16px 36px -10px rgba(15, 23, 42, 0.06), 0 0 0 1px rgba(15, 23, 42, 0.04);
        }

        .glass-card-nested {
            background: rgba(255, 255, 255, 0.80);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.95);
        }

        .glass-dock {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.9);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col font-sans text-slate-authority antialiased selection:bg-amber-action/30 selection:text-slate-authority relative overflow-x-hidden">
    <!-- Luminous Ambient Glow Background Accents -->
    <div class="pointer-events-none fixed -top-40 -left-40 w-[30rem] h-[30rem] rounded-full bg-amber-200/30 blur-3xl -z-10"></div>
    <div class="pointer-events-none fixed top-1/4 -right-40 w-[36rem] h-[36rem] rounded-full bg-orange-100/50 blur-3xl -z-10"></div>
    <div class="pointer-events-none fixed bottom-1/3 left-1/4 w-[28rem] h-[28rem] rounded-full bg-amber-100/30 blur-3xl -z-10"></div>

    <!-- 1. FLOATING PILL CUSTOMER NAVBAR -->
    @include('components.nav')

    <main class="flex-1 w-full pb-16">
        <!-- 2. HERO SECTION - 3D DIMENSIONAL SHOWCASE -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 pt-2 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
                <!-- Left Column: Copy & Floating Glass Search Pill -->
                <div class="lg:col-span-6 flex flex-col items-start z-10">
                    <!-- Eyebrow Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/15 border border-amber-500/30 mb-5 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span class="font-mono text-[11px] uppercase text-amber-700 font-bold tracking-wider">✦ BAZAARIO MARKETPLACE PLATFORM ✦</span>
                    </div>
                    <!-- Headline -->
                    <h1 class="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl text-slate-authority tracking-tight leading-[1.1] mb-4">
                        Shop <span class="text-amber-500 underline decoration-amber-500/30 underline-offset-4">smarter</span>.<br>
                        Sell <span class="text-amber-500 underline decoration-amber-500/30 underline-offset-4">bigger</span>.
                    </h1>
                    <p class="text-base sm:text-lg text-slate-authority/75 max-w-xl mb-7 leading-relaxed font-sans">
                        Curated products, live auctions, AI-powered recommendations and a trusted community — all in one place.
                    </p>

                    <!-- Floating Glass Search Pill Form -->
                    <form action="{{ route('products.index') }}" method="GET" class="w-full max-w-xl glass-panel rounded-2xl p-1.5 pl-4 shadow-glass flex items-center gap-2 ring-1 ring-slate-authority/5 mb-3 transition-all hover:border-amber-500/50">
                        <span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
                        <input class="w-full bg-transparent border-none text-slate-authority text-xs sm:text-sm placeholder:text-slate-400 focus:outline-none focus:ring-0 px-1 py-1.5" 
                               placeholder="Search curated products, auctions, sellers..." 
                               type="text" 
                               name="search">
                        <div class="hidden sm:flex items-center border-l border-slate-200 pl-2">
                            <span class="font-mono text-[10px] bg-amber-50 text-amber-900 font-semibold px-2 py-1 rounded-md border border-amber-200/80">All Categories</span>
                        </div>
                        <button class="bg-amber-500 hover:bg-amber-400 text-slate-950 w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm transition-transform active:scale-95" type="submit">
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    </form>

                    <!-- Popular Search Chips -->
                    <div class="flex flex-wrap items-center gap-2 font-mono text-[11px] text-slate-600 pt-1">
                        <span class="font-bold text-slate-900">Popular:</span>
                        <a class="px-2.5 py-0.5 rounded-full bg-white/80 border border-slate-200 hover:border-amber-500 hover:text-amber-600 text-slate-700 transition-colors"
                           href="{{ route('products.index', ['search' => 'iPhone 16 Pro']) }}">iPhone 16 Pro</a>
                        <a class="px-2.5 py-0.5 rounded-full bg-white/80 border border-slate-200 hover:border-amber-500 hover:text-amber-600 text-slate-700 transition-colors"
                           href="{{ route('products.index', ['search' => 'Leica M3']) }}">Leica M3</a>
                        <a class="px-2.5 py-0.5 rounded-full bg-white/80 border border-slate-200 hover:border-amber-500 hover:text-amber-600 text-slate-700 transition-colors"
                           href="{{ route('products.index', ['search' => 'Mechanical Keys']) }}">Mechanical Keys</a>
                        <a class="px-2.5 py-0.5 rounded-full bg-white/80 border border-slate-200 hover:border-amber-500 hover:text-amber-600 text-slate-700 transition-colors"
                           href="{{ route('products.index', ['search' => 'Sneakers']) }}">Sneakers</a>
                    </div>
                </div>

                <!-- Right Column: 3D Dimensional Visual Card with HUD Badges -->
                <div class="lg:col-span-6 relative">
                    <div class="relative glass-panel rounded-3xl p-3 shadow-glass group overflow-hidden border border-white">
                        <div class="relative aspect-[16/11] w-full rounded-2xl overflow-hidden bg-gradient-to-br from-amber-50/60 to-orange-50/40">
                            <!-- Official 3D Isometric Artwork -->
                            <img alt="Bazaario 3D Marketplace Platform"
                                 class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
                                 src="{{ asset('images/screen.png') }}">
                            
                            <!-- Floating HUD Badge 1 (Top-Left) -->
                            <div class="absolute top-3.5 left-3.5 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-full border border-slate-200/80 shadow-md flex items-center gap-2 pointer-events-none">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="font-mono text-[11px] text-slate-900 font-bold">0.2s Latency</span>
                                <span class="font-mono text-[10px] text-slate-500 hidden sm:inline">· Real-time floor</span>
                            </div>
                            <!-- Floating HUD Badge 2 (Top-Right) -->
                            <div class="absolute top-3.5 right-3.5 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-full border border-slate-200/80 shadow-md flex items-center gap-1.5 pointer-events-none">
                                <span class="material-symbols-outlined text-[15px] text-amber-500">verified_user</span>
                                <span class="font-mono text-[11px] text-slate-900 font-bold">100% Escrow</span>
                                <span class="font-mono text-[10px] text-slate-500 hidden sm:inline">· Dispute Protection</span>
                            </div>
                            <!-- Floating HUD Badge 3 (Bottom-Left Live Ticker) -->
                            <div class="absolute bottom-3.5 left-3.5 right-3.5 bg-slate-900/90 text-white backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/10 shadow-lg flex items-center justify-between pointer-events-none">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span class="font-mono text-[11px] font-semibold text-white/90">
                                        {{ number_format($stats['total_sellers'] ?? 14000) }}+ Verified Sellers Trading
                                    </span>
                                </div>
                                <span class="font-mono text-[9px] bg-amber-400 text-slate-950 px-2 py-0.5 rounded font-bold uppercase tracking-wider">LIVE</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4-Pillar Trust Glass Badges -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-10">
                <div class="glass-panel rounded-2xl p-4 flex items-center gap-3 transition-transform hover:-translate-y-0.5 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">auto_awesome</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xs sm:text-sm text-slate-900">AI Recommendations</h4>
                        <p class="text-[11px] text-slate-500">Tailored to your taste</p>
                    </div>
                </div>
                <div class="glass-panel rounded-2xl p-4 flex items-center gap-3 transition-transform hover:-translate-y-0.5 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/15 text-orange-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">gavel</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xs sm:text-sm text-slate-900">Live Auctions</h4>
                        <p class="text-[11px] text-slate-500">Sub-second bidding</p>
                    </div>
                </div>
                <div class="glass-panel rounded-2xl p-4 flex items-center gap-3 transition-transform hover:-translate-y-0.5 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">verified_user</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xs sm:text-sm text-slate-900">Verified Escrow</h4>
                        <p class="text-[11px] text-slate-500">100% funds protection</p>
                    </div>
                </div>
                <div class="glass-panel rounded-2xl p-4 flex items-center gap-3 transition-transform hover:-translate-y-0.5 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/15 text-indigo-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">bolt</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xs sm:text-sm text-slate-900">Instant Payouts</h4>
                        <p class="text-[11px] text-slate-500">Automated settlement</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. SHOP BY CATEGORY - TACTILE GLASS TILES -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-8" id="categories">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="font-mono text-[11px] uppercase tracking-wider text-amber-600 font-bold mb-1">
                        DISCOVER MARKETPLACE
                    </div>
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-900 tracking-tight">Shop by Category</h2>
                </div>
                <a class="text-xs sm:text-sm font-bold text-slate-800 hover:text-amber-600 transition-colors flex items-center gap-1 group"
                   href="{{ route('products.index') }}">
                    View all categories
                    <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>

            @php
                $catEmojis = [
                    'Electronics' => '🎧',
                    'Electronics & Gadgets' => '🎧',
                    'Fashion' => '🧥',
                    'Fashion & Apparel' => '🧥',
                    'Home' => '🪑',
                    'Home & Living' => '🪑',
                    'Automotive' => '🚗',
                    'Sports' => '⚽',
                    'Beauty & Care' => '🧴',
                    'Toys & Kids' => '🧸',
                    'Books & Fine Stationery' => '📚',
                    'Artisan & Handmade Crafts' => '🎨',
                    'Rare Collectibles & Antiques' => '🏺',
                    'Jewelry' => '💍'
                ];
                $catColors = [
                    'from-amber-200 to-amber-300/60',
                    'from-indigo-200 to-indigo-300/60',
                    'from-orange-200 to-orange-300/60',
                    'from-rose-200 to-rose-300/60',
                    'from-emerald-200 to-emerald-300/60',
                    'from-purple-200 to-purple-300/60',
                    'from-teal-200 to-teal-300/60',
                    'from-sky-200 to-sky-300/60'
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3.5">
                @forelse(($dbCategories ?? collect())->take(8) as $catIndex => $cat)
                    @php
                        $catEmoji = $catEmojis[$cat->name] ?? '🛍️';
                        $catColor = $catColors[$catIndex % count($catColors)];
                    @endphp
                    <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
                       href="{{ route('products.index', ['category' => $cat->slug]) }}">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $catColor }} flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform shadow-xs">
                            <span class="text-2xl">{{ $catEmoji }}</span>
                        </div>
                        <span class="font-display font-bold text-xs text-slate-900 line-clamp-1">{{ $cat->name }}</span>
                        <span class="font-mono text-[10px] text-slate-500 mt-0.5">{{ $cat->products_count ?? 0 }} items</span>
                    </a>
                @empty
                    <div class="col-span-full text-center py-8 text-slate-500 font-sans text-sm">No categories active right now.</div>
                @endforelse
            </div>
        </section>

        <!-- 4. DUAL FEATURE BANNERS - LIVE AUCTIONS & AI ASSISTANT -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-8" id="auctions">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Card 1: Live Auctions Spotlight -->
                <div class="lg:col-span-6 bg-slate-900 text-white rounded-3xl p-7 sm:p-8 backdrop-blur-xl border border-white/10 shadow-2xl relative overflow-hidden flex flex-col justify-between group">
                    <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>
                    
                    <div class="flex items-center justify-between mb-6 z-10">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 font-mono text-amber-400 text-[11px] font-bold tracking-wider">
                            ✦ BID • WIN • SAVE
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 text-rose-400 font-mono text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            LIVE BIDDING
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center z-10 mb-6">
                        <div class="sm:col-span-6">
                            <h3 class="font-display font-bold text-2xl sm:text-3xl text-white tracking-tight leading-tight mb-2">
                                Unique items.<br>Real value.
                            </h3>
                            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed mb-4">
                                From rare Leica collectibles to mechanical watches — discover live bidding with anti-sniping protection.
                            </p>
                            <a class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-5 py-2.5 rounded-full transition-all shadow-md active:scale-95"
                               href="{{ route('products.index', ['sale_type' => 'auction']) }}">
                                <span>Explore Auctions</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>

                        <!-- Auction Item Box -->
                        <div class="sm:col-span-6 bg-white/5 backdrop-blur-md rounded-2xl p-3 border border-white/10 shadow-lg">
                            <div class="h-32 w-full rounded-xl overflow-hidden mb-2 relative bg-slate-950">
                                @if($featuredAuction ?? null)
                                    @php
                                        $auctionImgPath = $featuredAuction->product->primaryImage->image_path ?? null;
                                        $auctionImg = $auctionImgPath
                                            ? (str_starts_with($auctionImgPath, 'http') ? $auctionImgPath : \Illuminate\Support\Facades\Storage::url($auctionImgPath))
                                            : asset('images/products/keyboard_1.jpg');
                                    @endphp
                                    <img alt="{{ $featuredAuction->product->name ?? 'Featured Auction' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $auctionImg }}">
                                    <span class="absolute bottom-2 right-2 bg-slate-900/90 text-white font-mono text-[10px] px-2 py-0.5 rounded border border-white/10">
                                        {{ $featuredAuction->bids->count() }} Bids
                                    </span>
                                @else
                                    <img alt="Vintage 1954 Leica M3" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ asset('images/products/keyboard_1.jpg') }}">
                                    <span class="absolute bottom-2 right-2 bg-slate-900/90 text-white font-mono text-[10px] px-2 py-0.5 rounded border border-white/10">24 Bids</span>
                                @endif
                            </div>

                            @if($featuredAuction ?? null)
                                <div class="text-[11px] text-slate-200 font-semibold font-display line-clamp-1">
                                    {{ $featuredAuction->product->name ?? 'Featured Auction Item' }}
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <div>
                                        <span class="font-mono text-[9px] text-slate-400 block uppercase">Current Bid</span>
                                        <span class="font-display text-lg font-bold text-amber-400">₹ {{ number_format($featuredAuction->current_bid ?? $featuredAuction->start_price ?? 0, 0) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-mono text-[9px] text-slate-400 block uppercase">Ends in</span>
                                        <span class="font-mono text-xs font-bold text-white flex items-center gap-1 justify-end">
                                            <span class="material-symbols-outlined text-[12px] text-amber-400">timer</span>
                                            {{ $featuredAuction->ends_at ? $featuredAuction->ends_at->diffForHumans() : 'Ending soon' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="text-[11px] text-slate-200 font-semibold font-display">Vintage 1954 Leica M3</div>
                                <div class="flex items-center justify-between mt-1">
                                    <div>
                                        <span class="font-mono text-[9px] text-slate-400 block uppercase">Current Bid</span>
                                        <span class="font-display text-lg font-bold text-amber-400">₹ 8,500</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-mono text-[9px] text-slate-400 block uppercase">Ends in</span>
                                        <span class="font-mono text-xs font-bold text-white flex items-center gap-1 justify-end">
                                            <span class="material-symbols-outlined text-[12px] text-amber-400">timer</span>
                                            00:14:32
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card 2: AI Shopping Assistant -->
                <div class="lg:col-span-6 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-white/80 backdrop-blur-xl rounded-3xl p-7 sm:p-8 border border-amber-500/30 shadow-glass relative overflow-hidden flex flex-col justify-between" id="ai">
                    <div class="flex items-center justify-between mb-6 z-10">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-500/30 font-mono text-slate-900 text-[11px] font-bold">
                            <span class="material-symbols-outlined text-[14px] text-amber-600">auto_awesome</span>
                            AI SHOPPING ASSISTANT
                        </span>
                        <span class="font-mono text-[11px] text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center z-10 mb-2">
                        <div class="sm:col-span-6">
                            <h3 class="font-display font-bold text-2xl sm:text-3xl text-slate-900 tracking-tight leading-tight mb-2">
                                Smarter choices.<br>Better buys.
                            </h3>
                            <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-4">
                                Ask anything. Our semantic agent scans verified reviews, live prices, and authentic seller inventories in seconds.
                            </p>
                            <a class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-5 py-2.5 rounded-full transition-all shadow-md active:scale-95" 
                               href="{{ route('products.index') }}">
                                <span>Try AI Compare</span>
                                <span class="material-symbols-outlined text-[16px] text-amber-400">auto_awesome</span>
                            </a>
                        </div>

                        <!-- Floating Prompt Bubbles with Mascot -->
                        <div class="sm:col-span-6 flex flex-col items-center gap-2.5">
                            <a href="{{ route('products.index', ['search' => 'Headphones']) }}" class="bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full text-[11px] font-medium text-slate-800 shadow-xs border border-slate-200 -rotate-2 w-full text-center hover:border-amber-500 cursor-pointer transition-colors">
                                💬 "Best noise-canceling headphones?"
                            </a>
                            <div class="w-14 h-14 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-3xl shadow-sm">
                                🤖
                            </div>
                            <a href="{{ route('products.index', ['search' => 'Wireless']) }}" class="bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full text-[11px] font-medium text-slate-800 shadow-xs border border-slate-200 rotate-1 w-full text-center hover:border-amber-500 cursor-pointer transition-colors">
                                ⚡ "Compare Sony WH-1000 vs AirPods"
                            </a>
                            <a href="{{ route('products.index', ['search' => 'Ceramic']) }}" class="bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full text-[11px] font-medium text-slate-800 shadow-xs border border-slate-200 -rotate-1 w-full text-center hover:border-amber-500 cursor-pointer transition-colors">
                                🔍 "Find artisan pottery under ₹1,000"
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. TRENDING PRODUCTS SHELF -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-10" id="shop">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <div class="font-mono text-[11px] uppercase tracking-wider text-amber-600 font-bold mb-1">
                        CURATED CATALOG
                    </div>
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-900 tracking-tight">Trending Products</h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Most active listings and community verified finds.</p>
                </div>
                <a class="text-xs sm:text-sm font-bold text-slate-900 hover:text-amber-600 transition-colors flex items-center gap-1 group"
                   href="{{ route('products.index') }}">
                    View all products
                    <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>

            <!-- 5 Product Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">
                @forelse(($trendingProducts ?? []) as $index => $prod)
                    @php
                        $prodImage = $prod->primaryImage->image_path 
                            ?? ($prod->images->first()->image_path ?? ($prod->image_url ?? ($prod->image ?? null)));
                        
                        if ($prodImage && !str_starts_with($prodImage, 'http')) {
                            $prodImage = \Illuminate\Support\Facades\Storage::url($prodImage);
                        }
                        
                        $badgeColors = [
                            'bg-slate-900/90 text-amber-400 border border-amber-400/30',
                            'bg-emerald-600 text-white',
                            'bg-amber-500 text-slate-950 font-bold',
                            'bg-rose-600 text-white',
                            'bg-indigo-600 text-white'
                        ];
                        $badgeLabels = ['✦ AI PICK', 'NEW', 'BESTSELLER', 'HOT DEAL', 'FEATURED'];
                        $badgeStyle = $badgeColors[$index % count($badgeColors)];
                        $badgeText = $badgeLabels[$index % count($badgeLabels)];
                        
                        $productSlugOrId = $prod->slug ?? $prod->id;
                    @endphp
                    
                    <div class="glass-panel rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-glass group">
                        <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-slate-100 mb-3 flex items-center justify-center">
                            <span class="absolute top-2.5 left-2.5 font-mono text-[10px] {{ $badgeStyle }} px-2 py-0.5 rounded backdrop-blur-md font-bold z-10">
                                {{ $badgeText }}
                            </span>
                            <a href="{{ route('products.show', $productSlugOrId) }}" class="w-full h-full block">
                                <img alt="{{ $prod->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                                     src="{{ $prodImage ?? 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&auto=format&fit=crop&q=80' }}">
                            </a>
                        </div>
                        <div>
                            <div class="flex items-center justify-between font-mono text-[10px] text-slate-500 mb-1">
                                <span>{{ $prod->category->name ?? 'General' }}</span>
                                <span class="flex items-center text-amber-600 font-bold">★ {{ number_format($prod->rating ?? 4.8, 1) }}</span>
                            </div>
                            <h4 class="font-display font-bold text-sm text-slate-900 line-clamp-1 mb-0.5">
                                <a href="{{ route('products.show', $productSlugOrId) }}" class="hover:text-amber-600 transition-colors">
                                    {{ $prod->name }}
                                </a>
                            </h4>
                            <p class="text-xs text-slate-500 mb-3 line-clamp-1">{{ $prod->short_description ?? ($prod->description ?? 'High quality item') }}</p>
                            
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <div>
                                    <span class="font-display font-bold text-base text-slate-900" data-price-inr="{{ $prod->price }}">₹ {{ number_format($prod->price, 0) }}</span>
                                    @if($prod->original_price ?? ($prod->old_price ?? null))
                                        <span class="text-[10px] text-slate-400 line-through block -mt-1" data-price-inr="{{ $prod->original_price ?? $prod->old_price }}">
                                            ₹ {{ number_format($prod->original_price ?? $prod->old_price, 0) }}
                                        </span>
                                    @endif
                                </div>
                                <form action="{{ route('cart.store') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $prod->id }}">
                                    <button type="submit" aria-label="Add to cart" class="w-8 h-8 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 flex items-center justify-center transition-transform active:scale-95 shadow-sm">
                                        <span class="material-symbols-outlined text-[17px]">shopping_bag</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Fallback static products if DB products not found -->
                    <div class="glass-panel rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-glass group">
                        <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-slate-100 mb-3">
                            <span class="absolute top-2.5 left-2.5 font-mono text-[10px] bg-slate-900 text-amber-400 px-2 py-0.5 rounded backdrop-blur-md font-bold z-10 border border-amber-400/30">
                                ✦ AI PICK
                            </span>
                            <img alt="Studio Wireless Pro" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80">
                        </div>
                        <div>
                            <div class="flex items-center justify-between font-mono text-[10px] text-slate-500 mb-1">
                                <span>Wireless Audio</span>
                                <span class="flex items-center text-amber-600 font-bold">★ 4.8</span>
                            </div>
                            <h4 class="font-display font-bold text-sm text-slate-900 line-clamp-1 mb-0.5">Studio Wireless Pro</h4>
                            <p class="text-xs text-slate-500 mb-3">Noise Cancelling 30h</p>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <div>
                                    <span class="font-display font-bold text-base text-slate-900">₹ 2,499</span>
                                    <span class="text-[10px] text-slate-400 line-through block -mt-1">₹ 3,299</span>
                                </div>
                                <a href="{{ route('products.index') }}" aria-label="View product" class="w-8 h-8 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 flex items-center justify-center transition-transform active:scale-95 shadow-sm">
                                    <span class="material-symbols-outlined text-[17px]">shopping_bag</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 5.5 FEATURED SELLERS SHELF -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                <div>
                    <div class="font-mono text-[11px] uppercase tracking-wider text-amber-600 font-bold mb-1">
                        VERIFIED STALLS & ARTISANS
                    </div>
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-900 tracking-tight">Featured Sellers</h2>
                </div>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-amber-600 transition-colors group">
                    Explore Marketplace Sellers <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse(($featuredSellers ?? []) as $seller)
                    @php
                        $profile = $seller->sellerProfile;
                        $shopName = $profile->shop_name ?? ($seller->name . "'s Store");
                        $city = $profile->city ?? 'Marketplace Seller';
                        $bio = $profile->bio ?? 'Verified seller on Bazaario offering authentic products with fast delivery.';
                        $logo = $profile->logo_path ? Storage::url($profile->logo_path) : null;
                        $banner = $profile->banner_path ? Storage::url($profile->banner_path) : null;
                        $trustScore = $profile->trust_score ?? 98;
                    @endphp
                    <div class="glass-panel rounded-3xl p-5 hover:border-amber-500/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl group flex flex-col justify-between relative overflow-hidden bg-white/70 backdrop-blur-md">
                        <!-- Top Decorative Accent -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-orange-400 to-amber-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div>
                            <!-- Banner & Logo Header -->
                            <div class="relative h-28 rounded-2xl overflow-hidden mb-5 bg-gradient-to-br from-amber-100 via-orange-50 to-slate-100 border border-slate-200/80 shadow-inner">
                                @if($banner)
                                    <img src="{{ $banner }}" alt="{{ $shopName }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                @else
                                    <div class="w-full h-full bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-amber-400/20 via-orange-300/10 to-transparent flex items-center justify-center">
                                        <span class="material-symbols-outlined text-amber-500/20 text-6xl">storefront</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/10 to-transparent"></div>
                                
                                <!-- Store Logo Avatar -->
                                <div class="absolute -bottom-3 left-4 w-12 h-12 rounded-2xl bg-white p-0.5 shadow-lg border border-white/80 ring-2 ring-slate-900/5">
                                    @if($logo)
                                        <img src="{{ $logo }}" alt="{{ $shopName }}" class="w-full h-full object-cover rounded-xl">
                                    @else
                                        <div class="w-full h-full rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-display font-extrabold flex items-center justify-center text-lg shadow-sm">
                                            {{ strtoupper(mb_substr($shopName, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Verified Pill Badge -->
                                <div class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full bg-slate-900/80 hover:bg-slate-900 text-amber-400 text-[10px] font-bold flex items-center gap-1 shadow-md backdrop-blur-md border border-amber-500/30">
                                    <span class="material-symbols-outlined text-[13px] text-amber-400">verified</span>
                                    Verified Stall
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="pt-1 px-1">
                                <h3 class="font-display font-bold text-slate-900 text-base group-hover:text-amber-600 transition-colors truncate tracking-tight">
                                    {{ $shopName }}
                                </h3>
                                <div class="flex items-center gap-1.5 text-xs text-amber-600 font-medium mt-1 mb-2.5">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    <span>{{ $city }}</span>
                                </div>
                                <p class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed font-normal">
                                    {{ $bio }}
                                </p>
                            </div>
                        </div>

                        <!-- Footer Info & Button -->
                        <div class="px-1 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between py-2 text-xs mb-3">
                                <div class="flex items-center gap-1.5 text-slate-500 font-medium">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">inventory_2</span>
                                    <span><strong class="text-slate-900 font-bold">{{ $seller->products_count ?? 0 }}</strong> Products</span>
                                </div>
                                <div class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-[11px] border border-emerald-200/60">
                                    <span class="material-symbols-outlined text-[13px]">shield</span>
                                    <span>{{ $trustScore }}%</span>
                                </div>
                            </div>
                            <a href="{{ route('products.index', ['seller' => $seller->id]) }}" class="w-full py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-semibold text-xs flex items-center justify-center gap-2 transition-all duration-200 shadow-sm active:scale-95 group/btn">
                                <span>Visit Stall</span>
                                <span class="material-symbols-outlined text-[15px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <!-- Card 1: Clay & Kiln Studio -->
                    <div class="glass-panel rounded-3xl p-5 hover:border-amber-500/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl group flex flex-col justify-between relative overflow-hidden bg-white/70 backdrop-blur-md">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-orange-400 to-amber-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div>
                            <div class="relative h-28 rounded-2xl overflow-hidden mb-5 bg-gradient-to-br from-amber-500/15 via-orange-400/10 to-amber-600/20 border border-slate-200/80 shadow-inner">
                                <img src="https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?auto=format&fit=crop&w=600&q=80" alt="Clay & Kiln Studio Banner" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/10 to-transparent"></div>
                                
                                <div class="absolute -bottom-3 left-4 w-12 h-12 rounded-2xl bg-white p-0.5 shadow-lg border border-white/80 ring-2 ring-slate-900/5 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=150&q=80" alt="Clay Logo" class="w-full h-full object-cover rounded-xl">
                                </div>

                                <div class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full bg-slate-900/80 hover:bg-slate-900 text-amber-400 text-[10px] font-bold flex items-center gap-1 shadow-md backdrop-blur-md border border-amber-500/30">
                                    <span class="material-symbols-outlined text-[13px] text-amber-400">verified</span>
                                    Verified Stall
                                </div>
                            </div>

                            <div class="pt-1 px-1">
                                <h3 class="font-display font-bold text-slate-900 text-base group-hover:text-amber-600 transition-colors truncate tracking-tight">
                                    Clay & Kiln Studio
                                </h3>
                                <div class="flex items-center gap-1.5 text-xs text-amber-600 font-medium mt-1 mb-2.5">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    <span>Jaipur, Rajasthan</span>
                                </div>
                                <p class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed font-normal">
                                    Handcrafted stoneware ceramics, artisan coffee drippers, tablewares, and terracottas.
                                </p>
                            </div>
                        </div>

                        <div class="px-1 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between py-2 text-xs mb-3">
                                <div class="flex items-center gap-1.5 text-slate-500 font-medium">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">inventory_2</span>
                                    <span><strong class="text-slate-900 font-bold">48</strong> Products</span>
                                </div>
                                <div class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-[11px] border border-emerald-200/60">
                                    <span class="material-symbols-outlined text-[13px]">shield</span>
                                    <span>99%</span>
                                </div>
                            </div>
                            <a href="{{ route('products.index') }}" class="w-full py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-semibold text-xs flex items-center justify-center gap-2 transition-all duration-200 shadow-sm active:scale-95 group/btn">
                                <span>Visit Stall</span>
                                <span class="material-symbols-outlined text-[15px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    <!-- Card 2: Varanasi Weavers Guild -->
                    <div class="glass-panel rounded-3xl p-5 hover:border-amber-500/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl group flex flex-col justify-between relative overflow-hidden bg-white/70 backdrop-blur-md">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-orange-400 to-amber-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div>
                            <div class="relative h-28 rounded-2xl overflow-hidden mb-5 bg-gradient-to-br from-purple-500/15 via-pink-400/10 to-rose-600/20 border border-slate-200/80 shadow-inner">
                                <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=600&q=80" alt="Varanasi Weavers Banner" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/10 to-transparent"></div>
                                
                                <div class="absolute -bottom-3 left-4 w-12 h-12 rounded-2xl bg-white p-0.5 shadow-lg border border-white/80 ring-2 ring-slate-900/5 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1606760227091-3dd850d492a6?auto=format&fit=crop&w=150&q=80" alt="Weavers Logo" class="w-full h-full object-cover rounded-xl">
                                </div>

                                <div class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full bg-slate-900/80 hover:bg-slate-900 text-amber-400 text-[10px] font-bold flex items-center gap-1 shadow-md backdrop-blur-md border border-amber-500/30">
                                    <span class="material-symbols-outlined text-[13px] text-amber-400">verified</span>
                                    Verified Stall
                                </div>
                            </div>

                            <div class="pt-1 px-1">
                                <h3 class="font-display font-bold text-slate-900 text-base group-hover:text-amber-600 transition-colors truncate tracking-tight">
                                    Varanasi Weavers Guild
                                </h3>
                                <div class="flex items-center gap-1.5 text-xs text-amber-600 font-medium mt-1 mb-2.5">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    <span>Varanasi, UP</span>
                                </div>
                                <p class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed font-normal">
                                    Traditional handloom master weavers preserving organic mulberry silk, sarees, & brocades.
                                </p>
                            </div>
                        </div>

                        <div class="px-1 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between py-2 text-xs mb-3">
                                <div class="flex items-center gap-1.5 text-slate-500 font-medium">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">inventory_2</span>
                                    <span><strong class="text-slate-900 font-bold">64</strong> Products</span>
                                </div>
                                <div class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-[11px] border border-emerald-200/60">
                                    <span class="material-symbols-outlined text-[13px]">shield</span>
                                    <span>98%</span>
                                </div>
                            </div>
                            <a href="{{ route('products.index') }}" class="w-full py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-semibold text-xs flex items-center justify-center gap-2 transition-all duration-200 shadow-sm active:scale-95 group/btn">
                                <span>Visit Stall</span>
                                <span class="material-symbols-outlined text-[15px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    <!-- Card 3: Heritage Leatherworks -->
                    <div class="glass-panel rounded-3xl p-5 hover:border-amber-500/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl group flex flex-col justify-between relative overflow-hidden bg-white/70 backdrop-blur-md">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-orange-400 to-amber-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div>
                            <div class="relative h-28 rounded-2xl overflow-hidden mb-5 bg-gradient-to-br from-amber-800/15 via-orange-900/10 to-amber-700/20 border border-slate-200/80 shadow-inner">
                                <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=600&q=80" alt="Heritage Leatherworks Banner" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/10 to-transparent"></div>
                                
                                <div class="absolute -bottom-3 left-4 w-12 h-12 rounded-2xl bg-white p-0.5 shadow-lg border border-white/80 ring-2 ring-slate-900/5 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=150&q=80" alt="Leather Logo" class="w-full h-full object-cover rounded-xl">
                                </div>

                                <div class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full bg-slate-900/80 hover:bg-slate-900 text-amber-400 text-[10px] font-bold flex items-center gap-1 shadow-md backdrop-blur-md border border-amber-500/30">
                                    <span class="material-symbols-outlined text-[13px] text-amber-400">verified</span>
                                    Verified Stall
                                </div>
                            </div>

                            <div class="pt-1 px-1">
                                <h3 class="font-display font-bold text-slate-900 text-base group-hover:text-amber-600 transition-colors truncate tracking-tight">
                                    Heritage Leatherworks
                                </h3>
                                <div class="flex items-center gap-1.5 text-xs text-amber-600 font-medium mt-1 mb-2.5">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    <span>Kolkata, WB</span>
                                </div>
                                <p class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed font-normal">
                                    Master artisan guild specializing in full-grain genuine leather bags, wallets, & journals.
                                </p>
                            </div>
                        </div>

                        <div class="px-1 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between py-2 text-xs mb-3">
                                <div class="flex items-center gap-1.5 text-slate-500 font-medium">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">inventory_2</span>
                                    <span><strong class="text-slate-900 font-bold">32</strong> Products</span>
                                </div>
                                <div class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-[11px] border border-emerald-200/60">
                                    <span class="material-symbols-outlined text-[13px]">shield</span>
                                    <span>97%</span>
                                </div>
                            </div>
                            <a href="{{ route('products.index') }}" class="w-full py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-semibold text-xs flex items-center justify-center gap-2 transition-all duration-200 shadow-sm active:scale-95 group/btn">
                                <span>Visit Stall</span>
                                <span class="material-symbols-outlined text-[15px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    <!-- Card 4: TechForge Systems -->
                    <div class="glass-panel rounded-3xl p-5 hover:border-amber-500/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl group flex flex-col justify-between relative overflow-hidden bg-white/70 backdrop-blur-md">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-orange-400 to-amber-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div>
                            <div class="relative h-28 rounded-2xl overflow-hidden mb-5 bg-gradient-to-br from-blue-500/15 via-indigo-400/10 to-cyan-600/20 border border-slate-200/80 shadow-inner">
                                <img src="https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=600&q=80" alt="TechForge Banner" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/10 to-transparent"></div>
                                
                                <div class="absolute -bottom-3 left-4 w-12 h-12 rounded-2xl bg-white p-0.5 shadow-lg border border-white/80 ring-2 ring-slate-900/5 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=150&q=80" alt="Tech Logo" class="w-full h-full object-cover rounded-xl">
                                </div>

                                <div class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full bg-slate-900/80 hover:bg-slate-900 text-amber-400 text-[10px] font-bold flex items-center gap-1 shadow-md backdrop-blur-md border border-amber-500/30">
                                    <span class="material-symbols-outlined text-[13px] text-amber-400">verified</span>
                                    Verified Stall
                                </div>
                            </div>

                            <div class="pt-1 px-1">
                                <h3 class="font-display font-bold text-slate-900 text-base group-hover:text-amber-600 transition-colors truncate tracking-tight">
                                    TechForge Systems
                                </h3>
                                <div class="flex items-center gap-1.5 text-xs text-amber-600 font-medium mt-1 mb-2.5">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    <span>Bengaluru, KA</span>
                                </div>
                                <p class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed font-normal">
                                    Curators of premium mechanical keyboards, custom desk pads, ANC audio, & workspace gear.
                                </p>
                            </div>
                        </div>

                        <div class="px-1 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between py-2 text-xs mb-3">
                                <div class="flex items-center gap-1.5 text-slate-500 font-medium">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">inventory_2</span>
                                    <span><strong class="text-slate-900 font-bold">52</strong> Products</span>
                                </div>
                                <div class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-[11px] border border-emerald-200/60">
                                    <span class="material-symbols-outlined text-[13px]">shield</span>
                                    <span>100%</span>
                                </div>
                            </div>
                            <a href="{{ route('products.index') }}" class="w-full py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-semibold text-xs flex items-center justify-center gap-2 transition-all duration-200 shadow-sm active:scale-95 group/btn">
                                <span>Visit Stall</span>
                                <span class="material-symbols-outlined text-[15px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 6. WHY CHOOSE BAZAARIO - 5-PILLAR FLOATING GLASS HUB -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
            <div class="glass-panel rounded-3xl p-8 sm:p-10 shadow-glass">
                <div class="text-center max-w-xl mx-auto mb-10">
                    <div class="font-mono text-[11px] uppercase tracking-wider text-amber-600 font-bold mb-1">
                        TRUST & SPEED INFRASTRUCTURE
                    </div>
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-900 tracking-tight">Why Choose Bazaario?</h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">A transparent, high-frequency marketplace engineered for genuine trade.</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                    <div class="flex flex-col items-center text-center group">
                        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px]">verified</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-900 text-sm mb-0.5">Curated Products</h4>
                        <p class="text-[11px] text-slate-500">Quality vetted creators</p>
                    </div>

                    <div class="flex flex-col items-center text-center group">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px]">shield</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-900 text-sm mb-0.5">Verified Sellers</h4>
                        <p class="text-[11px] text-slate-500">KYC & escrow secured</p>
                    </div>

                    <div class="flex flex-col items-center text-center group">
                        <div class="w-16 h-16 rounded-2xl bg-amber-500/15 border border-amber-500/35 flex items-center justify-center text-slate-900 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px] text-amber-600">psychology</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-900 text-sm mb-0.5">AI-Powered Deals</h4>
                        <p class="text-[11px] text-slate-500">Predictive recommendations</p>
                    </div>

                    <div class="flex flex-col items-center text-center group">
                        <div class="w-16 h-16 rounded-2xl bg-orange-500/10 border border-orange-500/30 flex items-center justify-center text-orange-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px]">gavel</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-900 text-sm mb-0.5">Live Auctions</h4>
                        <p class="text-[11px] text-slate-500">0.2s anti-sniping floor</p>
                    </div>

                    <div class="flex flex-col items-center text-center group col-span-2 md:col-span-1">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px]">lock</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-900 text-sm mb-0.5">Secure Escrow</h4>
                        <p class="text-[11px] text-slate-500">100% dispute protection</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. MERCHANT GROWTH BANNER -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-8" id="sell">
            <div class="bg-gradient-to-r from-[#fae7ce]/90 via-[#f7ecd9]/80 to-[#f4e2c8]/90 rounded-3xl p-8 sm:p-12 border border-amber-500/30 shadow-glass relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Left Text Info -->
                    <div class="lg:col-span-6 z-10">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-slate-900 font-mono text-[11px] font-bold uppercase tracking-wider mb-4">
                            ✦ FOR SELLERS & ARTISANS
                        </div>
                        <h2 class="font-display font-bold text-3xl sm:text-4xl text-slate-900 tracking-tight leading-tight mb-3">
                            Turn your passion into <span class="text-amber-600 underline decoration-amber-500/40">profit.</span>
                        </h2>
                        <p class="text-slate-700 text-sm sm:text-base mb-7 max-w-md leading-relaxed">
                            Join thousands of independent sellers and creators reaching high-intent collectors with low fees and instant escrow payouts.
                        </p>
                        <a class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm px-7 py-3 rounded-full transition-all shadow-md active:scale-95"
                           href="{{ url('/register') }}">
                            <span>Start Selling</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>

                    <!-- Right Visual / Seller Snapshot -->
                    <div class="lg:col-span-6 relative flex items-center justify-center pt-6 sm:pt-4">
                        <div class="absolute top-1 left-4 sm:left-10 bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-white/80 z-20 transition-transform hover:scale-105">
                            <div class="font-display text-2xl font-black text-slate-900 leading-none">
                                {{ ($stats['total_sellers'] ?? 0) > 50 ? number_format($stats['total_sellers']) . '+' : '2.5K+' }}
                            </div>
                            <div class="font-mono text-[10px] text-slate-500 uppercase mt-0.5 font-bold">Active Stores</div>
                            <div class="flex items-end gap-1 h-6 my-2">
                                <span class="w-2 h-2.5 bg-amber-500/40 rounded-sm"></span>
                                <span class="w-2 h-4 bg-amber-500/60 rounded-sm"></span>
                                <span class="w-2 h-3.5 bg-amber-500/75 rounded-sm"></span>
                                <span class="w-2 h-5 bg-amber-500/90 rounded-sm"></span>
                                <span class="w-2 h-6 bg-amber-500 rounded-sm"></span>
                            </div>
                            <span class="font-mono text-[10px] font-bold text-emerald-600 flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[13px]">trending_up</span> +40% this month
                            </span>
                        </div>

                        <div class="w-full max-w-md h-72 sm:h-80 rounded-2xl overflow-hidden shadow-xl relative border border-white bg-amber-100/50">
                            <img alt="Seller packing orders" class="w-full h-full object-cover"
                                 src="{{ asset('images/categories/artisan_craft.jpg') }}"
                                 onerror="this.onerror=null; this.src='https://lh3.googleusercontent.com/aida-public/AB6AXuAG2vRC-DtkXADNz8vvl3ncrh_0Y5DviuLk7LtnN0CfYWRRkXij0sTk2g77tz4-mles9c1EP2Unz4nQot1EjsCPtDHkv2rYodBafoBLUQbJnEVIN3rqzDIr1siwY45RJapwW_AiTl3HsxfE0e-zVNpbgv_V_8wOexctcB3SzzbadPeBiLQNENTTHCfM69fy4LSSisafqjhiYAwuzPvxrjoCqSCoVSEs5r2PdQAqk7sigESM2HeCMpD2';">
                            <div class="absolute bottom-3 right-3 bg-slate-900/85 backdrop-blur-md px-3.5 py-1.5 rounded-xl text-white font-mono text-[11px] font-semibold border border-white/10 flex items-center gap-1.5 shadow-md">
                                <span class="material-symbols-outlined text-amber-400 text-[15px]">storefront</span>
                                Grow Your Business ✨
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. TESTIMONIALS (Tactile Glass Cards) -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <div class="font-mono text-[11px] uppercase tracking-wider text-amber-600 font-bold mb-1">
                        COMMUNITY STORIES
                    </div>
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-900 tracking-tight">What Our Users Say</h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Real feedback from authentic shoppers and store owners.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @forelse(($reviews ?? collect()) as $review)
                    <div class="glass-panel p-6 rounded-2xl flex flex-col justify-between shadow-sm hover:shadow-md transition-all border-l-2 border-amber-400/50">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                @if($review->user->profile_image ?? null)
                                    <img alt="{{ $review->user->name ?? 'User' }}"
                                         class="w-11 h-11 rounded-full object-cover ring-2 ring-amber-500/50"
                                         src="{{ \Illuminate\Support\Facades\Storage::url($review->user->profile_image) }}">
                                @else
                                    <div class="w-11 h-11 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ strtoupper(mb_substr($review->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-display font-bold text-sm text-slate-900">{{ $review->user->name ?? 'Verified Shopper' }}</h4>
                                    <span class="font-mono text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-semibold border border-emerald-200">
                                        Verified Buyer
                                    </span>
                                </div>
                            </div>
                            <div class="text-amber-500 text-xs mb-2 font-mono">
                                {{ str_repeat('★', intval($review->rating ?? 5)) }}
                            </div>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                "{{ $review->rating_comment ?? ($review->comment ?? ($review->body ?? 'Great product and smooth delivery!')) }}"
                            </p>
                        </div>
                    </div>
                @empty
                    <!-- Static Fallback Testimonials -->
                    <div class="glass-panel p-6 rounded-2xl flex flex-col justify-between shadow-sm hover:shadow-md transition-all border-l-2 border-amber-400/50">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-11 h-11 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-sm">P</div>
                                <div>
                                    <h4 class="font-display font-bold text-sm text-slate-900">Priya Sharma</h4>
                                    <span class="font-mono text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-semibold border border-emerald-200">Verified Buyer</span>
                                </div>
                            </div>
                            <div class="text-amber-500 text-xs mb-2">★★★★★</div>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                "Amazing experience! The AI recommendations are spot on. Found an authentic hand-carved tea set from an artisan in seconds."
                            </p>
                        </div>
                    </div>

                    <div class="glass-panel p-6 rounded-2xl flex flex-col justify-between shadow-sm hover:shadow-md transition-all border-l-2 border-amber-400/50">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-11 h-11 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-sm">R</div>
                                <div>
                                    <h4 class="font-display font-bold text-sm text-slate-900">Rahul Verma</h4>
                                    <span class="font-mono text-[10px] text-amber-800 bg-amber-50 px-2 py-0.5 rounded font-semibold border border-amber-200">Top Rated Seller</span>
                                </div>
                            </div>
                            <div class="text-amber-500 text-xs mb-2">★★★★★</div>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                "Bazaario made it effortless to start selling custom mechanical keyboards. Escrow payouts are instant and buyers are verified."
                            </p>
                        </div>
                    </div>

                    <div class="glass-panel p-6 rounded-2xl flex flex-col justify-between shadow-sm hover:shadow-md transition-all border-l-2 border-amber-400/50">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-11 h-11 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-sm">A</div>
                                <div>
                                    <h4 class="font-display font-bold text-sm text-slate-900">Ananya Das</h4>
                                    <span class="font-mono text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-semibold border border-emerald-200">Verified Collector</span>
                                </div>
                            </div>
                            <div class="text-amber-500 text-xs mb-2">★★★★★</div>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                "The live auctions floor with 0.2s latency is addictive! Won a pristine vintage 1954 Leica M3 Rangefinder at an unbelievable price."
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <!-- 10. FLOATING AI DOCKED BUTTON -->
    <div class="fixed bottom-6 right-6 z-40">
        <a href="{{ route('products.index') }}"
           class="bg-slate-900 text-white pl-4 pr-5 py-2.5 rounded-full font-semibold text-xs shadow-xl hover:bg-slate-800 transition-all flex items-center gap-2 ring-1 ring-white/20 active:scale-95 group">
            <span class="material-symbols-outlined text-amber-400 text-[19px] group-hover:rotate-12 transition-transform">auto_awesome</span>
            <span>✦ Ask Bazaario AI</span>
        </a>
    </div>

    <!-- 11. GLOBAL GLASS FOOTER -->
    <x-footer />

    <!-- Alpine.js script for interactive popovers/drawers -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
