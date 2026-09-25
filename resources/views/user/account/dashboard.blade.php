@extends('layouts.user')

@section('title', 'Dashboard — Bazaario')

@section('content')
<div class="flex flex-col w-full relative overflow-hidden">
    {{-- Atmospheric Luminous 3D Glass Gradients --}}
    <div class="pointer-events-none absolute -top-40 -left-20 w-[550px] h-[550px] rounded-full bg-amber-action/15 blur-[120px] mix-blend-multiply"></div>
    <div class="pointer-events-none absolute top-72 -right-28 w-[600px] h-[600px] rounded-full bg-slate-authority/10 blur-[140px] mix-blend-multiply"></div>
    <div class="pointer-events-none absolute top-[900px] left-1/3 w-[450px] h-[450px] rounded-full bg-amber-action/10 blur-[130px]"></div>

    <div class="max-w-container-max mx-auto px-gutter-md w-full relative z-10 py-gutter-lg flex flex-col gap-gutter-lg">

        {{-- 1. Header & Welcome Hero --}}
        <section class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 pb-2">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 font-label-micro text-label-micro tracking-widest uppercase bg-surface-container-lowest/80 text-amber-action px-3 py-1.5 rounded-full shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                        VERIFIED BUYER • UID: BZ-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}-IN
                    </span>
                    <span class="hidden sm:inline-flex items-center font-label-micro text-label-micro px-2.5 py-1 rounded-full bg-slate-authority/5 text-slate-authority">
                        Member since {{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}
                    </span>
                </div>
                <h1 class="font-display-hero text-display-hero-mobile md:text-display-hero text-slate-authority tracking-tight">
                    {{ $greeting }}, {{ $user->first_name }} <span class="inline-block transform origin-bottom-right hover:rotate-12 transition-transform cursor-pointer">👋</span>
                </h1>
                <p class="font-body-lead text-body-lead text-on-surface-variant max-w-2xl">
                    Here's what's happening with your Bazaario account, orders, and protected vaults today.
                </p>
            </div>
            {{-- Quick Action Pills --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm">
                    <span class="w-7 h-7 rounded-lg bg-amber-action/15 flex items-center justify-center text-amber-action">
                        <span class="material-symbols-outlined text-[18px]">currency_rupee</span>
                    </span>
                    <div class="flex flex-col">
                        <span class="font-label-micro text-label-micro text-on-surface-variant uppercase">Total Spent</span>
                        <span class="font-title-card text-title-card font-bold text-slate-authority leading-tight">₹{{ number_format($totalSpent, 2) }}</span>
                    </div>
                </div>
                <a href="{{ route('user.settings') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-lowest/80 backdrop-blur-md hover:bg-surface-container text-slate-authority transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[20px] text-on-surface-variant">settings</span>
                    <span class="font-button-text text-body-small font-semibold">Account Settings</span>
                </a>
            </div>
        </section>

        {{-- 2. 4 Stat Cards Grid --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Orders --}}
            <a href="{{ route('user.orders.index') }}" class="relative group p-5 rounded-2xl bg-surface-container-lowest/85 backdrop-blur-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-amber-action/10 blur-xl group-hover:scale-125 transition-transform"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-action/15 flex items-center justify-center text-amber-action shadow-inner">
                        <span class="material-symbols-outlined text-[26px]">inventory_2</span>
                    </div>
                    @if($inTransit > 0)
                    <span class="inline-flex items-center gap-1.5 font-label-micro text-label-micro bg-status-green/10 text-status-green px-2.5 py-1 rounded-full font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-status-green animate-ping"></span>
                        {{ $inTransit }} in transit
                    </span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-display-hero text-headline-section font-bold text-slate-authority">{{ $totalOrders }}</span>
                    <span class="font-body-small text-body-small text-on-surface-variant mt-0.5">Total Orders</span>
                </div>
            </a>
            {{-- Card 2: Wishlist --}}
            <a href="{{ route('user.wishlist.index') }}" class="relative group p-5 rounded-2xl bg-surface-container-lowest/85 backdrop-blur-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-error/10 blur-xl group-hover:scale-125 transition-transform"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-error/10 flex items-center justify-center text-error shadow-inner">
                        <span class="material-symbols-outlined text-[26px]">favorite</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-display-hero text-headline-section font-bold text-slate-authority">{{ $wishlistCount }}</span>
                    <span class="font-body-small text-body-small text-on-surface-variant mt-0.5">Saved Items</span>
                </div>
            </a>
            {{-- Card 3: Active Bids --}}
            <a href="{{ route('user.bids') }}" class="relative group p-5 rounded-2xl bg-surface-container-lowest/85 backdrop-blur-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-primary-container/10 blur-xl group-hover:scale-125 transition-transform"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-container/10 flex items-center justify-center text-slate-authority shadow-inner">
                        <span class="material-symbols-outlined text-[26px]">gavel</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-display-hero text-headline-section font-bold text-slate-authority">{{ $activeBids }}</span>
                    <span class="font-body-small text-body-small text-on-surface-variant mt-0.5">Live Auctions</span>
                </div>
            </a>
            {{-- Card 4: Coupons --}}
            <a href="{{ route('user.coupons') }}" class="relative group p-5 rounded-2xl bg-surface-container-lowest/85 backdrop-blur-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-status-green/10 blur-xl group-hover:scale-125 transition-transform"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-status-green/10 flex items-center justify-center text-status-green shadow-inner">
                        <span class="material-symbols-outlined text-[26px]">confirmation_number</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-display-hero text-headline-section font-bold text-slate-authority">{{ $availableCoupons }}</span>
                    <span class="font-body-small text-body-small text-on-surface-variant mt-0.5">Available Perks</span>
                </div>
            </a>
        </section>

        {{-- 3. Recent Orders Section --}}
        <section class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-headline-section text-headline-section text-slate-authority tracking-tight">Recent Orders</h2>
                    <p class="font-body-small text-body-small text-on-surface-variant">Your latest orders and delivery status</p>
                </div>
                <a href="{{ route('user.orders.index') }}" class="font-button-text text-body-small text-amber-action hover:text-amber-action/80 flex items-center gap-1 font-semibold transition-colors">
                    View all orders <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
            <div class="flex flex-col gap-3">
                @forelse($recentOrders as $order)
                @php
                    $firstItem = $order->sellerOrders->first()?->items->first();
                    $statusConfig = match($order->order_status) {
                        'completed' => ['label' => 'Delivered ✓', 'bg' => 'bg-status-green/10', 'text' => 'text-status-green', 'dot' => 'bg-status-green'],
                        'processing' => ['label' => 'Shipped 🚚', 'bg' => 'bg-primary-fixed', 'text' => 'text-slate-authority', 'dot' => 'bg-slate-authority'],
                        'cancelled' => ['label' => 'Cancelled', 'bg' => 'bg-error/10', 'text' => 'text-error', 'dot' => 'bg-error'],
                        'refunded' => ['label' => 'Refunded', 'bg' => 'bg-outline/10', 'text' => 'text-outline', 'dot' => 'bg-outline'],
                        default => ['label' => 'Processing ⏳', 'bg' => 'bg-amber-action/20', 'text' => 'text-slate-authority', 'dot' => 'bg-amber-action'],
                    };
                @endphp
                <div class="p-4 sm:p-5 rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-surface-container-high flex-shrink-0">
                            @if($firstItem && $firstItem->product_image)
                                <img alt="{{ $firstItem->product_name }}" class="w-full h-full object-cover" src="{{ Storage::url($firstItem->product_image) }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[32px]">inventory_2</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider">#{{ $order->order_number }}</span>
                                <span class="inline-flex items-center gap-1 font-label-micro text-label-micro {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2 py-0.5 rounded-full font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    {{ $statusConfig['label'] }}
                                </span>
                            </div>
                            <h3 class="font-title-card text-title-card font-semibold text-slate-authority truncate mt-1">
                                {{ $firstItem ? $firstItem->product_name : 'Order ' . $order->order_number }}
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant truncate">
                                {{ $order->order_type === 'auction' ? 'Auction Order' : 'Cart Order' }} •
                                {{ $order->sellerOrders->sum(fn($s) => $s->items->count()) }} item(s) •
                                Placed {{ $order->placed_at ? $order->placed_at->format('d M Y') : $order->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between md:justify-end gap-6 w-full md:w-auto flex-shrink-0 pt-2 md:pt-0">
                        <div class="flex flex-col md:text-right">
                            <span class="font-label-micro text-label-micro text-on-surface-variant">
                                {{ $order->payment_status === 'paid' ? 'Paid' : ucfirst($order->payment_status) }}
                            </span>
                            <span class="font-title-card text-title-card font-bold text-slate-authority">₹{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <a href="{{ route('user.orders.show', $order->id) }}" class="px-4 py-2.5 rounded-xl bg-surface-container-high hover:bg-surface-container-highest text-slate-authority font-button-text text-body-small font-semibold transition-colors flex items-center gap-1">
                            Details <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-8 rounded-2xl bg-surface-container-lowest/90 text-center flex flex-col items-center gap-3">
                    <span class="material-symbols-outlined text-[48px] text-on-surface-variant">inventory_2</span>
                    <p class="font-body-regular text-body-regular text-on-surface-variant">No orders yet.</p>
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold">Start Shopping</a>
                </div>
                @endforelse
            </div>
        </section>

        {{-- 4. Escrow Guarantee Banner --}}
        <section class="p-6 md:p-8 rounded-2xl bg-slate-authority text-canvas-ivory relative overflow-hidden shadow-lg">
            <div class="pointer-events-none absolute -right-20 -top-20 w-80 h-80 rounded-full bg-amber-action/20 blur-3xl"></div>
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-action/20 text-amber-action flex items-center justify-center flex-shrink-0 shadow-inner">
                        <span class="material-symbols-outlined text-[32px]">shield_with_heart</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-label-eyebrow text-label-eyebrow text-amber-action uppercase tracking-wider font-semibold">Tier-1 Smart Protection</span>
                            <span class="font-label-micro text-label-micro bg-status-green/20 text-secondary-fixed px-2 py-0.5 rounded-full font-mono">100% SECURE</span>
                        </div>
                        <h3 class="font-headline-section text-headline-section font-bold text-canvas-ivory leading-snug">Bazaario Escrow Protocol Protection Active</h3>
                        <p class="font-body-small text-body-small text-canvas-ivory/70 max-w-2xl mt-1">Your funds are held safely in escrow until delivery is inspected and approved by you.</p>
                    </div>
                </div>
                <a href="{{ route('user.security') }}" class="px-6 py-3 rounded-xl bg-amber-action hover:bg-amber-action/90 text-slate-authority font-button-text text-button-text font-bold transition-transform active:scale-[0.99] whitespace-nowrap shadow-md">
                    Verify Protection Details
                </a>
            </div>
        </section>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('button[type="button"]').forEach(btn => {
        btn.addEventListener('click', function () {
            const ripple = document.createElement('span');
            ripple.className = 'absolute inset-0 rounded-xl bg-white/20 pointer-events-none transition-opacity duration-300';
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 300);
        });
    });
</script>
@endpush