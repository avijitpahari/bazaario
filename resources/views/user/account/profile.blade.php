@extends('layouts.user')

@section('title', 'My Profile — Bazaario')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $avatarUrl = null;
    if ($user->profile_image) {
        $avatarUrl = Str::startsWith($user->profile_image, ['http://', 'https://'])
            ? $user->profile_image
            : Storage::url($user->profile_image);
    }
@endphp

<div class="relative w-full max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Breadcrumb --}}
    <div class="flex flex-wrap items-center justify-between gap-4 p-3 rounded-xl bg-card-white/80 backdrop-blur-md border border-white/80 shadow-[0_4px_24px_rgba(15,23,42,0.04)]">
        <nav class="flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant">
            <a class="hover:text-amber-action transition-colors" href="{{ route('home') }}">Home</a>
            <span class="text-on-surface-variant/40">/</span>
            <a class="hover:text-amber-action transition-colors" href="{{ route('user.dashboard') }}">Account</a>
            <span class="text-on-surface-variant/40">/</span>
            <span class="text-slate-authority font-semibold">My Profile</span>
        </nav>
        <div class="flex flex-wrap items-center gap-3 font-label-micro text-label-micro">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-status-green/10 text-status-green font-medium border border-status-green/20">
                <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                <span>VERIFIED BUYER • UID: BZ-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}-IN</span>
            </div>
        </div>
    </div>

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-8 shadow-[0_12px_36px_rgba(15,23,42,0.05)] border border-white/90">
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-amber-action/10 blur-3xl pointer-events-none"></div>
        <div class="absolute right-32 -bottom-20 w-64 h-64 rounded-full bg-primary-alpha-10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex flex-col gap-2 max-w-2xl">
                <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">verified</span>
                    Account Management
                </span>
                <h1 class="font-display-hero text-headline-section md:text-display-hero text-slate-authority leading-tight">My Profile</h1>
                <p class="font-body-regular text-body-regular text-on-surface-variant mt-1">
                    Manage your personal credentials, contact endpoints, and delivery preferences.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('user.profile.edit') }}" class="px-5 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-[0_4px_14px_rgba(245,166,35,0.35)] hover:opacity-95 active:scale-[0.99] transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                    Edit Profile
                </a>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT: Avatar Card --}}
        <div class="lg:col-span-4 flex flex-col gap-6">
            <div class="relative overflow-hidden rounded-2xl bg-card-white/85 backdrop-blur-xl border border-white/90 p-6 shadow-[0_12px_32px_rgba(15,23,42,0.06)] flex flex-col items-center text-center">
                {{-- Avatar --}}
                <div class="relative mb-4 mt-2">
                    <div class="absolute -inset-2 rounded-full bg-gradient-to-tr from-amber-action via-amber-200 to-amber-action/20 opacity-70 blur-md animate-pulse"></div>
                    <div class="relative w-28 h-28 rounded-full p-1 bg-gradient-to-b from-amber-action/80 to-slate-authority/30 shadow-[0_8px_20px_rgba(245,166,35,0.25)] flex items-center justify-center">
                        <div class="w-full h-full rounded-full bg-slate-authority flex flex-col items-center justify-center text-card-white relative overflow-hidden">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-full">
                            @else
                                <span class="font-display-hero text-headline-section font-bold tracking-tight text-canvas-ivory">{{ $user->initials }}</span>
                                <span class="absolute bottom-1 font-label-micro text-[9px] uppercase tracking-wider text-amber-action/90 font-mono">Bazaario</span>
                            @endif
                        </div>
                    </div>
                    <div class="absolute bottom-0 right-0 p-1.5 bg-card-white rounded-full shadow-md">
                        <span class="material-symbols-outlined text-[18px] text-status-green block" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </div>
                </div>

                <h2 class="font-title-card text-title-card font-bold text-slate-authority">{{ $user->name }}</h2>
                <span class="font-label-micro text-label-micro text-on-surface-variant mt-0.5">{{ $user->email }}</span>

                <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-action/15 border border-amber-action/30 text-slate-authority font-label-micro text-label-micro font-semibold">
                    <span class="material-symbols-outlined text-[14px] text-amber-action">award_star</span>
                    Active Buyer • {{ ucfirst($user->status) }}
                </div>

                {{-- Upload Photo Form --}}
                <form action="{{ route('user.profile.photo') }}" method="POST" enctype="multipart/form-data" class="mt-5 w-full">
                    @csrf
                    <label class="w-full py-2.5 px-4 rounded-xl bg-surface-container/70 hover:bg-surface-container border border-slate-authority/10 font-button-text text-body-small font-medium text-slate-authority hover:border-amber-action transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">photo_camera</span>
                        Change Profile Photo
                        <input type="file" name="profile_image" accept="image/*" class="hidden" onchange="this.closest('form').submit()">
                    </label>
                </form>

                <div class="w-full h-px bg-surface-container-high my-5"></div>

                {{-- Meta info --}}
                <div class="w-full flex flex-col gap-3 font-body-small text-body-small text-left">
                    <div class="flex items-center justify-between py-1">
                        <span class="text-on-surface-variant flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                            Member Since
                        </span>
                        <span class="font-label-micro text-label-micro font-semibold text-slate-authority">
                            {{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-on-surface-variant flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-status-green">verified_user</span>
                            Account Status
                        </span>
                        <span class="font-label-micro text-label-micro {{ $user->status === 'active' ? 'text-status-green bg-status-green/10' : 'text-error bg-error/10' }} px-2 py-0.5 rounded-full font-semibold">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-on-surface-variant flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">language</span>
                            Language
                        </span>
                        <span class="font-label-micro text-label-micro font-semibold text-slate-authority">
                            {{ strtoupper($user->preferred_language ?? 'EN') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-on-surface-variant flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">email</span>
                            Email Verified
                        </span>
                        <span class="font-label-micro text-label-micro font-semibold {{ $user->email_verified_at ? 'text-status-green' : 'text-error' }}">
                            {{ $user->email_verified_at ? 'Verified ✓' : 'Pending' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Personal Info & Addresses --}}
        <div class="lg:col-span-8 flex flex-col gap-6">

            {{-- Personal Credentials Card --}}
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 md:p-8 shadow-[0_12px_36px_rgba(15,23,42,0.05)] flex flex-col gap-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-surface-container">
                    <div>
                        <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-wider text-amber-action font-semibold">Credential Layer</span>
                        <h2 class="font-title-card text-title-card md:text-headline-section font-bold text-slate-authority mt-0.5">Personal Credentials</h2>
                    </div>
                    <a href="{{ route('user.profile.edit') }}" class="self-start sm:self-auto px-4 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm hover:opacity-95 active:scale-[0.99] transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[17px]">edit</span>
                        Edit Profile
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Full Name --}}
                    <div class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 transition-all hover:border-amber-action">
                        <div class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                            <span>FULL NAME</span>
                            <span class="material-symbols-outlined text-[15px]">person</span>
                        </div>
                        <p class="font-title-card text-title-card font-semibold text-slate-authority">{{ $user->name }}</p>
                    </div>
                    {{-- Email --}}
                    <div class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 transition-all hover:border-amber-action">
                        <div class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                            <span>PRIMARY EMAIL</span>
                            @if($user->email_verified_at)
                            <span class="inline-flex items-center gap-1 font-label-micro text-[11px] text-status-green font-semibold">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Verified
                            </span>
                            @endif
                        </div>
                        <p class="font-body-regular text-body-regular font-semibold text-slate-authority truncate">{{ $user->email }}</p>
                    </div>
                    {{-- Phone --}}
                    <div class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 transition-all hover:border-amber-action">
                        <div class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                            <span>REGISTERED PHONE</span>
                        </div>
                        <p class="font-body-regular text-body-regular font-semibold text-slate-authority font-mono">
                            {{ $user->phone ? substr($user->phone, 0, -5) . '•••••' : 'Not set' }}
                        </p>
                    </div>
                    {{-- Language --}}
                    <div class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 transition-all hover:border-amber-action">
                        <div class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                            <span>PREFERRED LANGUAGE</span>
                            <span class="material-symbols-outlined text-[15px]">globe_asia</span>
                        </div>
                        <p class="font-body-regular text-body-regular font-medium text-slate-authority">
                            {{ strtoupper($user->preferred_language ?? 'en') === 'EN' ? 'English' : strtoupper($user->preferred_language ?? 'EN') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Saved Addresses --}}
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 md:p-8 shadow-[0_12px_36px_rgba(15,23,42,0.05)] flex flex-col gap-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-action/10 flex items-center justify-center text-amber-action">
                            <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                        </div>
                        <div>
                            <h3 class="font-title-card text-title-card font-bold text-slate-authority">Saved Delivery Addresses</h3>
                            <p class="font-body-small text-body-small text-on-surface-variant">Validated postal drop-points for insured goods.</p>
                        </div>
                    </div>
                    <a href="{{ route('user.addresses.index') }}" class="hidden sm:inline-flex items-center gap-1 font-button-text text-body-small font-semibold text-slate-authority hover:text-amber-action transition-colors">
                        Manage All {{ $addresses->count() }} Addresses
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                    @forelse($addresses as $address)
                    <div class="relative p-4 rounded-xl {{ $address->is_default ? 'bg-surface-container-low/70 border border-amber-action/40' : 'bg-surface-container-low/70 border border-slate-authority/10 hover:border-amber-action' }} flex flex-col justify-between gap-3 transition-all">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] {{ $address->is_default ? 'text-amber-action' : 'text-on-surface-variant' }}">{{ $address->type_icon }}</span>
                                <span class="font-title-card text-body-regular font-bold text-slate-authority">{{ $address->type_label }}</span>
                            </div>
                            @if($address->is_default)
                            <span class="px-2 py-0.5 rounded-full bg-amber-action text-slate-authority font-label-micro text-label-micro font-bold">DEFAULT</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-micro text-label-micro">{{ strtoupper($address->type) }}</span>
                            @endif
                        </div>
                        <p class="font-body-small text-body-small text-on-surface-variant leading-relaxed">
                            {{ $address->address_line_1 }}{{ $address->address_line_2 ? ', ' . $address->address_line_2 : '' }},<br>
                            {{ $address->city }}, {{ $address->state }} — <span class="font-mono text-slate-authority font-semibold">{{ $address->postal_code }}</span>, {{ $address->country }}
                        </p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-authority/5 font-label-micro text-label-micro text-on-surface-variant">
                            <span>Receiver: {{ $address->full_name }}</span>
                            <span class="text-slate-authority">{{ $address->phone }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="md:col-span-2 p-6 rounded-xl border border-dashed border-slate-authority/20 flex flex-col items-center gap-3 text-center">
                        <span class="material-symbols-outlined text-[36px] text-on-surface-variant">add_location_alt</span>
                        <p class="font-body-small text-body-small text-on-surface-variant">No saved addresses yet.</p>
                        <a href="{{ route('user.addresses.index') }}" class="px-4 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold">Add Address</a>
                    </div>
                    @endforelse
                </div>
                <div class="sm:hidden text-center pt-2">
                    <a href="{{ route('user.addresses.index') }}" class="font-button-text text-body-small font-semibold text-slate-authority hover:text-amber-action">
                        Manage All Addresses →
                    </a>
                </div>
            </div>

            {{-- Security Banner --}}
            <div class="rounded-2xl bg-slate-authority text-canvas-ivory p-6 md:p-8 shadow-[0_16px_40px_rgba(15,23,42,0.18)] flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute -left-10 -bottom-10 w-48 h-48 rounded-full bg-amber-action/10 blur-2xl pointer-events-none"></div>
                <div class="flex items-start gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-canvas-ivory/10 border border-white/10 flex items-center justify-center text-amber-action shrink-0">
                        <span class="material-symbols-outlined text-[28px]">shield_with_heart</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-wider text-amber-action font-semibold">Account Security</span>
                        <h3 class="font-title-card text-title-card md:text-headline-section font-bold text-canvas-ivory">Keep your account safe</h3>
                        <p class="font-body-small text-body-small text-canvas-ivory/70 max-w-lg">Manage your password and two-factor authentication settings.</p>
                    </div>
                </div>
                <div class="relative z-10 shrink-0 w-full md:w-auto">
                    <a href="{{ route('user.security') }}" class="w-full md:w-auto px-5 py-3 rounded-xl bg-card-white/10 hover:bg-card-white/20 border border-white/20 text-canvas-ivory font-button-text text-body-small font-medium shadow-sm transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-amber-action">lock</span>
                        Security Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection