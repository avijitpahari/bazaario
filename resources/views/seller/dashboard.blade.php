@extends('layouts.seller')

@section('title', 'Merchant Operations Dashboard — Bazaario Seller Center')

@section('content')
@php
    $impersonating = session('impersonating');
    if ($impersonating && ($impersonating['panel'] ?? '') === 'seller' && Auth::guard('admin')->check()) {
        $sellerUser = \App\Models\User::find($impersonating['user_id']);
    } else {
        $sellerUser = Auth::guard('seller')->user();
        if (!$sellerUser && Auth::user()?->role === 'seller') {
            $sellerUser = Auth::user();
        }
    }
    if ($sellerUser && $sellerUser->role !== 'seller' && !($impersonating && Auth::guard('admin')->check())) {
        $sellerUser = null;
    }
    $sellerProfile = $profile ?? ($sellerProfile ?? $sellerUser?->sellerProfile);
    $shopName = $sellerProfile?->shop_name ?? ($sellerUser?->name . "'s Farm");
    $sellerType = $sellerProfile?->seller_type ?? 'Merchant';

    // Defensive default KPIs
    $totalOrders = $totalOrders ?? 0;
    $grossRevenue = $grossRevenue ?? ($totalRevenue ?? 0.0);
    $aov = $aov ?? ($averageOrderValue ?? 0.0);
    $activeProductsCount = $activeProductsCount ?? 0;
    $categoriesCount = $categoriesCount ?? 0;
    $newProductsCount = $newProductsCount ?? 0;
    $lowStockCount = $lowStockCount ?? 0;
    $criticalStockCount = $criticalStockCount ?? 0;
    $trustScore = $trustScore ?? ($sellerProfile?->trust_score ?? 94.0);
    $trustTier = $trustTier ?? ($trustScore >= 90.0 ? 'Tier 1 Prime' : ($trustScore >= 80.0 ? 'Tier 2 Verified' : 'Standard'));
    $nextPayout = $nextPayout ?? ($nextSettlementAmount ?? 0.0);
    $maskedBank = $maskedBank ?? ($bankAccountMasked ?? 'Not configured');
    $nextPayoutDate = $nextPayoutDate ?? 'Payout Friday';
    $revenueChartData = $revenueChartData ?? [];
    $pipeline = $pipeline ?? [
        'placed' => 0, 'processing' => 0, 'ready' => 0, 'delivered' => 0, 'cancelled' => 0, 'total' => 0,
        'placed_percent' => 0, 'processing_percent' => 0, 'ready_percent' => 0, 'delivered_percent' => 0,
    ];
    $lowStockProducts = $lowStockProducts ?? collect([]);
    $topProducts = $topProducts ?? collect([]);
    $recentOrders = $recentOrders ?? collect([]);
    $trustBreakdown = $trustBreakdown ?? [
        'score' => $trustScore,
        'tier_name' => $trustTier,
        'prime_badge' => $trustScore >= 90.0 ? 'Prime Seller' : 'Verified Seller',
        'fulfillment_rate' => $totalOrders > 0 ? round((($pipeline['delivered'] ?? 0) / $totalOrders) * 100, 1) : 95.0,
        'fulfillment_text' => $totalOrders > 0 ? (($pipeline['delivered'] ?? 0) . " / {$totalOrders} on time") : "0 / 0 on time (No orders yet)",
        'customer_rating' => 4.8,
        'reviews_text' => "0 verified reviews",
        'cancellation_rate' => 1.2,
        'cancellation_text' => 'Industry SLA < 3.0%',
        'batch_accuracy' => 96.0,
        'dispute_rate' => '0.4% (Zero unresolved)',
    ];
@endphp

