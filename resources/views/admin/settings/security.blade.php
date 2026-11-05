@extends('layouts.admin')

@section('title', 'Security & Access Control Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Security, Session & Access Control</h1>
            <p class="text-xs text-slate-500 mt-1">Audit administrative session timeouts, OTP verification policies, and authentication guardrails.</p>
        </div>
        <a href="{{ route('admin.settings.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Master Settings
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5 shadow-2xs">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-emerald-50/60 border border-emerald-200/80 rounded-xl">
                <div class="flex items-center gap-2 text-emerald-900 font-bold text-xs mb-1">
                    <span class="material-symbols-outlined text-base">verified_user</span>
                    Role-Based Access Control (RBAC)
                </div>
                <p class="text-[11px] text-emerald-800">Guards <code>auth:admin</code>, <code>auth:seller</code>, and <code>auth:user</code> isolate customer, merchant, and administrative privileges.</p>
            </div>

            <div class="p-4 bg-blue-50/60 border border-blue-200/80 rounded-xl">
                <div class="flex items-center gap-2 text-blue-900 font-bold text-xs mb-1">
                    <span class="material-symbols-outlined text-base">lock</span>
                    OTP & Email Security
                </div>
                <p class="text-[11px] text-blue-800">Email OTP verification with 10-minute expiry windows and rate limiting on registration endpoints.</p>
            </div>
        </div>

        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-600">
            <p class="font-bold text-slate-800">Audit Logging & Protection</p>
            <p class="mt-1 text-[11px] text-slate-500">Atomic database transactions (<code>DB::transaction</code>) and pessimistic row locking protect auction bids, payouts, and order fulfillment against concurrent race conditions.</p>
        </div>
    </div>
</div>
@endsection
