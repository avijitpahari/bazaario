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
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    secondsLeft: {{ $liveAuction ? max(0, now()->diffInSeconds($liveAuction->ends_at, false)) : 0 }},
    timerText: '00:00:00',
    init() {
        this.updateTimer();
        setInterval(() => {
            if (this.secondsLeft > 0) {
                this.secondsLeft--;
                this.updateTimer();
            } else {
                this.timerText = '00:00:00 - CLOSED';
            }
        }, 1000);
    },
    updateTimer() {
        const hrs = Math.floor(this.secondsLeft / 3600);
        const mins = Math.floor((this.secondsLeft % 3600) / 60);
        const secs = this.secondsLeft % 60;
        this.timerText = String(hrs).padStart(2, '0') + ':' + String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }
}">

    <!-- BREADCRUMB & METRIC STATUS BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4 mb-2">
        <div class="flex items-center gap-2 text-xs font-mono text-brand-muted">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-primary transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('seller.auctions.index') }}" class="hover:text-primary transition-colors">Auctions</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Wholesale Live Terminal</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-white border border-[#E2DFD7]/60 rounded-[8px] font-mono text-[11px] uppercase text-primary font-semibold shadow-sm">
                <span class="w-2 h-2 rounded-full bg-brand-green animate-pulse"></span>
                APMC SPOT TICKER CONNECTED
            </span>
            <span class="font-mono text-xs text-brand-muted bg-surface-container-high px-2.5 py-1 rounded-[8px]">
                SELLER ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    <!-- WORKSPACE HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-[#E2DFD7]/60">
        <div class="space-y-1.5 max-w-3xl">
            <div class="flex items-center gap-3">
                <h1 class="font-heading text-2xl lg:text-3xl tracking-tight text-primary font-bold">
                    Wholesale Bidding Terminal
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-secondary-container text-on-secondary-container font-mono text-xs font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container"></span>
                    {{ $liveAuction ? '1 Active Lot' : 'No Live Lots' }}
                </span>
            </div>
            <p class="text-sm text-brand-muted leading-relaxed">
                Live spot-market auctions, real-time bidder telemetry, and verified bulk agricultural allocations for {{ $profile?->shop_name ?? 'My Farm' }}.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.auctions.index') }}" class="h-11 px-4 rounded-[12px] bg-white border border-[#E2DFD7]/60 text-primary font-heading text-xs font-bold shadow-sm hover:bg-surface-container transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">table_rows</span>
                <span>Registry Table</span>
            </a>
            <a href="{{ route('seller.auctions.create') }}" class="h-11 px-5 rounded-[12px] bg-brand-amber text-primary font-heading text-xs font-bold shadow-sm hover:brightness-105 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Create Auction</span>
            </a>
        </div>
    </div>

    @if(!$liveAuction)
        <!-- Empty Live State -->
        <div class="mt-8 bg-white rounded-[14px] p-12 text-center border border-[#E2DFD7]/60 shadow-sm flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-brand-amber/20 flex items-center justify-center text-brand-amber mb-4">
                <span class="material-symbols-outlined text-[32px]">gavel</span>
            </div>
            <h2 class="font-heading text-xl font-bold text-primary">No Active Live Auctions Currently</h2>
            <p class="text-sm text-brand-muted max-w-md mt-1 mb-6">There are currently no live bidding terminals broadcasting from your farm catalog. Create a scheduled or instant wholesale auction to begin receiving live trade orders.</p>
            <a href="{{ route('seller.auctions.create') }}" class="h-11 px-6 rounded-[12px] bg-brand-amber text-primary font-heading text-xs font-bold shadow-md hover:brightness-105 transition-all inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Schedule a Lot Now</span>
            </a>
        </div>
    @else
        <!-- DUAL-PANE WORKSPACE: LEFT 62% (8 Cols) / RIGHT 38% (4 Cols) -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- COLUMN A: 62% (8 Cols) - LIVE TERMINAL & TELEMETRY -->
            <div class="lg:col-span-8 flex flex-col gap-6">

                <!-- MAIN LIVE AUCTION CARD -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-brand-amber via-secondary to-brand-green"></div>

                    <!-- Header Block of Lot -->
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-mono text-xs font-bold bg-surface-container px-2.5 py-0.5 rounded text-primary">
                                    LOT #AUC-{{ str_pad($liveAuction->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-error-container text-on-error-container font-mono text-xs font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error animate-ping"></span>
                                    LIVE BIDDING
                                </span>
                                <span class="font-mono text-xs px-2 py-0.5 rounded bg-surface-container text-brand-muted uppercase">
                                    APMC Lot #{{ $liveAuction->product_id }}
                                </span>
                            </div>
                            <h2 class="font-heading text-xl md:text-2xl font-bold text-primary tracking-tight">
                                {{ $liveAuction->product?->name ?? 'Grade A+ Farm Lot' }}
                            </h2>
                            <p class="text-sm text-brand-muted">
                                {{ $liveAuction->product?->description ? Str::limit($liveAuction->product->description, 100) : 'Fresh farm harvest bulk allocation' }} • Unit: {{ $liveAuction->product?->unit_type ?? 'kg' }}
                            </p>
                        </div>

                        <!-- Verified Tags -->
                        <div class="flex sm:flex-col items-end gap-1.5 shrink-0">
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-[8px] bg-brand-green/20 text-brand-green font-mono text-xs font-semibold">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                GI CERTIFIED
                            </div>
                            <span class="font-mono text-[11px] text-brand-muted">Cold Chain Pre-Cooled</span>
                        </div>
                    </div>

                    <!-- Agronomic Specs Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-surface-container-low p-3.5 rounded-[12px] border border-[#E2DFD7]/40 text-xs">
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Harvest Date</span>
                            <span class="font-heading font-semibold text-primary mt-0.5">{{ $liveAuction->product?->harvest_date ? \Carbon\Carbon::parse($liveAuction->product->harvest_date)->format('M d, Y') : 'Fresh Harvest' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Shelf-Life</span>
                            <span class="font-heading font-semibold text-primary mt-0.5">{{ $liveAuction->product?->expiry_days ?? 7 }} Days Window</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Stock Allocated</span>
                            <span class="font-heading font-semibold text-primary mt-0.5">{{ $liveAuction->product?->stock ?? 100 }} {{ $liveAuction->product?->unit_type ?? 'units' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Inspection Grade</span>
                            <span class="font-heading font-semibold text-primary mt-0.5">APMC Grade A+ Verified</span>
                        </div>
                    </div>

                    <!-- HERO BIDDING METRICS - 4 TILES -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        
                        <!-- TILE 1: CURRENT HIGHEST BID (Dominant Anchor, 2 cols) -->
                        <div class="sm:col-span-2 xl:col-span-2 bg-surface-container p-5 rounded-[14px] flex flex-col justify-between relative overflow-hidden border border-[#E2DFD7]/60 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-xs uppercase tracking-wider text-brand-muted font-semibold">
                                    Current Highest Leading Bid
                                </span>
                                @if($isReserveMet)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-[6px] bg-brand-green/20 text-brand-green font-mono text-[11px] font-bold uppercase">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                        RESERVE MET
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-[6px] bg-brand-amber/20 text-brand-amber font-mono text-[11px] font-bold uppercase">
                                        <span class="material-symbols-outlined text-[14px]">info</span>
                                        RESERVE NOT MET (Target ₹{{ number_format($liveAuction->reserve_price ?? 0, 0) }})
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-baseline gap-3 my-1">
                                <span class="font-mono text-4xl lg:text-5xl font-bold tracking-tight text-primary">
                                    ₹{{ number_format($highestBid, 2) }}
                                </span>
                                <span class="text-xs text-brand-muted font-medium">
                                    / Base Lot
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-3 mt-1 border-t border-[#E2DFD7]/60 text-brand-muted">
                                <span>Starting Price: <strong class="font-mono text-primary">₹{{ number_format($liveAuction->starting_price, 2) }}</strong></span>
                                <span class="{{ $isReserveMet ? 'text-brand-green font-semibold' : 'text-brand-amber' }}">
                                    Min Increment: ₹{{ number_format($liveAuction->minimum_increment, 0) }}
                                </span>
                            </div>
                        </div>

                        <!-- TILE 2: COUNTDOWN TIMER -->
                        <div class="bg-surface-container-low p-4 rounded-[14px] flex flex-col justify-between border border-[#E2DFD7]/60">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs uppercase text-brand-muted font-medium">Time Remaining</span>
                                <span class="material-symbols-outlined text-[18px] text-brand-amber">timer</span>
                            </div>
                            <div class="my-2">
                                <div class="font-mono text-2xl font-bold text-secondary" x-text="timerText">
                                    00:00:00
                                </div>
                                <div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden mt-2">
                                    <div class="bg-brand-amber h-full rounded-full transition-all duration-1000" style="width: 75%;"></div>
                                </div>
                            </div>
                            <span class="font-mono text-[11px] text-brand-muted">Soft-close buffer active</span>
                        </div>

                        <!-- TILE 3: RULES & RESERVE -->
                        <div class="bg-surface-container-low p-4 rounded-[14px] flex flex-col justify-between border border-[#E2DFD7]/60">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs uppercase text-brand-muted font-medium">Reserve Spec</span>
                                <span class="material-symbols-outlined text-[18px] text-brand-muted">gavel</span>
                            </div>
                            <div class="my-1">
                                <span class="font-mono text-xl font-bold text-primary">₹{{ number_format($liveAuction->reserve_price ?? $liveAuction->starting_price, 2) }}</span>
                                <div class="text-xs text-brand-muted mt-1">
                                    Min Increment: <span class="font-mono font-bold text-primary">₹{{ number_format($liveAuction->minimum_increment, 0) }}</span>
                                </div>
                            </div>
                            <span class="font-mono text-[11px] text-brand-muted">Binding Trade Law</span>
                        </div>
                    </div>

                    <!-- Participation Telemetry Ribbon (TILE 4) -->
                    <div class="flex flex-wrap items-center justify-between gap-4 p-3.5 bg-surface-container-low rounded-[12px] border border-[#E2DFD7]/40">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-primary shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">groups</span>
                            </div>
                            <div>
                                <div class="text-sm font-heading font-bold text-primary">
                                    {{ $bidsCount }} Bids Placed by Verified Wholesale Network
                                </div>
                                <div class="text-xs text-brand-muted">
                                    100% of participants hold verified pre-funded trade escrow guarantees
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs px-2.5 py-1 rounded bg-white text-primary font-medium shadow-sm">
                                Velocity: Active Pulse
                            </span>
                        </div>
                    </div>

                    <!-- Real-Time Bid Activity Stream -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h3 class="font-heading text-base font-bold text-primary">
                                    Real-Time Bid Activity Stream
                                </h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-surface-container text-brand-muted font-mono text-[10px] uppercase">
                                    ANONYMIZED APMC PROTOCOL
                                </span>
                            </div>
                            <span class="font-mono text-[11px] text-brand-muted">Auto-updating live</span>
                        </div>

                        <div class="overflow-x-auto bg-surface-container-low rounded-[12px] border border-[#E2DFD7]/60">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-surface-container font-mono text-[10px] text-brand-muted uppercase tracking-wider border-b border-[#E2DFD7]/40">
                                    <tr>
                                        <th class="py-2.5 px-4">Bidder Hash</th>
                                        <th class="py-2.5 px-4">Entity Category / Node</th>
                                        <th class="py-2.5 px-4">Placed Bid</th>
                                        <th class="py-2.5 px-4">Timestamp</th>
                                        <th class="py-2.5 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E2DFD7]/40 text-primary">
                                    @forelse($bids as $index => $bid)
                                        <tr class="{{ $index === 0 ? 'bg-white font-semibold' : '' }}">
                                            <td class="py-3 px-4 font-mono font-bold flex items-center gap-2">
                                                @if($index === 0)
                                                    <span class="w-2 h-2 rounded-full bg-brand-green"></span>
                                                @endif
                                                Bidder #***{{ substr(md5($bid->user_id), 0, 4) }}
                                            </td>
                                            <td class="py-3 px-4 text-brand-muted">Verified APMC Wholesale Node</td>
                                            <td class="py-3 px-4 font-mono text-sm font-bold">₹{{ number_format($bid->amount, 2) }}</td>
                                            <td class="py-3 px-4 font-mono text-brand-muted">{{ $bid->created_at ? $bid->created_at->format('h:i:s A') : 'Now' }}</td>
                                            <td class="py-3 px-4 text-right">
                                                @if($index === 0)
                                                    <span class="px-2 py-0.5 rounded bg-secondary-container text-on-secondary-container font-mono text-[10px] font-bold uppercase">
                                                        Leading Bid
                                                    </span>
                                                @else
                                                    <span class="text-brand-muted font-mono text-[10px]">OUTBID</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-brand-muted font-mono text-xs">
                                                Awaiting opening bid from verified marketplace buyers...
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- AUCTION GUARDRAILS NOTIFICATION BAR -->
                    <div class="bg-surface-container-low p-4 rounded-[14px] border border-[#E2DFD7]/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] text-brand-amber mt-0.5">policy</span>
                            <div class="space-y-0.5">
                                <div class="text-xs font-heading font-bold text-primary">
                                    Seller Guardrail Policy: {{ $bidsCount > 0 ? 'Binding Bids Active' : 'Zero Bids Recorded' }}
                                </div>
                                <p class="text-[11px] text-brand-muted">
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
                                <button type="submit" class="px-3.5 py-1.5 rounded-[10px] bg-error-container text-on-error-container font-heading text-xs font-bold hover:brightness-95 transition-all">
                                    Cancel Lot
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- COLUMN B: 38% (4 Cols) - QUICK ACTIONS -->
            <div class="lg:col-span-4 flex flex-col gap-6">

                <!-- Lot Information Summary -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[#E2DFD7]/60">
                        <div>
                            <span class="font-mono text-[10px] font-bold text-secondary uppercase tracking-wider">AUCTION DETAILS</span>
                            <h3 class="font-heading text-base font-bold text-primary">Consignment Metadata</h3>
                        </div>
                        <a href="{{ route('seller.auctions.show', $liveAuction) }}" class="font-mono text-xs text-brand-amber hover:underline font-semibold">
                            Full Dossier →
                        </a>
                    </div>

                    <div class="space-y-2.5 font-mono text-xs">
                        <div class="flex items-center justify-between py-1.5 border-b border-[#E2DFD7]/40">
                            <span class="text-brand-muted">Scheduled Start</span>
                            <span class="font-bold text-primary">{{ $liveAuction->starts_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 border-b border-[#E2DFD7]/40">
                            <span class="text-brand-muted">Scheduled End</span>
                            <span class="font-bold text-primary">{{ $liveAuction->ends_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 border-b border-[#E2DFD7]/40">
                            <span class="text-brand-muted">Minimum Increment</span>
                            <span class="font-bold text-primary">₹{{ number_format($liveAuction->minimum_increment, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 border-b border-[#E2DFD7]/40">
                            <span class="text-brand-muted">Reserve Status</span>
                            <span class="font-bold {{ $isReserveMet ? 'text-brand-green' : 'text-brand-amber' }}">
                                {{ $isReserveMet ? 'Reserve Met' : 'Reserve Not Met' }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('seller.auctions.index') }}" class="w-full h-10 mt-2 rounded-[10px] bg-surface-container-low hover:bg-surface-container text-primary font-heading text-xs font-semibold transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">list</span>
                        <span>View All Auctions</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
