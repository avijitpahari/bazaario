@props([
    'theme' => 'light',
    'size' => 'md',
    'showTagline' => false,
    'badge' => null,
    'link' => url('/'),
])

@php
    $siteName = \App\Models\SiteSetting::getSiteName();
    $siteTagline = \App\Models\SiteSetting::getTagline();
    $hasLogo = \App\Models\SiteSetting::hasCustomLogo();
    $logoUrl = \App\Models\SiteSetting::getLogoUrl();
    $firstChar = strtoupper(substr($siteName, 0, 1) ?: 'B');

    $emblemClasses = match($size) {
        'sm' => 'w-7 h-7 text-xs rounded-lg',
        'lg' => 'w-11 h-11 text-xl rounded-2xl',
        default => 'w-8 h-8 sm:w-9 sm:h-9 text-base rounded-xl',
    };

    $imgClasses = match($size) {
        'sm' => 'h-7 max-w-[110px]',
        'lg' => 'h-11 max-w-[180px]',
        default => 'h-8 sm:h-9 max-w-[140px]',
    };

    $nameClasses = match($size) {
        'sm' => 'text-sm font-black',
        'lg' => 'text-2xl font-black',
        default => 'text-[17px] sm:text-[19px] font-black',
    };

    $textColor = $theme === 'dark' ? 'text-white' : 'text-slate-900';
    $dotColor = $theme === 'dark' ? 'bg-amber-400 shadow-amber-400/50' : 'bg-amber-500 shadow-amber-500/50';
@endphp

<a href="{{ $link }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 group transition-transform duration-150 select-none']) }}>
    @if($hasLogo && $logoUrl)
        <!-- Uploaded Custom Brand Logo -->
        <div class="{{ $theme === 'dark' ? 'bg-white/95 px-2 py-1 rounded-xl shadow-xs border border-white/20' : '' }} flex items-center shrink-0">
            <img src="{{ $logoUrl }}" 
                 alt="{{ $siteName }}" 
                 class="{{ $imgClasses }} w-auto object-contain transition-transform duration-200 group-hover:scale-105 shrink-0 rounded-md">
        </div>
    @else
        <!-- Modernist Geometric Brand Emblem Mark -->
        <div class="{{ $emblemClasses }} bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 shadow-sm shadow-amber-500/30 flex items-center justify-center text-slate-950 font-black font-['Outfit'] tracking-tighter shrink-0 border border-amber-300/70 ring-2 ring-amber-400/20 transition-all duration-200 group-hover:scale-105 group-hover:shadow-amber-500/40">
            <span>{{ $firstChar }}</span>
        </div>
    @endif

    <!-- Styled Brand Name -->
    <div class="flex flex-col min-w-0">
        <div class="flex items-center gap-2 min-w-0">
            <span class="font-['Outfit'] {{ $nameClasses }} tracking-tight {{ $textColor }} leading-none flex items-center transition-colors group-hover:text-amber-500">
                <span class="truncate">{{ $siteName }}</span>
                <span class="inline-block w-1.5 h-1.5 rounded-full {{ $dotColor }} ml-1 mb-0.5 shrink-0 shadow-xs"></span>
            </span>

            @if($badge)
                <span class="font-mono text-[9px] font-extrabold uppercase tracking-wider px-1.5 py-0.5 rounded-md shrink-0 {{ $theme === 'dark' ? 'bg-amber-400/15 text-amber-300 border border-amber-400/30' : 'bg-amber-500/15 text-amber-900 border border-amber-500/30' }}">
                    {{ $badge }}
                </span>
            @endif
        </div>

        @if($showTagline)
            <p class="text-[11px] {{ $theme === 'dark' ? 'text-slate-400' : 'text-slate-500' }} mt-1 leading-tight font-medium truncate">
                {{ $siteTagline }}
            </p>
        @endif
    </div>
</a>
