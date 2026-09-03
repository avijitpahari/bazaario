@extends('layouts.admin')

@section('title', 'Category Taxonomies & Rates — Bazaario HQ')
@section('page-title', 'Marketplace Category Governance')

@section('content')
<div class="space-y-6" x-data="{
    addModal: false,
    editModal: false,
    selectedCat: null,
    newCatName: '',
    newCatSlug: '',
    newCatParent: 'none',
    newCatFee: 8.5,
    newCatIcon: 'fa-tag',
    toastMsg: '',
    showToast: false,
    categories: [
        { id: 1, name: 'Artisan & Crafts', slug: 'artisan-crafts', parent: 'Root', products: 420, fee: 8.5, icon: 'fa-paintbrush', order: 1, status: 'active' },
        { id: 2, name: '— Leather Goods & Bags', slug: 'leather-goods', parent: 'Artisan & Crafts', products: 180, fee: 8.5, icon: 'fa-bag-shopping', order: 2, status: 'active' },
        { id: 3, name: '— Woodwork & Carving', slug: 'woodwork', parent: 'Artisan & Crafts', products: 120, fee: 8.5, icon: 'fa-tree', order: 3, status: 'active' },
        { id: 4, name: 'Electronics & Audio', slug: 'electronics', parent: 'Root', products: 310, fee: 6.0, icon: 'fa-headphones', order: 4, status: 'active' },
        { id: 5, name: '— Audiophile Headphones', slug: 'headphones', parent: 'Electronics & Audio', products: 85, fee: 6.0, icon: 'fa-music', order: 5, status: 'active' },
        { id: 6, name: 'Fashion & Wear', slug: 'apparel', parent: 'Root', products: 390, fee: 9.0, icon: 'fa-shirt', order: 6, status: 'active' },
        { id: 7, name: 'Home & Living', slug: 'home-living', parent: 'Root', products: 260, fee: 8.5, icon: 'fa-couch', order: 7, status: 'active' },
        { id: 8, name: 'Organic & Gourmet', slug: 'organic-gourmet', parent: 'Root', products: 100, fee: 7.5, icon: 'fa-lemon', order: 8, status: 'active' }
    ],
    addCategory() {
        if (!this.newCatName) return;
        this.categories.push({
            id: Date.now(),
            name: this.newCatName,
            slug: this.newCatSlug || this.newCatName.toLowerCase().replace(/[^a-z0-9]/g, '-'),
            parent: this.newCatParent === 'none' ? 'Root' : this.newCatParent,
            products: 0,
            fee: parseFloat(this.newCatFee),
            icon: this.newCatIcon,
            order: this.categories.length + 1,
            status: 'active'
        });
        this.toastMsg = 'Category ' + this.newCatName + ' added successfully!';
        this.showToast = true;
        setTimeout(() => this.showToast = false, 3500);
        this.newCatName = '';
        this.newCatSlug = '';
        this.addModal = false;
    }
}">

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
        <span x-text="toastMsg"></span>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold font-heading text-white">Marketplace Taxonomy Tree</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-indigo-500/20 text-indigo-300 border border-indigo-500/30" x-text="categories.length + ' Categories'"></span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Configure parent-child category hierarchies and custom commission fee overrides per category.</p>
        </div>
        <button @click="addModal = true" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition">
            <i class="fa-solid fa-plus"></i> Add New Category
        </button>
    </div>

    <!-- Category Taxonomy Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Category Name & Icon</th>
                        <th class="py-3.5 px-4">URL Routing Slug</th>
                        <th class="py-3.5 px-4">Taxonomy Hierarchy</th>
                        <th class="py-3.5 px-4">Category Fee Rate</th>
                        <th class="py-3.5 px-4">Active Listings</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 font-medium">
                    <template x-for="cat in categories" :key="cat.id">
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-800 text-indigo-400 flex items-center justify-center text-xs">
                                        <i class="fa-solid" :class="cat.icon"></i>
                                    </div>
                                    <span class="font-bold text-white text-sm" :class="cat.parent !== 'Root' ? 'pl-4 text-slate-300' : ''" x-text="cat.name"></span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-indigo-400" x-text="'/category/' + cat.slug"></td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold" :class="cat.parent === 'Root' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700'" x-text="cat.parent"></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-white bg-slate-950 px-2 py-0.5 rounded border border-slate-800" x-text="cat.fee + '%'"></span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-200" x-text="cat.products + ' products'"></td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="'/category/' + cat.slug" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800" title="View Catalog">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                    </a>
                                    <button @click="categories = categories.filter(c => c.id !== cat.id)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800" title="Delete Category">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="addModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5 text-xs text-slate-300">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold font-heading text-white">Create New Category</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>

            <div class="space-y-3">
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-200">Category Name</label>
                    <input type="text" x-model="newCatName" placeholder="e.g. Handmade Jewelry" class="w-full px-3 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white focus:outline-none focus:border-indigo-500 font-semibold">
                </div>

                <div class="space-y-1.5">
                    <label class="font-bold text-slate-200">URL Slug</label>
                    <input type="text" x-model="newCatSlug" placeholder="e.g. handmade-jewelry" class="w-full px-3 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white focus:outline-none focus:border-indigo-500 font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-200">Parent Taxonomy</label>
                        <select x-model="newCatParent" class="w-full px-3 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white focus:outline-none focus:border-indigo-500">
                            <option value="none">Root Category</option>
                            <option value="Artisan & Crafts">Artisan & Crafts</option>
                            <option value="Electronics & Audio">Electronics & Audio</option>
                            <option value="Fashion & Wear">Fashion & Wear</option>
                            <option value="Home & Living">Home & Living</option>
                            <option value="Organic & Gourmet">Organic & Gourmet</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="font-bold text-slate-200">Commission Rate (%)</label>
                        <input type="number" step="0.1" x-model="newCatFee" class="w-full px-3 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white font-bold font-mono">
                    </div>
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-800">
                <button @click="addModal = false" class="px-4 py-2 rounded-xl text-slate-400 hover:bg-slate-800">Cancel</button>
                <button @click="addCategory()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow">Save Category</button>
            </div>
        </div>
    </div>

</div>
@endsection
