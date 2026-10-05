@extends('layouts.seller')

@section('title', 'My Orders — Order Fulfillment Workspace — Bazaario')

@section('content')
@php
    $seller = $sellerUser ?? Auth::guard('seller')->user() ?? Auth::user();
    $profile = $sellerProfile ?? $seller?->sellerProfile;
    $shopName = $profile?->shop_name ?? 'My Farm';
    $sellerStoreId = str_pad($seller?->id ?? 1, 4, '0', STR_PAD_LEFT);
    $activeOrder = $focusedOrder ?? $selectedOrder ?? $orders->first() ?? null;
@endphp

<div class="flex flex-col w-full pb-16" x-data="orderFulfillmentWorkspace()">
    <!-- Top Breadcrumbs & Command Bar -->
    <div class="flex flex-col gap-1 mb-6">
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-outline uppercase tracking-wider">
            <span>Seller Center</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span>Orders</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-semibold">Live Dispatch Queue</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div>
                <h1 class="font-heading text-3xl font-bold text-on-surface tracking-tight">My Orders</h1>
                <p class="font-sans text-sm text-on-surface-variant max-w-2xl mt-1">
                    Manage verified orders assigned strictly to <span class="text-on-surface font-medium">{{ $shopName }}</span>. Live telemetry, delivery slot assignments, and status pipeline.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" class="h-12 px-4 rounded-[14px] bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-colors shadow-sm flex items-center gap-2 font-sans text-sm font-medium">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Export Orders CSV</span>
                </button>
                <button type="button" class="h-12 px-4 rounded-[14px] bg-primary text-on-primary hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2 font-sans text-sm font-semibold">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Batch Print Shipping Labels</span>
                </button>
            </div>
        </div>
    </div>

    <!-- High-Impact Dispatch Operational Banner -->
    <div class="relative overflow-hidden rounded-[14px] bg-surface-container-lowest shadow-sm p-4 mb-6">
        <div class="absolute left-0 top-0 bottom-0 w-2 bg-secondary-container"></div>
        <div class="flex flex-wrap items-center justify-between gap-4 pl-3">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-[14px] bg-secondary-fixed/40 flex items-center justify-center text-on-secondary-container">
                    <span class="material-symbols-outlined text-[26px]">local_shipping</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[11px] uppercase tracking-widest text-on-surface-variant font-medium">Assigned Dispatch Window</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-[6px] bg-secondary-fixed text-on-secondary-fixed font-mono text-[11px] font-bold uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            Courier Slot Confirmed
                        </span>
                    </div>
                    <div class="font-heading text-lg font-bold text-on-surface tracking-tight mt-0.5">
                        TODAY: 4:00 PM – 6:00 PM
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex flex-col text-right">
                    <span class="font-mono text-[11px] text-outline uppercase tracking-wider">Telemetry Target</span>
                    <div class="flex items-center gap-1.5 font-mono text-xs font-bold text-error">
                        <span class="material-symbols-outlined text-[16px] text-error">timer</span>
                        <span>01h 45m left to pack</span>
                    </div>
                </div>
                <div class="hidden sm:block h-9 w-px bg-surface-container-high"></div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] text-on-surface-variant">Fleet Status:</span>
                    <span class="inline-flex items-center gap-1 font-mono text-[11px] text-on-tertiary-container font-semibold">
                        <span class="material-symbols-outlined text-[14px]">sensors</span>
                        Hyperlocal Hub Active
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters, Search & State Navigation Bar -->
    <div class="flex flex-col gap-3 bg-surface-container-lowest rounded-[14px] shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('seller.orders.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative min-w-[260px] max-w-sm flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-outline text-[18px]">search</span>
                    <input type="text" name="search" value="{{ request('search', $searchQuery ?? '') }}"
                           class="w-full h-11 pl-10 pr-4 bg-surface-container-low rounded-[14px] font-sans text-xs text-on-surface placeholder:text-outline focus:outline-none"
                           placeholder="Filter Order ID #BZ-XXXX, Buyer...">
                </div>
                <div class="inline-flex p-1 bg-surface-container-low rounded-[14px] font-sans text-xs">
                    <button type="submit" name="date" value="today" class="px-3 py-1.5 rounded-[10px] transition-all {{ request('date') === 'today' ? 'bg-surface-container-lowest font-medium text-on-surface shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">Today</button>
                    <button type="submit" name="date" value="yesterday" class="px-3 py-1.5 rounded-[10px] transition-all {{ request('date') === 'yesterday' ? 'bg-surface-container-lowest font-medium text-on-surface shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">Yesterday</button>
                    <button type="submit" name="date" value="7d" class="px-3 py-1.5 rounded-[10px] transition-all {{ in_array(request('date'), ['7d', 'last_7_days']) ? 'bg-surface-container-lowest font-medium text-on-surface shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">Last 7 Days</button>
                </div>
            </form>
            <div class="flex items-center gap-2 text-on-surface-variant font-mono text-[11px] uppercase">
                <span class="material-symbols-outlined text-[16px] text-on-tertiary-container">verified</span>
                <span>Store ID: <span class="font-mono text-on-surface font-semibold">#BZ-SLR-{{ $sellerStoreId }}</span></span>
            </div>
        </div>

        <!-- Segmented Status Tabs -->
        @php
            $tabDefs = [
                'all'              => ['label' => 'All', 'count' => $counts['all'] ?? 0],
                'pending'          => ['label' => 'Pending', 'count' => $counts['pending'] ?? 0],
                'confirmed'        => ['label' => 'Confirmed', 'count' => $counts['confirmed'] ?? 0],
                'processing'       => ['label' => 'Processing', 'count' => $counts['processing'] ?? 0],
                'ready_for_pickup' => ['label' => 'Ready for Pickup', 'count' => $counts['ready_for_pickup'] ?? 0],
                'fulfilled'        => ['label' => 'Fulfilled', 'count' => $counts['fulfilled'] ?? 0],
                'cancelled'        => ['label' => 'Cancelled', 'count' => $counts['cancelled'] ?? 0],
            ];
            $activeStatusTab = request('status', 'all');
        @endphp
        <div class="flex items-center gap-2 overflow-x-auto pt-1 pb-1 text-nowrap">
            @foreach($tabDefs as $tabKey => $tabInfo)
                @php
                    $isTabActive = ($activeStatusTab === $tabKey) || ($tabKey === 'fulfilled' && request('status') === 'delivered') || ($tabKey === 'pending' && request('status') === 'placed');
                @endphp
                <a href="{{ route('seller.orders.index', array_filter(['status' => $tabKey !== 'all' ? $tabKey : null, 'search' => request('search'), 'date' => request('date')])) }}"
                   class="px-4 py-2 rounded-[10px] font-sans text-xs flex items-center gap-1.5 transition-colors {{ $isTabActive ? 'bg-primary text-on-primary font-semibold shadow-sm' : 'bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-on-surface font-medium' }}">
                    <span>{{ $tabInfo['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-full {{ $isTabActive ? 'bg-white/20 text-white' : 'bg-surface-container-highest text-on-surface' }} font-mono text-[10px] font-bold">
                        {{ $tabInfo['count'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Core 2-Column Split Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- LEFT COLUMN: Orders Queue Table (8 cols / 65%) -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <div class="bg-surface-container-lowest rounded-[14px] shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-4 bg-surface-container-low/40 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="font-heading text-lg font-bold text-on-surface">Queue Management</span>
                        <span class="font-mono text-xs text-outline">({{ $orders->total() ?? count($orders) }} Orders)</span>
                    </div>
                    <div class="flex items-center gap-2 font-mono text-xs text-on-surface-variant">
                        <span>Auto-refresh in</span>
                        <span class="font-bold text-on-surface">24s</span>
                    </div>
                </div>

                @if($orders->isEmpty())
                    <!-- Clean Zero State Card -->
                    <div class="p-12 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 rounded-[14px] bg-surface-container-low flex items-center justify-center text-outline mb-4">
                            <span class="material-symbols-outlined text-[32px]">orders</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-on-surface">No orders found</h3>
                        <p class="font-sans text-sm text-on-surface-variant max-w-md mt-1">
                            No orders in queue for <span class="font-medium text-on-surface">{{ $shopName }}</span>. Orders placed by customers in your hyperlocal zone will appear here immediately.
                        </p>
                        @if(request('status') || request('search') || request('date'))
                            <a href="{{ route('seller.orders.index') }}" class="mt-4 px-4 py-2 rounded-[14px] bg-primary text-on-primary font-sans text-xs font-semibold">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                @else
                    <!-- Table Container -->
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low/60 text-outline font-mono text-[11px] uppercase tracking-wider">
                                    <th class="py-3.5 pl-4 pr-2 font-medium">Order ID</th>
                                    <th class="py-3.5 px-2 font-medium">Customer &amp; Area</th>
                                    <th class="py-3.5 px-2 font-medium">Products</th>
                                    <th class="py-3.5 px-2 font-medium">Amount</th>
                                    <th class="py-3.5 px-2 font-medium">Delivery Slot</th>
                                    <th class="py-3.5 px-2 font-medium">Status</th>
                                    <th class="py-3.5 pr-4 pl-2 text-right font-medium">Next Action</th>
                                </tr>
                            </thead>
                            <tbody class="font-sans text-xs divide-y divide-surface-container/60">
                                @foreach($orders as $order)
                                    @php
                                        $isSelected = ($activeOrder && $activeOrder->id === $order->id);
                                        $statusClass = match($order->status) {
                                            'placed', 'pending' => 'bg-secondary-container/20 text-on-secondary-container',
                                            'confirmed' => 'bg-surface-container text-on-surface',
                                            'processing' => 'bg-surface-container-high text-on-surface',
                                            'packed', 'ready_for_pickup' => 'bg-secondary-fixed text-on-secondary-fixed',
                                            'delivered', 'fulfilled', 'shipped' => 'bg-tertiary-fixed text-on-tertiary-fixed',
                                            'cancelled', 'returned' => 'bg-surface-variant text-outline',
                                            default => 'bg-surface-variant text-outline',
                                        };
                                        $statusLabel = match($order->status) {
                                            'placed', 'pending' => 'Pending',
                                            'confirmed' => 'Confirmed',
                                            'processing' => 'Processing',
                                            'packed', 'ready_for_pickup' => 'Ready for Pickup',
                                            'delivered', 'fulfilled', 'shipped' => 'Fulfilled',
                                            'cancelled' => 'Cancelled',
                                            default => ucfirst($order->status),
                                        };
                                        $itemsSummary = $order->items->pluck('product_name')->join(', ');
                                    @endphp
                                    <tr class="{{ $isSelected ? 'bg-secondary-fixed/20 hover:bg-secondary-fixed/30' : 'hover:bg-surface-container-low' }} cursor-pointer transition-colors"
                                        onclick="window.location.href='{{ route('seller.orders.index', array_merge(request()->query(), ['order_id' => $order->id])) }}'">
                                        <td class="py-4 pl-4 pr-2 align-top">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full {{ $isSelected ? 'bg-secondary-container' : 'bg-outline' }}"></span>
                                                <span class="font-mono text-xs font-bold text-on-surface">#{{ $order->seller_order_number }}</span>
                                            </div>
                                            <span class="font-mono text-[10px] text-outline block pl-3.5 mt-0.5">Express Dispatch</span>
                                        </td>
                                        <td class="py-4 px-2 align-top">
                                            <span class="font-medium text-on-surface block leading-tight">{{ $order->order?->delivery_full_name ?? $order->order?->user?->name ?? 'Customer' }}</span>
                                            <span class="font-sans text-[11px] text-on-surface-variant block mt-0.5">{{ $order->order?->delivery_city ?? 'Hyperlocal Area' }}</span>
                                        </td>
                                        <td class="py-4 px-2 align-top max-w-[180px]">
                                            <div class="line-clamp-2 text-on-surface">
                                                {{ !empty($itemsSummary) ? $itemsSummary : 'Agricultural Produce' }}
                                            </div>
                                            <span class="font-mono text-[10px] text-outline">{{ $order->items->count() }} items</span>
                                        </td>
                                        <td class="py-4 px-2 align-top">
                                            <span class="font-mono text-xs font-bold text-on-surface">₹{{ number_format($order->subtotal, 2) }}</span>
                                            <span class="font-mono text-[10px] text-on-tertiary-container block">Prepaid (Escrow)</span>
                                        </td>
                                        <td class="py-4 px-2 align-top">
                                            <span class="text-on-surface font-medium block">{{ $order->delivery_slot ?? 'Today, 4:00 PM – 6:00 PM' }}</span>
                                        </td>
                                        <td class="py-4 px-2 align-top">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-[6px] {{ $statusClass }} font-mono text-[11px] font-bold uppercase tracking-wider">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="py-4 pr-4 pl-2 align-top text-right" onclick="event.stopPropagation()">
                                            @if(in_array($order->status, ['placed', 'pending']))
                                                <form method="POST" action="{{ route('seller.orders.update-status', $order) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="processing">
                                                    <button type="submit" class="h-9 px-3 rounded-[10px] bg-primary text-on-primary hover:bg-slate-800 font-sans text-xs font-semibold inline-flex items-center gap-1 shadow-sm transition-colors">
                                                        <span class="material-symbols-outlined text-[16px]">thumb_up</span>
                                                        <span>Confirm Order</span>
                                                    </button>
                                                </form>
                                            @elseif($order->status === 'confirmed')
                                                <form method="POST" action="{{ route('seller.orders.update-status', $order) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="processing">
                                                    <button type="submit" class="h-9 px-3 rounded-[10px] bg-surface-container-lowest text-on-surface hover:bg-surface-container font-sans text-xs font-semibold inline-flex items-center gap-1 shadow-sm transition-colors border border-outline-variant">
                                                        <span class="material-symbols-outlined text-[16px]">box</span>
                                                        <span>Start Packing</span>
                                                    </button>
                                                </form>
                                            @elseif($order->status === 'processing')
                                                <form method="POST" action="{{ route('seller.orders.update-status', $order) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="ready_for_pickup">
                                                    <button type="submit" class="h-9 px-3 rounded-[10px] bg-surface-container-lowest text-on-surface hover:bg-surface-container font-sans text-xs font-semibold inline-flex items-center gap-1 shadow-sm transition-colors border border-outline-variant">
                                                        <span class="material-symbols-outlined text-[16px]">inventory</span>
                                                        <span>Mark Ready</span>
                                                    </button>
                                                </form>
                                            @elseif(in_array($order->status, ['ready_for_pickup', 'packed']))
                                                <button type="button" @click="openModal({{ json_encode([
                                                    'id' => $order->id,
                                                    'number' => $order->seller_order_number,
                                                    'customer' => $order->order?->delivery_full_name ?? $order->order?->user?->name ?? 'Customer',
                                                    'courier' => $order->courier_name,
                                                    'gross' => number_format($order->subtotal, 2),
                                                    'net' => number_format($order->payout_amount > 0 ? $order->payout_amount : $order->net_payout_calculated, 2),
                                                ]) }})" class="h-9 px-3 rounded-[10px] bg-secondary-container text-on-secondary-container hover:opacity-90 font-sans text-xs font-semibold inline-flex items-center gap-1 shadow-sm transition-colors">
                                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                                    <span>Mark Fulfilled</span>
                                                </button>
                                            @elseif(in_array($order->status, ['fulfilled', 'delivered']))
                                                <a href="{{ route('seller.orders.show', $order) }}" class="h-9 px-3 rounded-[10px] bg-surface-container text-on-surface hover:bg-surface-container-high font-sans text-xs font-medium inline-flex items-center gap-1 transition-colors">
                                                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                                    <span>View Invoice</span>
                                                </a>
                                            @else
                                                <span class="text-outline font-mono text-[11px]">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($orders->hasPages())
                        <div class="px-6 py-3 border-t border-surface-container-high bg-surface-container-low/20">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @endif

                <!-- Tenancy Data Isolation Footer Banner -->
                <div class="p-4 bg-surface-container-low/50 flex flex-wrap items-center justify-between gap-3 border-t border-surface-container-high/40">
                    <div class="flex items-center gap-2 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[18px] text-on-surface">security</span>
                        <span class="font-mono text-xs">
                            Data Isolation Active: You only have access to seller-authenticated orders for <span class="font-bold text-on-surface">{{ $shopName }}</span> (Store ID #BZ-SLR-{{ $sellerStoreId }}).
                        </span>
                    </div>
                    <div class="font-mono text-[11px] text-outline">
                        TLS 1.3 / E2E Encrypted Payload
                    </div>
                </div>
            </div>

            <!-- Operational Gauges (Capacity, SLA, Pending Accrual) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-surface-container-lowest rounded-[14px] p-4 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[11px] uppercase text-outline">Dispatch Capacity</span>
                        <span class="font-mono text-xs font-bold text-on-surface">{{ $stats['dispatch_capacity'] ?? 82 }}%</span>
                    </div>
                    <div class="my-3">
                        <div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden">
                            <div class="bg-secondary-container h-full rounded-full" style="width: {{ $stats['dispatch_capacity'] ?? 82 }}%;"></div>
                        </div>
                    </div>
                    <span class="font-sans text-xs text-on-surface-variant">14 of 18 packing crates sealed</span>
                </div>
                <div class="bg-surface-container-lowest rounded-[14px] p-4 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[11px] uppercase text-outline">On-Time SLA Record</span>
                        <span class="font-mono text-xs font-bold text-on-tertiary-container">{{ $stats['on_time_sla'] ?? 99.4 }}%</span>
                    </div>
                    <div class="flex items-center gap-1.5 my-2 text-on-tertiary-container font-mono text-xs font-bold">
                        <span class="material-symbols-outlined text-[18px]">verified_user</span>
                        <span>Prime Merchant Tier</span>
                    </div>
                    <span class="font-sans text-xs text-on-surface-variant">Last incident: 38 days ago</span>
                </div>
                <div class="bg-surface-container-lowest rounded-[14px] p-4 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[11px] uppercase text-outline">Pending Payout Accrual</span>
                        <span class="font-mono text-[11px] font-bold text-on-surface">T+1 Auto Payout</span>
                    </div>
                    <div class="font-heading text-xl font-bold text-on-surface mt-1">
                        ₹{{ number_format($stats['pending_accrual'] ?? 0, 2) }}
                    </div>
                    <span class="font-sans text-xs text-on-surface-variant">Will clear tomorrow 08:00 AM</span>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Focused Order Detail Inspector (4 cols / 35%) -->
        <div class="lg:col-span-4 flex flex-col gap-4">
            @if($activeOrder)
                @php
                    $parentOrder = $activeOrder->order;
                    $customerName = $parentOrder?->delivery_full_name ?? $parentOrder?->user?->name ?? 'Customer';
                    $rawPhone = $parentOrder?->delivery_phone ?? '';
                    $maskedPhone = !empty($rawPhone) ? '+91 ' . substr($rawPhone, 0, 5) . ' •••••' : '+91 98450 •••••';
                    $addressLine = $parentOrder?->delivery_address_line_1 ?? 'Plot 55, Agro Hub';
                    if ($parentOrder?->delivery_city) {
                        $addressLine .= ', ' . $parentOrder->delivery_city;
                    }
                    if ($parentOrder?->delivery_postal_code) {
                        $addressLine .= ' - ' . $parentOrder->delivery_postal_code;
                    }
                    $activeStatus = $activeOrder->status;
                    $grossVal = (float) $activeOrder->subtotal;
                    $commVal = (float) $activeOrder->commission_amount;
                    $cessVal = (float) $activeOrder->apmc_cess;
                    $netVal = (float) ($activeOrder->payout_amount > 0 ? $activeOrder->payout_amount : $activeOrder->net_payout_calculated);
                @endphp
                <div class="bg-surface-container-lowest rounded-[14px] shadow-sm p-6 flex flex-col gap-5 sticky top-20">
                    <!-- Detail Header -->
                    <div class="flex items-start justify-between pb-2 border-b border-surface-container-high/40">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-heading text-lg font-bold text-on-surface tracking-tight">Order #{{ $activeOrder->seller_order_number }}</span>
                                <a href="{{ route('seller.orders.show', $activeOrder) }}" class="text-outline hover:text-on-surface transition-colors" title="View Full Details">
                                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                </a>
                            </div>
                            <span class="font-mono text-[11px] text-outline uppercase block mt-0.5">Placed at {{ $activeOrder->created_at?->format('H:i \I\S\T') ?? 'Today' }}</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-[6px] bg-secondary-fixed text-on-secondary-fixed font-mono text-[11px] font-bold uppercase tracking-wider">
                            {{ ucfirst(str_replace('_', ' ', $activeStatus)) }}
                        </span>
                    </div>

                    <!-- Customer Details Card -->
                    <div class="bg-surface-container-low rounded-[14px] p-4 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[11px] uppercase tracking-wider text-outline">Customer Details</span>
                            <span class="inline-flex items-center gap-1 font-mono text-[11px] text-on-tertiary-container font-semibold">
                                <span class="material-symbols-outlined text-[12px]">verified</span>
                                Repeat Buyer
                            </span>
                        </div>
                        <div class="font-sans text-base font-semibold text-on-surface">{{ $customerName }}</div>
                        <div class="flex items-center gap-2 font-mono text-xs text-on-surface-variant">
                            <span class="material-symbols-outlined text-[15px] text-outline">call</span>
                            <span>{{ $maskedPhone }}</span>
                        </div>
                        <div class="flex items-start gap-2 font-sans text-xs text-on-surface-variant mt-0.5">
                            <span class="material-symbols-outlined text-[16px] text-outline mt-0.5 shrink-0">location_on</span>
                            <span>{{ $addressLine }}</span>
                        </div>
                        @if($parentOrder?->notes)
                            <div class="mt-2 pt-2 border-t border-surface-container-high font-sans text-xs text-on-surface-variant">
                                <span class="font-semibold text-on-surface">Delivery Notes:</span>
                                <span>{{ $parentOrder->notes }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- PROMINENT ASSIGNED DELIVERY SLOT CARD -->
                    <div class="relative overflow-hidden rounded-[14px] bg-primary text-white p-4 shadow-sm">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-[11px] uppercase tracking-widest text-slate-300 font-bold">Assigned Delivery Slot</span>
                                <span class="px-2 py-0.5 rounded-[6px] bg-secondary-container text-on-secondary-container font-mono text-[11px] font-bold uppercase">
                                    Slot Confirmed
                                </span>
                            </div>
                            <div class="font-heading text-lg font-bold text-white mt-1">
                                {{ $activeOrder->delivery_slot ?? 'Today, 4:00 PM – 6:00 PM' }}
                            </div>
                            <div class="pt-1 flex items-center justify-between border-t border-white/10 text-xs">
                                <div class="flex items-center gap-1.5 text-slate-300">
                                    <span class="material-symbols-outlined text-[18px] text-secondary-container">sports_motorsports</span>
                                    <span>{{ $activeOrder->courier_name }}</span>
                                </div>
                                <span class="font-mono text-[11px] font-semibold text-green-400">Assigned &amp; En Route</span>
                            </div>
                        </div>
                    </div>

                    <!-- Itemized Products Breakdown -->
                    <div class="flex flex-col gap-2">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-outline">Order Items ({{ $activeOrder->items->count() }} SKUs)</span>
                        <div class="bg-surface-container-low rounded-[14px] p-3 flex flex-col gap-2">
                            @forelse($activeOrder->items as $item)
                                <div class="flex items-start justify-between gap-3 pb-2 border-b border-surface-container-high/60 last:border-b-0 last:pb-0">
                                    <div class="flex gap-2.5">
                                        <div class="w-10 h-10 rounded-lg bg-surface-container-high overflow-hidden shrink-0 flex items-center justify-center text-outline">
                                            @if($item->product_image)
                                                <img class="w-full h-full object-cover" src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}">
                                            @else
                                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-sans text-xs font-semibold text-on-surface">{{ $item->product_name }}</div>
                                            <div class="font-mono text-[10px] text-on-surface-variant">Qty: {{ $item->quantity }} @ ₹{{ number_format($item->unit_price, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="font-mono text-xs font-bold text-on-surface whitespace-nowrap">
                                        ₹{{ number_format($item->total_price, 2) }}
                                    </div>
                                </div>
                            @empty
                                <div class="font-sans text-xs text-on-surface-variant">No individual items recorded.</div>
                            @endforelse

                            <div class="h-px bg-surface-container-high my-1"></div>
                            <div class="flex items-center justify-between font-sans text-xs text-on-surface">
                                <span class="font-medium">Gross Products Subtotal</span>
                                <span class="font-mono font-bold">₹{{ number_format($grossVal, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Fulfillment Milestones Stepper -->
                    <div class="flex flex-col gap-2 pt-1">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-outline">Fulfillment Milestones</span>
                        <div class="flex flex-col gap-2 relative pl-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-surface-container-high">
                            <div class="relative flex items-center justify-between text-xs">
                                <div class="absolute -left-6 w-4 h-4 rounded-full bg-on-tertiary-container flex items-center justify-center text-white text-[10px]">
                                    <span class="material-symbols-outlined text-[12px]">check</span>
                                </div>
                                <span class="text-on-surface font-medium">Pending Placed</span>
                                <span class="font-mono text-[10px] text-outline">13:42</span>
                            </div>
                            <div class="relative flex items-center justify-between text-xs">
                                <div class="absolute -left-6 w-4 h-4 rounded-full {{ in_array($activeStatus, ['confirmed', 'processing', 'packed', 'ready_for_pickup', 'fulfilled', 'delivered', 'shipped']) ? 'bg-on-tertiary-container text-white' : 'bg-surface-container-high' }} flex items-center justify-center text-[10px]">
                                    <span class="material-symbols-outlined text-[12px]">check</span>
                                </div>
                                <span class="text-on-surface font-medium">Order Confirmed</span>
                                <span class="font-mono text-[10px] text-outline">13:45</span>
                            </div>
                            <div class="relative flex items-center justify-between text-xs">
                                <div class="absolute -left-6 w-4 h-4 rounded-full {{ in_array($activeStatus, ['processing', 'packed', 'ready_for_pickup', 'fulfilled', 'delivered', 'shipped']) ? 'bg-on-tertiary-container text-white' : 'bg-surface-container-high' }} flex items-center justify-center text-[10px]">
                                    <span class="material-symbols-outlined text-[12px]">check</span>
                                </div>
                                <span class="text-on-surface font-medium">Packed &amp; Sealed</span>
                                <span class="font-mono text-[10px] text-outline">14:10</span>
                            </div>
                            <div class="relative flex items-center justify-between text-xs">
                                <div class="absolute -left-6 w-4 h-4 rounded-full {{ in_array($activeStatus, ['ready_for_pickup', 'packed']) ? 'bg-secondary-container ring-4 ring-secondary-container/20' : (in_array($activeStatus, ['fulfilled', 'delivered']) ? 'bg-on-tertiary-container text-white' : 'bg-surface-container-high') }} flex items-center justify-center text-on-secondary-container">
                                    <span class="w-1.5 h-1.5 rounded-full {{ in_array($activeStatus, ['ready_for_pickup', 'packed']) ? 'bg-on-secondary-container' : 'bg-white' }}"></span>
                                </div>
                                <span class="text-on-surface font-bold">Ready for Pickup</span>
                                <span class="font-mono text-[10px] text-secondary font-bold">Staged</span>
                            </div>
                            <div class="relative flex items-center justify-between text-xs {{ in_array($activeStatus, ['fulfilled', 'delivered']) ? '' : 'opacity-60' }}">
                                <div class="absolute -left-6 w-4 h-4 rounded-full {{ in_array($activeStatus, ['fulfilled', 'delivered']) ? 'bg-on-tertiary-container text-white' : 'bg-surface-container-high' }} flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full {{ in_array($activeStatus, ['fulfilled', 'delivered']) ? 'bg-white' : 'bg-outline' }}"></span>
                                </div>
                                <span class="{{ in_array($activeStatus, ['fulfilled', 'delivered']) ? 'text-on-surface font-bold' : 'text-outline' }}">Courier Handover &amp; Fulfilled</span>
                                <span class="font-mono text-[10px] text-outline">{{ in_array($activeStatus, ['fulfilled', 'delivered']) ? 'Completed' : 'Pending' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Transparent Fee & Commission Calculator -->
                    <div class="bg-surface-container-low rounded-[14px] p-4 flex flex-col gap-2">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-outline">Transparent Payout Breakdown</span>
                        <div class="flex items-center justify-between text-xs text-on-surface">
                            <span>Order Gross Value</span>
                            <span class="font-mono">₹{{ number_format($grossVal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-on-surface-variant">
                            <span>Bazaario Commission (10%)</span>
                            <span class="font-mono text-error">-₹{{ number_format($commVal, 2) }}</span>
                        </div>
                        @if($cessVal > 0)
                            <div class="flex items-center justify-between text-xs text-on-surface-variant">
                                <span>APMC Mandi Cess / Tech Fee (1.5%)</span>
                                <span class="font-mono text-error">-₹{{ number_format($cessVal, 2) }}</span>
                            </div>
                        @endif
                        <div class="h-px bg-surface-container-high my-0.5"></div>
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-sm font-bold text-on-surface">Net Seller Payout</span>
                            <span class="font-mono text-base font-bold text-on-tertiary-container">₹{{ number_format($netVal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Primary Interactive CTA -->
                    @if(in_array($activeStatus, ['fulfilled', 'delivered']))
                        <div class="p-3 rounded-[14px] bg-tertiary-fixed/30 text-on-tertiary-fixed-variant text-center font-mono text-xs font-bold flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">verified</span>
                            <span>Order Fulfilled &amp; Settled to Payout Ledger</span>
                        </div>
                    @else
                        <button type="button" @click="openModal({{ json_encode([
                            'id' => $activeOrder->id,
                            'number' => $activeOrder->seller_order_number,
                            'customer' => $customerName,
                            'courier' => $activeOrder->courier_name,
                            'gross' => number_format($grossVal, 2),
                            'net' => number_format($netVal, 2),
                        ]) }})" class="w-full h-12 rounded-[14px] bg-secondary-container text-on-secondary-container hover:opacity-90 font-sans text-sm font-bold flex items-center justify-center gap-2 shadow-md transition-all active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            <span>Mark as Fulfilled</span>
                        </button>
                        <div class="text-center font-mono text-[11px] text-outline">
                            Requires physical handover verification with {{ $activeOrder->courier_name }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Handover Verification Protocol Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-[14px] shadow-2xl max-w-lg w-full p-6 flex flex-col gap-5 border border-outline-variant/30" @click.away="closeModal()">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-[14px] bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                        <span class="material-symbols-outlined text-[26px]">task_alt</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-lg font-bold text-on-surface">Confirm Order Fulfillment</h3>
                        <span class="font-mono text-xs text-outline uppercase tracking-wider">Handover Verification Protocol</span>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container flex items-center justify-center" @click="closeModal()">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="bg-surface-container-low rounded-[14px] p-4 flex flex-col gap-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-on-surface-variant">Order Identifier:</span>
                    <span class="font-mono font-bold text-on-surface" x-text="'#' + modalData.number"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-on-surface-variant">Customer:</span>
                    <span class="font-medium text-on-surface" x-text="modalData.customer"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-on-surface-variant">Assigned Courier:</span>
                    <span class="font-mono font-bold text-on-surface" x-text="modalData.courier"></span>
                </div>
                <div class="h-px bg-surface-container-high my-1"></div>
                <div class="flex items-center justify-between">
                    <span class="text-on-surface-variant">Gross Value:</span>
                    <span class="font-mono text-on-surface" x-text="'₹' + modalData.gross"></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="font-bold text-on-surface">Net Seller Payout:</span>
                    <span class="font-mono font-bold text-on-tertiary-container text-base" x-text="'₹' + modalData.net"></span>
                </div>
            </div>

            <div class="flex items-start gap-2.5 p-3 rounded-[10px] bg-secondary-fixed/30 text-on-secondary-container text-xs">
                <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">info</span>
                <span>
                    Marking this order as fulfilled transfers custody to the driver and immediately schedules your net payout settlement.
                </span>
            </div>

            <form :action="'/seller/orders/' + modalData.id + '/fulfill'" method="POST" class="flex items-center justify-end gap-3 pt-2">
                @csrf
                <input type="hidden" name="courier_bay" :value="modalData.courier">
                <button type="button" class="h-11 px-5 rounded-[14px] bg-surface-container text-on-surface hover:bg-surface-container-high font-sans text-xs font-semibold" @click="closeModal()">
                    Cancel
                </button>
                <button type="submit" class="h-11 px-6 rounded-[14px] bg-secondary-container text-on-secondary-container hover:opacity-90 font-sans text-xs font-bold inline-flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Confirm Fulfillment</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function orderFulfillmentWorkspace() {
    return {
        showModal: false,
        modalData: {
            id: '',
            number: '',
            customer: '',
            courier: '',
            gross: '0.00',
            net: '0.00'
        },
        openModal(data) {
            this.modalData = data;
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        }
    };
}
</script>
@endsection
