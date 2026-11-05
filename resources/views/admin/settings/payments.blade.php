@extends('layouts.admin')

@section('title', 'Payments & Order Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Payments & Checkout Configuration</h1>
            <p class="text-xs text-slate-500 mt-1">Configure accepted payment methods, cash on delivery thresholds, and currency policies.</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Master Settings
        </a>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5 shadow-2xs">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Cash on Delivery (COD) Checkout</label>
            <select name="cod_enabled" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                <option value="1" {{ (\App\Models\SiteSetting::get('cod_enabled', '1')) == '1' ? 'selected' : '' }}>Enabled (Permit cash at doorstep payments)</option>
                <option value="0" {{ (\App\Models\SiteSetting::get('cod_enabled', '1')) == '0' ? 'selected' : '' }}>Disabled (Only prepaid checkouts)</option>
            </select>
        </div>

        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-600">
            <span class="font-bold text-slate-800">Primary Currency: Indian Rupee (₹ INR)</span>
            <p class="mt-1 text-[11px] text-slate-500">Unified multi-vendor checkout automatically calculates split vendor disbursements in INR.</p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs">
                Save Payment Settings
            </button>
        </div>
    </form>
</div>
@endsection
