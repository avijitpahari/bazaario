@extends('layouts.admin')

@section('title', 'Edit Seller Verification')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Seller Profile & Compliance Governance</h1>
            <p class="text-xs text-slate-500 mt-1">Review merchant credentials, commission rate override, and status.</p>
        </div>
        <a href="{{ route('admin.sellers.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Sellers Directory
        </a>
    </div>

    @if(isset($seller))
    <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-6 shadow-2xs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-sm text-slate-900">{{ $seller->shop_name }}</h3>
                <p class="text-xs text-slate-500">{{ $seller->user?->name }} ({{ $seller->user?->email }})</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $seller->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                Status: {{ ucfirst($seller->status) }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.sellers.commission', $seller->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Custom Commission Rate (%)</label>
                <div class="relative max-w-xs">
                    <input type="number" step="0.1" min="0" max="100" name="commission_rate" value="{{ $seller->commission_rate }}" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none pr-8">
                    <span class="absolute right-3 top-2 text-xs text-slate-400 font-bold">%</span>
                </div>
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs">
                Update Commission Rate
            </button>
        </form>
    </div>
    @else
    <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-xs text-slate-500">
        <p>Seller record not specified. Please select a seller from the directory.</p>
        <a href="{{ route('admin.sellers.index') }}" class="mt-3 inline-block px-4 py-2 bg-amber-500 text-slate-950 rounded-lg font-bold">View Sellers</a>
    </div>
    @endif
</div>
@endsection
