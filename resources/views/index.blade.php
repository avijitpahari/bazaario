<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Bazaario — Shop Smarter. Sell Bigger.</title>
    {{-- Favicon --}}
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600;700&amp;family=Space+Grotesk:wght@500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "slate-authority": "#0F172A",
                        "canvas-ivory": "#FFFDF8",
                        "amber-action": "#F5A623",
                        "status-green": "#16A34A",
                        "card-white": "#FFFFFF",
                        "surface-container-low": "#FAF8F4",
                        primary: "#0F172A"
                    },
                    fontFamily: {
                        display: ['"Space Grotesk"', 'Inter', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace']
                    },
                    boxShadow: {
                        glass: "0 20px 45px -12px rgba(15, 23, 42, 0.08), 0 4px 16px -2px rgba(15, 23, 42, 0.04)",
                        "glass-sm": "0 8px 30px rgba(15, 23, 42, 0.04), 0 2px 8px rgba(15, 23, 42, 0.02)",
                        "floating-nav": "0 12px 36px -8px rgba(15, 23, 42, 0.09), 0 2px 8px -2px rgba(15, 23, 42, 0.04)",
                        "accent-glow": "0 8px 24px -4px rgba(245, 166, 35, 0.38)"
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #FFFDF8;
            background-image:
                radial-gradient(at 0% 0%, rgba(245, 166, 35, 0.12) 0px, transparent 48%),
                radial-gradient(at 100% 10%, rgba(245, 166, 35, 0.08) 0px, transparent 45%),
                radial-gradient(at 50% 50%, rgba(254, 243, 199, 0.3) 0px, transparent 65%),
                radial-gradient(at 90% 90%, rgba(15, 23, 42, 0.03) 0px, transparent 40%);
            background-attachment: fixed;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.85);
            box-shadow: 0 16px 36px -10px rgba(15, 23, 42, 0.06), 0 0 0 1px rgba(15, 23, 42, 0.04);
        }

        .glass-card-nested {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.9);
        }

        .glass-dock {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.85);
        }
    </style>
</head>

