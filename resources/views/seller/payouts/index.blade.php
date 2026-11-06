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
    <!-- Main Payouts Ledger View (Hidden on print when modal is active) -->
    <div class="flex flex-col w-full" :class="summaryModalOpen ? 'print:hidden' : ''">
        <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col gap-1 mb-6">
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-slate-500 uppercase tracking-wider">
            <span>Seller Center</span>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <span>Payouts &amp; Finance</span>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <span class="text-slate-900 font-semibold">Settlements</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div class="max-w-2xl">
                <h1 class="font-heading text-3xl font-bold text-slate-900 tracking-tight">
                    My Payouts &amp; Financial Ledger
                </h1>
                <p class="font-sans text-sm text-slate-500 mt-1">
                    Transparent commission rates, automated weekly bank settlements, and complete order-by-order earnings breakdown for <span class="font-medium text-slate-900">{{ $shopName }}</span>.
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

    <!-- Session Flash Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center gap-3 shadow-2xs">
            <span class="material-symbols-outlined text-emerald-600 text-[22px]">check_circle</span>
            <div class="font-sans text-xs font-semibold">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="mb-6 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 flex items-center gap-3 shadow-2xs">
            <span class="material-symbols-outlined text-sky-600 text-[22px]">info</span>
            <div class="font-sans text-xs font-semibold">{{ session('info') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center gap-3 shadow-2xs">
            <span class="material-symbols-outlined text-rose-600 text-[22px]">error</span>
            <div class="font-sans text-xs font-semibold">{{ session('error') }}</div>
        </div>
    @endif

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
            <!-- Demo Instant Settlement Button (Active only if unsettled amount > 0, disabled otherwise) -->
            @php
                $unsettledScheduled = (float) ($upcomingSettlement['amount'] ?? 0);
                $hasUnsettledBalance = ($unsettledScheduled > 0);
            @endphp
            @if($hasUnsettledBalance)
                <form action="{{ route('seller.payouts.demo-settle') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="h-10 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-heading text-xs font-bold flex items-center gap-1.5 shadow-sm hover:shadow-md transition-all active:scale-95 cursor-pointer" title="Instantly disburse and settle upcoming scheduled amount for demo testing">
                        <span class="material-symbols-outlined text-[17px] text-emerald-200">check_circle</span>
                        <span>⚡ Demo: Settle Amount Now</span>
                    </button>
                </form>
            @else
                <button type="button" disabled class="h-10 px-4 rounded-xl bg-slate-100 border border-slate-200/90 text-slate-400 font-heading text-xs font-bold flex items-center gap-1.5 cursor-not-allowed opacity-60 shadow-none select-none" title="No unsettled balance available. All payouts are settled.">
                    <span class="material-symbols-outlined text-[17px] text-slate-400">check_circle</span>
                    <span>⚡ Demo: Settle Amount Now</span>
                </button>
            @endif

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
                    <span class="text-amber-600 font-semibold">{{ $summary['commission_rate_string'] ?? '10.0%' }}</span>
                    <span class="text-slate-400">{{ $summary['commission_tier_subtitle'] ?? 'Platform Standard rate • No hidden fees' }}</span>
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
                        <span class="font-mono text-[11px] text-slate-500">Contract ID: BZ-MERCHANT-{{ str_pad($sellerId ?? 1, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="font-heading text-base font-bold text-slate-900">
                        Your Tier: {{ $summary['commission_tier'] ?? 'Tier 1 Prime Farmer' }} ({{ $summary['commission_rate_string'] ?? '10.0%' }} Commission)
                    </h3>
                </div>
            </div>
            <div class="bg-slate-50 border border-slate-200/80 px-4 py-2 rounded-xl flex items-center gap-2 font-mono text-xs">
                <span class="text-slate-500 font-medium">Earnings Formula:</span>
                <span class="text-slate-900 font-bold">Gross Order - {{ $summary['commission_rate_string'] ?? '10.0%' }} Platform Fee = {{ number_format(100 - (float)($summary['commission_rate'] ?? 10), 1) }}% Net Seller Payout</span>
            </div>
        </div>
        <div class="bg-slate-50/80 border border-slate-200/60 p-4 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600 text-[20px]">info</span>
                <p class="font-sans text-slate-800">
                    @php
                        $exampleGross = 10000;
                        $commRate = (float)($summary['commission_rate'] ?? 10);
                        $exampleComm = round($exampleGross * ($commRate / 100), 2);
                        $exampleNet = round($exampleGross - $exampleComm, 2);
                    @endphp
                    <span class="font-semibold">Calculation Example:</span> On a ₹10,000 order → <span class="font-semibold text-amber-700">₹{{ number_format($exampleComm, 2) }} Platform Commission ({{ $summary['commission_rate_string'] ?? '10.0%' }})</span> → <span class="font-semibold text-emerald-700">₹{{ number_format($exampleNet, 2) }} Guaranteed Seller Earnings</span>.
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
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                        <span class="material-symbols-outlined text-[28px]">payments</span>
                    </div>
                    <h3 class="font-heading text-base font-bold text-slate-900">No settlements found</h3>
                    <p class="font-sans text-xs text-slate-500 max-w-sm mt-1">
                        Settlements are automatically scheduled upon order fulfillment and processed in weekly batches.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-sans text-xs">
                        <thead>
                            <tr class="font-mono text-[11px] text-slate-500 uppercase tracking-wider bg-slate-50/90 rounded-lg">
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
                        <tbody class="divide-y divide-slate-100">
                            @foreach($payouts as $payout)
                                @php
                                    $isSel = ($focusedPayout && $focusedPayout->id === $payout->id);
                                    $pStatus = strtoupper($payout->status);
                                    $statusBadgeClass = match($payout->status) {
                                        'paid' => 'bg-emerald-50 text-emerald-800 border border-emerald-200/80',
                                        'processing' => 'bg-amber-50 text-amber-900 border border-amber-200/80',
                                        'pending' => 'bg-slate-100 text-slate-700 border border-slate-200',
                                        'failed' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                        default => 'bg-slate-100 text-slate-700 border border-slate-200',
                                    };
                                @endphp
                                <tr class="{{ $isSel ? 'bg-amber-50/50' : 'hover:bg-slate-50/80' }} transition-colors cursor-pointer"
                                    onclick="window.location.href='{{ route('seller.payouts.index', array_merge(request()->query(), ['payout_id' => $payout->id])) }}'">
                                    <td class="py-3.5 px-3">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $payout->status === 'paid' ? 'bg-emerald-500' : ($payout->status === 'processing' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                            <span class="font-mono font-bold text-slate-900">#{{ $payout->payout_reference }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-2">
                                        <div class="flex flex-col">
                                            <span class="font-mono text-slate-900 font-medium">
                                                {{ $payout->sellerOrder ? 'Order #' . $payout->sellerOrder->seller_order_number : 'Batch Settl.' }}
                                            </span>
                                            <span class="font-mono text-[10px] text-slate-500">Escrow Settled</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-2 text-right font-mono text-slate-900">
                                        ₹{{ number_format($payout->gross_amount, 2) }}
                                    </td>
                                    <td class="py-3.5 px-2 text-right font-mono text-rose-600 font-medium">
                                        -₹{{ number_format($payout->commission_amount, 2) }}
                                    </td>
                                    <td class="py-3.5 px-2 text-right font-mono font-bold text-slate-900">
                                        ₹{{ number_format($payout->net_amount, 2) }}
                                    </td>
                                    <td class="py-3.5 px-2 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md {{ $statusBadgeClass }} font-mono text-[10px] font-bold tracking-wider">
                                            {{ $pStatus }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-2 text-right font-mono text-[11px] text-slate-500">
                                        {{ $payout->paid_at ? $payout->paid_at->format('M j, Y') : ($payout->status === 'processing' ? 'Sched. Friday' : 'Pending') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right" onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($payout->status !== 'paid')
                                                <form action="{{ route('seller.payouts.demo-settle-single', $payout) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 rounded-[6px] bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-mono text-[10px] font-bold flex items-center gap-0.5 transition-colors cursor-pointer" title="Settle this payout now (Demo)">
                                                        <span class="material-symbols-outlined text-[13px] text-emerald-600">bolt</span>
                                                        <span>Settle (Demo)</span>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" 
                                                    @click="summaryModalOpen = true" 
                                                    class="px-2 py-1 rounded-[6px] bg-amber-500/15 hover:bg-amber-500/25 text-amber-900 border border-amber-300/60 font-mono text-[10px] font-bold transition-all flex items-center gap-1 cursor-pointer">
                                                <span class="material-symbols-outlined text-[13px]">summarize</span>
                                                <span>Summary</span>
                                            </button>
                                            <a href="{{ route('seller.payouts.show', $payout) }}" 
                                               class="p-1 rounded-[6px] hover:bg-slate-100 text-slate-500 hover:text-slate-900 transition-colors" 
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
                    <div class="pt-3 border-t border-slate-200/80">
                        {{ $payouts->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Right Column: Settlement Inspector (4 cols / 35%) -->
        <div class="lg:col-span-4 flex flex-col gap-4">
            @if($focusedPayout)
                <div class="bg-white p-6 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col gap-5 sticky top-20">
                    <!-- Inspector Header -->
                    <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                        <div class="flex flex-col">
                            <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Settlement Inspector</span>
                            <h2 class="font-heading text-lg font-bold text-slate-900">Payout Details — #{{ $focusedPayout->payout_reference }}</h2>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full {{ $focusedPayout->status === 'paid' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80' : 'bg-amber-50 text-amber-900 border border-amber-200/80' }} font-mono text-[10px] font-bold uppercase">
                            {{ strtoupper($focusedPayout->status) }}
                        </span>
                    </div>

                    <!-- Bank Account Destination Box -->
                    <div class="p-4 bg-slate-50/80 border border-slate-200/70 rounded-xl flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider font-semibold">Settlement Account</span>
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-mono text-[11px] font-semibold">
                                <span class="material-symbols-outlined text-[13px] text-emerald-600">verified</span> Primary Verified
                            </span>
                        </div>
                        <div class="flex items-center gap-3 mt-1">
                            <div class="w-8 h-8 rounded-lg bg-white border border-slate-200/80 flex items-center justify-center font-bold text-slate-900 font-mono text-sm shadow-2xs">
                                H
                            </div>
                            <div class="flex flex-col leading-tight">
                                <span class="font-sans text-xs font-semibold text-slate-900">{{ $bankName }}</span>
                                <span class="font-mono text-[11px] text-slate-500">{{ $shopName }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 mt-1 border-t border-slate-200/60 font-mono text-xs text-slate-700">
                            <span>A/C: <strong class="text-slate-900">{{ $maskedBankAcc }}</strong></span>
                            <span class="text-slate-500">IFSC: <strong class="text-slate-700">{{ $bankIfsc }}</strong></span>
                        </div>
                    </div>

                    <!-- Financial Breakdown Ledger -->
                    <div class="flex flex-col gap-2.5">
                        <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider font-semibold">Financial Breakdown</span>
                        <div class="flex flex-col gap-2 font-sans text-xs">
                            <div class="flex items-center justify-between text-slate-700">
                                <span>Gross Order Value</span>
                                <span class="font-mono font-semibold text-slate-900">₹{{ number_format($focusedPayout->gross_amount, 2) }}</span>
                            </div>
                            @php
                                $focusedCommRate = ($focusedPayout->gross_amount > 0 && $focusedPayout->commission_amount > 0)
                                    ? round(($focusedPayout->commission_amount / $focusedPayout->gross_amount) * 100, 1)
                                    : (float) ($summary['commission_rate'] ?? 10);
                            @endphp
                            <div class="flex items-center justify-between text-rose-600">
                                <span>Bazaario Commission (-{{ number_format($focusedCommRate, 1) }}%)</span>
                                <span class="font-mono font-medium">-₹{{ number_format($focusedPayout->commission_amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500">
                                <span>GST on Commission (18%)</span>
                                <span class="font-mono">₹0.00 <span class="text-[10px] text-emerald-700 font-semibold">(Agro Exempt)</span></span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500">
                                <span>Logistics &amp; Hub Fleet Fee</span>
                                <span class="font-mono">₹0.00 <span class="text-[10px] text-emerald-700 font-semibold">(Covered)</span></span>
                            </div>
                        </div>

                        <!-- Grand Total Highlight -->
                        <div class="bg-slate-900 text-white p-4 rounded-xl flex items-center justify-between mt-2 shadow-xs">
                            <div class="flex flex-col">
                                <span class="font-mono text-[11px] uppercase text-slate-400 font-bold">Net Deposit Payable</span>
                                <span class="font-mono text-[10px] text-slate-400">Direct NEFT Transfer</span>
                            </div>
                            <span class="font-heading text-xl font-bold text-white tracking-tight">
                                ₹{{ number_format($focusedPayout->net_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Settlement Lifecycle Stepper -->
                    <div class="flex flex-col gap-2">
                        <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider font-semibold">Settlement Lifecycle</span>
                        <div class="relative pl-6 flex flex-col gap-3 font-sans text-xs">
                            <div class="absolute left-2.5 top-2 bottom-2 w-0.5 bg-slate-200"></div>
                            <div class="relative flex flex-col">
                                <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full bg-emerald-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-[10px]">check</span>
                                </div>
                                <span class="font-mono font-bold text-slate-900">Step 1: Orders Fulfilled &amp; Escrow Released</span>
                                <span class="text-[11px] text-slate-500">Verified by Bazaario Dispatch Protocol</span>
                            </div>
                            <div class="relative flex flex-col">
                                <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full bg-emerald-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-[10px]">check</span>
                                </div>
                                <span class="font-mono font-bold text-slate-900">Step 2: Batch Ledger Calculated &amp; Locked</span>
                                <span class="text-[11px] text-slate-500">Deductions verified ({{ number_format($focusedCommRate, 1) }}% platform fee)</span>
                            </div>
                            <div class="relative flex flex-col">
                                <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full {{ $focusedPayout->status === 'paid' ? 'bg-emerald-600' : 'bg-amber-500' }} flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </div>
                                <span class="font-mono font-bold {{ $focusedPayout->status === 'paid' ? 'text-slate-900' : 'text-amber-800' }}">Step 3: Direct Bank NEFT Transmission</span>
                                <span class="text-[11px] text-slate-500">Transmitted to clearing gateway</span>
                            </div>
                            <div class="relative flex flex-col">
                                <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full {{ $focusedPayout->status === 'paid' ? 'bg-emerald-600' : 'bg-slate-200' }} flex items-center justify-center">
                                    @if($focusedPayout->status === 'paid')
                                        <span class="material-symbols-outlined text-white text-[10px]">check</span>
                                    @endif
                                </div>
                                <span class="font-mono font-medium {{ $focusedPayout->status === 'paid' ? 'text-slate-900 font-bold' : 'text-slate-400' }}">Step 4: Bank Clearance &amp; Deposit</span>
                                <span class="text-[11px] text-slate-500">{{ $focusedPayout->status === 'paid' ? 'Deposited' : 'Expected Friday 08:00 AM' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        @if($focusedPayout->status !== 'paid')
                            <form action="{{ route('seller.payouts.demo-settle-single', $focusedPayout) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full h-11 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-heading text-xs font-bold flex items-center justify-center gap-2 shadow-xs transition-all active:scale-95 cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">verified</span>
                                    <span>⚡ Demo: Settle This Payout Now</span>
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="w-full h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 font-heading text-xs font-bold flex items-center justify-center gap-2 cursor-not-allowed opacity-75 shadow-none select-none">
                                <span class="material-symbols-outlined text-[18px] text-emerald-600">verified</span>
                                <span>Disbursed &amp; Settled ({{ $focusedPayout->payout_reference }})</span>
                            </button>
                        @endif
                        <button type="button" 
                                @click="summaryModalOpen = true" 
                                class="h-11 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-heading text-xs font-bold flex items-center justify-center gap-2 shadow-xs transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">summarize</span>
                            <span>View Payout Summary</span>
                        </button>
                        <a href="{{ route('seller.payouts.show', $focusedPayout) }}" class="h-11 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-sans text-xs font-semibold flex items-center justify-center gap-2 shadow-xs transition-colors">
                            <span class="material-symbols-outlined text-[16px]">receipt</span>
                            <span>View Full Receipt</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-2xs text-center font-sans text-xs text-slate-500">
                    Select a payout from the ledger to inspect details.
                </div>
            @endif
        </div>
    </div>
    </div>

    <!-- Interactive Payout Summary Modal (Item 78) -->
    <div x-show="summaryModalOpen" 
         x-cloak 
         id="seller-payout-summary-modal-wrapper"
         class="fixed inset-0 z-50 overflow-y-auto print:static print:inset-auto print:z-auto print:overflow-visible print:block print:w-full print:p-0 print:m-0" 
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
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm print:hidden" 
             @click="summaryModalOpen = false"></div>

        <!-- Modal Dialog Container -->
        <div class="flex min-h-screen items-center justify-center p-4 sm:p-6 text-center print:block print:min-h-0 print:p-0 print:m-0 print:w-full">
            <div x-show="summaryModalOpen" 
                 x-transition:enter="ease-out duration-200" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95" 
                 id="seller-payout-summary-modal-card"
                 class="relative w-full max-w-4xl max-h-[92vh] overflow-y-auto transform rounded-3xl bg-white border border-slate-200/80 p-6 sm:p-8 text-left shadow-2xl transition-all print:static print:max-w-none print:w-full print:max-h-none print:overflow-visible print:border-none print:shadow-none print:rounded-none print:p-0 print:m-0 print:transform-none">
                
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

                <!-- Modal Top Ribbon & Close (Screen Only) -->
                <div class="flex items-start justify-between gap-4 pb-5 border-b border-slate-100 print:hidden">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[28px]">summarize</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-900 border border-amber-300/60">
                                    Financial Statement • Item 78
                                </span>
                                <span class="font-mono text-[11px] text-slate-400">•</span>
                                <span class="font-mono text-[11px] text-slate-500">Live Escrow &amp; Settlements</span>
                            </div>
                            <h2 id="modal-payout-summary-title" class="font-heading text-2xl font-bold text-slate-900 tracking-tight mt-0.5">
                                Merchant Payout Summary
                            </h2>
                            <p class="font-sans text-xs text-slate-500 mt-0.5">
                                Consolidated statement of lifetime sales, net disbursements, transparent deductions, and scheduled payouts for <span class="font-semibold text-slate-900">{{ $shopName }}</span>.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-sans text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer" title="Print Statement">
                            <span class="material-symbols-outlined text-[16px]">print</span>
                            <span class="hidden sm:inline">Print</span>
                        </button>
                        <button type="button" @click="summaryModalOpen = false" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                </div>

                <!-- 4 KPI Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 my-6 print:grid-cols-4 print:gap-3 print:my-4 print:break-inside-avoid">
                    <!-- KPI 1: Gross Sales -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between print:bg-slate-50/70 print:border-slate-300 print:shadow-none print:p-3 print:rounded-xl">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Gross Sales Realized</span>
                            <span class="material-symbols-outlined text-[18px]">payments</span>
                        </div>
                        <div class="mt-3">
                            <div class="font-heading text-xl font-bold text-slate-900 tracking-tight">
                                ₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}
                            </div>
                            <span class="font-sans text-[11px] text-slate-500 block mt-0.5">Total fulfilled orders</span>
                        </div>
                    </div>

                    <!-- KPI 2: Platform Commission -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between print:bg-slate-50/70 print:border-slate-300 print:shadow-none print:p-3 print:rounded-xl">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Marketplace Fee</span>
                            <span class="material-symbols-outlined text-[18px]">percent</span>
                        </div>
                        <div class="mt-3">
                            <div class="font-heading text-xl font-bold text-rose-600 tracking-tight">
                                -₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}
                            </div>
                            <span class="font-sans text-[11px] text-amber-800 font-medium block mt-0.5">Fixed {{ $summary['commission_rate_string'] ?? '10.0%' }} transparent</span>
                        </div>
                    </div>

                    <!-- KPI 3: Settled Disbursements -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between print:bg-slate-50/70 print:border-slate-300 print:shadow-none print:p-3 print:rounded-xl">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Total Disbursed (Paid)</span>
                            <span class="material-symbols-outlined text-[18px] text-emerald-600">verified</span>
                        </div>
                        <div class="mt-3">
                            <div class="font-heading text-xl font-bold text-emerald-700 tracking-tight">
                                ₹{{ number_format($kpis['total_settled'] ?? 0, 2) }}
                            </div>
                            <span class="font-sans text-[11px] text-slate-500 block mt-0.5">{{ $statusCounts['paid'] ?? 0 }} batches credited</span>
                        </div>
                    </div>

                    <!-- KPI 4: In-Flight Escrow -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between print:bg-slate-50/70 print:border-slate-300 print:shadow-none print:p-3 print:rounded-xl">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">In-Flight Escrow</span>
                            <span class="material-symbols-outlined text-[18px] text-amber-600">hourglass_top</span>
                        </div>
                        <div class="mt-3">
                            <div class="font-heading text-xl font-bold text-amber-700 tracking-tight">
                                ₹{{ number_format($kpis['pending_processing'] ?? 0, 2) }}
                            </div>
                            <span class="font-sans text-[11px] text-slate-500 block mt-0.5">Scheduled next cycle</span>
                        </div>
                    </div>
                </div>

                <!-- 2-Column Section: Itemized Ledger Breakdown + Destination Bank & Schedule -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6 print:grid-cols-2 print:gap-4 print:my-4 print:break-inside-avoid">
                    <!-- Left: Itemized Ledger & Deductions -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex flex-col gap-3.5 print:bg-white print:border-slate-300 print:p-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-slate-900">Fee &amp; Deductions Breakdown</span>
                            <span class="font-mono text-[10px] text-emerald-700 font-semibold">100% Transparent</span>
                        </div>
                        
                        <div class="flex flex-col gap-2.5 font-sans text-xs">
                            <div class="flex items-center justify-between text-slate-700">
                                <span>Gross Realized Volume</span>
                                <span class="font-mono font-semibold text-slate-900">₹{{ number_format($kpis['lifetime_revenue'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-rose-600">
                                <span class="flex items-center gap-1">
                                    <span>Platform Commission</span>
                                    <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-rose-50 text-rose-700 border border-rose-200">{{ $summary['commission_rate_string'] ?? '10.0%' }}</span>
                                </span>
                                <span class="font-mono font-semibold">-₹{{ number_format($kpis['platform_commission'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-rose-600">
                                <span class="flex items-center gap-1">
                                    <span>Statutory Mandi / APMC Cess</span>
                                    <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">Tracked</span>
                                </span>
                                <span class="font-mono font-semibold">-₹{{ number_format($payoutSummary['total_apmc_cess'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500">
                                <span>GST on Mandi Agricultural Harvest</span>
                                <span class="font-mono text-emerald-700 font-semibold">₹0.00 (0% Exempt)</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500">
                                <span>Listing &amp; Catalog Management Fee</span>
                                <span class="font-mono text-emerald-700 font-semibold">₹0.00 (Free)</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500">
                                <span>Payment Gateway &amp; Direct NEFT Surcharge</span>
                                <span class="font-mono text-emerald-700 font-semibold">₹0.00 (Covered)</span>
                            </div>
                            
                            <div class="h-px bg-slate-200/80 my-1"></div>
                            
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex flex-col">
                                    <span class="font-heading text-sm font-bold text-slate-900">Net Seller Accrual</span>
                                    <span class="font-mono text-[10px] text-slate-400">Disbursed + In-Flight Escrow</span>
                                </div>
                                <span class="font-mono text-base font-bold text-emerald-700">
                                    ₹{{ number_format(($kpis['total_settled'] ?? 0) + ($kpis['pending_processing'] ?? 0), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Settlement Route & Batch Telemetry -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex flex-col gap-3.5 print:bg-white print:border-slate-300 print:p-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-slate-900">Routing Bank &amp; Schedule</span>
                            <span class="font-mono text-[10px] text-amber-800 font-bold">Automated NEFT</span>
                        </div>

                        <!-- Bank Destination Info -->
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200/70 flex flex-col gap-2 print:border-slate-300">
                            <div class="flex items-center justify-between">
                                <span class="font-sans text-xs font-bold text-slate-900">{{ $bankName }}</span>
                                @if($hasBankDetails)
                                    <span class="inline-flex items-center gap-0.5 text-emerald-700 font-mono text-[10px] font-semibold">
                                        <span class="material-symbols-outlined text-[12px] text-emerald-600">verified</span> Primary Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-0.5 text-rose-600 font-mono text-[10px] font-semibold">
                                        <span class="material-symbols-outlined text-[12px]">warning</span> Action Required
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between font-mono text-xs text-slate-600">
                                <span>A/C: <strong class="text-slate-900">{{ $maskedBankAcc }}</strong></span>
                                <span>IFSC: <strong class="text-slate-900">{{ $bankIfsc }}</strong></span>
                            </div>
                            <span class="font-mono text-[11px] text-slate-400">Beneficiary: {{ $shopName }}</span>
                        </div>

                        <!-- Schedule & Batch Status -->
                        <div class="flex flex-col gap-2 font-sans text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Next Settlement Batch:</span>
                                <span class="font-mono font-semibold text-slate-900">{{ $upcomingSettlement['date'] ?? 'Friday' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Pending Batch Transfer:</span>
                                <span class="font-mono font-bold text-amber-700">₹{{ number_format($upcomingSettlement['amount'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Average Payout Size:</span>
                                <span class="font-mono font-semibold text-slate-900">₹{{ number_format($payoutSummary['avg_payout'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Settled Batches Count:</span>
                                <span class="font-mono font-semibold text-slate-900">{{ $statusCounts['paid'] ?? 0 }} paid / {{ $statusCounts['all'] ?? 0 }} total</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Official Print Footer (Print Only) -->
                <div class="hidden print:block mt-6 pt-4 border-t border-slate-300 text-center font-sans text-[10px] text-slate-500 print:break-inside-avoid">
                    <p class="font-semibold text-slate-700">Official Computer-Generated Financial Statement &bull; Bazaario Merchant Settlements Protocol</p>
                    <p class="mt-0.5">All settlements are audited and disbursed via Direct NEFT/RTGS to the verified banking node on file. For financial disputes or reconciliation inquiries, contact merchant-finance@bazaario.in.</p>
                </div>

                <!-- Modal Bottom Action Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-5 border-t border-slate-100 print:hidden">
                    <a href="{{ route('seller.payouts.summary') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-800 hover:text-amber-900 hover:underline">
                        <span>Open Fullscreen Payout Statement</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="window.print()" class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-sans text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">print</span>
                            <span>Print Summary</span>
                        </button>
                        <button type="button" @click="summaryModalOpen = false" class="h-10 px-5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-sans text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>

            </div>
        </div>
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
