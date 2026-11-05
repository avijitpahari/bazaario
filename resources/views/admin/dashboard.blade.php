@extends('layouts.admin')

@section('title', 'Executive Dashboard')

@section('content')
<div class="flex flex-col w-full gap-space-xl">
    <!-- Top Operational Banner & Dashboard Header -->
    <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-space-lg">
        <div class="flex flex-col gap-1 max-w-2xl">
            <div class="flex items-center gap-2">
                <span class="font-label-sm text-[11px] uppercase tracking-wider text-slate-500 font-medium">Bazaario Regional HQ • Bengal Network</span>
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#F5A623]"></span>
                <span class="font-label-sm text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                    Cluster Sync Operational
                </span>
            </div>
            <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">
                Good morning, {{ Auth::guard('admin')->user()->name ?? 'Rajesh' }}
            </h1>
            <p class="font-body-md text-xs sm:text-sm text-slate-600 leading-relaxed">
                Monitor Bazaario marketplace activity, live auctions, seller operations, and order fulfilment across multi-district delivery corridors.
            </p>
        </div>

        <!-- Right Global Action Bar -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs text-[#0F172A]">
                <span class="material-symbols-outlined text-[18px] text-[#F5A623]">calendar_today</span>
                <span class="font-label-md text-xs font-semibold text-[#0F172A]">{{ date('D, M d, Y') }} &bull; Live Feed</span>
            </div>
            <a href="#site-analytics-section" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200/90 text-[#0F172A] hover:bg-slate-50 transition-colors shadow-2xs font-body-md text-xs font-semibold">
                <span class="material-symbols-outlined text-[18px] text-indigo-600">insights</span>
                <span>View Site Analytics</span>
            </a>
            <a href="{{ route('admin.sellers.approvals') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-[#F5A623] text-[#0F172A] hover:bg-amber-400 transition-colors shadow-2xs font-body-md text-xs font-bold">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                <span>View Pending Seller Approvals ({{ $stats['pending_kyc'] ?? 0 }})</span>
            </a>
        </div>
    </section>

    <!-- Requested Core KPI Grid: View Total Users, Total Sellers, Total Orders, GMV, Pending Seller Approvals, Site Analytics -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-space-md">
        <!-- 1. View Total Users -->
        <a href="{{ route('admin.customers.index') }}" class="group flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:border-amber-400 hover:shadow-md transition-all">
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold">TOTAL USERS</span>
                    <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-amber-500 transition-colors">group</span>
                </div>
                <div class="font-label-lg text-2xl font-bold text-[#0F172A] mt-1">{{ number_format($stats['total_users'] ?? 0) }}</div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-slate-500">{{ number_format($stats['buyers_count'] ?? 0) }} buyers active</span>
                <span class="text-amber-700 font-label-sm text-[10px] font-bold group-hover:underline flex items-center">
                    <span>View Users</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </span>
            </div>
        </a>

        <!-- 2. View Total Sellers -->
        <a href="{{ route('admin.sellers.index') }}" class="group flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:border-amber-400 hover:shadow-md transition-all">
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold">TOTAL SELLERS</span>
                    <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-amber-500 transition-colors">storefront</span>
                </div>
                <div class="font-label-lg text-2xl font-bold text-[#0F172A] mt-1">{{ number_format($stats['total_sellers'] ?? 0) }}</div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-emerald-700 font-medium font-mono">{{ $stats['sellers_count'] ?? 0 }} approved</span>
                <span class="text-amber-700 font-label-sm text-[10px] font-bold group-hover:underline flex items-center">
                    <span>View Sellers</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </span>
            </div>
        </a>

        <!-- 3. View Total Orders -->
        <a href="{{ route('admin.orders.index') }}" class="group flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:border-amber-400 hover:shadow-md transition-all">
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold">TOTAL ORDERS</span>
                    <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-amber-500 transition-colors">receipt_long</span>
                </div>
                <div class="font-label-lg text-2xl font-bold text-[#0F172A] mt-1">{{ number_format($stats['orders_count'] ?? 0) }}</div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-emerald-700 font-medium font-mono">Live Hubs</span>
                <span class="text-amber-700 font-label-sm text-[10px] font-bold group-hover:underline flex items-center">
                    <span>View Orders</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </span>
            </div>
        </a>

        <!-- 4. View Gross Merchandise Value (GMV) -->
        <div class="flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:border-emerald-300 hover:shadow-md transition-all">
            <div class="flex flex-col gap-1 min-w-0">
                <div class="flex items-center justify-between gap-1">
                    <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold truncate" title="Gross Merchandise Value">GMV (SETTLED)</span>
                    <span class="material-symbols-outlined text-[18px] text-emerald-600 shrink-0">currency_rupee</span>
                </div>
                <div class="flex items-baseline gap-1 mt-1 flex-wrap" title="₹{{ $stats['gmv'] ?? '0.00' }}">
                    <span class="font-label-lg text-lg sm:text-xl font-bold text-[#0F172A] tracking-tight leading-tight">
                        {{ $stats['gmv_compact'] ?? ('₹' . ($stats['gmv_whole'] ?? '0')) }}
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono font-medium truncate">
                        (₹{{ $stats['gmv_whole'] ?? '0' }})
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2 bg-slate-50 -mx-4 -mb-4 px-4 pb-2.5 rounded-b-xl border-t border-slate-100 flex items-center justify-between text-[10px] gap-1">
                <span class="text-blue-700 font-mono font-semibold truncate" title="Escrow holds: ₹{{ $stats['escrow_holds'] ?? '0.00' }}">
                    Hold: {{ $stats['escrow_compact'] ?? ('₹' . ($stats['escrow_whole'] ?? '0')) }}
                </span>
                <span class="text-emerald-700 font-label-sm text-[10px] font-bold shrink-0">
                    Settled
                </span>
            </div>
        </div>

        <!-- 5. View Pending Seller Approvals -->
        <a href="{{ route('admin.sellers.approvals') }}" class="group flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:border-amber-400 hover:shadow-md transition-all">
            <div class="flex flex-col gap-1 min-w-0">
                <div class="flex items-center justify-between gap-1">
                    <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold truncate" title="Pending Seller Approvals">PENDING APPROVALS</span>
                    <span class="material-symbols-outlined text-[18px] text-amber-500 shrink-0">pending_actions</span>
                </div>
                <div class="flex items-center justify-between mt-1 gap-1">
                    <span class="font-label-lg text-2xl font-bold text-[#0F172A]">{{ $stats['pending_kyc'] ?? 0 }}</span>
                    @if(($stats['pending_kyc'] ?? 0) > 0)
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 font-label-sm text-[10px] font-bold animate-pulse whitespace-nowrap">Action Needed</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-[10px] font-bold whitespace-nowrap">All Cleared</span>
                    @endif
                </div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100 flex items-center justify-between text-[10px]">
                <span class="text-slate-500 truncate">KYC Verification</span>
                <span class="text-amber-700 font-label-sm text-[10px] font-bold group-hover:underline flex items-center shrink-0">
                    <span>Audit</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </span>
            </div>
        </a>

        <!-- 6. View Site Analytics Quick Link -->
        <a href="#site-analytics-section" class="group flex flex-col justify-between p-4 rounded-xl bg-gradient-to-br from-indigo-900 to-slate-900 text-white shadow-2xs hover:shadow-md transition-all">
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-[10px] tracking-wider text-indigo-200 uppercase font-semibold">SITE ANALYTICS</span>
                    <span class="material-symbols-outlined text-[18px] text-[#F5A623]">insights</span>
                </div>
                <div class="font-label-lg text-xl font-bold text-white mt-1">Telemetry Live</div>
            </div>
            <div class="mt-3 pt-2.5 bg-white/10 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-white/10 flex items-center justify-between">
                <span class="text-[10px] text-indigo-200 font-mono">{{ $analytics['total_products'] ?? 0 }} items &bull; {{ $analytics['active_auctions'] ?? 0 }} auctions</span>
                <span class="text-[#F5A623] font-label-sm text-[10px] font-bold group-hover:underline flex items-center">
                    <span>Explore</span>
                    <span class="material-symbols-outlined text-[14px]">south</span>
                </span>
            </div>
        </a>
    </section>

    <!-- Main Two-Column Layout (8 Cols Analytics + 4 Cols Operations) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl">
        <!-- LEFT COLUMN (8 Columns) -->
        <div class="lg:col-span-8 flex flex-col gap-space-xl min-w-0">
            <!-- 1. Live GMV Velocity & Split Analysis -->
            <div class="p-5 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-headline-sm text-base font-bold text-[#0F172A]">Live GMV & Multi-Channel Hourly Velocity</h3>
                        <p class="font-body-sm text-xs text-slate-500">Real-time checkout volume comparing Direct Retail vs Live Wholesale Auctions.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-label-sm text-xs font-bold border border-emerald-200/60 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                            Live Streaming
                        </span>
                    </div>
                </div>

                <!-- Chart Visual Container with SVG Curves -->
                <div class="relative w-full h-[220px] bg-slate-50/70 rounded-xl p-3 border border-slate-100 flex flex-col justify-between overflow-hidden">
                    <!-- Peak Data Tooltip Overlay -->
                    <div class="absolute top-8 right-24 bg-[#0F172A] text-white px-3 py-1.5 rounded-lg shadow-md z-10 flex flex-col items-center pointer-events-none">
                        <span class="font-label-sm text-[10px] text-slate-300 tracking-wider uppercase">Oct Peak Volume</span>
                        <span class="font-label-md text-xs text-[#F5A623] font-bold">₹6,42,100</span>
                        <div class="w-2 h-2 bg-[#0F172A] rotate-45 -mb-1"></div>
                    </div>

                    <!-- SVG Chart Curves -->
                    <svg class="w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 760 170">
                        <defs>
                            <linearGradient id="auctionAmberGradient" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#F5A623" stop-opacity="0.35"></stop>
                                <stop offset="100%" stop-color="#F5A623" stop-opacity="0.0"></stop>
                            </linearGradient>
                            <linearGradient id="slateMarketGradient" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#0F172A" stop-opacity="0.12"></stop>
                                <stop offset="100%" stop-color="#0F172A" stop-opacity="0.0"></stop>
                            </linearGradient>
                        </defs>
                        <!-- Grid Lines -->
                        <line x1="0" y1="40" x2="760" y2="40" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                        <line x1="0" y1="80" x2="760" y2="80" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                        <line x1="0" y1="120" x2="760" y2="120" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                        <!-- Marketplace Soft Slate Area -->
                        <path d="M 0,140 C 80,135 140,115 210,105 C 280,95 340,110 420,85 C 500,60 560,75 640,50 C 690,35 730,42 760,30 L 760,170 L 0,170 Z" fill="url(#slateMarketGradient)"></path>
                        <path d="M 0,140 C 80,135 140,115 210,105 C 280,95 340,110 420,85 C 500,60 560,75 640,50 C 690,35 730,42 760,30" fill="none" stroke="#0F172A" stroke-linecap="round" stroke-width="2.5"></path>
                        <!-- Live Auction Amber Area -->
                        <path d="M 0,155 C 70,150 150,135 220,130 C 300,125 360,100 430,95 C 510,85 570,60 645,35 C 690,20 730,28 760,15 L 760,170 L 0,170 Z" fill="url(#auctionAmberGradient)"></path>
                        <path d="M 0,155 C 70,150 150,135 220,130 C 300,125 360,100 430,95 C 510,85 570,60 645,35 C 690,20 730,28 760,15" fill="none" stroke="#F5A623" stroke-linecap="round" stroke-width="3"></path>
                        <circle cx="645" cy="35" fill="#F5A623" r="5" stroke="#FFFFFF" stroke-width="2.5"></circle>
                    </svg>

                    <!-- Timeline Labels -->
                    <div class="flex items-center justify-between text-slate-500 font-label-sm text-[10px] pt-1">
                        <span>08:00 AM</span>
                        <span>11:00 AM</span>
                        <span>02:00 PM</span>
                        <span>05:00 PM (Auction Push)</span>
                        <span>08:00 PM (Current)</span>
                        <span>11:00 PM Projected</span>
                    </div>
                </div>

                <!-- Metric Attribution Strips -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-3.5 h-3.5 rounded-full bg-[#0F172A] shrink-0"></div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-body-sm text-xs text-slate-500">Direct Retail Marketplace</span>
                            <div class="flex items-baseline gap-2">
                                <span class="font-label-lg text-sm font-bold text-[#0F172A]">₹29,12,850</span>
                                <span class="font-label-sm text-[11px] text-slate-500 font-semibold">(75.8%)</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50/60 border border-amber-100">
                        <div class="w-3.5 h-3.5 rounded-full bg-[#F5A623] shrink-0"></div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-body-sm text-xs text-amber-900 font-medium">Wholesale & Live Auction Bids</span>
                            <div class="flex items-baseline gap-2">
                                <span class="font-label-lg text-sm font-bold text-[#0F172A]">₹9,30,000</span>
                                <span class="font-label-sm text-[11px] text-amber-800 font-bold">(24.2%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Recent Orders & Multi-Seller Split Table -->
            <div class="p-5 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-headline-sm text-base font-bold text-[#0F172A]">Recent Orders & Multi-Seller Splits</h3>
                        <p class="font-body-sm text-xs text-slate-500">Real-time checkout payloads across coastal and city delivery clusters.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.orders.index') }}" class="font-label-md text-xs text-amber-700 hover:text-slate-900 font-bold transition-colors flex items-center gap-1">
                            <span>View All Orders</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-[#FFFDF8] text-slate-500 font-label-sm text-[11px] uppercase tracking-wider border-b border-slate-100">
                                <th class="py-2.5 px-3 rounded-l-lg">Order Ref</th>
                                <th class="py-2.5 px-3">Customer</th>
                                <th class="py-2.5 px-3">Seller Split Breakdown</th>
                                <th class="py-2.5 px-3 text-right">Settled GMV</th>
                                <th class="py-2.5 px-3">Payment</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3 rounded-r-lg text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-body-sm text-[#0F172A]">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-3 font-label-md font-bold text-[#0F172A] whitespace-nowrap">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:text-amber-600">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-3 font-medium whitespace-nowrap">
                                        {{ $order->delivery_full_name ?? ($order->user?->name ?? 'Buyer') }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[#0F172A] font-label-sm text-[10px] font-semibold">
                                                {{ $order->sellerOrders->count() ?: 1 }} {{ Str::plural('Seller', $order->sellerOrders->count() ?: 1) }}
                                            </span>
                                            <span class="text-slate-500 text-[11px] truncate max-w-[200px]">
                                                {{ $order->sellerOrders->pluck('seller.name')->filter()->implode(', ') ?: 'Bazaario Direct' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 font-label-md text-right font-bold text-[#0F172A] whitespace-nowrap">
                                        ₹ {{ number_format($order->total_amount ?? 0, 2) }}
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="font-label-sm text-slate-600 flex items-center gap-1 uppercase">
                                            <span class="material-symbols-outlined text-[15px] text-emerald-600">payments</span>
                                            {{ str_replace('_', ' ', (string)($order->payment_method ?? 'N/A')) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($order->order_status === 'completed')
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-label-sm text-[10px] font-bold border border-emerald-200/60">Delivered</span>
                                        @elseif($order->order_status === 'processing')
                                            <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-label-sm text-[10px] font-bold border border-blue-200/60">Processing</span>
                                        @elseif($order->order_status === 'cancelled')
                                            <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-label-sm text-[10px] font-bold border border-rose-200/60">Cancelled</span>
                                        @elseif($order->order_status === 'refunded')
                                            <span class="px-2.5 py-0.5 rounded-full bg-purple-50 text-purple-700 font-label-sm text-[10px] font-bold border border-purple-200/60">Refunded</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-label-sm text-[10px] font-bold border border-amber-200/60">In Escrow</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-semibold transition-colors">
                                            Inspect
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400">
                                        No recent orders in this billing period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN (4 Columns) -->
        <div class="lg:col-span-4 flex flex-col gap-space-xl min-w-0">
            <!-- 1. Pending Actions & Approvals Queue -->
            <div class="p-5 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#F5A623]"></span>
                        <h3 class="font-headline-sm text-base font-bold text-[#0F172A]">Action Queue</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 font-label-sm text-[10px] font-bold">
                        {{ count($pendingSellers) }} Pending KYC
                    </span>
                </div>

                <div class="flex flex-col gap-3">
                    @forelse($pendingSellers as $seller)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex flex-col min-w-0">
                                    <span class="font-body-md text-xs font-bold text-[#0F172A] truncate">{{ $seller->shop_name }}</span>
                                    <span class="font-label-sm text-[10px] text-slate-500">{{ $seller->user?->name ?? 'Merchant' }} • {{ $seller->city ?? 'West Bengal' }}</span>
                                </div>
                                <span class="material-symbols-outlined text-[18px] text-amber-600 shrink-0">verified_user</span>
                            </div>
                            <p class="font-body-sm text-[11px] text-slate-600 leading-snug line-clamp-2">
                                {{ $seller->bio ?? 'Merchant application submitted with GSTIN & trade documents.' }}
                            </p>
                            <div class="flex items-center justify-end gap-2 pt-1">
                                <a href="{{ route('admin.sellers.approvals') }}" class="px-3.5 py-1 rounded-lg bg-[#0F172A] text-white hover:bg-slate-800 font-label-sm text-[11px] font-semibold">Audit & Approve</a>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-400">
                            No pending merchant KYC applications.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 2. Top Regional Hubs -->
            <div class="p-5 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col gap-3.5">
                <div class="flex items-center justify-between">
                    <h3 class="font-headline-sm text-base font-bold text-[#0F172A]">Top Regional Hubs</h3>
                    <span class="material-symbols-outlined text-[20px] text-slate-400">share_location</span>
                </div>
                <div class="flex flex-col gap-3">
                    <!-- Hub 1 -->
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-[#0F172A]">1. Kolkata Metro</span>
                            <span class="font-label-md font-bold text-[#0F172A]">₹18.2L</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-[#0F172A] rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                    <!-- Hub 2 -->
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-[#0F172A]">2. Contai & Digha Coastal</span>
                            <span class="font-label-md font-bold text-[#0F172A]">₹11.4L</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-[#F5A623] rounded-full" style="width: 58%"></div>
                        </div>
                    </div>
                    <!-- Hub 3 -->
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-[#0F172A]">3. Howrah & Midnapore</span>
                            <span class="font-label-md font-bold text-[#0F172A]">₹6.8L</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-slate-600 rounded-full" style="width: 38%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Live Auction Pulse Section -->
            <div class="p-5 rounded-xl bg-[#0F172A] text-white shadow-md flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F5A623] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#F5A623]"></span>
                        </span>
                        <h3 class="font-headline-sm text-sm font-bold text-white">Live Auction Pulse</h3>
                    </div>
                    <span class="font-label-sm text-[10px] text-[#F5A623] font-bold">{{ count($liveAuctions) }} Active Lots</span>
                </div>
                <div class="flex flex-col gap-2.5">
                    @forelse($liveAuctions as $auc)
                        <div class="p-3 rounded-lg bg-[#1E293B] flex flex-col gap-1.5 border border-slate-700/50">
                            <div class="flex items-center justify-between">
                                <span class="font-label-sm text-[10px] text-slate-400">LOT #AUC-{{ $auc->id }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-300 font-label-sm text-[9px] font-bold">
                                    {{ $auc->status === 'live' ? 'Bidding Live' : 'Ended' }}
                                </span>
                            </div>
                            <a href="{{ route('admin.auctions.show', $auc->id) }}" class="font-body-md text-xs font-bold text-white hover:text-amber-400 truncate">
                                {{ $auc->product?->name ?? 'Auction Lot' }}
                            </a>
                            <div class="flex items-baseline justify-between mt-0.5">
                                <span class="font-label-sm text-[10px] text-slate-400">Seller: {{ $auc->seller?->name ?? 'Verified' }}</span>
                                <span class="font-label-md text-xs font-bold text-[#F5A623]">₹{{ number_format((float)($auc->current_price ?: ($auc->starting_price ?? 0))) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-center text-xs text-slate-400">
                            No live auction sessions at this moment.
                        </div>
                    @endforelse
                </div>
                <a href="{{ route('admin.auctions.index') }}" class="w-full py-2 rounded-lg bg-[#1E293B] hover:bg-slate-700 text-white font-body-sm text-xs font-semibold transition-colors flex items-center justify-center gap-1.5 border border-slate-700">
                    <span class="material-symbols-outlined text-[16px] text-[#F5A623]">gavel</span>
                    <span>Open Auction Moderation Desk</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ── Comprehensive Site Analytics & Telemetry Section ── -->
    <section id="site-analytics-section" class="flex flex-col gap-space-lg p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs scroll-mt-24">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">monitoring</span>
                </div>
                <div>
                    <h2 class="font-headline-sm text-lg font-bold text-[#0F172A]">Platform Telemetry & Site Analytics</h2>
                    <p class="font-body-sm text-xs text-slate-500">Live operational snapshot across accounts, order conversions, fulfillment pipeline, and catalog health.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-label-sm text-xs font-bold border border-emerald-200/60 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    Live Production DB
                </span>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- Total Users -->
            <a href="{{ route('admin.customers.index') }}" class="p-4 rounded-xl bg-slate-50/70 border border-slate-100 hover:border-slate-300 transition-colors flex flex-col justify-between">
                <span class="text-[10px] font-mono uppercase text-slate-500 font-bold">Total Users</span>
                <div class="font-bold text-xl text-[#0F172A] my-1">{{ number_format($analytics['total_users'] ?? 0) }}</div>
                <span class="text-[11px] text-slate-600 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-amber-500">badge</span>
                    <span>{{ $analytics['buyers_count'] ?? 0 }} buyers</span>
                </span>
            </a>

            <!-- Total Sellers -->
            <a href="{{ route('admin.sellers.index') }}" class="p-4 rounded-xl bg-slate-50/70 border border-slate-100 hover:border-slate-300 transition-colors flex flex-col justify-between">
                <span class="text-[10px] font-mono uppercase text-slate-500 font-bold">Total Sellers</span>
                <div class="font-bold text-xl text-[#0F172A] my-1">{{ number_format($analytics['total_sellers'] ?? 0) }}</div>
                <span class="text-[11px] text-emerald-700 flex items-center gap-1 font-medium">
                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                    <span>{{ $analytics['approved_sellers'] ?? 0 }} approved</span>
                </span>
            </a>

            <!-- Pending Approvals -->
            <a href="{{ route('admin.sellers.approvals') }}" class="p-4 rounded-xl bg-amber-50/60 border border-amber-100 hover:border-amber-300 transition-colors flex flex-col justify-between">
                <span class="text-[10px] font-mono uppercase text-amber-900 font-bold">Pending Approvals</span>
                <div class="font-bold text-xl text-amber-900 my-1">{{ number_format($analytics['pending_sellers'] ?? 0) }}</div>
                <span class="text-[11px] text-amber-800 flex items-center gap-1 font-semibold">
                    <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                    <span>Needs review</span>
                </span>
            </a>

            <!-- Total Orders -->
            <a href="{{ route('admin.orders.index') }}" class="p-4 rounded-xl bg-slate-50/70 border border-slate-100 hover:border-slate-300 transition-colors flex flex-col justify-between">
                <span class="text-[10px] font-mono uppercase text-slate-500 font-bold">Total Orders</span>
                <div class="font-bold text-xl text-[#0F172A] my-1">{{ number_format($analytics['total_orders'] ?? 0) }}</div>
                <span class="text-[11px] text-slate-600 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-emerald-600">local_shipping</span>
                    <span>{{ $analytics['order_statuses']['completed'] ?? 0 }} delivered</span>
                </span>
            </a>

            <!-- Settled GMV -->
            <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-100 flex flex-col justify-between">
                <span class="text-[10px] font-mono uppercase text-slate-500 font-bold">Gross GMV</span>
                <div class="font-bold text-lg text-[#0F172A] my-1">₹{{ $analytics['gmv_formatted'] ?? '0.00' }}</div>
                <span class="text-[11px] text-blue-700 flex items-center gap-1 font-medium">
                    <span class="material-symbols-outlined text-[14px]">lock</span>
                    <span>₹{{ $analytics['escrow_formatted'] ?? '0.00' }} hold</span>
                </span>
            </div>

            <!-- Catalog Items -->
            <a href="{{ route('admin.products.index') }}" class="p-4 rounded-xl bg-slate-50/70 border border-slate-100 hover:border-slate-300 transition-colors flex flex-col justify-between">
                <span class="text-[10px] font-mono uppercase text-slate-500 font-bold">Live Products</span>
                <div class="font-bold text-xl text-[#0F172A] my-1">{{ number_format($analytics['total_products'] ?? 0) }}</div>
                <span class="text-[11px] text-slate-600 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-purple-600">category</span>
                    <span>{{ $analytics['total_categories'] ?? 0 }} categories</span>
                </span>
            </a>
        </div>

        <!-- Fulfillment Pipeline Breakdown & Daily Orders Table -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 pt-2">
            <!-- Order Pipeline Status Bar -->
            <div class="lg:col-span-5 p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-between gap-3">
                <div class="flex items-center justify-between">
                    <span class="font-headline-sm text-xs font-bold text-[#0F172A] uppercase tracking-wider">Order Status Breakdown</span>
                    <span class="font-mono text-[11px] text-slate-500">{{ $analytics['total_orders'] ?? 0 }} Total</span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-2.5 rounded-lg bg-white border border-slate-200/80">
                        <span class="text-[10px] text-slate-500 block uppercase">Delivered</span>
                        <span class="font-bold text-emerald-700 text-sm">{{ $analytics['order_statuses']['completed'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-white border border-slate-200/80">
                        <span class="text-[10px] text-slate-500 block uppercase">In Transit / Proc</span>
                        <span class="font-bold text-blue-700 text-sm">{{ $analytics['order_statuses']['processing'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-white border border-slate-200/80">
                        <span class="text-[10px] text-slate-500 block uppercase">Pending Escrow</span>
                        <span class="font-bold text-amber-700 text-sm">{{ $analytics['order_statuses']['pending'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-white border border-slate-200/80">
                        <span class="text-[10px] text-slate-500 block uppercase">Cancelled</span>
                        <span class="font-bold text-rose-700 text-sm">{{ $analytics['order_statuses']['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Timeline / Volume by Day -->
            <div class="lg:col-span-7 p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-2.5">
                <div class="flex items-center justify-between">
                    <span class="font-headline-sm text-xs font-bold text-[#0F172A] uppercase tracking-wider">Recent Daily Order Velocity</span>
                    <span class="font-label-sm text-[11px] text-slate-500 font-mono">Last Activity Log</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-500 font-label-sm text-[10px] uppercase border-b border-slate-200">
                                <th class="pb-2">Date</th>
                                <th class="pb-2 text-center">Orders</th>
                                <th class="pb-2 text-right">Settled Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60">
                            @forelse($analytics['recent_days'] ?? [] as $day)
                                <tr>
                                    <td class="py-2 font-mono font-medium text-slate-700">{{ date('M d, Y', strtotime($day->date)) }}</td>
                                    <td class="py-2 text-center">
                                        <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-800 font-mono text-[11px] font-bold">
                                            {{ $day->count }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-right font-mono font-bold text-[#0F172A]">
                                        ₹{{ number_format((float)$day->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-slate-400 text-xs">
                                        No recent transaction days recorded.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
