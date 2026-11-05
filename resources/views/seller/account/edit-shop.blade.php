@extends('layouts.seller')

@section('title', 'Edit Shop — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
@endphp

<div class="max-w-4xl mx-auto space-y-6 pb-16">
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm border border-[#E2DFD7]/60 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold font-heading text-slate-900">Edit Shop Profile</h1>
            <p class="text-xs text-slate-500">Update shop bio, customer contact, and storefront details.</p>
        </div>
        <a href="{{ route('seller.account.profile') }}" class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-50 text-slate-700 hover:text-amber-800 text-xs font-semibold border border-slate-200/80 hover:border-amber-200 transition-all">
            <span class="material-symbols-outlined text-[15px] transition-transform duration-200 group-hover:-translate-x-1">arrow_back</span>
            <span>Back to Profile</span>
        </a>
    </div>

    <form method="POST" action="{{ route('seller.account.profile.update') }}" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Shop Name</label>
            <input type="text" name="shop_name" value="{{ $profile?->shop_name }}" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Storefront Bio</label>
            <textarea name="bio" rows="4" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">{{ $profile?->bio }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">City</label>
                <input type="text" name="city" value="{{ $profile?->city }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">State</label>
                <input type="text" name="state" value="{{ $profile?->state }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-sm">
                Save Storefront Changes
            </button>
        </div>
    </form>
</div>
@endsection
