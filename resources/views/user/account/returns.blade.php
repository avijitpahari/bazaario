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
            <!-- Interactive Modal Container for "Request Return" -->
            <div class="fixed inset-0 z-50 hidden bg-slate-authority/60 backdrop-blur-sm items-center justify-center p-4"
                id="modal-request-return">
                <div class="bg-card-white rounded-2xl max-w-lg w-full p-6 shadow-2xl flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="material-symbols-outlined text-amber-action text-[24px]">assignment_return</span>
                            <h3 class="font-headline-section text-headline-section font-bold text-slate-authority">Open
                                Return Request</h3>
                        </div>
                        <button class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant"
                            onclick="document.getElementById('modal-request-return').classList.add('hidden'); document.getElementById('modal-request-return').classList.remove('flex');">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    <p class="font-body-small text-body-small text-on-surface-variant">Select an eligible verified
                        purchase under Bazaario Buyer Protection. Instant collateral release activates once scanned by
                        courier.</p>
                    <div class="flex flex-col gap-3">
                        <label class="font-label-eyebrow text-label-eyebrow uppercase text-slate-authority">Select
                            Order</label>
                        <div
                            class="p-3 bg-surface-container-low rounded-xl flex items-center justify-between cursor-pointer hover:bg-surface-container">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-amber-action">inventory_2</span>
                                <div>
                                    <p class="font-button-text text-body-small font-semibold text-slate-authority">Order
                                        #BZ-2026-00128</p>
                                    <p class="font-label-micro text-label-micro text-on-surface-variant">Sony WH-1000XM5
                                        • Delivered 3 days ago</p>
                                </div>
                            </div>
                            <span
                                class="font-label-micro text-label-micro bg-status-green/10 text-status-green px-2 py-0.5 rounded-full font-semibold">Eligible
                                (11d left)</span>
                        </div>
                        <label class="font-label-eyebrow text-label-eyebrow uppercase text-slate-authority mt-2">Primary
                            Return Reason</label>
                        <select
                            class="w-full bg-surface-container-low rounded-xl px-3 py-2.5 font-body-small text-body-small text-slate-authority focus:outline-none">
                            <option>Defective / Hardware acoustic rattle</option>
                            <option>Item materially different from description</option>
                            <option>Package damaged during transit</option>
                            <option>Wrong variant or color delivered</option>
                        </select>
                        <label
                            class="font-label-eyebrow text-label-eyebrow uppercase text-slate-authority mt-2">Preferred
                            Payout Destination</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-2.5 rounded-xl bg-slate-authority text-canvas-ivory flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-label-micro text-label-micro text-amber-action">RECOMMENDED</span>
                                    <span
                                        class="material-symbols-outlined text-[16px] text-status-green">check_circle</span>
                                </div>
                                <p class="font-button-text text-body-small font-bold">Liquid Vault</p>
                                <p class="font-label-micro text-label-micro opacity-70">Instant release (&lt;200ms)</p>
                            </div>
                            <div
                                class="p-2.5 rounded-xl bg-surface-container-low text-slate-authority flex flex-col gap-1">
                                <span class="font-label-micro text-label-micro text-on-surface-variant">ORIGINAL
                                    METHOD</span>
                                <p class="font-button-text text-body-small font-bold">UPI / Bank Direct</p>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">1-3 business days
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            class="px-4 py-2 font-button-text text-body-small text-slate-authority hover:bg-surface-container rounded-xl"
                            onclick="document.getElementById('modal-request-return').classList.add('hidden'); document.getElementById('modal-request-return').classList.remove('flex');">Cancel</button>
                        <button
                            class="px-5 py-2 font-button-text text-body-small bg-amber-action text-slate-authority rounded-xl font-semibold shadow-sm hover:opacity-95"
                            onclick="document.getElementById('modal-request-return').classList.add('hidden'); document.getElementById('modal-request-return').classList.remove('flex');">Generate
                            Return Waybill</button>
                    </div>
                </div>
            </div>
            <!-- Telemetry Bar -->
            <div class="w-full bg-surface-container-low/80 backdrop-blur-md">
                <div
                    class="max-w-container-max mx-auto px-gutter-md py-3 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-card-white shadow-sm font-button-text text-body-small font-medium text-slate-authority hover:bg-surface-container transition-colors"
                            href="#">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            <span>Back to Account</span>
                        </a>
                        <div
                            class="hidden sm:flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant">
                            <span>Home</span>
                            <span>/</span>
                            <span>Account</span>
                            <span>/</span>
                            <span class="text-slate-authority font-bold">Returns &amp; Refunds</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="hidden md:flex items-center gap-2 bg-card-white/80 px-3 py-1.5 rounded-full shadow-sm font-label-micro text-label-micro text-slate-authority">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-status-green opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-status-green"></span>
                            </span>
                            <span class="font-mono uppercase">Escrow Verified Buyer</span>
                            <span class="text-on-surface-variant">•</span>
                            <span class="font-mono text-on-surface-variant">VAULT #ESC-9041</span>
                            <span class="text-on-surface-variant">•</span>
                            <span class="font-mono text-on-surface-variant">UID: BZ-891-IN</span>
                        </div>
                        <div
                            class="flex items-center gap-2 bg-slate-authority text-canvas-ivory px-3.5 py-1.5 rounded-full shadow-sm">
                            <span class="material-symbols-outlined text-[16px] text-amber-action">lock</span>
                            <span class="font-label-eyebrow text-label-eyebrow">Vault: ₹60,500.00</span>
                            <button class="ml-1 text-on-primary-container hover:text-canvas-ivory transition-colors"
                                title="Vault Settings">
                                <span class="material-symbols-outlined text-[14px]">settings</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Container -->
            <div class="max-w-container-max mx-auto px-gutter-md py-8 flex flex-col gap-8">
                <!-- Hero / Header Section -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-2">
                    <div class="flex flex-col gap-2 max-w-2xl">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-amber-action">verified</span>
                            <span
                                class="font-label-eyebrow text-label-eyebrow text-amber-action uppercase font-semibold">
                                Account Management • Escrow Dispute &amp; Refund Guarantee • 100% Buyer Shield
                            </span>
                        </div>
                        <h1 class="font-display-hero text-display-hero font-bold tracking-tight text-slate-authority">
                            Returns &amp; Refunds
                        </h1>
                        <p class="font-body-regular text-body-regular text-on-surface-variant">
                            Manage your return requests, track cryptographic escrow refunds, and inspect return transit
                            telemetry in real-time.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <button
                            class="bg-amber-action text-slate-authority font-button-text text-button-text px-5 py-2.5 rounded-xl font-semibold shadow-sm hover:opacity-95 active:scale-[0.99] transition-all flex items-center gap-2"
                            onclick="document.getElementById('modal-request-return').classList.remove('hidden'); document.getElementById('modal-request-return').classList.add('flex');">
                            <span class="material-symbols-outlined text-[20px]">add</span>
                            <span>Request a Return</span>
                        </button>
                        <button
                            class="bg-card-white shadow-sm hover:bg-surface-container-low text-slate-authority font-button-text text-button-text px-4 py-2.5 rounded-xl font-medium transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-status-green">shield</span>
                            <span>Escrow Dispute Policy</span>
                        </button>
                    </div>
                </div>
                <!-- 3D Dimensional Glass Metrics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Metric 1 -->
                    <div
                        class="bg-card-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col gap-3 relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -top-4 w-20 h-20 bg-amber-action/10 rounded-full blur-xl group-hover:scale-125 transition-transform">
                        </div>
                        <div class="flex items-center justify-between">
                            <span
                                class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant font-mono">Total
                                Refunded</span>
                            <span class="material-symbols-outlined text-amber-action text-[22px]">currency_rupee</span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="font-display-hero text-headline-section font-bold text-slate-authority">₹7,298.00</span>
                            <span
                                class="font-label-micro text-label-micro text-status-green font-medium mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">lock_reset</span>
                                Lifetime via 100% Escrow Shield
                            </span>
                        </div>
                    </div>
                    <!-- Metric 2 -->
                    <div
                        class="bg-card-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col gap-3 relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -top-4 w-20 h-20 bg-status-green/10 rounded-full blur-xl group-hover:scale-125 transition-transform">
                        </div>
                        <div class="flex items-center justify-between">
                            <span
                                class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant font-mono">Active
                                Returns</span>
                            <span class="material-symbols-outlined text-status-green text-[22px]">pending_actions</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-display-hero text-headline-section font-bold text-slate-authority">1 In
                                Progress</span>
                            <span
                                class="font-label-micro text-label-micro text-on-surface-variant font-medium mt-0.5 flex items-center gap-1">
                                Return #RET-00128 • Approved ✓
                            </span>
                        </div>
                    </div>
                    <!-- Metric 3 -->
                    <div
                        class="bg-card-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col gap-3 relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -top-4 w-20 h-20 bg-primary-alpha-10 rounded-full blur-xl group-hover:scale-125 transition-transform">
                        </div>
                        <div class="flex items-center justify-between">
                            <span
                                class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant font-mono">Average
                                Refund Time</span>
                            <span class="material-symbols-outlined text-slate-authority text-[22px]">timer</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-display-hero text-headline-section font-bold text-slate-authority">4.2
                                Hours</span>
                            <span
                                class="font-label-micro text-label-micro text-status-green font-medium mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">bolt</span>
                                Instant release on courier scan
                            </span>
                        </div>
                    </div>
                    <!-- Metric 4 -->
                    <div
                        class="bg-card-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col gap-3 relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -top-4 w-20 h-20 bg-status-green/15 rounded-full blur-xl group-hover:scale-125 transition-transform">
                        </div>
                        <div class="flex items-center justify-between">
                            <span
                                class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant font-mono">Escrow
                                Protection</span>
                            <span class="material-symbols-outlined text-status-green text-[22px]">verified_user</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-display-hero text-headline-section font-bold text-slate-authority">100%
                                Secured</span>
                            <span
                                class="font-label-micro text-label-micro text-on-surface-variant font-medium mt-0.5 flex items-center gap-1">
                                Zero seller-delay guarantee
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Filter Tabs & Sort Controls -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-2">
                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap items-center gap-2 p-1.5 bg-surface-container-high rounded-xl">
                        <button
                            class="px-3.5 py-1.5 rounded-lg bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold shadow-sm transition-all">
                            All Returns (3)
                        </button>
                        <button
                            class="px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-slate-authority font-button-text text-body-small transition-all">
                            Requested (0)
                        </button>
                        <button
                            class="px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-slate-authority font-button-text text-body-small flex items-center gap-1.5 transition-all">
                            <span>Approved</span>
                            <span
                                class="px-1.5 py-0.2 rounded-full bg-status-green text-card-white font-label-micro text-label-micro font-bold">1</span>
                        </button>
                        <button
                            class="px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-slate-authority font-button-text text-body-small transition-all">
                            Completed / Refunded (2)
                        </button>
                        <button
                            class="px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-slate-authority font-button-text text-body-small transition-all">
                            Dispute Mediation (0)
                        </button>
                    </div>
                    <!-- Right Sync & Sort Controls -->
                    <div class="flex items-center gap-3 self-end sm:self-center">
                        <div
                            class="flex items-center gap-1.5 bg-surface-container-low px-2.5 py-1.5 rounded-lg text-on-surface-variant font-label-micro text-label-micro">
                            <span class="w-1.5 h-1.5 rounded-full bg-status-green animate-pulse"></span>
                            <span>Live Sync: 0.1s</span>
                        </div>
                        <div class="relative">
                            <select
                                class="bg-card-white shadow-sm font-body-small text-body-small text-slate-authority font-medium rounded-xl px-3.5 py-1.5 pr-8 focus:outline-none cursor-pointer appearance-none">
                                <option>Sort: Most Recent</option>
                                <option>Sort: Highest Amount</option>
                                <option>Sort: Status Active</option>
                            </select>
                            <span
                                class="material-symbols-outlined text-[16px] text-on-surface-variant absolute right-2.5 top-2.5 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>
                <!-- Returns List Section -->
                <div class="flex flex-col gap-4">
                    <!-- Featured Return Card: Sony WH-1000XM5 (From Inspiration Context) -->
                    <div
                        class="bg-card-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col gap-5 relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-amber-action"></div>
                        <!-- Card Header Bar -->
                        <div
                            class="flex flex-wrap items-center justify-between gap-3 pb-4 bg-surface-container-low/50 -mx-6 -mt-6 px-6 pt-5">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-slate-authority text-body-regular">RETURN
                                    #RET-00128</span>
                                <span
                                    class="font-label-micro text-label-micro text-on-surface-variant bg-surface-container-high px-2 py-0.5 rounded font-mono">TRANSIT
                                    TOKEN: #TT-9801-XC</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow bg-status-green/10 text-status-green font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-status-green"></span>
                                    Approved ✓
                                </span>
                            </div>
                        </div>
                        <!-- Inner Content Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                            <!-- Product Visual Anchor -->
                            <div class="lg:col-span-2 flex justify-center lg:justify-start">
                                <div
                                    class="w-28 h-28 sm:w-32 sm:h-32 rounded-xl bg-surface-container-high overflow-hidden shadow-inner flex items-center justify-center p-2 relative">
                                    <img class="w-full h-full object-cover rounded-lg"
                                        data-alt="Sony WH-1000XM5 matte black wireless noise cancelling headphones resting elegantly on a sculpted curved wooden headphone stand on a walnut desk with soft natural studio window lighting"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBEHZqBPXtvhqWQ1b5rBhTaWxObmZdeWEgfjUHtN_Jg9xfqoej7S4Z3FMolEsWBzzWI1EESsltBni1k-EIEDnlVd04lexc1HROA6unFhLvi8UY2N4SDL_kgZ1QR7NK6rpo8vWjn6gsjUJZAesJz417lfjgjmQMLp8jYLs2tvLEs3khELibvv5rnWAf8S2LZVj_AHzUTI6x3SkVec3l1aSiq-yZZzyBYAXL-qUuKbWW1khNyUfc8YMR3" />
                                    <div
                                        class="absolute bottom-1 right-1 bg-slate-authority/80 text-card-white font-label-micro text-label-micro px-1.5 py-0.5 rounded">
                                        Inspection Ready</div>
                                </div>
                            </div>
                            <!-- Product Details & Reason -->
                            <div class="lg:col-span-6 flex flex-col gap-2.5">
                                <div class="flex flex-col">
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant font-mono">ORDER
                                        #BZ-2026-00128 • PURCHASED FROM AUDIOCRAFT OFFICIAL STALL</span>
                                    <h3 class="font-title-card text-title-card font-bold text-slate-authority mt-0.5">
                                        Sony WH-1000XM5 Wireless Noise-Cancelling Headphones (Black)
                                    </h3>
                                </div>
                                <!-- Callout: Reason -->
                                <div class="bg-surface-container-low rounded-xl p-3 flex items-start gap-2.5">
                                    <span
                                        class="material-symbols-outlined text-amber-action text-[20px] mt-0.5">warning</span>
                                    <div class="flex flex-col">
                                        <span
                                            class="font-label-micro text-label-micro font-bold text-slate-authority uppercase font-mono">Verified
                                            Dispute Reason</span>
                                        <p class="font-body-small text-body-small text-on-surface-variant">Product
                                            damaged / Left earcup driver rattle at frequencies below 120Hz</p>
                                    </div>
                                </div>
                                <div
                                    class="flex flex-wrap items-center gap-y-1 gap-x-4 font-label-micro text-label-micro text-on-surface-variant">
                                    <span class="flex items-center gap-1">
                                        <span
                                            class="material-symbols-outlined text-[16px] text-on-surface-variant">schedule</span>
                                        Requested: 14 Sep 2026 • 10:24 AM
                                    </span>
                                    <span class="flex items-center gap-1 text-slate-authority font-medium">
                                        <span
                                            class="material-symbols-outlined text-[16px] text-amber-action">local_shipping</span>
                                        Courier Pickup: BlueDart Express #BD-RET-4412 (Today 2:00 PM)
                                    </span>
                                </div>
                            </div>
                            <!-- Financial & Actions Block -->
                            <div
                                class="lg:col-span-4 flex flex-col justify-between items-start lg:items-end gap-4 lg:pl-4 bg-surface-container-low/30 lg:bg-transparent p-4 lg:p-0 rounded-xl">
                                <div class="flex flex-col items-start lg:items-end">
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant uppercase font-mono">Guaranteed
                                        Escrow Refund</span>
                                    <span
                                        class="font-display-hero text-headline-section font-bold text-slate-authority text-status-green">₹2,499.00</span>
                                    <span
                                        class="font-label-micro text-label-micro text-slate-authority font-medium text-right mt-0.5">
                                        Destination: <span class="text-status-green font-bold">Liquid Escrow
                                            Vault</span> (Instant release)
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row lg:flex-col w-full gap-2">
                                    <button
                                        class="w-full bg-amber-action text-slate-authority font-button-text text-body-small px-4 py-2.5 rounded-xl font-semibold shadow-sm hover:opacity-95 transition-all flex items-center justify-center gap-2">
                                        <span>View Return Details</span>
                                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                    </button>
                                    <button
                                        class="w-full bg-card-white hover:bg-surface-container font-button-text text-body-small text-slate-authority px-4 py-2 rounded-xl font-medium shadow-sm transition-all flex items-center justify-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                        <span>Download Waybill PDF</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Telemetry Progress Timeline -->
                        <div class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-3">
                            <div
                                class="flex items-center justify-between font-label-micro text-label-micro text-on-surface-variant">
                                <span
                                    class="font-mono uppercase text-slate-authority font-semibold flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-amber-action">radar</span>
                                    Automated Escrow Transit Stepper
                                </span>
                                <span class="font-mono text-status-green">Phase 2 of 4 Active</span>
                            </div>
                            <div class="grid grid-cols-4 gap-2 text-center">
                                <div class="flex flex-col gap-1.5 items-center">
                                    <div class="w-full h-1.5 bg-status-green rounded-full"></div>
                                    <span class="font-label-micro text-label-micro font-bold text-status-green">1.
                                        Return Filed</span>
                                </div>
                                <div class="flex flex-col gap-1.5 items-center">
                                    <div class="w-full h-1.5 bg-amber-action rounded-full animate-pulse"></div>
                                    <span class="font-label-micro text-label-micro font-bold text-amber-action">2.
                                        Pickup Assigned</span>
                                </div>
                                <div class="flex flex-col gap-1.5 items-center">
                                    <div class="w-full h-1.5 bg-surface-container-highest rounded-full"></div>
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant opacity-60">3.
                                        Hub Scan</span>
                                </div>
                                <div class="flex flex-col gap-1.5 items-center">
                                    <div class="w-full h-1.5 bg-surface-container-highest rounded-full"></div>
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant opacity-60">4.
                                        Vault Payout</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Historical Return Card 2: Artisan Ceramic Pour-Over -->
                    <div
                        class="bg-card-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col gap-5">
                        <!-- Top bar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-3">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-slate-authority text-body-regular">RETURN
                                    #RET-00094</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant">Order
                                    #BZ-2026-00082</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow bg-status-green/10 text-status-green font-bold px-3 py-0.5 rounded-full flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Refund Credited ✓
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                            <div class="lg:col-span-2 flex justify-center lg:justify-start">
                                <div
                                    class="w-24 h-24 rounded-xl bg-surface-container-high overflow-hidden shadow-inner flex items-center justify-center p-2">
                                    <img class="w-full h-full object-cover rounded-lg"
                                        data-alt="Handcrafted minimalist ceramic pour over dripper coffee set in matte sandstone finish resting on a dark warm wooden countertop with coffee beans"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCUe1TQ5-OriQIsFtgMgnxsbCZ6TaJHqz58yXlgNgVWpoz-fBkHyPsPiazYtWSe31aofci-mSrRmakvYXGuOT4YBWcDTituPzVi6_-KXo1dVVRBxOm82D0yk5SGbRqxlxrVlDk66Hl6gcLskX_i6rY4vKYZZwYhibqA2LLQ6lz7qAD8a1Gjaklxp4VCVfm4yyGx1ljqaMKpBG9ZB_f2G9bJjZ6TS59Gh5FQ3BGrhgZGwIpk7e4eVMnW" />
                                </div>
                            </div>
                            <div class="lg:col-span-6 flex flex-col gap-1.5">
                                <h3 class="font-title-card text-title-card font-bold text-slate-authority">
                                    Artisan Ceramic Pour-Over Coffee Dripper Set
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant">
                                    Reason: Wrong glaze variant received (Olive instead of Sandstone)
                                </p>
                                <div
                                    class="font-label-micro text-label-micro text-on-surface-variant flex items-center gap-3">
                                    <span>Requested: 28 Aug 2026</span>
                                    <span>•</span>
                                    <span class="text-status-green">Credited to Vault on 30 Aug 2026 • Tx
                                        #TX-39218</span>
                                </div>
                            </div>
                            <div class="lg:col-span-4 flex flex-col lg:items-end justify-between gap-3">
                                <div class="flex flex-col lg:items-end">
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant uppercase font-mono">Amount
                                        Refunded</span>
                                    <span
                                        class="font-display-hero text-headline-section font-bold text-slate-authority">₹1,299.00</span>
                                </div>
                                <button
                                    class="px-4 py-2 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-button-text text-body-small font-medium transition-all flex items-center gap-1 self-start lg:self-end">
                                    <span>View Summary</span>
                                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Historical Return Card 3: Mechanical Keyboard -->
                    <div
                        class="bg-card-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col gap-5">
                        <!-- Top bar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-3">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-slate-authority text-body-regular">RETURN
                                    #RET-00041</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant">Order
                                    #BZ-2026-00019</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow bg-status-green/10 text-status-green font-bold px-3 py-0.5 rounded-full flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Refund Credited ✓
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                            <div class="lg:col-span-2 flex justify-center lg:justify-start">
                                <div
                                    class="w-24 h-24 rounded-xl bg-surface-container-high overflow-hidden shadow-inner flex items-center justify-center p-2">
                                    <img class="w-full h-full object-cover rounded-lg"
                                        data-alt="Retro cream mechanical keyboard with tactile switches and custom artisan keycaps on a felt desk mat beside a fountain pen and warm task lamp"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAM02QnuZmmBvyz_aAfxHHJOjt4nkIF9dDDYjxZewjT_x5hxNRRZ46HIHGcWlyUUr8-jkMaUpleMdxuQ7XvO5sCfSvUjsJJ_lVeWV23wEe-tzvowBblwg-UqEByCd_GRu-Q43XILMs1H_Aa7HXvxjPojiQyyUSt-vYxEDfPFlyFzGA0_S97OS43mTmpk4snYrLsejahCO7miaXxj5yrj-A9dIYoinWzko_dOMU5XCx6_RP0z3jHdeFg" />
                                </div>
                            </div>
                            <div class="lg:col-span-6 flex flex-col gap-1.5">
                                <h3 class="font-title-card text-title-card font-bold text-slate-authority">
                                    Retro Cream Mechanical Keyboard (Hot-swap RGB)
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant">
                                    Reason: Switch socket connectivity fault in row 3
                                </p>
                                <div
                                    class="font-label-micro text-label-micro text-on-surface-variant flex items-center gap-3">
                                    <span>Requested: 10 Jun 2026</span>
                                    <span>•</span>
                                    <span class="text-status-green">Credited to Vault on 12 Jun 2026 • Tx
                                        #TX-11842</span>
                                </div>
                            </div>
                            <div class="lg:col-span-4 flex flex-col lg:items-end justify-between gap-3">
                                <div class="flex flex-col lg:items-end">
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant uppercase font-mono">Amount
                                        Refunded</span>
                                    <span
                                        class="font-display-hero text-headline-section font-bold text-slate-authority">₹3,500.00</span>
                                </div>
                                <button
                                    class="px-4 py-2 rounded-xl bg-surface-container-low hover:bg-surface-container text-slate-authority font-button-text text-body-small font-medium transition-all flex items-center gap-1 self-start lg:self-end">
                                    <span>View Summary</span>
                                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 3D Dimensional "How Escrow Refunds Work" Banner -->
                <div
                    class="bg-slate-authority text-canvas-ivory rounded-2xl p-8 shadow-xl relative overflow-hidden flex flex-col gap-8 my-4">
                    <div
                        class="absolute -right-16 -bottom-16 w-96 h-96 bg-amber-action/10 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute left-1/3 -top-20 w-64 h-64 bg-status-green/10 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <!-- Banner Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 z-10">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span
                                    class="material-symbols-outlined text-amber-action text-[20px]">verified_user</span>
                                <span
                                    class="font-label-eyebrow text-label-eyebrow text-amber-action uppercase font-mono font-bold tracking-wider">
                                    Bazaario 3-Step Escrow Refund Guarantee
                                </span>
                            </div>
                            <h2 class="font-headline-section text-headline-section font-bold text-canvas-ivory">
                                Autonomous collateral protection from doorstep to vault.
                            </h2>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <a class="px-4 py-2 rounded-xl bg-card-white/10 hover:bg-card-white/20 font-button-text text-body-small text-canvas-ivory transition-colors"
                                href="#">
                                Read Refund Terms
                            </a>
                            <a class="px-4 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-95 transition-all flex items-center gap-1.5"
                                href="#">
                                <span class="material-symbols-outlined text-[18px]">support_agent</span>
                                <span>Contact Concierge</span>
                            </a>
                        </div>
                    </div>
                    <!-- 3 Visual Steps Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 z-10">
                        <!-- Step 1 -->
                        <div class="bg-card-white/5 backdrop-blur-md rounded-xl p-5 flex flex-col gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-amber-action/20 text-amber-action flex items-center justify-center font-bold font-mono">
                                01
                            </div>
                            <div class="flex flex-col gap-1">
                                <h4 class="font-title-card text-title-card font-semibold text-canvas-ivory">1. Initiate
                                    Request &amp; Courier Pickup</h4>
                                <p class="font-body-small text-body-small text-canvas-ivory/70">
                                    The merchant stall is notified instantly. A prepaid BlueDart return label with
                                    anti-tamper QR code is provisioned to your portal.
                                </p>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div class="bg-card-white/5 backdrop-blur-md rounded-xl p-5 flex flex-col gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-status-green/20 text-status-green flex items-center justify-center font-bold font-mono">
                                02
                            </div>
                            <div class="flex flex-col gap-1">
                                <h4 class="font-title-card text-title-card font-semibold text-canvas-ivory">2. Transit
                                    &amp; Handshake Inspection</h4>
                                <p class="font-body-small text-body-small text-canvas-ivory/70">
                                    Courier verifies physical integrity at pickup. Once registered into the logistics
                                    mesh, state lock updates in Bazaario Escrow smart-contracts.
                                </p>
                            </div>
                        </div>
                        <!-- Step 3 -->
                        <div class="bg-card-white/5 backdrop-blur-md rounded-xl p-5 flex flex-col gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-amber-action text-slate-authority flex items-center justify-center font-bold font-mono">
                                03
                            </div>
                            <div class="flex flex-col gap-1">
                                <h4 class="font-title-card text-title-card font-semibold text-canvas-ivory">3. Instant
                                    Collateral Unlock</h4>
                                <p class="font-body-small text-body-small text-canvas-ivory/70">
                                    No endless merchant waiting. The escrow releases funds to your Liquid Vault within
                                    200 milliseconds of verified hub handoff.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Floating Docked Assistant Pill -->
            <div class="fixed bottom-6 right-6 z-40">
                <div
                    class="bg-slate-authority text-canvas-ivory px-4 py-2.5 rounded-full shadow-2xl backdrop-blur-xl flex items-center gap-3 cursor-pointer hover:scale-105 active:scale-95 transition-all">
                    <span class="w-2 h-2 rounded-full bg-amber-action animate-pulse"></span>
                    <span class="material-symbols-outlined text-amber-action text-[18px]">smart_toy</span>
                    <span class="font-body-small text-body-small font-medium">
                        ✦ Ask Bazaario AI: <span class="text-amber-action font-mono">"Check status of return
                            #RET-00128"</span>
                    </span>
                    <button class="p-1 rounded-full bg-card-white/10 hover:bg-card-white/20 text-canvas-ivory"
                        title="Quick AI Check">
                        <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
                    </button>
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