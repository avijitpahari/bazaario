@extends('layouts.admin')

@section('title', 'Live Auction Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Live Auction Floor Configuration</h1>
            <p class="text-xs text-slate-500 mt-1">Configure live bidding rules, minimum increments, and anti-sniping buffers.</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Master Settings
        </a>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5 shadow-2xs">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Auction System Status</label>
            <select name="auction_system_enabled" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                <option value="1" {{ (\App\Models\SiteSetting::get('auction_system_enabled', '1')) == '1' ? 'selected' : '' }}>Enabled (Auctions active and open for bidding)</option>
                <option value="0" {{ (\App\Models\SiteSetting::get('auction_system_enabled', '1')) == '0' ? 'selected' : '' }}>Paused (Bidding temporarily disabled)</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Default Minimum Bid Increment (₹ INR)</label>
            <input type="number" step="1" min="1" name="default_bid_increment" value="{{ \App\Models\SiteSetting::get('default_bid_increment', '50') }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
            <p class="text-[11px] text-slate-400 mt-1">Minimum step required above the active highest bid.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Anti-Sniping Buffer Extension (Seconds)</label>
            <input type="number" step="10" min="0" max="3600" name="auction_anti_snipe_seconds" value="{{ \App\Models\SiteSetting::get('auction_anti_snipe_seconds', '120') }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
            <p class="text-[11px] text-slate-400 mt-1">Seconds appended to the end time when a competitive bid arrives within the final 2 minutes.</p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs">
                Save Auction Settings
            </button>
        </div>
    </form>
</div>
@endsection