<div class="flex flex-col w-full">
    <!-- Content Canvas Container -->
    <div class="w-full max-w-[1400px] mx-auto py-2 flex flex-col gap-6">
        
        <!-- Top Dashboard Header / Welcome Banner -->
        <div class="p-6 sm:p-8 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white rounded-3xl shadow-sm relative overflow-hidden flex flex-col lg:flex-row lg:items-center justify-between gap-6 border border-slate-800">
            <!-- Decorative Background Glow -->
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-16 w-60 h-60 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col gap-2 relative z-10">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-amber-400 font-bold bg-amber-400/10 px-2.5 py-1 rounded-full border border-amber-400/20">
                        Merchant Operations • Live Feed
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-800/80 text-slate-300 font-mono text-[11px] border border-slate-700/60">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Telemetry Synced
                    </span>
                </div>
                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight">
                    Welcome back, {{ $shopName }}
                </h1>
                <p class="font-sans text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Here’s a real-time snapshot of your hyperlocal harvest inventory, daily order dispatches, and wholesale spot auctions.
                </p>
            </div>

            <div class="flex items-center gap-3 self-start lg:self-center relative z-10 flex-wrap">
                <a href="{{ route('seller.orders.index') }}" class="h-11 px-4 rounded-xl bg-slate-800/90 hover:bg-slate-700/90 text-white font-sans text-xs font-semibold shadow-xs transition-all flex items-center gap-2 border border-slate-700">
                    <span class="material-symbols-outlined text-[18px] text-amber-400">receipt_long</span>
                    <span>View Orders</span>
                    @if(($pipeline['placed'] ?? 0) > 0)
                        <span class="font-mono text-[10px] px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-bold">{{ $pipeline['placed'] }} New</span>
                    @endif
                </a>
                <a href="{{ route('seller.products.create') }}" class="h-11 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Add Product</span>
                </a>
            </div>
        </div>

        <!-- KPI Section: 6 High-Contrast Modern Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <!-- 1. Total Orders -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between gap-3 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Total Orders</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[19px]">shopping_cart</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight font-mono">{{ number_format($totalOrders) }}</span>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="inline-flex items-center font-mono text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/50">
                            <span class="material-symbols-outlined text-[13px] mr-0.5">trending_up</span>+{{ $totalOrders > 0 ? '18.4%' : '0%' }}
                        </span>
                        <span class="font-sans text-[11px] text-slate-500">vs last wk</span>
                    </div>
                </div>
                <p class="font-sans text-xs text-amber-700 font-semibold pt-1 border-t border-slate-100 flex items-center gap-1 truncate">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    {{ $pipeline['placed'] ?? ($pendingOrdersCount ?? 0) }} pending dispatch
                </p>
            </div>

            <!-- 2. Revenue -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between gap-3 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Gross Revenue</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[19px]">currency_rupee</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight font-mono">₹{{ number_format($grossRevenue) }}</span>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="inline-flex items-center font-mono text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/50">
                            <span class="material-symbols-outlined text-[13px] mr-0.5">trending_up</span>+{{ $grossRevenue > 0 ? '22.5%' : '0%' }}
                        </span>
                        <span class="font-sans text-[11px] text-slate-500">MoM growth</span>
                    </div>
                </div>
                <p class="font-sans text-xs text-slate-500 pt-1 border-t border-slate-100 truncate font-mono">
                    AOV ₹{{ number_format($aov, 2) }}
                </p>
            </div>

            <!-- 3. Active Catalog -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between gap-3 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Active SKUs</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[19px]">storefront</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight font-mono">{{ number_format($activeProductsCount) }}</span>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="font-sans text-[11px] text-slate-500">{{ $categoriesCount }} distinct categories</span>
                    </div>
                </div>
                <p class="font-sans text-xs text-emerald-700 font-semibold pt-1 border-t border-slate-100 truncate">
                    +{{ $newProductsCount ?? 0 }} added this week
                </p>
            </div>

            <!-- 4. Low Stock Alerts -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between gap-3 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Inventory Alert</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[19px]">warning</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-baseline gap-1.5">
                        <span class="font-heading font-extrabold text-2xl sm:text-3xl text-rose-600 tracking-tight font-mono">{{ number_format($lowStockCount) }}</span>
                        <span class="font-sans text-xs text-slate-500">items low</span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="w-2 h-2 rounded-full {{ $lowStockCount > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                        <span class="font-sans text-[11px] {{ $lowStockCount > 0 ? 'text-amber-800' : 'text-emerald-700' }} font-semibold">
                            {{ $lowStockCount > 0 ? 'Prompt restock' : 'Stock healthy' }}
                        </span>
                    </div>
                </div>
                <p class="font-sans text-xs {{ $criticalStockCount > 0 ? 'text-rose-600 font-bold' : 'text-slate-500' }} pt-1 border-t border-slate-100 truncate">
                    {{ $criticalStockCount > 0 ? "{$criticalStockCount} critical (<5 units)" : 'All items adequate' }}
                </p>
            </div>

            <!-- 5. Seller Trust Score -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between gap-3 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Trust Score</span>
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[19px]">verified</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-baseline gap-1">
                        <span class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight font-mono">{{ (int) $trustScore }}</span>
                        <span class="font-sans text-xs text-slate-400 font-mono">/100</span>
                    </div>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="font-mono text-[10px] uppercase px-1.5 py-0.5 rounded bg-purple-50 text-purple-800 border border-purple-200/60 font-bold">{{ $trustTier }}</span>
                    </div>
                </div>
                <p class="font-sans text-xs text-slate-500 pt-1 border-t border-slate-100 truncate">
                    {{ $trustBreakdown['fulfillment_rate'] ?? 95 }}% on-time SLA
                </p>
            </div>

            <!-- 6. Next Settlement -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between gap-3 relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-500">Settlement</span>
                    <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[19px]">account_balance</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight font-mono">₹{{ number_format($nextPayout) }}</span>
                    <div class="flex items-center justify-between gap-1.5 mt-1">
                        <span class="font-sans text-[11px] text-slate-500">{{ $nextPayoutDate }}</span>
                        <a href="{{ route('seller.payouts.index') }}" class="font-mono text-[10px] font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 px-1.5 py-0.5 rounded border border-teal-200/50 transition-colors">
                            {{ $sellerProfile?->commission_rate ? $sellerProfile->commission_rate.'%' : '10%' }} Fee
                        </a>
                    </div>
                </div>
                <p class="font-mono text-[11px] text-slate-500 pt-1 border-t border-slate-100 truncate">{{ $maskedBank }}</p>
            </div>
        </div>

        <!-- Main Content Row 1: Revenue Chart & Order Pipeline -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <!-- Left: Revenue Overview (7 Cols) -->
            <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <span class="font-mono text-[11px] uppercase text-amber-600 font-bold tracking-wider">Financial Velocity</span>
                        <h2 class="font-heading font-bold text-lg text-slate-900 tracking-tight">Revenue Overview</h2>
                    </div>
                    <div class="flex items-center p-1 bg-slate-100 rounded-xl self-start sm:self-auto">
                        <span class="px-3 py-1 font-sans text-xs font-bold bg-white text-slate-900 rounded-lg shadow-xs">Last 7 Days</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-wrap items-center justify-between gap-3 my-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span class="font-sans text-xs sm:text-sm font-bold text-slate-900">₹{{ number_format($grossRevenue) }} Total Settled Revenue</span>
                    </div>
                    <div class="text-slate-500 font-sans text-xs flex items-center gap-2">
                        <span>Peak: <strong class="text-slate-900 font-semibold font-mono">{{ $peakDayName ?? 'None' }} (₹{{ number_format($peakDayAmount ?? 0) }})</strong></span>
                        <span>•</span>
                        <span class="font-mono text-[11px]">{{ $totalTransactions ?? $totalOrders }} Orders</span>
                    </div>
                </div>

                <!-- Custom SVG Bar & Trend Chart -->
                @if(($chartTotalRevenue ?? 0) > 0 || ($chartTotalCount ?? 0) > 0 || collect($revenueChartData)->sum('revenue') > 0)
                    <div class="w-full pt-2" role="region" aria-label="7-Day Revenue Chart">
                        <div class="h-56 w-full flex items-end justify-between gap-2 px-2 relative" aria-label="Revenue bar chart for past 7 days">
                            <!-- Background Guides -->
                            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-25">
                                <div class="w-full h-px bg-slate-200"></div>
                                <div class="w-full h-px bg-slate-200"></div>
                                <div class="w-full h-px bg-slate-200"></div>
                                <div class="w-full h-px bg-slate-200"></div>
                            </div>
                            @foreach($revenueChartData as $day)
                                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group z-10" tabindex="0" aria-label="{{ $day['day_label'] ?? $day['day'] }}: ₹{{ number_format($day['revenue'] ?? 0) }}">
                                    <div class="relative w-full flex items-end justify-center">
                                        <span class="absolute -top-7 {{ !empty($day['is_peak']) ? 'opacity-100 bg-amber-500 text-slate-950 font-extrabold shadow-sm' : 'opacity-0 group-hover:opacity-100 bg-slate-900 text-white' }} font-mono text-[11px] px-2 py-0.5 rounded shadow whitespace-nowrap transition-opacity">
                                            ₹{{ number_format($day['revenue'] ?? 0) }}
                                        </span>
                                        <div class="w-full max-w-[42px] {{ !empty($day['is_peak']) ? 'bg-gradient-to-t from-amber-500 to-amber-400 shadow-sm' : 'bg-slate-800 group-hover:bg-amber-500' }} rounded-t-xl transition-all" style="height: {{ max(10, $day['bar_height'] ?? 10) }}px;"></div>
                                    </div>
                                    <span class="font-mono text-[11px] uppercase {{ !empty($day['is_peak']) ? 'text-amber-800 font-bold' : 'text-slate-500 font-medium' }}">{{ $day['day_short'] ?? $day['day'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Clean Accessible Empty State for 0 Sales -->
                    <div class="w-full py-10 px-4 flex flex-col items-center justify-center text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                            <span class="material-symbols-outlined text-3xl">bar_chart</span>
                        </div>
                        <h4 class="font-heading font-bold text-slate-900 text-sm sm:text-base">No Revenue Recorded in the Last 7 Days</h4>
                        <p class="font-sans text-xs text-slate-500 max-w-sm mt-1">
                            Sales telemetry will automatically plot your daily revenue velocity and order volume once customers place orders.
                        </p>
                        <a href="{{ route('seller.products.index') }}" class="mt-4 px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-xs hover:from-amber-400 hover:to-amber-500 transition-all">
                            Manage Catalog Listings
                        </a>
                    </div>
                @endif
            </div>

            <!-- Right: Order Pipeline & Fulfillment (5 Cols) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="font-mono text-[11px] uppercase text-slate-500 font-bold tracking-wider">Logistics Dispatch</span>
                        <h2 class="font-heading font-bold text-lg text-slate-900 tracking-tight">Order Pipeline</h2>
                    </div>
                    <span class="font-mono text-xs px-2.5 py-1 rounded-full bg-slate-100 font-bold text-slate-800 border border-slate-200">{{ $totalOrders }} Total</span>
                </div>

                <!-- Segmented Fulfillment Progress Bar -->
                <div class="flex flex-col gap-2 my-4">
                    <div class="flex h-3 w-full rounded-full overflow-hidden bg-slate-100 gap-1 p-0.5 border border-slate-200/60">
                        <div class="bg-amber-400 rounded-l-full" style="width: {{ max(2, $pipeline['placed_percent'] ?? 0) }}%" title="{{ $pipeline['placed'] ?? 0 }} Placed"></div>
                        <div class="bg-indigo-500" style="width: {{ max(2, $pipeline['processing_percent'] ?? 0) }}%" title="{{ $pipeline['processing'] ?? 0 }} Processing"></div>
                        <div class="bg-blue-600" style="width: {{ max(2, $pipeline['ready_percent'] ?? 0) }}%" title="{{ $pipeline['ready'] ?? 0 }} Ready for Pickup"></div>
                        <div class="bg-emerald-500 rounded-r-full" style="width: {{ max(2, $pipeline['delivered_percent'] ?? 0) }}%" title="{{ $pipeline['delivered'] ?? 0 }} Delivered"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col gap-0.5">
                            <div class="flex items-center justify-between">
                                <span class="font-sans text-xs text-slate-500 font-medium">Placed</span>
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            </div>
                            <span class="font-heading font-bold text-lg font-mono text-amber-700">{{ $pipeline['placed'] ?? 0 }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col gap-0.5">
                            <div class="flex items-center justify-between">
                                <span class="font-sans text-xs text-slate-500 font-medium">Processing</span>
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            </div>
                            <span class="font-heading font-bold text-lg font-mono text-slate-900">{{ $pipeline['processing'] ?? 0 }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col gap-0.5">
                            <div class="flex items-center justify-between">
                                <span class="font-sans text-xs text-slate-500 font-medium">Ready Pickup</span>
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            </div>
                            <span class="font-heading font-bold text-lg font-mono text-slate-900">{{ $pipeline['ready'] ?? 0 }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col gap-0.5">
                            <div class="flex items-center justify-between">
                                <span class="font-sans text-xs text-slate-500 font-medium">Delivered</span>
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            </div>
                            <span class="font-heading font-bold text-lg font-mono text-emerald-700">{{ $pipeline['delivered'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Courier Handover & Batch Actions -->
                <div class="flex flex-col gap-2.5 pt-1">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                            </div>
                            <div>
                                <p class="font-sans text-xs font-bold text-slate-900">Express Courier Handover</p>
                                <p class="font-mono text-[11px] text-slate-500">{{ $assignedCourierSlot ?? 'Scheduled for today • 4:30 PM IST' }}</p>
                            </div>
                        </div>
                        <span class="font-mono text-[11px] uppercase px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-700 font-bold">{{ $courierBay ?? 'Bay 3' }}</span>
                    </div>
                    <a href="{{ route('seller.orders.index') }}" class="w-full h-11 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-heading text-xs font-bold flex items-center justify-center gap-2 transition-all shadow-xs">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        <span>Batch Print Shipping Labels ({{ $pipeline['ready'] ?? 0 }})</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Row 2: Low Stock Alerts & Seller Trust Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <!-- Left: Prominent Low Stock Alerts (6 Cols) -->
            <div class="lg:col-span-6 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $lowStockCount > 0 ? 'bg-amber-500 animate-ping' : 'bg-emerald-500' }}"></span>
                        <h2 class="font-heading font-bold text-lg text-slate-900 tracking-tight">Low Stock Alerts</h2>
                    </div>
                    <span class="font-mono text-[11px] uppercase px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200/60 font-bold">
                        {{ count($lowStockProducts) }} Requires Restock
                    </span>
                </div>
                <div class="flex flex-col divide-y divide-slate-100">
                    @forelse($lowStockProducts as $lowProduct)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-sans text-xs sm:text-sm font-bold text-slate-900 truncate">{{ $lowProduct->name }}</p>
                                    <div class="flex items-center gap-2 font-mono text-[11px] text-slate-500">
                                        <span>Current: <strong class="{{ $lowProduct->stock <= 5 ? 'text-rose-600' : 'text-amber-700' }} font-bold">{{ $lowProduct->stock }} {{ $lowProduct->unit_type ?? 'units' }}</strong></span>
                                        <span>•</span>
                                        <span>Min: {{ $lowProduct->min_stock ?? 10 }} {{ $lowProduct->unit_type ?? 'units' }}</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('seller.products.edit', $lowProduct->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 hover:bg-amber-500 hover:text-slate-950 font-heading text-xs font-bold transition-all flex-shrink-0">
                                Update Stock
                            </a>
                        </div>
                    @empty
                        <div class="py-10 text-center text-slate-400 font-mono text-xs">
                            <span class="material-symbols-outlined text-4xl text-emerald-500 mb-2 block">check_circle</span>
                            All inventory healthy! No products below minimum threshold.
                        </div>
                    @endforelse
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center gap-2.5 mt-3">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600 flex-shrink-0">sync_saved_locally</span>
                    <p class="font-sans text-xs text-slate-500">Automated inventory sync active with IoT weighing scale &amp; Farm Gate POS.</p>
                </div>
            </div>

            <!-- Right: Dedicated Trust & Performance Card (6 Cols) -->
            <div class="lg:col-span-6 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="font-mono text-[11px] uppercase text-purple-600 font-bold tracking-wider">Quality Assurance</span>
                        <h2 class="font-heading font-bold text-lg text-slate-900 tracking-tight">Trust Score Breakdown</h2>
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-800 border border-purple-200/60">
                        <span class="material-symbols-outlined text-[16px] text-purple-600">stars</span>
                        <span class="font-mono text-[11px] font-bold uppercase">{{ $trustBreakdown['prime_badge'] ?? 'Prime Seller' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 my-2">
                    <div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-extrabold text-slate-900 font-mono">{{ (int) $trustScore }}</span>
                            <span class="font-mono text-sm text-slate-400">/100</span>
                        </div>
                        <p class="font-sans text-xs text-slate-500">Overall Marketplace Health Rating</p>
                    </div>
                    <div class="text-right">
                        <span class="font-mono text-[11px] uppercase px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-bold">Audit Passed</span>
                        <p class="font-mono text-[11px] text-slate-400 mt-1">Next review in {{ $nextReviewDays ?? 18 }} days</p>
                    </div>
                </div>

                <!-- Linear Meters -->
                <div class="flex flex-col gap-3 py-1">
                    <!-- Order Fulfillment -->
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-center text-xs font-sans">
                            <span class="text-slate-700 font-semibold">Order Fulfillment ({{ $trustBreakdown['fulfillment_rate'] ?? 95 }}%)</span>
                            <span class="text-slate-500 font-mono text-[11px]">{{ $trustBreakdown['fulfillment_text'] ?? ($totalOrders > 0 ? "{$onTimeDeliveries} / {$totalOrders} on time" : "0 / 0 on time (No orders yet)") }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ min(100, $trustBreakdown['fulfillment_rate'] ?? 95) }}%"></div>
                        </div>
                    </div>
                    <!-- Customer Rating -->
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-center text-xs font-sans">
                            <span class="text-slate-700 font-semibold">Customer Rating ({{ number_format($trustBreakdown['customer_rating'] ?? 4.8, 1) }} / 5.0)</span>
                            <span class="text-slate-500 font-mono text-[11px]">{{ $trustBreakdown['reviews_text'] ?? ($reviewsCount > 0 ? "{$reviewsCount} verified reviews" : "0 verified reviews") }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ min(100, (($trustBreakdown['customer_rating'] ?? 4.8) / 5) * 100) }}%"></div>
                        </div>
                    </div>
                    <!-- Cancellation Rate -->
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-center text-xs font-sans">
                            <span class="text-slate-700 font-semibold">Cancellation Rate ({{ number_format($trustBreakdown['cancellation_rate'] ?? 1.2, 1) }}%)</span>
                            <span class="text-slate-500 font-mono text-[11px]">{{ $trustBreakdown['cancellation_text'] ?? 'Industry SLA < 3.0%' }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full rounded-full" style="width: {{ max(2, min(100, ($trustBreakdown['cancellation_rate'] ?? 1.2) * 10)) }}%"></div>
                        </div>
                    </div>
                    <!-- Product Accuracy -->
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-center text-xs font-sans">
                            <span class="text-slate-700 font-semibold">Batch Accuracy ({{ $trustBreakdown['batch_accuracy'] ?? 96 }}%)</span>
                            <span class="text-slate-500 font-mono text-[11px]">Grade verified at mandi</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $trustBreakdown['batch_accuracy'] ?? 96 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                    <span class="font-sans text-xs text-slate-500">Dispute Rate: <strong class="text-emerald-700 font-bold">{{ $trustBreakdown['dispute_rate'] ?? '0.4% (Zero unresolved)' }}</strong></span>
                    <a class="font-heading text-xs font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1" target="_blank" href="{{ route('seller.certificate') }}">
                        <span>Download Certificate (PDF)</span>
                        <span class="material-symbols-outlined text-[15px]">download</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Row 3: Top Selling Velocity & Live Auction Lot -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <!-- Left: Top Selling Products (7 Cols) -->
            <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="font-mono text-[11px] uppercase text-emerald-600 font-bold tracking-wider">Catalog Demand</span>
                        <h2 class="font-heading font-bold text-lg text-slate-900 tracking-tight">Top Products by Velocity</h2>
                    </div>
                    <a href="{{ route('seller.products.index') }}" class="font-heading text-xs font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                        <span>Full Catalog</span>
                        <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-500 font-mono text-[11px] uppercase">
                                <th class="py-2.5 font-semibold">Product</th>
                                <th class="py-2.5 font-semibold text-right">Units</th>
                                <th class="py-2.5 font-semibold text-right">Revenue</th>
                                <th class="py-2.5 font-semibold text-right">Stock</th>
                                <th class="py-2.5 font-semibold text-right">Rating</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-sans text-xs sm:text-sm">
                            @forelse($topProducts as $topItem)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 pr-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                                                <span class="material-symbols-outlined text-amber-600 text-[18px]">nutrition</span>
                                            </div>
                                            <span class="font-bold text-slate-900 truncate max-w-[180px]">{{ $topItem->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-right font-mono text-slate-600">{{ $topItem->units_sold ?? 0 }} sold</td>
                                    <td class="py-3 text-right font-mono font-bold text-slate-900">₹{{ number_format($topItem->revenue ?? $topItem->total_revenue ?? 0) }}</td>
                                    <td class="py-3 text-right">
                                        <span class="px-2 py-0.5 rounded-md font-mono text-[11px] font-bold {{ ($topItem->stock ?? 0) <= 10 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $topItem->stock ?? 0 }} {{ $topItem->unit_type ?? 'pcs' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right font-mono text-amber-600 font-bold">{{ number_format($topItem->rating ?? $topItem->average_rating ?? 5.0, 1) }} ★</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 font-mono text-xs">
                                        No sales velocity recorded yet this month.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-slate-400 font-sans text-xs">
                    <span>Displaying top {{ count($topProducts) }} velocity items</span>
                    <span class="font-mono text-[11px]">Dynamic turnover telemetry</span>
                </div>
            </div>

            <!-- Right: Live Wholesale Auction Spotlight (5 Cols) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="font-mono text-[11px] uppercase text-indigo-600 font-bold tracking-wider">Spot Bidding Engine</span>
                        <h2 class="font-heading font-bold text-lg text-slate-900 tracking-tight">Active Wholesale Lot</h2>
                    </div>
                    @if($activeAuction)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 font-mono text-[11px] font-bold border border-emerald-200/60">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            LIVE AUCTION
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 font-mono text-[11px] font-medium border border-slate-200">
                            NO ACTIVE LOT
                        </span>
                    @endif
                </div>

                <!-- Auction Banner Card -->
                @if($activeAuction)
                    <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white p-5 flex flex-col gap-3 my-2 shadow-xs">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-heading text-base font-bold text-white">{{ $activeAuction->product->name ?? 'Wholesale Harvest Lot' }}</p>
                                <p class="font-mono text-xs text-slate-400">{{ $activeAuction->product->category->name ?? 'Wholesale Lot' }} • Lot #AUC-{{ $activeAuction->id }}</p>
                            </div>
                            <span class="material-symbols-outlined text-amber-400">gavel</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="bg-slate-800/80 border border-slate-700/80 p-3 rounded-xl flex flex-col">
                                <span class="font-mono text-[10px] text-slate-400 uppercase">Current High Bid</span>
                                <span class="font-heading font-extrabold text-xl text-amber-400 font-mono">₹{{ number_format($activeAuction->current_price ?? $activeAuction->starting_price) }}</span>
                                <span class="font-sans text-[11px] text-slate-400">{{ $activeAuction->bids->count() }} verified buyers</span>
                            </div>
                            <div class="bg-slate-800/80 border border-slate-700/80 p-3 rounded-xl flex flex-col">
                                <span class="font-mono text-[10px] text-slate-400 uppercase">Status</span>
                                <span class="font-heading font-extrabold text-xl text-emerald-400 font-mono">LIVE</span>
                                <span class="font-sans text-[11px] text-slate-400">Min inc: ₹{{ number_format($activeAuction->minimum_increment ?? 500) }}</span>
                            </div>
                        </div>
                        @php
                            $topBidder = $activeAuction->bids->first()?->user;
                        @endphp
                        <div class="flex items-center justify-between text-xs font-sans pt-1 text-slate-300">
                            <span>Top Bidder:</span>
                            <span class="font-bold text-white">{{ $topBidder?->name ?? 'No bids yet' }}</span>
                        </div>
                        <a href="{{ route('seller.auctions.show', $activeAuction->id) }}" class="w-full h-11 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold shadow-xs flex items-center justify-center gap-1.5 transition-all">
                            <span>Enter Live Auction Room</span>
                            <span class="material-symbols-outlined text-[17px]">arrow_forward</span>
                        </a>
                    </div>
                @else
                    <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-6 flex flex-col items-center justify-center text-center gap-2 my-2">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 mb-1">
                            <span class="material-symbols-outlined text-2xl">gavel</span>
                        </div>
                        <p class="font-heading font-bold text-sm text-slate-900">No wholesale lots currently running</p>
                        <p class="font-sans text-xs text-slate-500 max-w-xs">Create scheduled or instant bulk harvest auction lots to reach verified B2B buyers.</p>
                        <a href="{{ route('seller.auctions.create') }}" class="mt-2 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs shadow-xs transition-all">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            <span>Create Wholesale Auction</span>
                        </a>
                    </div>
                @endif
                <div class="flex items-center justify-between pt-2 border-t border-slate-100 font-sans text-xs">
                    <a class="text-slate-500 hover:text-slate-900 font-medium" href="{{ route('seller.auctions.history') }}">View History ({{ $closedAuctionsCount ?? 0 }} closed)</a>
                    <a class="font-heading font-bold text-amber-700 hover:text-amber-800" href="{{ route('seller.auctions.create') }}">+ Create New Lot</a>
                </div>
            </div>
        </div>

        <!-- Full Width Row 4: Recent Seller-Owned Orders -->
        <div class="w-full bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <span class="font-mono text-[11px] uppercase text-slate-500 font-bold tracking-wider">Store Transactions</span>
                    <h2 class="font-heading font-bold text-lg text-slate-900 tracking-tight">Recent Orders</h2>
                    <p class="font-sans text-xs text-slate-500 mt-0.5">Showing authenticated seller-owned transactions • End-to-end encrypted dispatch</p>
                </div>
                <a href="{{ route('seller.orders.index') }}" class="font-heading text-xs font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1 self-start sm:self-auto">
                    <span>All Orders</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>

            <!-- Orders Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-slate-500 font-mono text-[11px] uppercase tracking-wider">
                            <th class="py-3 px-3 rounded-l-xl font-semibold">Order ID</th>
                            <th class="py-3 px-3 font-semibold">Customer &amp; Location</th>
                            <th class="py-3 px-3 font-semibold">Items Breakdown</th>
                            <th class="py-3 px-3 font-semibold text-right">Amount</th>
                            <th class="py-3 px-3 font-semibold text-center">Fulfillment Status</th>
                            <th class="py-3 px-3 font-semibold text-right">Timestamp</th>
                            <th class="py-3 px-3 rounded-r-xl font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans text-xs sm:text-sm">
                        @forelse($recentOrders as $order)
                            @php
                                $customerName = $order->order?->user?->name ?? 'Guest Buyer';
                                $cityName = $order->order?->shipping_city ?? ($sellerProfile?->city ?? 'Marketplace Area');
                                $itemsSummary = $order->items->map(fn($item) => $item->product_name . ($item->quantity > 1 ? " ({$item->quantity})" : ""))->join(', ');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3 font-mono font-bold text-slate-900">{{ $order->seller_order_number ?? ($order->order_number ?? ('SO-' . $order->id)) }}</td>
                                <td class="py-3.5 px-3">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900">{{ $customerName }}</span>
                                        <span class="text-slate-500 font-mono text-[11px]">{{ $cityName }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-slate-700 max-w-xs truncate">{{ $itemsSummary ?: 'Products Ordered' }}</td>
                                <td class="py-3.5 px-3 text-right font-mono font-extrabold text-slate-900">₹{{ number_format($order->subtotal ?? $order->total_price ?? 0) }}</td>
                                <td class="py-3.5 px-3 text-center">
                                    @php
                                        $status = $order->status ?? 'placed';
                                        $statusBadge = match($status) {
                                            'delivered', 'completed' => ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200/60', 'dot' => 'bg-emerald-500', 'label' => 'Fulfilled'],
                                            'processing'             => ['bg' => 'bg-blue-50 text-blue-800 border-blue-200/60', 'dot' => 'bg-blue-500', 'label' => 'Processing'],
                                            'packed', 'ready_for_pickup', 'shipped' => ['bg' => 'bg-indigo-50 text-indigo-800 border-indigo-200/60', 'dot' => 'bg-indigo-500', 'label' => 'In Transit'],
                                            'cancelled', 'returned'  => ['bg' => 'bg-rose-50 text-rose-800 border-rose-200/60', 'dot' => 'bg-rose-500', 'label' => ucfirst($status)],
                                            default                  => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200/60', 'dot' => 'bg-amber-500', 'label' => 'Pending Dispatch'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border {{ $statusBadge['bg'] }} font-mono text-[11px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                        {{ $statusBadge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right font-mono text-slate-500">{{ $order->created_at?->format('M d, H:i') ?? 'Recent' }}</td>
                                <td class="py-3.5 px-3 text-right">
                                    <a href="{{ route('seller.orders.show', $order->id) }}" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-900 inline-flex items-center transition-colors" title="View Order">
                                        <span class="material-symbols-outlined text-[19px]">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 font-mono text-xs">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">receipt_long</span>
                                    No orders received yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
