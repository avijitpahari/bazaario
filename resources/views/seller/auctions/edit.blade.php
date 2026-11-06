@extends('layouts.seller')

@section('title', 'Manage Auction Lot — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
    $bidsCount = isset($auction) && $auction->bids ? $auction->bids->count() : 0;
    $isReserveMet = isset($auction) ? $auction->isReserveMet() : false;

    // Helper for safe auction thumbnail resolution
    if (!function_exists('sellerEditAuctionThumb')) {
        function sellerEditAuctionThumb($product) {
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
    $thumb = isset($auction) ? sellerEditAuctionThumb($auction->product) : null;
@endphp

<div class="flex flex-col w-full pb-16 max-w-5xl mx-auto">

    <!-- BREADCRUMB & METRIC STATUS BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-3 mb-2">
        <div class="flex items-center gap-2 text-xs font-mono text-slate-500">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-amber-600 transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <a href="{{ route('seller.auctions.index') }}" class="hover:text-amber-600 transition-colors">Auctions</a>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <span class="text-slate-900 font-bold">Manage Lot #AUC-{{ isset($auction) ? str_pad($auction->id, 4, '0', STR_PAD_LEFT) : '---' }}</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200/70 rounded-xl font-mono text-[11px] uppercase text-emerald-800 font-semibold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                APMC SPOT TICKER CONNECTED
            </span>
        </div>
    </div>

    <!-- WORKSPACE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200/80 mb-6">
        <div>
            <h1 class="font-heading text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">
                Manage Auction Lot
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Inspect bidding floor status, review reserve guardrails, and execute administrative lot actions.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.auctions.index') }}" class="h-11 px-4 rounded-xl bg-white border border-slate-200/80 text-slate-700 font-heading text-xs font-semibold shadow-2xs hover:bg-slate-50 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>All Auctions</span>
            </a>
            @if(isset($auction) && $auction->status === 'live')
                <a href="{{ route('seller.auctions.live') }}" class="h-11 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold shadow-xs hover:shadow-md transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">stream</span>
                    <span>Open Live Terminal</span>
                </a>
            @endif
        </div>
    </div>

    @if(isset($auction))
        <div class="flex flex-col gap-6">

            <!-- MAIN LOT CARD -->
            <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xs border border-slate-200/80 flex flex-col gap-6">
                
                <!-- Lot Header Strip -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-5 pb-6 border-b border-slate-200/80">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-slate-50 shrink-0 border border-slate-200/80 shadow-2xs flex items-center justify-center p-2">
                            <img src="{{ $thumb }}" 
                                 alt="{{ $auction->product?->name ?? 'Produce' }}" 
                                 class="w-full h-full object-contain"
                                 onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-xs font-bold bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-lg text-slate-700">
                                    LOT #AUC-{{ str_pad($auction->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-lg font-mono text-[11px] font-bold uppercase {{ $auction->status === 'live' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : ($auction->status === 'ended' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                    {{ $auction->status }}
                                </span>
                                <span class="font-mono text-xs text-slate-500">
                                    SKU #{{ $auction->product_id }}
                                </span>
                            </div>
                            <h2 class="font-heading text-xl lg:text-2xl font-extrabold text-slate-900">
                                {{ $auction->product?->name ?? 'Consignment Lot' }}
                            </h2>
                            <p class="text-xs text-slate-500 font-mono">
                                Starts: {{ $auction->starts_at->format('M d, Y h:i A') }} • Ends: {{ $auction->ends_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('seller.auctions.show', $auction) }}" class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-heading font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            <span>Full Dossier</span>
                        </a>
                    </div>
                </div>

                <!-- 4 HERO KPI TILES -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs">
                        <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Starting Floor</span>
                        <div class="font-mono text-xl font-extrabold text-slate-900 mt-1">₹{{ number_format($auction->starting_price, 2) }}</div>
                        <span class="text-[11px] text-slate-500 font-mono">Min Increment: ₹{{ number_format($auction->minimum_increment, 0) }}</span>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs">
                        <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Secret Reserve</span>
                        <div class="font-mono text-xl font-extrabold text-slate-900 mt-1">₹{{ number_format($auction->reserve_price ?? $auction->starting_price, 2) }}</div>
                        <div class="mt-1">
                            @if($isReserveMet)
                                <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold inline-flex items-center gap-0.5">
                                    <span class="material-symbols-outlined text-[12px]">check_circle</span> Reserve Met
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 font-mono text-[10px] font-bold">Reserve Not Met</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs">
                        <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Current Leading Bid</span>
                        <div class="font-mono text-xl font-extrabold text-slate-900 mt-1">₹{{ number_format($auction->current_price, 2) }}</div>
                        <span class="text-[11px] text-slate-500 font-mono">{{ $bidsCount > 0 ? 'Active Top Offer' : 'Opening Floor' }}</span>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs">
                        <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Participation</span>
                        <div class="font-mono text-xl font-extrabold text-slate-900 mt-1">{{ $bidsCount }} Bids</div>
                        <span class="text-[11px] text-emerald-700 font-mono font-semibold">100% Escrow Funded</span>
                    </div>
                </div>

                <!-- CANCELLATION & POLICY GUARDRAIL SECTION -->
                <div class="p-6 rounded-2xl border transition-all {{ $bidsCount > 0 ? 'bg-amber-50/40 border-amber-200/80' : 'bg-slate-50 border-slate-200/80' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[24px] {{ $bidsCount > 0 ? 'text-amber-600' : 'text-slate-400' }} shrink-0 mt-0.5">
                                {{ $bidsCount > 0 ? 'lock' : 'cancel' }}
                            </span>
                            <div>
                                <h4 class="font-heading text-sm font-bold text-slate-900">
                                    {{ $bidsCount > 0 ? 'Active Binding Bids Locked (APMC Rule)' : 'Eligible for Consignment Cancellation' }}
                                </h4>
                                <p class="text-xs text-slate-500 mt-0.5 max-w-xl leading-relaxed">
                                    @if($bidsCount > 0)
                                        Under APMC wholesale spot-trading regulations, lots that have received verified buyer bids cannot be unilaterally cancelled. The highest valid bid will be awarded at auction close.
                                    @else
                                        No binding bids have been submitted for this lot yet. You may withdraw and cancel this wholesale consignment permanently from the spot exchange.
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($bidsCount === 0 && !in_array($auction->status, ['ended', 'cancelled']))
                            <form method="POST" action="{{ route('seller.auctions.cancel', $auction->id) }}" onsubmit="return confirm('Are you sure you want to permanently cancel this auction lot? This action cannot be reversed.');" class="shrink-0">
                                @csrf
                                <button type="submit" class="h-11 px-5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-heading text-xs font-bold transition-all shadow-2xs flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                    <span>Cancel Auction Lot</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    @else
        <div class="bg-white rounded-3xl p-10 text-center border border-slate-200/80 shadow-xs max-w-lg mx-auto">
            <span class="material-symbols-outlined text-[36px] text-slate-400 mb-2">error_outline</span>
            <h3 class="font-heading text-base font-bold text-slate-900">Auction Lot Not Found</h3>
            <p class="text-xs text-slate-500 mt-1 mb-4">The requested wholesale auction lot could not be located in your profile catalog.</p>
            <a href="{{ route('seller.auctions.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 text-slate-950 rounded-xl text-xs font-bold font-heading">
                Return to Auctions
            </a>
        </div>
    @endif

</div>
@endsection
