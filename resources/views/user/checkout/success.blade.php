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
            <!-- Interactive & Dynamic Ambient Visual Wrapper -->
            <div class="max-w-container-max mx-auto px-gutter-md pb-section-final-bottom w-full">
                <!-- Hero Confirmation 3D Glass Module -->
                <div
                    class="relative w-full rounded-3xl p-8 md:p-12 mb-8 bg-surface-container-lowest/90 backdrop-blur-2xl shadow-xl overflow-hidden text-center flex flex-col items-center">
                    <!-- Tactile Inner Glow Accent & Geometric Scrim -->
                    <div
                        class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 rounded-full bg-gradient-to-b from-amber-action/25 via-secondary/10 to-transparent blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-primary-alpha-10 rounded-full blur-2xl pointer-events-none">
                    </div>
                    <!-- 3D Dimensional Checkmark Orb Badge -->
                    <div class="relative mb-6 group cursor-default">
                        <div
                            class="w-24 h-24 rounded-full bg-gradient-to-tr from-amber-action via-amber-300 to-white p-1.5 shadow-[0_20px_40px_rgba(245,166,35,0.35)] flex items-center justify-center relative">
                            <div
                                class="w-full h-full rounded-full bg-slate-authority flex items-center justify-center shadow-inner relative overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-tr from-secondary/80 to-transparent opacity-60">
                                </div>
                                <!-- Dynamic 3D Volumetric Glow Spot -->
                                <div
                                    class="absolute -top-2 -left-2 w-8 h-8 rounded-full bg-white/40 blur-xs pointer-events-none">
                                </div>
                                <span class="material-symbols-outlined text-canvas-ivory text-4xl relative z-10"
                                    style="font-variation-settings: 'FILL' 1, 'wght' 700;">check</span>
                            </div>
                        </div>
                        <!-- Micro floating 3D glass chips -->
                        <div
                            class="absolute -top-1 -right-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-full shadow-md text-slate-authority font-label-micro text-label-micro font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-status-green text-label-micro"
                                style="font-variation-settings: 'FILL' 1;">shield</span>
                            SECURED
                        </div>
                    </div>
                    <!-- Secured Escrow Status Eyebrow -->
                    <div
                        class="inline-flex items-center gap-2 bg-secondary/10 text-secondary px-4 py-1 rounded-full mb-4 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                        <span class="font-label-eyebrow text-label-eyebrow font-bold tracking-widest uppercase">PAYMENT
                            &amp; ESCROW SECURED</span>
                    </div>
                    <!-- Main Headline -->
                    <h1
                        class="font-headline-section text-display-hero text-slate-authority mb-3 tracking-tight max-w-2xl leading-none">
                        Order Confirmed!
                    </h1>
                    <p class="font-body-regular text-body-lead text-on-surface-variant max-w-xl mx-auto mb-8">
                        Thank you for your purchase. Your funds are protected in Bazaario Escrow until delivery
                        verification is validated.
                    </p>
                    <!-- Key Metadata Pill Ribbon -->
                    <div
                        class="w-full max-w-3xl bg-surface-container-low/90 backdrop-blur-md rounded-2xl p-4 md:p-5 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
                        <!-- Item 1: Order ID -->
                        <div class="flex items-center gap-3 bg-surface-container-lowest p-3 rounded-xl shadow-xs">
                            <div
                                class="w-10 h-10 rounded-lg bg-slate-authority/5 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-slate-authority">receipt_long</span>
                            </div>
                            <div class="min-w-0">
                                <span
                                    class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider block">Order
                                    ID</span>
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="font-title-card text-body-small font-bold text-slate-authority truncate"
                                        id="orderIdText">BZ-2026-00128</span>
                                    <button class="text-on-surface-variant hover:text-amber-action transition-colors"
                                        id="copyOrderBtn" title="Copy Order ID" type="button">
                                        <span class="material-symbols-outlined text-body-small">content_copy</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Item 2: Amount Locked -->
                        <div class="flex items-center gap-3 bg-surface-container-lowest p-3 rounded-xl shadow-xs">
                            <div
                                class="w-10 h-10 rounded-lg bg-amber-action/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-amber-action">lock</span>
                            </div>
                            <div class="min-w-0">
                                <span
                                    class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider block">Escrow
                                    Locked</span>
                                <div class="font-title-card text-body-small font-bold text-slate-authority">
                                    $4,998.00 <span
                                        class="font-label-micro text-label-micro text-status-green font-normal">(Instant
                                        Wire)</span>
                                </div>
                            </div>
                        </div>
                        <!-- Item 3: Est. Delivery -->
                        <div class="flex items-center gap-3 bg-surface-container-lowest p-3 rounded-xl shadow-xs">
                            <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-status-green">local_shipping</span>
                            </div>
                            <div class="min-w-0">
                                <span
                                    class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider block">Est.
                                    Delivery</span>
                                <div class="font-title-card text-body-small font-bold text-slate-authority">
                                    Sep 14, 2026 <span
                                        class="font-label-micro text-label-micro text-on-surface-variant font-normal">(2
                                        Days)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Asymmetric 2-Column Dashboard Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Left Column: Live 3D Progression & Escrow Pipeline (7 cols) -->
                    <div class="lg:col-span-7 flex flex-col gap-6">
                        <!-- Live Milestones Track -->
                        <div
                            class="bg-surface-container-lowest rounded-3xl p-6 md:p-8 shadow-md relative overflow-hidden">
                            <div class="flex items-center justify-between mb-8">
                                <div>
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow text-amber-action font-bold uppercase tracking-wider block">LIVE
                                        FULFILLMENT AUDIT</span>
                                    <h2 class="font-headline-section text-headline-section text-slate-authority">
                                        Milestone Pipeline</h2>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1.5 bg-status-green/10 text-status-green font-label-micro text-label-micro font-bold px-3 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green animate-ping"></span>
                                    Escrow Active
                                </span>
                            </div>
                            <!-- Timeline Track Elements -->
                            <div class="relative flex flex-col gap-8 pl-4">
                                <!-- Vertical connecting bar with gradient -->
                                <div
                                    class="absolute left-7 top-4 bottom-4 w-1 bg-gradient-to-b from-status-green via-amber-action to-surface-container-highest rounded-full">
                                </div>
                                <!-- Step 1: Completed -->
                                <div class="relative flex items-start gap-4 group">
                                    <div
                                        class="relative z-10 w-7 h-7 rounded-full bg-status-green text-canvas-ivory flex items-center justify-center shadow-md shrink-0 ring-4 ring-white">
                                        <span class="material-symbols-outlined text-body-small font-bold">check</span>
                                    </div>
                                    <div class="flex-1 bg-surface-container-low p-4 rounded-2xl">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="font-title-card text-title-card text-slate-authority font-bold">Payment
                                                Confirmed</span>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant">Today,
                                                14:28 EST</span>
                                        </div>
                                        <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                                            $4,998.00 securely locked into Bazaario Smart Vault #ESC-9482. Seller
                                            receives zero disbursement until physical acceptance.
                                        </p>
                                    </div>
                                </div>
                                <!-- Step 2: Completed -->
                                <div class="relative flex items-start gap-4 group">
                                    <div
                                        class="relative z-10 w-7 h-7 rounded-full bg-status-green text-canvas-ivory flex items-center justify-center shadow-md shrink-0 ring-4 ring-white">
                                        <span class="material-symbols-outlined text-body-small font-bold">check</span>
                                    </div>
                                    <div class="flex-1 bg-surface-container-low p-4 rounded-2xl">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="font-title-card text-title-card text-slate-authority font-bold">Order
                                                Placed &amp; Logged</span>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant">Today,
                                                14:29 EST</span>
                                        </div>
                                        <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                                            Cryptographic contract issued. Dispatched dispatch request alerts to 2
                                            verified market craftspeople.
                                        </p>
                                    </div>
                                </div>
                                <!-- Step 3: Active Stage -->
                                <div class="relative flex items-start gap-4 group">
                                    <div
                                        class="relative z-10 w-7 h-7 rounded-full bg-amber-action text-slate-authority flex items-center justify-center shadow-lg shrink-0 ring-4 ring-amber-100 animate-pulse">
                                        <span
                                            class="material-symbols-outlined text-body-small font-bold">inventory_2</span>
                                    </div>
                                    <div class="flex-1 bg-amber-action/10 p-4 rounded-2xl shadow-xs">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="font-title-card text-title-card text-slate-authority font-bold flex items-center gap-2">
                                                Seller Packaging in Progress
                                                <span
                                                    class="font-label-micro text-label-micro bg-amber-action text-slate-authority px-2 py-0.5 rounded-full font-bold">PROCESSING</span>
                                            </span>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant font-bold">Estimated
                                                4h left</span>
                                        </div>
                                        <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                                            Studio TechWorld &amp; CeramicWorks are inspecting tactile seals, serial
                                            verification, and bubble-armored transit wrap.
                                        </p>
                                        <div class="mt-3 w-full bg-surface-container rounded-full h-2 overflow-hidden">
                                            <div
                                                class="bg-amber-action h-full w-2/3 rounded-full transition-all duration-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Step 4: Upcoming -->
                                <div class="relative flex items-start gap-4 group opacity-60">
                                    <div
                                        class="relative z-10 w-7 h-7 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center shrink-0 ring-4 ring-white">
                                        <span class="material-symbols-outlined text-body-small">local_shipping</span>
                                    </div>
                                    <div class="flex-1 bg-surface-container-low p-4 rounded-2xl">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="font-title-card text-title-card text-slate-authority font-semibold">Courier
                                                Carrier Hand-off</span>
                                            <span
                                                class="font-label-micro text-label-micro text-on-surface-variant">Pending</span>
                                        </div>
                                        <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                                            Assigned to Priority Armored Courier with real-time GPS telemetry and
                                            unboxing OTP lock.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 3D Interactive Escrow Vault Visual Badge -->
                        <div
                            class="bg-slate-authority text-canvas-ivory rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="relative z-10">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-amber-action">verified_user</span>
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow text-amber-action uppercase font-bold tracking-widest">Autonomous
                                        Guarantee</span>
                                </div>
                                <h3 class="font-headline-section text-title-card font-bold mb-2">7-Day Unconditional
                                    Inspection Window</h3>
                                <p class="font-body-small text-body-small text-white/70 max-w-md">
                                    You inspect the physical hardware or craft before final release. If unsatisfied or
                                    damaged, our automated escrow reverses payment directly to your account.
                                </p>
                            </div>
                            <div class="shrink-0 relative">
                                <div
                                    class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-lg flex flex-col items-center justify-center p-3 text-center shadow-lg">
                                    <span
                                        class="font-display-hero text-headline-section font-bold text-amber-action leading-none">100%</span>
                                    <span
                                        class="font-label-micro text-label-micro text-white/80 uppercase font-semibold mt-1">Zero
                                        Risk</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column: Order Items & Actions Deck (5 cols) -->
                    <div class="lg:col-span-5 flex flex-col gap-6">
                        <!-- Purchased Articles Glass Matrix -->
                        <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-md">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="font-headline-section text-title-card text-slate-authority">Package Contents
                                </h2>
                                <span
                                    class="font-label-micro text-label-micro bg-surface-container text-on-surface-variant px-2.5 py-1 rounded-full font-bold">2
                                    Articles</span>
                            </div>
                            <!-- Product Item 1 -->
                            <div class="flex items-center gap-4 py-4 border-b border-surface-container">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-surface-container-low overflow-hidden relative shrink-0 shadow-xs">
                                    <img alt="Artisan Ceramic Pour-Over and Cup" class="w-full h-full object-cover"
                                        data-alt="A tactile handcrafted artisan matte ceramic pour-over dripper set on warm ivory surface with soft sunlight and studio lighting"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBYgVxfNRWBJ9QNVxGH3usyXxDw9WAUi4RutfaCG4DUbbg_vXxaScqGtN8HPmHsYUUfdFLZS1gcFJ53f5xjWzVcnJZgjmrolnHGA_0qFTtb9sgFSVPYqWhsZnZZ6-isr919zYiKAGqRnUYYGKwrfSBntYgS1mZVWqn9TpaebXOZcZqaP5-uqt2kZJdNet39WPMc9YIrwR_uNEJ178XXN6nZWp_SGKfve1D6FQrHBjF-mxDWwHcKVRFY" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span
                                        class="font-label-micro text-label-micro text-amber-action font-semibold block uppercase">Studio
                                        Craft</span>
                                    <h3 class="font-title-card text-body-small font-bold text-slate-authority truncate">
                                        Artisan Ceramic Pour-Over &amp; Mug</h3>
                                    <p class="font-body-small text-label-micro text-on-surface-variant">Qty: 1 • Glazed
                                        Sandstone</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span
                                        class="font-title-card text-body-small font-bold text-slate-authority block">$1,500.00</span>
                                    <span class="font-label-micro text-label-micro text-status-green">Verified
                                        Auth</span>
                                </div>
                            </div>
                            <!-- Product Item 2 -->
                            <div class="flex items-center gap-4 py-4">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-surface-container-low overflow-hidden relative shrink-0 shadow-xs">
                                    <img alt="Retro Cream Mechanical Keyboard Keycaps"
                                        class="w-full h-full object-cover"
                                        data-alt="High quality retro cream mechanical keyboard custom keycaps on textured modern wooden aesthetic workspace with soft dynamic 3D lighting"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDWw9P7oBUdO2-L1vkDSsJyrOYCRTLtpGkEopAZFEA4Zvv5PSRxlnNCO31zg6Y8R9WZx78kT7zcrqo4uKBCQAd3J8tx4CyzCPdw5drV4fR2vX6aLjl3oAEknKaJcIsGUczIr8ppAcfm-RgAW8T9H68SgGGj49OOWxv0WhNxurKWe_ehBD0lnvsvxbVTmZ0NPVD-DK59pZzi57fi9A7XqC7g6eUolTrzsttbsBP4w-DbDHytOvJJIzsU" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span
                                        class="font-label-micro text-label-micro text-amber-action font-semibold block uppercase">TechWorld
                                        Store</span>
                                    <h3 class="font-title-card text-body-small font-bold text-slate-authority truncate">
                                        Retro Cream Wireless Keycaps Set</h3>
                                    <p class="font-body-small text-label-micro text-on-surface-variant">Qty: 1 • ANSI
                                        Double-shot PBT</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span
                                        class="font-title-card text-body-small font-bold text-slate-authority block">$3,498.00</span>
                                    <span class="font-label-micro text-label-micro text-status-green">Sealed Mint</span>
                                </div>
                            </div>
                            <!-- Price Calculation Sub-Panel -->
                            <div class="bg-surface-container-low rounded-2xl p-4 mt-2 flex flex-col gap-2">
                                <div
                                    class="flex justify-between font-body-small text-body-small text-on-surface-variant">
                                    <span>Items Total (MRP)</span>
                                    <span>$4,998.00</span>
                                </div>
                                <div
                                    class="flex justify-between font-body-small text-body-small text-on-surface-variant">
                                    <span>Escrow Protection Fee</span>
                                    <span class="text-status-green font-bold">FREE (Tier 1)</span>
                                </div>
                                <div
                                    class="flex justify-between font-body-small text-body-small text-on-surface-variant">
                                    <span>Insured Freight &amp; Courier</span>
                                    <span class="text-status-green font-bold">FREE</span>
                                </div>
                                <div class="h-px bg-surface-container-highest my-1"></div>
                                <div
                                    class="flex justify-between font-title-card text-title-card text-slate-authority font-bold">
                                    <span>Settlement Total</span>
                                    <span class="text-amber-action">$4,998.00</span>
                                </div>
                            </div>
                        </div>
                        <!-- Verified Shipping Address Snippet -->
                        <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-md">
                            <div class="flex items-center justify-between mb-3">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow text-on-surface-variant uppercase font-bold tracking-wider">Secured
                                    Dispatch Node</span>
                                <span class="material-symbols-outlined text-amber-action text-body-lead">pin_drop</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-slate-authority text-canvas-ivory flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-body-small">home_pin</span>
                                </div>
                                <div class="text-left">
                                    <h4 class="font-title-card text-body-small font-bold text-slate-authority">Avijit
                                        Roy (Verified Buyer)</h4>
                                    <p class="font-body-small text-body-small text-on-surface-variant mt-0.5">
                                        402 Palm Heights, Central Boulevard<br />
                                        Bandra West, Mumbai, 400050<br />
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant mt-1 block">Phone:
                                            +91 98200 ••••• (OTP verification locked)</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Interactive Action Deck -->
                        <div class="flex flex-col gap-3">
                            <!-- Primary Track Order CTA -->
                            <a class="w-full bg-amber-action hover:bg-amber-action/90 text-slate-authority font-button-text text-button-text font-bold py-4 px-6 rounded-2xl shadow-md flex items-center justify-center gap-2 active:scale-[0.99] transition-all"
                                data-path="order-tracking" href="#">
                                <span class="material-symbols-outlined text-title-card">travel_explore</span>
                                Track Order Dispatch
                            </a>
                            <!-- Secondary Glass CTA -->
                            <a class="w-full bg-surface-container-lowest hover:bg-white text-slate-authority font-button-text text-body-small font-semibold py-3.5 px-6 rounded-2xl shadow-xs flex items-center justify-center gap-2 active:scale-[0.99] transition-all"
                                data-path="shop" href="#">
                                Continue Exploring Market
                                <span class="material-symbols-outlined text-body-small">arrow_forward</span>
                            </a>
                            <!-- Download PDF Receipt Link -->
                            <div class="text-center pt-2">
                                <button
                                    class="inline-flex items-center gap-1.5 font-label-micro text-body-small text-on-surface-variant hover:text-slate-authority transition-colors cursor-pointer"
                                    id="downloadInvoiceBtn" type="button">
                                    <span class="material-symbols-outlined text-body-small">picture_as_pdf</span>
                                    <span>Download GST Cryptographic Invoice (PDF)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Interactive AI Assistant Bottom Dock Bar -->
                <div
                    class="mt-8 bg-surface-container-lowest/80 backdrop-blur-xl p-4 md:p-5 rounded-2xl shadow-md flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-action to-purple-400 flex items-center justify-center text-slate-authority font-bold shadow-xs">
                            <span class="material-symbols-outlined text-body-lead">auto_awesome</span>
                        </div>
                        <div>
                            <span class="font-title-card text-body-small font-bold text-slate-authority block">Need
                                instantaneous assistance with your order?</span>
                            <span class="font-body-small text-label-micro text-on-surface-variant">Bazaario Intelligence
                                AI is standing by 24/7 with direct carrier teleport data.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input
                            class="w-full sm:w-64 bg-surface-container-low border-0 rounded-xl px-3.5 py-2 text-body-small text-slate-authority placeholder:text-on-surface-variant/50 focus:ring-1 focus:ring-amber-action text-left"
                            id="aiQueryInput" placeholder="Ask AI: 'When is dispatch?'" type="text" />
                        <button
                            class="shrink-0 bg-slate-authority text-canvas-ivory px-4 py-2 rounded-xl font-button-text text-body-small font-semibold hover:bg-slate-authority/90 transition-all"
                            id="aiAskBtn" type="button">
                            Ask
                        </button>
                    </div>
                </div>
            </div>
            <!-- Notification Toast Container for Copy / Download Action -->
            <div class="fixed bottom-6 right-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none"
                id="actionToast">
                <div
                    class="bg-slate-authority text-canvas-ivory px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-amber-action" id="toastIcon">check_circle</span>
                    <span class="font-body-small text-body-small font-medium" id="toastMsg">Action completed</span>
                </div>
            </div>
            <script>
                    (function () {
                        const copyBtn = document.getElementById('copyOrderBtn');
                        const invoiceBtn = document.getElementById('downloadInvoiceBtn');
                        const toast = document.getElementById('actionToast');
                        const toastMsg = document.getElementById('toastMsg');
                        const toastIcon = document.getElementById('toastIcon');
                        const aiAskBtn = document.getElementById('aiAskBtn');
                        const aiInput = document.getElementById('aiQueryInput');

                        function showToast(message, icon = 'check_circle') {
                            if (!toast) return;
                            toastMsg.textContent = message;
                            toastIcon.textContent = icon;
                            toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
                            setTimeout(() => {
                                toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
                            }, 3000);
                        }

                        if (copyBtn) {
                            copyBtn.addEventListener('click', () => {
                                const orderId = document.getElementById('orderIdText')?.textContent || 'BZ-2026-00128';
                                navigator.clipboard?.writeText(orderId).then(() => {
                                    showToast('Order ID copied to clipboard!', 'content_copy');
                                }).catch(() => {
                                    showToast('Order ID copied: ' + orderId, 'content_copy');
                                });
                            });
                        }

                        if (invoiceBtn) {
                            invoiceBtn.addEventListener('click', () => {
                                showToast('Generating official tax PDF receipt...', 'picture_as_pdf');
                            });
                        }

                        if (aiAskBtn && aiInput) {
                            aiAskBtn.addEventListener('click', () => {
                                const query = aiInput.value.trim();
                                if (query) {
                                    showToast('AI: Packaging estimated within 3.5 hrs. Safe transit assured.', 'auto_awesome');
                                    aiInput.value = '';
                                } else {
                                    showToast('Please enter an inquiry for Bazaario AI.', 'info');
                                }
                            });

                            aiInput.addEventListener('keydown', (e) => {
                                if (e.key === 'Enter') {
                                    aiAskBtn.click();
                                }
                            });
                        }
                    })();
            </script>
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