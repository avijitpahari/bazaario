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
                <a href="{{ route('seller.products.index') }}" class="group inline-flex items-center gap-1.5 font-mono text-[11px] text-amber-900 uppercase tracking-widest bg-amber-50 hover:bg-amber-100/90 border border-amber-200/70 px-2.5 py-1 rounded-lg font-bold transition-all shadow-2xs hover:shadow-xs">
                    <span class="material-symbols-outlined text-[13px] text-amber-700 transition-transform duration-200 ease-out group-hover:-translate-x-0.5">arrow_back</span>
                    <span>Back to Catalog</span>
                </a>
                <span class="text-slate-300 text-[12px]">•</span>
                <span class="font-mono text-xs text-slate-500">SKU: {{ $product->sku ?? ('BZ-' . $product->id) }}</span>
            </div>
            <h1 class="font-heading text-3xl sm:text-4xl text-slate-900 tracking-tight font-bold">{{ $product->name }}</h1>
            <p class="font-sans text-sm text-slate-500">
                Category: <span class="font-semibold text-slate-900">{{ $product->category?->name ?? 'General' }}</span> | Unit: <span class="font-mono text-amber-700 font-bold uppercase">{{ $product->unit_type }}</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('seller.products.edit', $product->id) }}" class="h-11 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold flex items-center gap-2 shadow-xs transition-all">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                <span>Edit Product</span>
            </a>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80">
                <h2 class="font-heading text-lg font-bold text-slate-900 mb-4">Product Overview</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-slate-50 border border-slate-200/80 p-4 rounded-xl shadow-2xs">
                        <span class="font-mono text-[10px] uppercase text-slate-500 font-semibold">Price</span>
                        <div class="font-heading text-xl font-bold text-slate-900 mt-1">₹{{ number_format((float)($product->price ?? 0), 2) }}</div>
                        <span class="font-mono text-[10px] text-slate-500">per {{ $product->unit_type }}</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 p-4 rounded-xl shadow-2xs">
                        <span class="font-mono text-[10px] uppercase text-slate-500 font-semibold">Stock Level</span>
                        <div class="font-heading text-xl font-bold {{ $product->stock <= 0 ? 'text-rose-600' : ($product->stock <= ($product->low_stock_threshold ?? 10) ? 'text-amber-600' : 'text-slate-900') }} mt-1">
                            {{ $product->stock }} {{ $product->unit_type }}
                        </div>
                        <span class="font-mono text-[10px] text-slate-500">Safety: Min {{ $product->low_stock_threshold ?? 10 }}</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 p-4 rounded-xl shadow-2xs">
                        <span class="font-mono text-[10px] uppercase text-slate-500 font-semibold">Status</span>
                        <div class="mt-1">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase {{ $product->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $product->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $product->status }}
                            </span>
                        </div>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 p-4 rounded-xl shadow-2xs">
                        <span class="font-mono text-[10px] uppercase text-slate-500 font-semibold">Perishable</span>
                        <div class="font-heading text-sm font-bold text-slate-900 mt-1">
                            {{ $product->is_perishable ? 'Yes' : 'No' }}
                        </div>
                        @if($product->expiry_date)
                        <span class="font-mono text-[10px] text-amber-700 font-medium">Exp: {{ \Carbon\Carbon::parse($product->expiry_date)->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>

                @if($product->short_description)
                <div class="mb-4">
                    <h3 class="font-heading text-sm font-bold text-slate-900 mb-1">Short Description</h3>
                    <p class="font-sans text-sm text-slate-600">{{ $product->short_description }}</p>
                </div>
                @endif

                @if($product->description)
                <div>
                    <h3 class="font-heading text-sm font-bold text-slate-900 mb-1">Full Description</h3>
                    <div class="font-sans text-sm text-slate-600 leading-relaxed">{{ $product->description }}</div>
                </div>
                @endif
            </div>

            @if($product->isOrganicCategory())
            <!-- Agronomic Details (Organic Produce Only) -->
            <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80">
                <h2 class="font-heading text-lg font-bold text-slate-900 mb-4">Agronomic &amp; Freshness Details</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="font-mono text-xs text-slate-500 uppercase font-semibold">Farm Origin</span>
                        <p class="font-sans text-sm font-semibold text-slate-900 mt-0.5">{{ $product->farm_origin ?: 'Local Regional Farm' }}</p>
                    </div>
                    <div>
                        <span class="font-mono text-xs text-slate-500 uppercase font-semibold">Harvest Date</span>
                        <p class="font-sans text-sm font-semibold text-slate-900 mt-0.5">{{ $product->harvest_date ? \Carbon\Carbon::parse($product->harvest_date)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="font-mono text-xs text-slate-500 uppercase font-semibold">Shelf Life Window</span>
                        <p class="font-sans text-sm font-semibold text-slate-900 mt-0.5">{{ $product->expiry_days ? $product->expiry_days . ' Days' : 'Indefinite' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar / Media -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80">
                <h2 class="font-heading text-lg font-bold text-slate-900 mb-4">Product Images</h2>
                @if($product->images && $product->images->count() > 0)
                <div class="grid grid-cols-2 gap-3">
                    @foreach($product->images as $img)
                    <div class="relative rounded-xl overflow-hidden aspect-square border border-slate-200 shadow-2xs">
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @if($img->is_primary)
                        <span class="absolute top-2 left-2 bg-slate-950/80 text-amber-400 font-mono text-[9px] uppercase font-bold px-2 py-0.5 rounded-md backdrop-blur-xs">Primary</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="aspect-video rounded-xl bg-slate-50 border border-slate-200 flex flex-col items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-1">image_not_supported</span>
                    <span class="font-sans text-xs">No media uploaded</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
