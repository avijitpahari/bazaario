<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Bazaario</title>
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
                        'accent-glow': '0 8px 24px -4px rgba(245, 166, 35, 0.38)'
                    }
                }
            }
        }
    </script>
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

    <div class="pointer-events-none fixed -top-40 -left-40 w-96 h-96 rounded-full bg-amber-200/20 blur-3xl -z-10"></div>
    <div class="pointer-events-none fixed top-1/3 -right-40 w-[30rem] h-[30rem] rounded-full bg-orange-100/30 blur-3xl -z-10"></div>

    <!-- Navigation -->
    @include('components.nav', ['currentRoute' => Route::currentRouteName()])

    <!-- Main Content -->
    <main class="flex-1 max-w-6xl mx-auto w-full px-6 pt-8 pb-16 flex items-center justify-center">
        <div class="w-full max-w-md bg-white border border-primary/10 rounded-card p-8 sm:p-9 shadow-glass relative">

            <!-- Card Header & Badge -->
            <div class="flex items-center justify-between mb-4">
                <span class="font-mono text-xs uppercase tracking-wide text-accent font-semibold">Security Update</span>
                <div class="flex items-center gap-1.5 font-mono text-[11px] text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2.5 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Token Verified</span>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="font-display font-bold text-3xl text-primary tracking-tight mb-2">
                Set new password 🔑
            </h1>
            <p class="text-sm text-primary/70 mb-6">
                Choose a strong new password for your account (at least 8 characters).
            </p>

            <!-- Alerts -->
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

            <!-- Form -->
            <form class="space-y-4" action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block font-mono text-xs uppercase tracking-wider text-primary/70 font-semibold mb-1.5">
                        Email Address
                    </label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email', $email) }}"
                            class="w-full px-4 py-3 bg-surface border border-primary/15 rounded-card text-sm text-primary focus:outline-none focus:border-accent focus:bg-white focus:ring-2 focus:ring-accent/20 transition-all"
                            required>
                        <div class="absolute right-3.5 top-3.5 text-primary/40 pointer-events-none">
                            <span class="material-symbols-outlined text-[18px]">alternate_email</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="password" class="block font-mono text-xs uppercase tracking-wider text-primary/70 font-semibold mb-1.5">
                        New Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="At least 8 characters"
                            class="w-full px-4 py-3 bg-surface border border-primary/15 rounded-card text-sm text-primary placeholder:text-primary/40 focus:outline-none focus:border-accent focus:bg-white focus:ring-2 focus:ring-accent/20 transition-all font-mono"
                            required autofocus>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block font-mono text-xs uppercase tracking-wider text-primary/70 font-semibold mb-1.5">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password"
                            class="w-full px-4 py-3 bg-surface border border-primary/15 rounded-card text-sm text-primary placeholder:text-primary/40 focus:outline-none focus:border-accent focus:bg-white focus:ring-2 focus:ring-accent/20 transition-all font-mono"
                            required>
                    </div>
                </div>

                <button type="submit"
                    class="w-full mt-2 bg-accent hover:bg-accent-hover text-primary font-semibold text-sm py-3.5 px-6 rounded-card transition-all duration-150 shadow-accent-glow hover:shadow-lg active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Update Password</span>
                    <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-8 pt-6 border-t border-primary/10 text-center">
                <p class="text-xs text-primary/70">
                    Nevermind?
                    <a href="{{ route('login') }}"
                        class="font-bold text-primary hover:text-accent transition-colors ml-1 underline decoration-accent/40 decoration-2 underline-offset-4">
                        Back to sign in
                    </a>
                </p>
            </div>

            <!-- Trust Footer -->
            <div class="mt-6 flex items-center justify-center gap-2 text-[11px] font-mono text-primary/50">
                <span class="material-symbols-outlined text-[14px] text-emerald-600">verified_user</span>
                <span>256-BIT SSL ENCRYPTED &amp; SECURE</span>
            </div>

        </div>
    </main>

    <x-footer />

</body>

</html>
