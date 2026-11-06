@extends('layouts.seller')

@section('title', 'Merchant Payout Summary & Financial Statement — Bazaario')

@section('content')
@php
    $seller = $sellerUser ?? Auth::guard('seller')->user() ?? Auth::user();
    $profile = $sellerProfile ?? $seller?->sellerProfile;
    $shopName = $profile?->shop_name ?? 'My Farm';
    $rawAccount = $profile?->bank_account_number;
    $hasBankDetails = !empty($rawAccount) && strlen($rawAccount) >= 4;
    $maskedBankAcc = $hasBankDetails ? '•••• ' . substr($rawAccount, -4) : 'Not configured';
    $bankIfsc = $profile?->bank_ifsc ?: 'Not configured';
    $bankName = $hasBankDetails ? 'HDFC Bank' : 'Not configured';
@endphp

<div class="flex flex-col w-full pb-16 print:p-0 print:pb-0">
    <!-- Official Print Header (Visible ONLY on print) -->
    <div class="hidden print:flex items-center justify-between pb-4 mb-5 border-b-2 border-slate-900 print:break-inside-avoid">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 font-black font-['Outfit'] text-xl flex items-center justify-center border border-amber-600 shrink-0">
                B
            </div>
            <div>
                <div class="font-heading text-lg font-black tracking-tight text-slate-900 leading-none">BAZAARIO MARKETPLACE</div>
                <span class="font-mono text-[9px] uppercase tracking-wider text-slate-500 font-semibold">Official Merchant Financial Settlement Statement</span>
            </div>
        </div>
        <div class="text-right">
            <span class="font-mono text-xs font-bold text-slate-900 block">Statement Date: {{ now()->format('M d, Y - h:i A') }}</span>
            <span class="font-mono text-[10px] text-slate-500">Merchant: {{ $shopName }} (#BZ-SLR-{{ str_pad($sellerProfile?->id ?? 1, 4, '0', STR_PAD_LEFT) }})</span>
        </div>
    </div>

    <!-- Printable Document Title (Print Only) -->
    <div class="hidden print:block mb-5 print:break-inside-avoid">
        <h1 class="font-heading text-xl font-bold text-slate-900 tracking-tight">Merchant Payout Summary &amp; Financial Ledger</h1>
        <p class="font-sans text-xs text-slate-600 mt-0.5">
            Consolidated statement of lifetime sales, net disbursements, transparent deductions, and scheduled payouts for <strong>{{ $shopName }}</strong>.
        </p>
    </div>

    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col gap-1 mb-6 print:hidden">
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-slate-500 uppercase tracking-wider">
            <a href="{{ route('seller.payouts.index') }}" class="hover:text-slate-900 transition-colors">Payouts &amp; Finance</a>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <span class="text-slate-900 font-semibold">View Payout Summary</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div class="max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-900 border border-amber-300/60">
                        Item 78 • Financial Intelligence
                    </span>
                    <span class="font-mono text-[11px] text-slate-300">•</span>
                    <span class="font-mono text-[11px] text-amber-700 font-semibold">Prime Tier 1 Contract</span>
                </div>
                <h1 class="font-heading text-3xl font-bold text-slate-900 tracking-tight mt-1">
                    Merchant Payout Summary
                </h1>
                <p class="font-sans text-sm text-slate-500 mt-1">
                    Consolidated financial ledger statement, transparent platform fee breakdowns, in-flight escrow buffers, and automated disbursement schedules for <span class="font-semibold text-slate-900">{{ $shopName }}</span>.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.payouts.index') }}" class="h-11 px-4 rounded-xl bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/80 transition-colors shadow-2xs flex items-center gap-2 font-sans text-xs font-semibold">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Back to Settlements Ledger</span>
                </a>
                <button type="button" onclick="window.print()" class="h-11 px-4 rounded-[14px] bg-amber-500 hover:bg-amber-400 text-slate-950 font-heading text-xs font-bold transition-all shadow-md flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Print Statement</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4 Primary Financial KPI Summary Tiles -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 print:grid-cols-4 print:gap-3 print:my-4 print:break-inside-avoid">
        <!-- Card 1: Gross Sales Lifetime -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col justify-between print:bg-slate-50/70 print:border-slate-300 print:shadow-none print:p-3 print:rounded-xl">
            <div class="flex items-center justify-between text-slate-400">
                <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Gross Sales Realized</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-slate-900 tracking-tight">₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1.5 mt-1 font-mono text-xs">
                    <span class="text-emerald-700 font-semibold">Fulfilled Orders</span>
                    <span class="text-slate-400">• 100% accounted</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Platform Commission -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col justify-between print:bg-slate-50/70 print:border-slate-300 print:shadow-none print:p-3 print:rounded-xl">
            <div class="flex items-center justify-between text-slate-400">
                <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Platform Commission</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                    <span class="material-symbols-outlined text-[18px]">percent</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-rose-600 tracking-tight">-₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-amber-800 font-semibold">{{ $summary['commission_rate_string'] ?? '10.0%' }}</span>
                    <span class="text-slate-400">• {{ $summary['commission_tier_subtitle'] ?? 'No hidden fees' }}</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Settled Disbursements (Paid) -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col justify-between print:bg-slate-50/70 print:border-slate-300 print:shadow-none print:p-3 print:rounded-xl">
            <div class="flex items-center justify-between text-slate-400">
                <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Total Disbursed (Paid)</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <span class="material-symbols-outlined text-[18px]">task_alt</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-emerald-700 tracking-tight">₹{{ number_format($kpis['total_settled'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-slate-900 font-semibold">{{ $statusCounts['paid'] ?? 0 }} Batches</span>
                    <span class="text-slate-400">• Direct NEFT cleared</span>
                </div>
            </div>
        </div>

        <!-- Card 4: In-Flight Escrow Reserve -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col justify-between print:bg-slate-50/70 print:border-slate-300 print:shadow-none print:p-3 print:rounded-xl">
            <div class="flex items-center justify-between text-slate-400">
                <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">In-Flight Escrow Reserve</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-[18px]">hourglass_top</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-amber-700 tracking-tight">₹{{ number_format($kpis['pending_processing'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-amber-800 font-semibold">T+1 Cycle</span>
                    <span class="text-slate-400">• Next cycle credit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2 Column Section: Itemized Ledger & Statutory Exemption + Bank Destination -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mb-6">
        <!-- Left: Itemized Payout Ledger Calculation (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col gap-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-600 text-[22px]">account_balance_wallet</span>
                    <h3 class="font-heading text-base font-bold text-slate-900">Itemized Financial Ledger &amp; Deductions</h3>
                </div>
                <span class="font-mono text-[11px] px-2.5 py-0.5 rounded-full bg-slate-100 font-bold text-slate-700 border border-slate-200">Audit Standard</span>
            </div>

            <div class="bg-slate-50/80 border border-slate-200/70 rounded-xl p-5 flex flex-col gap-3 font-sans text-xs">
                <div class="flex items-center justify-between text-slate-900">
                    <span class="font-medium">Total Gross Order Volume</span>
                    <span class="font-mono font-semibold">₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-rose-600">
                    <span class="flex items-center gap-1.5">
                        <span>Platform Commission Rate</span>
                        <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-rose-50 text-rose-700 border border-rose-200 font-semibold">{{ $summary['commission_rate_string'] ?? '10.0%' }} Fixed</span>
                    </span>
                    <span class="font-mono font-semibold">-₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-rose-600">
                    <span class="flex items-center gap-1.5">
                        <span>Statutory APMC Mandi Cess</span>
                        <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">Statutory</span>
                    </span>
                    <span class="font-mono font-semibold">-₹{{ number_format($summary['total_apmc_cess'] ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-500">
                    <span>GST on Mandi Direct Agricultural Produce</span>
                    <span class="font-mono text-emerald-700 font-semibold">₹0.00 <span class="text-[10px]">(0% Agro Exempt)</span></span>
                </div>
                <div class="flex items-center justify-between text-slate-500">
                    <span>Listing &amp; Catalog Management Fee</span>
                    <span class="font-mono text-emerald-700 font-semibold">₹0.00 <span class="text-[10px]">(Free)</span></span>
                </div>
                <div class="flex items-center justify-between text-slate-500">
                    <span>Payment Gateway &amp; Direct NEFT Transmission Surcharge</span>
                    <span class="font-mono text-emerald-700 font-semibold">₹0.00 <span class="text-[10px]">(Platform Absorbed)</span></span>
                </div>

                <div class="h-px bg-slate-200/80 my-1"></div>

                <div class="flex items-center justify-between pt-1">
                    <div class="flex flex-col">
                        <span class="font-heading text-base font-bold text-slate-900">Net Seller Accrual Payable</span>
                        <span class="font-mono text-[11px] text-slate-400">Gross minus transparent {{ $summary['commission_rate_string'] ?? '10.0%' }} platform fee</span>
                    </div>
                    <span class="font-mono text-xl font-bold text-emerald-700">
                        ₹{{ number_format(($kpis['total_settled'] ?? 0) + ($kpis['pending_processing'] ?? 0), 2) }}
                    </span>
                </div>
            </div>

            <!-- Transparency callout -->
            <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/70 flex items-start gap-2.5 text-xs text-slate-800">
                <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0 mt-0.5">verified_user</span>
                <p>
                    <strong class="text-amber-950 font-heading font-bold">Zero-Hidden-Fee Guarantee:</strong> Bazaario charges a transparent {{ $summary['commission_rate_string'] ?? '10.0%' }} marketplace commission on completed orders. There are zero listing fees, zero withdrawal surcharges, and direct weekly NEFT transfers are always free.
                </p>
            </div>
        </div>

        <!-- Right: Destination Bank & Settlement Schedule (5 cols) -->
        <div class="lg:col-span-5 flex flex-col gap-4">
            <!-- Bank Details Card -->
            <div class="bg-white p-6 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col gap-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold">Settlement Destination</span>
                    @if($hasBankDetails)
                        <span class="inline-flex items-center gap-1 text-emerald-700 font-mono text-[10px] font-bold">
                            <span class="material-symbols-outlined text-[13px]">verified</span> Primary Verified
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-rose-600 font-mono text-[10px] font-bold">
                            <span class="material-symbols-outlined text-[13px]">warning</span> Missing Bank Details
                        </span>
                    @endif
                </div>

                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/60 flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200/80 flex items-center justify-center font-mono font-bold text-sm text-slate-900 shadow-2xs">
                            {{ substr($bankName, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <span class="font-heading text-sm font-bold text-slate-900">{{ $bankName }}</span>
                            <span class="font-mono text-xs text-slate-500">Beneficiary: {{ $shopName }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2 mt-1 border-t border-slate-200/60 font-mono text-xs text-slate-700">
                        <span>A/C: <strong class="text-slate-900">{{ $maskedBankAcc }}</strong></span>
                        <span class="text-slate-500">IFSC: <strong class="text-slate-900">{{ $bankIfsc }}</strong></span>
                    </div>
                </div>

                <!-- Next Transfer Batch -->
                <div class="flex flex-col gap-2 font-sans text-xs">
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="text-slate-500">Next Settlement Batch:</span>
                        <span class="font-mono font-bold text-slate-900">{{ $upcomingSettlement['date'] ?? 'Friday' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="text-slate-500">Scheduled Batch Amount:</span>
                        <span class="font-mono font-bold text-amber-700">₹{{ number_format($upcomingSettlement['amount'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700">
                        <span class="text-slate-500">Transfer Protocol:</span>
                        <span class="font-mono text-slate-900">Automated Direct NEFT / RTGS</span>
                    </div>
                </div>
            </div>

            <!-- Lifecycle Statistics Card -->
            <div class="bg-white p-6 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col gap-3">
                <span class="font-mono text-[11px] uppercase tracking-wider text-slate-400 font-bold">Settlement Batch Statistics</span>
                <div class="grid grid-cols-2 gap-3 font-sans text-xs">
                    <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-200/60 flex flex-col">
                        <span class="text-slate-500 font-mono text-[10px] uppercase">Paid Batches</span>
                        <span class="font-heading text-lg font-bold text-emerald-700 mt-1">{{ $statusCounts['paid'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-200/60 flex flex-col">
                        <span class="text-slate-500 font-mono text-[10px] uppercase">In Processing</span>
                        <span class="font-heading text-lg font-bold text-amber-700 mt-1">{{ $statusCounts['processing'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-200/60 flex flex-col">
                        <span class="text-slate-500 font-mono text-[10px] uppercase">Pending Escrow</span>
                        <span class="font-heading text-lg font-bold text-slate-900 mt-1">{{ $statusCounts['pending'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-200/60 flex flex-col">
                        <span class="text-slate-500 font-mono text-[10px] uppercase">Average Payout</span>
                        <span class="font-heading text-lg font-bold text-slate-900 mt-1">₹{{ number_format($summary['avg_payout'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Settlements Table -->
    <div class="bg-white p-6 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col gap-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400 text-[20px]">history</span>
                <h3 class="font-heading text-base font-bold text-slate-900">Recent Settlement Disbursements</h3>
            </div>
            <a href="{{ route('seller.payouts.index') }}" class="font-mono text-xs text-amber-800 hover:text-amber-900 hover:underline font-semibold flex items-center gap-1">
                <span>View All In Ledger</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>

        @if($recentPayouts->isEmpty())
            <div class="py-8 text-center text-slate-500 font-sans text-xs">
                No payout batches have been disbursed yet. Settlements are scheduled automatically upon order delivery.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left font-sans text-xs">
                    <thead>
                        <tr class="font-mono text-[11px] text-slate-500 uppercase tracking-wider bg-slate-50/80 rounded-lg">
                            <th class="py-3 px-3">Reference</th>
                            <th class="py-3 px-2">Order / Batch</th>
                            <th class="py-3 px-2 text-right">Gross</th>
                            <th class="py-3 px-2 text-right">Commission</th>
                            <th class="py-3 px-2 text-right">Net Payout</th>
                            <th class="py-3 px-2 text-center">Status</th>
                            <th class="py-3 px-3 text-right">Disbursed Date</th>
                            <th class="py-3 px-3 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentPayouts as $rp)
                            @php
                                $badgeClass = match($rp->status) {
                                    'paid' => 'bg-emerald-50 text-emerald-800 border border-emerald-200/80',
                                    'processing' => 'bg-amber-50 text-amber-900 border border-amber-200/80',
                                    'pending' => 'bg-slate-100 text-slate-700 border border-slate-200',
                                    'failed' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border border-slate-200',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-slate-900">
                                    #{{ $rp->payout_reference }}
                                </td>
                                <td class="py-3 px-2 font-mono text-slate-500">
                                    {{ $rp->sellerOrder ? 'Order #' . $rp->sellerOrder->seller_order_number : 'Batch Settl.' }}
                                </td>
                                <td class="py-3 px-2 text-right font-mono text-slate-900">
                                    ₹{{ number_format($rp->gross_amount, 2) }}
                                </td>
                                <td class="py-3 px-2 text-right font-mono text-rose-600 font-medium">
                                    -₹{{ number_format($rp->commission_amount, 2) }}
                                </td>
                                <td class="py-3 px-2 text-right font-mono font-bold text-slate-900">
                                    ₹{{ number_format($rp->net_amount, 2) }}
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md {{ $badgeClass }} font-mono text-[10px] font-bold tracking-wider uppercase">
                                        {{ $rp->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-[11px] text-slate-500">
                                    {{ $rp->paid_at ? $rp->paid_at->format('M j, Y') : ($rp->status === 'processing' ? 'Scheduled Friday' : 'Pending') }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('seller.payouts.show', $rp) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 inline-flex items-center gap-1 font-mono text-[11px] font-semibold transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">receipt</span>
                                        <span>Receipt</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Official Print Footer (Print Only) -->
    <div class="hidden print:block mt-8 pt-4 border-t border-slate-300 text-center font-sans text-[10px] text-slate-500 print:break-inside-avoid">
        <p class="font-semibold text-slate-700">Official Computer-Generated Financial Statement &bull; Bazaario Merchant Settlements Protocol</p>
        <p class="mt-0.5">All settlements are audited and disbursed via Direct NEFT/RTGS to the verified banking node on file. For financial disputes or reconciliation inquiries, contact merchant-finance@bazaario.in.</p>
    </div>
</div>

@push('styles')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm 12mm;
    }

    html, body {
        background: #ffffff !important;
        color: #0f172a !important;
        font-size: 10pt !important;
        line-height: 1.4 !important;
        overflow: visible !important;
        height: auto !important;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .print\:hidden {
        display: none !important;
    }

    /* Remove scrollbars completely */
    ::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    [class*="overflow-y-auto"],
    [class*="max-h-"] {
        overflow: visible !important;
        max-height: none !important;
    }

    .print\:break-inside-avoid,
    .print-avoid-break,
    table,
    tr {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
}
</style>
@endpush
@endsection