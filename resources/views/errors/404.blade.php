@extends('layouts.app')

@section('title', '404 — Stall or Product Not Found | Bazaario')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16 text-center">
    <div class="max-w-md space-y-6">
        <div class="relative">
            <span class="text-8xl sm:text-9xl font-black font-heading text-indigo-100 block">404</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-amber-500 text-white flex items-center justify-center text-2xl shadow-xl shadow-indigo-600/30">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900">This stall or product has moved</h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                The item or merchant page you are looking for might have been unlisted, moved, or temporarily closed.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/20 transition">
                <i class="fa-solid fa-house"></i> Return to Bazaario Home
            </a>
            <a href="{{ route('products.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-sm transition">
                <i class="fa-solid fa-compass"></i> Explore Catalog
            </a>
        </div>
    </div>
</div>
@endsection
