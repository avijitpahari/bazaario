@extends('layouts.admin')

@section('title', 'AI Engine Hub & Model Ops')

@section('content')
<form method="POST" action="{{ route('admin.settings.ai.update') }}" class="flex flex-col w-full gap-space-lg">
    @csrf

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-space-sm mb-1">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">AI Engine Hub & Model Operations</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-900 font-mono text-[11px] font-bold border border-indigo-200/60 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-ping"></span>
                    {{ strtoupper($settings['gemini_model'] ?? 'GEMINI 1.5 FLASH') }} ACTIVE
                </span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500">
                Configure autonomous shopping assistants, automated support auto-triage, vector catalog embeddings, and Bengali/Hindi translation models.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-body-md text-xs font-bold shadow-2xs hover:bg-amber-400 transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Save AI Config</span>
            </button>
        </div>
    </div>

    <!-- Telemetry Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Average Inference Latency</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-emerald-700">142 ms</span>
            </div>
            <span class="text-[10px] text-slate-400">99.2% requests &lt; 250ms SLA</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Autonomous Support Resolution</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">84.6%</span>
            </div>
            <span class="text-[10px] text-emerald-700 font-bold">3,120 queries handled</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Multilingual Translation Quality</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">4.9 / 5.0</span>
            </div>
            <span class="text-[10px] text-slate-400">Bengali, Hindi, English</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Smart Recommendation Lift</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-[#F5A623]">+ 22.4%</span>
            </div>
            <span class="text-[10px] text-slate-400">Cart conversion boost</span>
        </div>
    </div>

    <!-- AI Model Credentials & Hyperparameters -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
        <!-- Panel 1: LLM Engine Credentials -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col gap-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-indigo-600 text-xl">smart_toy</span>
                <h3 class="font-bold text-sm text-[#0F172A]">Google Gemini API & Foundation Model</h3>
            </div>

            <div class="flex flex-col gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Gemini API Key</label>
                    <input 
                        type="password" 
                        name="gemini_api_key" 
                        value="{{ $settings['gemini_api_key'] ?? '' }}" 
                        placeholder="AIzaSy..." 
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono"
                    />
                    <span class="text-[10px] text-slate-400 mt-1 block">Used for product descriptions, translation, and automated order triage.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Target Foundation Model</label>
                    <select name="gemini_model" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-body-sm">
                        <option value="gemini-1.5-flash" {{ ($settings['gemini_model'] ?? '') === 'gemini-1.5-flash' ? 'selected' : '' }}>
                            Gemini 1.5 Flash (Production Default — Lowest Latency)
                        </option>
                        <option value="gemini-1.5-pro" {{ ($settings['gemini_model'] ?? '') === 'gemini-1.5-pro' ? 'selected' : '' }}>
                            Gemini 1.5 Pro (High Reasoning & Multi-Modal Vision)
                        </option>
                        <option value="gemini-2.0-flash" {{ ($settings['gemini_model'] ?? '') === 'gemini-2.0-flash' ? 'selected' : '' }}>
                            Gemini 2.0 Flash (Next-Gen High Throughput)
                        </option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Temperature (Creativity)</label>
                        <input 
                            type="number" 
                            step="0.1" 
                            min="0.0" 
                            max="1.0" 
                            name="temperature" 
                            value="{{ $settings['temperature'] ?? '0.7' }}" 
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono"
                        />
                        <span class="text-[10px] text-slate-400 mt-0.5 block">0.0 (Deterministic) - 1.0 (Creative)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dispute Confidence Threshold (%)</label>
                        <input 
                            type="number" 
                            min="50" 
                            max="100" 
                            name="dispute_confidence_threshold" 
                            value="{{ $settings['dispute_confidence_threshold'] ?? '85' }}" 
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono"
                        />
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Minimum confidence score to suggest resolution</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel 2: Marketplace Financial & Autonomous Parameters -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col gap-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-amber-600 text-xl">account_balance_wallet</span>
                <h3 class="font-bold text-sm text-[#0F172A]">Escrow Settlement & Commission Hyperparameters</h3>
            </div>

            <div class="flex flex-col gap-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Base Platform Commission (%)</label>
                        <input 
                            type="number" 
                            step="0.1" 
                            name="platform_commission_base" 
                            value="{{ $settings['platform_commission_base'] ?? '8.5' }}" 
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono"
                        />
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Applied across default merchant tiers</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Escrow Cooling Period (Days)</label>
                        <input 
                            type="number" 
                            name="escrow_cooling_period_days" 
                            value="{{ $settings['escrow_cooling_period_days'] ?? '7' }}" 
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono"
                        />
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Dispute holding window prior to payout</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex flex-col gap-3">
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <span class="font-bold text-xs text-[#0F172A] block">Autonomous Dispute Auto-Triage</span>
                            <span class="text-[11px] text-slate-500">Automatically inspect buyer photographic proof & courier tracking</span>
                        </div>
                        <select name="auto_triage_enabled" class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-xs font-bold font-mono">
                            <option value="true" {{ ($settings['auto_triage_enabled'] ?? '') === 'true' ? 'selected' : '' }}>ENABLED</option>
                            <option value="false" {{ ($settings['auto_triage_enabled'] ?? '') === 'false' ? 'selected' : '' }}>DISABLED</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <span class="font-bold text-xs text-[#0F172A] block">Vector Recommendation Engine</span>
                            <span class="text-[11px] text-slate-500">Semantic search embeddings for Bengali & Hindi colloquial queries</span>
                        </div>
                        <select name="recommendation_engine_enabled" class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-xs font-bold font-mono">
                            <option value="true" {{ ($settings['recommendation_engine_enabled'] ?? '') === 'true' ? 'selected' : '' }}>ENABLED</option>
                            <option value="false" {{ ($settings['recommendation_engine_enabled'] ?? '') === 'false' ? 'selected' : '' }}>DISABLED</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Save Action Bar -->
    <div class="p-4 bg-white rounded-xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
        <span class="text-xs text-slate-500">
            Changes will take effect across live production AI worker nodes immediately.
        </span>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-slate-950 font-bold text-xs transition-colors shadow-2xs flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">verified</span>
            <span>Deploy AI & Escrow Configuration</span>
        </button>
    </div>
</form>
@endsection
