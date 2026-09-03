@extends('layouts.seller')

@section('title', 'Add / Edit Product — Stall Merchant | Bazaario')
@section('page-title', 'Create New Stall Listing')

@section('content')
<form action="{{ route('seller.products.index') }}" method="GET" class="space-y-8 pb-20" x-data="{
    price: 149.00,
    stock: 10,
    isPublished: true
}">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('seller.products.index') }}" class="hover:text-indigo-600">Products</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-900 font-bold">New Product Listing</span>
        </div>
        <a href="{{ route('seller.products.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
            Cancel & Return
        </a>
    </div>

    <!-- 1. Image Upload Block (Stacked Block 1) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <h3 class="text-base font-bold font-heading text-slate-900">Product Media & Gallery</h3>
            </div>
            <span class="text-xs text-slate-400">Up to 6 high-res photos</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Main Photo -->
            <div class="relative h-44 rounded-2xl border-2 border-indigo-500 overflow-hidden group">
                <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=400&q=80" class="w-full h-full object-cover">
                <span class="absolute top-2 left-2 bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">Primary Cover</span>
                <button type="button" class="absolute top-2 right-2 w-7 h-7 rounded-lg bg-black/60 text-white hover:bg-rose-600 text-xs flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Upload Slots -->
            <div class="h-44 rounded-2xl border-2 border-dashed border-slate-200 hover:border-indigo-400 bg-slate-50 hover:bg-indigo-50/30 flex flex-col items-center justify-center text-center p-4 cursor-pointer transition">
                <i class="fa-solid fa-cloud-arrow-up text-slate-400 text-2xl mb-1"></i>
                <span class="text-xs font-bold text-slate-700">Add Photo</span>
                <span class="text-[10px] text-slate-400">PNG, JPG up to 5MB</span>
            </div>

            <div class="h-44 rounded-2xl border-2 border-dashed border-slate-200 hover:border-indigo-400 bg-slate-50 hover:bg-indigo-50/30 flex flex-col items-center justify-center text-center p-4 cursor-pointer transition">
                <i class="fa-solid fa-cloud-arrow-up text-slate-400 text-2xl mb-1"></i>
                <span class="text-xs font-bold text-slate-700">Add Photo</span>
                <span class="text-[10px] text-slate-400">Angle / Detail shot</span>
            </div>

            <div class="h-44 rounded-2xl border-2 border-dashed border-slate-200 hover:border-indigo-400 bg-slate-50 hover:bg-indigo-50/30 flex flex-col items-center justify-center text-center p-4 cursor-pointer transition">
                <i class="fa-solid fa-cloud-arrow-up text-slate-400 text-2xl mb-1"></i>
                <span class="text-xs font-bold text-slate-700">Add Photo</span>
                <span class="text-[10px] text-slate-400">Scale / In-use shot</span>
            </div>
        </div>
    </div>

    <!-- 2. Basic Details Block (Stacked Block 2) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-tag"></i>
            </div>
            <h3 class="text-base font-bold font-heading text-slate-900">Basic Details & Description</h3>
        </div>

        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Product Title</label>
                <input type="text" value="Full-Grain Leather Messenger Briefcase" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Marketplace Category</label>
                    <select class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
                        <option selected>Artisan & Crafts</option>
                        <option>Electronics & Audio</option>
                        <option>Fashion & Apparel</option>
                        <option>Home & Living</option>
                        <option>Organic & Gourmet</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">SKU / Internal Identifier</label>
                    <input type="text" value="NW-BAG-01" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 font-mono">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Detailed Description & Specifications</label>
                <textarea rows="5" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 leading-relaxed">Handcrafted from vegetable-tanned Scandinavian bovine leather. Padded laptop compartment, solid antique brass hardware, reinforced wax-stitched seams.</textarea>
            </div>
        </div>
    </div>

    <!-- 3. Pricing & Stock Block (Stacked Block 3) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <h3 class="text-base font-bold font-heading text-slate-900">Pricing & Inventory Management</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Selling Price ($)</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">$</span>
                    <input type="number" step="0.01" x-model="price" class="w-full pl-8 pr-4 py-2.5 text-xs font-bold text-slate-900 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
                </div>
                <span class="text-[10px] text-slate-400 block">Stall receives: $<span x-text="(price * 0.915).toFixed(2)"></span> (net of 8.5% fee)</span>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Available Stock Quantity</label>
                <input type="number" x-model="stock" class="w-full px-4 py-2.5 text-xs font-bold text-slate-900 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Low Stock Alert Threshold</label>
                <input type="number" value="3" class="w-full px-4 py-2.5 text-xs text-slate-900 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
                <span class="text-[10px] text-amber-600 block font-semibold">Triggers dashboard warning banner</span>
            </div>
        </div>
    </div>

    <!-- 4. Shipping Details Block (Stacked Block 4) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <h3 class="text-base font-bold font-heading text-slate-900">Fulfillment & Shipping Profile</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Package Weight (kg / lbs)</label>
                <input type="text" value="1.4 kg" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Parcel Dimensions (L x W x H)</label>
                <input type="text" value="16 x 12 x 5 in" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Processing & Dispatch Time</label>
                <select class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
                    <option selected>24 to 48 hours (Standard)</option>
                    <option>Same-day dispatch</option>
                    <option>3 to 5 business days (Custom / Made to order)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Sticky Bottom Action Bar (Section 3.4) -->
    <div class="fixed bottom-0 left-0 lg:left-72 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200 p-4 px-6 sm:px-8 z-30 flex items-center justify-between shadow-2xl">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span class="text-xs font-bold text-slate-800">Ready to publish to Bazaario catalog</span>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-bold transition">
                Save as Draft
            </button>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition flex items-center gap-1.5">
                <i class="fa-solid fa-cloud-arrow-up"></i> Publish Listing Now
            </button>
        </div>
    </div>

</form>
@endsection
