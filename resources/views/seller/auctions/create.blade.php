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
            'category' => $p->category?->name ?? 'General Lot',
            'description' => \Illuminate\Support\Str::limit($p->description ?? 'Verified consignment lot ready for wholesale spot-trading.', 120),
            'image' => sellerCreateThumb($p),
        ];
    })->values();

    $categoriesList = $categories ?? \App\Models\Category::where('status', 'active')->orderBy('name')->get();
    if ($categoriesList->isEmpty()) {
        $categoriesList = collect([
            (object) ['id' => 1, 'name' => 'Artisan & Handmade Crafts', 'slug' => 'artisan-craft'],
            (object) ['id' => 2, 'name' => 'Books & Fine Stationery', 'slug' => 'books-stationery'],
            (object) ['id' => 3, 'name' => 'Dairy & Farm Items', 'slug' => 'dairy-farm-items'],
            (object) ['id' => 4, 'name' => 'Electronics & Gadgets', 'slug' => 'electronics'],
            (object) ['id' => 5, 'name' => 'Fashion & Apparel', 'slug' => 'fashion'],
            (object) ['id' => 6, 'name' => 'Fresh Fruits', 'slug' => 'fresh-fruits'],
            (object) ['id' => 7, 'name' => 'Fresh Vegetables', 'slug' => 'fresh-vegetables'],
            (object) ['id' => 8, 'name' => 'Grains & Cereals', 'slug' => 'grains-cereals'],
            (object) ['id' => 9, 'name' => 'Home & Living', 'slug' => 'home-living'],
            (object) ['id' => 10, 'name' => 'Jewelry & Gemstones', 'slug' => 'jewelry-gemstones'],
            (object) ['id' => 11, 'name' => 'Luxury Horology & Watches', 'slug' => 'luxury-horology'],
            (object) ['id' => 12, 'name' => 'Organic Produce', 'slug' => 'organic-produce'],
            (object) ['id' => 13, 'name' => 'Rare Collectibles & Antiques', 'slug' => 'collectibles'],
            (object) ['id' => 14, 'name' => 'Spices & Herbs', 'slug' => 'spices-herbs'],
        ]);
    }

    $firstCategory = $categoriesList->first();
    $defaultCategoryId = old('manual_category_id', $firstCategory?->id ?? 1);
    $defaultCategoryName = old('manual_category_name', $firstCategory?->name ?? 'General Category');

    $defaultProductId = old('product_id', $productsList->first()?->id ?? '');
    $defaultStartsAt = old('starts_at', now()->format('Y-m-d\TH:i'));
    $defaultEndsAt = old('ends_at', now()->addDay()->format('Y-m-d\TH:i'));
    $initialProductMode = old('product_source', $productsList->isNotEmpty() ? 'existing' : 'manual');
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    productMode: '{{ $initialProductMode }}',
    products: {{ Js::from($productsData) }},
    selectedProductId: '{{ $defaultProductId }}',
    manualName: '{{ addslashes(old('manual_name', '')) }}',
    manualCategoryId: '{{ $defaultCategoryId }}',
    manualCategoryName: '{{ addslashes($defaultCategoryName) }}',
    manualStock: '{{ old('manual_stock', '') }}',
    manualUnit: '{{ old('manual_unit_type', 'unit') }}',
    manualPrice: '{{ old('manual_price', '') }}',
    manualDescription: '{{ addslashes(old('manual_description', '')) }}',
    manualFarmOrigin: '{{ addslashes(old('manual_farm_origin', $profile?->city ? $profile->city . ' Consignment' : '')) }}',
    manualHarvestDate: '{{ old('manual_harvest_date', '') }}',
    manualImagePreview: null,
    startPrice: '{{ old('starting_price', '') }}',
    reservePrice: '{{ old('reserve_price', '') }}',
    minIncrement: '{{ old('minimum_increment', '') }}',
    startsAt: '{{ $defaultStartsAt }}',
    endsAt: '{{ $defaultEndsAt }}',
    
    get currentProduct() {
        if (this.productMode === 'manual') {
            return {
                id: 'manual',
                name: (this.manualName && this.manualName.trim()) ? this.manualName.trim() : 'New Wholesale Consignment',
                category: this.manualCategoryName || 'General Category',
                stock: this.manualStock || '',
                unit: this.manualUnit || 'unit',
                price: parseFloat(this.manualPrice || this.startPrice || 0),
                description: (this.manualDescription && this.manualDescription.trim()) ? this.manualDescription.trim() : 'Verified consignment lot ready for spot floor allocation.',
                image: this.manualImagePreview || '{{ asset('images/product-placeholder.svg') }}',
            };
        }
        return this.products.find(p => String(p.id) === String(this.selectedProductId)) || null;
    },

    handleImageUpload(event) {
        const file = event.target.files && event.target.files[0];
        if (file) {
            this.manualImagePreview = URL.createObjectURL(file);
        }
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
        if (val === '' || val === null || val === undefined) return '₹ —';
        const num = parseFloat(val);
        if (isNaN(num)) return '₹ —';
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
                    Spot Market Verified
                </span>
            </div>
            <p class="text-sm text-slate-500 leading-relaxed">
                List a verified consignment of merchandise, artisanal goods, luxury items, or wholesale stock on the live spot-bidding exchange with transparent reserve floor protections. Choose from your existing catalog SKUs or add a custom lot directly.
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

    <!-- GLOBAL ERROR ALERTS -->
    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-mono flex items-start gap-3 shadow-2xs">
            <span class="material-symbols-outlined text-rose-600 text-[20px] shrink-0">error</span>
            <div class="space-y-1">
                <span class="font-bold text-sm block font-heading">Please correct the highlighted issues:</span>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- DUAL-PANE LISTING WORKSPACE: 7 COLS FORM / 5 COLS LIVE PREVIEW -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: INTERACTIVE CREATION FORM (7 COLS) -->
        <div class="lg:col-span-7 flex flex-col gap-6">
            <form method="POST" action="{{ route('seller.auctions.store') }}" enctype="multipart/form-data" class="flex flex-col gap-6">
                @csrf

                <!-- SECTION 01: Product Consignment & Source Mode -->
                <div class="bg-white rounded-3xl p-6 lg:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-800 font-mono text-xs font-bold flex items-center justify-center border border-amber-500/20">
                                01
                            </span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-slate-900">Product Consignment &amp; Source</h3>
                                <p class="text-xs text-slate-500">Choose between your listed catalog inventory or add a new consignment manually</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-mono text-[10px] font-bold uppercase tracking-wider">
                            Verified Consignment Lot
                        </span>
                    </div>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="product_source" :value="productMode">

                    <!-- SEGMENTED SOURCE SELECTION CARDS -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Option 1: Own Listed Product -->
                        <button type="button" 
                                @click="productMode = 'existing'"
                                class="relative p-4 rounded-2xl border text-left transition-all flex items-start gap-3 cursor-pointer group"
                                :class="productMode === 'existing' 
                                    ? 'bg-amber-50/50 border-amber-500 ring-2 ring-amber-500/15 shadow-2xs' 
                                    : 'bg-slate-50/70 border-slate-200 hover:border-slate-300 hover:bg-slate-50'">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                 :class="productMode === 'existing' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-white text-slate-600 border border-slate-200'">
                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-heading font-extrabold text-slate-900">My Listed Products</span>
                                    <span class="w-2 h-2 rounded-full" :class="productMode === 'existing' ? 'bg-amber-500' : 'bg-transparent'"></span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                    Select from {{ $productsList->count() }} active store catalog items
                                </p>
                            </div>
                        </button>

                        <!-- Option 2: Add Product Manually -->
                        <button type="button" 
                                @click="productMode = 'manual'"
                                class="relative p-4 rounded-2xl border text-left transition-all flex items-start gap-3 cursor-pointer group"
                                :class="productMode === 'manual' 
                                    ? 'bg-amber-50/50 border-amber-500 ring-2 ring-amber-500/15 shadow-2xs' 
                                    : 'bg-slate-50/70 border-slate-200 hover:border-slate-300 hover:bg-slate-50'">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                 :class="productMode === 'manual' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-white text-slate-600 border border-slate-200'">
                                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-heading font-extrabold text-slate-900">Add Product Manually</span>
                                    <span class="w-2 h-2 rounded-full" :class="productMode === 'manual' ? 'bg-amber-500' : 'bg-transparent'"></span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                    List custom product or lot without catalog pre-entry
                                </p>
                            </div>
                        </button>
                    </div>

                    <!-- SUB-VIEW 1: EXISTING LISTED PRODUCT -->
                    <div x-show="productMode === 'existing'" class="flex flex-col gap-4">
                        @if($productsList->isEmpty())
                            <div class="p-6 rounded-2xl bg-amber-50/60 border border-amber-200 text-center flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-[32px] text-amber-700">inventory_2</span>
                                <p class="text-sm font-heading font-bold text-slate-900">No Catalog Products Available</p>
                                <p class="text-xs text-slate-600 max-w-md">
                                    You don't have any products in your store catalog yet. You can list directly by clicking below to switch to manual entry, or add to your catalog.
                                </p>
                                <div class="flex items-center gap-3 mt-2 flex-wrap justify-center">
                                    <button type="button" @click="productMode = 'manual'" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-heading text-xs font-bold shadow-2xs transition-all flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px]">edit_note</span>
                                        <span>Switch to Add Product Manually</span>
                                    </button>
                                    <a href="{{ route('seller.products.create') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-heading text-xs font-semibold shadow-2xs transition-all flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                        <span>Add to Store Catalog</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-heading font-semibold text-slate-700">Choose Catalog SKU</label>
                                <div class="relative">
                                    <select name="product_id" 
                                            x-model="selectedProductId" 
                                            :required="productMode === 'existing'"
                                            class="w-full h-12 pl-4 pr-10 bg-slate-50/80 rounded-2xl text-sm font-semibold text-slate-900 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all appearance-none cursor-pointer">
                                        <option value="" disabled>Select from your approved products...</option>
                                        @foreach($productsList as $prod)
                                            <option value="{{ $prod->id }}">
                                                {{ $prod->name }} • Available: {{ $prod->stock }} {{ $prod->unit_type ?? 'unit' }} (₹{{ number_format($prod->base_price ?? $prod->price, 2) }}/{{ $prod->unit_type ?? 'unit' }})
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
                            <template x-if="currentProduct && currentProduct.id !== 'manual'">
                                <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-50 via-amber-50/10 to-slate-50 border border-slate-200/80 flex items-center gap-4 transition-all">
                                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-white shrink-0 border border-slate-200 shadow-2xs flex items-center justify-center p-1">
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
                        @endif
                    </div>

                    <!-- SUB-VIEW 2: ADD PRODUCT MANUALLY -->
                    <div x-show="productMode === 'manual'" class="flex flex-col gap-4">
                        <!-- Row 1: Item Title & Category (Clean 2-Column Grid) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700 flex items-center justify-between">
                                    <span>Item / Lot Title <span class="text-rose-500">*</span></span>
                                    <span class="text-[10px] font-mono text-slate-400">e.g. Watches, Crafts, Collectibles</span>
                                </label>
                                <input type="text" 
                                       name="manual_name" 
                                       x-model="manualName" 
                                       :required="productMode === 'manual'"
                                       placeholder="e.g. Handcrafted Teakwood Chair or Swiss Chronometer Lot" 
                                       class="w-full h-11 px-4 bg-slate-50/80 rounded-xl text-xs font-semibold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all">
                                @error('manual_name') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700 flex items-center justify-between">
                                    <span>Category <span class="text-rose-500">*</span></span>
                                    <span class="text-[10px] font-mono text-slate-400">All Listed Categories</span>
                                </label>
                                <div class="relative">
                                    <select name="manual_category_id" 
                                            x-model="manualCategoryId" 
                                            @change="manualCategoryName = $event.target.options[$event.target.selectedIndex]?.text || ''"
                                            class="w-full h-11 pl-4 pr-10 bg-slate-50/80 rounded-xl text-xs font-semibold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all appearance-none cursor-pointer">
                                        @foreach($categoriesList as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">
                                        expand_more
                                    </span>
                                </div>
                                @error('manual_category_id') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Row 2: Lot Quantity, Unit Type, & Base Value -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700">Wholesale Quantity <span class="text-rose-500">*</span></label>
                                <input type="number" 
                                       step="any" 
                                       min="1" 
                                       name="manual_stock" 
                                       x-model="manualStock" 
                                       :required="productMode === 'manual'"
                                       placeholder="e.g. 100" 
                                       class="w-full h-11 px-4 bg-slate-50/80 rounded-xl font-mono text-xs font-semibold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all">
                                @error('manual_stock') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700">Unit of Measurement</label>
                                <div class="relative">
                                    <select name="manual_unit_type" 
                                            x-model="manualUnit" 
                                            class="w-full h-11 pl-4 pr-10 bg-slate-50/80 rounded-xl text-xs font-semibold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all appearance-none cursor-pointer">
                                        <option value="kg">kg (Kilograms)</option>
                                        <option value="Quintal (q)">Quintal (q - 100kg)</option>
                                        <option value="Metric Ton (MT)">Metric Ton (MT)</option>
                                        <option value="Crates">Crates</option>
                                        <option value="Boxes">Boxes</option>
                                        <option value="Bags">Bags</option>
                                        <option value="Bunches">Bunches</option>
                                        <option value="Pieces">Pieces</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">
                                        expand_more
                                    </span>
                                </div>
                                @error('manual_unit_type') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700">Est. Retail / Lot Value (₹)</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 font-mono text-xs text-slate-400 font-bold">₹</span>
                                    <input type="number" 
                                           step="0.01" 
                                           min="0" 
                                           name="manual_price" 
                                           x-model="manualPrice" 
                                           placeholder="e.g. 1500" 
                                           class="w-full h-11 pl-8 pr-3.5 bg-slate-50/80 rounded-xl font-mono text-xs font-semibold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all">
                                </div>
                                @error('manual_price') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Row 3: Origin & Manufacturing / Harvest Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700">Origin / Facility Location (Optional)</label>
                                <input type="text" 
                                       name="manual_farm_origin" 
                                       x-model="manualFarmOrigin" 
                                       placeholder="e.g. Workshop Studio / Artisan Hub / Regional Facility" 
                                       class="w-full h-11 px-4 bg-slate-50/80 rounded-xl text-xs font-semibold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all">
                                @error('manual_farm_origin') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-heading font-semibold text-slate-700">Manufacture / Harvest Date (Optional)</label>
                                <input type="date" 
                                       name="manual_harvest_date" 
                                       x-model="manualHarvestDate" 
                                       class="w-full h-11 px-4 bg-slate-50/80 rounded-xl font-mono text-xs font-semibold text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all">
                                @error('manual_harvest_date') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Row 4: Photo Upload with Dropzone & Live Preview -->
                        <div class="flex flex-col gap-1.5" x-data="{ manualFileName: '' }">
                            <label class="text-xs font-heading font-semibold text-slate-700 flex items-center justify-between">
                                <span>Consignment Photo</span>
                                <span class="text-[10px] font-mono text-slate-400">Optional • Max 10MB</span>
                            </label>
                            <div @click="$refs.manualFileInput.click()" 
                                 class="flex items-center gap-4 p-3.5 bg-slate-50/80 hover:bg-amber-50/20 rounded-2xl border border-dashed border-slate-300 hover:border-amber-500 transition-all cursor-pointer group">
                                <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 group-hover:border-amber-400 flex items-center justify-center overflow-hidden shrink-0 shadow-2xs transition-colors">
                                    <template x-if="manualImagePreview">
                                        <img :src="manualImagePreview" alt="Upload preview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!manualImagePreview">
                                        <span class="material-symbols-outlined text-[26px] text-slate-400 group-hover:text-amber-600 transition-colors">add_a_photo</span>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500 group-hover:bg-amber-400 text-slate-950 font-heading text-xs font-bold shadow-2xs transition-all pointer-events-none">
                                            <span class="material-symbols-outlined text-[16px]">upload_file</span>
                                            <span x-text="manualImagePreview ? 'Change Photo' : 'Choose Photo'"></span>
                                        </span>
                                        <span class="text-xs font-mono text-slate-600 truncate" x-text="manualFileName || 'No file selected'"></span>
                                    </div>
                                    <span class="text-[10px] font-mono text-slate-400 mt-1 block">JPG, PNG, or WEBP up to 10MB. High-resolution item photos attract more active bidders.</span>
                                </div>
                                <input type="file" 
                                       x-ref="manualFileInput"
                                       name="manual_image" 
                                       accept="image/png,image/jpeg,image/webp,image/jpg" 
                                       @change="manualFileName = $event.target.files && $event.target.files[0] ? $event.target.files[0].name : ''; handleImageUpload($event)"
                                       class="hidden">
                            </div>
                            @error('manual_image') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Row 5: Description & Quality Notes -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-slate-700 flex items-center justify-between">
                                <span>Description &amp; Specifications</span>
                                <span class="text-[10px] font-mono text-slate-400">Condition, materials, packaging, dimensions</span>
                            </label>
                            <textarea name="manual_description" 
                                      x-model="manualDescription" 
                                      rows="3" 
                                      placeholder="Specify condition (e.g. Brand New, Handcrafted, Vintage, Grade A), materials, dimensions, grading specifications, packaging, and certifications..." 
                                      class="w-full p-3.5 bg-slate-50/80 rounded-xl text-xs text-slate-900 border border-slate-300 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all leading-relaxed"></textarea>
                            @error('manual_description') <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
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
                            <label class="text-xs font-heading font-semibold text-slate-700">Starting Floor (₹) <span class="text-rose-500">*</span></label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 font-mono text-sm text-slate-400 font-bold">₹</span>
                                <input type="number" 
                                       step="0.01" 
                                       min="1"
                                       name="starting_price" 
                                       x-model="startPrice" 
                                       placeholder="e.g. 1000"
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
                            <label class="text-xs font-heading font-semibold text-slate-700">Min Increment (₹) <span class="text-rose-500">*</span></label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 font-mono text-sm text-slate-400 font-bold">₹</span>
                                <input type="number" 
                                       step="1" 
                                       min="1" 
                                       name="minimum_increment" 
                                       x-model="minIncrement" 
                                       placeholder="e.g. 100"
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

                <!-- SECTION 03: Live Spot Trading Window -->
                <div class="bg-white rounded-3xl p-6 lg:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-5">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-800 font-mono text-xs font-bold flex items-center justify-center border border-amber-500/20">
                                03
                            </span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-slate-900">Live Spot Trading Window</h3>
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
                                <label class="text-xs font-heading font-semibold text-slate-700">Start Date &amp; Time <span class="text-rose-500">*</span></label>
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
                                <label class="text-xs font-heading font-semibold text-slate-700">Closing Date &amp; Time <span class="text-rose-500">*</span></label>
                                <span class="text-[10px] font-mono text-slate-400">Scheduled Close Clock</span>
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
                        <button type="button" @click="setDuration(72)" class="px-3 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all">+3 Days (Bulk Lot)</button>
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
                    <button type="submit" class="h-12 px-8 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-extrabold shadow-sm hover:shadow-md transition-all flex items-center gap-2 cursor-pointer">
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
                        <template x-if="currentProduct && currentProduct.image">
                            <img :src="currentProduct.image" 
                                 :alt="currentProduct.name" 
                                 class="w-full h-full object-contain p-2"
                                 onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                        </template>
                        <template x-if="!currentProduct || !currentProduct.image">
                            <div class="text-center text-slate-500 flex flex-col items-center">
                                <span class="material-symbols-outlined text-[36px] text-slate-400 mb-1">image</span>
                                <span class="text-xs font-mono">Select or enter a product to preview</span>
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

                        <!-- Lot Source Indicator -->
                        <div class="absolute bottom-3 left-3 bg-slate-900/90 backdrop-blur-md px-2.5 py-1 rounded-lg font-mono text-[11px] font-bold text-white border border-slate-700 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span x-text="productMode === 'manual' ? 'MANUAL LOT' : 'CATALOG LOT'"></span>
                        </div>
                    </div>

                    <!-- Lot Details Content -->
                    <div class="p-5 flex flex-col gap-4">
                        <div>
                            <div class="flex items-center justify-between text-xs font-mono text-slate-500 mb-1">
                                <span x-text="currentProduct ? currentProduct.category : 'Selected Category'"></span>
                                <span class="font-bold text-amber-700" x-text="'Lot: ' + (currentProduct && currentProduct.stock ? (currentProduct.stock + ' ' + currentProduct.unit) : '—')"></span>
                            </div>
                            <h4 class="font-heading text-lg font-bold text-slate-900 line-clamp-1" x-text="currentProduct ? currentProduct.name : 'Enter Item / Lot Name'"></h4>
                            <p class="text-xs text-slate-500 line-clamp-2 mt-0.5" x-text="currentProduct ? currentProduct.description : 'Consignment lot ready for live bidding floor.'"></p>
                        </div>

                        <!-- Bidding Metrics Tile -->
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-mono uppercase text-slate-400 font-bold block">Opening Floor</span>
                                <div class="font-mono text-2xl font-extrabold text-slate-900" x-text="startPrice && parseFloat(startPrice) > 0 ? formatCurrency(startPrice) : '₹ —'"></div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-mono uppercase text-slate-400 font-bold block">Next Minimum Bid</span>
                                <div class="font-mono text-sm font-bold text-emerald-700" x-text="startPrice && parseFloat(startPrice) > 0 ? formatCurrency(parseFloat(startPrice) + (parseFloat(minIncrement) || 0)) : '—'"></div>
                            </div>
                        </div>

                        <!-- Live Quick Presets Pill Preview -->
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[10px] font-mono text-slate-400 uppercase font-bold">1-Click Quick Bidding Presets</span>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="py-2 text-center rounded-xl bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200 flex items-center justify-center gap-1">
                                    <span class="text-amber-600 font-bold">⚡</span>
                                    <span x-text="'+' + formatCurrency(minIncrement || 100)"></span>
                                </div>
                                <div class="py-2 text-center rounded-xl bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200 flex items-center justify-center gap-1">
                                    <span class="text-amber-600 font-bold">⚡</span>
                                    <span x-text="'+' + formatCurrency((minIncrement || 100) * 2)"></span>
                                </div>
                                <div class="py-2 text-center rounded-xl bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200 flex items-center justify-center gap-1">
                                    <span class="text-amber-600 font-bold">⚡</span>
                                    <span x-text="'+' + formatCurrency((minIncrement || 100) * 5)"></span>
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
                        <span class="font-medium text-[11px]">Quality Verified Lot</span>
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

</div>
@endsection
