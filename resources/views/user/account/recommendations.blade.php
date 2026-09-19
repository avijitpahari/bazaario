<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="web_standard" name="shell-type" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600&amp;family=Space+Grotesk:wght@500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <style>
        @layer base {

            html,
            body {
                margin: 0;
                padding: 0;
            }

            body {
                overscroll-behavior: none;
            }

            main>:first-child {
                margin-top: 0 !important;
            }

            main>:last-child {
                margin-bottom: 0 !important;
            }
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script
        id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { "colors": { "error-container": "#ffdad6", "on-primary-fixed": "#131b2e", "secondary-fixed-dim": "#62df7d", "secondary-fixed": "#7ffc97", "primary-fixed-dim": "#bec6e0", "primary-alpha-10": "rgba(15, 23, 42, 0.10)", "status-green": "#16A34A", "on-tertiary-fixed-variant": "#633f00", "inverse-on-surface": "#f2f1ec", "outline": "#76777d", "on-error": "#ffffff", "inverse-primary": "#bec6e0", "ivory-alpha-70": "rgba(255, 253, 248, 0.70)", "surface": "#fbf9f4", "surface-container": "#efeee9", "canvas-ivory": "#FFFDF8", "tertiary": "#000000", "slate-authority": "#0F172A", "on-error-container": "#93000a", "error": "#ba1a1a", "outline-variant": "#c6c6cd", "on-tertiary-container": "#b57700", "primary-alpha-20": "rgba(15, 23, 42, 0.20)", "on-secondary-fixed-variant": "#005320", "on-secondary-container": "#007230", "on-secondary": "#ffffff", "tertiary-fixed-dim": "#ffb955", "primary-fixed": "#dae2fd", "on-primary": "#ffffff", "surface-container-highest": "#e4e2de", "primary": "#000000", "primary-container": "#131b2e", "on-tertiary-fixed": "#291800", "on-tertiary": "#ffffff", "surface-container-lowest": "#ffffff", "surface-bright": "#fbf9f4", "primary-alpha-70": "rgba(15, 23, 42, 0.70)", "surface-dim": "#dbdad5", "tertiary-fixed": "#ffddb4", "on-surface-variant": "#45464d", "background": "#fbf9f4", "secondary": "#006e2d", "tertiary-container": "#291800", "surface-container-low": "#f5f3ee", "surface-variant": "#e4e2de", "surface-tint": "#565e74", "on-primary-container": "#7c839b", "inverse-surface": "#30312e", "on-secondary-fixed": "#002109", "on-primary-fixed-variant": "#3f465c", "card-white": "#FFFFFF", "amber-action": "#F5A623", "on-surface": "#1b1c19", "on-background": "#1b1c19", "surface-container-high": "#eae8e3", "secondary-container": "#7cf994" }, "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, "spacing": { "container-max": "72rem", "section-final-bottom": "4rem", "card-padding": "1.25rem", "section-hero-bottom": "2.5rem", "gutter-md": "1.5rem", "gutter-lg": "2.5rem", "gutter-sm": "1rem", "banner-padding": "2.5rem", "gutter-xl": "3.5rem", "section-interior-y": "3.5rem", "section-hero-top": "4rem", "gutter-xs": "0.5rem" }, "fontFamily": { "label-micro": ["JetBrains Mono"], "body-small": ["Inter"], "body-regular": ["Inter"], "display-hero": ["Space Grotesk"], "headline-section": ["Space Grotesk"], "label-eyebrow": ["JetBrains Mono"], "body-lead": ["Inter"], "button-text": ["Inter"], "display-hero-mobile": ["Space Grotesk"], "title-card": ["Space Grotesk"] }, "fontSize": { "label-micro": ["10px", { "lineHeight": "14px", "letterSpacing": "0.025em", "fontWeight": "500" }], "body-small": ["14px", { "lineHeight": "20px", "fontWeight": "400" }], "body-regular": ["16px", { "lineHeight": "24px", "fontWeight": "400" }], "display-hero": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }], "headline-section": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700" }], "label-eyebrow": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "500" }], "body-lead": ["18px", { "lineHeight": "28px", "fontWeight": "400" }], "button-text": ["16px", { "lineHeight": "24px", "fontWeight": "600" }], "display-hero-mobile": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700" }], "title-card": ["18px", { "lineHeight": "24px", "letterSpacing": "-0.01em", "fontWeight": "600" }] } } } };</script>
</head>

