@extends('layouts.admin')

@section('title', 'Live Auction Monitoring Desk')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-space-sm">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Live Auction Terminal</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 font-mono text-[11px] font-bold border border-emerald-200/60 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span>
                    ENGINE V4.2 ACTIVE
                </span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500 mt-1">Real-time bidding desk, anti-sniping velocity engine, and live bid moderation controls.</p>
        </div>
        <!-- Right Header Controls -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.auctions.index') }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ !request('status') ? 'bg-[#0F172A] text-white border-[#0F172A]' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                All Lots ({{ $stats['total'] }})
            </a>
            <a href="{{ route('admin.auctions.index', ['status' => 'live']) }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ request('status') === 'live' ? 'bg-amber-500 text-slate-950 font-bold border-amber-500' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                Live Now ({{ $stats['live'] }})
            </a>
            <a href="{{ route('admin.auctions.index', ['status' => 'ended']) }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ request('status') === 'ended' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                Hammer Down ({{ $stats['ended'] }})
            </a>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Active Live Auctions</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-emerald-700">{{ $stats['live'] }}</span>
            </div>
            <span class="text-xs text-emerald-700 font-bold">Anti-sniping active</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Hammered / Ended</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">{{ $stats['ended'] }}</span>
            </div>
            <span class="text-xs text-slate-400">Awarded to winning bidders</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Total Platform Lots</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-amber-600">{{ $stats['total'] }}</span>
            </div>
            <span class="text-xs text-slate-400">Agricultural & Artisan Lots</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Total Bids Placed</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-indigo-700">{{ $stats['total_bids'] }}</span>
            </div>
            <span class="text-xs text-indigo-700 font-bold">Verified escrow bids</span>
        </div>
    </div>

    @php
        $featured = $auctions->where('status', 'live')->first() ?? $auctions->first();
    @endphp

    @if($featured)
        <!-- Featured Active Lot -->
        <div class="w-full bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
            <!-- Room Status Stripe -->
            <div class="px-5 py-2.5 bg-slate-50 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs px-2.5 py-0.5 rounded bg-[#0F172A] text-white font-bold">#AUC-{{ $featured->id }}</span>
                    <span class="font-mono text-[11px] text-slate-500 font-semibold uppercase">FEATURED MARKETPLACE LOT</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($featured->status === 'live')
                        <div class="flex items-center gap-1.5 text-emerald-700 font-mono font-bold text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>LIVE AUCTION SYNCHRONIZED</span>
                        </div>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-mono text-[10px] font-bold">STATUS: {{ strtoupper($featured->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="p-5 flex flex-col gap-4">
                <div class="flex flex-col md:flex-row gap-4 justify-between items-start">
                    <div class="flex gap-3.5 items-start">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-amber-50 border border-amber-100 flex items-center justify-center text-4xl shrink-0">
                            🏺
                        </div>
                        <div class="flex flex-col min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900">VERIFIED LOT</span>
                                <span class="font-mono text-[10px] text-slate-500 uppercase">{{ $featured->product->category->name ?? 'Marketplace Consignment' }}</span>
                            </div>
                            <h2 class="text-base font-bold text-[#0F172A] mt-1">{{ $featured->product->name ?? 'Consignment Lot #' . $featured->id }}</h2>
                            <div class="text-xs text-slate-500 mt-1">
                                Seller: <strong class="text-[#0F172A]">{{ $featured->seller->name ?? 'Producer' }}</strong> • 
                                Ends: <span class="font-mono text-slate-700">{{ $featured->ends_at ? $featured->ends_at->format('M d, Y H:i') : 'Open Lot' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Price & Actions Engine -->
                    <div class="w-full md:w-auto bg-[#0F172A] text-white rounded-xl p-4 flex flex-col items-center justify-center min-w-[200px] shadow-sm">
                        <div class="flex items-center justify-between w-full pb-1 border-b border-slate-800 text-[10px] text-slate-400 font-mono">
                            <span>CURRENT HIGHEST BID</span>
                            <span class="bg-[#F5A623] text-slate-950 px-1.5 py-0.2 rounded font-bold">{{ strtoupper($featured->status) }}</span>
                        </div>
                        <div class="text-3xl font-mono font-bold text-[#F5A623] my-1">
                            ₹{{ number_format($featured->current_price ?? $featured->starting_price, 2) }}
                        </div>
                        <div class="flex items-center justify-between w-full text-[10px] text-slate-400 font-mono">
                            <span>Reserve: ₹{{ number_format($featured->reserve_price, 2) }}</span>
                            <span>{{ $featured->bids_count }} Bids</span>
                        </div>
                    </div>
                </div>

                <!-- Moderator Action Bar -->
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3 text-xs font-mono">
                        <span class="text-slate-500">Starting: <strong class="text-[#0F172A]">₹{{ number_format($featured->starting_price, 2) }}</strong></span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-500">Min Increment: <strong class="text-[#0F172A]">₹{{ number_format($featured->minimum_increment, 2) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.auctions.show', $featured->id) }}" class="px-3.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 text-xs font-semibold shadow-2xs">
                            View Full Bidding Ladder
                        </a>
                        @if($featured->status === 'live')
                            <form method="POST" action="{{ route('admin.auctions.cancel', $featured->id) }}" onsubmit="return confirm('Cancel this auction?');" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-white border border-red-200 text-red-700 hover:bg-red-50 text-xs font-semibold">
                                    Cancel Lot
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.auctions.end', $featured->id) }}" onsubmit="return confirm('Hammer down lot #{{ $featured->id }} and assign to highest bidder?');" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#F5A623] hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-2xs">
                                    Hammer Down (Sell)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- All Auction Lots Directory -->
    <div class="rounded-xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 font-mono">Marketplace Auction Lots Directory</h3>
            <span class="text-xs text-slate-400 font-mono">Showing {{ $auctions->total() }} Lots</span>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#FFFDF8] text-slate-500 font-mono text-[10px] uppercase border-b border-slate-100">
                        <th class="py-3 px-4">Lot ID</th>
                        <th class="py-3 px-4">Consignment Item</th>
                        <th class="py-3 px-4">Seller</th>
                        <th class="py-3 px-4 text-right">Starting Bid</th>
                        <th class="py-3 px-4 text-right">Current High</th>
                        <th class="py-3 px-4 text-center">Bids</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-body-sm text-[#0F172A]">
                    @forelse($auctions as $auc)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-[#0F172A]">
                                #AUC-{{ $auc->id }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-[#0F172A]">{{ $auc->product->name ?? 'Auction Item #' . $auc->product_id }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $auc->product->category->name ?? 'Standard Category' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-medium">
                                {{ $auc->seller->name ?? 'Seller #' . $auc->seller_id }}
                            </td>
                            <td class="py-3 px-4 font-mono text-right text-slate-500">
                                ₹{{ number_format($auc->starting_price, 2) }}
                            </td>
                            <td class="py-3 px-4 font-mono text-right font-bold text-amber-700">
                                ₹{{ number_format($auc->current_price ?? $auc->starting_price, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold">
                                {{ $auc->bids_count }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($auc->status === 'live')
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold border border-emerald-200">LIVE</span>
                                @elseif($auc->status === 'ended')
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-mono text-[10px] font-bold border border-slate-200">ENDED</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-red-50 text-red-700 font-mono text-[10px] font-bold border border-red-200">{{ strtoupper($auc->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.auctions.show', $auc->id) }}" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-semibold transition-colors">
                                    Lot Dossier
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400">
                                <span class="material-symbols-outlined text-3xl mb-1 text-slate-300">gavel</span>
                                <p>No auctions found matching criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($auctions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $auctions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
