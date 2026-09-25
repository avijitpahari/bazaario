@extends('layouts.user')

@section('title', 'My Orders — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Page Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-amber-action/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold">Order History</span>
                <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">My Orders</h1>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                    {{ $statusCounts['all'] }} total order(s) • {{ $statusCounts['processing'] }} in transit
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="self-start md:self-auto px-5 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm hover:opacity-95 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                Continue Shopping
            </a>
        </div>
    </div>

    {{-- Status Filter Tabs --}}
    <div class="flex items-center gap-2 flex-wrap">
        @foreach(['all' => 'All Orders', 'pending' => 'Pending', 'processing' => 'Processing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
        <a href="{{ route('user.orders.index', ['status' => $key]) }}"
            class="px-4 py-2 rounded-full font-body-small text-body-small font-semibold transition-all
                {{ $status === $key
                    ? 'bg-slate-authority text-canvas-ivory shadow-sm'
                    : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
            {{ $label }}
            <span class="ml-1.5 font-label-micro text-label-micro {{ $status === $key ? 'text-amber-action' : 'text-on-surface-variant' }}">
                ({{ $statusCounts[$key] }})
            </span>
        </a>
        @endforeach
    </div>

    {{-- Orders List --}}
    <div class="flex flex-col gap-3">
        @forelse($orders as $order)
        @php
            $firstItem = $order->sellerOrders->first()?->items->first();
            $statusConfig = match($order->order_status) {
                'completed' => ['label' => 'Delivered', 'bg' => 'bg-status-green/10', 'text' => 'text-status-green', 'dot' => 'bg-status-green', 'icon' => 'check_circle'],
                'processing' => ['label' => 'Shipped', 'bg' => 'bg-primary-fixed', 'text' => 'text-slate-authority', 'dot' => 'bg-slate-authority', 'icon' => 'local_shipping'],
                'cancelled' => ['label' => 'Cancelled', 'bg' => 'bg-error/10', 'text' => 'text-error', 'dot' => 'bg-error', 'icon' => 'cancel'],
                'refunded' => ['label' => 'Refunded', 'bg' => 'bg-outline/10', 'text' => 'text-outline', 'dot' => 'bg-outline', 'icon' => 'currency_exchange'],
                default => ['label' => 'Pending', 'bg' => 'bg-amber-action/20', 'text' => 'text-slate-authority', 'dot' => 'bg-amber-action', 'icon' => 'hourglass_empty'],
            };
            $paymentConfig = match($order->payment_status) {
                'paid' => ['label' => 'Paid', 'class' => 'text-status-green'],
                'failed' => ['label' => 'Failed', 'class' => 'text-error'],
                'refunded' => ['label' => 'Refunded', 'class' => 'text-outline'],
                default => ['label' => 'Pending', 'class' => 'text-amber-action'],
            };
        @endphp
        <div class="p-4 sm:p-5 rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm hover:shadow-md transition-all border border-white/60">
            {{-- Order Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-4 border-b border-surface-container">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider">#{{ $order->order_number }}</span>
                    <span class="inline-flex items-center gap-1.5 font-label-micro text-label-micro {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2.5 py-1 rounded-full font-semibold">
                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">{{ $statusConfig['icon'] }}</span>
                        {{ $statusConfig['label'] }}
                    </span>
                    <span class="font-label-micro text-label-micro text-on-surface-variant">
                        {{ $order->placed_at ? $order->placed_at->format('d M Y') : $order->created_at->format('d M Y') }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-label-micro text-label-micro {{ $paymentConfig['class'] }} font-semibold">{{ $paymentConfig['label'] }}</span>
                    <span class="font-title-card text-title-card font-bold text-slate-authority">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            {{-- Order Items Preview --}}
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-surface-container-high flex-shrink-0">
                    @if($firstItem && $firstItem->product_image)
                        <img alt="{{ $firstItem->product_name }}" class="w-full h-full object-cover" src="{{ Storage::url($firstItem->product_image) }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[28px]">inventory_2</span>
                        </div>
                    @endif
                </div>
                <div class="flex flex-col min-w-0">
                    <h3 class="font-title-card text-title-card font-semibold text-slate-authority truncate">
                        {{ $firstItem ? $firstItem->product_name : 'Order ' . $order->order_number }}
                    </h3>
                    <p class="font-body-small text-body-small text-on-surface-variant truncate">
                        {{ ucfirst($order->payment_method) }} •
                        {{ $order->sellerOrders->sum(fn($s) => $s->items->count()) }} item(s) •
                        {{ ucfirst($order->order_type) }} Order
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('user.orders.show', $order->id) }}" class="px-4 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold hover:bg-slate-authority/90 transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                    View Details
                </a>
                @if(in_array($order->order_status, ['pending', 'processing']))
                <a href="{{ route('user.orders.show', $order->id) }}" class="px-4 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-90 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">near_me</span>
                    Track Order
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 rounded-2xl bg-surface-container-lowest/90 text-center flex flex-col items-center gap-4">
            <span class="material-symbols-outlined text-[64px] text-on-surface-variant/40">inventory_2</span>
            <div>
                <h3 class="font-title-card text-title-card font-semibold text-slate-authority">No orders found</h3>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                    {{ $status !== 'all' ? 'No ' . $status . ' orders.' : 'You haven\'t placed any orders yet.' }}
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm">
                Start Shopping
            </a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="flex justify-center pt-2">
        {{ $orders->links() }}
    </div>
    @endif

</div>
@endsection
