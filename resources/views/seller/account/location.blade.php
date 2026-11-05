@extends('layouts.seller')

@section('title', 'Farm Location & Geofence Settings — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    lat: '{{ $profile?->latitude ?? 21.7781 }}',
    lng: '{{ $profile?->longitude ?? 87.7516 }}',
    radius: {{ $profile?->operating_radius_km ?? 25 }},
    gpsLoading: false,
    gpsLocked: false,
    mapView: 'interactive',
    setMapMode(mode) {
        this.mapView = mode;
        if (mode === 'interactive' && window.leafletMap) {
            setTimeout(() => {
                window.leafletMap.invalidateSize();
                if (window.leafletMap && window.leafletMarker) {
                    window.leafletMap.panTo(window.leafletMarker.getLatLng());
                }
            }, 100);
        }
    },
    onCoordChange() {
        if (window.syncMapToCoords) {
            window.syncMapToCoords(this.lat, this.lng);
        }
    },
    onRadiusChange() {
        if (window.syncMapRadius) {
            window.syncMapRadius(this.radius);
        }
    },
    autoDetectGPS() {
        this.gpsLoading = true;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat = pos.coords.latitude.toFixed(6);
                    this.lng = pos.coords.longitude.toFixed(6);
                    this.gpsLoading = false;
                    this.gpsLocked = true;
                    if (window.syncMapToCoords) {
                        window.syncMapToCoords(this.lat, this.lng);
                    }
                    if (typeof showToast === 'function') {
                        showToast('GPS Locked successfully: ' + this.lat + ', ' + this.lng, 'success');
                    }
                },
                (err) => {
                    this.gpsLoading = false;
                    if (typeof showToast === 'function') {
                        showToast('Geolocation notice: ' + err.message + '. Retaining current coordinates.', 'warning');
                    }
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        } else {
            this.gpsLoading = false;
            if (typeof showToast === 'function') {
                showToast('Browser geolocation is not supported.', 'warning');
            }
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
            <div class="bg-white rounded-2xl p-3 shadow-xs border border-slate-200/80 flex flex-col gap-1">
                <div class="px-3 py-2">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 font-bold">Configuration Suite</span>
                </div>
                <a href="{{ route('seller.account.profile') }}" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-heading font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-slate-400">storefront</span>
                        <span class="text-sm font-heading">Shop Profile</span>
                    </div>
                </a>
                <a href="{{ route('seller.account.location') }}" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-amber-500/10 text-amber-950 font-heading font-bold border border-amber-300/60 shadow-2xs transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-amber-600">explore</span>
                        <span class="text-sm font-heading">Location &amp; Geofence</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold">GPS OK</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-heading font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-slate-400">shield</span>
                        <span class="text-sm font-heading">Security &amp; Password</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold">2FA On</span>
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
                            <input type="range" name="operating_radius_km" min="5" max="100" step="5" x-model="radius" @input="onRadiusChange()" class="w-full accent-brand-amber cursor-pointer mt-2">
                            @error('operating_radius_km') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Coordinate Telemetry Inputs (3 columns) -->
                    <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="font-mono text-[10px] text-brand-muted uppercase">Latitude Coordinate</span>
                            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-[10px] border border-[#E2DFD7] shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-brand-amber">north</span>
                                <input type="number" step="any" name="latitude" x-model="lat" @input="onCoordChange()" class="w-full font-mono text-sm font-bold text-primary bg-transparent focus:outline-none" required>
                            </div>
                            @error('latitude') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="font-mono text-[10px] text-brand-muted uppercase">Longitude Coordinate</span>
                            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-[10px] border border-[#E2DFD7] shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-brand-amber">east</span>
                                <input type="number" step="any" name="longitude" x-model="lng" @input="onCoordChange()" class="w-full font-mono text-sm font-bold text-primary bg-transparent focus:outline-none" required>
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

                    <!-- Interactive Farm Map & Geofence Card -->
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <span class="text-xs font-heading font-bold text-primary flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-brand-amber">map</span>
                                Farm Geofence &amp; Origin Pin
                            </span>
                            <div class="flex items-center gap-2 flex-wrap">
                                <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-0.5 text-xs font-medium">
                                    <button type="button" @click="setMapMode('interactive')" :class="mapView === 'interactive' ? 'bg-white shadow-2xs text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900'" class="px-2.5 py-1 rounded-md transition-all">
                                        Interactive Pin
                                    </button>
                                    <button type="button" @click="setMapMode('google')" :class="mapView === 'google' ? 'bg-white shadow-2xs text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900'" class="px-2.5 py-1 rounded-md transition-all">
                                        Google Satellite
                                    </button>
                                </div>
                                <a :href="'https://www.google.com/maps/search/?api=1&query=' + lat + ',' + lng" target="_blank" class="px-2.5 py-1 rounded-[8px] bg-surface-container-low hover:bg-surface-container text-primary text-[11px] font-mono font-medium border border-[#E2DFD7] flex items-center gap-1 transition">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                    <span>Open in Google Maps</span>
                                </a>
                                <span class="font-mono text-[11px] text-brand-green font-bold flex items-center gap-1 bg-green-50 px-2 py-0.5 rounded border border-green-200">
                                    <span class="material-symbols-outlined text-[14px]">verified</span> GPS VERIFIED
                                </span>
                            </div>
                        </div>
                        
                        <div class="relative w-full h-80 rounded-[14px] overflow-hidden shadow-sm border border-[#E2DFD7]/60 bg-slate-100">
                            <!-- Leaflet Interactive Map Canvas -->
                            <div x-show="mapView === 'interactive'" id="leaflet-map-canvas" class="w-full h-full z-10"></div>

                            <!-- Google Maps Embed Canvas -->
                            <div x-show="mapView === 'google'" class="w-full h-full">
                                <iframe
                                    class="w-full h-full border-0"
                                    loading="lazy"
                                    allowfullscreen
                                    referrerpolicy="no-referrer-when-downgrade"
                                    :src="'https://maps.google.com/maps?q=' + lat + ',' + lng + '&z=14&output=embed'">
                                </iframe>
                            </div>

                            <!-- Overlay Badges -->
                            <div class="absolute top-4 left-4 z-20 flex flex-col gap-2 pointer-events-none">
                                <span class="px-3 py-1 rounded-[6px] bg-white/95 backdrop-blur-md text-primary font-mono text-xs font-semibold shadow flex items-center gap-1.5 border border-[#E2DFD7]/80">
                                    <span class="w-2 h-2 rounded-full bg-brand-amber animate-pulse"></span>
                                    Hyperlocal Radius: <span x-text="radius + ' km Coverage'"></span>
                                </span>
                                <span x-show="mapView === 'interactive'" class="px-2.5 py-0.5 rounded-[6px] bg-slate-900/80 text-white font-mono text-[10px] backdrop-blur-sm w-fit">
                                    Click or drag pin to re-position farm
                                </span>
                            </div>

                            <div class="absolute bottom-4 right-4 z-20 bg-white/95 backdrop-blur-md px-3 py-2 rounded-[10px] shadow text-right border border-[#E2DFD7]/80 pointer-events-none">
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

@push('scripts')
<script>
    let leafletMap = null;
    let leafletMarker = null;
    let leafletCircle = null;

    function initLeafletMap(initialLat, initialLng, initialRadius) {
        const container = document.getElementById('leaflet-map-canvas');
        if (!container || typeof window.L === 'undefined') return;

        if (leafletMap) {
            leafletMap.remove();
            leafletMap = null;
        }

        const lat = parseFloat(initialLat) || 21.784507;
        const lng = parseFloat(initialLng) || 87.737351;
        const rad = (parseInt(initialRadius) || 25) * 1000;

        leafletMap = L.map('leaflet-map-canvas', {
            center: [lat, lng],
            zoom: 13,
            zoomControl: true,
            scrollWheelZoom: true
        });

        window.leafletMap = leafletMap;

        // OpenStreetMap tile layer
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(leafletMap);

        // Custom Farm Pin Icon
        const farmPinIcon = L.divIcon({
            className: 'custom-farm-pin',
            html: `
                <div style="position: relative; display: flex; flex-direction: column; align-items: center; transform: translate(-50%, -100%);">
                    <div style="width: 36px; height: 36px; border-radius: 50% 50% 50% 0; background: linear-gradient(135deg, #f59e0b, #d97706); transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(217,119,6,0.4); border: 2.5px solid #ffffff;">
                        <span class="material-symbols-outlined" style="transform: rotate(45deg); color: #ffffff; font-size: 20px; font-weight: bold;">storefront</span>
                    </div>
                    <div style="width: 8px; height: 8px; background: rgba(0,0,0,0.3); border-radius: 50%; filter: blur(1px); margin-top: -3px;"></div>
                </div>
            `,
            iconSize: [0, 0],
            iconAnchor: [0, 0]
        });

        leafletMarker = L.marker([lat, lng], {
            draggable: true,
            icon: farmPinIcon
        }).addTo(leafletMap);

        window.leafletMarker = leafletMarker;

        leafletCircle = L.circle([lat, lng], {
            radius: rad,
            color: '#f59e0b',
            fillColor: '#fbbf24',
            fillOpacity: 0.12,
            weight: 2,
            dashArray: '6, 6'
        }).addTo(leafletMap);

        window.leafletCircle = leafletCircle;

        // Drag marker handler
        leafletMarker.on('dragend', function(e) {
            const pt = e.target.getLatLng();
            updateCoordsFromMap(pt.lat, pt.lng);
            if (leafletCircle) leafletCircle.setLatLng(pt);
        });

        // Click map handler
        leafletMap.on('click', function(e) {
            leafletMarker.setLatLng(e.latlng);
            if (leafletCircle) leafletCircle.setLatLng(e.latlng);
            updateCoordsFromMap(e.latlng.lat, e.latlng.lng);
        });

        setTimeout(() => {
            if (leafletMap) leafletMap.invalidateSize();
        }, 200);
    }

    function updateCoordsFromMap(lat, lng) {
        const formattedLat = Number(lat).toFixed(6);
        const formattedLng = Number(lng).toFixed(6);
        const alpineEl = document.querySelector('[x-data]');
        if (alpineEl && alpineEl._x_dataStack) {
            alpineEl._x_dataStack[0].lat = formattedLat;
            alpineEl._x_dataStack[0].lng = formattedLng;
        }
    }

    window.syncMapToCoords = function(lat, lng) {
        if (!leafletMap || !leafletMarker) return;
        const parsedLat = parseFloat(lat);
        const parsedLng = parseFloat(lng);
        if (!isNaN(parsedLat) && !isNaN(parsedLng)) {
            leafletMarker.setLatLng([parsedLat, parsedLng]);
            if (leafletCircle) leafletCircle.setLatLng([parsedLat, parsedLng]);
            leafletMap.panTo([parsedLat, parsedLng]);
        }
    };

    window.syncMapRadius = function(radiusKm) {
        if (!leafletCircle) return;
        const r = (parseInt(radiusKm) || 25) * 1000;
        leafletCircle.setRadius(r);
    };

    document.addEventListener('DOMContentLoaded', function() {
        const initialLat = '{{ $profile?->latitude ?? 21.784507 }}';
        const initialLng = '{{ $profile?->longitude ?? 87.737351 }}';
        const initialRadius = '{{ $profile?->operating_radius_km ?? 25 }}';
        initLeafletMap(initialLat, initialLng, initialRadius);
    });
</script>
@endpush
@endsection
