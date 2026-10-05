@php
    $impersonating = session('impersonating');
@endphp

@if($impersonating && auth()->guard('admin')->check())
<div class="fixed bottom-0 inset-x-0 z-[9999] bg-slate-900 text-white px-4 py-2.5 flex items-center justify-between gap-4 shadow-2xl border-t-2 border-amber-400 font-sans text-xs"
     style="font-family: 'JetBrains Mono', monospace;">

    {{-- Left: Info --}}
    <div class="flex items-center gap-3 min-w-0">
        {{-- Pulse indicator --}}
        <span class="relative flex h-3 w-3 shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-400"></span>
        </span>

        <span class="font-bold text-amber-400 uppercase tracking-wider shrink-0">Admin Preview</span>

        <span class="text-slate-400 shrink-0">|</span>

        <span class="text-white truncate">
            Viewing as
            <strong class="text-amber-300">{{ $impersonating['name'] }}</strong>
            <span class="text-slate-400">({{ $impersonating['email'] }})</span>
        </span>

        <span class="hidden sm:inline text-slate-500 shrink-0">
            · {{ ucfirst($impersonating['panel']) }} Panel ·
            Started {{ \Carbon\Carbon::parse($impersonating['started_at'])->diffForHumans() }}
        </span>
    </div>

    {{-- Right: Stop Button --}}
    <form method="POST" action="{{ route('admin.impersonate.stop') }}" class="shrink-0">
        @csrf
        <button type="submit"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold rounded-lg transition-colors text-xs whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Exit Preview → Admin
        </button>
    </form>
</div>

{{-- Add bottom padding to page so content isn't hidden behind fixed bar --}}
<style>
    body { padding-bottom: 48px !important; }
</style>
@endif
