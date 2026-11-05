@extends('layouts.seller')

@section('title', 'My Public Storefront — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
@endphp

<div class="flex flex-col w-full pb-16 space-y-6">
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm border border-[#E2DFD7]/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-heading text-slate-900">{{ $profile?->shop_name ?? 'My Merchant Shop' }}</h1>
            <p class="text-xs text-slate-500 font-mono">{{ $profile?->city ?? 'Location not set' }} • Trust Score: {{ $profile?->trust_score ?? '98.5' }}%</p>
        </div>
        <div class="flex items-center gap-2">
            @if($profile?->shop_slug)
                <a href="{{ url('/sellers/' . $profile->shop_slug) }}" target="_blank" class="px-4 py-2 rounded-xl bg-secondary-container text-on-secondary-container hover:bg-secondary-fixed text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                    <span>View Public Storefront</span>
                </a>
            @endif
            <a href="{{ route('seller.account.profile') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                Edit Store Profile
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm space-y-4">
        @if($profile?->banner_path)
            <div class="w-full h-40 rounded-xl overflow-hidden mb-4 border border-slate-200">
                <img src="{{ asset('storage/' . $profile->banner_path) }}" alt="Storefront Banner" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-2xl font-heading overflow-hidden border border-amber-200 shrink-0">
                @if($profile?->logo_path)
                    <img src="{{ asset('storage/' . $profile->logo_path) }}" alt="Logo" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($profile?->shop_name ?? 'S', 0, 1)) }}
                @endif
            </div>
            <div>
                <h3 class="font-bold text-base text-slate-900">{{ $profile?->shop_name }}</h3>
                <p class="text-xs text-slate-500">{{ $profile?->bio ?: 'No storefront bio provided.' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-[11px] text-slate-400 font-semibold block">Total Active Products</span>
                <span class="text-xl font-bold font-mono text-slate-900">{{ $seller?->products()?->where('status', 'active')->count() ?? 0 }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-[11px] text-slate-400 font-semibold block">Store Operating Type</span>
                <span class="text-sm font-bold text-slate-900">{{ $profile?->seller_type ?? 'Independent Artisan' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-[11px] text-slate-400 font-semibold block">Platform Commission Rate</span>
                <span class="text-xl font-bold font-mono text-amber-700">{{ $profile?->commission_rate ?? '8.5' }}%</span>
            </div>
        </div>
    </div>
</div>
@endsection
