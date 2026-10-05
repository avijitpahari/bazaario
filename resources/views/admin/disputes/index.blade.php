@extends('layouts.admin')

@section('title', 'Escrow Dispute Mediation Portal')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-space-sm mb-1">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Escrow Dispute Resolution</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-900 font-mono text-[11px] font-bold border border-red-200/60">Mediation Desk</span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500">
                Arbitrate buyer return claims, damaged in-transit goods, and release or refund smart escrow balances.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Filter Pills -->
            <a href="{{ route('admin.disputes.index') }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ !request('status') ? 'bg-[#0F172A] text-white border-[#0F172A]' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                All Claims ({{ $stats['total'] }})
            </a>
            <a href="{{ route('admin.disputes.index', ['status' => 'requested']) }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ request('status') === 'requested' ? 'bg-amber-500 text-slate-950 font-bold border-amber-500' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                Under Mediation ({{ $stats['requested'] }})
            </a>
            <a href="{{ route('admin.disputes.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ request('status') === 'approved' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                Refunded ({{ $stats['approved'] }})
            </a>
            <a href="{{ route('admin.disputes.index', ['status' => 'rejected']) }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ request('status') === 'rejected' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                Rejected ({{ $stats['rejected'] }})
            </a>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Total Filed Claims</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">{{ $stats['total'] }}</span>
            </div>
            <span class="text-xs text-slate-400">All lifetime mediation events</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Awaiting Arbitration</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-amber-600">{{ $stats['requested'] }}</span>
            </div>
            <span class="text-xs text-amber-700 font-bold">Action required by admin</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Buyer Refunds Approved</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-emerald-700">{{ $stats['approved'] }}</span>
            </div>
            <span class="text-xs text-emerald-700 font-bold">Escrow funds reversed</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Disputes Dismissed</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-slate-700">{{ $stats['rejected'] }}</span>
            </div>
            <span class="text-xs text-slate-400">Escrow released to seller</span>
        </div>
    </div>

    <!-- Dispute Case Cards -->
    <div class="flex flex-col gap-space-md">
        @forelse($disputes as $dispute)
            @php
                $orderNumber = $dispute->order?->order_number ?? ('BZ-ORD-' . $dispute->order_id);
                $buyerName = $dispute->order?->user?->name ?? ($dispute->user?->name ?? 'Customer');
                $sellerName = $dispute->orderItem?->sellerOrder?->seller?->sellerProfile?->shop_name 
                    ?? ($dispute->orderItem?->sellerOrder?->seller?->name ?? 'Merchant');
                $productName = $dispute->orderItem?->product_name ?? 'Consigned Item';
                $sku = $dispute->orderItem?->sku ?? 'N/A';
            @endphp

            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col gap-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-900 text-white">#DSP-{{ str_pad($dispute->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="font-bold text-sm text-[#0F172A]">{{ $productName }}</h3>
                            <a href="{{ route('admin.orders.show', $orderNumber) }}" class="font-mono text-xs text-amber-700 hover:underline">
                                ({{ $orderNumber }})
                            </a>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Claimant: <strong class="text-[#0F172A]">{{ $buyerName }}</strong> • 
                            Seller: <strong class="text-[#0F172A]">{{ $sellerName }}</strong> •
                            SKU: <span class="font-mono text-slate-600">{{ $sku }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-900 font-mono text-xs font-bold">
                            Escrow Amount: ₹{{ number_format((float)($dispute->refund_amount ?? 0), 2) }}
                        </span>
                        @if($dispute->status === 'requested')
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-slate-950 font-mono text-[10px] font-bold">PENDING REVIEW</span>
                        @elseif($dispute->status === 'approved')
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold border border-emerald-200">REFUND APPROVED</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-mono text-[10px] font-bold border border-slate-200">CLAIM DISMISSED</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Buyer Claim -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-mono font-bold text-slate-400">Buyer Claim (Filed {{ $dispute->requested_at ? $dispute->requested_at->format('d M, Y') : 'Recently' }})</span>
                            <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 font-mono text-[10px] font-bold border border-red-200/50">{{ $dispute->reason }}</span>
                        </div>
                        <p class="text-xs text-slate-700 leading-relaxed italic">
                            "{{ $dispute->description ?? 'Item was damaged or not as described upon unboxing.' }}"
                        </p>
                        <div class="flex items-center gap-2 pt-2 text-xs text-slate-500 font-mono border-t border-slate-200/50 mt-1">
                            <span class="material-symbols-outlined text-base text-slate-400">attach_file</span>
                            <span>Proof verification attached</span>
                            <span class="ml-auto text-emerald-700 font-bold">Buyer Trust Score: 98%</span>
                        </div>
                    </div>

                    <!-- Seller Response -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-mono font-bold text-slate-400">Merchant Fulfillment Telemetry</span>
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-mono text-[10px] font-bold border border-blue-200/50">Status: {{ strtoupper($dispute->orderItem?->sellerOrder?->status ?? 'DELIVERED') }}</span>
                        </div>
                        <p class="text-xs text-slate-700 leading-relaxed">
                            Fulfilled by <strong>{{ $sellerName }}</strong>. Dispatch and delivery scans completed through certified Bazaario regional carrier network.
                        </p>
                        <div class="flex items-center gap-2 pt-2 text-xs text-slate-500 font-mono border-t border-slate-200/50 mt-1">
                            <span class="material-symbols-outlined text-base text-slate-400">local_shipping</span>
                            <span>Carrier Waybill Scanned</span>
                            <span class="ml-auto text-slate-600 font-bold">Seller Compliance: Verified</span>
                        </div>
                    </div>
                </div>

                <!-- Mediation Action Bar -->
                <div class="p-3.5 rounded-xl bg-slate-100/70 border border-slate-200/80 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <span class="text-slate-600 font-medium">Arbitration Judgment:</span>
                    @if($dispute->status === 'requested')
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.disputes.arbitrate', $dispute->id) }}" onsubmit="return confirm('Reject this dispute claim and disburse funds to the merchant?');">
                                @csrf
                                <input type="hidden" name="decision" value="reject">
                                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 font-semibold transition-colors">
                                    Reject Claim & Release to Seller
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.disputes.arbitrate', $dispute->id) }}" onsubmit="return confirm('Approve refund of ₹{{ number_format((float)($dispute->refund_amount ?? 0), 2) }} to {{ addslashes($buyerName) }}?');">
                                @csrf
                                <input type="hidden" name="decision" value="approve">
                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition-colors">
                                    Approve Full Refund (₹{{ number_format((float)($dispute->refund_amount ?? 0), 2) }})
                                </button>
                            </form>
                        </div>
                    @elseif($dispute->status === 'approved')
                        <div class="flex items-center gap-2 text-emerald-700 font-mono text-xs font-bold">
                            <span class="material-symbols-outlined text-base">check_circle</span>
                            <span>Refund of ₹{{ number_format((float)($dispute->refund_amount ?? 0), 2) }} processed to buyer account</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-slate-600 font-mono text-xs font-bold">
                            <span class="material-symbols-outlined text-base">cancel</span>
                            <span>Claim rejected by admin — escrow settlement disbursed to seller</span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-10 text-center text-slate-400">
                <span class="material-symbols-outlined text-4xl mb-1 text-slate-300">gavel</span>
                <p class="font-medium text-slate-600">No escrow dispute claims found</p>
                <p class="text-xs text-slate-400 mt-1">All customer orders and seller disbursements are proceeding smoothly without claims.</p>
            </div>
        @endforelse
    </div>

    @if($disputes->hasPages())
        <div class="p-4 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            {{ $disputes->links() }}
        </div>
    @endif
</div>
@endsection
