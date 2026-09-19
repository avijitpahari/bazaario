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
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-dim": "#dbdad5",
                        "tertiary": "#000000",
                        "inverse-on-surface": "#f2f1ec",
                        "on-surface": "#1b1c19",
                        "ivory-alpha-70": "rgba(255, 253, 248, 0.70)",
                        "secondary": "#006e2d",
                        "status-green": "#16A34A",
                        "on-secondary": "#ffffff",
                        "primary-fixed-dim": "#bec6e0",
                        "error-container": "#ffdad6",
                        "secondary-fixed": "#7ffc97",
                        "background": "#fbf9f4",
                        "outline-variant": "#c6c6cd",
                        "primary-container": "#131b2e",
                        "tertiary-fixed": "#ffddb4",
                        "primary": "#000000",
                        "card-white": "#FFFFFF",
                        "error": "#ba1a1a",
                        "on-primary": "#ffffff",
                        "surface-container-high": "#eae8e3",
                        "canvas-ivory": "#FFFDF8",
                        "primary-fixed": "#dae2fd",
                        "outline": "#76777d",
                        "inverse-surface": "#30312e",
                        "on-error": "#ffffff",
                        "surface-bright": "#fbf9f4",
                        "on-background": "#1b1c19",
                        "on-error-container": "#93000a",
                        "on-surface-variant": "#45464d",
                        "inverse-primary": "#bec6e0",
                        "on-secondary-fixed": "#002109",
                        "on-primary-fixed-variant": "#3f465c",
                        "primary-alpha-10": "rgba(15, 23, 42, 0.10)",
                        "tertiary-fixed-dim": "#ffb955",
                        "on-primary-fixed": "#131b2e",
                        "slate-authority": "#0F172A",
                        "surface-container": "#efeee9",
                        "surface-tint": "#565e74",
                        "surface": "#fbf9f4",
                        "on-primary-container": "#7c839b",
                        "on-secondary-fixed-variant": "#005320",
                        "on-tertiary-container": "#b57700",
                        "tertiary-container": "#291800",
                        "surface-container-highest": "#e4e2de",
                        "on-secondary-container": "#007230",
                        "surface-container-low": "#f5f3ee",
                        "surface-variant": "#e4e2de",
                        "primary-alpha-20": "rgba(15, 23, 42, 0.20)",
                        "secondary-fixed-dim": "#62df7d",
                        "on-tertiary-fixed-variant": "#633f00",
                        "on-tertiary-fixed": "#291800",
                        "on-tertiary": "#ffffff",
                        "secondary-container": "#7cf994",
                        "surface-container-lowest": "#ffffff",
                        "amber-action": "#F5A623",
                        "primary-alpha-70": "rgba(15, 23, 42, 0.70)"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "3xl": "1.5rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "section-final-bottom": "4rem",
                        "section-interior-y": "3.5rem",
                        "container-max": "72rem",
                        "section-hero-bottom": "2.5rem",
                        "gutter-lg": "2.5rem",
                        "gutter-xl": "3.5rem",
                        "section-hero-top": "4rem",
                        "gutter-xs": "0.5rem",
                        "banner-padding": "2.5rem",
                        "gutter-sm": "1rem",
                        "gutter-md": "1.5rem",
                        "card-padding": "1.25rem"
                    },
                    "fontFamily": {
                        "display-hero": [
                            "Space Grotesk"
                        ],
                        "button-text": [
                            "Inter"
                        ],
                        "label-micro": [
                            "JetBrains Mono"
                        ],
                        "body-regular": [
                            "Inter"
                        ],
                        "label-eyebrow": [
                            "JetBrains Mono"
                        ],
                        "headline-section": [
                            "Space Grotesk"
                        ],
                        "title-card": [
                            "Space Grotesk"
                        ],
                        "display-hero-mobile": [
                            "Space Grotesk"
                        ],
                        "body-small": [
                            "Inter"
                        ],
                        "body-lead": [
                            "Inter"
                        ]
                    },
                    "fontSize": {
                        "display-hero": [
                            "48px",
                            {
                                "lineHeight": "56px",
                                "letterSpacing": "-0.02em",
                                "fontWeight": "700"
                            }
                        ],
                        "button-text": [
                            "16px",
                            {
                                "lineHeight": "24px",
                                "fontWeight": "600"
                            }
                        ],
                        "label-micro": [
                            "10px",
                            {
                                "lineHeight": "14px",
                                "letterSpacing": "0.025em",
                                "fontWeight": "500"
                            }
                        ],
                        "body-regular": [
                            "16px",
                            {
                                "lineHeight": "24px",
                                "fontWeight": "400"
                            }
                        ],
                        "label-eyebrow": [
                            "12px",
                            {
                                "lineHeight": "16px",
                                "letterSpacing": "0.05em",
                                "fontWeight": "500"
                            }
                        ],
                        "headline-section": [
                            "24px",
                            {
                                "lineHeight": "32px",
                                "letterSpacing": "-0.01em",
                                "fontWeight": "700"
                            }
                        ],
                        "title-card": [
                            "18px",
                            {
                                "lineHeight": "24px",
                                "letterSpacing": "-0.01em",
                                "fontWeight": "600"
                            }
                        ],
                        "display-hero-mobile": [
                            "36px",
                            {
                                "lineHeight": "44px",
                                "letterSpacing": "-0.02em",
                                "fontWeight": "700"
                            }
                        ],
                        "body-small": [
                            "14px",
                            {
                                "lineHeight": "20px",
                                "fontWeight": "400"
                            }
                        ],
                        "body-lead": [
                            "18px",
                            {
                                "lineHeight": "28px",
                                "fontWeight": "400"
                            }
                        ]
                    }
                },
            },
        }</script>
