@extends('layouts.user')

@section('title', 'My Cart — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority">Shopping Cart</h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                {{ $cart ? $cart->items->sum('quantity') : 0 }} item(s) in your cart
            </p>
        </div>
        <a href="{{ route('products.index') }}" class="font-body-small text-body-small text-amber-action hover:underline font-semibold flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Continue Shopping
        </a>
    </div>

    @if($cart && $cart->items->isNotEmpty())
    @php
        $sellerGroups = isset($groupedItems) && $groupedItems->isNotEmpty()
            ? $groupedItems
            : $cart->items->groupBy(fn($item) => $item->product->seller_id ?? 0);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Feature 34: Cart Items Grouped by Seller Merchant Blocks --}}
        <div class="lg:col-span-2 flex flex-col gap-5">
            @foreach($sellerGroups as $sellerId => $sellerItems)
                @php
                    $firstItem = $sellerItems->first();
                    $sellerUser = $firstItem->product->seller ?? null;
                    $sellerProfile = $sellerUser ? $sellerUser->sellerProfile : null;
                    $shopName = $sellerProfile->shop_name ?? ($sellerUser->name ?? 'Verified Merchant');
                    $sellerType = $sellerProfile->seller_type ?? 'Kirana Store';
                    $trustScore = $sellerProfile->trust_score ?? 95.0;

                    $merchantSubtotal = isset($sellerSubtotals[$sellerId])
                        ? $sellerSubtotals[$sellerId]
                        : $sellerItems->sum(fn ($i) => ($i->product->price ?? 0) * $i->quantity);

                    $typeBadgeClasses = match($sellerType) {
                        'Farmer' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'Dark Store' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                        'Individual' => 'bg-purple-50 text-purple-800 border-purple-200',
                        default => 'bg-amber-50 text-amber-800 border-amber-200',
                    };
                @endphp

                <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 overflow-hidden">
                    {{-- Merchant Header --}}
                    <div class="p-4 bg-slate-50/90 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-sm shrink-0">
                                <span class="material-symbols-outlined text-[20px]">storefront</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="font-title-card text-title-card font-bold text-slate-authority">{{ $shopName }}</h2>
                                    {{-- Feature 26: Seller Type badge --}}
                                    <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $typeBadgeClasses }}">
                                        {{ $sellerType }}
                                    </span>
                                    {{-- Feature 29: Seller Trust Score badge --}}
                                    <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        🛡️ {{ number_format($trustScore, 1) }}% Trust
                                    </span>
                                </div>
                                @if($sellerProfile && $sellerProfile->city)
                                    <p class="font-mono text-[11px] text-on-surface-variant flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[12px]">location_on</span> {{ $sellerProfile->city }}, {{ $sellerProfile->state ?? 'India' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="font-mono text-[11px] text-slate-500 font-semibold uppercase tracking-wider">
                                {{ $sellerItems->sum('quantity') }} Item(s)
                            </span>
                        </div>
                    </div>

                    {{-- Merchant Item Cards --}}
                    <div class="divide-y divide-slate-100 p-3 sm:p-4 space-y-2">
                        @foreach($sellerItems as $item)
                            @php $product = $item->product; @endphp
                            <div class="p-3 rounded-xl hover:bg-slate-50/50 flex flex-col sm:flex-row gap-4 items-start sm:items-center transition-colors">
                                {{-- Image --}}
                                <div class="w-20 h-20 rounded-xl overflow-hidden bg-surface-container-high shrink-0 border border-slate-200/60">
                                    @php $img = $product->main_image_url; @endphp
                                    <img alt="{{ $product->name ?? 'Product' }}" class="w-full h-full object-cover" src="{{ $img }}" onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 flex flex-col gap-1 min-w-0">
                                    <h3 class="font-title-card text-title-card font-semibold text-slate-authority truncate">
                                        <a href="{{ route('products.show', $product->slug) }}" class="hover:text-amber-action transition-colors">
                                            {{ $product->name ?? 'Unknown Product' }}
                                        </a>
                                    </h3>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-body-small text-body-small text-on-surface-variant font-mono font-medium">₹{{ number_format($product->price ?? 0, 2) }} / {{ $product->unit_type ?? 'piece' }}</p>
                                        @if($product->unit_type)
                                            {{-- Feature 28: Unit Type badge --}}
                                            <span class="font-mono text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">
                                                Unit: {{ $product->unit_type }}
                                            </span>
                                        @endif
                                        @if($product->stock > 0)
                                            <span class="font-mono text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">✓ In Stock ({{ $product->stock }})</span>
                                        @else
                                            <span class="font-mono text-[10px] text-rose-700 bg-rose-50 px-2 py-0.5 rounded font-bold">✕ Out of Stock</span>
                                        @endif
                                    </div>

                                    {{-- Feature 35: Quantity Stepper --}}
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2 mt-2" x-data="{ updating: false }" @submit="updating = true">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex items-center gap-1 bg-surface-container-high rounded-lg overflow-hidden border border-slate-200 transition-opacity" :class="updating ? 'opacity-50 pointer-events-none' : ''">
                                            <button type="button" :disabled="updating" onclick="if(this.closest('form').__x?.$data.updating) return; const i=this.nextElementSibling; if(i.value>1){i.value--; if(this.closest('form').__x) this.closest('form').__x.$data.updating = true; this.closest('form').submit();}" class="px-3 py-1.5 text-slate-authority hover:bg-surface-container-highest font-bold transition-colors disabled:cursor-not-allowed">−</button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99"
                                                class="w-12 text-center bg-transparent font-body-regular text-body-regular text-slate-authority font-semibold border-0 focus:outline-none">
                                            <button type="button" :disabled="updating" onclick="if(this.closest('form').__x?.$data.updating) return; const i=this.previousElementSibling; i.value++; if(this.closest('form').__x) this.closest('form').__x.$data.updating = true; this.closest('form').submit();}" class="px-3 py-1.5 text-slate-authority hover:bg-surface-container-highest font-bold transition-colors disabled:cursor-not-allowed">+</button>
                                        </div>
                                        <span x-show="updating" x-cloak class="text-xs text-amber-action flex items-center">
                                            <x-loading-spinner size="14px" />
                                        </span>
                                    </form>
                                </div>

                                {{-- Price + Feature 36: Remove Item --}}
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <p class="font-title-card text-title-card font-bold text-slate-authority font-mono">₹{{ number_format(($product->price ?? 0) * $item->quantity, 2) }}</p>
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST" x-data="{ removing: false }" @submit="removing = true">
                                        @csrf @method('DELETE')
                                        <button type="submit" :disabled="removing" class="text-error hover:text-error/70 font-body-small text-body-small font-medium flex items-center gap-1 transition-colors disabled:opacity-50">
                                            <span x-show="!removing" class="material-symbols-outlined text-[16px]">delete</span>
                                            <span x-show="removing" x-cloak><x-loading-spinner size="14px" class="text-error" /></span>
                                            <span x-text="removing ? 'Removing...' : 'Remove'">Remove</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Feature 37: Seller-wise Subtotal Breakdown --}}
                    <div class="p-3.5 bg-slate-50 border-t border-slate-200/80 flex items-center justify-between">
                        <span class="font-mono text-xs font-semibold text-slate-600">Merchant Consignment Subtotal:</span>
                        <span class="font-mono font-bold text-sm text-slate-900">₹{{ number_format($merchantSubtotal, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Order Summary --}}
        <div class="flex flex-col gap-4">
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 shadow-sm flex flex-col gap-4 sticky top-24">
                <h2 class="font-title-card text-title-card font-bold text-slate-authority">Order Summary</h2>

                <div class="flex flex-col gap-2.5 font-body-small text-body-small">
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Shipping</span>
                        <span>₹{{ number_format($shipping, 2) }}</span>
                    </div>
                    @if($discount > 0)
                    <div class="flex justify-between text-status-green font-semibold">
                        <span>Coupon Discount</span>
                        <span>-₹{{ number_format($discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="h-px bg-surface-container-high"></div>
                    <div class="flex justify-between font-bold text-slate-authority font-title-card text-title-card">
                        <span>Total</span>
                        <span>₹{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                {{-- Feature 38: Apply Promo Coupon Code --}}
                @auth
                <div class="pt-2 border-t border-surface-container">
                    @if($appliedCoupon)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-status-green/10 border border-status-green/20 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-status-green">confirmation_number</span>
                            <span class="font-body-small text-body-small font-semibold text-status-green">{{ $appliedCoupon['code'] }}</span>
                        </div>
                        <form action="{{ route('cart.coupon.remove') }}" method="POST" x-data="{ removingCoupon: false }" @submit="removingCoupon = true">
                            @csrf @method('DELETE')
                            <button type="submit" :disabled="removingCoupon" class="text-error hover:text-error/70 transition-colors disabled:opacity-50" title="Remove coupon">
                                <span x-show="!removingCoupon" class="material-symbols-outlined text-[18px]">close</span>
                                <span x-show="removingCoupon" x-cloak><x-loading-spinner size="14px" class="text-error" /></span>
                            </button>
                        </form>
                    </div>
                    @else
                    <form action="{{ route('cart.coupon') }}" method="POST" class="flex gap-2" x-data="{ applying: false }" @submit="applying = true">
                        @csrf
                        <input type="text" name="coupon_code" placeholder="Enter coupon code" required
                            class="flex-1 bg-surface-container-low border border-slate-authority/20 rounded-xl px-3 py-2 text-slate-authority font-body-small text-body-small focus:outline-none focus:border-amber-action transition-colors">
                        <button type="submit" :disabled="applying" class="px-3.5 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold hover:bg-slate-authority/90 transition-colors flex items-center justify-center gap-1.5 disabled:opacity-60 disabled:cursor-not-allowed">
                            <span x-show="!applying">Apply</span>
                            <span x-show="applying" x-cloak class="flex items-center gap-1">
                                <x-loading-spinner size="14px" />
                                <span>Checking...</span>
                            </span>
                        </button>
                    </form>
                    @endif
                </div>
                @endauth

                <div x-data="{ checkingOut: false }">
                    <a href="{{ route('checkout.index') }}" @click="checkingOut = true"
                        class="w-full py-3.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-button-text font-bold text-center shadow-[0_4px_14px_rgba(245,166,35,0.35)] hover:opacity-95 active:scale-[0.99] transition-all flex items-center justify-center gap-2"
                        :class="checkingOut ? 'opacity-70 pointer-events-none' : ''">
                        <template x-if="!checkingOut">
                            <span class="flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">payment</span>
                                <span>Proceed to Checkout</span>
                            </span>
                        </template>
                        <template x-if="checkingOut">
                            <span class="flex items-center justify-center gap-2">
                                <x-loading-spinner size="18px" />
                                <span>Loading Checkout...</span>
                            </span>
                        </template>
                    </a>
                </div>

                <div class="flex items-center gap-2 text-center justify-center font-label-micro text-label-micro text-on-surface-variant">
                    <span class="material-symbols-outlined text-[14px] text-status-green">verified_user</span>
                    Secured by Bazaario Escrow Protection
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- Empty Cart --}}
    <div class="flex flex-col items-center justify-center py-20 gap-6 text-center">
        <div class="w-24 h-24 rounded-full bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant">shopping_bag</span>
        </div>
        <div>
            <h2 class="font-headline-section text-headline-section font-bold text-slate-authority">Your cart is empty</h2>
            <p class="font-body-regular text-body-regular text-on-surface-variant mt-2 max-w-md">Discover thousands of artisan products, electronics, and collectibles on Bazaario.</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-8 py-3.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-button-text font-bold shadow-sm hover:opacity-95">
            Start Shopping
        </a>
    </div>
    @endif
</div>
@endsection