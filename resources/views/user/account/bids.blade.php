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
            <!-- Subtle Ambient Glows -->
            <div class="relative w-full overflow-hidden">
                <div
                    class="pointer-events-none absolute -top-40 right-10 w-96 h-96 rounded-full bg-amber-action/10 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute top-96 -left-32 w-80 h-80 rounded-full bg-status-green/10 blur-3xl">
                </div>
                <div class="max-w-container-max mx-auto px-gutter-md pt-6 pb-section-final-bottom flex flex-col gap-6">
                    <!-- Sub-nav Bar: Breadcrumb + Escrow Trust Status -->
                    <nav
                        class="flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-2xl bg-card-white/70 backdrop-blur-md shadow-sm">
                        <div class="flex flex-wrap items-center gap-3">
                            <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-container hover:bg-surface-container-high transition-colors font-button-text text-body-small text-slate-authority"
                                href="#">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Back to Account
                            </a>
                            <div class="h-4 w-px bg-slate-authority/10"></div>
                            <div
                                class="flex items-center gap-1.5 font-label-micro text-label-micro text-on-surface-variant">
                                <span>Home</span>
                                <span>/</span>
                                <span>Account</span>
                                <span>/</span>
                                <span class="text-slate-authority font-semibold">My Bids</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-ping"></span>
                                <span>ESCROW VERIFIED BUYER • VAULT #ESC-9041 • UID: BZ-891-IN</span>
                            </div>
                            <button
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-authority text-canvas-ivory font-label-micro text-label-micro hover:bg-slate-authority/90 transition-colors">
                                <span class="material-symbols-outlined text-[14px] text-amber-action">lock</span>
                                <span>Vault: ₹60,500.00</span>
                                <span class="material-symbols-outlined text-[14px] opacity-70">tune</span>
                            </button>
                        </div>
                    </nav>
                    <!-- Page Header with 3D Depth & Quick Action Drivers -->
                    <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pt-2">
                        <div class="flex flex-col gap-1.5 max-w-2xl">
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow text-amber-action font-semibold tracking-wider uppercase">Account
                                    Management • Escrow Bidding Tracker</span>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-slate-authority/5 text-slate-authority font-label-micro text-label-micro">Live
                                    0.1s Pulse</span>
                            </div>
                            <h1
                                class="font-display-hero text-display-hero-mobile md:text-display-hero text-slate-authority tracking-tight">
                                My Bids &amp; Escrow Stakes
                            </h1>
                            <p class="font-body-regular text-body-regular text-on-surface-variant">
                                Track active auction positions, collateral locked safely in audited smart escrow, live
                                outbid counter-strikes, and won collectibles.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-card-white/90 backdrop-blur text-slate-authority shadow-sm hover:bg-surface-container transition-all font-button-text text-body-small">
                                <span class="material-symbols-outlined text-[18px] text-amber-action">bolt</span>
                                Auto-Bid Config
                            </button>
                            <a class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small shadow-md hover:opacity-95 active:scale-[0.99] transition-all"
                                href="#">
                                <span class="material-symbols-outlined text-[18px]">gavel</span>
                                Explore Live Auctions
                            </a>
                        </div>
                    </section>
                    <!-- Dimensional Summary Metrics Matrix -->
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Metric 1 -->
                        <div
                            class="p-5 rounded-2xl bg-card-white/90 backdrop-blur shadow-sm flex flex-col justify-between gap-4">
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Total
                                    Locked Escrow</span>
                                <span
                                    class="w-8 h-8 rounded-lg bg-slate-authority/5 flex items-center justify-center text-slate-authority">
                                    <span class="material-symbols-outlined text-[18px]">lock_clock</span>
                                </span>
                            </div>
                            <div>
                                <div
                                    class="font-display-hero text-headline-section font-bold text-slate-authority tracking-tight">
                                    ₹41,700<span class="text-body-small font-normal text-on-surface-variant">.00</span>
                                </div>
                                <p
                                    class="font-label-micro text-label-micro text-status-green flex items-center gap-1 mt-1 font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">verified</span>
                                    Secured across 4 active lots
                                </p>
                            </div>
                        </div>
                        <!-- Metric 2 -->
                        <div
                            class="p-5 rounded-2xl bg-card-white/90 backdrop-blur shadow-sm flex flex-col justify-between gap-4">
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Current
                                    Position</span>
                                <span
                                    class="w-8 h-8 rounded-lg bg-amber-action/10 flex items-center justify-center text-amber-action">
                                    <span class="material-symbols-outlined text-[18px]">equalizer</span>
                                </span>
                            </div>
                            <div>
                                <div
                                    class="flex items-center gap-2 font-display-hero text-headline-section font-bold text-slate-authority">
                                    <span class="text-status-green">2</span>
                                    <span class="text-xs text-on-surface-variant font-label-micro">LEAD</span>
                                    <span class="text-slate-authority/20">|</span>
                                    <span class="text-error">2</span>
                                    <span class="text-xs text-on-surface-variant font-label-micro">OUTBID</span>
                                </div>
                                <div class="w-full bg-surface-container rounded-full h-1.5 mt-2 overflow-hidden flex">
                                    <div class="bg-status-green h-full w-1/2"></div>
                                    <div class="bg-error h-full w-1/2"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Metric 3 -->
                        <div
                            class="p-5 rounded-2xl bg-card-white/90 backdrop-blur shadow-sm flex flex-col justify-between gap-4">
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Upcoming
                                    Close</span>
                                <span
                                    class="w-8 h-8 rounded-lg bg-error/10 flex items-center justify-center text-error">
                                    <span class="material-symbols-outlined text-[18px]">alarm</span>
                                </span>
                            </div>
                            <div>
                                <div
                                    class="font-label-eyebrow text-headline-section font-bold text-slate-authority tracking-tight font-mono text-error">
                                    00:45:12
                                </div>
                                <p class="font-label-micro text-label-micro text-on-surface-variant truncate mt-1">
                                    Lot #HZ-402 • Leica M 35mm
                                </p>
                            </div>
                        </div>
                        <!-- Metric 4 -->
                        <div
                            class="p-5 rounded-2xl bg-slate-authority text-canvas-ivory shadow-sm flex flex-col justify-between gap-4">
                            <div class="flex items-center justify-between">
                                <span
                                    class="font-label-micro text-label-micro uppercase tracking-wider text-canvas-ivory/60">Historical
                                    Win Rate</span>
                                <span
                                    class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-action">
                                    <span class="material-symbols-outlined text-[18px]">trophy</span>
                                </span>
                            </div>
                            <div>
                                <div
                                    class="font-display-hero text-headline-section font-bold text-canvas-ivory tracking-tight">
                                    78% <span class="font-mono text-body-small font-normal text-amber-action">(14 Lots
                                        Won)</span>
                                </div>
                                <p class="font-label-micro text-label-micro text-canvas-ivory/60 mt-1">
                                    Top 5% bidder accuracy rank
                                </p>
                            </div>
                        </div>
                    </section>
                    <!-- Filter Controls & Realtime Socket Indicator -->
                    <section class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                class="px-4 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small flex items-center gap-2 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                                Active Bids (4)
                            </button>
                            <button
                                class="px-4 py-2 rounded-xl bg-card-white/80 hover:bg-card-white text-on-surface font-button-text text-body-small transition-colors">
                                Won Lots (14)
                            </button>
                            <button
                                class="px-4 py-2 rounded-xl bg-card-white/80 hover:bg-card-white text-on-surface font-button-text text-body-small transition-colors">
                                Outbid / Lost (6)
                            </button>
                            <button
                                class="px-4 py-2 rounded-xl bg-card-white/80 hover:bg-card-white text-on-surface font-button-text text-body-small transition-colors">
                                Escrow Refunds (3)
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-card-white text-on-surface-variant font-label-micro text-label-micro shadow-sm">
                                <span
                                    class="material-symbols-outlined text-[16px] text-status-green">wifi_tethering</span>
                                <span class="text-slate-authority font-medium">Live Socket: 0.1s sync</span>
                            </div>
                            <div
                                class="relative flex items-center bg-card-white rounded-lg px-3 py-1.5 text-slate-authority shadow-sm">
                                <span
                                    class="font-label-micro text-label-micro text-on-surface-variant mr-2">Sort:</span>
                                <select
                                    class="bg-transparent font-label-micro text-label-micro font-semibold text-slate-authority focus:outline-none cursor-pointer">
                                    <option>Ending Soonest</option>
                                    <option>Highest Escrow Stake</option>
                                    <option>Recently Outbid</option>
                                    <option>Lowest Increment</option>
                                </select>
                            </div>
                        </div>
                    </section>
                    <!-- Main Bid Cards Container -->
                    <section class="flex flex-col gap-5">
                        <!-- CARD 1: Urgency Critical Snipe Window (Leica Camera) -->
                        <article
                            class="p-5 md:p-6 rounded-3xl bg-card-white shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                                <!-- Left Image with dimensional depth -->
                                <div
                                    class="relative w-full lg:w-64 h-56 lg:h-48 rounded-2xl overflow-hidden flex-shrink-0 bg-surface-container">
                                    <img class="w-full h-full object-cover"
                                        data-alt="Close up editorial shot of a vintage Leica M brass 35mm rangefinder camera with aged patina on walnut surface, warm studio spotlight, film canister beside it"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuABEXCMnCkU5bSBfEB_lZ1_Ux8Kywg1aZu8tvllxwL0zjRxv15ixrix1FvpBV-l8PxVclmez75vFZCOdfqlCOy72svOLCpWemfgxk_OhdErQ_j1HWSjW25a4VbACmwm89Uem7WffdVKc_EgqZjhz8Q_Sz4s6fy_jt7temm4CzElGC346rBSmQwC32wz-ba1fOTc_lP182kRnKCAvDih2YIJPmtY-8wHf0Y4PFA5GRkY6mxj6zwsYcD8" />
                                    <div class="absolute top-2.5 left-2.5">
                                        <span
                                            class="px-2.5 py-1 rounded-full bg-error text-canvas-ivory font-mono text-[10px] font-bold tracking-wide uppercase flex items-center gap-1 shadow-sm">
                                            <span
                                                class="material-symbols-outlined text-[12px] animate-pulse">crisis_alert</span>
                                            Snipe Window
                                        </span>
                                    </div>
                                    <div
                                        class="absolute bottom-2.5 left-2.5 right-2.5 px-2.5 py-1.5 rounded-xl bg-slate-authority/80 backdrop-blur-md text-canvas-ivory flex items-center justify-between">
                                        <span
                                            class="font-label-micro text-label-micro text-canvas-ivory/80 font-mono">LOT
                                            #LEI-88</span>
                                        <span
                                            class="font-mono text-label-micro text-amber-action font-semibold flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]">timer</span> 00:45:12
                                        </span>
                                    </div>
                                </div>
                                <!-- Middle Details -->
                                <div class="flex-1 flex flex-col gap-3 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="px-2.5 py-0.5 rounded-full bg-error/10 text-error font-label-micro text-label-micro font-semibold flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                                            Outbid by ₹500
                                        </span>
                                        <span class="text-on-surface-variant/40">•</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant font-mono">Rangefinder
                                            Optics Lot</span>
                                        <span class="text-on-surface-variant/40">•</span>
                                        <span
                                            class="font-label-micro text-label-micro text-status-green flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[13px]">shield</span> Inspected
                                        </span>
                                    </div>
                                    <div>
                                        <h3
                                            class="font-title-card text-headline-section font-bold text-slate-authority tracking-tight leading-snug">
                                            Vintage Leica M 35mm f/2 Summicron Rangefinder
                                        </h3>
                                        <p
                                            class="font-body-small text-body-small text-on-surface-variant mt-0.5 flex items-center gap-1.5">
                                            <span>Seller: VintageOptics Berlin</span>
                                            <span class="text-slate-authority/20">•</span>
                                            <span class="text-amber-action flex items-center">
                                                <span class="material-symbols-outlined text-[14px]">star</span> 4.97
                                                (120 sales)
                                            </span>
                                        </p>
                                    </div>
                                    <!-- Metrics Row -->
                                    <div class="grid grid-cols-3 gap-3 p-3.5 rounded-xl bg-surface-container-low">
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Your
                                                Bid</span>
                                            <span
                                                class="font-mono font-bold text-body-lead text-slate-authority">₹17,500</span>
                                            <span
                                                class="font-label-micro text-[10px] text-on-surface-variant block">Vault
                                                Collateral</span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Leading
                                                Bid</span>
                                            <span class="font-mono font-bold text-body-lead text-error">₹18,000</span>
                                            <span class="font-label-micro text-[10px] text-on-surface-variant block">by
                                                Bidder #409</span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Min
                                                Counter</span>
                                            <span
                                                class="font-mono font-bold text-body-lead text-status-green">₹18,500</span>
                                            <span
                                                class="font-label-micro text-[10px] text-on-surface-variant block">+₹500
                                                step</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right Actions Block -->
                                <div class="w-full lg:w-56 flex flex-col gap-2.5 justify-center pt-2 lg:pt-0">
                                    <button
                                        class="w-full px-4 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold hover:opacity-95 active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-sm">
                                        <span class="material-symbols-outlined text-[18px]">bolt</span>
                                        Counter-Bid ₹18,500
                                    </button>
                                    <button
                                        class="w-full px-4 py-2.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-button-text text-body-small transition-colors flex items-center justify-center gap-1.5">
                                        <span>View Lot Live</span>
                                        <span class="material-symbols-outlined text-[16px]">north_east</span>
                                    </button>
                                    <span
                                        class="font-label-micro text-center text-label-micro text-on-surface-variant font-mono">1-Click
                                        Escrow Re-stake</span>
                                </div>
                            </div>
                        </article>
                        <!-- CARD 2: Outbid State (Vintage Rolex Submariner) -->
                        <article
                            class="p-5 md:p-6 rounded-3xl bg-card-white shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                                <!-- Left Image -->
                                <div
                                    class="relative w-full lg:w-64 h-56 lg:h-48 rounded-2xl overflow-hidden flex-shrink-0 bg-surface-container">
                                    <img class="w-full h-full object-cover"
                                        data-alt="Vintage Rolex Submariner oyster perpetual luxury watch with black dial and faded bezel resting on deep burgundy velvet cushion in dramatic warm moody watchmaker lighting"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsm8l2mjKzcS3QN9ZK4q5OEWBvFpnFsPn9Q5frE1RMG6InP85mf78fdroJ8_MAncgczz8m-SRjw--UQ60ftDzMZa793J-5ck80g9HDbVbq9MU77DK-shpZG5SrxQN2SmFxvgFf6mDTaxUmthiWOXg1jzwqqi3WpR4pXz1KcfgdJx9prR3MBeHJN_RSerWzF5WfrzDQBBAsvwJiDO5eCTDL3UwholuPzB1mJempqlCxYENkX6SdYXsx" />
                                    <div class="absolute top-2.5 left-2.5">
                                        <span
                                            class="px-2.5 py-1 rounded-full bg-error/90 text-canvas-ivory font-mono text-[10px] font-bold tracking-wide uppercase flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-canvas-ivory"></span>
                                            Outbid (2 Ahead)
                                        </span>
                                    </div>
                                    <div
                                        class="absolute bottom-2.5 left-2.5 right-2.5 px-2.5 py-1.5 rounded-xl bg-slate-authority/80 backdrop-blur-md text-canvas-ivory flex items-center justify-between">
                                        <span
                                            class="font-label-micro text-label-micro text-canvas-ivory/80 font-mono">LOT
                                            #HZ-402</span>
                                        <span
                                            class="font-mono text-label-micro text-amber-action font-semibold flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]">schedule</span> 01:22:10
                                        </span>
                                    </div>
                                </div>
                                <!-- Middle Details -->
                                <div class="flex-1 flex flex-col gap-3 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="px-2.5 py-0.5 rounded-full bg-amber-action/10 text-slate-authority font-label-micro text-label-micro font-semibold">
                                            Horology Lot #402
                                        </span>
                                        <span class="text-on-surface-variant/40">•</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant">Timepiece
                                            Atelier • ★ 4.9 Verified</span>
                                        <span class="text-on-surface-variant/40">•</span>
                                        <span
                                            class="font-label-micro text-label-micro text-slate-authority font-mono">34
                                            Total Bids</span>
                                    </div>
                                    <div>
                                        <h3
                                            class="font-title-card text-headline-section font-bold text-slate-authority tracking-tight leading-snug">
                                            Vintage Submariner Mechanical Watch (1984 Oyster Perpetual)
                                        </h3>
                                        <p class="font-body-small text-body-small text-on-surface-variant mt-0.5">
                                            Original box and paperwork included. Escrow authentication guaranteed with
                                            Geneva appraisal certificate.
                                        </p>
                                    </div>
                                    <!-- Metrics Row -->
                                    <div class="grid grid-cols-3 gap-3 p-3.5 rounded-xl bg-surface-container-low">
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Your
                                                Placed Bid</span>
                                            <span
                                                class="font-mono font-bold text-body-lead text-slate-authority">₹9,200</span>
                                            <span
                                                class="font-label-micro text-[10px] text-on-surface-variant block">Escrow
                                                locked</span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Current
                                                Lead</span>
                                            <span class="font-mono font-bold text-body-lead text-error">₹9,500</span>
                                            <span
                                                class="font-label-micro text-[10px] text-on-surface-variant block">Bidder
                                                #719</span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Next
                                                Valid Bid</span>
                                            <span
                                                class="font-mono font-bold text-body-lead text-slate-authority">₹9,750</span>
                                            <span
                                                class="font-label-micro text-[10px] text-on-surface-variant block">+₹250
                                                min step</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right Actions -->
                                <div class="w-full lg:w-56 flex flex-col gap-2.5 justify-center pt-2 lg:pt-0">
                                    <button
                                        class="w-full px-4 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold hover:opacity-95 active:scale-[0.99] transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                        Increase to ₹9,750 →
                                    </button>
                                    <button
                                        class="w-full px-4 py-2.5 rounded-xl bg-card-white border border-slate-authority/10 hover:bg-surface-container text-slate-authority font-button-text text-body-small transition-colors flex items-center justify-center gap-1">
                                        <span
                                            class="material-symbols-outlined text-[16px] text-amber-action">flash_on</span>
                                        1-Click Match &amp; Raise
                                    </button>
                                    <a class="font-label-micro text-center text-label-micro text-on-surface-variant hover:text-slate-authority transition-colors inline-flex items-center justify-center gap-1"
                                        href="#">
                                        View Live Bid Feed <span
                                            class="material-symbols-outlined text-[12px]">open_in_new</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                        <!-- CARD 3: Leading / Highest Bidder State (Gaming GPU) -->
                        <article
                            class="p-5 md:p-6 rounded-3xl bg-card-white shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                                <!-- Left Image -->
                                <div
                                    class="relative w-full lg:w-64 h-56 lg:h-48 rounded-2xl overflow-hidden flex-shrink-0 bg-surface-container">
                                    <img class="w-full h-full object-cover"
                                        data-alt="NVIDIA GeForce RTX 4090 OC gaming graphics card with triple black fans and subtle RGB strip glowing on dark premium studio matte podium"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBMohEpcd51AM7Bn0ZrymLzvVTHtLxTM6li-jUiJF8jLYMyYnCDQ9-eaLbvzNZ1pIGWF6NE7aGHciwn4oktUbYAcHLsN3INsbJtxamcp3b7-93B3gDFN8543VxESIas2yyTgazj4pnETWYpMtoaS1nazPpBH6lvnDaOTwgSyWfFRPVkSUqi_S7FL_ux1zeSu3mHEPVw3-GB7N3WDxXdmyDb4GgFS3bLRsfm5wlTtLiO00hRf2xlonuQ" />
                                    <div class="absolute top-2.5 left-2.5">
                                        <span
                                            class="px-2.5 py-1 rounded-full bg-status-green text-canvas-ivory font-mono text-[10px] font-bold tracking-wide uppercase flex items-center gap-1 shadow-sm">
                                            <span class="material-symbols-outlined text-[12px]">verified</span>
                                            Highest Bidder
                                        </span>
                                    </div>
                                    <div
                                        class="absolute bottom-2.5 left-2.5 right-2.5 px-2.5 py-1.5 rounded-xl bg-slate-authority/80 backdrop-blur-md text-canvas-ivory flex items-center justify-between">
                                        <span
                                            class="font-label-micro text-label-micro text-canvas-ivory/80 font-mono">LOT
                                            #GPU-910</span>
                                        <span
                                            class="font-mono text-label-micro text-canvas-ivory font-semibold flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]">schedule</span> 05:12:20
                                        </span>
                                    </div>
                                </div>
                                <!-- Middle Details -->
                                <div class="flex-1 flex flex-col gap-3 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="px-2.5 py-0.5 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-semibold flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                            Leading Position (1st Place)
                                        </span>
                                        <span class="text-on-surface-variant/40">•</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant font-mono">Hardware
                                            • Factory Sealed</span>
                                        <span class="text-on-surface-variant/40">•</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant">SiliconVault
                                            Official ★ 5.0</span>
                                    </div>
                                    <div>
                                        <h3
                                            class="font-title-card text-headline-section font-bold text-slate-authority tracking-tight leading-snug">
                                            GeForce RTX 4090 OC 24GB Gaming Graphics Card
                                        </h3>
                                        <p class="font-body-small text-body-small text-on-surface-variant mt-0.5">
                                            Transferable warranty included. Free priority insured courier dispatch upon
                                            auction hammer.
                                        </p>
                                    </div>
                                    <!-- Metrics Row -->
                                    <div class="grid grid-cols-3 gap-3 p-3.5 rounded-xl bg-surface-container-low">
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Your
                                                Bid (Highest)</span>
                                            <span
                                                class="font-mono font-bold text-body-lead text-status-green">₹32,500</span>
                                            <span
                                                class="font-label-micro text-[10px] text-status-green block font-medium">Reserve
                                                Met ✓</span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant uppercase block">2nd
                                                Position</span>
                                            <span
                                                class="font-mono font-bold text-body-lead text-slate-authority">₹31,000</span>
                                            <span
                                                class="font-label-micro text-[10px] text-on-surface-variant block">Behind
                                                by ₹1,500</span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant uppercase block">Market
                                                Activity</span>
                                            <span class="font-mono font-bold text-body-lead text-slate-authority">28
                                                Bids</span>
                                            <span class="font-label-micro text-[10px] text-on-surface-variant block">12
                                                Active watchers</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right Actions -->
                                <div class="w-full lg:w-56 flex flex-col gap-2.5 justify-center pt-2 lg:pt-0">
                                    <div
                                        class="p-3 rounded-xl bg-status-green/10 text-status-green font-label-micro text-label-micro text-center font-medium">
                                        🛡 You are leading this auction
                                    </div>
                                    <button
                                        class="w-full px-4 py-3 rounded-xl bg-card-white hover:bg-surface-container text-slate-authority font-button-text text-body-small font-semibold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-amber-action">bolt</span>
                                        Config Auto-Bid Cap
                                    </button>
                                    <a class="font-label-micro text-center text-label-micro text-on-surface-variant hover:text-slate-authority transition-colors"
                                        href="#">
                                        Lot Terms &amp; Inspection Report
                                    </a>
                                </div>
                            </div>
                        </article>
                    </section>
                    <!-- AI Snipe Strategy Concierge Floating Pill -->
                    <section
                        class="p-4 rounded-2xl bg-card-white/80 backdrop-blur-md shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-action/10 text-amber-action flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[22px]">smart_toy</span>
                            </div>
                            <div>
                                <span
                                    class="font-label-micro text-label-micro uppercase tracking-wider text-amber-action font-semibold block">Bazaario
                                    AI Bid Concierge</span>
                                <p class="font-body-small text-body-small text-slate-authority font-medium">
                                    "Suggested snipe for <span class="font-mono font-bold">Lot #HZ-402</span>: Place
                                    counter of <span class="font-bold text-amber-action font-mono">₹9,750</span> at
                                    <span class="font-mono font-bold">00:01:15</span> mark to minimize competitor bid
                                    escalation."
                                </p>
                            </div>
                        </div>
                        <button
                            class="flex-shrink-0 px-4 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small hover:bg-slate-authority/90 transition-colors">
                            Apply AI Snipe Schedule
                        </button>
                    </section>
                    <!-- Authority Escrow Security Banner (Modern 3D Deep Slate Surface) -->
                    <section
                        class="p-8 md:p-10 rounded-3xl bg-slate-authority text-canvas-ivory shadow-lg flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
                        <div class="max-w-2xl flex flex-col gap-3">
                            <div
                                class="inline-flex items-center gap-2 font-mono text-xs text-amber-action uppercase tracking-wider font-semibold">
                                <span class="material-symbols-outlined text-[16px]">verified_user</span>
                                Zero-Limbo Collateral Protocol
                            </div>
                            <h2
                                class="font-headline-section text-headline-section font-bold tracking-tight text-canvas-ivory">
                                Cryptographic Outbid Escrow Protection
                            </h2>
                            <p class="font-body-regular text-body-regular text-canvas-ivory/70 leading-relaxed">
                                When you are outbid in any Bazaario auction, your staked collateral is never locked in
                                limbo. Your funds are instantly credited back to your liquid balance with 0-second
                                release, ready for an immediate counter-bid or instant payout to your verified bank
                                account.
                            </p>
                            <div
                                class="flex flex-wrap items-center gap-6 pt-2 font-label-micro text-label-micro text-canvas-ivory/60 font-mono">
                                <span class="flex items-center gap-1.5"><span
                                        class="w-1.5 h-1.5 rounded-full bg-status-green"></span>Smart Contract
                                    Audited</span>
                                <span class="flex items-center gap-1.5"><span
                                        class="w-1.5 h-1.5 rounded-full bg-status-green"></span>Multi-Sig Vault
                                    Reserves</span>
                                <span class="flex items-center gap-1.5"><span
                                        class="w-1.5 h-1.5 rounded-full bg-status-green"></span>Biometric
                                    Counter-Strike</span>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row lg:flex-col gap-3 w-full lg:w-auto flex-shrink-0">
                            <button
                                class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold hover:opacity-95 active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                                View Vault Balance
                            </button>
                            <button
                                class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-canvas-ivory font-button-text text-body-small font-semibold transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">policy</span>
                                Escrow Contract Audit
                            </button>
                        </div>
                    </section>
                </div>
            </div>
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