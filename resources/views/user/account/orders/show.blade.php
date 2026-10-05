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

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @php
        $statusConfig = match($order->order_status) {
            'completed' => ['label' => 'Delivered', 'bg' => 'bg-status-green/10', 'text' => 'text-status-green', 'icon' => 'check_circle'],
            'processing' => ['label' => 'Processing', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'icon' => 'sync'],
            'shipped' => ['label' => 'Shipped', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'icon' => 'local_shipping'],
            'cancelled' => ['label' => 'Cancelled', 'bg' => 'bg-error/10', 'text' => 'text-error', 'icon' => 'cancel'],
            default => ['label' => 'Pending', 'bg' => 'bg-amber-action/20', 'text' => 'text-slate-authority', 'icon' => 'hourglass_empty'],
        };

        $timeSlot = 'Morning: 8 AM - 12 PM';
        if ($order->notes && preg_match('/Time Slot:\s*([^|\n]+)/i', $order->notes, $matches)) {
            $timeSlot = trim($matches[1]);
        }
    @endphp

    {{-- Order Hero Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-amber-action/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold">Order Telemetry</span>
                <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">Order #{{ $order->order_number }}</h1>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                    Placed on {{ $order->placed_at?->format('d M Y, h:i A') ?? $order->created_at?->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A') }}
                </p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-2 font-body-small text-body-small {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-4 py-2 rounded-full font-semibold border border-current/20">
                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">{{ $statusConfig['icon'] }}</span>
                    {{ $statusConfig['label'] }}
                </span>

                {{-- Feature 49: 1-Click Reorder Button --}}
                <form action="{{ route('user.orders.reorder', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold hover:bg-slate-authority/90 transition-colors flex items-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">replay</span>
                        Reorder Items
                    </button>
                </form>

                {{-- Feature 48: Order Cancellation Button --}}
                @if(in_array($order->order_status, ['pending', 'processing']))
                    <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to cancel this order? Any deducted inventory will be immediately restored.');">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-50 text-red-700 border border-red-200 font-button-text text-body-small font-semibold hover:bg-red-100 transition-colors flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                            Cancel Order
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Feature 47: Order Status Lifecycle Progression Tracker --}}
    @if($order->order_status !== 'cancelled')
        @php
            $steps = [
                'pending'    => ['label' => 'Order Placed', 'desc' => 'Order received', 'icon' => 'receipt'],
                'processing' => ['label' => 'Processing', 'desc' => 'Merchant packing', 'icon' => 'inventory_2'],
                'shipped'    => ['label' => 'In Transit', 'desc' => 'Courier dispatched', 'icon' => 'local_shipping'],
                'completed'  => ['label' => 'Delivered', 'desc' => 'Consignment handed over', 'icon' => 'home'],
            ];
            $stages = ['pending', 'processing', 'shipped', 'completed'];
            $currentStatus = $order->order_status;
            // Map 'shipped' to order status if any seller order is shipped
            if ($currentStatus === 'processing' && $order->sellerOrders->contains('status', 'shipped')) {
                $currentStatus = 'shipped';
            }
            $currentIndex = array_search($currentStatus, $stages);
            if ($currentIndex === false) { $currentIndex = 0; }
        @endphp
        <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6">
            <h2 class="font-title-card text-title-card font-bold text-slate-authority mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-action text-[22px]">timeline</span>
                Order Status Tracking
            </h2>

            <div class="relative">
                {{-- Connector Line --}}
                <div class="hidden sm:block absolute top-1/2 left-8 right-8 -translate-y-1/2 h-1 bg-slate-200 -z-0">
                    <div class="h-full bg-amber-action transition-all duration-500"
                         style="width: {{ ($currentIndex / (count($stages) - 1)) * 100 }}%;"></div>
                </div>

                {{-- Steps --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 relative z-10">
                    @foreach($stages as $idx => $stageKey)
                        @php
                            $step = $steps[$stageKey];
                            $isComplete = $idx <= $currentIndex;
                            $isCurrent = $idx === $currentIndex;
                        @endphp
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 shadow-sm
                                        {{ $isCurrent
                                            ? 'bg-amber-action text-slate-authority ring-4 ring-amber-100 scale-110'
                                            : ($isComplete
                                                ? 'bg-emerald-600 text-white'
                                                : 'bg-white text-slate-400 border-2 border-slate-300') }}">
                                <span class="material-symbols-outlined text-[20px]">{{ $isComplete && !$isCurrent ? 'check' : $step['icon'] }}</span>
                            </div>
                            <span class="font-semibold text-xs mt-3 text-slate-authority">{{ $step['label'] }}</span>
                            <span class="text-[11px] text-on-surface-variant mt-0.5">{{ $step['desc'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        {{-- Cancelled Order Banner --}}
        <div class="rounded-2xl bg-red-50 border border-red-200 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">cancel</span>
            </div>
            <div>
                <h3 class="font-bold text-red-900 text-sm">Order Cancelled</h3>
                <p class="text-xs text-red-700 mt-0.5">
                    This order has been cancelled and all product inventory has been restored. If you were charged, a refund has been initiated.
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Left: Feature 46 Multi-Seller Order Consignments & Tracking --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-title-card text-title-card font-bold text-slate-authority flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-action text-[22px]">local_shipping</span>
                        Per-Seller Shipment Tracking ({{ $order->sellerOrders->count() }} Seller Consignment(s))
                    </h2>
                </div>

                {{-- Loop Over Each Seller Order --}}
                @foreach($order->sellerOrders as $sellerOrder)
                    @php
                        $seller = $sellerOrder->seller;
                        $profile = $seller?->sellerProfile;
                        $shopName = $profile?->shop_name ?? ($seller?->name ?? 'Verified Merchant');
                        $trackingNumber = $sellerOrder->tracking_number ?? 'BZ-TRK-' . $sellerOrder->id;
                    @endphp
                    <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 overflow-hidden">
                        {{-- Merchant Consignment Bar --}}
                        <div class="p-4 bg-slate-50 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-sm shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-bold text-slate-authority text-sm">{{ $shopName }}</h3>
                                        <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-slate-200 text-slate-700">
                                            Sub-Order: #{{ $sellerOrder->seller_order_number }}
                                        </span>
                                    </div>
                                    @if($profile && $profile->city)
                                        <p class="font-mono text-[11px] text-on-surface-variant flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[12px]">location_on</span>
                                            {{ $profile->city }}, {{ $profile->state ?? 'India' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3 self-start sm:self-auto">
                                <span class="font-mono text-[10px] font-bold uppercase px-2.5 py-1 rounded-full border
                                    {{ $sellerOrder->status === 'delivered'
                                        ? 'bg-emerald-50 text-emerald-800 border-emerald-200'
                                        : ($sellerOrder->status === 'shipped'
                                            ? 'bg-indigo-50 text-indigo-800 border-indigo-200'
                                            : ($sellerOrder->status === 'cancelled'
                                                ? 'bg-red-50 text-red-800 border-red-200'
                                                : 'bg-amber-50 text-amber-800 border-amber-200')) }}">
                                    {{ $sellerOrder->status }}
                                </span>
                            </div>
                        </div>

                        {{-- Feature 46: Courier & Telemetry Details --}}
                        <div class="px-4 py-3 bg-amber-50/30 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-action text-[18px]">near_me</span>
                                <span class="text-slate-600">Courier Tracking #:</span>
                                <span class="font-mono font-bold text-slate-authority bg-white px-2 py-0.5 rounded border border-slate-200">
                                    {{ $trackingNumber }}
                                </span>
                            </div>
                            <div class="text-on-surface-variant text-[11px]">
                                @if($sellerOrder->shipped_at)
                                    <span>Dispatched: {{ $sellerOrder->shipped_at->format('d M Y, h:i A') }}</span>
                                @else
                                    <span class="text-amber-700 font-semibold">Preparing for Courier Handover</span>
                                @endif
                            </div>
                        </div>

                        {{-- Items under this merchant --}}
                        <div class="divide-y divide-slate-100 p-4 space-y-2">
                            @foreach($sellerOrder->items as $item)
                                <div class="flex items-center justify-between gap-4 p-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center">
                                            @php $img = $item->product?->primaryImage?->url ?? ($item->product_image ? Storage::url($item->product_image) : null); @endphp
                                            @if($img)
                                                <img src="{{ $img }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="material-symbols-outlined text-slate-400 text-[24px]">inventory_2</span>
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
                @endforeach
            </div>

        </div>

        {{-- Right: Financial Summary & Delivery Address --}}
        <div class="flex flex-col gap-6">

            {{-- Price Summary --}}
            <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6 flex flex-col gap-3">
                <h2 class="font-title-card text-title-card font-bold text-slate-authority border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>Order Summary</span>
                    <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $order->payment_status }}
                    </span>
                </h2>

                <div class="flex flex-col gap-2 font-body-small text-body-small">
                    <div class="flex justify-between text-on-surface-variant">
                        <span>Subtotal</span>
                        <span class="font-mono font-semibold">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-emerald-700">
                            <span>Discount</span>
                            <span class="font-mono font-semibold">-₹{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-on-surface-variant">
                        <span>Shipping</span>
                        <span class="font-mono font-semibold">₹{{ number_format($order->shipping_amount, 2) }}</span>
                    </div>

                    <div class="h-px bg-slate-200 my-1"></div>

                    <div class="flex justify-between font-bold text-slate-authority text-base">
                        <span>Total Amount</span>
                        <span class="font-mono text-lg text-amber-action">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-1.5 font-label-micro text-label-micro mt-2">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Payment Method:</span>
                        <span class="text-slate-authority font-bold uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Payment Status:</span>
                        <span class="{{ $order->payment_status === 'paid' ? 'text-emerald-700' : 'text-amber-action' }} font-bold uppercase">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Delivery Info Card --}}
            <div class="rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-slate-200/80 p-6 flex flex-col gap-3">
                <h2 class="font-title-card text-title-card font-bold text-slate-authority flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-amber-action text-[20px]">local_shipping</span>
                    Delivery Information
                </h2>
                <div class="font-body-small text-body-small text-on-surface-variant flex flex-col gap-1">
                    <p class="font-bold text-slate-authority text-sm">{{ $order->delivery_full_name }}</p>
                    <p class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">phone</span> {{ $order->delivery_phone }}</p>
                    <p>{{ $order->delivery_address_line_1 }}{{ $order->delivery_address_line_2 ? ', ' . $order->delivery_address_line_2 : '' }}</p>
                    <p>{{ $order->delivery_city }}, {{ $order->delivery_state }} {{ $order->delivery_postal_code }}</p>
                    <p>{{ $order->delivery_country }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex flex-col gap-1 text-xs">
                    <span class="text-slate-500 font-bold uppercase text-[10px]">Time Slot:</span>
                    <p class="font-semibold text-slate-authority">{{ $timeSlot }}</p>
                </div>

                @if($order->notes)
                    <div class="p-3 rounded-xl bg-slate-50 font-body-small text-body-small text-on-surface-variant border border-slate-200">
                        <span class="font-semibold text-slate-authority">Customer Note: </span>{{ $order->notes }}
                    </div>
                @endif
            </div>

            <a href="{{ route('user.orders.index') }}"
               class="px-4 py-2.5 rounded-xl bg-surface-container-low text-slate-authority font-button-text text-body-small font-semibold hover:bg-surface-container transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Back to All Orders
            </a>

        </div>

    </div>

</div>
@endsection
