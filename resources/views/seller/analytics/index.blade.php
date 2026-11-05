@extends('layouts.seller')

@section('title', 'Store Analytics & Intelligence — Bazaario Seller Center')

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Top Header & Filter Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-3 border-b border-[#E2DFD7]/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[11px] font-bold tracking-wider text-amber-700 uppercase font-mono">Store Intelligence</span>
                <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                <span class="text-[11px] font-medium text-on-surface-variant">Real-time Performance & Unit Economics</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-on-surface tracking-tight">
                Store Analytics & Financial Ledger
            </h1>
            <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">
                Analyze your merchant revenue, net take-home earnings, fulfillment rates, and top moving products.
            </p>
        </div>

        <!-- Filter Presets & Actions -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Timeframe Pill Selector -->
            <div class="inline-flex items-center p-1 bg-surface-container rounded-xl border border-outline-variant/40 shadow-xs">
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
                    <a href="{{ route('seller.analytics.index', ['range' => $key]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $range === $key ? 'bg-surface-container-lowest text-on-surface shadow-xs font-bold' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Export CSV Button -->
            <a href="{{ route('seller.analytics.export', ['range' => $range]) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-on-surface hover:bg-slate-800 text-surface rounded-xl text-xs font-semibold shadow-xs transition-colors">
                <span class="material-symbols-outlined text-[18px] text-amber-400">download</span>
                <span>Export Report CSV</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

        <!-- 1. Gross Revenue -->
        <div class="flex flex-col justify-between p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs hover:border-amber-400/70 transition-all">
            <div class="flex items-center justify-between text-on-surface-variant mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider font-mono">GROSS SALES</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-600">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-heading text-on-surface">
                    ₹{{ number_format($storeGrossSales, 2) }}
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    @if($salesGrowth !== null)
                        <span class="inline-flex items-center text-[11px] font-bold {{ $salesGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            <span class="material-symbols-outlined text-[14px]">{{ $salesGrowth >= 0 ? 'trending_up' : 'trending_down' }}</span>
                            {{ $salesGrowth >= 0 ? '+' : '' }}{{ $salesGrowth }}%
                        </span>
                        <span class="text-[10px] text-on-surface-variant">vs prev period</span>
                    @else
                        <span class="text-[11px] text-on-surface-variant">Selected timeframe</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Net Take-Home Earnings -->
        <div class="flex flex-col justify-between p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs hover:border-emerald-400/70 transition-all">
            <div class="flex items-center justify-between text-on-surface-variant mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider font-mono">NET EARNINGS</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-600">
                    <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-heading text-emerald-600">
                    ₹{{ number_format($netEarnings, 2) }}
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    @if($earningsGrowth !== null)
                        <span class="text-[11px] font-bold {{ $earningsGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $earningsGrowth >= 0 ? '+' : '' }}{{ $earningsGrowth }}%
                        </span>
                        <span class="text-[10px] text-on-surface-variant">payout cleared</span>
                    @else
                        <span class="text-[11px] text-on-surface-variant">After platform fee</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Commission Deducted -->
        <div class="flex flex-col justify-between p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs hover:border-amber-400/70 transition-all">
            <div class="flex items-center justify-between text-on-surface-variant mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider font-mono">MARKETPLACE FEE</span>
                <span class="p-1.5 rounded-lg bg-surface-container-high text-on-surface-variant">
                    <span class="material-symbols-outlined text-[18px]">toll</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-heading text-on-surface">
                    ₹{{ number_format($commissionIncurred, 2) }}
                </div>
                <div class="text-[11px] text-on-surface-variant mt-1">
                    Platform commission paid
                </div>
            </div>
        </div>

        <!-- 4. Fulfillment Rate -->
        <div class="flex flex-col justify-between p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs hover:border-blue-400/70 transition-all">
            <div class="flex items-center justify-between text-on-surface-variant mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider font-mono">FULFILLMENT</span>
                <span class="p-1.5 rounded-lg bg-blue-500/10 text-blue-600">
                    <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-heading text-on-surface">
                    {{ $fulfillmentRate }}%
                </div>
                <div class="text-[11px] text-slate-600 font-medium mt-1">
                    {{ $deliveredCount }} of {{ $totalOrdersCount }} delivered
                </div>
            </div>
        </div>

        <!-- 5. Average Order Value -->
        <div class="flex flex-col justify-between p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs hover:border-purple-400/70 transition-all">
            <div class="flex items-center justify-between text-on-surface-variant mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider font-mono">AVG ORDER VALUE</span>
                <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-600">
                    <span class="material-symbols-outlined text-[18px]">shopping_basket</span>
                </span>
            </div>
            <div>
                <div class="text-xl xl:text-2xl font-black font-heading text-on-surface">
                    ₹{{ number_format($aov, 2) }}
                </div>
                <div class="text-[11px] text-on-surface-variant mt-1">
                    Per completed cart
                </div>
            </div>
        </div>

        <!-- 6. Unit Economics & Volume -->
        <div class="flex flex-col justify-between p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs hover:border-amber-400/70 transition-all">
            <div class="flex items-center justify-between text-on-surface-variant mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider font-mono">UNIT ECONOMICS</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-600">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                </span>
            </div>
            <div>
                <div class="text-base font-bold text-on-surface font-heading">
                    ₹{{ number_format($asp, 2) }} <span class="text-xs font-normal text-on-surface-variant">avg/unit</span>
                </div>
                <div class="text-[11px] text-on-surface-variant mt-1 font-mono">
                    {{ number_format($totalUnitsSold) }} total items sold
                </div>
            </div>
        </div>

    </div>

    <!-- Interactive Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sales & Payout Velocity Line Chart (2 cols) -->
        <div class="lg:col-span-2 p-5 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-base font-bold font-heading text-on-surface">Store Sales & Net Payout Velocity</h2>
                    <p class="text-xs text-on-surface-variant">Daily gross product revenue alongside your net take-home earnings.</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="text-on-surface-variant">Gross Sales (₹)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-on-surface-variant">Net Earnings (₹)</span>
                    </div>
                </div>
            </div>

            <div class="relative w-full h-[280px]">
                <canvas id="sellerTimelineChart"></canvas>
            </div>
        </div>

        <!-- Fulfillment Status Pipeline Donut (1 col) -->
        <div class="p-5 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold font-heading text-on-surface">Order Fulfillment Pipeline</h2>
                <p class="text-xs text-on-surface-variant mb-4">Status distribution across active and completed store orders.</p>

                <div class="relative w-full h-[200px] flex items-center justify-center">
                    <canvas id="sellerPipelineChart"></canvas>
                </div>
            </div>

            <!-- Pipeline Status Legend -->
            <div class="grid grid-cols-2 gap-2.5 pt-3 mt-2 border-t border-outline-variant/30 text-xs">
                <div class="flex items-center justify-between p-2 rounded-xl bg-emerald-50/60 border border-emerald-200/50">
                    <span class="text-emerald-700 font-semibold">Delivered</span>
                    <span class="font-mono font-bold text-slate-900">{{ $pipeline['delivered'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-xl bg-blue-50/60 border border-blue-200/50">
                    <span class="text-blue-700 font-semibold">Shipped</span>
                    <span class="font-mono font-bold text-slate-900">{{ $pipeline['shipped'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-xl bg-amber-50/60 border border-amber-200/50">
                    <span class="text-amber-700 font-semibold">Processing</span>
                    <span class="font-mono font-bold text-slate-900">{{ $pipeline['processing'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-xl bg-rose-50/60 border border-rose-200/50">
                    <span class="text-rose-700 font-semibold">Cancelled</span>
                    <span class="font-mono font-bold text-slate-900">{{ $pipeline['cancelled'] }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Escrow Ledger & Payout Clearance Summary Card -->
    <div class="p-5 bg-gradient-to-br from-amber-50/50 via-surface-container-lowest to-surface-container-lowest rounded-2xl border border-amber-200/70 shadow-xs flex flex-col lg:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-300 text-amber-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[28px]">verified</span>
            </div>
            <div>
                <h3 class="text-base font-bold font-heading text-slate-900">Escrow Clearance & Payout Schedule</h3>
                <p class="text-xs text-slate-600 mt-0.5">
                    Funds for delivered orders clear automatically every Friday directly to your registered bank account.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            <div class="text-right">
                <div class="text-[10px] font-bold uppercase text-slate-500 font-mono tracking-wider">PENDING CLEARANCE</div>
                <div class="text-lg font-black font-heading text-amber-700">₹{{ number_format($readyEscrowPayout, 2) }}</div>
            </div>
            <div class="h-8 w-px bg-slate-200"></div>
            <div class="text-right">
                <div class="text-[10px] font-bold uppercase text-slate-500 font-mono tracking-wider">LIFETIME CLEARED</div>
                <div class="text-lg font-black font-heading text-emerald-700">₹{{ number_format($clearedPayouts, 2) }}</div>
            </div>
            <a href="{{ route('seller.payouts.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition-colors shadow-xs">
                View Payouts
            </a>
        </div>
    </div>

    <!-- Top Performing Products -->
    <div class="p-5 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h2 class="text-base font-bold font-heading text-on-surface">Top Performing Products (Velocity Movers)</h2>
                <p class="text-xs text-on-surface-variant">Your highest revenue and fastest selling inventory items in this period.</p>
            </div>
            <a href="{{ route('seller.products.index') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800 inline-flex items-center gap-1">
                <span>Manage Inventory</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-outline-variant/40 text-on-surface-variant font-semibold uppercase tracking-wider text-[10px] font-mono">
                        <th class="py-3 px-3">Product Item</th>
                        <th class="py-3 px-3 text-center">Units Sold</th>
                        <th class="py-3 px-3 text-right">Revenue Generated</th>
                        <th class="py-3 px-3 text-center">Current Stock</th>
                        <th class="py-3 px-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($topProducts as $item)
                        <tr class="hover:bg-surface-container/40 transition-colors">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item['image'] }}" 
                                         alt="{{ $item['name'] }}" 
                                         class="w-10 h-10 rounded-lg object-cover border border-outline-variant/40 shrink-0">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-on-surface">{{ $item['name'] }}</span>
                                        <span class="text-[10px] text-on-surface-variant font-mono">Product ID: #{{ $item['id'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-on-surface text-sm">
                                {{ $item['units'] }}
                            </td>
                            <td class="py-3 px-3 text-right font-black font-heading text-emerald-600 text-sm">
                                ₹{{ number_format($item['revenue'], 2) }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($item['is_low_stock'])
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        {{ $item['current_stock'] }} left (Low Stock)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-semibold border border-emerald-200">
                                        {{ $item['current_stock'] }} in stock
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('seller.products.edit', $item['id']) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold text-[11px] transition-colors">
                                    <span>Edit</span>
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-on-surface-variant">
                                No product sales recorded in this timeframe. Once orders arrive, your top movers will display here.
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
    const pipeline = @json($pipeline);

    // 1. Seller Timeline Chart
    const timelineCtx = document.getElementById('sellerTimelineChart');
    if (timelineCtx) {
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Gross Sales (₹)',
                        data: chartData.sales,
                        borderColor: '#F59E0B',
                        backgroundColor: 'rgba(245, 158, 11, 0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: chartData.labels.length > 20 ? 0 : 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#F59E0B',
                    },
                    {
                        label: 'Net Take-Home (₹)',
                        data: chartData.earnings,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: chartData.labels.length > 20 ? 0 : 2,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#10B981',
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
                    legend: { display: false },
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
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            color: '#64748B',
                            maxTicksLimit: 12,
                        }
                    },
                    y: {
                        grid: { color: '#F1F5F9' },
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

    // 2. Fulfillment Pipeline Donut Chart
    const pipelineCtx = document.getElementById('sellerPipelineChart');
    if (pipelineCtx) {
        const totalPipeline = (pipeline.delivered || 0) + (pipeline.shipped || 0) + (pipeline.processing || 0) + (pipeline.cancelled || 0);
        new Chart(pipelineCtx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Shipped', 'Processing', 'Cancelled'],
                datasets: [{
                    data: totalPipeline > 0 ? [pipeline.delivered, pipeline.shipped, pipeline.processing, pipeline.cancelled] : [1, 0, 0, 0],
                    backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + ' orders';
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
