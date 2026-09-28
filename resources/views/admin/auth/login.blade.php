<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Portal Login — Bazaario Warm Modernist</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary-container": "#F5A623",
                        "on-primary-container": "#644000",
                    },
                    fontFamily: {
                        "display": ["'Plus Jakarta Sans'", "sans-serif"],
                        "headline-lg": ["'Plus Jakarta Sans'", "sans-serif"],
                        "headline-md": ["'Plus Jakarta Sans'", "sans-serif"],
                        "body-md": ["Inter", "sans-serif"],
                        "label-sm": ["'JetBrains Mono'", "monospace"],
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-[#0F172A] font-body-md text-slate-100 min-h-screen flex flex-col justify-between selection:bg-[#F5A623] selection:text-[#0F172A] relative overflow-x-hidden">

    <!-- Ambient Amber Lighting Gradients -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-[#F5A623]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Top Minimal Navigation -->
    <header class="w-full px-6 py-5 flex items-center justify-between border-b border-[#1E293B] relative z-10">
        <a href="{{ url('/') }}" class="flex items-center gap-2 group text-[#94A3B8] hover:text-white transition-colors text-xs font-medium">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Return to Marketplace Front</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-label-sm text-[11px] text-slate-400">Security Guard: ACTIVE</span>
        </div>
    </header>

    <!-- Main Content Center -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 relative z-10">
        <div class="w-full max-w-md">

            <!-- Card Shell -->
            <div class="bg-[#1E293B]/90 backdrop-blur-xl border border-[#334155] rounded-2xl shadow-2xl p-6 sm:p-8">
                
                <!-- Bazaario Brand Emblem -->
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 44" fill="none" class="h-10 w-auto">
                            <rect width="36" height="36" y="4" rx="10" fill="#F5A623"/>
                            <path d="M12 14H24C26.2 14 28 15.8 28 18C28 20.2 26.2 22 24 22M12 22H25C27.2 22 29 23.8 29 26C29 28.2 27.2 30 25 30H12V14Z" stroke="#0F172A" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="27" cy="11" r="3" fill="#0F172A"/>
                            <text x="46" y="29" font-family="'Space Grotesk', sans-serif" font-weight="800" font-size="22" letter-spacing="1.5" fill="#FFFFFF">BAZAARIO</text>
                            <rect x="46" y="34" width="28" height="2" rx="1" fill="#F5A623"/>
                        </svg>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#0F172A] border border-[#334155] mb-3">
                        <span class="material-symbols-outlined text-[14px] text-[#F5A623]">shield_lock</span>
                        <span class="font-label-sm text-[11px] font-bold tracking-wider text-slate-300">ADMIN CONTROL CENTER</span>
                    </div>
                    <h1 class="font-headline-lg text-2xl font-bold text-white tracking-tight">System Sign-In</h1>
                    <p class="text-xs text-slate-400 mt-1">Authenticate to access marketplace telemetry, auction rooms & payout rails.</p>
                </div>

                <!-- Session / Validation Errors -->
                @if (isset($errors) && $errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-950/40 border border-rose-500/40 flex items-start gap-3">
                        <span class="material-symbols-outlined text-rose-400 text-[20px] shrink-0 mt-0.5">error</span>
                        <div class="text-xs text-rose-200">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Demo Autofill Helper -->
                <div class="mb-5 p-3 rounded-xl bg-[#0F172A]/70 border border-[#334155]/60 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-[#F5A623]">key</span>
                        <span class="text-slate-300 font-label-sm text-[11px]">admin@bazaario.com</span>
                    </div>
                    <button type="button" onclick="autofillDemo()" class="text-[11px] font-semibold text-[#F5A623] hover:underline cursor-pointer">
                        Autofill Credentials
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block font-headline-sm text-xs font-semibold text-slate-300 mb-1.5">
                            Admin Identity (Email)
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">mail</span>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                value="{{ old('email', 'admin@bazaario.com') }}" 
                                required 
                                autofocus
                                placeholder="admin@bazaario.com"
                                class="w-full pl-10 pr-4 py-2.5 bg-[#0F172A] border border-[#334155] rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#F5A623] focus:ring-1 focus:ring-[#F5A623] transition-all font-body-md"
                            />
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block font-headline-sm text-xs font-semibold text-slate-300">
                                Security Passkey
                            </label>
                            <span class="text-[11px] text-slate-500">2FA Verified</span>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">lock</span>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                placeholder="••••••••••••"
                                value="Password123!"
                                class="w-full pl-10 pr-11 py-2.5 bg-[#0F172A] border border-[#334155] rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#F5A623] focus:ring-1 focus:ring-[#F5A623] transition-all font-body-md"
                            />
                            <button 
                                type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition-colors"
                            >
                                <span id="togglePasswordIcon" class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between py-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                id="remember" 
                                class="w-4 h-4 rounded bg-[#0F172A] border-[#334155] text-[#F5A623] focus:ring-0 focus:ring-offset-0 accent-[#F5A623]" 
                                checked
                            />
                            <span class="text-xs text-slate-400">Maintain active session (30 days)</span>
                        </label>
                    </div>

                    <!-- Sign In Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full mt-2 py-3 px-4 bg-[#F5A623] hover:bg-[#e09419] text-[#0F172A] font-headline-sm font-bold text-sm rounded-xl transition duration-150 flex items-center justify-center gap-2 shadow-lg shadow-[#F5A623]/20 active:scale-[0.99] cursor-pointer"
                    >
                        <span>Access Administrator Console</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </form>

                <!-- Security Advisory -->
                <div class="mt-6 pt-5 border-t border-[#334155]/60 flex items-start gap-2.5 text-[11px] text-slate-400">
                    <span class="material-symbols-outlined text-[16px] text-amber-400 shrink-0 mt-0.5">verified_user</span>
                    <p class="leading-relaxed">
                        Authorized personnel only. All access requests, geo-IP vectors, and escrow judgments are permanently committed to the immutable audit log.
                    </p>
                </div>
            </div>

            <!-- Footer Meta -->
            <div class="mt-6 text-center text-xs text-slate-500 flex items-center justify-center gap-4">
                <span>Bazaario Core v3.2</span>
                <span>•</span>
                <span>ISO 27001 Certified</span>
                <span>•</span>
                <span class="font-label-sm">Asia/Kolkata (IST)</span>
            </div>

        </div>
    </main>

    <!-- Bottom Status Bar -->
    <footer class="w-full px-6 py-3 border-t border-[#1E293B] text-[11px] text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 relative z-10">
        <div>
            &copy; {{ date('Y') }} Bazaario Multi-Vendor Marketplace Inc. Platform Operations.
        </div>
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                API Gateway: 99.99%
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Auction Engine: Synced
            </span>
        </div>
    </footer>

    <!-- Interactive script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        function autofillDemo() {
            document.getElementById('email').value = 'admin@bazaario.com';
            document.getElementById('password').value = 'Password123!';
        }
    </script>
</body>
</html>
