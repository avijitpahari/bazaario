@extends('layouts.admin')

@section('title', 'Wholesale Auction Disputes & Arbitration Desk')

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-space-sm">
                <a href="{{ route('admin.auctions.index') }}" class="font-body-sm text-xs text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">bolt</span>
                    <span>Live Auctions</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="font-mono text-xs text-rose-700 font-bold uppercase">Dispute Arbitration Desk</span>
            </div>
            <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight mt-1">
                Wholesale Auction Disputes
            </h1>
            <p class="font-body-md text-xs sm:text-sm text-slate-500 mt-1">
                Executive arbitration for winner payment defaults, catalog quality discrepancies, shill bidding allegations, and consignment damage.
            </p>
        </div>

        <!-- Right Header Action -->
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.auctions.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">gavel</span>
                <span>Auction Terminal</span>
            </a>
            <a href="{{ route('admin.support.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">support_agent</span>
                <span>Live Support Desk</span>
            </a>
        </div>
    </div>

    <!-- Telemetry KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <!-- Active Action Required -->
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Action Required (Open)</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-rose-600">{{ $stats['open'] }}</span>
            </div>
            <span class="text-xs text-rose-700 font-bold">Awaiting administrative ruling</span>
        </div>

        <!-- Under Review -->
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Under Investigation</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-amber-600">{{ $stats['under_review'] }}</span>
            </div>
            <span class="text-xs text-amber-700 font-bold">Evidence & GPS audit underway</span>
        </div>

        <!-- Contested Escrow Stakes -->
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Contested Escrow Capital</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">₹{{ number_format($stats['contested_escrow'], 2) }}</span>
            </div>
            <span class="text-xs text-slate-400">Frozen in escrow buyer vault</span>
        </div>

        <!-- Resolved -->
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Arbitrated / Closed</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-emerald-700">{{ $stats['resolved'] }}</span>
            </div>
            <span class="text-xs text-emerald-700 font-bold">Executive rulings issued</span>
        </div>
    </div>

    <!-- Filter Tabs & Search Console -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 flex flex-col gap-4">
        <!-- Status Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
                <a href="{{ route('admin.auctions.disputes.index') }}" 
                   class="px-3.5 py-1.5 rounded-xl transition-all {{ (!request('status') || request('status') === 'all') ? 'bg-[#0F172A] text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    All Inquiries ({{ $stats['total'] }})
                </a>
                <a href="{{ route('admin.auctions.disputes.index', ['status' => 'open']) }}" 
                   class="px-3.5 py-1.5 rounded-xl transition-all {{ request('status') === 'open' ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 hover:bg-rose-50 hover:text-rose-700' }}">
                    Open ({{ $stats['open'] }})
                </a>
                <a href="{{ route('admin.auctions.disputes.index', ['status' => 'under_review']) }}" 
                   class="px-3.5 py-1.5 rounded-xl transition-all {{ request('status') === 'under_review' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-800' }}">
                    Under Review ({{ $stats['under_review'] }})
                </a>
                <a href="{{ route('admin.auctions.disputes.index', ['status' => 'resolved']) }}" 
                   class="px-3.5 py-1.5 rounded-xl transition-all {{ request('status') === 'resolved' ? 'bg-emerald-700 text-white font-bold' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' }}">
                    Arbitrated / Closed ({{ $stats['resolved'] }})
                </a>
            </div>

            <!-- Total counter pill -->
            <span class="text-xs font-mono text-slate-400">
                Showing {{ $disputes->count() }} of {{ $disputes->total() }} records
            </span>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.auctions.disputes.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">

            <!-- Search Field -->
            <div class="sm:col-span-5 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search by dispute #, subject, bidder, or lot title..." 
                       class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:outline-none text-xs text-slate-900 font-sans">
            </div>

            <!-- Dispute Type Dropdown -->
            <div class="sm:col-span-3">
                <select name="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:outline-none text-xs text-slate-700">
                    <option value="">All Dispute Causes</option>
                    <option value="quality_mismatch" {{ request('type') === 'quality_mismatch' ? 'selected' : '' }}>Catalog / Grade Mismatch</option>
                    <option value="non_payment" {{ request('type') === 'non_payment' ? 'selected' : '' }}>Winner Non-Payment</option>
                    <option value="seller_refusal" {{ request('type') === 'seller_refusal' ? 'selected' : '' }}>Seller Refusal to Honor Lot</option>
                    <option value="damaged_transit" {{ request('type') === 'damaged_transit' ? 'selected' : '' }}>Transit Damage</option>
                    <option value="shill_bidding" {{ request('type') === 'shill_bidding' ? 'selected' : '' }}>Shill / Fraudulent Bidding</option>
                    <option value="reserve_dispute" {{ request('type') === 'reserve_dispute' ? 'selected' : '' }}>Reserve Price Conflict</option>
                </select>
            </div>

            <!-- Priority Dropdown -->
            <div class="sm:col-span-2">
                <select name="priority" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:outline-none text-xs text-slate-700">
                    <option value="">All Priorities</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>

            <!-- Submit / Clear Buttons -->
            <div class="sm:col-span-2 flex items-center gap-1.5">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors flex items-center justify-center gap-1 shadow-2xs">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'type', 'priority']))
                    <a href="{{ route('admin.auctions.disputes.index', ['status' => request('status', 'all')]) }}" class="py-2 px-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold" title="Reset Filters">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Master Dispute Register Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 font-mono text-[10px] uppercase text-slate-500 border-b border-slate-200/80 tracking-wider">
                        <th class="py-3.5 px-4 font-bold">Dispute Reference</th>
                        <th class="py-3.5 px-4 font-bold">Auction Lot Context</th>
                        <th class="py-3.5 px-4 font-bold">Complainant / Roles</th>
                        <th class="py-3.5 px-4 font-bold">Nature of Dispute</th>
                        <th class="py-3.5 px-4 text-right font-bold">Contested Escrow</th>
                        <th class="py-3.5 px-4 text-center font-bold">Ruling Status</th>
                        <th class="py-3.5 px-4 text-right font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($disputes as $disp)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Dispute # -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base {{ $disp->status === 'open' ? 'text-rose-500' : ($disp->status === 'under_review' ? 'text-amber-500' : 'text-emerald-600') }}">
                                        {{ $disp->status === 'open' ? 'error' : ($disp->status === 'under_review' ? 'pending' : 'verified') }}
                                    </span>
                                    <div>
                                        <a href="{{ route('admin.auctions.disputes.show', $disp->id) }}" class="font-mono font-bold text-slate-900 hover:text-amber-700 block">
                                            #{{ $disp->dispute_number }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            {{ $disp->created_at ? $disp->created_at->format('M d, Y H:i') : '' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Auction Lot Context -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="flex flex-col">
                                    <a href="{{ route('admin.auctions.show', $disp->auction_id) }}" class="font-mono text-[11px] font-bold text-amber-700 hover:underline">
                                        Lot #AUC-{{ $disp->auction_id }}
                                    </a>
                                    <span class="font-bold text-slate-900 text-xs truncate" title="{{ $disp->auction?->product?->name ?? 'Auction Lot #' . $disp->auction_id }}">
                                        {{ $disp->auction?->product?->name ?? 'Auction Lot #' . $disp->auction_id }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        Lot Status: {{ strtoupper($disp->auction?->status ?? 'N/A') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Complainant & Counterparty -->
                            <td class="py-3.5 px-4">
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold bg-slate-100 text-slate-700 uppercase">Claimant</span>
                                        <span class="font-semibold text-slate-800 truncate">{{ $disp->claimant?->name ?? 'User #' . $disp->user_id }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono truncate">{{ $disp->claimant?->email ?? '' }}</span>
                                    @if($disp->seller)
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            Merchant: <span class="font-semibold text-slate-700">{{ $disp->seller?->shop_name ?? 'Producer' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Nature of Dispute -->
                            <td class="py-3.5 px-4">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold font-mono uppercase tracking-wide
                                        {{ $disp->priority === 'urgent' ? 'bg-rose-100 text-rose-800 border border-rose-200' :
                                           ($disp->priority === 'high' ? 'bg-amber-100 text-amber-900 border border-amber-200' :
                                           'bg-slate-100 text-slate-700') }}">
                                        {{ strtoupper($disp->priority) }} PRIORITY
                                    </span>
                                    <span class="font-semibold text-slate-800 text-xs">
                                        {{ $disp->type_label }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 line-clamp-1 max-w-xs">
                                        {{ $disp->subject }}
                                    </span>
                                </div>
                            </td>

                            <!-- Contested Escrow -->
                            <td class="py-3.5 px-4 text-right font-mono">
                                <span class="font-bold text-slate-900 text-sm">
                                    ₹{{ number_format((float)$disp->contested_amount, 2) }}
                                </span>
                                <span class="text-[10px] text-slate-400 block font-normal">Escrow Deposit</span>
                            </td>

                            <!-- Status & Ruling -->
                            <td class="py-3.5 px-4 text-center font-mono">
                                <div class="flex flex-col items-center gap-1">
                                    @if($disp->status === 'open')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 uppercase">
                                            Open / Pending
                                        </span>
                                    @elseif($disp->status === 'under_review')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200 uppercase">
                                            Under Review
                                        </span>
                                    @elseif($disp->status === 'resolved')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase">
                                            Arbitrated
                                        </span>
                                        <span class="text-[9px] text-emerald-700 font-semibold">{{ $disp->ruling_label }}</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700 uppercase">
                                            Dismissed
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.auctions.disputes.show', $disp->id) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-2xs transition-colors">
                                    <span>Arbitrate</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-2xl">verified_user</span>
                                    </div>
                                    <p class="font-bold text-sm text-slate-700">No Auction Disputes Found</p>
                                    <p class="text-xs text-slate-400 max-w-sm">All wholesale auction floor settlements, bidder allocations, and escrow payouts are currently clean and uncontested.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($disputes->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $disputes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
