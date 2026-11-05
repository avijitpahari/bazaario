@extends('layouts.admin')

@section('title', 'Platform Mode Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Operating Mode Architecture</h1>
            <p class="text-xs text-slate-500 mt-1">Configure whether Bazaario operates as an open Multi-Vendor marketplace or a centralized Single-Vendor flagship store.</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Master Settings
        </a>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-6 shadow-2xs">
        @csrf
        @php
            $currentMode = \App\Models\SiteSetting::get('platform_mode', 'multi_vendor');
        @endphp

        <div class="space-y-4">
            <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-slate-50 transition {{ $currentMode === 'multi_vendor' ? 'border-amber-500 bg-amber-50/40 ring-1 ring-amber-500/30' : 'border-slate-200' }}">
                <input type="radio" name="platform_mode" value="multi_vendor" {{ $currentMode === 'multi_vendor' ? 'checked' : '' }} class="mt-1 text-amber-600 focus:ring-amber-500">
                <div>
                    <span class="block text-sm font-bold text-slate-900">Multi-Vendor Marketplace Mode</span>
                    <span class="block text-xs text-slate-500 mt-1">Enables seller registration, vendor onboarding queues, multi-seller split orders, seller product catalogs, and vendor payout ledgers.</span>
                </div>
            </label>

            <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-slate-50 transition {{ $currentMode === 'single_vendor' ? 'border-amber-500 bg-amber-50/40 ring-1 ring-amber-500/30' : 'border-slate-200' }}">
                <input type="radio" name="platform_mode" value="single_vendor" {{ $currentMode === 'single_vendor' ? 'checked' : '' }} class="mt-1 text-amber-600 focus:ring-amber-500">
                <div>
                    <span class="block text-sm font-bold text-slate-900">Single-Vendor Storefront Mode</span>
                    <span class="block text-xs text-slate-500 mt-1">Operates as a direct single-brand store. Disables external vendor onboarding while preserving existing product records and orders under the platform store umbrella.</span>
                </div>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs">
                Save Platform Mode
            </button>
        </div>
    </form>
</div>
@endsection
