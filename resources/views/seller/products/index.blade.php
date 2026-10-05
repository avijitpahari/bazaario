@extends('layouts.seller')

@section('title', 'My Products — Catalog Management — Bazaario')

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
    $sellerProfile = $sellerUser?->sellerProfile;
    $sellerId = $sellerUser?->id ?? 0;

    // Defensive fallback for products collection
    if (!isset($products)) {
        $products = \App\Models\Product::where('seller_id', $sellerId)
            ->with(['category', 'primaryImage', 'images'])
            ->latest()
            ->paginate(15);
    }

    // Defensive fallbacks for status counts
    $totalCount = $totalCount ?? \App\Models\Product::where('seller_id', $sellerId)->count();
    $activeCount = $activeCount ?? \App\Models\Product::where('seller_id', $sellerId)->where('status', 'active')->count();
    $draftCount = $draftCount ?? \App\Models\Product::where('seller_id', $sellerId)->where('status', 'draft')->count();
    $lowStockCount = $lowStockCount ?? \App\Models\Product::where('seller_id', $sellerId)->lowStock()->count();
    $outOfStockCount = $outOfStockCount ?? \App\Models\Product::where('seller_id', $sellerId)->where('stock', '<=', 0)->count();
    $staleCount = $staleCount ?? \App\Models\Product::where('seller_id', $sellerId)->stale()->count();
@endphp

