@extends('layouts.seller')

@section('title', 'Order #' . $sellerOrder->seller_order_number . ' — Bazaario')

@section('content')
@php
    $parentOrder = $sellerOrder->order;
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
    $grossVal = (float) $sellerOrder->subtotal;
    $commVal = (float) $sellerOrder->commission_amount;
    $cessVal = (float) $sellerOrder->apmc_cess;
    $netVal = (float) ($sellerOrder->payout_amount > 0 ? $sellerOrder->payout_amount : $sellerOrder->net_payout_calculated);
@endphp

<div class="flex flex-col w-full pb-16">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col gap-1 mb-6">
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-outline uppercase tracking-wider">
            <a href="{{ route('seller.orders.index') }}" class="hover:text-on-surface transition-colors">Orders</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-semibold">#{{ $sellerOrder->seller_order_number }}</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-3xl font-bold text-on-surface tracking-tight">Order #{{ $sellerOrder->seller_order_number }}</h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-[6px] bg-secondary-fixed text-on-secondary-fixed font-mono text-xs font-bold uppercase tracking-wider">
                        {{ ucfirst(str_replace('_', ' ', $sellerOrder->status)) }}
                    </span>
                </div>
                <p class="font-sans text-sm text-on-surface-variant max-w-2xl mt-1">
                    Placed at {{ $sellerOrder->created_at?->format('F j, Y, g:i A \I\S\T') ?? 'Today' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.orders.index') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-colors shadow-sm flex items-center gap-2 font-sans text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Back to Orders</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Assigned Delivery Slot Card -->
    <div class="relative overflow-hidden rounded-[14px] bg-primary text-white p-5 shadow-sm mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="font-mono text-[11px] uppercase tracking-widest text-slate-300 font-bold block mb-1">Assigned Delivery Slot</span>
                <div class="font-heading text-xl font-bold text-white">
                    {{ $sellerOrder->delivery_slot ?? 'Today, 4:00 PM – 6:00 PM' }}
                </div>
                <div class="flex items-center gap-2 text-slate-300 text-xs mt-1">
                    <span class="material-symbols-outlined text-[16px] text-secondary-container">sports_motorsports</span>
                    <span>{{ $sellerOrder->courier_name }}</span>
                    <span>•</span>
                    <span class="font-mono text-green-400 font-semibold">Courier Slot Confirmed</span>
                </div>
            </div>
            @if(in_array($sellerOrder->status, ['ready_for_pickup', 'packed']))
                <form method="POST" action="{{ route('seller.orders.fulfill', $sellerOrder) }}">
                    @csrf
                    <button type="submit" class="h-11 px-5 rounded-[14px] bg-secondary-container text-on-secondary-container hover:opacity-90 font-sans text-xs font-bold inline-flex items-center gap-2 shadow-md">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        <span>Confirm Handover &amp; Fulfill</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- 2 Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Itemized Order Consignment -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <div class="bg-surface-container-lowest rounded-[14px] shadow-sm overflow-hidden p-6 flex flex-col gap-4">
                <h3 class="font-heading text-lg font-bold text-on-surface">Order Items ({{ $sellerOrder->items->count() }} SKUs)</h3>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left font-sans text-xs">
                        <thead>
                            <tr class="bg-surface-container-low/60 text-outline font-mono text-[11px] uppercase tracking-wider">
                                <th class="py-3 px-3">Product</th>
                                <th class="py-3 px-3">SKU</th>
                                <th class="py-3 px-3 text-right">Unit Price</th>
                                <th class="py-3 px-3 text-center">Qty</th>
                                <th class="py-3 px-3 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container/60">
                            @forelse($sellerOrder->items as $item)
                                <tr>
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-10 h-10 rounded-lg bg-surface-container-high overflow-hidden shrink-0 flex items-center justify-center text-outline">
                                                @if($item->product_image)
                                                    <img class="w-full h-full object-cover" src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}">
                                                @else
                                                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                                @endif
                                            </div>
                                            <span class="font-semibold text-on-surface">{{ $item->product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 font-mono text-outline">{{ $item->sku ?? 'N/A' }}</td>
                                    <td class="py-3 px-3 text-right font-mono">₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-3 px-3 text-center font-mono font-bold">{{ $item->quantity }}</td>
                                    <td class="py-3 px-3 text-right font-mono font-bold text-on-surface">₹{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-on-surface-variant">No items recorded in this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-surface-container-high pt-4 flex flex-col gap-2 max-w-xs ml-auto text-xs">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Subtotal:</span>
                        <span class="font-mono font-semibold">₹{{ number_format($grossVal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-error">
                        <span>Platform Commission (10%):</span>
                        <span class="font-mono">-₹{{ number_format($commVal, 2) }}</span>
                    </div>
                    @if($cessVal > 0)
                        <div class="flex justify-between text-error">
                            <span>APMC Mandi Cess (1.5%):</span>
                            <span class="font-mono">-₹{{ number_format($cessVal, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-surface-container-high pt-2 flex justify-between font-bold text-sm">
                        <span>Net Seller Payout:</span>
                        <span class="font-mono text-on-tertiary-container">₹{{ number_format($netVal, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Customer & Logistics Info -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <!-- Customer Details Card -->
            <div class="bg-surface-container-lowest rounded-[14px] shadow-sm p-6 flex flex-col gap-3">
                <span class="font-mono text-[11px] uppercase tracking-wider text-outline">Customer &amp; Shipping</span>
                <div class="font-sans text-base font-semibold text-on-surface">{{ $customerName }}</div>
                <div class="flex items-center gap-2 font-mono text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-[16px] text-outline">call</span>
                    <span>{{ $maskedPhone }}</span>
                </div>
                <div class="flex items-start gap-2 font-sans text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-[16px] text-outline mt-0.5 shrink-0">location_on</span>
                    <span>{{ $addressLine }}</span>
                </div>
                @if($parentOrder?->notes)
                    <div class="mt-2 pt-2 border-t border-surface-container-high font-sans text-xs text-on-surface-variant">
                        <span class="font-semibold text-on-surface">Buyer Notes:</span>
                        <p class="mt-0.5">{{ $parentOrder->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Handover & Logistics Status -->
            <div class="bg-surface-container-lowest rounded-[14px] shadow-sm p-6 flex flex-col gap-3">
                <span class="font-mono text-[11px] uppercase tracking-wider text-outline">Fulfillment Status</span>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-on-surface-variant">Handover Status:</span>
                    <span class="font-mono font-bold {{ in_array($sellerOrder->status, ['fulfilled', 'delivered']) ? 'text-on-tertiary-container' : 'text-secondary' }}">
                        {{ in_array($sellerOrder->status, ['fulfilled', 'delivered']) ? 'Handover Confirmed' : 'Pending Custody Transfer' }}
                    </span>
                </div>
                @if($sellerOrder->delivered_at)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-on-surface-variant">Delivered At:</span>
                        <span class="font-mono">{{ $sellerOrder->delivered_at->format('M j, Y H:i \I\S\T') }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-xs">
                    <span class="text-on-surface-variant">Tracking Number:</span>
                    <span class="font-mono">{{ $sellerOrder->tracking_number ?? 'BZ-TRK-PENDING' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
