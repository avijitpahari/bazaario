@extends('layouts.user')

@section('title', 'Order Confirmed #' . $order->order_number . ' — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-8 flex flex-col gap-6">

    @php
        $timeSlot = 'Morning: 8 AM - 12 PM';
        if ($order->notes && preg_match('/Time Slot:\s*([^|\n]+)/i', $order->notes, $matches)) {
            $timeSlot = trim($matches[1]);
        }
        $estDelivery = ($order->placed_at ?? now())->addDays(3)->format('l, d M Y');
    @endphp

    {{-- Order Confirmation Banner --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-50 via-surface-container-lowest to-amber-50/40 p-8 shadow-sm border border-emerald-200/80 text-center flex flex-col items-center">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 shadow-inner">
            <span class="material-symbols-outlined text-[36px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
        <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-emerald-700 font-bold">Order Confirmed</span>
        <h1 class="font-headline-section text-2xl sm:text-3xl font-bold text-slate-authority mt-1">Thank you for your order!</h1>
        <p class="font-body-small text-on-surface-variant max-w-xl mt-2 text-sm">
            Your consignment has been securely placed with our hyperlocal merchants. We've sent a confirmation receipt to your registered account.
        </p>
        <div class="mt-4 inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900 text-amber-400 font-mono text-xs font-bold shadow-sm">
            <span>Order Reference:</span>
            <span>#{{ $order->order_number }}</span>
        </div>
    </div>

    {{-- Order Details Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Left: Items Breakdown & Multi-Seller Details --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Feature 44: Items Breakdown --}}
            <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6 flex flex-col gap-4">
                <h2 class="font-title-card text-title-card font-bold text-slate-authority flex items-center justify-between border-b border-slate-100 pb-3">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-action text-[22px]">inventory_2</span>
                        Purchased Items Breakdown
                    </span>
                    <span class="font-mono text-xs text-on-surface-variant">
                        {{ $order->sellerOrders->sum(fn($so) => $so->items->count()) }} Line Item(s)
                    </span>
                </h2>

                <div class="space-y-6">
                    @forelse($order->sellerOrders as $sellerOrder)
                        @php
                            $seller = $sellerOrder->seller;
                            $shopName = $seller?->sellerProfile?->shop_name ?? ($seller?->name ?? 'Verified Merchant');
                        @endphp
                        <div class="rounded-xl border border-slate-200/80 overflow-hidden bg-slate-50/50">
                            {{-- Merchant Header --}}
                            <div class="px-4 py-3 bg-slate-100 border-b border-slate-200/80 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-slate-700">storefront</span>
                                    <span class="font-bold text-slate-authority">{{ $shopName }}</span>
                                    <span class="font-mono text-[10px] text-slate-500">({{ $sellerOrder->seller_order_number }})</span>
                                </div>
                                <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 uppercase text-[10px]">
                                    {{ $sellerOrder->status }}
                                </span>
                            </div>

                            {{-- Line Items --}}
                            <div class="divide-y divide-slate-100 p-3 space-y-2 bg-white">
                                @foreach($sellerOrder->items as $item)
                                    <div class="flex items-center justify-between gap-4 p-2">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                                                @if($item->product_image)
                                                    <img src="{{ Storage::url($item->product_image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="material-symbols-outlined text-slate-400 text-[20px]">package_2</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-semibold text-slate-authority text-sm truncate">{{ $item->product_name }}</h4>
                                                <p class="font-mono text-xs text-on-surface-variant">
                                                    Qty: {{ $item->quantity }} • ₹{{ number_format($item->unit_price, 2) }} each
                                                    @if($item->sku) • SKU: {{ $item->sku }} @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="font-mono font-bold text-slate-authority text-sm">
                                                ₹{{ number_format($item->total_price, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-on-surface-variant">No items recorded for this order.</p>
                    @endforelse
                </div>
            </div>

            {{-- Delivery & Time Slot Info Card --}}
            <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-title-card text-sm font-bold text-slate-authority flex items-center gap-2 border-b border-slate-100 pb-2 mb-3">
                        <span class="material-symbols-outlined text-amber-action text-[20px]">local_shipping</span>
                        Delivery Address
                    </h3>
                    <div class="text-xs text-on-surface-variant space-y-1">
                        <p class="font-bold text-slate-authority text-sm">{{ $order->delivery_full_name }}</p>
                        <p class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">phone</span> {{ $order->delivery_phone }}</p>
                        <p>{{ $order->delivery_address_line_1 }}</p>
                        @if($order->delivery_address_line_2)<p>{{ $order->delivery_address_line_2 }}</p>@endif
                        <p>{{ $order->delivery_city }}, {{ $order->delivery_state }} - {{ $order->delivery_postal_code }}</p>
                        <p>{{ $order->delivery_country }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="font-title-card text-sm font-bold text-slate-authority flex items-center gap-2 border-b border-slate-100 pb-2 mb-3">
                        <span class="material-symbols-outlined text-amber-action text-[20px]">schedule</span>
                        Estimated Delivery & Slot
                    </h3>
                    <div class="text-xs text-on-surface-variant space-y-2">
                        <div>
                            <span class="text-slate-500 uppercase text-[10px] font-bold tracking-wider">Estimated Arrival</span>
                            <p class="font-bold text-slate-authority text-sm">{{ $estDelivery }}</p>
                        </div>
                        <div>
                            <span class="text-slate-500 uppercase text-[10px] font-bold tracking-wider">Chosen Time Slot</span>
                            <p class="font-semibold text-amber-action text-xs">{{ $timeSlot }}</p>
                        </div>
                        @if($order->notes)
                            <div class="pt-2 border-t border-slate-100">
                                <span class="text-slate-500 uppercase text-[10px] font-bold tracking-wider">Customer Notes</span>
                                <p class="text-slate-700 italic">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: Financial Receipt & COD Instructions --}}
        <div class="flex flex-col gap-6">

            {{-- Financial Receipt Card --}}
            <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6 flex flex-col gap-4">
                <h2 class="font-title-card text-title-card font-bold text-slate-authority border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>Payment Receipt</span>
                    <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $order->payment_status }}
                    </span>
                </h2>

                <div class="flex flex-col gap-2 font-body-small text-body-small">
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Items Subtotal</span>
                        <span class="font-mono font-semibold">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-emerald-700">
                            <span>Coupon Discount</span>
                            <span class="font-mono font-semibold">-₹{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-on-surface-variant">
                        <span>Shipping & Handling</span>
                        <span class="font-mono font-semibold">₹{{ number_format($order->shipping_amount, 2) }}</span>
                    </div>

                    <div class="h-px bg-slate-200 my-1"></div>

                    <div class="flex justify-between font-bold text-slate-authority text-base">
                        <span>Total Amount</span>
                        <span class="font-mono text-lg text-amber-action">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                {{-- Payment Method Breakdown --}}
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Payment Method:</span>
                        <span class="font-bold text-slate-authority uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Order Status:</span>
                        <span class="font-bold text-amber-action uppercase">{{ $order->order_status }}</span>
                    </div>
                </div>

                {{-- Cash On Delivery Instructions --}}
                @if($order->payment_method === 'cod')
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                        <p class="font-bold flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-amber-700">payments</span>
                            Cash on Delivery Instructions
                        </p>
                        <p class="text-[11px] leading-relaxed">
                            Please keep exact cash of <strong>₹{{ number_format($order->total_amount, 2) }}</strong> ready at the time of delivery. A physical printed invoice and digital receipt will be provided upon handover.
                        </p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-col gap-2 pt-2">
                    <a href="{{ route('user.orders.show', $order->id) }}"
                       class="w-full py-2.5 px-4 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-xs font-semibold hover:bg-slate-authority/90 transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                        View Order Details & Tracking
                    </a>

                    <a href="{{ route('user.orders.index') }}"
                       class="w-full py-2.5 px-4 rounded-xl bg-surface-container-low text-slate-authority font-button-text text-xs font-semibold hover:bg-surface-container transition-colors flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">history</span>
                        View Order History
                    </a>

                    <a href="{{ route('products.index') }}"
                       class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-slate-700 font-button-text text-xs font-semibold hover:bg-slate-50 transition-colors flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
                        Continue Shopping
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
