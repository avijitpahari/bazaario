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
                <span class="font-label-md text-xs font-semibold text-[#0F172A]">{{ date('D, M d • Live Feed') }}</span>
            </div>
            <button class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200/90 text-[#0F172A] hover:bg-slate-50 transition-colors shadow-2xs font-body-md text-xs font-medium">
                <span class="material-symbols-outlined text-[18px] text-slate-500">file_download</span>
                <span>Download Daily Report</span>
            </button>
            <a href="{{ route('admin.sellers.approvals') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-[#F5A623] text-[#0F172A] hover:bg-amber-400 transition-colors shadow-2xs font-body-md text-xs font-bold">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Review Pending Sellers</span>
            </a>
        </div>
    </section>

    <!-- KPI Grid (6 High-Density Cards) -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-space-md">
        <!-- Card 1: Gross Merchandise Value -->
        <div class="flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md transition-shadow">
            <div class="flex flex-col gap-1">
                <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold">GROSS MERCHANDISE VALUE</span>
                <div class="font-label-lg text-2xl font-bold text-[#0F172A] mt-1">₹{{ $stats['gmv'] ?? '38,42,850' }}</div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100">
                <div class="flex items-center gap-1.5 text-emerald-700 font-label-sm text-[11px] font-semibold">
                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                    <span>+18.4% vs last week</span>
                </div>
                <p class="font-body-sm text-[10px] text-slate-500 mt-0.5 truncate">3,412 transactions processed</p>
            </div>
        </div>

        <!-- Card 2: Total Orders -->
        <div class="flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md transition-shadow">
            <div class="flex flex-col gap-1">
                <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold">VOLUME ACROSS HUBS</span>
                <div class="font-label-lg text-2xl font-bold text-[#0F172A] mt-1">{{ number_format($stats['orders_count'] ?? 1482) }}</div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100">
                <div class="flex items-center gap-1.5 text-emerald-700 font-label-sm text-[11px] font-semibold">
                    <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                    <span>+12.1% dispatched</span>
                </div>
                <p class="font-body-sm text-[10px] text-slate-500 mt-0.5 truncate">94.8% on-time fulfilment</p>
            </div>
        </div>

        <!-- Card 3: Active Sellers -->
        <div class="flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md transition-shadow">
            <div class="flex flex-col gap-1">
                <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold">VERIFIED MERCHANTS</span>
                <div class="font-label-lg text-2xl font-bold text-[#0F172A] mt-1">{{ $stats['sellers_count'] ?? 348 }}</div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100">
                <div class="flex items-center gap-1.5 text-emerald-700 font-label-sm text-[11px] font-semibold">
                    <span class="material-symbols-outlined text-[16px]">add_circle</span>
                    <span>+14 joined this week</span>
                </div>
                <p class="font-body-sm text-[10px] text-slate-500 mt-0.5 truncate">Kolkata, Digha, Contai</p>
            </div>
        </div>

        <!-- Card 4: Total Customers -->
        <div class="flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md transition-shadow">
            <div class="flex flex-col gap-1">
                <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold">REGISTERED BUYERS</span>
                <div class="font-label-lg text-2xl font-bold text-[#0F172A] mt-1">{{ number_format($stats['buyers_count'] ?? 28940) }}</div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100">
                <div class="flex items-center gap-1.5 text-[#0F172A] font-label-sm text-[11px] font-semibold">
                    <span class="material-symbols-outlined text-[16px] text-[#F5A623]">group</span>
                    <span>+2,120 active today</span>
                </div>
                <p class="font-body-sm text-[10px] text-slate-500 mt-0.5 truncate">84% repeat customer rate</p>
            </div>
        </div>

        <!-- Card 5: Pending Approvals -->
        <div class="flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md transition-shadow">
            <div class="flex flex-col gap-1">
                <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold">KYC & STORE AUDIT</span>
                <div class="flex items-center justify-between mt-1">
                    <span class="font-label-lg text-2xl font-bold text-[#0F172A]">{{ $stats['pending_kyc'] ?? 4 }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 font-label-sm text-[10px] font-bold">Action Needed</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100">
                <a href="{{ route('admin.sellers.approvals') }}" class="flex items-center justify-between text-amber-700 font-label-sm text-[11px] font-semibold hover:underline">
                    <span>Review Documents</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
                <p class="font-body-sm text-[10px] text-slate-500 mt-0.5 truncate">GSTIN & Trade licenses</p>
            </div>
        </div>

        <!-- Card 6: Escrow Holds -->
        <div class="flex flex-col justify-between p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md transition-shadow">
            <div class="flex flex-col gap-1">
                <span class="font-label-sm text-[10px] tracking-wider text-slate-500 uppercase font-semibold">ESCROW FUNDS IN TRANSIT</span>
                <div class="font-label-lg text-2xl font-bold text-[#0F172A] mt-1">₹{{ $stats['escrow_holds'] ?? '14,20,500' }}</div>
            </div>
            <div class="mt-3 pt-2.5 bg-slate-50 -mx-4 -mb-4 px-4 pb-3 rounded-b-xl border-t border-slate-100">
                <div class="flex items-center gap-1.5 text-blue-700 font-label-sm text-[11px] font-semibold">
                    <span class="material-symbols-outlined text-[16px]">lock</span>
                    <span>100% Escrow Protected</span>
                </div>
                <p class="font-body-sm text-[10px] text-slate-500 mt-0.5 truncate">Scheduled for batch release</p>
            </div>
        </div>
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
</div>
@endsection
