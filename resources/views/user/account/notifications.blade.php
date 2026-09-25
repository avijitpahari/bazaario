@extends('layouts.user')

@section('title', 'Notifications — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 shadow-sm border border-white/90">
        <div>
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold">Activity Feed</span>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">Notifications</h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                {{ $unreadCount > 0 ? $unreadCount . ' unread notification(s)' : 'All caught up!' }}
            </p>
        </div>
        @if($unreadCount > 0)
        <form action="{{ route('user.notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold hover:bg-slate-authority/90 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">done_all</span>
                Mark All Read
            </button>
        </form>
        @endif
    </div>

    {{-- Notifications List --}}
    @if($notifications->isNotEmpty())
    <div class="flex flex-col gap-2">
        @foreach($notifications as $notification)
        @php
            $data = is_string($notification->data) ? json_decode($notification->data, true) : (array) $notification->data;
            $isRead = !is_null($notification->read_at);
        @endphp
        <div class="flex items-start gap-4 p-4 rounded-2xl {{ $isRead ? 'bg-surface-container-lowest/60' : 'bg-card-white/95 shadow-sm border border-white/90' }} transition-all">
            <div class="w-10 h-10 rounded-full {{ $isRead ? 'bg-surface-container' : 'bg-amber-action/15' }} flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-[20px] {{ $isRead ? 'text-on-surface-variant' : 'text-amber-action' }}" style="font-variation-settings: 'FILL' 1;">
                    notifications
                </span>
            </div>
            <div class="flex-1 min-w-0">
                @if(!empty($notification->title))
                    <h4 class="font-body-small text-body-small font-bold text-slate-authority">{{ $notification->title }}</h4>
                @endif
                <p class="font-body-small text-body-small {{ $isRead ? 'text-on-surface-variant' : 'text-slate-authority' }} mt-0.5">
                    {{ $notification->message ?? ($data['message'] ?? 'You have a new notification.') }}
                </p>
                <p class="font-label-micro text-label-micro text-on-surface-variant mt-1">
                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                </p>
            </div>
            <div class="flex-shrink-0 flex items-center gap-2">
                @if(!$isRead)
                <form action="{{ route('user.notifications.read', $notification->id) }}" method="POST">
                    @csrf
                    <button type="submit" title="Mark as read" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="flex justify-center pt-2">{{ $notifications->links() }}</div>
    @endif

    @else
    <div class="flex flex-col items-center justify-center py-20 gap-6 text-center">
        <div class="w-24 h-24 rounded-full bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant">notifications_none</span>
        </div>
        <div>
            <h2 class="font-headline-section text-headline-section font-bold text-slate-authority">No notifications yet</h2>
            <p class="font-body-regular text-body-regular text-on-surface-variant mt-2">You're all caught up! We'll notify you about orders, deals, and more.</p>
        </div>
    </div>
    @endif
</div>
@endsection