<div class="flex flex-col w-full pb-16">
    <!-- Top Ambient Glow -->
    <div class="relative w-full">
        <div class="absolute -top-10 right-1/4 w-96 h-96 bg-secondary-fixed/30 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute top-20 left-10 w-72 h-72 bg-tertiary-fixed-dim/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
    </div>

    <!-- Header View: Title, Context and Primaries -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-2">
                <span class="font-mono text-[11px] text-secondary uppercase tracking-widest bg-secondary-container/20 px-2 py-0.5 rounded-[6px] font-bold">Catalogue Management</span>
                <span class="text-on-surface-variant text-[12px]">•</span>
                <span class="font-mono text-xs text-on-surface-variant">{{ $totalCount }} Active Stock Keeping Units</span>
            </div>
            <h1 class="font-heading text-3xl sm:text-4xl text-on-surface tracking-tight font-bold">My Products</h1>
            <p class="font-sans text-sm text-on-surface-variant max-w-2xl">
                Manage your artisanal yields, dynamic wholesale pricing, inventory velocity, and perishable freshness guardrails across all regional hubs.
            </p>
        </div>

        <!-- Actions Panel -->
        <div class="flex flex-wrap items-center gap-3" x-data="{ bulkOpen: false }">
            <!-- Dedicated Form for Bulk Actions (P24) -->
            <form id="bulkActionForm" action="{{ route('seller.products.bulk') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="action" id="bulkActionInput" value="">
            </form>

            <div class="relative inline-block text-left">
                <button @click="bulkOpen = !bulkOpen" @click.outside="bulkOpen = false" type="button" class="h-12 px-4 rounded-[14px] bg-surface-container-lowest shadow-sm flex items-center gap-2 text-on-surface hover:bg-surface-container transition-all">
                    <span class="material-symbols-outlined text-[18px]">checklist</span>
                    <span class="font-sans text-sm font-medium">Bulk Actions</span>
                    <span class="material-symbols-outlined text-[18px] text-on-surface-variant transition-transform" :class="{ 'rotate-180': bulkOpen }">keyboard_arrow_down</span>
                </button>
                <div x-cloak x-show="bulkOpen" x-transition class="absolute right-0 mt-2 w-56 rounded-[14px] bg-surface-container-lowest shadow-xl py-2 z-30 border border-surface-container-high">
                    <button type="button" @click="submitBulkAction('activate'); bulkOpen = false;" class="w-full text-left flex items-center gap-2 px-4 py-2 font-sans text-xs text-on-surface hover:bg-surface-container-high transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-on-tertiary-container">check_circle</span> Update Status: Active
                    </button>
                    <button type="button" @click="submitBulkAction('deactivate'); bulkOpen = false;" class="w-full text-left flex items-center gap-2 px-4 py-2 font-sans text-xs text-on-surface hover:bg-surface-container-high transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-on-surface-variant">pause_circle</span> Update Status: Inactive
                    </button>
                    <button type="button" @click="submitBulkAction('archive'); bulkOpen = false;" class="w-full text-left flex items-center gap-2 px-4 py-2 font-sans text-xs text-secondary hover:bg-surface-container-high transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-secondary">archive</span> Archive Selected SKUs
                    </button>
                    <button type="button" @click="submitBulkAction('delete'); bulkOpen = false;" class="w-full text-left flex items-center gap-2 px-4 py-2 font-sans text-xs text-error hover:bg-error-container/30 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-error">delete</span> Delete Selected SKUs
                    </button>
                </div>
            </div>

            <button type="button" onclick="alert('Exporting products to CSV format...')" class="h-12 px-4 rounded-[14px] bg-surface-container-lowest shadow-sm flex items-center gap-2 text-on-surface hover:bg-surface-container transition-all">
                <span class="material-symbols-outlined text-[18px]">file_download</span>
                <span class="font-sans text-sm font-medium">Export CSV</span>
            </button>

            <a href="{{ route('seller.products.create') }}" class="h-12 px-6 rounded-[14px] bg-secondary-container text-on-secondary-container shadow-md hover:bg-secondary-fixed hover:shadow-lg transition-all flex items-center gap-2 font-heading font-semibold text-sm">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Add Product</span>
            </a>
        </div>
    </div>

    <!-- Freshness & Automated Guardrails Banner -->
    <div class="rounded-[14px] bg-surface-container-lowest shadow-sm p-6 mb-6 relative overflow-hidden border border-[#E2DFD7]/50">
        <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-secondary-container/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-start gap-4 max-w-xl">
                <div class="w-12 h-12 rounded-[14px] bg-secondary-container/20 text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">eco</span>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-heading text-lg font-bold text-on-surface">Automated Freshness Engine</span>
                        <span class="font-mono text-[10px] bg-surface-container-high text-on-surface-variant px-1.5 py-0.5 rounded-[6px] font-bold uppercase tracking-wider">V2.4 ACTIVE</span>
                    </div>
                    <p class="font-sans text-xs text-on-surface-variant mt-1">
                        Real-time shelf-life tracking based on farm harvest timestamps, humidity variables, and ambient transport profiles.
                    </p>
                </div>
            </div>

            <!-- Live Automations Toggles -->
            <div class="flex flex-wrap items-center gap-6">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <div class="relative">
                        <input type="checkbox" checked class="sr-only peer" id="autoFlagToggle">
                        <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-sans text-xs font-semibold text-on-surface">Auto-flag stale items</span>
                        <span class="font-mono text-[10px] text-on-surface-variant">Alert buyer on window close</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <div class="relative">
                        <input type="checkbox" checked class="sr-only peer" id="autoHideToggle">
                        <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-sans text-xs font-semibold text-on-surface">Auto-hide expired SKUs</span>
                        <span class="font-mono text-[10px] text-on-surface-variant">Prevent refund liability</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Alert Sub-panel -->
        @if($staleCount > 0)
        <div class="mt-4 pt-3 bg-secondary-container/10 -mx-6 -mb-6 px-6 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-secondary-container/20">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[20px]">warning</span>
                <span class="font-sans text-xs text-on-surface font-medium">
                    <strong class="font-semibold text-secondary">{{ $staleCount }} perishable items require attention:</strong> Harvest window is approaching the 48-hour shelf-life limit.
                </span>
            </div>
            <button type="button" onclick="filterByTab('stale')" class="px-4 py-1.5 rounded-[14px] bg-secondary-container text-on-secondary-container font-sans text-xs font-semibold hover:bg-secondary-fixed transition-colors">
                Review Freshness
            </button>
        </div>
        @endif
    </div>

    <!-- Filter, Search & Tabs Strip -->
    <div class="bg-surface-container-lowest rounded-[14px] shadow-sm p-4 mb-4 flex flex-col gap-4 border border-[#E2DFD7]/50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant">search</span>
                <input type="text" id="productSearchInput" oninput="handleSearch(this.value)" placeholder="Filter by harvest batch, SKU, crop name..." class="w-full h-11 pl-11 pr-4 bg-surface-container-low rounded-[14px] font-sans text-xs text-on-surface placeholder:text-on-surface-variant outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-brand-amber transition-all">
            </div>

            <!-- Quick Metrics Spark & Sorter -->
            <div class="flex items-center gap-4 self-end lg:self-auto">
                <div class="hidden xl:flex items-center gap-3 px-3 py-1.5 bg-surface-container-low rounded-[14px]">
                    <span class="font-mono text-[10px] uppercase text-on-surface-variant font-bold">Catalog Health:</span>
                    <svg class="w-20 h-5 overflow-visible text-on-tertiary-container" fill="none" viewBox="0 0 60 16">
                        <path d="M0 12 L10 10 L20 14 L30 6 L40 9 L50 2 L60 4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                    <span class="font-mono text-xs font-bold text-on-tertiary-container">98.4%</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="font-sans text-xs text-on-surface-variant hidden sm:inline">Sort:</span>
                    <select id="sortDropdown" onchange="handleSort(this.value)" class="h-11 px-3 bg-surface-container-low rounded-[14px] font-sans text-xs text-on-surface outline-none cursor-pointer border-none">
                        <option value="recent">Recently Harvested</option>
                        <option value="revenue">Highest Velocity</option>
                        <option value="stock-asc">Stock: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Filter Pills / Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-nowrap scrollbar-none" id="tabsList">
            <button type="button" class="tab-pill active-tab px-3.5 py-1.5 rounded-[14px] font-sans text-xs font-medium bg-primary text-white transition-all shadow-sm" data-tab="all" onclick="filterByTab('all')">
                All <span class="ml-1 font-mono text-[11px] opacity-80">{{ $totalCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-[14px] font-sans text-xs font-medium bg-surface-container hover:bg-surface-container-high text-on-surface-variant transition-all" data-tab="active" onclick="filterByTab('active')">
                Active <span class="ml-1 font-mono text-[11px]">{{ $activeCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-[14px] font-sans text-xs font-medium bg-surface-container hover:bg-surface-container-high text-on-surface-variant transition-all" data-tab="draft" onclick="filterByTab('draft')">
                Drafts <span class="ml-1 font-mono text-[11px]">{{ $draftCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-[14px] font-sans text-xs font-medium bg-surface-container hover:bg-surface-container-high text-on-surface-variant transition-all" data-tab="low" onclick="filterByTab('low')">
                Low Stock <span class="ml-1 px-1.5 py-0.5 bg-secondary-container/30 text-secondary rounded-[6px] font-mono text-[10px] font-bold">{{ $lowStockCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-[14px] font-sans text-xs font-medium bg-surface-container hover:bg-surface-container-high text-on-surface-variant transition-all" data-tab="out" onclick="filterByTab('out')">
                Out of Stock <span class="ml-1 font-mono text-[11px]">{{ $outOfStockCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-[14px] font-sans text-xs font-medium bg-surface-container hover:bg-surface-container-high text-on-surface-variant transition-all" data-tab="stale" onclick="filterByTab('stale')">
                Stale / Attention <span class="ml-1 px-1.5 py-0.5 bg-error-container text-error rounded-[6px] font-mono text-[10px] font-bold">{{ $staleCount }}</span>
            </button>
        </div>
    </div>

    <!-- Products Master Table Container -->
    <div class="bg-surface-container-lowest rounded-[14px] shadow-sm overflow-hidden border border-[#E2DFD7]/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-mono text-[11px] uppercase tracking-wider">
                        <th class="py-3.5 pl-6 pr-3 w-10">
                            <input type="checkbox" onclick="toggleSelectAll(this)" class="w-4 h-4 rounded text-primary focus:ring-0 cursor-pointer">
                        </th>
                        <th class="py-3.5 px-4 font-semibold">Product / Origin</th>
                        <th class="py-3.5 px-4 font-semibold">Category</th>
                        <th class="py-3.5 px-4 font-semibold">Unit Price</th>
                        <th class="py-3.5 px-4 font-semibold">Current Stock</th>
                        <th class="py-3.5 px-4 font-semibold">Velocity</th>
                        <th class="py-3.5 px-4 font-semibold">Freshness / Harvest SLA</th>
                        <th class="py-3.5 pr-6 pl-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-high/40 font-sans" id="productTableBody">
                    @forelse($products as $product)
                    @php
                        $isLow = $product->isLowStock();
                        $isOutOfStock = $product->stock <= 0;
                        $isStale = $product->isStale();
                        $statusTags = ($product->status ?? 'active') . ($isLow ? ' low' : '') . ($isOutOfStock ? ' out' : '') . ($isStale ? ' stale' : '');
                        $imgUrl = $product->main_image_url ?? asset('images/products/leather_bag_1.jpg');
                    @endphp
                    <tr class="product-row hover:bg-surface-container-low/60 transition-colors" data-category="{{ $product->category?->name ?? 'General' }}" data-name="{{ $product->name }}" data-status="{{ $statusTags }}">
                        <td class="py-4 pl-6 pr-3 align-middle">
                            <input type="checkbox" class="product-check w-4 h-4 rounded text-primary focus:ring-0 cursor-pointer">
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-3">
                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-[10px] object-cover shrink-0 shadow-sm border border-surface-container-high">
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('seller.products.edit', $product->id) }}" class="font-heading text-sm font-semibold text-on-surface truncate hover:text-secondary transition-colors">
                                            {{ $product->name }}
                                        </a>
                                        @if($product->is_perishable)
                                        <span class="material-symbols-outlined text-[14px] text-on-tertiary-container" title="Pesticide-free / Fresh harvest">verified</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="font-mono text-[11px] text-on-surface-variant">{{ $product->sku ?? ('SKU-' . $product->id) }}</span>
                                        <span class="font-mono text-[10px] uppercase bg-surface-container px-1.5 py-0.5 rounded-[4px] text-on-surface-variant font-medium">
                                            {{ $product->farm_origin ?? ($sellerProfile?->shop_name ?? 'Farm Origin') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <span class="font-sans text-xs text-on-surface-variant">{{ $product->category?->name ?? 'Fresh Produce' }}</span>
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="font-mono text-sm font-semibold text-on-surface">
                                ₹{{ number_format($product->price, 0) }}
                                <span class="font-sans text-xs font-normal text-on-surface-variant">/ {{ $product->unit_type ?? 'kg' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm font-semibold text-on-surface">{{ $product->stock }} {{ $product->unit_type ?? 'kg' }}</span>
                                    @if($isOutOfStock)
                                    <span class="px-2 py-0.5 rounded-[6px] bg-error-container text-error font-mono text-[10px] uppercase font-bold">Depleted</span>
                                    @elseif($isLow)
                                    <span class="px-2 py-0.5 rounded-[6px] bg-secondary-container/30 text-secondary font-mono text-[10px] uppercase font-bold">Low Stock</span>
                                    @else
                                    <span class="px-2 py-0.5 rounded-[6px] bg-tertiary-fixed/30 text-on-tertiary-container font-mono text-[10px] uppercase font-bold">In Stock</span>
                                    @endif
                                </div>
                                <div class="w-24 h-1.5 bg-surface-container rounded-full overflow-hidden">
                                    <div class="{{ $isOutOfStock ? 'bg-error w-0' : ($isLow ? 'bg-secondary-container w-1/4' : 'bg-on-tertiary-container w-3/4') }} h-full rounded-full"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="flex flex-col">
                                <span class="font-mono text-xs font-medium text-on-surface">{{ $product->total_reviews ?? 0 }} sold</span>
                                <span class="font-mono text-[10px] text-on-tertiary-container flex items-center">
                                    <span class="material-symbols-outlined text-[12px]">trending_up</span> Active velocity
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-4 align-middle">
                            @if($product->is_perishable && $product->expiry_date)
                            <div class="flex flex-col gap-1">
                                @if($isStale)
                                <div class="flex items-center gap-1.5 text-error font-medium font-sans text-xs">
                                    <span class="material-symbols-outlined text-[16px]">timer_off</span>
                                    <span>Expired / Stale</span>
                                </div>
                                @else
                                <div class="flex items-center gap-1.5 text-secondary font-medium font-sans text-xs">
                                    <span class="material-symbols-outlined text-[16px]">timer</span>
                                    <span>{{ now()->diffInDays($product->expiry_date, false) }} days remaining</span>
                                </div>
                                @endif
                                <span class="font-mono text-[10px] text-on-surface-variant">
                                    Harvested: {{ $product->harvest_date ? $product->harvest_date->format('d M') : 'Recent' }} • SLA Active
                                </span>
                            </div>
                            @else
                            <div class="flex flex-col gap-1">
                                <span class="font-sans text-xs text-on-tertiary-container font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span> Stable Shelf-Life
                                </span>
                                <span class="font-mono text-[10px] text-on-surface-variant">Non-perishable item</span>
                            </div>
                            @endif
                        </td>
                        <td class="py-4 pr-6 pl-4 align-middle text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" class="p-2 rounded-[8px] hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors" onclick="openDrawer('{{ addslashes($product->name) }}', '{{ $product->sku ?? 'SKU-'.$product->id }}', '₹{{ number_format($product->price, 0) }} / {{ $product->unit_type }}', '{{ $product->stock }} {{ $product->unit_type }}', '{{ $product->total_reviews ?? 0 }} units', '{{ $product->harvest_date?->format('d M') ?? 'N/A' }}', '{{ $isStale ? 'Expired' : 'Optimal' }}', '{{ addslashes($product->farm_origin ?? 'Origin Farm') }}', '{{ addslashes($product->category?->name ?? 'General') }}')" title="Inspect SKU">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                                <a href="{{ route('seller.products.edit', $product->id) }}" class="p-2 rounded-[8px] hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors" title="Edit Item">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <button type="button" class="p-2 rounded-[8px] hover:bg-error-container/40 text-on-surface-variant hover:text-error transition-colors" onclick="openDeleteModal('{{ addslashes($product->name) }}', '{{ route('seller.products.destroy', $product->id) }}')" title="Delete Product">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-on-surface-variant">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <span class="material-symbols-outlined text-4xl text-on-surface-variant">inventory_2</span>
                                <p class="font-heading font-semibold text-lg text-on-surface">No products listed yet</p>
                                <p class="text-xs text-on-surface-variant max-w-sm">Publish your first crop harvest or artisanal item to start receiving orders.</p>
                                <a href="{{ route('seller.products.create') }}" class="mt-2 px-5 py-2.5 rounded-[12px] bg-secondary-container text-on-secondary-container font-heading font-semibold text-xs shadow-sm">
                                    Add Your First Product
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-4 bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-4 font-sans text-xs text-on-surface-variant border-t border-surface-container-high/40">
            <div class="flex items-center gap-2">
                <span>Showing <strong class="text-on-surface">{{ $products->count() }}</strong> of <strong class="text-on-surface">{{ $totalCount }}</strong> products</span>
                <span class="text-outline-variant">•</span>
                <span class="font-mono text-[11px]">Selected: <span class="font-bold text-on-surface" id="selectedCounter">0</span> items</span>
            </div>
            <div>
                @if(method_exists($products, 'links'))
                    {{ $products->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Slide-Over Product Details Drawer -->
<div class="hidden fixed inset-0 z-50 overflow-hidden bg-primary/40 backdrop-blur-xs transition-opacity" id="productDrawer">
    <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
        <div class="w-screen max-w-md bg-surface-container-lowest shadow-2xl flex flex-col">
            <!-- Drawer Header -->
            <div class="p-6 bg-surface-container-low flex items-center justify-between border-b border-surface-container-high">
                <div class="flex flex-col">
                    <span class="font-mono text-[10px] text-secondary uppercase font-bold tracking-wider">SKU Inspection</span>
                    <h2 class="font-heading text-lg font-bold text-on-surface truncate max-w-xs" id="drawerTitle">Product Details</h2>
                </div>
                <button type="button" class="p-2 rounded-[10px] hover:bg-surface-container text-on-surface-variant hover:text-on-surface" onclick="closeDrawer()">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Drawer Body Content -->
            <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">
                <!-- Quick Status Chip -->
                <div class="p-4 rounded-[14px] bg-surface-container-low flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] text-on-surface-variant uppercase">Current Status</span>
                        <span class="font-sans text-sm font-semibold text-secondary" id="drawerFreshnessStatus">Checking SLA...</span>
                    </div>
                    <span class="font-mono text-xs bg-surface-container-highest px-2 py-1 rounded-[6px] text-on-surface" id="drawerSku">SKU-000</span>
                </div>

                <!-- Price & Stock Bento -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-4 rounded-[14px] bg-surface-container-lowest shadow-sm flex flex-col border border-surface-container-high">
                        <span class="font-mono text-[10px] text-on-surface-variant uppercase">Listing Price</span>
                        <span class="font-heading text-lg font-bold text-on-surface mt-1" id="drawerPrice">₹0.00</span>
                        <span class="font-mono text-[10px] text-on-tertiary-container mt-0.5">Platform Commission: 10%</span>
                    </div>
                    <div class="p-4 rounded-[14px] bg-surface-container-lowest shadow-sm flex flex-col border border-surface-container-high">
                        <span class="font-mono text-[10px] text-on-surface-variant uppercase">Stock Available</span>
                        <span class="font-heading text-lg font-bold text-on-surface mt-1" id="drawerStock">0 units</span>
                        <span class="font-mono text-[10px] text-on-surface-variant mt-0.5">Depot: Hub-Central</span>
                    </div>
                </div>

                <!-- Harvest & Freshness Insights -->
                <div class="p-4 rounded-[14px] bg-surface-container-low flex flex-col gap-3">
                    <h4 class="font-heading text-sm font-semibold text-on-surface">Freshness SLA Audit</h4>
                    <div class="flex items-center justify-between font-sans text-xs">
                        <span class="text-on-surface-variant">Farm Harvest Date:</span>
                        <span class="font-mono font-medium text-on-surface" id="drawerHarvest">-</span>
                    </div>
                    <div class="flex items-center justify-between font-sans text-xs">
                        <span class="text-on-surface-variant">Farm Origin:</span>
                        <span class="font-sans font-medium text-on-surface" id="drawerOrigin">-</span>
                    </div>
                    <div class="flex items-center justify-between font-sans text-xs">
                        <span class="text-on-surface-variant">Sales Velocity:</span>
                        <span class="font-mono font-medium text-on-surface" id="drawerSales">-</span>
                    </div>
                    <div class="flex items-center justify-between font-sans text-xs">
                        <span class="text-on-surface-variant">Category:</span>
                        <span class="font-sans font-medium text-on-surface" id="drawerCategory">-</span>
                    </div>
                </div>

                <!-- Shelf Life Degradation Visual -->
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[10px] text-on-surface-variant uppercase font-bold">Degradation Curve</span>
                        <span class="font-mono text-[11px] text-on-surface font-medium">Auto-Delist @ 0%</span>
                    </div>
                    <div class="w-full h-3 bg-surface-container rounded-full overflow-hidden flex">
                        <div class="bg-on-tertiary-container h-full w-[40%]" title="Prime Quality"></div>
                        <div class="bg-secondary-container h-full w-[35%]" title="Acceptable Quality"></div>
                        <div class="bg-error h-full w-[25%]" title="Critical Window"></div>
                    </div>
                    <div class="flex justify-between font-mono text-[10px] text-on-surface-variant">
                        <span>Prime (0-48h)</span>
                        <span>Discount (48-72h)</span>
                        <span>Expired (&gt;72h)</span>
                    </div>
                </div>

                <!-- Threshold Quick Adjust -->
                <div class="p-4 rounded-[14px] bg-surface-container-lowest shadow-sm flex flex-col gap-3 border border-surface-container-high">
                    <span class="font-heading text-sm font-semibold text-on-surface">Update Restock Threshold</span>
                    <div class="flex items-center gap-3">
                        <input type="number" id="quickThresholdInput" value="10" class="w-24 h-10 px-3 bg-surface-container-low rounded-[10px] font-mono text-xs text-on-surface outline-none">
                        <button type="button" onclick="alert('Threshold updated successfully')" class="h-10 px-4 rounded-[10px] bg-primary text-white font-sans text-xs font-semibold hover:bg-primary/90 transition-colors">
                            Save Limit
                        </button>
                    </div>
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="p-4 bg-surface-container-low flex items-center justify-between gap-3 border-t border-surface-container-high">
                <button type="button" onclick="closeDrawer()" class="h-11 px-4 rounded-[14px] bg-surface-container hover:bg-surface-container-high text-on-surface font-sans text-xs font-medium">
                    Dismiss
                </button>
                <button type="button" onclick="alert('Listing synced to mobile catalog'); closeDrawer();" class="h-11 px-5 rounded-[14px] bg-secondary-container text-on-secondary-container font-heading text-xs font-semibold hover:bg-secondary-fixed">
                    Sync Listing
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Product Confirmation Modal -->
<div class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-primary/40 backdrop-blur-xs transition-opacity" id="deleteModal">
    <div class="bg-surface-container-lowest rounded-[14px] shadow-2xl max-w-md w-full p-6 flex flex-col gap-4 border border-surface-container-high transform scale-100 transition-transform">
        <div class="w-12 h-12 rounded-[14px] bg-error-container text-error flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[26px]">delete_forever</span>
        </div>
        <div class="flex flex-col gap-1">
            <h3 class="font-heading text-lg font-bold text-on-surface">Delete this product?</h3>
            <p class="font-sans text-xs text-on-surface-variant leading-relaxed">
                Are you sure you want to remove <span class="font-semibold text-on-surface" id="deleteItemTitle"></span>? This action cannot be undone and will cancel active customer cart reservations.
            </p>
        </div>
        <form id="deleteProductForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeDeleteModal()" class="h-10 px-4 rounded-[12px] bg-surface-container hover:bg-surface-container-high text-on-surface font-sans text-xs font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" class="h-10 px-4 rounded-[12px] bg-error text-white hover:bg-on-error-container font-heading text-xs font-semibold transition-colors">
                    Delete Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openDrawer(title, sku, price, stock, sales, harvest, freshness, origin, category) {
        document.getElementById('drawerTitle').textContent = title;
        document.getElementById('drawerSku').textContent = sku;
        document.getElementById('drawerPrice').textContent = price;
        document.getElementById('drawerStock').textContent = stock;
        document.getElementById('drawerSales').textContent = sales;
        document.getElementById('drawerHarvest').textContent = harvest;
        document.getElementById('drawerFreshnessStatus').textContent = freshness;
        document.getElementById('drawerOrigin').textContent = origin;
        document.getElementById('drawerCategory').textContent = category;
        document.getElementById('productDrawer').classList.remove('hidden');
    }

    function closeDrawer() {
        document.getElementById('productDrawer').classList.add('hidden');
    }

    function openDeleteModal(productName, deleteUrl) {
        document.getElementById('deleteItemTitle').textContent = `"${productName}"`;
        document.getElementById('deleteProductForm').action = deleteUrl;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function filterByTab(tab) {
        const pills = document.querySelectorAll('.tab-pill');
        pills.forEach(p => {
            if (p.getAttribute('data-tab') === tab) {
                p.classList.remove('bg-surface-container', 'text-on-surface-variant');
                p.classList.add('bg-primary', 'text-white');
            } else {
                p.classList.add('bg-surface-container', 'text-on-surface-variant');
                p.classList.remove('bg-primary', 'text-white');
            }
        });

        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            const statuses = row.getAttribute('data-status') || '';
            if (tab === 'all') {
                row.style.display = '';
            } else if (statuses.includes(tab)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function handleSearch(query) {
        const q = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }

    function handleSort(criteria) {
        console.log('Sorted table by:', criteria);
    }

    function toggleSelectAll(master) {
        const checks = document.querySelectorAll('.product-check');
        checks.forEach(c => c.checked = master.checked);
        updateSelectedCount();
    }

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('product-check')) {
            updateSelectedCount();
        }
    });

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.product-check:checked').length;
        const counter = document.getElementById('selectedCounter');
        if (counter) counter.textContent = checked;
    }
</script>
@endpush
@endsection
