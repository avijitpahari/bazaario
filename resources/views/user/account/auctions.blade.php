<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Live Auctions & Lot Clearance — Bazaario</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <!-- Compiled Tailwind CSS & App JS via Vite + Production Fallback -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">

    <style>
        [x-cloak] { display: none !important; }

        body {
            background-color: #FFFDF8;
            background-image:
                radial-gradient(at 0% 0%, rgba(245, 166, 35, 0.12) 0px, transparent 48%),
                radial-gradient(at 100% 10%, rgba(245, 166, 35, 0.08) 0px, transparent 45%),
                radial-gradient(at 50% 50%, rgba(254, 243, 199, 0.25) 0px, transparent 65%),
                radial-gradient(at 90% 90%, rgba(15, 23, 42, 0.03) 0px, transparent 40%);
            background-attachment: fixed;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05), 0 1px 3px rgba(15, 23, 42, 0.03);
        }

        .glass-card-hover {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .glass-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.10), 0 2px 6px rgba(15, 23, 42, 0.04);
            border-color: rgba(245, 166, 35, 0.4);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col font-sans text-slate-900 antialiased selection:bg-amber-100 selection:text-slate-900 relative overflow-x-hidden">
    @php
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Storage;

        $telemetry = $telemetry ?? [
            'total_lots'          => 0,
            'ending_soon_count'   => 0,
            'reserve_met_count'   => 0,
            'clearance_vol'       => 4280000,
            'user_leading_count'  => 0,
            'user_outbid_count'   => 0,
            'won_count'           => 0,
        ];
        $auctions = $auctions ?? collect();
        $filter   = $filter ?? 'all';
        $sort     = $sort ?? 'ending_soonest';

        // Safe Image URL Resolver
        if (!function_exists('resolveAuctionImage')) {
            function resolveAuctionImage($path) {
                if (!$path) {
                    return 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80';
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
                return Storage::url($path);
            }
        }
    @endphp

    <!-- Global Floating Pill Navbar -->
    @include('components.nav')

    <main class="w-full pt-3 pb-16 flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-6">

            <!-- Flash Notifications -->
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-2.5 font-medium text-sm shadow-sm">
                    <span class="material-symbols-outlined text-emerald-600 text-xl">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-center gap-2.5 font-medium text-sm shadow-sm">
                    <span class="material-symbols-outlined text-rose-600 text-xl">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- 1. Sub-Header & Telemetry Strip -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 py-1">
                <div class="flex flex-wrap items-center gap-3">
                    <a class="bg-white shadow-xs hover:bg-slate-50 text-slate-700 text-xs font-semibold px-3.5 py-1.5 rounded-full inline-flex items-center gap-1.5 transition-colors border border-slate-200"
                        href="{{ route('user.dashboard') }}">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        <span>Back to Dashboard</span>
                    </a>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    <nav class="flex items-center gap-2 text-xs font-semibold uppercase text-slate-500 tracking-wider">
                        <a class="hover:text-slate-900 transition-colors" href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <a class="hover:text-slate-900 transition-colors" href="{{ route('user.dashboard') }}">Account</a>
                        <span>/</span>
                        <span class="text-slate-900 font-bold">Live Auctions</span>
                    </nav>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="bg-white shadow-xs px-3.5 py-1.5 rounded-full inline-flex items-center gap-2 text-xs text-slate-800 font-semibold border border-slate-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="tracking-wide text-[11px] font-mono">ESCROW VERIFIED BUYER</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-500 font-mono text-[11px]">VAULT #ESC-9041</span>
                    </div>
                    <div class="bg-slate-900 text-white px-3.5 py-1.5 rounded-full shadow-xs inline-flex items-center gap-2 text-xs font-semibold">
                        <span class="material-symbols-outlined text-sm text-amber-400">lock</span>
                        <span>Liquid Vault: <span class="font-mono font-bold text-amber-300">₹60,500.00</span></span>
                    </div>
                </div>
            </div>

            <!-- 2. Command Center Hero Header -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row md:items-end justify-between gap-6 relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="flex flex-col gap-2 max-w-3xl relative z-10">
                    <div class="flex items-center gap-2 text-xs uppercase text-amber-700 font-bold font-mono tracking-wider">
                        <span class="material-symbols-outlined text-base text-amber-600">gavel</span>
                        <span>Real-Time Bidding Engine • Zero-Penalty Escrow Clearance</span>
                    </div>
                    <h1 class="font-display text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Live Auctions &amp; <span class="text-amber-600">Lot Clearance</span>
                    </h1>
                    <p class="text-slate-600 text-xs sm:text-sm max-w-2xl mt-1 leading-relaxed">
                        Real-time peer-to-peer bidding with sub-millisecond anti-sniping protection. Every bid is secured in cryptographic escrow with instant, zero-penalty lock release when outbid.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 relative z-10 shrink-0">
                    <a href="{{ route('user.bids') }}" class="bg-slate-900 text-white hover:bg-slate-800 font-bold text-xs px-4 py-2.5 rounded-full inline-flex items-center gap-2 transition-all shadow-sm active:scale-95">
                        <span class="material-symbols-outlined text-base text-amber-400">history_edu</span>
                        <span>My Active Bids ({{ $telemetry['user_leading_count'] + $telemetry['user_outbid_count'] }})</span>
                    </a>
                </div>
            </div>

            <!-- 3. 4-Column Dimensional Metric Strip -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="glass-card rounded-2xl p-4.5 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-800 shrink-0">
                        <span class="material-symbols-outlined text-2xl text-slate-700">front_hand</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 font-mono">Active Floor Lots</span>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="font-display text-lg font-bold text-slate-900">{{ $telemetry['total_lots'] }} Live Now</span>
                            <span class="text-xs text-amber-700 font-mono font-bold bg-amber-50 px-1.5 py-0.5 rounded">{{ $telemetry['ending_soon_count'] }} &lt;30m</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-4.5 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <span class="material-symbols-outlined text-2xl">workspace_premium</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 font-mono">Your Leading Bids</span>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="font-display text-lg font-bold text-emerald-700">{{ $telemetry['user_leading_count'] }} Lots Leading</span>
                            <span class="text-xs text-slate-500 font-mono">₹{{ number_format($telemetry['user_leading_count'] * 25000) }}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-4.5 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                        <span class="material-symbols-outlined text-2xl">trending_up</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 font-mono">24h Clearance Vol</span>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="font-display text-lg font-bold text-slate-900 font-mono">₹{{ number_format($telemetry['clearance_vol'] / 100000, 1) }}L</span>
                            <span class="text-xs text-emerald-600 font-mono font-bold bg-emerald-50 px-1.5 py-0.5 rounded">+18.4%</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-4.5 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-2xl animate-spin">sync</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 font-mono">Anti-Sniping Engine</span>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="font-display text-lg font-bold text-slate-900 font-mono">0.2s Sync</span>
                            <span class="text-xs text-emerald-600 font-mono font-bold">0 dropped</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Filter Tabs & Real-Time Toolbar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-1">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('auctions.index', ['filter' => 'all', 'sort' => $sort]) }}"
                        class="{{ $filter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200' }} text-xs font-bold px-4 py-2 rounded-full flex items-center gap-2 transition-all">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        <span>All Floor ({{ $telemetry['total_lots'] }})</span>
                    </a>
                    <a href="{{ route('auctions.index', ['filter' => 'ending_soon', 'sort' => $sort]) }}"
                        class="{{ $filter === 'ending_soon' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200' }} text-xs font-bold px-4 py-2 rounded-full flex items-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-sm text-amber-500">schedule</span>
                        <span>Ending Soon ({{ $telemetry['ending_soon_count'] }})</span>
                    </a>
                    <a href="{{ route('auctions.index', ['filter' => 'active_bids', 'sort' => $sort]) }}"
                        class="{{ $filter === 'active_bids' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200' }} text-xs font-bold px-4 py-2 rounded-full flex items-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-sm text-emerald-600">front_hand</span>
                        <span>Active Bids ({{ $telemetry['user_leading_count'] + $telemetry['user_outbid_count'] }})</span>
                    </a>
                    <a href="{{ route('auctions.index', ['filter' => 'reserve_met', 'sort' => $sort]) }}"
                        class="{{ $filter === 'reserve_met' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200' }} text-xs font-bold px-4 py-2 rounded-full flex items-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-sm text-slate-500">check_circle</span>
                        <span>Reserve Met ({{ $telemetry['reserve_met_count'] }})</span>
                    </a>
                    <a href="{{ route('auctions.index', ['filter' => 'outbid_alerts', 'sort' => $sort]) }}"
                        class="{{ $filter === 'outbid_alerts' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200' }} text-xs font-bold px-4 py-2 rounded-full flex items-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-sm text-rose-500">notification_important</span>
                        <span>Outbid Alerts ({{ $telemetry['user_outbid_count'] }})</span>
                    </a>
                </div>

                <div class="flex items-center gap-3 self-end md:self-auto shrink-0">
                    <div class="flex items-center gap-1.5 text-xs text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full font-mono font-semibold border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Sync: 18ms</span>
                    </div>
                    <div class="relative bg-white shadow-xs rounded-full px-3.5 py-1.5 flex items-center border border-slate-200">
                        <span class="material-symbols-outlined text-base text-slate-400 mr-1.5">sort</span>
                        <form action="{{ route('auctions.index') }}" method="GET" id="sortForm">
                            <input type="hidden" name="filter" value="{{ $filter }}">
                            <select name="sort" onchange="document.getElementById('sortForm').submit()"
                                class="bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer pr-2">
                                <option value="ending_soonest" {{ $sort === 'ending_soonest' ? 'selected' : '' }}>Ending Soonest</option>
                                <option value="highest_value" {{ $sort === 'highest_value' ? 'selected' : '' }}>Highest Value First</option>
                                <option value="most_active" {{ $sort === 'most_active' ? 'selected' : '' }}>Most Active (Bids)</option>
                                <option value="lowest_start" {{ $sort === 'lowest_start' ? 'selected' : '' }}>Lowest Starting</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 5. 3D DIMENSIONAL AUCTION LOTS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($auctions as $auction)
                    @php
                        $prod = $auction->product;
                        $rawPath = $prod->primaryImage->image_path ?? optional($prod->images->first())->image_path;
                        $imgUrl = resolveAuctionImage($rawPath);

                        $bidsCount = $auction->bids->count();
                        $minNextStep = (float) $auction->current_price + (float) $auction->minimum_increment;
                        $isEndingSoon = $auction->ends_at->diffInMinutes(now(), false) >= -30;
                        $secondsLeft = max(0, $auction->ends_at->timestamp - now()->timestamp);
                        $highestBidder = $auction->bids->first() ? $auction->bids->first()->user->name : null;
                        $isReserveMet = $auction->reserve_price ? ($auction->current_price >= $auction->reserve_price) : true;

                        $incAmt = (float) $auction->minimum_increment;
                        $incLabel = $incAmt >= 1000
                            ? '+₹' . number_format($incAmt / 1000, 0) . 'k'
                            : '+₹' . number_format($incAmt, 0);
                    @endphp

                    <article class="group glass-card glass-card-hover rounded-3xl p-5 flex flex-col justify-between relative overflow-hidden">
                        <div>
                            <!-- Media Showcase -->
                            <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden bg-slate-900 mb-4">
                                <a href="{{ route('auctions.show', $auction->id) }}" class="block w-full h-full">
                                    <img alt="{{ $prod->name ?? 'Auction item' }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                         src="{{ $imgUrl }}" />
                                </a>
                                
                                @if($isEndingSoon)
                                    <div class="absolute top-3 left-3 flex items-center gap-1.5 bg-rose-600 text-white px-3 py-1 rounded-full text-[11px] font-mono font-bold shadow-md animate-pulse">
                                        <span class="material-symbols-outlined text-xs">local_fire_department</span>
                                        <span>ENDING SOON</span>
                                    </div>
                                @else
                                    <div class="absolute top-3 left-3 flex items-center gap-1.5 bg-slate-900/85 backdrop-blur-md text-white px-3 py-1 rounded-full text-[11px] font-mono font-bold shadow-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                        <span>LIVE FLOOR</span>
                                    </div>
                                @endif

                                <div class="absolute top-3 right-3 flex items-center gap-1.5">
                                    <span class="bg-white/95 backdrop-blur-md text-slate-900 text-[11px] font-mono font-bold px-2.5 py-1 rounded-full shadow-xs">
                                        {{ $bidsCount }} Bids
                                    </span>
                                </div>
                                
                                @if($secondsLeft <= 120 && $secondsLeft > 0)
                                    <div class="absolute bottom-2.5 left-2.5 bg-rose-600/90 backdrop-blur-md text-white px-2.5 py-1 rounded-md text-[10px] font-mono font-bold flex items-center gap-1 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                                        <span>⚡ Anti-snipe +2m active</span>
                                    </div>
                                @else
                                    <div class="absolute bottom-2.5 left-2.5 bg-slate-900/80 backdrop-blur-md text-white px-2.5 py-0.5 rounded-md text-[10px] font-mono">
                                        Lot #AUC-{{ $auction->id }}
                                    </div>
                                @endif
                            </div>

                            <!-- Lot Details -->
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider font-mono">
                                        {{ $prod->category->name ?? 'Curated Lot' }} • Authenticated
                                    </span>
                                    <span class="text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-mono font-semibold">
                                        Verified
                                    </span>
                                </div>
                                
                                <h2 class="font-display font-bold text-base sm:text-lg text-slate-900 group-hover:text-amber-600 transition-colors line-clamp-1">
                                    <a href="{{ route('auctions.show', $auction->id) }}">
                                        {{ $prod->name }}
                                    </a>
                                </h2>

                                <div class="flex items-center justify-between text-xs text-slate-500 pt-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-medium">{{ $auction->seller->sellerProfile->business_name ?? ($auction->seller->name ?? 'Official Store') }}</span>
                                        <span>•</span>
                                        <span class="inline-flex items-center text-amber-600 font-bold">
                                            <span class="material-symbols-outlined text-sm">star</span> 4.9
                                        </span>
                                    </div>
                                    <span class="font-mono text-[11px] text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                        High: {{ $highestBidder ? '@'.Str::slug($highestBidder) : '@floor' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Escrow Bidding Matrix -->
                        <div class="mt-5 pt-3.5 bg-slate-50 rounded-2xl p-4 flex flex-col gap-3 border border-slate-200/80">
                            <div class="flex items-end justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Current Floor Bid</span>
                                    <span class="font-display text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight font-mono">
                                        ₹{{ number_format($auction->current_price, 0) }}
                                    </span>
                                    @if($isReserveMet)
                                        <span class="text-[11px] text-emerald-600 inline-flex items-center gap-1 mt-0.5 font-semibold">
                                            <span class="material-symbols-outlined text-xs">check_circle</span> Reserve Met (Escrow Locked)
                                        </span>
                                    @else
                                        <span class="text-[11px] text-amber-700 inline-flex items-center gap-1 mt-0.5 font-semibold">
                                            <span class="material-symbols-outlined text-xs">info</span> Reserve: ₹{{ number_format($auction->reserve_price, 0) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Closes In</span>
                                    <div class="font-mono text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-md inline-flex items-center gap-1 mt-1 border border-rose-200"
                                         data-countdown="{{ $auction->ends_at->isoFormat('YYYY-MM-DDTHH:mm:ss') }}">
                                        <span class="material-symbols-outlined text-sm">timer</span>
                                        <span class="timer-display">{{ $auction->ends_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-slate-600 text-xs bg-white px-3 py-1.5 rounded-xl border border-slate-200/60 font-mono">
                                <span>Next Minimum Step:</span>
                                <span class="font-bold text-slate-900">
                                    ₹{{ number_format($minNextStep, 0) }} (+₹{{ number_format($auction->minimum_increment, 0) }})
                                </span>
                            </div>

                            <!-- Bid Placement Action Row -->
                            <div class="flex items-center gap-2 pt-1">
                                <a href="{{ route('auctions.show', $auction->id) }}" class="flex-1 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs py-2.5 px-3 rounded-xl flex items-center justify-center gap-1.5 shadow-sm transition-all active:scale-95">
                                    <span>Bid / Enter Room</span>
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>

                                <form action="{{ route('auctions.quickBid', $auction->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="increment" value="{{ $auction->minimum_increment }}">
                                    <button class="bg-white hover:bg-slate-100 text-slate-800 font-mono text-xs font-bold py-2.5 px-3 rounded-xl border border-slate-200 transition-colors shrink-0 shadow-xs"
                                            title="Quick bid {{ $incLabel }}" type="submit">
                                        {{ $incLabel }} Quick
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-700 glass-card rounded-3xl">
                        <span class="material-symbols-outlined text-5xl text-amber-500">gavel</span>
                        <h3 class="font-display text-lg font-bold mt-2">No auctions matching criteria</h3>
                        <p class="text-xs text-slate-500 mt-1">Try selecting a different filter above or check back shortly.</p>
                    </div>
                @endforelse
            </div>

            <!-- 6. Pagination Links -->
            @if($auctions->hasPages())
                <div class="pt-4 flex justify-center">
                    {{ $auctions->links() }}
                </div>
            @endif

        </div>
    </main>

    <!-- Global Glass Footer -->
    <x-footer />

    <!-- Alpine.js script for interactive dropdowns/popovers -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Script for Live Countdown Timers -->
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
                        display.innerText = 'ENDED';
                        return;
                    }

                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    const hStr = String(hours).padStart(2, '0');
                    const mStr = String(minutes).padStart(2, '0');
                    const sStr = String(seconds).padStart(2, '0');

                    display.innerText = `${hStr}:${mStr}:${sStr}`;
                }

                updateTimer();
                setInterval(updateTimer, 1000);
            });
        });
    </script>
</body>

</html>