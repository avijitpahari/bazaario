@props([
    'icon' => 'dashboard',
    'title' => 'Section Title',
    'subtitle' => '',
    'count' => null,
    'countLabel' => '',
    'class' => '',
])

<div class="px-4 py-3 border-b border-slate-200 bg-white shrink-0 {{ $class }}">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center">
            <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-heading font-bold text-sm text-slate-900 truncate">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-[11px] text-slate-500 font-sans">{{ $subtitle }}</p>
            @endif
        </div>
        @if($count !== null)
            <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                {{ $countLabel }} {{ $count }}
            </span>
        @endif
    </div>
</div>