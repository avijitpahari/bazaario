@extends('layouts.admin')

@section('title', 'Product Catalog & Inventory Management')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col">
            <div class="flex items-center gap-space-sm">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Products & Catalog</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-label-sm text-[11px] font-bold border border-amber-200/60">Inventory Ops</span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500 mt-1">
                Oversee catalog moderation, inventory levels, merchant SKUs, and auction lots across all categories.
            </p>
        </div>
        <!-- Top Action Buttons -->
        <div class="flex items-center flex-wrap gap-2">
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 text-[#0F172A] font-body-md text-xs font-semibold shadow-2xs hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined text-[18px]">category</span>
                <span>Manage Categories</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 text-[#0F172A] font-body-md text-xs font-semibold shadow-2xs hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Export Manifest</span>
            </button>
            <a href="{{ url('/products') }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-[#F5A623] text-[#0F172A] font-body-md text-xs font-bold shadow-2xs hover:bg-amber-400 transition-all">
                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                <span>Browse Marketplace</span>
            </a>
        </div>
    </div>

    <!-- Metrics Bar: 4 Primary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <!-- Card 1 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Catalog SKUs</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-[#0F172A]">{{ number_format($stats['total'] ?? 121) }}</span>
                <span class="font-label-sm text-xs text-emerald-700 font-semibold flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +8.4%
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Verified Active Listings</span>
                <span class="font-semibold text-[#0F172A]">{{ $stats['active'] ?? 115 }} active</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Live Auction Lots</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">gavel</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-[#0F172A]">{{ number_format($stats['auctions'] ?? 24) }}</span>
                <span class="font-label-sm text-xs text-amber-600 font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> Live Bidding
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Anti-sniping Protection</span>
                <span class="font-semibold text-emerald-600">Enabled</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Low Stock Trigger (<5)</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">warning</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-rose-600">{{ number_format($stats['low_stock'] ?? 8) }}</span>
                <span class="font-label-sm text-xs text-rose-600 font-medium">Re-order Alert</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Auto-seller Ping</span>
                <span class="font-semibold text-[#0F172A]">Active</span>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Active Categories</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">folder_special</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-[#0F172A]">{{ count($categories) }}</span>
                <span class="font-label-sm text-xs text-indigo-600 font-medium">100% mapped</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Commission Baseline</span>
                <span class="font-semibold text-[#0F172A]">5.5% — 12%</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3">
            <!-- Search Text -->
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by SKU, product title, keywords, or merchant..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] placeholder-slate-400 focus:outline-none focus:border-[#F5A623] focus:bg-white transition-all font-body-md"
                />
            </div>

            <!-- Category Filter -->
            <div class="w-full md:w-52">
                <select name="category_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-[#F5A623] focus:bg-white transition-all font-body-md cursor-pointer">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sale Type Filter -->
            <div class="w-full md:w-40">
                <select name="sale_type" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-[#F5A623] focus:bg-white transition-all font-body-md cursor-pointer">
                    <option value="">All Sale Types</option>
                    <option value="direct" {{ request('sale_type') == 'direct' ? 'selected' : '' }}>Direct Purchase</option>
                    <option value="auction" {{ request('sale_type') == 'auction' ? 'selected' : '' }}>Auction Lot</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-36">
                <select name="status" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-[#F5A623] focus:bg-white transition-all font-body-md cursor-pointer">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-[#0F172A] text-white hover:bg-slate-800 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Apply Filters</span>
                </button>
                @if(request()->anyFilled(['search', 'category_id', 'sale_type', 'status']))
                    <a href="{{ route('admin.products.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-medium flex items-center justify-center transition-all" title="Reset Filters">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Table Card -->
    <div class="rounded-xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="font-headline-sm text-sm font-bold text-[#0F172A]">Marketplace Catalog Directory</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-label-sm text-[11px] font-semibold">
                    {{ $products->total() }} SKUs recorded
                </span>
            </div>
            <div class="text-xs text-slate-500 font-body-sm">
                Showing page {{ $products->currentPage() }} of {{ $products->lastPage() }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#0F172A]">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-label-md">
                    <tr>
                        <th class="py-3 px-4">Product / SKU</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Merchant</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4 text-right">Price</th>
                        <th class="py-3 px-4 text-center">Stock</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Product title and SKU -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-700 font-bold shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">package_2</span>
                                    </div>
                                    <div class="flex flex-col min-w-0 max-w-xs">
                                        <a href="{{ url('/products/' . ($product->slug ?? $product->id)) }}" target="_blank" class="font-semibold text-[#0F172A] hover:text-[#835500] hover:underline truncate">
                                            {{ $product->name }}
                                        </a>
                                        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-slate-400 font-label-sm">
                                            <span>SKU: {{ $product->sku ?? ('BZ-PRD-' . str_pad($product->id, 5, '0', STR_PAD_LEFT)) }}</span>
                                            <span>•</span>
                                            <span>{{ $product->created_at ? $product->created_at->format('M d, Y') : 'Recent' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3.5 px-4 font-body-md text-slate-600">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200/50">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>

                            <!-- Seller -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-medium text-[#0F172A]">{{ $product->seller->name ?? 'Direct Merchant' }}</span>
                                    <span class="material-symbols-outlined text-blue-500 text-[14px]" title="KYC Verified Seller">verified</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-label-sm">
                                    ID: {{ $product->seller_id ? 'SLR-' . str_pad($product->seller_id, 4, '0', STR_PAD_LEFT) : 'SYS' }}
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="py-3.5 px-4">
                                @if(($product->sale_type ?? 'direct') === 'auction')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-label-sm text-[10px] font-bold">
                                        <span class="material-symbols-outlined text-[12px]">gavel</span>
                                        <span>Auction Lot</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-800 border border-blue-200 font-label-sm text-[10px] font-bold">
                                        <span class="material-symbols-outlined text-[12px]">shopping_cart</span>
                                        <span>Buy Now</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Price -->
                            <td class="py-3.5 px-4 text-right font-label-md font-bold text-[#0F172A] text-sm">
                                <button type="button" 
                                        onclick="openQuickStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ (float)($product->price ?? 0) }}, {{ (int)($product->stock ?? 0) }})"
                                        class="hover:text-amber-600 hover:underline cursor-pointer inline-flex items-center gap-1 justify-end"
                                        title="Click to edit price and stock">
                                    <span>₹{{ number_format((float)($product->price ?? 0), 2) }}</span>
                                </button>
                            </td>

                            <!-- Stock -->
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" 
                                        onclick="openQuickStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ (float)($product->price ?? 0) }}, {{ (int)($product->stock ?? 0) }})"
                                        class="cursor-pointer hover:opacity-80 transition-opacity"
                                        title="Click to edit stock quantity">
                                    @if(($product->stock ?? 0) <= 0)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-label-sm text-[10px] font-bold">
                                            Out of Stock
                                        </span>
                                    @elseif(($product->stock ?? 0) <= 4)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 font-label-sm text-[10px] font-bold flex items-center justify-center gap-1 w-fit mx-auto">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            {{ $product->stock }} left
                                        </span>
                                    @else
                                        <span class="font-label-sm font-semibold text-slate-700">
                                            {{ $product->stock }} units
                                        </span>
                                    @endif
                                </button>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center">
                                @if(($product->status ?? 'active') === 'active')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-[10px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @elseif(($product->status ?? '') === 'inactive')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-label-sm text-[10px] font-bold">
                                        Inactive
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-label-sm text-[10px] font-bold">
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" 
                                            onclick="openQuickStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ (float)($product->price ?? 0) }}, {{ (int)($product->stock ?? 0) }})" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer" 
                                            title="Quick Edit Stock & Price">
                                        <span class="material-symbols-outlined text-[18px]">tune</span>
                                    </button>
                                    <a href="{{ url('/products/' . ($product->slug ?? $product->id)) }}" target="_blank" class="p-1.5 rounded-lg text-slate-500 hover:text-[#0F172A] hover:bg-slate-100 transition-colors" title="View Listing Live">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.toggle-status', $product->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer" title="{{ $product->status === 'active' ? 'Deactivate SKU' : 'Activate SKU' }}">
                                            <span class="material-symbols-outlined text-[18px]">{{ $product->status === 'active' ? 'pause_circle' : 'play_circle' }}</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Remove product SKU &quot;{{ addslashes($product->name) }}&quot; from catalog?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors cursor-pointer" title="Delete Product">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300">search_off</span>
                                <p class="mt-2 font-headline-sm text-sm font-semibold text-slate-600">No products matching the selected criteria</p>
                                <p class="text-xs text-slate-400 mt-1">Try resetting your search query or category filters.</p>
                                <a href="{{ route('admin.products.index') }}" class="mt-3 inline-block px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium">
                                    Reset Filters
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs text-slate-500 font-label-sm">
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                </div>
                <div class="flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($products->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 text-xs cursor-not-allowed font-medium">Previous</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-medium transition-all">Previous</a>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-medium transition-all">Next</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 text-xs cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Interactive Quick Stock & Price Modal -->
    <div id="quickStockModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-fadeIn">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">tune</span>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-headline-sm text-sm font-bold text-[#0F172A] truncate">Update Stock & Price</h3>
                        <p id="quickStockProductName" class="text-[11px] text-slate-500 font-body-sm truncate"></p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickStockModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
            <form id="quickStockForm" method="POST" action="" class="p-5 flex flex-col gap-4">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="quick_stock_input" class="block font-headline-sm text-xs font-bold text-slate-700 mb-1">
                            Available Stock *
                        </label>
                        <input 
                            type="number" 
                            id="quick_stock_input" 
                            name="stock" 
                            required 
                            min="0" 
                            max="1000000" 
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-label-md font-bold text-[#0F172A] focus:outline-none focus:border-amber-500 focus:bg-white transition-all" />
                        <p class="text-[10px] text-slate-400 mt-1 font-body-sm">Inventory units</p>
                    </div>

                    <div>
                        <label for="quick_price_input" class="block font-headline-sm text-xs font-bold text-slate-700 mb-1">
                            Unit Price (₹) *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">₹</span>
                            <input 
                                type="number" 
                                step="0.01" 
                                id="quick_price_input" 
                                name="price" 
                                required 
                                min="0" 
                                max="10000000" 
                                class="w-full pl-7 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-label-md font-bold text-[#0F172A] focus:outline-none focus:border-amber-500 focus:bg-white transition-all" />
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 font-body-sm">Catalog price</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeQuickStockModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer flex items-center gap-1.5 shadow-2xs">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openQuickStockModal(id, name, price, stock) {
            const form = document.getElementById('quickStockForm');
            form.action = '{{ url("admin/products") }}/' + id + '/update-stock';
            document.getElementById('quickStockProductName').textContent = name;
            document.getElementById('quick_price_input').value = price;
            document.getElementById('quick_stock_input').value = stock;
            document.getElementById('quickStockModal').classList.remove('hidden');
        }

        function closeQuickStockModal() {
            document.getElementById('quickStockModal').classList.add('hidden');
        }
    </script>
</div>
@endsection
