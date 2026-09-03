@extends('layouts.seller')

@section('title', 'Manage Products — Stall Merchant Catalog | Bazaario')
@section('page-title', 'My Stall Catalog & Products')

@section('content')
<div class="space-y-6" x-data="{
    tab: 'all',
    search: '',
    quickEditModal: false,
    selectedProduct: null,
    products: [
        { id: 1, name: 'Full-Grain Leather Messenger Briefcase', sku: 'NW-BAG-01', category: 'Artisan & Crafts', price: 149.00, stock: 3, status: 'active', sales: 128, img: 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=200&q=80' },
        { id: 2, name: 'Solid Brass Heavy-Duty Leather Key Hook', sku: 'NW-KEY-08', category: 'Accessories', price: 24.00, stock: 18, status: 'active', sales: 86, img: 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=200&q=80' },
        { id: 3, name: 'Slim Bifold Vegetable-Tanned Card Wallet', sku: 'NW-WAL-04', category: 'Artisan & Crafts', price: 45.00, stock: 12, status: 'active', sales: 48, img: 'https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&w=200&q=80' },
        { id: 4, name: 'Executive Leather Desk Pad Blotter', sku: 'NW-DSK-02', category: 'Home & Office', price: 79.00, stock: 2, status: 'active', sales: 32, img: 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=200&q=80' },
        { id: 5, name: 'Hand-burnished Minimalist Leather Coasters (Set of 4)', sku: 'NW-CST-01', category: 'Home & Living', price: 32.00, stock: 0, status: 'out_of_stock', sales: 19, img: 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=200&q=80' }
    ]
}">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold font-heading text-slate-900">Listed Products (<span x-text="products.length"></span>)</h2>
            <p class="text-xs text-slate-500">Manage pricing, inventory alerts, and publishing status.</p>
        </div>
        <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition">
            <i class="fa-solid fa-plus"></i> Add New Product
        </a>
    </div>

    <!-- Filter & Search Controls -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <button @click="tab = 'all'" :class="tab === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">All (5)</button>
            <button @click="tab = 'active'" :class="tab === 'active' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Active (4)</button>
            <button @click="tab = 'low_stock'" :class="tab === 'low_stock' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Low Stock (2)</button>
            <button @click="tab = 'out_of_stock'" :class="tab === 'out_of_stock' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Out of Stock (1)</button>
        </div>

        <div class="relative w-full sm:w-64">
            <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3 top-3 text-xs"></i>
            <input type="text" x-model="search" placeholder="Search by name or SKU..." class="w-full py-2 pl-8 pr-3 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
        </div>
    </div>

    <!-- Product Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Product</th>
                        <th class="py-3.5 px-4">Category & SKU</th>
                        <th class="py-3.5 px-4">Price</th>
                        <th class="py-3.5 px-4">Stock Level</th>
                        <th class="py-3.5 px-4">Sales</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <template x-for="p in products" :key="p.id">
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img :src="p.img" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                                    <div>
                                        <a :href="'/product/' + p.id" target="_blank" class="font-bold text-slate-900 hover:text-indigo-600 block line-clamp-1" x-text="p.name"></a>
                                        <span class="text-[10px] text-slate-400">ID: #PRD-00<span x-text="p.id"></span></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-800 font-semibold block" x-text="p.category"></span>
                                <span class="text-[10px] text-slate-400 font-mono" x-text="p.sku"></span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 font-heading text-sm">
                                $<span x-text="p.price.toFixed(2)"></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold" :class="p.stock <= 3 ? 'text-amber-600' : 'text-slate-800'" x-text="p.stock + ' units'"></span>
                                    <span x-show="p.stock <= 3 && p.stock > 0" class="text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.2 rounded">Low</span>
                                    <span x-show="p.stock === 0" class="text-[10px] font-bold text-rose-700 bg-rose-100 px-1.5 py-0.2 rounded">Out</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-semibold">
                                <span x-text="p.sales"></span> sold
                            </td>
                            <td class="py-3.5 px-4">
                                <span x-show="p.status === 'active'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                <span x-show="p.status === 'out_of_stock'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Sold Out</span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('seller.products.create') }}" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50" title="Edit Product">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button @click="p.stock += 5" class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50" title="Quick Restock +5">
                                        <i class="fa-solid fa-plus-circle"></i>
                                    </button>
                                    <button @click="products = products.filter(item => item.id !== p.id)" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50" title="Delete">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
