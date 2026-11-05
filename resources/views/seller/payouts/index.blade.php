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

<div x-data="{ summaryModalOpen: {{ request('open_summary') ? 'true' : 'false' }}, selectedPayoutId: '{{ $focusedPayout?->id }}' }" class="flex flex-col w-full pb-16">
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
                <button type="button" 
                        @click="summaryModalOpen = true" 
                        id="btn-view-payout-summary"
                        class="h-12 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 active:scale-95 text-slate-950 font-heading text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">summarize</span>
                    <span>View Payout Summary</span>
                </button>
                <button type="button" class="h-12 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/90 font-sans text-xs font-semibold shadow-2xs hover:shadow-xs transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-slate-500">receipt_long</span>
                    <span>Download Tax Invoice (GST)</span>
                </button>
                <button type="button" class="h-12 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-sans text-xs font-semibold shadow-xs hover:shadow-md transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-amber-400">file_download</span>
                    <span>Export History CSV</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Upcoming Automated Settlement Banner -->
    <div class="rounded-2xl bg-white border border-slate-200/80 p-6 shadow-2xs flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 relative overflow-hidden">
        <div class="flex items-start lg:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-amber-500/15 border border-amber-300/40 text-amber-700 flex items-center justify-center shrink-0 shadow-2xs">
                <span class="material-symbols-outlined text-[26px]">account_balance</span>
            </div>
            <div class="flex flex-col">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-xs font-bold uppercase tracking-wider text-amber-600">Upcoming Transfer</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span class="font-mono text-xs font-medium text-slate-700">{{ $upcomingSettlement['date'] ?? 'Friday' }}</span>
                </div>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="font-heading text-xl font-bold text-slate-900">Scheduled Amount: ₹{{ number_format($upcomingSettlement['amount'] ?? 0, 2) }}</span>
                    @if($hasBankDetails)
                        <span class="font-sans text-xs text-slate-500">to {{ $bankName }} ({{ $maskedBankAcc }})</span>
                    @else
                        <span class="font-sans text-xs text-rose-600 font-semibold">Bank details: Not configured</span>
                    @endif
                </div>
                <div class="flex items-center gap-3 text-slate-500 font-mono text-xs mt-1">
                    <span>IFSC: {{ $bankIfsc }}</span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                        <span class="material-symbols-outlined text-[14px] text-emerald-600">bolt</span> Automated Direct NEFT
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 relative z-10 flex-wrap">
            <a href="{{ route('seller.account.settings') }}" class="h-10 px-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-sans text-xs font-semibold flex items-center gap-1.5 transition-colors shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-amber-400">account_balance</span>
                <span>{{ $hasBankDetails ? 'Manage Bank' : 'Add Bank Account' }}</span>
            </a>
            <form action="{{ route('seller.account.bank.bypass') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="h-10 px-3.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-sans text-xs font-bold flex items-center gap-1.5 transition-all shadow-2xs hover:shadow-xs active:scale-95 cursor-pointer" title="Auto-configure verified sandbox bank details for test settlements">
                    <span class="material-symbols-outlined text-[16px] text-amber-600">flash_on</span>
                    <span>🧪 Test Bypass</span>
                </button>
            </form>
            <button type="button" 
                    @click="summaryModalOpen = true" 
                    class="h-10 px-3.5 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 text-amber-900 border border-amber-300/60 font-sans text-xs font-bold flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-[16px]">summarize</span>
                <span>View Payout Summary</span>
            </button>
            <div class="px-4 py-2 bg-slate-100 border border-slate-200/80 rounded-xl flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                </span>
                <span class="font-mono text-xs font-bold uppercase text-slate-800">Batch Processing In Progress</span>
            </div>
        </div>
    </div>

    <!-- 4 Financial Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Lifetime Revenue -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Total Revenue (Lifetime)</span>
                <span class="material-symbols-outlined text-slate-400 text-[20px]">payments</span>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-slate-900 tracking-tight">₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1.5 mt-1 font-mono text-xs">
                    <span class="inline-flex items-center text-emerald-700 font-semibold">
                        <span class="material-symbols-outlined text-[14px]">trending_up</span> +22.5%
                    </span>
                    <span class="text-slate-400">vs last month</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Marketplace Commission -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Marketplace Commission</span>
                <span class="material-symbols-outlined text-slate-400 text-[20px]">percent</span>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-slate-900 tracking-tight">₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-amber-600 font-semibold">Fixed 10.0%</span>
                    <span class="text-slate-400">Prime Farmer rate • No hidden fees</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Settled (Paid) -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Total Settled (Paid)</span>
                <span class="material-symbols-outlined text-emerald-600 text-[20px]">task_alt</span>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-emerald-700 tracking-tight">₹{{ number_format($kpis['total_settled'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-slate-700 font-semibold">Verified</span>
                    <span class="text-slate-400">100% verified settlement rate</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Pending / Processing -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Pending / Processing</span>
                <span class="material-symbols-outlined text-amber-500 text-[20px]">hourglass_top</span>
            </div>
            <div class="mt-4">
                <div class="font-heading text-2xl font-bold text-amber-700 tracking-tight">₹{{ number_format($kpis['pending_processing'] ?? 0, 2) }}</div>
                <div class="flex items-center gap-1 mt-1 font-mono text-xs">
                    <span class="text-amber-600 font-semibold">Escrow Clearing</span>
                    <span class="text-slate-400">T+1 Cycle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Transparent Commission Rate Structure Card -->
    <div class="bg-white p-6 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col gap-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-300/40 flex items-center justify-center text-amber-700 shadow-2xs">
                    <span class="material-symbols-outlined text-[22px]">workspace_premium</span>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-amber-600 font-bold">Seller Program</span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="font-mono text-[11px] text-slate-500">Contract ID: BZ-FARM-9932</span>
                    </div>
                    <h3 class="font-heading text-base font-bold text-slate-900">
                        Your Tier: Tier 1 Prime Farmer (Standard 10% Commission)
                    </h3>
                </div>
            </div>
            <div class="bg-slate-50 border border-slate-200/80 px-4 py-2 rounded-xl flex items-center gap-2 font-mono text-xs">
                <span class="text-slate-500 font-medium">Earnings Formula:</span>
                <span class="text-slate-900 font-bold">Gross Order - 10% Platform Fee = 90% Net Seller Payout</span>
            </div>
        </div>
        <div class="bg-slate-50/80 border border-slate-200/60 p-4 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600 text-[20px]">info</span>
                <p class="font-sans text-slate-800">
                    <span class="font-semibold">Calculation Example:</span> On a ₹10,000 order → <span class="font-semibold text-amber-700">₹1,000 Platform Commission (10%)</span> → <span class="font-semibold text-emerald-700">₹9,000 Guaranteed Seller Earnings</span>.
                </p>
            </div>
            <div class="flex items-center gap-4 text-slate-500 font-mono text-[11px]">
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-emerald-600">check</span> 0 Listing Fee</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-emerald-600">check</span> 0 Gateway Surcharge</span>
                <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-emerald-600">check</span> Free Weekly NEFT</span>
            </div>
        </div>
    </div>

    <!-- Two-Column Financial Workspace (Settlements Table + Detail Inspector) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Column: Settlements Table (8 cols / 65%) -->
        <div class="lg:col-span-8 flex flex-col gap-4 bg-white p-6 rounded-2xl shadow-2xs border border-slate-200/80">
            <!-- Table Controls & Tabs -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl overflow-x-auto text-nowrap">
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
                           class="px-3.5 py-1.5 rounded-lg font-sans text-xs transition-colors {{ $activePayoutTab === $ptKey ? 'bg-white font-bold text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            {{ $ptInfo['label'] }} ({{ $ptInfo['count'] }})
                        </a>
                    @endforeach
                </div>
                <form method="GET" action="{{ route('seller.payouts.index') }}" class="flex items-center gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-2.5 top-2 text-slate-400 text-[16px]">search</span>
                        <input type="text" name="search" value="{{ request('search', $searchQuery ?? '') }}"
                               class="h-9 pl-8 pr-3 bg-slate-50 border border-slate-200/80 rounded-lg font-sans text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-amber-500 focus:bg-white transition"
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
                                <th class="py-3 px-2 text-right">Settlement Date</th>
                                <th class="py-3 px-3 text-right">Summary</th>
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
                                    <td class="py-3.5 px-2 text-right font-mono text-[11px] text-on-surface-variant">
                                        {{ $payout->paid_at ? $payout->paid_at->format('M j, Y') : ($payout->status === 'processing' ? 'Sched. Friday' : 'Pending') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right" onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="summaryModalOpen = true" 
                                                    class="px-2 py-1 rounded-[6px] bg-amber-500/15 hover:bg-amber-500/25 text-amber-900 border border-amber-300/60 font-mono text-[10px] font-bold transition-all flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px]">summarize</span>
                                                <span>Summary</span>
                                            </button>
                                            <a href="{{ route('seller.payouts.show', $payout) }}" 
                                               class="p-1 rounded-[6px] hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors" 
                                               title="View Receipt">
                                                <span class="material-symbols-outlined text-[15px]">receipt</span>
                                            </a>
                                        </div>
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
                        <button type="button" 
                                @click="summaryModalOpen = true" 
                                class="h-11 rounded-[14px] bg-amber-500 hover:bg-amber-400 text-slate-950 font-heading text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition-colors">
                            <span class="material-symbols-outlined text-[18px]">summarize</span>
                            <span>View Payout Summary</span>
                        </button>
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

    <!-- Interactive Payout Summary Modal (Item 78) -->
    <div x-show="summaryModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="modal-payout-summary-title">
        
        <!-- Backdrop -->
        <div x-show="summaryModalOpen" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" 
             @click="summaryModalOpen = false"></div>

        <!-- Modal Dialog Container -->
        <div class="flex min-h-screen items-center justify-center p-4 sm:p-6 text-center">
            <div x-show="summaryModalOpen" 
                 x-transition:enter="ease-out duration-200" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95" 
                 class="relative w-full max-w-4xl max-h-[92vh] overflow-y-auto transform rounded-3xl bg-surface-container-lowest border border-surface-container-high/60 p-6 sm:p-8 text-left shadow-2xl transition-all">
                
                <!-- Modal Top Ribbon & Close -->
                <div class="flex items-start justify-between gap-4 pb-5 border-b border-surface-container-high/60">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[28px]">summarize</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-900 border border-amber-300/60">
                                    Financial Statement • Item 78
                                </span>
                                <span class="font-mono text-[11px] text-outline">•</span>
                                <span class="font-mono text-[11px] text-on-surface-variant">Live Escrow &amp; Settlements</span>
                            </div>
                            <h2 id="modal-payout-summary-title" class="font-heading text-2xl font-bold text-on-surface tracking-tight mt-0.5">
                                Merchant Payout Summary
                            </h2>
                            <p class="font-sans text-xs text-on-surface-variant mt-0.5">
                                Consolidated statement of lifetime sales, net disbursements, transparent deductions, and scheduled payouts for <span class="font-semibold text-on-surface">{{ $shopName }}</span>.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="h-9 px-3 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-sans text-xs font-semibold flex items-center gap-1.5 transition-colors" title="Print Statement">
                            <span class="material-symbols-outlined text-[16px]">print</span>
                            <span class="hidden sm:inline">Print</span>
                        </button>
                        <button type="button" @click="summaryModalOpen = false" class="w-9 h-9 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                </div>

                <!-- 4 KPI Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 my-6">
                    <!-- KPI 1: Gross Sales -->
                    <div class="p-4 rounded-2xl bg-surface-container-low border border-surface-container flex flex-col justify-between">
                        <div class="flex items-center justify-between text-outline">
                            <span class="font-mono text-[11px] uppercase tracking-wider font-semibold">Gross Sales Realized</span>
                            <span class="material-symbols-outlined text-[18px]">payments</span>
                        </div>
                        <div class="mt-3">
                            <div class="font-heading text-xl font-bold text-on-surface tracking-tight">
                                ₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}
                            </div>
                            <span class="font-sans text-[11px] text-on-surface-variant block mt-0.5">Total fulfilled orders</span>
                        </div>
                    </div>

                    <!-- KPI 2: Platform Commission -->
                    <div class="p-4 rounded-2xl bg-surface-container-low border border-surface-container flex flex-col justify-between">
                        <div class="flex items-center justify-between text-outline">
                            <span class="font-mono text-[11px] uppercase tracking-wider font-semibold">Marketplace Fee</span>
                            <span class="material-symbols-outlined text-[18px]">percent</span>
                        </div>
                        <div class="mt-3">
                            <div class="font-heading text-xl font-bold text-error tracking-tight">
                                -₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}
                            </div>
                            <span class="font-sans text-[11px] text-secondary font-medium block mt-0.5">Fixed 10.0% transparent</span>
                        </div>
                    </div>

                    <!-- KPI 3: Settled Disbursements -->
                    <div class="p-4 rounded-2xl bg-surface-container-low border border-surface-container flex flex-col justify-between">
                        <div class="flex items-center justify-between text-outline">
                            <span class="font-mono text-[11px] uppercase tracking-wider font-semibold">Total Disbursed (Paid)</span>
                            <span class="material-symbols-outlined text-[18px] text-on-tertiary-container">verified</span>
                        </div>
                        <div class="mt-3">
                            <div class="font-heading text-xl font-bold text-on-tertiary-container tracking-tight">
                                ₹{{ number_format($kpis['total_settled'] ?? 0, 2) }}
                            </div>
                            <span class="font-sans text-[11px] text-on-surface-variant block mt-0.5">{{ $statusCounts['paid'] ?? 0 }} batches credited</span>
                        </div>
                    </div>

                    <!-- KPI 4: In-Flight Escrow -->
                    <div class="p-4 rounded-2xl bg-surface-container-low border border-surface-container flex flex-col justify-between">
                        <div class="flex items-center justify-between text-outline">
                            <span class="font-mono text-[11px] uppercase tracking-wider font-semibold">In-Flight Escrow</span>
                            <span class="material-symbols-outlined text-[18px] text-secondary">hourglass_top</span>
                        </div>
                        <div class="mt-3">
                            <div class="font-heading text-xl font-bold text-secondary tracking-tight">
                                ₹{{ number_format($kpis['pending_processing'] ?? 0, 2) }}
                            </div>
                            <span class="font-sans text-[11px] text-on-surface-variant block mt-0.5">Scheduled next cycle</span>
                        </div>
                    </div>
                </div>

                <!-- 2-Column Section: Itemized Ledger Breakdown + Destination Bank & Schedule -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <!-- Left: Itemized Ledger & Deductions -->
                    <div class="p-5 rounded-2xl bg-surface-container-low/70 border border-surface-container flex flex-col gap-3.5">
                        <div class="flex items-center justify-between pb-2 border-b border-surface-container-high/60">
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-on-surface">Fee &amp; Deductions Breakdown</span>
                            <span class="font-mono text-[10px] text-on-tertiary-container font-semibold">100% Transparent</span>
                        </div>
                        
                        <div class="flex flex-col gap-2.5 font-sans text-xs">
                            <div class="flex items-center justify-between text-on-surface">
                                <span>Gross Realized Volume</span>
                                <span class="font-mono font-semibold">₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-error">
                                <span class="flex items-center gap-1">
                                    <span>Platform Commission</span>
                                    <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-error-container/30 text-error">10.0%</span>
                                </span>
                                <span class="font-mono font-semibold">-₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-error">
                                <span class="flex items-center gap-1">
                                    <span>Statutory Mandi / APMC Cess</span>
                                    <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-surface-container text-on-surface-variant">Tracked</span>
                                </span>
                                <span class="font-mono font-semibold">-₹{{ number_format($payoutSummary['total_apmc_cess'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-on-surface-variant">
                                <span>GST on Mandi Agricultural Harvest</span>
                                <span class="font-mono text-on-tertiary-container font-semibold">₹0.00 (0% Exempt)</span>
                            </div>
                            <div class="flex items-center justify-between text-on-surface-variant">
                                <span>Listing &amp; Catalog Management Fee</span>
                                <span class="font-mono text-on-tertiary-container font-semibold">₹0.00 (Free)</span>
                            </div>
                            <div class="flex items-center justify-between text-on-surface-variant">
                                <span>Payment Gateway &amp; Direct NEFT Surcharge</span>
                                <span class="font-mono text-on-tertiary-container font-semibold">₹0.00 (Covered)</span>
                            </div>
                            
                            <div class="h-px bg-surface-container-high my-1"></div>
                            
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex flex-col">
                                    <span class="font-heading text-sm font-bold text-on-surface">Net Seller Accrual</span>
                                    <span class="font-mono text-[10px] text-outline">Disbursed + In-Flight Escrow</span>
                                </div>
                                <span class="font-mono text-base font-bold text-on-tertiary-container">
                                    ₹{{ number_format(($kpis['total_settled'] ?? 0) + ($kpis['pending_processing'] ?? 0), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Settlement Route & Batch Telemetry -->
                    <div class="p-5 rounded-2xl bg-surface-container-low/70 border border-surface-container flex flex-col gap-3.5">
                        <div class="flex items-center justify-between pb-2 border-b border-surface-container-high/60">
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-on-surface">Routing Bank &amp; Schedule</span>
                            <span class="font-mono text-[10px] text-secondary font-bold">Automated NEFT</span>
                        </div>

                        <!-- Bank Destination Info -->
                        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-surface-container flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="font-sans text-xs font-bold text-on-surface">{{ $bankName }}</span>
                                @if($hasBankDetails)
                                    <span class="inline-flex items-center gap-0.5 text-on-tertiary-container font-mono text-[10px] font-semibold">
                                        <span class="material-symbols-outlined text-[12px]">verified</span> Primary Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-0.5 text-error font-mono text-[10px] font-semibold">
                                        <span class="material-symbols-outlined text-[12px]">warning</span> Action Required
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between font-mono text-xs text-on-surface-variant">
                                <span>A/C: <strong class="text-on-surface">{{ $maskedBankAcc }}</strong></span>
                                <span>IFSC: <strong class="text-on-surface">{{ $bankIfsc }}</strong></span>
                            </div>
                            <span class="font-mono text-[11px] text-outline">Beneficiary: {{ $shopName }}</span>
                        </div>

                        <!-- Schedule & Batch Status -->
                        <div class="flex flex-col gap-2 font-sans text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Next Settlement Batch:</span>
                                <span class="font-mono font-semibold text-on-surface">{{ $upcomingSettlement['date'] ?? 'Friday' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Pending Batch Transfer:</span>
                                <span class="font-mono font-bold text-secondary">₹{{ number_format($upcomingSettlement['amount'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Average Payout Size:</span>
                                <span class="font-mono font-semibold text-on-surface">₹{{ number_format($payoutSummary['avg_payout'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Settled Batches Count:</span>
                                <span class="font-mono font-semibold text-on-surface">{{ $statusCounts['paid'] ?? 0 }} paid / {{ $statusCounts['all'] ?? 0 }} total</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Bottom Action Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-5 border-t border-surface-container-high/60">
                    <a href="{{ route('seller.payouts.summary') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-secondary hover:underline">
                        <span>Open Fullscreen Payout Statement</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="window.print()" class="h-10 px-4 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-sans text-xs font-semibold flex items-center gap-1.5 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">print</span>
                            <span>Print Summary</span>
                        </button>
                        <button type="button" @click="summaryModalOpen = false" class="h-10 px-5 rounded-xl bg-primary text-on-primary font-sans text-xs font-semibold shadow-sm hover:bg-slate-800 transition-colors">
                            Close
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
