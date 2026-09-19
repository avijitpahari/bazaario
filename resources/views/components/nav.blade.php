@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    $currentRoute = Route::currentRouteName();
    $currentUser = Auth::guard('user')->user() ?? Auth::guard('admin')->user() ?? Auth::user();
@endphp

<header class="fixed top-5 left-0 w-full z-50 flex justify-center px-4 pointer-events-none">
    <div class="pointer-events-auto max-w-4xl lg:max-w-6xl mx-auto w-full relative">
        <div
            class="bg-white/95 backdrop-blur-xl border border-slate-200/80 rounded-full px-6 py-2.5 shadow-[0_4px_32px_rgba(15,23,42,0.12),0_1px_4px_rgba(15,23,42,0.06)] flex items-center justify-between transition-all duration-200">

            {{-- ── Logo ── --}}
            <div class="flex items-center gap-7">
                <a class="flex items-center gap-2 group shrink-0" href="{{ url('/') }}">
                    <img alt="Bazaario"
                        class="h-10 w-auto object-contain transition-transform group-hover:scale-105"
                        src="{{ asset('images/bazaario-logo.png') }}">
                </a>
            </div>

            {{-- ── Right side ── --}}
            <div class="flex items-center gap-3.5">

                @if($currentUser)
                    {{-- ── Logged in: show profile ── --}}
                    <div class="flex items-center gap-3">

                        @php
                            $dashboardRoute = $currentUser->role === 'admin'
                                ? route('admin.dashboard')
                                : ($currentUser->role === 'seller' ? route('seller.dashboard') : route('user.dashboard'));
                        @endphp

                        <a href="{{ $dashboardRoute }}"
                            class="font-sans text-[12px] font-semibold bg-slate-900 text-white px-3.5 py-1.5 rounded-full hover:bg-slate-800 transition-colors shadow-sm">
                            Dashboard
                        </a>

                        {{-- Name --}}
                        <span class="hidden sm:block font-sans text-[13px] font-semibold text-slate-800/80">
                            {{ $currentUser->name }}
                        </span>

                        {{-- Profile picture or initials --}}
                        @if($currentUser->profile_image)
                            <img src="{{ Storage::url($currentUser->profile_image) }}"
                                alt="{{ $currentUser->name }}"
                                class="w-8 h-8 rounded-full object-cover border-2 border-amber-400/50 shadow-sm">
                        @else
                            <div class="w-8 h-8 rounded-full bg-amber-400/20 border-2 border-amber-400/40 flex items-center justify-center shadow-sm">
                                <span class="text-[13px] font-bold text-slate-800">
                                    {{ strtoupper(mb_substr($currentUser->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        {{-- Logout --}}
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="font-sans text-[13px] font-semibold text-slate-500 hover:text-red-500 transition-colors px-1">
                                Sign out
                            </button>
                        </form>
                    </div>

                @else
                    {{-- ── Guest: highlight current page button ── --}}

                    {{-- Sign in button --}}
                    <a href="{{ route('login') }}"
                        class="font-sans text-[14px] font-semibold transition-all
                            {{ $currentRoute === 'login'
                                ? 'bg-amber-400 text-slate-900 px-5 py-2 rounded-full hover:brightness-105 shadow-md shadow-amber-400/30'
                                : 'text-slate-700/80 hover:text-slate-900 px-2' }}">
                        Sign in
                    </a>

                    {{-- Get started button --}}
                    <a href="{{ route('register') }}"
                        class="font-sans text-[14px] font-semibold transition-all
                            {{ $currentRoute === 'register'
                                ? 'bg-amber-400 text-slate-900 px-5 py-2 rounded-full hover:brightness-105 shadow-md shadow-amber-400/30'
                                : 'text-slate-700/80 hover:text-slate-900 px-2' }}">
                        Get started
                    </a>

                @endif
            </div>

        </div>
    </div>
</header>