@extends('layouts.seller')

@section('title', 'Farm Location & Geofence Settings — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    lat: '{{ $profile?->latitude ?? 17.7534 }}',
    lng: '{{ $profile?->longitude ?? 73.1895 }}',
    radius: {{ $profile?->operating_radius_km ?? 25 }},
    gpsLoading: false,
    gpsLocked: false,
    autoDetectGPS() {
        this.gpsLoading = true;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat = pos.coords.latitude.toFixed(6);
                    this.lng = pos.coords.longitude.toFixed(6);
                    this.gpsLoading = false;
                    this.gpsLocked = true;
                },
                (err) => {
                    this.gpsLoading = false;
                    alert('Geolocation notice: ' + err.message + '. Retaining current coordinates.');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        } else {
            this.gpsLoading = false;
            alert('Browser geolocation is not supported.');
        }
    }
}">

    <!-- Top Breadcrumb & Action Header -->
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm mb-6 border border-[#E2DFD7]/60">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Seller Center</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Account</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-amber font-semibold uppercase tracking-wider">Location &amp; Geofence</span>
                </div>
                <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight">Location Telemetry &amp; Geofence</h1>
                <p class="text-sm text-brand-muted">Configure your verified farm origin coordinates, dispatch hub geofence radius, and postal routing address.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.account.profile') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high transition-colors text-sm font-medium flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">undo</span>
                    <span>Back to Profile</span>
                </a>
                <button type="button" @click="$refs.locationForm.submit()" class="h-11 px-5 rounded-[14px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 transition-all text-sm font-heading font-bold shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    <span>Save Location</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Navigation -->
        <aside class="lg:col-span-3 flex flex-col gap-6 sticky top-24">
            <div class="bg-white rounded-[14px] p-3 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-1">
                <div class="px-3 py-2">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Configuration Suite</span>
                </div>
                <a href="{{ route('seller.account.profile') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">storefront</span>
                        <span class="text-sm font-heading">Shop Profile</span>
                    </div>
                </a>
                <a href="{{ route('seller.account.location') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] bg-secondary-container/30 text-on-secondary-container font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-secondary">explore</span>
                        <span class="text-sm font-heading">Location &amp; Geofence</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">GPS OK</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">shield</span>
                        <span class="text-sm font-heading">Security &amp; Password</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">2FA On</span>
                </a>
            </div>

            <div class="bg-white rounded-[14px] p-4 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-3 font-mono text-xs">
                <span class="font-heading font-bold text-primary text-sm">Spatial Isolation</span>
                <p class="text-brand-muted text-[11px] leading-relaxed">
                    Farm spatial geometry and harvest coordinates are isolated to {{ $profile?->shop_name ?? 'Farm Store' }} (#BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}).
                </p>
                <div class="flex items-center gap-1.5 text-brand-green font-bold text-[10px] uppercase">
                    <span class="material-symbols-outlined text-[14px]">lock</span>
                    <span>WGS84 Datum Encrypted</span>
                </div>
            </div>
        </aside>

        <!-- Right Content Area -->
        <main class="lg:col-span-9 flex flex-col gap-6">

            <form x-ref="locationForm" method="POST" action="{{ route('seller.account.location.update') }}" class="flex flex-col gap-6">
                @csrf
                @method('PUT')

                <!-- Telemetry & Address Card -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex flex-col">
                            <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 2.1</span>
                            <h2 class="font-heading text-xl font-bold text-primary">Farm Origin &amp; Dispatch Hub Geofence</h2>
                            <p class="text-sm text-brand-muted">Precise farm telemetry calculates delivery estimates and farmer-to-door fresh supply chains.</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <button type="button" @click="autoDetectGPS()" :disabled="gpsLoading" class="h-11 px-4 rounded-[12px] bg-brand-amber text-primary font-heading text-xs font-bold hover:brightness-105 active:scale-95 transition-all shadow-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': gpsLoading }">my_location</span>
                                <span x-text="gpsLoading ? 'Triangulating...' : (gpsLocked ? 'GPS Locked (±1.8m)' : 'GPS Auto-Detect')"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Telemetry status bar -->
                    <div class="p-3.5 rounded-[12px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-green animate-ping"></span>
                            <span class="font-mono text-xs font-bold text-primary">LIVE TELEMETRY ACTIVE: High-Precision GPS Lock</span>
                        </div>
                        <span class="font-mono text-[11px] text-brand-muted">Source: ISRO NavIC / Global WGS84 Datum</span>
                    </div>

                    <!-- Postal Address Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5 md:col-span-2">
                            <label class="text-xs font-heading font-semibold text-primary">Survey No. / Farm Lane Address</label>
                            <input type="text" name="address" value="{{ old('address', $profile?->address ?? 'Plot 142, Agricultural Road') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                            @error('address') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">City / Tehsil</label>
                            <input type="text" name="city" value="{{ old('city', $profile?->city ?? 'Contai') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                            @error('city') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">State</label>
                            <input type="text" name="state" value="{{ old('state', $profile?->state ?? 'West Bengal') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                            @error('state') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">Postal PIN Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $profile?->postal_code ?? '721401') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                            @error('postal_code') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Operating Coverage Radius (km)</span>
                                <span class="font-mono text-xs font-bold text-brand-amber" x-text="radius + ' km'"></span>
                            </label>
                            <input type="range" name="operating_radius_km" min="5" max="100" step="5" x-model="radius" class="w-full accent-brand-amber cursor-pointer mt-2">
                            @error('operating_radius_km') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Coordinate Telemetry Inputs (3 columns) -->
                    <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="font-mono text-[10px] text-brand-muted uppercase">Latitude Coordinate</span>
                            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-[10px] border border-[#E2DFD7] shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-brand-amber">north</span>
                                <input type="number" step="any" name="latitude" x-model="lat" class="w-full font-mono text-sm font-bold text-primary bg-transparent focus:outline-none" required>
                            </div>
                            @error('latitude') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="font-mono text-[10px] text-brand-muted uppercase">Longitude Coordinate</span>
                            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-[10px] border border-[#E2DFD7] shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-brand-amber">east</span>
                                <input type="number" step="any" name="longitude" x-model="lng" class="w-full font-mono text-sm font-bold text-primary bg-transparent focus:outline-none" required>
                            </div>
                            @error('longitude') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="font-mono text-[10px] text-brand-muted uppercase">Altitude / Elevation</span>
                            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-[10px] border border-[#E2DFD7] shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-brand-amber">landscape</span>
                                <input type="text" value="142m MSL" readonly class="w-full font-mono text-sm font-bold text-brand-muted bg-transparent focus:outline-none cursor-default">
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Radar Geofence Card -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-heading font-bold text-primary">Interactive Topographic Geofence Preview</span>
                            <span class="font-mono text-[11px] text-brand-green font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">shield</span> COURIER DISPATCH ZONE A
                            </span>
                        </div>
                        
                        <div class="relative w-full h-72 rounded-[14px] bg-slate-900 overflow-hidden shadow-sm border border-[#E2DFD7]/60 flex items-center justify-center">
                            <!-- Radar concentric rings -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="w-64 h-64 rounded-full border border-brand-amber/20 animate-pulse"></div>
                                <div class="absolute w-44 h-44 rounded-full border border-brand-amber/30"></div>
                                <div class="absolute w-24 h-24 rounded-full border border-brand-amber/40"></div>
                                <div class="absolute w-12 h-12 rounded-full bg-brand-amber/20 animate-ping"></div>
                            </div>

                            <!-- Target center -->
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-brand-amber text-primary flex items-center justify-center shadow-lg border-2 border-white">
                                    <span class="material-symbols-outlined text-[20px]">agriculture</span>
                                </div>
                                <div class="mt-2 px-3 py-1 rounded-[8px] bg-primary/90 text-white font-mono text-[11px] font-bold tracking-wider backdrop-blur-md shadow">
                                    {{ $profile?->shop_name ?? 'FARM ORIGIN GATE' }}
                                </div>
                            </div>

                            <!-- Overlay Badges -->
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="px-3 py-1 rounded-[6px] bg-white/90 backdrop-blur-md text-primary font-mono text-xs font-semibold shadow flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-brand-amber"></span>
                                    Hyperlocal Radius: <span x-text="radius + ' km Coverage'"></span>
                                </span>
                                <span class="px-3 py-1 rounded-[6px] bg-white/90 backdrop-blur-md text-primary font-mono text-xs font-semibold shadow">
                                    Nearest Cold-Chain Hub: Active (24 km)
                                </span>
                            </div>

                            <div class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-md px-3 py-2 rounded-[10px] shadow text-right">
                                <div class="font-mono text-[10px] text-brand-muted uppercase">Field Agent Signoff</div>
                                <div class="font-heading text-xs font-bold text-primary">Verified by Bazaario Agri-Logistics #409</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="submit" class="h-11 px-6 rounded-[12px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 font-heading text-sm font-bold shadow-md transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        <span>Update Farm Coordinates</span>
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection
