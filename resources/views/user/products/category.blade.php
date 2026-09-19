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
                    <a aria-current="page"
                        class="transition-all bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold px-3.5 py-1.5 rounded-full shadow-[0_2px_8px_rgba(245,166,35,0.35)] border border-amber-300/40"
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
            <div class="w-full max-w-container-max mx-auto px-gutter-md py-4 flex flex-col gap-6">
                <!-- Top Breadcrumb & Micro-Trust Bar -->
                <div
                    class="flex flex-wrap items-center justify-between gap-3 text-on-surface-variant text-body-small font-body-small">
                    <nav
                        class="flex items-center gap-2 bg-white/50 backdrop-blur-md px-3 py-1 rounded-full border border-white/60 shadow-xs">
                        <a class="hover:text-slate-authority transition-colors flex items-center gap-1" data-path="home"
                            href="#">
                            <span class="material-symbols-outlined text-body-small">home</span>
                            <span>Home</span>
                        </a>
                        <span class="text-outline/40">/</span>
                        <a class="hover:text-slate-authority transition-colors" data-path="categories"
                            href="#">Categories</a>
                        <span class="text-outline/40">/</span>
                        <span class="text-slate-authority font-bold">Electronics</span>
                    </nav>
                    <div
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-status-green/10 backdrop-blur-md text-status-green border border-status-green/25 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                        <span class="font-label-micro text-label-micro font-semibold uppercase tracking-wider">BAZAARIO
                            ESCROW SECURED</span>
                    </div>
                </div>
                <!-- Category Hero Banner with 3D Dimensional Glass Atmosphere -->
                <section
                    class="relative w-full rounded-3xl bg-gradient-to-br from-slate-authority/95 via-[#131E35] to-[#1E293B] text-canvas-ivory overflow-hidden p-6 md:p-10 border border-white/20 shadow-[0_20px_50px_rgba(15,23,42,0.25)] backdrop-blur-2xl">
                    <!-- Glowing Ambient Iridescent Accents -->
                    <div
                        class="absolute -right-16 -top-16 w-[420px] h-[420px] rounded-full bg-gradient-to-bl from-amber-action/25 to-purple-500/15 blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute left-1/3 -bottom-24 w-80 h-80 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,0.08),transparent_70%)] pointer-events-none">
                    </div>
                    <div class="relative z-10 grid md:grid-cols-12 gap-8 items-center">
                        <div class="md:col-span-7 flex flex-col gap-4">
                            <div
                                class="inline-flex items-center gap-2 self-start bg-white/10 backdrop-blur-xl px-3.5 py-1.5 rounded-full border border-white/20 text-amber-action font-label-eyebrow text-label-eyebrow uppercase tracking-widest font-semibold shadow-inner">
                                <span class="material-symbols-outlined text-label-eyebrow">memory</span>
                                Certified Authenticity Division
                            </div>
                            <h1
                                class="font-display-hero text-display-hero tracking-tight text-white font-bold leading-none drop-shadow-sm">
                                ELECTRONICS <span
                                    class="bg-gradient-to-r from-amber-300 via-amber-action to-amber-200 bg-clip-text text-transparent drop-shadow-sm font-semibold">VAULT</span>
                            </h1>
                            <p class="font-body-lead text-body-lead text-white/80 max-w-xl">
                                Everything you need, all in one place. Every chip, lens, and display strictly inspected
                                via multi-point decentralized escrow assurance.
                            </p>
                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                <div
                                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 border border-white/20 backdrop-blur-md shadow-xs">
                                    <span
                                        class="material-symbols-outlined text-amber-action text-body-regular">inventory_2</span>
                                    <span class="font-label-eyebrow text-label-eyebrow font-medium text-white">328
                                        Curated Products</span>
                                </div>
                                <div
                                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 border border-white/20 backdrop-blur-md shadow-xs">
                                    <span
                                        class="material-symbols-outlined text-status-green text-body-regular">verified</span>
                                    <span class="font-label-eyebrow text-label-eyebrow font-medium text-white">42
                                        Verified Sellers</span>
                                </div>
                                <div
                                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 border border-white/20 backdrop-blur-md shadow-xs">
                                    <span
                                        class="material-symbols-outlined text-amber-action text-body-regular">shield_with_heart</span>
                                    <span class="font-label-eyebrow text-label-eyebrow font-medium text-white">100%
                                        Escrow Protected</span>
                                </div>
                            </div>
                        </div>
                        <!-- 3D Dimensional Marketplace Glass Showcase Widget -->
                        <div class="md:col-span-5 flex flex-col items-center md:items-end justify-center">
                            <div
                                class="relative w-full max-w-sm rounded-3xl bg-white/[0.08] backdrop-blur-2xl border border-white/25 p-5 flex flex-col gap-4 shadow-[0_16px_40px_rgba(0,0,0,0.35)] relative overflow-hidden group">
                                <!-- Specular highlight line on top border -->
                                <div
                                    class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/50 to-transparent">
                                </div>
                                <div class="flex items-center justify-between pb-3 border-b border-white/15">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-3 h-3 rounded-full bg-amber-action shadow-[0_0_10px_rgba(245,166,35,0.8)]"></span>
                                        <span
                                            class="font-label-micro text-label-micro tracking-widest text-white/90 uppercase font-bold">Hardware
                                            Guard Metric</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green font-bold bg-status-green/20 px-2.5 py-0.5 rounded-full border border-status-green/30">PASS
                                        99.8%</span>
                                </div>
                                <!-- 3D Visual Teaser & Metric Cards -->
                                <div
                                    class="relative rounded-2xl overflow-hidden border border-white/20 bg-black/20 p-2 flex items-center justify-between">
                                    <img alt="3D Marketplace Glass Render"
                                        class="w-20 h-20 rounded-xl object-cover shrink-0 border border-white/20 shadow-md transform group-hover:scale-105 transition-transform duration-300"
                                        src="https://lh3.googleusercontent.com/aida/AEtjO1VbxQxTE236aWF3wyF8Ou9sGV29jSDWJIeyPpXw4ZRNlpaNJ14xn4G4_qbazvvebzc-PqOkeQn7dBNi0J1r2NeH47T8DOXLkBSh1SM1lUb9b59v-i3l-Zf5KAmb_4_qlGOrLfO2PLHLp-Sif4BhDyz06RvpKqLELulEVdJPG48ixs_7SMoyfwJlW7C2Cn0sg9YiUorrmQXNWohqsHBXyBVnIqX4CSt-Zqs28Enmerd6ePRu_C-WdPPEcEk" />
                                    <div class="grid grid-cols-2 gap-2 flex-1 ml-3 text-center">
                                        <div
                                            class="p-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 flex flex-col items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-amber-action text-body-small mb-0.5">radar</span>
                                            <span class="font-label-micro text-label-micro text-white/70">Serial
                                                Match</span>
                                            <span
                                                class="font-headline-section text-title-card text-white font-bold leading-tight">100%</span>
                                        </div>
                                        <div
                                            class="p-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 flex flex-col items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-status-green text-body-small mb-0.5">lock_clock</span>
                                            <span class="font-label-micro text-label-micro text-white/70">Escrow
                                                Hold</span>
                                            <span
                                                class="font-headline-section text-title-card text-white font-bold leading-tight">48
                                                Hrs</span>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="p-3 rounded-xl bg-gradient-to-r from-amber-action/25 to-amber-500/10 border border-amber-action/35 flex items-center gap-2.5 text-canvas-ivory backdrop-blur-md">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-amber-action text-slate-authority flex items-center justify-center shrink-0 font-bold shadow-sm">
                                        <span class="material-symbols-outlined text-body-regular">gavel</span>
                                    </div>
                                    <p class="font-label-micro text-label-micro leading-snug text-white/90">
                                        Inspected for motherboard integrity &amp; serial tampering prior to payout.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Subcategory Quick Filter Pill Bar (Frosted 3D Glass Pill Dock) -->
                <div
                    class="w-full bg-white/60 backdrop-blur-xl border border-white/80 rounded-2xl p-2 shadow-[0_8px_24px_rgba(15,23,42,0.04)] relative">
                    <div class="flex items-center gap-2.5 overflow-x-auto no-scrollbar scroll-smooth">
                        <button
                            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold shadow-[0_4px_14px_rgba(245,166,35,0.4)] border border-amber-300/60 transition-all hover:scale-[1.02] active:scale-[0.98]"
                            type="button">
                            <span class="material-symbols-outlined text-body-regular">grid_view</span>
                            <span>All</span>
                            <span
                                class="font-label-micro text-label-micro bg-slate-authority/15 px-2 py-0.5 rounded-full">328</span>
                        </button>
                        <button
                            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/70 hover:bg-white text-slate-authority font-button-text text-body-small font-semibold border border-white/80 hover:border-amber-action/50 shadow-xs hover:shadow-md transition-all hover:scale-[1.02] active:scale-[0.98]"
                            type="button">
                            <span
                                class="material-symbols-outlined text-body-regular text-slate-authority">smartphone</span>
                            <span>Phones</span>
                            <span
                                class="font-label-micro text-label-micro text-on-surface-variant bg-black/[0.04] px-2 py-0.5 rounded-full border border-black/[0.06]">94</span>
                        </button>
                        <button
                            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/70 hover:bg-white text-slate-authority font-button-text text-body-small font-semibold border border-white/80 hover:border-amber-action/50 shadow-xs hover:shadow-md transition-all hover:scale-[1.02] active:scale-[0.98]"
                            type="button">
                            <span
                                class="material-symbols-outlined text-body-regular text-slate-authority">laptop_mac</span>
                            <span>Laptops</span>
                            <span
                                class="font-label-micro text-label-micro text-on-surface-variant bg-black/[0.04] px-2 py-0.5 rounded-full border border-black/[0.06]">62</span>
                        </button>
                        <button
                            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/70 hover:bg-white text-slate-authority font-button-text text-body-small font-semibold border border-white/80 hover:border-amber-action/50 shadow-xs hover:shadow-md transition-all hover:scale-[1.02] active:scale-[0.98]"
                            type="button">
                            <span
                                class="material-symbols-outlined text-body-regular text-slate-authority">headphones</span>
                            <span>Audio</span>
                            <span
                                class="font-label-micro text-label-micro text-on-surface-variant bg-black/[0.04] px-2 py-0.5 rounded-full border border-black/[0.06]">58</span>
                        </button>
                        <button
                            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/70 hover:bg-white text-slate-authority font-button-text text-body-small font-semibold border border-white/80 hover:border-amber-action/50 shadow-xs hover:shadow-md transition-all hover:scale-[1.02] active:scale-[0.98]"
                            type="button">
                            <span
                                class="material-symbols-outlined text-body-regular text-slate-authority">photo_camera</span>
                            <span>Cameras</span>
                            <span
                                class="font-label-micro text-label-micro text-on-surface-variant bg-black/[0.04] px-2 py-0.5 rounded-full border border-black/[0.06]">31</span>
                        </button>
                        <button
                            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/70 hover:bg-white text-slate-authority font-button-text text-body-small font-semibold border border-white/80 hover:border-amber-action/50 shadow-xs hover:shadow-md transition-all hover:scale-[1.02] active:scale-[0.98]"
                            type="button">
                            <span
                                class="material-symbols-outlined text-body-regular text-slate-authority">keyboard</span>
                            <span>Keyboards &amp; Accessories</span>
                            <span
                                class="font-label-micro text-label-micro text-on-surface-variant bg-black/[0.04] px-2 py-0.5 rounded-full border border-black/[0.06]">83</span>
                        </button>
                    </div>
                </div>
                <!-- Catalog Filter & Sorting Bar -->
                <div
                    class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 py-2 border-b border-black/[0.06]">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2
                            class="font-headline-section text-headline-section font-bold text-slate-authority tracking-tight">
                            Electronics Catalog
                        </h2>
                        <span
                            class="font-label-micro text-label-micro font-bold bg-slate-authority text-canvas-ivory px-2.5 py-1 rounded-full shadow-xs">
                            328 PRODUCTS
                        </span>
                        <div class="hidden sm:flex items-center gap-2 ml-2">
                            <button
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-label-eyebrow font-label-eyebrow bg-white/80 backdrop-blur-sm border border-white/80 hover:border-amber-action text-slate-authority shadow-xs transition-all hover:scale-105"
                                type="button">
                                <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                <span>In Stock</span>
                            </button>
                            <button
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-label-eyebrow font-label-eyebrow bg-white/80 backdrop-blur-sm border border-white/80 hover:border-amber-action text-slate-authority shadow-xs transition-all hover:scale-105"
                                type="button">
                                <span
                                    class="material-symbols-outlined text-label-eyebrow text-amber-action">verified</span>
                                <span>Escrow Verified</span>
                            </button>
                            <button
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-label-eyebrow font-label-eyebrow bg-white/80 backdrop-blur-sm border border-white/80 hover:border-amber-action text-slate-authority shadow-xs transition-all hover:scale-105"
                                type="button">
                                <span
                                    class="material-symbols-outlined text-label-eyebrow text-amber-action">hotel_class</span>
                                <span>Top Rated (4.8+)</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 self-end lg:self-auto w-full sm:w-auto">
                        <!-- Compact Search -->
                        <div class="relative flex-1 sm:w-64">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-small">search</span>
                            <input
                                class="w-full bg-white/80 backdrop-blur-md border border-white/90 rounded-xl pl-9 pr-3 py-2 text-body-small font-body-small text-slate-authority placeholder:text-on-surface-variant/60 focus:outline-none focus:border-amber-action shadow-xs"
                                placeholder="Search electronics..." type="text" />
                        </div>
                        <!-- Sort Select -->
                        <div class="relative">
                            <select
                                class="appearance-none bg-white/80 backdrop-blur-md border border-white/90 rounded-xl pl-3.5 pr-8 py-2 text-body-small font-button-text font-semibold text-slate-authority hover:border-amber-action cursor-pointer focus:outline-none focus:border-amber-action shadow-xs">
                                <option>Sort: Popular</option>
                                <option>Price: Low to High</option>
                                <option>Price: High to Low</option>
                                <option>Highest Rating</option>
                                <option>Newest Arrivals</option>
                            </select>
                            <span
                                class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-authority pointer-events-none text-body-small">expand_more</span>
                        </div>
                        <!-- Layout toggle -->
                        <div
                            class="flex items-center bg-white/70 backdrop-blur-md rounded-xl p-1 border border-white/80 shadow-xs">
                            <button class="p-1.5 rounded-lg bg-white text-slate-authority shadow-sm" title="Grid View"
                                type="button">
                                <span class="material-symbols-outlined text-body-regular block">grid_view</span>
                            </button>
                            <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-slate-authority"
                                title="List View" type="button">
                                <span class="material-symbols-outlined text-body-regular block">view_list</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Curated 4-Column Product Grid (Tactile 3D Dimensional Glass Tiles) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Item 1: MacBook Pro M3 -->
                    <article
                        class="group bg-white/80 backdrop-blur-xl border border-white/90 rounded-3xl p-4 flex flex-col justify-between hover:border-amber-action/60 hover:-translate-y-1.5 shadow-[0_12px_36px_rgba(15,23,42,0.06)] hover:shadow-[0_20px_45px_rgba(245,166,35,0.15)] transition-all duration-300 relative overflow-hidden">
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent">
                        </div>
                        <div>
                            <div
                                class="relative w-full aspect-square rounded-2xl overflow-hidden bg-gradient-to-b from-stone-100 to-stone-200/70 mb-3.5 flex items-center justify-center p-2 shadow-inner">
                                <img alt="Space Black Apple MacBook Pro M3"
                                    class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1WmVwiFHlpeR7aYWEXJI4MXdP_CMYZQzm9EupPH1KVffNXSiO5PbZE_Tl4V_tHbuNP3hZJ0_nT2oTXbZIQZyfuHDsn592L5uVajacxVQs83-yA9rgzvgTCkCPFH1bEB5Xhg6AZfBoRqtN_xg9zUgydCzGubY78iWLgaQXB-BLQKX-06YRbcoJMVWNDeA3muHHDYcfbSIMMagz19qyd9UFJwbexjK_t_q8IxTxQur18NCgCLnTQ057oWX-E" />
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="font-label-micro text-label-micro uppercase font-bold bg-amber-action/90 backdrop-blur-md text-slate-authority px-2.5 py-1 rounded-lg tracking-wider shadow-sm border border-amber-200/50">Top
                                        Pick</span>
                                </div>
                                <button aria-label="Add to wishlist"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border border-white/90 flex items-center justify-center text-slate-authority hover:text-error hover:bg-white hover:scale-110 transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-body-regular">favorite</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action font-bold tracking-wider">Laptops</span>
                                <div
                                    class="flex items-center gap-1 text-label-micro font-label-micro text-on-surface-variant font-medium bg-black/[0.03] px-2 py-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-amber-action text-label-eyebrow"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-slate-authority font-bold">4.9</span>
                                    <span>(142)</span>
                                </div>
                            </div>
                            <h3
                                class="font-title-card text-title-card font-bold text-slate-authority line-clamp-1 group-hover:text-amber-600 transition-colors">
                                Space Black Apple MacBook Pro M3
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                M3 Pro 18GB unified, Liquid Retina XDR 14-inch
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-black/[0.05] flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="font-headline-section text-headline-section font-bold text-slate-authority">₹1,69,999</span>
                                <span
                                    class="font-body-small text-body-small line-through text-on-surface-variant/70">₹1,89,999</span>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 border border-status-green/20 px-2 py-0.5 rounded-md">10%
                                    OFF</span>
                            </div>
                            <button
                                class="w-full bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:shadow-[0_4px_16px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-sm border border-amber-300/60"
                                type="button">
                                <span class="material-symbols-outlined text-body-regular">shopping_cart</span>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </article>
                    <!-- Item 2: Sony WH-1000XM5 -->
                    <article
                        class="group bg-white/80 backdrop-blur-xl border border-white/90 rounded-3xl p-4 flex flex-col justify-between hover:border-amber-action/60 hover:-translate-y-1.5 shadow-[0_12px_36px_rgba(15,23,42,0.06)] hover:shadow-[0_20px_45px_rgba(245,166,35,0.15)] transition-all duration-300 relative overflow-hidden">
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent">
                        </div>
                        <div>
                            <div
                                class="relative w-full aspect-square rounded-2xl overflow-hidden bg-gradient-to-b from-stone-100 to-stone-200/70 mb-3.5 flex items-center justify-center p-2 shadow-inner">
                                <img alt="Sony WH-1000XM5 Wireless Headphones"
                                    class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1ULHYfZ58DWHb3ovmkHCOx30d2y2KNFJNjSaNeyIekug2Nn45SSGMa1s8vyF89_u8JpyK5DcURAFa2FtF4vEIv2pVZGC2MzAZq1rRjLYQKE-t52YAMdsIJW5prgjmSFsExmX2aiDaBf_jZkASVFGXLFGDmCoTL9fUxPtPDX9hogJvtf9H4TP8MNd42K8QLAuao7kkBFZ2byKJcoFdTnhVs9s7R2FX26LF17V1ZCh9hOhUu4lmibOG9-0g" />
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="font-label-micro text-label-micro uppercase font-bold bg-slate-authority/90 backdrop-blur-md text-canvas-ivory px-2.5 py-1 rounded-lg tracking-wider shadow-sm border border-white/20">Bestseller</span>
                                </div>
                                <button aria-label="Add to wishlist"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border border-white/90 flex items-center justify-center text-slate-authority hover:text-error hover:bg-white hover:scale-110 transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-body-regular">favorite</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action font-bold tracking-wider">Audio</span>
                                <div
                                    class="flex items-center gap-1 text-label-micro font-label-micro text-on-surface-variant font-medium bg-black/[0.03] px-2 py-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-amber-action text-label-eyebrow"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-slate-authority font-bold">4.8</span>
                                    <span>(312)</span>
                                </div>
                            </div>
                            <h3
                                class="font-title-card text-title-card font-bold text-slate-authority line-clamp-1 group-hover:text-amber-600 transition-colors">
                                Sony WH-1000XM5 Noise Cancelling
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                Auto NC Optimizer, 30hr battery, multipoint pairing
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-black/[0.05] flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="font-headline-section text-headline-section font-bold text-slate-authority">₹24,990</span>
                                <span
                                    class="font-body-small text-body-small line-through text-on-surface-variant/70">₹29,990</span>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 border border-status-green/20 px-2 py-0.5 rounded-md">17%
                                    OFF</span>
                            </div>
                            <button
                                class="w-full bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:shadow-[0_4px_16px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-sm border border-amber-300/60"
                                type="button">
                                <span class="material-symbols-outlined text-body-regular">shopping_cart</span>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </article>
                    <!-- Item 3: iPhone 15 Pro Max -->
                    <article
                        class="group bg-white/80 backdrop-blur-xl border border-white/90 rounded-3xl p-4 flex flex-col justify-between hover:border-amber-action/60 hover:-translate-y-1.5 shadow-[0_12px_36px_rgba(15,23,42,0.06)] hover:shadow-[0_20px_45px_rgba(245,166,35,0.15)] transition-all duration-300 relative overflow-hidden">
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent">
                        </div>
                        <div>
                            <div
                                class="relative w-full aspect-square rounded-2xl overflow-hidden bg-gradient-to-b from-stone-100 to-stone-200/70 mb-3.5 flex items-center justify-center p-2 shadow-inner">
                                <img alt="Apple iPhone 15 Pro Max Natural Titanium"
                                    class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1WesLuM3IFLMPZUGxaMNdOhk2dJKDYQ9yuKcNdYKX5C18SpBicMn17UyRJAchtK3huE0flK1zcWeUZ2E942aIxfmt56ttVCJnTuVZro-ChI7-Gx-yDq86MhgcaZx1u8zK9Sydayqwz7q33bBL62MraJpkjRmKW2NFUL7KDzwDehHSlscPCLZJWfnyjxGASuHYn-dwCRd4AzCFyXSDcKSHZMYcuodKUilGRnaAriohwlHoo3_jPF0417mN0" />
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="font-label-micro text-label-micro uppercase font-bold bg-status-green/90 backdrop-blur-md text-white px-2.5 py-1 rounded-lg tracking-wider flex items-center gap-1 shadow-sm border border-emerald-300/40">
                                        <span class="material-symbols-outlined text-label-micro">verified</span>Escrow
                                        Verified
                                    </span>
                                </div>
                                <button aria-label="Add to wishlist"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border border-white/90 flex items-center justify-center text-slate-authority hover:text-error hover:bg-white hover:scale-110 transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-body-regular">favorite</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action font-bold tracking-wider">Phones</span>
                                <div
                                    class="flex items-center gap-1 text-label-micro font-label-micro text-on-surface-variant font-medium bg-black/[0.03] px-2 py-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-amber-action text-label-eyebrow"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-slate-authority font-bold">4.9</span>
                                    <span>(248)</span>
                                </div>
                            </div>
                            <h3
                                class="font-title-card text-title-card font-bold text-slate-authority line-clamp-1 group-hover:text-amber-600 transition-colors">
                                Apple iPhone 15 Pro Max Titanium
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                256GB Grade A+ Refurbished with battery report 100%
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-black/[0.05] flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="font-headline-section text-headline-section font-bold text-slate-authority">₹1,19,999</span>
                                <span
                                    class="font-body-small text-body-small line-through text-on-surface-variant/70">₹1,34,999</span>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 border border-status-green/20 px-2 py-0.5 rounded-md">11%
                                    OFF</span>
                            </div>
                            <button
                                class="w-full bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:shadow-[0_4px_16px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-sm border border-amber-300/60"
                                type="button">
                                <span class="material-symbols-outlined text-body-regular">shopping_cart</span>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </article>
                    <!-- Item 4: Retro Mechanical Keyboard 75% -->
                    <article
                        class="group bg-white/80 backdrop-blur-xl border border-white/90 rounded-3xl p-4 flex flex-col justify-between hover:border-amber-action/60 hover:-translate-y-1.5 shadow-[0_12px_36px_rgba(15,23,42,0.06)] hover:shadow-[0_20px_45px_rgba(245,166,35,0.15)] transition-all duration-300 relative overflow-hidden">
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent">
                        </div>
                        <div>
                            <div
                                class="relative w-full aspect-square rounded-2xl overflow-hidden bg-gradient-to-b from-stone-100 to-stone-200/70 mb-3.5 flex items-center justify-center p-2 shadow-inner">
                                <img alt="Retro Cream Mechanical Keyboard 75%"
                                    class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1V962eUfdX3W9GnnupcSTLq4Tj4fBcsQVyefenltjqVi57DxdkzzgTDibxjj3Yzbz2nHjzJc4mf14iqX6-ZoBGNkGV2MuMAUjwjBX5bHwb5HsGHHGEGqa8O93vN9qHtPEl-439Y21ZODyv1zHYyXj8PmErpyFrKKLYka9HfSEGbq2va_SX1PLem6YZwjhcJ88Tjmv7w83mQkPJSQYoQYZYPPhtt-jy46KqZisQ9VjvIcBg-CbzOJV9SzQ" />
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="font-label-micro text-label-micro uppercase font-bold bg-secondary/90 backdrop-blur-md text-canvas-ivory px-2.5 py-1 rounded-lg tracking-wider shadow-sm border border-emerald-300/30">Artisan
                                        Guild</span>
                                </div>
                                <button aria-label="Add to wishlist"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border border-white/90 flex items-center justify-center text-slate-authority hover:text-error hover:bg-white hover:scale-110 transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-body-regular">favorite</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action font-bold tracking-wider">Accessories</span>
                                <div
                                    class="flex items-center gap-1 text-label-micro font-label-micro text-on-surface-variant font-medium bg-black/[0.03] px-2 py-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-amber-action text-label-eyebrow"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-slate-authority font-bold">4.7</span>
                                    <span>(89)</span>
                                </div>
                            </div>
                            <h3
                                class="font-title-card text-title-card font-bold text-slate-authority line-clamp-1 group-hover:text-amber-600 transition-colors">
                                Retro Cream Mechanical Keyboard
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                Hot-swappable tactile amber switches with brass plate
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-black/[0.05] flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="font-headline-section text-headline-section font-bold text-slate-authority">₹18,500</span>
                                <span
                                    class="font-body-small text-body-small line-through text-on-surface-variant/70">₹21,000</span>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 border border-status-green/20 px-2 py-0.5 rounded-md">12%
                                    OFF</span>
                            </div>
                            <button
                                class="w-full bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:shadow-[0_4px_16px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-sm border border-amber-300/60"
                                type="button">
                                <span class="material-symbols-outlined text-body-regular">shopping_cart</span>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </article>
                    <!-- Item 5: Vintage Leica M 35mm -->
                    <article
                        class="group bg-white/80 backdrop-blur-xl border border-white/90 rounded-3xl p-4 flex flex-col justify-between hover:border-amber-action/60 hover:-translate-y-1.5 shadow-[0_12px_36px_rgba(15,23,42,0.06)] hover:shadow-[0_20px_45px_rgba(245,166,35,0.15)] transition-all duration-300 relative overflow-hidden">
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent">
                        </div>
                        <div>
                            <div
                                class="relative w-full aspect-square rounded-2xl overflow-hidden bg-gradient-to-b from-stone-100 to-stone-200/70 mb-3.5 flex items-center justify-center p-2 shadow-inner">
                                <img alt="Vintage Leica M 35mm Rangefinder Camera"
                                    class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1WtXge9GPbqerXxtDR81FmoO_hfHqYgmqlaYYUwc2Yh_MefBJoICSP9w7Wb0Cqr6tyKGrqQHUndgUWVoT8ggWFORDtiLGGzfQHiKXsLNWSKSi8IniszOYx4fofXjH1O2Fr6JH_kTpLyAmT-GYlGMOKE3SeqInPG0mwsWk2CEDPZEvzeA4FChdtZ_EWJd2NUmm0zgSRZojYH02eszXaZDsXknosVC-J0Vy91v025yswoZN2tqWyLVRPaitw" />
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="font-label-micro text-label-micro uppercase font-bold bg-[#7C2D12]/90 backdrop-blur-md text-canvas-ivory px-2.5 py-1 rounded-lg tracking-wider shadow-sm border border-orange-300/30">Rare
                                        Collector</span>
                                </div>
                                <button aria-label="Add to wishlist"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border border-white/90 flex items-center justify-center text-slate-authority hover:text-error hover:bg-white hover:scale-110 transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-body-regular">favorite</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action font-bold tracking-wider">Cameras</span>
                                <div
                                    class="flex items-center gap-1 text-label-micro font-label-micro text-on-surface-variant font-medium bg-black/[0.03] px-2 py-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-amber-action text-label-eyebrow"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-slate-authority font-bold">4.9</span>
                                    <span>(52)</span>
                                </div>
                            </div>
                            <h3
                                class="font-title-card text-title-card font-bold text-slate-authority line-clamp-1 group-hover:text-amber-600 transition-colors">
                                Vintage Leica M 35mm Rangefinder
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                Brass patina body, shutter timing verified by stallmaster
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-black/[0.05] flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="font-headline-section text-headline-section font-bold text-slate-authority">₹85,000</span>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-secondary bg-secondary/10 border border-secondary/20 px-2 py-0.5 rounded-md">Auction
                                    Grade</span>
                            </div>
                            <button
                                class="w-full bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:shadow-[0_4px_16px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-sm border border-amber-300/60"
                                type="button">
                                <span class="material-symbols-outlined text-body-regular">shopping_cart</span>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </article>
                    <!-- Item 6: Apple Watch Ultra 2 Titanium -->
                    <article
                        class="group bg-white/80 backdrop-blur-xl border border-white/90 rounded-3xl p-4 flex flex-col justify-between hover:border-amber-action/60 hover:-translate-y-1.5 shadow-[0_12px_36px_rgba(15,23,42,0.06)] hover:shadow-[0_20px_45px_rgba(245,166,35,0.15)] transition-all duration-300 relative overflow-hidden">
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent">
                        </div>
                        <div>
                            <div
                                class="relative w-full aspect-square rounded-2xl overflow-hidden bg-gradient-to-b from-stone-100 to-stone-200/70 mb-3.5 flex items-center justify-center p-2 shadow-inner">
                                <img alt="Apple Watch Ultra 2 Titanium"
                                    class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1WCV79P_WvGQN5KQJBCsoSb-BB7bk_bI9raCF39grO__0osmkm6wz8yOcsJDmbNAtpsoOLotAmSTm8MaUvtbCzz2kFK6vXUGpeo78gNOeqO1yyDPtdaIb533Ya-QTC0PN7pTYfSKxV6V-u3H9sok9Nj0YVvjmpsuzEplsjAWJRMufP453b17PWleiI6RG79hsIYDKekSa9mJ0sz3bbZnIjMwEebK0zG_961mM9Bh1cY8T9TmdK9Ce5EeQc" />
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="font-label-micro text-label-micro uppercase font-bold bg-slate-authority/90 backdrop-blur-md text-canvas-ivory px-2.5 py-1 rounded-lg tracking-wider shadow-sm border border-white/20">Direct
                                        Stall</span>
                                </div>
                                <button aria-label="Add to wishlist"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border border-white/90 flex items-center justify-center text-slate-authority hover:text-error hover:bg-white hover:scale-110 transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-body-regular">favorite</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action font-bold tracking-wider">Accessories</span>
                                <div
                                    class="flex items-center gap-1 text-label-micro font-label-micro text-on-surface-variant font-medium bg-black/[0.03] px-2 py-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-amber-action text-label-eyebrow"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-slate-authority font-bold">4.9</span>
                                    <span>(388)</span>
                                </div>
                            </div>
                            <h3
                                class="font-title-card text-title-card font-bold text-slate-authority line-clamp-1 group-hover:text-amber-600 transition-colors">
                                Apple Watch Ultra 2 Titanium
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                49mm Case with Ocean Band, precision dual-frequency GPS
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-black/[0.05] flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="font-headline-section text-headline-section font-bold text-slate-authority">₹42,000</span>
                                <span
                                    class="font-body-small text-body-small line-through text-on-surface-variant/70">₹49,900</span>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 border border-status-green/20 px-2 py-0.5 rounded-md">15%
                                    OFF</span>
                            </div>
                            <button
                                class="w-full bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:shadow-[0_4px_16px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-sm border border-amber-300/60"
                                type="button">
                                <span class="material-symbols-outlined text-body-regular">shopping_cart</span>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </article>
                    <!-- Item 7: Aura P50 Gold Edition Flagship -->
                    <article
                        class="group bg-white/80 backdrop-blur-xl border border-white/90 rounded-3xl p-4 flex flex-col justify-between hover:border-amber-action/60 hover:-translate-y-1.5 shadow-[0_12px_36px_rgba(15,23,42,0.06)] hover:shadow-[0_20px_45px_rgba(245,166,35,0.15)] transition-all duration-300 relative overflow-hidden">
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent">
                        </div>
                        <div>
                            <div
                                class="relative w-full aspect-square rounded-2xl overflow-hidden bg-gradient-to-b from-stone-100 to-stone-200/70 mb-3.5 flex items-center justify-center p-2 shadow-inner">
                                <img alt="Aura P50 Gold Edition Flagship"
                                    class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1U3oJ6qEpXnJRplfQ2cJaxsl6uVmv_P0CQvmKXrYwLfyvZyYHTx9MBo-40o6gU4zPDVUI_eEir24c0nvJhy7MznF2PT46ANJyjzLSQ2mFr-1fXzdbcx-L-TA4FV20dbrVF2qtEq_87em-lFUM7J83FkDQUEaRnMq4LtG3pjNFYWfG-e6YU0PKLAeYDKS4IY0pMei9EplREiltU-4fsg-VSnV442WLUfM3oWs3ccW48OULbyBUubsqKU8Jk" />
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="font-label-micro text-label-micro uppercase font-bold bg-amber-action/90 backdrop-blur-md text-slate-authority px-2.5 py-1 rounded-lg tracking-wider flex items-center gap-1 shadow-sm border border-amber-200/50">
                                        <span class="material-symbols-outlined text-label-micro">auto_awesome</span>98%
                                        AI Match
                                    </span>
                                </div>
                                <button aria-label="Add to wishlist"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border border-white/90 flex items-center justify-center text-slate-authority hover:text-error hover:bg-white hover:scale-110 transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-body-regular">favorite</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action font-bold tracking-wider">Phones</span>
                                <div
                                    class="flex items-center gap-1 text-label-micro font-label-micro text-on-surface-variant font-medium bg-black/[0.03] px-2 py-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-amber-action text-label-eyebrow"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-slate-authority font-bold">4.8</span>
                                    <span>(194)</span>
                                </div>
                            </div>
                            <h3
                                class="font-title-card text-title-card font-bold text-slate-authority line-clamp-1 group-hover:text-amber-600 transition-colors">
                                Aura P50 Gold Flagship Edition
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                Periscope 5x telephoto, curved OLED, 512GB storage
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-black/[0.05] flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="font-headline-section text-headline-section font-bold text-slate-authority">₹28,000</span>
                                <span
                                    class="font-body-small text-body-small line-through text-on-surface-variant/70">₹34,000</span>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 border border-status-green/20 px-2 py-0.5 rounded-md">18%
                                    OFF</span>
                            </div>
                            <button
                                class="w-full bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:shadow-[0_4px_16px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-sm border border-amber-300/60"
                                type="button">
                                <span class="material-symbols-outlined text-body-regular">shopping_cart</span>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </article>
                    <!-- Item 8: Titanium Gray Flagship Pro OLED -->
                    <article
                        class="group bg-white/80 backdrop-blur-xl border border-white/90 rounded-3xl p-4 flex flex-col justify-between hover:border-amber-action/60 hover:-translate-y-1.5 shadow-[0_12px_36px_rgba(15,23,42,0.06)] hover:shadow-[0_20px_45px_rgba(245,166,35,0.15)] transition-all duration-300 relative overflow-hidden">
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent">
                        </div>
                        <div>
                            <div
                                class="relative w-full aspect-square rounded-2xl overflow-hidden bg-gradient-to-b from-stone-100 to-stone-200/70 mb-3.5 flex items-center justify-center p-2 shadow-inner">
                                <img alt="Titanium Gray Flagship Pro OLED"
                                    class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1WJqsIaif7cmfvLsishfWYq34gojgu0svIdwXok5gEG0Q_W-6tykrS7xH3hsA883j4tF_eEEK0z1sjfo9ceM5LFNRcmojAq3IUyh3Y7kj1ZlCXH8zjOck5QvqjSULDo0Tj0Tl4BVpcLDrOTrDPu8FnX80uvK4431hjXlubO4-lbyE6F9hxAUI9oI4hK5YsY0WZ_UOiICGIY2mTyJHoGXi5sWysIOazYBXA3kEvpiOrqM-kwIYOce3ZkxAc" />
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="font-label-micro text-label-micro uppercase font-bold bg-[#0284C7]/90 backdrop-blur-md text-canvas-ivory px-2.5 py-1 rounded-lg tracking-wider shadow-sm border border-cyan-200/40">Trending</span>
                                </div>
                                <button aria-label="Add to wishlist"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border border-white/90 flex items-center justify-center text-slate-authority hover:text-error hover:bg-white hover:scale-110 transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-body-regular">favorite</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span
                                    class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action font-bold tracking-wider">Phones</span>
                                <div
                                    class="flex items-center gap-1 text-label-micro font-label-micro text-on-surface-variant font-medium bg-black/[0.03] px-2 py-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-amber-action text-label-eyebrow"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-slate-authority font-bold">4.7</span>
                                    <span>(118)</span>
                                </div>
                            </div>
                            <h3
                                class="font-title-card text-title-card font-bold text-slate-authority line-clamp-1 group-hover:text-amber-600 transition-colors">
                                Titanium Gray Flagship Pro OLED
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                120Hz LTPO display, Snapdragon Gen 3, ceramic shield
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-black/[0.05] flex flex-col gap-3">
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="font-headline-section text-headline-section font-bold text-slate-authority">₹32,499</span>
                                <span
                                    class="font-body-small text-body-small line-through text-on-surface-variant/70">₹38,000</span>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 border border-status-green/20 px-2 py-0.5 rounded-md">14%
                                    OFF</span>
                            </div>
                            <button
                                class="w-full bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 hover:shadow-[0_4px_16px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-sm border border-amber-300/60"
                                type="button">
                                <span class="material-symbols-outlined text-body-regular">shopping_cart</span>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </article>
                </div>
                <!-- Pagination with 3D Dimensional Glass Pill Container -->
                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 pb-2 border-t border-black/[0.06]">
                    <span class="font-body-small text-body-small text-on-surface-variant font-medium">
                        Showing <strong class="text-slate-authority font-bold">1–8</strong> of <strong
                            class="text-slate-authority font-bold">328</strong> Products
                    </span>
                    <div
                        class="flex items-center gap-1.5 p-1 bg-white/70 backdrop-blur-md border border-white/80 rounded-2xl shadow-xs">
                        <button
                            class="px-3 py-1.5 rounded-xl border border-transparent text-on-surface-variant/40 font-button-text text-body-small cursor-not-allowed"
                            disabled="" type="button">
                            Previous
                        </button>
                        <button
                            class="w-9 h-9 rounded-xl bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-body-small font-bold flex items-center justify-center shadow-[0_2px_8px_rgba(245,166,35,0.35)] border border-amber-300/50"
                            type="button">
                            1
                        </button>
                        <button
                            class="w-9 h-9 rounded-xl bg-white/80 hover:bg-white border border-white/90 hover:border-amber-action/50 text-slate-authority font-button-text text-body-small font-semibold flex items-center justify-center transition-all hover:scale-105 shadow-2xs"
                            type="button">
                            2
                        </button>
                        <button
                            class="w-9 h-9 rounded-xl bg-white/80 hover:bg-white border border-white/90 hover:border-amber-action/50 text-slate-authority font-button-text text-body-small font-semibold flex items-center justify-center transition-all hover:scale-105 shadow-2xs"
                            type="button">
                            3
                        </button>
                        <button
                            class="w-9 h-9 rounded-xl bg-white/80 hover:bg-white border border-white/90 hover:border-amber-action/50 text-slate-authority font-button-text text-body-small font-semibold flex items-center justify-center transition-all hover:scale-105 shadow-2xs"
                            type="button">
                            4
                        </button>
                        <span class="px-1 text-on-surface-variant font-mono">…</span>
                        <button
                            class="w-9 h-9 rounded-xl bg-white/80 hover:bg-white border border-white/90 hover:border-amber-action/50 text-slate-authority font-button-text text-body-small font-semibold flex items-center justify-center transition-all hover:scale-105 shadow-2xs"
                            type="button">
                            41
                        </button>
                        <button
                            class="px-3 py-1.5 rounded-xl border border-white/80 bg-white/70 hover:bg-white hover:border-amber-action/50 text-slate-authority font-button-text text-body-small font-semibold transition-all shadow-2xs"
                            type="button">
                            Next
                        </button>
                    </div>
                </div>
                <!-- Category Featured Escrow Authority Banner (3D Glass Translucent Dark Theme) -->
                <section
                    class="w-full bg-gradient-to-r from-slate-authority via-[#111A2E] to-[#1E293B] text-canvas-ivory rounded-3xl p-6 sm:p-8 relative overflow-hidden border border-white/20 shadow-[0_16px_40px_rgba(15,23,42,0.2)] backdrop-blur-2xl">
                    <div
                        class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-amber-action/15 blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent">
                    </div>
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="max-w-2xl">
                            <div
                                class="inline-flex items-center gap-2 text-amber-action font-label-eyebrow text-label-eyebrow uppercase font-bold tracking-wider mb-2 bg-white/10 px-3 py-1 rounded-full border border-white/15 backdrop-blur-sm">
                                <span class="material-symbols-outlined text-label-eyebrow">verified_user</span>
                                Device Certification Standard
                            </div>
                            <h3 class="font-headline-section text-headline-section font-bold tracking-tight text-white">
                                Bazaario Electronics Escrow Guarantee
                            </h3>
                            <p class="font-body-regular text-body-regular text-white/75 mt-2 leading-relaxed">
                                Every high-value electronic device is inspected for hardware integrity, battery state,
                                clean IMEI, and serial matching before seller payout is unlocked. Return guarantee
                                remains protected in smart escrow for 7 days.
                            </p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <a class="bg-gradient-to-r from-amber-action to-amber-400 text-slate-authority font-button-text text-button-text px-6 py-3.5 rounded-2xl font-bold hover:shadow-[0_4px_20px_rgba(245,166,35,0.4)] active:scale-[0.98] transition-all shadow-lg border border-amber-200/60 inline-flex items-center gap-2"
                                data-path="escrow-guarantee" href="#">
                                <span>Learn About Escrow</span>
                                <span class="material-symbols-outlined text-body-regular">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </section>
            </div>
            <!-- Docked Floating Pill (3D Glass AI Shopping Assistant) -->
            <aside class="fixed bottom-6 right-6 z-40">
                <button
                    class="group flex items-center gap-3 bg-slate-authority/85 backdrop-blur-2xl border border-white/25 hover:border-amber-action text-canvas-ivory px-4 py-2.5 rounded-full shadow-[0_12px_36px_rgba(15,23,42,0.35)] transition-all hover:scale-105 active:scale-95 relative overflow-hidden"
                    type="button">
                    <div
                        class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/50 to-transparent">
                    </div>
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-action to-amber-300 flex items-center justify-center text-slate-authority shadow-[0_2px_8px_rgba(245,166,35,0.5)] shrink-0">
                        <span class="material-symbols-outlined text-body-regular"
                            style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                    </div>
                    <div class="flex flex-col text-left pr-1">
                        <span
                            class="font-label-micro text-label-micro text-amber-action font-bold uppercase tracking-wider flex items-center gap-1">
                            <span>✦ AI Shopping Agent</span>
                        </span>
                        <span
                            class="font-body-small text-body-small font-medium text-white/90 group-hover:text-amber-action transition-colors">
                            "Help me choose an electronics item by budget"
                        </span>
                    </div>
                    <span
                        class="material-symbols-outlined text-body-small text-white/60 group-hover:translate-x-1 group-hover:text-amber-action transition-all">arrow_forward</span>
                </button>
            </aside>
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