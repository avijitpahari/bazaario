@extends('layouts.user')

@section('title', 'Settings — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Header --}}
    <div class="p-6 rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 shadow-sm border border-white/90">
        <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">settings</span> Preferences
        </span>
        <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">Account Settings</h1>
        <p class="font-body-small text-body-small text-on-surface-variant mt-1">Manage your preferences and account information.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Sidebar Links --}}
        <div class="flex flex-col gap-2">
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-4 shadow-sm flex flex-col gap-1 font-body-small text-body-small">
                @foreach([
                    ['route' => 'user.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
                    ['route' => 'user.profile', 'icon' => 'person', 'label' => 'Profile'],
                    ['route' => 'user.orders.index', 'icon' => 'inventory_2', 'label' => 'Orders'],
                    ['route' => 'user.addresses.index', 'icon' => 'location_on', 'label' => 'Addresses'],
                    ['route' => 'user.security', 'icon' => 'lock', 'label' => 'Security'],
                    ['route' => 'user.settings', 'icon' => 'settings', 'label' => 'Settings', 'active' => true],
                ] as $link)
                <a href="{{ route($link['route']) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ isset($link['active']) ? 'bg-amber-action/15 text-amber-action font-semibold' : 'text-on-surface-variant hover:bg-surface-container hover:text-slate-authority' }}">
                    <span class="material-symbols-outlined text-[18px]">{{ $link['icon'] }}</span>
                    {{ $link['label'] }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Settings Form --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 md:p-8 shadow-sm">
                <div class="flex items-center gap-2 mb-6 pb-4 border-b border-surface-container">
                    <span class="material-symbols-outlined text-amber-action text-[24px]">tune</span>
                    <div>
                        <h2 class="font-title-card text-title-card font-bold text-slate-authority">General Settings</h2>
                        <p class="font-body-small text-body-small text-on-surface-variant">Update your account information and preferences.</p>
                    </div>
                </div>

                @if(isset($errors) && $errors->any())
                <div class="mb-4 p-4 rounded-xl bg-error/10 border border-error/20 text-error font-body-small text-body-small">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form action="{{ route('user.settings.update') }}" method="POST" class="flex flex-col gap-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-small text-body-small font-semibold text-slate-authority">Display Name *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority font-body-regular text-body-regular focus:outline-none focus:border-amber-action transition-colors"
                                placeholder="Your display name" required>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-small text-body-small font-semibold text-slate-authority">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority font-body-regular text-body-regular focus:outline-none focus:border-amber-action transition-colors"
                                placeholder="+91 9876543210">
                        </div>

                        <div class="flex flex-col gap-1.5 md:col-span-2">
                            <label class="font-body-small text-body-small font-semibold text-slate-authority">Preferred Language</label>
                            <select name="preferred_language"
                                class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority font-body-regular text-body-regular focus:outline-none focus:border-amber-action transition-colors">
                                <option value="en" {{ ($user->preferred_language ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="hi" {{ ($user->preferred_language ?? '') === 'hi' ? 'selected' : '' }}>Hindi</option>
                                <option value="bn" {{ ($user->preferred_language ?? '') === 'bn' ? 'selected' : '' }}>Bengali</option>
                                <option value="ta" {{ ($user->preferred_language ?? '') === 'ta' ? 'selected' : '' }}>Tamil</option>
                                <option value="te" {{ ($user->preferred_language ?? '') === 'te' ? 'selected' : '' }}>Telugu</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 p-4 rounded-xl bg-surface-container-low/80 border border-slate-authority/10">
                            <p class="font-body-small text-body-small font-semibold text-slate-authority mb-1">Email Address</p>
                            <p class="font-body-regular text-body-regular text-on-surface-variant">{{ $user->email }}</p>
                            <p class="font-label-micro text-label-micro text-on-surface-variant mt-1">Email cannot be changed from settings. Contact support if needed.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-container">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm hover:opacity-95 active:scale-[0.99] transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Save Settings
                        </button>
                    </div>
                </form>

                {{-- Danger Zone --}}
                <div class="mt-8 pt-6 border-t border-surface-container">
                    <h3 class="font-body-regular text-body-regular font-semibold text-error mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">warning</span>
                        Danger Zone
                    </h3>
                    <div class="p-4 rounded-xl border border-error/20 bg-error/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <p class="font-body-small text-body-small font-semibold text-slate-authority">Log out of all devices</p>
                            <p class="font-label-micro text-label-micro text-on-surface-variant">This will invalidate all your active sessions.</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl border border-error/30 text-error font-button-text text-body-small font-semibold hover:bg-error hover:text-white transition-all">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection