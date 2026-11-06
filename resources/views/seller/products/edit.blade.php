@extends('layouts.seller')

@section('title', 'Edit Product — ' . ($product->name ?? 'Product') . ' — Bazaario')

@section('content')
@php
    $categories = $categories ?? \App\Models\Category::all();
    $sellerUser = Auth::guard('seller')->user() ?? Auth::user();
    $sellerProfile = $sellerUser?->sellerProfile;

    // Defensive fallback instance for $product
    if (!isset($product) || !$product) {
        $product = \App\Models\Product::where('seller_id', $sellerUser?->id)->first() ?? new \App\Models\Product([
            'id' => 1,
            'name' => 'Organic Ratnagiri Alphonso Mango',
            'price' => 350,
            'stock' => 45,
            'unit_type' => 'kg',
            'harvest_grade' => 'Grade A+ Export',
            'farm_origin' => 'Green Valley Farm, Sector 4 Block C, Ratnagiri',
            'harvest_date' => now()->subDays(2)->toDateString(),
            'expiry_days' => 5,
            'is_perishable' => true,
            'auto_hide_expired' => true,
            'low_stock_threshold' => 10,
            'description' => 'Naturally ripened on tree branches without ethylene gas chambers or chemical carbides.',
        ]);
    }
    $currentUnit = old('unit_type', $product->unit_type ?? 'kg');
@endphp

