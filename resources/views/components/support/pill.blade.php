@props([
    'variant' => 'default', // default, status, priority, role, escalated
    'value' => '',
    'size' => 'md', // sm, md
    'animate' => false,
    'dot' => false,
    'class' => '',
])

@php
    $sizeClass = $size === 'sm' ? 'support-pill-sm' : 'support-pill';

    $variantClasses = match($variant) {
        'status' => match($value) {
            'open' => 'bg-support-status-open-bg text-support-status-open-text border border-support-status-open/20',
            'in_progress' => 'bg-support-status-progress-bg text-support-status-progress-text border border-support-status-progress/20',
            'waiting_customer' => 'bg-support-status-waiting-customer-bg text-support-status-waiting-customer-text border border-support-status-waiting-customer/20',
            'waiting_seller' => 'bg-support-status-waiting-seller-bg text-support-status-waiting-seller-text border border-support-status-waiting-seller/20',
            'escalated' => 'bg-support-status-escalated-bg text-support-status-escalated-text border border-support-status-escalated/20' . ($animate ? ' animate-pulse' : ''),
            'resolved' => 'bg-support-status-resolved-bg text-support-status-resolved-text border border-support-status-resolved/20',
            'closed' => 'bg-support-status-closed-bg text-support-status-closed-text border border-support-status-closed/20',
            'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
            default => 'bg-slate-100 text-slate-700 border border-slate-200',
        },
        'priority' => match($value) {
            'urgent' => 'bg-support-priority-urgent-bg text-support-priority-urgent-text border border-support-priority-urgent/20',
            'high' => 'bg-support-priority-high-bg text-support-priority-high-text border border-support-priority-high/20',
            'medium' => 'bg-support-priority-medium-bg text-support-priority-medium-text border border-support-priority-medium/20',
            'low' => 'bg-support-priority-low-bg text-support-priority-low-text border border-support-priority-low/20',
            default => 'bg-slate-100 text-slate-700 border border-slate-200',
        },
        'role' => match($value) {
            'customer' => 'bg-support-role-customer-bg text-support-role-customer-text border border-support-role-customer/20',
            'seller' => 'bg-support-role-seller-bg text-support-role-seller-text border border-support-role-seller/20',
            'admin' => 'bg-support-role-admin-bg text-support-role-admin-text border border-support-role-admin/20',
            'system' => 'bg-support-role-system-bg text-support-role-system-text border border-support-role-system/20',
            default => 'bg-slate-100 text-slate-700 border border-slate-200',
        },
        'escalated' => 'bg-rose-50 text-rose-700 border border-rose-200' . ($animate ? ' animate-pulse' : ''),
        default => 'bg-slate-100 text-slate-700 border border-slate-200',
    };

    $label = match($variant) {
        'status' => ucfirst(str_replace('_', ' ', $value)),
        'priority' => ucfirst($value),
        'role' => ucfirst($value),
        'escalated' => 'Escalated',
        default => $value,
    };

    $dotColor = match($value) {
        'open', 'resolved' => 'bg-emerald-500',
        'in_progress' => 'bg-blue-500',
        'waiting_customer', 'waiting_seller', 'pending' => 'bg-amber-500',
        'escalated' => 'bg-rose-500 animate-pulse',
        'closed' => 'bg-slate-400',
        default => 'bg-slate-400',
    };
@endphp

<span class="inline-flex items-center gap-1.5 {{ $sizeClass }} {{ $variantClasses }} {{ $class }}"
      {{ $dot ? 'style="position: relative;"' : '' }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"
              aria-hidden="true"></span>
    @endif
    {{ $label }}
</span>