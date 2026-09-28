@extends('layouts.user')

@section('title', 'My Coupons & Perks — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6"
     x-data="{ 
         copiedCode: null,
         copyToClipboard(code) {
             navigator.clipboard.writeText(code);
             this.copiedCode = code;
             setTimeout(() => {
                 if (this.copiedCode === code) this.copiedCode = null;
             }, 2000);
         }
     }">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-status-green/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-xl">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-status-green font-semibold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">confirmation_number</span> 
                <span>Exclusive Savings & Rewards</span>
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1 tracking-tight">
                My Coupons &amp; Perks
            </h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                Active promo vouchers automatically verified at checkout. Copy your voucher code and apply it during purchase.
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10 shrink-0">
            <div class="p-3.5 rounded-xl bg-card-white shadow-xs border border-slate-authority/10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-status-green/10 text-status-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">local_activity</span>
                </div>
                <div>
                    <div class="font-display-hero text-title-card font-bold text-slate-authority">
                        {{ count($coupons ?? []) }}
                    </div>
                    <div class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider">
                        Available Perks
                    </div>
                </div>
            </div>

            <a href="{{ route('products.index') }}" class="px-5 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold shadow-sm hover:opacity-95 active:scale-[0.99] transition-all flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                <span>Shop Marketplace</span>
            </a>
        </div>
    </div>

    {{-- Coupons Grid --}}
    @if(isset($coupons) && count($coupons) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($coupons as $coupon)
                @php
                    $isPercentage = $coupon->discount_type === 'percentage';
                    $discountLabel = $isPercentage ? intval($coupon->discount_value) . '% OFF' : '₹' . number_format($coupon->discount_value, 0) . ' OFF';
                    $colorTheme = match($loop->iteration % 3) {
                        1 => ['bg' => 'bg-emerald-50/70', 'badge' => 'bg-emerald-600', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700'],
                        2 => ['bg' => 'bg-amber-50/70', 'badge' => 'bg-amber-600', 'border' => 'border-amber-200', 'text' => 'text-amber-800'],
                        default => ['bg' => 'bg-indigo-50/70', 'badge' => 'bg-indigo-600', 'border' => 'border-indigo-200', 'text' => 'text-indigo-700']
                    };
                @endphp
                <div class="relative rounded-2xl bg-card-white border {{ $colorTheme['border'] }} shadow-xs hover:shadow-md transition-all p-5 flex flex-col justify-between gap-4 overflow-hidden group">
                    {{-- Top row: Discount Value & Status --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex flex-col">
                            <span class="font-label-micro text-[11px] font-bold uppercase tracking-wider {{ $colorTheme['text'] }}">
                                {{ $isPercentage ? 'Percentage Discount' : 'Flat Instant Voucher' }}
                            </span>
                            <div class="font-display-hero text-headline-section font-bold text-slate-authority tracking-tight mt-0.5">
                                {{ $discountLabel }}
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-status-green/10 text-status-green font-mono text-[10px] font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-status-green animate-pulse"></span>
                            ACTIVE
                        </span>
                    </div>

                    {{-- Coupon Code Box with 1-click copy --}}
                    <div class="p-3 rounded-xl bg-surface-container-low border border-dashed border-slate-authority/20 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-amber-action">sell</span>
                            <span class="font-mono font-bold text-body-lead text-slate-authority tracking-wider select-all">
                                {{ $coupon->code }}
                            </span>
                        </div>
                        <button type="button"
                                @click="copyToClipboard('{{ $coupon->code }}')"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1 shadow-xs cursor-pointer"
                                :class="copiedCode === '{{ $coupon->code }}' ? 'bg-status-green text-white scale-105' : 'bg-card-white hover:bg-surface-container text-slate-authority border border-slate-authority/10'">
                            <span class="material-symbols-outlined text-[14px]"
                                  x-text="copiedCode === '{{ $coupon->code }}' ? 'done' : 'content_copy'">
                            </span>
                            <span x-text="copiedCode === '{{ $coupon->code }}' ? 'Copied! ✓' : 'Copy Code'"></span>
                        </button>
                    </div>

                    {{-- Rules & Limits --}}
                    <div class="space-y-1.5 text-xs text-on-surface-variant font-body-small">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px] text-slate-authority/60">shopping_cart</span>
                            <span>Min order: <strong class="text-slate-authority font-mono font-semibold">₹{{ number_format($coupon->minimum_order_amount, 0) }}</strong></span>
                        </div>
                        @if($coupon->maximum_discount_amount && $isPercentage)
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px] text-slate-authority/60">currency_rupee</span>
                                <span>Max savings cap: <strong class="text-slate-authority font-mono font-semibold">₹{{ number_format($coupon->maximum_discount_amount, 0) }}</strong></span>
                            </div>
                        @endif
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px] text-slate-authority/60">event</span>
                            <span>Valid until: <strong class="text-slate-authority">{{ $coupon->expires_at ? $coupon->expires_at->format('d M, Y') : 'No Expiry' }}</strong></span>
                        </div>
                    </div>

                    {{-- Quick Action Button --}}
                    <div class="pt-2 border-t border-slate-authority/5 flex items-center justify-between">
                        <span class="font-label-micro text-label-micro text-on-surface-variant/80">
                            {{ max(0, $coupon->usage_limit - $coupon->used_count) }} claims remaining
                        </span>
                        <a href="{{ route('products.index') }}" class="font-label-micro text-label-micro font-bold text-amber-action hover:underline flex items-center gap-0.5">
                            <span>Use in Shop</span>
                            <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 gap-6 text-center bg-card-white rounded-2xl border border-slate-authority/10">
            <div class="w-20 h-20 rounded-full bg-surface-container flex items-center justify-center">
                <span class="material-symbols-outlined text-[42px] text-on-surface-variant">confirmation_number</span>
            </div>
            <div>
                <h2 class="font-title-card text-title-card font-bold text-slate-authority">No active coupons available</h2>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1">Watch for deals and seasonal offers. New coupons appear here automatically.</p>
            </div>
            <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm">
                Shop &amp; Earn Rewards
            </a>
        </div>
    @endif

    {{-- How to Redeem Instruction Box --}}
    <div class="p-6 rounded-2xl bg-surface-container-low border border-slate-authority/10">
        <h3 class="font-title-card text-title-card font-bold text-slate-authority flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-amber-action">info</span>
            <span>How to Redeem Your Coupons</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div class="p-4 rounded-xl bg-card-white border border-slate-authority/10 flex flex-col gap-1.5">
                <div class="font-mono text-xs font-bold text-amber-action">STEP 01</div>
                <div class="font-bold text-sm text-slate-authority">Copy Coupon Code</div>
                <p class="font-body-small text-xs text-on-surface-variant">Click the "Copy Code" button on any active coupon tile above.</p>
            </div>
            <div class="p-4 rounded-xl bg-card-white border border-slate-authority/10 flex flex-col gap-1.5">
                <div class="font-mono text-xs font-bold text-amber-action">STEP 02</div>
                <div class="font-bold text-sm text-slate-authority">Add Products to Cart</div>
                <p class="font-body-small text-xs text-on-surface-variant">Explore marketplace items and ensure your cart meets the minimum purchase value.</p>
            </div>
            <div class="p-4 rounded-xl bg-card-white border border-slate-authority/10 flex flex-col gap-1.5">
                <div class="font-mono text-xs font-bold text-amber-action">STEP 03</div>
                <div class="font-bold text-sm text-slate-authority">Apply at Checkout</div>
                <p class="font-body-small text-xs text-on-surface-variant">Paste the voucher code during checkout to instantly reduce your escrow payable total.</p>
            </div>
        </div>
    </div>

</div>
@endsection