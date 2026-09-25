@extends('layouts.user')

@section('title', 'Security Settings — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-authority via-slate-authority/95 to-slate-authority/90 p-7 shadow-lg border border-white/10 text-canvas-ivory">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-amber-action/15 blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">security</span>
                Account Security
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-canvas-ivory mt-1">Security Settings</h1>
            <p class="font-body-small text-body-small text-canvas-ivory/70 mt-1">Protect your account with a strong password.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Left: Status --}}
        <div class="flex flex-col gap-4">
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 shadow-sm flex flex-col gap-4">
                <h2 class="font-title-card text-title-card font-bold text-slate-authority flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-action text-[22px]">shield</span>
                    Account Status
                </h2>
                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low/80 border border-slate-authority/5">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[18px] text-status-green">verified_user</span>
                            <span class="font-body-small text-body-small text-slate-authority font-medium">Account Status</span>
                        </div>
                        <span class="font-label-micro text-label-micro {{ $user->status === 'active' ? 'text-status-green' : 'text-error' }} font-semibold">{{ ucfirst($user->status) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low/80 border border-slate-authority/5">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[18px] text-status-green">mark_email_read</span>
                            <span class="font-body-small text-body-small text-slate-authority font-medium">Email Verified</span>
                        </div>
                        <span class="font-label-micro text-label-micro {{ $user->email_verified_at ? 'text-status-green' : 'text-amber-action' }} font-semibold">
                            {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low/80 border border-slate-authority/5">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">phonelink_lock</span>
                            <span class="font-body-small text-body-small text-slate-authority font-medium">Two-Factor Auth</span>
                        </div>
                        <span class="font-label-micro text-label-micro text-amber-action font-semibold">Coming Soon</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Change Password --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 md:p-8 shadow-sm">
                <div class="flex items-center gap-2 mb-6 pb-4 border-b border-surface-container">
                    <span class="material-symbols-outlined text-amber-action text-[24px]">lock_reset</span>
                    <div>
                        <h2 class="font-title-card text-title-card font-bold text-slate-authority">Change Password</h2>
                        <p class="font-body-small text-body-small text-on-surface-variant">Use a strong password with at least 8 characters.</p>
                    </div>
                </div>

                @if(isset($errors) && $errors->any())
                <div class="mb-4 p-4 rounded-xl bg-error/10 border border-error/20 text-error font-body-small text-body-small">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form action="{{ route('user.security.password') }}" method="POST" class="flex flex-col gap-5">
                    @csrf

                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-small text-body-small font-semibold text-slate-authority">Current Password *</label>
                        <input type="password" name="current_password" autocomplete="current-password"
                            class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority font-body-regular text-body-regular focus:outline-none focus:border-amber-action transition-colors @error('current_password') border-error @enderror"
                            placeholder="Enter your current password" required>
                        @error('current_password')<p class="text-error font-body-small text-body-small mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-small text-body-small font-semibold text-slate-authority">New Password *</label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority font-body-regular text-body-regular focus:outline-none focus:border-amber-action transition-colors @error('password') border-error @enderror"
                            placeholder="Min 8 characters, mix of letters and numbers" required>
                        @error('password')<p class="text-error font-body-small text-body-small mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-small text-body-small font-semibold text-slate-authority">Confirm New Password *</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority font-body-regular text-body-regular focus:outline-none focus:border-amber-action transition-colors"
                            placeholder="Re-enter new password" required>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-container">
                        <a href="{{ route('user.profile') }}" class="px-5 py-2.5 rounded-xl border border-slate-authority/20 text-slate-authority font-button-text text-body-small hover:bg-surface-container transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold shadow-sm hover:bg-slate-authority/90 active:scale-[0.99] transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection