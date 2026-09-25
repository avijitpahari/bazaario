<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Bazaario</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600&amp;family=Space+Grotesk:wght@500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F172A',
                        accent: {
                            DEFAULT: '#F5A623',
                            hover: '#E09214',
                            light: '#FEF3C7',
                            muted: '#FFFBEB'
                        },
                        bg: '#FFFDF8',
                        surface: '#FAF8F4',
                        card: '#FFFFFF'
                    },
                    fontFamily: {
                        display: ['"Space Grotesk"', 'Inter', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace']
                    },
                    borderRadius: {
                        card: '14px'
                    },
                    boxShadow: {
                        glass: '0 20px 45px -12px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.04)',
                        'floating-nav': '0 12px 36px -8px rgba(15, 23, 42, 0.09), 0 2px 8px -2px rgba(15, 23, 42, 0.04)',
                        'accent-glow': '0 8px 24px -4px rgba(245, 166, 35, 0.38)'
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #FFFDF8;
            background-image:
                radial-gradient(at 0% 0%, rgba(245, 166, 35, 0.09) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(245, 166, 35, 0.06) 0px, transparent 50%),
                radial-gradient(at 85% 15%, rgba(124, 58, 237, 0.04) 0px, transparent 40%);
            background-attachment: fixed;
        }
    </style>
</head>

<body
    class="min-h-screen flex flex-col font-sans text-primary antialiased selection:bg-amber-300/30 selection:text-primary relative overflow-x-hidden">

    <!-- Ambient background glow elements -->
    <div class="pointer-events-none fixed -top-40 -left-40 w-96 h-96 rounded-full bg-amber-200/20 blur-3xl -z-10"></div>
    <div class="pointer-events-none fixed top-1/3 -right-40 w-[30rem] h-[30rem] rounded-full bg-orange-100/30 blur-3xl -z-10"></div>

    <!-- Navigation -->
    @include('components.nav', ['currentRoute' => Route::currentRouteName()])

    <!-- Main Content Grid -->
    <main class="flex-1 max-w-6xl mx-auto w-full px-6 pt-6 sm:pt-8 pb-12 flex items-center justify-center">
        <div class="w-full grid lg:grid-cols-12 gap-12 lg:gap-14 items-center">

            <!-- Left Column: Value Prop & 3D Glass Artwork (Shop smarter. Sell bigger.) -->
            <div class="lg:col-span-6 flex flex-col justify-center">

                <!-- Eyebrow Badge -->
                <div
                    class="inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wide px-3.5 py-1.5 rounded-full bg-accent/10 border border-accent/25 text-primary w-fit mb-4">
                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                    <span class="">Bazaario Marketplace Platform</span>
                </div>

                <!-- Hero Headline -->
                <h1 class="font-display font-bold text-4xl sm:text-5xl tracking-tight text-primary leading-[1.12] mb-4">
                    Shop <em class="text-accent not-italic">smarter</em>.<br>
                    Sell <em class="text-accent not-italic">bigger</em>.
                </h1>

                <p class="text-base text-primary/70 max-w-lg mb-8 leading-relaxed">
                    Welcome back to the autonomous marketplace for curated crafts, verified provenance, and real-time
                    floor auctions.
                </p>

                <!-- Feature List Checkmarks (as requested in spec) -->
                <div class="grid grid-cols-2 gap-3 mb-8">
                    <div
                        class="flex items-center gap-2.5 p-2.5 bg-white/70 border border-primary/10 rounded-card shadow-xs">
                        <div
                            class="w-6 h-6 rounded-full bg-accent/15 flex items-center justify-center text-accent flex-shrink-0">
                            <svg class="w-3.5 h-3.5 font-bold" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-primary">AI Recommendations</span>
                    </div>

                    <div
                        class="flex items-center gap-2.5 p-2.5 bg-white/70 border border-primary/10 rounded-card shadow-xs">
                        <div
                            class="w-6 h-6 rounded-full bg-accent/15 flex items-center justify-center text-accent flex-shrink-0">
                            <svg class="w-3.5 h-3.5 font-bold" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-primary">Smart Comparison</span>
                    </div>

                    <div
                        class="flex items-center gap-2.5 p-2.5 bg-white/70 border border-primary/10 rounded-card shadow-xs">
                        <div
                            class="w-6 h-6 rounded-full bg-accent/15 flex items-center justify-center text-accent flex-shrink-0">
                            <svg class="w-3.5 h-3.5 font-bold" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-primary">Live Auctions</span>
                    </div>

                    <div
                        class="flex items-center gap-2.5 p-2.5 bg-white/70 border border-primary/10 rounded-card shadow-xs">
                        <div
                            class="w-6 h-6 rounded-full bg-accent/15 flex items-center justify-center text-accent flex-shrink-0">
                            <svg class="w-3.5 h-3.5 font-bold" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-primary">Trusted Sellers</span>
                    </div>
                </div>

                <!-- 3D Dimensional Marketplace Visual Container -->
                <div
                    class="relative rounded-card border border-primary/10 bg-white/90 shadow-glass overflow-hidden p-2 group">
                    <!-- Floating Status Chips -->
                    <div
                        class="absolute top-4 left-4 z-10 bg-white/90 backdrop-blur-md border border-primary/10 rounded-full px-3 py-1 shadow-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span class="font-mono text-[11px] font-medium text-primary">0.2s Latency</span>
                        <span class="text-primary/30">|</span>
                        <span class="text-[11px] text-primary/70">Real-time floor</span>
                    </div>

                    <div
                        class="absolute top-4 right-4 z-10 bg-white/90 backdrop-blur-md border border-primary/10 rounded-full px-3 py-1 shadow-sm flex items-center gap-1.5 text-primary">
                        <span class="font-mono text-[11px] font-bold text-accent">100% Escrow</span>
                        <span class="text-[10px] text-primary/60">Protected</span>
                    </div>

                    <!-- The 3D Render Image from previous reference -->
                    <div
                        class="aspect-[16/10] sm:aspect-[16/9] w-full rounded-lg overflow-hidden bg-gradient-to-br from-amber-50/50 to-orange-50/30 relative">
                        <img src="{{ asset('images/screen.png') }}"
                            alt="Bazaario 3D Marketplace Elements"
                            class="w-full h-full object-cover object-center group-hover:scale-102 transition-transform duration-500">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-primary/20 via-transparent to-transparent opacity-60">
                        </div>
                    </div>

                    <!-- Bottom Live Ticker Banner inside image card -->
                    <div
                        class="absolute bottom-4 left-4 right-4 bg-primary/90 backdrop-blur-md rounded-lg p-2.5 px-3.5 flex items-center justify-between text-white text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-accent"></span>
                            <span class="font-mono text-[11px] tracking-wide text-white/90">14k+ Active Stores</span>
                        </div>
                        <span
                            class="bg-accent text-primary text-[10px] font-bold uppercase font-mono px-2 py-0.5 rounded">Live</span>
                    </div>
                </div>

            </div>

            <!-- Right Column: Login Card -->
            <div class="lg:col-span-6 flex justify-center lg:justify-end">
                <div
                    class="w-full max-w-md bg-white border border-primary/10 rounded-card p-8 sm:p-9 shadow-glass relative">

                    <!-- Card Header & Badge -->
                    <div class="flex items-center justify-between mb-4">
                        <span
                            class="font-mono text-xs uppercase tracking-wide text-accent font-semibold">Authentication</span>
                        <div
                            class="flex items-center gap-1.5 font-mono text-[11px] text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="">Encrypted</span>
                        </div>
                    </div>

                    <!-- Title & Subtitle -->
                    <h2 class="font-display font-bold text-3xl text-primary tracking-tight mb-2">
                        Welcome back 👋
                    </h2>
                    <p class="text-sm text-primary/70 mb-7">
                        Sign in to continue to your Bazaario account.
                    </p>

                    <!-- Flash & Validation Error Alerts -->
                    @if ($errors->any())
                        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-700 text-xs font-medium mb-4 flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-[18px] text-rose-600 shrink-0 mt-0.5">error</span>
                            <div class="flex-1">
                                @if ($errors->count() == 1)
                                    <span>{{ $errors->first() }}</span>
                                @else
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-xs font-medium mb-4 flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[18px] text-emerald-600 shrink-0">check_circle</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs font-medium mb-4 flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[18px] text-amber-600 shrink-0">info</span>
                            <span>{{ session('info') }}</span>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form class="space-y-4" action="{{ route('login') }}" method="POST">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email"
                                class="block font-mono text-xs uppercase tracking-wider text-primary/70 font-semibold mb-1.5">
                                Email Address
                            </label>
                            <div class="relative">
                                <input type="email" id="email" name="email" placeholder="you@example.com"
                                    value="{{ old('email') }}"
                                    class="w-full px-4 py-3 bg-surface border border-primary/15 rounded-card text-sm text-primary placeholder:text-primary/40 focus:outline-none focus:border-accent focus:bg-white focus:ring-2 focus:ring-accent/20 transition-all"
                                    required autofocus>
                                <div class="absolute right-3.5 top-3.5 text-primary/40 pointer-events-none">
                                    <span class="material-symbols-outlined text-[18px]">alternate_email</span>
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password"
                                class="block font-mono text-xs uppercase tracking-wider text-primary/70 font-semibold mb-1.5">
                                Password
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                    placeholder="Enter your password"
                                    class="w-full px-4 py-3 bg-surface border border-primary/15 rounded-card text-sm text-primary placeholder:text-primary/40 focus:outline-none focus:border-accent focus:bg-white focus:ring-2 focus:ring-accent/20 transition-all pr-11 font-mono"
                                    required>
                                <!-- Toggle Password Visibility -->
                                <button type="button"
                                    class="absolute right-3.5 top-3.5 text-primary/45 hover:text-primary focus:outline-none transition-colors"
                                    onclick="const p = document.getElementById('password'); const ic = document.getElementById('login-pwd-icon'); if (p.type === 'password') { p.type = 'text'; ic.textContent = 'visibility_off'; } else { p.type = 'password'; ic.textContent = 'visibility'; }"
                                    title="Toggle password visibility">
                                    <span class="material-symbols-outlined text-[18px]" id="login-pwd-icon">visibility</span>
                                </button>
                            </div>
                        </div>

                        <!-- Remember me & Forgot Password -->
                        <div class="flex items-center justify-between pt-1 pb-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-primary/20 text-accent focus:ring-accent/30 accent-accent cursor-pointer">
                                <span class="text-xs text-primary/75 font-medium">Remember me</span>
                            </label>

                            <a href="#"
                                class="text-xs font-semibold text-primary hover:text-accent transition-colors underline-offset-2 hover:underline">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Sign In Button (bg-accent text-primary as per DESIGN.md & reference) -->
                        <button type="submit"
                            class="w-full mt-2 bg-accent hover:bg-accent-hover text-primary font-semibold text-sm py-3.5 px-6 rounded-card transition-all duration-150 shadow-accent-glow hover:shadow-lg active:scale-[0.99] flex items-center justify-center gap-2">
                            <span class="">Sign In</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3">
                                </path>
                            </svg>
                        </button>

                    </form>

                    <!-- Divider & Register Link -->
                    <div class="mt-8 pt-6 border-t border-primary/10 text-center">
                        <p class="text-xs text-primary/70">
                            Don't have an account?
                            <a href="{{ route('register') }}"
                                class="font-bold text-primary hover:text-accent transition-colors ml-1 underline decoration-accent/40 decoration-2 underline-offset-4">
                                Create account
                            </a>
                        </p>
                    </div>

                    <!-- Trust Footer Badges -->
                    <div class="mt-6 flex items-center justify-center gap-2 text-[11px] font-mono text-primary/50">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        <span class="">256-BIT SSL ENCRYPTED &amp; SECURE</span>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <!-- Global Footer Bar -->
    <x-footer />




</body>

</html>