@extends('layouts.admin')

@section('title', 'Order Dossier #' . ($order->order_number ?? 'BZ-10482'))

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Breadcrumb & Top Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.orders.index') }}" class="font-body-sm text-xs text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                    <span>Orders</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="font-label-sm text-xs text-slate-600 font-bold">Order #{{ $order->order_number ?? 'BZ-10482' }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="font-headline-lg text-2xl font-bold text-[#0F172A] tracking-tight">Order #{{ $order->order_number ?? 'BZ-10482' }}</h1>
                @if($order->order_status === 'completed')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-label-sm text-xs font-bold border border-emerald-200/60 uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Delivered & Settled
                    </span>
                @elseif($order->order_status === 'processing')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-800 font-label-sm text-xs font-bold border border-blue-200/60 uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                        Processing & Dispatched
                    </span>
                @elseif($order->order_status === 'cancelled')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-800 font-label-sm text-xs font-bold border border-rose-200/60 uppercase">
                        Cancelled
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 font-label-sm text-xs font-bold border border-amber-200/60 uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        In Escrow Hold
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-label-md text-xs">
                <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                <span>Placed on {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'Today' }} IST</span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-600 font-medium">Payment: {{ strtoupper($order->payment_method ?? 'UPI') }} ({{ ucfirst($order->payment_status ?? 'paid') }})</span>
            </div>
        </div>

        <!-- Order Status Updater Form & Manifest Print -->
        <div class="flex flex-wrap items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-[#0F172A] font-body-md text-xs font-medium transition-all shadow-2xs cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Print Packing Slip</span>
            </button>

            <!-- Status Modifier Form -->
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200">
                @csrf
                <span class="text-xs font-bold text-slate-700 pl-2">Status:</span>
                <select name="order_status" class="py-1 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-[#0F172A] focus:outline-none focus:border-[#F5A623] cursor-pointer">
                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-3 py-1 bg-[#0F172A] text-white rounded-lg text-xs font-semibold hover:bg-slate-800 transition cursor-pointer">
                    Save
                </button>
            </form>
        </div>
    </div>

    <!-- Fulfillment Consignments & Customer Dossier -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
        <!-- Multi-Seller Consignment Splits (8 Cols) -->
        <div class="lg:col-span-8 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h3 class="font-headline-sm text-base font-bold text-[#0F172A]">Consignment Splits & Item Ledgers</h3>
                <span class="text-xs font-label-sm text-slate-500">{{ $order->sellerOrders->count() }} Merchant Consignments</span>
            </div>

            @forelse($order->sellerOrders as $so)
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-900 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($so->seller->name ?? 'SL', 0, 2)) }}
                            </div>
                            <div>
                                <span class="font-bold text-xs text-[#0F172A]">{{ $so->seller->sellerProfile->shop_name ?? ($so->seller->name ?? 'Direct Merchant') }}</span>
                                <span class="text-[10px] text-slate-500 block">Sub-Order #{{ $so->seller_order_number ?? ('BZ-' . $order->id . '-S' . $so->id) }} • Commission: {{ $so->commission_rate ?? 8.5 }}%</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full font-label-sm text-[10px] font-bold uppercase {{ $so->status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                            {{ $so->status }}
                        </span>
                    </div>

                    <!-- Items Table -->
                    <div class="p-4 flex flex-col gap-3">
                        @forelse($so->items as $item)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-700 font-bold shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">package_2</span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-[#0F172A] block text-sm">{{ $item->product_name }}</span>
                                        <span class="font-label-sm text-[11px] text-slate-500">
                                            SKU: {{ $item->sku ?? 'N/A' }} • {{ $item->quantity }} unit(s) × ₹{{ number_format($item->unit_price, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <span class="font-label-md font-bold text-sm text-[#0F172A]">
                                    ₹{{ number_format($item->total_price ?: ($item->unit_price * $item->quantity), 2) }}
                                </span>
                            </div>
                        @empty
                            <div class="py-2 text-xs text-slate-400">No individual line items registered.</div>
                        @endforelse
                    </div>

                    <!-- Tracking and Subtotal Footer -->
                    <div class="p-3 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                        <span>Carrier Tracking: <strong class="text-[#0F172A]">{{ $so->tracking_number ?? 'Pending Dispatch Assignment' }}</strong></span>
                        <span>Consignment Payout: <strong class="font-label-md text-emerald-700 font-bold">₹{{ number_format($so->payout_amount ?: ($so->subtotal * 0.915), 2) }}</strong></span>
                    </div>
                </div>
            @empty
                <div class="p-8 rounded-xl bg-white border border-slate-200/90 text-center text-slate-400 text-xs">
                    No multi-seller split consignments found for this order.
                </div>
            @endforelse
        </div>

        <!-- Financial & Buyer Summary (4 Cols) -->
        <div class="lg:col-span-4 flex flex-col gap-4">
            <!-- Customer Dossier Card -->
            <div class="p-5 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col gap-3">
                <span class="font-label-sm text-[10px] uppercase tracking-wider text-slate-400 font-bold">Buyer Dossier</span>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#0F172A] text-[#F5A623] flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($order->delivery_full_name ?? ($order->user->name ?? 'B'), 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-[#0F172A] text-sm">{{ $order->delivery_full_name ?? ($order->user->name ?? 'Guest Buyer') }}</h4>
                        <span class="text-[11px] text-slate-500 font-label-sm">{{ $order->user->email ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2 text-xs">
                    <div>
                        <span class="text-slate-400 text-[11px] block uppercase font-label-sm">Delivery Address</span>
                        <p class="font-medium text-[#0F172A] mt-0.5 leading-snug">
                            {{ $order->delivery_address_line_1 }}
                            @if($order->delivery_address_line_2), {{ $order->delivery_address_line_2 }} @endif
                            <br/>
                            {{ $order->delivery_city }}, {{ $order->delivery_state }} - {{ $order->delivery_postal_code }}
                        </p>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span class="text-slate-500">Phone:</span>
                        <span class="font-label-sm font-bold text-[#0F172A]">{{ $order->delivery_phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Order Channel:</span>
                        <span class="font-semibold text-slate-800 uppercase text-[11px]">{{ $order->order_type ?? 'Cart' }}</span>
                    </div>
                </div>
            </div>

            <!-- Escrow Ledger Settlement -->
            <div class="p-5 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col gap-3">
                <span class="font-label-sm text-[10px] uppercase tracking-wider text-slate-400 font-bold">Order Financial Ledger</span>
                <div class="flex flex-col gap-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Items Subtotal:</span>
                        <span class="font-label-md font-bold text-[#0F172A]">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Shipping / Logistics:</span>
                        <span class="font-label-md text-slate-700">₹{{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Voucher Discount:</span>
                            <span class="font-label-md text-emerald-700 font-bold">- ₹{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="pt-2 border-t border-slate-200 flex justify-between text-sm">
                        <span class="font-bold text-[#0F172A]">Total Settlement:</span>
                        <span class="font-label-md font-bold text-[#0F172A] text-base">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
                <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-medium flex items-center gap-1.5 mt-1">
                    <span class="material-symbols-outlined text-[16px]">lock</span>
                    <span>Protected by Bazaario Multi-Vendor Escrow</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
