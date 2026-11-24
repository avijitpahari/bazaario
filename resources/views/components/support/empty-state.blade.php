@props([
    'icon' => 'inbox', // material-symbols-outlined icon name
    'title' => 'No items found',
    'description' => '',
    'action' => null, // ['label' => '', 'url' => '']
    'class' => '',
])

<div class="support-empty {{ $class }}">
    <div class="support-empty-icon">
        <span class="material-symbols-outlined text-3xl">{{ $icon }}</span>
    </div>
    <h4 class="support-empty-title">{{ $title }}</h4>
    @if($description)
        <p class="support-empty-desc">{{ $description }}</p>
    @endif
    @if($action)
        <a href="{{ $action['url'] }}" class="mt-3 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-heading font-semibold text-xs transition-colors inline-flex items-center gap-1">
            {{ $action['label'] }}
            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
        </a>
    @endif
</div>