@extends('layouts.seller')

@section('title', 'Add New Product — Bazaario Seller Center')

@section('content')
@php
    $categories = $categories ?? \App\Models\Category::all();
    $sellerUser = Auth::guard('seller')->user() ?? Auth::user();
    $sellerProfile = $sellerUser?->sellerProfile;
    $defaultSku = 'BZ-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $sellerProfile?->shop_name ?? 'FARM'), 0, 4)) . '-' . date('Y') . '-' . rand(100, 999);
@endphp

<form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" id="createProductForm">
    @csrf

    <div class="flex flex-col w-full pb-16">
        <!-- Top Navigation & Sticky Status Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4 mb-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2 font-mono text-xs text-on-surface-variant">
                    <a href="{{ route('seller.products.index') }}" class="hover:text-on-surface transition-colors">Products</a>
                    <span>/</span>
                    <span class="text-on-surface font-semibold">Add New Product</span>
                </div>
                <div class="flex items-baseline gap-3 mt-1">
                    <h1 class="font-heading text-2xl sm:text-3xl font-bold tracking-tight text-on-surface">Add New Product</h1>
                    <span class="font-mono text-xs px-2 py-0.5 rounded bg-surface-container-highest text-secondary uppercase font-semibold tracking-wider">
                        SKU: {{ $defaultSku }}
                    </span>
                </div>
                <p class="font-sans text-xs text-on-surface-variant">
                    List a new farm harvest or artisan item to local buyers and wholesale auction pools.
                </p>
            </div>

            <!-- Header Action CTAs -->
            <div class="flex items-center gap-3 self-start md:self-auto">
                <a href="{{ route('seller.products.index') }}" class="px-4 py-2.5 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high transition-colors font-sans text-xs font-medium">
                    Cancel
                </a>
                <button type="submit" name="status" value="draft" class="px-4 py-2.5 rounded-[14px] bg-surface-container-high text-on-surface hover:bg-surface-container-highest transition-colors font-sans text-xs font-medium flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">bookmark</span>
                    <span>Save Draft</span>
                </button>
                <button type="submit" name="status" value="active" class="px-6 py-2.5 rounded-[14px] bg-secondary-container text-on-secondary-container hover:bg-secondary-fixed-dim transition-all shadow-sm font-heading font-semibold text-xs flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                    <span>Publish Product</span>
                </button>
            </div>
        </div>

        <!-- Unsaved Changes Notice Strip -->
        <div class="mb-6 px-4 py-3 rounded-[14px] bg-surface-container-low flex items-center justify-between border border-[#E2DFD7]/60">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-secondary-container animate-pulse"></span>
                <span class="font-sans text-xs text-on-surface">Auto-saved to local draft storage.</span>
                <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-surface-container-highest text-on-surface-variant uppercase">Version 1.0</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="font-mono text-xs text-on-surface-variant">Origin Seller: {{ $sellerProfile?->shop_name ?? ($sellerUser?->name ?? 'Verified Farmer') }}</span>
            </div>
        </div>

        <!-- Main Multi-Section Two-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- LEFT / MAIN WORKSTATION COLUMN (7 cols) -->
            <div class="lg:col-span-7 flex flex-col gap-6">

                <!-- Section 1: Basic Information -->
                <section class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm flex flex-col gap-4 border border-[#E2DFD7]/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[11px] px-2 py-0.5 rounded bg-surface-container text-on-surface font-bold">SECTION 01</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">Basic Information</h2>
                        </div>
                        <span class="material-symbols-outlined text-on-surface-variant text-[20px]">info</span>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="product-name" class="font-sans text-xs font-semibold text-on-surface-variant">Product Title &amp; Botanical Strain</label>
                        <input type="text" name="name" id="product-name" value="{{ old('name', 'Organic Ratnagiri Alphonso Mango') }}" oninput="syncPreviewTitle(this.value)" placeholder="e.g. Organic Ratnagiri Alphonso Mango" required class="w-full h-12 px-4 rounded-[14px] bg-surface-container-low text-on-surface font-sans text-sm outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-brand-amber transition-colors border-none">
                        <div class="flex justify-between items-center px-1">
                            <span class="font-mono text-[11px] text-on-surface-variant">Recommended: Cultivar, Region, and Grade</span>
                            <span class="font-mono text-[11px] text-on-surface-variant" id="title-char-count">33 / 80</span>
                        </div>
                        @error('name')
                            <p class="font-sans text-xs text-error mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="category-select" class="font-sans text-xs font-semibold text-on-surface-variant">Category</label>
                            <div class="relative">
                                <select name="category_id" id="category-select" required class="w-full h-12 pl-4 pr-10 rounded-[14px] bg-surface-container-low text-on-surface font-sans text-sm outline-none appearance-none cursor-pointer border-none">
                                    @forelse($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @empty
                                        <option value="1">Fresh Fruits</option>
                                        <option value="2">Vegetables</option>
                                        <option value="3">Cold-Pressed Oils</option>
                                        <option value="4">Dairy &amp; Ghee</option>
                                    @endforelse
                                </select>
                                <span class="material-symbols-outlined pointer-events-none absolute right-3 top-3 text-on-surface-variant text-[20px]">unfold_more</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="harvest-grade" class="font-sans text-xs font-semibold text-on-surface-variant">Harvest Grade</label>
                            <div class="relative">
                                <select name="harvest_grade" id="harvest-grade" class="w-full h-12 pl-4 pr-10 rounded-[14px] bg-surface-container-low text-on-surface font-sans text-sm outline-none appearance-none cursor-pointer border-none">
                                    <option value="Grade A+ Export" selected>Grade A+ Export (250g - 300g per pc)</option>
                                    <option value="Grade A Table Quality">Grade A Table Quality (200g - 250g)</option>
                                    <option value="Grade B Pulping & Juice">Grade B Pulping &amp; Juice</option>
                                    <option value="Standard Organic">Standard Organic</option>
                                </select>
                                <span class="material-symbols-outlined pointer-events-none absolute right-3 top-3 text-on-surface-variant text-[20px]">unfold_more</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <label for="product-desc" class="font-sans text-xs font-semibold text-on-surface-variant">Detailed Description &amp; Farm Origin Notes</label>
                            <span class="font-mono text-[10px] text-secondary uppercase font-bold">Rich Text Enabled</span>
                        </div>
                        <div class="rounded-[14px] bg-surface-container-low p-2">
                            <div class="flex items-center gap-1 pb-2 mb-2 bg-surface-container-lowest px-2 py-1.5 rounded-[10px] border border-surface-container-high">
                                <button type="button" class="p-1 rounded hover:bg-surface-container-high text-on-surface text-xs font-bold">B</button>
                                <button type="button" class="p-1 rounded hover:bg-surface-container-high text-on-surface text-xs italic font-serif">I</button>
                                <button type="button" class="p-1 rounded hover:bg-surface-container-high text-on-surface text-xs underline">U</button>
                                <span class="text-outline-variant mx-1">|</span>
                                <button type="button" class="p-1 rounded hover:bg-surface-container-high text-on-surface flex items-center">
                                    <span class="material-symbols-outlined text-[16px]">format_list_bulleted</span>
                                </button>
                                <button type="button" class="p-1 rounded hover:bg-surface-container-high text-on-surface flex items-center">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                </button>
                                <span class="text-outline-variant mx-1">|</span>
                                <span class="font-mono text-[11px] text-on-surface-variant">Tags: #GI-Tagged #CarbideFree</span>
                            </div>
                            <textarea name="description" id="product-desc" rows="4" placeholder="Describe cultivation methods, taste notes, soil type, and ripeness state..." class="w-full px-2 bg-transparent text-on-surface font-sans text-xs outline-none resize-y">{{ old('description', 'Naturally ripened on tree branches without ethylene gas chambers or chemical carbides. Authentic GI-tagged Ratnagiri coastal soil profile yields an intense saffron hue, thin skin, and an average Brix sweetness rating over 19°. Packed in ventilated organic hay crates within 4 hours of twilight harvesting.') }}</textarea>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Pricing & Unit Type -->
                <section class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm flex flex-col gap-4 border border-[#E2DFD7]/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[11px] px-2 py-0.5 rounded bg-surface-container text-on-surface font-bold">SECTION 02</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">Pricing &amp; Unit Type</h2>
                        </div>
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-secondary-container/20 text-on-secondary-container">
                            <span class="material-symbols-outlined text-[16px]">percent</span>
                            <span class="font-mono text-[10px] font-bold uppercase">Dynamic Discount Engine</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="base-price" class="font-sans text-xs font-semibold text-on-surface-variant">Base Selling Price</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-4 font-mono font-bold text-on-surface-variant">₹</span>
                                <input type="number" step="0.01" name="price" id="base-price" value="{{ old('price', 350) }}" oninput="calculatePrice()" required class="w-full h-12 pl-8 pr-4 rounded-[14px] bg-surface-container-low text-on-surface font-mono text-sm outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-brand-amber transition-colors border-none">
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="discount-pct" class="font-sans text-xs font-semibold text-on-surface-variant">Seasonal Discount</label>
                            <div class="relative flex items-center">
                                <input type="number" id="discount-pct" value="10" oninput="calculatePrice()" class="w-full h-12 pl-4 pr-8 rounded-[14px] bg-surface-container-low text-on-surface font-mono text-sm outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-brand-amber transition-colors border-none">
                                <span class="absolute right-4 font-mono font-bold text-on-surface-variant">%</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-sans text-xs font-semibold text-on-surface-variant">Calculated Final Price</label>
                            <div class="h-12 px-4 rounded-[14px] bg-surface-container-high flex items-center justify-between">
                                <span class="font-mono text-sm font-bold text-on-surface" id="final-price-display">₹315.00</span>
                                <span class="font-mono text-[10px] uppercase px-1.5 py-0.5 rounded bg-surface-container-highest text-on-tertiary-container font-bold">Saved ₹35</span>
                            </div>
                        </div>
                    </div>

                    <!-- Commercial Unit of Measurement (UoM) Buttons (6 types) -->
                    <div class="flex flex-col gap-2 mt-2">
                        <label class="font-sans text-xs font-semibold text-on-surface-variant">Commercial Unit of Measurement (UoM)</label>
                        <input type="hidden" name="unit_type" id="unit-type-input" value="{{ old('unit_type', 'kg') }}">
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2" id="unit-selector-group">
                            <button type="button" onclick="selectUnit(this, 'kg')" class="unit-btn active h-12 rounded-[14px] bg-primary text-white font-mono text-xs font-medium flex flex-col items-center justify-center transition-all">
                                <span>kg</span>
                                <span class="text-[9px] uppercase tracking-wider opacity-75">Kilogram</span>
                            </button>
                            <button type="button" onclick="selectUnit(this, 'dozen')" class="unit-btn h-12 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high font-mono text-xs font-medium flex flex-col items-center justify-center transition-all">
                                <span>dozen</span>
                                <span class="text-[9px] uppercase tracking-wider text-on-surface-variant">12 Units</span>
                            </button>
                            <button type="button" onclick="selectUnit(this, 'bundle')" class="unit-btn h-12 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high font-mono text-xs font-medium flex flex-col items-center justify-center transition-all">
                                <span>bundle</span>
                                <span class="text-[9px] uppercase tracking-wider text-on-surface-variant">Bound pack</span>
                            </button>
                            <button type="button" onclick="selectUnit(this, 'litre')" class="unit-btn h-12 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high font-mono text-xs font-medium flex flex-col items-center justify-center transition-all">
                                <span>litre</span>
                                <span class="text-[9px] uppercase tracking-wider text-on-surface-variant">Volume</span>
                            </button>
                            <button type="button" onclick="selectUnit(this, 'piece')" class="unit-btn h-12 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high font-mono text-xs font-medium flex flex-col items-center justify-center transition-all">
                                <span>piece</span>
                                <span class="text-[9px] uppercase tracking-wider text-on-surface-variant">Per item</span>
                            </button>
                            <button type="button" onclick="selectUnit(this, 'pack')" class="unit-btn h-12 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high font-mono text-xs font-medium flex flex-col items-center justify-center transition-all">
                                <span>pack</span>
                                <span class="text-[9px] uppercase tracking-wider text-on-surface-variant">Boxed</span>
                            </button>
                        </div>
                    </div>

                    <!-- Live Listing displays as -->
                    <div class="mt-1 p-3 rounded-[14px] bg-surface-container-low flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary text-[20px]">sell</span>
                            <span class="font-sans text-xs text-on-surface">Listing displays to buyers as:</span>
                            <span class="font-mono text-xs font-bold text-on-surface" id="price-unit-tag">₹315 / kg</span>
                        </div>
                        <span class="font-mono text-[10px] text-on-surface-variant">Taxes included (Mandatory APMC GST 0%)</span>
                    </div>
                </section>

                <!-- Section 3: Inventory & Safety Thresholds -->
                <section class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm flex flex-col gap-4 border border-[#E2DFD7]/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[11px] px-2 py-0.5 rounded bg-surface-container text-on-surface font-bold">SECTION 03</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">Inventory &amp; Stock Buffer</h2>
                        </div>
                        <div class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] bg-tertiary-fixed-dim/20 text-on-tertiary-container">
                            <span class="material-symbols-outlined text-[16px]">warehouse</span>
                            <span class="font-mono text-[10px] font-bold uppercase">Automated Depletion</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="stock-qty" class="font-sans text-xs font-semibold text-on-surface-variant">Current Available Stock Quantity</label>
                            <div class="relative flex items-center">
                                <input type="number" name="stock" id="stock-qty" value="{{ old('stock', 45) }}" oninput="updateStockStatus()" required class="w-full h-12 px-4 rounded-[14px] bg-surface-container-low text-on-surface font-mono text-sm outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-brand-amber transition-colors border-none">
                                <span class="absolute right-4 font-mono text-xs text-on-surface-variant unit-label-text">kg</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="threshold-qty" class="font-sans text-xs font-semibold text-on-surface-variant">Minimum Stock Alert Threshold</label>
                            <div class="relative flex items-center">
                                <input type="number" name="low_stock_threshold" id="threshold-qty" value="{{ old('low_stock_threshold', 10) }}" oninput="updateStockStatus()" class="w-full h-12 px-4 rounded-[14px] bg-surface-container-low text-on-surface font-mono text-sm outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-brand-amber transition-colors border-none">
                                <span class="absolute right-4 font-mono text-xs text-on-surface-variant unit-label-text">kg</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Stock Status Card with Gauge Visual -->
                    <div class="p-4 rounded-[14px] bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="relative w-12 h-12 flex-shrink-0">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-surface-container-highest" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.5"></path>
                                    <path class="text-on-tertiary-container" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" id="stock-gauge" stroke="currentColor" stroke-dasharray="78, 100" stroke-linecap="round" stroke-width="3.5"></path>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center font-mono text-[10px] font-bold text-on-surface">
                                    78%
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="font-heading text-sm font-semibold text-on-surface" id="stock-status-title">Status: In Stock</span>
                                    <span class="font-mono text-[10px] px-2 py-0.5 rounded bg-tertiary-fixed-dim/20 text-on-tertiary-container font-semibold uppercase">Healthy Level</span>
                                </div>
                                <p class="font-sans text-xs text-on-surface-variant" id="stock-buffer-note">
                                    Safe buffer: +35 kg over minimum alert threshold.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 4: Farmer-Specific Agronomic Ledger -->
                <section class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm flex flex-col gap-4 border border-[#E2DFD7]/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[11px] px-2 py-0.5 rounded bg-surface-container text-on-surface font-bold">SECTION 04</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">Farmer-Specific Agronomic Ledger</h2>
                        </div>
                        <div class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] bg-tertiary-fixed-dim/20 text-on-tertiary-container">
                            <span class="material-symbols-outlined text-[16px]">verified_user</span>
                            <span class="font-mono text-[10px] font-bold uppercase">Farmer Identity Verified</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="farm-origin" class="font-sans text-xs font-semibold text-on-surface-variant">Farm Origin &amp; Plot Code</label>
                            <input type="text" name="farm_origin" id="farm-origin" value="{{ old('farm_origin', $sellerProfile?->address ?? 'Green Valley Farm, Sector 4 Block C, Ratnagiri') }}" class="w-full h-12 px-4 rounded-[14px] bg-surface-container-low text-on-surface font-sans text-xs outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-brand-amber transition-colors border-none">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="harvest-date" class="font-sans text-xs font-semibold text-on-surface-variant">Harvest Date</label>
                            <input type="date" name="harvest_date" id="harvest-date" value="{{ old('harvest_date', date('Y-m-d')) }}" class="w-full h-12 px-4 rounded-[14px] bg-surface-container-low text-on-surface font-mono text-xs outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-brand-amber transition-colors border-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="expiry-window" class="font-sans text-xs font-semibold text-on-surface-variant">Peak Freshness Shelf Window</label>
                            <div class="relative">
                                <select name="expiry_days" id="expiry-window" class="w-full h-12 pl-4 pr-10 rounded-[14px] bg-surface-container-low text-on-surface font-sans text-xs outline-none appearance-none cursor-pointer border-none">
                                    <option value="3">3 Days from harvest</option>
                                    <option value="5" selected>5 Days from harvest</option>
                                    <option value="7">7 Days from harvest</option>
                                    <option value="10">10 Days from harvest</option>
                                    <option value="14">14+ Days (Cold Storage Preserved)</option>
                                </select>
                                <span class="material-symbols-outlined pointer-events-none absolute right-3 top-3 text-on-surface-variant text-[20px]">schedule</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-sans text-xs font-semibold text-on-surface-variant">Quality Validation Index</label>
                            <div class="h-12 px-4 rounded-[14px] bg-surface-container-low flex items-center justify-between">
                                <span class="font-sans text-xs text-on-surface font-medium">Brix Sweetness Score</span>
                                <span class="font-mono text-xs px-2 py-0.5 rounded bg-surface-container-highest font-bold text-on-tertiary-container">19.4° Bx</span>
                            </div>
                        </div>
                    </div>

                    <!-- Automated Stale Listing Options -->
                    <div class="flex flex-col gap-3 mt-1 p-4 rounded-[14px] bg-surface-container-low">
                        <span class="font-heading text-sm font-semibold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary text-[18px]">rule_settings</span>
                            Automated Freshness Guardrails
                        </span>
                        <div class="flex flex-col gap-3 mt-1">
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="is_perishable" value="1" checked class="mt-0.5 w-4 h-4 rounded text-primary focus:ring-0 cursor-pointer">
                                <div class="flex flex-col">
                                    <span class="font-sans text-xs font-semibold text-on-surface">Automatically flag as STALE when within 24 hours of expiry window</span>
                                    <span class="font-sans text-[11px] text-on-surface-variant">Sends urgent price markdown suggestions or wholesale auction prompt.</span>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="auto_hide_expired" value="1" checked class="mt-0.5 w-4 h-4 rounded text-primary focus:ring-0 cursor-pointer">
                                <div class="flex flex-col">
                                    <span class="font-sans text-xs font-semibold text-on-surface">Automatically hide listing when harvest expiry window lapses</span>
                                    <span class="font-sans text-[11px] text-on-surface-variant">Protects farmer trust score against stale produce disputes.</span>
                                </div>
                            </label>
                        </div>
                        <div class="mt-1 pt-2 flex items-center gap-2 text-on-surface-variant border-t border-surface-container-highest">
                            <span class="material-symbols-outlined text-[16px] text-secondary">shield</span>
                            <span class="font-mono text-[10px]">Note: Bazaario never silently deletes products; expired goods will be moved to Hidden / Restock archive.</span>
                        </div>
                    </div>
                </section>
            </div>

            <!-- RIGHT COLUMN / ASSET & SIMULATION PANEL (5 cols) -->
            <div class="lg:col-span-5 flex flex-col gap-6">

                <!-- Section 5: Product Media & Visuals -->
                <section class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm flex flex-col gap-4 border border-[#E2DFD7]/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[11px] px-2 py-0.5 rounded bg-surface-container text-on-surface font-bold">SECTION 05</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">Media &amp; Visuals</h2>
                        </div>
                        <span class="font-mono text-[11px] text-on-surface-variant">Max 6 photos</span>
                    </div>

                    <!-- Drag & Drop Target Area -->
                    <div class="relative p-6 rounded-[14px] bg-surface-container-low flex flex-col items-center justify-center text-center transition-all cursor-pointer group hover:bg-surface-container-high border-2 border-dashed border-surface-container-highest">
                        <input type="file" name="images[]" multiple accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                        <div class="w-12 h-12 rounded-full bg-surface-container-lowest flex items-center justify-center shadow-sm mb-3 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-secondary text-[24px]">cloud_upload</span>
                        </div>
                        <span class="font-heading text-sm font-semibold text-on-surface">Drag &amp; drop high-resolution harvest photos here</span>
                        <span class="font-sans text-xs text-on-surface-variant mt-1">or <span class="text-secondary font-medium underline">browse files</span> from your local device</span>
                        <span class="font-mono text-[10px] text-on-surface-variant mt-2 uppercase">PNG, JPG up to 10MB each. Max 6 photos.</span>
                    </div>

                    <!-- Image Previews Mosaic -->
                    <div class="flex flex-col gap-2">
                        <span class="font-sans text-xs font-semibold text-on-surface-variant">Harvest Gallery Stream</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="relative group rounded-[12px] overflow-hidden bg-surface-container-high h-24">
                                <img src="https://images.unsplash.com/photo-1553279768-865429fa0078?w=400" alt="Harvest Photo" class="w-full h-full object-cover">
                                <span class="absolute top-1.5 left-1.5 font-mono text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-white text-on-surface shadow-sm">Primary</span>
                            </div>
                            <div class="relative group rounded-[12px] overflow-hidden bg-surface-container-high h-24">
                                <img src="https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?w=400" alt="Harvest Photo" class="w-full h-full object-cover">
                            </div>
                            <div class="relative group rounded-[12px] overflow-hidden bg-surface-container-high h-24 flex items-center justify-center bg-surface-container border border-dashed border-surface-container-highest text-on-surface-variant">
                                <span class="material-symbols-outlined text-[24px]">add_photo_alternate</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-[10px] bg-surface-container-low text-[11px] text-on-surface-variant font-mono">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-on-tertiary-container">check_circle</span> High-res inspection pass
                        </span>
                        <span>SRGB profile embedded</span>
                    </div>
                </section>

                <!-- Section 6: Quick Live Listing Preview Card (Sticky) -->
                <section class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm flex flex-col gap-4 sticky top-20 border border-[#E2DFD7]/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[10px] px-2 py-0.5 rounded bg-secondary-container text-on-secondary-container font-bold">PREVIEW</span>
                            <h2 class="font-heading text-lg font-bold text-on-surface">Buyer View Simulation</h2>
                        </div>
                        <span class="flex items-center gap-1.5 font-mono text-[10px] text-on-tertiary-container font-bold uppercase">
                            <span class="w-2 h-2 rounded-full bg-on-tertiary-container"></span> Live Synced
                        </span>
                    </div>
                    <p class="font-sans text-xs text-on-surface-variant">
                        This preview dynamically renders the consumer product card as displayed in the Bazaario consumer app &amp; wholesale buyer auction portal.
                    </p>

                    <!-- Simulated Consumer Marketplace Card -->
                    <div class="w-full rounded-[14px] overflow-hidden bg-surface-container-low shadow-md transition-all hover:shadow-lg flex flex-col border border-surface-container-high">
                        <!-- Card Image with Badges -->
                        <div class="relative w-full h-44 bg-surface-container-high overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1553279768-865429fa0078?w=600" alt="Product Preview" class="w-full h-full object-cover">
                            <div class="absolute top-2.5 left-2.5 flex flex-wrap gap-1.5">
                                <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-on-tertiary-container text-white shadow-sm uppercase">Harvested Today</span>
                                <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-white text-on-surface shadow-sm uppercase flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px] text-on-tertiary-container">verified</span> GI Tagged
                                </span>
                            </div>
                            <div class="absolute bottom-2.5 right-2.5 px-2 py-1 rounded-[8px] bg-primary text-white font-mono text-[10px] font-semibold flex items-center gap-1 shadow-md">
                                <span class="material-symbols-outlined text-[13px] text-secondary-container">shield</span>
                                <span>Score: 94/100</span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-4 flex flex-col gap-2">
                            <div class="flex items-center justify-between text-xs text-on-surface-variant">
                                <span class="font-mono text-[10px] uppercase tracking-wider font-semibold">{{ $sellerProfile?->shop_name ?? 'Green Valley Farm' }}</span>
                                <span class="flex items-center gap-1 font-mono text-[11px]">
                                    <span class="material-symbols-outlined text-[14px] text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
                                    4.9 (128 reviews)
                                </span>
                            </div>
                            <h3 class="font-heading text-base text-on-surface font-semibold leading-tight line-clamp-1" id="preview-card-title">
                                Organic Ratnagiri Alphonso Mango
                            </h3>
                            <p class="font-sans text-xs text-on-surface-variant line-clamp-2">
                                Naturally tree-ripened, chemical carbide free with Brix sweetness score above 19°.
                            </p>

                            <!-- Price with Strike-through & Unit -->
                            <div class="flex items-baseline justify-between pt-2">
                                <div class="flex items-baseline gap-2">
                                    <span class="font-heading text-lg font-bold text-on-surface" id="preview-final-price">₹315</span>
                                    <span class="font-mono text-xs text-on-surface-variant line-through" id="preview-base-price">₹350</span>
                                    <span class="font-mono text-xs font-bold text-secondary" id="preview-unit-suffix">/ kg</span>
                                </div>
                                <span class="font-mono text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-secondary-container/30 text-on-secondary-container">
                                    10% OFF
                                </span>
                            </div>
                            <button type="button" class="w-full mt-2 h-10 rounded-[10px] bg-primary text-white font-sans text-xs font-semibold flex items-center justify-center gap-2 hover:bg-primary/90 transition-colors">
                                <span class="material-symbols-outlined text-[16px]">add_shopping_cart</span>
                                <span>Simulate Buyer Cart</span>
                            </button>
                        </div>
                    </div>

                    <!-- Sticky Card Bottom Actions -->
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" name="status" value="active" class="flex-1 h-12 rounded-[14px] bg-secondary-container text-on-secondary-container hover:bg-secondary-fixed-dim transition-all shadow-sm font-heading text-xs font-bold flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">check</span>
                            <span>Publish Listing</span>
                        </button>
                        <button type="reset" class="px-4 h-12 rounded-[14px] bg-surface-container-high text-on-surface hover:bg-surface-container-highest transition-colors font-sans text-xs font-medium">
                            Reset
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    let currentUnit = 'kg';

    function calculatePrice() {
        const baseInput = document.getElementById('base-price');
        const discountInput = document.getElementById('discount-pct');
        const finalDisplay = document.getElementById('final-price-display');
        const priceUnitTag = document.getElementById('price-unit-tag');
        const previewFinal = document.getElementById('preview-final-price');
        const previewBase = document.getElementById('preview-base-price');

        const base = parseFloat(baseInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const final = Math.max(0, base - (base * (discount / 100)));

        if (finalDisplay) finalDisplay.innerText = '₹' + final.toFixed(2);
        if (priceUnitTag) priceUnitTag.innerText = '₹' + Math.round(final) + ' / ' + currentUnit;
        if (previewFinal) previewFinal.innerText = '₹' + Math.round(final);
        if (previewBase) previewBase.innerText = '₹' + Math.round(base);
    }

    function selectUnit(element, unit) {
        document.querySelectorAll('.unit-btn').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white', 'active');
            btn.classList.add('bg-surface-container-low', 'text-on-surface');
        });
        element.classList.remove('bg-surface-container-low', 'text-on-surface');
        element.classList.add('bg-primary', 'text-white', 'active');

        currentUnit = unit;
        document.getElementById('unit-type-input').value = unit;

        document.querySelectorAll('.unit-label-text').forEach(span => {
            span.innerText = unit;
        });

        const previewUnit = document.getElementById('preview-unit-suffix');
        if (previewUnit) previewUnit.innerText = '/ ' + unit;

        calculatePrice();
        updateStockStatus();
    }

    function syncPreviewTitle(text) {
        const previewHeading = document.getElementById('preview-card-title');
        const charCount = document.getElementById('title-char-count');
        if (previewHeading) previewHeading.innerText = text || 'Untitled Product Harvest';
        if (charCount) charCount.innerText = text.length + ' / 80';
    }

    function updateStockStatus() {
        const stockQty = parseFloat(document.getElementById('stock-qty').value) || 0;
        const thresholdQty = parseFloat(document.getElementById('threshold-qty').value) || 0;
        const statusTitle = document.getElementById('stock-status-title');
        const bufferNote = document.getElementById('stock-buffer-note');
        const gauge = document.getElementById('stock-gauge');

        const diff = stockQty - thresholdQty;
        if (diff > 0) {
            if (statusTitle) statusTitle.innerText = 'Status: In Stock';
            if (bufferNote) bufferNote.innerText = 'Safe buffer: +' + diff + ' ' + currentUnit + ' over minimum alert threshold.';
            if (gauge) gauge.setAttribute('class', 'text-on-tertiary-container');
        } else if (diff === 0) {
            if (statusTitle) statusTitle.innerText = 'Status: At Alert Level';
            if (bufferNote) bufferNote.innerText = 'Inventory reached exact threshold level of ' + thresholdQty + ' ' + currentUnit + '.';
            if (gauge) gauge.setAttribute('class', 'text-secondary');
        } else {
            if (statusTitle) statusTitle.innerText = 'Status: Low Stock Warning';
            if (bufferNote) bufferNote.innerText = 'Critical shortage: ' + Math.abs(diff) + ' ' + currentUnit + ' deficit from minimum.';
            if (gauge) gauge.setAttribute('class', 'text-error');
        }
    }

    // Initialize calculations on DOM mount
    calculatePrice();
    updateStockStatus();
</script>
@endpush
@endsection