<body
    class="bg-canvas-ivory text-on-surface antialiased selection:bg-amber-action selection:text-slate-authority min-h-screen flex flex-col">
    <header
        class="fixed top-0 left-0 right-0 w-full z-50 bg-ivory-alpha-70 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
        <div class="h-20 max-w-container-max mx-auto px-gutter-md flex items-center justify-between gap-gutter-sm">
            <div class="flex items-center gap-gutter-md"><a class="flex items-center gap-3 group" data-path="home"
                    href="#"><span
                        class="font-display-hero text-headline-section font-bold tracking-tight text-slate-authority group-hover:text-amber-action transition-colors">Bazaario</span></a>
                <nav class="hidden xl:flex items-center gap-1"
                    data-active-classes="bg-surface-container text-on-surface font-semibold rounded-lg"><a
                        class="px-3 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="home" href="#">Home</a><a
                        class="px-3 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="shop" href="#">Shop</a><a
                        class="px-3 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="categories" href="#">Categories</a><a
                        class="px-3 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="auctions" href="#">Auctions</a><a
                        class="px-3 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="user-account-auctions" href="#">My Bids &amp; Auctions</a><a
                        class="px-3 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="ai-compare" href="#">AI Compare</a></nav>
            </div>
            <div class="flex items-center gap-2.5 sm:gap-3">
                <div class="relative hidden md:flex items-center"><span
                        class="material-symbols-outlined absolute left-3 text-[20px] text-on-surface-variant pointer-events-none">search</span><input
                        class="w-48 lg:w-60 pl-9 pr-3 py-1.5 rounded-full bg-surface-container-low text-body-small font-body-small text-slate-authority placeholder:text-on-surface-variant/60 focus:outline-none focus:ring-1 focus:ring-amber-action border-none"
                        placeholder="Search market..." type="text" /></div><button aria-label="Notifications"
                    class="relative p-2.5 rounded-full hover:bg-surface-container-high transition-colors text-slate-authority flex items-center justify-center"
                    data-path="notifications" type="button"><span
                        class="material-symbols-outlined text-[22px]">notifications</span><span
                        class="absolute top-2 right-2 w-2 h-2 rounded-full bg-amber-action ring-2 ring-card-white"></span></button><a
                    aria-label="Wishlist"
                    class="relative p-2.5 rounded-full hover:bg-surface-container-high transition-colors text-slate-authority flex items-center justify-center"
                    data-path="wishlist" href="#"><span
                        class="material-symbols-outlined text-[22px]">favorite</span><span
                        class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 rounded-full bg-amber-action text-slate-authority font-label-micro text-label-micro flex items-center justify-center font-bold">12</span></a><a
                    aria-label="Cart"
                    class="relative p-2.5 rounded-full hover:bg-surface-container-high transition-colors text-slate-authority flex items-center justify-center"
                    data-path="cart" href="#"><span
                        class="material-symbols-outlined text-[22px]">shopping_bag</span><span
                        class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 rounded-full bg-slate-authority text-canvas-ivory font-label-micro text-label-micro flex items-center justify-center font-bold">3</span></a>
                <div class="h-6 w-[1px] bg-primary-alpha-10 mx-0.5 hidden sm:block"></div><a
                    class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-full bg-card-white shadow-[0_1px_4px_rgba(0,0,0,0.04)] hover:bg-surface-container-low transition-colors"
                    data-path="user-profile" href="#"><img alt="Profile" class="w-8 h-8 rounded-full object-cover"
                        src="https://lh3.googleusercontent.com/aida/AEtjO1XSJITP3lGfyftjWK-KyQdkr_p-dkjB55PjIN7euT8WuESTgjD3lPkAVAUXxlFIjVJPBz9Oqm2cKZ_x9x3S_amnhGmh900sc5ueTNsDEcNpP93YxqAkt1txHQPaxSo6QEOdfMl0VwK1kInjDJv-PlIFHTQi_ymjzclU6QdqVlntqPIYbPl96vON-MAHiiu67gshPxDEtQbgZyyE8fCJ0BPrIQVNzhnbyuckQwvYyDNRcbUonyAzkarFSyqzC63lc4cgjr8Z0R0_qQ" />
                    <div class="flex flex-col text-left"><span
                            class="font-button-text text-body-small font-semibold text-slate-authority leading-tight">Avijit</span><span
                            class="inline-flex items-center gap-1 font-label-micro text-label-micro text-status-green font-medium leading-none"><span
                                class="w-1.5 h-1.5 rounded-full bg-status-green"></span>Verified</span></div>
                </a>
            </div>
        </div>
    </header>
    <main class="w-full pt-20 bg-canvas-ivory flex-1">
        <div class="flex flex-col w-full">
            <!-- Subtle Ambient Glow Orbs -->
            <div class="relative w-full overflow-hidden">
                <div
                    class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-amber-action/5 blur-3xl pointer-events-none">
                </div>
                <div
                    class="absolute top-96 -left-32 w-96 h-96 rounded-full bg-status-green/5 blur-3xl pointer-events-none">
                </div>
                <div class="max-w-container-max mx-auto px-gutter-md pt-6 pb-section-final-bottom flex flex-col gap-10">
                    <!-- 1. SUB-HEADER TELEMETRY & ESCROW STATUS BAR -->
                    <section
                        class="flex flex-wrap items-center justify-between gap-4 py-3 px-5 rounded-xl bg-surface-container-lowest/80 backdrop-blur-md shadow-sm">
                        <div class="flex flex-wrap items-center gap-3">
                            <a class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-high/60 text-slate-authority hover:bg-surface-container-high transition-colors font-body-small text-body-small font-medium"
                                data-path="user-account" href="#">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Back to Account
                            </a>
                            <div class="h-4 w-[1px] bg-primary-alpha-10 hidden sm:block"></div>
                            <nav aria-label="Breadcrumb"
                                class="hidden sm:flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant font-mono">
                                <a class="hover:text-slate-authority transition-colors" href="#">Home</a>
                                <span>/</span>
                                <a class="hover:text-slate-authority transition-colors" href="#">Account</a>
                                <span>/</span>
                                <span class="text-slate-authority font-semibold">Recommendations</span>
                            </nav>
                        </div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-mono">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-status-green opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-status-green"></span>
                                </span>
                                <span class="font-semibold tracking-wide">ESCROW VERIFIED</span>
                                <span class="opacity-60 hidden md:inline">• VAULT #ESC-9041 • UID: BZ-891-IN</span>
                            </div>
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container-low text-slate-authority font-label-micro text-label-micro font-mono">
                                <span class="material-symbols-outlined text-[15px] text-amber-action">lock</span>
                                <span class="font-medium">Vault:</span>
                                <span class="font-semibold text-slate-authority">₹60,500.00</span>
                                <button aria-label="Vault settings"
                                    class="hover:text-amber-action transition-colors flex items-center">
                                    <span class="material-symbols-outlined text-[14px]">settings</span>
                                </button>
                            </div>
                        </div>
                    </section>
                    <!-- 2. HERO PAGE HEADER & AI PREDICTIVE CONCIERGE ENGINE -->
                    <section class="flex flex-col gap-6">
                        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                            <div class="flex flex-col gap-2 max-w-3xl">
                                <div
                                    class="flex items-center gap-2 font-label-eyebrow text-label-eyebrow text-amber-action font-mono uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-sm bg-amber-action"></span>
                                    <span>ACCOUNT MANAGEMENT • BAZAARIO AI CONCIERGE • 98.4% MATCH ACCURACY</span>
                                </div>
                                <h1 class="font-display-hero text-display-hero text-slate-authority tracking-tight">
                                    ✨ Recommended For You
                                </h1>
                                <p class="font-body-lead text-body-lead text-on-surface-variant max-w-2xl">
                                    AI-powered personalized market picks based on your recent acoustic browsing, bid
                                    habits, and escrow transaction history.
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <button
                                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-card-white shadow-sm hover:bg-surface-container-low text-slate-authority font-button-text text-button-text font-semibold transition-all">
                                    <span
                                        class="material-symbols-outlined text-[20px] text-on-surface-variant">tune</span>
                                    Tune AI Preferences
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-button-text font-semibold shadow-md hover:opacity-95 active:scale-[0.99] transition-all"
                                    id="refreshModelBtn">
                                    <span class="material-symbols-outlined text-[20px]">auto_awesome</span>
                                    Refresh AI Model
                                </button>
                            </div>
                        </div>
                        <!-- Category Matrix Filter Tabs -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none pt-2">
                            <button
                                class="px-4 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-body-small text-body-small font-semibold shrink-0 shadow-sm flex items-center gap-1.5">
                                <span>All Picks</span>
                                <span
                                    class="font-label-micro text-label-micro px-1.5 py-0.5 rounded-full bg-white/20">24</span>
                            </button>
                            <button
                                class="px-4 py-2 rounded-xl bg-card-white hover:bg-surface-container-low text-slate-authority font-body-small text-body-small font-medium shrink-0 transition-colors flex items-center gap-1.5">
                                <span>🎧 Audiophile Core</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant">8</span>
                            </button>
                            <button
                                class="px-4 py-2 rounded-xl bg-card-white hover:bg-surface-container-low text-slate-authority font-body-small text-body-small font-medium shrink-0 transition-colors flex items-center gap-1.5">
                                <span>⌚ Smart Wearables</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant">6</span>
                            </button>
                            <button
                                class="px-4 py-2 rounded-xl bg-card-white hover:bg-surface-container-low text-slate-authority font-body-small text-body-small font-medium shrink-0 transition-colors flex items-center gap-1.5">
                                <span>⌨ Mechanical Keyboards</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant">4</span>
                            </button>
                            <button
                                class="px-4 py-2 rounded-xl bg-card-white hover:bg-surface-container-low text-slate-authority font-body-small text-body-small font-medium shrink-0 transition-colors flex items-center gap-1.5">
                                <span>🔨 Rare Auctions</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant">6</span>
                            </button>
                        </div>
                    </section>
                    <!-- 3. PRIMARY HERO CURATION: BECAUSE YOU LIKED HEADPHONES -->
                    <section class="flex flex-col gap-6">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-4 rounded-xl bg-surface-container-low">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-lg bg-amber-action/15 flex items-center justify-center text-amber-action">
                                    <span class="material-symbols-outlined text-[24px]">headphones</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="font-title-card text-title-card font-semibold text-slate-authority">
                                            Because you inspected Headphones</h2>
                                        <span
                                            class="font-label-micro text-label-micro px-2 py-0.5 rounded-full bg-status-green/10 text-status-green font-mono font-medium">99%
                                            Acoustic Match</span>
                                    </div>
                                    <p class="font-body-small text-body-small text-on-surface-variant">Curated for
                                        high-resolution LDAC playback, balanced mids, and active noise control.</p>
                                </div>
                            </div>
                            <a class="inline-flex items-center gap-1 font-body-small text-body-small font-semibold text-slate-authority hover:text-amber-action transition-colors"
                                href="#">
                                <span>View All 8 Audio Items</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                        </div>
                        <!-- 4-Card Horizontal Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                            <!-- Card 1: Sony WH-1000XM5 -->
                            <div
                                class="flex flex-col bg-card-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                <div class="relative w-full aspect-square bg-surface-container-low overflow-hidden">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        data-alt="Sony WH-1000XM5 wireless premium noise cancelling headphones resting on a minimalist sculptural curved walnut wood desk stand in a warm sunlight room with book shelves and leather journal in the background"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCssfJ9RirhiIhAhkb0wa7gqskVJz9SPxAk74kv73rxvm9F9r9BmqOCymwUVWNbagBdQ3KTfw-AD94BXUU4EgIb94qRjvVSJVjggU2hSWOOVDHj0YlTnKCRrN9vNTf8NdeLrF3StzCCZGBiHnPHKSf2gKYcOPxXEFgiDAabyakGHuHpnc4qZ35mLplYosxsR87q53RzCIyH0ZvC-2m3f4_jGzOiUD21pzed7MoR4IgBrxeNhvECkUEs" />
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-slate-authority text-canvas-ivory font-label-micro text-label-micro font-mono tracking-wider font-semibold">
                                            BEST SOUND MATCH 🎧
                                        </span>
                                    </div>
                                    <button aria-label="Toggle wishlist"
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-sm shadow-sm flex items-center justify-center text-slate-authority hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[18px]"
                                            style="font-variation-settings: 'FILL' 1;">favorite</span>
                                    </button>
                                    <div
                                        class="absolute bottom-3 left-3 right-3 flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-authority/85 backdrop-blur-md text-canvas-ivory font-label-micro text-label-micro font-mono">
                                        <span>99.4% AI Match</span>
                                        <span class="text-status-green">In Stock (12)</span>
                                    </div>
                                </div>
                                <div class="p-card-padding flex flex-col flex-1 justify-between gap-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1 text-amber-action">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-slate-authority font-bold">4.8</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-on-surface-variant">(1,420)</span>
                                            </div>
                                            <span
                                                class="font-label-micro text-label-micro px-2 py-0.5 rounded bg-surface-container-high text-slate-authority font-mono">LDAC
                                                / ANC</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                            Sony WH-1000XM5 Wireless Headphones
                                        </h3>
                                        <div class="flex items-baseline gap-2">
                                            <span
                                                class="font-headline-section text-headline-section font-bold text-slate-authority">₹2,499</span>
                                            <span
                                                class="font-body-small text-body-small text-on-surface-variant/60 line-through">₹3,299</span>
                                            <span
                                                class="font-label-micro text-label-micro font-mono text-status-green font-bold bg-status-green/10 px-1.5 py-0.5 rounded">24%
                                                OFF</span>
                                        </div>
                                        <div
                                            class="p-2.5 rounded-lg bg-surface-container-low text-on-surface-variant font-body-small text-[13px] leading-snug">
                                            <span class="text-amber-action font-semibold">Why AI:</span> Dual-processor
                                            active noise cancellation &amp; custom 30mm driver matches your profile.
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 pt-2">
                                        <button
                                            class="w-full py-2.5 px-4 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold flex items-center justify-center gap-2 hover:opacity-95 active:scale-[0.99] transition-all">
                                            <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                            Add to Cart
                                        </button>
                                        <button
                                            class="w-full py-2 px-4 rounded-lg bg-surface-container-high/60 hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-medium transition-colors">
                                            Compare Specs
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 2: Apple Watch Ultra 2 -->
                            <div
                                class="flex flex-col bg-card-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                <div class="relative w-full aspect-square bg-surface-container-low overflow-hidden">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        data-alt="Apple Watch Ultra 2 with vibrant bright orange rugged ocean band placed on a light natural ash wood tabletop next to a modern ceramic cup"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuB9tEBYoN6VVWNTrVWOVUQyooyg0badBXqK0VVN7PPzHlVGgjvWs3m8SwIexeyL4Cvrxigkis1mqhSrtFyDOoSIsIJ2Zod0UP-FBXEuXnkoUR8IjMZ2iJLIu8BHTgzjo8CVQ2puuV55qt3MpfiIcfKVXjqDZEAK-NzP7df4sXoRlOWNp0AWj_8LWp1GpvZxbFAx91FBUUUncYlPV9eECkzkEjA2cDoWegYlQd9VhNtf3rCx8bbEUctg" />
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-amber-action text-slate-authority font-label-micro text-label-micro font-mono tracking-wider font-semibold">
                                            FREQUENTLY BOUGHT TOGETHER ⌚
                                        </span>
                                    </div>
                                    <button aria-label="Toggle wishlist"
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-sm shadow-sm flex items-center justify-center text-slate-authority hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                                    </button>
                                    <div
                                        class="absolute bottom-3 left-3 right-3 flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-authority/85 backdrop-blur-md text-canvas-ivory font-label-micro text-label-micro font-mono">
                                        <span>96.8% AI Match</span>
                                        <span class="text-status-green">Verified Seller</span>
                                    </div>
                                </div>
                                <div class="p-card-padding flex flex-col flex-1 justify-between gap-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1 text-amber-action">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-slate-authority font-bold">4.9</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-on-surface-variant">(840)</span>
                                            </div>
                                            <span
                                                class="font-label-micro text-label-micro px-2 py-0.5 rounded bg-surface-container-high text-slate-authority font-mono">GPS
                                                + Cellular</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                            Apple Watch Ultra 2 Titanium
                                        </h3>
                                        <div class="flex items-baseline gap-2">
                                            <span
                                                class="font-headline-section text-headline-section font-bold text-slate-authority">₹3,999</span>
                                            <span
                                                class="font-body-small text-body-small text-on-surface-variant/60 line-through">₹4,999</span>
                                            <span
                                                class="font-label-micro text-label-micro font-mono text-status-green font-bold bg-status-green/10 px-1.5 py-0.5 rounded">20%
                                                OFF</span>
                                        </div>
                                        <div
                                            class="p-2.5 rounded-lg bg-surface-container-low text-on-surface-variant font-body-small text-[13px] leading-snug">
                                            <span class="text-amber-action font-semibold">Why AI:</span> Seamless
                                            pairing with audio ecosystem and rugged titanium design for active
                                            listeners.
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 pt-2">
                                        <button
                                            class="w-full py-2.5 px-4 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold flex items-center justify-center gap-2 hover:opacity-95 active:scale-[0.99] transition-all">
                                            <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                            Add to Cart
                                        </button>
                                        <button
                                            class="w-full py-2 px-4 rounded-lg bg-surface-container-high/60 hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-medium transition-colors">
                                            Compare Specs
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 3: Sony WF-1000XM5 ANC Earbuds -->
                            <div
                                class="flex flex-col bg-card-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                <div class="relative w-full aspect-square bg-surface-container-low overflow-hidden">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        data-alt="Matte black wireless ANC earbuds in their open charging case illuminated by soft warm golden studio lighting on a dark slate surface"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuA5tAfSqMOsl62Gdb4hjlNPQKkG_us3QGxtGi6m6MALGNCCM2d_hpgV920ECDKh7oKxCthZIYsVQgXeEhTiboYXxbe45tsfQ4RD6izcwsfgrrapJEGSyLSQ07ySWA9DkqJzV04DK31DczfwB8b9ULZQUe-H0lIMHyMsPdKVSitJnGT1owZfCI7_nqupeEoKbMuxGlLHejV4ab8Czi80LXbVu5lgnwrqKsNcz2puP-RoOq275vDkyecZ" />
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-surface-container-high text-slate-authority font-label-micro text-label-micro font-mono tracking-wider font-semibold">
                                            PORTABLE ALTERNATIVE ⚡
                                        </span>
                                    </div>
                                    <button aria-label="Toggle wishlist"
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-sm shadow-sm flex items-center justify-center text-slate-authority hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                                    </button>
                                    <div
                                        class="absolute bottom-3 left-3 right-3 flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-authority/85 backdrop-blur-md text-canvas-ivory font-label-micro text-label-micro font-mono">
                                        <span>94.2% AI Match</span>
                                        <span class="text-amber-action">High Demand</span>
                                    </div>
                                </div>
                                <div class="p-card-padding flex flex-col flex-1 justify-between gap-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1 text-amber-action">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-slate-authority font-bold">4.7</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-on-surface-variant">(890)</span>
                                            </div>
                                            <span
                                                class="font-label-micro text-label-micro px-2 py-0.5 rounded bg-surface-container-high text-slate-authority font-mono">IPX4
                                                / 24H</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                            Sony WF-1000XM5 ANC Earbuds
                                        </h3>
                                        <div class="flex items-baseline gap-2">
                                            <span
                                                class="font-headline-section text-headline-section font-bold text-slate-authority">₹1,899</span>
                                            <span
                                                class="font-body-small text-body-small text-on-surface-variant/60 line-through">₹2,499</span>
                                            <span
                                                class="font-label-micro text-label-micro font-mono text-status-green font-bold bg-status-green/10 px-1.5 py-0.5 rounded">24%
                                                OFF</span>
                                        </div>
                                        <div
                                            class="p-2.5 rounded-lg bg-surface-container-low text-on-surface-variant font-body-small text-[13px] leading-snug">
                                            <span class="text-amber-action font-semibold">Why AI:</span> Ultra-compact
                                            IPX4 build for mobile listening with identical dual-chip processing.
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 pt-2">
                                        <button
                                            class="w-full py-2.5 px-4 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold flex items-center justify-center gap-2 hover:opacity-95 active:scale-[0.99] transition-all">
                                            <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                            Add to Cart
                                        </button>
                                        <button
                                            class="w-full py-2 px-4 rounded-lg bg-surface-container-high/60 hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-medium transition-colors">
                                            Compare Specs
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 4: Studio Monitor Wireless Over-Ear -->
                            <div
                                class="flex flex-col bg-card-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                <div class="relative w-full aspect-square bg-surface-container-low overflow-hidden">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        data-alt="High-end reference studio open-back headphones with brushed gunmetal grilles resting on an acoustic sound-treated workspace with mixing console lights in soft bokeh"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBMLbsHmYA9yB3fFlVr-msncpVQKuoORtKBgg3Ibk_TUD4H6AdcNbnew6ZgibxC8zRZ8ZrYKKuZD96Ll-5Ev4Xh4O4yrGbxq19XrwkMYdOxN9XHJ1vBMnBa5oHQCDpKzo2TmEWvUmna0_5doB1v_vCG1lU616j5wKAtAgUrO8eWupgmRnJGK8H6YoONvdxX-VAvbyAD5q7DnzwBqzPAlo4UkhraZdg6oqQ0VwNCvGD_e2AgupNqMeRC" />
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-status-green/15 text-status-green font-label-micro text-label-micro font-mono tracking-wider font-semibold">
                                            STUDIO GRADE ✦
                                        </span>
                                    </div>
                                    <button aria-label="Toggle wishlist"
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-sm shadow-sm flex items-center justify-center text-slate-authority hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                                    </button>
                                    <div
                                        class="absolute bottom-3 left-3 right-3 flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-authority/85 backdrop-blur-md text-canvas-ivory font-label-micro text-label-micro font-mono">
                                        <span>92.1% AI Match</span>
                                        <span class="text-status-green">Direct Studio Hub</span>
                                    </div>
                                </div>
                                <div class="p-card-padding flex flex-col flex-1 justify-between gap-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1 text-amber-action">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-slate-authority font-bold">4.9</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-on-surface-variant">(430)</span>
                                            </div>
                                            <span
                                                class="font-label-micro text-label-micro px-2 py-0.5 rounded bg-surface-container-high text-slate-authority font-mono">Hi-Res
                                                Audio</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                            Studio Monitor Wireless Over-Ear
                                        </h3>
                                        <div class="flex items-baseline gap-2">
                                            <span
                                                class="font-headline-section text-headline-section font-bold text-slate-authority">₹4,299</span>
                                            <span
                                                class="font-body-small text-body-small text-on-surface-variant/60 line-through">₹5,199</span>
                                            <span
                                                class="font-label-micro text-label-micro font-mono text-status-green font-bold bg-status-green/10 px-1.5 py-0.5 rounded">17%
                                                OFF</span>
                                        </div>
                                        <div
                                            class="p-2.5 rounded-lg bg-surface-container-low text-on-surface-variant font-body-small text-[13px] leading-snug">
                                            <span class="text-amber-action font-semibold">Why AI:</span> High-impedance
                                            acoustic tuning and neutral frequency response preferred by 82% of
                                            audiophiles.
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 pt-2">
                                        <button
                                            class="w-full py-2.5 px-4 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold flex items-center justify-center gap-2 hover:opacity-95 active:scale-[0.99] transition-all">
                                            <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                            Add to Cart
                                        </button>
                                        <button
                                            class="w-full py-2 px-4 rounded-lg bg-surface-container-high/60 hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-medium transition-colors">
                                            Compare Specs
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- 4. SECOND SECTION: TRENDING FOR YOU & COLLABORATIVE FILTER -->
                    <section class="flex flex-col gap-6">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-4 rounded-xl bg-surface-container-low">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-lg bg-slate-authority text-canvas-ivory flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[24px]">hub</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="font-title-card text-title-card font-semibold text-slate-authority">
                                            Trending In Your Affinity Groups</h2>
                                        <span
                                            class="font-label-micro text-label-micro px-2 py-0.5 rounded-full bg-surface-container-high text-slate-authority font-mono font-medium">Updated
                                            4 mins ago</span>
                                    </div>
                                    <p class="font-body-small text-body-small text-on-surface-variant">Curated from live
                                        bid clusters, artisan mechanical setups, and high-frequency sellers.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-micro text-label-micro font-mono text-on-surface-variant">Cluster:
                                    #AudioHifi-DeskRig</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                            <!-- Card 1: Mechanical Keyboard -->
                            <div
                                class="flex flex-col bg-card-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                <div class="relative w-full aspect-square bg-surface-container-low overflow-hidden">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        data-alt="Custom mechanical keyboard with vintage cream keycaps and warm yellow backlight sitting on an oak wood desk beside a matching coiled aviator cable"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAvOl1nyr0waU9v490KDp5mvYd7kDaguZHpmjGsEIIpg09jToUiq-UvO75YdqyMoixQKaHLvB-vhHvAFlzGpbvVodiWSCCeLzEyT6pWTSdLXitoWz8zCNuJK_B5nv4RU26BRlHUdi7VA5M7P29D9KHR8utHQBw9a35hTQgrVaL73jig9GYdd9b8Qx7aFHIIswcgO_075Ng0ISMBuGjJI4idvJ_JPEqVwyelcq0vxtSfwHQ2_X3ObvrL" />
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-surface-container-high text-slate-authority font-label-micro text-label-micro font-mono tracking-wider font-semibold">
                                            TRENDING IN DESK SETUP ⌨
                                        </span>
                                    </div>
                                    <button aria-label="Toggle wishlist"
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-sm shadow-sm flex items-center justify-center text-slate-authority hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                                    </button>
                                </div>
                                <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1 text-amber-action">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-slate-authority font-bold">4.8</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-on-surface-variant">(620)</span>
                                            </div>
                                            <span
                                                class="font-label-micro text-label-micro font-mono text-status-green bg-status-green/10 px-1.5 py-0.5 rounded">Hot
                                                Drop</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                            Retro Cream Mechanical Keyboard
                                        </h3>
                                        <p class="font-body-small text-body-small text-on-surface-variant line-clamp-2">
                                            Gasket-mounted hot-swap PCB with custom lubricated linear switches.</p>
                                    </div>
                                    <div class="flex items-center justify-between pt-2">
                                        <span
                                            class="font-headline-section text-headline-section font-bold text-slate-authority">₹2,199</span>
                                        <button
                                            class="py-2 px-4 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 transition-opacity">
                                            View Stall
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 2: Vintage Submariner Watch (Auction) -->
                            <div
                                class="flex flex-col bg-card-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                <div class="relative w-full aspect-square bg-surface-container-low overflow-hidden">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        data-alt="Vintage luxury steel diver watch with black ceramic dial and patina lume markers lying on rich brown leather background under dramatic directional lighting"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDWkNoxYM4goz7fqoninNG4OFPgwAUYH59wXf0Z5t3_y4R9PsHV_SJ82Y0Bd2J33dXIusTUzYbjJgoDr6_itgdfdjdX-x0NYWJkkG272qorSKZ6abwrUu96vmsAoWprFNhw0os2DeQqMrGQC31S8QtJbRL8eH1RHzs75CjjSiKMZ-MwHQwFPZvbaJdQozqKX1UEv1-CtBw2r86Derk-Z7Y17lC7BuQxPUOj7YkrWliMcx4V3m6iwaCc" />
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-error text-card-white font-label-micro text-label-micro font-mono tracking-wider font-semibold">
                                            LIVE AUCTION 🔨
                                        </span>
                                    </div>
                                    <div
                                        class="absolute bottom-3 left-3 right-3 flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-authority/90 backdrop-blur-md text-canvas-ivory font-label-micro text-label-micro font-mono">
                                        <span class="flex items-center gap-1"><span
                                                class="w-2 h-2 rounded-full bg-error animate-pulse"></span>⏱ 01:22:10
                                            Left</span>
                                        <span class="text-amber-action font-semibold">18 Bids Placed</span>
                                    </div>
                                </div>
                                <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="font-label-micro text-label-micro font-mono text-on-surface-variant uppercase">Lot
                                                #4029 • Certified Mint</span>
                                            <span
                                                class="font-label-micro text-label-micro font-mono text-slate-authority font-bold">1982
                                                Vintage</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                            Vintage Submariner Oyster Mechanical Watch
                                        </h3>
                                        <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1">
                                            Original box, papers, verified authenticity by Bazaario Vault.</p>
                                    </div>
                                    <div class="flex items-center justify-between pt-2">
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant block">Current
                                                Bid</span>
                                            <span
                                                class="font-headline-section text-headline-section font-bold text-slate-authority">₹9,500</span>
                                        </div>
                                        <button
                                            class="py-2 px-4 rounded-lg bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold hover:bg-slate-authority/90 transition-colors">
                                            Place Bid
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 3: Nvidia GeForce RTX 4090 GPU -->
                            <div
                                class="flex flex-col bg-card-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                <div class="relative w-full aspect-square bg-surface-container-low overflow-hidden">
                                    <img alt="High-end graphics card Nvidia GeForce RTX 4090 gaming GPU with glowing RGB accents"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        src="https://lh3.googleusercontent.com/aida/AEtjO1VcEt-0AZNPrBEwdI7wCSEJnspqCX0ETwTjhq-TkMcWBPyzqUJ6kKVKaPSq6SnGLgnEfIvK8Bkculq9vwuujae_hmTnytWq2E-a0YLja-YTT7WnEV6dIJ_8yjGxiepfFLkPuctf49BMVJqUwOYxsUpTmph6d0bnQpdewwAoFLBqa9lzaLghqGdRoJAAhLiQuVBFZrlLAqA_RHVZ0_HIN1kYXb3GjTj6KDAiRiJOPRVUspwGXmwpERMHEYM" />
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-surface-container-high text-slate-authority font-label-micro text-label-micro font-mono tracking-wider font-semibold">
                                            HIGH POWER HARDWARE ⚡
                                        </span>
                                    </div>
                                    <button aria-label="Toggle wishlist"
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-sm shadow-sm flex items-center justify-center text-slate-authority hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                                    </button>
                                </div>
                                <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1 text-amber-action">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-slate-authority font-bold">5.0</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-on-surface-variant">(112)</span>
                                            </div>
                                            <span
                                                class="font-label-micro text-label-micro font-mono text-status-green bg-status-green/10 px-1.5 py-0.5 rounded">Verified
                                                HW</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                            Nvidia GeForce RTX 4090 OC 24GB
                                        </h3>
                                        <p class="font-body-small text-body-small text-on-surface-variant line-clamp-2">
                                            Ada Lovelace architecture, extreme cooling solution, factory sealed.</p>
                                    </div>
                                    <div class="flex items-center justify-between pt-2">
                                        <span
                                            class="font-headline-section text-headline-section font-bold text-slate-authority">₹32,000</span>
                                        <button
                                            class="py-2 px-4 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 transition-opacity">
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Card 4: Artisan Ceramic Coffee Pour-Over Set -->
                            <div
                                class="flex flex-col bg-card-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                <div class="relative w-full aspect-square bg-surface-container-low overflow-hidden">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        data-alt="Handcrafted ceramic matte stone pour over dripper and glass coffee carafe on a sunny kitchen counter with warm natural light"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuA2ReAsGBrMc2HAPhziOH-MDaeR3rPBY_ewPoZ9I5H_TPv9_qnf4oi7k_GMq-JH6_EWD3aQkJtPxGwc4AWx-mFB8SeHgPIU_1KJzflLEYmLwrOheUSwWh9V8MQI2hThFVxbT5p9O5yWE4GVx21Q_9E8TYST0_YEKRbR6uaJb3NP_eYB6Y29udlfyBvNDRprpL7K79lW26_XCCTwHZekRwD43i28ZKiS2zJwj4bp7ERuqp1PxvNQlmrw" />
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-surface-container-high text-slate-authority font-label-micro text-label-micro font-mono tracking-wider font-semibold">
                                            COMMUNITY FAVORITE ☕
                                        </span>
                                    </div>
                                    <button aria-label="Toggle wishlist"
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-sm shadow-sm flex items-center justify-center text-slate-authority hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                                    </button>
                                </div>
                                <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1 text-amber-action">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-slate-authority font-bold">4.9</span>
                                                <span
                                                    class="font-label-micro text-label-micro font-mono text-on-surface-variant">(310)</span>
                                            </div>
                                            <span
                                                class="font-label-micro text-label-micro font-mono text-slate-authority bg-surface-container px-1.5 py-0.5 rounded">Handmade</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                            Artisan Ceramic Coffee Pour-Over Set
                                        </h3>
                                        <p class="font-body-small text-body-small text-on-surface-variant line-clamp-2">
                                            Hand-thrown stoneware with custom spiral extraction grooves.</p>
                                    </div>
                                    <div class="flex items-center justify-between pt-2">
                                        <span
                                            class="font-headline-section text-headline-section font-bold text-slate-authority">₹1,299</span>
                                        <button
                                            class="py-2 px-4 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 transition-opacity">
                                            View Stall
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- 5. AI SHOPPING ENGINE INSIGHTS BANNER (SIGNATURE 3D DARK CONTAINER) -->
                    <section
                        class="rounded-xl bg-slate-authority text-canvas-ivory p-card-padding sm:p-banner-padding shadow-xl relative overflow-hidden">
                        <div
                            class="absolute -right-16 -bottom-16 w-80 h-80 rounded-full bg-amber-action/10 blur-3xl pointer-events-none">
                        </div>
                        <div
                            class="absolute -left-16 -top-16 w-80 h-80 rounded-full bg-status-green/10 blur-3xl pointer-events-none">
                        </div>
                        <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-8 z-10">
                            <div class="flex flex-col gap-3 max-w-2xl">
                                <div
                                    class="inline-flex items-center gap-2 font-label-eyebrow text-label-eyebrow text-amber-action font-mono uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[18px]">psychology</span>
                                    <span>Bazaario AI Predictive Engine v4.8</span>
                                </div>
                                <h2
                                    class="font-headline-section text-headline-section font-bold tracking-tight text-canvas-ivory">
                                    Recommendations adapt dynamically to your taste and bid patterns.
                                </h2>
                                <p class="font-body-regular text-body-regular text-canvas-ivory/70 leading-relaxed">
                                    Every time you inspect specs, place an escrow bid, or save a product, your acoustic
                                    and hardware weights calibrate. Every single merchant is fully escrow-verified and
                                    community-backed.
                                </p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <span
                                        class="font-label-micro text-label-micro text-canvas-ivory/60 font-mono">Suggested
                                        AI query:</span>
                                    <button
                                        class="px-3 py-1.5 rounded-full bg-white/10 hover:bg-white/15 text-canvas-ivory font-label-micro text-label-micro font-mono transition-colors text-left">
                                        ✦ "Find wireless earbuds with best microphone under ₹3,000"
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0">
                                <button
                                    class="px-8 py-3.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-button-text font-semibold shadow-md hover:opacity-95 active:scale-[0.99] transition-all text-center">
                                    Personalize Feed
                                </button>
                                <a class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-canvas-ivory font-button-text text-body-small font-medium text-center transition-colors"
                                    data-path="ai-compare" href="#">
                                    Open AI Compare Studio
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <!-- Notification Toast for Micro-interaction -->
            <div class="fixed bottom-6 right-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none"
                id="aiToast">
                <div
                    class="px-4 py-3 rounded-xl bg-slate-authority text-canvas-ivory shadow-2xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-status-green text-[20px]">check_circle</span>
                    <span class="font-body-small text-body-small font-medium">Bazaario AI Model refreshed with latest
                        market weights!</span>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const refreshBtn = document.getElementById('refreshModelBtn');
                    const toast = document.getElementById('aiToast');

                    if (refreshBtn && toast) {
                        refreshBtn.addEventListener('click', () => {
                            const icon = refreshBtn.querySelector('.material-symbols-outlined');
                            if (icon) icon.classList.add('animate-spin');

                            setTimeout(() => {
                                if (icon) icon.classList.remove('animate-spin');
                                toast.classList.remove('translate-y-24', 'opacity-0');
                                toast.classList.add('translate-y-0', 'opacity-100');

                                setTimeout(() => {
                                    toast.classList.remove('translate-y-0', 'opacity-100');
                                    toast.classList.add('translate-y-24', 'opacity-0');
                                }, 3500);
                            }, 800);
                        });
                    }
                });
            </script>
        </div>
    </main>
    <footer class="w-full bg-surface-container-low mt-auto">
        <div class="max-w-container-max mx-auto px-gutter-md py-gutter-xl flex flex-col gap-gutter-lg">
            <div
                class="flex flex-col md:flex-row items-center justify-between gap-gutter-md p-card-padding rounded-xl bg-card-white shadow-[0_1px_8px_rgba(0,0,0,0.02)]">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-status-green/10 flex items-center justify-center text-status-green">
                        <span class="material-symbols-outlined text-[28px]">verified_user</span></div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-title-card text-title-card font-semibold text-slate-authority">Bazaario
                                Escrow Guarantee</h3><span
                                class="font-label-micro text-label-micro bg-status-green/10 text-status-green rounded-full px-2 py-0.5 font-medium">100%
                                Protected</span>
                        </div>
                        <p class="font-body-small text-body-small text-on-surface-variant mt-0.5">Funds are kept safely
                            in escrow until you inspect and approve your physical or digital delivery.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div
                        class="relative flex items-center bg-surface-container-high rounded-lg px-3 py-2 text-slate-authority">
                        <span
                            class="material-symbols-outlined text-[18px] text-on-surface-variant mr-2">attach_money</span><select
                            class="bg-transparent font-body-small text-body-small font-semibold text-slate-authority focus:outline-none cursor-pointer pr-4">
                            <option value="USD">USD ($)</option>
                            <option value="INR">INR (₹)</option>
                        </select></div>
                    <div
                        class="relative flex items-center bg-surface-container-high rounded-lg px-3 py-2 text-slate-authority">
                        <span
                            class="material-symbols-outlined text-[18px] text-on-surface-variant mr-2">language</span><select
                            class="bg-transparent font-body-small text-body-small font-semibold text-slate-authority focus:outline-none cursor-pointer pr-4">
                            <option value="EN">English</option>
                            <option value="HI">Hindi</option>
                            <option value="ES">Español</option>
                        </select></div>
                </div>
            </div>
            <div class="flex flex-col md:flex-row items-center justify-between gap-gutter-sm text-center md:text-left">
                <p class="font-body-small text-body-small text-on-surface-variant">© 2025 Bazaario Inc. Authentic
                    community marketplace.</p>
                <div class="flex flex-wrap items-center justify-center gap-6"><a
                        class="font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="privacy-policy" href="#">Privacy Policy</a><a
                        class="font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="terms-of-service" href="#">Terms of Service</a><a
                        class="font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="market-rules" href="#">Market Rules</a></div>
            </div>
        </div>
    </footer>
</body>

</html>