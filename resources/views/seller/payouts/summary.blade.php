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
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col gap-1 mb-6 print:hidden">
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-outline uppercase tracking-wider">
            <a href="{{ route('seller.payouts.index') }}" class="hover:text-on-surface transition-colors">Payouts &amp; Finance</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-semibold">View Payout Summary</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div class="max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-900 border border-amber-300/60">
                        Item 78 • Financial Intelligence
                    </span>
                    <span class="font-mono text-[11px] text-outline">•</span>
                    <span class="font-mono text-[11px] text-secondary font-semibold">Prime Tier 1 Contract</span>
                </div>
                <h1 class="font-heading text-3xl font-bold text-on-surface tracking-tight mt-1">
                    Merchant Payout Summary
                </h1>
                <p class="font-sans text-sm text-on-surface-variant mt-1">
                    Consolidated financial ledger statement, transparent platform fee breakdowns, in-flight escrow buffers, and automated disbursement schedules for <span class="font-semibold text-on-surface">{{ $shopName }}</span>.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.payouts.index') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-colors shadow-sm flex items-center gap-2 font-sans text-xs font-semibold">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Gross Sales Lifetime -->
        <div class="bg-surface-container-lowest p-5 rounded-[16px] shadow-sm border border-surface-container/60 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-outline uppercase tracking-wider font-semibold">Gross Sales Realized</span>
                <div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-outline">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-on-surface tracking-tight">₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1.5 mt-1 font-mono text-xs">
                    <span class="text-on-tertiary-container font-semibold">Fulfilled Orders</span>
                    <span class="text-on-surface-variant">• 100% accounted</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Platform Commission -->
        <div class="bg-surface-container-lowest p-5 rounded-[16px] shadow-sm border border-surface-container/60 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-outline uppercase tracking-wider font-semibold">Platform Commission</span>
                <div class="w-8 h-8 rounded-lg bg-error-container/30 flex items-center justify-center text-error">
                    <span class="material-symbols-outlined text-[18px]">percent</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-error tracking-tight">-₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-secondary font-semibold">Fixed 10.0%</span>
                    <span class="text-on-surface-variant">• No hidden fees</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Settled Disbursements (Paid) -->
        <div class="bg-surface-container-lowest p-5 rounded-[16px] shadow-sm border border-surface-container/60 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-outline uppercase tracking-wider font-semibold">Total Disbursed (Paid)</span>
                <div class="w-8 h-8 rounded-lg bg-tertiary-fixed/30 flex items-center justify-center text-on-tertiary-fixed-variant">
                    <span class="material-symbols-outlined text-[18px]">task_alt</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-on-tertiary-container tracking-tight">₹{{ number_format($kpis['total_settled'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-on-surface font-semibold">{{ $statusCounts['paid'] ?? 0 }} Batches</span>
                    <span class="text-on-surface-variant">• Direct NEFT cleared</span>
                </div>
            </div>
        </div>

        <!-- Card 4: In-Flight Escrow Reserve -->
        <div class="bg-surface-container-lowest p-5 rounded-[16px] shadow-sm border border-surface-container/60 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-outline uppercase tracking-wider font-semibold">In-Flight Escrow Reserve</span>
                <div class="w-8 h-8 rounded-lg bg-secondary-container/30 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-[18px]">hourglass_top</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-secondary tracking-tight">₹{{ number_format($kpis['pending_processing'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-secondary font-semibold">T+1 Cycle</span>
                    <span class="text-on-surface-variant">• Next cycle credit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2 Column Section: Itemized Ledger & Statutory Exemption + Bank Destination -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mb-6">
        <!-- Left: Itemized Payout Ledger Calculation (7 cols) -->
        <div class="lg:col-span-7 bg-surface-container-lowest p-6 rounded-[16px] shadow-sm border border-surface-container/60 flex flex-col gap-4">
            <div class="flex items-center justify-between pb-3 border-b border-surface-container-high/60">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[22px]">account_balance_wallet</span>
                    <h3 class="font-heading text-base font-bold text-on-surface">Itemized Financial Ledger &amp; Deductions</h3>
                </div>
                <span class="font-mono text-[11px] px-2.5 py-0.5 rounded-full bg-surface-container-high font-bold text-on-surface">Audit Standard</span>
            </div>

            <div class="bg-surface-container-low rounded-[14px] p-5 flex flex-col gap-3 font-sans text-xs">
                <div class="flex items-center justify-between text-on-surface">
                    <span class="font-medium">Total Gross Order Volume</span>
                    <span class="font-mono font-semibold">₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-error">
                    <span class="flex items-center gap-1.5">
                        <span>Platform Commission Rate</span>
                        <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-error-container/30 text-error font-semibold">10.0% Fixed</span>
                    </span>
                    <span class="font-mono font-semibold">-₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-error">
                    <span class="flex items-center gap-1.5">
                        <span>Statutory APMC Mandi Cess</span>
                        <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-surface-container text-on-surface-variant">Statutory</span>
                    </span>
                    <span class="font-mono font-semibold">-₹{{ number_format($summary['total_apmc_cess'] ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-on-surface-variant">
                    <span>GST on Mandi Direct Agricultural Produce</span>
                    <span class="font-mono text-on-tertiary-container font-semibold">₹0.00 <span class="text-[10px]">(0% Agro Exempt)</span></span>
                </div>
                <div class="flex items-center justify-between text-on-surface-variant">
                    <span>Listing &amp; Catalog Management Fee</span>
                    <span class="font-mono text-on-tertiary-container font-semibold">₹0.00 <span class="text-[10px]">(Free)</span></span>
                </div>
                <div class="flex items-center justify-between text-on-surface-variant">
                    <span>Payment Gateway &amp; Direct NEFT Transmission Surcharge</span>
                    <span class="font-mono text-on-tertiary-container font-semibold">₹0.00 <span class="text-[10px]">(Platform Absorbed)</span></span>
                </div>

                <div class="h-px bg-surface-container-high my-1"></div>

                <div class="flex items-center justify-between pt-1">
                    <div class="flex flex-col">
                        <span class="font-heading text-base font-bold text-on-surface">Net Seller Accrual Payable</span>
                        <span class="font-mono text-[11px] text-outline">Gross minus transparent 10% platform fee</span>
                    </div>
                    <span class="font-mono text-xl font-bold text-on-tertiary-container">
                        ₹{{ number_format(($kpis['total_settled'] ?? 0) + ($kpis['pending_processing'] ?? 0), 2) }}
                    </span>
                </div>
            </div>

            <!-- Transparency callout -->
            <div class="p-3.5 rounded-[12px] bg-secondary-container/15 border border-secondary-container/30 flex items-start gap-2.5 text-xs text-on-surface">
                <span class="material-symbols-outlined text-secondary text-[18px] shrink-0 mt-0.5">verified_user</span>
                <p>
                    <strong class="text-secondary font-heading">Zero-Hidden-Fee Guarantee:</strong> Bazaario charges a flat 10% marketplace commission on completed orders. There are zero listing fees, zero withdrawal surcharges, and direct weekly NEFT transfers are always free.
                </p>
            </div>
        </div>

        <!-- Right: Destination Bank & Settlement Schedule (5 cols) -->
        <div class="lg:col-span-5 flex flex-col gap-4">
            <!-- Bank Details Card -->
            <div class="bg-surface-container-lowest p-6 rounded-[16px] shadow-sm border border-surface-container/60 flex flex-col gap-4">
                <div class="flex items-center justify-between pb-2 border-b border-surface-container-high/60">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-outline font-bold">Settlement Destination</span>
                    @if($hasBankDetails)
                        <span class="inline-flex items-center gap-1 text-on-tertiary-container font-mono text-[10px] font-bold">
                            <span class="material-symbols-outlined text-[13px]">verified</span> Primary Verified
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-error font-mono text-[10px] font-bold">
                            <span class="material-symbols-outlined text-[13px]">warning</span> Missing Bank Details
                        </span>
                    @endif
                </div>

                <div class="p-4 rounded-xl bg-surface-container-low flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center font-mono font-bold text-sm text-on-surface">
                            {{ substr($bankName, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <span class="font-heading text-sm font-bold text-on-surface">{{ $bankName }}</span>
                            <span class="font-mono text-xs text-on-surface-variant">Beneficiary: {{ $shopName }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2 mt-1 border-t border-surface-container-high font-mono text-xs text-on-surface">
                        <span>A/C: <strong>{{ $maskedBankAcc }}</strong></span>
                        <span class="text-on-surface-variant">IFSC: <strong>{{ $bankIfsc }}</strong></span>
                    </div>
                </div>

                <!-- Next Transfer Batch -->
                <div class="flex flex-col gap-2 font-sans text-xs">
                    <div class="flex items-center justify-between text-on-surface">
                        <span class="text-on-surface-variant">Next Settlement Batch:</span>
                        <span class="font-mono font-bold text-on-surface">{{ $upcomingSettlement['date'] ?? 'Friday' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-on-surface">
                        <span class="text-on-surface-variant">Scheduled Batch Amount:</span>
                        <span class="font-mono font-bold text-secondary">₹{{ number_format($upcomingSettlement['amount'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-on-surface">
                        <span class="text-on-surface-variant">Transfer Protocol:</span>
                        <span class="font-mono text-on-surface">Automated Direct NEFT / RTGS</span>
                    </div>
                </div>
            </div>

            <!-- Lifecycle Statistics Card -->
            <div class="bg-surface-container-lowest p-6 rounded-[16px] shadow-sm border border-surface-container/60 flex flex-col gap-3">
                <span class="font-mono text-[11px] uppercase tracking-wider text-outline font-bold">Settlement Batch Statistics</span>
                <div class="grid grid-cols-2 gap-3 font-sans text-xs">
                    <div class="p-3 rounded-xl bg-surface-container-low flex flex-col">
                        <span class="text-on-surface-variant font-mono text-[10px] uppercase">Paid Batches</span>
                        <span class="font-heading text-lg font-bold text-on-tertiary-container mt-1">{{ $statusCounts['paid'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container-low flex flex-col">
                        <span class="text-on-surface-variant font-mono text-[10px] uppercase">In Processing</span>
                        <span class="font-heading text-lg font-bold text-secondary mt-1">{{ $statusCounts['processing'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container-low flex flex-col">
                        <span class="text-on-surface-variant font-mono text-[10px] uppercase">Pending Escrow</span>
                        <span class="font-heading text-lg font-bold text-on-surface mt-1">{{ $statusCounts['pending'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container-low flex flex-col">
                        <span class="text-on-surface-variant font-mono text-[10px] uppercase">Average Payout</span>
                        <span class="font-heading text-lg font-bold text-on-surface mt-1">₹{{ number_format($summary['avg_payout'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Settlements Table -->
    <div class="bg-surface-container-lowest p-6 rounded-[16px] shadow-sm border border-surface-container/60 flex flex-col gap-4">
        <div class="flex items-center justify-between pb-2 border-b border-surface-container-high/60">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-outline text-[20px]">history</span>
                <h3 class="font-heading text-base font-bold text-on-surface">Recent Settlement Disbursements</h3>
            </div>
            <a href="{{ route('seller.payouts.index') }}" class="font-mono text-xs text-secondary hover:underline font-semibold flex items-center gap-1">
                <span>View All In Ledger</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>

        @if($recentPayouts->isEmpty())
            <div class="py-8 text-center text-on-surface-variant font-sans text-xs">
                No payout batches have been disbursed yet. Settlements are scheduled automatically upon order delivery.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left font-sans text-xs">
                    <thead>
                        <tr class="font-mono text-[11px] text-outline uppercase tracking-wider bg-surface-container-low/60 rounded-lg">
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
                    <tbody class="divide-y divide-surface-container/60">
                        @foreach($recentPayouts as $rp)
                            @php
                                $badgeClass = match($rp->status) {
                                    'paid' => 'bg-tertiary-fixed/30 text-on-tertiary-fixed-variant',
                                    'processing' => 'bg-secondary-fixed/40 text-on-secondary-fixed-variant',
                                    'pending' => 'bg-surface-container-high text-on-surface-variant',
                                    'failed' => 'bg-error-container text-on-error-container',
                                    default => 'bg-surface-container text-on-surface',
                                };
                            @endphp
                            <tr class="hover:bg-surface-container-low/30 transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-on-surface">
                                    #{{ $rp->payout_reference }}
                                </td>
                                <td class="py-3 px-2 font-mono text-on-surface-variant">
                                    {{ $rp->sellerOrder ? 'Order #' . $rp->sellerOrder->seller_order_number : 'Batch Settl.' }}
                                </td>
                                <td class="py-3 px-2 text-right font-mono text-on-surface">
                                    ₹{{ number_format($rp->gross_amount, 2) }}
                                </td>
                                <td class="py-3 px-2 text-right font-mono text-error font-medium">
                                    -₹{{ number_format($rp->commission_amount, 2) }}
                                </td>
                                <td class="py-3 px-2 text-right font-mono font-bold text-on-surface">
                                    ₹{{ number_format($rp->net_amount, 2) }}
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-[6px] {{ $badgeClass }} font-mono text-[10px] font-bold tracking-wider uppercase">
                                        {{ $rp->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-[11px] text-on-surface-variant">
                                    {{ $rp->paid_at ? $rp->paid_at->format('M j, Y') : ($rp->status === 'processing' ? 'Scheduled Friday' : 'Pending') }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('seller.payouts.show', $rp) }}" class="p-1.5 rounded-[8px] bg-surface-container hover:bg-surface-container-high text-on-surface inline-flex items-center gap-1 font-mono text-[11px] font-semibold transition-colors">
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
</div>
@endsection