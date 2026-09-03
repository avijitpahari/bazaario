@extends('layouts.admin')

@section('title', 'Branding & Store Identity — Bazaario HQ')
@section('page-title', 'Global Platform Branding & Customization')

@section('content')
<div class="space-y-8" x-data="{
    siteName: 'Bazaario',
    tagline: 'Multi-Vendor Bazaar',
    announcement: 'Over 450+ independent verified stalls shipping across the nation',
    showAnnouncement: true,
    previewDevice: 'desktop',
    accentColor: '#4f46e5',
    savedToast: false
}">

    <!-- Toast Notification -->
    <div x-show="savedToast" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-slate-950 px-5 py-3.5 rounded-2xl shadow-2xl font-bold text-xs flex items-center gap-2.5">
        <i class="fa-solid fa-palette text-base"></i> Branding updated! Applied site-wide across all buyer & seller pages.
    </div>

    <!-- 1. Live Interactive Real-Time Simulator (Section 4.6) -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Live Real-Time Storefront Simulator
                </span>
                <p class="text-xs text-slate-500 mt-0.5">Preview header branding changes across different viewport sizes.</p>
            </div>
            
            <div class="flex items-center gap-2 bg-slate-900 border border-slate-800 p-1 rounded-2xl text-xs font-bold">
                <button @click="previewDevice = 'desktop'" :class="previewDevice === 'desktop' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-display"></i> Desktop
                </button>
                <button @click="previewDevice = 'mobile'" :class="previewDevice === 'mobile' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-mobile-screen"></i> Mobile
                </button>
            </div>
        </div>

        <!-- Simulator Viewport Box -->
        <div class="p-4 sm:p-6 rounded-3xl bg-slate-900/60 border-2 border-dashed border-slate-800 flex justify-center">
            
            <!-- Desktop Simulator -->
            <div x-show="previewDevice === 'desktop'" class="w-full bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden text-slate-900 transition-all duration-300">
                
                <!-- Simulated Announcement Bar -->
                <div x-show="showAnnouncement" class="bg-slate-900 text-slate-300 text-[11px] py-1.5 px-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span x-text="announcement"></span>
                    </span>
                    <span class="text-amber-400 font-bold">Open your own stall &rarr;</span>
                </div>

                <!-- Simulated Navigation Bar -->
                <div class="px-6 py-4 flex items-center justify-between gap-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-amber-500 flex items-center justify-center text-white shadow-md">
                            <i class="fa-solid fa-bag-shopping text-base"></i>
                        </div>
                        <div>
                            <span class="text-xl font-bold font-heading text-slate-900 tracking-tight block" x-text="siteName || 'Bazaario'"></span>
                            <span class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider -mt-1 block" x-text="tagline || 'Multi-Vendor Bazaar'"></span>
                        </div>
                    </div>

                    <!-- Search Bar in Simulator -->
                    <div class="flex-1 max-w-md mx-4">
                        <div class="flex items-center rounded-xl bg-slate-50 border border-slate-200 px-3 py-1.5 text-xs text-slate-400">
                            <i class="fa-solid fa-magnifying-glass mr-2"></i>
                            <span>Search 10,000+ unique products, stalls, artisans...</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-xs font-bold text-slate-700">
                        <span class="hover:text-indigo-600 cursor-pointer">Explore Stalls</span>
                        <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 cursor-pointer">Seller Hub</span>
                        <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center">
                            <i class="fa-solid fa-cart-shopping text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Simulator -->
            <div x-show="previewDevice === 'mobile'" x-cloak class="w-full max-w-sm bg-white rounded-3xl shadow-2xl border-4 border-slate-800 overflow-hidden text-slate-900 transition-all duration-300">
                <div class="p-3 bg-slate-900 text-white text-[10px] flex items-center justify-between">
                    <span class="truncate" x-text="announcement"></span>
                </div>
                <div class="p-3.5 flex items-center justify-between border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-amber-500 flex items-center justify-center text-white text-xs">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                        <span class="font-bold font-heading text-sm text-slate-900" x-text="siteName || 'Bazaario'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-bars"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. Branding Configuration Form -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 text-xs text-slate-300">
        <h3 class="text-base font-bold font-heading text-white border-b border-slate-800 pb-3">Platform Identity Configurations</h3>

        <form @submit.prevent="savedToast = true; setTimeout(() => savedToast = false, 3500)" class="space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-200">Marketplace Brand Name</label>
                    <input type="text" x-model="siteName" class="w-full px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white font-semibold focus:outline-none focus:border-indigo-500">
                    <span class="text-[10px] text-slate-500">Rendered in header, emails, invoices & metadata title tags</span>
                </div>

                <div class="space-y-1.5">
                    <label class="font-bold text-slate-200">Tagline / Sub-heading</label>
                    <input type="text" x-model="tagline" class="w-full px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white font-semibold focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="font-bold text-slate-200">Top Header Announcement Bar Text</label>
                    <label class="flex items-center gap-1.5 text-slate-400 cursor-pointer">
                        <input type="checkbox" x-model="showAnnouncement" checked class="w-3.5 h-3.5 rounded text-indigo-600">
                        <span class="text-[10px]">Show Bar</span>
                    </label>
                </div>
                <input type="text" x-model="announcement" class="w-full px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Logo & Favicon Assets -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-200 block">Header Vector Logo</span>
                            <span class="text-[10px] text-slate-500">SVG or transparent PNG (40x40px)</span>
                        </div>
                    </div>
                    <button type="button" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold">Replace</button>
                </div>

                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-shapes"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-200 block">Browser Favicon</span>
                            <span class="text-[10px] text-slate-500">32x32px .ico or .png</span>
                        </div>
                    </div>
                    <button type="button" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold">Replace</button>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/25 transition">
                    Save Branding Configurations
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
