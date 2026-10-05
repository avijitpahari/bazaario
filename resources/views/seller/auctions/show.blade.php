@extends('layouts.seller')

@section('title', 'Auction Lot Inspector — Bazaario Seller Center')

@section('content')
@php
    $bidsCount = $auction->bids ? $auction->bids->count() : 0;
    $isReserveMet = $auction->isReserveMet();
@endphp

<div class="flex flex-col w-full pb-16">
    <div class="flex items-center gap-2 text-xs font-mono text-brand-muted mb-4">
        <a href="{{ route('seller.dashboard') }}" class="hover:text-primary transition-colors">Seller Center</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('seller.auctions.index') }}" class="hover:text-primary transition-colors">Auctions</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">Lot #AUC-{{ str_pad($auction->id, 4, '0', STR_PAD_LEFT) }}</span>
    </div>

    <div class="bg-white rounded-[14px] p-6 lg:p-8 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-[#E2DFD7]/60">
            <div>
                <span class="font-mono text-xs font-bold bg-surface-container px-2.5 py-0.5 rounded text-primary">
                    LOT #AUC-{{ str_pad($auction->id, 4, '0', STR_PAD_LEFT) }}
                </span>
                <h1 class="font-heading text-2xl font-bold text-primary mt-1">{{ $auction->product?->name }}</h1>
                <p class="text-xs text-brand-muted">Starts {{ $auction->starts_at->format('M d, Y h:i A') }} • Ends {{ $auction->ends_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-[8px] font-mono text-xs font-bold uppercase {{ $auction->status === 'live' ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container text-primary' }}">
                    Status: {{ $auction->status }}
                </span>
                @if($auction->status === 'live')
                    <a href="{{ route('seller.auctions.live') }}" class="h-10 px-4 rounded-[10px] bg-brand-amber text-primary text-xs font-heading font-bold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">stream</span>
                        <span>Open Live</span>
                    </a>
                @endif
                @if($bidsCount === 0 && !in_array($auction->status, ['ended', 'cancelled']))
                    <form method="POST" action="{{ route('seller.auctions.cancel', $auction) }}" onsubmit="return confirm('Cancel this auction lot?');" class="inline">
                        @csrf
                        <button type="submit" class="h-10 px-4 rounded-[10px] bg-error-container text-on-error-container text-xs font-heading font-bold hover:brightness-95 transition-all">
                            Cancel Lot
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Metrics Grid (4 Hero Tiles) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60">
                <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Starting Price</span>
                <div class="font-mono text-lg font-bold text-primary mt-1">₹{{ number_format($auction->starting_price, 2) }}</div>
                <span class="text-[10px] text-brand-muted font-mono">Min Inc: ₹{{ number_format($auction->minimum_increment, 0) }}</span>
            </div>
            <div class="p-4 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60">
                <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Reserve Price</span>
                <div class="font-mono text-lg font-bold text-primary mt-1">₹{{ number_format($auction->reserve_price ?? $auction->starting_price, 2) }}</div>
                <div class="mt-1">
                    @if($isReserveMet)
                        <span class="px-2 py-0.5 rounded bg-brand-green/20 text-brand-green font-mono text-[11px] font-bold">Reserve Met</span>
                    @else
                        <span class="px-2 py-0.5 rounded bg-brand-amber/20 text-brand-amber font-mono text-[11px] font-bold">Reserve Not Met</span>
                    @endif
                </div>
            </div>
            <div class="p-4 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60">
                <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Current Highest Bid</span>
                <div class="font-mono text-lg font-bold text-primary mt-1">₹{{ number_format($auction->current_price, 2) }}</div>
                <span class="text-[10px] text-brand-muted font-mono">{{ $bidsCount > 0 ? 'Active Leading Offer' : 'Opening Price' }}</span>
            </div>
            <div class="p-4 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60">
                <span class="font-mono text-[10px] uppercase text-brand-muted font-medium">Participation</span>
                <div class="font-mono text-lg font-bold text-primary mt-1">{{ $bidsCount }} Bids</div>
                <span class="text-[10px] text-brand-muted font-mono">Verified Bidders</span>
            </div>
        </div>

        <!-- Bid Log -->
        <div class="flex flex-col gap-3">
            <h3 class="font-heading text-sm font-bold text-primary">Consignment Bid History</h3>
            <div class="overflow-x-auto rounded-[12px] border border-[#E2DFD7]/60">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-container-low font-mono text-[10px] text-brand-muted uppercase">
                        <tr>
                            <th class="py-2.5 px-4">Bidder</th>
                            <th class="py-2.5 px-4">Amount</th>
                            <th class="py-2.5 px-4">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2DFD7]/40">
                        @forelse($auction->bids()->latest('id')->get() as $b)
                            <tr>
                                <td class="py-3 px-4 font-mono font-bold">Bidder #***{{ substr(md5($b->user_id), 0, 4) }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-primary">₹{{ number_format($b->amount, 2) }}</td>
                                <td class="py-3 px-4 font-mono text-brand-muted">{{ $b->created_at ? $b->created_at->format('M d, Y h:i:s A') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-brand-muted">No bids recorded on this lot.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
