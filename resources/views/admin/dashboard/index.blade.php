@extends('layouts.admin')

@section('title', 'Executive Dashboard — Bazaario HQ Platform Supervision')
@section('page-title', 'Platform Executive Overview')

@section('content')
<div class="space-y-8" x-data="{
    timeframe: '30d',
    inspectModal: false,
    selectedOrder: null,
    quickApproveToast: false,
    toastText: '',
    approveStall(name) {
        this.toastText = 'Stall ' + name + ' has been approved & unlocked!';
        this.quickApproveToast = true;
        setTimeout(() => this.quickApproveToast = false, 3500);
    }
}">

    <!-- Quick Approve Toast -->
    <div x-show="quickApproveToast" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-slate-950 px-5 py-3.5 rounded-2xl shadow-2xl font-bold text-xs flex items-center gap-2.5">
        <i class="fa-solid fa-circle-check text-base"></i>
        <span x-text="toastText"></span>
    </div>

    <!-- 1. Top Executive KPI Bar (6 High-Impact Metrics) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        
        <!-- Metric 1: Total GMV -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 space-y-2 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="font-semibold">Platform GMV</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="text-2xl font-black font-heading text-white">$142,850</div>
            <div class="text-[11px] font-bold text-emerald-400 flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> +24.8% MoM
            </div>
        </div>

        <!-- Metric 2: Net Commission Revenue -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 space-y-2 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="font-semibold">Platform Fee (8.5%)</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
            <div class="text-2xl font-black font-heading text-emerald-400">$12,142</div>
            <div class="text-[11px] text-slate-400 font-mono">
                Net Take-Rate 8.5%
            </div>
        </div>

        <!-- Metric 3: Escrow Holding -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 space-y-2 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="font-semibold">Escrow In-Holding</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
            <div class="text-2xl font-black font-heading text-purple-300">$24,910</div>
            <div class="text-[11px] text-slate-400">
                Protected buyer funds
            </div>
        </div>

        <!-- Metric 4: Active Verified Stalls -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 space-y-2 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="font-semibold">Active Stalls</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-store"></i>
                </div>
            </div>
            <div class="text-2xl font-black font-heading text-white">124 stalls</div>
            <div class="text-[11px] text-emerald-400 font-bold">
                99.2% avg trust score
            </div>
        </div>

        <!-- Metric 5: Pending Approvals -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 space-y-2 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="font-semibold">Pending Queue</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
            </div>
            <div class="text-2xl font-black font-heading text-amber-400">7 sellers</div>
            <a href="{{ route('admin.sellers.pending') }}" class="text-[11px] text-amber-400 hover:underline font-bold flex items-center gap-1">
                Review queue &rarr;
            </a>
        </div>

        <!-- Metric 6: Open Disputes -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 space-y-2 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="font-semibold">Open Disputes</span>
                <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-2xl font-black font-heading text-rose-400">2 cases</div>
            <a href="{{ route('admin.orders.index') }}" class="text-[11px] text-rose-400 hover:underline font-bold flex items-center gap-1">
                Mediate now &rarr;
            </a>
        </div>

    </div>

    <!-- 2. GMV Analytics Visual + Pending Approval Queue Card (Section 4.1) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Two-Thirds Interactive GMV & Commission Chart (Span 8) -->
        <div class="lg:col-span-8 bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold font-heading text-white">Gross Merchandise Value & Revenue Dynamics</h3>
                    <p class="text-xs text-slate-400">Tracking aggregate sales across all 124 verified independent merchant storefronts</p>
                </div>
                <!-- Timeframe Selector -->
                <div class="flex items-center gap-1 p-1 bg-slate-950 rounded-2xl border border-slate-800 text-xs font-bold">
                    <button @click="timeframe = '7d'" :class="timeframe === '7d' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl transition">7 Days</button>
                    <button @click="timeframe = '30d'" :class="timeframe === '30d' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl transition">30 Days</button>
                    <button @click="timeframe = '90d'" :class="timeframe === '90d' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl transition">90 Days</button>
                    <button @click="timeframe = '1y'" :class="timeframe === '1y' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl transition">YTD</button>
                </div>
            </div>

            <!-- Volume Growth Visualization -->
            <div class="h-64 flex items-end justify-between gap-3 pt-6 px-2 border-b border-slate-800/80 pb-3">
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-slate-800 group-hover:bg-indigo-600 rounded-t-xl transition-all duration-300 h-28 relative">
                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-950 text-white text-[10px] font-mono px-2 py-0.5 rounded border border-slate-800 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">$18.2k</span>
                    </div>
                    <span class="text-[10px] text-slate-500 font-mono">Week 1</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-slate-800 group-hover:bg-indigo-600 rounded-t-xl transition-all duration-300 h-36 relative">
                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-950 text-white text-[10px] font-mono px-2 py-0.5 rounded border border-slate-800 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">$26.4k</span>
                    </div>
                    <span class="text-[10px] text-slate-500 font-mono">Week 2</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-slate-800 group-hover:bg-indigo-600 rounded-t-xl transition-all duration-300 h-44 relative">
                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-950 text-white text-[10px] font-mono px-2 py-0.5 rounded border border-slate-800 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">$34.1k</span>
                    </div>
                    <span class="text-[10px] text-slate-500 font-mono">Week 3</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-gradient-to-t from-indigo-600 via-indigo-500 to-amber-400 rounded-t-xl h-56 shadow-lg shadow-indigo-600/30 relative">
                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-950 text-amber-400 text-[10px] font-bold font-mono px-2 py-0.5 rounded border border-slate-800 whitespace-nowrap">$64.1k (Peak)</span>
                    </div>
                    <span class="text-[10px] text-indigo-400 font-bold font-mono">Week 4</span>
                </div>
            </div>

            <!-- Bottom Sub-Metrics Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs pt-1">
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80">
                    <span class="text-slate-400 block text-[11px]">Avg Order Value (AOV)</span>
                    <span class="text-lg font-bold font-heading text-white mt-0.5 block">$94.20</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80">
                    <span class="text-slate-400 block text-[11px]">Repeat Buyer Rate</span>
                    <span class="text-lg font-bold font-heading text-emerald-400 mt-0.5 block">41.8%</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80">
                    <span class="text-slate-400 block text-[11px]">COD Fulfillment Rate</span>
                    <span class="text-lg font-bold font-heading text-amber-400 mt-0.5 block">99.4%</span>
                </div>
            </div>
        </div>

        <!-- One-Third Pending Approvals Queue Card (Span 4) -->
        <div class="lg:col-span-4 bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user-clock text-amber-400"></i>
                        <h3 class="text-base font-bold font-heading text-white">Pending Stall Queue</h3>
                    </div>
                    <span class="text-[10px] font-bold font-mono px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">7 waiting</span>
                </div>

                <div class="space-y-3">
                    
                    <!-- Applicant 1 -->
                    <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-white">Heritage Wood & Iron</span>
                            <span class="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded">Low Risk</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Marcus Vance • Artisan & Furniture category</p>
                        <div class="flex items-center gap-2 pt-1">
                            <button @click="approveStall('Heritage Wood & Iron')" class="flex-1 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold transition shadow">
                                <i class="fa-solid fa-check"></i> Approve
                            </button>
                            <a href="{{ route('admin.sellers.pending') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold transition">
                                Inspect
                            </a>
                        </div>
                    </div>

                    <!-- Applicant 2 -->
                    <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-white">Aura Botanical Perfumes</span>
                            <span class="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded">Low Risk</span>
                        </div>
                        <p class="text-[11px] text-slate-300">Claire Delacroix • Gourmet & Organics</p>
                        <div class="flex items-center gap-2 pt-1">
                            <button @click="approveStall('Aura Botanical Perfumes')" class="flex-1 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold transition shadow">
                                <i class="fa-solid fa-check"></i> Approve
                            </button>
                            <a href="{{ route('admin.sellers.pending') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold transition">
                                Inspect
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <a href="{{ route('admin.sellers.pending') }}" class="w-full py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold text-center block transition border border-slate-700">
                View Full Queue (7 Pending Stalls) &rarr;
            </a>
        </div>

    </div>

    <!-- 3. Top Earning Stalls Leaderboard & Category Breakdown Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Top Performing Stalls Leaderboard (Span 7) -->
        <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold font-heading text-white flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-400"></i> Top Earning Stalls Leaderboard
                    </h3>
                    <p class="text-xs text-slate-400">Ranked by 30-day verified gross transaction volume</p>
                </div>
                <a href="{{ route('admin.sellers.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">
                    All 124 Stalls &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-800/80 text-xs font-medium">
                
                <div class="py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-xs font-mono">1</span>
                        <div>
                            <a href="{{ route('stall.show', 'nordic-leather') }}" target="_blank" class="font-bold text-white hover:text-indigo-400 transition">Nordic Leather Atelier</a>
                            <span class="text-[10px] text-slate-400 block">Stockholm • 1,420 orders fulfilled</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold font-heading text-sm text-white">$42,890.00</span>
                        <span class="text-[10px] text-emerald-400 font-bold block">99.2% Trust</span>
                    </div>
                </div>

                <div class="py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-lg bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-xs font-mono">2</span>
                        <div>
                            <a href="{{ route('stall.show', 'timber-grain') }}" target="_blank" class="font-bold text-white hover:text-indigo-400 transition">Timber & Grain Workshop</a>
                            <span class="text-[10px] text-slate-400 block">Oregon, USA • 2,190 orders fulfilled</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold font-heading text-sm text-white">$38,120.00</span>
                        <span class="text-[10px] text-emerald-400 font-bold block">99.4% Trust</span>
                    </div>
                </div>

                <div class="py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-lg bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-xs font-mono">3</span>
                        <div>
                            <a href="{{ route('stall.show', 'audiocraft') }}" target="_blank" class="font-bold text-white hover:text-indigo-400 transition">AudioCraft Studios</a>
                            <span class="text-[10px] text-slate-400 block">Berlin, Germany • 840 orders fulfilled</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold font-heading text-sm text-white">$28,450.00</span>
                        <span class="text-[10px] text-emerald-400 font-bold block">98.4% Trust</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category Volume Breakdown (Span 5) -->
        <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 space-y-4">
            <h3 class="text-base font-bold font-heading text-white">Category GMV Share</h3>
            
            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex justify-between text-slate-300 mb-1">
                        <span>Artisan & Crafts (42%)</span>
                        <span class="font-bold text-white">$60,000</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full w-[42%]"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-slate-300 mb-1">
                        <span>Electronics & Audio (28%)</span>
                        <span class="font-bold text-white">$40,000</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full w-[28%]"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-slate-300 mb-1">
                        <span>Home & Living (18%)</span>
                        <span class="font-bold text-white">$25,713</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full w-[18%]"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-slate-300 mb-1">
                        <span>Organic & Gourmet (12%)</span>
                        <span class="font-bold text-white">$17,137</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-rose-500 rounded-full w-[12%]"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. Platform-Wide Recent Orders Table (Section 4.1) -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold font-heading text-white">Live Multi-Vendor Order Stream</h3>
                <p class="text-xs text-slate-400">Order item splitting, dispatch status, and automated commission fee auditing</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">
                View All Platform Orders &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Order ID & Date</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Multi-Vendor Packages</th>
                        <th class="py-3.5 px-4">Gross Total</th>
                        <th class="py-3.5 px-4">Commission (8.5%)</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 font-medium">
                    
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-white font-mono block">#BZR-98241</span>
                            <span class="text-[10px] text-slate-500">Today, 10:24 AM</span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-200">Sarah Miller</td>
                        <td class="py-3.5 px-4">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-[10px] font-bold border border-amber-500/30">Nordic Leather</span>
                                <span class="px-2 py-0.5 rounded-md bg-indigo-500/20 text-indigo-300 text-[10px] font-bold border border-indigo-500/30">AudioCraft</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-white font-heading text-sm">$374.40</td>
                        <td class="py-3.5 px-4 font-mono text-emerald-400 font-bold">+$31.82</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                In Fulfillment
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('admin.orders.index') }}" class="px-3 py-1 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[11px] transition shadow">
                                Inspect Order
                            </a>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-white font-mono block">#BZR-98210</span>
                            <span class="text-[10px] text-slate-500">Yesterday, 03:15 PM</span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-200">Liam Johnson</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-[10px] font-bold border border-amber-500/30">Timber & Grain</span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-white font-heading text-sm">$64.00</td>
                        <td class="py-3.5 px-4 font-mono text-emerald-400 font-bold">+$5.44</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                Dispatched
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('admin.orders.index') }}" class="px-3 py-1 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[11px] transition">
                                Inspect Order
                            </a>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
