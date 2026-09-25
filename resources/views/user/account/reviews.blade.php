@extends('layouts.user')

@section('title', 'My Reviews — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Header --}}
    <div class="p-6 rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 shadow-sm border border-white/90">
        <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span> Feedback
        </span>
        <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">My Reviews</h1>
        <p class="font-body-small text-body-small text-on-surface-variant mt-1">{{ $reviews->total() }} review(s) submitted</p>
    </div>

    {{-- Reviews List --}}
    @if($reviews->isNotEmpty())
    <div class="flex flex-col gap-4">
        @foreach($reviews as $review)
        <div class="p-5 rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm border border-white/60 flex flex-col gap-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <h3 class="font-title-card text-title-card font-semibold text-slate-authority truncate">{{ $review->product?->name ?? 'Product' }}</h3>
                    <p class="font-label-micro text-label-micro text-on-surface-variant mt-0.5">
                        Reviewed {{ $review->created_at->format('d M Y') }}
                    </p>
                </div>
                {{-- Stars --}}
                <div class="flex items-center gap-0.5 flex-shrink-0">
                    @for($i = 1; $i <= 5; $i++)
                    <span class="material-symbols-outlined text-[18px] {{ $i <= $review->rating ? 'text-amber-action' : 'text-on-surface-variant/30' }}"
                        @if($i <= $review->rating) style="font-variation-settings: 'FILL' 1;" @endif>star</span>
                    @endfor
                </div>
            </div>
            @if($review->title)
            <p class="font-body-regular text-body-regular font-semibold text-slate-authority">{{ $review->title }}</p>
            @endif
            @if($review->body)
            <p class="font-body-small text-body-small text-on-surface-variant leading-relaxed">{{ $review->body }}</p>
            @endif
        </div>
        @endforeach
    </div>

    @if($reviews->hasPages())
    <div class="flex justify-center pt-2">{{ $reviews->links() }}</div>
    @endif

    @else
    <div class="flex flex-col items-center justify-center py-20 gap-6 text-center">
        <div class="w-24 h-24 rounded-full bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant">reviews</span>
        </div>
        <div>
            <h2 class="font-headline-section text-headline-section font-bold text-slate-authority">No reviews yet</h2>
            <p class="font-body-regular text-body-regular text-on-surface-variant mt-2">After you receive products, share your experience to help others.</p>
        </div>
        <a href="{{ route('user.orders.index') }}" class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold">View Orders</a>
    </div>
    @endif
</div>
@endsection