@extends('layouts.admin')

@section('title', 'Product Inspection')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Catalog Product Inspection</h1>
            <p class="text-xs text-slate-500 mt-1">Audit merchant listing specifications, pricing, stock levels, and compliance status.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Products Catalog
        </a>
    </div>

    @if(isset($product))
    <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-base text-slate-900">{{ $product->name }}</h3>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-slate-500 font-mono">SKU: {{ $product->sku ?? 'N/A' }}</span>
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200/80 text-[11px] font-semibold">
                        <span class="material-symbols-outlined text-[13px] text-amber-700">category</span>
                        {{ $product->category?->name ?? 'Uncategorized' }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $product->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ ucfirst($product->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[11px] text-slate-400 font-semibold block">Price (INR)</span>
                <span class="text-lg font-bold font-mono text-slate-900">₹{{ number_format($product->price, 2) }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[11px] text-slate-400 font-semibold block">Inventory Stock</span>
                <span class="text-lg font-bold font-mono text-slate-900">{{ $product->stock }} {{ $product->unit_type ?? 'units' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[11px] text-slate-400 font-semibold block">Merchant Shop</span>
                <span class="text-sm font-bold text-slate-900">{{ $product->seller?->sellerProfile?->shop_name ?? 'Bazaario Central' }}</span>
            </div>
        </div>

        <div>
            <h4 class="text-xs font-bold text-slate-700 mb-1">Description</h4>
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-600 leading-relaxed">
                {{ $product->description ?: 'No description provided.' }}
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <form method="POST" action="{{ route('admin.products.toggle-status', $product->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold {{ $product->status === 'active' ? 'bg-amber-100 text-amber-900 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-900 hover:bg-emerald-200' }} transition">
                    {{ $product->status === 'active' ? 'Suspend / Deactivate Product' : 'Approve & Activate Product' }}
                </button>
            </form>

            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">Back to List</a>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-xs text-slate-500">
        <p>Product record not found.</p>
        <a href="{{ route('admin.products.index') }}" class="mt-3 inline-block px-4 py-2 bg-amber-500 text-slate-950 rounded-lg font-bold">Return to Catalog</a>
    </div>
    @endif
</div>
@endsection