<body
    class="min-h-screen flex flex-col font-sans text-slate-authority antialiased selection:bg-amber-action/30 selection:text-slate-authority relative overflow-x-hidden">
    <!-- Luminous Ambient Glow Background Accents -->
    <div
        class="pointer-events-none fixed -top-40 -left-40 w-[30rem] h-[30rem] rounded-full bg-amber-200/25 blur-3xl -z-10">
    </div>
    <div
        class="pointer-events-none fixed top-1/4 -right-40 w-[36rem] h-[36rem] rounded-full bg-orange-100/40 blur-3xl -z-10">
    </div>
    <div
        class="pointer-events-none fixed bottom-1/3 left-1/4 w-[28rem] h-[28rem] rounded-full bg-amber-100/20 blur-3xl -z-10">
    </div>
    <!-- 1. FLOATING DOCK NAVBAR -->
    <header class="fixed top-0 left-0 right-0 z-50 pt-4 sm:pt-5 px-4 sm:px-6 pointer-events-none">
        <div
            class="pointer-events-auto max-w-6xl mx-auto flex items-center justify-between glass-dock rounded-full px-5 py-2.5 shadow-floating-nav ring-1 ring-slate-authority/5 transition-all">
            <!-- Logo & Dock Navigation Links -->
            <div class="flex items-center gap-6">
                <a class="flex items-center pr-5 border-r border-slate-authority/10 group transition-transform shrink-0"
                    href="#">
                    <img alt="Bazaario"
                        class="h-7 sm:h-8 w-auto object-contain transition-transform group-hover:scale-105"
                        src="{{ asset('images/bazaario-logo.png') }}" />
                </a>
                <!-- Dock Nav Pills -->
                <nav
                    class="hidden lg:flex items-center gap-1.5 bg-surface-container-low/80 p-1 rounded-full border border-slate-authority/5">
                    <a class="px-4 py-1.5 rounded-full text-xs font-semibold bg-slate-authority text-white shadow-sm transition-all"
                        href="#shop">
                        Shop
                    </a>
                    <a class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-authority/70 hover:text-slate-authority hover:bg-white/70 transition-colors"
                        href="#auctions">
                        Auctions
                    </a>
                    <a class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-authority/70 hover:text-slate-authority hover:bg-white/70 transition-colors"
                        href="#categories">
                        Categories
                    </a>
                    <a class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-authority/70 hover:text-slate-authority hover:bg-white/70 transition-colors flex items-center gap-1.5"
                        href="#ai">
                        AI Compare
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-action animate-pulse"></span>
                    </a>
                    <a class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-authority/70 hover:text-slate-authority hover:bg-white/70 transition-colors"
                        href="#sell">
                        Sell
                    </a>
                    <a class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-authority/70 hover:text-slate-authority hover:bg-white/70 transition-colors"
                        href="#about">
                        About
                    </a>
                    <a class="px-3 py-1.5 rounded-full text-xs font-medium text-slate-authority/60 hover:text-slate-authority flex items-center gap-0.5 transition-colors"
                        href="#developers">
                        <span>Developers</span>
                        <span class="material-symbols-outlined text-[14px]">expand_more</span>
                    </a>
                </nav>
            </div>
            <!-- Right Action Pills -->
            <div class="flex items-center gap-3">
                <a class="text-xs font-semibold text-slate-authority/80 hover:text-slate-authority px-3 py-1.5 transition-colors hidden sm:inline"
                    href="#signin">
                    Sign in
                </a>
                <a class="bg-amber-action hover:brightness-105 text-slate-authority font-bold text-xs px-5 py-2 rounded-full transition-all shadow-md shadow-amber-action/25 active:scale-95 flex items-center gap-1.5"
                    href="#signup">
                    <span>Get started</span>
                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </header>
    <main class="flex-1 w-full pt-28 pb-16">
        <!-- 2. HERO SECTION - 3D DIMENSIONAL SHOWCASE -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 pt-4 pb-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
                <!-- Left Column: Copy & Floating Glass Search Pill -->
                <div class="lg:col-span-6 flex flex-col items-start z-10">
                    <!-- Eyebrow Badge -->
                    <div
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-action/15 border border-amber-action/30 mb-5 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-action animate-pulse"></span>
                        <span class="font-mono text-[11px] uppercase text-slate-authority font-bold tracking-wider">✦
                            BAZAARIO MARKETPLACE PLATFORM ✦</span>
                    </div>
                    <!-- Headline -->
                    <h1
                        class="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl text-slate-authority tracking-tight leading-[1.1] mb-4">
                        Shop <span
                            class="text-amber-action underline decoration-amber-action/30 underline-offset-4">smarter</span>.<br />
                        Sell <span
                            class="text-amber-action underline decoration-amber-action/30 underline-offset-4">bigger</span>.
                    </h1>
                    <p class="text-base sm:text-lg text-slate-authority/70 max-w-xl mb-7 leading-relaxed font-sans">
                        Curated products, live auctions, AI-powered recommendations and a trusted community — all in one
                        place.
                    </p>
                    <!-- Floating Glass Search Pill -->
                    <div
                        class="w-full max-w-xl glass-panel rounded-2xl p-1.5 pl-4 shadow-glass flex items-center gap-2 ring-1 ring-slate-authority/5 mb-3 transition-all hover:border-amber-action/50">
                        <span class="material-symbols-outlined text-slate-authority/45 text-[20px]">search</span>
                        <input
                            class="w-full bg-transparent border-none text-slate-authority text-xs sm:text-sm placeholder:text-slate-authority/45 focus:outline-none focus:ring-0 px-1 py-1.5"
                            placeholder="Search curated products, auctions, sellers..." type="text" />
                        <div class="hidden sm:flex items-center border-l border-slate-authority/10 pl-2">
                            <span
                                class="font-mono text-[10px] bg-surface-container-low text-slate-authority/60 px-2 py-1 rounded-md border border-slate-authority/10">All
                                Categories</span>
                        </div>
                        <button
                            class="bg-amber-action hover:brightness-105 text-slate-authority w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm transition-transform active:scale-95"
                            type="button">
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    </div>
                    <!-- Popular Search Chips -->
                    <div class="flex flex-wrap items-center gap-2 font-mono text-[11px] text-slate-authority/60 pt-1">
                        <span class="font-bold text-slate-authority">Popular:</span>
                        <a class="px-2.5 py-0.5 rounded-full bg-white/70 border border-slate-authority/10 hover:border-amber-action text-slate-authority transition-colors"
                            href="#">iPhone 16 Pro</a>
                        <a class="px-2.5 py-0.5 rounded-full bg-white/70 border border-slate-authority/10 hover:border-amber-action text-slate-authority transition-colors"
                            href="#">Leica M3</a>
                        <a class="px-2.5 py-0.5 rounded-full bg-white/70 border border-slate-authority/10 hover:border-amber-action text-slate-authority transition-colors"
                            href="#">Mechanical Keys</a>
                        <a class="px-2.5 py-0.5 rounded-full bg-white/70 border border-slate-authority/10 hover:border-amber-action text-slate-authority transition-colors"
                            href="#">Sneakers</a>
                    </div>
                </div>
                <!-- Right Column: 3D Dimensional Visual Card with HUD Badges -->
                <div class="lg:col-span-6 relative">
                    <div
                        class="relative glass-panel rounded-3xl p-3 shadow-glass group overflow-hidden border border-white">
                        <div
                            class="relative aspect-[16/11] w-full rounded-2xl overflow-hidden bg-gradient-to-br from-amber-50/50 to-orange-50/30">
                            <!-- Official 3D Isometric Artwork IMAGE_26 -->
                            <img alt="Bazaario 3D Marketplace Platform"
                                class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
                                src="{{ asset('images/screen.png') }}" />
                            <!-- Floating HUD Badge 1 (Top-Left) -->
                            <div
                                class="absolute top-3.5 left-3.5 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-full border border-slate-authority/10 shadow-md flex items-center gap-2 pointer-events-none">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                                <span class="font-mono text-[11px] text-slate-authority font-bold">0.2s Latency</span>
                                <span class="font-mono text-[10px] text-slate-authority/60 hidden sm:inline">· Real-time
                                    floor</span>
                            </div>
                            <!-- Floating HUD Badge 2 (Top-Right) -->
                            <div
                                class="absolute top-3.5 right-3.5 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-full border border-slate-authority/10 shadow-md flex items-center gap-1.5 pointer-events-none">
                                <span
                                    class="material-symbols-outlined text-[15px] text-amber-action">verified_user</span>
                                <span class="font-mono text-[11px] text-slate-authority font-bold">100% Escrow</span>
                                <span class="font-mono text-[10px] text-slate-authority/60 hidden sm:inline">· Dispute
                                    Protection</span>
                            </div>
                            <!-- Floating HUD Badge 3 (Bottom-Left Live Ticker) -->
                            <div
                                class="absolute bottom-3.5 left-3.5 right-3.5 bg-slate-authority/90 text-white backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/10 shadow-lg flex items-center justify-between pointer-events-none">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-action"></span>
                                    <span class="font-mono text-[11px] font-semibold text-white/90">14k+ Active Stores
                                        Trading</span>
                                </div>
                                <span
                                    class="font-mono text-[9px] bg-amber-action text-slate-authority px-2 py-0.5 rounded font-bold uppercase tracking-wider">LIVE</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 4-Pillar Trust Glass Badges -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-10">
                <div
                    class="glass-panel rounded-2xl p-4 flex items-center gap-3 transition-transform hover:-translate-y-0.5 shadow-sm">
                    <div
                        class="w-10 h-10 rounded-xl bg-amber-action/15 text-amber-action flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">auto_awesome</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xs sm:text-sm text-slate-authority">AI Recommendations
                        </h4>
                        <p class="text-[11px] text-slate-authority/60">Tailored to your taste</p>
                    </div>
                </div>
                <div
                    class="glass-panel rounded-2xl p-4 flex items-center gap-3 transition-transform hover:-translate-y-0.5 shadow-sm">
                    <div
                        class="w-10 h-10 rounded-xl bg-orange-500/15 text-orange-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">gavel</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xs sm:text-sm text-slate-authority">Live Auctions</h4>
                        <p class="text-[11px] text-slate-authority/60">Sub-second bidding</p>
                    </div>
                </div>
                <div
                    class="glass-panel rounded-2xl p-4 flex items-center gap-3 transition-transform hover:-translate-y-0.5 shadow-sm">
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">verified_user</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xs sm:text-sm text-slate-authority">Verified Escrow</h4>
                        <p class="text-[11px] text-slate-authority/60">100% funds protection</p>
                    </div>
                </div>
                <div
                    class="glass-panel rounded-2xl p-4 flex items-center gap-3 transition-transform hover:-translate-y-0.5 shadow-sm">
                    <div
                        class="w-10 h-10 rounded-xl bg-indigo-500/15 text-indigo-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">bolt</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xs sm:text-sm text-slate-authority">Instant Payouts</h4>
                        <p class="text-[11px] text-slate-authority/60">Automated settlement</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- 3. SHOP BY CATEGORY - TACTILE GLASS TILES -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-8" id="categories">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="font-mono text-[11px] uppercase tracking-wider text-amber-action font-semibold mb-1">
                        DISCOVER MARKETPLACE</div>
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-authority tracking-tight">Shop by
                        Category</h2>
                </div>
                <a class="text-xs sm:text-sm font-bold text-slate-authority hover:text-amber-action transition-colors flex items-center gap-1 group"
                    href="#">
                    View all categories
                    <span
                        class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
            <!-- 8 Frosted Glass Category Tiles -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3.5">
                <!-- Cat 1 -->
                <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
                    href="#">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-100 to-amber-200/50 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform shadow-xs">
                        <span class="text-2xl">🎧</span>
                    </div>
                    <span class="font-display font-bold text-xs text-slate-authority">Electronics</span>
                    <span class="font-mono text-[10px] text-slate-authority/50 mt-0.5">3.2k items</span>
                </a>
                <!-- Cat 2 -->
                <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
                    href="#">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-100 to-indigo-200/50 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform shadow-xs">
                        <span class="text-2xl">🧥</span>
                    </div>
                    <span class="font-display font-bold text-xs text-slate-authority">Fashion</span>
                    <span class="font-mono text-[10px] text-slate-authority/50 mt-0.5">5.1k items</span>
                </a>
                <!-- Cat 3 -->
                <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
                    href="#">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-100 to-orange-200/50 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform shadow-xs">
                        <span class="text-2xl">🪑</span>
                    </div>
                    <span class="font-display font-bold text-xs text-slate-authority">Home &amp; Living</span>
                    <span class="font-mono text-[10px] text-slate-authority/50 mt-0.5">1.8k items</span>
                </a>
                <!-- Cat 4 -->
                <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
                    href="#">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-100 to-red-200/50 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform shadow-xs">
                        <span class="text-2xl">🚗</span>
                    </div>
                    <span class="font-display font-bold text-xs text-slate-authority">Automotive</span>
                    <span class="font-mono text-[10px] text-slate-authority/50 mt-0.5">940 items</span>
                </a>
                <!-- Cat 5 -->
                <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
                    href="#">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-200/50 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform shadow-xs">
                        <span class="text-2xl">⚽</span>
                    </div>
                    <span class="font-display font-bold text-xs text-slate-authority">Sports</span>
                    <span class="font-mono text-[10px] text-slate-authority/50 mt-0.5">1.2k items</span>
                </a>
                <!-- Cat 6 -->
                <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
                    href="#">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-100 to-rose-200/50 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform shadow-xs">
                        <span class="text-2xl">🧴</span>
                    </div>
                    <span class="font-display font-bold text-xs text-slate-authority">Beauty &amp; Care</span>
                    <span class="font-mono text-[10px] text-slate-authority/50 mt-0.5">2.4k items</span>
                </a>
                <!-- Cat 7 -->
                <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
                    href="#">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-100 to-purple-200/50 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform shadow-xs">
                        <span class="text-2xl">🧸</span>
                    </div>
                    <span class="font-display font-bold text-xs text-slate-authority">Toys &amp; Kids</span>
                    <span class="font-mono text-[10px] text-slate-authority/50 mt-0.5">850 items</span>
                </a>
                <!-- Cat 8 -->
                <a class="glass-panel hover:bg-white p-4 rounded-2xl flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group"
                    href="#">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-100 to-teal-200/50 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform shadow-xs">
                        <span class="text-2xl">📚</span>
                    </div>
                    <span class="font-display font-bold text-xs text-slate-authority">Books &amp; Media</span>
                    <span class="font-mono text-[10px] text-slate-authority/50 mt-0.5">4.6k items</span>
                </a>
            </div>
        </section>
        <!-- 4. DUAL FEATURE BANNERS - LIVE AUCTIONS & AI ASSISTANT -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-8" id="auctions">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Card 1: Live Auctions Spotlight (Deep Dark Slate Glassmorphic Card) -->
                <div
                    class="lg:col-span-6 bg-slate-authority/95 text-white rounded-3xl p-7 sm:p-8 backdrop-blur-xl border border-white/10 shadow-2xl relative overflow-hidden flex flex-col justify-between group">
                    <!-- Glow Orb -->
                    <div
                        class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-amber-action/10 blur-3xl pointer-events-none">
                    </div>
                    <!-- Top Status Bar -->
                    <div class="flex items-center justify-between mb-6 z-10">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 font-mono text-amber-action text-[11px] font-bold tracking-wider">
                            ✦ BID • WIN • SAVE
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 text-rose-400 font-mono text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            LIVE BIDDING
                        </span>
                    </div>
                    <!-- Content Grid: Copy + Vintage Camera Image IMAGE_16 -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center z-10 mb-6">
                        <div class="sm:col-span-6">
                            <h3
                                class="font-display font-bold text-2xl sm:text-3xl text-white tracking-tight leading-tight mb-2">
                                Unique items.<br />Real value.
                            </h3>
                            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed mb-4">
                                From rare Leica collectibles to mechanical watches — discover live bidding with
                                anti-sniping protection.
                            </p>
                            <a class="inline-flex items-center gap-2 bg-amber-action hover:brightness-105 text-slate-authority font-bold text-xs px-5 py-2.5 rounded-full transition-all shadow-md active:scale-95"
                                href="#auctions">
                                <span>Explore Auctions</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>
                        <!-- Vintage Camera Box with Live Timer HUD -->
                        <div
                            class="sm:col-span-6 bg-white/5 backdrop-blur-md rounded-2xl p-3 border border-white/10 shadow-lg">
                            <div class="h-32 w-full rounded-xl overflow-hidden mb-2 relative bg-slate-900">
                                <img alt="Vintage 1954 Leica M3"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1VvVeoMB_yM4xbNZiQ0VAuwZ-1YtHWDrSWnOI1lS_xjB4JG5UeZz07ncArP0Yu4a_2AvvaphE7N9a_T5f1W7_3OTy4UnAi1YYSz2uRwvzYhcr8DGTy_y_wdTzw-BsfxJPjfkhHtVRAeizHf4-q9VnCX8wdPEcaxTRySWB-SV0wEj9AoFMenH2ljB4nejhWydR9EAKJPLX_tkxSwFRJe9wxewIeffIp6D1SWFo-zqcCd_NoC86zDheBLkO4" />
                                <span
                                    class="absolute bottom-2 right-2 bg-slate-authority/90 text-white font-mono text-[10px] px-2 py-0.5 rounded border border-white/10">
                                    24 Bids
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-300 font-semibold font-display">Vintage 1954 Leica M3
                            </div>
                            <div class="flex items-center justify-between mt-1">
                                <div>
                                    <span class="font-mono text-[9px] text-slate-400 block uppercase">Current Bid</span>
                                    <span class="font-display text-lg font-bold text-amber-action">₹ 8,500</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono text-[9px] text-slate-400 block uppercase">Ends in</span>
                                    <span
                                        class="font-mono text-xs font-bold text-white flex items-center gap-1 justify-end">
                                        <span
                                            class="material-symbols-outlined text-[12px] text-amber-action">timer</span>
                                        00:14:32
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 2: AI Shopping Assistant (Warm Frosted Dimensional Card) -->
                <div class="lg:col-span-6 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-white/70 backdrop-blur-xl rounded-3xl p-7 sm:p-8 border border-amber-action/25 shadow-glass relative overflow-hidden flex flex-col justify-between"
                    id="ai">
                    <!-- Top Tag -->
                    <div class="flex items-center justify-between mb-6 z-10">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-action/15 border border-amber-action/30 font-mono text-slate-authority text-[11px] font-bold">
                            <span class="material-symbols-outlined text-[14px] text-amber-action">auto_awesome</span>
                            AI SHOPPING ASSISTANT
                        </span>
                        <span
                            class="font-mono text-[11px] text-status-green bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span> Online
                        </span>
                    </div>
                    <!-- Content Grid: AI Prompts & Mascot -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center z-10 mb-2">
                        <div class="sm:col-span-6">
                            <h3
                                class="font-display font-bold text-2xl sm:text-3xl text-slate-authority tracking-tight leading-tight mb-2">
                                Smarter choices.<br />Better buys.
                            </h3>
                            <p class="text-slate-authority/70 text-xs sm:text-sm leading-relaxed mb-4">
                                Ask anything. Our semantic agent scans verified reviews, live prices, and authentic
                                seller inventories in seconds.
                            </p>
                            <a class="inline-flex items-center gap-2 bg-slate-authority hover:bg-slate-800 text-white font-bold text-xs px-5 py-2.5 rounded-full transition-all shadow-md active:scale-95"
                                href="#ai-compare">
                                <span>Try AI Compare</span>
                                <span
                                    class="material-symbols-outlined text-[16px] text-amber-action">auto_awesome</span>
                            </a>
                        </div>
                        <!-- Floating Prompt Bubbles with Mascot -->
                        <div class="sm:col-span-6 flex flex-col items-center gap-2.5">
                            <div
                                class="bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full text-[11px] font-medium text-slate-authority shadow-xs border border-slate-authority/10 -rotate-2 w-full text-center hover:border-amber-action cursor-pointer transition-colors">
                                💬 "Best noise-canceling headphones?"
                            </div>
                            <div
                                class="w-16 h-16 rounded-2xl bg-amber-action/15 border border-amber-action/30 flex items-center justify-center text-3xl shadow-sm">
                                🤖
                            </div>
                            <div
                                class="bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full text-[11px] font-medium text-slate-authority shadow-xs border border-slate-authority/10 rotate-1 w-full text-center hover:border-amber-action cursor-pointer transition-colors">
                                ⚡ "Compare Sony WH-1000 vs AirPods"
                            </div>
                            <div
                                class="bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full text-[11px] font-medium text-slate-authority shadow-xs border border-slate-authority/10 -rotate-1 w-full text-center hover:border-amber-action cursor-pointer transition-colors">
                                🔍 "Find artisan pottery under ₹1,000"
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- 5. TRENDING PRODUCTS SHELF (Frosted Glass Tactile Cards) -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-10" id="shop">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <div class="font-mono text-[11px] uppercase tracking-wider text-amber-action font-semibold mb-1">
                        CURATED CATALOG</div>
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-authority tracking-tight">Trending
                        Products</h2>
                    <p class="text-xs sm:text-sm text-slate-authority/60 mt-0.5">Most active listings and community
                        verified finds.</p>
                </div>
                <a class="text-xs sm:text-sm font-bold text-slate-authority hover:text-amber-action transition-colors flex items-center gap-1 group"
                    href="#">
                    View all products
                    <span
                        class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
            <!-- 5 Product Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">
                <!-- Product 1: Sony Headphones IMAGE_13 -->
                <div
                    class="glass-panel rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-glass group">
                    <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container-low mb-3">
                        <span
                            class="absolute top-2.5 left-2.5 font-mono text-[10px] bg-slate-authority/90 text-amber-action px-2 py-0.5 rounded backdrop-blur-md font-bold z-10 border border-amber-action/30">
                            ✦ AI PICK
                        </span>
                        <img alt="Sony WH-1000XM5"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            src="https://lh3.googleusercontent.com/aida/AEtjO1XUZ06pzyaEtN5_aA9fTVzxbefhzbLmS2fQJwOsZ8mKMlj6oe6EYev2in_zfjaSUyDVj3OuncS_ohfIFjObj5LoY6R3kfOHGJ-UrqUOoz_FGL13SU__1lreIo1-WcXj8EYpJr1uQa2bc-Ki0AFy-Bgl03gHVPODzqjQVoV9ls8CEmx81gVosNshiqM2uta72IxjbqRSaAOy_9e4k2cbwnMgo9I665oY0wj2Giv9sgzui2-VQGZQsy9oRQ" />
                    </div>
                    <div>
                        <div
                            class="flex items-center justify-between font-mono text-[10px] text-slate-authority/60 mb-1">
                            <span>Wireless Audio</span>
                            <span class="flex items-center text-amber-action font-bold">★ 4.8</span>
                        </div>
                        <h4 class="font-display font-bold text-sm text-slate-authority line-clamp-1 mb-0.5">Studio
                            Wireless Pro</h4>
                        <p class="text-xs text-slate-authority/60 mb-3">Noise Cancelling 30h</p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-authority/5">
                            <div>
                                <span class="font-display font-bold text-base text-slate-authority">₹ 2,499</span>
                                <span class="text-[10px] text-slate-authority/40 line-through block -mt-1">₹
                                    3,299</span>
                            </div>
                            <button aria-label="Add to cart"
                                class="w-8 h-8 rounded-xl bg-amber-action hover:brightness-105 text-slate-authority flex items-center justify-center transition-transform active:scale-95 shadow-sm">
                                <span class="material-symbols-outlined text-[17px]">shopping_bag</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product 2: Retro Mechanical Keyboard IMAGE_14 -->
                <div
                    class="glass-panel rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-glass group">
                    <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container-low mb-3">
                        <span
                            class="absolute top-2.5 left-2.5 font-mono text-[10px] bg-emerald-600 text-white px-2 py-0.5 rounded backdrop-blur-md font-bold z-10">
                            NEW
                        </span>
                        <img alt="Retro Cream Keyboard"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            src="https://lh3.googleusercontent.com/aida/AEtjO1XVHYGeacZAZPQO4GUt_YXBGtpZf6MN3JIy-A5i66LItKr3wS8ELBWAmpm8ioBV1ehA32g6Awx6olpr1TNil4hzcX2CP0XFibqF2KYFIELkSeJqMEpTBTK4Pnff2StxQAEBti16U6bam4ThrFck_unUHYpqQhq_TxkeJBh2QAIT0tJ95MLz6YIyiqycAIqD0jMpnQAUtw2BfUvET28CqAF2oh4dTjm6T3mhtaoLUu0V8hGNEjQeIsyWBw" />
                    </div>
                    <div>
                        <div
                            class="flex items-center justify-between font-mono text-[10px] text-slate-authority/60 mb-1">
                            <span>Mechanical</span>
                            <span class="flex items-center text-amber-action font-bold">★ 4.9</span>
                        </div>
                        <h4 class="font-display font-bold text-sm text-slate-authority line-clamp-1 mb-0.5">Retro Cream
                            Keyboard</h4>
                        <p class="text-xs text-slate-authority/60 mb-3">Gateron Yellow Switches</p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-authority/5">
                            <div>
                                <span class="font-display font-bold text-base text-slate-authority">₹ 1,999</span>
                                <span class="text-[10px] text-slate-authority/40 line-through block -mt-1">₹
                                    2,599</span>
                            </div>
                            <button aria-label="Add to cart"
                                class="w-8 h-8 rounded-xl bg-amber-action hover:brightness-105 text-slate-authority flex items-center justify-center transition-transform active:scale-95 shadow-sm">
                                <span class="material-symbols-outlined text-[17px]">shopping_bag</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product 3: Artisan Dripper Set IMAGE_15 -->
                <div
                    class="glass-panel rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-glass group">
                    <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container-low mb-3">
                        <span
                            class="absolute top-2.5 left-2.5 font-mono text-[10px] bg-amber-action text-slate-authority px-2 py-0.5 rounded backdrop-blur-md font-bold z-10">
                            BESTSELLER
                        </span>
                        <img alt="Artisan Dripper Set"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            src="https://lh3.googleusercontent.com/aida/AEtjO1WCcusKa_eLQkRK5PEaVUVxukfThvLIqy4NdxaRCgaxdN6PFzRS0Fh0tLjz3U6x0yViLHOl59nFyO066WwEmxJ2TApt5nKGKaW3ZYD_la2-DCzqBj7CWwLFj9T4zfp3kWWovWzmn26xnmV7T-dDvFSBGLtV2zCdaUuFKtm-1EiYe0c5afE9SKL4DgZELJkWj8mdrkNQCOeVf_7mmOp4Ivv32QgyC8hFkEpDeEL5_WFZWTL7Stlw8Tww6_M" />
                    </div>
                    <div>
                        <div
                            class="flex items-center justify-between font-mono text-[10px] text-slate-authority/60 mb-1">
                            <span>Craft Pottery</span>
                            <span class="flex items-center text-amber-action font-bold">★ 4.9</span>
                        </div>
                        <h4 class="font-display font-bold text-sm text-slate-authority line-clamp-1 mb-0.5">Artisan
                            Dripper Set</h4>
                        <p class="text-xs text-slate-authority/60 mb-3">Ceramic Pour-Over &amp; Mug</p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-authority/5">
                            <div>
                                <span class="font-display font-bold text-base text-slate-authority">₹ 899</span>
                                <span class="text-[10px] text-slate-authority/40 line-through block -mt-1">₹
                                    1,199</span>
                            </div>
                            <button aria-label="Add to cart"
                                class="w-8 h-8 rounded-xl bg-amber-action hover:brightness-105 text-slate-authority flex items-center justify-center transition-transform active:scale-95 shadow-sm">
                                <span class="material-symbols-outlined text-[17px]">shopping_bag</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product 4: Nike Air Max 270 -->
                <div
                    class="glass-panel rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-glass group">
                    <div
                        class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container-low mb-3 flex items-center justify-center">
                        <span
                            class="absolute top-2.5 left-2.5 font-mono text-[10px] bg-rose-500 text-white px-2 py-0.5 rounded backdrop-blur-md font-bold z-10">
                            20% OFF
                        </span>
                        <img alt="Nike Air Max 270"
                            class="w-4/5 h-4/5 object-contain group-hover:scale-105 transition-transform duration-300"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBT7A40GAGjuVWVGdBqvoBFFxMVdgPe-fY0lwPaKbj6uRKG3VDm1BGX1ncROjUrCcqoo6xSbJPUJbE0MAczE2Onye2d2UTUZ1b6D4bSWN9248Rv_JSi8VXDc9KGlqzO30aLsJBHQG54qWnoOnEDZM7Oc8O3V1bUrTgukjbjhfX7AKjt-HesuQy31nfbod0Peo5UZMS01CFxHNtRhr85zOfxwRbGKIkIf8iLano3TtfTsysk9VT8TKmF" />
                    </div>
                    <div>
                        <div
                            class="flex items-center justify-between font-mono text-[10px] text-slate-authority/60 mb-1">
                            <span>Footwear</span>
                            <span class="flex items-center text-amber-action font-bold">★ 4.6</span>
                        </div>
                        <h4 class="font-display font-bold text-sm text-slate-authority line-clamp-1 mb-0.5">Nike Air Max
                            270</h4>
                        <p class="text-xs text-slate-authority/60 mb-3">Men's Running Edition</p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-authority/5">
                            <div>
                                <span class="font-display font-bold text-base text-slate-authority">₹ 7,999</span>
                                <span class="text-[10px] text-slate-authority/40 line-through block -mt-1">₹
                                    9,999</span>
                            </div>
                            <button aria-label="Add to cart"
                                class="w-8 h-8 rounded-xl bg-amber-action hover:brightness-105 text-slate-authority flex items-center justify-center transition-transform active:scale-95 shadow-sm">
                                <span class="material-symbols-outlined text-[17px]">shopping_bag</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product 5: Apple Watch Series 9 -->
                <div
                    class="glass-panel rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-glass group">
                    <div
                        class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container-low mb-3 flex items-center justify-center">
                        <span
                            class="absolute top-2.5 left-2.5 font-mono text-[10px] bg-slate-authority text-white px-2 py-0.5 rounded backdrop-blur-md font-bold z-10">
                            APPLE CARE+
                        </span>
                        <img alt="Apple Watch Series 9"
                            class="w-4/5 h-4/5 object-contain group-hover:scale-105 transition-transform duration-300"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAta09GcDqWN0QWl8f9g2usytPoQ2AJC4xx0lqz1Knvc0fTcyrzZk1Z0MdWkKX3R7Z8kBQUC4ETqBAirErWMekgLfGj0hYOrkeZYVACchSgJpzDAPhk7cTmejaRuMM0Y4fYONfPFM8nusu47O12vQGsA5qIQwU8hSfj9-aVBW8dtWRP2ylK1b_ipZ-0FBliaFB4dhnj1nuKnvVPZ-0-dO84M8SIdpOLxNDKvAjijfRtRaD8PCYmC5HY" />
                    </div>
                    <div>
                        <div
                            class="flex items-center justify-between font-mono text-[10px] text-slate-authority/60 mb-1">
                            <span>Smartwatches</span>
                            <span class="flex items-center text-amber-action font-bold">★ 4.8</span>
                        </div>
                        <h4 class="font-display font-bold text-sm text-slate-authority line-clamp-1 mb-0.5">Apple Watch
                            Series 9</h4>
                        <p class="text-xs text-slate-authority/60 mb-3">GPS 41mm Midnight</p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-authority/5">
                            <div>
                                <span class="font-display font-bold text-base text-slate-authority">₹ 38,900</span>
                                <span class="text-[10px] text-slate-authority/40 line-through block -mt-1">₹
                                    41,900</span>
                            </div>
                            <button aria-label="Add to cart"
                                class="w-8 h-8 rounded-xl bg-amber-action hover:brightness-105 text-slate-authority flex items-center justify-center transition-transform active:scale-95 shadow-sm">
                                <span class="material-symbols-outlined text-[17px]">shopping_bag</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- 6. WHY CHOOSE BAZAARIO - 5-PILLAR FLOATING GLASS HUB -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
            <div class="glass-panel rounded-3xl p-8 sm:p-10 shadow-glass">
                <div class="text-center max-w-xl mx-auto mb-10">
                    <div class="font-mono text-[11px] uppercase tracking-wider text-amber-action font-semibold mb-1">
                        TRUST &amp; SPEED INFRASTRUCTURE</div>
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-authority tracking-tight">Why
                        Choose Bazaario?</h2>
                    <p class="text-xs sm:text-sm text-slate-authority/60 mt-1">A transparent, high-frequency marketplace
                        engineered for genuine trade.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                    <!-- Feature 1 -->
                    <div class="flex flex-col items-center text-center group">
                        <div
                            class="w-16 h-16 rounded-2xl bg-amber-action/10 border border-amber-action/30 flex items-center justify-center text-amber-action mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px]">verified</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-authority text-sm mb-0.5">Curated Products</h4>
                        <p class="text-[11px] text-slate-authority/60">Quality vetted creators</p>
                    </div>
                    <!-- Feature 2 -->
                    <div class="flex flex-col items-center text-center group">
                        <div
                            class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px]">shield</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-authority text-sm mb-0.5">Verified Sellers</h4>
                        <p class="text-[11px] text-slate-authority/60">KYC &amp; escrow secured</p>
                    </div>
                    <!-- Feature 3 -->
                    <div class="flex flex-col items-center text-center group">
                        <div
                            class="w-16 h-16 rounded-2xl bg-amber-action/15 border border-amber-action/35 flex items-center justify-center text-slate-authority mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px] text-amber-action">psychology</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-authority text-sm mb-0.5">AI-Powered Deals</h4>
                        <p class="text-[11px] text-slate-authority/60">Predictive recommendations</p>
                    </div>
                    <!-- Feature 4 -->
                    <div class="flex flex-col items-center text-center group">
                        <div
                            class="w-16 h-16 rounded-2xl bg-orange-500/10 border border-orange-500/30 flex items-center justify-center text-orange-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px]">gavel</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-authority text-sm mb-0.5">Live Auctions</h4>
                        <p class="text-[11px] text-slate-authority/60">0.2s anti-sniping floor</p>
                    </div>
                    <!-- Feature 5 -->
                    <div class="flex flex-col items-center text-center group col-span-2 md:col-span-1">
                        <div
                            class="w-16 h-16 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-600 mb-3 group-hover:scale-110 transition-transform shadow-sm">
                            <span class="material-symbols-outlined text-[28px]">lock</span>
                        </div>
                        <h4 class="font-display font-bold text-slate-authority text-sm mb-0.5">Secure Escrow</h4>
                        <p class="text-[11px] text-slate-authority/60">100% dispute protection</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- 7. MERCHANT GROWTH BANNER -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-8" id="sell">
            <div
                class="bg-gradient-to-r from-[#fae7ce]/90 via-[#f7ecd9]/80 to-[#f4e2c8]/90 rounded-3xl p-8 sm:p-12 border border-amber-action/30 shadow-glass relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Left Text Info -->
                    <div class="lg:col-span-6 z-10">
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-action/20 text-slate-authority font-mono text-[11px] font-bold uppercase tracking-wider mb-4">
                            ✦ FOR SELLERS &amp; ARTISANS
                        </div>
                        <h2
                            class="font-display font-bold text-3xl sm:text-4xl text-slate-authority tracking-tight leading-tight mb-3">
                            Turn your passion into <span
                                class="text-amber-action underline decoration-amber-action/40">profit.</span>
                        </h2>
                        <p class="text-slate-authority/70 text-sm sm:text-base mb-7 max-w-md leading-relaxed">
                            Join thousands of independent sellers and creators reaching high-intent collectors with low
                            fees and instant escrow payouts.
                        </p>
                        <a class="inline-flex items-center gap-2 bg-amber-action hover:brightness-105 text-slate-authority font-bold text-sm px-7 py-3 rounded-full transition-all shadow-accent-glow active:scale-95"
                            href="#seller-signup">
                            <span>Start Selling</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>
                    <!-- Right Visual / Seller Snapshot -->
                    <div class="lg:col-span-6 relative flex items-center justify-center">
                        <!-- Floating Stats Box -->
                        <div
                            class="absolute -top-3 left-4 sm:left-8 bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-white/80 z-20">
                            <div class="font-display text-2xl font-black text-slate-authority leading-none">2.5K+</div>
                            <div class="font-mono text-[10px] text-slate-authority/60 uppercase mt-0.5">Active Stores
                            </div>
                            <div class="flex items-end gap-1 h-6 my-2">
                                <span class="w-2 h-2.5 bg-amber-action/30 rounded-sm"></span>
                                <span class="w-2 h-4 bg-amber-action/50 rounded-sm"></span>
                                <span class="w-2 h-3.5 bg-amber-action/70 rounded-sm"></span>
                                <span class="w-2 h-5 bg-amber-action/85 rounded-sm"></span>
                                <span class="w-2 h-6 bg-amber-action rounded-sm"></span>
                            </div>
                            <span class="font-mono text-[10px] font-bold text-status-green flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[13px]">trending_up</span> +40% this month
                            </span>
                        </div>
                        <!-- Seller Image Card -->
                        <div
                            class="w-full max-w-md h-72 sm:h-80 rounded-2xl overflow-hidden shadow-xl relative border border-white">
                            <img alt="Seller packing orders" class="w-full h-full object-cover"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAG2vRC-DtkXADNz8vvl3ncrh_0Y5DviuLk7LtnN0CfYWRRkXij0sTk2g77tz4-mles9c1EP2Unz4nQot1EjsCPtDHkv2rYodBafoBLUQbJnEVIN3rqzDIr1siwY45RJapwW_AiTl3HsxfE0e-zVNpbgv_V_8wOexctcB3SzzbadPeBiLQNENTTHCfM69fy4LSSisafqjhiYAwuzPvxrjoCqSCoVSEs5r2PdQAqk7sigESM2HeCMpD2" />
                            <div
                                class="absolute bottom-3 right-3 bg-slate-authority/85 backdrop-blur-md px-3.5 py-1.5 rounded-xl text-white font-mono text-[11px] font-semibold border border-white/10 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-amber-action text-[15px]">storefront</span>
                                Grow Your Business ✨
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- 8. TESTIMONIALS (Tactile Glass Cards) -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <div class="font-mono text-[11px] uppercase tracking-wider text-amber-action font-semibold mb-1">
                        COMMUNITY STORIES</div>
                    <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-authority tracking-tight">What Our
                        Users Say</h2>
                    <p class="text-xs sm:text-sm text-slate-authority/60 mt-0.5">Real feedback from authentic shoppers
                        and store owners.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button aria-label="Previous testimonial"
                        class="w-9 h-9 rounded-full glass-panel flex items-center justify-center text-slate-authority hover:bg-white transition-colors"
                        type="button">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <button aria-label="Next testimonial"
                        class="w-9 h-9 rounded-full glass-panel flex items-center justify-center text-slate-authority hover:bg-white transition-colors"
                        type="button">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Review 1 -->
                <div
                    class="glass-panel p-6 rounded-2xl flex flex-col justify-between shadow-sm hover:shadow-md transition-all">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <img alt="Priya Sharma"
                                class="w-11 h-11 rounded-full object-cover ring-2 ring-amber-action/50"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAZavmIuqsOS8a8QbcON7i6tlKMEtIxoPncBTuIViy24ic0S1ZyQsZ2qxli_sjFbg7OBXtfk91115GjNW8WZD8I-6vPs4wYMJ0lkOoWOWifibhGtMiv20wbDhqtduT9LZ0MrP4DZH9vb04TBVC_THplgseAWfoiPaDE5KeNIwvXPWd2m7j_pr7DIOwhAG57DuLb0RK_MHxTRSc2RNJ74Wyqt9BHcqZ1UrnfvcPERtNHsEozDeH8woEy" />
                            <div>
                                <h4 class="font-display font-bold text-sm text-slate-authority">Priya Sharma</h4>
                                <span
                                    class="font-mono text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-semibold border border-emerald-200">Verified
                                    Buyer</span>
                            </div>
                        </div>
                        <div class="text-amber-action text-xs mb-2">★★★★★</div>
                        <p class="text-xs sm:text-sm text-slate-authority/75 leading-relaxed">
                            "Amazing experience! The AI recommendations are spot on. Found an authentic hand-carved tea
                            set from an artisan in seconds."
                        </p>
                    </div>
                </div>
                <!-- Review 2 -->
                <div
                    class="glass-panel p-6 rounded-2xl flex flex-col justify-between shadow-sm hover:shadow-md transition-all">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <img alt="Rahul Verma"
                                class="w-11 h-11 rounded-full object-cover ring-2 ring-amber-action/50"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjLd15ksAmBMA0SWMtbztRg_n4AiBYcP9ZRmGbpAlOOvomPFKaZ_n1K38BDtiDSdZ_3Rfi_oR-ro5R1vYA0wpXmV3ITuEZT3HmRzZeITY_3jVCCovOIj7U6EF-WPlYaUy1RvBLalL3d57lc0Ug2KX6ZtblMIgkWLAEIQfSFwolviL-tNE8cXukTG4Np_541rWUrYnL5EU4XkpmyIi8JHS_xBmhL_FSgmtB--lSztkCgJicEN2KODmg" />
                            <div>
                                <h4 class="font-display font-bold text-sm text-slate-authority">Rahul Verma</h4>
                                <span
                                    class="font-mono text-[10px] text-amber-800 bg-amber-50 px-2 py-0.5 rounded font-semibold border border-amber-200">Top
                                    Rated Seller</span>
                            </div>
                        </div>
                        <div class="text-amber-action text-xs mb-2">★★★★★</div>
                        <p class="text-xs sm:text-sm text-slate-authority/75 leading-relaxed">
                            "Bazaario made it effortless to start selling custom mechanical keyboards. Escrow payouts
                            are instant and buyers are verified."
                        </p>
                    </div>
                </div>
                <!-- Review 3 -->
                <div
                    class="glass-panel p-6 rounded-2xl flex flex-col justify-between shadow-sm hover:shadow-md transition-all">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <img alt="Ananya Das"
                                class="w-11 h-11 rounded-full object-cover ring-2 ring-amber-action/50"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDl4FZBmRfOktxnIjQJhMwH1nwVR7v-lOKweD2ZsXDcBbfGtLZ38Cl1D6UsReefa04Y2KU5VcwwwRvIXbF3yRaMaeTa7uDTyoqxNrLAlw2QukwMrwgUZ7FUj2cSnCBuCuxoTU2742sEm8riKPKqLs9AmMBhf8-dLl9qs6qg6k-CuOENjBrfpeauZLu9Of43wXuvKFY87W768VHVfkdmmKFxd1gf6SXghP8VtGsqVYM8_158U3Ffqer5" />
                            <div>
                                <h4 class="font-display font-bold text-sm text-slate-authority">Ananya Das</h4>
                                <span
                                    class="font-mono text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-semibold border border-emerald-200">Verified
                                    Collector</span>
                            </div>
                        </div>
                        <div class="text-amber-action text-xs mb-2">★★★★★</div>
                        <p class="text-xs sm:text-sm text-slate-authority/75 leading-relaxed">
                            "The live auctions floor with 0.2s latency is addictive! Won a pristine vintage 1954 Leica
                            M3 Rangefinder at an unbelievable price."
                        </p>
                    </div>
                </div>
            </div>
        </section>
        
    </main>
    <!-- 10. FLOATING AI DOCKED BUTTON (Matching Screen 10) -->
    <div class="fixed bottom-6 right-6 z-40">
        <button
            class="bg-slate-authority text-white pl-4 pr-5 py-2.5 rounded-full font-semibold text-xs shadow-xl hover:bg-slate-800 transition-all flex items-center gap-2 ring-1 ring-white/20 active:scale-95 group"
            type="button">
            <span
                class="material-symbols-outlined text-amber-action text-[19px] group-hover:rotate-12 transition-transform">auto_awesome</span>
            <span>✦ Ask Bazaario AI</span>
        </button>
    </div>
    <!-- 11. GLOBAL GLASS FOOTER -->
    <footer
        class="w-full bg-white/70 backdrop-blur-md border-t border-slate-authority/10 pt-14 pb-8 text-xs text-slate-authority/70 relative z-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-slate-authority/10">
                <!-- Brand & Description -->
                <div class="md:col-span-4 flex flex-col items-start space-y-3">
                    <a class="flex items-center gap-2" href="#">
                        <img alt="Bazaario" class="h-7 w-auto object-contain"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDdp_HL-1dTkLF1fww5fKw9p_N0agUQ35pDzd6rLS5uSVKy2K5MfI6U7YonPH782CyD15fBYh6HQkLUK5e9UGia_cYWYdrXUSYHS7HqbWI3dFMdR5UwsyheZTmplHxn-laG7qkNjC_luOCkG02phlrpVa3uQNnSMvZiIaXM8iitchjz18GFBoFgI6jl9XQbx8zT6ZRnEp94WuiT_1efjhNGL2GnDAtr5E4ri824t7bnrzcxW5bnYnz7lQc-eCaaqE28iA" />
                    </a>
                    <p class="text-xs text-slate-authority/60 leading-relaxed max-w-xs">
                        The next-generation marketplace platform with sub-second live auctions, verified escrow, and
                        autonomous AI recommendations.
                    </p>
                    <!-- Social Badges -->
                    <div class="flex items-center space-x-2 pt-2">
                        <a aria-label="Instagram"
                            class="w-7 h-7 rounded-lg bg-surface-container-low border border-slate-authority/10 flex items-center justify-center text-slate-authority hover:bg-amber-action transition-colors text-[11px] font-mono font-bold"
                            href="#">ig</a>
                        <a aria-label="X Twitter"
                            class="w-7 h-7 rounded-lg bg-surface-container-low border border-slate-authority/10 flex items-center justify-center text-slate-authority hover:bg-amber-action transition-colors text-[11px] font-mono font-bold"
                            href="#">x</a>
                        <a aria-label="YouTube"
                            class="w-7 h-7 rounded-lg bg-surface-container-low border border-slate-authority/10 flex items-center justify-center text-slate-authority hover:bg-amber-action transition-colors text-[11px] font-mono font-bold"
                            href="#">yt</a>
                        <a aria-label="GitHub"
                            class="w-7 h-7 rounded-lg bg-surface-container-low border border-slate-authority/10 flex items-center justify-center text-slate-authority hover:bg-amber-action transition-colors text-[11px] font-mono font-bold"
                            href="#">gh</a>
                    </div>
                </div>
                <!-- Links: Shop -->
                <div class="md:col-span-2">
                    <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">Shop
                    </h5>
                    <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                        <li><a class="hover:text-amber-action transition-colors" href="#">All Products</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Live Auctions</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Categories</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">AI Compare</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Escrow Deals</a></li>
                    </ul>
                </div>
                <!-- Links: Sell -->
                <div class="md:col-span-2">
                    <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">Sell
                    </h5>
                    <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                        <li><a class="hover:text-amber-action transition-colors" href="#">Become a Seller</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Seller Guild</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Fees &amp; Commission</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Floor API</a></li>
                    </ul>
                </div>
                <!-- Links: Platform -->
                <div class="md:col-span-2">
                    <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">
                        Company</h5>
                    <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                        <li><a class="hover:text-amber-action transition-colors" href="#">About Us</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Careers</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Press Kit</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Status</a></li>
                    </ul>
                </div>
                <!-- Links: Help & Escrow -->
                <div class="md:col-span-2">
                    <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">
                        Security</h5>
                    <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                        <li><a class="hover:text-amber-action transition-colors" href="#">Escrow Guarantee</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Dispute Center</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Privacy Policy</a></li>
                        <li><a class="hover:text-amber-action transition-colors" href="#">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <!-- Bottom Bar -->
            <div
                class="pt-6 flex flex-col sm:flex-row items-center justify-between text-[11px] font-mono text-slate-authority/60 gap-4">
                <div>
                    © 2026 BAZAARIO MARKETPLACE. ALL RIGHTS RESERVED.
                </div>
                <div class="flex items-center space-x-6">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-status-green"></span> All Systems Operational
                    </span>
                    <button class="flex items-center gap-1 hover:text-slate-authority">
                        <span>🇮🇳</span> English (IN) <span>⌄</span>
                    </button>
                    <button class="flex items-center gap-1 hover:text-slate-authority">
                        INR (₹) <span>⌄</span>
                    </button>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>