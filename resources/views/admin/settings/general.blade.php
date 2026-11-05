@extends('layouts.admin')

@section('title', 'General Branding Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Platform Branding & Identity</h1>
            <p class="text-xs text-slate-500 mt-1">Configure marketplace title, public tagline, and logo imagery.</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Master Settings
        </a>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5 shadow-2xs">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Site / Marketplace Name</label>
            <input type="text" name="site_name" value="{{ \App\Models\SiteSetting::get('site_name', 'Bazaario') }}" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Public Tagline</label>
            <input type="text" name="site_tagline" value="{{ \App\Models\SiteSetting::get('site_tagline', 'AI-Powered Configurable Multi-Vendor Marketplace') }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Platform Brand Logo</label>
            @if(\App\Models\SiteSetting::get('site_logo'))
                <div class="mb-2 p-2 bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-3">
                    <img src="{{ asset('storage/' . \App\Models\SiteSetting::get('site_logo')) }}" alt="Logo" class="h-8 max-w-[140px] object-contain">
                    <span class="text-[11px] text-slate-500 font-mono">Current active logo</span>
                </div>
            @endif
            <input type="file" name="site_logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Support Contact Email</label>
            <input type="email" name="contact_email" value="{{ \App\Models\SiteSetting::get('contact_email', 'support@bazaario.in') }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Support Phone</label>
            <input type="text" name="contact_phone" value="{{ \App\Models\SiteSetting::get('contact_phone', '+91 800-BAZAARIO') }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-5 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs">
                Save Branding Settings
            </button>
        </div>
    </form>
</div>
@endsection
