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
    <div class="w-full bg-primary text-white rounded-xl p-4 shadow-md mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-secondary-container/40">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">priority_high</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-secondary-container font-bold">Critical Stock Notice</span>
                    <span class="text-outline text-[12px]">•</span>
                    <span class="font-mono text-xs text-brand-amber font-semibold">{{ $depletedItem->sku ?? ('SKU-' . $depletedItem->id) }}</span>
                </div>
                <p class="font-sans text-xs text-white font-medium mt-0.5">
                    <span class="font-bold text-secondary-container">{{ $depletedItem->name }}</span> is currently completely depleted (0 {{ $depletedItem->unit_type }}). Buyers are encountering out-of-stock bounce rates.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 self-end md:self-auto shrink-0">
            <button type="button" class="px-4 py-2 rounded-xl bg-secondary-container text-on-secondary-container hover:bg-secondary-fixed transition-colors font-heading text-xs font-bold flex items-center gap-1.5 shadow-sm" onclick="openAdjustmentModal('{{ addslashes($depletedItem->name) }}', '{{ $depletedItem->sku ?? ('SKU-' . $depletedItem->id) }}', '0 {{ $depletedItem->unit_type }}', '{{ $depletedItem->low_stock_threshold ?? 10 }} {{ $depletedItem->unit_type }}', '{{ $depletedItem->unit_type }}', '{{ route('seller.products.adjust-stock', $depletedItem->id) }}')">
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
                <span class="font-mono text-xs text-secondary font-bold tracking-tight">LOGISTICS &amp; WAREHOUSE // LIVE TELEMETRY</span>
            </div>
            <h1 class="font-heading text-3xl font-bold text-on-surface tracking-tight">Inventory &amp; Stock Management</h1>
            <p class="font-sans text-xs text-on-surface-variant max-w-2xl mt-1">
                Live harvest inventory tracking, safety buffers, quick restock adjustments, and automated freshness flags across all storage depots.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="alert('Exporting inventory ledger to CSV...')" class="px-4 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high transition-all text-on-surface font-sans text-xs font-medium flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">file_download</span>
                <span>Export CSV</span>
            </button>
            <button type="button" onclick="openFirstProductAdjustment()" class="px-4 py-2.5 rounded-xl bg-secondary-container text-on-secondary-container hover:bg-secondary-fixed font-heading text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">add_box</span>
                <span>Quick Restock</span>
            </button>
        </div>
    </div>

    <!-- 4 Top KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- KPI 1 -->
        <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm flex flex-col justify-between border border-[#E2DFD7]/50">
            <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] uppercase tracking-wider text-on-surface-variant font-bold">Total Tracked SKU</span>
                <div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-on-surface">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-heading text-3xl font-bold text-on-surface">{{ $totalSkus }}</span>
                    <span class="font-sans text-xs text-on-surface-variant font-medium">Products</span>
                </div>
                <p class="font-mono text-[11px] text-on-surface-variant mt-2 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-on-tertiary-container">sync</span>
                    All catalog roots synced
                </p>
            </div>
        </div>

        <!-- KPI 2 -->
        <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm flex flex-col justify-between border border-[#E2DFD7]/50">
            <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] uppercase tracking-wider text-on-surface-variant font-bold">In-Stock Healthy</span>
                <div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-on-tertiary-container">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-heading text-3xl font-bold text-on-surface">{{ $healthyCount }}</span>
                    <span class="font-sans text-xs text-on-surface-variant font-medium">Items</span>
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <div class="w-full bg-surface-container rounded-full h-1.5 overflow-hidden">
                        <div class="bg-on-tertiary-container h-full rounded-full" style="width: {{ $healthyPct }}%;"></div>
                    </div>
                    <span class="font-mono text-[11px] text-on-tertiary-container font-semibold">{{ $healthyPct }}%</span>
                </div>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm flex flex-col justify-between border border-[#E2DFD7]/50">
            <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] uppercase tracking-wider text-secondary font-bold">Low Stock Alerts</span>
                <div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-[18px]">warning</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-heading text-3xl font-bold text-secondary">{{ $lowStockCount }}</span>
                    <span class="font-sans text-xs text-on-surface-variant font-medium">Items</span>
                </div>
                <p class="font-mono text-[11px] text-secondary mt-2 flex items-center gap-1 font-medium">
                    <span class="material-symbols-outlined text-[14px]">trending_down</span>
                    Buffer below safety line
                </p>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm flex flex-col justify-between border border-[#E2DFD7]/50">
            <div class="flex items-center justify-between mb-3">
                <span class="font-mono text-[10px] uppercase tracking-wider text-error font-bold">Out of Stock</span>
                <div class="w-8 h-8 rounded-lg bg-error-container text-on-error-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">remove_shopping_cart</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-heading text-3xl font-bold text-error">{{ $outOfStockCount }}</span>
                    <span class="font-sans text-xs text-on-surface-variant font-medium">Depleted</span>
                </div>
                <p class="font-mono text-[11px] text-error mt-2 font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    Immediate restock required
                </p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar Strip -->
    <div class="bg-surface-container-lowest rounded-xl p-4 shadow-sm mb-4 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 border border-[#E2DFD7]/50">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0 scrollbar-none">
            <button type="button" class="inv-tab active px-4 py-2 rounded-xl bg-primary text-white font-sans text-xs font-semibold shrink-0 shadow-sm" onclick="filterInvTab('all')">
                All Items ({{ $totalSkus }})
            </button>
            <button type="button" class="inv-tab px-4 py-2 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors font-sans text-xs font-medium shrink-0 flex items-center gap-1.5" onclick="filterInvTab('low')">
                Low Stock
                <span class="w-2 h-2 rounded-full bg-secondary-container"></span>
                <span class="font-mono font-bold">{{ $lowStockCount }}</span>
            </button>
            <button type="button" class="inv-tab px-4 py-2 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors font-sans text-xs font-medium shrink-0 flex items-center gap-1.5" onclick="filterInvTab('out')">
                Out of Stock
                <span class="w-2 h-2 rounded-full bg-error"></span>
                <span class="font-mono font-bold">{{ $outOfStockCount }}</span>
            </button>
        </div>

        <!-- Search Input -->
        <div class="flex items-center gap-3 flex-1 lg:max-w-md">
            <div class="flex items-center w-full px-4 py-2 bg-surface-container rounded-xl focus-within:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-on-surface-variant text-[20px] mr-2">search</span>
                <input type="text" id="invSearchInput" oninput="handleInvSearch(this.value)" placeholder="Search by name, SKU, or Batch ID..." class="w-full bg-transparent font-sans text-xs text-on-surface placeholder:text-on-surface-variant outline-none border-none">
            </div>
        </div>
    </div>

    <!-- Dedicated Inventory Table Container -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden mb-6 border border-[#E2DFD7]/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-mono text-[11px] uppercase tracking-wider">
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
                <tbody class="divide-y divide-surface-container-high/40 font-sans text-xs text-on-surface" id="inventoryTableBody">
                    @forelse($products as $product)
                    @php
                        $isLow = $product->isLowStock();
                        $isOutOfStock = $product->stock <= 0;
                        $invStatus = $isOutOfStock ? 'out' : ($isLow ? 'low' : 'healthy');
                        $elementId = 'stock-qty-' . $product->id;
                    @endphp
                    <tr class="inv-row hover:bg-surface-container-low/60 transition-colors" data-status="{{ $invStatus }}" data-name="{{ $product->name }}">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover shrink-0 bg-surface-container border border-surface-container-high">
                                <div class="flex flex-col min-w-0">
                                    <span class="font-heading font-semibold text-sm text-on-surface truncate">{{ $product->name }}</span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="font-mono text-[11px] text-on-surface-variant">{{ $product->sku ?? ('SKU-' . $product->id) }}</span>
                                        <span class="text-outline-variant text-[10px]">•</span>
                                        <span class="font-mono text-[10px] text-on-surface-variant uppercase">{{ $product->category?->name ?? 'Farm Yield' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <!-- Inline Quick-Adjustment Steppers (+/-) -->
                            <div class="flex items-center gap-2 bg-surface-container px-2.5 py-1 rounded-xl w-fit border border-surface-container-high">
                                <button type="button" class="w-6 h-6 rounded-lg bg-surface-container-lowest hover:bg-surface text-on-surface flex items-center justify-center font-bold transition-all shadow-xs" onclick="stepStock('{{ $elementId }}', -1, {{ $product->id }})">
                                    -
                                </button>
                                <span class="font-mono text-xs font-bold px-1.5 {{ $isOutOfStock ? 'text-error' : ($isLow ? 'text-secondary' : 'text-on-surface') }}" id="{{ $elementId }}">
                                    {{ $product->stock }}
                                </span>
                                <span class="font-sans text-[11px] text-on-surface-variant">{{ $product->unit_type ?? 'kg' }}</span>
                                <button type="button" class="w-6 h-6 rounded-lg bg-surface-container-lowest hover:bg-surface text-on-surface flex items-center justify-center font-bold transition-all shadow-xs" onclick="stepStock('{{ $elementId }}', 1, {{ $product->id }})">
                                    +
                                </button>
                            </div>
                        </td>
                        <td class="py-4 px-4 font-mono text-xs text-on-surface-variant">
                            {{ ucfirst($product->unit_type ?? 'kg') }} ({{ $product->unit_type ?? 'kg' }})
                        </td>
                        <td class="py-4 px-4 font-mono text-xs text-on-surface font-medium">
                            Min {{ $product->low_stock_threshold ?? 10 }} {{ $product->unit_type ?? 'kg' }}
                        </td>
                        <td class="py-4 px-4">
                            @if($isOutOfStock)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-error-container text-error font-mono text-[10px] uppercase font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-error"></span> Out of Stock
                            </span>
                            @elseif($isLow)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-secondary-fixed/50 text-secondary font-mono text-[10px] uppercase font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> Low Stock
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-tertiary-fixed/30 text-on-tertiary-container font-mono text-[10px] uppercase font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container"></span> In Stock
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col">
                                @if($product->is_perishable && $product->expiry_date)
                                <div class="flex items-center gap-1 text-secondary font-semibold font-sans text-xs">
                                    <span class="material-symbols-outlined text-[15px]">hourglass_bottom</span>
                                    <span>{{ now()->diffInDays($product->expiry_date, false) }} Days Left</span>
                                </div>
                                @else
                                <div class="flex items-center gap-1 text-on-surface font-semibold font-sans text-xs">
                                    <span>Stable Shelf Life</span>
                                </div>
                                @endif
                                <span class="font-mono text-[10px] text-on-surface-variant">BATCH-{{ $product->id }}-{{ date('M') }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col">
                                <span class="font-sans text-xs text-on-surface">{{ $product->updated_at ? $product->updated_at->diffForHumans() : 'Recent' }}</span>
                                <span class="font-mono text-[10px] text-on-surface-variant uppercase">Automated Batch</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button type="button" class="px-3.5 py-1.5 rounded-xl bg-surface-container hover:bg-secondary-container hover:text-on-secondary-container transition-all text-on-surface font-heading text-xs font-semibold" onclick="openAdjustmentModal('{{ addslashes($product->name) }}', '{{ $product->sku ?? ('SKU-' . $product->id) }}', '{{ $product->stock }} {{ $product->unit_type }}', '{{ $product->low_stock_threshold ?? 10 }} {{ $product->unit_type }}', '{{ $product->unit_type }}', '{{ route('seller.products.adjust-stock', $product->id) }}')">
                                Adjust
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-on-surface-variant font-sans text-xs">
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
<div class="hidden fixed inset-0 bg-primary/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 transition-opacity duration-200" id="stockModalBackdrop">
    <div class="bg-surface-container-lowest rounded-xl max-w-lg w-full p-6 shadow-2xl relative border border-surface-container-high">
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-3 mb-4 border-b border-surface-container-high">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="font-mono text-[10px] uppercase text-secondary font-bold">Warehouse Action</span>
                    <span class="text-outline-variant">•</span>
                    <span class="font-mono text-xs text-on-surface-variant" id="modalSku">SKU-000</span>
                </div>
                <h2 class="font-heading text-lg font-bold text-on-surface mt-0.5" id="modalTitle">Quick Stock Adjustment</h2>
                <p class="font-sans text-xs text-on-surface-variant mt-0.5" id="modalProductName">
                    Product Title
                </p>
            </div>
            <button type="button" class="w-8 h-8 rounded-xl bg-surface-container hover:bg-surface-container-high transition-colors flex items-center justify-center text-on-surface-variant" onclick="closeModal()">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form id="stockAdjustmentForm" method="POST" action="">
            @csrf
            <input type="hidden" name="action" id="modalActionInput" value="add">

            <!-- Current Stock Status Tile -->
            <div class="bg-surface-container-low rounded-xl p-4 mb-4 flex items-center justify-between border border-surface-container-high">
                <div>
                    <span class="font-mono text-[10px] uppercase text-on-surface-variant">Current Available Stock</span>
                    <div class="font-heading text-xl font-bold text-on-surface mt-0.5" id="modalCurrentStock">
                        0 kg
                    </div>
                </div>
                <div class="text-right">
                    <span class="font-mono text-[10px] uppercase text-on-surface-variant">Safety Threshold</span>
                    <div class="font-mono text-xs text-secondary font-semibold mt-0.5" id="modalThreshold">
                        Min 10 kg
                    </div>
                </div>
            </div>

            <!-- Action Type Selector Tabs (3 Modes) -->
            <div class="grid grid-cols-3 gap-1 bg-surface-container p-1 rounded-xl mb-4">
                <button type="button" class="py-2 text-center rounded-lg bg-surface-container-lowest font-sans text-xs font-bold text-on-surface shadow-xs transition-all" id="tabAdd" onclick="setActionType('add')">
                    Add Stock (+)
                </button>
                <button type="button" class="py-2 text-center rounded-lg text-on-surface-variant hover:text-on-surface font-sans text-xs font-medium transition-all" id="tabReduce" onclick="setActionType('reduce')">
                    Reduce (-)
                </button>
                <button type="button" class="py-2 text-center rounded-lg text-on-surface-variant hover:text-on-surface font-sans text-xs font-medium transition-all" id="tabSet" onclick="setActionType('set')">
                    Set Exact (=)
                </button>
            </div>

            <!-- Input Field & Unit -->
            <div class="mb-4">
                <label class="block font-sans text-xs font-semibold text-on-surface-variant mb-1">
                    Adjustment Quantity
                </label>
                <div class="flex items-center bg-surface-container-low rounded-xl px-4 py-1 focus-within:bg-surface-container transition-colors border border-surface-container-high">
                    <span class="font-mono text-base font-bold text-secondary mr-2" id="adjustmentSign">+</span>
                    <input type="number" name="quantity" id="modalQuantityInput" oninput="calculatePreview()" value="10" min="0" required class="w-full bg-transparent font-mono font-bold text-lg text-on-surface outline-none py-2 border-none">
                    <span class="font-mono text-xs text-on-surface-variant uppercase font-semibold ml-2" id="modalUnitLabel">kg</span>
                </div>
            </div>

            <!-- Reason for Adjustment Dropdown -->
            <div class="mb-4">
                <label class="block font-sans text-xs font-semibold text-on-surface-variant mb-1">
                    Reason for Inventory Adjustment
                </label>
                <select name="reason" class="w-full bg-surface-container-low text-on-surface font-sans text-xs rounded-xl px-4 py-3 outline-none cursor-pointer focus:bg-surface-container transition-colors border-none">
                    <option selected>New Morning Harvest Intake</option>
                    <option>Damaged or spoiled in transit</option>
                    <option>Physical Warehouse Inventory Recount</option>
                    <option>Wholesale Auction Lot Allocation</option>
                    <option>Direct Farm Gate Customer Purchase</option>
                </select>
            </div>

            <!-- Updated Stock Preview Notification Box -->
            <div class="bg-surface-container p-4 rounded-xl mb-6 flex items-center justify-between border border-surface-container-high">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-surface-container-lowest text-on-tertiary-container flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-[20px]">trending_up</span>
                    </div>
                    <div>
                        <span class="font-mono text-[10px] text-on-surface-variant uppercase">New Projected Level</span>
                        <div class="font-heading text-sm font-bold text-on-surface" id="modalPreview">
                            New Stock: 10 kg
                        </div>
                    </div>
                </div>
                <span class="px-2 py-1 rounded-md bg-tertiary-fixed/40 text-on-tertiary-container font-mono text-[10px] uppercase font-bold" id="modalStatusBadge">
                    Healthy In-Stock
                </span>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high transition-colors font-sans text-xs font-medium text-on-surface" onclick="closeModal()">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-secondary-container hover:bg-secondary-fixed text-on-secondary-container transition-all font-heading text-xs font-bold flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Confirm Stock Update</span>
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
            btn.className = "py-2 text-center rounded-lg text-on-surface-variant hover:text-on-surface font-sans text-xs font-medium transition-all";
        });

        if (mode === 'add') {
            tabAdd.className = "py-2 text-center rounded-lg bg-surface-container-lowest font-sans text-xs font-bold text-on-surface shadow-xs transition-all";
            sign.innerText = "+";
        } else if (mode === 'reduce') {
            tabReduce.className = "py-2 text-center rounded-lg bg-surface-container-lowest font-sans text-xs font-bold text-on-surface shadow-xs transition-all";
            sign.innerText = "-";
        } else {
            tabSet.className = "py-2 text-center rounded-lg bg-surface-container-lowest font-sans text-xs font-bold text-on-surface shadow-xs transition-all";
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
                badge.className = "px-2 py-1 rounded-md bg-error-container text-error font-mono text-[10px] uppercase font-bold";
            } else if (result < 10) {
                badge.innerText = "Low Stock Buffer";
                badge.className = "px-2 py-1 rounded-md bg-secondary-fixed/50 text-secondary font-mono text-[10px] uppercase font-bold";
            } else {
                badge.innerText = "Healthy In-Stock";
                badge.className = "px-2 py-1 rounded-md bg-tertiary-fixed/40 text-on-tertiary-container font-mono text-[10px] uppercase font-bold";
            }
        }
    }

    function stepStock(elementId, delta, productId) {
        const el = document.getElementById(elementId);
        if (!el) return;
        let currentVal = parseInt(el.innerText, 10) || 0;
        let newVal = Math.max(0, currentVal + delta);
        el.innerText = newVal;

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
        }).catch(err => console.error('Stock step error', err));
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
            alert('No products available to restock.');
        }
    }

    function filterInvTab(tab) {
        const tabs = document.querySelectorAll('.inv-tab');
        tabs.forEach(t => {
            t.classList.remove('bg-primary', 'text-white');
            t.classList.add('bg-surface-container', 'text-on-surface');
        });
        event.currentTarget.classList.remove('bg-surface-container', 'text-on-surface');
        event.currentTarget.classList.add('bg-primary', 'text-white');

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