<form method="POST" action="{{ route('seller.products.update', $product->id ?? 1) }}" enctype="multipart/form-data" id="editProductForm">
    @csrf
    @method('PUT')

    <div class="flex flex-col w-full pb-16">
        <!-- Top Navigation & Sticky Status Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4 mb-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2 font-mono text-xs text-slate-500 mb-1">
                    <a href="{{ route('seller.products.index') }}" class="hover:text-slate-900 transition-colors">Products</a>
                    <span>/</span>
                    <span class="text-slate-900 font-semibold truncate max-w-sm">Edit: {{ $product->name }}</span>
                </div>
                <div class="flex items-baseline gap-3 mt-1">
                    <h1 class="font-heading text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Edit Product</h1>
                    <span class="font-mono text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 uppercase font-semibold tracking-wider border border-slate-200/80">
                        SKU: {{ $product->sku ?? ('BZ-PROD-' . $product->id) }}
                    </span>
                </div>
                <p class="font-sans text-xs sm:text-sm text-slate-500 mt-1">
                    Update crop specifications, harvest SLA dates, wholesale pricing, or inventory safety thresholds.
                </p>
            </div>

            <!-- Header Action CTAs -->
            <div class="flex items-center gap-3 self-start md:self-auto">
                <a href="{{ route('seller.products.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200/80 transition-colors font-sans text-xs font-semibold">
                    Cancel
                </a>
                <button type="submit" name="status" value="draft" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200/80 transition-colors font-sans text-xs font-semibold flex items-center gap-1.5 border border-slate-200/70">
                    <span class="material-symbols-outlined text-[18px]">bookmark</span>
                    <span>Save Draft</span>
                </button>
                <button type="submit" name="status" value="active" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading font-bold text-xs flex items-center gap-2 shadow-xs transition-all">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>Update Listing</span>
                </button>
            </div>
        </div>

        <!-- Main Multi-Section Two-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- LEFT / MAIN WORKSTATION COLUMN (7 cols) -->
            <div class="lg:col-span-7 flex flex-col gap-6">

                <!-- Section 1: Basic Information -->
                <section class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/90 flex flex-col gap-6 transition-all hover:shadow-sm">
                    <!-- Section Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold uppercase tracking-wider">SECTION 01</span>
                                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 font-mono">Core Details</span>
                                </div>
                                <h2 class="font-heading text-lg font-bold text-slate-900 mt-0.5">Basic Information</h2>
                            </div>
                        </div>
                        <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 text-xs">
                            <span class="text-rose-500 font-bold">*</span>
                            <span class="font-medium">Required fields</span>
                        </div>
                    </div>

                    <!-- 1. Product Title -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <label for="product-name" class="font-sans text-xs font-bold text-slate-800 flex items-center gap-1">
                                <span>Product Title &amp; Cultivar / Strain</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <span class="font-mono text-[11px] text-slate-400" id="title-char-count">{{ strlen($product->name ?? '') }} / 80</span>
                        </div>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[18px] pointer-events-none">label</span>
                            <input type="text" 
                                   name="name" 
                                   id="product-name" 
                                   value="{{ old('name', $product->name) }}" 
                                   oninput="syncPreviewTitle(this.value)" 
                                   placeholder="e.g. Organic Ratnagiri Alphonso Mango, Darjeeling Whole Leaf Tea, Fresh Spinach" 
                                   required 
                                   maxlength="120"
                                   class="w-full h-12 pl-10 pr-4 rounded-xl bg-slate-50/60 hover:bg-white focus:bg-white text-slate-900 font-sans text-sm outline-none border border-slate-200/90 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-2xs">
                        </div>
                        <div class="flex items-center justify-between px-1 text-[11px] text-slate-500">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-amber-500">lightbulb</span>
                                Recommended: Cultivar name, harvest region, and quality grade for highest search visibility
                            </span>
                        </div>
                        @error('name')
                            <p class="font-sans text-xs text-rose-600 mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- 2. Category & Harvest Grade Dual Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Category Selector -->
                        <div class="flex flex-col gap-2 transition-all duration-200" id="category-container">
                            <div class="flex items-center justify-between">
                                <label for="category-select" class="font-sans text-xs font-bold text-slate-800 flex items-center gap-1">
                                    <span>Primary Category</span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <span class="font-mono text-[10px]" id="category-tag-hint"></span>
                            </div>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-3.5 text-slate-400 text-[18px] pointer-events-none">category</span>
                                <select name="category_id" 
                                        id="category-select" 
                                        required 
                                        onchange="handleCategoryChange()" 
                                        class="w-full h-12 pl-10 pr-10 rounded-xl bg-slate-50/60 hover:bg-white focus:bg-white text-slate-900 font-sans text-sm outline-none border border-slate-200/90 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 appearance-none cursor-pointer transition-all shadow-2xs">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" 
                                                data-slug="{{ $cat->slug }}" 
                                                data-name="{{ strtolower($cat->name) }}" 
                                                {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined pointer-events-none absolute right-3.5 top-3.5 text-slate-400 text-[18px]">unfold_more</span>
                            </div>
                            @error('category_id')
                                <p class="font-sans text-xs text-rose-600 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Harvest Grade -->
                        <div class="flex flex-col gap-2 transition-all duration-200" id="harvest-grade-container">
                            <label for="harvest-grade" class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span>Harvest Grade / Quality Standard</span>
                                <span class="text-[10px] font-mono text-slate-400">Agricultural</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-3.5 text-slate-400 text-[18px] pointer-events-none">workspace_premium</span>
                                <select name="harvest_grade" 
                                        id="harvest-grade" 
                                        class="w-full h-12 pl-10 pr-10 rounded-xl bg-slate-50/60 hover:bg-white focus:bg-white text-slate-900 font-sans text-sm outline-none border border-slate-200/90 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 appearance-none cursor-pointer transition-all shadow-2xs">
                                    <option value="Grade A+ Export" {{ old('harvest_grade', $product->harvest_grade) == 'Grade A+ Export' ? 'selected' : '' }}>Grade A+ Export (Premium Select)</option>
                                    <option value="Grade A Table Quality" {{ old('harvest_grade', $product->harvest_grade) == 'Grade A Table Quality' ? 'selected' : '' }}>Grade A Table Quality (Standard Prime)</option>
                                    <option value="Grade B Pulping & Juice" {{ old('harvest_grade', $product->harvest_grade) == 'Grade B Pulping & Juice' ? 'selected' : '' }}>Grade B Processing &amp; Juicing</option>
                                    <option value="Standard Organic" {{ old('harvest_grade', $product->harvest_grade) == 'Standard Organic' ? 'selected' : '' }}>Standard Farm Certified Organic</option>
                                </select>
                                <span class="material-symbols-outlined pointer-events-none absolute right-3.5 top-3.5 text-slate-400 text-[18px]">unfold_more</span>
                            </div>
                            @error('harvest_grade')
                                <p class="font-sans text-xs text-rose-600 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 3. Short Summary Tagline -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <label for="product-short-desc" class="font-sans text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span>Short Tagline &amp; Storefront Summary</span>
                                <span class="text-[10px] font-mono font-normal text-slate-400">(Optional • Max 500 chars)</span>
                            </label>
                            <span class="font-mono text-[10px] text-slate-400" id="short-desc-counter">{{ strlen($product->short_description ?? '') }} / 500</span>
                        </div>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[18px] pointer-events-none">short_text</span>
                            <input type="text" 
                                   name="short_description" 
                                   id="product-short-desc" 
                                   value="{{ old('short_description', $product->short_description) }}" 
                                   maxlength="500"
                                   oninput="document.getElementById('short-desc-counter').innerText = this.value.length + ' / 500'"
                                   placeholder="e.g. Naturally tree-ripened Alphonso mangoes with rich saffron hue and intense sweetness." 
                                   class="w-full h-11 pl-10 pr-4 rounded-xl bg-slate-50/60 hover:bg-white focus:bg-white text-slate-900 font-sans text-xs outline-none border border-slate-200/90 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-2xs">
                        </div>
                        @error('short_description')
                            <p class="font-sans text-xs text-rose-600 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 4. Detailed Description & Rich Notes -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <label for="product-desc" class="font-sans text-xs font-bold text-slate-800 flex items-center gap-1">
                                <span>Detailed Description &amp; Farm Origin Notes</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[10px] px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold border border-amber-200/50">Markdown &amp; Tags</span>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200/90 bg-slate-50/60 overflow-hidden focus-within:border-amber-500 focus-within:ring-4 focus-within:ring-amber-500/10 focus-within:bg-white transition-all shadow-2xs">
                            <!-- Functional Formatting Toolbar -->
                            <div class="flex flex-wrap items-center gap-1.5 p-2 bg-white border-b border-slate-200/70 text-slate-600">
                                <div class="flex items-center gap-0.5 bg-slate-100/80 p-0.5 rounded-lg border border-slate-200/60">
                                    <button type="button" onclick="formatDescText('**', '**')" class="px-2 py-1 rounded hover:bg-white hover:text-slate-900 text-xs font-bold transition-colors cursor-pointer" title="Bold (**text**)">B</button>
                                    <button type="button" onclick="formatDescText('*', '*')" class="px-2 py-1 rounded hover:bg-white hover:text-slate-900 text-xs italic font-serif transition-colors cursor-pointer" title="Italic (*text*)">I</button>
                                    <button type="button" onclick="formatDescText('__', '__')" class="px-2 py-1 rounded hover:bg-white hover:text-slate-900 text-xs underline transition-colors cursor-pointer" title="Underline">U</button>
                                </div>

                                <span class="text-slate-300">|</span>

                                <div class="flex items-center gap-0.5 bg-slate-100/80 p-0.5 rounded-lg border border-slate-200/60">
                                    <button type="button" onclick="insertDescBullet()" class="px-2 py-1 rounded hover:bg-white hover:text-slate-900 flex items-center text-xs transition-colors cursor-pointer" title="Bullet List">
                                        <span class="material-symbols-outlined text-[15px]">format_list_bulleted</span>
                                    </button>
                                </div>

                                <span class="text-slate-300">|</span>

                                <!-- Quick Insertion Tags -->
                                <div class="flex items-center gap-1 overflow-x-auto py-0.5 text-[10px]">
                                    <span class="font-mono text-slate-400 font-semibold mr-1">Tags:</span>
                                    <button type="button" onclick="insertDescTag('#GI-Tagged')" class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/70 font-mono transition-colors font-medium cursor-pointer">#GI-Tagged</button>
                                    <button type="button" onclick="insertDescTag('#100%Organic')" class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/70 font-mono transition-colors font-medium cursor-pointer">#100%Organic</button>
                                    <button type="button" onclick="insertDescTag('#CarbideFree')" class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/70 font-mono transition-colors font-medium cursor-pointer">#CarbideFree</button>
                                    <button type="button" onclick="insertDescTag('#DirectFromFarm')" class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200/70 font-mono transition-colors font-medium cursor-pointer">#DirectFromFarm</button>
                                </div>
                            </div>

                            <textarea name="description" 
                                      id="product-desc" 
                                      rows="5" 
                                      oninput="updateDescCharCount()"
                                      placeholder="Provide detailed cultivation notes, organic harvesting practices, Brix sweetness rating, flavor notes, packaging specs, or culinary recommendations..." 
                                      class="w-full p-3.5 bg-transparent text-slate-900 font-sans text-xs outline-none resize-y leading-relaxed">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="flex items-center justify-between px-1 text-[11px] text-slate-400">
                            <span>Markdown headers, bolding, and bullet points supported</span>
                            <span class="font-mono" id="desc-char-count">{{ strlen($product->description ?? '') }} characters</span>
                        </div>
                        @error('description')
                            <p class="font-sans text-xs text-rose-600 mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </section>

                <!-- Section 2: Pricing & Unit Type -->
                <section class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/90 flex flex-col gap-6 transition-all hover:shadow-sm">
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-700 shadow-2xs">
                                <span class="material-symbols-outlined text-[20px]">payments</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider">SECTION 02</span>
                                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 font-mono">Commerce &amp; Valuation</span>
                                </div>
                                <h2 class="font-heading text-lg font-bold text-slate-900 mt-0.5">Pricing &amp; Unit Type</h2>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50/80 border border-amber-200/60 text-amber-800 text-xs font-semibold shadow-2xs">
                            <span class="material-symbols-outlined text-[16px] text-amber-600">percent</span>
                            <span class="font-mono text-[10px] uppercase tracking-wider">Dynamic Discount Engine</span>
                        </div>
                    </div>

                    <!-- 3-Column Pricing Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                        <!-- 1. Base Selling Price -->
                        <div class="flex flex-col gap-2">
                            <label for="base-price" class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span class="flex items-center gap-1">
                                    Base Selling Price <span class="text-rose-500">*</span>
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">INR (₹)</span>
                            </label>
                            <div class="relative flex items-center group">
                                <span class="inline-flex items-center justify-center w-11 h-12 rounded-l-xl bg-slate-100/90 border border-r-0 border-slate-200/90 text-slate-700 font-mono font-bold text-base select-none">₹</span>
                                <input type="number" 
                                       step="0.01" 
                                       min="0.01"
                                       name="price" 
                                       id="base-price" 
                                       value="{{ old('price', $product->price) }}" 
                                       oninput="calculatePrice()" 
                                       required 
                                       placeholder="0.00"
                                       class="w-full h-12 pl-3.5 pr-4 rounded-r-xl bg-slate-50/60 group-hover:bg-white focus:bg-white text-slate-900 font-mono text-base font-bold outline-none border border-slate-200/90 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-2xs">
                            </div>
                            <span class="text-[11px] text-slate-500">Gross retail price per commercial unit</span>
                            @error('price')
                                <p class="font-sans text-xs text-rose-600 mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- 2. Seasonal Discount -->
                        <div class="flex flex-col gap-2">
                            <label for="discount-pct" class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span>Seasonal Discount</span>
                                <span class="text-[10px] font-mono text-slate-400">Optional</span>
                            </label>
                            <div class="relative flex items-center group">
                                <input type="number" 
                                       id="discount-pct" 
                                       min="0"
                                       max="100"
                                       value="{{ old('discount_pct', 10) }}" 
                                       oninput="calculatePrice()" 
                                       placeholder="0"
                                       class="w-full h-12 pl-4 pr-3.5 rounded-l-xl bg-slate-50/60 group-hover:bg-white focus:bg-white text-slate-900 font-mono text-base font-bold outline-none border border-slate-200/90 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-2xs">
                                <span class="inline-flex items-center justify-center w-11 h-12 rounded-r-xl bg-slate-100/90 border border-l-0 border-slate-200/90 text-slate-700 font-mono font-bold text-sm select-none">%</span>
                            </div>
                            <span class="text-[11px] text-slate-500">Promotional price reduction applied at checkout</span>
                        </div>

                        <!-- 3. Calculated Final Price Card -->
                        <div class="flex flex-col gap-2">
                            <label class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span>Calculated Final Price</span>
                                <span class="text-[10px] font-mono text-emerald-600 font-semibold">Live Buyer Cost</span>
                            </label>
                            <div class="h-12 px-4 rounded-xl bg-gradient-to-r from-emerald-50 via-teal-50/40 to-slate-50 border border-emerald-200/80 flex items-center justify-between shadow-2xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-emerald-700 text-[18px]">price_check</span>
                                    <span class="font-mono text-base sm:text-lg font-black text-emerald-950 tracking-tight" id="final-price-display">₹{{ number_format($product->price * 0.9, 2) }}</span>
                                </div>
                                <span class="font-mono text-[11px] uppercase px-2.5 py-0.5 rounded-md bg-emerald-600 text-white font-bold shadow-2xs tracking-wider" id="price-savings-display">SAVED ₹{{ round($product->price * 0.1) }}</span>
                            </div>
                            <span class="text-[11px] text-slate-500">Net price visible to customers in storefront catalog</span>
                        </div>
                    </div>

                    <!-- Commercial Unit of Measurement (UoM) Cards (6 types) -->
                    <div class="flex flex-col gap-2.5 pt-2">
                        <div class="flex items-center justify-between">
                            <label class="font-sans text-xs font-bold text-slate-800 flex items-center gap-1">
                                <span>Commercial Unit of Measurement (UoM)</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <span class="font-mono text-[11px] text-slate-400">APMC Standardized Units</span>
                        </div>
                        <input type="hidden" name="unit_type" id="unit-type-input" value="{{ old('unit_type', $currentUnit) }}">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5" id="unit-selector-group">
                            @php
                                $units = [
                                    'kg'     => ['label' => 'Kilogram', 'desc' => 'Weight Metric', 'icon' => 'scale'],
                                    'dozen'  => ['label' => '12 Units', 'desc' => 'Fixed Count', 'icon' => 'grid_view'],
                                    'bundle' => ['label' => 'Bound Pack', 'desc' => 'Tied Stems', 'icon' => 'view_stream'],
                                    'litre'  => ['label' => 'Volume', 'desc' => 'Liquid Metric', 'icon' => 'water_drop'],
                                    'piece'  => ['label' => 'Per Item', 'desc' => 'Single Unit', 'icon' => 'sell'],
                                    'pack'   => ['label' => 'Boxed', 'desc' => 'Crated Box', 'icon' => 'inventory_2'],
                                ];
                                $selectedUnit = old('unit_type', $currentUnit);
                            @endphp
                            @foreach($units as $unitKey => $uInfo)
                                @php $isActive = ($selectedUnit === $unitKey); @endphp
                                <button type="button" 
                                        onclick="selectUnit(this, '{{ $unitKey }}')" 
                                        data-unit="{{ $unitKey }}"
                                        class="unit-btn {{ $isActive ? 'active bg-slate-900 text-white border-slate-900 shadow-md ring-2 ring-amber-400 ring-offset-2 ring-offset-white' : 'bg-slate-50/80 hover:bg-white text-slate-700 hover:text-slate-900 border-slate-200/90 hover:border-slate-300' }} border rounded-xl p-3 flex flex-col items-center justify-center gap-1 transition-all cursor-pointer group text-center">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors {{ $isActive ? 'bg-white/10' : 'bg-slate-200/50 group-hover:bg-amber-50' }}">
                                        <span class="material-symbols-outlined unit-icon text-[18px] transition-colors {{ $isActive ? 'text-amber-400' : 'text-slate-500 group-hover:text-amber-600' }}">{{ $uInfo['icon'] }}</span>
                                    </div>
                                    <span class="font-mono text-sm font-bold tracking-tight">{{ $unitKey }}</span>
                                    <span class="unit-sub text-[9px] uppercase tracking-wider font-semibold transition-colors {{ $isActive ? 'text-slate-300' : 'text-slate-400' }}">{{ $uInfo['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                        @error('unit_type')
                            <p class="font-sans text-xs text-rose-600 mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Live Listing Ribbon -->
                    <div class="p-4 rounded-xl bg-gradient-to-r from-amber-50/90 via-amber-50/40 to-slate-50 border border-amber-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-amber-500/10 border border-amber-400/40 flex items-center justify-center text-amber-700 shadow-2xs">
                                <span class="material-symbols-outlined text-[20px]">sell</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-sans text-xs font-semibold text-slate-700">Listing displays to buyers as:</span>
                                <span class="font-mono text-sm font-black text-amber-950 bg-white/90 px-3 py-1 rounded-lg border border-amber-300/80 shadow-2xs" id="price-unit-tag">₹{{ number_format($product->price * 0.9, 0) }} / {{ $selectedUnit }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/80 border border-slate-200/70 text-slate-600 text-xs shadow-2xs self-start sm:self-auto">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="font-mono text-[11px] font-semibold">Taxes included (Mandatory APMC GST 0% Exempt)</span>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Inventory & Safety Thresholds -->
                <section class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/90 flex flex-col gap-6 transition-all hover:shadow-sm">
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-700 shadow-2xs">
                                <span class="material-symbols-outlined text-[20px]">warehouse</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider">SECTION 03</span>
                                    <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200/60 font-mono">Logistics &amp; Buffer</span>
                                </div>
                                <h2 class="font-heading text-lg font-bold text-slate-900 mt-0.5">Inventory &amp; Stock Buffer</h2>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50/80 border border-emerald-200/60 text-emerald-800 text-xs font-semibold shadow-2xs">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">sync_alt</span>
                            <span class="font-mono text-[10px] uppercase tracking-wider">Automated Depletion Engine</span>
                        </div>
                    </div>

                    <!-- 2-Column Stock Input Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <!-- 1. Available Stock -->
                        <div class="flex flex-col gap-2">
                            <label for="stock-qty" class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span class="flex items-center gap-1">
                                    Current Available Stock Quantity <span class="text-rose-500">*</span>
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">Total Units</span>
                            </label>
                            <div class="relative flex items-center group">
                                <input type="number" 
                                       name="stock" 
                                       id="stock-qty" 
                                       value="{{ old('stock', $product->stock) }}" 
                                       oninput="updateStockStatus()" 
                                       required 
                                       min="0"
                                       step="any"
                                       placeholder="0"
                                       class="w-full h-12 pl-4 pr-16 rounded-xl bg-slate-50/60 group-hover:bg-white focus:bg-white text-slate-900 font-mono text-base font-bold outline-none border border-slate-200/90 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-2xs">
                                <span class="absolute right-2.5 top-2.5 h-7 px-2 rounded-lg bg-slate-200/70 border border-slate-300/80 flex items-center font-mono text-xs font-bold text-slate-700 unit-label-text select-none uppercase">{{ old('unit_type', $currentUnit) }}</span>
                            </div>
                            <span class="text-[11px] text-slate-500">Live warehouse/farm stock available for instant order dispatch</span>
                            @error('stock')
                                <p class="font-sans text-xs text-rose-600 mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- 2. Minimum Stock Alert Threshold -->
                        <div class="flex flex-col gap-2">
                            <label for="threshold-qty" class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span>Minimum Stock Alert Threshold</span>
                                <span class="text-[10px] font-mono text-slate-400">Low Stock Trigger</span>
                            </label>
                            <div class="relative flex items-center group">
                                <input type="number" 
                                       name="low_stock_threshold" 
                                       id="threshold-qty" 
                                       value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 10) }}" 
                                       oninput="updateStockStatus()" 
                                       min="0"
                                       step="any"
                                       placeholder="10"
                                       class="w-full h-12 pl-4 pr-16 rounded-xl bg-slate-50/60 group-hover:bg-white focus:bg-white text-slate-900 font-mono text-base font-bold outline-none border border-slate-200/90 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-2xs">
                                <span class="absolute right-2.5 top-2.5 h-7 px-2 rounded-lg bg-slate-200/70 border border-slate-300/80 flex items-center font-mono text-xs font-bold text-slate-700 unit-label-text select-none uppercase">{{ old('unit_type', $currentUnit) }}</span>
                            </div>
                            <span class="text-[11px] text-slate-500">Automatic SMS/dashboard notification sent when stock hits this buffer</span>
                            @error('low_stock_threshold')
                                <p class="font-sans text-xs text-rose-600 mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Dynamic Stock Status Card with Gauge Visual -->
                    <div class="p-4 sm:p-5 rounded-xl bg-slate-50/90 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-2xs">
                        <div class="flex items-center gap-4">
                            <div class="relative w-14 h-14 flex-shrink-0 flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-slate-200" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.5"></path>
                                    <path class="text-emerald-600 transition-all duration-300" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" id="stock-gauge" stroke="currentColor" stroke-dasharray="78, 100" stroke-linecap="round" stroke-width="3.5"></path>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center font-mono text-xs font-black text-slate-800" id="stock-gauge-percent">
                                    78%
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="font-heading text-sm font-bold text-slate-900" id="stock-status-title">Status: {{ $product->stock > 10 ? 'In Stock' : 'Low Stock Warning' }}</span>
                                    <span id="stock-status-badge" class="font-mono text-[10px] px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold uppercase border border-emerald-200/80">HEALTHY LEVEL</span>
                                </div>
                                <p class="font-sans text-xs text-slate-600 mt-1 flex items-center gap-1.5" id="stock-buffer-note">
                                    <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                                    <span>Safe buffer: +{{ max(0, $product->stock - 10) }} {{ $currentUnit }} over minimum alert threshold.</span>
                                </p>
                            </div>
                        </div>
                        <div class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200/80 text-xs text-slate-600 shadow-2xs max-w-xs">
                            <span class="material-symbols-outlined text-amber-500 text-[18px] flex-shrink-0">shield</span>
                            <span class="text-[11px] leading-relaxed">Safety buffer prevents overselling during high-velocity live wholesale auctions.</span>
                        </div>
                    </div>
                </section>

                <!-- Section 4: Farmer-Specific Agronomic Ledger (Visible only for Organic Food category) -->
                <section id="agronomic-ledger-section" class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/90 flex flex-col gap-6 transition-all hover:shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-700 shadow-2xs">
                                <span class="material-symbols-outlined text-[20px]">eco</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider">SECTION 04</span>
                                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 font-mono">Organic Produce Track</span>
                                </div>
                                <h2 class="font-heading text-lg font-bold text-slate-900 mt-0.5">Farmer-Specific Agronomic Ledger</h2>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50/80 border border-emerald-200/60 text-emerald-800 text-xs font-semibold shadow-2xs">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">verified</span>
                            <span class="font-mono text-[10px] uppercase tracking-wider">APMC Traceability</span>
                        </div>
                    </div>

                    <div class="p-3.5 bg-amber-50/80 border border-amber-200/90 rounded-xl flex items-center gap-2.5 text-amber-950 text-xs shadow-2xs">
                        <span class="material-symbols-outlined text-amber-600 text-[18px]">verified</span>
                        <span>This Agronomic Ledger is active specifically for the <strong>Organic Food</strong> category to ensure farm-gate traceability and harvest fresh delivery.</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div class="flex flex-col gap-2">
                            <label for="farm-origin" class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span>Farm Origin &amp; Plot Code</span>
                                <span class="text-[10px] font-mono text-slate-400">Harvest Geo-Tag</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[18px] pointer-events-none">pin_drop</span>
                                <input type="text" 
                                       name="farm_origin" 
                                       id="farm-origin" 
                                       value="{{ old('farm_origin', $product->farm_origin) }}" 
                                       placeholder="e.g. Green Valley Farm, Ratnagiri"
                                       class="w-full h-12 pl-10 pr-4 rounded-xl bg-slate-50/60 hover:bg-white focus:bg-white text-slate-900 font-sans text-xs outline-none border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-2xs">
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="harvest-date" class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span>Harvest Date</span>
                                <span class="text-[10px] font-mono text-slate-400">Field Cutting</span>
                            </label>
                            <input type="date" 
                                   name="harvest_date" 
                                   id="harvest-date" 
                                   value="{{ old('harvest_date', $product->harvest_date?->format('Y-m-d')) }}" 
                                   class="w-full h-12 px-4 rounded-xl bg-slate-50/60 hover:bg-white focus:bg-white text-slate-900 font-mono text-xs outline-none border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div class="flex flex-col gap-2">
                            <label for="expiry-window" class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span>Peak Freshness Shelf Window</span>
                                <span class="text-[10px] font-mono text-emerald-600 font-semibold">Decay Guard</span>
                            </label>
                            <div class="relative">
                                <select name="expiry_days" 
                                        id="expiry-window" 
                                        class="w-full h-12 pl-4 pr-10 rounded-xl bg-slate-50/60 hover:bg-white focus:bg-white text-slate-900 font-sans text-xs outline-none border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-2xs appearance-none cursor-pointer">
                                    @foreach([3, 5, 7, 10, 14] as $days)
                                    <option value="{{ $days }}" {{ old('expiry_days', $product->expiry_days) == $days ? 'selected' : '' }}>
                                        {{ $days }} Days from harvest
                                    </option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined pointer-events-none absolute right-3.5 top-3.5 text-slate-400 text-[18px]">schedule</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-sans text-xs font-bold text-slate-800 flex items-center justify-between">
                                <span>Quality Validation Index</span>
                                <span class="text-[10px] font-mono text-slate-400">Spectrometry</span>
                            </label>
                            <div class="h-12 px-4 rounded-xl bg-slate-50/80 border border-slate-200/90 flex items-center justify-between shadow-2xs">
                                <span class="font-sans text-xs text-slate-700 font-medium">Brix Sweetness Score</span>
                                <span class="font-mono text-xs px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold border border-emerald-200/80">19.4° Bx</span>
                            </div>
                        </div>
                    </div>

                    <!-- Automated Stale Listing Options -->
                    <div class="flex flex-col gap-3 p-4 rounded-xl bg-slate-50/80 border border-slate-200/80">
                        <span class="font-heading text-xs font-bold text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-[18px]">verified_user</span>
                            Automated Freshness Guardrails
                        </span>
                        <div class="flex flex-col gap-3 mt-0.5">
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="is_perishable" value="1" {{ old('is_perishable', $product->is_perishable) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded text-amber-500 focus:ring-amber-500/20 cursor-pointer accent-amber-500">
                                <div class="flex flex-col">
                                    <span class="font-sans text-xs font-semibold text-slate-800">Automatically flag as STALE when within 24 hours of expiry window</span>
                                    <span class="font-sans text-[11px] text-slate-500">Sends urgent price markdown suggestions or wholesale auction prompt.</span>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="auto_hide_expired" value="1" {{ old('auto_hide_expired', $product->auto_hide_expired) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded text-amber-500 focus:ring-amber-500/20 cursor-pointer accent-amber-500">
                                <div class="flex flex-col">
                                    <span class="font-sans text-xs font-semibold text-slate-800">Automatically hide listing when harvest expiry window lapses</span>
                                    <span class="font-sans text-[11px] text-slate-500">Protects farmer trust score against stale produce disputes.</span>
                                </div>
                            </label>
                        </div>
                        <div class="pt-2 flex items-center gap-2 text-slate-500 border-t border-slate-200/70">
                            <span class="material-symbols-outlined text-[16px] text-amber-600">info</span>
                            <span class="font-mono text-[10px]">Note: Bazaario never silently deletes products; expired goods will be moved to Hidden / Restock archive.</span>
                        </div>
                    </div>
                </section>
            </div>

            <!-- RIGHT COLUMN / ASSET & SIMULATION PANEL (5 cols) -->
            <div class="lg:col-span-5 flex flex-col gap-6">

                <!-- Section 5: Media & Visuals -->
                <section class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/90 flex flex-col gap-6 transition-all hover:shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]">photo_library</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider">SECTION 05</span>
                                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 font-mono">Catalog Assets</span>
                                </div>
                                <h2 class="font-heading text-lg font-bold text-slate-900 mt-0.5">Media &amp; Visuals</h2>
                            </div>
                        </div>
                        <span id="selected-photos-count" class="font-mono text-xs text-slate-500 font-semibold bg-slate-50 px-3 py-1 rounded-full border border-slate-200/80">Max 6 photos</span>
                    </div>

                    <!-- Hidden input to store primary image index for new uploads -->
                    <input type="hidden" name="primary_image_index" id="primary-image-index" value="0">

                    <!-- Existing Gallery Stream -->
                    <div class="flex flex-col gap-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-xs font-bold text-slate-800">Existing Gallery ({{ $product->images->count() }})</span>
                            <span class="font-mono text-[10px] text-slate-400">Select Primary or Delete</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2.5">
                            @forelse($product->images as $img)
                                <div class="relative group rounded-xl overflow-hidden bg-slate-100 h-24 border-2 transition-all {{ $img->is_primary ? 'border-amber-500 shadow-md ring-2 ring-amber-400/30' : 'border-slate-200 hover:border-slate-300' }}" id="existing-img-card-{{ $img->id }}">
                                    <img src="{{ $img->url }}" alt="Product Photo" class="w-full h-full object-cover">
                                    
                                    @if($img->is_primary)
                                        <span class="absolute top-1.5 left-1.5 font-mono text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-amber-500 text-slate-950 shadow-sm flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[10px]">star</span> Primary
                                        </span>
                                    @else
                                        <label class="absolute top-1.5 left-1.5 font-mono text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-black/60 hover:bg-slate-900 text-white shadow-sm transition-colors cursor-pointer opacity-90 group-hover:opacity-100 flex items-center gap-1">
                                            <input type="radio" name="primary_image_id" value="{{ $img->id }}" class="hidden" onchange="setExistingPrimary(this, '{{ $img->url }}')">
                                            Set Primary
                                        </label>
                                    @endif

                                    <button type="button" onclick="toggleExistingDelete({{ $img->id }})" class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-black/70 hover:bg-rose-600 text-white flex items-center justify-center shadow-sm transition-colors cursor-pointer opacity-80 group-hover:opacity-100" title="Delete Photo">
                                        <span class="material-symbols-outlined text-[12px]">delete</span>
                                    </button>
                                    <input type="checkbox" name="delete_image_ids[]" id="delete-input-{{ $img->id }}" value="{{ $img->id }}" class="hidden">
                                </div>
                            @empty
                                <div class="col-span-3 p-4 rounded-xl bg-slate-50 border border-dashed border-slate-300 text-center text-xs text-slate-500">
                                    No gallery images uploaded yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Dropzone for New Photos -->
                    <div id="product-dropzone" class="relative p-6 sm:p-7 rounded-2xl bg-slate-50/70 hover:bg-slate-50 flex flex-col items-center justify-center text-center transition-all cursor-pointer group border-2 border-dashed border-slate-300 hover:border-amber-400">
                        <input type="file" id="product-images-input" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/jpg" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-200/90 flex items-center justify-center shadow-2xs mb-3 group-hover:scale-110 text-amber-600 transition-transform">
                            <span class="material-symbols-outlined text-[24px]">cloud_upload</span>
                        </div>
                        <span class="font-heading text-sm font-bold text-slate-900">Upload additional harvest photos</span>
                        <span class="font-sans text-xs text-slate-500 mt-1">or <span class="text-amber-600 font-semibold group-hover:text-amber-700 transition-colors">browse files</span> from your local device</span>
                        <span class="font-mono text-[10px] text-slate-400 mt-2 uppercase">PNG, JPG, WEBP up to 10MB each</span>
                    </div>

                    <!-- Newly Selected Photos Preview -->
                    <div id="new-photos-section" class="hidden flex-col gap-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-xs font-bold text-slate-800">New Photos (Pending Save)</span>
                            <span id="new-photos-count" class="font-mono text-[10px] text-amber-700 font-bold"></span>
                        </div>
                        <div id="gallery-preview-grid" class="grid grid-cols-3 gap-2.5 min-h-[96px]"></div>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600 font-mono">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-emerald-600">check_circle</span> High-res inspection pass
                        </span>
                        <span class="text-slate-400">sRGB color profile auto-optimized</span>
                    </div>
                </section>

                <!-- Section 6: Live Buyer View Simulation Card (Sticky) -->
                <section class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/90 flex flex-col gap-5 sticky top-20">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[10px] px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/60 font-bold uppercase tracking-wider">PREVIEW</span>
                            <h2 class="font-heading text-lg font-bold text-slate-900">Buyer View Simulation</h2>
                        </div>
                        <span class="flex items-center gap-1.5 font-mono text-[10px] text-emerald-700 font-bold uppercase bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span> Live Synced
                        </span>
                    </div>

                    <div class="w-full rounded-2xl overflow-hidden bg-white shadow-md hover:shadow-lg transition-all flex flex-col border border-slate-200/90">
                        <div class="relative w-full h-44 bg-slate-100 overflow-hidden">
                            <img id="preview-card-image" src="{{ $product->main_image_url }}" alt="Preview" class="w-full h-full object-cover transition-opacity duration-200">
                            <div class="absolute top-2.5 left-2.5 flex flex-wrap gap-1.5" id="preview-organic-badges">
                                <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-700 text-white shadow-xs uppercase">Harvested Item</span>
                                <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-md bg-white text-slate-800 shadow-xs uppercase flex items-center gap-1 border border-slate-200">
                                    <span class="material-symbols-outlined text-[13px] text-emerald-600">verified</span> GI Tagged
                                </span>
                            </div>
                            <div class="absolute top-2.5 left-2.5 flex flex-wrap gap-1.5 hidden" id="preview-standard-badges">
                                <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-md bg-white text-slate-800 shadow-xs uppercase flex items-center gap-1 border border-slate-200">
                                    <span class="material-symbols-outlined text-[13px] text-amber-600">verified</span> Verified SKU
                                </span>
                            </div>
                            <div class="absolute bottom-2.5 right-2.5 px-2.5 py-1 rounded-lg bg-slate-900/90 backdrop-blur-xs text-amber-300 font-mono text-[10px] font-semibold flex items-center gap-1 shadow-md border border-white/10">
                                <span class="material-symbols-outlined text-[13px] text-amber-400">shield</span>
                                <span>Score: 94/100</span>
                            </div>
                        </div>

                        <div class="p-4 flex flex-col gap-2">
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span class="font-mono text-[10px] uppercase tracking-wider font-semibold text-slate-600">{{ $sellerProfile?->shop_name ?? 'Green Valley Farm' }}</span>
                                <span class="flex items-center gap-1 font-mono text-[11px] text-slate-600">
                                    <span class="material-symbols-outlined text-[14px] text-amber-500 fill-1">star</span>
                                    {{ $product->average_rating ?? '4.9' }} ({{ $product->total_reviews ?? 12 }} reviews)
                                </span>
                            </div>
                            <h3 class="font-heading text-base text-slate-900 font-bold leading-tight line-clamp-1" id="preview-card-title">
                                {{ $product->name }}
                            </h3>
                            <p class="font-sans text-xs text-slate-500 line-clamp-2">
                                {{ $product->description }}
                            </p>

                            <div class="flex items-baseline justify-between pt-2">
                                <div class="flex items-baseline gap-2">
                                    <span class="font-heading text-lg font-black text-slate-900" id="preview-final-price">₹{{ number_format($product->price * 0.9, 0) }}</span>
                                    <span class="font-mono text-xs text-slate-400 line-through" id="preview-base-price">₹{{ number_format($product->price, 0) }}</span>
                                    <span class="font-mono text-xs font-bold text-slate-600" id="preview-unit-suffix">/ {{ $currentUnit }}</span>
                                </div>
                                <span class="font-mono text-[10px] uppercase font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                    10% OFF
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" name="status" value="active" class="flex-1 h-12 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold flex items-center justify-center gap-2 shadow-xs transition-all">
                            <span class="material-symbols-outlined text-[18px]">check</span>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    let currentUnit = '{{ old('unit_type', $currentUnit) }}';

    function calculatePrice() {
        const baseInput = document.getElementById('base-price');
        const discountInput = document.getElementById('discount-pct');
        const finalDisplay = document.getElementById('final-price-display');
        const priceUnitTag = document.getElementById('price-unit-tag');
        const savingsDisplay = document.getElementById('price-savings-display');
        const previewFinal = document.getElementById('preview-final-price');
        const previewBase = document.getElementById('preview-base-price');

        const base = parseFloat(baseInput ? baseInput.value : 0) || 0;
        const discount = Math.max(0, Math.min(100, parseFloat(discountInput ? discountInput.value : 0) || 0));
        const savings = base * (discount / 100);
        const final = Math.max(0, base - savings);

        if (finalDisplay) finalDisplay.innerText = '₹' + final.toFixed(2);
        if (savingsDisplay) {
            if (discount > 0) {
                savingsDisplay.innerText = 'SAVED ₹' + Math.round(savings);
                savingsDisplay.className = 'font-mono text-[11px] uppercase px-2.5 py-0.5 rounded-md bg-emerald-600 text-white font-bold shadow-2xs tracking-wider';
            } else {
                savingsDisplay.innerText = 'FULL PRICE';
                savingsDisplay.className = 'font-mono text-[11px] uppercase px-2.5 py-0.5 rounded-md bg-slate-200 text-slate-700 font-bold';
            }
        }
        if (priceUnitTag) priceUnitTag.innerText = '₹' + Math.round(final) + ' / ' + currentUnit;
        if (previewFinal) previewFinal.innerText = '₹' + Math.round(final);
        if (previewBase) previewBase.innerText = '₹' + Math.round(base);
    }

    function selectUnit(element, unit) {
        document.querySelectorAll('.unit-btn').forEach(btn => {
            btn.classList.remove('bg-slate-900', 'text-white', 'border-slate-900', 'shadow-md', 'ring-2', 'ring-amber-400', 'ring-offset-2', 'ring-offset-white', 'active');
            btn.classList.add('bg-slate-50/80', 'text-slate-700', 'border-slate-200/90');
            const icon = btn.querySelector('.unit-icon');
            if (icon) {
                icon.classList.remove('text-amber-400', 'group-hover:text-amber-600');
                icon.classList.add('text-slate-500');
            }
            const iconWrap = btn.querySelector('div');
            if (iconWrap) {
                iconWrap.classList.remove('bg-white/10');
                iconWrap.classList.add('bg-slate-200/50');
            }
            const sub = btn.querySelector('.unit-sub');
            if (sub) {
                sub.classList.remove('text-slate-300');
                sub.classList.add('text-slate-400');
            }
        });

        element.classList.remove('bg-slate-50/80', 'text-slate-700', 'border-slate-200/90');
        element.classList.add('bg-slate-900', 'text-white', 'border-slate-900', 'shadow-md', 'ring-2', 'ring-amber-400', 'ring-offset-2', 'ring-offset-white', 'active');
        const activeIcon = element.querySelector('.unit-icon');
        if (activeIcon) {
            activeIcon.classList.remove('text-slate-500');
            activeIcon.classList.add('text-amber-400');
        }
        const activeIconWrap = element.querySelector('div');
        if (activeIconWrap) {
            activeIconWrap.classList.remove('bg-slate-200/50');
            activeIconWrap.classList.add('bg-white/10');
        }
        const activeSub = element.querySelector('.unit-sub');
        if (activeSub) {
            activeSub.classList.remove('text-slate-400');
            activeSub.classList.add('text-slate-300');
        }

        currentUnit = unit;
        const unitInput = document.getElementById('unit-type-input');
        if (unitInput) unitInput.value = unit;

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
        if (charCount) {
            charCount.innerText = text.length + ' / 80';
            if (text.length > 80) {
                charCount.className = 'font-mono text-[11px] text-amber-600 font-bold';
            } else {
                charCount.className = 'font-mono text-[11px] text-slate-400';
            }
        }
    }

    function updateDescCharCount() {
        const descEl = document.getElementById('product-desc');
        const countEl = document.getElementById('desc-char-count');
        if (descEl && countEl) {
            countEl.innerText = descEl.value.length + ' characters';
        }
    }

    function formatDescText(prefix, suffix) {
        const textarea = document.getElementById('product-desc');
        if (!textarea) return;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selected = textarea.value.substring(start, end);
        const replacement = prefix + (selected || 'text') + suffix;
        textarea.setRangeText(replacement, start, end, 'select');
        textarea.focus();
        updateDescCharCount();
    }

    function insertDescBullet() {
        const textarea = document.getElementById('product-desc');
        if (!textarea) return;
        const start = textarea.selectionStart;
        const newline = start > 0 && textarea.value[start - 1] !== '\n' ? '\n' : '';
        const bullet = newline + '• ';
        textarea.setRangeText(bullet, start, start, 'end');
        textarea.focus();
        updateDescCharCount();
    }

    function insertDescTag(tag) {
        const textarea = document.getElementById('product-desc');
        if (!textarea) return;
        const start = textarea.selectionStart;
        const space = start > 0 && textarea.value[start - 1] !== ' ' && textarea.value[start - 1] !== '\n' ? ' ' : '';
        const textToInsert = space + tag + ' ';
        textarea.setRangeText(textToInsert, start, start, 'end');
        textarea.focus();
        updateDescCharCount();
    }

    function updateStockStatus() {
        const stockInput = document.getElementById('stock-qty');
        const thresholdInput = document.getElementById('threshold-qty');
        const stockQty = parseFloat(stockInput ? stockInput.value : 0) || 0;
        const thresholdQty = parseFloat(thresholdInput ? thresholdInput.value : 0) || 0;
        const statusTitle = document.getElementById('stock-status-title');
        const bufferNote = document.getElementById('stock-buffer-note');
        const gauge = document.getElementById('stock-gauge');
        const gaugePercent = document.getElementById('stock-gauge-percent');
        const statusBadge = document.getElementById('stock-status-badge');

        let percent = 0;
        if (stockQty > 0) {
            const maxRef = Math.max(thresholdQty * 2, stockQty, 1);
            percent = Math.min(100, Math.max(5, Math.round((stockQty / maxRef) * 100)));
        }
        if (gaugePercent) gaugePercent.innerText = percent + '%';
        if (gauge) gauge.setAttribute('stroke-dasharray', percent + ', 100');

        const diff = stockQty - thresholdQty;
        if (stockQty <= 0) {
            if (statusTitle) statusTitle.innerText = 'Status: Out of Stock';
            if (statusBadge) {
                statusBadge.innerText = 'OUT OF STOCK';
                statusBadge.className = 'font-mono text-[10px] px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold uppercase border border-rose-200';
            }
            if (bufferNote) bufferNote.innerHTML = '<span class="material-symbols-outlined text-[16px] text-rose-600">cancel</span><span>Critical stockout: Listing will be paused until inventory is restocked.</span>';
            if (gauge) gauge.setAttribute('class', 'text-rose-600 transition-all duration-300');
        } else if (diff > 0) {
            if (statusTitle) statusTitle.innerText = 'Status: In Stock';
            if (statusBadge) {
                statusBadge.innerText = 'HEALTHY LEVEL';
                statusBadge.className = 'font-mono text-[10px] px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold uppercase border border-emerald-200/80';
            }
            if (bufferNote) bufferNote.innerHTML = '<span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span><span>Safe buffer: +' + diff + ' ' + currentUnit + ' over minimum alert threshold.</span>';
            if (gauge) gauge.setAttribute('class', 'text-emerald-600 transition-all duration-300');
        } else if (diff === 0) {
            if (statusTitle) statusTitle.innerText = 'Status: At Alert Level';
            if (statusBadge) {
                statusBadge.innerText = 'REORDER SOON';
                statusBadge.className = 'font-mono text-[10px] px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold uppercase border border-amber-200/80';
            }
            if (bufferNote) bufferNote.innerHTML = '<span class="material-symbols-outlined text-[16px] text-amber-600">warning</span><span>Inventory reached exact threshold level of ' + thresholdQty + ' ' + currentUnit + '.';
            if (gauge) gauge.setAttribute('class', 'text-amber-500 transition-all duration-300');
        } else {
            if (statusTitle) statusTitle.innerText = 'Status: Low Stock Warning';
            if (statusBadge) {
                statusBadge.innerText = 'CRITICAL DEFICIT';
                statusBadge.className = 'font-mono text-[10px] px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold uppercase border border-rose-200';
            }
            if (bufferNote) bufferNote.innerHTML = '<span class="material-symbols-outlined text-[16px] text-rose-600">error</span><span>Critical shortage: ' + Math.abs(diff) + ' ' + currentUnit + ' deficit from minimum alert threshold.</span>';
            if (gauge) gauge.setAttribute('class', 'text-rose-600 transition-all duration-300');
        }
    }

    function handleCategoryChange() {
        const select = document.getElementById('category-select');
        const agronomicSection = document.getElementById('agronomic-ledger-section');
        const harvestGradeContainer = document.getElementById('harvest-grade-container');
        const categoryContainer = document.getElementById('category-container');
        const categoryHint = document.getElementById('category-tag-hint');
        const organicBadges = document.getElementById('preview-organic-badges');
        const standardBadges = document.getElementById('preview-standard-badges');

        if (!select) return;

        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption) return;

        const slug = (selectedOption.getAttribute('data-slug') || '').toLowerCase();
        const name = (selectedOption.getAttribute('data-name') || selectedOption.textContent || '').toLowerCase();

        const isOrganic = slug === 'organic-food' || slug.includes('organic') || name.includes('organic')
            || slug.includes('produce') || slug.includes('fruit') || slug.includes('vegetable') || slug.includes('veg')
            || slug.includes('dairy') || slug.includes('farm') || slug.includes('harvest') || slug.includes('crop')
            || slug.includes('grain') || slug.includes('spice') || slug.includes('food') || slug.includes('agro')
            || name.includes('fruit') || name.includes('vegetable') || name.includes('produce') || name.includes('dairy')
            || name.includes('food') || name.includes('crop') || name.includes('farm');

        if (isOrganic) {
            if (agronomicSection) {
                agronomicSection.style.display = 'flex';
                agronomicSection.querySelectorAll('input, select').forEach(el => el.disabled = false);
            }
            if (harvestGradeContainer) {
                harvestGradeContainer.style.display = 'flex';
                harvestGradeContainer.querySelectorAll('select').forEach(el => el.disabled = false);
            }
            if (categoryContainer) {
                categoryContainer.className = 'flex flex-col gap-2 transition-all duration-200';
            }
            if (categoryHint) {
                categoryHint.innerHTML = '<span class="inline-flex items-center gap-0.5 text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 text-[10px] font-bold"><span class="material-symbols-outlined text-[12px]">eco</span> Agronomic Track Active</span>';
            }
            if (organicBadges) organicBadges.classList.remove('hidden');
            if (standardBadges) standardBadges.classList.add('hidden');
        } else {
            if (agronomicSection) {
                agronomicSection.style.display = 'none';
                agronomicSection.querySelectorAll('input, select').forEach(el => el.disabled = true);
            }
            if (harvestGradeContainer) {
                harvestGradeContainer.style.display = 'none';
                harvestGradeContainer.querySelectorAll('select').forEach(el => el.disabled = true);
            }
            if (categoryContainer) {
                categoryContainer.className = 'flex flex-col gap-2 transition-all duration-200 sm:col-span-2';
            }
            if (categoryHint) {
                categoryHint.innerHTML = '<span class="inline-flex items-center gap-0.5 text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded text-[10px] font-medium">Standard Retail Item</span>';
            }
            if (organicBadges) organicBadges.classList.add('hidden');
            if (standardBadges) standardBadges.classList.remove('hidden');
        }
    }

    calculatePrice();
    updateStockStatus();
    handleCategoryChange();
    updateDescCharCount();

    // Existing Images Management
    window.toggleExistingDelete = function(imgId) {
        const card = document.getElementById('existing-img-card-' + imgId);
        const input = document.getElementById('delete-input-' + imgId);
        if (!card || !input) return;

        input.checked = !input.checked;
        if (input.checked) {
            card.classList.add('opacity-40', 'grayscale', 'ring-2', 'ring-rose-500');
            const delBadge = document.createElement('div');
            delBadge.id = 'del-badge-' + imgId;
            delBadge.className = 'absolute inset-0 bg-rose-950/70 flex flex-col items-center justify-center text-white text-[10px] font-bold';
            delBadge.innerHTML = '<span class="material-symbols-outlined text-[18px]">delete</span>Marked for deletion';
            card.appendChild(delBadge);
        } else {
            card.classList.remove('opacity-40', 'grayscale', 'ring-2', 'ring-rose-500');
            const delBadge = document.getElementById('del-badge-' + imgId);
            if (delBadge) delBadge.remove();
        }
    };

    window.setExistingPrimary = function(radio, url) {
        const buyerImg = document.getElementById('preview-card-image');
        if (buyerImg && url) {
            buyerImg.src = url;
        }
    };

    // New Image Upload & Gallery Preview Controller
    const fileInput = document.getElementById('product-images-input');
    const dropzone = document.getElementById('product-dropzone');
    const primaryInput = document.getElementById('primary-image-index');
    const galleryGrid = document.getElementById('gallery-preview-grid');
    const newPhotosSection = document.getElementById('new-photos-section');
    const newPhotosCountEl = document.getElementById('new-photos-count');
    const buyerCardImg = document.getElementById('preview-card-image');

    let selectedFiles = []; // Array of File objects
    let primaryFileIndex = 0;

    function renderNewGalleryPreviews() {
        if (!galleryGrid || !newPhotosSection) return;
        galleryGrid.innerHTML = '';

        if (selectedFiles.length === 0) {
            newPhotosSection.classList.add('hidden');
            newPhotosSection.classList.remove('flex');
            return;
        }

        newPhotosSection.classList.remove('hidden');
        newPhotosSection.classList.add('flex');
        if (newPhotosCountEl) newPhotosCountEl.innerText = `${selectedFiles.length} photo(s) selected`;

        selectedFiles.forEach((file, index) => {
            const objectUrl = URL.createObjectURL(file);

            const tile = document.createElement('div');
            tile.className = 'relative group rounded-xl overflow-hidden bg-slate-100 h-24 border-2 border-amber-500/50 shadow-xs transition-all';

            tile.innerHTML = `
                <img src="${objectUrl}" alt="New Photo ${index + 1}" class="w-full h-full object-cover">
                
                <span class="absolute top-1.5 left-1.5 font-mono text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-amber-500 text-white shadow-sm flex items-center gap-0.5">
                    New
                </span>

                <button type="button" onclick="removeNewImage(${index})" class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-black/70 hover:bg-rose-600 text-white flex items-center justify-center shadow-sm transition-colors cursor-pointer opacity-80 group-hover:opacity-100" title="Remove photo">
                    <span class="material-symbols-outlined text-[12px]">close</span>
                </button>

                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent p-1 px-1.5 text-[9px] font-mono text-white truncate">
                    ${file.name}
                </div>
            `;

            galleryGrid.appendChild(tile);
        });
    }

    function syncInputFiles() {
        if (!fileInput) return;
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function handleFilesAdded(newFiles) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        let addedCount = 0;

        for (let i = 0; i < newFiles.length; i++) {
            const file = newFiles[i];

            if (selectedFiles.length >= 6) {
                showToast('Maximum 6 new photos allowed.', 'warning');
                break;
            }

            if (!allowedTypes.includes(file.type)) {
                showToast(`File "${file.name}" is not a supported image format. Please select PNG, JPG, or WEBP.`, 'error');
                continue;
            }

            if (file.size > 10 * 1024 * 1024) {
                showToast(`File "${file.name}" exceeds the 10MB limit.`, 'error');
                continue;
            }

            selectedFiles.push(file);
            addedCount++;
        }

        if (addedCount > 0) {
            syncInputFiles();
            renderNewGalleryPreviews();
            // Preview the latest added image in Buyer Simulation
            if (buyerCardImg && selectedFiles.length > 0) {
                buyerCardImg.src = URL.createObjectURL(selectedFiles[0]);
            }
        }
    }

    window.removeNewImage = function(index) {
        selectedFiles.splice(index, 1);
        syncInputFiles();
        renderNewGalleryPreviews();
    };

    if (fileInput) {
        fileInput.addEventListener('click', () => {
            fileInput.value = '';
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files.length > 0) {
                handleFilesAdded(Array.from(e.target.files));
            }
        });
    }

    // Ensure all selected files are bound to the form payload upon submit
    const editForm = document.getElementById('editProductForm');
    if (editForm) {
        editForm.addEventListener('submit', (e) => {
            syncInputFiles();
        });
    }

    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-amber-500', 'bg-amber-50/40', 'ring-2', 'ring-amber-500/20');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-amber-500', 'bg-amber-50/40', 'ring-2', 'ring-amber-500/20');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                handleFilesAdded(dt.files);
            }
        }, false);
    }
</script>
@endpush
@endsection
