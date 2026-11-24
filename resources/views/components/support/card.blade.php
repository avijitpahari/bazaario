@props([
    'elevation' => 'surface', // surface, raised, overlay, glass
    'class' => '',
    'padded' => true,
])

@php
    $elevationClasses = match($elevation) {
        'surface' => 'support-card',
        'raised' => 'support-card-raised',
        'overlay' => 'support-card-overlay',
        'glass' => 'support-card-glass',
        default => 'support-card',
    };

    $paddingClass = $padded ? 'p-4' : '';
@endphp

<div class="{{ $elevationClasses }} {{ $paddingClass }} {{ $class }}">
    {{ $slot }}
</div>