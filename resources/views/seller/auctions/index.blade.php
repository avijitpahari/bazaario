@extends('layouts.seller')

@section('title', 'Wholesale Auctions Master Registry — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
    $currentStatus = $status ?? request('status', 'all');

    // Helper for safe auction thumbnail resolution
    if (!function_exists('sellerAuctionThumb')) {
        function sellerAuctionThumb($product) {
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
@endphp

<div class="flex flex-col w-full pb-16">

    <!-- BREADCRUMB & METRIC STATUS BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-3 mb-2">
        <div class="flex items-center gap-2 text-xs font-mono text-slate-500">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-amber-600 transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <span class="text-slate-900 font-bold">Auctions &amp; Wholesale Registry</span>
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

    <!-- WORKSPACE HEADER & ACTION BUTTONS -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-slate-200/80">
        <div class="space-y-1.5 max-w-3xl">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="font-heading text-2xl lg:text-3xl tracking-tight text-slate-900 font-extrabold">
                    Auctions &amp; Wholesale Registry
                </h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-900 border border-amber-200 font-mono text-xs font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    {{ $counts['live'] ?? 0 }} Live Lots
                </span>
            </div>
            <p class="text-sm text-slate-500 leading-relaxed">
                Live spot-market auctions, real-time bidder telemetry, and verified bulk agricultural allocations for <span class="font-semibold text-slate-800">{{ $profile?->shop_name ?? 'My Farm' }}</span>.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('seller.auctions.live') }}" class="h-11 px-5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-heading text-xs font-bold shadow-xs hover:shadow-md transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-amber-400">stream</span>
                <span>Open Live Terminal</span>
            </a>
            <a href="{{ route('seller.auctions.create') }}" class="h-11 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold shadow-xs hover:shadow-md transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Create New Auction</span>
            </a>
        </div>
    </div>

    <!-- 4 OPERATIONAL KPI TILES -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <!-- Live Clearing Lots -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Active Floor Lots</span>
                <span class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">gavel</span>
                </span>
            </div>
            <div class="mt-3">
                <div class="font-heading text-2xl font-extrabold text-slate-900 font-mono">{{ $counts['live'] ?? 0 }}</div>
                <div class="flex items-center gap-1.5 mt-1 font-mono text-xs">
                    <span class="inline-flex items-center text-emerald-700 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> Spot Trading
                    </span>
                    <span class="text-slate-400">active now</span>
                </div>
            </div>
        </div>

        <!-- Scheduled Upcoming Lots -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Scheduled Pipeline</span>
                <span class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">event</span>
                </span>
            </div>
            <div class="mt-3">
                <div class="font-heading text-2xl font-extrabold text-slate-900 font-mono">{{ $counts['scheduled'] ?? 0 }}</div>
                <div class="flex items-center gap-1.5 mt-1 font-mono text-xs text-slate-500">
                    <span>Awaiting start window</span>
                </div>
            </div>
        </div>

        <!-- Completed Lots -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Cleared / Completed</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </span>
            </div>
            <div class="mt-3">
                <div class="font-heading text-2xl font-extrabold text-slate-900 font-mono">{{ $counts['ended'] ?? 0 }}</div>
                <div class="flex items-center gap-1.5 mt-1 font-mono text-xs text-emerald-700 font-semibold">
                    <span>Assigned to Escrow</span>
                </div>
            </div>
        </div>

        <!-- Total Consigned Lots -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Total Consigned Lots</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                </span>
            </div>
            <div class="mt-3">
                <div class="font-heading text-2xl font-extrabold text-slate-900 font-mono">{{ $counts['all'] ?? 0 }}</div>
                <div class="flex items-center gap-1.5 mt-1 font-mono text-xs text-slate-500">
                    <span>Lifetime catalog volume</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MASTER REGISTRY TABLE CARD -->
    <div class="mt-6 bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden flex flex-col" x-data="{ search: '' }">
        
        <!-- Table Header & Status Filter Pills & Quick Search -->
        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200/80">
            <div>
                <h3 class="font-heading text-lg font-bold text-slate-900">All Auctions Master Registry</h3>
                <p class="text-xs text-slate-500">Historical &amp; real-time performance of your bulk commodity lots</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Real-time Client Search -->
                <div class="relative flex items-center min-w-[200px]">
                    <span class="material-symbols-outlined text-[18px] text-slate-400 absolute left-3 pointer-events-none">search</span>
                    <input type="text" 
                           x-model="search" 
                           placeholder="Filter lots or SKU..." 
                           class="w-full h-9 pl-9 pr-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition-all">
                </div>

                <div class="flex flex-wrap items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/80 text-xs">
                    <a href="{{ route('seller.auctions.index') }}" class="px-3 py-1.5 rounded-lg font-heading transition-all {{ $currentStatus === 'all' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900' }}">
                        All ({{ $counts['all'] ?? 0 }})
                    </a>
                    <a href="{{ route('seller.auctions.index', ['status' => 'scheduled']) }}" class="px-3 py-1.5 rounded-lg font-heading transition-all {{ $currentStatus === 'scheduled' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900' }}">
                        Scheduled ({{ $counts['scheduled'] ?? 0 }})
                    </a>
                    <a href="{{ route('seller.auctions.index', ['status' => 'live']) }}" class="px-3 py-1.5 rounded-lg font-heading transition-all flex items-center gap-1.5 {{ $currentStatus === 'live' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live ({{ $counts['live'] ?? 0 }})
                    </a>
                    <a href="{{ route('seller.auctions.index', ['status' => 'ended']) }}" class="px-3 py-1.5 rounded-lg font-heading transition-all {{ $currentStatus === 'ended' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900' }}">
                        Completed ({{ $counts['ended'] ?? 0 }})
                    </a>
                    <a href="{{ route('seller.auctions.index', ['status' => 'cancelled']) }}" class="px-3 py-1.5 rounded-lg font-heading transition-all {{ $currentStatus === 'cancelled' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200/60' : 'text-slate-600 hover:text-slate-900' }}">
                        Cancelled ({{ $counts['cancelled'] ?? 0 }})
                    </a>
                </div>
            </div>
        </div>

        <!-- Registry Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 font-mono text-[11px] text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-3.5 px-5">Lot ID</th>
                        <th class="py-3.5 px-5">Product Lot &amp; Spec</th>
                        <th class="py-3.5 px-5">Starting Price</th>
                        <th class="py-3.5 px-5">Current / Final Bid</th>
                        <th class="py-3.5 px-5">Bid Count</th>
                        <th class="py-3.5 px-5">Timing / Status</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-900">
                    @forelse($auctions ?? [] as $auc)
                        @php
                            $bidsCount = $auc->bids ? $auc->bids->count() : 0;
                            $isReserveMet = $auc->isReserveMet();
                            $thumbUrl = sellerAuctionThumb($auc->product);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $auc->isLive() ? 'bg-amber-50/20' : '' }}"
                            x-show="!search || '{{ strtolower(addslashes(($auc->product?->name ?? '') . ' AUC-' . $auc->id . ' ' . $auc->status)) }}'.includes(search.toLowerCase())">
                            <td class="py-4 px-5">
                                <span class="font-mono font-bold text-slate-900">#AUC-{{ str_pad($auc->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-50 shrink-0 border border-slate-200/80 shadow-2xs flex items-center justify-center p-1">
                                        <img src="{{ $thumbUrl }}" 
                                             alt="{{ $auc->product?->name ?? 'Produce' }}" 
                                             class="w-full h-full object-contain"
                                             onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('seller.auctions.show', $auc) }}" class="font-heading font-bold text-slate-900 hover:text-amber-600 transition-colors truncate block">
                                            {{ $auc->product?->name ?? 'Agricultural Lot' }}
                                        </a>
                                        <div class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                                            <span class="truncate">{{ $auc->product?->category?->name ?? 'Farm Produce' }}</span>
                                            <span>•</span>
                                            <span class="font-mono">Unit: {{ $auc->product?->unit_type ?? 'lot' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5 font-mono text-slate-600">
                                ₹{{ number_format($auc->starting_price, 2) }}
                            </td>
                            <td class="py-4 px-5 font-mono">
                                <div class="font-bold text-slate-900">₹{{ number_format($auc->current_price, 2) }}</div>
                                @if($auc->reserve_price)
                                    @if($isReserveMet)
                                        <span class="text-[11px] text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[13px]">check_circle</span> Reserve Met
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400">Target: ₹{{ number_format($auc->reserve_price, 0) }}</span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-4 px-5 font-mono">
                                <span class="font-bold text-slate-900">{{ $bidsCount }}</span>
                                <span class="text-xs text-slate-500 block">bids placed</span>
                            </td>
                            <td class="py-4 px-5">
                                @if($auc->status === 'live')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 font-mono text-xs font-bold uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        LIVE BIDDING
                                    </span>
                                @elseif($auc->status === 'scheduled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/60 font-mono text-xs font-semibold uppercase">
                                        Scheduled
                                    </span>
                                @elseif($auc->status === 'ended')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-xs font-bold uppercase">
                                        Completed
                                    </span>
                                @elseif($auc->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/60 font-mono text-xs font-bold uppercase">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if($auc->status === 'live')
                                        <a href="{{ route('seller.auctions.live') }}" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold shadow-xs">
                                            Monitor Live
                                        </a>
                                    @endif
                                    <a href="{{ route('seller.auctions.show', $auc) }}" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/80 text-xs font-semibold shadow-2xs">
                                        Details
                                    </a>
                                    @if($bidsCount === 0 && !in_array($auc->status, ['ended', 'cancelled']))
                                        <form method="POST" action="{{ route('seller.auctions.cancel', $auc) }}" onsubmit="return confirm('Cancel this auction lot? This action cannot be undone.');" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-xl text-rose-600 hover:bg-rose-50 border border-rose-200/60 text-xs font-semibold transition-colors">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-[36px] text-slate-300 mb-2">gavel</span>
                                <p class="font-heading font-medium text-slate-900">No auctions found matching this filter</p>
                                <p class="text-xs mt-1 text-slate-500">Create a new wholesale lot to begin spot-market bidding.</p>
                                <a href="{{ route('seller.auctions.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold shadow-xs">
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
            <div class="p-4 bg-slate-50 border-t border-slate-200/80">
                {{ $auctions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
