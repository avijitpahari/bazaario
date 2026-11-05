@extends('layouts.seller')

@section('title', 'Orders Workspace — Order Fulfillment & Live Queue — Bazaario')

@section('content')
@php
    $seller = $sellerUser ?? Auth::guard('seller')->user() ?? Auth::user();
    $profile = $sellerProfile ?? $seller?->sellerProfile;
    $shopName = $profile?->shop_name ?? 'My Farm';
    $sellerStoreId = str_pad($seller?->id ?? 1, 4, '0', STR_PAD_LEFT);
    $activeOrder = $focusedOrder ?? $selectedOrder ?? $orders->first() ?? null;

    $pendingOrdersCount = $stats['pending_orders_count'] ?? (
        ($counts['pending'] ?? 0) + ($counts['confirmed'] ?? 0) + ($counts['processing'] ?? 0) + ($counts['ready_for_pickup'] ?? 0)
    );
    $hasPendingDispatches = ($pendingOrdersCount > 0);
    $activeDispatchSlot = $stats['next_dispatch_slot'] ?? ($hasPendingDispatches ? ($activeOrder?->delivery_slot ?? 'Today, 4:00 PM – 6:00 PM') : 'No Active Slots');
@endphp

<div class="flex flex-col w-full pb-16" x-data="orderFulfillmentWorkspace()">
    <!-- Top Breadcrumbs & Command Bar -->
    <div class="flex flex-col gap-2 mb-6">
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-slate-400 uppercase tracking-wider">
            <span>Seller Center</span>
            <span class="material-symbols-outlined text-[14px] text-slate-300">chevron_right</span>
            <span>Orders</span>
            <span class="material-symbols-outlined text-[14px] text-slate-300">chevron_right</span>
            <span class="text-slate-800 font-bold">Live Dispatch Queue</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div>
                <h1 class="font-heading text-3xl font-extrabold text-slate-900 tracking-tight">My Orders</h1>
                <p class="font-sans text-sm text-slate-500 max-w-2xl mt-1">
                    Manage verified orders assigned strictly to <span class="text-slate-900 font-semibold">{{ $shopName }}</span>. Live telemetry, delivery slot assignments, and automated status pipeline.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" class="group h-11 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/90 shadow-2xs hover:shadow-xs transition-all flex items-center gap-2 font-sans text-xs font-semibold">
                    <span class="material-symbols-outlined text-[18px] text-slate-500 group-hover:text-slate-900 transition-colors">download</span>
                    <span>Export Orders CSV</span>
                </button>
                <button type="button" class="group h-11 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white shadow-xs hover:shadow-md transition-all flex items-center gap-2 font-sans text-xs font-semibold">
                    <span class="material-symbols-outlined text-[18px] text-amber-400 transition-transform group-hover:scale-110">print</span>
                    <span>Batch Print Shipping Labels</span>
                </button>
            </div>
        </div>
    </div>

    <!-- High-Impact Dispatch Operational Banner -->
    @if($hasPendingDispatches)
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500/10 via-white to-white border border-amber-200/80 shadow-2xs p-4 sm:p-5 mb-6">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-amber-400 to-amber-600"></div>
            <div class="flex flex-wrap items-center justify-between gap-4 pl-2">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/15 border border-amber-300/40 flex items-center justify-center text-amber-700 shadow-2xs">
                        <span class="material-symbols-outlined text-[26px]">local_shipping</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Assigned Dispatch Window</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[10px] font-bold uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Courier Slot Confirmed
                            </span>
                        </div>
                        <div class="font-heading text-lg font-bold text-slate-900 tracking-tight mt-0.5">
                            {{ $activeDispatchSlot }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-5 sm:gap-6">
                    <div class="flex flex-col text-right">
                        <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Telemetry Target</span>
                        <div class="flex items-center gap-1.5 font-mono text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-lg shadow-2xs">
                            <span class="material-symbols-outlined text-[15px] text-rose-600 animate-spin" style="animation-duration: 4s;">timer</span>
                            <span>{{ $pendingOrdersCount }} {{ Str::plural('order', $pendingOrdersCount) }} to pack</span>
                        </div>
                    </div>
                    <div class="hidden sm:block h-9 w-px bg-slate-200"></div>
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-xl">
                        <span class="font-mono text-[11px] text-slate-500">Fleet Status:</span>
                        <span class="inline-flex items-center gap-1 font-mono text-[11px] text-emerald-700 font-bold">
                            <span class="material-symbols-outlined text-[14px] text-emerald-600">sensors</span>
                            Hyperlocal Hub Active
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500/10 via-white to-white border border-emerald-200/80 shadow-2xs p-4 sm:p-5 mb-6">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-emerald-400 to-emerald-600"></div>
            <div class="flex flex-wrap items-center justify-between gap-4 pl-2">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/15 border border-emerald-300/40 flex items-center justify-center text-emerald-700 shadow-2xs">
                        <span class="material-symbols-outlined text-[26px]">task_alt</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Fulfillment Telemetry</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[10px] font-bold uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                All Dispatches Complete
                            </span>
                        </div>
                        <div class="font-heading text-lg font-bold text-slate-900 tracking-tight mt-0.5">
                            @if(($counts['fulfilled'] ?? 0) > 0)
                                All Current Orders Fulfilled &amp; Delivered
                            @else
                                Fulfillment Queue Clear — Ready for New Orders
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-5 sm:gap-6">
                    <div class="flex flex-col text-right">
                        <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Telemetry Target</span>
                        <div class="flex items-center gap-1.5 font-mono text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg shadow-2xs">
                            <span class="material-symbols-outlined text-[15px] text-emerald-600">verified</span>
                            <span>0 orders waiting to pack</span>
                        </div>
                    </div>
                    <div class="hidden sm:block h-9 w-px bg-slate-200"></div>
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-xl">
                        <span class="font-mono text-[11px] text-slate-500">Fleet Status:</span>
                        <span class="inline-flex items-center gap-1 font-mono text-[11px] text-emerald-700 font-bold">
                            <span class="material-symbols-outlined text-[14px] text-emerald-600">sensors</span>
                            Standby • Ready for Next Slot
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters, Search & State Navigation Bar -->
    <div class="flex flex-col gap-3.5 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('seller.orders.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative min-w-[260px] max-w-sm flex-1 flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[18px]">search</span>
                    <input type="text" name="search" value="{{ request('search', $searchQuery ?? '') }}"
                           class="w-full h-11 pl-10 pr-4 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200/80 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl font-sans text-xs text-slate-900 placeholder:text-slate-400 outline-none transition-all shadow-2xs"
                           placeholder="Filter Order ID #BZ-XXXX, Buyer...">
                </div>
                <div class="inline-flex p-1 bg-slate-100 rounded-xl font-sans text-xs font-semibold border border-slate-200/70">
                    <button type="submit" name="date" value="today" class="px-3 py-1.5 rounded-lg transition-all {{ request('date') === 'today' ? 'bg-white font-bold text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Today</button>
                    <button type="submit" name="date" value="yesterday" class="px-3 py-1.5 rounded-lg transition-all {{ request('date') === 'yesterday' ? 'bg-white font-bold text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Yesterday</button>
                    <button type="submit" name="date" value="7d" class="px-3 py-1.5 rounded-lg transition-all {{ in_array(request('date'), ['7d', 'last_7_days']) ? 'bg-white font-bold text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Last 7 Days</button>
                </div>
            </form>
            <div class="flex items-center gap-2 text-slate-500 font-mono text-[11px] bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-xl uppercase">
                <span class="material-symbols-outlined text-[16px] text-emerald-600">verified</span>
                <span>Store ID: <span class="font-mono text-slate-900 font-bold">#BZ-SLR-{{ $sellerStoreId }}</span></span>
            </div>
        </div>

        <!-- Segmented Status Tabs -->
        @php
            $tabDefs = [
                'all'              => ['label' => 'All Orders', 'count' => $counts['all'] ?? 0, 'dot' => 'bg-slate-400'],
                'pending'          => ['label' => 'Pending', 'count' => $counts['pending'] ?? 0, 'dot' => 'bg-amber-500'],
                'confirmed'        => ['label' => 'Confirmed', 'count' => $counts['confirmed'] ?? 0, 'dot' => 'bg-slate-600'],
                'processing'       => ['label' => 'Processing', 'count' => $counts['processing'] ?? 0, 'dot' => 'bg-sky-500'],
                'ready_for_pickup' => ['label' => 'Ready for Pickup', 'count' => $counts['ready_for_pickup'] ?? 0, 'dot' => 'bg-indigo-500'],
                'fulfilled'        => ['label' => 'Fulfilled', 'count' => $counts['fulfilled'] ?? 0, 'dot' => 'bg-emerald-500'],
                'cancelled'        => ['label' => 'Cancelled', 'count' => $counts['cancelled'] ?? 0, 'dot' => 'bg-rose-500'],
            ];
            $activeStatusTab = request('status', 'all');
        @endphp
        <div class="flex items-center gap-2 overflow-x-auto pt-1 pb-1 text-nowrap scrollbar-none">
            @foreach($tabDefs as $tabKey => $tabInfo)
                @php
                    $isTabActive = ($activeStatusTab === $tabKey) || ($tabKey === 'fulfilled' && request('status') === 'delivered') || ($tabKey === 'pending' && request('status') === 'placed');
                @endphp
                <a href="{{ route('seller.orders.index', array_filter(['status' => $tabKey !== 'all' ? $tabKey : null, 'search' => request('search'), 'date' => request('date')])) }}"
                   class="px-3.5 py-2 rounded-xl font-sans text-xs flex items-center gap-2 transition-all {{ $isTabActive ? 'bg-slate-900 text-white font-bold shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 font-semibold border border-slate-200/60' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $isTabActive ? 'bg-amber-400' : $tabInfo['dot'] }}"></span>
                    <span>{{ $tabInfo['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-full {{ $isTabActive ? 'bg-white/20 text-white' : 'bg-slate-200/80 text-slate-700' }} font-mono text-[10px] font-bold">
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
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-200/80 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="font-heading text-lg font-bold text-slate-900">Queue Management</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-mono text-xs font-bold">{{ $orders->total() ?? count($orders) }} Orders</span>
                    </div>
                    <div class="flex items-center gap-2 font-mono text-xs text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Auto-refresh active</span>
                    </div>
                </div>

                @if($orders->isEmpty())
                    <!-- Clean Zero State Card -->
                    <div class="p-12 sm:p-16 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200/70 text-amber-700 flex items-center justify-center mb-4 shadow-2xs">
                            <span class="material-symbols-outlined text-[32px]">inventory_2</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-slate-900">No orders found</h3>
                        <p class="font-sans text-sm text-slate-500 max-w-md mt-1">
                            No orders in queue for <span class="font-semibold text-slate-800">{{ $shopName }}</span>. Orders placed by customers in your hyperlocal zone will appear here immediately.
                        </p>
                        @if(request('status') || request('search') || request('date'))
                            <a href="{{ route('seller.orders.index') }}" class="mt-4 px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 font-sans text-xs font-bold transition-all shadow-xs">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                @else
                    <!-- Table Container -->
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/90 text-slate-500 font-mono text-[10px] uppercase tracking-wider border-b border-slate-200/80">
                                    <th class="py-3.5 pl-5 pr-2 font-bold">Order ID</th>
                                    <th class="py-3.5 px-3 font-bold">Customer &amp; Area</th>
                                    <th class="py-3.5 px-3 font-bold">Products</th>
                                    <th class="py-3.5 px-3 font-bold">Amount</th>
                                    <th class="py-3.5 px-3 font-bold">Delivery Slot</th>
                                    <th class="py-3.5 px-3 font-bold">Status</th>
                                    <th class="py-3.5 pr-5 pl-2 text-right font-bold">Next Action</th>
                                </tr>
                            </thead>
                            <tbody class="font-sans text-xs divide-y divide-slate-100">
                                @foreach($orders as $order)
                                    @php
                                        $isSelected = ($activeOrder && $activeOrder->id === $order->id);
                                        $statusBadge = match($order->status) {
                                            'placed', 'pending' => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200/80', 'dot' => 'bg-amber-500'],
                                            'confirmed' => ['bg' => 'bg-slate-100 text-slate-800 border-slate-200/80', 'dot' => 'bg-slate-600'],
                                            'processing' => ['bg' => 'bg-sky-50 text-sky-800 border-sky-200/80', 'dot' => 'bg-sky-500'],
                                            'packed', 'ready_for_pickup' => ['bg' => 'bg-indigo-50 text-indigo-800 border-indigo-200/80', 'dot' => 'bg-indigo-500'],
                                            'delivered', 'fulfilled', 'shipped' => ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80', 'dot' => 'bg-emerald-500'],
                                            'cancelled', 'returned' => ['bg' => 'bg-rose-50 text-rose-800 border-rose-200/80', 'dot' => 'bg-rose-500'],
                                            default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'],
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
                                    <tr class="{{ $isSelected ? 'bg-amber-50/40 border-l-4 border-amber-500' : 'hover:bg-slate-50/80' }} cursor-pointer transition-colors"
                                        onclick="window.location.href='{{ route('seller.orders.index', array_merge(request()->query(), ['order_id' => $order->id])) }}'">
                                        <td class="py-4 pl-5 pr-2 align-top">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full {{ $isSelected ? 'bg-amber-500 animate-pulse' : 'bg-slate-300' }}"></span>
                                                <span class="font-mono text-xs font-bold text-slate-900">#{{ $order->seller_order_number }}</span>
                                            </div>
                                            <span class="font-mono text-[10px] text-slate-400 block pl-3.5 mt-0.5">Express Dispatch</span>
                                        </td>
                                        <td class="py-4 px-3 align-top">
                                            <span class="font-bold text-slate-900 block leading-tight">{{ $order->order?->delivery_full_name ?? $order->order?->user?->name ?? 'Customer' }}</span>
                                            <span class="font-sans text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-[13px] text-slate-400">location_on</span>
                                                <span>{{ $order->order?->delivery_city ?? 'Hyperlocal Area' }}</span>
                                            </span>
                                        </td>
                                        <td class="py-4 px-3 align-top max-w-[190px]">
                                            <div class="line-clamp-2 text-slate-800 font-medium">
                                                {{ !empty($itemsSummary) ? $itemsSummary : 'Agricultural Produce' }}
                                            </div>
                                            <span class="inline-flex items-center gap-1 font-mono text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded mt-1 font-semibold">
                                                {{ $order->items->count() }} items
                                            </span>
                                        </td>
                                        <td class="py-4 px-3 align-top">
                                            <span class="font-mono text-xs font-extrabold text-slate-900">₹{{ number_format($order->subtotal, 2) }}</span>
                                            <span class="font-mono text-[10px] text-emerald-700 block font-semibold mt-0.5">Prepaid (Escrow)</span>
                                        </td>
                                        <td class="py-4 px-3 align-top">
                                            @if($order->status === 'delivered')
                                                <div class="flex items-center gap-1 font-bold text-emerald-700 leading-tight">
                                                    <span class="material-symbols-outlined text-[15px] text-emerald-600">task_alt</span>
                                                    <span>Delivered</span>
                                                </div>
                                                <span class="font-mono text-[10px] text-slate-500 block mt-0.5">Slot: {{ $order->delivery_slot ?? 'Completed' }}</span>
                                                <span class="font-mono text-[10px] text-slate-400 block">{{ $order->courier_name }}</span>
                                            @elseif($order->status === 'fulfilled')
                                                <div class="flex items-center gap-1 font-bold text-emerald-700 leading-tight">
                                                    <span class="material-symbols-outlined text-[15px] text-emerald-600">verified</span>
                                                    <span>Handover Complete</span>
                                                </div>
                                                <span class="font-mono text-[10px] text-slate-500 block mt-0.5">Slot: {{ $order->delivery_slot ?? 'Completed' }}</span>
                                                <span class="font-mono text-[10px] text-slate-400 block">{{ $order->courier_name }}</span>
                                            @else
                                                <span class="text-slate-800 font-semibold block leading-tight">{{ $order->delivery_slot ?? 'Today, 4:00 PM – 6:00 PM' }}</span>
                                                <span class="font-mono text-[10px] text-slate-400 block mt-0.5">{{ $order->courier_name }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-3 align-top">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $statusBadge['bg'] }} border font-mono text-[10px] font-bold uppercase tracking-wider shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                                <span>{{ $statusLabel }}</span>
                                            </span>
                                        </td>
                                        <td class="py-4 pr-5 pl-2 align-top text-right" onclick="event.stopPropagation()">
                                            @if(in_array($order->status, ['placed', 'pending']))
                                                <form method="POST" action="{{ route('seller.orders.update-status', $order) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="processing">
                                                    <button type="submit" class="h-9 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-sans text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs hover:shadow-xs transition-all active:scale-95">
                                                        <span class="material-symbols-outlined text-[16px] text-amber-400">thumb_up</span>
                                                        <span>Confirm Order</span>
                                                    </button>
                                                </form>
                                            @elseif($order->status === 'confirmed')
                                                <form method="POST" action="{{ route('seller.orders.update-status', $order) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="processing">
                                                    <button type="submit" class="h-9 px-3 rounded-xl bg-white hover:bg-slate-50 text-slate-800 font-sans text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs border border-slate-200/90 transition-all active:scale-95">
                                                        <span class="material-symbols-outlined text-[16px] text-sky-600">box</span>
                                                        <span>Start Packing</span>
                                                    </button>
                                                </form>
                                            @elseif($order->status === 'processing')
                                                <form method="POST" action="{{ route('seller.orders.update-status', $order) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="ready_for_pickup">
                                                    <button type="submit" class="h-9 px-3 rounded-xl bg-white hover:bg-slate-50 text-slate-800 font-sans text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs border border-slate-200/90 transition-all active:scale-95">
                                                        <span class="material-symbols-outlined text-[16px] text-indigo-600">inventory</span>
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
                                                ]) }})" class="h-9 px-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs transition-all active:scale-95">
                                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                                    <span>Mark Fulfilled</span>
                                                </button>
                                            @elseif(in_array($order->status, ['fulfilled', 'delivered']))
                                                <a href="{{ route('seller.orders.show', $order) }}" class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-sans text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
                                                    <span class="material-symbols-outlined text-[16px] text-slate-500">receipt_long</span>
                                                    <span>View Invoice</span>
                                                </a>
                                            @else
                                                <span class="text-slate-400 font-mono text-[11px]">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($orders->hasPages())
                        <div class="px-6 py-3.5 border-t border-slate-200/80 bg-slate-50/50">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @endif

                <!-- Tenancy Data Isolation Footer Banner -->
                <div class="p-4 bg-slate-50/70 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/80">
                    <div class="flex items-center gap-2 text-slate-600">
                        <span class="material-symbols-outlined text-[18px] text-slate-700">security</span>
                        <span class="font-mono text-xs">
                            Data Isolation Active: You only have access to seller-authenticated orders for <span class="font-bold text-slate-900">{{ $shopName }}</span> (Store ID #BZ-SLR-{{ $sellerStoreId }}).
                        </span>
                    </div>
                    <div class="font-mono text-[11px] text-slate-400">
                        TLS 1.3 / E2E Encrypted Payload
                    </div>
                </div>
            </div>

            <!-- Operational Gauges (Capacity, SLA, Pending Accrual) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-bold">Dispatch Capacity</span>
                        <span class="font-mono text-xs font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">{{ $stats['dispatch_capacity'] ?? 82 }}%</span>
                    </div>
                    <div class="my-3">
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden p-0.5 border border-slate-200/60">
                            <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-full rounded-full transition-all" style="width: {{ $stats['dispatch_capacity'] ?? 82 }}%;"></div>
                        </div>
                    </div>
                    <span class="font-sans text-xs text-slate-500 font-medium">14 of 18 packing crates sealed</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-bold">On-Time SLA Record</span>
                        <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">{{ $stats['on_time_sla'] ?? 99.4 }}%</span>
                    </div>
                    <div class="flex items-center gap-1.5 my-2.5 text-emerald-700 font-heading text-sm font-bold">
                        <span class="material-symbols-outlined text-[18px]">verified_user</span>
                        <span>Prime Merchant Tier</span>
                    </div>
                    <span class="font-sans text-xs text-slate-500 font-medium">Last incident: 38 days ago</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-bold">Pending Payout Accrual</span>
                        <span class="font-mono text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">T+1 Auto Payout</span>
                    </div>
                    <div class="font-heading text-2xl font-black text-slate-900 mt-2">
                        ₹{{ number_format($stats['pending_accrual'] ?? 0, 2) }}
                    </div>
                    <span class="font-sans text-xs text-slate-500 font-medium mt-1">Will clear tomorrow 08:00 AM</span>
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
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col gap-5 sticky top-20">
                    <!-- Detail Header -->
                    <div class="flex items-start justify-between pb-3 border-b border-slate-200/80">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-heading text-xl font-bold text-slate-900 tracking-tight">Order #{{ $activeOrder->seller_order_number }}</span>
                                <a href="{{ route('seller.orders.show', $activeOrder) }}" class="text-slate-400 hover:text-slate-900 transition-colors p-1 rounded-lg hover:bg-slate-100" title="View Full Dossier">
                                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                </a>
                            </div>
                            <span class="font-mono text-[11px] text-slate-400 uppercase block mt-0.5">Placed at {{ $activeOrder->created_at?->format('H:i \I\S\T') ?? 'Today' }}</span>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200 font-mono text-xs font-bold uppercase tracking-wider">
                            {{ ucfirst(str_replace('_', ' ', $activeStatus)) }}
                        </span>
                    </div>

                    <!-- Customer Details Card -->
                    <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-200/70 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-bold">Customer Details</span>
                            <span class="inline-flex items-center gap-1 font-mono text-[10px] text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 font-bold">
                                <span class="material-symbols-outlined text-[13px] text-emerald-600">verified</span>
                                Repeat Buyer
                            </span>
                        </div>
                        <div class="font-sans text-sm font-bold text-slate-900">{{ $customerName }}</div>
                        <div class="flex items-center gap-2 font-mono text-xs text-slate-600">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">call</span>
                            <span>{{ $maskedPhone }}</span>
                        </div>
                        <div class="flex items-start gap-2 font-sans text-xs text-slate-600 mt-0.5">
                            <span class="material-symbols-outlined text-[16px] text-slate-400 mt-0.5 shrink-0">location_on</span>
                            <span>{{ $addressLine }}</span>
                        </div>
                        @if($parentOrder?->notes)
                            <div class="mt-2 pt-2 border-t border-slate-200/80 font-sans text-xs text-slate-600">
                                <span class="font-bold text-slate-800">Delivery Notes:</span>
                                <span>{{ $parentOrder->notes }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- PROMINENT ASSIGNED / FULFILLED DELIVERY SLOT CARD -->
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 to-slate-800 text-white p-4 shadow-sm">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-[10px] uppercase tracking-widest text-slate-300 font-bold">
                                    {{ in_array($activeStatus, ['delivered', 'fulfilled']) ? 'Delivered Slot & Status' : 'Assigned Delivery Slot' }}
                                </span>
                                @if($activeStatus === 'delivered')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 font-mono text-[10px] font-bold uppercase">
                                        Delivered
                                    </span>
                                @elseif($activeStatus === 'fulfilled')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 font-mono text-[10px] font-bold uppercase">
                                        Fulfilled
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30 font-mono text-[10px] font-bold uppercase">
                                        Slot Confirmed
                                    </span>
                                @endif
                            </div>
                            <div class="font-heading text-lg font-bold text-white mt-0.5">
                                {{ $activeOrder->delivery_slot ?? 'Today, 4:00 PM – 6:00 PM' }}
                            </div>
                            <div class="pt-2 flex items-center justify-between border-t border-white/10 text-xs">
                                <div class="flex items-center gap-1.5 text-slate-300 font-medium">
                                    <span class="material-symbols-outlined text-[18px] {{ in_array($activeStatus, ['delivered', 'fulfilled']) ? 'text-emerald-400' : 'text-amber-400' }}">
                                        {{ in_array($activeStatus, ['delivered', 'fulfilled']) ? 'check_circle' : 'sports_motorsports' }}
                                    </span>
                                    <span>{{ $activeOrder->courier_name }}</span>
                                </div>
                                @if($activeStatus === 'delivered')
                                    <span class="font-mono text-[11px] font-bold text-emerald-400 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">done_all</span>
                                        Delivered to Customer
                                    </span>
                                @elseif($activeStatus === 'fulfilled')
                                    <span class="font-mono text-[11px] font-bold text-emerald-400 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">verified</span>
                                        Handover Complete
                                    </span>
                                @else
                                    <span class="font-mono text-[11px] font-bold text-emerald-400 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Assigned &amp; En Route
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Itemized Products Breakdown -->
                    <div class="flex flex-col gap-2">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-bold">Order Items ({{ $activeOrder->items->count() }} SKUs)</span>
                        <div class="bg-slate-50/80 rounded-xl p-3.5 border border-slate-200/70 flex flex-col gap-2.5">
                            @forelse($activeOrder->items as $item)
                                <div class="flex items-start justify-between gap-3 pb-2.5 border-b border-slate-200/70 last:border-b-0 last:pb-0">
                                    <div class="flex gap-2.5 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center text-slate-400">
                                            @if($item->product_image)
                                                <img class="w-full h-full object-cover" src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}">
                                            @else
                                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-sans text-xs font-bold text-slate-900 truncate">{{ $item->product_name }}</div>
                                            <div class="font-mono text-[10px] text-slate-500">Qty: {{ $item->quantity }} @ ₹{{ number_format($item->unit_price, 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="font-mono text-xs font-bold text-slate-900 whitespace-nowrap">
                                        ₹{{ number_format($item->total_price, 2) }}
                                    </div>
                                </div>
                            @empty
                                <div class="font-sans text-xs text-slate-500">No individual items recorded.</div>
                            @endforelse

                            <div class="h-px bg-slate-200 my-1"></div>
                            <div class="flex items-center justify-between font-sans text-xs text-slate-800">
                                <span class="font-medium text-slate-500">Gross Products Subtotal</span>
                                <span class="font-mono font-bold text-slate-900">₹{{ number_format($grossVal, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Fulfillment Milestones Stepper -->
                    <div class="flex flex-col gap-2.5 pt-1">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-bold">Fulfillment Milestones</span>
                        <div class="flex flex-col gap-3 relative pl-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                            <div class="relative flex items-center justify-between text-xs">
                                <div class="absolute -left-6 w-4 h-4 rounded-full bg-emerald-600 flex items-center justify-center text-white text-[10px]">
                                    <span class="material-symbols-outlined text-[12px]">check</span>
                                </div>
                                <span class="text-slate-800 font-medium">Pending Placed</span>
                                <span class="font-mono text-[10px] text-slate-400">13:42</span>
                            </div>
                            <div class="relative flex items-center justify-between text-xs">
                                <div class="absolute -left-6 w-4 h-4 rounded-full {{ in_array($activeStatus, ['confirmed', 'processing', 'packed', 'ready_for_pickup', 'fulfilled', 'delivered', 'shipped']) ? 'bg-emerald-600 text-white' : 'bg-slate-200' }} flex items-center justify-center text-[10px]">
                                    <span class="material-symbols-outlined text-[12px]">check</span>
                                </div>
                                <span class="text-slate-800 font-medium">Order Confirmed</span>
                                <span class="font-mono text-[10px] text-slate-400">13:45</span>
                            </div>
                            <div class="relative flex items-center justify-between text-xs">
                                <div class="absolute -left-6 w-4 h-4 rounded-full {{ in_array($activeStatus, ['processing', 'packed', 'ready_for_pickup', 'fulfilled', 'delivered', 'shipped']) ? 'bg-emerald-600 text-white' : 'bg-slate-200' }} flex items-center justify-center text-[10px]">
                                    <span class="material-symbols-outlined text-[12px]">check</span>
                                </div>
                                <span class="text-slate-800 font-medium">Packed &amp; Sealed</span>
                                <span class="font-mono text-[10px] text-slate-400">14:10</span>
                            </div>
                            <div class="relative flex items-center justify-between text-xs">
                                <div class="absolute -left-6 w-4 h-4 rounded-full {{ in_array($activeStatus, ['ready_for_pickup', 'packed']) ? 'bg-indigo-600 ring-4 ring-indigo-100' : (in_array($activeStatus, ['fulfilled', 'delivered']) ? 'bg-emerald-600 text-white' : 'bg-slate-200') }} flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full {{ in_array($activeStatus, ['ready_for_pickup', 'packed']) ? 'bg-white' : (in_array($activeStatus, ['fulfilled', 'delivered']) ? 'bg-white' : 'bg-slate-400') }}"></span>
                                </div>
                                <span class="text-slate-900 font-bold">Ready for Pickup</span>
                                <span class="font-mono text-[10px] text-indigo-700 font-bold bg-indigo-50 px-1.5 py-0.5 rounded">Staged</span>
                            </div>
                            <div class="relative flex items-center justify-between text-xs {{ in_array($activeStatus, ['fulfilled', 'delivered']) ? '' : 'opacity-60' }}">
                                <div class="absolute -left-6 w-4 h-4 rounded-full {{ in_array($activeStatus, ['fulfilled', 'delivered']) ? 'bg-emerald-600 text-white' : 'bg-slate-200' }} flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full {{ in_array($activeStatus, ['fulfilled', 'delivered']) ? 'bg-white' : 'bg-slate-400' }}"></span>
                                </div>
                                <span class="{{ in_array($activeStatus, ['fulfilled', 'delivered']) ? 'text-slate-900 font-bold' : 'text-slate-400' }}">Courier Handover &amp; Fulfilled</span>
                                <span class="font-mono text-[10px] text-slate-400">{{ in_array($activeStatus, ['fulfilled', 'delivered']) ? 'Completed' : 'Pending' }}</span>
                            </div>
                            @if($activeStatus === 'delivered')
                                <div class="relative flex items-center justify-between text-xs">
                                    <div class="absolute -left-6 w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">
                                        <span class="material-symbols-outlined text-[12px]">done_all</span>
                                    </div>
                                    <span class="text-emerald-800 font-extrabold">Delivered to Customer</span>
                                    <span class="font-mono text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Delivered</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Transparent Fee & Commission Calculator -->
                    <div class="bg-slate-50/90 rounded-xl p-4 border border-slate-200/80 flex flex-col gap-2">
                        <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-bold">Transparent Payout Breakdown</span>
                        <div class="flex items-center justify-between text-xs text-slate-700">
                            <span>Order Gross Value</span>
                            <span class="font-mono font-semibold">₹{{ number_format($grossVal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-rose-700">
                            <span>Bazaario Commission (10%)</span>
                            <span class="font-mono font-semibold">-₹{{ number_format($commVal, 2) }}</span>
                        </div>
                        @if($cessVal > 0)
                            <div class="flex items-center justify-between text-xs text-rose-700">
                                <span>APMC Mandi Cess / Tech Fee (1.5%)</span>
                                <span class="font-mono font-semibold">-₹{{ number_format($cessVal, 2) }}</span>
                            </div>
                        @endif
                        <div class="h-px bg-slate-200 my-1"></div>
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-sm font-extrabold text-slate-900">Net Seller Payout</span>
                            <span class="font-mono text-lg font-black text-emerald-700">₹{{ number_format($netVal, 2) }}</span>
                        </div>
                    </div>

                    <!-- Primary Interactive CTA -->
                    @if(in_array($activeStatus, ['fulfilled', 'delivered']))
                        <div class="p-3.5 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-200 text-center font-mono text-xs font-bold flex items-center justify-center gap-2 shadow-2xs">
                            <span class="material-symbols-outlined text-[18px] text-emerald-600">verified</span>
                            <span>{{ $activeStatus === 'delivered' ? 'Order Delivered to Customer & Settled' : 'Order Fulfilled & Settled to Payout Ledger' }}</span>
                        </div>
                    @else
                        <button type="button" @click="openModal({{ json_encode([
                            'id' => $activeOrder->id,
                            'number' => $activeOrder->seller_order_number,
                            'customer' => $customerName,
                            'courier' => $activeOrder->courier_name,
                            'gross' => number_format($grossVal, 2),
                            'net' => number_format($netVal, 2),
                        ]) }})" class="w-full h-12 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-sm font-bold flex items-center justify-center gap-2 shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            <span>Mark as Fulfilled</span>
                        </button>
                        <div class="text-center font-mono text-[11px] text-slate-400">
                            Requires physical handover verification with {{ $activeOrder->courier_name }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Handover Verification Protocol Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 sm:p-7 flex flex-col gap-5 border border-slate-200" @click.away="closeModal()">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/15 border border-amber-300/40 flex items-center justify-center text-amber-700">
                        <span class="material-symbols-outlined text-[26px]">task_alt</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-lg font-bold text-slate-900">Confirm Order Fulfillment</h3>
                        <span class="font-mono text-xs text-slate-400 uppercase tracking-wider">Handover Verification Protocol</span>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition-colors" @click="closeModal()">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/80 flex flex-col gap-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Order Identifier:</span>
                    <span class="font-mono font-bold text-slate-900" x-text="'#' + modalData.number"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Customer:</span>
                    <span class="font-bold text-slate-900" x-text="modalData.customer"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Assigned Courier:</span>
                    <span class="font-mono font-bold text-slate-900" x-text="modalData.courier"></span>
                </div>
                <div class="h-px bg-slate-200 my-1"></div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Gross Value:</span>
                    <span class="font-mono font-bold text-slate-900" x-text="'₹' + modalData.gross"></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="font-bold text-slate-900">Net Seller Payout:</span>
                    <span class="font-mono font-black text-emerald-700 text-base" x-text="'₹' + modalData.net"></span>
                </div>
            </div>

            <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-900 text-xs">
                <span class="material-symbols-outlined text-[18px] text-amber-700 shrink-0 mt-0.5">info</span>
                <span>
                    Marking this order as fulfilled transfers custody to the driver and immediately schedules your net payout settlement.
                </span>
            </div>

            <form :action="'/seller/orders/' + modalData.id + '/fulfill'" method="POST" class="flex items-center justify-end gap-3 pt-2">
                @csrf
                <input type="hidden" name="courier_bay" :value="modalData.courier">
                <button type="button" class="h-11 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-sans text-xs font-semibold transition-colors" @click="closeModal()">
                    Cancel
                </button>
                <button type="submit" class="h-11 px-6 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold inline-flex items-center gap-2 shadow-xs hover:shadow-md transition-all active:scale-95">
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
