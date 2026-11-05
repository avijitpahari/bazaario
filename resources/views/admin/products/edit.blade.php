@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Edit Product Stock & Pricing</h1>
            <p class="text-xs text-slate-500 mt-1">Adjust administrative stock levels and moderation attributes.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Products
        </a>
    </div>

    @if(isset($product))
    <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5 shadow-2xs">
        <form method="POST" action="{{ route('admin.products.update-stock', $product->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Product Title</label>
                <input type="text" value="{{ $product->name }}" disabled class="w-full px-3 py-2 text-xs border border-slate-200 bg-slate-50 text-slate-500 rounded-lg">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adjust Inventory Stock</label>
                    <input type="number" name="stock" value="{{ $product->stock }}" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Price (₹ INR)</label>
                    <input type="number" step="0.01" value="{{ $product->price }}" disabled class="w-full px-3 py-2 text-xs border border-slate-200 bg-slate-50 text-slate-500 rounded-lg">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs">
                    Update Stock
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-xs text-slate-500">
        <p>Product record not found.</p>
        <a href="{{ route('admin.products.index') }}" class="mt-3 inline-block px-4 py-2 bg-amber-500 text-slate-950 rounded-lg font-bold">Return to Catalog</a>
    </div>
    @endif
</div>
@endsection
