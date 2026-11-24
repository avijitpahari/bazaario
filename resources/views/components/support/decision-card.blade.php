@props([
    'value' => '', // approve_refund, reject_refund, release_escrow, custom
    'selected' => false,
    'disabled' => false,
    'icon' => '',
    'title' => '',
    'description' => '',
    'accentColor' => 'rose', // rose, emerald, blue, indigo
    'class' => '',
])

@php
    $accentClasses = match($accentColor) {
        'rose' => 'hover:border-rose-400 hover:bg-rose-50 focus:ring-rose-500',
        'emerald' => 'hover:border-emerald-400 hover:bg-emerald-50 focus:ring-emerald-500',
        'blue' => 'hover:border-blue-400 hover:bg-blue-50 focus:ring-blue-500',
        'indigo' => 'hover:border-indigo-400 hover:bg-indigo-50 focus:ring-indigo-500',
        default => 'hover:border-rose-400 hover:bg-rose-50 focus:ring-rose-500',
    };

    $iconMap = [
        'approve_refund' => 'check_circle',
        'reject_refund' => 'block',
        'release_escrow' => 'payments',
        'custom' => 'gavel',
    ];
    $icon = $icon ?: $iconMap[$value] ?? 'gavel';
@endphp

<label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 {{ $accentClasses }} cursor-pointer transition-colors {{ $selected ? 'border-2 bg-' . $accentColor . '-50' : '' }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }} {{ $class }}"
       x-on:click="$dispatch('decision-selected', { value: '{{ $value }}' })">
    <input type="radio"
           name="decision"
           value="{{ $value }}"
           class="text-{{ $accentColor }}-600 focus:ring-{{ $accentColor }}-500"
           {{ $selected ? 'checked' : '' }}
           {{ $disabled ? 'disabled' : '' }}
           aria-label="{{ $title }}">
    <span class="material-symbols-outlined text-[20px] text-{{ $accentColor }}-600 shrink-0">{{ $icon }}</span>
    <div class="flex-1 min-w-0">
        <span class="font-semibold text-xs text-slate-900 truncate block">{{ $title }}</span>
        <p class="text-[10px] text-slate-500 truncate">{{ $description }}</p>
    </div>
    @if($selected)
        <span class="material-symbols-outlined text-[20px] text-{{ $accentColor }}-600 shrink-0">check</span>
    @endif
</label>