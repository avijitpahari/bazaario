@extends('layouts.admin')

@section('title', 'Orders Directory & Logistics Hub')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-space-sm">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Marketplace Orders</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-900 font-label-sm text-[11px] font-bold border border-blue-200/60">Logistics & Escrow</span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500 mt-1">
                Real-time tracking of multi-vendor split orders, dispatch SLAs, and automated escrow releases across Bengal delivery corridors.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-[#0F172A] font-body-md text-xs font-semibold shadow-2xs hover:bg-slate-50 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Export Manifest</span>
            </button>
        </div>
    </div>

    <!-- Metrics Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Orders</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-[#0F172A]">{{ number_format($stats['total'] ?? 9) }}</span>
                <span class="text-xs text-emerald-700 font-bold">Live DB</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1">₹{{ $stats['total_volume'] ?? '1,20,500' }} Total Volume</span>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">In Processing / Transit</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-blue-700">{{ $stats['processing'] ?? 4 }}</span>
                <span class="text-xs text-blue-700 font-bold">Active SLA</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1">Dispatched to Corridors</span>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Delivered & Completed</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-emerald-700">{{ $stats['completed'] ?? 4 }}</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-bold">Escrow Settled</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1">Verified OTP Delivery</span>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Cancelled Orders</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-rose-700">{{ $stats['cancelled'] ?? 1 }}</span>
                <span class="text-xs text-slate-500 font-bold">Refunded</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1">Customer Cancellations</span>
        </div>
    </div>

    <!-- Filter & Search Console -->
    <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400 pointer-events-none">search</span>
                <input 
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-[#F5A623] transition-all font-body-md" 
                    placeholder="Search by Order ID (#BZ-10482), Customer Name, Phone, or City..." type="text"/>
            </div>
            <div class="flex items-center flex-wrap gap-2 text-xs">
                <select name="status" class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium focus:outline-none focus:border-[#F5A623] cursor-pointer">
                    <option value="">All Fulfillment Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending / Placed</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing & Shipped</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Delivered & Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <select name="payment_status" class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium focus:outline-none focus:border-[#F5A623] cursor-pointer">
                    <option value="">All Payments</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Payment Pending</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-[#0F172A] text-white hover:bg-slate-800 rounded-xl font-semibold flex items-center gap-1.5 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Apply</span>
                </button>

                @if(request()->anyFilled(['search', 'status', 'payment_status']))
                    <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition" title="Reset Filters">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="rounded-xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#FFFDF8] text-slate-500 font-label-sm text-[11px] uppercase tracking-wider border-b border-slate-100">
                        <th class="py-3 px-4">Order Ref</th>
                        <th class="py-3 px-4">Customer Name</th>
                        <th class="py-3 px-4">Delivery Destination</th>
                        <th class="py-3 px-4">Seller Consignments</th>
                        <th class="py-3 px-4 text-right">Settled Amount</th>
                        <th class="py-3 px-4">Payment</th>
                        <th class="py-3 px-4 text-center">Fulfillment Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-body-sm text-[#0F172A]">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Order Ref -->
                            <td class="py-3.5 px-4 font-label-md font-bold text-[#0F172A] whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-[#0F172A] hover:text-amber-600 hover:underline">
                                    #{{ $order->order_number }}
                                </a>
                                <span class="text-[10px] text-slate-400 block font-normal">{{ $order->created_at ? $order->created_at->format('d M, h:i A') : 'Today' }}</span>
                            </td>

                            <!-- Customer -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-[#0F172A]">{{ $order->delivery_full_name ?? ($order->user->name ?? 'Guest Buyer') }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $order->delivery_phone }}</span>
                            </td>

                            <!-- Destination -->
                            <td class="py-3.5 px-4 text-slate-600">
                                <span class="font-medium text-[#0F172A]">{{ $order->delivery_city ?? 'Kolkata' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $order->delivery_state ?? 'West Bengal' }}</span>
                            </td>

                            <!-- Seller Split -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[#0F172A] font-label-sm text-[10px] font-semibold">
                                        {{ $order->sellerOrders->count() ?: 1 }} {{ Str::plural('Seller', $order->sellerOrders->count() ?: 1) }}
                                    </span>
                                    <span class="text-slate-500 text-[11px] truncate max-w-[180px]">
                                        {{ $order->sellerOrders->pluck('seller.name')->filter()->implode(', ') ?: 'Bazaario Direct' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="py-3.5 px-4 text-right font-label-md font-bold text-[#0F172A] text-sm whitespace-nowrap">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </td>

                            <!-- Payment Method -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-label-sm text-slate-600 flex items-center gap-1 uppercase">
                                    <span class="material-symbols-outlined text-[15px] text-emerald-600">credit_card</span>
                                    {{ str_replace('_', ' ', $order->payment_method ?? 'card') }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($order->order_status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-label-sm text-[10px] font-bold border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Completed
                                    </span>
                                @elseif($order->order_status === 'processing')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-label-sm text-[10px] font-bold border border-blue-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Processing
                                    </span>
                                @elseif($order->order_status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-label-sm text-[10px] font-bold border border-rose-200/60">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-label-sm text-[10px] font-bold border border-amber-200/60">
                                        In Escrow
                                    </span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-semibold transition-colors">
                                    Dossier &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300">receipt_long</span>
                                <p class="mt-2 font-headline-sm text-sm font-semibold text-slate-600">No orders matching the selected criteria</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs text-slate-500 font-label-sm">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
