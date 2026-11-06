@extends('layouts.seller')

@section('title', 'Auction Lot Inspector — Bazaario Seller Center')

@section('content')
@php
    $bidsCount = $auction->bids ? $auction->bids->count() : 0;
    $isReserveMet = $auction->isReserveMet();

    // Helper for safe auction thumbnail resolution
    if (!function_exists('sellerShowAuctionThumb')) {
        function sellerShowAuctionThumb($product) {
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
    $thumb = sellerShowAuctionThumb($auction->product);
@endphp

<div class="flex flex-col w-full pb-16">
    <!-- Breadcrumb Nav -->
    <div class="flex items-center gap-2 text-xs font-mono text-slate-500 mb-4 py-2">
        <a href="{{ route('seller.dashboard') }}" class="hover:text-amber-600 transition-colors">Seller Center</a>
        <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
        <a href="{{ route('seller.auctions.index') }}" class="hover:text-amber-600 transition-colors">Auctions</a>
        <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
        <span class="text-slate-900 font-bold">Lot #AUC-{{ str_pad($auction->id, 4, '0', STR_PAD_LEFT) }}</span>
    </div>

    <!-- Main Dossier Card -->
    <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-xs border border-slate-200/80 flex flex-col gap-6">
        
        <!-- Header Strip -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-5 pb-6 border-b border-slate-200/80">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-slate-50 shrink-0 border border-slate-200/80 shadow-2xs flex items-center justify-center p-2">
                    <img src="{{ $thumb }}" 
                         alt="{{ $auction->product?->name ?? 'Produce' }}" 
                         class="w-full h-full object-contain"
                         onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-lg text-slate-700">
                            LOT #AUC-{{ str_pad($auction->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-lg font-mono text-[11px] font-bold uppercase {{ $auction->status === 'live' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                            {{ $auction->status }}
                        </span>
                    </div>
                    <h1 class="font-heading text-2xl font-extrabold text-slate-900 mt-1.5">{{ $auction->product?->name }}</h1>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">
                        Starts: {{ $auction->starts_at->format('M d, Y h:i A') }} • Ends: {{ $auction->ends_at->format('M d, Y h:i A') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($auction->status === 'live')
                    <a href="{{ route('seller.auctions.live') }}" class="h-11 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-xs font-heading font-bold flex items-center gap-2 shadow-xs hover:shadow-md transition-all">
                        <span class="material-symbols-outlined text-[18px]">stream</span>
                        <span>Open Live Terminal</span>
                    </a>
                @endif
                @if($bidsCount === 0 && !in_array($auction->status, ['ended', 'cancelled']))
                    <form method="POST" action="{{ route('seller.auctions.cancel', $auction) }}" onsubmit="return confirm('Cancel this auction lot? This action is permanent.');" class="inline">
                        @csrf
                        <button type="submit" class="h-11 px-4 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-heading font-bold transition-all shadow-2xs">
                            Cancel Lot
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Metrics Grid (4 Hero Tiles) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs">
                <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Starting Price</span>
                <div class="font-mono text-xl font-extrabold text-slate-900 mt-1">₹{{ number_format($auction->starting_price, 2) }}</div>
                <span class="text-[11px] text-slate-500 font-mono">Min Inc: ₹{{ number_format($auction->minimum_increment, 0) }}</span>
            </div>
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs">
                <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Reserve Price</span>
                <div class="font-mono text-xl font-extrabold text-slate-900 mt-1">₹{{ number_format($auction->reserve_price ?? $auction->starting_price, 2) }}</div>
                <div class="mt-1">
                    @if($isReserveMet)
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[11px] font-bold inline-flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[13px]">check_circle</span> Reserve Met
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 font-mono text-[11px] font-bold">Reserve Not Met</span>
                    @endif
                </div>
            </div>
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs">
                <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Current Highest Bid</span>
                <div class="font-mono text-xl font-extrabold text-slate-900 mt-1">₹{{ number_format($auction->current_price, 2) }}</div>
                <span class="text-[11px] text-slate-500 font-mono">{{ $bidsCount > 0 ? 'Active Leading Offer' : 'Opening Price' }}</span>
            </div>
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs">
                <span class="font-mono text-[10px] uppercase text-slate-400 font-bold">Participation</span>
                <div class="font-mono text-xl font-extrabold text-slate-900 mt-1">{{ $bidsCount }} Bids</div>
                <span class="text-[11px] text-emerald-700 font-mono font-semibold">100% Escrow Funded</span>
            </div>
        </div>

        <!-- Bid Log History Stream -->
        <div class="flex flex-col gap-3 pt-2">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-heading text-base font-bold text-slate-900">Consignment Bid History</h3>
                    <p class="text-xs text-slate-500">Chronological cryptographic bid sequence submitted by verified network buyers</p>
                </div>
                <span class="font-mono text-xs px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 font-medium">
                    Total: {{ $bidsCount }} bids
                </span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200/80">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 font-mono text-[10px] text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th class="py-3 px-4">Bidder Token</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Timestamp</th>
                            <th class="py-3 px-4 text-right">Verification</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($auction->bids()->latest('id')->get() as $idx => $b)
                            <tr class="hover:bg-slate-50/70 transition-colors {{ $idx === 0 ? 'bg-amber-50/20' : '' }}">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 flex items-center gap-2">
                                    @if($idx === 0)
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    @endif
                                    Bidder #***{{ substr(md5($b->user_id), 0, 4) }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-sm">₹{{ number_format($b->amount, 2) }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-500">{{ $b->created_at ? $b->created_at->format('M d, Y h:i:s A') : 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($idx === 0)
                                        <span class="px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold uppercase inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">verified</span> Leader
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-mono text-[10px]">Outbid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 font-mono text-xs">
                                    No bids submitted for this consignment lot yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
