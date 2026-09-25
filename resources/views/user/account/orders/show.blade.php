@extends('layouts.user')

@section('title', 'Order #' . $order->order_number . ' — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant">
        <a href="{{ route('home') }}" class="hover:text-amber-action transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('user.orders.index') }}" class="hover:text-amber-action transition-colors">Orders</a>
        <span>/</span>
        <span class="text-slate-authority font-semibold">#{{ $order->order_number }}</span>
    </nav>

    @php
        $statusConfig = match($order->order_status) {
            'completed' => ['label' => 'Delivered', 'bg' => 'bg-status-green/10', 'text' => 'text-status-green', 'icon' => 'check_circle'],
            'processing' => ['label' => 'Shipped', 'bg' => 'bg-primary-fixed', 'text' => 'text-slate-authority', 'icon' => 'local_shipping'],
            'cancelled' => ['label' => 'Cancelled', 'bg' => 'bg-error/10', 'text' => 'text-error', 'icon' => 'cancel'],
            default => ['label' => 'Pending', 'bg' => 'bg-amber-action/20', 'text' => 'text-slate-authority', 'icon' => 'hourglass_empty'],
        };
    @endphp

    {{-- Order Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-amber-action/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold">Order Details</span>
                <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">#{{ $order->order_number }}</h1>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                    Placed on {{ $order->placed_at?->format('d M Y, h:i A') ?? $order->created_at?->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A') }}
                </p>
            </div>
            <span class="self-start inline-flex items-center gap-2 font-body-small text-body-small {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-4 py-2 rounded-full font-semibold">
                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">{{ $statusConfig['icon'] }}</span>
                {{ $statusConfig['label'] }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Items --}}
        <div class="lg:col-span-2 flex flex-col gap-4">
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 shadow-sm flex flex-col gap-4">
                <h2 class="font-title-card text-title-card font-bold text-slate-authority flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-action text-[22px]">inventory_2</span>
                    Order Items
                </h2>
                @foreach($order->sellerOrders as $sellerOrder)
                    @foreach($sellerOrder->items as $item)
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-surface-container-low/80 border border-slate-authority/5">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-surface-container-high flex-shrink-0">
                            @if($item->product_image)
                                <img alt="{{ $item->product_name }}" class="w-full h-full object-cover" src="{{ Storage::url($item->product_image) }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col flex-1 min-w-0">
                            <h3 class="font-body-regular text-body-regular font-semibold text-slate-authority truncate">{{ $item->product_name }}</h3>
                            <p class="font-body-small text-body-small text-on-surface-variant">SKU: {{ $item->sku ?? 'N/A' }} • Qty: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-body-small text-body-small text-on-surface-variant">₹{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                            <p class="font-title-card text-title-card font-bold text-slate-authority">₹{{ number_format($item->total_price, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Right: Summary --}}
        <div class="flex flex-col gap-4">

            {{-- Price Summary --}}
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 shadow-sm flex flex-col gap-3">
                <h2 class="font-title-card text-title-card font-bold text-slate-authority">Order Summary</h2>
                <div class="flex flex-col gap-2 font-body-small text-body-small">
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-status-green">
                        <span>Discount</span>
                        <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Shipping</span>
                        <span>₹{{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    <div class="h-px bg-surface-container-high my-1"></div>
                    <div class="flex justify-between font-semibold text-slate-authority font-title-card text-title-card">
                        <span>Total</span>
                        <span>₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
                <div class="mt-2 p-3 rounded-xl bg-surface-container-low/80 flex flex-col gap-1.5 font-label-micro text-label-micro">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Payment Method</span>
                        <span class="text-slate-authority font-semibold uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Payment Status</span>
                        <span class="{{ $order->payment_status === 'paid' ? 'text-status-green' : ($order->payment_status === 'failed' ? 'text-error' : 'text-amber-action') }} font-semibold">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Delivery Info --}}
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 shadow-sm flex flex-col gap-3">
                <h2 class="font-title-card text-title-card font-bold text-slate-authority flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-action text-[20px]">local_shipping</span>
                    Delivery Address
                </h2>
                <div class="font-body-small text-body-small text-on-surface-variant flex flex-col gap-1">
                    <p class="font-semibold text-slate-authority">{{ $order->delivery_full_name }}</p>
                    <p>{{ $order->delivery_phone }}</p>
                    <p>{{ $order->delivery_address_line_1 }}{{ $order->delivery_address_line_2 ? ', ' . $order->delivery_address_line_2 : '' }}</p>
                    <p>{{ $order->delivery_city }}, {{ $order->delivery_state }} {{ $order->delivery_postal_code }}</p>
                    <p>{{ $order->delivery_country }}</p>
                </div>
                @if($order->notes)
                <div class="p-3 rounded-xl bg-surface-container-low/80 font-body-small text-body-small text-on-surface-variant">
                    <span class="font-semibold text-slate-authority">Note: </span>{{ $order->notes }}
                </div>
                @endif
            </div>

            <a href="{{ route('user.orders.index') }}" class="px-4 py-2.5 rounded-xl bg-surface-container-low text-slate-authority font-button-text text-body-small font-semibold hover:bg-surface-container transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Back to Orders
            </a>
        </div>
    </div>
</div>
@endsection
