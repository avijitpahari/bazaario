@extends('layouts.seller')

@section('title', ($product->name ?? 'Product Details') . ' — Bazaario Seller Center')

@section('content')
@php
    $sellerUser = Auth::guard('seller')->user() ?? Auth::user();
    $sellerProfile = $sellerUser?->sellerProfile;
@endphp

<div class="flex flex-col w-full pb-16">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-2">
                <a href="{{ route('seller.products.index') }}" class="font-mono text-[11px] text-secondary uppercase tracking-widest bg-secondary-container/20 px-2 py-0.5 rounded-[6px] font-bold hover:underline">
                    &larr; Back to Catalog
                </a>
                <span class="text-on-surface-variant text-[12px]">•</span>
                <span class="font-mono text-xs text-on-surface-variant">SKU: {{ $product->sku ?? ('BZ-' . $product->id) }}</span>
            </div>
            <h1 class="font-heading text-3xl sm:text-4xl text-on-surface tracking-tight font-bold">{{ $product->name }}</h1>
            <p class="font-sans text-sm text-on-surface-variant">
                Category: <span class="font-semibold text-on-surface">{{ $product->category?->name ?? 'General' }}</span> | Unit: <span class="font-mono text-secondary font-bold uppercase">{{ $product->unit_type }}</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('seller.products.edit', $product->id) }}" class="h-11 px-5 rounded-[14px] bg-secondary text-white font-sans text-sm font-semibold flex items-center gap-2 shadow-sm hover:bg-secondary/90 transition-all">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                <span>Edit Product</span>
            </a>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-lowest rounded-[20px] p-6 shadow-sm border border-surface-container-high/60">
                <h2 class="font-heading text-lg font-bold text-on-surface mb-4">Product Overview</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-surface-container-low p-4 rounded-xl">
                        <span class="font-mono text-[10px] uppercase text-on-surface-variant">Price</span>
                        <div class="font-heading text-xl font-bold text-on-surface mt-1">₹{{ number_format((float)($product->price ?? 0), 2) }}</div>
                        <span class="font-mono text-[10px] text-on-surface-variant">per {{ $product->unit_type }}</span>
                    </div>
                    <div class="bg-surface-container-low p-4 rounded-xl">
                        <span class="font-mono text-[10px] uppercase text-on-surface-variant">Stock Level</span>
                        <div class="font-heading text-xl font-bold {{ $product->stock <= 0 ? 'text-error' : ($product->stock <= ($product->low_stock_threshold ?? 10) ? 'text-secondary' : 'text-on-surface') }} mt-1">
                            {{ $product->stock }} {{ $product->unit_type }}
                        </div>
                        <span class="font-mono text-[10px] text-on-surface-variant">Safety: Min {{ $product->low_stock_threshold ?? 10 }}</span>
                    </div>
                    <div class="bg-surface-container-low p-4 rounded-xl">
                        <span class="font-mono text-[10px] uppercase text-on-surface-variant">Status</span>
                        <div class="mt-1">
                            <span class="px-2 py-0.5 rounded-full text-xs font-mono font-bold uppercase {{ $product->status === 'active' ? 'bg-tertiary-fixed text-on-tertiary-container' : 'bg-surface-container text-on-surface-variant' }}">
                                {{ $product->status }}
                            </span>
                        </div>
                    </div>
                    <div class="bg-surface-container-low p-4 rounded-xl">
                        <span class="font-mono text-[10px] uppercase text-on-surface-variant">Perishable</span>
                        <div class="font-heading text-sm font-bold text-on-surface mt-1">
                            {{ $product->is_perishable ? 'Yes' : 'No' }}
                        </div>
                        @if($product->expiry_date)
                        <span class="font-mono text-[10px] text-secondary">Exp: {{ \Carbon\Carbon::parse($product->expiry_date)->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>

                @if($product->short_description)
                <div class="mb-4">
                    <h3 class="font-heading text-sm font-bold text-on-surface mb-1">Short Description</h3>
                    <p class="font-sans text-sm text-on-surface-variant">{{ $product->short_description }}</p>
                </div>
                @endif

                @if($product->description)
                <div>
                    <h3 class="font-heading text-sm font-bold text-on-surface mb-1">Full Description</h3>
                    <div class="font-sans text-sm text-on-surface-variant leading-relaxed">{{ $product->description }}</div>
                </div>
                @endif
            </div>

            <!-- Agronomic Details -->
            <div class="bg-surface-container-lowest rounded-[20px] p-6 shadow-sm border border-surface-container-high/60">
                <h2 class="font-heading text-lg font-bold text-on-surface mb-4">Agronomic & Freshness Details</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="font-mono text-xs text-on-surface-variant uppercase">Farm Origin</span>
                        <p class="font-sans text-sm font-semibold text-on-surface mt-0.5">{{ $product->farm_origin ?: 'Local Regional Farm' }}</p>
                    </div>
                    <div>
                        <span class="font-mono text-xs text-on-surface-variant uppercase">Harvest Date</span>
                        <p class="font-sans text-sm font-semibold text-on-surface mt-0.5">{{ $product->harvest_date ? \Carbon\Carbon::parse($product->harvest_date)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="font-mono text-xs text-on-surface-variant uppercase">Shelf Life Window</span>
                        <p class="font-sans text-sm font-semibold text-on-surface mt-0.5">{{ $product->expiry_days ? $product->expiry_days . ' Days' : 'Indefinite' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Media -->
        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-[20px] p-6 shadow-sm border border-surface-container-high/60">
                <h2 class="font-heading text-lg font-bold text-on-surface mb-4">Product Images</h2>
                @if($product->images && $product->images->count() > 0)
                <div class="grid grid-cols-2 gap-3">
                    @foreach($product->images as $img)
                    <div class="relative rounded-xl overflow-hidden aspect-square border border-surface-container">
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @if($img->is_primary)
                        <span class="absolute top-2 left-2 bg-secondary text-white font-mono text-[9px] uppercase font-bold px-2 py-0.5 rounded-md">Primary</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="aspect-video rounded-xl bg-surface-container-low flex flex-col items-center justify-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl mb-1">image_not_supported</span>
                    <span class="font-sans text-xs">No media uploaded</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
