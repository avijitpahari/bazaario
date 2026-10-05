@php
    $prod = $product ?? null;
    $prodName = $prod ? $prod->name : 'Handcrafted Leather Messenger Bag';
    $prodPrice = $prod ? (float)$prod->price : 2499.00;
    $prodCategory = ($prod && $prod->category) ? $prod->category->name : 'Artisan Crafts';
    $sellerUser = ($prod && $prod->seller) ? $prod->seller : null;
    $sellerProfile = $sellerUser ? $sellerUser->sellerProfile : null;
    $prodSeller = $sellerProfile ? $sellerProfile->shop_name : ($sellerUser ? $sellerUser->name : 'Bazaario Verified');
    $sellerType = $sellerProfile ? ($sellerProfile->seller_type ?? 'Kirana Store') : 'Kirana Store';
    $trustScore = $sellerProfile ? (float)($sellerProfile->trust_score ?? 95.0) : 95.0;
    $sellerCity = $sellerProfile ? ($sellerProfile->city ?? 'India') : 'India';
    $sellerState = $sellerProfile ? ($sellerProfile->state ?? '') : '';
    $prodStock = $prod ? (int)$prod->stock : 10;
    $prodUnitType = $prod ? ($prod->unit_type ?? 'piece') : 'piece';
    $prodDescription = ($prod && ($prod->description || $prod->short_description))
        ? ($prod->description ?: $prod->short_description)
        : 'Premium handcrafted quality item with authentic materials and escrow-backed guarantee.';
    
    // Dynamic Product Images Gallery (Feature 24)
    $galleryImages = [];
    if ($prod && $prod->images && $prod->images->isNotEmpty()) {
        foreach ($prod->images as $img) {
            $url = $img->url;
            if (!empty($url) && !in_array($url, $galleryImages)) {
                $galleryImages[] = $url;
            }
        }
    }
    
    if ($prod && !empty($prod->main_image_url) && !in_array($prod->main_image_url, $galleryImages)) {
        array_unshift($galleryImages, $prod->main_image_url);
    }
    
    if (empty($galleryImages)) {
        $galleryImages = [
            asset('images/product-placeholder.svg'),
        ];
    }

    $mainImage = $galleryImages[0];

    // Reviews Computation (Feature 32)
    $reviews = ($prod && $prod->reviews) ? $prod->reviews : collect();
    $totalReviewsCount = $reviews->count();
    $avgRating = $totalReviewsCount > 0 ? (float)$reviews->avg('rating') : (float)($prod->average_rating ?? 5.0);
    $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    foreach ($reviews as $rev) {
        $r = (int)$rev->rating;
        if ($r >= 1 && $r <= 5) {
            $ratingCounts[$r]++;
        }
    }

    $sellerTypeBadgeClasses = match($sellerType) {
        'Farmer' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'Dark Store' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
        'Individual' => 'bg-purple-50 text-purple-800 border-purple-200',
        default => 'bg-amber-50 text-amber-800 border-amber-200',
    };

    // Wishlist check
    $isWishlisted = false;
    if (Auth::guard('user')->check() && $prod) {
        $wishlist = Auth::guard('user')->user()->wishlist()->with('items')->first();
        if ($wishlist && $wishlist->items->where('product_id', $prod->id)->isNotEmpty()) {
            $isWishlisted = true;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $prodName }} — Bazaario</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600;700&amp;family=Space+Grotesk:wght@500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F172A',
                        accent: { DEFAULT: '#F5A623', hover: '#E09214' }
                    },
                    borderRadius: { card: '16px' }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #FFFDF8;
            background-image:
                radial-gradient(circle at 12% 10%, rgba(245, 166, 35, 0.06) 0%, transparent 45%),
                radial-gradient(circle at 88% 18%, rgba(15, 23, 42, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 50% 80%, rgba(245, 166, 35, 0.04) 0%, transparent 50%);
            background-attachment: fixed;
        }

        .glass-pill {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        /* Prevent Alpine.js x-cloak elements from flashing open */
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="font-sans text-slate-800 antialiased min-h-screen flex flex-col justify-between">
    <!-- NAVIGATION BAR -->
    <x-nav />

    <!-- FLASH MESSAGES -->
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 pt-4">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">error</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- MAIN PRODUCT DETAIL CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 pt-6 pb-24 lg:pb-8 space-y-10 flex-1 w-full">
        <!-- BREADCRUMBS & ESCROW BADGE -->
        <div class="flex items-center justify-between text-xs font-mono text-slate-500 flex-wrap gap-2">
            <nav class="flex items-center gap-2">
                <a class="hover:text-[#0F172A] transition" href="{{ url('/') }}">Home</a>
                <span>/</span>
                <a class="hover:text-[#0F172A] transition" href="{{ route('products.index') }}">Products</a>
                <span>/</span>
                <a class="hover:text-[#0F172A] transition" href="{{ route('products.index', ['category' => $prodCategory]) }}">{{ $prodCategory }}</a>
                <span>/</span>
                <span class="text-[#0F172A] font-semibold truncate max-w-[200px] sm:max-w-none">{{ $prodName }}</span>
            </nav>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[11px] font-bold">
                    ● BAZAARIO ESCROW SECURED
                </span>
                <span class="text-slate-400 font-mono text-[11px] hidden md:inline">SKU: {{ $prod->sku ?? 'BZ-' . ($prod->id ?? '101') }}</span>
            </div>
        </div>

        <!-- MAIN PRODUCT OVERVIEW -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- LEFT COLUMN: Feature 24 PRODUCT GALLERY -->
            <div class="lg:col-span-6 flex flex-col gap-4">
                <!-- Main Showcase Card -->
                <div class="bg-white rounded-card p-6 border border-slate-900/10 shadow-sm relative overflow-hidden group">
                    <!-- Top Badges -->
                    <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
                        <span class="font-mono text-[11px] bg-[#0F172A]/85 backdrop-blur-md text-white rounded-full px-3 py-1 inline-flex items-center gap-1.5 shadow-sm border border-white/20 font-semibold">
                            <span class="text-[#F5A623]">⚡</span> 100% Escrow Verified
                        </span>
                        <span class="font-mono text-[11px] bg-emerald-100/90 backdrop-blur-md text-emerald-800 rounded-full px-3 py-1 inline-flex items-center gap-1 font-semibold border border-emerald-200">
                            <span class="text-[#16A34A]">⚡</span> Express Dispatch
                        </span>
                    </div>

                    <!-- Centered Product Image with Fallback -->
                    <div class="w-full min-h-[390px] md:min-h-[420px] flex items-center justify-center p-2 overflow-hidden cursor-crosshair">
                        <img alt="{{ $prodName }}"
                            class="w-full max-h-[410px] object-contain mx-auto transition-transform duration-500 ease-out group-hover:scale-105"
                            id="main-product-image"
                            src="{{ $mainImage }}"
                            onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';" />
                    </div>

                    <!-- Interactive Nav Arrows (< >) -->
                    <button
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-[#0F172A] flex items-center justify-center border border-slate-200 shadow-sm transition opacity-0 group-hover:opacity-100"
                        onclick="cycleGallery(-1)" title="Previous Image" type="button">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <button
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-[#0F172A] flex items-center justify-center border border-slate-200 shadow-sm transition opacity-0 group-hover:opacity-100"
                        onclick="cycleGallery(1)" title="Next Image" type="button">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                </div>

                <!-- Thumbnails Rail -->
                <div class="grid grid-cols-4 gap-3" id="gallery-thumbs">
                    @foreach($galleryImages as $idx => $imgUrl)
                        <button
                            type="button"
                            class="thumb-btn border {{ $idx === 0 ? 'border-2 border-[#F5A623]' : 'border-slate-200' }} p-1.5 rounded-card bg-white shadow-xs transition hover:scale-[1.02] flex items-center justify-center aspect-square"
                            onclick="selectThumb({{ $idx }}, '{{ $imgUrl }}', this)">
                            <img alt="Thumbnail {{ $idx + 1 }}" class="w-full h-16 object-contain"
                                src="{{ $imgUrl }}"
                                onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';" />
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- RIGHT COLUMN: PRODUCT INFORMATION & BUY BOX -->
            <div class="lg:col-span-6 flex flex-col gap-5">
                <div class="bg-white rounded-card p-6 md:p-7 border border-slate-900/10 shadow-sm space-y-5">
                    
                    <!-- Seller Brand & Feature 27 Stock Urgency -->
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <span class="font-mono text-xs uppercase tracking-wider text-slate-500 font-semibold flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-amber-500">storefront</span>
                            {{ $prodSeller }}
                        </span>

                        @if($prodStock > 0)
                            <div class="flex items-center gap-1.5 font-mono text-xs font-semibold text-[#16A34A] bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-[#16A34A] animate-pulse"></span>
                                <span>✓ In Stock</span>
                                <span class="text-emerald-700 font-medium">• Only {{ $prodStock }} left in stock!</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 font-mono text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 px-3 py-1 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                <span>✕ Out of Stock</span>
                            </div>
                        @endif
                    </div>

                    <!-- Product Title & Review Meta -->
                    <div>
                        <h1 class="font-display font-bold text-3xl md:text-4xl text-[#0F172A] tracking-tight leading-tight">
                            {{ $prodName }}
                        </h1>
                        <div class="flex items-center gap-2.5 pt-2 flex-wrap">
                            <span class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-900 px-2.5 py-0.5 rounded-full font-mono text-xs font-bold">
                                <span class="material-symbols-outlined text-[15px] text-[#F5A623]" style="font-variation-settings: 'FILL' 1;">star</span>
                                ★ {{ number_format($avgRating, 1) }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <a class="text-xs font-mono text-slate-600 hover:text-[#0F172A] underline underline-offset-2" href="#customer-reviews">
                                {{ $totalReviewsCount }} customer reviews
                            </a>
                            <span class="text-slate-300">•</span>
                            <span class="text-[11px] font-mono text-slate-500">{{ $prodCategory }}</span>
                        </div>
                    </div>

                    <!-- Feature 26, 28, 29: Badges Rail -->
                    <div class="flex items-center gap-2 flex-wrap pt-1">
                        {{-- Feature 26: Seller Type badge --}}
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full border text-xs font-mono font-bold {{ $sellerTypeBadgeClasses }}">
                            <span class="material-symbols-outlined text-[14px]">store</span>
                            <span>Seller: {{ $sellerType }}</span>
                        </span>

                        {{-- Feature 28: Unit Type badge --}}
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-xs font-mono font-bold">
                            <span class="material-symbols-outlined text-[14px]">scale</span>
                            <span>Unit: {{ $prodUnitType }}</span>
                        </span>

                        {{-- Feature 29: Seller Trust Score badge --}}
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-mono font-bold">
                            <span class="material-symbols-outlined text-[14px] text-emerald-600">verified_user</span>
                            <span>{{ number_format($trustScore, 1) }}% Trust Score</span>
                        </span>
                    </div>

                    <!-- Feature 27: Dynamic Pricing Section -->
                    <div class="pt-3 border-t border-slate-100 flex items-baseline gap-3 flex-wrap">
                        <span class="font-display font-bold text-3xl md:text-4xl text-[#0F172A] tracking-tight" id="active-price">
                            ₹{{ number_format($prodPrice, 2) }}
                        </span>
                        <span class="text-sm font-mono text-slate-500 font-medium">/ {{ $prodUnitType }}</span>
                        <span class="text-lg text-slate-400 line-through font-medium" id="mrp-price">
                            MRP ₹{{ number_format(round($prodPrice * 1.25), 2) }}
                        </span>
                        <span class="rounded-card px-2.5 py-1 bg-amber-100 text-amber-800 font-mono text-xs font-bold">
                            20% OFF
                        </span>
                    </div>

                    <!-- Product Short Description -->
                    <div class="text-xs md:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        {{ $prodDescription }}
                    </div>

                    <!-- Feature 30 & 31: Quantity Stepper & Main CTA Buttons -->
                    <div class="space-y-3 pt-2">
                        @if($prodStock > 0)
                            <div class="flex items-center gap-3">
                                <!-- Tactile Stepper: [ - ] 1 [ + ] -->
                                <div class="bg-slate-100 rounded-card p-1 flex items-center border border-slate-200/90 shadow-inner">
                                    <button
                                        type="button"
                                        class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-700 hover:bg-white hover:text-slate-900 transition text-base font-bold active:scale-95"
                                        onclick="adjustQty(-1)">
                                        −
                                    </button>
                                    <span class="w-10 text-center font-mono text-sm font-bold text-[#0F172A]" id="stepper-count">
                                        1
                                    </span>
                                    <button
                                        type="button"
                                        class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-700 hover:bg-white hover:text-slate-900 transition text-base font-bold active:scale-95"
                                        onclick="adjustQty(1)">
                                        +
                                    </button>
                                </div>

                                <!-- Feature 30: Primary Add to Cart Button -->
                                <button
                                    type="button"
                                    class="flex-1 py-3.5 px-6 bg-[#F5A623] hover:brightness-105 text-[#0F172A] rounded-card font-semibold text-sm shadow-sm flex items-center justify-center gap-2 transition active:scale-[0.98]"
                                    id="add-to-cart-btn" onclick="submitCartForm(false)">
                                    <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                    <span>🛒 Add to Cart</span>
                                </button>
                            </div>

                            <!-- Feature 31: Instant Escrow Buy Now Button + Wishlist Heart -->
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="flex-1 py-3.5 px-6 bg-[#0F172A] hover:bg-slate-800 text-white rounded-card font-semibold text-sm shadow-sm flex items-center justify-center gap-2 transition active:scale-[0.98]"
                                    onclick="submitCartForm(true)">
                                    <span class="text-[#F5A623]">⚡</span>
                                    <span>⚡ Instant Escrow Buy Now</span>
                                </button>
                                <button
                                    type="button"
                                    class="w-12 h-12 rounded-card border border-slate-200 bg-slate-50 hover:bg-white text-slate-700 hover:text-red-500 flex items-center justify-center transition active:scale-95 shadow-xs {{ $isWishlisted ? 'text-red-500' : '' }}"
                                    onclick="toggleWishlist(this)" title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Save to Wishlist' }}">
                                    <span class="material-symbols-outlined text-[22px]">{{ $isWishlisted ? 'favorite' : 'favorite_border' }}</span>
                                </button>
                            </div>
                        @else
                            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-center">
                                <p class="font-bold text-rose-800 text-sm">This product is currently out of stock.</p>
                                <p class="text-xs text-rose-600 mt-1">Check back later or browse similar products in the catalog.</p>
                            </div>
                            <button disabled class="w-full py-3.5 px-6 bg-slate-200 text-slate-400 rounded-card font-semibold text-sm cursor-not-allowed">
                                Out of Stock
                            </button>
                        @endif
                    </div>

                    <!-- Product Benefits Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-2">
                        <div class="p-3 rounded-card bg-slate-50/80 border border-slate-200/70 flex items-center gap-2">
                            <span class="text-lg">🚚</span>
                            <div>
                                <p class="font-display font-bold text-xs text-[#0F172A]">Fast Delivery</p>
                                <p class="font-mono text-[10px] text-slate-500">2-4 days</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-card bg-slate-50/80 border border-slate-200/70 flex items-center gap-2">
                            <span class="text-lg">↩</span>
                            <div>
                                <p class="font-display font-bold text-xs text-[#0F172A]">7 Day Returns</p>
                                <p class="font-mono text-[10px] text-slate-500">Guaranteed</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-card bg-slate-50/80 border border-slate-200/70 flex items-center gap-2">
                            <span class="text-lg">🔒</span>
                            <div>
                                <p class="font-display font-bold text-xs text-[#0F172A]">Escrow Vault</p>
                                <p class="font-mono text-[10px] text-slate-500">100% Protected</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-card bg-slate-50/80 border border-slate-200/70 flex items-center gap-2">
                            <span class="text-lg">✓</span>
                            <div>
                                <p class="font-display font-bold text-xs text-[#0F172A]">Genuine</p>
                                <p class="font-mono text-[10px] text-slate-500">Verified Stall</p>
                            </div>
                        </div>
                    </div>

                    <!-- Features 26 & 29: Seller Information Card -->
                    <div class="border-t border-slate-100 pt-4 space-y-2">
                        <span class="font-mono text-[11px] text-slate-400 uppercase tracking-wider block font-semibold">
                            Sold by Merchant
                        </span>
                        <div class="p-3.5 rounded-card bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-display font-bold text-sm text-[#0F172A]">{{ $prodSeller }}</span>
                                    <span class="border font-mono text-[10px] font-bold px-2 py-0.5 rounded-full {{ $sellerTypeBadgeClasses }}">
                                        {{ $sellerType }}
                                    </span>
                                    <span class="bg-emerald-100 text-emerald-800 font-mono text-[10px] font-bold px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                                        🛡️ {{ number_format($trustScore, 1) }}% Trust Score
                                    </span>
                                </div>
                                <p class="font-mono text-[11px] text-slate-500">
                                    {{ $sellerCity }}{{ $sellerState ? ', ' . $sellerState : '' }} • Verified Merchant
                                </p>
                            </div>
                            <a href="{{ route('products.index', ['search' => $prodSeller]) }}"
                                class="self-start sm:self-center font-mono text-xs font-bold text-[#0F172A] hover:text-amber-600 bg-white border border-slate-200 px-3 py-1.5 rounded-lg transition shrink-0">
                                [ View Stall Catalog → ]
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Feature 25: PRODUCT SPECIFICATIONS & DETAILS -->
        <section class="w-full space-y-3">
            <h2 class="font-display font-bold text-2xl text-[#0F172A]">Product Specifications & Details</h2>
            <div class="bg-white rounded-card overflow-hidden border border-slate-900/10 shadow-sm">
                <table class="w-full text-left text-xs md:text-sm border-collapse">
                    <tbody class="divide-y divide-slate-200/80 font-sans">
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Product Name</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">{{ $prodName }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">SKU Code</td>
                            <td class="w-2/3 py-3 px-4 font-mono font-semibold text-[#0F172A]">{{ $prod->sku ?? 'N/A' }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Category</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">{{ $prodCategory }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Unit Type</td>
                            <td class="w-2/3 py-3 px-4 font-mono font-semibold text-[#0F172A]">{{ $prodUnitType }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Weight</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">{{ $prod->weight ? $prod->weight . ' kg' : 'Standard' }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Dimensions (L × W × H)</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">
                                @if($prod->length || $prod->width || $prod->height)
                                    {{ $prod->length ?? 0 }} × {{ $prod->width ?? 0 }} × {{ $prod->height ?? 0 }} cm
                                @else
                                    Standard Packaging
                                @endif
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Stock Availability</td>
                            <td class="w-2/3 py-3 px-4 font-semibold {{ $prodStock > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $prodStock > 0 ? "{$prodStock} units available" : 'Out of Stock' }}
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Sale Type</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">{{ ucfirst($prod->sale_type ?? 'fixed') }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Dispatch Processing Time</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">{{ $prod->processing_time_days ? $prod->processing_time_days . ' business days' : '1-2 business days' }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Merchant Stall & Origin</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">{{ $prodSeller }} ({{ $sellerCity }}{{ $sellerState ? ', ' . $sellerState : '' }})</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Feature 32 & 33: CUSTOMER REVIEWS & RATING FORM -->
        <section class="space-y-6 pt-2" id="customer-reviews">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <span class="font-mono text-xs font-bold text-amber-700 uppercase tracking-wider block">VERIFIED BUYER FEEDBACK</span>
                    <h2 class="font-display font-bold text-2xl md:text-3xl text-[#0F172A]">⭐ Customer Reviews & Ratings</h2>
                </div>
                <div class="font-mono text-xs text-slate-500">
                    Showing {{ $reviews->count() }} verified buyer reviews
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Rating summary card (5 cols) -->
                <div class="lg:col-span-5 glass-card rounded-card p-6 shadow-sm space-y-5">
                    <div class="flex items-baseline gap-3">
                        <span class="font-display font-bold text-5xl text-[#0F172A]">{{ number_format($avgRating, 1) }}</span>
                        <div class="space-y-1">
                            <span class="text-sm font-mono text-slate-500">/ 5.0</span>
                            <div class="flex text-[#F5A623] text-lg">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($avgRating))
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <p class="font-mono text-xs text-slate-500">Based on {{ $totalReviewsCount }} customer ratings</p>
                    
                    <!-- Horizontal distribution bars -->
                    <div class="space-y-2 font-mono text-xs pt-2 border-t border-slate-100">
                        @foreach([5, 4, 3, 2, 1] as $star)
                            @php
                                $cnt = $ratingCounts[$star] ?? 0;
                                $pct = $totalReviewsCount > 0 ? round(($cnt / $totalReviewsCount) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 text-right font-medium">{{ $star }} ★</span>
                                <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="bg-[#F5A623] h-full rounded-full" style="width: {{ $pct }}%;"></div>
                                </div>
                                <span class="w-16 text-right text-slate-600 font-semibold">{{ $cnt }} ({{ $pct }}%)</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Feature 33: Add Review Form Container -->
                    <div class="pt-4 border-t border-slate-200">
                        <h3 class="font-display font-bold text-base text-[#0F172A] mb-2">Write a Review</h3>
                        @auth
                            <form action="{{ route('user.reviews.store') }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $prod->id }}">

                                <div>
                                    <label class="block font-mono text-xs font-semibold text-slate-700 mb-1">Your Rating</label>
                                    <div class="flex items-center gap-3">
                                        @for($s = 5; $s >= 1; $s--)
                                            <label class="flex items-center gap-1 cursor-pointer font-mono text-xs">
                                                <input type="radio" name="rating" value="{{ $s }}" {{ $s === 5 ? 'checked' : '' }} class="accent-[#F5A623]">
                                                <span>{{ $s }} ★</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-mono text-xs font-semibold text-slate-700 mb-1">Headline / Title</label>
                                    <input type="text" name="title" placeholder="Summary of your experience"
                                        class="w-full text-xs p-2 rounded-lg border border-slate-200 focus:outline-none focus:border-amber-500">
                                </div>

                                <div>
                                    <label class="block font-mono text-xs font-semibold text-slate-700 mb-1">Comments</label>
                                    <textarea name="comment" rows="3" placeholder="Tell other buyers what you liked or how this item performed..."
                                        class="w-full text-xs p-2 rounded-lg border border-slate-200 focus:outline-none focus:border-amber-500"></textarea>
                                </div>

                                <button type="submit"
                                    class="w-full py-2.5 bg-[#0F172A] hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition">
                                    Submit Feedback
                                </button>
                            </form>
                        @else
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-center text-slate-600">
                                Please <a href="{{ route('login') }}" class="font-bold text-amber-700 underline">Sign In</a> to share your review and rating.
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Feature 32: Real Customer Review Cards (7 cols) -->
                <div class="lg:col-span-7 space-y-4">
                    @if($reviews->isNotEmpty())
                        @foreach($reviews as $rev)
                            @php
                                $authorName = $rev->user->name ?? 'Verified Buyer';
                                $initials = strtoupper(mb_substr($authorName, 0, 2));
                                $revRating = (int)($rev->rating ?? 5);
                            @endphp
                            <div class="glass-card rounded-card p-5 shadow-sm space-y-2.5">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-[#0F172A] text-white font-display font-bold text-xs flex items-center justify-center">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-display font-bold text-sm text-[#0F172A]">{{ $authorName }}</span>
                                                <span class="bg-emerald-100 text-emerald-800 font-mono text-[10px] px-2 py-0.5 rounded-full font-semibold">
                                                    Verified Purchase ✓
                                                </span>
                                            </div>
                                            <span class="font-mono text-[10px] text-slate-400">
                                                {{ $rev->created_at ? $rev->created_at->diffForHumans() : 'Recently' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-[#F5A623] text-sm font-mono">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $revRating)★@else☆@endif
                                        @endfor
                                    </div>
                                </div>
                                @if($rev->title)
                                    <h4 class="font-display font-bold text-sm text-[#0F172A]">{{ $rev->title }}</h4>
                                @endif
                                <p class="text-xs md:text-sm text-slate-700 leading-relaxed">
                                    {{ $rev->comment ?? $rev->body ?? 'Great product quality and fast delivery.' }}
                                </p>
                            </div>
                        @endforeach
                    @else
                        <div class="glass-card rounded-card p-8 text-center space-y-3">
                            <span class="material-symbols-outlined text-4xl text-amber-500">rate_review</span>
                            <h3 class="font-display font-bold text-base text-slate-800">No customer reviews yet</h3>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                Be the first customer to purchase and share authentic feedback on this stall listing!
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- RELATED PRODUCTS: YOU MAY ALSO LIKE -->
        @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
        <section class="space-y-5 pt-4 pb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display font-bold text-2xl text-[#0F172A]">✨ You may also like</h2>
                </div>
                <a class="text-xs font-mono font-bold text-[#0F172A] hover:text-amber-600 transition" href="{{ route('products.index') }}">
                    Browse catalog →
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($relatedProducts as $rel)
                    @php $relImg = $rel->primaryImage?->url ?? $rel->images->first()?->url ?? asset('images/product-placeholder.svg'); @endphp
                    <article class="bg-white rounded-card p-4 border border-slate-900/10 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                        <a href="{{ route('products.show', $rel->slug) }}" class="space-y-3 block">
                            <div class="w-full aspect-square bg-slate-50 rounded-lg overflow-hidden flex items-center justify-center p-3">
                                <img alt="{{ $rel->name }}" class="w-full h-full object-contain group-hover:scale-105 transition duration-300"
                                    src="{{ $relImg }}" onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';" />
                            </div>
                            <div>
                                <div class="flex items-center gap-1 text-[#F5A623] text-xs font-mono">
                                    <span>★ {{ number_format($rel->average_rating ?: 4.8, 1) }}</span>
                                </div>
                                <h3 class="font-display font-bold text-sm text-[#0F172A] truncate">{{ $rel->name }}</h3>
                                <p class="font-mono font-bold text-base text-[#0F172A] mt-1">₹{{ number_format($rel->price, 2) }}</p>
                            </div>
                        </a>
                        <form action="{{ route('cart.store') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $rel->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-slate-100 hover:bg-[#0F172A] hover:text-white text-[#0F172A] font-semibold text-xs py-2 rounded-lg transition">
                                🛒 Add to Cart
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        </section>
        @endif
    </main>

    <!-- FOOTER -->
    <x-footer />

    <!-- JAVASCRIPT FOR GALLERY & CART -->
    <script>
        const galleryImages = {!! json_encode($galleryImages) !!};
        let currentGalleryIndex = 0;

        function selectThumb(idx, url, btn) {
            currentGalleryIndex = idx;
            const img = document.getElementById('main-product-image');
            if (img) {
                img.style.opacity = '0.3';
                setTimeout(() => {
                    img.src = url;
                    img.style.opacity = '1';
                }, 100);
            }
            document.querySelectorAll('.thumb-btn').forEach((b, i) => {
                if (i === idx) {
                    b.className = 'thumb-btn border-2 border-[#F5A623] p-1.5 rounded-card bg-white shadow-xs transition hover:scale-[1.02] flex items-center justify-center aspect-square';
                } else {
                    b.className = 'thumb-btn border border-slate-200 p-1.5 rounded-card bg-white shadow-xs transition hover:scale-[1.02] flex items-center justify-center aspect-square';
                }
            });
        }

        function cycleGallery(delta) {
            if (!galleryImages || galleryImages.length === 0) return;
            currentGalleryIndex = (currentGalleryIndex + delta + galleryImages.length) % galleryImages.length;
            const url = galleryImages[currentGalleryIndex];
            const buttons = document.querySelectorAll('.thumb-btn');
            const targetBtn = buttons[currentGalleryIndex] || null;
            selectThumb(currentGalleryIndex, url, targetBtn);
        }

        let qty = 1;
        const maxStock = {{ $prodStock > 0 ? $prodStock : 1 }};
        function adjustQty(delta) {
            qty = Math.max(1, Math.min(maxStock, qty + delta));
            document.getElementById('stepper-count').innerText = qty;
        }

        function submitCartForm(buyNow = false) {
            const productId = "{{ $prod ? $prod->id : '' }}";
            if (!productId) {
                alert('Product not found.');
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('cart.store') }}";

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            form.appendChild(csrf);

            const prodInput = document.createElement('input');
            prodInput.type = 'hidden';
            prodInput.name = 'product_id';
            prodInput.value = productId;
            form.appendChild(prodInput);

            const qtyInput = document.createElement('input');
            qtyInput.type = 'hidden';
            qtyInput.name = 'quantity';
            qtyInput.value = qty;
            form.appendChild(qtyInput);

            if (buyNow) {
                const buyNowInput = document.createElement('input');
                buyNowInput.type = 'hidden';
                buyNowInput.name = 'buy_now';
                buyNowInput.value = '1';
                form.appendChild(buyNowInput);
            }

            document.body.appendChild(form);
            form.submit();
        }

        function toggleWishlist(btn) {
            const productId = '{{ $product->id }}';

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('user.wishlist.store') }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = 'product_id';
            productIdInput.value = productId;
            form.appendChild(productIdInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
    <!-- Alpine.js — required for nav dropdowns and interactive UI -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>