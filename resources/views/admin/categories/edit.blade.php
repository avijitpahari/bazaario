@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Edit Category</h1>
            <p class="text-xs text-slate-500 mt-1">Update category metadata, status, or display image.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Categories
        </a>
    </div>

    @if(isset($category))
    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5 shadow-2xs">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Category Name</label>
            <input type="text" name="name" value="{{ $category->name }}" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">{{ $category->description }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                <option value="active" {{ $category->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $category->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs">
                Update Category
            </button>
        </div>
    </form>
    @else
    <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-xs text-slate-500">
        <p>Category record not found. Please navigate from the categories directory.</p>
        <a href="{{ route('admin.categories.index') }}" class="mt-3 inline-block px-4 py-2 bg-amber-500 text-slate-950 rounded-lg font-bold">Go to Categories</a>
    </div>
    @endif
</div>
@endsection
