@extends('layouts.seller')

@section('title', 'Create Wholesale Auction Lot — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;

    // Helper for safe auction thumbnail resolution
    if (!function_exists('sellerCreateThumb')) {
        function sellerCreateThumb($product) {
            if (!$product) return asset('images/product-placeholder.svg');
            $raw = $product->primaryImage?->image_path ?? optional($product->images?->first())->image_path;
            if (!$raw) return asset('images/product-placeholder.svg');
            if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) return $raw;
            $clean = ltrim($raw, '/');
            if (file_exists(public_path($clean))) return asset($clean);
            if (file_exists(public_path('images/' . $clean))) return asset('images/' . $clean);
            if (file_exists(public_path('storage/' . $clean))) return asset('storage/' . $clean);
            return \Illuminate\Support\Facades\Storage::url($raw);
        }
    }

    $productsList = $products ?? collect();
    $productsData = $productsList->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'stock' => (int) $p->stock,
            'unit' => $p->unit_type ?? 'kg',
            'price' => (float) ($p->base_price ?? $p->price ?? 100),
            'category' => $p->category?->name ?? 'Agricultural Lot',
            'description' => \Illuminate\Support\Str::limit($p->description ?? 'Fresh certified farm consignment ready for wholesale spot-trading.', 120),
            'image' => sellerCreateThumb($p),
        ];
    })->values();

    $defaultProductId = old('product_id', $productsList->first()?->id ?? '');
    $defaultStartsAt = old('starts_at', now()->format('Y-m-d\TH:i'));
    $defaultEndsAt = old('ends_at', now()->addDay()->format('Y-m-d\TH:i'));
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    products: {{ Js::from($productsData) }},
    selectedProductId: '{{ $defaultProductId }}',
    startPrice: {{ old('starting_price', 1000) }},
    reservePrice: {{ old('reserve_price', 1500) }},
    minIncrement: {{ old('minimum_increment', 100) }},
    startsAt: '{{ $defaultStartsAt }}',
    endsAt: '{{ $defaultEndsAt }}',
    
    get currentProduct() {
        return this.products.find(p => String(p.id) === String(this.selectedProductId)) || null;
    },

    setStartNow() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        this.startsAt = now.toISOString().slice(0, 16);
        this.setDuration(24);
    },

    setStartTomorrowMorning() {
        const d = new Date();
        d.setDate(d.getDate() + 1);
        d.setHours(9, 0, 0, 0);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        this.startsAt = d.toISOString().slice(0, 16);
        this.setDuration(12);
    },

    setDuration(hours) {
        if (!this.startsAt) return;
        const start = new Date(this.startsAt);
        const end = new Date(start.getTime() + (hours * 3600 * 1000));
        end.setMinutes(end.getMinutes() - end.getTimezoneOffset());
        this.endsAt = end.toISOString().slice(0, 16);
    },

    formatCurrency(val) {
        const num = parseFloat(val) || 0;
        return '₹' + num.toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }
}">

    <!-- BREADCRUMB & CONTEXT STATUS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-3 mb-2">
        <div class="flex items-center gap-2 text-xs font-mono text-slate-500">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-amber-600 transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <a href="{{ route('seller.auctions.index') }}" class="hover:text-amber-600 transition-colors">Auctions</a>
            <span class="material-symbols-outlined text-[14px] text-slate-400">chevron_right</span>
            <span class="text-slate-900 font-bold">Create Wholesale Lot</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 border border-amber-200/70 rounded-xl font-mono text-[11px] uppercase text-amber-800 font-semibold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                SPOT FLOOR LISTING ENGINE
            </span>
            <span class="font-mono text-xs text-slate-600 bg-white border border-slate-200 px-3 py-1 rounded-xl shadow-2xs">
                SELLER ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    <!-- WORKSPACE HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-slate-200/80 mb-6">
        <div class="space-y-1.5 max-w-3xl">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="font-heading text-2xl lg:text-3xl tracking-tight text-slate-900 font-extrabold">
                    Create Wholesale Auction Lot
                </h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-xs font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    APMC Regulated
                </span>
            </div>
            <p class="text-sm text-slate-500 leading-relaxed">
                List a verified consignment of farm produce on the live spot-bidding exchange with transparent reserve floor protections.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.auctions.index') }}" class="h-11 px-4 rounded-xl bg-white border border-slate-200/80 text-slate-700 font-heading text-xs font-semibold shadow-2xs hover:bg-slate-50 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Back to Registry</span>
            </a>
            <a href="{{ route('seller.auctions.live') }}" class="h-11 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-heading text-xs font-bold shadow-xs hover:shadow-md transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-amber-400">stream</span>
                <span>Live Terminal</span>
            </a>
        </div>
    </div>

    @if($productsList->isEmpty())
        <!-- NO PRODUCTS EMPTY STATE -->
        <div class="bg-white rounded-3xl p-10 text-center border border-slate-200/80 shadow-xs max-w-2xl mx-auto flex flex-col items-center">
            <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-200 flex items-center justify-center text-amber-600 mb-4">
                <span class="material-symbols-outlined text-[32px]">inventory_2</span>
            </div>
            <h2 class="font-heading text-xl font-bold text-slate-900">No Farm Products Registered Yet</h2>
            <p class="text-sm text-slate-500 max-w-md mt-1 mb-6">
                Before launching a live wholesale auction, you must have at least one approved agricultural product in your catalog.
            </p>
            <a href="{{ route('seller.products.create') }}" class="h-11 px-6 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold shadow-xs transition-all inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Add Produce to Catalog</span>
            </a>
        </div>
    @else
        <!-- DUAL-PANE LISTING WORKSPACE: 7 COLS FORM / 5 COLS LIVE PREVIEW -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: INTERACTIVE CREATION FORM (7 COLS) -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                <form method="POST" action="{{ route('seller.auctions.store') }}" class="flex flex-col gap-6">
                    @csrf

                    <!-- SECTION 01: Certified Produce Consignment -->
                    <div class="bg-white rounded-3xl p-6 lg:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-5">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-800 font-mono text-xs font-bold flex items-center justify-center border border-amber-500/20">
                                    01
                                </span>
                                <div>
                                    <h3 class="font-heading text-base font-bold text-slate-900">Certified Produce Consignment</h3>
                                    <p class="text-xs text-slate-500">Select authenticated farm inventory for spot allocation</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-mono text-[10px] font-bold uppercase tracking-wider">
                                APMC Grade Lot
                            </span>
                        </div>

                        <!-- Product Selector Dropdown -->
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-heading font-semibold text-slate-700">Choose Produce Catalog SKU</label>
                            <div class="relative">
                                <select name="product_id" 
                                        x-model="selectedProductId" 
                                        class="w-full h-12 pl-4 pr-10 bg-slate-50/80 rounded-2xl text-sm font-semibold text-slate-900 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all appearance-none cursor-pointer" 
                                        required>
                                    <option value="" disabled>Select from your approved products...</option>
                                    @foreach($productsList as $prod)
                                        <option value="{{ $prod->id }}">
                                            {{ $prod->name }} • Available: {{ $prod->stock }} {{ $prod->unit_type ?? 'kg' }} (₹{{ number_format($prod->base_price ?? $prod->price, 2) }}/{{ $prod->unit_type ?? 'kg' }})
                                        </option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[20px]">
                                    expand_more
                                </span>
                            </div>
                            @error('product_id') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Selected Product Summary Card -->
                        <template x-if="currentProduct">
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 via-amber-50/10 to-slate-50 border border-slate-200/80 flex items-center gap-4 transition-all">
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-50 shrink-0 border border-slate-200 shadow-2xs flex items-center justify-center p-1">
                                    <img :src="currentProduct.image" 
                                         :alt="currentProduct.name" 
                                         class="w-full h-full object-contain"
                                         onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-heading font-extrabold text-slate-900 truncate" x-text="currentProduct.name"></span>
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 font-mono text-[10px] text-slate-600 font-semibold truncate" x-text="currentProduct.category"></span>
                                    </div>
                                    <div class="flex items-center gap-3 mt-1 font-mono text-xs">
                                        <span class="inline-flex items-center text-emerald-700 font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                            In Stock: <span class="ml-1" x-text="currentProduct.stock + ' ' + currentProduct.unit"></span>
                                        </span>
                                        <span class="text-slate-400">•</span>
                                        <span class="text-slate-500">Retail Base: <span class="font-bold text-slate-800" x-text="formatCurrency(currentProduct.price)"></span></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- SECTION 02: Pricing Structure & Reserve Floor -->
                    <div class="bg-white rounded-3xl p-6 lg:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-5">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-800 font-mono text-xs font-bold flex items-center justify-center border border-amber-500/20">
                                    02
                                </span>
                                <div>
                                    <h3 class="font-heading text-base font-bold text-slate-900">Pricing Structure &amp; Reserve Floor</h3>
                                    <p class="text-xs text-slate-500">Configure opening floor, step increments, and secret reserve</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 border border-amber-200/60 font-mono text-[10px] font-bold uppercase tracking-wider">
                                Escrow Protected
                            </span>
                        </div>

                        <!-- Pricing Inputs (3 Col) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Starting Floor Price -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700">Starting Floor (₹)</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 font-mono text-sm text-slate-400 font-bold">₹</span>
                                    <input type="number" 
                                           step="0.01" 
                                           min="1"
                                           name="starting_price" 
                                           x-model="startPrice" 
                                           class="w-full h-11 pl-8 pr-3.5 bg-slate-50/80 rounded-xl font-mono text-sm font-bold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all" 
                                           required>
                                </div>
                                <span class="text-[10px] font-mono text-slate-400">Opening bid floor</span>
                                @error('starting_price') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <!-- Secret Reserve Price -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700 flex items-center justify-between">
                                    <span>Secret Reserve (₹)</span>
                                    <span class="font-mono text-[10px] text-amber-600 font-bold">Optional</span>
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 font-mono text-sm text-slate-400 font-bold">₹</span>
                                    <input type="number" 
                                           step="0.01" 
                                           min="1"
                                           name="reserve_price" 
                                           x-model="reservePrice" 
                                           placeholder="e.g. 1500"
                                           class="w-full h-11 pl-8 pr-3.5 bg-slate-50/80 rounded-xl font-mono text-sm font-bold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all">
                                </div>
                                <span class="text-[10px] font-mono text-slate-400">Hidden safety threshold</span>
                                @error('reserve_price') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <!-- Minimum Increment -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700">Min Increment (₹)</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 font-mono text-sm text-slate-400 font-bold">₹</span>
                                    <input type="number" 
                                           step="1" 
                                           min="1"
                                           name="minimum_increment" 
                                           x-model="minIncrement" 
                                           class="w-full h-11 pl-8 pr-3.5 bg-slate-50/80 rounded-xl font-mono text-sm font-bold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all" 
                                           required>
                                </div>
                                <span class="text-[10px] font-mono text-slate-400">Step per bidder tick</span>
                                @error('minimum_increment') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Increment Quick Presets -->
                        <div class="flex items-center gap-2 pt-1 flex-wrap">
                            <span class="text-xs font-mono text-slate-500">Quick Step:</span>
                            <button type="button" @click="minIncrement = 50" class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold transition-all" :class="minIncrement == 50 ? 'bg-amber-500 text-slate-950 shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'">+₹50</button>
                            <button type="button" @click="minIncrement = 100" class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold transition-all" :class="minIncrement == 100 ? 'bg-amber-500 text-slate-950 shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'">+₹100</button>
                            <button type="button" @click="minIncrement = 250" class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold transition-all" :class="minIncrement == 250 ? 'bg-amber-500 text-slate-950 shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'">+₹250</button>
                            <button type="button" @click="minIncrement = 500" class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold transition-all" :class="minIncrement == 500 ? 'bg-amber-500 text-slate-950 shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'">+₹500</button>
                        </div>

                        <!-- Dynamic Safety Calculation Note -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs flex items-start gap-3 text-slate-600">
                            <span class="material-symbols-outlined text-[18px] text-amber-600 shrink-0 mt-0.5">verified_user</span>
                            <div class="space-y-1">
                                <p class="font-semibold text-slate-800">
                                    Reserve Protection Policy:
                                </p>
                                <p class="text-slate-500 text-[11px] leading-relaxed">
                                    Bids submitted below your secret reserve floor (<span class="font-mono font-bold text-slate-700" x-text="formatCurrency(reservePrice || startPrice)"></span>) will be recorded on the spot floor, but do not legally bind allocation if the floor remains unreached at closing.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 03: APMC Spot Trading Window -->
                    <div class="bg-white rounded-3xl p-6 lg:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-5">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-800 font-mono text-xs font-bold flex items-center justify-center border border-amber-500/20">
                                    03
                                </span>
                                <div>
                                    <h3 class="font-heading text-base font-bold text-slate-900">APMC Spot Trading Window</h3>
                                    <p class="text-xs text-slate-500">Set start execution timestamp and scheduled close duration</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200/60 font-mono text-[10px] font-bold uppercase tracking-wider">
                                Auto-Snipe Guard
                            </span>
                        </div>

                        <!-- Date & Time Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Starts At -->
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-heading font-semibold text-slate-700">Start Date &amp; Time</label>
                                    <button type="button" @click="setStartNow()" class="text-[10px] font-mono text-amber-600 hover:text-amber-800 font-bold">
                                        • Start Now (Live)
                                    </button>
                                </div>
                                <input type="datetime-local" 
                                       name="starts_at" 
                                       x-model="startsAt" 
                                       class="w-full h-11 px-4 bg-slate-50/80 rounded-xl font-mono text-xs font-semibold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all" 
                                       required>
                                @error('starts_at') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <!-- Ends At -->
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-heading font-semibold text-slate-700">Closing Date &amp; Time</label>
                                    <span class="text-[10px] font-mono text-slate-400">APMC Close Clock</span>
                                </div>
                                <input type="datetime-local" 
                                       name="ends_at" 
                                       x-model="endsAt" 
                                       class="w-full h-11 px-4 bg-slate-50/80 rounded-xl font-mono text-xs font-semibold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all" 
                                       required>
                                @error('ends_at') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Duration Presets -->
                        <div class="flex items-center gap-2 pt-1 flex-wrap">
                            <span class="text-xs font-mono text-slate-500">Duration Presets:</span>
                            <button type="button" @click="setDuration(4)" class="px-3 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all">+4 Hours (Flash)</button>
                            <button type="button" @click="setDuration(12)" class="px-3 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all">+12 Hours (Intraday)</button>
                            <button type="button" @click="setDuration(24)" class="px-3 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all">+24 Hours (Standard)</button>
                            <button type="button" @click="setDuration(72)" class="px-3 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all">+3 Days (Bulk Consignment)</button>
                        </div>

                        <!-- Anti-Snipe Buffer Ribbon -->
                        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-amber-50/70 border border-amber-200/60 font-mono text-xs text-amber-900">
                            <span class="material-symbols-outlined text-[18px] text-amber-700 shrink-0">timer</span>
                            <span>+2m Soft Close Auto-Extension enabled to prevent late snipe manipulation.</span>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('seller.auctions.index') }}" class="h-12 px-6 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-heading font-bold shadow-2xs transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                            <span>Cancel &amp; Discard</span>
                        </a>
                        <button type="submit" class="h-12 px-8 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-extrabold shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">publish</span>
                            <span>Publish Wholesale Auction Lot</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- RIGHT COLUMN: LIVE SPOT FLOOR PREVIEW CARD (5 COLS - STICKY) -->
            <div class="lg:col-span-5 flex flex-col gap-5 sticky top-24">
                <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 flex flex-col gap-5">
                    
                    <!-- Preview Title Bar -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-600 text-[18px]">visibility</span>
                            <span class="font-heading text-sm font-bold text-slate-900">Live Spot Floor Preview</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[10px] font-bold uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            What Buyers See
                        </span>
                    </div>

                    <!-- THE SIMULATED BUYER LOT CARD -->
                    <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-2xs bg-white">
                        
                        <!-- Artwork / Thumbnail Container -->
                        <div class="w-full h-52 bg-slate-50 relative flex items-center justify-center overflow-hidden border-b border-slate-100">
                            <template x-if="currentProduct">
                                <img :src="currentProduct.image" 
                                     :alt="currentProduct.name" 
                                     class="w-full h-full object-contain p-2"
                                     onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                            </template>
                            <template x-if="!currentProduct">
                                <div class="text-center text-slate-500 flex flex-col items-center">
                                    <span class="material-symbols-outlined text-[36px] text-slate-600 mb-1">image</span>
                                    <span class="text-xs font-mono">Select a product to preview</span>
                                </div>
                            </template>

                            <!-- Badge Overlays -->
                            <div class="absolute top-3 left-3 flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-rose-600 text-white font-mono text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 shadow-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                    LIVE FLOOR LOT
                                </span>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-mono text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 shadow-xs">
                                    <span class="material-symbols-outlined text-[13px]">verified</span>
                                    GI CERTIFIED
                                </span>
                            </div>

                            <!-- Lot ID Tag -->
                            <div class="absolute bottom-3 left-3 bg-slate-900/90 backdrop-blur-md px-2.5 py-1 rounded-lg font-mono text-[11px] font-bold text-white border border-slate-700">
                                LOT #AUC-NEW
                            </div>
                        </div>

                        <!-- Lot Details Content -->
                        <div class="p-5 flex flex-col gap-4">
                            <div>
                                <div class="flex items-center justify-between text-xs font-mono text-slate-500 mb-1">
                                    <span x-text="currentProduct ? currentProduct.category : 'Farm Produce'"></span>
                                    <span class="font-bold text-amber-700" x-text="'Unit: ' + (currentProduct ? currentProduct.unit : 'kg')"></span>
                                </div>
                                <h4 class="font-heading text-lg font-bold text-slate-900 line-clamp-1" x-text="currentProduct ? currentProduct.name : 'Select Produce Item'"></h4>
                                <p class="text-xs text-slate-500 line-clamp-2 mt-0.5" x-text="currentProduct ? currentProduct.description : 'Fresh harvest consignment ready for spot floor allocation.'"></p>
                            </div>

                            <!-- Bidding Metrics Tile -->
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-mono uppercase text-slate-400 font-bold block">Opening Floor</span>
                                    <div class="font-mono text-2xl font-extrabold text-slate-900" x-text="formatCurrency(startPrice)"></div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-mono uppercase text-slate-400 font-bold block">Next Minimum Bid</span>
                                    <div class="font-mono text-sm font-bold text-emerald-700" x-text="formatCurrency(parseFloat(startPrice || 0) + parseFloat(minIncrement || 0))"></div>
                                </div>
                            </div>

                            <!-- Live Quick Presets Pill Preview -->
                            <div class="flex flex-col gap-1.5">
                                <span class="text-[10px] font-mono text-slate-400 uppercase font-bold">1-Click Quick Bidding Presets</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="py-2 text-center rounded-xl bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200 flex items-center justify-center gap-1">
                                        <span class="text-amber-600 font-bold">⚡</span>
                                        <span x-text="'+' + formatCurrency(minIncrement)"></span>
                                    </div>
                                    <div class="py-2 text-center rounded-xl bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200 flex items-center justify-center gap-1">
                                        <span class="text-amber-600 font-bold">⚡</span>
                                        <span x-text="'+' + formatCurrency(minIncrement * 2)"></span>
                                    </div>
                                    <div class="py-2 text-center rounded-xl bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200 flex items-center justify-center gap-1">
                                        <span class="text-amber-600 font-bold">⚡</span>
                                        <span x-text="'+' + formatCurrency(minIncrement * 5)"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Reserve Indicator -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-mono">
                                <span class="text-slate-500">Reserve Floor Target:</span>
                                <template x-if="reservePrice && parseFloat(reservePrice) > 0">
                                    <span class="font-bold text-slate-800" x-text="formatCurrency(reservePrice) + ' (Protected)'"></span>
                                </template>
                                <template x-if="!reservePrice || parseFloat(reservePrice) <= 0">
                                    <span class="font-bold text-emerald-700">Open Clearance (No Reserve)</span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Seals List -->
                    <div class="grid grid-cols-2 gap-3 pt-2 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">security</span>
                            <span class="font-medium text-[11px]">100% Escrow Funded</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">verified</span>
                            <span class="font-medium text-[11px]">APMC Grade Verification</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">local_shipping</span>
                            <span class="font-medium text-[11px]">Cold-Chain Logistics</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">payments</span>
                            <span class="font-medium text-[11px]">Direct T+1 Settlement</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    @endif

</div>
@endsection
