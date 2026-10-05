@extends('layouts.admin')

@section('title', 'Category Taxonomy & Commission Rates')

@section('content')
<div class="flex flex-col w-full gap-space-lg" x-data="{ showCreateModal: false }">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col">
            <div class="flex items-center gap-space-sm">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Category Taxonomy</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 font-mono text-[11px] font-bold border border-emerald-200/60">Taxonomy & Tiers</span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500 mt-1">
                Configure platform catalog classification, merchant commission schedules, GST brackets, and auction eligibility.
            </p>
        </div>
        <!-- Top Action Buttons -->
        <div class="flex items-center flex-wrap gap-2">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 text-[#0F172A] font-body-md text-xs font-semibold shadow-2xs hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                <span>View Products Directory</span>
            </a>
            <button type="button" onclick="document.getElementById('createCategoryDrawer').classList.toggle('hidden')" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-[#F5A623] text-[#0F172A] font-body-md text-xs font-bold shadow-2xs hover:bg-amber-400 transition-all">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Create New Category</span>
            </button>
        </div>
    </div>

    <!-- Collapsible Create Category Drawer -->
    <div id="createCategoryDrawer" class="hidden bg-white rounded-xl p-5 border border-amber-200/80 shadow-sm transition-all">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600 text-xl">folder_managed</span>
                <h3 class="font-bold text-sm text-[#0F172A]">Add New Taxonomy Node</h3>
            </div>
            <button type="button" onclick="document.getElementById('createCategoryDrawer').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. Traditional Silk & Textiles" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-body-sm" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Description / Marketplace Scope</label>
                <input type="text" name="description" placeholder="Brief scope and regional consignments covered..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-body-sm" />
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                    Deploy Taxonomy Node
                </button>
                <button type="button" onclick="document.getElementById('createCategoryDrawer').classList.add('hidden')" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- Metrics Bar: 4 Primary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <!-- Card 1 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-mono text-[10px] uppercase tracking-wider text-slate-500 font-bold">Active Categories</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">category</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">{{ count($categories) }}</span>
                <span class="font-mono text-xs text-emerald-700 font-bold flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[14px]">check</span> Fully Indexed
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Direct & Auction Mapped</span>
                <span class="font-semibold text-[#0F172A]">100% active</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-mono text-[10px] uppercase tracking-wider text-slate-500 font-bold">Cataloged Items</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">inventory</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">{{ number_format($totalProducts ?? 0) }}</span>
                <span class="font-mono text-xs text-slate-500 font-medium">SKUs active</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Avg Density / Category</span>
                <span class="font-semibold text-emerald-600">~{{ count($categories) > 0 ? round(($totalProducts ?? 0) / count($categories)) : 0 }} items</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-mono text-[10px] uppercase tracking-wider text-slate-500 font-bold">Average Take-Rate</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">percent</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">7.5%</span>
                <span class="font-mono text-xs text-emerald-700 font-medium">+0.4% QoQ</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Tier Range</span>
                <span class="font-semibold text-[#0F172A]">5.0% - 12.5%</span>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-mono text-[10px] uppercase tracking-wider text-slate-500 font-bold">TDS / TCS Compliance</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">1.0%</span>
                <span class="font-mono text-xs text-emerald-600 font-medium">Sec 194-O</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>GST E-Commerce Operator</span>
                <span class="font-semibold text-[#0F172A]">Standardized</span>
            </div>
        </div>
    </div>

    <!-- Category Taxonomy Table -->
    <div class="rounded-xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="font-headline-sm text-sm font-bold text-[#0F172A]">Marketplace Taxonomy Matrix</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-mono text-[11px] font-semibold">
                    {{ count($categories) }} Categories Defined
                </span>
            </div>
            <div class="text-xs text-slate-500 font-mono">
                Real-time Commission & Escrow Settlement Parameters
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#0F172A]">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-mono">
                    <tr>
                        <th class="py-3 px-4">Taxonomy Node</th>
                        <th class="py-3 px-4">Slug Identifier</th>
                        <th class="py-3 px-4 text-center">Listed SKUs</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Category Name & Description -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-200/60 flex items-center justify-center text-indigo-700 font-bold shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">folder</span>
                                    </div>
                                    <div class="flex flex-col min-w-0 max-w-sm">
                                        <span class="font-semibold text-[#0F172A] text-sm">
                                            {{ $category->name }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                                            {{ $category->description ?? 'Standard marketplace primary vertical with multi-seller support.' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Slug -->
                            <td class="py-3.5 px-4 font-mono text-slate-500">
                                <code class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] border border-slate-200/60">
                                    /{{ $category->slug }}
                                </code>
                            </td>

                            <!-- Listed SKUs -->
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 hover:bg-amber-100 text-slate-800 hover:text-amber-900 font-mono text-[11px] font-bold transition-colors">
                                    <span>{{ $category->products_count ?? 0 }} SKUs</span>
                                    <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                                </a>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-[#0F172A] hover:bg-slate-100 transition-colors" title="View Products in Category">
                                        <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                    </a>
                                    <button type="button" 
                                            onclick="openEditCategoryModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->slug) }}', '{{ addslashes($category->description ?? '') }}', {{ ($category->is_active ?? true) ? 'true' : 'false' }})" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer" 
                                            title="Edit Category">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    @if(($category->products_count ?? 0) === 0 && ($category->children_count ?? 0) === 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('Delete category {{ addslashes($category->name) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors cursor-pointer" title="Delete Empty Category">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="p-1.5 rounded-lg text-slate-300 cursor-not-allowed" title="Cannot delete: {{ ($category->products_count ?? 0) > 0 ? 'has ' . $category->products_count . ' active products' : 'has ' . $category->children_count . ' active sub-categories' }}">
                                            <span class="material-symbols-outlined text-[18px]">lock</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300">category</span>
                                <p class="mt-2 font-headline-sm text-sm font-semibold text-slate-600">No categories found in system</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Interactive Category Edit Modal -->
    <div id="editCategoryModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-fadeIn">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">edit</span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-sm font-bold text-[#0F172A]">Edit Taxonomy Node</h3>
                        <p class="text-[11px] text-slate-500 font-body-sm">Update category nomenclature, slug, description, and status</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditCategoryModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
            <form id="editCategoryForm" method="POST" action="" class="p-5 flex flex-col gap-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="edit_cat_name" class="block font-headline-sm text-xs font-bold text-slate-700 mb-1">Category Name *</label>
                    <input type="text" id="edit_cat_name" name="name" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 focus:bg-white font-body-sm transition-all" />
                </div>

                <div>
                    <label for="edit_cat_slug" class="block font-headline-sm text-xs font-bold text-slate-700 mb-1">Slug Identifier</label>
                    <input type="text" id="edit_cat_slug" name="slug" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-amber-500 focus:bg-white font-mono transition-all" placeholder="auto-generated-if-empty" />
                </div>

                <div>
                    <label for="edit_cat_description" class="block font-headline-sm text-xs font-bold text-slate-700 mb-1">Description / Marketplace Scope</label>
                    <textarea id="edit_cat_description" name="description" rows="3" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 focus:bg-white font-body-sm transition-all resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="edit_cat_is_active" name="is_active" value="1" class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500 cursor-pointer" />
                    <label for="edit_cat_is_active" class="font-body-sm text-xs font-semibold text-slate-700 cursor-pointer">
                        Active Category (Visible across catalog and vendor listing interfaces)
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer flex items-center gap-1.5 shadow-2xs">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditCategoryModal(id, name, slug, description, isActive) {
            const form = document.getElementById('editCategoryForm');
            form.action = '{{ url("admin/categories") }}/' + id;
            document.getElementById('edit_cat_name').value = name;
            document.getElementById('edit_cat_slug').value = slug;
            document.getElementById('edit_cat_description').value = description;
            document.getElementById('edit_cat_is_active').checked = !!isActive;
            document.getElementById('editCategoryModal').classList.remove('hidden');
        }

        function closeEditCategoryModal() {
            document.getElementById('editCategoryModal').classList.add('hidden');
        }
    </script>
</div>
@endsection
