@props([
    'type' => 'card', // card, avatar, text, text-sm
    'count' => 1,
    'class' => '',
])

@php
    $typeClasses = match($type) {
        'card' => 'support-skeleton support-skeleton-card',
        'avatar' => 'support-skeleton support-skeleton-avatar',
        'text' => 'support-skeleton support-skeleton-text',
        'text-sm' => 'support-skeleton support-skeleton-text-sm',
        default => 'support-skeleton',
    };
@endphp

<div class="{{ $class }}">
    @for($i = 0; $i < $count; $i++)
        <div class="{{ $typeClasses }} {{ $i > 0 ? 'mt-3' : '' }}"></div>
    @endfor
</div>