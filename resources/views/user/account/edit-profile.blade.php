@extends('layouts.user')

@section('title', 'Edit Profile — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant">
        <a href="{{ route('user.dashboard') }}" class="hover:text-amber-action transition-colors">Dashboard</a>
        <span>/</span>
        <a href="{{ route('user.profile') }}" class="hover:text-amber-action transition-colors">Profile</a>
        <span>/</span>
        <span class="text-slate-authority font-semibold">Edit</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Left: Avatar preview --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 shadow-sm flex flex-col items-center text-center gap-4">
                <div class="relative">
                    <div class="w-24 h-24 rounded-full bg-slate-authority flex items-center justify-center text-canvas-ivory overflow-hidden shadow-md">
                        @php
                            $avatarUrl = null;
                            if ($user->profile_image) {
                                $avatarUrl = \Illuminate\Support\Str::startsWith($user->profile_image, ['http://', 'https://'])
                                    ? $user->profile_image
                                    : \Illuminate\Support\Facades\Storage::url($user->profile_image);
                            }
                        @endphp
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="font-display-hero text-headline-section font-bold">{{ $user->initials }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="font-title-card text-title-card font-bold text-slate-authority">{{ $user->name }}</p>
                    <p class="font-body-small text-body-small text-on-surface-variant">{{ $user->email }}</p>
                </div>
                <form action="{{ route('user.profile.photo') }}" method="POST" enctype="multipart/form-data" class="w-full">
                    @csrf
                    <label class="w-full py-2.5 px-4 rounded-xl bg-surface-container/70 hover:bg-surface-container border border-slate-authority/10 font-button-text text-body-small font-medium text-slate-authority hover:border-amber-action transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">photo_camera</span>
                        Update Photo
                        <input type="file" name="profile_image" accept="image/*" class="hidden" onchange="this.closest('form').submit()">
                    </label>
                </form>
            </div>
        </div>

        {{-- Right: Edit form --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 md:p-8 shadow-sm">
                <div class="flex items-center gap-2 mb-6 pb-4 border-b border-surface-container">
                    <span class="material-symbols-outlined text-amber-action text-[24px]">manage_accounts</span>
                    <div>
                        <h1 class="font-title-card text-title-card font-bold text-slate-authority">Edit Profile</h1>
                        <p class="font-body-small text-body-small text-on-surface-variant">Update your personal information</p>
                    </div>
                </div>

                @if(isset($errors) && $errors->any())
                <div class="mb-4 p-4 rounded-xl bg-error/10 border border-error/20 text-error font-body-small text-body-small">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('user.profile.update') }}" method="POST" class="flex flex-col gap-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-small text-body-small font-semibold text-slate-authority">Full Name <span class="text-error">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority font-body-regular text-body-regular focus:outline-none focus:border-amber-action transition-colors @error('name') border-error @enderror"
                                placeholder="Your full name" required>
                            @error('name')<p class="text-error font-body-small text-body-small mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-small text-body-small font-semibold text-slate-authority">Email Address</label>
                            <input type="email" value="{{ $user->email }}"
                                class="bg-surface-container border border-slate-authority/10 rounded-xl px-4 py-3 text-on-surface-variant font-body-regular text-body-regular cursor-not-allowed"
                                disabled>
                            <p class="font-label-micro text-label-micro text-on-surface-variant">Email cannot be changed.</p>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-small text-body-small font-semibold text-slate-authority">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority font-body-regular text-body-regular focus:outline-none focus:border-amber-action transition-colors @error('phone') border-error @enderror"
                                placeholder="+91 9876543210">
                            @error('phone')<p class="text-error font-body-small text-body-small mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
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
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-container mt-2">
                        <a href="{{ route('user.profile') }}" class="px-5 py-2.5 rounded-xl border border-slate-authority/20 text-slate-authority font-button-text text-body-small hover:bg-surface-container transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm hover:opacity-95 active:scale-[0.99] transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection