<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Lot #AUC-{{ $auction->id }} • {{ $auction->product->name ?? 'Live Auction' }} — Bazaario</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-heading: 'Outfit', 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        [x-cloak] { display: none !important; }

        body {
            background-color: #F8FAFC;
            background-image:
                radial-gradient(at 0% 0%, rgba(245, 166, 35, 0.08) 0px, transparent 40%),
                radial-gradient(at 100% 0%, rgba(14, 165, 233, 0.05) 0px, transparent 40%),
                radial-gradient(at 50% 100%, rgba(245, 166, 35, 0.04) 0px, transparent 50%);
            background-attachment: fixed;
            font-family: var(--font-sans);
        }

        h1, h2, h3, h4, .font-heading {
            font-family: var(--font-heading) !important;
            letter-spacing: -0.025em;
        }

        .font-mono {
            font-family: var(--font-mono) !important;
            font-variant-numeric: tabular-nums;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900 antialiased selection:bg-amber-100 selection:text-amber-950 min-h-screen flex flex-col font-sans">
    @php
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Storage;

        $prod = $auction->product;
        $images = $prod->images;
        $primaryImgPath = $prod->primaryImage->image_path ?? optional($images->first())->image_path;

        if (!function_exists('resolveShowImage')) {
            function resolveShowImage($path) {
                if (!$path) {
                    return 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1000&auto=format&fit=crop&q=80';
                }
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }
                $clean = ltrim($path, '/');
                if (file_exists(public_path($clean))) {
                    return asset($clean);
                }
                if (file_exists(public_path('images/' . $clean))) {
                    return asset('images/' . $clean);
                }
                if (file_exists(public_path('storage/' . $clean))) {
                    return asset('storage/' . $clean);
                }
                return Storage::url($path);
            }
        }

        // Safe Seller Logo Resolver
        $sellerLogoUrl = null;
        $rawSellerLogo = $auction->seller?->logo_path ?? $auction->seller?->user?->profile_image ?? null;
        if (!empty($rawSellerLogo)) {
            if (str_starts_with($rawSellerLogo, 'http://') || str_starts_with($rawSellerLogo, 'https://')) {
                $sellerLogoUrl = $rawSellerLogo;
            } elseif (file_exists(public_path('storage/' . ltrim($rawSellerLogo, '/')))) {
                $sellerLogoUrl = asset('storage/' . ltrim($rawSellerLogo, '/'));
            } elseif (file_exists(public_path(ltrim($rawSellerLogo, '/')))) {
                $sellerLogoUrl = asset(ltrim($rawSellerLogo, '/'));
            } else {
                $sellerLogoUrl = Storage::url($rawSellerLogo);
            }
        }

        $primaryImgUrl = resolveShowImage($primaryImgPath);
        $highestBidderName = $highestBid ? $highestBid->user->name : null;
        $bidsCount = $auction->bids->count();
        $incAmt = (float) $auction->minimum_increment;

        $isUpcoming = $auction->status === 'scheduled';
        $isEnded = $auction->status === 'ended' || ($auction->ends_at && $auction->ends_at->isPast() && $auction->status !== 'scheduled');
        $isWinner = Auth::check() && ($auction->winner_id === Auth::id());
    @endphp

    <!-- Global Floating Pill Navbar -->
    @include('components.nav')

    <main class="w-full pt-4 pb-16 flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col gap-6">

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-2.5 font-medium text-sm shadow-xs">
                    <span class="material-symbols-outlined text-emerald-600 text-xl">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-center gap-2.5 font-medium text-sm shadow-xs">
                    <span class="material-symbols-outlined text-rose-600 text-xl">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Navigation Breadcrumb & Live Escrow Badge Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-1">
                <div class="flex flex-wrap items-center gap-3">
                    <a class="bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-3.5 py-1.5 rounded-xl inline-flex items-center gap-1.5 transition-colors border border-slate-200/80 shadow-2xs"
                        href="{{ route('auctions.index') }}">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        <span>Back to Live Floor</span>
                    </a>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    <nav class="flex items-center gap-1.5 text-xs font-mono uppercase text-slate-400 tracking-wider">
                        <a class="hover:text-slate-800 transition-colors" href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <a class="hover:text-slate-800 transition-colors" href="{{ route('auctions.index') }}">Auctions</a>
                        <span>/</span>
                        <span class="text-slate-900 font-bold">Lot #AUC-{{ $auction->id }}</span>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-mono text-[11px] font-bold border border-emerald-200/70 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>CRYPTOGRAPHIC ESCROW LOCKED • 0.2s SYNC</span>
                    </div>
                </div>
            </div>

            <!-- Main Live Lot Bidding Room Split Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- ── LEFT COLUMN: Lot Media Gallery & Specifications (7 cols) ── -->
                <div class="lg:col-span-7 flex flex-col gap-6">

                    <!-- High-Res Media Showcase Card -->
                    <div class="bg-white rounded-3xl p-4 sm:p-5 shadow-xs border border-slate-200/80 overflow-hidden relative">
                        <!-- Main Preview Image: Clean aspect container with crisp containment -->
                        <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden bg-slate-950 border border-slate-200/60 flex items-center justify-center">
                            <img id="main-lot-image"
                                 src="{{ $primaryImgUrl }}"
                                 alt="{{ $prod->name }}"
                                 class="w-full h-full object-contain p-2 transition-all duration-300"
                                 onerror="this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1000&auto=format&fit=crop&q=80'">

                            <!-- Live Room Overlay Badge -->
                            <div class="absolute top-3.5 left-3.5 flex items-center gap-2">
                                @if($isUpcoming)
                                    <span class="px-3 py-1 rounded-xl bg-sky-600 text-white font-mono text-[11px] font-bold tracking-wide uppercase flex items-center gap-1.5 shadow-md">
                                        <span class="material-symbols-outlined text-[13px]">event</span>
                                        UPCOMING AUCTION
                                    </span>
                                @elseif($isEnded)
                                    <span class="px-3 py-1 rounded-xl bg-slate-900 text-slate-200 font-mono text-[11px] font-bold tracking-wide uppercase flex items-center gap-1.5 shadow-md">
                                        <span class="material-symbols-outlined text-[13px]">gavel</span>
                                        AUCTION CLOSED
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-xl bg-rose-600 text-white font-mono text-[11px] font-bold tracking-wide uppercase flex items-center gap-1.5 shadow-md animate-pulse">
                                        <span class="w-2 h-2 rounded-full bg-white"></span>
                                        LIVE FLOOR LOT
                                    </span>
                                @endif

                                @if($isReserveMet && !$isUpcoming)
                                    <span class="px-3 py-1 rounded-xl bg-emerald-600 text-white font-mono text-[11px] font-bold tracking-wide uppercase flex items-center gap-1 shadow-xs">
                                        <span class="material-symbols-outlined text-[13px]">verified</span>
                                        RESERVE MET
                                    </span>
                                @endif
                            </div>

                            <div class="absolute bottom-3.5 left-3.5 right-3.5 p-3 rounded-xl bg-slate-950/85 backdrop-blur-md text-white flex items-center justify-between text-xs border border-white/10">
                                <span class="font-mono text-[11px] text-slate-300">LOT ID: #AUC-{{ $auction->id }}</span>
                                <span class="font-mono text-[11px] text-amber-400 font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">history</span>
                                    {{ $bidsCount }} bids placed
                                </span>
                            </div>
                        </div>

                        <!-- Multi-Angle Thumbnail Row -->
                        @if($images && $images->count() > 1)
                            <div class="flex items-center gap-3 mt-4 overflow-x-auto pb-1">
                                @foreach($images as $img)
                                    @php
                                        $tUrl = resolveShowImage($img->image_path);
                                    @endphp
                                    <button type="button" 
                                            onclick="document.getElementById('main-lot-image').src='{{ $tUrl }}'"
                                            class="w-16 h-16 rounded-xl overflow-hidden border-2 border-slate-200 hover:border-amber-500 transition-all shrink-0 focus:outline-none focus:border-amber-500 bg-slate-900 p-0.5 cursor-pointer">
                                        <img src="{{ $tUrl }}" alt="Thumb" class="w-full h-full object-contain" onerror="this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80'">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Lot Specifications & Authenticity Seal -->
                    <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 flex flex-col gap-5">
                        <div class="flex items-start justify-between border-b border-slate-100 pb-4 gap-4">
                            <div>
                                <span class="font-mono text-[11px] text-amber-700 font-bold uppercase tracking-wider">
                                    {{ $prod->category->name ?? 'Curated Specialty' }}
                                </span>
                                <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                                    {{ $prod->name }}
                                </h1>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-600 shrink-0">
                                <span class="material-symbols-outlined text-[26px]">verified</span>
                            </div>
                        </div>

                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ $prod->description ?? $prod->short_description ?? 'This verified lot is in prime physical condition, inspected by authorized curators, and certified authentic for peer-to-peer clearance.' }}
                        </p>

                        <!-- Dimensional Key Metrics Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                                <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Starting Floor</span>
                                <span class="font-mono font-bold text-slate-900 text-base mt-1 block">
                                    ₹{{ number_format($auction->starting_price, 0) }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                                <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Min Increment</span>
                                <span class="font-mono font-bold text-slate-900 text-base mt-1 block">
                                    ₹{{ number_format($auction->minimum_increment, 0) }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                                <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Authenticity</span>
                                <span class="font-bold text-emerald-700 text-sm mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-emerald-600">shield</span> 100% Certified
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                                <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Settlement</span>
                                <span class="font-mono font-bold text-slate-900 text-sm mt-1 block">
                                    Instant Escrow
                                </span>
                            </div>
                        </div>

                        <!-- Seller Verification Card -->
                        <div class="p-4 rounded-2xl bg-slate-900 text-white flex items-center justify-between gap-4 mt-2 border border-slate-800">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-gradient-to-tr from-amber-500 to-amber-600 text-slate-950 flex items-center justify-center font-bold font-heading text-lg shrink-0 ring-2 ring-white/10 shadow-xs">
                                    @if($sellerLogoUrl)
                                        <img src="{{ $sellerLogoUrl }}" 
                                             alt="{{ $auction->seller->shop_name ?? ($auction->seller->name ?? 'Shop Logo') }}" 
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                                        <span class="hidden">{{ strtoupper(substr($auction->seller->shop_name ?? ($auction->seller->name ?? 'S'), 0, 1)) }}</span>
                                    @else
                                        <span>{{ strtoupper(substr($auction->seller->shop_name ?? ($auction->seller->name ?? 'S'), 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold font-heading text-sm text-white truncate">{{ $auction->seller->shop_name ?? ($auction->seller->user->name ?? ($auction->seller->name ?? 'Verified Seller')) }}</span>
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400 text-[10px] font-mono font-bold border border-emerald-500/30">VERIFIED SELLER</span>
                                    </div>
                                    <span class="text-xs text-slate-400 flex items-center gap-1 mt-0.5 font-mono">
                                        <span class="material-symbols-outlined text-[13px] text-amber-400">star</span>
                                        4.9 Rating • 100% Dispatched on time
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('shop') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-heading text-xs font-semibold transition-colors shrink-0">
                                View Store
                            </a>
                        </div>
                    </div>

                </div>

                <!-- ── RIGHT COLUMN: Sticky Real-Time Bidding Terminal (5 cols) ── -->
                <div class="lg:col-span-5 flex flex-col gap-6 sticky top-24">

                    <!-- Live Bidding Console Card -->
                    <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200/90 relative overflow-hidden flex flex-col gap-5">
                        
                        <!-- Top HUD Bar: Countdown & Anti-Snipe -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                                    {{ $isUpcoming ? 'Auction Starts In' : ($isEnded ? 'Auction Status' : 'Auction Closes In') }}
                                </span>
                                @if($isUpcoming)
                                    <div class="font-mono text-2xl font-extrabold text-sky-700 flex items-center gap-1.5 mt-0.5"
                                         data-countdown="{{ $auction->starts_at?->toIso8601String() }}">
                                        <span class="material-symbols-outlined text-[22px]">event_upcoming</span>
                                        <span class="timer-display">{{ $auction->starts_at ? $auction->starts_at->diffForHumans() : 'Scheduled' }}</span>
                                    </div>
                                @elseif($isEnded)
                                    <div class="font-mono text-xl font-extrabold text-slate-800 flex items-center gap-1.5 mt-0.5">
                                        <span class="material-symbols-outlined text-[22px] text-slate-500">flag</span>
                                        <span>Auction Closed</span>
                                    </div>
                                @else
                                    <div class="font-mono text-2xl font-extrabold text-rose-600 flex items-center gap-1.5 mt-0.5 animate-pulse"
                                         data-countdown="{{ $auction->ends_at->toIso8601String() }}">
                                        <span class="material-symbols-outlined text-[22px]">timer</span>
                                        <span class="timer-display">{{ $auction->ends_at->diffForHumans() }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Anti-Snipe Buffer</span>
                                <span class="font-mono text-[11px] font-bold text-amber-800 bg-amber-50 border border-amber-200/60 px-2.5 py-1 rounded-lg inline-block mt-0.5">
                                    +2m Auto-Extension
                                </span>
                            </div>
                        </div>

                        <!-- Current Floor Price Matrix -->
                        <div class="bg-slate-50/80 p-4.5 rounded-2xl flex flex-col gap-2.5 border border-slate-200/70">
                            <div class="flex items-end justify-between">
                                <div>
                                    <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                        {{ $isUpcoming ? 'Starting Floor' : ($isEnded ? 'Final Winning Bid' : 'Current Leading Bid') }}
                                    </span>
                                    <div class="font-mono text-3xl font-extrabold text-slate-900 leading-tight mt-0.5">
                                        ₹{{ number_format($auction->current_price ?: $auction->starting_price, 0) }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                                        {{ $isEnded ? 'Winner:' : 'Leader:' }}
                                    </span>
                                    <span class="font-mono text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-md inline-block mt-0.5">
                                        {{ $highestBidderName ? '@'.Str::slug($highestBidderName) : '@floor' }}
                                    </span>
                                </div>
                            </div>

                            @if($isWinner)
                                <div class="mt-2 p-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs flex items-center justify-between gap-2 shadow-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[20px]">emoji_events</span>
                                        <span>Congratulations! You won this auction lot.</span>
                                    </div>
                                    <a href="{{ route('user.orders.index') }}" class="px-3 py-1 bg-slate-950 text-white rounded-lg text-xs font-mono hover:bg-slate-800 transition-colors">
                                        View Order
                                    </a>
                                </div>
                            @elseif($isEnded)
                                <div class="mt-2 p-3 rounded-xl bg-slate-100 text-slate-700 text-xs font-medium flex items-center gap-2 border border-slate-200">
                                    <span class="material-symbols-outlined text-[18px] text-slate-500">lock</span>
                                    <span>This auction lot has closed. All non-winning escrow deposits have been returned.</span>
                                </div>
                            @elseif($isUserLeading)
                                <div class="mt-2 p-3 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-200/80 text-xs font-bold flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-emerald-600">verified</span>
                                    <span>You are currently the highest bidder for this lot!</span>
                                </div>
                            @elseif($isUserOutbid)
                                <div class="mt-2 p-3 rounded-xl bg-rose-50 text-rose-900 border border-rose-200/80 text-xs font-bold flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-rose-600">warning</span>
                                    <span>You have been outbid! Raise your collateral to reclaim the lead.</span>
                                </div>
                            @endif
                        </div>

                        @if(!$isEnded && !$isUpcoming)
                            <!-- Quick Increment Preset Buttons -->
                            <div>
                                <label class="font-mono text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">
                                    1-Click Quick Bidding Presets
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <form action="{{ route('auctions.quickBid', $auction->id) }}" method="POST" x-data="{ qb: false }" @submit="qb = true">
                                        @csrf
                                        <input type="hidden" name="increment" value="{{ $auction->minimum_increment }}">
                                        <button :disabled="qb" class="w-full py-2.5 px-2 rounded-xl bg-slate-50 hover:bg-amber-50 hover:border-amber-300 text-slate-800 font-mono font-bold text-xs border border-slate-200/80 transition-all flex items-center justify-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-2xs" type="submit">
                                            <span x-show="!qb" class="material-symbols-outlined text-[15px] text-amber-600">bolt</span>
                                            <span x-show="qb" x-cloak><x-loading-spinner size="12px" class="text-amber-600" /></span>
                                            <span>+₹{{ number_format($auction->minimum_increment >= 1000 ? $auction->minimum_increment/1000 : $auction->minimum_increment, 0) }}{{ $auction->minimum_increment >= 1000 ? 'k' : '' }}</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('auctions.quickBid', $auction->id) }}" method="POST" x-data="{ qb: false }" @submit="qb = true">
                                        @csrf
                                        <input type="hidden" name="increment" value="{{ $auction->minimum_increment * 2 }}">
                                        <button :disabled="qb" class="w-full py-2.5 px-2 rounded-xl bg-slate-50 hover:bg-amber-50 hover:border-amber-300 text-slate-800 font-mono font-bold text-xs border border-slate-200/80 transition-all flex items-center justify-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-2xs" type="submit">
                                            <span x-show="!qb" class="material-symbols-outlined text-[15px] text-amber-600">bolt</span>
                                            <span x-show="qb" x-cloak><x-loading-spinner size="12px" class="text-amber-600" /></span>
                                            <span>+₹{{ number_format(($auction->minimum_increment * 2) >= 1000 ? ($auction->minimum_increment * 2)/1000 : ($auction->minimum_increment * 2), 0) }}{{ ($auction->minimum_increment * 2) >= 1000 ? 'k' : '' }}</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('auctions.quickBid', $auction->id) }}" method="POST" x-data="{ qb: false }" @submit="qb = true">
                                        @csrf
                                        <input type="hidden" name="increment" value="{{ $auction->minimum_increment * 5 }}">
                                        <button :disabled="qb" class="w-full py-2.5 px-2 rounded-xl bg-slate-50 hover:bg-amber-50 hover:border-amber-300 text-slate-800 font-mono font-bold text-xs border border-slate-200/80 transition-all flex items-center justify-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-2xs" type="submit">
                                            <span x-show="!qb" class="material-symbols-outlined text-[15px] text-amber-600">bolt</span>
                                            <span x-show="qb" x-cloak><x-loading-spinner size="12px" class="text-amber-600" /></span>
                                            <span>+₹{{ number_format(($auction->minimum_increment * 5) >= 1000 ? ($auction->minimum_increment * 5)/1000 : ($auction->minimum_increment * 5), 0) }}{{ ($auction->minimum_increment * 5) >= 1000 ? 'k' : '' }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Custom Bid Form Console -->
                            <form action="{{ route('auctions.placeBid', $auction->id) }}" method="POST" class="flex flex-col gap-3"
                                  x-data="{ placingBid: false }"
                                  @submit="if(placingBid){ $event.preventDefault(); return; } placingBid = true;">
                                @csrf
                                <div>
                                    <label class="font-mono text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">
                                        Custom Bid Amount (Min: ₹{{ number_format($minNextBid, 0) }})
                                    </label>
                                    <div class="relative flex items-center">
                                        <span class="absolute left-4 font-mono font-bold text-slate-500 text-base">₹</span>
                                        <input type="number" 
                                               name="amount" 
                                               value="{{ $minNextBid }}" 
                                               min="{{ $minNextBid }}" 
                                               step="1"
                                               class="w-full pl-9 pr-4 py-3 rounded-xl bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 font-mono font-bold text-lg text-slate-900 outline-none transition-all shadow-inner"
                                               required>
                                    </div>
                                </div>

                                <button type="submit" :disabled="placingBid"
                                        class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading font-bold text-sm shadow-md transition-all active:scale-[0.99] flex items-center justify-center gap-2 mt-1 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                                    <template x-if="!placingBid">
                                        <span class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[20px]">gavel</span>
                                            <span>Place Bid &amp; Secure Escrow</span>
                                        </span>
                                    </template>
                                    <template x-if="placingBid">
                                        <span class="flex items-center gap-2">
                                            <x-loading-spinner size="18px" />
                                            <span>Securing Escrow &amp; Placing Bid...</span>
                                        </span>
                                    </template>
                                </button>
                            </form>
                        @elseif($isUpcoming)
                            <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 text-xs font-medium flex flex-col gap-2">
                                <div class="flex items-center gap-2 font-bold text-sm font-heading">
                                    <span class="material-symbols-outlined text-sky-600">notifications_active</span>
                                    <span>Bidding Opens Soon</span>
                                </div>
                                <p class="text-sky-800 leading-relaxed text-xs">
                                    This lot is in pre-auction inspection. Review the catalog specifications and ensure your escrow vault is funded before starts at {{ $auction->starts_at ? $auction->starts_at->format('M d, Y h:i A') : 'scheduled time' }}.
                                </p>
                            </div>
                        @else
                            <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-medium flex flex-col gap-1.5 text-center">
                                <span class="font-bold text-sm text-slate-900 font-heading">Bidding is Closed</span>
                                <p class="text-slate-500">This auction has terminated. Winning bids are moving into fulfillment.</p>
                            </div>
                        @endif

                        <div class="p-3 rounded-xl bg-slate-50 text-slate-600 font-mono text-[11px] flex items-center gap-2 border border-slate-200/60">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">lock</span>
                            <span>Collateral locked securely in vault. 100% instant refund if outbid.</span>
                        </div>
                    </div>

                    <!-- Live Bids Ledger Activity Stream -->
                    <div class="bg-white rounded-3xl p-5 shadow-xs border border-slate-200/80 flex flex-col gap-3.5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                <h3 class="font-heading text-sm font-bold text-slate-900">Live Bid Stream</h3>
                            </div>
                            <span class="font-mono text-[11px] text-slate-500">{{ $bidsCount }} Total Bids</span>
                        </div>

                        <div class="flex flex-col divide-y divide-slate-100 max-h-64 overflow-y-auto pr-1">
                            @forelse($auction->bids as $index => $b)
                                <div class="py-2.5 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg {{ $index === 0 ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700 font-semibold' }} flex items-center justify-center font-mono text-[11px]">
                                            #{{ $bidsCount - $index }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-800 block">
                                                Bidder #{{ substr(md5($b->user_id), 0, 4) }}
                                                @if($index === 0)
                                                    <span class="text-[10px] text-emerald-700 font-mono font-bold ml-1">(Leading)</span>
                                                @endif
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $b->created_at ? $b->created_at->diffForHumans() : 'Just now' }}</span>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold {{ $index === 0 ? 'text-emerald-700 text-sm' : 'text-slate-900' }}">
                                        ₹{{ number_format($b->amount, 0) }}
                                    </span>
                                </div>
                            @empty
                                <div class="py-6 text-center text-slate-400 text-xs">
                                    No bids placed yet. Be the first to bid at floor price!
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

            <!-- Related Live Lots -->
            @if($relatedAuctions && $relatedAuctions->isNotEmpty())
                <div class="mt-8 flex flex-col gap-5 pt-8 border-t border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-mono text-[11px] text-amber-700 font-bold uppercase tracking-wider">Other Live Clearances</span>
                            <h2 class="font-heading text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Active Floor Lots</h2>
                        </div>
                        <a href="{{ route('auctions.index') }}" class="font-heading text-xs font-bold text-slate-700 hover:text-amber-600 transition-colors flex items-center gap-1">
                            <span>View All ({{ $relatedAuctions->count() }})</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach($relatedAuctions as $rel)
                            @php
                                $rProd = $rel->product;
                                $rImg = $rProd->primaryImage->image_path ?? optional($rProd->images->first())->image_path;
                                $rImgUrl = resolveShowImage($rImg);
                            @endphp
                            <a href="{{ route('auctions.show', $rel->id) }}" class="group bg-white rounded-2xl p-3.5 shadow-xs hover:shadow-md transition-all border border-slate-200/80 flex flex-col justify-between">
                                <div>
                                    <div class="aspect-square rounded-xl overflow-hidden bg-slate-900 mb-3 relative flex items-center justify-center">
                                        <img src="{{ $rImgUrl }}" 
                                             alt="{{ $rProd->name }}" 
                                             class="w-full h-full object-contain p-1 group-hover:scale-105 transition-transform duration-300"
                                             onerror="this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80'">
                                        <span class="absolute bottom-2 left-2 bg-slate-950/80 backdrop-blur text-white font-mono text-[10px] px-2 py-0.5 rounded">
                                            {{ $rel->bids->count() }} Bids
                                        </span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-xs font-heading group-hover:text-amber-600 transition-colors line-clamp-1">
                                        {{ $rProd->name }}
                                    </h4>
                                </div>
                                <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                                    <span class="font-mono font-bold text-slate-900 text-xs">₹{{ number_format($rel->current_price, 0) }}</span>
                                    <span class="text-xs font-bold text-amber-600 font-mono flex items-center gap-0.5">Bid →</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </main>

    <!-- Global Glass Footer -->
    <x-footer />

    <!-- Live Countdown Timer Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const timers = document.querySelectorAll('[data-countdown]');
            timers.forEach(timer => {
                const endTimeStr = timer.getAttribute('data-countdown');
                const endTime = new Date(endTimeStr).getTime();
                const display = timer.querySelector('.timer-display');

                function updateTimer() {
                    const now = new Date().getTime();
                    const distance = endTime - now;

                    if (distance <= 0) {
                        display.innerText = 'Auction Ended';
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    const hStr = String(hours).padStart(2, '0');
                    const mStr = String(minutes).padStart(2, '0');
                    const sStr = String(seconds).padStart(2, '0');

                    if (days > 0) {
                        display.innerText = `${days}d ${hStr}:${mStr}:${sStr}`;
                    } else {
                        display.innerText = `${hStr}:${mStr}:${sStr}`;
                    }
                }

                updateTimer();
                setInterval(updateTimer, 1000);
            });
        });
    </script>
</body>

</html>
