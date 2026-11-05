@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Add New Product Category</h1>
            <p class="text-xs text-slate-500 mt-1">Create a top-level or sub-category taxonomy for catalog classification.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Categories
        </a>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5 shadow-2xs">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Category Name</label>
            <input type="text" name="name" required placeholder="e.g. Organic Farm Produce" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
            <textarea name="description" rows="3" placeholder="Category description..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none"></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Category Image</label>
            <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100">
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs">
                Create Category
            </button>
        </div>
    </form>
</div>
@endsection
