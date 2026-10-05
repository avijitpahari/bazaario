@extends('layouts.seller')

@section('title', 'My Payouts & Financial Ledger — Bazaario')

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
    $focusedPayout = $selectedPayout ?? $payouts->first() ?? null;
@endphp

<div class="flex flex-col w-full pb-16">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col gap-1 mb-6">
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-outline uppercase tracking-wider">
            <span>Seller Center</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span>Payouts &amp; Finance</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-semibold">Settlements</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div class="max-w-2xl">
                <h1 class="font-heading text-3xl font-bold text-on-surface tracking-tight">
                    My Payouts &amp; Financial Ledger
                </h1>
                <p class="font-sans text-sm text-on-surface-variant mt-1">
                    Transparent commission rates, automated weekly bank settlements, and complete order-by-order earnings breakdown for <span class="font-medium text-on-surface">{{ $shopName }}</span>.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" class="h-12 px-4 rounded-[14px] bg-surface-container-lowest text-on-surface font-sans text-xs font-semibold shadow-sm hover:bg-surface-container transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                    <span>Download Tax Invoice (GST)</span>
                </button>
                <button type="button" class="h-12 px-4 rounded-[14px] bg-primary text-on-primary font-sans text-xs font-semibold shadow-md hover:bg-slate-800 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">file_download</span>
                    <span>Export History CSV</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Upcoming Automated Settlement Banner -->
    <div class="rounded-[14px] bg-surface-container-lowest p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 relative overflow-hidden">
        <div class="flex items-start lg:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-[14px] bg-secondary-container/20 text-on-secondary-container flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">account_balance</span>
            </div>
            <div class="flex flex-col">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-xs font-bold uppercase tracking-wider text-secondary">Upcoming Transfer</span>
                    <span class="w-1 h-1 rounded-full bg-outline"></span>
                    <span class="font-mono text-xs font-medium text-on-surface">{{ $upcomingSettlement['date'] ?? 'Friday' }}</span>
                </div>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="font-heading text-xl font-bold text-on-surface">Scheduled Amount: ₹{{ number_format($upcomingSettlement['amount'] ?? 0, 2) }}</span>
                    @if($hasBankDetails)
                        <span class="font-sans text-xs text-on-surface-variant">to {{ $bankName }} ({{ $maskedBankAcc }})</span>
                    @else
                        <span class="font-sans text-xs text-error font-semibold">Bank details: Not configured</span>
                    @endif
                </div>
                <div class="flex items-center gap-3 text-on-surface-variant font-mono text-xs mt-1">
                    <span>IFSC: {{ $bankIfsc }}</span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1 text-on-tertiary-container font-semibold">
                        <span class="material-symbols-outlined text-[14px]">bolt</span> Automated Direct NEFT
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 relative z-10">
            <div class="px-4 py-2 bg-surface-container rounded-[14px] flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-secondary-container"></span>
                </span>
                <span class="font-mono text-xs font-bold uppercase text-on-surface">Batch Processing In Progress</span>
            </div>
        </div>
    </div>

    <!-- 4 Financial Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Lifetime Revenue -->
        <div class="bg-surface-container-lowest p-5 rounded-[14px] shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-outline uppercase tracking-wider">Total Revenue (Lifetime)</span>
                <span class="material-symbols-outlined text-outline text-[20px]">payments</span>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-on-surface tracking-tight">₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1.5 mt-1 font-mono text-xs">
                    <span class="inline-flex items-center text-on-tertiary-container font-semibold">
                        <span class="material-symbols-outlined text-[14px]">trending_up</span> +22.5%
                    </span>
                    <span class="text-on-surface-variant">vs last month</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Marketplace Commission -->
        <div class="bg-surface-container-lowest p-5 rounded-[14px] shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-outline uppercase tracking-wider">Marketplace Commission</span>
                <span class="material-symbols-outlined text-outline text-[20px]">percent</span>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-on-surface tracking-tight">₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-secondary font-semibold">Fixed 10.0%</span>
                    <span class="text-on-surface-variant">Prime Farmer rate • No hidden fees</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Settled (Paid) -->
        <div class="bg-surface-container-lowest p-5 rounded-[14px] shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-outline uppercase tracking-wider">Total Settled (Paid)</span>
                <span class="material-symbols-outlined text-on-tertiary-container text-[20px]">task_alt</span>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-on-surface tracking-tight">₹{{ number_format($kpis['total_settled'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-on-surface font-semibold">Verified</span>
                    <span class="text-on-surface-variant">100% verified settlement rate</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Pending / Processing -->
        <div class="bg-surface-container-lowest p-5 rounded-[14px] shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-outline uppercase tracking-wider">Pending / Processing</span>
                <span class="material-symbols-outlined text-secondary-container text-[20px]">hourglass_top</span>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-on-surface tracking-tight">₹{{ number_format($kpis['pending_processing'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-secondary font-semibold">Escrow Clearing</span>
                    <span class="text-on-surface-variant">T+1 Cycle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Transparent Commission Rate Structure Card -->
    <div class="bg-surface-container-lowest p-6 rounded-[14px] shadow-sm flex flex-col gap-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-[14px] bg-surface-container-high flex items-center justify-center text-on-surface">
                    <span class="material-symbols-outlined text-[22px]">workspace_premium</span>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-secondary font-bold">Seller Program</span>
                        <span class="w-1 h-1 rounded-full bg-outline"></span>
                        <span class="font-mono text-[11px] text-on-surface-variant">Contract ID: BZ-FARM-9932</span>
                    </div>
                    <h3 class="font-heading text-base font-bold text-on-surface">
                        Your Tier: Tier 1 Prime Farmer (Standard 10% Commission)
                    </h3>
                </div>
            </div>
            <div class="bg-surface-container px-4 py-2 rounded-[14px] flex items-center gap-2 font-mono text-xs">
                <span class="text-on-surface-variant font-medium">Earnings Formula:</span>
                <span class="text-on-surface font-bold">Gross Order - 10% Platform Fee = 90% Net Seller Payout</span>
            </div>
        </div>
        <div class="bg-surface-container-low p-4 rounded-[14px] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[20px]">info</span>
                <p class="font-sans text-on-surface">
                    <span class="font-semibold">Calculation Example:</span> On a ₹10,000 order → <span class="font-semibold text-secondary">₹1,000 Platform Commission (10%)</span> → <span class="font-semibold text-on-tertiary-container">₹9,000 Guaranteed Seller Earnings</span>.
                </p>
            </div>
            <div class="flex items-center gap-4 text-on-surface-variant font-mono text-[11px]">
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-on-tertiary-container">check</span> 0 Listing Fee</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-on-tertiary-container">check</span> 0 Gateway Surcharge</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-on-tertiary-container">check</span> Free Weekly NEFT</span>
            </div>
        </div>
    </div>

    <!-- Two-Column Financial Workspace (Settlements Table + Detail Inspector) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Column: Settlements Table (8 cols / 65%) -->
        <div class="lg:col-span-8 flex flex-col gap-4 bg-surface-container-lowest p-6 rounded-[14px] shadow-sm">
            <!-- Table Controls & Tabs -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
                <div class="flex items-center gap-1 bg-surface-container-low p-1 rounded-[14px] overflow-x-auto text-nowrap">
                    @php
                        $payoutTabs = [
                            'all'        => ['label' => 'All Settlements', 'count' => $counts['all'] ?? 0],
                            'processing' => ['label' => 'Processing', 'count' => $counts['processing'] ?? 0],
                            'paid'       => ['label' => 'Paid', 'count' => $counts['paid'] ?? 0],
                            'pending'    => ['label' => 'Pending', 'count' => $counts['pending'] ?? 0],
                            'failed'     => ['label' => 'Failed', 'count' => $counts['failed'] ?? 0],
                        ];
                        $activePayoutTab = request('status', 'all');
                    @endphp
                    @foreach($payoutTabs as $ptKey => $ptInfo)
                        <a href="{{ route('seller.payouts.index', array_filter(['status' => $ptKey !== 'all' ? $ptKey : null, 'search' => request('search')])) }}"
                           class="px-3.5 py-1.5 rounded-lg font-sans text-xs transition-colors {{ $activePayoutTab === $ptKey ? 'bg-surface-container-lowest font-semibold text-on-surface shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">
                            {{ $ptInfo['label'] }} ({{ $ptInfo['count'] }})
                        </a>
                    @endforeach
                </div>
                <form method="GET" action="{{ route('seller.payouts.index') }}" class="flex items-center gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-2.5 top-2 text-outline text-[16px]">search</span>
                        <input type="text" name="search" value="{{ request('search', $searchQuery ?? '') }}"
                               class="h-9 pl-8 pr-3 bg-surface-container-low rounded-lg font-sans text-xs text-on-surface placeholder:text-outline focus:outline-none"
                               placeholder="Filter batch or UTR...">
                    </div>
                </form>
            </div>

            <!-- Table View -->
            @if($payouts->isEmpty())
                <div class="p-10 flex flex-col items-center justify-center text-center">
                    <div class="w-14 h-14 rounded-[14px] bg-surface-container-low flex items-center justify-center text-outline mb-3">
                        <span class="material-symbols-outlined text-[28px]">payments</span>
                    </div>
                    <h3 class="font-heading text-base font-bold text-on-surface">No settlements found</h3>
                    <p class="font-sans text-xs text-on-surface-variant max-w-sm mt-1">
                        Settlements are automatically scheduled upon order fulfillment and processed in weekly batches.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-sans text-xs">
                        <thead>
                            <tr class="font-mono text-[11px] text-outline uppercase tracking-wider bg-surface-container-low/60 rounded-lg">
                                <th class="py-3 px-3">Payout ID</th>
                                <th class="py-3 px-2">Linked Batch / Order</th>
                                <th class="py-3 px-2 text-right">Gross</th>
                                <th class="py-3 px-2 text-right">Commission</th>
                                <th class="py-3 px-2 text-right">Net Payout</th>
                                <th class="py-3 px-2 text-center">Status</th>
                                <th class="py-3 px-3 text-right">Settlement Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container/60">
                            @foreach($payouts as $payout)
                                @php
                                    $isSel = ($focusedPayout && $focusedPayout->id === $payout->id);
                                    $pStatus = strtoupper($payout->status);
                                    $statusBadgeClass = match($payout->status) {
                                        'paid' => 'bg-tertiary-fixed/30 text-on-tertiary-fixed-variant',
                                        'processing' => 'bg-secondary-fixed/40 text-on-secondary-fixed-variant',
                                        'pending' => 'bg-surface-container-high text-on-surface-variant',
                                        'failed' => 'bg-error-container text-on-error-container',
                                        default => 'bg-surface-container text-on-surface',
                                    };
                                @endphp
                                <tr class="{{ $isSel ? 'bg-surface-container-low/60' : 'hover:bg-surface-container-low/30' }} transition-colors cursor-pointer"
                                    onclick="window.location.href='{{ route('seller.payouts.index', array_merge(request()->query(), ['payout_id' => $payout->id])) }}'">
                                    <td class="py-3.5 px-3">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $payout->status === 'paid' ? 'bg-tertiary-fixed' : ($payout->status === 'processing' ? 'bg-secondary-container' : 'bg-outline') }}"></span>
                                            <span class="font-mono font-bold text-on-surface">#{{ $payout->payout_reference }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-2">
                                        <div class="flex flex-col">
                                            <span class="font-mono text-on-surface font-medium">
                                                {{ $payout->sellerOrder ? 'Order #' . $payout->sellerOrder->seller_order_number : 'Batch Settl.' }}
                                            </span>
                                            <span class="font-mono text-[10px] text-on-surface-variant">Escrow Settled</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-2 text-right font-mono text-on-surface">
                                        ₹{{ number_format($payout->gross_amount, 2) }}
                                    </td>
                                    <td class="py-3.5 px-2 text-right font-mono text-error font-medium">
                                        -₹{{ number_format($payout->commission_amount, 2) }}
                                    </td>
                                    <td class="py-3.5 px-2 text-right font-mono font-bold text-on-surface">
                                        ₹{{ number_format($payout->net_amount, 2) }}
                                    </td>
                                    <td class="py-3.5 px-2 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-[6px] {{ $statusBadgeClass }} font-mono text-[10px] font-bold tracking-wider">
                                            {{ $pStatus }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right font-mono text-[11px] text-on-surface-variant">
                                        {{ $payout->paid_at ? $payout->paid_at->format('M j, Y') : ($payout->status === 'processing' ? 'Sched. Friday' : 'Pending') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($payouts->hasPages())
                    <div class="pt-3 border-t border-surface-container-high">
                        {{ $payouts->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Right Column: Settlement Inspector (4 cols / 35%) -->
        <div class="lg:col-span-4 flex flex-col gap-4">
            @if($focusedPayout)
                <div class="bg-surface-container-lowest p-6 rounded-[14px] shadow-sm flex flex-col gap-5 sticky top-20">
                    <!-- Inspector Header -->
                    <div class="flex items-start justify-between pb-2 border-b border-surface-container-high/40">
                        <div class="flex flex-col">
                            <span class="font-mono text-[11px] text-outline uppercase tracking-wider">Settlement Inspector</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">Payout Details — #{{ $focusedPayout->payout_reference }}</h2>
                        </div>
                        <span class="px-2 py-0.5 rounded-[6px] bg-secondary-fixed/40 text-on-secondary-fixed-variant font-mono text-[10px] font-bold uppercase">
                            {{ strtoupper($focusedPayout->status) }}
                        </span>
                    </div>

                    <!-- Bank Account Destination Box -->
                    <div class="p-4 bg-surface-container-low rounded-[14px] flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[11px] text-on-surface-variant uppercase">Settlement Account</span>
                            <span class="inline-flex items-center gap-0.5 text-on-tertiary-container font-mono text-[11px] font-semibold">
                                <span class="material-symbols-outlined text-[13px]">verified</span> Primary Verified
                            </span>
                        </div>
                        <div class="flex items-center gap-3 mt-1">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center font-bold text-on-surface font-mono text-sm">
                                H
                            </div>
                            <div class="flex flex-col leading-tight">
                                <span class="font-sans text-xs font-semibold text-on-surface">{{ $bankName }}</span>
                                <span class="font-mono text-[11px] text-on-surface-variant">{{ $shopName }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 mt-1 border-t border-surface-container-high/60 font-mono text-xs text-on-surface">
                            <span>A/C: {{ $maskedBankAcc }}</span>
                            <span class="text-on-surface-variant">IFSC: {{ $bankIfsc }}</span>
                        </div>
                    </div>

                    <!-- Financial Breakdown Ledger -->
                    <div class="flex flex-col gap-2">
                        <span class="font-mono text-[11px] text-outline uppercase tracking-wider">Financial Breakdown</span>
                        <div class="flex flex-col gap-2 font-sans text-xs">
                            <div class="flex items-center justify-between text-on-surface">
                                <span>Gross Order Value</span>
                                <span class="font-mono font-medium">₹{{ number_format($focusedPayout->gross_amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-error">
                                <span>Bazaario Commission (-10%)</span>
                                <span class="font-mono font-medium">-₹{{ number_format($focusedPayout->commission_amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-on-surface-variant">
                                <span>GST on Commission (18%)</span>
                                <span class="font-mono">₹0.00 <span class="text-[10px] text-on-tertiary-container font-semibold">(Agro Exempt)</span></span>
                            </div>
                            <div class="flex items-center justify-between text-on-surface-variant">
                                <span>Logistics &amp; Hub Fleet Fee</span>
                                <span class="font-mono">₹0.00 <span class="text-[10px] text-on-tertiary-container font-semibold">(Covered)</span></span>
                            </div>
                        </div>

                        <!-- Grand Total Highlight -->
                        <div class="bg-surface-container-high/60 p-4 rounded-[14px] flex items-center justify-between mt-2">
                            <div class="flex flex-col">
                                <span class="font-mono text-[11px] uppercase text-on-surface-variant font-bold">Net Deposit Payable</span>
                                <span class="font-mono text-[10px] text-outline">Direct NEFT Transfer</span>
                            </div>
                            <span class="font-heading text-xl font-bold text-on-surface tracking-tight">
                                ₹{{ number_format($focusedPayout->net_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Settlement Lifecycle Stepper -->
                    <div class="flex flex-col gap-2">
                        <span class="font-mono text-[11px] text-outline uppercase tracking-wider">Settlement Lifecycle</span>
                        <div class="relative pl-6 flex flex-col gap-3 font-sans text-xs">
                            <div class="absolute left-2.5 top-2 bottom-2 w-0.5 bg-surface-container-high"></div>
                            <div class="relative flex flex-col">
                                <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full bg-on-tertiary-container flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-[10px]">check</span>
                                </div>
                                <span class="font-mono font-bold text-on-surface">Step 1: Orders Fulfilled &amp; Escrow Released</span>
                                <span class="text-[11px] text-on-surface-variant">Verified by Bazaario Dispatch Protocol</span>
                            </div>
                            <div class="relative flex flex-col">
                                <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full bg-on-tertiary-container flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-[10px]">check</span>
                                </div>
                                <span class="font-mono font-bold text-on-surface">Step 2: Batch Ledger Calculated &amp; Locked</span>
                                <span class="text-[11px] text-on-surface-variant">Deductions verified (10% platform fee)</span>
                            </div>
                            <div class="relative flex flex-col">
                                <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full {{ $focusedPayout->status === 'paid' ? 'bg-on-tertiary-container' : 'bg-secondary' }} flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </div>
                                <span class="font-mono font-bold {{ $focusedPayout->status === 'paid' ? 'text-on-surface' : 'text-secondary' }}">Step 3: Direct Bank NEFT Transmission</span>
                                <span class="text-[11px] text-on-surface-variant">Transmitted to clearing gateway</span>
                            </div>
                            <div class="relative flex flex-col">
                                <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full {{ $focusedPayout->status === 'paid' ? 'bg-on-tertiary-container' : 'bg-surface-container-high' }} flex items-center justify-center">
                                    @if($focusedPayout->status === 'paid')
                                        <span class="material-symbols-outlined text-white text-[10px]">check</span>
                                    @endif
                                </div>
                                <span class="font-mono font-medium {{ $focusedPayout->status === 'paid' ? 'text-on-surface font-bold' : 'text-outline' }}">Step 4: Bank Clearance &amp; Deposit</span>
                                <span class="text-[11px] text-on-surface-variant">{{ $focusedPayout->status === 'paid' ? 'Deposited' : 'Expected Friday 08:00 AM' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        <a href="{{ route('seller.payouts.show', $focusedPayout) }}" class="h-11 rounded-[14px] bg-primary text-on-primary font-sans text-xs font-semibold flex items-center justify-center gap-2 shadow-sm hover:bg-slate-800 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">receipt</span>
                            <span>View Full Receipt</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-surface-container-lowest p-6 rounded-[14px] shadow-sm text-center font-sans text-xs text-on-surface-variant">
                    Select a payout from the ledger to inspect details.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
