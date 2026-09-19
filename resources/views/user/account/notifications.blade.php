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
            <div class="flex items-center gap-gutter-lg"><a class="flex items-center gap-3 group" data-path="home"
                    href="#"><span
                        class="font-display-hero text-headline-section font-bold tracking-tight text-slate-authority group-hover:text-amber-action transition-colors">Bazaario</span></a>
                <nav class="hidden lg:flex items-center gap-1"
                    data-active-classes="bg-surface-container text-on-surface font-semibold rounded-lg"><a
                        class="px-3.5 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="home" href="#">Home</a><a
                        class="px-3.5 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="shop" href="#">Shop</a><a
                        class="px-3.5 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="categories" href="#">Categories</a><a
                        class="px-3.5 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="auctions" href="#">Auctions</a><a
                        class="px-3.5 py-2 font-body-small text-body-small text-on-surface-variant hover:text-on-surface transition-colors"
                        data-path="ai-compare" href="#">AI Compare</a></nav>
            </div>
            <div class="flex items-center gap-3"><button aria-label="Notifications"
                    class="relative p-2.5 rounded-full hover:bg-surface-container-high transition-colors text-slate-authority flex items-center justify-center"
                    type="button"><span class="material-symbols-outlined text-[22px]">notifications</span><span
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
                <div class="h-6 w-[1px] bg-primary-alpha-10 mx-1 hidden sm:block"></div><a
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
            <!-- Dynamic Canvas Glow Orbs (Contained) -->
            <div class="relative w-full overflow-hidden">
                <div
                    class="absolute -top-32 left-1/4 w-96 h-96 bg-amber-action/10 rounded-full blur-3xl pointer-events-none">
                </div>
                <div
                    class="absolute top-96 right-10 w-[30rem] h-[30rem] bg-secondary-container/20 rounded-full blur-3xl pointer-events-none">
                </div>
                <div class="max-w-container-max mx-auto px-gutter-md pt-6 pb-section-final-bottom flex flex-col gap-8">
                    <!-- Sub-navigation & Header Bar -->
                    <nav aria-label="Breadcrumb and Vault Status"
                        class="w-full flex flex-col lg:flex-row lg:items-center justify-between gap-4 py-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <a class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-card-white shadow-sm hover:bg-surface-container transition-colors text-slate-authority font-body-small text-body-small font-medium"
                                href="#">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Back to Account
                            </a>
                            <span class="text-on-surface-variant/40 font-mono text-xs">/</span>
                            <div
                                class="flex items-center gap-2 font-label-eyebrow text-label-eyebrow text-on-surface-variant">
                                <span>Home</span>
                                <span class="text-on-surface-variant/40">/</span>
                                <span>Account</span>
                                <span class="text-on-surface-variant/40">/</span>
                                <span class="text-slate-authority font-semibold">Notifications</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Escrow verification badge -->
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-card-white shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                                <span
                                    class="font-label-micro text-label-micro text-slate-authority tracking-wider font-semibold">
                                    ESCROW VERIFIED BUYER • VAULT #ESC-9041 • UID: BZ-891-IN
                                </span>
                            </div>
                            <!-- Vault balance pill -->
                            <a class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-authority text-canvas-ivory hover:bg-primary-container transition-all shadow-sm group"
                                href="#">
                                <span class="material-symbols-outlined text-[16px] text-amber-action">lock</span>
                                <span class="font-label-eyebrow text-label-eyebrow font-semibold">Vault:
                                    ₹60,500.00</span>
                                <span
                                    class="material-symbols-outlined text-[16px] text-canvas-ivory/60 group-hover:text-canvas-ivory transition-colors">settings</span>
                            </a>
                        </div>
                    </nav>
                    <!-- Page Header with Dimensional Glass Styling -->
                    <section
                        class="relative rounded-2xl bg-card-white/90 backdrop-blur-md p-6 lg:p-8 shadow-sm overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-80 h-full bg-gradient-to-l from-amber-action/10 via-transparent to-transparent pointer-events-none">
                        </div>
                        <div class="relative z-10 flex flex-col gap-6">
                            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                                <div class="flex flex-col gap-2 max-w-2xl">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow text-amber-action uppercase font-semibold tracking-wider flex items-center gap-2">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-action"></span>
                                        ACCOUNT MANAGEMENT • REAL-TIME DISPATCH &amp; ESCROW ALERTS
                                    </span>
                                    <h1
                                        class="font-display-hero text-headline-section lg:text-display-hero text-slate-authority font-bold tracking-tight">
                                        Notifications
                                    </h1>
                                    <p
                                        class="font-body-regular text-body-regular text-on-surface-variant leading-relaxed">
                                        Manage real-time logistics tracking, live auction floor telemetry, and verified
                                        cryptographic escrow releases.
                                    </p>
                                </div>
                                <!-- Quick Global Actions -->
                                <div class="flex items-center gap-3 shrink-0">
                                    <button
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-button-text text-body-small font-semibold shadow-sm transition-all active:scale-[0.98]"
                                        id="markAllReadBtn">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-status-green">done_all</span>
                                        <span>Mark All as Read</span>
                                    </button>
                                    <button aria-label="Notification Preferences"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-card-white hover:bg-surface-container-low text-slate-authority font-button-text text-body-small font-semibold shadow-sm transition-all">
                                        <span class="material-symbols-outlined text-[18px]">tune</span>
                                        <span class="hidden sm:inline">Preferences</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Interactive Tab Filters -->
                            <div class="flex items-center gap-2 overflow-x-auto pb-1 pt-2 no-scrollbar"
                                id="tabFilterGroup">
                                <button
                                    class="filter-tab active px-4 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-body-small text-body-small font-semibold whitespace-nowrap shadow-sm transition-all"
                                    data-category="all">
                                    All Alerts <span
                                        class="ml-1 px-1.5 py-0.5 rounded-full bg-canvas-ivory/20 font-label-micro text-label-micro">18</span>
                                </button>
                                <button
                                    class="filter-tab px-4 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-body-small text-body-small font-medium whitespace-nowrap transition-all"
                                    data-category="unread">
                                    Unread <span
                                        class="ml-1 px-1.5 py-0.5 rounded-full bg-amber-action text-slate-authority font-label-micro text-label-micro font-bold">4</span>
                                </button>
                                <button
                                    class="filter-tab px-4 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-body-small text-body-small font-medium whitespace-nowrap transition-all"
                                    data-category="logistics">
                                    Orders &amp; Logistics <span
                                        class="ml-1 px-1.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-micro text-label-micro">8</span>
                                </button>
                                <button
                                    class="filter-tab px-4 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-body-small text-body-small font-medium whitespace-nowrap transition-all"
                                    data-category="auctions">
                                    Live Auctions <span
                                        class="ml-1 px-1.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-micro text-label-micro">4</span>
                                </button>
                                <button
                                    class="filter-tab px-4 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-body-small text-body-small font-medium whitespace-nowrap transition-all"
                                    data-category="security">
                                    Security &amp; Escrow <span
                                        class="ml-1 px-1.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-micro text-label-micro">2</span>
                                </button>
                            </div>
                        </div>
                    </section>
                    <!-- Notifications Stream Container -->
                    <div class="flex flex-col gap-10">
                        <!-- TIMELINE SECTION: TODAY -->
                        <section class="flex flex-col gap-4">
                            <div class="flex items-center justify-between px-1">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow font-bold uppercase tracking-wider text-slate-authority bg-surface-container-high px-3 py-1 rounded-md">
                                        Today
                                    </span>
                                    <span class="font-label-micro text-label-micro text-on-surface-variant">4 updates
                                        pending review</span>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro text-amber-action font-semibold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-action animate-ping"></span> Live
                                    sync active
                                </span>
                            </div>
                            <div class="flex flex-col gap-3.5">
                                <!-- Notification 1: Order/Shipping (Sapphire Blue Accent) -->
                                <article
                                    class="notification-item unread logistics group relative rounded-2xl bg-card-white/95 backdrop-blur-md p-5 lg:p-6 shadow-sm transition-all hover:bg-card-white">
                                    <div class="flex flex-col md:flex-row items-start justify-between gap-4">
                                        <div class="flex items-start gap-4 w-full">
                                            <!-- 3D Glowing Sapphire Badge -->
                                            <div
                                                class="relative shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-700 flex items-center justify-center text-card-white shadow-md shadow-blue-500/20">
                                                <span
                                                    class="material-symbols-outlined text-[26px]">local_shipping</span>
                                                <span
                                                    class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-amber-action ring-2 ring-card-white"></span>
                                            </div>
                                            <!-- Content Area -->
                                            <div class="flex flex-col gap-2 flex-1 min-w-0">
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="font-label-micro text-label-micro px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-semibold uppercase tracking-wide">
                                                            Logistics Dispatch
                                                        </span>
                                                        <span
                                                            class="font-label-micro text-label-micro text-on-surface-variant/60 font-mono">2m
                                                            ago</span>
                                                    </div>
                                                    <span
                                                        class="font-label-micro text-label-micro text-status-green bg-status-green/10 px-2.5 py-0.5 rounded-full font-medium flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                                        In Transit
                                                    </span>
                                                </div>
                                                <h2
                                                    class="font-title-card text-title-card font-semibold text-slate-authority leading-snug">
                                                    Your order <span
                                                        class="font-mono text-slate-authority font-bold">#BZ-2026-00128</span>
                                                    has been shipped.
                                                </h2>
                                                <!-- Telemetry Strip -->
                                                <div
                                                    class="flex flex-wrap items-center gap-y-1 gap-x-3 py-1.5 px-3 rounded-xl bg-surface-container-low font-label-micro text-label-micro text-on-surface-variant font-mono">
                                                    <span
                                                        class="flex items-center gap-1 text-slate-authority font-medium">
                                                        <span
                                                            class="material-symbols-outlined text-[14px] text-blue-600">hub</span>
                                                        Carrier: BlueDart Express
                                                    </span>
                                                    <span>•</span>
                                                    <span>Tracking: <strong
                                                            class="text-slate-authority">#BD-98214-IN</strong></span>
                                                    <span>•</span>
                                                    <span>Destination: Contai, West Bengal (721401)</span>
                                                </div>
                                                <!-- Associated Product Cardlet + CTAs -->
                                                <div
                                                    class="mt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                                                    <div class="flex items-center gap-3">
                                                        <img class="w-12 h-12 rounded-xl object-cover shadow-sm bg-surface-container"
                                                            data-alt="High-end matte black Sony WH-1000XM5 wireless noise cancelling headphones isolated on pristine studio lighting with soft gold accents, ultra clear product photography."
                                                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCwtM6bpRW93-p6YQsuEmbgOevEZO0D4YcpVaiIY_ErA0mM2mIOpQhIZQIFzU-u2UjDAkOlDdyScXOAiD5EICizWNTrHREXfHLbwJytCE9VjbWi2xkuHOt9dBbP64AxEhzPxNbx1gnXvONgobJGpEnG2YZ0lciuzWMjhD8sZwLkUMgMr8kp4yCK8mzyWuVBn56eUzURqQV1_pBsr3WMv5V3tNtXCMdFjn100TeyDk9aSkBJOBVHMPH6" />
                                                        <div class="flex flex-col">
                                                            <span
                                                                class="font-body-small text-body-small font-semibold text-slate-authority leading-tight">Sony
                                                                WH-1000XM5 Wireless</span>
                                                            <span
                                                                class="font-label-micro text-label-micro text-on-surface-variant">Qty:
                                                                1 • Sealed Cryptographic Escrow Tag attached</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2.5 shrink-0">
                                                        <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 active:scale-[0.98] transition-all shadow-sm"
                                                            href="#">
                                                            <span>Track Package</span>
                                                            <span
                                                                class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                                        </a>
                                                        <a class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-button-text text-body-small font-medium transition-colors"
                                                            href="#">
                                                            <span>Details</span>
                                                            <span
                                                                class="material-symbols-outlined text-[16px]">open_in_new</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <!-- Notification 2: Live Auction (Amethyst Violet Accent) -->
                                <article
                                    class="notification-item unread auctions group relative rounded-2xl bg-card-white/95 backdrop-blur-md p-5 lg:p-6 shadow-sm transition-all hover:bg-card-white">
                                    <div class="flex items-start gap-4 w-full">
                                        <!-- 3D Glowing Purple Auction Gavel Badge -->
                                        <div
                                            class="relative shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-600 via-indigo-600 to-purple-800 flex items-center justify-center text-card-white shadow-md shadow-purple-600/20">
                                            <span class="material-symbols-outlined text-[26px]">gavel</span>
                                            <span
                                                class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-amber-action ring-2 ring-card-white animate-pulse"></span>
                                        </div>
                                        <div class="flex flex-col gap-2 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-label-micro text-label-micro px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 font-semibold uppercase tracking-wide">
                                                        Live Floor Telemetry
                                                    </span>
                                                    <span
                                                        class="font-label-micro text-label-micro text-on-surface-variant/60 font-mono">1h
                                                        ago</span>
                                                </div>
                                                <div
                                                    class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-action/10 text-amber-action font-label-micro text-label-micro font-bold">
                                                    <span class="material-symbols-outlined text-[14px]">timer</span>
                                                    <span>Floor Clock: 01:14:32 remaining</span>
                                                </div>
                                            </div>
                                            <h2
                                                class="font-title-card text-title-card font-semibold text-slate-authority leading-snug">
                                                New bid placed on your followed auction: <span
                                                    class="text-slate-authority underline decoration-amber-action/50 decoration-2 underline-offset-4">Vintage
                                                    Leica M 35mm</span>.
                                            </h2>
                                            <div
                                                class="flex flex-wrap items-center gap-y-1 gap-x-3 py-1.5 px-3 rounded-xl bg-surface-container-low font-label-micro text-label-micro text-on-surface-variant font-mono">
                                                <span class="text-slate-authority font-medium">Bidder #942</span>
                                                <span>placed</span>
                                                <span class="font-bold text-slate-authority">₹86,500</span>
                                                <span class="text-status-green font-semibold">(+₹1,500 over
                                                    reserve)</span>
                                                <span>•</span>
                                                <span>Verified Authenticator Certificate #LEI-881</span>
                                            </div>
                                            <div class="mt-2 flex flex-wrap items-center justify-between gap-4 pt-1">
                                                <div
                                                    class="flex items-center gap-2 text-on-surface-variant font-body-small text-body-small">
                                                    <span
                                                        class="material-symbols-outlined text-[18px] text-amber-action">trending_up</span>
                                                    <span>Next minimum counter-bid: <strong
                                                            class="text-slate-authority">₹88,000</strong></span>
                                                </div>
                                                <div class="flex items-center gap-2.5">
                                                    <button
                                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 active:scale-[0.98] transition-all shadow-sm">
                                                        <span
                                                            class="material-symbols-outlined text-[18px]">pan_tool_alt</span>
                                                        <span>Place Counter-Bid</span>
                                                    </button>
                                                    <a class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-button-text text-body-small font-medium transition-colors"
                                                        href="#">
                                                        <span>View Live Room</span>
                                                        <span
                                                            class="material-symbols-outlined text-[16px]">open_in_new</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <!-- Notification 3: Payment / Escrow (Emerald Tone) -->
                                <article
                                    class="notification-item unread security group relative rounded-2xl bg-card-white/95 backdrop-blur-md p-5 lg:p-6 shadow-sm transition-all hover:bg-card-white">
                                    <div class="flex items-start gap-4 w-full">
                                        <!-- 3D Glowing Emerald Shield Badge -->
                                        <div
                                            class="relative shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-card-white shadow-md shadow-emerald-600/20">
                                            <span class="material-symbols-outlined text-[26px]">verified_user</span>
                                            <span
                                                class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-status-green ring-2 ring-card-white"></span>
                                        </div>
                                        <div class="flex flex-col gap-2 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-label-micro text-label-micro px-2 py-0.5 rounded-full bg-emerald-50 text-status-green font-semibold uppercase tracking-wide">
                                                        Cryptographic Escrow Hold
                                                    </span>
                                                    <span
                                                        class="font-label-micro text-label-micro text-on-surface-variant/60 font-mono">3h
                                                        ago</span>
                                                </div>
                                                <span
                                                    class="font-label-micro text-label-micro px-2.5 py-0.5 rounded-full bg-status-green/10 text-status-green font-bold">
                                                    Escrow Vault #ESC-9041
                                                </span>
                                            </div>
                                            <h2
                                                class="font-title-card text-title-card font-semibold text-slate-authority leading-snug">
                                                Your payment was successfully processed &amp; locked into Escrow Vault
                                                #ESC-9041.
                                            </h2>
                                            <p class="font-body-regular text-body-small text-on-surface-variant">
                                                Amount <strong
                                                    class="text-slate-authority font-mono font-semibold">₹2,499.00</strong>
                                                held under Bazaario Buyer Protection until inspection sign-off at Contai
                                                delivery point.
                                            </p>
                                            <div class="mt-2 flex flex-wrap items-center justify-between gap-4 pt-1">
                                                <div
                                                    class="flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant">
                                                    <span
                                                        class="material-symbols-outlined text-[16px] text-status-green">lock</span>
                                                    <span>Funds will auto-release only upon two-party handshake
                                                        confirmation</span>
                                                </div>
                                                <button
                                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-button-text text-body-small font-medium transition-colors">
                                                    <span
                                                        class="material-symbols-outlined text-[18px]">receipt_long</span>
                                                    <span>Download Cryptographic Receipt</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <!-- Notification 4: Price Drop (Amber Tone) -->
                                <article
                                    class="notification-item unread group relative rounded-2xl bg-card-white/95 backdrop-blur-md p-5 lg:p-6 shadow-sm transition-all hover:bg-card-white">
                                    <div class="flex items-start gap-4 w-full">
                                        <!-- 3D Glowing Amber Lightning Badge -->
                                        <div
                                            class="relative shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-600 flex items-center justify-center text-slate-authority shadow-md shadow-amber-500/20">
                                            <span
                                                class="material-symbols-outlined text-[26px] text-slate-authority">bolt</span>
                                            <span
                                                class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-slate-authority ring-2 ring-card-white"></span>
                                        </div>
                                        <div class="flex flex-col gap-2 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-label-micro text-label-micro px-2 py-0.5 rounded-full bg-amber-50 text-amber-900 font-semibold uppercase tracking-wide">
                                                        Price Drop Opportunity
                                                    </span>
                                                    <span
                                                        class="font-label-micro text-label-micro text-on-surface-variant/60 font-mono">5h
                                                        ago</span>
                                                </div>
                                                <span
                                                    class="font-label-micro text-label-micro px-2.5 py-0.5 rounded-full bg-red-100 text-error font-bold">
                                                    20% Markdown
                                                </span>
                                            </div>
                                            <h2
                                                class="font-title-card text-title-card font-semibold text-slate-authority leading-snug">
                                                Price drop on saved item: <span
                                                    class="font-bold text-slate-authority">Apple Watch Ultra 2</span> is
                                                now 20% off!
                                            </h2>
                                            <div
                                                class="flex flex-wrap items-center gap-y-1 gap-x-3 py-1.5 px-3 rounded-xl bg-surface-container-low font-label-micro text-label-micro text-on-surface-variant">
                                                <span>Price reduced from <span class="line-through">₹4,999</span> to
                                                    <strong
                                                        class="text-status-green font-mono text-body-small font-bold">₹3,999</strong></span>
                                                <span>•</span>
                                                <span>Merchant: Apex Cellular Collective</span>
                                                <span>•</span>
                                                <span class="text-error font-semibold flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[14px]">warning</span>
                                                    Only 3 units available in stock
                                                </span>
                                            </div>
                                            <div class="mt-2 flex flex-wrap items-center justify-end gap-3 pt-1">
                                                <button
                                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 active:scale-[0.98] transition-all shadow-sm">
                                                    <span
                                                        class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                                    <span>Add to Cart</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </section>
                        <!-- TIMELINE SECTION: EARLIER -->
                        <section class="flex flex-col gap-4">
                            <div class="flex items-center justify-between px-1">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow font-bold uppercase tracking-wider text-on-surface-variant bg-surface-container px-3 py-1 rounded-md">
                                        Earlier This Week
                                    </span>
                                    <span class="font-label-micro text-label-micro text-on-surface-variant/70">Archived
                                        institutional updates</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-3.5">
                                <!-- Notification 5: Onboarding / Security (Slate Tone, Read) -->
                                <article
                                    class="notification-item read security group relative rounded-2xl bg-card-white/70 backdrop-blur-sm p-5 lg:p-6 shadow-sm transition-all hover:bg-card-white opacity-90 hover:opacity-100">
                                    <div class="flex items-start gap-4 w-full">
                                        <!-- 3D Silver/Slate Badge -->
                                        <div
                                            class="shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-200 to-slate-400 flex items-center justify-center text-slate-authority shadow-sm">
                                            <span class="material-symbols-outlined text-[26px]">stars</span>
                                        </div>
                                        <div class="flex flex-col gap-2 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-label-micro text-label-micro px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-semibold uppercase tracking-wide">
                                                        Account Onboarding
                                                    </span>
                                                    <span
                                                        class="font-label-micro text-label-micro text-on-surface-variant/60 font-mono">2d
                                                        ago</span>
                                                </div>
                                                <span
                                                    class="font-label-micro text-label-micro text-on-surface-variant/50 flex items-center gap-1 font-mono">
                                                    <span class="material-symbols-outlined text-[14px]">done</span> Read
                                                </span>
                                            </div>
                                            <h2
                                                class="font-title-card text-title-card font-semibold text-slate-authority leading-snug">
                                                Welcome to Bazaario Marketplace!
                                            </h2>
                                            <p
                                                class="font-body-regular text-body-small text-on-surface-variant leading-relaxed">
                                                Your account UID: <code
                                                    class="font-mono text-slate-authority font-semibold">BZ-891-IN</code>
                                                was successfully authenticated. Escrow wallet activated with ₹60,500
                                                initial reserve.
                                            </p>
                                            <div class="mt-1 flex items-center gap-3">
                                                <a class="font-body-small text-body-small font-semibold text-slate-authority hover:text-amber-action inline-flex items-center gap-1 transition-colors"
                                                    href="#">
                                                    <span>Explore Curated Collections</span>
                                                    <span
                                                        class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <!-- Notification 6: Security Alert (Slate/Indigo Tone, Read) -->
                                <article
                                    class="notification-item read security group relative rounded-2xl bg-card-white/70 backdrop-blur-sm p-5 lg:p-6 shadow-sm transition-all hover:bg-card-white opacity-90 hover:opacity-100">
                                    <div class="flex items-start gap-4 w-full">
                                        <!-- 3D Security Key Badge -->
                                        <div
                                            class="shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-950 flex items-center justify-center text-canvas-ivory shadow-sm">
                                            <span class="material-symbols-outlined text-[26px]">key</span>
                                        </div>
                                        <div class="flex flex-col gap-2 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-label-micro text-label-micro px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-semibold uppercase tracking-wide">
                                                        Security Handshake
                                                    </span>
                                                    <span
                                                        class="font-label-micro text-label-micro text-on-surface-variant/60 font-mono">4d
                                                        ago</span>
                                                </div>
                                                <span
                                                    class="font-label-micro text-label-micro text-on-surface-variant/50 flex items-center gap-1 font-mono">
                                                    <span class="material-symbols-outlined text-[14px]">done</span> Read
                                                </span>
                                            </div>
                                            <h2
                                                class="font-title-card text-title-card font-semibold text-slate-authority leading-snug">
                                                2FA Biometric Handshake enabled for Contai logistics endpoint.
                                            </h2>
                                            <p
                                                class="font-body-regular text-body-small text-on-surface-variant leading-relaxed">
                                                Hardware security token verified for safe parcel handoffs and escrow
                                                instant sign-offs at doorstep.
                                            </p>
                                            <div
                                                class="flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant font-mono mt-1">
                                                <span
                                                    class="material-symbols-outlined text-[14px] text-status-green">check_circle</span>
                                                <span>Device Fingerprint: ECDSA-SHA256 • Verified by Security
                                                    Mesh</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </section>
                    </div>
                    <!-- Bottom Security & Telemetry Card (Dimensional Dark Slate Container) -->
                    <section
                        class="relative rounded-2xl bg-slate-authority text-canvas-ivory p-8 lg:p-10 shadow-lg overflow-hidden mt-4">
                        <div
                            class="absolute -right-16 -bottom-16 w-80 h-80 bg-amber-action/10 rounded-full blur-3xl pointer-events-none">
                        </div>
                        <div
                            class="absolute top-0 right-1/3 w-64 h-64 bg-status-green/10 rounded-full blur-3xl pointer-events-none">
                        </div>
                        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                            <div class="flex items-start gap-4 max-w-2xl">
                                <div
                                    class="w-12 h-12 rounded-xl bg-card-white/10 flex items-center justify-center text-amber-action shrink-0 shadow-inner">
                                    <span class="material-symbols-outlined text-[28px]">shield_with_heart</span>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow text-amber-action uppercase font-semibold">
                                        INFRASTRUCTURE TELEMETRY
                                    </span>
                                    <h2 class="font-title-card text-title-card font-semibold text-canvas-ivory">
                                        Real-Time Escrow &amp; Webhook Telemetry Active
                                    </h2>
                                    <p class="font-body-small text-body-small text-canvas-ivory/70 leading-relaxed">
                                        Instant SMS and push dispatch alerts are cryptographically verified through
                                        Vault #ESC-9041 node. Zero false positive notifications guaranteed by Bazaario
                                        Mesh protocol.
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 shrink-0">
                                <button
                                    class="px-5 py-3 rounded-xl bg-card-white/10 hover:bg-card-white/20 text-canvas-ivory font-button-text text-body-small font-semibold transition-all">
                                    Configure Channels
                                </button>
                                <button
                                    class="px-5 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 active:scale-[0.98] transition-all shadow-md">
                                    Ask AI Concierge
                                </button>
                            </div>
                        </div>
                    </section>
                    <!-- Interactive Micro AI Dock Trigger -->
                    <aside aria-label="Bazaario AI Quick Prompt" class="fixed bottom-6 right-6 z-40">
                        <button
                            class="group flex items-center gap-3 px-4 py-3 rounded-full bg-slate-authority text-canvas-ivory shadow-xl hover:shadow-2xl hover:bg-primary-container transition-all active:scale-[0.98]">
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-action opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-action"></span>
                            </span>
                            <span class="font-button-text text-body-small font-semibold">
                                ✦ Ask Bazaario AI: <span
                                    class="font-normal text-canvas-ivory/80 group-hover:text-canvas-ivory">"Summarize
                                    today's updates"</span>
                            </span>
                            <span class="material-symbols-outlined text-[18px] text-amber-action">auto_awesome</span>
                        </button>
                    </aside>
                </div>
            </div>
            <!-- Inline Interaction Logic for Filter Tabs & Mark as Read -->
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const tabs = document.querySelectorAll('.filter-tab');
                    const items = document.querySelectorAll('.notification-item');
                    const markAllBtn = document.getElementById('markAllReadBtn');

                    tabs.forEach(tab => {
                        tab.addEventListener('click', () => {
                            tabs.forEach(t => {
                                t.classList.remove('bg-slate-authority', 'text-canvas-ivory', 'font-semibold');
                                t.classList.add('bg-surface-container', 'text-on-surface-variant', 'font-medium');
                            });
                            tab.classList.remove('bg-surface-container', 'text-on-surface-variant', 'font-medium');
                            tab.classList.add('bg-slate-authority', 'text-canvas-ivory', 'font-semibold');

                            const category = tab.getAttribute('data-category');
                            items.forEach(item => {
                                if (category === 'all') {
                                    item.style.display = 'block';
                                } else if (category === 'unread') {
                                    item.style.display = item.classList.contains('unread') ? 'block' : 'none';
                                } else {
                                    item.style.display = item.classList.contains(category) ? 'block' : 'none';
                                }
                            });
                        });
                    });

                    if (markAllBtn) {
                        markAllBtn.addEventListener('click', () => {
                            items.forEach(item => {
                                item.classList.remove('unread');
                                item.classList.add('read', 'opacity-90');
                            });
                            const unreadPill = document.querySelector('[data-category="unread"] span');
                            if (unreadPill) {
                                unreadPill.textContent = '0';
                                unreadPill.classList.replace('bg-amber-action', 'bg-surface-container-highest');
                                unreadPill.classList.replace('text-slate-authority', 'text-on-surface-variant');
                            }
                            markAllBtn.innerHTML = '<span class="material-symbols-outlined text-[18px] text-status-green">check_circle</span><span>All Read</span>';
                            markAllBtn.classList.add('pointer-events-none', 'opacity-80');
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