</head>

<body
    class="bg-[#FAF7F0] text-on-surface font-body-regular text-body-regular min-h-screen relative selection:bg-amber-action/30 selection:text-slate-authority">
    <!-- Ambient 3D Volumetric Mesh & Glow Blobs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div
            class="absolute -top-32 left-1/4 w-[520px] h-[520px] rounded-full bg-gradient-to-br from-amber-400/20 via-orange-300/15 to-transparent blur-3xl opacity-70 transform -rotate-12">
        </div>
        <div
            class="absolute top-1/3 -right-28 w-[480px] h-[480px] rounded-full bg-gradient-to-bl from-purple-400/15 via-indigo-300/10 to-transparent blur-3xl opacity-60">
        </div>
        <div
            class="absolute bottom-1/4 -left-20 w-[420px] h-[420px] rounded-full bg-gradient-to-tr from-amber-300/15 via-emerald-200/10 to-transparent blur-3xl opacity-50">
        </div>
    </div>
    <header class="fixed top-4 left-0 right-0 z-50 px-gutter-md">
        <div
            class="h-20 max-w-container-max mx-auto bg-white/70 backdrop-blur-2xl border border-white/80 rounded-full px-6 py-3 flex items-center justify-between shadow-[0_8px_32px_rgba(15,23,42,0.06)] relative before:absolute before:inset-0 before:rounded-full before:pointer-events-none before:border-t before:border-white/90">
            <div class="flex items-center gap-6">
                <a class="flex items-center gap-3 group" data-path="home" href="#">
                    <div
                        class="relative w-9 h-9 rounded-full overflow-hidden p-0.5 bg-gradient-to-tr from-amber-action to-amber-200 shadow-sm">
                        <img alt="Profile" class="w-full h-full rounded-full object-cover"
                            src="https://lh3.googleusercontent.com/aida/AEtjO1XSJITP3lGfyftjWK-KyQdkr_p-dkjB55PjIN7euT8WuESTgjD3lPkAVAUXxlFIjVJPBz9Oqm2cKZ_x9x3S_amnhGmh900sc5ueTNsDEcNpP93YxqAkt1txHQPaxSo6QEOdfMl0VwK1kInjDJv-PlIFHTQi_ymjzclU6QdqVlntqPIYbPl96vON-MAHiiu67gshPxDEtQbgZyyE8fCJ0BPrIQVNzhnbyuckQwvYyDNRcbUonyAzkarFSyqzC63lc4cgjr8Z0R0_qQ" />
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="font-headline-section text-title-card font-bold tracking-tight text-slate-authority drop-shadow-xs">BAZAARIO</span>
                        <span
                            class="font-label-micro text-label-micro uppercase tracking-wider text-amber-action font-semibold">Curated
                            Market</span>
                    </div>
                </a>
                <nav class="hidden lg:flex items-center gap-1.5 p-1 bg-black/[0.03] backdrop-blur-sm border border-white/60 rounded-full shadow-inner"
                    data-active-classes="bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold px-3.5 py-1.5 rounded-full shadow-[0_2px_8px_rgba(245,166,35,0.35)]">
                    <a class="text-on-surface-variant font-button-text text-body-small px-3.5 py-1.5 rounded-full hover:text-slate-authority hover:bg-white/50 transition-all"
                        data-path="home" href="#">Home</a>
                    <a class="text-on-surface-variant font-button-text text-body-small px-3.5 py-1.5 rounded-full hover:text-slate-authority hover:bg-white/50 transition-all"
                        data-path="shop" href="#">Shop</a>
                    <a class="text-on-surface-variant font-button-text text-body-small px-3.5 py-1.5 rounded-full hover:text-slate-authority hover:bg-white/50 transition-all"
                        data-path="categories" href="#">Categories</a>
                    <a class="text-on-surface-variant font-button-text text-body-small px-3.5 py-1.5 rounded-full hover:text-slate-authority hover:bg-white/50 transition-all"
                        data-path="auctions" href="#">Auctions</a>
                    <a class="text-on-surface-variant font-button-text text-body-small px-3.5 py-1.5 rounded-full hover:text-slate-authority hover:bg-white/50 transition-all"
                        data-path="ai-compare" href="#">AI Compare</a>
                </nav>
            </div>
            <div class="flex items-center gap-2.5">
                <button aria-label="Notifications"
                    class="relative p-2 text-slate-authority bg-white/60 hover:bg-white border border-white/80 rounded-full transition-all shadow-[0_2px_6px_rgba(0,0,0,0.03)]"
                    type="button">
                    <span class="material-symbols-outlined">notifications</span>
                    <span
                        class="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-action rounded-full ring-2 ring-white"></span>
                </button>
                <a class="relative p-2 text-slate-authority bg-white/60 hover:bg-white border border-white/80 rounded-full transition-all shadow-[0_2px_6px_rgba(0,0,0,0.03)] flex items-center"
                    data-path="wishlist" href="#">
                    <span class="material-symbols-outlined">favorite</span>
                    <span
                        class="absolute -top-1 -right-1 bg-slate-authority text-canvas-ivory font-label-micro text-label-micro px-1.5 py-0.5 rounded-full border border-white/20 font-bold shadow-xs">8</span>
                </a>
                <a class="inline-flex items-center gap-2 bg-white/80 border border-white/90 hover:border-amber-action px-3.5 py-1.5 rounded-full text-slate-authority transition-all shadow-[0_2px_8px_rgba(0,0,0,0.04)]"
                    data-path="cart" href="#">
                    <span class="material-symbols-outlined text-body-lead text-slate-authority">shopping_cart</span>
                    <span
                        class="font-label-micro text-label-micro font-bold bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority px-2 py-0.5 rounded-full shadow-xs">3</span>
                </a>
                <div class="h-6 w-px bg-slate-authority/10 mx-1 hidden sm:block"></div>
                <a class="hidden sm:flex items-center gap-2.5 pl-1.5 pr-3.5 py-1 bg-white/70 border border-white/90 rounded-full hover:border-amber-action transition-all shadow-[0_2px_8px_rgba(0,0,0,0.04)]"
                    data-path="profile" href="#">
                    <img alt="Profile" class="w-7 h-7 rounded-full object-cover ring-1 ring-amber-action/40"
                        src="https://lh3.googleusercontent.com/aida/AEtjO1XSJITP3lGfyftjWK-KyQdkr_p-dkjB55PjIN7euT8WuESTgjD3lPkAVAUXxlFIjVJPBz9Oqm2cKZ_x9x3S_amnhGmh900sc5ueTNsDEcNpP93YxqAkt1txHQPaxSo6QEOdfMl0VwK1kInjDJv-PlIFHTQi_ymjzclU6QdqVlntqPIYbPl96vON-MAHiiu67gshPxDEtQbgZyyE8fCJ0BPrIQVNzhnbyuckQwvYyDNRcbUonyAzkarFSyqzC63lc4cgjr8Z0R0_qQ" />
                    <div class="flex flex-col text-left">
                        <span
                            class="font-button-text text-body-small font-semibold text-slate-authority leading-tight">Avijit</span>
                        <span
                            class="font-label-micro text-label-micro text-status-green flex items-center gap-0.5"><span
                                class="material-symbols-outlined text-label-micro">verified</span>Verified</span>
                    </div>
                </a>
            </div>
        </div>
    </header>
    <main class="w-full pt-28 relative z-10">
        <div class="flex flex-col w-full">
            <!-- Content Canvas Container -->
            <div class="w-full max-w-container-max mx-auto px-gutter-md pb-section-final-bottom flex flex-col gap-8">
                <!-- 1. Breadcrumb, Back Action & Escrow Status Pill -->
                <section class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <a class="inline-flex items-center gap-2 bg-white/80 hover:bg-white text-slate-authority font-button-text text-body-small font-semibold px-4 py-2 rounded-full shadow-sm transition-all"
                            data-path="orders" href="#">
                            <span class="material-symbols-outlined text-body-lead">arrow_back</span>
                            <span>Back to Orders</span>
                        </a>
                        <div class="h-4 w-px bg-slate-authority/15 hidden sm:block"></div>
                        <nav aria-label="Breadcrumbs"
                            class="flex items-center gap-2 text-on-surface-variant font-label-micro text-label-micro uppercase tracking-wider">
                            <a class="hover:text-slate-authority transition-colors" data-path="home" href="#">Home</a>
                            <span>/</span>
                            <a class="hover:text-slate-authority transition-colors" data-path="account"
                                href="#">Account</a>
                            <span>/</span>
                            <a class="hover:text-slate-authority transition-colors" data-path="orders" href="#">My
                                Orders</a>
                            <span>/</span>
                            <span class="text-slate-authority font-bold">#BZ-2026-00128</span>
                        </nav>
                    </div>
                    <!-- Trust State Badges -->
                    <div class="flex items-center gap-2.5 shrink-0">
                        <div
                            class="inline-flex items-center gap-2 bg-secondary/10 text-secondary font-label-micro text-label-micro font-semibold px-3 py-1.5 rounded-full shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                            <span>Delivered • Escrow Settled</span>
                        </div>
                        <div
                            class="inline-flex items-center gap-1.5 bg-slate-authority text-canvas-ivory font-label-micro text-label-micro font-medium px-3 py-1.5 rounded-full shadow-xs">
                            <span class="material-symbols-outlined text-label-eyebrow text-amber-action">verified</span>
                            <span>Vault #ESC-9482</span>
                        </div>
                    </div>
                </section>
                <!-- 2. Order Header & Action Bar -->
                <header
                    class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 md:p-8 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap items-baseline gap-3">
                            <h1
                                class="font-display-hero text-headline-section md:text-display-hero-mobile text-slate-authority font-bold tracking-tight">
                                Order #BZ-2026-00128
                            </h1>
                            <span
                                class="bg-amber-action/20 text-slate-authority font-label-micro text-label-micro uppercase tracking-wider px-2.5 py-1 rounded-full font-bold">
                                Priority Armored Node
                            </span>
                        </div>
                        <p
                            class="font-label-eyebrow text-body-small text-on-surface-variant flex flex-wrap items-center gap-x-2.5 gap-y-1">
                            <span>Placed on 12 Sep 2026</span>
                            <span>•</span>
                            <span>14:28 EST</span>
                            <span>•</span>
                            <span class="inline-flex items-center gap-1 font-semibold text-slate-authority">
                                <span
                                    class="material-symbols-outlined text-body-small text-amber-action">local_shipping</span>
                                Carrier: Priority Armored Express (DEL-99214)
                            </span>
                        </p>
                    </div>
                    <!-- Quick Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <button
                            class="inline-flex items-center gap-2 bg-white hover:bg-surface-container-low text-slate-authority font-button-text text-body-small font-semibold px-4 py-2.5 rounded-2xl shadow-sm transition-all"
                            onclick="alert('Cryptographic Invoice #BZ-2026-00128.pdf generated with verified SHA-256 seal.')"
                            type="button">
                            <span class="material-symbols-outlined text-body-lead text-slate-authority">download</span>
                            <span>Invoice (PDF)</span>
                        </button>
                        <div class="relative group">
                            <button
                                class="inline-flex items-center gap-2 bg-error/10 hover:bg-error/15 text-error font-button-text text-body-small font-semibold px-4 py-2.5 rounded-2xl transition-all shadow-xs"
                                onclick="alert('Return window is active: 2 days remaining. Dispute coordinator assigned.')"
                                type="button">
                                <span class="material-symbols-outlined text-body-lead">assignment_return</span>
                                <span>Return / Dispute</span>
                                <span
                                    class="bg-error text-white font-label-micro text-label-micro px-1.5 py-0.5 rounded-full font-bold">2d
                                    Left</span>
                            </button>
                        </div>
                        <button
                            class="inline-flex items-center gap-1.5 bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold px-5 py-2.5 rounded-2xl shadow-sm transition-all"
                            onclick="alert('Thank you for supporting verified stall TechWorld Store!')" type="button">
                            <span class="material-symbols-outlined text-body-lead"
                                style="font-variation-settings: 'FILL' 1;">hotel_class</span>
                            <span>Rate Stall</span>
                        </button>
                    </div>
                </header>
                <!-- 3. 3D Dimensional Order Tracking Pipeline -->
                <section
                    class="bg-gradient-to-br from-white/90 via-white/70 to-amber-50/40 backdrop-blur-2xl rounded-3xl p-6 md:p-8 shadow-md relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-2xl bg-slate-authority text-amber-action flex items-center justify-center shadow-xs">
                                <span class="material-symbols-outlined text-headline-section">conversion_path</span>
                            </div>
                            <div>
                                <h2 class="font-headline-section text-title-card font-bold text-slate-authority">Order
                                    Tracking Telemetry</h2>
                                <p
                                    class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider">
                                    Multi-Signature Proof of Custody</p>
                            </div>
                        </div>
                        <!-- Real-Time Status Pill -->
                        <div class="flex items-center gap-2 bg-secondary/10 px-3.5 py-1.5 rounded-full">
                            <span class="w-2.5 h-2.5 rounded-full bg-status-green"></span>
                            <span
                                class="font-label-micro text-label-micro font-bold text-secondary uppercase tracking-wide">100%
                                Completed &amp; Signed</span>
                        </div>
                    </div>
                    <!-- Horizontal 4-Step Interactive Timeline -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                        <!-- Connecting Bar (Desktop) -->
                        <div
                            class="hidden md:block absolute top-7 left-12 right-12 h-1 bg-gradient-to-r from-status-green via-status-green to-status-green/90 rounded-full z-0">
                        </div>
                        <!-- Step 1 -->
                        <div
                            class="relative z-10 flex flex-col gap-3 bg-white/70 backdrop-blur-sm p-4 rounded-2xl shadow-xs">
                            <div class="flex items-center justify-between md:justify-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-status-green text-white flex items-center justify-center font-bold shadow-md ring-4 ring-white">
                                    <span class="material-symbols-outlined text-body-lead">check</span>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro text-secondary font-bold uppercase tracking-wider">Step
                                    01 • Settled</span>
                            </div>
                            <div>
                                <h3 class="font-title-card text-body-regular font-bold text-slate-authority">Ordered
                                </h3>
                                <p class="font-label-micro text-label-micro text-on-surface-variant mt-0.5">12 Sep 2026,
                                    14:28 EST</p>
                                <p class="font-body-small text-body-small text-on-surface-variant mt-2 leading-relaxed">
                                    Payment authorized and locked into decentralized escrow vault.
                                </p>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div
                            class="relative z-10 flex flex-col gap-3 bg-white/70 backdrop-blur-sm p-4 rounded-2xl shadow-xs">
                            <div class="flex items-center justify-between md:justify-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-status-green text-white flex items-center justify-center font-bold shadow-md ring-4 ring-white">
                                    <span class="material-symbols-outlined text-body-lead">verified</span>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro text-secondary font-bold uppercase tracking-wider">Step
                                    02 • Inspected</span>
                            </div>
                            <div>
                                <h3 class="font-title-card text-body-regular font-bold text-slate-authority">Confirmed
                                </h3>
                                <p class="font-label-micro text-label-micro text-on-surface-variant mt-0.5">12 Sep 2026,
                                    16:45 EST</p>
                                <p class="font-body-small text-body-small text-on-surface-variant mt-2 leading-relaxed">
                                    Stall <span class="font-semibold text-slate-authority">TechWorld</span> completed
                                    physical QC &amp; holographic seal check.
                                </p>
                            </div>
                        </div>
                        <!-- Step 3 -->
                        <div
                            class="relative z-10 flex flex-col gap-3 bg-white/70 backdrop-blur-sm p-4 rounded-2xl shadow-xs">
                            <div class="flex items-center justify-between md:justify-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-status-green text-white flex items-center justify-center font-bold shadow-md ring-4 ring-white">
                                    <span class="material-symbols-outlined text-body-lead">local_shipping</span>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro text-secondary font-bold uppercase tracking-wider">Step
                                    03 • In Transit</span>
                            </div>
                            <div>
                                <h3 class="font-title-card text-body-regular font-bold text-slate-authority">Shipped
                                </h3>
                                <p class="font-label-micro text-label-micro text-on-surface-variant mt-0.5">13 Sep 2026,
                                    09:15 EST</p>
                                <p class="font-body-small text-body-small text-on-surface-variant mt-2 leading-relaxed">
                                    Carrier transfer confirmed. Real-time satellite telemetry logged.
                                </p>
                            </div>
                        </div>
                        <!-- Step 4 -->
                        <div
                            class="relative z-10 flex flex-col gap-3 bg-gradient-to-b from-white to-amber-100/40 p-4 rounded-2xl shadow-sm">
                            <div class="flex items-center justify-between md:justify-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-amber-action text-slate-authority flex items-center justify-center font-bold shadow-md ring-4 ring-white">
                                    <span class="material-symbols-outlined text-body-lead"
                                        style="font-variation-settings: 'FILL' 1;">package_2</span>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro text-slate-authority font-bold uppercase tracking-wider">Step
                                    04 • Completed</span>
                            </div>
                            <div>
                                <h3 class="font-title-card text-body-regular font-bold text-slate-authority">Delivered
                                </h3>
                                <p class="font-label-micro text-label-micro text-on-surface-variant mt-0.5">15 Sep 2026,
                                    11:30 EST</p>
                                <p class="font-body-small text-body-small text-on-surface-variant mt-2 leading-relaxed">
                                    Unboxing OTP verified. Receiver digital signature validated on-chain.
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Telemetry Bottom Bar / Live Tracking ID -->
                    <div
                        class="mt-8 pt-6 bg-black/[0.02] -mx-6 -mb-6 md:-mx-8 md:-mb-8 px-6 md:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-amber-action">qr_code_scanner</span>
                            <span class="font-label-eyebrow text-body-small text-slate-authority font-semibold">Tracking
                                Number:</span>
                            <code
                                class="font-label-micro text-body-small bg-white px-2.5 py-1 rounded-lg text-slate-authority font-bold shadow-xs"
                                id="tracking-id">TRK-99281-BLUEDART</code>
                            <button
                                class="p-1.5 hover:bg-white rounded-lg text-on-surface-variant hover:text-slate-authority transition-colors"
                                onclick="navigator.clipboard.writeText('TRK-99281-BLUEDART'); alert('Copied Tracking ID to clipboard!');"
                                title="Copy tracking code" type="button">
                                <span class="material-symbols-outlined text-body-small">content_copy</span>
                            </button>
                        </div>
                        <button
                            class="inline-flex items-center gap-1.5 text-slate-authority font-button-text text-body-small font-bold hover:text-amber-action transition-colors"
                            onclick="alert('Viewing Telemetry Map: Final delivery leg completed via Armored Courier Station WB-401.')"
                            type="button">
                            <span class="material-symbols-outlined text-body-lead text-amber-action">near_me</span>
                            <span>View Courier Telemetry Map →</span>
                        </button>
                    </div>
                </section>
                <!-- 4. Two-Column Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- LEFT COLUMN: Package Contents & Seller Stall (7 cols) -->
                    <section class="lg:col-span-7 flex flex-col gap-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-6 bg-amber-action rounded-full"></span>
                                <h2 class="font-headline-section text-headline-section font-bold text-slate-authority">
                                    Items in Package</h2>
                                <span
                                    class="font-label-micro text-label-micro bg-slate-authority/10 text-slate-authority px-2.5 py-0.5 rounded-full font-bold">1
                                    Item</span>
                            </div>
                            <span class="font-label-micro text-label-micro text-status-green font-semibold">100%
                                Authentic Authenticated</span>
                        </div>
                        <!-- Product Card -->
                        <article
                            class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col sm:flex-row gap-6">
                            <!-- Tactile Product Image with Wooden Stand -->
                            <div
                                class="relative w-full sm:w-44 h-48 sm:h-auto rounded-2xl overflow-hidden shrink-0 bg-surface-container-low flex items-center justify-center group shadow-inner">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    data-alt="Sony WH-1000XM5 noise-cancelling headphones resting on an organic curved bent-plywood wooden stand on a warm timber desk, soft natural morning window light, photorealistic commerce imagery with high fidelity texture"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAaRD6U4fltRp378Pj075BlH9C96cTnOuLcVoYv_P-eFlUvO2avj3oywVE6nVKkyZLZhjBqGTYP7lJ4aPIzlAnmQpQmcKFPcUpiwBH8i1au4uSF8GgoJp90MSHhhDpD_TPH5IjNsGAjnhMYumIy6YCMBP_ESBsFM99zkhLjNbS6K-0dJeKUlHVJIdRuFu7YkEwmR6rbTROQLNH3cHCmNohw_igpdLK-LdLh_UI2J-Kj3uztFf_CZeP2" />
                                <span
                                    class="absolute top-2 left-2 bg-slate-authority/85 backdrop-blur-md text-canvas-ivory font-label-micro text-label-micro px-2 py-0.5 rounded-md font-medium">
                                    Flagship Audio
                                </span>
                            </div>
                            <!-- Product Details -->
                            <div class="flex flex-col justify-between flex-1 gap-4">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <span
                                                class="font-label-micro text-label-micro uppercase tracking-wider text-amber-action font-semibold">Handcrafted
                                                Stall Item</span>
                                            <h3
                                                class="font-title-card text-title-card font-bold text-slate-authority leading-snug">
                                                Sony WH-1000XM5 Wireless Noise Cancelling Headphones
                                            </h3>
                                        </div>
                                    </div>
                                    <!-- Product Specs & Edition -->
                                    <p class="font-body-small text-body-small text-on-surface-variant leading-relaxed">
                                        Matte Black Edition • 30-Hour Battery Life • Multi-point Bluetooth 5.2 • Auto NC
                                        Optimizer
                                    </p>
                                    <!-- Seller Stall Badge -->
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <a class="inline-flex items-center gap-1.5 bg-surface-container-low hover:bg-surface-container px-3 py-1 rounded-xl text-slate-authority font-label-eyebrow text-label-micro font-bold transition-colors"
                                            data-path="stall-techworld" href="#">
                                            <span
                                                class="material-symbols-outlined text-body-small text-amber-action">storefront</span>
                                            <span>TechWorld Store</span>
                                            <span class="text-amber-action ml-1">★ 4.8</span>
                                            <span class="text-on-surface-variant font-normal">(312)</span>
                                        </a>
                                        <span
                                            class="inline-flex items-center gap-1 bg-secondary/10 text-secondary font-label-micro text-label-micro px-2.5 py-1 rounded-xl font-medium">
                                            <span class="material-symbols-outlined text-label-micro">verified</span>
                                            Verified Maker
                                        </span>
                                    </div>
                                    <!-- Cryptographic Match Badge -->
                                    <div
                                        class="mt-2 p-2.5 bg-black/[0.03] rounded-xl flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                                        <span class="flex items-center gap-1">
                                            <span
                                                class="material-symbols-outlined text-label-micro text-slate-authority">fingerprint</span>
                                            SN: <span
                                                class="font-mono text-slate-authority font-semibold">SN-XM5-99412-IN</span>
                                        </span>
                                        <span class="text-status-green font-semibold">100% Cryptographic Match</span>
                                    </div>
                                </div>
                                <!-- Price & Item Actions -->
                                <div
                                    class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-black/[0.06]">
                                    <div class="flex items-baseline gap-2">
                                        <span class="font-label-eyebrow text-body-small text-on-surface-variant">Qty: 1
                                            ×</span>
                                        <span
                                            class="font-headline-section text-title-card font-bold text-slate-authority">₹2,499.00</span>
                                        <span
                                            class="font-body-small text-body-small line-through text-on-surface-variant">₹2,999.00</span>
                                        <span
                                            class="font-label-micro text-label-micro font-bold text-status-green bg-secondary/10 px-1.5 py-0.5 rounded">17%
                                            OFF</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a class="bg-white hover:bg-surface-container-low text-slate-authority font-button-text text-body-small font-semibold px-3.5 py-2 rounded-xl shadow-xs transition-colors"
                                            data-path="stall-techworld" href="#">
                                            View Stall
                                        </a>
                                        <button
                                            class="bg-amber-action hover:opacity-95 text-slate-authority font-button-text text-body-small font-bold px-4 py-2 rounded-xl shadow-xs active:scale-[0.99] transition-all"
                                            onclick="alert('Added Sony WH-1000XM5 to your bag!')" type="button">
                                            Buy Again
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <!-- Unboxing & Verification Proof Vault Card -->
                        <article class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 shadow-sm flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <h3
                                    class="font-title-card text-body-lead font-bold text-slate-authority flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-action">fact_check</span>
                                    Proof of Dispatch &amp; Authenticity Record
                                </h3>
                                <span
                                    class="font-label-micro text-label-micro bg-status-green/10 text-status-green font-bold px-2 py-0.5 rounded-full">Passed
                                    All Checks</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="p-3 bg-surface-container-low rounded-2xl flex flex-col gap-1">
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant uppercase">Weight
                                        At Dispatch</span>
                                    <span class="font-label-eyebrow text-body-small font-bold text-slate-authority">250g
                                        • Sealed Box</span>
                                </div>
                                <div class="p-3 bg-surface-container-low rounded-2xl flex flex-col gap-1">
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant uppercase">Carrier
                                        Handover OTP</span>
                                    <span
                                        class="font-label-eyebrow text-body-small font-bold text-slate-authority">Verified
                                        (4912)</span>
                                </div>
                                <div class="p-3 bg-surface-container-low rounded-2xl flex flex-col gap-1">
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant uppercase">Buyer
                                        Signature</span>
                                    <span class="font-label-eyebrow text-body-small font-bold text-status-green">Signed
                                        By Avijit</span>
                                </div>
                            </div>
                        </article>
                    </section>
                    <!-- RIGHT COLUMN: Delivery Node & Escrow Financial Settlement (5 cols) -->
                    <aside class="lg:col-span-5 flex flex-col gap-6">
                        <!-- Card A: Delivery Destination Node -->
                        <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 shadow-sm flex flex-col gap-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-action">home_pin</span>
                                    <h2 class="font-title-card text-title-card font-bold text-slate-authority">Delivery
                                        Node</h2>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro bg-secondary/15 text-secondary font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                                    <span class="material-symbols-outlined text-label-micro">task_alt</span> Verified
                                    Destination
                                </span>
                            </div>
                            <!-- Recipient info -->
                            <div class="flex flex-col gap-1.5 p-4 bg-surface-container-low rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-headline-section text-body-lead font-bold text-slate-authority">Avijit
                                        Pahari</span>
                                    <span
                                        class="font-label-micro text-label-micro bg-slate-authority text-canvas-ivory px-2 py-0.5 rounded font-mono">Primary
                                        Node</span>
                                </div>
                                <p class="font-body-regular text-body-small text-on-surface-variant leading-relaxed">
                                    Contai, Purba Medinipur, West Bengal — 721401, India
                                </p>
                                <p
                                    class="font-label-micro text-label-micro text-slate-authority font-medium mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-body-small text-secondary">phone</span>
                                    +91 98200 ••••• (Verified Phone)
                                </p>
                            </div>
                            <!-- Courier Details -->
                            <div class="flex flex-col gap-3 pt-2">
                                <div class="flex items-center justify-between text-body-small">
                                    <span class="text-on-surface-variant">Dispatch Protocol:</span>
                                    <span class="font-semibold text-slate-authority">Express Armored Delivery</span>
                                </div>
                                <div class="flex items-center justify-between text-body-small">
                                    <span class="text-on-surface-variant">Delivering Agent:</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-semibold text-slate-authority">Rajesh K.</span>
                                        <span
                                            class="font-label-micro text-label-micro bg-black/[0.06] px-1.5 py-0.5 rounded text-on-surface-variant font-mono">#AGT-4401</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-body-small">
                                    <span class="text-on-surface-variant">Delivery Handshake:</span>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green font-bold">Confirmed
                                        via SMS Token</span>
                                </div>
                            </div>
                        </div>
                        <!-- Card B: Payment & Escrow Settlement Breakdown -->
                        <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 shadow-sm flex flex-col gap-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-action">receipt_long</span>
                                    <h2 class="font-title-card text-title-card font-bold text-slate-authority">Payment
                                        &amp; Escrow</h2>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro bg-secondary/15 text-secondary font-bold px-2 py-0.5 rounded-full">
                                    Settled
                                </span>
                            </div>
                            <!-- Breakdown Ledger -->
                            <div class="flex flex-col gap-3">
                                <div class="flex justify-between items-center text-body-small">
                                    <span class="text-on-surface-variant">Subtotal (1 Item)</span>
                                    <span class="font-medium text-slate-authority">₹2,499.00</span>
                                </div>
                                <div class="flex justify-between items-center text-body-small">
                                    <span class="text-on-surface-variant">Express Delivery Fee</span>
                                    <span class="font-bold text-status-green">FREE</span>
                                </div>
                                <div class="flex justify-between items-center text-body-small">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-on-surface-variant">Market Voucher</span>
                                        <span
                                            class="font-label-micro text-label-micro bg-amber-action/20 text-slate-authority px-1.5 py-0.5 rounded font-mono font-bold">WELCOME200</span>
                                    </div>
                                    <span class="font-bold text-status-green">-₹200.00</span>
                                </div>
                                <div class="flex justify-between items-center text-body-small">
                                    <span class="text-on-surface-variant">Decentralized Escrow Fee</span>
                                    <span class="font-bold text-status-green">FREE (Covered)</span>
                                </div>
                                <div class="h-px bg-black/[0.08] my-1"></div>
                                <!-- Grand Total -->
                                <div class="flex justify-between items-baseline">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-headline-section text-title-card font-bold text-slate-authority">Total
                                            Settled</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant">Inclusive
                                            of all taxes &amp; duties</span>
                                    </div>
                                    <span
                                        class="font-display-hero text-headline-section font-bold text-slate-authority">₹2,299.00</span>
                                </div>
                            </div>
                            <!-- Payment Method & Escrow Status Details -->
                            <div class="p-4 bg-surface-container-low rounded-2xl flex flex-col gap-2.5">
                                <div class="flex items-center justify-between text-body-small">
                                    <span
                                        class="font-label-micro text-label-micro uppercase text-on-surface-variant font-bold">Payment
                                        Instrument</span>
                                    <span class="inline-flex items-center gap-1 font-semibold text-slate-authority">
                                        <span
                                            class="material-symbols-outlined text-body-small text-amber-action">account_balance_wallet</span>
                                        Razorpay • UPI
                                    </span>
                                </div>
                                <div
                                    class="flex items-center justify-between text-label-micro font-mono text-on-surface-variant">
                                    <span>UTR Reference:</span>
                                    <span class="text-slate-authority font-bold">UPI-992817264819</span>
                                </div>
                                <div class="pt-2 border-t border-black/[0.06] flex items-start gap-2">
                                    <span
                                        class="material-symbols-outlined text-body-lead text-secondary shrink-0">lock</span>
                                    <p
                                        class="font-label-micro text-label-micro text-on-surface-variant leading-relaxed">
                                        <span class="font-bold text-slate-authority">Escrow Status:</span> Fully
                                        disbursed to seller <span class="font-semibold text-slate-authority">TechWorld
                                            Store</span> on Sep 15, 2026, upon buyer unboxing verification.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
                <!-- 5. Trust & Assistance Section (Bazaario Protection Authority Card) -->
                <section
                    class="bg-slate-authority text-canvas-ivory rounded-3xl p-8 md:p-10 shadow-lg relative overflow-hidden flex flex-col lg:flex-row items-center justify-between gap-8">
                    <!-- Decorative Backdrop Glow -->
                    <div
                        class="absolute -right-16 -top-16 w-80 h-80 bg-amber-action/20 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <div class="flex items-start gap-5 relative z-10 max-w-2xl">
                        <div
                            class="w-14 h-14 rounded-2xl bg-amber-action text-slate-authority flex items-center justify-center shrink-0 shadow-md">
                            <span class="material-symbols-outlined text-display-hero-mobile">security</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-micro text-label-micro bg-white/15 text-amber-action px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">
                                    Bazaario Protocol
                                </span>
                                <span class="font-label-micro text-label-micro text-canvas-ivory/60">Tier-1
                                    Guaranteed</span>
                            </div>
                            <h2
                                class="font-headline-section text-headline-section font-bold text-canvas-ivory tracking-tight">
                                Protected by Decentralized Escrow Guarantee
                            </h2>
                            <p class="font-body-regular text-body-small text-canvas-ivory/80 leading-relaxed">
                                Every trade is held in cold storage escrow until both physical inspection and unboxing
                                telemetry verify genuine delivery. You remain protected under the Bazaario 7-day Dispute
                                Resolution Window.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-3 relative z-10 shrink-0 w-full lg:w-auto">
                        <button
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-amber-action hover:opacity-95 text-slate-authority font-button-text text-button-text font-bold px-6 py-3 rounded-2xl shadow-md transition-all active:scale-[0.99]"
                            onclick="alert('Opening dispute resolution desk. Your dedicated case manager is ready.')"
                            type="button">
                            <span class="material-symbols-outlined text-body-lead">support_agent</span>
                            <span>Contact Concierge</span>
                        </button>
                        <a class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-canvas-ivory font-button-text text-button-text px-5 py-3 rounded-2xl transition-all"
                            data-path="escrow-guarantee" href="#">
                            <span>Read Terms</span>
                        </a>
                    </div>
                </section>
                <!-- Floating AI Order Concierge Pill -->
                <div class="self-center -mt-2 inline-flex items-center gap-3 bg-white/90 backdrop-blur-md px-5 py-2.5 rounded-full shadow-md cursor-pointer hover:bg-white transition-all"
                    onclick="alert('Bazaario AI Order Assistant: All records confirm clean receipt! Can I help you with return requests, stall communication, or downloading tax receipts?')">
                    <span class="w-2 h-2 rounded-full bg-amber-action animate-ping"></span>
                    <span class="font-label-eyebrow text-body-small text-slate-authority font-medium">
                        <strong class="text-amber-action font-bold">✦ Bazaario AI Concierge:</strong> “Need help with
                        return window or stall inquiries?”
                    </span>
                    <span class="material-symbols-outlined text-body-lead text-slate-authority">chat_bubble</span>
                </div>
            </div>
        </div>
    </main>
    <!-- Footer with 3D Glass Surface Treatment -->
    <footer class="w-full bg-white/60 backdrop-blur-xl border-t border-white/80 mt-section-interior-y relative z-10">
        <div class="max-w-container-max mx-auto px-gutter-md py-banner-padding">
            <div
                class="bg-gradient-to-r from-slate-authority to-[#18233C] text-canvas-ivory rounded-3xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 mb-12 border border-white/20 shadow-[0_12px_36px_rgba(15,23,42,0.15)] relative overflow-hidden">
                <div
                    class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent">
                </div>
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-action/20 border border-amber-action/40 flex items-center justify-center shrink-0 backdrop-blur-md shadow-sm">
                        <span
                            class="material-symbols-outlined text-amber-action text-headline-section">verified_user</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-title-card text-title-card font-bold text-white">Bazaario Escrow Guarantee
                            </h3>
                            <span
                                class="font-label-micro text-label-micro bg-secondary/20 text-secondary-fixed rounded-full px-2.5 py-0.5 font-bold border border-secondary-fixed/30">Protected
                                Checkout</span>
                        </div>
                        <p class="font-body-small text-body-small text-white/70 mt-1">Funds held securely in
                            decentralized escrow until authentic dispatch and buyer verification.</p>
                    </div>
                </div>
                <a class="shrink-0 bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-button-text px-6 py-3 rounded-xl font-bold hover:shadow-[0_4px_16px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-md border border-amber-200/50"
                    data-path="escrow-guarantee" href="#">
                    Verify Protection
                </a>
            </div>
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pt-6 border-t border-black/[0.06]">
                <div class="flex flex-wrap items-center gap-6">
                    <span class="font-label-micro text-label-micro text-on-surface-variant font-medium">© 2026 Bazaario
                        Inc. Authentic Marketplace.</span>
                    <a class="font-body-small text-body-small text-on-surface-variant hover:text-slate-authority transition-colors"
                        data-path="privacy" href="#">Privacy Policy</a>
                    <a class="font-body-small text-body-small text-on-surface-variant hover:text-slate-authority transition-colors"
                        data-path="terms" href="#">Terms of Service</a>
                    <a class="font-body-small text-body-small text-on-surface-variant hover:text-slate-authority transition-colors"
                        data-path="market-rules" href="#">Market Rules</a>
                </div>
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-1.5 bg-white/80 border border-white/90 rounded-xl px-3 py-1.5 text-on-surface-variant font-label-micro text-label-micro shadow-xs">
                        <span class="material-symbols-outlined text-body-small">payments</span>
                        <span class="font-bold text-slate-authority">USD ($)</span>
                    </div>
                    <div
                        class="flex items-center gap-1.5 bg-white/80 border border-white/90 rounded-xl px-3 py-1.5 text-on-surface-variant font-label-micro text-label-micro shadow-xs">
                        <span class="material-symbols-outlined text-body-small">language</span>
                        <span class="font-bold text-slate-authority">English (US)</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>