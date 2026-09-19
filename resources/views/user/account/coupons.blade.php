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
            <!-- Interactive & Visual Styling via Tailwind -->
            <div class="max-w-container-max w-full mx-auto px-gutter-md py-gutter-md flex flex-col gap-gutter-lg">
                <!-- SUB-HEADER NAVIGATION & ESCROW TELEMETRY BAR -->
                <div
                    class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 p-4 rounded-xl bg-card-white shadow-sm">
                    <div class="flex flex-wrap items-center gap-3">
                        <a class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface-container-high hover:bg-surface-container text-slate-authority font-button-text text-body-small transition-colors"
                            href="#">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            <span>Back to Account</span>
                        </a>
                        <div class="h-4 w-px bg-surface-variant hidden sm:block"></div>
                        <nav
                            class="flex items-center gap-1.5 font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider">
                            <a class="hover:text-slate-authority transition-colors" href="#">Home</a>
                            <span>/</span>
                            <a class="hover:text-slate-authority transition-colors" href="#">Account</a>
                            <span>/</span>
                            <span class="text-slate-authority font-semibold">Invoices</span>
                        </nav>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium">
                            <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                            <span>ESCROW VERIFIED BUYER • VAULT #ESC-9041 • UID: BZ-891-IN</span>
                        </div>
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-authority text-canvas-ivory font-label-micro text-label-micro font-semibold shadow-sm ml-auto lg:ml-0">
                            <span class="material-symbols-outlined text-[15px] text-amber-action">lock</span>
                            <span>Vault: ₹60,500.00</span>
                            <button aria-label="Vault Settings"
                                class="text-canvas-ivory/60 hover:text-canvas-ivory transition-colors" type="button">
                                <span class="material-symbols-outlined text-[15px]">settings</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- PAGE HEADER & PRIMARY ACTION CONTROLS -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="space-y-2 max-w-2xl">
                        <div
                            class="inline-flex items-center gap-2 font-label-eyebrow text-label-eyebrow text-amber-action tracking-wider uppercase">
                            <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                            <span>Account Management • GST Compliant Tax Invoices • Cryptographic Proof</span>
                        </div>
                        <h1
                            class="font-display-hero text-display-hero-mobile sm:text-display-hero text-slate-authority tracking-tight font-bold">
                            Invoices &amp; Receipts
                        </h1>
                        <p class="font-body-regular text-body-regular text-on-surface-variant">
                            Download your purchase invoices, verified GST tax receipts, and cryptographic escrow
                            disbursement records.
                        </p>
                    </div>
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                        <button
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-card-white shadow-sm hover:bg-surface-container-low text-slate-authority font-button-text text-body-small transition-all active:scale-[0.99] w-full sm:w-auto"
                            type="button">
                            <span
                                class="material-symbols-outlined text-[20px] text-on-surface-variant">folder_zip</span>
                            <span>Bulk Download (ZIP)</span>
                        </button>
                        <button
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm hover:opacity-95 transition-all active:scale-[0.99] w-full sm:w-auto"
                            type="button">
                            <span class="material-symbols-outlined text-[20px]">file_download</span>
                            <span>Export Tax Summary (FY 2025-26)</span>
                        </button>
                    </div>
                </div>
                <!-- METRICS OVERVIEW STRIP (3D DIMENSIONAL SURFACES) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Card 1 -->
                    <div
                        class="p-card-padding rounded-xl bg-card-white shadow-sm relative overflow-hidden flex flex-col justify-between group hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <span
                                class="font-label-eyebrow text-label-eyebrow text-on-surface-variant uppercase tracking-wider">Total
                                Spent</span>
                            <div
                                class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-slate-authority">
                                <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                            </div>
                        </div>
                        <div>
                            <div
                                class="font-display-hero text-headline-section font-bold text-slate-authority tracking-tight">
                                ₹48,995.00</div>
                            <div class="font-label-micro text-label-micro text-on-surface-variant mt-1">Lifetime
                                marketplace orders (14 invoices)</div>
                        </div>
                        <div
                            class="mt-4 pt-3 bg-surface-container-lowest flex items-center justify-between text-status-green font-label-micro text-label-micro">
                            <span class="flex items-center gap-1 font-semibold">
                                <span class="material-symbols-outlined text-[14px]">trending_up</span>+12.4% vs last FY
                            </span>
                            <span class="text-on-surface-variant">Audited</span>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div
                        class="p-card-padding rounded-xl bg-card-white shadow-sm relative overflow-hidden flex flex-col justify-between group hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <span
                                class="font-label-eyebrow text-label-eyebrow text-on-surface-variant uppercase tracking-wider">GST
                                Input Credit</span>
                            <div
                                class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-amber-action">
                                <span class="material-symbols-outlined text-[18px]">percent</span>
                            </div>
                        </div>
                        <div>
                            <div
                                class="font-display-hero text-headline-section font-bold text-slate-authority tracking-tight">
                                ₹8,819.10</div>
                            <div class="font-label-micro text-label-micro text-on-surface-variant mt-1">18% eligible
                                commercial tax claim</div>
                        </div>
                        <div
                            class="mt-4 pt-3 bg-surface-container-lowest flex items-center justify-between text-slate-authority font-label-micro text-label-micro">
                            <span class="text-status-green flex items-center gap-1 font-semibold">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>GSTR-2B synced
                            </span>
                            <span class="text-on-surface-variant">Auto-reconciled</span>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div
                        class="p-card-padding rounded-xl bg-card-white shadow-sm relative overflow-hidden flex flex-col justify-between group hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <span
                                class="font-label-eyebrow text-label-eyebrow text-on-surface-variant uppercase tracking-wider">Latest
                                Invoice</span>
                            <div
                                class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-slate-authority">
                                <span class="material-symbols-outlined text-[18px]">article</span>
                            </div>
                        </div>
                        <div>
                            <div
                                class="font-display-hero text-headline-section font-bold text-slate-authority tracking-tight">
                                #INV-00128</div>
                            <div class="font-label-micro text-label-micro text-on-surface-variant mt-1">Generated 15 Sep
                                2026 • Verified</div>
                        </div>
                        <div
                            class="mt-4 pt-3 bg-surface-container-lowest flex items-center justify-between text-slate-authority font-label-micro text-label-micro">
                            <span class="font-mono font-medium">₹4,998.00</span>
                            <span
                                class="px-1.5 py-0.5 rounded bg-status-green/10 text-status-green font-semibold">PAID</span>
                        </div>
                    </div>
                    <!-- Card 4 -->
                    <div
                        class="p-card-padding rounded-xl bg-card-white shadow-sm relative overflow-hidden flex flex-col justify-between group hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <span
                                class="font-label-eyebrow text-label-eyebrow text-on-surface-variant uppercase tracking-wider">Escrow
                                Settlement</span>
                            <div
                                class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-status-green">
                                <span class="material-symbols-outlined text-[18px]">format_image_left</span>
                            </div>
                        </div>
                        <div>
                            <div
                                class="font-display-hero text-headline-section font-bold text-status-green tracking-tight">
                                100% Cleared</div>
                            <div class="font-label-micro text-label-micro text-on-surface-variant mt-1">Instant
                                cryptographic audit hash</div>
                        </div>
                        <div
                            class="mt-4 pt-3 bg-surface-container-lowest flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                            <span class="font-mono text-slate-authority truncate max-w-[120px]">0x9f4a...812c</span>
                            <span class="text-status-green font-semibold">Immutable</span>
                        </div>
                    </div>
                </div>
                <!-- FILTER TABS & SEARCH BAR -->
                <div
                    class="p-3 sm:p-4 rounded-xl bg-card-white shadow-sm flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                    <!-- Tabs -->
                    <div class="flex flex-wrap items-center gap-1.5 p-1 bg-surface-container rounded-lg">
                        <button
                            class="px-3.5 py-1.5 rounded-md bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold shadow-sm inline-flex items-center gap-2"
                            type="button">
                            <span>All Invoices</span>
                            <span
                                class="px-1.5 py-0.2 rounded-full bg-card-white/20 text-canvas-ivory font-label-micro text-label-micro">14</span>
                        </button>
                        <button
                            class="px-3.5 py-1.5 rounded-md text-on-surface-variant hover:text-slate-authority font-button-text text-body-small font-medium transition-colors inline-flex items-center gap-2"
                            type="button">
                            <span>FY 2025-26</span>
                            <span
                                class="px-1.5 py-0.2 rounded-full bg-surface-container-highest text-on-surface-variant font-label-micro text-label-micro">9</span>
                        </button>
                        <button
                            class="px-3.5 py-1.5 rounded-md text-on-surface-variant hover:text-slate-authority font-button-text text-body-small font-medium transition-colors inline-flex items-center gap-2"
                            type="button">
                            <span>FY 2024-25</span>
                            <span
                                class="px-1.5 py-0.2 rounded-full bg-surface-container-highest text-on-surface-variant font-label-micro text-label-micro">5</span>
                        </button>
                        <button
                            class="px-3.5 py-1.5 rounded-md text-on-surface-variant hover:text-slate-authority font-button-text text-body-small font-medium transition-colors inline-flex items-center gap-2"
                            type="button">
                            <span>Tax Deductible</span>
                            <span
                                class="px-1.5 py-0.2 rounded-full bg-surface-container-highest text-on-surface-variant font-label-micro text-label-micro">6</span>
                        </button>
                    </div>
                    <!-- Search & Controls -->
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <div class="relative w-full sm:w-72">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                            <input
                                class="w-full pl-9 pr-3 py-2 rounded-lg bg-surface-container text-body-small font-body-small text-slate-authority placeholder:text-on-surface-variant/60 focus:outline-none focus:ring-1 focus:ring-amber-action"
                                placeholder="Search by Invoice #, Order ID, or Item..." type="text" />
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div
                                class="relative flex items-center bg-surface-container rounded-lg px-3 py-2 w-full sm:w-auto">
                                <span
                                    class="material-symbols-outlined text-[18px] text-on-surface-variant mr-1.5">sort</span>
                                <select
                                    class="bg-transparent font-body-small text-body-small font-semibold text-slate-authority focus:outline-none cursor-pointer pr-4">
                                    <option value="recent">Sort: Most Recent</option>
                                    <option value="amount-high">Amount: High to Low</option>
                                    <option value="amount-low">Amount: Low to High</option>
                                    <option value="oldest">Date: Oldest First</option>
                                </select>
                            </div>
                            <button aria-label="Filter Options"
                                class="p-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-slate-authority transition-colors flex items-center justify-center"
                                type="button">
                                <span class="material-symbols-outlined text-[20px]">tune</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- INVOICES LIST SECTION -->
                <div class="flex flex-col gap-4">
                    <!-- CARD 1 (Featured Recent Invoice) -->
                    <div
                        class="p-card-padding rounded-xl bg-card-white shadow-sm flex flex-col gap-4 relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-action"></div>
                        <!-- Header row -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pl-2">
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow font-bold text-slate-authority bg-surface-container px-2.5 py-1 rounded">INVOICE
                                        #INV-00128</span>
                                    <span class="font-label-micro text-label-micro text-on-surface-variant">Order
                                        #BZ00128</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[15px]">schedule</span>
                                    <span>15 Sep 2026 • 11:42 AM</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-bold">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span> Payment:
                                    Paid ✓
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container text-slate-authority font-label-micro text-label-micro font-medium">
                                    <span
                                        class="material-symbols-outlined text-[14px] text-amber-action">verified</span>
                                    GST Verified • 18% Tax Included
                                </span>
                            </div>
                        </div>
                        <!-- Body row -->
                        <div
                            class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 p-4 rounded-lg bg-surface-container-low pl-4">
                            <div class="flex items-start sm:items-center gap-4 min-w-0">
                                <div
                                    class="relative w-16 h-16 rounded-lg bg-card-white overflow-hidden shrink-0 shadow-sm flex items-center justify-center">
                                    <img class="w-full h-full object-cover"
                                        data-alt="High quality studio photography of Sony WH-1000XM5 wireless noise cancelling headphones in matte black finish on warm neutral backdrop"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQNb_kJ2obfHcQ0pYuO32rwpii4Tp4Tj_YU3pWaIFWw7Cv_UH73IggbJgue6Y5Ama4FiLaURuEHrPKKi-oToy20VK9CeWSq4BSunLyv9e4MmNjHYUEx8jeUGDekV0qefvis8NocYGCZdvHyJNfK56NF-YhlQyeW9u-VMKT0dUmjCGJgQdAyxbyYPJqJ_CJ02F5XRCUam4m1v8nOAhTZGtksOCZDAv4v7q7qEaKfrA" />
                                    <span
                                        class="absolute bottom-1 right-1 px-1 rounded bg-slate-authority/80 text-canvas-ivory font-label-micro text-label-micro">+2</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h2
                                            class="font-title-card text-title-card font-semibold text-slate-authority truncate">
                                            Wireless Headphones + 2 items</h2>
                                        <span
                                            class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-label-micro text-label-micro">Electronics</span>
                                    </div>
                                    <p class="font-body-small text-body-small text-on-surface-variant truncate mt-0.5">
                                        Sony WH-1000XM5 Wireless, Braided Audio Cable, AudioCraft Hard Carry Case
                                    </p>
                                    <div
                                        class="flex items-center gap-2 mt-1.5 font-label-micro text-label-micro text-slate-authority">
                                        <span class="font-medium text-amber-action">Sold by: AudioCraft Official
                                            Stall</span>
                                        <span class="text-on-surface-variant">GSTIN: 27AABCA1234F1Z5</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex flex-row lg:flex-col items-baseline lg:items-end justify-between lg:justify-center shrink-0 pt-2 lg:pt-0">
                                <div class="text-left lg:text-right">
                                    <div class="font-display-hero text-headline-section font-bold text-slate-authority">
                                        ₹4,998.00</div>
                                    <div class="font-label-micro text-label-micro text-on-surface-variant">Subtotal
                                        ₹4,235.59 + IGST ₹762.41</div>
                                </div>
                            </div>
                        </div>
                        <!-- Footer / Action row -->
                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-2 pl-2">
                            <div
                                class="inline-flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant bg-surface-container px-2.5 py-1 rounded">
                                <span
                                    class="material-symbols-outlined text-[15px] text-status-green">verified_user</span>
                                <span>HASH: 0x9f4a...812c (Instant Escrow Release Verified)</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
                                <button
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-surface-container-high hover:bg-surface-container text-slate-authority font-button-text text-body-small font-semibold transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined text-[17px]">visibility</span>
                                    <span>View Invoice</span>
                                </button>
                                <button
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 shadow-sm transition-all active:scale-[0.99]"
                                    type="button">
                                    <span class="material-symbols-outlined text-[17px]">download</span>
                                    <span>Download PDF</span>
                                </button>
                                <button aria-label="Email Receipt"
                                    class="p-2 rounded-lg bg-surface-container-high hover:bg-surface-container text-slate-authority transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">mail</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- CARD 2 -->
                    <div
                        class="p-card-padding rounded-xl bg-card-white shadow-sm flex flex-col gap-4 relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow font-bold text-slate-authority bg-surface-container px-2.5 py-1 rounded">INVOICE
                                        #INV-00119</span>
                                    <span class="font-label-micro text-label-micro text-on-surface-variant">Order
                                        #BZ00119</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                                    <span>10 Sep 2026</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-bold">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span> Payment:
                                    Paid ✓
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 p-4 rounded-lg bg-surface-container-low">
                            <div class="flex items-start sm:items-center gap-4 min-w-0">
                                <div
                                    class="w-16 h-16 rounded-lg bg-card-white overflow-hidden shrink-0 shadow-sm flex items-center justify-center">
                                    <img class="w-full h-full object-cover"
                                        data-alt="Artisanal minimalist hand-thrown ceramic pour over dripper and matching stoneware coffee mug on warm stone slab with warm sunlight"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZDVuGk9ZFtmok4uIzXMWRWsOtfcPQ8L4LaeV0BLhmpomSJO0OWrYY3O0AuVW7Jkw4gBJL0a3Gk6uem5KAgamsfZAvl_B_Eq3Z-LI8rqpbehtjzZFEZhcobwYdJMsTEIAi6MGDhN8wxAED0KZmrv3tyws25zj1oKFwQvKdEA0ZK-zKajAOHkJsq_D2BdCo6ZGU5nZEZc1ziX5vD6z9rsjfLRlSQxiqh56Cz9YWqnE" />
                                </div>
                                <div class="min-w-0">
                                    <h2
                                        class="font-title-card text-title-card font-semibold text-slate-authority truncate">
                                        Artisan Ceramic Pour-Over Coffee Dripper + Stoneware Mug</h2>
                                    <p class="font-body-small text-body-small text-on-surface-variant truncate mt-0.5">
                                        Sold by Clay &amp; Stone Studio (Handcrafted Kitchenware)
                                    </p>
                                    <div
                                        class="flex items-center gap-2 mt-1 font-label-micro text-label-micro text-on-surface-variant">
                                        <span>HSN: 691200 • Tax Rate: 18%</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex flex-row lg:flex-col items-baseline lg:items-end justify-between lg:justify-center shrink-0">
                                <div class="text-left lg:text-right">
                                    <div class="font-display-hero text-headline-section font-bold text-slate-authority">
                                        ₹2,499.00</div>
                                    <div class="font-label-micro text-label-micro text-on-surface-variant">GST Paid:
                                        ₹381.20</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-1">
                            <div class="font-label-micro text-label-micro text-on-surface-variant">
                                Escrow Released via Transaction: <span
                                    class="font-mono text-slate-authority">#TXN-881902</span>
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                <button
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-surface-container-high hover:bg-surface-container text-slate-authority font-button-text text-body-small font-semibold transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined text-[17px]">visibility</span>
                                    <span>View Invoice</span>
                                </button>
                                <button
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 shadow-sm transition-all active:scale-[0.99]"
                                    type="button">
                                    <span class="material-symbols-outlined text-[17px]">download</span>
                                    <span>Download PDF</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- CARD 3 -->
                    <div
                        class="p-card-padding rounded-xl bg-card-white shadow-sm flex flex-col gap-4 relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow font-bold text-slate-authority bg-surface-container px-2.5 py-1 rounded">INVOICE
                                        #INV-00087</span>
                                    <span class="font-label-micro text-label-micro text-on-surface-variant">Order
                                        #BZ00087</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                                    <span>28 Aug 2026</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-bold">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span> Payment:
                                    Paid ✓
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 p-4 rounded-lg bg-surface-container-low">
                            <div class="flex items-start sm:items-center gap-4 min-w-0">
                                <div
                                    class="w-16 h-16 rounded-lg bg-card-white overflow-hidden shrink-0 shadow-sm flex items-center justify-center">
                                    <img class="w-full h-full object-cover"
                                        data-alt="Retro beige and cream vintage style mechanical keyboard with hot swappable RGB keycaps on a clean timber workspace"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAHgmXwrWdwPJxDN0OXPTbpNwL8W-MP3smjhrkYM0yjuGILfo02qxcEvyBGpTIbjkVG6_Uaq5dS5SmOrm6EuhvApFLkAYq0AsFFDwxC1cLwrGer7GBWLTbbfZ2f9Ig55fmfQBgNQkwa463G9JkywxpSg3OiaMpgp2wUMk4fZ4knqcj0d9nina6ySk8NEfxo2Bf5tH6YKVvbA5WEipTWs39-OGDjPnaUkfw_soS5qpA" />
                                </div>
                                <div class="min-w-0">
                                    <h2
                                        class="font-title-card text-title-card font-semibold text-slate-authority truncate">
                                        Retro Cream Mechanical Keyboard (Hot-swap RGB)</h2>
                                    <p class="font-body-small text-body-small text-on-surface-variant truncate mt-0.5">
                                        Sold by KeyCrafters Guild • 75% Layout, Gateron Oil Kings
                                    </p>
                                    <div
                                        class="flex items-center gap-2 mt-1 font-label-micro text-label-micro text-on-surface-variant">
                                        <span>Tax Deductible IT Expense Eligible</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex flex-row lg:flex-col items-baseline lg:items-end justify-between lg:justify-center shrink-0">
                                <div class="text-left lg:text-right">
                                    <div class="font-display-hero text-headline-section font-bold text-slate-authority">
                                        ₹3,500.00</div>
                                    <div class="font-label-micro text-label-micro text-on-surface-variant">GST Included
                                        (18%)</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-1">
                            <div class="font-label-micro text-label-micro text-on-surface-variant">
                                Escrow Released via Transaction: <span
                                    class="font-mono text-slate-authority">#TXN-772109</span>
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                <button
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-surface-container-high hover:bg-surface-container text-slate-authority font-button-text text-body-small font-semibold transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined text-[17px]">visibility</span>
                                    <span>View Invoice</span>
                                </button>
                                <button
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 shadow-sm transition-all active:scale-[0.99]"
                                    type="button">
                                    <span class="material-symbols-outlined text-[17px]">download</span>
                                    <span>Download PDF</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- CARD 4 -->
                    <div
                        class="p-card-padding rounded-xl bg-card-white shadow-sm flex flex-col gap-4 relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow font-bold text-slate-authority bg-surface-container px-2.5 py-1 rounded">INVOICE
                                        #INV-00042</span>
                                    <span class="font-label-micro text-label-micro text-on-surface-variant">Order
                                        #BZ00042</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                                    <span>14 Jul 2026</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-bold">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span> Payment:
                                    Paid ✓
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 p-4 rounded-lg bg-surface-container-low">
                            <div class="flex items-start sm:items-center gap-4 min-w-0">
                                <div
                                    class="w-16 h-16 rounded-lg bg-card-white overflow-hidden shrink-0 shadow-sm flex items-center justify-center">
                                    <img class="w-full h-full object-cover"
                                        data-alt="Apple Watch Ultra with vibrant amber orange ocean elastomer band resting on modern slate texture display stand"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4uz6dqS301xjK2mO7GiRctxZUW45dBWytA_rztumANCa4SCRcxdVokZtG15Z9xt-wttVFm90sBlU9J6zyrnUj6-EfcJnerDQwTCJgUo5SU67P2fkIVXGv_N7Zw4E0wY-0ipMOoKQ_jgR8kIf6SqeGvXJYeimxCm2lfIp8iTUlS_kksifVvcoelgUlU-soXsXs1iwZRNhqv8qSJ8nRfVapNKkROc05LPZ5nFu7E_w" />
                                </div>
                                <div class="min-w-0">
                                    <h2
                                        class="font-title-card text-title-card font-semibold text-slate-authority truncate">
                                        Apple Watch Ultra Ocean Band (Amber Orange)</h2>
                                    <p class="font-body-small text-body-small text-on-surface-variant truncate mt-0.5">
                                        Sold by Cupertino Gear Vault • Titanium Buckle Edition
                                    </p>
                                    <div
                                        class="flex items-center gap-2 mt-1 font-label-micro text-label-micro text-on-surface-variant">
                                        <span>Serial: #AP-8849-OC</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex flex-row lg:flex-col items-baseline lg:items-end justify-between lg:justify-center shrink-0">
                                <div class="text-left lg:text-right">
                                    <div class="font-display-hero text-headline-section font-bold text-slate-authority">
                                        ₹4,199.00</div>
                                    <div class="font-label-micro text-label-micro text-on-surface-variant">GST Included
                                        (18%)</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pt-1">
                            <div class="font-label-micro text-label-micro text-on-surface-variant">
                                Escrow Released via Transaction: <span
                                    class="font-mono text-slate-authority">#TXN-554101</span>
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                <button
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-surface-container-high hover:bg-surface-container text-slate-authority font-button-text text-body-small font-semibold transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined text-[17px]">visibility</span>
                                    <span>View Invoice</span>
                                </button>
                                <button
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 shadow-sm transition-all active:scale-[0.99]"
                                    type="button">
                                    <span class="material-symbols-outlined text-[17px]">download</span>
                                    <span>Download PDF</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- PAGINATION NAV -->
                <div class="flex items-center justify-between p-4 rounded-xl bg-card-white shadow-sm">
                    <div class="font-body-small text-body-small text-on-surface-variant">
                        Showing <span class="font-semibold text-slate-authority">1-4</span> of <span
                            class="font-semibold text-slate-authority">14</span> invoices
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-2 rounded-lg text-on-surface-variant/40 cursor-not-allowed" disabled=""
                            type="button">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </button>
                        <button
                            class="w-8 h-8 rounded-lg bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold flex items-center justify-center shadow-sm"
                            type="button">
                            1
                        </button>
                        <button
                            class="w-8 h-8 rounded-lg hover:bg-surface-container text-slate-authority font-button-text text-body-small flex items-center justify-center transition-colors"
                            type="button">
                            2
                        </button>
                        <button
                            class="w-8 h-8 rounded-lg hover:bg-surface-container text-slate-authority font-button-text text-body-small flex items-center justify-center transition-colors"
                            type="button">
                            3
                        </button>
                        <button class="p-2 rounded-lg text-slate-authority hover:bg-surface-container transition-colors"
                            type="button">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>
                </div>
                <!-- GST & BUSINESS BILLING INFORMATION BANNER (DIMENSIONAL AUTHORITY BLOCK) -->
                <div
                    class="rounded-xl bg-slate-authority text-canvas-ivory p-banner-padding relative overflow-hidden shadow-lg">
                    <div
                        class="absolute -right-10 -bottom-10 w-64 h-64 bg-amber-action/10 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                        <div class="space-y-2 max-w-2xl">
                            <div
                                class="inline-flex items-center gap-2 font-label-eyebrow text-label-eyebrow text-amber-action uppercase tracking-widest font-semibold">
                                <span class="material-symbols-outlined text-[16px]">corporate_fare</span>
                                <span>Enterprise Tax Automation</span>
                            </div>
                            <h2
                                class="font-headline-section text-headline-section font-bold tracking-tight text-canvas-ivory">
                                Need business GSTIN invoices for tax filing?
                            </h2>
                            <p class="font-body-regular text-body-regular text-canvas-ivory/70">
                                Your registered GSTIN (<span
                                    class="font-mono text-amber-action font-semibold">29AABCT1337M1Z8</span>) is
                                automatically mapped to all orders. Download aggregated quarterly reports in Excel or
                                JSON format.
                            </p>
                        </div>
                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full lg:w-auto shrink-0">
                            <button
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-canvas-ivory/10 hover:bg-canvas-ivory/20 text-canvas-ivory font-button-text text-body-small font-semibold transition-all w-full sm:w-auto"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                                <span>Configure GST Details</span>
                            </button>
                            <button
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 shadow-sm transition-all active:scale-[0.99] w-full sm:w-auto"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]">table_chart</span>
                                <span>Download Annual Tax Ledger</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FLOATING AI ASSISTANT DOCK TRIGGER -->
            <div class="fixed bottom-6 right-6 z-40">
                <button
                    class="group flex items-center gap-2.5 px-4 py-3 rounded-full bg-slate-authority text-canvas-ivory shadow-xl hover:shadow-2xl hover:scale-[1.02] transition-all border border-card-white/10"
                    type="button">
                    <div
                        class="w-6 h-6 rounded-full bg-amber-action text-slate-authority flex items-center justify-center font-bold text-xs animate-spin-slow">
                        ✦
                    </div>
                    <span class="font-body-small text-body-small font-medium pr-1">
                        Ask Bazaario AI: <span class="text-amber-action group-hover:underline">"Calculate total
                            electronics expense for Q2"</span>
                    </span>
                    <span
                        class="material-symbols-outlined text-[18px] text-canvas-ivory/60 group-hover:text-canvas-ivory">arrow_forward</span>
                </button>
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