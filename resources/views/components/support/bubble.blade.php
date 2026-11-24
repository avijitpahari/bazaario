@props([
    'sender' => 'customer', // customer, seller, agent, bot, system
    'message' => '',
    'time' => '',
    'avatar' => true,
    'name' => '',
    'attachment' => null,
    'systemType' => null, // arbitration, escalation, status-change
    'align' => null, // 'left', 'right', 'center' - auto if null
    'maxWidth' => '85%',
])

@php
    $senderClasses = match($sender) {
        'customer' => 'support-bubble support-bubble-customer',
        'seller' => 'support-bubble support-bubble-seller',
        'agent' => 'support-bubble support-bubble-agent',
        'bot' => 'support-bubble support-bubble-bot',
        'system' => 'support-bubble support-bubble-system' . ($systemType ? ' ' . $systemType : ''),
        default => 'support-bubble support-bubble-customer',
    };

    $avatarClasses = match($sender) {
        'customer' => 'support-avatar support-avatar-sm support-avatar-customer',
        'seller' => 'support-avatar support-avatar-sm support-avatar-seller',
        'agent' => 'support-avatar support-avatar-sm support-avatar-agent',
        'bot' => 'support-avatar support-avatar-sm support-avatar-bot',
        'system' => 'support-avatar support-avatar-sm support-avatar-system',
        default => 'support-avatar support-avatar-sm support-avatar-customer',
    };

    $alignClass = $align ?? match($sender) {
        'customer', 'seller' => 'justify-start',
        'agent', 'bot', 'system' => 'justify-center',
        default => 'justify-start',
    };

    $initial = $name ? strtoupper(substr($name, 0, 1)) : '?';
@endphp

<div class="w-full flex {{ $alignClass }}" role="listitem" aria-label="{{ ucfirst($sender) }} message">
    @if($sender !== 'system' && $avatar)
        <div class="{{ $avatarClasses }} flex-shrink-0 mt-0.5">{{ $initial }}</div>
    @endif

    <div class="flex flex-col gap-1 max-w-[{{ $maxWidth }}]" {{ $sender === 'system' ? 'style="width: 100%;"' : '' }}>
        @if($sender === 'seller')
            <div class="flex items-center gap-1.5 text-[10px] font-mono font-bold text-indigo-600">
                <span>Seller Reply</span>
            </div>
        @elseif($sender === 'agent')
            <div class="flex items-center gap-1.5 text-[10px] font-mono font-bold text-blue-600">
                <span>Agent Reply</span>
            </div>
        @endif

        <div class="{{ $senderClasses }} px-3 py-2 rounded-2xl text-xs leading-relaxed shadow-sm">
            <div class="chat-markdown text-slate-800 leading-relaxed">
                {!! \Illuminate\Support\Str::markdown($message) !!}
            </div>
            @if($attachment)
                <div class="mt-2 rounded-xl overflow-hidden border border-slate-200">
                    <a href="{{ $attachment }}" target="_blank">
                        <img src="{{ $attachment }}" alt="Attachment" class="max-h-52 w-auto object-cover rounded-lg hover:opacity-95 transition-opacity">
                    </a>
                </div>
            @endif
        </div>

        @if($time)
            <span class="text-[10px] text-slate-400 font-mono {{ $sender !== 'system' ? 'ml-1' : '' }}">{{ $time }}</span>
        @endif
    </div>
</div>