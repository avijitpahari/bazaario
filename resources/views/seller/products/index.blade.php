@extends('layouts.seller')

@section('title', 'Product Catalogue — Bazaario Warm Modernist')

@section('content')
<div class="flex flex-col w-full pb-16">
    <!-- Breadcrumb & Top Command Row -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4 mb-4">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <span class="font-mono text-[11px] text-amber-700 uppercase tracking-widest bg-amber-50 px-2 py-0.5 rounded-md font-bold border border-amber-200/60">Catalogue Management</span>
                <span class="text-slate-300 text-[12px]">•</span>
                <span class="font-mono text-xs text-slate-500">{{ $totalCount }} Active Stock Keeping Units</span>
            </div>
            <h1 class="font-heading text-3xl sm:text-4xl text-slate-900 tracking-tight font-bold">My Products</h1>
            <p class="font-sans text-sm text-slate-500 max-w-2xl">
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
                <button @click="bulkOpen = !bulkOpen" @click.outside="bulkOpen = false" type="button" class="h-11 px-4 rounded-xl bg-white border border-slate-200/80 shadow-2xs flex items-center gap-2 text-slate-800 hover:bg-slate-50 transition-all font-sans text-xs font-semibold">
                    <span class="material-symbols-outlined text-[18px] text-slate-500">checklist</span>
                    <span>Bulk Actions</span>
                    <span class="material-symbols-outlined text-[18px] text-slate-400 transition-transform" :class="{ 'rotate-180': bulkOpen }">keyboard_arrow_down</span>
                </button>
                <div x-cloak x-show="bulkOpen" x-transition class="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl py-2 z-30 border border-slate-200">
                    <button type="button" @click="submitBulkAction('activate'); bulkOpen = false;" class="w-full text-left flex items-center gap-2 px-4 py-2 font-sans text-xs text-slate-800 hover:bg-slate-50 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span> Update Status: Active
                    </button>
                    <button type="button" @click="submitBulkAction('deactivate'); bulkOpen = false;" class="w-full text-left flex items-center gap-2 px-4 py-2 font-sans text-xs text-slate-800 hover:bg-slate-50 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">pause_circle</span> Update Status: Inactive
                    </button>
                    <button type="button" @click="submitBulkAction('archive'); bulkOpen = false;" class="w-full text-left flex items-center gap-2 px-4 py-2 font-sans text-xs text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-slate-500">archive</span> Archive Selected SKUs
                    </button>
                    <button type="button" @click="submitBulkAction('delete'); bulkOpen = false;" class="w-full text-left flex items-center gap-2 px-4 py-2 font-sans text-xs text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-rose-600">delete</span> Delete Selected SKUs
                    </button>
                </div>
            </div>

            <a href="{{ route('seller.products.export') }}" class="h-11 px-4 rounded-xl bg-white border border-slate-200/80 shadow-2xs flex items-center gap-2 text-slate-800 hover:bg-slate-50 transition-all font-sans text-xs font-semibold">
                <span class="material-symbols-outlined text-[18px] text-slate-500">file_download</span>
                <span>Export CSV</span>
            </a>

            <a href="{{ route('seller.products.create') }}" class="h-11 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-xs hover:from-amber-400 hover:to-amber-500 transition-all flex items-center gap-2 font-heading font-bold text-xs">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Add Product</span>
            </a>
        </div>
    </div>

    <!-- Freshness & Automated Guardrails Banner -->
    <div class="rounded-2xl bg-white shadow-2xs p-6 mb-6 relative overflow-hidden border border-slate-200/80">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-start gap-4 max-w-xl">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/60 text-emerald-700 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">eco</span>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-heading text-lg font-bold text-slate-900">Automated Freshness Engine</span>
                        <span class="font-mono text-[10px] bg-emerald-50 text-emerald-800 border border-emerald-200/80 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">V2.4 ACTIVE</span>
                    </div>
                    <p class="font-sans text-xs text-slate-500 mt-1">
                        Real-time shelf-life tracking based on farm harvest timestamps, humidity variables, and ambient transport profiles.
                    </p>
                </div>
            </div>

            <!-- Live Automations Toggles -->
            <div class="flex flex-wrap items-center gap-6">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <div class="relative">
                        <input type="checkbox" checked class="sr-only peer" id="autoFlagToggle">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-sans text-xs font-semibold text-slate-800">Auto-flag stale items</span>
                        <span class="font-mono text-[10px] text-slate-400">Alert buyer on window close</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <div class="relative">
                        <input type="checkbox" checked class="sr-only peer" id="autoHideToggle">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-sans text-xs font-semibold text-slate-800">Auto-hide expired SKUs</span>
                        <span class="font-mono text-[10px] text-slate-400">Prevent refund liability</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Alert Sub-panel -->
        @if($staleCount > 0)
        <div class="mt-4 pt-3.5 bg-amber-50/60 -mx-6 -mb-6 px-6 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-amber-200/70">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600 text-[20px]">warning</span>
                <span class="font-sans text-xs text-slate-800 font-medium">
                    <strong class="font-semibold text-amber-900">{{ $staleCount }} perishable items require attention:</strong> Harvest window is approaching the 48-hour shelf-life limit.
                </span>
            </div>
            <button type="button" onclick="filterByTab('stale')" class="px-4 py-1.5 rounded-xl bg-amber-500 text-slate-950 font-sans text-xs font-bold hover:bg-amber-400 transition-colors shadow-2xs">
                Review Freshness
            </button>
        </div>
        @endif
    </div>

    <!-- Filter, Search & Tabs Strip -->
    <div class="bg-white rounded-2xl shadow-2xs p-4 mb-4 flex flex-col gap-4 border border-slate-200/80">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
                <input type="text" id="productSearchInput" oninput="handleSearch(this.value)" placeholder="Filter by harvest batch, SKU, crop name..." class="w-full h-11 pl-10 pr-4 bg-slate-50/70 hover:bg-white focus:bg-white rounded-xl font-sans text-xs text-slate-900 placeholder:text-slate-400 outline-none border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
            </div>

            <!-- Quick Metrics Spark & Sorter -->
            <div class="flex items-center gap-4 self-end lg:self-auto">
                <div class="hidden xl:flex items-center gap-3 px-3 py-1.5 bg-slate-50 rounded-xl border border-slate-200/70">
                    <span class="font-mono text-[10px] uppercase text-slate-500 font-bold">Catalog Health:</span>
                    <svg class="w-20 h-5 overflow-visible text-emerald-600" fill="none" viewBox="0 0 60 16">
                        <path d="M0 12 L10 10 L20 14 L30 6 L40 9 L50 2 L60 4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                    <span class="font-mono text-xs font-bold text-emerald-700">98.4%</span>
                </div>

                <!-- Category Filter -->
                <div class="flex items-center gap-2">
                    <span class="font-sans text-xs text-slate-500 hidden sm:inline">Category:</span>
                    <select id="categoryFilter" onchange="handleCategoryFilter(this.value)" class="h-11 px-3 bg-slate-50 hover:bg-white focus:bg-white rounded-xl font-sans text-xs text-slate-800 outline-none cursor-pointer border border-slate-300 focus:border-amber-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <span class="font-sans text-xs text-slate-500 hidden sm:inline">Sort:</span>
                    <select id="sortDropdown" onchange="handleSort(this.value)" class="h-11 px-3 bg-slate-50 hover:bg-white focus:bg-white rounded-xl font-sans text-xs text-slate-800 outline-none cursor-pointer border border-slate-300 focus:border-amber-500">
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
            <button type="button" class="tab-pill active-tab px-3.5 py-1.5 rounded-xl font-sans text-xs font-bold bg-slate-900 text-white transition-all shadow-2xs" data-tab="all" onclick="filterByTab('all')">
                All <span class="ml-1 font-mono text-[11px] opacity-80">{{ $totalCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-xl font-sans text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all" data-tab="active" onclick="filterByTab('active')">
                Active <span class="ml-1 font-mono text-[11px]">{{ $activeCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-xl font-sans text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all" data-tab="draft" onclick="filterByTab('draft')">
                Drafts <span class="ml-1 font-mono text-[11px]">{{ $draftCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-xl font-sans text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all" data-tab="low" onclick="filterByTab('low')">
                Low Stock <span class="ml-1 px-1.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-md font-mono text-[10px] font-bold">{{ $lowStockCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-xl font-sans text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all" data-tab="out" onclick="filterByTab('out')">
                Out of Stock <span class="ml-1 font-mono text-[11px]">{{ $outOfStockCount }}</span>
            </button>
            <button type="button" class="tab-pill px-3.5 py-1.5 rounded-xl font-sans text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all" data-tab="stale" onclick="filterByTab('stale')">
                Stale / Attention <span class="ml-1 px-1.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-md font-mono text-[10px] font-bold">{{ $staleCount }}</span>
            </button>
        </div>
    </div>

    <!-- Products Master Table Container -->
    <div class="bg-white rounded-2xl shadow-2xs overflow-hidden border border-slate-200/80">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 text-slate-500 font-mono text-[10px] uppercase tracking-wider border-b border-slate-200/80">
                        <th class="py-3.5 pl-6 pr-3 w-10">
                            <input type="checkbox" onclick="toggleSelectAll(this)" class="w-4 h-4 rounded text-amber-500 focus:ring-0 cursor-pointer accent-amber-500">
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
                <tbody class="divide-y divide-slate-100 font-sans" id="productTableBody">
                    @forelse($products as $product)
                    @php
                        $isLow = $product->isLowStock();
                        $isOutOfStock = $product->stock <= 0;
                        $isStale = $product->isStale();
                        $statusTags = ($product->status ?? 'active') . ($isLow ? ' low' : '') . ($isOutOfStock ? ' out' : '') . ($isStale ? ' stale' : '');
                        $imgUrl = $product->main_image_url ?? asset('images/product-placeholder.svg');
                    @endphp
                    <tr class="product-row hover:bg-slate-50/70 transition-colors" data-category="{{ $product->category?->name ?? 'General' }}" data-name="{{ $product->name }}" data-status="{{ $statusTags }}">
                        <td class="py-4 pl-6 pr-3 align-middle">
                            <input type="checkbox" form="bulkActionForm" name="product_ids[]" value="{{ $product->id }}" class="product-check w-4 h-4 rounded text-amber-500 focus:ring-0 cursor-pointer accent-amber-500">
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-3">
                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover shrink-0 shadow-2xs border border-slate-200/80">
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('seller.products.edit', $product->id) }}" class="font-heading text-sm font-bold text-slate-900 truncate hover:text-amber-600 transition-colors">
                                            {{ $product->name }}
                                        </a>
                                        @if($product->isOrganicCategory() && $product->is_perishable)
                                        <span class="material-symbols-outlined text-[14px] text-emerald-600" title="Pesticide-free / Fresh harvest">verified</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="font-mono text-[11px] text-slate-400">{{ $product->sku ?? ('SKU-' . $product->id) }}</span>
                                        <span class="font-mono text-[10px] uppercase bg-slate-100 px-1.5 py-0.5 rounded-md text-slate-600 font-medium border border-slate-200/60">
                                            {{ $product->isOrganicCategory() ? ($product->farm_origin ?? ($sellerProfile?->shop_name ?? 'Farm Origin')) : ($sellerProfile?->shop_name ?? 'Retail Catalog') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 align-middle">
                            @if($product->category)
                                <span class="inline-flex items-center gap-1 font-sans text-xs {{ $product->isOrganicCategory() ? 'text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/80' : 'text-slate-600' }}">
                                    @if($product->isOrganicCategory())
                                        <span class="material-symbols-outlined text-[13px] text-emerald-600">eco</span>
                                    @endif
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="font-sans text-xs text-slate-500">General</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="font-mono text-sm font-bold text-slate-900">
                                ₹{{ number_format($product->price, 0) }}
                                <span class="font-sans text-xs font-normal text-slate-400">/ {{ $product->unit_type ?? 'kg' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm font-semibold text-slate-800">{{ $product->stock }} {{ $product->unit_type ?? 'kg' }}</span>
                                    @if($isOutOfStock)
                                    <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 font-mono text-[10px] uppercase font-bold">Depleted</span>
                                    @elseif($isLow)
                                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 font-mono text-[10px] uppercase font-bold">Low Stock</span>
                                    @else
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[10px] uppercase font-bold">In Stock</span>
                                    @endif
                                </div>
                                <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="{{ $isOutOfStock ? 'bg-rose-500 w-0' : ($isLow ? 'bg-amber-500 w-1/4' : 'bg-emerald-500 w-3/4') }} h-full rounded-full"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 align-middle">
                            <div class="flex flex-col">
                                <span class="font-mono text-xs font-semibold text-slate-800">{{ $product->total_reviews ?? 0 }} sold</span>
                                <span class="font-mono text-[10px] text-emerald-700 flex items-center">
                                    <span class="material-symbols-outlined text-[12px]">trending_up</span> Active velocity
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-4 align-middle">
                            @if($product->isOrganicCategory() && $product->is_perishable && $product->expiry_date)
                            <div class="flex flex-col gap-1">
                                @if($isStale)
                                <div class="flex items-center gap-1.5 text-rose-600 font-medium font-sans text-xs">
                                    <span class="material-symbols-outlined text-[16px]">timer_off</span>
                                    <span>Expired / Stale</span>
                                </div>
                                @else
                                <div class="flex items-center gap-1.5 text-slate-700 font-medium font-sans text-xs">
                                    <span class="material-symbols-outlined text-[16px] text-amber-600">timer</span>
                                    <span>{{ max(0, (int) round(now()->floatDiffInDays($product->expiry_date, false))) }} days remaining</span>
                                </div>
                                @endif
                                <span class="font-mono text-[10px] text-slate-400">
                                    Harvested: {{ $product->harvest_date ? $product->harvest_date->format('d M') : 'Recent' }} • SLA Active
                                </span>
                            </div>
                            @elseif($product->isOrganicCategory())
                            <div class="flex flex-col gap-1">
                                <span class="font-sans text-xs text-emerald-700 font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">eco</span> Stable Shelf-Life
                                </span>
                                <span class="font-mono text-[10px] text-slate-400">Non-perishable organic</span>
                            </div>
                            @else
                            <div class="flex flex-col gap-0.5">
                                <span class="font-sans text-xs text-slate-500 font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">inventory_2</span> Standard Retail
                                </span>
                                <span class="font-mono text-[10px] text-slate-400">SLA N/A</span>
                            </div>
                            @endif
                        </td>
                        <td class="py-4 pr-6 pl-4 align-middle text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-900 transition-colors" onclick="openDrawer('{{ addslashes($product->name) }}', '{{ $product->sku ?? 'SKU-'.$product->id }}', '₹{{ number_format($product->price, 0) }} / {{ $product->unit_type }}', '{{ $product->stock }} {{ $product->unit_type }}', '{{ $product->total_reviews ?? 0 }} units', '{{ $product->isOrganicCategory() ? ($product->harvest_date?->format('d M') ?? 'Recent') : 'N/A' }}', '{{ $product->isOrganicCategory() ? ($isStale ? 'Expired' : 'Optimal') : 'Standard' }}', '{{ addslashes($product->farm_origin ?? 'Origin Farm') }}', '{{ addslashes($product->category?->name ?? 'General') }}')" title="Inspect SKU">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                                <a href="{{ route('seller.products.edit', $product->id) }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-900 transition-colors" title="Edit Item">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <button type="button" class="p-2 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition-colors" onclick="openDeleteModal('{{ addslashes($product->name) }}', '{{ route('seller.products.destroy', $product->id) }}')" title="Delete Product">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <span class="material-symbols-outlined text-4xl text-slate-300">inventory_2</span>
                                <p class="font-heading font-semibold text-lg text-slate-900">No products listed yet</p>
                                <p class="text-xs text-slate-500 max-w-sm">Publish your first crop harvest or artisanal item to start receiving orders.</p>
                                <a href="{{ route('seller.products.create') }}" class="mt-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-heading font-bold text-xs shadow-xs hover:from-amber-400">
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
        <div class="p-4 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 font-sans text-xs text-slate-500 border-t border-slate-200/80">
            <div class="flex items-center gap-2">
                <span>Showing <strong class="text-slate-900">{{ $products->count() }}</strong> of <strong class="text-slate-900">{{ $totalCount }}</strong> products</span>
                <span class="text-slate-300">•</span>
                <span class="font-mono text-[11px]">Selected: <span class="font-bold text-slate-900" id="selectedCounter">0</span> items</span>
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
<div class="hidden fixed inset-0 z-50 overflow-hidden bg-slate-900/60 backdrop-blur-xs transition-opacity" id="productDrawer">
    <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
        <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
            <!-- Drawer Header -->
            <div class="p-6 bg-slate-50 flex items-center justify-between border-b border-slate-200/80">
                <div class="flex flex-col">
                    <span class="font-mono text-[10px] text-amber-700 uppercase font-bold tracking-wider">SKU Inspection</span>
                    <h2 class="font-heading text-lg font-bold text-slate-900 truncate max-w-xs" id="drawerTitle">Product Details</h2>
                </div>
                <button type="button" class="p-2 rounded-xl hover:bg-slate-200/70 text-slate-500 hover:text-slate-900" onclick="closeDrawer()">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Drawer Body Content -->
            <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">
                <!-- Quick Status Chip -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] text-slate-400 uppercase">Current Status</span>
                        <span class="font-sans text-sm font-semibold text-slate-800" id="drawerFreshnessStatus">Checking SLA...</span>
                    </div>
                    <span class="font-mono text-xs bg-slate-200/80 px-2 py-1 rounded-md text-slate-800 font-bold" id="drawerSku">SKU-000</span>
                </div>

                <!-- Price & Stock Bento -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-4 rounded-xl bg-white shadow-2xs flex flex-col border border-slate-200/80">
                        <span class="font-mono text-[10px] text-slate-400 uppercase">Listing Price</span>
                        <span class="font-heading text-lg font-bold text-slate-900 mt-1" id="drawerPrice">₹0.00</span>
                        <span class="font-mono text-[10px] text-emerald-700 mt-0.5">Platform Commission: {{ number_format($sellerProfile ? $sellerProfile->getEffectiveCommissionRate() : \App\Models\SiteSetting::getCommissionRate(), 1) }}%</span>
                    </div>
                    <div class="p-4 rounded-xl bg-white shadow-2xs flex flex-col border border-slate-200/80">
                        <span class="font-mono text-[10px] text-slate-400 uppercase">Stock Available</span>
                        <span class="font-heading text-lg font-bold text-slate-900 mt-1" id="drawerStock">0 units</span>
                        <span class="font-mono text-[10px] text-slate-500 mt-0.5">Depot: Hub-Central</span>
                    </div>
                </div>

                <!-- Harvest & Freshness Insights -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col gap-3">
                    <h4 class="font-heading text-sm font-bold text-slate-900">Freshness SLA Audit</h4>
                    <div class="flex items-center justify-between font-sans text-xs">
                        <span class="text-slate-500">Farm Harvest Date:</span>
                        <span class="font-mono font-medium text-slate-900" id="drawerHarvest">-</span>
                    </div>
                    <div class="flex items-center justify-between font-sans text-xs">
                        <span class="text-slate-500">Farm Origin:</span>
                        <span class="font-sans font-medium text-slate-900" id="drawerOrigin">-</span>
                    </div>
                    <div class="flex items-center justify-between font-sans text-xs">
                        <span class="text-slate-500">Sales Velocity:</span>
                        <span class="font-mono font-medium text-slate-900" id="drawerSales">-</span>
                    </div>
                    <div class="flex items-center justify-between font-sans text-xs">
                        <span class="text-slate-500">Category:</span>
                        <span class="font-sans font-medium text-slate-900" id="drawerCategory">-</span>
                    </div>
                </div>

                <!-- Shelf Life Degradation Visual -->
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[10px] text-slate-400 uppercase font-bold">Degradation Curve</span>
                        <span class="font-mono text-[11px] text-slate-800 font-medium">Auto-Delist @ 0%</span>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex">
                        <div class="bg-emerald-500 h-full w-[40%]" title="Prime Quality"></div>
                        <div class="bg-amber-500 h-full w-[35%]" title="Acceptable Quality"></div>
                        <div class="bg-rose-500 h-full w-[25%]" title="Critical Window"></div>
                    </div>
                    <div class="flex justify-between font-mono text-[10px] text-slate-400">
                        <span>Prime (0-48h)</span>
                        <span>Discount (48-72h)</span>
                        <span>Expired (&gt;72h)</span>
                    </div>
                </div>

                <!-- Threshold Quick Adjust -->
                <div class="p-4 rounded-xl bg-white shadow-2xs flex flex-col gap-3 border border-slate-200/80">
                    <span class="font-heading text-sm font-bold text-slate-900">Update Restock Threshold</span>
                    <div class="flex items-center gap-3">
                        <input type="number" id="quickThresholdInput" value="10" class="w-24 h-10 px-3 bg-slate-50 rounded-xl font-mono text-xs text-slate-900 border border-slate-300 outline-none">
                        <button type="button" onclick="showToast('Threshold updated successfully', 'success')" class="h-10 px-4 rounded-xl bg-slate-900 text-white font-sans text-xs font-semibold hover:bg-slate-800 transition-colors shadow-2xs">
                            Save Limit
                        </button>
                    </div>
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="p-4 bg-slate-50 flex items-center justify-between gap-3 border-t border-slate-200/80">
                <button type="button" onclick="closeDrawer()" class="h-11 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-sans text-xs font-medium">
                    Dismiss
                </button>
                <button type="button" onclick="showToast('Listing synced to mobile catalog', 'success'); closeDrawer();" class="h-11 px-5 rounded-xl bg-amber-500 text-slate-950 font-heading text-xs font-bold hover:bg-amber-400">
                    Sync Listing
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Product Confirmation Modal -->
<div class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" id="deleteModal">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 flex flex-col gap-4 border border-slate-200 transform scale-100 transition-transform">
        <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[26px]">delete_forever</span>
        </div>
        <div class="flex flex-col gap-1">
            <h3 class="font-heading text-lg font-bold text-slate-900">Delete this product?</h3>
            <p class="font-sans text-xs text-slate-500 leading-relaxed">
                Are you sure you want to remove <span class="font-semibold text-slate-900" id="deleteItemTitle"></span>? This action cannot be undone and will cancel active customer cart reservations.
            </p>
        </div>
        <form id="deleteProductForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeDeleteModal()" class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-sans text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" class="h-10 px-4 rounded-xl bg-rose-600 text-white hover:bg-rose-700 font-heading text-xs font-bold transition-colors shadow-2xs">
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
                p.classList.remove('bg-slate-100', 'text-slate-700');
                p.classList.add('bg-slate-900', 'text-white', 'font-bold');
            } else {
                p.classList.add('bg-slate-100', 'text-slate-700');
                p.classList.remove('bg-slate-900', 'text-white', 'font-bold');
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

    function handleCategoryFilter(catId) {
        const url = new URL(window.location.href);
        if (catId) {
            url.searchParams.set('category_id', catId);
        } else {
            url.searchParams.delete('category_id');
        }
        url.searchParams.delete('page');
        window.location.href = url.toString();
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

    function submitBulkAction(actionName) {
        const checked = document.querySelectorAll('.product-check:checked');
        if (checked.length === 0) {
            showToast('Please select at least one product.', 'warning');
            return;
        }

        if (actionName === 'delete') {
            if (!confirm(`Are you sure you want to delete ${checked.length} selected product(s)? This action cannot be undone.`)) {
                return;
            }
        }

        const actionInput = document.getElementById('bulkActionInput');
        const form = document.getElementById('bulkActionForm');
        if (actionInput && form) {
            actionInput.value = actionName;
            form.submit();
        }
    }
</script>
@endpush
@endsection
