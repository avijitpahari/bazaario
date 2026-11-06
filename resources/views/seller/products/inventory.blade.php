@extends('layouts.seller')

@section('title', 'Inventory & Stock Management — Bazaario Seller Center')

@section('content')
@php
    $sellerUser = Auth::guard('seller')->user() ?? Auth::user();
    $sellerId = $sellerUser?->id ?? 0;

    // Defensive fallback for products collection
    if (!isset($products)) {
        $products = \App\Models\Product::where('seller_id', $sellerId)
            ->with(['category', 'primaryImage', 'images'])
            ->latest()
            ->paginate(15);
    }

    // Defensive fallbacks for KPI metrics
    $totalSkus = $totalSkus ?? \App\Models\Product::where('seller_id', $sellerId)->count();
    $healthyCount = $healthyCount ?? \App\Models\Product::where('seller_id', $sellerId)->where('stock', '>', 10)->count();
    $lowStockCount = $lowStockCount ?? \App\Models\Product::where('seller_id', $sellerId)->lowStock()->count();
    $outOfStockCount = $outOfStockCount ?? \App\Models\Product::where('seller_id', $sellerId)->where('stock', '<=', 0)->count();
    $healthyPct = $totalSkus > 0 ? round(($healthyCount / $totalSkus) * 100) : 100;

    $depletedItem = \App\Models\Product::where('seller_id', $sellerId)->where('stock', '<=', 0)->first();
@endphp

