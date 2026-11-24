@props([
    'items' => [], // [{label, url, count, icon, active, color}]
    'vertical' => false,
    'class' => '',
])

@php
    $baseClasses = $vertical ? 'flex flex-col gap-1' : 'flex items-center gap-1';
    $itemBaseClasses = 'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all';
@endphp

<div class="{{ $baseClasses }} {{ $class }}">
    @foreach($items as $item)
        @php
            $color = $item['color'] ?? 'slate';
            $active = $item['active'] ?? false;
            $colorMap = [
                'slate' => ['active' => 'bg-white text-slate-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
                'blue' => ['active' => 'bg-white text-blue-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
                'amber' => ['active' => 'bg-white text-amber-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
                'emerald' => ['active' => 'bg-white text-emerald-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
                'indigo' => ['active' => 'bg-white text-indigo-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
                'rose' => ['active' => 'bg-white text-rose-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
            ];
            $colors = $colorMap[$color] ?? $colorMap['slate'];
            $classes = $itemBaseClasses . ' ' . ($active ? $colors['active'] : $colors['inactive']);
            $icon = $item['icon'] ?? null;
        @endphp
        <a href="{{ $item['url'] }}" class="{{ $classes }} flex items-center {{ $vertical ? 'justify-between' : 'gap-1' }}">
            @if($icon)
                <span class="material-symbols-outlined text-[14px]">{{ $icon }}</span>
            @endif
            <span>{{ $item['label'] }}</span>
            @if(isset($item['count']))
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ $active ? 'bg-' . $color . '-100 text-' . $color . '-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ $item['count'] }}
                </span>
            @endif
        </a>
    @endforeach
</div>