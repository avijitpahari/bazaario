@extends('layouts.admin')

@section('title', 'Platform Analytics & Intelligence')

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Top Header & Filter Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[11px] font-bold tracking-wider text-amber-600 uppercase">Operational Telemetry</span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                <span class="text-[11px] font-medium text-slate-500">Real-time Platform Metrics</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold font-['Outfit'] text-slate-900 tracking-tight">
                Platform Analytics & Intelligence
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                Monitor Gross Merchandise Value (GMV), platform commission take-rate, merchant sales velocity, and regional fulfillment hubs.
            </p>
        </div>

        <!-- Filter Presets & Actions -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Timeframe Pill Selector -->
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200/80 shadow-xs">
                @php
                    $ranges = [
                        '7d' => 'Last 7 Days',
                        '30d' => 'Last 30 Days',
                        '90d' => 'Quarter',
                        '1y' => '1 Year',
                        'all' => 'All-Time',
                    ];
                @endphp
                @foreach($ranges as $key => $label)
                    <a href="{{ route('admin.analytics.index', ['range' => $key]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $range === $key ? 'bg-white text-slate-900 shadow-xs border border-slate-200/60 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Export CSV Button -->
            <a href="{{ route('admin.analytics.export', ['range' => $range]) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                <span class="material-symbols-outlined text-[18px] text-amber-400">download</span>
                <span>Export Manifest CSV</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        
        <!-- 1. Platform GMV -->
        <div class="flex flex-col justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-amber-400/60 transition-all">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">PLATFORM GMV</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-600">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-['Outfit'] text-slate-900">
                    ₹{{ number_format($totalGmv, 2) }}
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    @if($gmvGrowth !== null)
                        <span class="inline-flex items-center text-[11px] font-bold {{ $gmvGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            <span class="material-symbols-outlined text-[14px]">{{ $gmvGrowth >= 0 ? 'trending_up' : 'trending_down' }}</span>
                            {{ $gmvGrowth >= 0 ? '+' : '' }}{{ $gmvGrowth }}%
                        </span>
                        <span class="text-[10px] text-slate-400">vs prev period</span>
                    @else
                        <span class="text-[11px] text-slate-400">Selected period total</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Platform Commission -->
        <div class="flex flex-col justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-400/60 transition-all">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">NET COMMISSION</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-600">
                    <span class="material-symbols-outlined text-[18px]">toll</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-['Outfit'] text-emerald-600">
                    ₹{{ number_format($platformCommission, 2) }}
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200/60">
                        {{ $takeRate }}% take-rate
                    </span>
                    @if($commissionGrowth !== null)
                        <span class="text-[10px] {{ $commissionGrowth >= 0 ? 'text-emerald-600 font-semibold' : 'text-rose-600 font-semibold' }}">
                            {{ $commissionGrowth >= 0 ? '+' : '' }}{{ $commissionGrowth }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Total Orders -->
        <div class="flex flex-col justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-blue-400/60 transition-all">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">TOTAL ORDERS</span>
                <span class="p-1.5 rounded-lg bg-blue-500/10 text-blue-600">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-['Outfit'] text-slate-900">
                    {{ number_format($totalOrdersCount) }}
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="text-[11px] font-semibold text-slate-600">
                        {{ $deliveredOrdersCount }} delivered
                    </span>
                    @if($ordersGrowth !== null)
                        <span class="text-[10px] {{ $ordersGrowth >= 0 ? 'text-emerald-600 font-bold' : 'text-rose-600 font-bold' }}">
                            ({{ $ordersGrowth >= 0 ? '+' : '' }}{{ $ordersGrowth }}%)
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. Average Order Value -->
        <div class="flex flex-col justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-purple-400/60 transition-all">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">AVG ORDER VALUE</span>
                <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-600">
                    <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-['Outfit'] text-slate-900">
                    ₹{{ number_format($aov, 2) }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Across fulfilled carts
                </div>
            </div>
        </div>

        <!-- 5. Fulfillment Rate -->
        <div class="flex flex-col justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-amber-400/60 transition-all">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">FULFILLMENT RATE</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-600">
                    <span class="material-symbols-outlined text-[18px]">task_alt</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-['Outfit'] text-slate-900">
                    {{ $fulfillmentRate }}%
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ min(100, $fulfillmentRate) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Escrow & Disputes -->
        <div class="flex flex-col justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-rose-400/60 transition-all">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">ESCROW & DISPUTES</span>
                <span class="p-1.5 rounded-lg bg-slate-100 text-slate-600">
                    <span class="material-symbols-outlined text-[18px]">gavel</span>
                </span>
            </div>
            <div>
                <div class="text-base font-bold text-slate-900">
                    ₹{{ number_format($totalEscrowHeld, 0) }} <span class="text-xs font-normal text-slate-500">in escrow</span>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $disputeCount > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700' }}">
                        {{ $disputeCount }} {{ Str::plural('Dispute', $disputeCount) }}
                    </span>
                    <span class="text-[10px] text-slate-500">₹{{ number_format($totalPayoutsReleased, 0) }} paid</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Interactive Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Timeline Chart (2 cols) -->
        <div class="lg:col-span-2 p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-base font-bold font-['Outfit'] text-slate-900">Sales & Platform Revenue Timeline</h2>
                    <p class="text-xs text-slate-500">Daily Gross Merchandise Value and platform commission retention.</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="text-slate-600">GMV (₹)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600">Commission (₹)</span>
                    </div>
                </div>
            </div>

            <div class="relative w-full h-[280px]">
                <canvas id="adminTimelineChart"></canvas>
            </div>
        </div>

        <!-- Order Channels Split Donut (1 col) -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold font-['Outfit'] text-slate-900">Transaction Channels</h2>
                <p class="text-xs text-slate-500 mb-4">Direct marketplace retail vs live auction settlements.</p>

                <div class="relative w-full h-[200px] flex items-center justify-center">
                    <canvas id="channelSplitChart"></canvas>
                </div>
            </div>

            <!-- Legend Stats -->
            <div class="grid grid-cols-2 gap-3 pt-3 mt-2 border-t border-slate-100">
                <div class="p-2.5 rounded-xl bg-amber-50/60 border border-amber-200/50">
                    <div class="text-[10px] font-bold uppercase text-amber-700 tracking-wider">DIRECT RETAIL</div>
                    <div class="text-base font-black text-slate-900 mt-0.5">₹{{ number_format($directGmv, 0) }}</div>
                    <div class="text-[11px] text-slate-500">{{ $directCount }} orders ({{ $directShare }}%)</div>
                </div>
                <div class="p-2.5 rounded-xl bg-indigo-50/60 border border-indigo-200/50">
                    <div class="text-[10px] font-bold uppercase text-indigo-700 tracking-wider">LIVE AUCTIONS</div>
                    <div class="text-base font-black text-slate-900 mt-0.5">₹{{ number_format($auctionGmv, 0) }}</div>
                    <div class="text-[11px] text-slate-500">{{ $auctionCount }} wins ({{ $auctionShare }}%)</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Category Contribution & Regional Fulfillment Corridors -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Category Revenue Breakdown -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold font-['Outfit'] text-slate-900">Top Categories by Revenue</h2>
                    <p class="text-xs text-slate-500">Gross revenue share and inventory units sold by taxonomy.</p>
                </div>
                <span class="material-symbols-outlined text-slate-400">category</span>
            </div>

            <div class="flex flex-col gap-3.5">
                @forelse($categoryBreakdown as $cat)
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-800">{{ $cat['name'] }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500 font-mono text-[11px]">{{ $cat['units'] }} units</span>
                                <span class="font-bold text-slate-900">₹{{ number_format($cat['revenue'], 2) }}</span>
                                <span class="text-amber-600 font-mono text-[11px]">({{ $cat['share'] }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2 rounded-full transition-all duration-500" style="width: {{ $cat['share'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        No category sales recorded for the selected timeframe.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Regional Dispatch Hubs & Delivery Corridors -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold font-['Outfit'] text-slate-900">Regional Fulfillment Corridors</h2>
                    <p class="text-xs text-slate-500">Geographical order density and dispatched package volumes.</p>
                </div>
                <span class="material-symbols-outlined text-slate-400">pin_drop</span>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($regionalHubs as $index => $hub)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/80 border border-slate-200/60 hover:bg-slate-100/60 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-amber-500/15 text-amber-700 font-bold text-xs flex items-center justify-center font-mono">
                                #{{ $index + 1 }}
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $hub['region'] }}</div>
                                <div class="text-[11px] text-slate-500">{{ $hub['orders_count'] }} orders dispatched</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-slate-900">₹{{ number_format($hub['total_volume'], 2) }}</div>
                            <span class="text-[10px] font-semibold text-emerald-600">Active Corridor</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        No regional delivery logs recorded in this timeframe.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Merchant Performance Leaderboard Table -->
    <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h2 class="text-base font-bold font-['Outfit'] text-slate-900">Top Merchant Leaderboard</h2>
                <p class="text-xs text-slate-500">Leading sellers ranked by gross sales, fulfillment performance, and commission contribution.</p>
            </div>
            <a href="{{ route('admin.sellers.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 inline-flex items-center gap-1">
                <span>View All Sellers</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3">Rank & Merchant</th>
                        <th class="py-3 px-3">Trust Rating</th>
                        <th class="py-3 px-3 text-center">Total Orders</th>
                        <th class="py-3 px-3 text-center">Delivered</th>
                        <th class="py-3 px-3 text-right">Gross Sales</th>
                        <th class="py-3 px-3 text-right">Commission Earned</th>
                        <th class="py-3 px-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($merchantLeaderboard as $index => $seller)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full {{ $index === 0 ? 'bg-amber-400 text-slate-950 font-black' : ($index === 1 ? 'bg-slate-300 text-slate-800 font-bold' : ($index === 2 ? 'bg-amber-700 text-white font-bold' : 'bg-slate-100 text-slate-600')) }} text-[10px] flex items-center justify-center font-mono">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900">{{ $seller['store_name'] }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $seller['seller_name'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-bold text-[11px] border border-amber-200/60">
                                    <span class="material-symbols-outlined text-[14px] text-amber-500 fill-1">star</span>
                                    {{ number_format($seller['rating'], 1) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-mono text-slate-700 font-semibold">
                                {{ $seller['total_orders'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono text-emerald-600 font-bold">
                                {{ $seller['delivered_orders'] }}
                            </td>
                            <td class="py-3 px-3 text-right font-black font-['Outfit'] text-slate-900 text-sm">
                                ₹{{ number_format($seller['gross_sales'], 2) }}
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-emerald-600">
                                ₹{{ number_format($seller['commission_paid'], 2) }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('admin.sellers.show', $seller['seller_id']) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition-colors">
                                    <span>Details</span>
                                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                                No merchant performance activity recorded for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartData = @json($chartData);
    const directGmv = {{ $directGmv }};
    const auctionGmv = {{ $auctionGmv }};

    // 1. Timeline Chart (GMV + Commission)
    const timelineCtx = document.getElementById('adminTimelineChart');
    if (timelineCtx) {
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Gross Merchandise Value (₹)',
                        data: chartData.gmv,
                        borderColor: '#F59E0B',
                        backgroundColor: 'rgba(245, 158, 11, 0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: chartData.labels.length > 20 ? 0 : 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#F59E0B',
                        yAxisID: 'y',
                    },
                    {
                        label: 'Platform Commission (₹)',
                        data: chartData.commission,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: chartData.labels.length > 20 ? 0 : 2,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#10B981',
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '₹' + Number(context.parsed.y).toLocaleString('en-IN', { minimumFractionDigits: 2 });
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: { size: 10 },
                            color: '#64748B',
                            maxTicksLimit: 12,
                        }
                    },
                    y: {
                        grid: {
                            color: '#F1F5F9',
                        },
                        ticks: {
                            font: { size: 10 },
                            color: '#64748B',
                            callback: function(value) {
                                if (value >= 100000) return '₹' + (value / 100000).toFixed(1) + 'L';
                                if (value >= 1000) return '₹' + (value / 1000).toFixed(0) + 'k';
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Channel Split Donut Chart
    const splitCtx = document.getElementById('channelSplitChart');
    if (splitCtx) {
        new Chart(splitCtx, {
            type: 'doughnut',
            data: {
                labels: ['Direct Retail', 'Live Auctions'],
                datasets: [{
                    data: [directGmv > 0 || auctionGmv > 0 ? directGmv : 1, directGmv > 0 || auctionGmv > 0 ? auctionGmv : 0],
                    backgroundColor: ['#F59E0B', '#6366F1'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = Number(context.raw).toLocaleString('en-IN', { minimumFractionDigits: 2 });
                                return context.label + ': ₹' + val;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
