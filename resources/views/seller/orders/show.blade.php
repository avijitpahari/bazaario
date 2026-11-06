@extends('layouts.seller')

@section('title', 'Order #' . $sellerOrder->seller_order_number . ' — Fulfillment Dossier — Bazaario')

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
    $commRateVal = (float) ($sellerOrder->commission_rate ?: ($grossVal > 0 && $commVal > 0 ? round(($commVal / $grossVal) * 100, 1) : \App\Models\SiteSetting::getCommissionRate()));

    $statusBadge = match($sellerOrder->status) {
        'placed', 'pending' => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200/80', 'dot' => 'bg-amber-500'],
        'confirmed' => ['bg' => 'bg-slate-100 text-slate-800 border-slate-200/80', 'dot' => 'bg-slate-600'],
        'processing' => ['bg' => 'bg-sky-50 text-sky-800 border-sky-200/80', 'dot' => 'bg-sky-500'],
        'packed', 'ready_for_pickup' => ['bg' => 'bg-indigo-50 text-indigo-800 border-indigo-200/80', 'dot' => 'bg-indigo-500'],
        'delivered', 'fulfilled', 'shipped' => ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80', 'dot' => 'bg-emerald-500'],
        'cancelled', 'returned' => ['bg' => 'bg-rose-50 text-rose-800 border-rose-200/80', 'dot' => 'bg-rose-500'],
        default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'],
    };
@endphp

