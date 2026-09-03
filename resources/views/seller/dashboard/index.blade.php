@extends('layouts.seller')

@section('title', 'Seller Dashboard — Artisan & Timber | Bazaario')
@section('page-title', 'Stall Overview & Performance')

@section('content')
<div class="space-y-8" x-data="{
    alertDismissed: false
}">

    <!-- 1. Low Stock Alert Banner (Section 3.2) -->
    <div x-show="!alertDismissed" x-cloak class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-amber-500 via-amber-600 to-amber-500 text-slate-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg shadow-amber-500/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/30 backdrop-blur text-slate-950 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm font-heading">Inventory Alert: 3 products are below threshold</h3>
                <p class="text-xs text-slate-900/80">Rustic Walnut Desk (2 left), Hand-turned Bowl (1 left), Brass Key Hook (3 left)</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('seller.products.index') }}" class="px-4 py-2 rounded-xl bg-slate-950 hover:bg-slate-900 text-white text-xs font-bold transition shadow">
                Restock Inventory Now
            </a>
            <button @click="alertDismissed = true" class="p-2 text-slate-900 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- 2. Stat Summary Cards (5 Metrics) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Metric 1: Orders This Week -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Orders This Week</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold font-heading text-slate-900">38 orders</div>
            <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> +14.2% vs last week
            </span>
        </div>

        <!-- Metric 2: Revenue -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Net Sales Volume</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold font-heading text-slate-900">$18,420</div>
            <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> +8.5% growth
            </span>
        </div>

        <!-- Metric 3: Active Products Listed -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Active Products</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold font-heading text-slate-900">48 items</div>
            <span class="text-[11px] text-slate-400">across 4 categories</span>
        </div>

        <!-- Metric 4: Low Stock Count -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Low Stock Alert</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold font-heading text-amber-600">3 items</div>
            <span class="text-[11px] text-amber-700 font-semibold">Needs attention</span>
        </div>

        <!-- Metric 5: Trust Score -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">Stall Trust Score</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold font-heading text-purple-700">98.4%</div>
            <span class="text-[11px] font-bold text-emerald-600">Top 5% on Bazaario</span>
        </div>

    </div>

    <!-- 3. Performance Chart & Quick Actions Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sales Trajectory Card (Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base font-heading text-slate-900">Weekly Revenue & Order Flow</h3>
                    <p class="text-xs text-slate-400">Direct shipments dispatched through Bazaario network</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold px-2 py-1 rounded-lg bg-indigo-50 text-indigo-700">This Month</span>
                </div>
            </div>

            <!-- Simulated Visual Bar Graph -->
            <div class="h-48 flex items-end justify-between gap-3 pt-6 px-2">
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-indigo-100 hover:bg-indigo-500 rounded-t-xl transition-all duration-300 h-28"></div>
                    <span class="text-[10px] text-slate-400 font-semibold">Mon</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-indigo-100 hover:bg-indigo-500 rounded-t-xl transition-all duration-300 h-36"></div>
                    <span class="text-[10px] text-slate-400 font-semibold">Tue</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-indigo-100 hover:bg-indigo-500 rounded-t-xl transition-all duration-300 h-24"></div>
                    <span class="text-[10px] text-slate-400 font-semibold">Wed</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-indigo-600 rounded-t-xl h-44 shadow-lg shadow-indigo-600/30"></div>
                    <span class="text-[10px] text-indigo-600 font-bold">Thu (Peak)</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-indigo-100 hover:bg-indigo-500 rounded-t-xl transition-all duration-300 h-32"></div>
                    <span class="text-[10px] text-slate-400 font-semibold">Fri</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-indigo-100 hover:bg-indigo-500 rounded-t-xl transition-all duration-300 h-40"></div>
                    <span class="text-[10px] text-slate-400 font-semibold">Sat</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-indigo-100 hover:bg-indigo-500 rounded-t-xl transition-all duration-300 h-30"></div>
                    <span class="text-[10px] text-slate-400 font-semibold">Sun</span>
                </div>
            </div>
        </div>

        <!-- Quick Stall Actions -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <h3 class="font-bold text-base font-heading text-slate-900">Stall Quick Actions</h3>
            <div class="space-y-2.5">
                <a href="{{ route('seller.products.create') }}" class="flex items-center justify-between p-3 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <i class="fa-solid fa-plus-circle text-base"></i> Add New Product
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
                <a href="{{ route('seller.orders.index') }}" class="flex items-center justify-between p-3 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-800 transition">
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <i class="fa-solid fa-boxes-packing text-base"></i> Print Packing Slips
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
                <a href="{{ route('seller.payouts.index') }}" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 transition">
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <i class="fa-solid fa-file-invoice-dollar text-base"></i> Request Payout
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- 4. Recent Orders Table (Own Orders Only) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden space-y-4 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-base font-heading text-slate-900">Recent Customer Orders</h3>
                <p class="text-xs text-slate-400">Order items mapped directly to your stall</p>
            </div>
            <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                View All Orders &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-y border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Order ID</th>
                        <th class="py-3 px-4">Item Details</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Stall Payout</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    
                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-900">#BZR-98241</td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-800 block">Full-Grain Messenger Bag (1x)</span>
                            <span class="text-[10px] text-slate-400">+ Solid Brass Key Hook (2x)</span>
                        </td>
                        <td class="py-3.5 px-4">Sarah Miller</td>
                        <td class="py-3.5 px-4 font-extrabold text-slate-900">$180.25 <span class="text-[10px] text-slate-400 font-normal">(-8.5% fee)</span></td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                Packing in Workshop
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('seller.orders.index') }}" class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold">
                                Fulfill &rarr;
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td class="py-3.5 px-4 font-bold text-slate-900">#BZR-98210</td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-800 block">Solid Walnut Desk Tray (1x)</span>
                        </td>
                        <td class="py-3.5 px-4">Liam Johnson</td>
                        <td class="py-3.5 px-4 font-extrabold text-slate-900">$58.56 <span class="text-[10px] text-slate-400 font-normal">(-8.5% fee)</span></td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">
                                Shipped (FedEx #441)
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <button class="px-3 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold">
                                Tracking
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