<div class="flex flex-col w-full pb-16">
    <!-- Top Banner / Alert for Depleted Inventory (Shown if any SKU is 0 stock) -->
    @if($depletedItem)
    <div class="w-full bg-rose-50/90 text-rose-950 rounded-2xl p-4 shadow-2xs mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-rose-200">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                <span class="material-symbols-outlined text-[22px]">priority_high</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-rose-700 font-bold">Critical Stock Notice</span>
                    <span class="text-rose-300 text-[12px]">•</span>
                    <span class="font-mono text-xs text-rose-900 font-bold">{{ $depletedItem->sku ?? ('SKU-' . $depletedItem->id) }}</span>
                </div>
                <p class="font-sans text-xs text-rose-800 font-medium mt-0.5">
                    <span class="font-bold text-rose-950">{{ $depletedItem->name }}</span> is currently completely depleted (0 {{ $depletedItem->unit_type }}). Buyers are encountering out-of-stock bounce rates.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 self-end md:self-auto shrink-0">
            <button type="button" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white transition-colors font-heading text-xs font-bold flex items-center gap-1.5 shadow-xs" onclick="openAdjustmentModal('{{ addslashes($depletedItem->name) }}', '{{ $depletedItem->sku ?? ('SKU-' . $depletedItem->id) }}', '0 {{ $depletedItem->unit_type }}', '{{ $depletedItem->low_stock_threshold ?? 10 }} {{ $depletedItem->unit_type }}', '{{ $depletedItem->unit_type }}', '{{ route('seller.products.adjust-stock', $depletedItem->id) }}')">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Restock Now</span>
            </button>
        </div>
    </div>
    @endif

    <!-- Header Section with Title & Context -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="font-mono text-xs text-amber-700 font-bold uppercase tracking-wider">Logistics &amp; Warehouse // Live Telemetry</span>
            </div>
            <h1 class="font-heading text-3xl font-bold text-slate-900 tracking-tight">Inventory &amp; Stock Management</h1>
            <p class="font-sans text-xs text-slate-500 max-w-2xl mt-1">
                Live harvest inventory tracking, safety buffers, quick restock adjustments, and automated freshness flags across all storage depots.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="showToast('Exporting inventory ledger to CSV...', 'info')" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200/80 transition-all text-slate-700 font-sans text-xs font-semibold flex items-center gap-2 shadow-2xs">
                <span class="material-symbols-outlined text-[18px] text-slate-500">file_download</span>
                <span>Export CSV</span>
            </button>
            <button type="button" onclick="openFirstProductAdjustment()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold transition-all flex items-center gap-2 shadow-xs">
                <span class="material-symbols-outlined text-[18px]">add_box</span>
                <span>Quick Restock</span>
            </button>
        </div>
    </div>

    <!-- 4 Top KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- KPI 1 -->
        <div class="bg-white rounded-2xl p-5 shadow-xs flex flex-col justify-between border border-slate-200/80">
            <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] uppercase tracking-wider text-slate-500 font-bold">Total Tracked SKU</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-heading text-3xl font-bold text-slate-900">{{ $totalSkus }}</span>
                    <span class="font-sans text-xs text-slate-500 font-medium">Products</span>
                </div>
                <p class="font-mono text-[11px] text-slate-500 mt-2 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-emerald-600">sync</span>
                    All catalog roots synced
                </p>
            </div>
        </div>

        <!-- KPI 2 -->
        <div class="bg-white rounded-2xl p-5 shadow-xs flex flex-col justify-between border border-slate-200/80">
            <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] uppercase tracking-wider text-slate-500 font-bold">In-Stock Healthy</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-heading text-3xl font-bold text-slate-900">{{ $healthyCount }}</span>
                    <span class="font-sans text-xs text-slate-500 font-medium">Items</span>
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $healthyPct }}%;"></div>
                    </div>
                    <span class="font-mono text-[11px] text-emerald-600 font-semibold">{{ $healthyPct }}%</span>
                </div>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="bg-white rounded-2xl p-5 shadow-xs flex flex-col justify-between border border-slate-200/80">
            <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] uppercase tracking-wider text-amber-700 font-bold">Low Stock Alerts</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">warning</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-heading text-3xl font-bold text-amber-600">{{ $lowStockCount }}</span>
                    <span class="font-sans text-xs text-slate-500 font-medium">Items</span>
                </div>
                <p class="font-mono text-[11px] text-amber-700 mt-2 flex items-center gap-1 font-medium">
                    <span class="material-symbols-outlined text-[14px]">trending_down</span>
                    Buffer below safety line
                </p>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="bg-white rounded-2xl p-5 shadow-xs flex flex-col justify-between border border-slate-200/80">
            <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] uppercase tracking-wider text-rose-600 font-bold">Out of Stock</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">remove_shopping_cart</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-heading text-3xl font-bold text-rose-600">{{ $outOfStockCount }}</span>
                    <span class="font-sans text-xs text-slate-500 font-medium">Depleted</span>
                </div>
                <p class="font-mono text-[11px] text-rose-600 mt-2 font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    Immediate restock required
                </p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar Strip -->
    <div class="bg-white rounded-2xl p-4 shadow-xs mb-4 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 border border-slate-200/80">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0 scrollbar-none">
            <button type="button" class="inv-tab active px-4 py-2 rounded-xl bg-slate-900 text-white font-sans text-xs font-semibold shrink-0 shadow-xs" onclick="filterInvTab('all')">
                All Items ({{ $totalSkus }})
            </button>
            <button type="button" class="inv-tab px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-700 transition-colors font-sans text-xs font-medium shrink-0 flex items-center gap-1.5" onclick="filterInvTab('low')">
                Low Stock
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="font-mono font-bold">{{ $lowStockCount }}</span>
            </button>
            <button type="button" class="inv-tab px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-700 transition-colors font-sans text-xs font-medium shrink-0 flex items-center gap-1.5" onclick="filterInvTab('out')">
                Out of Stock
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span class="font-mono font-bold">{{ $outOfStockCount }}</span>
            </button>
        </div>

        <!-- Search Input -->
        <div class="flex items-center gap-3 flex-1 lg:max-w-md">
            <div class="flex items-center w-full px-3.5 py-2 bg-slate-50/70 border border-slate-300 rounded-xl focus-within:bg-white focus-within:border-amber-500 focus-within:ring-4 focus-within:ring-amber-500/10 transition-all shadow-2xs">
                <span class="material-symbols-outlined text-slate-400 text-[20px] mr-2">search</span>
                <input type="text" id="invSearchInput" oninput="handleInvSearch(this.value)" placeholder="Search by name, SKU, or Batch ID..." class="w-full bg-transparent font-sans text-xs text-slate-900 placeholder:text-slate-400 outline-none border-none">
            </div>
        </div>
    </div>

    <!-- Dedicated Inventory Table Container -->
    <div class="bg-white rounded-2xl shadow-xs overflow-hidden mb-6 border border-slate-200/80">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-mono text-[11px] uppercase tracking-wider border-b border-slate-200/80">
                        <th class="py-3.5 px-6 font-semibold">Product &amp; SKU</th>
                        <th class="py-3.5 px-4 font-semibold">Current Stock Level</th>
                        <th class="py-3.5 px-4 font-semibold">Unit Type</th>
                        <th class="py-3.5 px-4 font-semibold">Safety Buffer</th>
                        <th class="py-3.5 px-4 font-semibold">Stock Status</th>
                        <th class="py-3.5 px-4 font-semibold">Harvest &amp; Freshness</th>
                        <th class="py-3.5 px-4 font-semibold">Last Sync</th>
                        <th class="py-3.5 px-6 text-right font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-sans text-xs text-slate-900" id="inventoryTableBody">
                    @forelse($products as $product)
                    @php
                        $isLow = $product->isLowStock();
                        $isOutOfStock = $product->stock <= 0;
                        $invStatus = $isOutOfStock ? 'out' : ($isLow ? 'low' : 'healthy');
                        $elementId = 'stock-qty-' . $product->id;
                    @endphp
                    <tr class="inv-row hover:bg-slate-50/70 transition-colors" data-status="{{ $invStatus }}" data-name="{{ $product->name }}">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover shrink-0 bg-slate-100 border border-slate-200/80 shadow-2xs">
                                <div class="flex flex-col min-w-0">
                                    <span class="font-heading font-semibold text-sm text-slate-900 truncate">{{ $product->name }}</span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="font-mono text-[11px] text-slate-500">{{ $product->sku ?? ('SKU-' . $product->id) }}</span>
                                        <span class="text-slate-300 text-[10px]">•</span>
                                        <span class="font-mono text-[10px] text-slate-500 uppercase">{{ $product->category?->name ?? 'Farm Yield' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <!-- Inline Quick-Adjustment Steppers (+/-) -->
                            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-xl w-fit shadow-2xs">
                                <button type="button" class="w-6 h-6 rounded-lg bg-white hover:bg-slate-100 text-slate-700 flex items-center justify-center font-bold transition-all shadow-2xs border border-slate-200/70" onclick="stepStock('{{ $elementId }}', -1, {{ $product->id }})">
                                    -
                                </button>
                                <span class="font-mono text-xs font-bold px-1.5 {{ $isOutOfStock ? 'text-rose-600' : ($isLow ? 'text-amber-600' : 'text-slate-900') }}" id="{{ $elementId }}">
                                    {{ $product->stock }}
                                </span>
                                <span class="font-sans text-[11px] text-slate-500">{{ $product->unit_type ?? 'kg' }}</span>
                                <button type="button" class="w-6 h-6 rounded-lg bg-white hover:bg-slate-100 text-slate-700 flex items-center justify-center font-bold transition-all shadow-2xs border border-slate-200/70" onclick="stepStock('{{ $elementId }}', 1, {{ $product->id }})">
                                    +
                                </button>
                            </div>
                        </td>
                        <td class="py-4 px-4 font-mono text-xs text-slate-600">
                            {{ ucfirst($product->unit_type ?? 'kg') }} ({{ $product->unit_type ?? 'kg' }})
                        </td>
                        <td class="py-4 px-4 font-mono text-xs text-slate-800 font-medium">
                            Min {{ $product->low_stock_threshold ?? 10 }} {{ $product->unit_type ?? 'kg' }}
                        </td>
                        <td class="py-4 px-4">
                            @if($isOutOfStock)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/60 font-mono text-[10px] uppercase font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Out of Stock
                            </span>
                            @elseif($isLow)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 font-mono text-[10px] uppercase font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Low Stock
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] uppercase font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> In Stock
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col">
                                @if($product->isOrganicCategory() && $product->is_perishable && $product->expiry_date)
                                <div class="flex items-center gap-1 text-amber-700 font-semibold font-sans text-xs">
                                    <span class="material-symbols-outlined text-[15px]">hourglass_bottom</span>
                                    <span>{{ max(0, (int) round(now()->floatDiffInDays($product->expiry_date, false))) }} Days Left</span>
                                </div>
                                @elseif($product->isOrganicCategory())
                                <div class="flex items-center gap-1 text-emerald-700 font-semibold font-sans text-xs">
                                    <span class="material-symbols-outlined text-[15px]">eco</span>
                                    <span>Stable Organic Shelf Life</span>
                                </div>
                                @else
                                <div class="flex items-center gap-1 text-slate-500 font-semibold font-sans text-xs">
                                    <span>Standard Retail SKU</span>
                                </div>
                                @endif
                                <span class="font-mono text-[10px] text-slate-400">BATCH-{{ $product->id }}-{{ date('M') }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col">
                                <span class="font-sans text-xs text-slate-700">{{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Recent' }}</span>
                                <span class="font-mono text-[10px] text-slate-400 uppercase">Automated Batch</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button type="button" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-amber-50 hover:text-amber-700 hover:border-amber-300 border border-slate-200/80 transition-all text-slate-700 font-heading text-xs font-semibold shadow-2xs" onclick="openAdjustmentModal('{{ addslashes($product->name) }}', '{{ $product->sku ?? ('SKU-' . $product->id) }}', '{{ $product->stock }} {{ $product->unit_type }}', '{{ $product->low_stock_threshold ?? 10 }} {{ $product->unit_type }}', '{{ $product->unit_type }}', '{{ route('seller.products.adjust-stock', $product->id) }}')">
                                Adjust
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400 font-sans text-xs">
                            No warehouse stock records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Dedicated Modal: Compact Quick Stock Adjustment -->
<div class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 transition-opacity duration-200" id="stockModalBackdrop">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative border border-slate-200">
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-3 mb-4 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="font-mono text-[10px] uppercase text-amber-700 font-bold">Warehouse Action</span>
                    <span class="text-slate-300">•</span>
                    <span class="font-mono text-xs text-slate-500" id="modalSku">SKU-000</span>
                </div>
                <h2 class="font-heading text-lg font-bold text-slate-900 mt-0.5" id="modalTitle">Quick Stock Adjustment</h2>
                <p class="font-sans text-xs text-slate-500 mt-0.5" id="modalProductName">
                    Product Title
                </p>
            </div>
            <button type="button" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors flex items-center justify-center text-slate-500 hover:text-slate-800" onclick="closeModal()">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form id="stockAdjustmentForm" method="POST" action="" x-data="{ updating: false }" @submit="if(updating){ $event.preventDefault(); return; } updating = true;">
            @csrf
            <input type="hidden" name="action" id="modalActionInput" value="add">

            <!-- Current Stock Status Tile -->
            <div class="bg-slate-50 rounded-xl p-4 mb-4 flex items-center justify-between border border-slate-200">
                <div>
                    <span class="font-mono text-[10px] uppercase text-slate-500 font-semibold">Current Available Stock</span>
                    <div class="font-heading text-xl font-bold text-slate-900 mt-0.5" id="modalCurrentStock">
                        0 kg
                    </div>
                </div>
                <div class="text-right">
                    <span class="font-mono text-[10px] uppercase text-slate-500 font-semibold">Safety Threshold</span>
                    <div class="font-mono text-xs text-amber-700 font-bold mt-0.5" id="modalThreshold">
                        Min 10 kg
                    </div>
                </div>
            </div>

            <!-- Action Type Selector Tabs (3 Modes) -->
            <div class="grid grid-cols-3 gap-1 bg-slate-100 p-1 rounded-xl mb-4 border border-slate-200/80">
                <button type="button" class="py-2 text-center rounded-lg bg-white font-sans text-xs font-bold text-slate-900 shadow-2xs transition-all border border-slate-200/70" id="tabAdd" onclick="setActionType('add')">
                    Add Stock (+)
                </button>
                <button type="button" class="py-2 text-center rounded-lg text-slate-600 hover:text-slate-900 font-sans text-xs font-medium transition-all" id="tabReduce" onclick="setActionType('reduce')">
                    Reduce (-)
                </button>
                <button type="button" class="py-2 text-center rounded-lg text-slate-600 hover:text-slate-900 font-sans text-xs font-medium transition-all" id="tabSet" onclick="setActionType('set')">
                    Set Exact (=)
                </button>
            </div>

            <!-- Input Field & Unit -->
            <div class="mb-4">
                <label class="block font-sans text-xs font-semibold text-slate-700 mb-1">
                    Adjustment Quantity
                </label>
                <div class="flex items-center bg-slate-50/60 rounded-xl px-4 py-1 border border-slate-300 focus-within:bg-white focus-within:border-amber-500 focus-within:ring-4 focus-within:ring-amber-500/10 transition-all shadow-2xs">
                    <span class="font-mono text-base font-bold text-amber-600 mr-2" id="adjustmentSign">+</span>
                    <input type="number" name="quantity" id="modalQuantityInput" oninput="calculatePreview()" value="10" min="0" required class="w-full bg-transparent font-mono font-bold text-lg text-slate-900 outline-none py-2 border-none">
                    <span class="font-mono text-xs text-slate-500 uppercase font-semibold ml-2" id="modalUnitLabel">kg</span>
                </div>
            </div>

            <!-- Reason for Adjustment Dropdown -->
            <div class="mb-4">
                <label class="block font-sans text-xs font-semibold text-slate-700 mb-1">
                    Reason for Inventory Adjustment
                </label>
                <select name="reason" class="w-full bg-slate-50/60 text-slate-900 font-sans text-xs rounded-xl px-4 py-3 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none cursor-pointer transition-all shadow-2xs">
                    <option selected>New Morning Harvest Intake</option>
                    <option>Damaged or spoiled in transit</option>
                    <option>Physical Warehouse Inventory Recount</option>
                    <option>Wholesale Auction Lot Allocation</option>
                    <option>Direct Farm Gate Customer Purchase</option>
                </select>
            </div>

            <!-- Updated Stock Preview Notification Box -->
            <div class="bg-slate-50 p-4 rounded-xl mb-6 flex items-center justify-between border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white text-emerald-600 flex items-center justify-center font-bold shadow-2xs border border-slate-200">
                        <span class="material-symbols-outlined text-[20px]">trending_up</span>
                    </div>
                    <div>
                        <span class="font-mono text-[10px] text-slate-500 uppercase font-semibold">New Projected Level</span>
                        <div class="font-heading text-sm font-bold text-slate-900" id="modalPreview">
                            New Stock: 10 kg
                        </div>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] uppercase font-bold" id="modalStatusBadge">
                    Healthy In-Stock
                </span>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200/80 transition-colors font-sans text-xs font-semibold text-slate-700 shadow-2xs" onclick="closeModal()">
                    Cancel
                </button>
                <button type="submit" :disabled="updating" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 transition-all font-heading text-xs font-bold flex items-center gap-2 shadow-xs disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-show="!updating" class="material-symbols-outlined text-[18px]">verified</span>
                    <span x-show="updating" x-cloak><x-loading-spinner size="16px" color="text-slate-950" /></span>
                    <span x-text="updating ? 'Updating...' : 'Confirm Stock Update'">Confirm Stock Update</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentMode = 'add';
    let baseQty = 0;
    let unit = 'kg';

    function setActionType(mode) {
        currentMode = mode;
        const actionInput = document.getElementById('modalActionInput');
        if (actionInput) actionInput.value = mode;

        const tabAdd = document.getElementById('tabAdd');
        const tabReduce = document.getElementById('tabReduce');
        const tabSet = document.getElementById('tabSet');
        const sign = document.getElementById('adjustmentSign');

        [tabAdd, tabReduce, tabSet].forEach(btn => {
            btn.className = "py-2 text-center rounded-lg text-slate-600 hover:text-slate-900 font-sans text-xs font-medium transition-all";
        });

        if (mode === 'add') {
            tabAdd.className = "py-2 text-center rounded-lg bg-white font-sans text-xs font-bold text-slate-900 shadow-2xs transition-all border border-slate-200/70";
            sign.innerText = "+";
        } else if (mode === 'reduce') {
            tabReduce.className = "py-2 text-center rounded-lg bg-white font-sans text-xs font-bold text-slate-900 shadow-2xs transition-all border border-slate-200/70";
            sign.innerText = "-";
        } else {
            tabSet.className = "py-2 text-center rounded-lg bg-white font-sans text-xs font-bold text-slate-900 shadow-2xs transition-all border border-slate-200/70";
            sign.innerText = "=";
        }
        calculatePreview();
    }

    function calculatePreview() {
        const input = document.getElementById('modalQuantityInput');
        const preview = document.getElementById('modalPreview');
        const badge = document.getElementById('modalStatusBadge');
        const val = parseFloat(input.value) || 0;

        let result = baseQty;
        if (currentMode === 'add') {
            result = baseQty + val;
        } else if (currentMode === 'reduce') {
            result = Math.max(0, baseQty - val);
        } else {
            result = val;
        }

        if (preview) preview.innerText = `New Stock: ${result} ${unit}`;

        if (badge) {
            if (result <= 0) {
                badge.innerText = "Out of Stock";
                badge.className = "px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/60 font-mono text-[10px] uppercase font-bold";
            } else if (result < 10) {
                badge.innerText = "Low Stock Buffer";
                badge.className = "px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 font-mono text-[10px] uppercase font-bold";
            } else {
                badge.innerText = "Healthy In-Stock";
                badge.className = "px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] uppercase font-bold";
            }
        }
    }

    function stepStock(elementId, delta, productId) {
        const el = document.getElementById(elementId);
        if (!el) return;
        let currentVal = parseInt(el.innerText, 10) || 0;
        let newVal = Math.max(0, currentVal + delta);
        el.innerText = newVal;
        el.classList.add('opacity-40', 'animate-pulse');

        fetch(`/seller/products/${productId}/stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: delta > 0 ? 'add' : 'reduce',
                quantity: Math.abs(delta),
                reason: 'Inline stepper quick adjustment'
            })
        })
        .finally(() => {
            el.classList.remove('opacity-40', 'animate-pulse');
        })
        .catch(err => console.error('Stock step error', err));
    }

    function openAdjustmentModal(name, sku, currentStock, threshold, unitType, updateUrl) {
        document.getElementById('modalProductName').innerText = name;
        document.getElementById('modalSku').innerText = sku;
        document.getElementById('modalCurrentStock').innerText = currentStock;
        document.getElementById('modalThreshold').innerText = `Min ${threshold}`;
        document.getElementById('modalUnitLabel').innerText = unitType;
        if (updateUrl) {
            document.getElementById('stockAdjustmentForm').action = updateUrl;
        }

        baseQty = parseFloat(currentStock) || 0;
        unit = unitType;
        document.getElementById('modalQuantityInput').value = 10;
        setActionType('add');

        const modal = document.getElementById('stockModalBackdrop');
        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('stockModalBackdrop');
        modal.classList.add('hidden');
    }

    function openFirstProductAdjustment() {
        const firstRow = document.querySelector('.inv-row');
        if (firstRow) {
            const btn = firstRow.querySelector('button[onclick*="openAdjustmentModal"]');
            if (btn) btn.click();
        } else {
            showToast('No products available to restock.', 'warning');
        }
    }

    function filterInvTab(tab) {
        const tabs = document.querySelectorAll('.inv-tab');
        tabs.forEach(t => {
            t.className = "inv-tab px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-700 transition-colors font-sans text-xs font-medium shrink-0 flex items-center gap-1.5";
        });
        event.currentTarget.className = "inv-tab active px-4 py-2 rounded-xl bg-slate-900 text-white font-sans text-xs font-semibold shrink-0 shadow-xs flex items-center gap-1.5";

        const rows = document.querySelectorAll('.inv-row');
        rows.forEach(r => {
            const st = r.getAttribute('data-status') || '';
            if (tab === 'all') {
                r.style.display = '';
            } else if (st === tab) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    function handleInvSearch(query) {
        const q = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.inv-row');
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            r.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script>
@endpush
@endsection
