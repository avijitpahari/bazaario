@extends('layouts.app')

@section('title', 'Order Confirmed #BZR-98241 — Bazaario Multi-Vendor Marketplace')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Success Hero -->
    <div class="text-center space-y-4">
        <div class="w-20 h-20 rounded-3xl bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-3xl shadow-lg ring-8 ring-emerald-50">
            <i class="fa-solid fa-check"></i>
        </div>
        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">
            Order Placed Successfully
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold font-heading text-slate-900">Thank you for your order!</h1>
        <p class="text-sm text-slate-500 max-w-md mx-auto">
            Your order <strong>#BZR-98241</strong> has been confirmed and routed to the individual seller workshops.
        </p>
    </div>

    <!-- Quick Action CTAs -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-8">
        <a href="{{ route('account.orders') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/20 transition">
            <i class="fa-solid fa-truck-fast"></i> Track Your Order Shipments
        </a>
        <a href="{{ route('products.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs border border-slate-200 shadow-sm transition">
            <i class="fa-solid fa-compass"></i> Continue Shopping
        </a>
    </div>

    <!-- Order Detail Recap (Multi-Vendor Packages) -->
    <div class="mt-12 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-8">
        
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <p class="text-xs text-slate-400">Order Reference</p>
                <p class="text-base font-bold font-heading text-slate-900">#BZR-98241</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Date Placed</p>
                <p class="text-xs font-bold text-slate-900">{{ date('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Payment Method</p>
                <p class="text-xs font-bold text-emerald-600">Cash on Delivery (COD)</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Total Amount</p>
                <p class="text-base font-extrabold font-heading text-indigo-600">$374.40</p>
            </div>
        </div>

        <!-- Package 1 from Nordic Leather Atelier -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span class="text-xs font-bold text-slate-900">Package 1 of 2: Nordic Leather Atelier</span>
                    <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Processing in Workshop</span>
                </div>
                <span class="text-xs text-slate-500">Est. Dispatch: Within 24 hours</span>
            </div>

            <div class="divide-y divide-slate-200/60 text-xs">
                <div class="py-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=100&q=80" class="w-12 h-12 rounded-xl object-cover">
                        <div>
                            <span class="font-bold text-slate-900 block">Full-Grain Leather Messenger Briefcase</span>
                            <span class="text-slate-500">Qty: 1</span>
                        </div>
                    </div>
                    <span class="font-bold font-heading text-slate-900">$149.00</span>
                </div>

                <div class="py-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=100&q=80" class="w-12 h-12 rounded-xl object-cover">
                        <div>
                            <span class="font-bold text-slate-900 block">Solid Brass Leather Key Hook</span>
                            <span class="text-slate-500">Qty: 2</span>
                        </div>
                    </div>
                    <span class="font-bold font-heading text-slate-900">$48.00</span>
                </div>
            </div>
        </div>

        <!-- Package 2 from AudioCraft Studios -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <span class="text-xs font-bold text-slate-900">Package 2 of 2: AudioCraft Studios</span>
                    <span class="text-[10px] font-bold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-full">Dispatched via DHL</span>
                </div>
                <span class="text-xs text-slate-500">Tracking: #DHL-9948271</span>
            </div>

            <div class="text-xs">
                <div class="py-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=100&q=80" class="w-12 h-12 rounded-xl object-cover">
                        <div>
                            <span class="font-bold text-slate-900 block">Aura Hi-Fi Studio Wireless Headphones</span>
                            <span class="text-slate-500">Qty: 1</span>
                        </div>
                    </div>
                    <span class="font-bold font-heading text-slate-900">$219.00</span>
                </div>
            </div>
        </div>

        <!-- Delivery Address Recap -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 flex items-start gap-3 text-xs text-slate-600">
            <i class="fa-solid fa-location-dot text-indigo-600 mt-1"></i>
            <div>
                <strong class="text-slate-900">Shipping To:</strong> Sarah Miller • 742 Evergreen Terrace, Apt 4B, Springfield, OR 97477 • Phone: +1 (555) 234-5678
            </div>
        </div>

    </div>

</div>
@endsection
