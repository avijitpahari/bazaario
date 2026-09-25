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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Cart Items --}}
        <div class="lg:col-span-2 flex flex-col gap-3">
            @foreach($cart->items as $item)
            @php $product = $item->product; @endphp
            <div class="p-4 sm:p-5 rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-white/60 flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                {{-- Image --}}
                <div class="w-20 h-20 rounded-xl overflow-hidden bg-surface-container-high flex-shrink-0">
                    @php $img = $product->images->first(); @endphp
                    @if($img)
                        <img alt="{{ $product->name ?? 'Product' }}" class="w-full h-full object-cover" src="{{ $img->url }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[28px]">image</span>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 flex flex-col gap-1 min-w-0">
                    <h3 class="font-title-card text-title-card font-semibold text-slate-authority truncate">{{ $product->name ?? 'Unknown Product' }}</h3>
                    <p class="font-body-small text-body-small text-on-surface-variant">₹{{ number_format($product->price ?? 0, 2) }} each</p>

                    {{-- Quantity --}}
                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2 mt-2">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center gap-1 bg-surface-container-high rounded-lg overflow-hidden">
                            <button type="button" onclick="const i=this.nextElementSibling; if(i.value>1){i.value--;i.closest('form').submit();}" class="px-3 py-1.5 text-slate-authority hover:bg-surface-container-highest font-bold transition-colors">−</button>
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99"
                                class="w-12 text-center bg-transparent font-body-regular text-body-regular text-slate-authority font-semibold border-0 focus:outline-none">
                            <button type="button" onclick="const i=this.previousElementSibling; i.value++;i.closest('form').submit();" class="px-3 py-1.5 text-slate-authority hover:bg-surface-container-highest font-bold transition-colors">+</button>
                        </div>
                    </form>
                </div>

                {{-- Price + Remove --}}
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <p class="font-title-card text-title-card font-bold text-slate-authority">₹{{ number_format(($product->price ?? 0) * $item->quantity, 2) }}</p>
                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-error hover:text-error/70 font-body-small text-body-small font-medium flex items-center gap-1 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">delete</span> Remove
                        </button>
                    </form>
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

                {{-- Coupon --}}
                @auth
                <div class="pt-2 border-t border-surface-container">
                    @if($appliedCoupon)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-status-green/10 border border-status-green/20 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-status-green">confirmation_number</span>
                            <span class="font-body-small text-body-small font-semibold text-status-green">{{ $appliedCoupon['code'] }}</span>
                        </div>
                        <form action="{{ route('cart.coupon.remove') }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-error hover:text-error/70 transition-colors"><span class="material-symbols-outlined text-[18px]">close</span></button>
                        </form>
                    </div>
                    @else
                    <form action="{{ route('cart.coupon') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="coupon_code" placeholder="Enter coupon code"
                            class="flex-1 bg-surface-container-low border border-slate-authority/20 rounded-xl px-3 py-2 text-slate-authority font-body-small text-body-small focus:outline-none focus:border-amber-action transition-colors">
                        <button type="submit" class="px-3 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold hover:bg-slate-authority/90 transition-colors">
                            Apply
                        </button>
                    </form>
                    @endif
                </div>
                @endauth

                <a href="{{ route('checkout.index') }}"
                    class="w-full py-3.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-button-text font-bold text-center shadow-[0_4px_14px_rgba(245,166,35,0.35)] hover:opacity-95 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">payment</span>
                    Proceed to Checkout
                </a>

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