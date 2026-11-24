@props([
    'type' => 'customer', // customer, seller, agent, bot, system
    'size' => 'md', // sm, md, lg, xl
    'name' => 'User',
    'status' => null, // open, progress, waiting, escalated, resolved, closed
    'srLabel' => null,
])

@php
    $sizeClasses = [
        'sm' => 'support-avatar support-avatar-sm',
        'md' => 'support-avatar support-avatar-md',
        'lg' => 'support-avatar support-avatar-lg',
        'xl' => 'support-avatar support-avatar-xl',
    ];

    $typeClasses = [
        'customer' => 'support-avatar-customer',
        'seller' => 'support-avatar-seller',
        'agent' => 'support-avatar-agent',
        'bot' => 'support-avatar-bot',
        'system' => 'support-avatar-system',
    ];

    $initial = strtoupper(substr($name, 0, $size === 'sm' ? 1 : 2));
@endphp

<div class="{{ $sizeClasses[$size] }} {{ $typeClasses[$type] }} relative"
     {{ $srLabel ? 'aria-label="' . $srLabel . '"' : '' }}>
    {{ $initial }}
    @if($status)
        <span class="support-status-dot
            support-status-dot-{{ $size === 'sm' ? 'sm' : ($size === 'lg' || $size === 'xl' ? 'lg' : 'md') }}
            support-status-{{ $status }}"
             aria-hidden="true"></span>
    @endif
</div>