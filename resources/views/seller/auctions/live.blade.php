@extends('layouts.seller')

@section('title', 'Wholesale Live Bidding Terminal — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
    $liveAuction = $auction ?? null;
    $bids = $liveAuction ? $liveAuction->bids()->latest('id')->take(10)->get() : collect();
    $highestBid = $liveAuction ? (float) $liveAuction->current_price : 0;
    $isReserveMet = $liveAuction ? $liveAuction->isReserveMet() : false;
    $bidsCount = $liveAuction ? $liveAuction->bids()->count() : 0;

    // Helper for safe auction thumbnail resolution
    if (!function_exists('sellerLiveAuctionThumb')) {
        function sellerLiveAuctionThumb($product) {
            if (!$product) return asset('images/product-placeholder.svg');
            $raw = $product->primaryImage?->image_path ?? optional($product->images?->first())->image_path;
            if (!$raw) return asset('images/product-placeholder.svg');
            if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) return $raw;
            $clean = ltrim($raw, '/');
            if (file_exists(public_path($clean))) return asset($clean);
            if (file_exists(public_path('images/' . $clean))) return asset('images/' . $clean);
            if (file_exists(public_path('storage/' . $clean))) return asset('storage/' . $clean);
            return \Illuminate\Support\Facades\Storage::url($raw);
        }
    }
    $mainThumb = $liveAuction ? sellerLiveAuctionThumb($liveAuction->product) : null;
    $initialSecondsLeft = $liveAuction ? max(0, (int) now()->diffInSeconds($liveAuction->ends_at, false)) : 0;
    $initialTotalDuration = $liveAuction ? max(1, (int) $liveAuction->starts_at->diffInSeconds($liveAuction->ends_at, false)) : 1;
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    secondsLeft: {{ $initialSecondsLeft }},
    totalDuration: {{ $initialTotalDuration }},
    timerText: '00:00:00',
    init() {
        this.updateTimer();
        setInterval(() => {
            if (this.secondsLeft > 0) {
                this.secondsLeft--;
                this.updateTimer();
            } else {
                this.timerText = 'CLOSED';
            }
        }, 1000);
    },
    updateTimer() {
        const totalSecs = Math.max(0, Math.floor(this.secondsLeft));
        const days = Math.floor(totalSecs / 86400);
        const hrs = Math.floor((totalSecs % 86400) / 3600);
        const mins = Math.floor((totalSecs % 3600) / 60);
        const secs = totalSecs % 60;
        
        if (days > 0) {
            this.timerText = days + 'd ' + String(hrs).padStart(2, '0') + 'h ' + String(mins).padStart(2, '0') + 'm ' + String(secs).padStart(2, '0') + 's';
        } else {
            this.timerText = String(hrs).padStart(2, '0') + ':' + String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        }
    },
    get progressPercent() {
        if (this.secondsLeft <= 0) return 0;
        return Math.min(100, Math.max(0, Math.round((this.secondsLeft / this.totalDuration) * 100)));
    }
}">

    <!-- BREADCRUMB & METRIC STATUS BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-3 mb-2">
        <div class="flex items-center gap-2 text-xs font-mono text-slate-500">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-amber-600 transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <a href="{{ route('seller.auctions.index') }}" class="hover:text-amber-600 transition-colors">Auctions</a>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <span class="text-slate-900 font-bold">Wholesale Live Terminal</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200/70 rounded-xl font-mono text-[11px] uppercase text-emerald-800 font-semibold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                APMC SPOT TICKER CONNECTED
            </span>
            <span class="font-mono text-xs text-slate-600 bg-white border border-slate-200 px-3 py-1 rounded-xl shadow-2xs">
                SELLER ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    <!-- WORKSPACE HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-slate-200/80">
        <div class="space-y-1.5 max-w-3xl">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="font-heading text-2xl lg:text-3xl tracking-tight text-slate-900 font-extrabold">
                    Wholesale Bidding Terminal
                </h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-900 border border-amber-200 font-mono text-xs font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $liveAuction ? '1 Active Floor Lot' : 'No Live Lots' }}
                </span>
            </div>
            <p class="text-sm text-slate-500 leading-relaxed">
                Live spot-market auctions, real-time bidder telemetry, and verified bulk agricultural allocations for <span class="font-semibold text-slate-800">{{ $profile?->shop_name ?? 'My Farm' }}</span>.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.auctions.index') }}" class="h-11 px-4 rounded-xl bg-white border border-slate-200/80 text-slate-700 font-heading text-xs font-semibold shadow-2xs hover:bg-slate-50 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">table_rows</span>
                <span>Registry Table</span>
            </a>
            <a href="{{ route('seller.auctions.create') }}" class="h-11 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold shadow-xs hover:shadow-md transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Create Auction</span>
            </a>
        </div>
    </div>

    @if(!$liveAuction)
        <!-- Empty Live State -->
        <div class="mt-8 bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs flex flex-col items-center justify-center">
            <div class="w-20 h-20 rounded-2xl bg-amber-500/10 border border-amber-200 flex items-center justify-center text-amber-600 mb-4 shadow-xs">
                <span class="material-symbols-outlined text-[40px]">gavel</span>
            </div>
            <h2 class="font-heading text-xl font-bold text-slate-900">No Active Live Auctions Currently</h2>
            <p class="text-sm text-slate-500 max-w-md mt-1 mb-6">There are currently no live bidding terminals broadcasting from your farm catalog. Create a scheduled or instant wholesale auction to begin receiving live trade orders.</p>
            <a href="{{ route('seller.auctions.create') }}" class="h-11 px-6 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold shadow-xs transition-all inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Schedule a Lot Now</span>
            </a>
        </div>
    @else
        <!-- DUAL-PANE WORKSPACE: LEFT 65% (8 Cols) / RIGHT 35% (4 Cols) -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- COLUMN A: 8 Cols - LIVE TERMINAL & TELEMETRY -->
            <div class="lg:col-span-8 flex flex-col gap-6">

                <!-- MAIN LIVE AUCTION CARD -->
                <div class="bg-white rounded-3xl p-6 lg:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-amber-500 to-emerald-500"></div>

                    <!-- Header Block of Lot with Artwork Frame -->
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-5">
                        <div class="flex items-start gap-4">
                            <!-- Product Image Container -->
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-slate-50 shrink-0 border border-slate-200/80 shadow-2xs flex items-center justify-center p-2">
                                <img src="{{ $mainThumb }}" 
                                     alt="{{ $liveAuction->product?->name ?? 'Lot' }}" 
                                     class="w-full h-full object-contain"
                                     onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                            </div>
                            <div class="space-y-1.5 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs font-bold bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-lg text-slate-700">
                                        LOT #AUC-{{ str_pad($liveAuction->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/60 font-mono text-xs font-bold uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                        LIVE BIDDING
                                    </span>
                                    <span class="font-mono text-xs px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 uppercase">
                                        APMC Lot #{{ $liveAuction->product_id }}
                                    </span>
                                </div>
                                <h2 class="font-heading text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">
                                    {{ $liveAuction->product?->name ?? 'Grade A+ Farm Lot' }}
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-500 line-clamp-2">
                                    {{ $liveAuction->product?->description ? Str::limit($liveAuction->product->description, 120) : 'Fresh farm harvest bulk allocation' }} • Unit: {{ $liveAuction->product?->unit_type ?? 'kg' }}
                                </p>
                            </div>
                        </div>

                        <!-- Verified Tags -->
                        <div class="flex sm:flex-col items-end gap-1.5 shrink-0">
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-mono text-xs font-bold shadow-2xs">
                                <span class="material-symbols-outlined text-[15px] text-emerald-600">verified</span>
                                GI CERTIFIED
                            </div>
                            <span class="font-mono text-[11px] text-slate-400">Cold Chain Pre-Cooled</span>
                        </div>
                    </div>

                    <!-- Agronomic Specs Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 text-xs">
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Harvest Date</span>
                            <span class="font-heading font-bold text-slate-900 mt-0.5">{{ $liveAuction->product?->harvest_date ? \Carbon\Carbon::parse($liveAuction->product->harvest_date)->format('M d, Y') : 'Fresh Harvest' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Shelf-Life</span>
                            <span class="font-heading font-bold text-slate-900 mt-0.5">{{ $liveAuction->product?->expiry_days ?? 7 }} Days Window</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Stock Allocated</span>
                            <span class="font-heading font-bold text-slate-900 mt-0.5">{{ $liveAuction->product?->stock ?? 100 }} {{ $liveAuction->product?->unit_type ?? 'kg' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Inspection Grade</span>
                            <span class="font-heading font-bold text-slate-900 mt-0.5">APMC Grade A+ Verified</span>
                        </div>
                    </div>

                    <!-- HERO BIDDING METRICS - 4 BALANCED TILES (2x2 GRID) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- TILE 1: CURRENT HIGHEST LEADING BID -->
                        <div class="bg-gradient-to-br from-amber-50/60 via-slate-50 to-slate-50/80 p-5 rounded-2xl flex flex-col justify-between border border-amber-200/80 shadow-xs relative overflow-hidden">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-xs uppercase tracking-wider text-slate-500 font-bold">
                                    Current Highest Leading Bid
                                </span>
                                @if($isReserveMet)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold uppercase">
                                        <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                        RESERVE MET
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 font-mono text-[10px] font-bold uppercase">
                                        <span class="material-symbols-outlined text-[13px]">info</span>
                                        RESERVE NOT MET
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-baseline gap-2 my-1">
                                <span class="font-mono text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                                    ₹{{ number_format($highestBid, 2) }}
                                </span>
                                <span class="text-xs text-slate-500 font-medium">
                                    / Base Lot
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-3 mt-2 border-t border-slate-200/80 text-slate-500">
                                <span>Starting: <strong class="font-mono text-slate-900">₹{{ number_format($liveAuction->starting_price, 2) }}</strong></span>
                                <span class="{{ $isReserveMet ? 'text-emerald-700 font-semibold' : 'text-amber-700 font-semibold' }}">
                                    +₹{{ number_format($liveAuction->minimum_increment, 0) }} step
                                </span>
                            </div>
                        </div>

                        <!-- TILE 2: TIME REMAINING / CLOCK -->
                        <div class="bg-slate-50 p-5 rounded-2xl flex flex-col justify-between border border-slate-200/80 shadow-2xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs uppercase text-slate-500 font-bold">Time Remaining</span>
                                <span class="material-symbols-outlined text-[18px] text-amber-600">timer</span>
                            </div>
                            <div class="my-1">
                                <div class="font-mono text-2xl sm:text-3xl font-extrabold text-amber-700 tracking-tight" x-text="timerText">
                                    00:00:00
                                </div>
                                <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden mt-2.5">
                                    <div class="bg-amber-500 h-full rounded-full transition-all duration-1000" :style="'width: ' + progressPercent + '%'"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-3 mt-1 border-t border-slate-200/80 text-slate-500">
                                <span>Closes: <strong class="font-mono text-slate-800">{{ $liveAuction->ends_at->format('M d, h:i A') }}</strong></span>
                                <span class="font-mono text-[10px] text-amber-700 font-semibold">+2m Soft Close</span>
                            </div>
                        </div>

                        <!-- TILE 3: RESERVE SPEC & RULES -->
                        <div class="bg-slate-50 p-5 rounded-2xl flex flex-col justify-between border border-slate-200/80 shadow-2xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs uppercase text-slate-500 font-bold">Reserve Spec</span>
                                <span class="material-symbols-outlined text-[18px] text-slate-500">gavel</span>
                            </div>
                            <div class="my-1">
                                <div class="font-mono text-2xl font-extrabold text-slate-900">
                                    ₹{{ number_format($liveAuction->reserve_price ?? $liveAuction->starting_price, 2) }}
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    Min Increment: <span class="font-mono font-bold text-slate-800">₹{{ number_format($liveAuction->minimum_increment, 0) }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-3 mt-1 border-t border-slate-200/80 text-slate-500">
                                <span>Policy: <strong class="text-slate-800">{{ $liveAuction->reserve_price ? 'Floor Protected' : 'Open Spot Clearance' }}</strong></span>
                                <span class="font-mono text-[10px] text-emerald-700 font-bold">APMC Enforced</span>
                            </div>
                        </div>

                        <!-- TILE 4: PARTICIPATION TELEMETRY -->
                        <div class="bg-slate-50 p-5 rounded-2xl flex flex-col justify-between border border-slate-200/80 shadow-2xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs uppercase text-slate-500 font-bold">Participation</span>
                                <span class="material-symbols-outlined text-[18px] text-amber-600">groups</span>
                            </div>
                            <div class="my-1">
                                <div class="font-mono text-2xl font-extrabold text-slate-900">
                                    {{ $bidsCount }} Bids Placed
                                </div>
                                <div class="text-xs text-emerald-700 font-semibold mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">shield</span>
                                    100% Escrow Funded Network
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-3 mt-1 border-t border-slate-200/80 text-slate-500">
                                <span>Velocity: <strong class="font-mono text-slate-800">Active Live Floor</strong></span>
                                <span class="font-mono text-[10px] text-slate-400">T+1 Settlement</span>
                            </div>
                        </div>

                    </div>

                    <!-- Real-Time Bid Activity Stream -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h3 class="font-heading text-base font-bold text-slate-900">
                                    Real-Time Bid Activity Stream
                                </h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono text-[10px] uppercase font-bold">
                                    ANONYMIZED APMC PROTOCOL
                                </span>
                            </div>
                            <span class="font-mono text-[11px] text-slate-400">Auto-updating live</span>
                        </div>

                        <div class="overflow-x-auto bg-white rounded-2xl border border-slate-200/80">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 font-mono text-[10px] text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                                    <tr>
                                        <th class="py-3 px-4">Bidder Hash</th>
                                        <th class="py-3 px-4">Entity Category / Node</th>
                                        <th class="py-3 px-4">Placed Bid</th>
                                        <th class="py-3 px-4">Timestamp</th>
                                        <th class="py-3 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-900">
                                    @forelse($bids as $index => $bid)
                                        <tr class="{{ $index === 0 ? 'bg-amber-50/25 font-semibold' : 'hover:bg-slate-50/70' }} transition-colors">
                                            <td class="py-3.5 px-4 font-mono font-bold flex items-center gap-2">
                                                @if($index === 0)
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                @endif
                                                Bidder #***{{ substr(md5($bid->user_id), 0, 4) }}
                                            </td>
                                            <td class="py-3.5 px-4 text-slate-500">Verified APMC Wholesale Node</td>
                                            <td class="py-3.5 px-4 font-mono text-sm font-bold text-slate-900">₹{{ number_format($bid->amount, 2) }}</td>
                                            <td class="py-3.5 px-4 font-mono text-slate-500">{{ $bid->created_at ? $bid->created_at->format('h:i:s A') : 'Now' }}</td>
                                            <td class="py-3.5 px-4 text-right">
                                                @if($index === 0)
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold uppercase inline-flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[12px]">verified</span> Leading Bid
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 font-mono text-[10px] bg-slate-100 px-2 py-0.5 rounded">OUTBID</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-slate-400 font-mono text-xs">
                                                Awaiting opening bid from verified marketplace buyers...
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- AUCTION GUARDRAILS NOTIFICATION BAR -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] text-amber-600 mt-0.5">policy</span>
                            <div class="space-y-0.5">
                                <div class="text-xs font-heading font-bold text-slate-900">
                                    Seller Guardrail Policy: {{ $bidsCount > 0 ? 'Binding Bids Active' : 'Zero Bids Recorded' }}
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    @if($bidsCount > 0)
                                        This lot cannot be cancelled as active binding bids exist. Upon timer expiry, the lot will automatically assign to the highest bidder with escrow funding.
                                    @else
                                        Cancellation is currently permitted with zero fee or trust penalty since no bids have been submitted yet.
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($bidsCount === 0)
                            <form method="POST" action="{{ route('seller.auctions.cancel', $liveAuction) }}" onsubmit="return confirm('Confirm cancellation of this live auction with 0 bids?');" class="shrink-0">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-heading text-xs font-bold transition-all shadow-2xs">
                                    Cancel Lot
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- COLUMN B: 4 Cols - DOSSIER & SUMMARY -->
            <div class="lg:col-span-4 flex flex-col gap-6 sticky top-24">

                <!-- Lot Information Summary -->
                <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 flex flex-col gap-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
                        <div>
                            <span class="font-mono text-[10px] font-bold text-amber-700 uppercase tracking-wider">AUCTION DETAILS</span>
                            <h3 class="font-heading text-base font-bold text-slate-900">Consignment Metadata</h3>
                        </div>
                        <a href="{{ route('seller.auctions.show', $liveAuction) }}" class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100/90 text-amber-900 font-bold text-xs border border-amber-200/70 shadow-2xs hover:shadow-xs transition-all">
                            <span>View Dossier</span>
                            <span class="material-symbols-outlined text-[14px] text-amber-600 transition-transform duration-200 ease-out group-hover:translate-x-1">arrow_forward</span>
                        </a>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Scheduled Start</span>
                            <span class="font-mono font-bold text-slate-900">{{ $liveAuction->starts_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Scheduled End</span>
                            <span class="font-mono font-bold text-slate-900">{{ $liveAuction->ends_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Minimum Increment</span>
                            <span class="font-mono font-bold text-slate-900">₹{{ number_format($liveAuction->minimum_increment, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Reserve Status</span>
                            <span class="font-bold {{ $isReserveMet ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $isReserveMet ? 'Reserve Met' : 'Reserve Not Met' }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('seller.auctions.index') }}" class="w-full h-11 mt-1 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200/80 font-heading text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-2xs">
                        <span class="material-symbols-outlined text-[18px] text-slate-500">table_rows</span>
                        <span>View All Auctions Registry</span>
                    </a>
                </div>

                <!-- APMC Trust & Escrow Assurance Box -->
                <div class="bg-gradient-to-br from-slate-50 via-amber-50/10 to-slate-50 rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-3">
                    <div class="flex items-center gap-2 text-slate-900 font-heading font-bold text-xs uppercase tracking-wider">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">verified_user</span>
                        <span>APMC Escrow Protections</span>
                    </div>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px] text-emerald-600">check</span>
                            <span>100% pre-funded buyer trade escrow</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px] text-emerald-600">check</span>
                            <span>+2m Anti-sniping soft close extension</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px] text-emerald-600">check</span>
                            <span>Automated T+1 NEFT bank remittance</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px] text-emerald-600">check</span>
                            <span>Direct farm-to-merchant invoice allocation</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    @endif
</div>
@endsection
