@extends('layouts.seller')

@section('title', 'Wholesale Auctions Master Registry — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
    $currentStatus = $status ?? request('status', 'all');
@endphp

<div class="flex flex-col w-full pb-16">

    <!-- BREADCRUMB & METRIC STATUS BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4 mb-2">
        <div class="flex items-center gap-2 text-xs font-mono text-brand-muted">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-primary transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Auctions &amp; Wholesale Registry</span>
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

    <!-- WORKSPACE HEADER & ACTION BUTTONS -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-[#E2DFD7]/60">
        <div class="space-y-1.5 max-w-3xl">
            <div class="flex items-center gap-3">
                <h1 class="font-heading text-2xl lg:text-3xl tracking-tight text-primary font-bold">
                    Auctions &amp; Wholesale Bidding
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-secondary-container text-on-secondary-container font-mono text-xs font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container"></span>
                    {{ $counts['live'] ?? 0 }} Live Lots
                </span>
            </div>
            <p class="text-sm text-brand-muted leading-relaxed">
                Live spot-market auctions, real-time bidder telemetry, and verified bulk agricultural allocations for {{ $profile?->shop_name ?? 'My Farm' }}.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('seller.auctions.live') }}" class="h-11 px-5 rounded-[12px] bg-primary text-white font-heading text-xs font-bold shadow-sm hover:bg-slate-800 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-brand-amber">stream</span>
                <span>Open Live Terminal</span>
            </a>
            <a href="{{ route('seller.auctions.create') }}" class="h-11 px-5 rounded-[12px] bg-brand-amber text-primary font-heading text-xs font-bold shadow-sm hover:brightness-105 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Create New Auction</span>
            </a>
        </div>
    </div>

    <!-- MASTER REGISTRY TABLE CARD -->
    <div class="mt-6 bg-white rounded-[14px] shadow-sm border border-[#E2DFD7]/60 overflow-hidden flex flex-col">
        
        <!-- Table Header & Status Filter Pills -->
        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-[#E2DFD7]/60">
            <div>
                <h3 class="font-heading text-lg font-bold text-primary">All Auctions Master Registry</h3>
                <p class="text-xs text-brand-muted">Historical &amp; real-time performance of your bulk commodity lots</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-1.5 bg-surface-container-low p-1 rounded-[12px] border border-[#E2DFD7]/40 text-xs">
                <a href="{{ route('seller.auctions.index') }}" class="px-3 py-1.5 rounded-[8px] font-heading {{ $currentStatus === 'all' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    All ({{ $counts['all'] ?? 0 }})
                </a>
                <a href="{{ route('seller.auctions.index', ['status' => 'scheduled']) }}" class="px-3 py-1.5 rounded-[8px] font-heading {{ $currentStatus === 'scheduled' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    Scheduled ({{ $counts['scheduled'] ?? 0 }})
                </a>
                <a href="{{ route('seller.auctions.index', ['status' => 'live']) }}" class="px-3 py-1.5 rounded-[8px] font-heading flex items-center gap-1.5 {{ $currentStatus === 'live' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-green animate-pulse"></span>
                    Live ({{ $counts['live'] ?? 0 }})
                </a>
                <a href="{{ route('seller.auctions.index', ['status' => 'ended']) }}" class="px-3 py-1.5 rounded-[8px] font-heading {{ $currentStatus === 'ended' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    Completed ({{ $counts['ended'] ?? 0 }})
                </a>
                <a href="{{ route('seller.auctions.index', ['status' => 'cancelled']) }}" class="px-3 py-1.5 rounded-[8px] font-heading {{ $currentStatus === 'cancelled' ? 'bg-white text-primary font-bold shadow-sm' : 'text-brand-muted hover:text-primary' }}">
                    Cancelled ({{ $counts['cancelled'] ?? 0 }})
                </a>
            </div>
        </div>

        <!-- Registry Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low font-mono text-[11px] text-brand-muted uppercase tracking-wider border-b border-[#E2DFD7]/60">
                    <tr>
                        <th class="py-3.5 px-5">Auction ID</th>
                        <th class="py-3.5 px-5">Product Lot &amp; Spec</th>
                        <th class="py-3.5 px-5">Starting Price</th>
                        <th class="py-3.5 px-5">Current / Final Bid</th>
                        <th class="py-3.5 px-5">Bid Count</th>
                        <th class="py-3.5 px-5">Timing / Status</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2DFD7]/60 text-primary">
                    @forelse($auctions ?? [] as $auc)
                        @php
                            $bidsCount = $auc->bids ? $auc->bids->count() : 0;
                            $isReserveMet = $auc->isReserveMet();
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition-colors {{ $auc->isLive() ? 'bg-brand-amber/5' : '' }}">
                            <td class="py-4 px-5">
                                <span class="font-mono font-bold text-primary">#AUC-{{ str_pad($auc->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="py-4 px-5">
                                <div class="font-heading font-bold text-primary">{{ $auc->product?->name ?? 'Agricultural Lot' }}</div>
                                <div class="text-xs text-brand-muted">{{ $auc->product?->category?->name ?? 'Farm Produce' }} • Unit: {{ $auc->product?->unit_type ?? 'lot' }}</div>
                            </td>
                            <td class="py-4 px-5 font-mono text-brand-muted">
                                ₹{{ number_format($auc->starting_price, 2) }}
                            </td>
                            <td class="py-4 px-5 font-mono">
                                <div class="font-bold text-primary">₹{{ number_format($auc->current_price, 2) }}</div>
                                @if($auc->reserve_price)
                                    @if($isReserveMet)
                                        <span class="text-[11px] text-brand-green font-semibold">Reserve Met</span>
                                    @else
                                        <span class="text-[11px] text-brand-muted">Target: ₹{{ number_format($auc->reserve_price, 0) }}</span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-4 px-5 font-mono">
                                <span class="font-bold text-primary">{{ $bidsCount }}</span>
                                <span class="text-xs text-brand-muted block">bids placed</span>
                            </td>
                            <td class="py-4 px-5">
                                @if($auc->status === 'live')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-secondary-container text-on-secondary-container font-mono text-xs font-bold uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container animate-pulse"></span>
                                        LIVE BIDDING
                                    </span>
                                @elseif($auc->status === 'scheduled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-surface-container-high text-primary font-mono text-xs font-semibold uppercase">
                                        Scheduled
                                    </span>
                                @elseif($auc->status === 'ended')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-brand-green/20 text-brand-green font-mono text-xs font-bold uppercase">
                                        Completed
                                    </span>
                                @elseif($auc->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-error-container text-on-error-container font-mono text-xs font-bold uppercase">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if($auc->status === 'live')
                                        <a href="{{ route('seller.auctions.live') }}" class="px-3 py-1.5 rounded-[10px] bg-brand-amber text-primary font-heading text-xs font-bold hover:brightness-105">
                                            Monitor Live
                                        </a>
                                    @endif
                                    <a href="{{ route('seller.auctions.show', $auc) }}" class="px-3 py-1.5 rounded-[10px] bg-surface-container hover:bg-surface-container-high text-xs font-medium text-primary">
                                        Details
                                    </a>
                                    @if($bidsCount === 0 && !in_array($auc->status, ['ended', 'cancelled']))
                                        <form method="POST" action="{{ route('seller.auctions.cancel', $auc) }}" onsubmit="return confirm('Cancel this auction lot? This action cannot be undone.');" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-[10px] text-error hover:bg-error-container hover:text-on-error-container text-xs font-medium transition-colors">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-brand-muted">
                                <span class="material-symbols-outlined text-[36px] text-brand-muted/40 mb-2">gavel</span>
                                <p class="font-heading font-medium text-primary">No auctions found matching this filter</p>
                                <p class="text-xs mt-1">Create a new wholesale lot to begin spot-market bidding.</p>
                                <a href="{{ route('seller.auctions.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-[10px] bg-brand-amber text-primary font-heading text-xs font-bold">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    <span>Create Auction</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($auctions) && method_exists($auctions, 'links'))
            <div class="p-4 bg-surface-container-low border-t border-[#E2DFD7]/60">
                {{ $auctions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
