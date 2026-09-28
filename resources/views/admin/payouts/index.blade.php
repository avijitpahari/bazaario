@extends('layouts.admin')

@section('title', 'Merchant Settlement & Escrow Payouts')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col max-w-2xl">
            <div class="flex items-center gap-space-sm mb-1">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Merchant Payouts & Settlements</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-mono text-[10px] font-bold">ESCROW v2.4</span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500">
                Manage seller payouts, commission deduction rates, escrow holding periods, and direct NEFT/RTGS bank settlement batches.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Filter Pills -->
            <a href="{{ route('admin.payouts.index') }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ !request('status') ? 'bg-[#0F172A] text-white border-[#0F172A]' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                All Payouts
            </a>
            <a href="{{ route('admin.payouts.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ request('status') === 'pending' ? 'bg-amber-500 text-slate-950 font-bold border-amber-500' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                Pending Escrow
            </a>
            <a href="{{ route('admin.payouts.index', ['status' => 'paid']) }}" class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition-colors {{ request('status') === 'paid' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                Disbursed
            </a>

            @if($stats['pending_count'] > 0)
                <form method="POST" action="{{ route('admin.payouts.batch-release') }}" onsubmit="return confirm('Release all {{ $stats['pending_count'] }} pending payouts totaling ₹{{ $stats['pending_settlement'] }} via escrow rails?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-body-md text-xs font-bold shadow-2xs hover:bg-amber-400 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">bolt</span>
                        <span>Process Batch (₹<span class="font-mono">{{ $stats['pending_settlement'] }}</span>)</span>
                    </button>
                </form>
            @else
                <button disabled class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-400 font-body-md text-xs font-semibold cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>All Batches Settled</span>
                </button>
            @endif
        </div>
    </div>

    <!-- KPI Grid (4 Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Disbursed to Merchants</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">₹{{ $stats['total_settled'] }}</span>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>Completed disbursements</span>
                <span class="text-emerald-700 font-bold">100% on-time</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Escrow Vault Balance</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-amber-700">₹{{ $stats['pending_settlement'] }}</span>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>{{ $stats['pending_count'] }} payouts pending</span>
                <span class="text-blue-700 font-bold">T+3 Escrow</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">TDS / TCS Deducted</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">1.0%</span>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>Section 194-O (E-Commerce)</span>
                <span class="font-bold text-slate-700">Automated</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Commission Retained</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-emerald-700">₹{{ $stats['commission_retained'] }}</span>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>Marketplace Take</span>
                <span class="text-emerald-700 font-bold">Direct Revenue</span>
            </div>
        </div>
    </div>

    <!-- Settlement Table -->
    <div class="rounded-xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 font-mono">Merchant Settlement Records</h3>
            <span class="text-xs text-slate-400 font-mono">Showing {{ $payouts->total() }} Escrow Entries</span>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#FFFDF8] text-slate-500 font-mono text-[10px] uppercase border-b border-slate-100">
                        <th class="py-3 px-4">Payout ID</th>
                        <th class="py-3 px-4">Merchant Name</th>
                        <th class="py-3 px-4">Bank & IFSC</th>
                        <th class="py-3 px-4 text-right">Gross GMV</th>
                        <th class="py-3 px-4 text-right">Commission</th>
                        <th class="py-3 px-4 text-right">Net Payout</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Action / Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-body-sm text-[#0F172A]">
                    @forelse($payouts as $payout)
                        @php
                            $sellerShop = $payout->seller->sellerProfile->shop_name ?? ($payout->seller->name ?? 'Merchant #' . $payout->seller_id);
                            $bankAcc = $payout->seller->sellerProfile->bank_account_number ?? null;
                            $bankIfsc = $payout->seller->sellerProfile->bank_ifsc ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-[#0F172A]">
                                #PO-{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-3 px-4 font-medium">
                                <div class="flex flex-col">
                                    <span class="font-bold text-[#0F172A]">{{ $sellerShop }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $payout->seller->email ?? 'seller@bazaario.com' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600">
                                @if($bankAcc && $bankIfsc)
                                    <span>•••• {{ substr($bankAcc, -4) }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $bankIfsc }}</span>
                                @else
                                    <span class="text-amber-600 text-[11px] font-sans">A/C Pending Verification</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-right">₹{{ number_format($payout->gross_amount, 2) }}</td>
                            <td class="py-3 px-4 font-mono text-right text-emerald-700">- ₹{{ number_format($payout->commission_amount, 2) }}</td>
                            <td class="py-3 px-4 font-mono text-right font-bold text-[#0F172A]">₹{{ number_format($payout->net_amount, 2) }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($payout->status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold border border-emerald-200/60">Disbursed</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-mono text-[10px] font-bold border border-amber-200/60">Escrow Hold</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($payout->status === 'pending')
                                    <form method="POST" action="{{ route('admin.payouts.release', $payout->id) }}" onsubmit="return confirm('Release ₹{{ number_format($payout->net_amount, 2) }} to {{ addslashes($sellerShop) }}?');" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-lg bg-[#F5A623] hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-2xs transition-colors">
                                            Release Payout
                                        </button>
                                    </form>
                                @else
                                    <div class="flex flex-col items-end">
                                        <span class="font-mono text-[10px] font-bold text-slate-600">{{ $payout->payout_reference ?? 'NEFT-COMPLETED' }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $payout->paid_at ? $payout->paid_at->format('d M, h:i A') : 'Settled' }}</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400 font-body-sm">
                                <span class="material-symbols-outlined text-3xl mb-1 text-slate-300">payments</span>
                                <p>No payout records found matching criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payouts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $payouts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