<div class="flex flex-col w-full pb-16">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col gap-2 mb-6">
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-slate-400 uppercase tracking-wider">
            <a href="{{ route('seller.orders.index') }}" class="hover:text-slate-900 transition-colors">Orders</a>
            <span class="material-symbols-outlined text-[14px] text-slate-300">chevron_right</span>
            <span class="text-slate-800 font-bold">#{{ $sellerOrder->seller_order_number }}</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-3xl font-extrabold text-slate-900 tracking-tight">Order #{{ $sellerOrder->seller_order_number }}</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $statusBadge['bg'] }} border font-mono text-xs font-bold uppercase tracking-wider shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                        <span>{{ ucfirst(str_replace('_', ' ', $sellerOrder->status)) }}</span>
                    </span>
                </div>
                <p class="font-sans text-sm text-slate-500 max-w-2xl mt-1">
                    Placed at {{ $sellerOrder->created_at?->format('F j, Y, g:i A \I\S\T') ?? 'Today' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.orders.index') }}" class="group h-11 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/90 shadow-2xs hover:shadow-xs transition-all flex items-center gap-2 font-sans text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px] text-slate-500 transition-transform duration-200 ease-out group-hover:-translate-x-1">arrow_back</span>
                    <span>Back to Orders</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Assigned / Delivered Delivery Slot Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-5 sm:p-6 shadow-sm mb-6 border border-slate-800">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="font-mono text-[10px] uppercase tracking-widest text-slate-300 font-bold block mb-1">
                    {{ in_array($sellerOrder->status, ['delivered', 'fulfilled']) ? 'Delivered Slot & Status' : 'Assigned Delivery Slot' }}
                </span>
                <div class="font-heading text-2xl font-bold text-white tracking-tight">
                    {{ $sellerOrder->delivery_slot ?? 'Today, 4:00 PM – 6:00 PM' }}
                </div>
                <div class="flex items-center gap-2 text-slate-300 text-xs mt-1.5">
                    <span class="material-symbols-outlined text-[18px] {{ in_array($sellerOrder->status, ['delivered', 'fulfilled']) ? 'text-emerald-400' : 'text-amber-400' }}">
                        {{ in_array($sellerOrder->status, ['delivered', 'fulfilled']) ? 'check_circle' : 'sports_motorsports' }}
                    </span>
                    <span class="font-semibold">{{ $sellerOrder->courier_name }}</span>
                    <span>•</span>
                    @if($sellerOrder->status === 'delivered')
                        <span class="font-mono text-emerald-400 font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">done_all</span>
                            Delivered to Customer
                        </span>
                    @elseif($sellerOrder->status === 'fulfilled')
                        <span class="font-mono text-emerald-400 font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">verified</span>
                            Courier Handover Complete
                        </span>
                    @else
                        <span class="font-mono text-emerald-400 font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Courier Slot Confirmed
                        </span>
                    @endif
                </div>
            </div>
            @if(in_array($sellerOrder->status, ['ready_for_pickup', 'packed']))
                <form method="POST" action="{{ route('seller.orders.fulfill', $sellerOrder) }}">
                    @csrf
                    <button type="submit" class="h-11 px-6 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold inline-flex items-center gap-2 shadow-sm hover:shadow-md transition-all active:scale-95">
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
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden p-6 flex flex-col gap-5">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                    <h3 class="font-heading text-lg font-bold text-slate-900">Order Items ({{ $sellerOrder->items->count() }} SKUs)</h3>
                    <span class="font-mono text-xs text-slate-500">Verified Seller Items</span>
                </div>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left font-sans text-xs">
                        <thead>
                            <tr class="bg-slate-50/90 text-slate-500 font-mono text-[10px] uppercase tracking-wider border-b border-slate-200/80">
                                <th class="py-3 px-3 font-bold">Product</th>
                                <th class="py-3 px-3 font-bold">SKU</th>
                                <th class="py-3 px-3 text-right font-bold">Unit Price</th>
                                <th class="py-3 px-3 text-center font-bold">Qty</th>
                                <th class="py-3 px-3 text-right font-bold">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($sellerOrder->items as $item)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center text-slate-400">
                                                @if($item->product_image)
                                                    <img class="w-full h-full object-cover" src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}">
                                                @else
                                                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                                @endif
                                            </div>
                                            <span class="font-bold text-slate-900 text-xs">{{ $item->product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3 font-mono text-slate-500 font-semibold">{{ $item->sku ?? 'N/A' }}</td>
                                    <td class="py-3.5 px-3 text-right font-mono font-semibold text-slate-800">₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-3.5 px-3 text-center font-mono font-bold text-slate-900">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-xs">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right font-mono font-bold text-slate-900 text-sm">₹{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-500">No items recorded in this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Payout Summary Ledger Card -->
                <div class="border-t border-slate-200/80 pt-4 flex flex-col gap-2.5 max-w-sm ml-auto text-xs w-full">
                    <div class="flex justify-between text-slate-600">
                        <span class="font-medium">Subtotal:</span>
                        <span class="font-mono font-bold text-slate-900">₹{{ number_format($grossVal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-rose-700">
                        <span>Platform Commission ({{ (intval($commRateVal) == $commRateVal) ? intval($commRateVal) : number_format($commRateVal, 1) }}%):</span>
                        <span class="font-mono font-semibold">-₹{{ number_format($commVal, 2) }}</span>
                    </div>
                    @if($cessVal > 0)
                        <div class="flex justify-between text-rose-700">
                            <span>APMC Mandi Cess (1.5%):</span>
                            <span class="font-mono font-semibold">-₹{{ number_format($cessVal, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-slate-200/80 pt-2 flex justify-between font-bold text-sm">
                        <span class="text-slate-900">Net Seller Payout:</span>
                        <span class="font-mono text-emerald-700 text-base font-black">₹{{ number_format($netVal, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Customer & Logistics Info -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <!-- Customer Details Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col gap-3.5">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-bold">Customer &amp; Shipping</span>
                    <span class="material-symbols-outlined text-[18px] text-slate-400">person</span>
                </div>
                <div class="font-sans text-base font-extrabold text-slate-900">{{ $customerName }}</div>
                <div class="flex items-center gap-2 font-mono text-xs text-slate-600">
                    <span class="material-symbols-outlined text-[16px] text-slate-400">call</span>
                    <span>{{ $maskedPhone }}</span>
                </div>
                <div class="flex items-start gap-2 font-sans text-xs text-slate-600">
                    <span class="material-symbols-outlined text-[16px] text-slate-400 mt-0.5 shrink-0">location_on</span>
                    <span>{{ $addressLine }}</span>
                </div>
                @if($parentOrder?->notes)
                    <div class="mt-2 pt-2.5 border-t border-slate-200/80 font-sans text-xs text-slate-600">
                        <span class="font-bold text-slate-800">Buyer Notes:</span>
                        <p class="mt-0.5">{{ $parentOrder->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Handover & Logistics Status -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col gap-3.5">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                    <span class="font-mono text-[11px] uppercase tracking-wider text-slate-500 font-bold">Fulfillment Status</span>
                    <span class="material-symbols-outlined text-[18px] text-slate-400">local_shipping</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Handover Status:</span>
                    <span class="font-mono font-bold {{ in_array($sellerOrder->status, ['fulfilled', 'delivered']) ? 'text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded' : 'text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded' }}">
                        {{ in_array($sellerOrder->status, ['fulfilled', 'delivered']) ? 'Handover Confirmed' : 'Pending Custody Transfer' }}
                    </span>
                </div>
                @if($sellerOrder->delivered_at)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-600 font-medium">Delivered At:</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $sellerOrder->delivered_at->format('M j, Y H:i \I\S\T') }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Tracking Number:</span>
                    <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200/80">{{ $sellerOrder->tracking_number ?? 'BZ-TRK-PENDING' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
