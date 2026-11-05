@extends('layouts.admin')

@section('title', 'Commissions & Fees Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Platform Commissions & Payout Rules</h1>
            <p class="text-xs text-slate-500 mt-1">Configure default marketplace commission cut and vendor withdrawal minimums.</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Master Settings
        </a>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5 shadow-2xs">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Standard Base Commission Rate (%)</label>
            <div class="relative">
                <input type="number" step="0.1" min="0" max="100" name="commission_rate" value="{{ \App\Models\SiteSetting::get('commission_rate', '8.5') }}" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none pr-8">
                <span class="absolute right-3 top-2 text-xs text-slate-400 font-bold">%</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Platform fee deducted from gross order totals when seller orders reach fulfilled status.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Minimum Payout Withdrawal (₹ INR)</label>
            <input type="number" step="10" min="0" name="min_payout_amount" value="{{ \App\Models\SiteSetting::get('min_payout_amount', '500') }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
            <p class="text-[11px] text-slate-400 mt-1">Minimum seller balance required to generate an automated payout request.</p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs">
                Save Commission Settings
            </button>
        </div>
    </form>
</div>
@endsection
