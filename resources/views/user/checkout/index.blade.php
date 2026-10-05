@extends('layouts.user')

@section('title', 'Checkout — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6" x-data="{ selectedAddress: '{{ $defaultAddress->id ?? 'new' }}', selectedSlot: 'Morning: 8 AM - 12 PM', selectedPayment: 'cod' }">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant">
        <a href="{{ route('home') }}" class="hover:text-amber-action transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('cart.index') }}" class="hover:text-amber-action transition-colors">Cart</a>
        <span>/</span>
        <span class="text-slate-authority font-semibold">Checkout</span>
    </nav>

    {{-- Page Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-amber-action/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold">Secure Checkout</span>
                <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">Order Checkout</h1>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                    Complete your delivery address, preferred time slot, and payment details below.
                </p>
            </div>
            <a href="{{ route('cart.index') }}" class="self-start md:self-auto px-4 py-2 rounded-xl bg-surface-container-low text-slate-authority font-button-text text-body-small font-semibold hover:bg-surface-container transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                Return to Cart
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
            <div class="font-bold flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[18px]">error</span>
                Please resolve the following errors:
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- Left Column: Delivery & Payment Options --}}
            <div class="lg:col-span-2 flex flex-col gap-6">

                {{-- Feature 40: Delivery Address Selection & Creation --}}
                <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6 flex flex-col gap-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-amber-action/20 text-slate-authority flex items-center justify-center font-bold text-sm">1</span>
                            <div>
                                <h2 class="font-title-card text-title-card font-bold text-slate-authority">Delivery Address</h2>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Select an existing address or add a new one</p>
                            </div>
                        </div>
                    </div>

                    {{-- Address List --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($addresses as $addr)
                            <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                                   :class="selectedAddress == '{{ $addr->id }}' ? 'border-amber-action bg-amber-50/30 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="address_id" value="{{ $addr->id }}"
                                               x-model="selectedAddress"
                                               class="text-amber-action focus:ring-amber-action w-4 h-4">
                                        <span class="font-semibold text-slate-authority text-sm">{{ $addr->full_name }}</span>
                                    </div>
                                    <span class="font-mono text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                        {{ $addr->type }}
                                    </span>
                                </div>
                                <div class="mt-2 text-xs text-on-surface-variant space-y-0.5 pl-6">
                                    <p>{{ $addr->phone }}</p>
                                    <p>{{ $addr->address_line_1 }}</p>
                                    @if($addr->address_line_2)<p>{{ $addr->address_line_2 }}</p>@endif
                                    <p>{{ $addr->city }}, {{ $addr->state }} - {{ $addr->postal_code }}</p>
                                </div>
                            </label>
                        @endforeach

                        {{-- New Address Radio Option --}}
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                               :class="selectedAddress == 'new' ? 'border-amber-action bg-amber-50/30 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="address_id" value="new"
                                       x-model="selectedAddress"
                                       class="text-amber-action focus:ring-amber-action w-4 h-4">
                                <span class="font-semibold text-slate-authority text-sm flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px] text-amber-action">add_circle</span>
                                    Add New Delivery Address
                                </span>
                            </div>
                            <p class="mt-2 text-xs text-on-surface-variant pl-6">Enter new shipping details directly below</p>
                        </label>
                    </div>

                    {{-- Inline New Address Form --}}
                    <div x-show="selectedAddress === 'new'" x-cloak class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3 mt-2">
                        <h3 class="font-title-card text-sm font-bold text-slate-authority flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-amber-action">home_pin</span>
                            New Delivery Address Details
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                                <input type="text" name="new_full_name" value="{{ old('new_full_name', $user->name) }}"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-action focus:border-amber-action"
                                       placeholder="Recipient's name">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number *</label>
                                <input type="text" name="new_phone" value="{{ old('new_phone', $user->phone) }}"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-action focus:border-amber-action"
                                       placeholder="10-digit mobile number">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Address Line 1 *</label>
                                <input type="text" name="new_address_line_1" value="{{ old('new_address_line_1') }}"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-action focus:border-amber-action"
                                       placeholder="House/Flat No., Building Name, Street">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Address Line 2 (Optional)</label>
                                <input type="text" name="new_address_line_2" value="{{ old('new_address_line_2') }}"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-action focus:border-amber-action"
                                       placeholder="Landmark, Area, Colony">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">City *</label>
                                <input type="text" name="new_city" value="{{ old('new_city') }}"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-action focus:border-amber-action"
                                       placeholder="e.g. Kolkata">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">State *</label>
                                <input type="text" name="new_state" value="{{ old('new_state', 'West Bengal') }}"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-action focus:border-amber-action"
                                       placeholder="State">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Postal Code (PIN) *</label>
                                <input type="text" name="new_postal_code" value="{{ old('new_postal_code') }}"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-action focus:border-amber-action"
                                       placeholder="6-digit PIN code">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Address Type</label>
                                <select name="new_type" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-action focus:border-amber-action">
                                    <option value="home">Home (All Day Delivery)</option>
                                    <option value="work">Work (10 AM - 6 PM)</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Feature 41: Select Delivery Time Slot --}}
                <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6 flex flex-col gap-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-amber-action/20 text-slate-authority flex items-center justify-center font-bold text-sm">2</span>
                            <div>
                                <h2 class="font-title-card text-title-card font-bold text-slate-authority">Delivery Time Slot</h2>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Choose your preferred arrival window</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($timeSlots as $slot)
                            @php
                                $icon = str_contains($slot, 'Morning') ? 'wb_twilight' : (str_contains($slot, 'Afternoon') ? 'wb_sunny' : 'nightlight');
                            @endphp
                            <label class="flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all"
                                   :class="selectedSlot === '{{ $slot }}' ? 'border-amber-action bg-amber-50/40 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center justify-between">
                                    <span class="material-symbols-outlined text-[20px]"
                                          :class="selectedSlot === '{{ $slot }}' ? 'text-amber-action' : 'text-slate-400'">{{ $icon }}</span>
                                    <input type="radio" name="delivery_time_slot" value="{{ $slot }}"
                                           x-model="selectedSlot"
                                           class="text-amber-action focus:ring-amber-action w-4 h-4">
                                </div>
                                <span class="font-semibold text-slate-authority text-xs mt-3">{{ $slot }}</span>
                                <span class="text-[11px] text-on-surface-variant mt-0.5">Express Dispatch</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Feature 42: Cash on Delivery & Payment Methods --}}
                <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6 flex flex-col gap-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-amber-action/20 text-slate-authority flex items-center justify-center font-bold text-sm">3</span>
                            <div>
                                <h2 class="font-title-card text-title-card font-bold text-slate-authority">Payment Method</h2>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Select how you want to pay for your consignment</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        {{-- Cash On Delivery (COD) Option --}}
                        <label class="relative flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all"
                               :class="selectedPayment === 'cod' ? 'border-amber-action bg-amber-50/40 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="payment_method" value="cod"
                                   x-model="selectedPayment"
                                   class="mt-1 text-amber-action focus:ring-amber-action w-4 h-4">
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-action text-[22px]">payments</span>
                                        <span class="font-bold text-slate-authority text-sm">Cash on Delivery (COD)</span>
                                    </div>
                                    <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Recommended
                                    </span>
                                </div>
                                <p class="text-xs text-on-surface-variant mt-1">
                                    Pay with cash upon delivery at your doorstep. Please ensure exact cash is ready for the delivery partner.
                                </p>
                            </div>
                        </label>

                        {{-- UPI / Digital Options --}}
                        <label class="relative flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all"
                               :class="selectedPayment === 'upi' ? 'border-amber-action bg-amber-50/40 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="payment_method" value="upi"
                                   x-model="selectedPayment"
                                   class="mt-1 text-amber-action focus:ring-amber-action w-4 h-4">
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-600 text-[22px]">qr_code_2</span>
                                        <span class="font-bold text-slate-authority text-sm">UPI (GPay / PhonePe / Paytm)</span>
                                    </div>
                                    <span class="font-mono text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">Instant</span>
                                </div>
                                <p class="text-xs text-on-surface-variant mt-1">
                                    UPI QR code and payment link generated upon confirmation.
                                </p>
                            </div>
                        </label>

                        {{-- Card Option --}}
                        <label class="relative flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all"
                               :class="selectedPayment === 'card' ? 'border-amber-action bg-amber-50/40 shadow-sm' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="payment_method" value="card"
                                   x-model="selectedPayment"
                                   class="mt-1 text-amber-action focus:ring-amber-action w-4 h-4">
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-600 text-[22px]">credit_card</span>
                                        <span class="font-bold text-slate-authority text-sm">Credit / Debit Card</span>
                                    </div>
                                    <span class="font-mono text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">Visa / MC / RuPay</span>
                                </div>
                                <p class="text-xs text-on-surface-variant mt-1">
                                    Secure 256-bit encrypted card processing.
                                </p>
                            </div>
                        </label>
                    </div>

                    {{-- Order Delivery Notes --}}
                    <div class="mt-2 pt-3 border-t border-slate-100">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Delivery Instructions / Landmark Notes (Optional)
                        </label>
                        <textarea name="notes" rows="2"
                                  class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-action focus:border-amber-action"
                                  placeholder="e.g. Ring the bell twice, leave with security at gate, call upon arrival..."></textarea>
                    </div>
                </div>

            </div>

            {{-- Right Column: Order Summary & Place Order CTA --}}
            <div class="flex flex-col gap-6">

                {{-- Order Summary Card --}}
                <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6 flex flex-col gap-4">
                    <h2 class="font-title-card text-title-card font-bold text-slate-authority border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span>Order Summary</span>
                        <span class="font-mono text-xs text-amber-action font-semibold">{{ $cart->items->sum('quantity') }} Item(s)</span>
                    </h2>

                    {{-- Cart Items Quick Preview --}}
                    <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto pr-1">
                        @foreach($cart->items as $item)
                            <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                        @php $img = $item->product->images->first()?->url ?? null; @endphp
                                        @if($img)
                                            <img src="{{ $img }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-authority truncate">{{ $item->product->name }}</p>
                                        <p class="text-on-surface-variant font-mono">Qty: {{ $item->quantity }} × ₹{{ number_format($item->product->price ?? 0, 2) }}</p>
                                    </div>
                                </div>
                                <span class="font-mono font-bold text-slate-authority shrink-0">
                                    ₹{{ number_format(($item->product->price ?? 0) * $item->quantity, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Financial Breakdown --}}
                    <div class="pt-3 border-t border-slate-100 flex flex-col gap-2 font-body-small text-body-small">
                        <div class="flex justify-between text-on-surface-variant">
                            <span>Subtotal</span>
                            <span class="font-mono font-semibold">₹{{ number_format($subtotal, 2) }}</span>
                        </div>

                        @if($discount > 0)
                            <div class="flex justify-between text-emerald-700">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">local_offer</span>
                                    Coupon Discount ({{ session('coupon.code') }})
                                </span>
                                <span class="font-mono font-semibold">-₹{{ number_format($discount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-on-surface-variant">
                            <span>Delivery & Handling</span>
                            <span class="font-mono font-semibold">
                                @if($shipping > 0)
                                    ₹{{ number_format($shipping, 2) }}
                                @else
                                    <span class="text-emerald-700 font-bold">FREE</span>
                                @endif
                            </span>
                        </div>

                        <div class="h-px bg-slate-200 my-1"></div>

                        <div class="flex justify-between font-bold text-slate-authority text-base">
                            <span>Total Payable</span>
                            <span class="font-mono text-lg text-amber-action">₹{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    {{-- Feature 43: Place Order Button --}}
                    <button type="submit" id="btn-place-order"
                            class="w-full py-3.5 px-4 rounded-xl bg-amber-action text-slate-authority font-button-text font-bold shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2 text-sm mt-2">
                        <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                        Place Order (COD) • ₹{{ number_format($total, 2) }}
                    </button>

                    <p class="text-[11px] text-center text-on-surface-variant">
                        By placing this order, you agree to Bazaario's Terms of Sale & Hyperlocal Return Policy.
                    </p>
                </div>

                {{-- Trust Badges --}}
                <div class="rounded-2xl bg-surface-container-lowest/80 border border-slate-200/80 p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-amber-action text-[22px]">verified</span>
                        <div class="text-xs">
                            <p class="font-bold text-slate-authority">100% Genuine Marketplace</p>
                            <p class="text-on-surface-variant">Direct from verified hyperlocal merchants</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-600 text-[22px]">lock</span>
                        <div class="text-xs">
                            <p class="font-bold text-slate-authority">Pessimistic Stock Lock</p>
                            <p class="text-on-surface-variant">Inventory reserved safely upon order</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-indigo-600 text-[22px]">local_shipping</span>
                        <div class="text-xs">
                            <p class="font-bold text-slate-authority">Hyperlocal Swift Delivery</p>
                            <p class="text-on-surface-variant">Timely delivery in chosen time slot</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection
