<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Lot #AUC-{{ $auction->id }} • {{ $auction->product->name ?? 'Live Auction' }} — Bazaario</title>

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />

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

<body class="bg-canvas-ivory text-on-surface antialiased selection:bg-amber-action selection:text-slate-authority min-h-screen flex flex-col font-sans">
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
                return Storage::url($path);
            }
        }

        $primaryImgUrl = resolveShowImage($primaryImgPath);
        $highestBidderName = $highestBid ? $highestBid->user->name : null;
        $bidsCount = $auction->bids->count();
        $incAmt = (float) $auction->minimum_increment;
    @endphp

    <!-- Global Floating Pill Navbar -->
    @include('components.nav')

    <main class="w-full pt-3 pb-16 bg-canvas-ivory flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-4 pb-12 flex flex-col gap-6">

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-status-green/10 border border-status-green/30 text-status-green px-4 py-3 rounded-2xl flex items-center gap-2.5 font-medium text-sm shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-error/10 border border-error/30 text-error px-4 py-3 rounded-2xl flex items-center gap-2.5 font-medium text-sm shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Navigation Breadcrumb & Telemetry Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-1">
                <div class="flex flex-wrap items-center gap-3">
                    <a class="bg-card-white shadow-sm hover:bg-surface-container text-slate-authority font-body-small text-body-small font-medium px-3.5 py-1.5 rounded-full inline-flex items-center gap-1.5 transition-colors border border-slate-authority/10"
                        href="{{ route('auctions.index') }}">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        <span>Back to Live Floor</span>
                    </a>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-authority/20"></span>
                    <nav class="flex items-center gap-2 font-label-micro text-label-micro uppercase text-on-surface-variant tracking-wider">
                        <a class="hover:text-slate-authority transition-colors" href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <a class="hover:text-slate-authority transition-colors" href="{{ route('auctions.index') }}">Auctions</a>
                        <span>/</span>
                        <span class="text-slate-authority font-semibold">Lot #AUC-{{ $auction->id }}</span>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium border border-status-green/20">
                        <span class="w-2 h-2 rounded-full bg-status-green animate-ping"></span>
                        <span>CRYPTOGRAPHIC ESCROW LOCKED • 0.2s SYNC</span>
                    </div>
                </div>
            </div>

            <!-- Main Live Lot Bidding Room Split Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- ── LEFT COLUMN: Lot Media Gallery & Specifications (7 cols) ── -->
                <div class="lg:col-span-7 flex flex-col gap-6">

                    <!-- High-Res Media Showcase Card -->
                    <div class="bg-card-white rounded-3xl p-5 shadow-sm border border-slate-authority/10 overflow-hidden relative">
                        <!-- Main Preview Image -->
                        <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden bg-slate-900 border border-slate-authority/5">
                            <img id="main-lot-image"
                                 src="{{ $primaryImgUrl }}"
                                 alt="{{ $prod->name }}"
                                 class="w-full h-full object-cover transition-all duration-300">

                            <!-- Live Room Overlay Badge -->
                            <div class="absolute top-3.5 left-3.5 flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full bg-error text-card-white font-mono text-[11px] font-bold tracking-wide uppercase flex items-center gap-1.5 shadow-md animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-card-white"></span>
                                    LIVE FLOOR LOT
                                </span>
                                @if($isReserveMet)
                                    <span class="px-3 py-1 rounded-full bg-status-green/90 backdrop-blur-md text-card-white font-mono text-[11px] font-bold tracking-wide uppercase flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-[13px]">verified</span>
                                        RESERVE MET
                                    </span>
                                @endif
                            </div>

                            <div class="absolute bottom-3.5 left-3.5 right-3.5 p-3 rounded-xl bg-slate-authority/85 backdrop-blur-md text-canvas-ivory flex items-center justify-between text-xs">
                                <span class="font-mono text-label-micro text-canvas-ivory/80">LOT ID: #AUC-{{ $auction->id }}</span>
                                <span class="font-mono text-label-micro text-amber-action font-semibold flex items-center gap-1">
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
                                        $tUrl = str_starts_with($img->image_path, 'http') ? $img->image_path : Storage::url($img->image_path);
                                    @endphp
                                    <button type="button" 
                                            onclick="document.getElementById('main-lot-image').src='{{ $tUrl }}'"
                                            class="w-16 h-16 rounded-xl overflow-hidden border-2 border-slate-authority/10 hover:border-amber-action transition-all shrink-0 focus:outline-none focus:border-amber-action">
                                        <img src="{{ $tUrl }}" alt="Thumb" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Lot Specifications & Authenticity Seal -->
                    <div class="bg-card-white rounded-3xl p-6 shadow-sm border border-slate-authority/10 flex flex-col gap-5">
                        <div class="flex items-center justify-between border-b border-slate-authority/10 pb-4">
                            <div>
                                <span class="font-label-eyebrow text-label-eyebrow text-amber-action font-bold uppercase tracking-wider">
                                    {{ $prod->category->name ?? 'Curated Specialty' }}
                                </span>
                                <h1 class="font-display-hero text-headline-section font-bold text-slate-authority tracking-tight mt-0.5">
                                    {{ $prod->name }}
                                </h1>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-action/15 flex items-center justify-center text-amber-action shrink-0">
                                <span class="material-symbols-outlined text-[28px]">verified</span>
                            </div>
                        </div>

                        <p class="font-body-regular text-body-regular text-on-surface-variant leading-relaxed">
                            {{ $prod->description ?? $prod->short_description ?? 'This verified lot is in prime physical condition, inspected by authorized horologists/curators, and certified authentic for peer-to-peer clearance.' }}
                        </p>

                        <!-- Dimensional Key Metrics Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                            <div class="p-3.5 rounded-2xl bg-surface-container-low border border-slate-authority/5">
                                <span class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Starting Floor</span>
                                <span class="font-mono font-bold text-slate-authority text-body-regular mt-0.5 block">
                                    ₹{{ number_format($auction->starting_price, 0) }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-surface-container-low border border-slate-authority/5">
                                <span class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Min Increment</span>
                                <span class="font-mono font-bold text-slate-authority text-body-regular mt-0.5 block">
                                    ₹{{ number_format($auction->minimum_increment, 0) }}
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-surface-container-low border border-slate-authority/5">
                                <span class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Authenticity</span>
                                <span class="font-bold text-status-green text-body-regular mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">shield</span> 100% Certified
                                </span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-surface-container-low border border-slate-authority/5">
                                <span class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Settlement</span>
                                <span class="font-mono font-bold text-slate-authority text-body-regular mt-0.5 block">
                                    Instant Escrow
                                </span>
                            </div>
                        </div>

                        <!-- Seller Verification Card -->
                        <div class="p-4 rounded-2xl bg-slate-authority text-canvas-ivory flex items-center justify-between gap-4 mt-2">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-xl bg-amber-action text-slate-authority flex items-center justify-center font-bold font-display text-lg">
                                    {{ strtoupper(substr($auction->seller->name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-card-white">{{ $auction->seller->sellerProfile->business_name ?? $auction->seller->name }}</span>
                                        <span class="px-2 py-0.5 rounded-full bg-status-green/20 text-status-green text-[10px] font-mono font-bold">VERIFIED SELLER</span>
                                    </div>
                                    <span class="text-xs text-canvas-ivory/70 flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[14px] text-amber-action">star</span>
                                        4.9 Rating • 100% Dispatched on time
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="px-4 py-2 rounded-xl bg-card-white/10 hover:bg-card-white/20 text-card-white font-button-text text-xs transition-colors">
                                View Store
                            </button>
                        </div>
                    </div>

                </div>

                <!-- ── RIGHT COLUMN: Sticky Real-Time Bidding Terminal (5 cols) ── -->
                <div class="lg:col-span-5 flex flex-col gap-6 sticky top-24">

                    <!-- Live Bidding Console Card -->
                    <div class="bg-card-white rounded-3xl p-6 shadow-xl border-2 border-amber-action/30 relative overflow-hidden flex flex-col gap-5">
                        
                        <!-- Top HUD Bar: Countdown & Anti-Snipe -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-authority/10">
                            <div>
                                <span class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant block">Auction Closes In</span>
                                <div class="font-mono text-headline-section font-bold text-error flex items-center gap-1.5 mt-0.5 animate-pulse"
                                     data-countdown="{{ $auction->ends_at->isoFormat('YYYY-MM-DDTHH:mm:ss') }}">
                                    <span class="material-symbols-outlined text-[20px]">timer</span>
                                    <span class="timer-display">{{ $auction->ends_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant block">Anti-Snipe Buffer</span>
                                <span class="font-mono text-label-micro text-amber-action font-semibold bg-amber-action/10 px-2.5 py-1 rounded-md inline-block mt-0.5">
                                    +2m Auto-Extension
                                </span>
                            </div>
                        </div>

                        <!-- Current Floor Price Matrix -->
                        <div class="bg-surface-container-low p-4 rounded-2xl flex flex-col gap-2 border border-slate-authority/5">
                            <div class="flex items-end justify-between">
                                <div>
                                    <span class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Current Leading Bid</span>
                                    <div class="font-display-hero text-display-hero font-bold text-slate-authority leading-none mt-1 font-mono">
                                        ₹{{ number_format($auction->current_price, 0) }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-label-micro text-label-micro text-on-surface-variant block">Leader:</span>
                                    <span class="font-mono text-body-small font-bold text-status-green bg-status-green/10 px-2 py-0.5 rounded">
                                        {{ $highestBidderName ? '@'.Str::slug($highestBidderName) : '@floor' }}
                                    </span>
                                </div>
                            </div>

                            @if($isUserLeading)
                                <div class="mt-2 p-2.5 rounded-xl bg-status-green/15 text-status-green font-label-micro text-label-micro font-bold flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    <span>You are currently the highest bidder for this lot!</span>
                                </div>
                            @elseif($isUserOutbid)
                                <div class="mt-2 p-2.5 rounded-xl bg-error/15 text-error font-label-micro text-label-micro font-bold flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">warning</span>
                                    <span>You have been outbid! Raise your collateral to reclaim the lead.</span>
                                </div>
                            @endif
                        </div>

                        <!-- Quick Increment Preset Buttons -->
                        <div>
                            <label class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant block mb-2 font-semibold">
                                1-Click Quick Bidding Presets
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <form action="{{ route('auctions.quickBid', $auction->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="increment" value="{{ $auction->minimum_increment }}">
                                    <button class="w-full py-2.5 px-2 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-mono font-bold text-xs border border-slate-authority/10 transition-all flex items-center justify-center gap-1" type="submit">
                                        <span class="material-symbols-outlined text-[14px] text-amber-action">bolt</span>
                                        +₹{{ number_format($auction->minimum_increment >= 1000 ? $auction->minimum_increment/1000 : $auction->minimum_increment, 0) }}{{ $auction->minimum_increment >= 1000 ? 'k' : '' }}
                                    </button>
                                </form>
                                <form action="{{ route('auctions.quickBid', $auction->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="increment" value="{{ $auction->minimum_increment * 2 }}">
                                    <button class="w-full py-2.5 px-2 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-mono font-bold text-xs border border-slate-authority/10 transition-all flex items-center justify-center gap-1" type="submit">
                                        <span class="material-symbols-outlined text-[14px] text-amber-action">bolt</span>
                                        +₹{{ number_format(($auction->minimum_increment * 2) >= 1000 ? ($auction->minimum_increment * 2)/1000 : ($auction->minimum_increment * 2), 0) }}{{ ($auction->minimum_increment * 2) >= 1000 ? 'k' : '' }}
                                    </button>
                                </form>
                                <form action="{{ route('auctions.quickBid', $auction->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="increment" value="{{ $auction->minimum_increment * 5 }}">
                                    <button class="w-full py-2.5 px-2 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-mono font-bold text-xs border border-slate-authority/10 transition-all flex items-center justify-center gap-1" type="submit">
                                        <span class="material-symbols-outlined text-[14px] text-amber-action">bolt</span>
                                        +₹{{ number_format(($auction->minimum_increment * 5) >= 1000 ? ($auction->minimum_increment * 5)/1000 : ($auction->minimum_increment * 5), 0) }}{{ ($auction->minimum_increment * 5) >= 1000 ? 'k' : '' }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Custom Bid Form Console -->
                        <form action="{{ route('auctions.placeBid', $auction->id) }}" method="POST" class="flex flex-col gap-3">
                            @csrf
                            <div>
                                <label class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant block mb-1.5 font-semibold">
                                    Custom Bid Amount (Min: ₹{{ number_format($minNextBid, 0) }})
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-4 font-mono font-bold text-slate-authority text-base">₹</span>
                                    <input type="number" 
                                           name="amount" 
                                           value="{{ $minNextBid }}" 
                                           min="{{ $minNextBid }}" 
                                           step="1"
                                           class="w-full pl-9 pr-4 py-3 rounded-xl bg-canvas-ivory border-2 border-slate-authority/15 font-mono font-bold text-lg text-slate-authority focus:outline-none focus:border-amber-action transition-colors shadow-inner"
                                           required>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="w-full py-3.5 px-4 rounded-xl bg-amber-action hover:opacity-95 text-slate-authority font-button-text font-bold text-body-regular shadow-md transition-all active:scale-[0.99] flex items-center justify-center gap-2 mt-1">
                                <span class="material-symbols-outlined text-[20px]">gavel</span>
                                <span>Place Bid &amp; Secure Escrow</span>
                            </button>
                        </form>

                        <div class="p-3 rounded-xl bg-surface-container-low text-on-surface-variant font-label-micro text-[11px] flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-status-green">lock</span>
                            <span>Collateral locked securely in vault. 100% instant refund if outbid.</span>
                        </div>
                    </div>

                    <!-- Live Bids Ledger Activity Stream -->
                    <div class="bg-card-white rounded-3xl p-5 shadow-sm border border-slate-authority/10 flex flex-col gap-3.5">
                        <div class="flex items-center justify-between border-b border-slate-authority/10 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-ping"></span>
                                <h3 class="font-title-card text-body-regular font-bold text-slate-authority">Live Bid Stream</h3>
                            </div>
                            <span class="font-mono text-label-micro text-on-surface-variant">{{ $bidsCount }} Total Bids</span>
                        </div>

                        <div class="flex flex-col divide-y divide-slate-authority/5 max-h-64 overflow-y-auto pr-1">
                            @forelse($auction->bids as $index => $b)
                                <div class="py-2.5 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg {{ $index === 0 ? 'bg-status-green text-card-white font-bold' : 'bg-surface-container text-slate-authority' }} flex items-center justify-center font-mono text-[11px]">
                                            #{{ $bidsCount - $index }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-authority block">
                                                Bidder #{{ substr(md5($b->user_id), 0, 4) }}
                                                @if($index === 0)
                                                    <span class="text-[10px] text-status-green font-mono font-bold ml-1">(Leading)</span>
                                                @endif
                                            </span>
                                            <span class="text-[10px] text-on-surface-variant font-mono">{{ $b->created_at ? $b->created_at->diffForHumans() : 'Just now' }}</span>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold {{ $index === 0 ? 'text-status-green text-sm' : 'text-slate-authority' }}">
                                        ₹{{ number_format($b->amount, 0) }}
                                    </span>
                                </div>
                            @empty
                                <div class="py-6 text-center text-on-surface-variant text-xs">
                                    No bids placed yet. Be the first to bid at floor price!
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

            <!-- Related Live Lots -->
            @if($relatedAuctions && $relatedAuctions->isNotEmpty())
                <div class="mt-12 flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-label-eyebrow text-label-eyebrow text-amber-action font-bold uppercase tracking-wider">Other Live Clearances</span>
                            <h2 class="font-display-hero text-headline-section font-bold text-slate-authority tracking-tight">Active Floor Lots</h2>
                        </div>
                        <a href="{{ route('auctions.index') }}" class="font-button-text text-body-small font-bold text-slate-authority hover:text-amber-action transition-colors flex items-center gap-1">
                            <span>View All ({{ $relatedAuctions->count() }})</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach($relatedAuctions as $rel)
                            @php
                                $rProd = $rel->product;
                                $rImg = $rProd->primaryImage->image_path ?? optional($rProd->images->first())->image_path;
                                $rImgUrl = $rImg 
                                    ? (str_starts_with($rImg, 'http') ? $rImg : Storage::url($rImg))
                                    : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';
                            @endphp
                            <a href="{{ route('auctions.show', $rel->id) }}" class="group bg-card-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-all border border-slate-authority/10 flex flex-col justify-between">
                                <div>
                                    <div class="aspect-square rounded-xl overflow-hidden bg-slate-900 mb-3 relative">
                                        <img src="{{ $rImgUrl }}" alt="{{ $rProd->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <span class="absolute bottom-2 left-2 bg-slate-authority/80 backdrop-blur text-canvas-ivory font-mono text-[10px] px-2 py-0.5 rounded">
                                            {{ $rel->bids->count() }} Bids
                                        </span>
                                    </div>
                                    <h4 class="font-bold text-slate-authority text-body-small group-hover:text-amber-action transition-colors line-clamp-1">
                                        {{ $rProd->name }}
                                    </h4>
                                </div>
                                <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate-authority/5">
                                    <span class="font-mono font-bold text-slate-authority">₹{{ number_format($rel->current_price, 0) }}</span>
                                    <span class="text-xs font-bold text-amber-action flex items-center gap-0.5">Bid →</span>
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
