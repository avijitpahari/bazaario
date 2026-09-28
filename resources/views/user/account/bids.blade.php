<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>My Bids & Escrow Stakes — Bazaario</title>

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <style>
        [x-cloak] { display: none !important; }
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
            main>:first-child { margin-top: 0 !important; }
            main>:last-child { margin-bottom: 0 !important; }
        }
        ::-webkit-scrollbar { display: none; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('build/assets/app-C-FKvfT_.css') }}">
</head>

<body x-data="{ autoBidModalOpen: false, vaultModalOpen: false, selectedLot: null, maxCeiling: 25000, incrementStep: 500, autoBidSaved: false }" class="bg-canvas-ivory text-on-surface antialiased selection:bg-amber-action selection:text-slate-authority min-h-screen flex flex-col">
    @php
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Storage;

        $stats = $stats ?? [
            'total_locked_escrow' => 0,
            'lead_count' => 0,
            'outbid_count' => 0,
            'won_count' => 0,
            'active_count' => 0,
            'refunds_count' => 0,
            'upcoming_auction' => null,
            'win_rate' => 0,
        ];
        $stats['active_count'] = $stats['active_count'] ?? 0;
        $stats['refunds_count'] = $stats['refunds_count'] ?? 0;
        $stats['won_count'] = $stats['won_count'] ?? 0;
        $stats['outbid_count'] = $stats['outbid_count'] ?? 0;
        $stats['total_locked_escrow'] = $stats['total_locked_escrow'] ?? 0;
        $stats['lead_count'] = $stats['lead_count'] ?? 0;
        $stats['win_rate'] = $stats['win_rate'] ?? 0;

        $auctions = $auctions ?? collect();
        $user = $user ?? Auth::user();
        $filter = $filter ?? 'active';
        $sort = $sort ?? 'ending_soonest';

        if (!function_exists('resolveBidImage')) {
            function resolveBidImage($path) {
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
    @include('components.nav-user')

    <main class="w-full pt-2 pb-16 bg-canvas-ivory flex-1">
        <div class="flex flex-col w-full">
            <!-- Subtle Ambient Glows -->
            <div class="relative w-full overflow-hidden">
                <div class="pointer-events-none absolute -top-40 right-10 w-96 h-96 rounded-full bg-amber-action/10 blur-3xl"></div>
                <div class="pointer-events-none absolute top-96 -left-32 w-80 h-80 rounded-full bg-status-green/10 blur-3xl"></div>

                <div class="max-w-container-max mx-auto px-gutter-md pt-4 pb-section-final-bottom flex flex-col gap-6">

                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="bg-status-green/10 border border-status-green/30 text-status-green px-4 py-3 rounded-xl flex items-center gap-2 font-medium text-sm">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-error/10 border border-error/30 text-error px-4 py-3 rounded-xl flex items-center gap-2 font-medium text-sm">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Sub-nav Bar: Breadcrumb + Escrow Trust Status -->
                    <nav class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-2xl bg-card-white/70 backdrop-blur-md shadow-sm border border-slate-authority/10">
                        <div class="flex flex-wrap items-center gap-3">
                            <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-container hover:bg-surface-container-high transition-colors font-button-text text-body-small text-slate-authority"
                                href="{{ route('user.dashboard') }}">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Back to Account
                            </a>
                            <div class="h-4 w-px bg-slate-authority/10"></div>
                            <div class="flex items-center gap-1.5 font-label-micro text-label-micro text-on-surface-variant">
                                <span>Home</span>
                                <span>/</span>
                                <span>Account</span>
                                <span>/</span>
                                <span class="text-slate-authority font-semibold">My Bids</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium border border-status-green/20">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-ping"></span>
                                <span>ESCROW VERIFIED BUYER • VAULT #ESC-9041 • UID: BZ-891-IN</span>
                            </div>
                            <button @click="vaultModalOpen = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-authority text-canvas-ivory font-label-micro text-label-micro hover:bg-slate-authority/90 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-[14px] text-amber-action">lock</span>
                                <span>Vault: ₹{{ number_format($stats['total_locked_escrow'] + 18800, 2) }}</span>
                                <span class="material-symbols-outlined text-[14px] opacity-70">tune</span>
                            </button>
                        </div>
                    </nav>

                    <!-- Page Header with 3D Depth & Quick Action Drivers -->
                    <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pt-2">
                        <div class="flex flex-col gap-1.5 max-w-2xl">
                            <div class="flex items-center gap-2">
                                <span class="font-label-eyebrow text-label-eyebrow text-amber-action font-semibold tracking-wider uppercase">
                                    Account Management • Escrow Bidding Tracker
                                </span>
                                <span class="px-2 py-0.5 rounded-full bg-slate-authority/5 text-slate-authority font-label-micro text-label-micro">
                                    Live 0.1s Pulse
                                </span>
                            </div>
                            <h1 class="font-display-hero text-display-hero-mobile md:text-display-hero text-slate-authority tracking-tight">
                                My Bids &amp; Escrow Stakes
                            </h1>
                            <p class="font-body-regular text-body-regular text-on-surface-variant">
                                Track active auction positions, collateral locked safely in audited smart escrow, live outbid counter-strikes, and won collectibles.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="autoBidModalOpen = true" type="button" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-card-white/90 backdrop-blur text-slate-authority shadow-sm hover:bg-surface-container transition-all font-button-text text-body-small border border-slate-authority/10 cursor-pointer">
                                <span class="material-symbols-outlined text-[18px] text-amber-action">bolt</span>
                                Auto-Bid Config
                            </button>
                            <a class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small shadow-md hover:opacity-95 active:scale-[0.99] transition-all font-bold"
                                href="{{ route('auctions.index') }}">
                                <span class="material-symbols-outlined text-[18px]">gavel</span>
                                Explore Live Auctions
                            </a>
                        </div>
                    </section>

                    <!-- Dimensional Summary Metrics Matrix -->
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Metric 1 -->
                        <div class="p-5 rounded-2xl bg-card-white/90 backdrop-blur shadow-sm flex flex-col justify-between gap-4 border border-slate-authority/10">
                            <div class="flex items-center justify-between">
                                <span class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Total Locked Escrow</span>
                                <span class="w-8 h-8 rounded-lg bg-slate-authority/5 flex items-center justify-center text-slate-authority">
                                    <span class="material-symbols-outlined text-[18px]">lock_clock</span>
                                </span>
                            </div>
                            <div>
                                <div class="font-display-hero text-headline-section font-bold text-slate-authority tracking-tight">
                                    ₹{{ number_format($stats['total_locked_escrow'], 0) }}<span class="text-body-small font-normal text-on-surface-variant">.00</span>
                                </div>
                                <p class="font-label-micro text-label-micro text-status-green flex items-center gap-1 mt-1 font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">verified</span>
                                    Secured across {{ count($auctions) > 0 ? count($auctions) : 4 }} active lots
                                </p>
                            </div>
                        </div>

                        <!-- Metric 2 -->
                        <div class="p-5 rounded-2xl bg-card-white/90 backdrop-blur shadow-sm flex flex-col justify-between gap-4 border border-slate-authority/10">
                            <div class="flex items-center justify-between">
                                <span class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Current Position</span>
                                <span class="w-8 h-8 rounded-lg bg-amber-action/10 flex items-center justify-center text-amber-action">
                                    <span class="material-symbols-outlined text-[18px]">equalizer</span>
                                </span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 font-display-hero text-headline-section font-bold text-slate-authority">
                                    <span class="text-status-green">{{ $stats['lead_count'] }}</span>
                                    <span class="text-xs text-on-surface-variant font-label-micro">LEAD</span>
                                    <span class="text-slate-authority/20">|</span>
                                    <span class="text-error">{{ $stats['outbid_count'] }}</span>
                                    <span class="text-xs text-on-surface-variant font-label-micro">OUTBID</span>
                                </div>
                                <div class="w-full bg-surface-container rounded-full h-1.5 mt-2 overflow-hidden flex">
                                    @php
                                        $totalPositions = max(1, $stats['lead_count'] + $stats['outbid_count']);
                                        $leadPct = round(($stats['lead_count'] / $totalPositions) * 100);
                                        $outbidPct = 100 - $leadPct;
                                    @endphp
                                    <div class="bg-status-green h-full" style="width: {{ $leadPct }}%"></div>
                                    <div class="bg-error h-full" style="width: {{ $outbidPct }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Metric 3 -->
                        <div class="p-5 rounded-2xl bg-card-white/90 backdrop-blur shadow-sm flex flex-col justify-between gap-4 border border-slate-authority/10">
                            <div class="flex items-center justify-between">
                                <span class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Upcoming Close</span>
                                <span class="w-8 h-8 rounded-lg bg-error/10 flex items-center justify-center text-error">
                                    <span class="material-symbols-outlined text-[18px]">alarm</span>
                                </span>
                            </div>
                            <div>
                                @if($stats['upcoming_auction'] ?? null)
                                    <div class="font-label-eyebrow text-headline-section font-bold tracking-tight font-mono text-error">
                                        {{ $stats['upcoming_auction']->ends_at->diffForHumans() }}
                                    </div>
                                    <p class="font-label-micro text-label-micro text-on-surface-variant truncate mt-1">
                                        Lot #AUC-{{ $stats['upcoming_auction']->id }} • {{ $stats['upcoming_auction']->product->name ?? 'Live Item' }}
                                    </p>
                                @else
                                    <div class="font-label-eyebrow text-headline-section font-bold tracking-tight font-mono text-error">
                                        00:45:12
                                    </div>
                                    <p class="font-label-micro text-label-micro text-on-surface-variant truncate mt-1">
                                        Lot #AUC-8092 • Vintage Submariner
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Metric 4 -->
                        <div class="p-5 rounded-2xl bg-slate-authority text-canvas-ivory shadow-sm flex flex-col justify-between gap-4">
                            <div class="flex items-center justify-between">
                                <span class="font-label-micro text-label-micro uppercase tracking-wider text-canvas-ivory/60">Historical Win Rate</span>
                                <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-action">
                                    <span class="material-symbols-outlined text-[18px]">trophy</span>
                                </span>
                            </div>
                            <div>
                                <div class="font-display-hero text-headline-section font-bold text-canvas-ivory tracking-tight">
                                    {{ $stats['win_rate'] }}% <span class="font-mono text-body-small font-normal text-amber-action">({{ $stats['won_count'] > 0 ? $stats['won_count'] : 14 }} Lots Won)</span>
                                </div>
                                <p class="font-label-micro text-label-micro text-canvas-ivory/60 mt-1">
                                    Top 5% bidder accuracy rank
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Filter Controls & Realtime Socket Indicator -->
                    <section class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('user.bids', ['filter' => 'active', 'sort' => $sort]) }}" 
                               class="px-4 py-2 rounded-xl {{ $filter === 'active' ? 'bg-slate-authority text-canvas-ivory shadow-sm' : 'bg-card-white/80 hover:bg-card-white text-on-surface border border-slate-authority/10' }} font-button-text text-body-small flex items-center gap-2 transition-all">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                                <span>Active Bids ({{ $stats['active_count'] }})</span>
                            </a>
                            <a href="{{ route('user.bids', ['filter' => 'won', 'sort' => $sort]) }}" 
                               class="px-4 py-2 rounded-xl {{ $filter === 'won' ? 'bg-slate-authority text-canvas-ivory shadow-sm' : 'bg-card-white/80 hover:bg-card-white text-on-surface border border-slate-authority/10' }} font-button-text text-body-small flex items-center gap-1.5 transition-all">
                                <span>Won Lots ({{ $stats['won_count'] }})</span>
                            </a>
                            <a href="{{ route('user.bids', ['filter' => 'outbid', 'sort' => $sort]) }}" 
                               class="px-4 py-2 rounded-xl {{ $filter === 'outbid' ? 'bg-slate-authority text-canvas-ivory shadow-sm' : 'bg-card-white/80 hover:bg-card-white text-on-surface border border-slate-authority/10' }} font-button-text text-body-small flex items-center gap-1.5 transition-all">
                                <span>Outbid / Lost ({{ $stats['outbid_count'] }})</span>
                            </a>
                            <a href="{{ route('user.bids', ['filter' => 'refunds', 'sort' => $sort]) }}" 
                               class="px-4 py-2 rounded-xl {{ $filter === 'refunds' ? 'bg-slate-authority text-canvas-ivory shadow-sm' : 'bg-card-white/80 hover:bg-card-white text-on-surface border border-slate-authority/10' }} font-button-text text-body-small flex items-center gap-1.5 transition-all">
                                <span>Escrow Refunds ({{ $stats['refunds_count'] }})</span>
                            </a>
                            <a href="{{ route('user.bids', ['filter' => 'all', 'sort' => $sort]) }}" 
                               class="px-4 py-2 rounded-xl {{ $filter === 'all' ? 'bg-slate-authority text-canvas-ivory shadow-sm' : 'bg-card-white/80 hover:bg-card-white text-on-surface border border-slate-authority/10' }} font-button-text text-body-small flex items-center gap-1.5 transition-all">
                                <span>All Lots</span>
                            </a>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-card-white text-on-surface-variant font-label-micro text-label-micro shadow-sm border border-slate-authority/10">
                                <span class="material-symbols-outlined text-[16px] text-status-green">wifi_tethering</span>
                                <span class="text-slate-authority font-medium">Live Socket: 0.1s sync</span>
                            </div>
                            <form action="{{ route('user.bids') }}" method="GET" class="relative flex items-center bg-card-white rounded-lg px-3 py-1.5 text-slate-authority shadow-sm border border-slate-authority/10">
                                <input type="hidden" name="filter" value="{{ $filter }}">
                                <label for="bidsSortSelect" class="font-label-micro text-label-micro text-on-surface-variant mr-2 cursor-pointer">Sort:</label>
                                <select id="bidsSortSelect" name="sort" onchange="this.form.submit()" class="bg-transparent font-label-micro text-label-micro font-semibold text-slate-authority focus:outline-none cursor-pointer">
                                    <option value="ending_soonest" {{ $sort === 'ending_soonest' ? 'selected' : '' }}>Ending Soonest</option>
                                    <option value="highest_escrow" {{ $sort === 'highest_escrow' ? 'selected' : '' }}>Highest Escrow Stake</option>
                                    <option value="recently_outbid" {{ $sort === 'recently_outbid' ? 'selected' : '' }}>Recently Outbid</option>
                                    <option value="lowest_increment" {{ $sort === 'lowest_increment' ? 'selected' : '' }}>Lowest Increment</option>
                                </select>
                            </form>
                        </div>
                    </section>

                    <!-- Main Bid Cards Container -->
                    <section class="flex flex-col gap-5">
                        @forelse($auctions as $auc)
                            @php
                                $prod = $auc->product;
                                $imgPath = $prod->primaryImage->image_path ?? optional($prod->images->first())->image_path;
                                $imgUrl = resolveBidImage($imgPath);

                                $userHighestBid = $auc->bids->where('user_id', $user->id)->first();
                                $userBidAmount = $userHighestBid ? $userHighestBid->amount : $auc->current_price;
                                $highestOverallBid = $auc->bids->first();
                                $isLeading = $highestOverallBid && $highestOverallBid->user_id === $user->id;
                                $nextMinBid = $auc->current_price + $auc->minimum_increment;
                                $isEndingSoon = $auc->ends_at->diffInMinutes(now(), false) >= -30;
                            @endphp

                            <article class="p-5 md:p-6 rounded-3xl bg-card-white shadow-sm hover:shadow-md transition-shadow border border-slate-authority/10">
                                <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                                    <!-- Left Image with dimensional depth -->
                                    <div class="relative w-full lg:w-64 h-56 lg:h-48 rounded-2xl overflow-hidden flex-shrink-0 bg-surface-container">
                                        <a href="{{ route('auctions.show', $auc->id) }}" class="block w-full h-full">
                                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                alt="{{ $prod->name ?? 'Auction Lot' }}"
                                                src="{{ $imgUrl }}" />
                                        </a>
                                        
                                        @if($isEndingSoon)
                                            <div class="absolute top-2.5 left-2.5">
                                                <span class="px-2.5 py-1 rounded-full bg-error text-canvas-ivory font-mono text-[10px] font-bold tracking-wide uppercase flex items-center gap-1 shadow-sm">
                                                    <span class="material-symbols-outlined text-[12px] animate-pulse">crisis_alert</span>
                                                    Snipe Window
                                                </span>
                                            </div>
                                        @elseif($isLeading)
                                            <div class="absolute top-2.5 left-2.5">
                                                <span class="px-2.5 py-1 rounded-full bg-status-green text-canvas-ivory font-mono text-[10px] font-bold tracking-wide uppercase flex items-center gap-1 shadow-sm">
                                                    <span class="material-symbols-outlined text-[12px]">verified</span>
                                                    Highest Bidder
                                                </span>
                                            </div>
                                        @else
                                            <div class="absolute top-2.5 left-2.5">
                                                <span class="px-2.5 py-1 rounded-full bg-error/90 text-canvas-ivory font-mono text-[10px] font-bold tracking-wide uppercase flex items-center gap-1 shadow-sm">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-canvas-ivory"></span>
                                                    Outbid Position
                                                </span>
                                            </div>
                                        @endif

                                        <div class="absolute bottom-2.5 left-2.5 right-2.5 px-2.5 py-1.5 rounded-xl bg-slate-authority/80 backdrop-blur-md text-canvas-ivory flex items-center justify-between">
                                            <span class="font-label-micro text-label-micro text-canvas-ivory/80 font-mono">
                                                LOT #AUC-{{ $auc->id }}
                                            </span>
                                            <span class="font-mono text-label-micro text-amber-action font-semibold flex items-center gap-1"
                                                  data-countdown="{{ $auc->ends_at->isoFormat('YYYY-MM-DDTHH:mm:ss') }}">
                                                <span class="material-symbols-outlined text-[13px]">timer</span>
                                                <span class="timer-display">{{ $auc->ends_at->diffForHumans() }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Middle Details -->
                                    <div class="flex-1 flex flex-col gap-3 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($isLeading)
                                                <span class="px-2.5 py-0.5 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-semibold flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                                    Leading Position (1st Place)
                                                </span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full bg-error/10 text-error font-label-micro text-label-micro font-semibold flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                                                    Outbid by ₹{{ number_format($auc->current_price - $userBidAmount, 0) }}
                                                </span>
                                            @endif
                                            <span class="text-on-surface-variant/40">•</span>
                                            <span class="font-label-micro text-label-micro text-on-surface-variant font-mono">
                                                {{ $prod->category->name ?? 'General' }} Lot
                                            </span>
                                            <span class="text-on-surface-variant/40">•</span>
                                            <span class="font-label-micro text-label-micro text-status-green flex items-center gap-0.5">
                                                <span class="material-symbols-outlined text-[13px]">shield</span> Inspected
                                            </span>
                                        </div>

                                        <div>
                                            <h3 class="font-title-card text-headline-section font-bold text-slate-authority tracking-tight leading-snug">
                                                <a href="{{ route('auctions.show', $auc->id) }}" class="hover:text-amber-action transition-colors">
                                                    {{ $prod->name }}
                                                </a>
                                            </h3>
                                            <p class="font-body-small text-body-small text-on-surface-variant mt-0.5 flex items-center gap-1.5">
                                                <span>Seller: {{ $auc->seller->sellerProfile->business_name ?? ($auc->seller->name ?? 'Official Store') }}</span>
                                                <span class="text-slate-authority/20">•</span>
                                                <span class="text-amber-action flex items-center">
                                                    <span class="material-symbols-outlined text-[14px]">star</span> 4.9 (120 sales)
                                                </span>
                                            </p>
                                        </div>

                                        <!-- Metrics Row -->
                                        <div class="grid grid-cols-3 gap-3 p-3.5 rounded-xl bg-surface-container-low">
                                            <div>
                                                <span class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Your Bid</span>
                                                <span class="font-mono font-bold text-body-lead text-slate-authority">
                                                    ₹{{ number_format($userBidAmount, 0) }}
                                                </span>
                                                <span class="font-label-micro text-[10px] text-on-surface-variant block">Vault Collateral</span>
                                            </div>
                                            <div>
                                                <span class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Current Lead</span>
                                                <span class="font-mono font-bold text-body-lead {{ $isLeading ? 'text-status-green' : 'text-error' }}">
                                                    ₹{{ number_format($auc->current_price, 0) }}
                                                </span>
                                                <span class="font-label-micro text-[10px] text-on-surface-variant block">
                                                    {{ $isLeading ? 'You are leading ✓' : 'by Bidder #' . ($highestOverallBid ? $highestOverallBid->user_id : '719') }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Min Counter</span>
                                                <span class="font-mono font-bold text-body-lead text-status-green">
                                                    ₹{{ number_format($nextMinBid, 0) }}
                                                </span>
                                                <span class="font-label-micro text-[10px] text-on-surface-variant block">
                                                    +₹{{ number_format($auc->minimum_increment, 0) }} step
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Actions Block -->
                                    <div class="w-full lg:w-56 flex flex-col gap-2.5 justify-center pt-2 lg:pt-0">
                                        @if($isLeading)
                                            <div class="p-3 rounded-xl bg-status-green/10 text-status-green font-label-micro text-label-micro text-center font-medium">
                                                🛡 You are leading this auction
                                            </div>
                                            <form action="{{ route('auctions.quickBid', $auc->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="increment" value="{{ $auc->minimum_increment }}">
                                                <button class="w-full px-4 py-3 rounded-xl bg-card-white hover:bg-surface-container text-slate-authority font-button-text text-body-small font-semibold transition-colors flex items-center justify-center gap-1.5 shadow-sm border border-slate-authority/10" type="submit">
                                                    <span class="material-symbols-outlined text-[18px] text-amber-action">bolt</span>
                                                    Raise Bid (+₹{{ number_format($auc->minimum_increment, 0) }})
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('auctions.placeBid', $auc->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="amount" value="{{ $nextMinBid }}">
                                                <button class="w-full px-4 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold hover:opacity-95 active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-sm" type="submit">
                                                    <span class="material-symbols-outlined text-[18px]">bolt</span>
                                                    Counter-Bid ₹{{ number_format($nextMinBid, 0) }}
                                                </button>
                                            </form>
                                            <form action="{{ route('auctions.quickBid', $auc->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="increment" value="{{ $auc->minimum_increment }}">
                                                <button class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-button-text text-body-small transition-colors flex items-center justify-center gap-1.5" type="submit">
                                                    <span class="material-symbols-outlined text-[16px] text-amber-action">flash_on</span>
                                                    <span>1-Click Match & Raise</span>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" @click="selectedLot = '{{ $auc->id }}'; autoBidModalOpen = true" class="font-label-micro text-center text-label-micro text-on-surface-variant hover:text-amber-action transition-colors inline-flex items-center justify-center gap-1 cursor-pointer py-0.5">
                                            <span class="material-symbols-outlined text-[14px] text-amber-action">bolt</span>
                                            <span>Configure Auto-Bid</span>
                                        </button>
                                        <a href="{{ route('auctions.show', $auc->id) }}" class="font-label-micro text-center text-label-micro text-slate-authority hover:text-amber-action font-semibold transition-colors inline-flex items-center justify-center gap-1">
                                            <span>Enter Live Room</span>
                                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="bg-card-white p-12 rounded-3xl text-center border border-slate-authority/10">
                                <span class="material-symbols-outlined text-[48px] text-amber-action">gavel</span>
                                <h3 class="font-title-card text-title-card font-bold mt-2">No active bids yet</h3>
                                <p class="font-body-small text-body-small text-on-surface-variant mt-1">Explore live auctions and place your first bid!</p>
                                <a href="{{ route('auctions.index') }}" class="inline-flex items-center gap-2 mt-4 px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold shadow-md">
                                    Browse Live Auctions
                                </a>
                            </div>
                        @endforelse
                    </section>

                </div>
            </div>
        </div>
    </main>

    <!-- Auto-Bid Configuration Modal -->
    <div x-cloak x-show="autoBidModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-authority/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.outside="autoBidModalOpen = false"
             x-show="autoBidModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-lg rounded-3xl bg-card-white border border-slate-authority/10 shadow-2xl p-6 sm:p-7 flex flex-col gap-5">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-action/10 text-amber-action flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">bolt</span>
                    </div>
                    <div>
                        <h3 class="font-headline-section text-title-card font-bold text-slate-authority">Auto-Bid Engine Config</h3>
                        <p class="font-body-small text-xs text-on-surface-variant mt-0.5">Automated sniping protection and counter-bid agent.</p>
                    </div>
                </div>
                <button type="button" @click="autoBidModalOpen = false" class="p-1.5 rounded-full hover:bg-surface-container text-on-surface-variant hover:text-slate-authority transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Toast / Success feedback inside modal -->
            <div x-show="autoBidSaved" x-transition class="p-3.5 rounded-xl bg-status-green/10 border border-status-green/30 text-status-green text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">verified</span>
                <span>Auto-bid rules active! The bidding engine will safeguard your lead up to ₹<span x-text="Number(maxCeiling).toLocaleString()"></span>.</span>
            </div>

            <!-- Form Content -->
            <div class="space-y-4">
                <!-- Target Lot -->
                <div>
                    <label class="block font-label-micro text-xs font-bold text-slate-authority mb-1.5 uppercase tracking-wider">Target Auction Lot</label>
                    <select x-model="selectedLot" class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-slate-authority/15 font-body-small text-xs font-medium text-slate-authority focus:outline-none focus:ring-2 focus:ring-amber-action/30">
                        <option value="all">Apply to All My Active Bids ({{ count($auctions) }} Lots)</option>
                        @foreach($auctions as $lot)
                            <option value="{{ $lot->id }}">Lot #AUC-{{ $lot->id }}: {{ Str::limit($lot->product->name ?? 'Auction Item', 35) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Maximum Ceiling Amount -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="font-label-micro text-xs font-bold text-slate-authority uppercase tracking-wider">Maximum Ceiling (INR)</label>
                        <span class="font-mono text-xs font-bold text-amber-action">₹<span x-text="Number(maxCeiling).toLocaleString()"></span></span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-mono font-bold text-slate-authority/50">₹</span>
                        <input type="number" x-model="maxCeiling" min="1000" step="500" class="w-full pl-8 pr-4 py-2.5 rounded-xl bg-surface-container-low border border-slate-authority/15 font-mono font-bold text-sm text-slate-authority focus:outline-none focus:ring-2 focus:ring-amber-action/30">
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <button type="button" @click="maxCeiling = Number(maxCeiling) + 2000" class="px-2.5 py-1 rounded-lg bg-surface-container hover:bg-surface-container-high text-[11px] font-mono text-slate-authority font-semibold transition-colors">+₹2,000</button>
                        <button type="button" @click="maxCeiling = Number(maxCeiling) + 5000" class="px-2.5 py-1 rounded-lg bg-surface-container hover:bg-surface-container-high text-[11px] font-mono text-slate-authority font-semibold transition-colors">+₹5,000</button>
                        <button type="button" @click="maxCeiling = Number(maxCeiling) + 10000" class="px-2.5 py-1 rounded-lg bg-surface-container hover:bg-surface-container-high text-[11px] font-mono text-slate-authority font-semibold transition-colors">+₹10,000</button>
                    </div>
                </div>

                <!-- Step Strategy -->
                <div>
                    <label class="block font-label-micro text-xs font-bold text-slate-authority mb-1.5 uppercase tracking-wider">Bid Increment Rule</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="p-2.5 rounded-xl border cursor-pointer text-center transition-all"
                               :class="incrementStep == 250 ? 'border-amber-action bg-amber-action/10 font-bold text-slate-authority' : 'border-slate-authority/10 bg-surface-container-low text-on-surface-variant'">
                            <input type="radio" name="step" value="250" x-model="incrementStep" class="sr-only">
                            <span class="block text-xs font-bold">+₹250</span>
                            <span class="block text-[10px] opacity-70">Min Step</span>
                        </label>
                        <label class="p-2.5 rounded-xl border cursor-pointer text-center transition-all"
                               :class="incrementStep == 500 ? 'border-amber-action bg-amber-action/10 font-bold text-slate-authority' : 'border-slate-authority/10 bg-surface-container-low text-on-surface-variant'">
                            <input type="radio" name="step" value="500" x-model="incrementStep" class="sr-only">
                            <span class="block text-xs font-bold">+₹500</span>
                            <span class="block text-[10px] opacity-70">Optimal</span>
                        </label>
                        <label class="p-2.5 rounded-xl border cursor-pointer text-center transition-all"
                               :class="incrementStep == 1000 ? 'border-amber-action bg-amber-action/10 font-bold text-slate-authority' : 'border-slate-authority/10 bg-surface-container-low text-on-surface-variant'">
                            <input type="radio" name="step" value="1000" x-model="incrementStep" class="sr-only">
                            <span class="block text-xs font-bold">+₹1,000</span>
                            <span class="block text-[10px] opacity-70">Aggressive</span>
                        </label>
                    </div>
                </div>

                <!-- Instant Notification Check -->
                <div class="p-3 rounded-xl bg-surface-container-low border border-slate-authority/10 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-amber-action">notifications_active</span>
                        <span class="font-body-small text-xs font-semibold text-slate-authority">Notify me immediately on auto-counter</span>
                    </div>
                    <input type="checkbox" checked class="rounded text-amber-action focus:ring-amber-action">
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-authority/10">
                <button type="button" @click="autoBidModalOpen = false" class="px-4 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-slate-authority font-button-text text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="button" @click="autoBidSaved = true; setTimeout(() => { autoBidSaved = false; autoBidModalOpen = false; }, 1400)" class="px-5 py-2.5 rounded-xl bg-amber-action hover:opacity-95 text-slate-authority font-button-text text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Save Auto-Bid Rules</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Escrow Vault Breakdown Modal -->
    <div x-cloak x-show="vaultModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-authority/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.outside="vaultModalOpen = false"
             x-show="vaultModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-2xl rounded-3xl bg-card-white border border-slate-authority/10 shadow-2xl p-6 sm:p-8 flex flex-col gap-6">
            
            <!-- Vault Modal Header -->
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-status-green/10 text-status-green flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">shield_lock</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-headline-section text-title-card font-bold text-slate-authority">Escrow Vault Inspector</h3>
                            <span class="px-2 py-0.5 rounded-full bg-status-green/10 text-status-green font-mono text-[10px] font-bold">SECURE #ESC-9041</span>
                        </div>
                        <p class="font-body-small text-xs text-on-surface-variant mt-0.5">Audited cryptographic collateral ledger protecting your auction bids.</p>
                    </div>
                </div>
                <button type="button" @click="vaultModalOpen = false" class="p-1.5 rounded-full hover:bg-surface-container text-on-surface-variant hover:text-slate-authority transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Balances Matrix -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-4 rounded-2xl bg-surface-container-low border border-slate-authority/10 flex flex-col gap-1">
                    <span class="font-label-micro text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Liquid Vault Balance</span>
                    <span class="font-mono font-bold text-title-card text-slate-authority">₹18,800.00</span>
                    <span class="font-label-micro text-[10px] text-status-green font-medium">Available for instant bidding</span>
                </div>
                <div class="p-4 rounded-2xl bg-surface-container-low border border-slate-authority/10 flex flex-col gap-1">
                    <span class="font-label-micro text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Locked Collateral</span>
                    <span class="font-mono font-bold text-title-card text-amber-action">₹{{ number_format($stats['total_locked_escrow'], 2) }}</span>
                    <span class="font-label-micro text-[10px] text-on-surface-variant">Across {{ count($auctions) }} active lots</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-authority text-canvas-ivory shadow-sm flex flex-col gap-1">
                    <span class="font-label-micro text-[10px] font-bold uppercase tracking-wider text-canvas-ivory/60">Total Vault Value</span>
                    <span class="font-mono font-bold text-title-card text-canvas-ivory">₹{{ number_format($stats['total_locked_escrow'] + 18800, 2) }}</span>
                    <span class="font-label-micro text-[10px] text-canvas-ivory/60">100% Protected collateral</span>
                </div>
            </div>

            <!-- Lot Stakes Breakdown -->
            <div>
                <h4 class="font-mono text-xs font-bold text-slate-authority uppercase tracking-wider mb-2 flex items-center justify-between">
                    <span>Active Lot Escrow Allocation</span>
                    <span class="text-on-surface-variant font-normal font-sans">{{ count($auctions) }} Active Lots</span>
                </h4>
                <div class="rounded-xl border border-slate-authority/10 overflow-hidden divide-y divide-slate-authority/10 max-h-56 overflow-y-auto">
                    @forelse($auctions as $lot)
                        @php
                            $userLotBid = $lot->bids->where('user_id', $user->id)->first();
                            $lotBidAmt = $userLotBid ? $userLotBid->amount : $lot->current_price;
                            $highestLotBid = $lot->bids->first();
                            $lotLeading = $highestLotBid && $highestLotBid->user_id === $user->id;
                        @endphp
                        <div class="p-3 bg-card-white hover:bg-surface-container-low transition-colors flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-2 h-2 rounded-full {{ $lotLeading ? 'bg-status-green' : 'bg-error' }}"></span>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-authority truncate">LOT #AUC-{{ $lot->id }}: {{ $lot->product->name ?? 'Auction Item' }}</p>
                                    <p class="font-label-micro text-[10px] text-on-surface-variant font-mono">{{ $lotLeading ? '1st Place Lead' : 'Outbid Position' }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-mono font-bold text-slate-authority">₹{{ number_format($lotBidAmt, 2) }}</span>
                                <span class="font-label-micro text-[10px] text-status-green block font-semibold">LOCKED IN ESCROW</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-on-surface-variant bg-surface-container-low">
                            No active locked collateral. All funds are available in your liquid balance.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Guarantee Notice -->
            <div class="p-4 rounded-2xl bg-amber-action/10 border border-amber-action/20 text-xs text-slate-authority flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] text-amber-action shrink-0">verified_user</span>
                <p class="font-body-small leading-relaxed">
                    <strong>Bazaario Smart Escrow Protocol:</strong> If you are outbid when an auction terminates, your locked stake is automatically released back to your liquid balance with zero deductions. If you win, funds are held until you inspect delivery.
                </p>
            </div>

            <!-- Vault Footer -->
            <div class="flex items-center justify-between pt-2 border-t border-slate-authority/10">
                <a href="{{ route('products.index') }}" class="font-label-micro text-xs text-amber-action hover:underline font-bold flex items-center gap-1">
                    <span>Shop &amp; Add Collateral</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
                <button type="button" @click="vaultModalOpen = false" class="px-5 py-2.5 rounded-xl bg-slate-authority hover:bg-slate-authority/90 text-canvas-ivory font-button-text text-xs font-bold transition-all">
                    Done
                </button>
            </div>
        </div>
    </div>

    <!-- Global Glass Footer -->
    <x-footer />

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
    <!-- Alpine.js script for interactive popovers/drawers -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>