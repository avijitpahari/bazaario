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
            <!-- Interactive Modal Container for Edit Review (Initially Hidden) -->
            <div class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-authority/40 backdrop-blur-sm p-4"
                id="editReviewModal">
                <div class="bg-card-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
                    <div class="flex items-center justify-between pb-4 border-b border-primary-alpha-10">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-action text-xl">rate_review</span>
                            <h3 class="font-title-card text-title-card text-slate-authority font-bold">Edit Verified
                                Feedback</h3>
                        </div>
                        <button
                            class="w-8 h-8 rounded-full hover:bg-surface-container flex items-center justify-center text-on-surface-variant"
                            onclick="closeEditModal()">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                    <div class="mt-4 flex flex-col gap-4">
                        <div>
                            <label
                                class="font-label-micro text-label-micro uppercase text-on-surface-variant tracking-wider block mb-1">Product</label>
                            <p class="font-body-small text-body-small font-semibold text-slate-authority"
                                id="modalProductTitle">Sony WH-1000XM5 Wireless Noise-Cancelling Headphones</p>
                        </div>
                        <div>
                            <label
                                class="font-label-micro text-label-micro uppercase text-on-surface-variant tracking-wider block mb-1">Your
                                Score</label>
                            <div class="flex items-center gap-1 text-amber-action cursor-pointer">
                                <span class="material-symbols-outlined text-2xl"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-2xl"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-2xl"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-2xl"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-2xl"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                <span
                                    class="ml-2 font-label-micro text-label-micro font-bold text-slate-authority bg-surface-container px-2 py-0.5 rounded">5.0
                                    / 5.0</span>
                            </div>
                        </div>
                        <div>
                            <label
                                class="font-label-micro text-label-micro uppercase text-on-surface-variant tracking-wider block mb-1">Headline</label>
                            <input
                                class="w-full bg-surface-container-low rounded-xl px-3.5 py-2.5 font-body-small text-body-small text-slate-authority focus:outline-none focus:ring-1 focus:ring-amber-action"
                                type="text"
                                value="Exceptional ANC and ultra-plush comfort for marathon studio sessions" />
                        </div>
                        <div>
                            <label
                                class="font-label-micro text-label-micro uppercase text-on-surface-variant tracking-wider block mb-1">Detailed
                                Narrative</label>
                            <textarea
                                class="w-full bg-surface-container-low rounded-xl p-3 font-body-small text-body-small text-slate-authority focus:outline-none focus:ring-1 focus:ring-amber-action"
                                rows="3">Excellent sound quality and very comfortable. The noise cancellation creates complete silence even in busy transit, and battery life easily lasts beyond 30 hours.</textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-primary-alpha-10">
                        <button
                            class="px-4 py-2 font-body-small text-body-small font-semibold text-on-surface-variant hover:text-slate-authority rounded-xl"
                            onclick="closeEditModal()">Cancel</button>
                        <button
                            class="bg-amber-action text-slate-authority font-button-text text-body-small font-semibold px-5 py-2 rounded-xl shadow-sm hover:opacity-95 transition-opacity flex items-center gap-1.5"
                            onclick="saveReview()">
                            <span class="material-symbols-outlined text-base">verified</span> Update On-Chain
                        </button>
                    </div>
                </div>
            </div>
            <!-- Central Constrained Container -->
            <div
                class="max-w-container-max mx-auto px-gutter-md w-full pt-8 pb-section-final-bottom flex flex-col gap-8">
                <!-- 1. Top Sub-Navigation & Cryptographic Ledger Rail -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-primary-alpha-10">
                    <div class="flex flex-wrap items-center gap-3">
                        <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-card-white shadow-sm hover:bg-surface-container text-slate-authority font-body-small text-body-small font-medium transition-colors"
                            href="#">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            Back to Account
                        </a>
                        <div class="flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant">
                            <span>Home</span>
                            <span>/</span>
                            <span>Account</span>
                            <span>/</span>
                            <span class="text-slate-authority font-semibold">My Reviews</span>
                        </div>
                    </div>
                    <!-- Ledger Identification Chips -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-status-green animate-pulse"></span>
                            ESCROW VERIFIED BUYER • VAULT #ESC-9041 • UID: BZ-891-IN
                        </div>
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-card-white text-slate-authority font-label-micro text-label-micro font-semibold shadow-sm hover:bg-surface-container cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-sm text-amber-action">lock</span>
                            Vault: ₹60,500.00
                            <span class="material-symbols-outlined text-xs text-on-surface-variant">settings</span>
                        </div>
                    </div>
                </div>
                <!-- 2. Header Section -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="flex flex-col gap-1.5 max-w-2xl">
                        <span
                            class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action tracking-widest font-semibold flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">social_distance</span>
                            Account Management • Feedback &amp; Reputation
                        </span>
                        <h1 class="font-display-hero text-display-hero font-bold tracking-tight text-slate-authority">
                            My Reviews
                        </h1>
                        <p class="font-body-lead text-body-lead text-on-surface-variant">
                            Manage your published ratings, verified purchase feedback, and community trust karma across
                            the marketplace.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="px-4 py-2.5 rounded-xl bg-card-white shadow-sm flex items-center gap-3">
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-action"></div>
                            <div class="flex flex-col">
                                <span class="font-label-micro text-label-micro uppercase text-on-surface-variant">Karma
                                    Level</span>
                                <span class="font-button-text text-body-small font-bold text-slate-authority">98%
                                    Helpful Karma (14 Pub)</span>
                            </div>
                        </div>
                        <button
                            class="bg-amber-action text-slate-authority px-5 py-2.5 rounded-xl font-button-text text-body-small font-semibold shadow-sm hover:opacity-95 transition-opacity flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">edit_square</span>
                            Write a Review
                        </button>
                    </div>
                </div>
                <!-- 3. Rating Summary / Reputation Glass Card (Top Widget) -->
                <div class="relative overflow-hidden rounded-2xl bg-card-white shadow-sm p-6 sm:p-8">
                    <div
                        class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-amber-action/5 blur-2xl pointer-events-none">
                    </div>
                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <!-- Left: Overall Score -->
                        <div
                            class="lg:col-span-4 flex flex-col justify-center border-b lg:border-b-0 lg:border-r border-primary-alpha-10 pb-6 lg:pb-0 lg:pr-6">
                            <div class="flex items-baseline gap-3">
                                <span
                                    class="font-display-hero text-display-hero font-extrabold text-slate-authority">4.8</span>
                                <div class="flex flex-col">
                                    <div class="flex items-center text-amber-action">
                                        <span class="material-symbols-outlined text-lg"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="material-symbols-outlined text-lg"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="material-symbols-outlined text-lg"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="material-symbols-outlined text-lg"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="material-symbols-outlined text-lg"
                                            style="font-variation-settings: 'FILL' 1;">star_half</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant font-medium">14
                                        Verified Reviews</span>
                                </div>
                            </div>
                            <div
                                class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface-container w-fit">
                                <span class="material-symbols-outlined text-amber-action text-base">military_tech</span>
                                <span class="font-label-micro text-label-micro text-slate-authority font-bold">
                                    Karma Score: 920 Pts <span class="text-status-green font-semibold">(Top 5% Reviewer
                                        Badge)</span>
                                </span>
                            </div>
                            <p class="font-body-small text-body-small text-on-surface-variant mt-2">
                                Your assessments have saved community members ₹1,42,000 in misaligned purchases.
                            </p>
                        </div>
                        <!-- Middle: 5-Star Breakdown Bar Chart -->
                        <div
                            class="lg:col-span-5 flex flex-col gap-2.5 justify-center border-b lg:border-b-0 lg:border-r border-primary-alpha-10 pb-6 lg:pb-0 lg:pr-6">
                            <!-- 5 Stars -->
                            <div class="flex items-center gap-3 font-label-micro text-label-micro">
                                <span class="w-10 text-slate-authority font-medium flex items-center gap-0.5">5 <span
                                        class="material-symbols-outlined text-xs text-amber-action"
                                        style="font-variation-settings: 'FILL' 1;">star</span></span>
                                <div class="flex-1 h-2 bg-surface-container rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-action rounded-full" style="width: 78.5%;"></div>
                                </div>
                                <span class="w-6 text-right text-on-surface-variant font-semibold">11</span>
                            </div>
                            <!-- 4 Stars -->
                            <div class="flex items-center gap-3 font-label-micro text-label-micro">
                                <span class="w-10 text-slate-authority font-medium flex items-center gap-0.5">4 <span
                                        class="material-symbols-outlined text-xs text-amber-action"
                                        style="font-variation-settings: 'FILL' 1;">star</span></span>
                                <div class="flex-1 h-2 bg-surface-container rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-action/70 rounded-full" style="width: 14.2%;"></div>
                                </div>
                                <span class="w-6 text-right text-on-surface-variant font-semibold">2</span>
                            </div>
                            <!-- 3 Stars -->
                            <div class="flex items-center gap-3 font-label-micro text-label-micro">
                                <span class="w-10 text-slate-authority font-medium flex items-center gap-0.5">3 <span
                                        class="material-symbols-outlined text-xs text-amber-action"
                                        style="font-variation-settings: 'FILL' 1;">star</span></span>
                                <div class="flex-1 h-2 bg-surface-container rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-action/40 rounded-full" style="width: 7.1%;"></div>
                                </div>
                                <span class="w-6 text-right text-on-surface-variant font-semibold">1</span>
                            </div>
                            <!-- 2 Stars -->
                            <div class="flex items-center gap-3 font-label-micro text-label-micro">
                                <span class="w-10 text-slate-authority font-medium flex items-center gap-0.5">2 <span
                                        class="material-symbols-outlined text-xs text-on-surface-variant">star</span></span>
                                <div class="flex-1 h-2 bg-surface-container rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-action/20 rounded-full" style="width: 0%;"></div>
                                </div>
                                <span class="w-6 text-right text-on-surface-variant">0</span>
                            </div>
                            <!-- 1 Star -->
                            <div class="flex items-center gap-3 font-label-micro text-label-micro">
                                <span class="w-10 text-slate-authority font-medium flex items-center gap-0.5">1 <span
                                        class="material-symbols-outlined text-xs text-on-surface-variant">star</span></span>
                                <div class="flex-1 h-2 bg-surface-container rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-action/20 rounded-full" style="width: 0%;"></div>
                                </div>
                                <span class="w-6 text-right text-on-surface-variant">0</span>
                            </div>
                        </div>
                        <!-- Right: Interactive Quick Filters -->
                        <div class="lg:col-span-3 flex flex-col gap-2">
                            <span
                                class="font-label-micro text-label-micro uppercase text-on-surface-variant font-semibold">View
                                Segments</span>
                            <div class="flex flex-wrap lg:flex-col gap-2">
                                <button
                                    class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-body-small text-body-small font-semibold transition-all">
                                    <span>All Reviews</span>
                                    <span
                                        class="font-label-micro text-label-micro bg-card-white/20 px-2 py-0.5 rounded-full">14</span>
                                </button>
                                <button
                                    class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-medium transition-all">
                                    <span>With Media &amp; Photos</span>
                                    <span
                                        class="font-label-micro text-label-micro bg-card-white px-2 py-0.5 rounded-full text-on-surface-variant">6</span>
                                </button>
                                <button
                                    class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-medium transition-all">
                                    <span>Awaiting Response</span>
                                    <span
                                        class="font-label-micro text-label-micro bg-amber-action/20 text-slate-authority font-bold px-2 py-0.5 rounded-full">2</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 4. Items Awaiting Your Review (Urgent Discovery Banner) -->
                <div
                    class="p-5 sm:p-6 rounded-2xl bg-amber-action/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div
                            class="w-12 h-12 rounded-xl bg-card-white flex items-center justify-center text-amber-action shadow-sm shrink-0">
                            <span class="material-symbols-outlined text-2xl">pending_actions</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-title-card text-title-card font-bold text-slate-authority">Pending
                                    Feedback Deliveries</span>
                                <span
                                    class="font-label-micro text-label-micro bg-amber-action text-slate-authority px-2 py-0.5 rounded font-bold">+50
                                    Karma Each</span>
                            </div>
                            <p class="font-body-small text-body-small text-on-surface-variant">
                                You have 2 delivered marketplace items awaiting authenticity endorsement.
                            </p>
                        </div>
                    </div>
                    <!-- Mini Carousel of pending items -->
                    <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
                        <div class="flex items-center gap-3 bg-card-white px-3 py-2 rounded-xl shadow-sm shrink-0">
                            <img class="w-10 h-10 rounded-lg object-cover"
                                data-alt="Handmade full-grain tan leather artisan desk mat resting on clean birch desk beside sleek pens in warm morning sunlight with soft organic shadows"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCtQzaYe5kxUoOis0lOnQKtEzkmhVtSLmsEgjJru0g-rNGFo5aXv9kbCttT-dLjts0tX6zoo5Py0PjAkECOQyJNXq2xw0aWS7G-qoX768QNf8wXwNgYhv1hJZxvHXkB6xT3ICmgoxBiF7iAPS35R5ztvEGZCd5anO_j5AZE95g-Bh_EIuNQbHC21csbaTK-KiXkjjw37IokZIoYeGn3Qj-Q4PIW6h4HH67Spt6uWeShLr3ykPPaVScE" />
                            <div class="flex flex-col">
                                <span class="font-body-small text-body-small font-semibold text-slate-authority">Artisan
                                    Leather Desk Mat</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant">Delivered 3d
                                    ago</span>
                            </div>
                            <button
                                class="bg-amber-action text-slate-authority px-3 py-1.5 rounded-lg font-label-micro text-label-micro font-bold hover:opacity-95 transition-opacity ml-2">Rate
                                Now</button>
                        </div>
                        <div class="flex items-center gap-3 bg-card-white px-3 py-2 rounded-xl shadow-sm shrink-0">
                            <img class="w-10 h-10 rounded-lg object-cover"
                                data-alt="Brushed solid brass mechanical fountain pen with intricate engraving lying on handwoven linen paper in warm cinematic desk lighting"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDpE8hlcpehe8Kz8DGsxlVidkpAmHFgkL3lY7zrU1gZEK5OPzBW7GWx7yS7cqP1-pWjwrtacjLrnljlq9F0Q10_WpyrDMXWTZBIJ_ireEhfcVypyHgJy6sqqydCIz8nqLgTfPkYmKfkKESszZGi__3oOU1bZypUjt4CPKVDsAD7LhvVLHA2gMVUBstq_CvvErVTRSnKM3TpYnHgUMNA0bi9BI3nE8iVjWEt7lrYiku2QyMu3nq8fqlI" />
                            <div class="flex flex-col">
                                <span class="font-body-small text-body-small font-semibold text-slate-authority">Solid
                                    Brass Rollerball</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant">Delivered
                                    yesterday</span>
                            </div>
                            <button
                                class="bg-amber-action text-slate-authority px-3 py-1.5 rounded-lg font-label-micro text-label-micro font-bold hover:opacity-95 transition-opacity ml-2">Rate
                                Now</button>
                        </div>
                    </div>
                </div>
                <!-- 5. Reviews Feed Header & Sorting -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <h2 class="font-headline-section text-headline-section font-bold text-slate-authority">Published
                            Feedback</h2>
                        <span
                            class="font-label-micro text-label-micro text-on-surface-variant bg-surface-container px-2.5 py-1 rounded-full font-semibold">14
                            Records</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span
                            class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider">Sort:</span>
                        <div
                            class="bg-card-white rounded-xl px-3 py-1.5 shadow-sm flex items-center gap-1.5 text-slate-authority font-body-small text-body-small font-medium">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            <span>Most Recent First</span>
                            <span class="material-symbols-outlined text-sm text-on-surface-variant">expand_more</span>
                        </div>
                    </div>
                </div>
                <!-- 6. Feed Cards -->
                <div class="flex flex-col gap-6">
                    <!-- REVIEW CARD 1: Sony WH-1000XM5 (Detailed with Images & Seller Response) -->
                    <article
                        class="bg-card-white rounded-2xl p-6 sm:p-7 shadow-sm flex flex-col gap-5 hover:shadow-md transition-shadow">
                        <!-- Card Top Bar: Product Context & Verification -->
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-primary-alpha-10">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-surface-container shrink-0">
                                    <img class="w-full h-full object-cover"
                                        data-alt="Sony WH-1000XM5 matte black wireless noise-cancelling headphones resting on an organic curved walnut wood stand on a rich mahogany desk, illuminated by warm sunlight streaming through a window, fine acoustic audio craft vibe"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuC7dlceI0GBp4TE39FQSW9uztbR0YrCpQpiXg56MKLCthgnHnArwlGBsR4IraDchg83ohy0TYKS9sduT7p40ANPy4SnjCb9RxAP130n7_qyYrVGWd0mCwhbjJQjXkOhmwRUuottrgH6U7mVL22rvzhB7W_BB7rt7UGECkg9GJDDBis9cjaQdGLfvH1uyf5qmH0_EFwbcQ-Xrkk9B2jSjlueUiXPUKfDO0_wq7Hmb7eU1Z1_vCer-vta" />
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3
                                            class="font-title-card text-title-card font-bold text-slate-authority hover:text-amber-action cursor-pointer transition-colors">
                                            Sony WH-1000XM5 Wireless Noise-Cancelling Headphones
                                        </h3>
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-semibold">
                                            <span class="material-symbols-outlined text-xs">verified</span> Verified
                                            Purchase
                                        </span>
                                    </div>
                                    <div
                                        class="flex flex-wrap items-center gap-2 mt-1 text-on-surface-variant font-label-micro text-label-micro">
                                        <span>Vendor: <strong class="text-slate-authority">AudioCraft
                                                Official</strong></span>
                                        <span>•</span>
                                        <span>Order #BZ-2026-00128</span>
                                        <span>•</span>
                                        <span>Settled in Escrow</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Star Score Pill -->
                            <div
                                class="flex items-center gap-2 bg-amber-action/10 px-3 py-1.5 rounded-xl self-start sm:self-center">
                                <div class="flex items-center text-amber-action">
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-slate-authority">5.0</span>
                            </div>
                        </div>
                        <!-- Headline & Review Content -->
                        <div class="flex flex-col gap-2">
                            <h4 class="font-title-card text-title-card font-bold text-slate-authority">
                                Exceptional ANC and ultra-plush comfort for marathon studio sessions
                            </h4>
                            <p class="font-body-regular text-body-regular text-slate-authority leading-relaxed">
                                Excellent sound quality and very comfortable. The noise cancellation creates complete
                                silence even in busy transit, and battery life easily lasts beyond 30 hours. The custom
                                EQ in the app makes acoustic tracks sparkle. The physical carry pouch is also refined
                                and compact.
                            </p>
                        </div>
                        <!-- Verified User Uploaded Photography -->
                        <div class="flex flex-col gap-2">
                            <span
                                class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant font-semibold">Buyer
                                Photo Attachments (2)</span>
                            <div class="flex items-center gap-3">
                                <div
                                    class="relative w-24 h-24 rounded-xl overflow-hidden group cursor-pointer shadow-sm">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                        data-alt="Close up view of Sony matte black headphone earcups resting on a curved walnut wood headphone stand with soft golden desk lamp reflection and leather notebook nearby"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuC3wc5x3fvnetwvpdVztb_O9zhVgjl5suvwi2gZ3cuKAPouFICUBLuTADXgv11sVdYPFuTRx5ggya2XkuN2XY0z0AHI0EoSgyjxfaT_DIHwuMoE4X1CgvtUdLPQSBl46mqniaaltAhcKl9xt-6-6ehtiOeskw67unSZwfj3ohp44x77NQqkzlIFh4dhrJCbNCTKzmRzf5S4hJNhcR3X5SuBhGATEoZ3l-IorzsMKQwHXf7xbzA_JC1G" />
                                    <div
                                        class="absolute inset-0 bg-slate-authority/10 group-hover:bg-transparent transition-colors">
                                    </div>
                                </div>
                                <div
                                    class="relative w-24 h-24 rounded-xl overflow-hidden group cursor-pointer shadow-sm">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                        data-alt="Architectural desk perspective showing walnut headphone stand holding black headphones next to a warm aesthetic coffee mug and mechanical keyboard in golden hour light"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKze9fYalwkyYEddor-LKOz6b4m84eHkNeUrKbAfb-t3HmzZpz8-GBEjVTq6GnxVhjKtDIRIss0rzLUN0HAOmpq7JfM2IPmvrpocDOxQdVuBaSlhrO67ev3dxYhjKBzirAQ_kox3fHn-75N0a2kQuTojHXPorgJFyqLcYZIdVern_x-ft-9ohchsAA40WPK5hjqvfSlK4j9q7EfcxMyr-jLasv_li2FVe8HeMH4jNpijui52j-V1DR" />
                                    <div
                                        class="absolute inset-0 bg-slate-authority/10 group-hover:bg-transparent transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Seller Official Reply Callout -->
                        <div class="p-4 rounded-xl bg-surface-container-low flex flex-col gap-1.5">
                            <div class="flex items-center gap-2 font-label-micro text-label-micro">
                                <span class="material-symbols-outlined text-base text-amber-action">storefront</span>
                                <span class="font-bold text-slate-authority">Seller Response: AudioCraft Official</span>
                                <span class="text-on-surface-variant">• 16 Sep 2026</span>
                            </div>
                            <p class="font-body-small text-body-small text-on-surface-variant italic">
                                "Thank you Avijit! We are thrilled the noise cancellation and EQ profile serve your
                                acoustic marathon sessions well. Your warranty has been automatically logged into the
                                Escrow registry."
                            </p>
                        </div>
                        <!-- Footer Actions & Metadata -->
                        <div
                            class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-primary-alpha-10 font-label-micro text-label-micro text-on-surface-variant">
                            <div class="flex items-center gap-3">
                                <span>Reviewed on 15 Sep 2026</span>
                                <span>•</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-xs text-status-green">location_on</span>
                                    Contai, WB</span>
                                <span>•</span>
                                <span class="text-slate-authority font-semibold">24 people found this helpful</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    class="px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-semibold flex items-center gap-1 transition-colors"
                                    onclick="openEditModal('Sony WH-1000XM5 Wireless Headphones')">
                                    <span class="material-symbols-outlined text-sm">edit</span> Edit Review
                                </button>
                                <button
                                    class="p-1.5 rounded-lg hover:bg-surface-container text-on-surface-variant transition-colors"
                                    title="Share link">
                                    <span class="material-symbols-outlined text-base">share</span>
                                </button>
                                <button class="p-1.5 rounded-lg hover:bg-error-container text-error transition-colors"
                                    title="Archive feedback">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- REVIEW CARD 2: Retro Cream Mechanical Keyboard -->
                    <article
                        class="bg-card-white rounded-2xl p-6 sm:p-7 shadow-sm flex flex-col gap-5 hover:shadow-md transition-shadow">
                        <!-- Card Top Bar -->
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-primary-alpha-10">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-surface-container shrink-0">
                                    <img class="w-full h-full object-cover"
                                        data-alt="Retro cream custom wireless mechanical keyboard with vintage beige dye-sub keycaps and coiled aviator cable resting on light oak wood desk next to coffee mug with glowing warm underglow key backlights"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCoSl6m0cmLfTT3XaJtxPMWx25QcFDs8XuP4vmp7HXVt_gGaW6FNoEalf1hlQYmjQsTyJM4obPrlFKZESLiPZcjnzl8I7-0DW3RzvbJxhmbZmGWfTMWL7zheburT4Bo5mP1oKeVFN0eGpCvSiohkXeGixbqQyG1tNJLhqeCYv2kZVepHvAxhiUP7-gISGoQLIqCk-YmE2N4HqdEX8G4Qnz5EqNTH5JojiT-CaUdG18yYWNE3svfsFiK" />
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3
                                            class="font-title-card text-title-card font-bold text-slate-authority hover:text-amber-action cursor-pointer transition-colors">
                                            Retro Cream Wireless Mechanical Keyboard (Gateron Yellow)
                                        </h3>
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-semibold">
                                            <span class="material-symbols-outlined text-xs">verified</span> Verified
                                            Purchase
                                        </span>
                                    </div>
                                    <div
                                        class="flex flex-wrap items-center gap-2 mt-1 text-on-surface-variant font-label-micro text-label-micro">
                                        <span>Vendor: <strong class="text-slate-authority">DeskCraft
                                                Studio</strong></span>
                                        <span>•</span>
                                        <span>Order #BZ-2026-00119</span>
                                        <span>•</span>
                                        <span>Escrow Cleared</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Star Score Pill (4 Stars) -->
                            <div
                                class="flex items-center gap-2 bg-amber-action/10 px-3 py-1.5 rounded-xl self-start sm:self-center">
                                <div class="flex items-center text-amber-action">
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span
                                        class="material-symbols-outlined text-base text-on-surface-variant/40">star</span>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-slate-authority">4.0</span>
                            </div>
                        </div>
                        <!-- Headline & Review Content -->
                        <div class="flex flex-col gap-2">
                            <h4 class="font-title-card text-title-card font-bold text-slate-authority">
                                Solid tactile thock and nostalgic vintage aesthetics
                            </h4>
                            <p class="font-body-regular text-body-regular text-slate-authority leading-relaxed">
                                Good keyboard for the price. The coiled aviator cable and amber backlighting match my
                                desk setup perfectly. Keycaps have a nice matte texture, though bluetooth switching
                                takes about 2 seconds to reconnect when toggling between MacBook and iPad. Overall very
                                satisfied with the typing feel.
                            </p>
                        </div>
                        <!-- Footer Actions & Metadata -->
                        <div
                            class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-primary-alpha-10 font-label-micro text-label-micro text-on-surface-variant">
                            <div class="flex items-center gap-3">
                                <span>Reviewed on 02 Sep 2026</span>
                                <span>•</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-xs text-status-green">location_on</span>
                                    Contai, WB</span>
                                <span>•</span>
                                <span class="text-slate-authority font-semibold">9 people found this helpful</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    class="px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-semibold flex items-center gap-1 transition-colors"
                                    onclick="openEditModal('Retro Cream Wireless Mechanical Keyboard')">
                                    <span class="material-symbols-outlined text-sm">edit</span> Edit Review
                                </button>
                                <a class="px-3 py-1.5 rounded-lg bg-card-white hover:bg-surface-container text-on-surface-variant hover:text-slate-authority font-body-small text-body-small font-medium flex items-center gap-1 transition-colors"
                                    href="#">
                                    <span>View Product</span>
                                    <span class="material-symbols-outlined text-sm">arrow_outward</span>
                                </a>
                            </div>
                        </div>
                    </article>
                    <!-- REVIEW CARD 3: Apple Watch Ultra 2 -->
                    <article
                        class="bg-card-white rounded-2xl p-6 sm:p-7 shadow-sm flex flex-col gap-5 hover:shadow-md transition-shadow">
                        <!-- Card Top Bar -->
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-primary-alpha-10">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-surface-container shrink-0">
                                    <img class="w-full h-full object-cover"
                                        data-alt="Apple Watch Ultra 2 titanium rugged smartwatch with high contrast modular screen and orange ocean strap resting on coastal wet sand and smooth pebbles in crisp outdoor daylight"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQ40ARKwTxW4yI5lhFFja_kk1SEUVUiHuN8a51F9u0VH1U8jQJNnNqA9kpE4ylLGSjrx8LsjopU4DwbfLi7RLbVZ98JkkXxQ94QY1_mZcBTYwU3el9UI-mNA60R6OB0cYOLDPX-xS-mHcau-7L4Arv-sGFgu-3_6SwpTvYfnFhwyJt5NxFPoUkJ3O2-U07ShRgOi3PRPMnDEuOp7V0gJHvf261Ctigpa56N9oCHSTCCqsJNy6gzCcI" />
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3
                                            class="font-title-card text-title-card font-bold text-slate-authority hover:text-amber-action cursor-pointer transition-colors">
                                            Apple Watch Ultra 2 (Titanium / Orange Ocean Band)
                                        </h3>
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-semibold">
                                            <span class="material-symbols-outlined text-xs">verified</span> Verified
                                            Purchase
                                        </span>
                                    </div>
                                    <div
                                        class="flex flex-wrap items-center gap-2 mt-1 text-on-surface-variant font-label-micro text-label-micro">
                                        <span>Vendor: <strong class="text-slate-authority">Apex Cellular
                                                Collective</strong></span>
                                        <span>•</span>
                                        <span>Order #BZ-2026-00124</span>
                                        <span>•</span>
                                        <span>Verified Delivery</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Star Score Pill -->
                            <div
                                class="flex items-center gap-2 bg-amber-action/10 px-3 py-1.5 rounded-xl self-start sm:self-center">
                                <div class="flex items-center text-amber-action">
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-base"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                </div>
                                <span
                                    class="font-label-micro text-label-micro font-bold text-slate-authority">5.0</span>
                            </div>
                        </div>
                        <!-- Headline & Review Content -->
                        <div class="flex flex-col gap-2">
                            <h4 class="font-title-card text-title-card font-bold text-slate-authority">
                                Indestructible titanium build and flawless coastal navigation tracking
                            </h4>
                            <p class="font-body-regular text-body-regular text-slate-authority leading-relaxed">
                                Flawless battery stamina and the titanium case feels indestructible. The dual-frequency
                                high-contrast GPS is deadly accurate for coastal cycling and kayaking in Purba
                                Medinipur. Screen visibility under direct tropical noon sun is unmatched.
                            </p>
                        </div>
                        <!-- Footer Actions & Metadata -->
                        <div
                            class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-primary-alpha-10 font-label-micro text-label-micro text-on-surface-variant">
                            <div class="flex items-center gap-3">
                                <span>Reviewed on 28 Aug 2026</span>
                                <span>•</span>
                                <span class="text-slate-authority font-semibold">38 people found this helpful</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    class="px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-semibold flex items-center gap-1 transition-colors"
                                    onclick="openEditModal('Apple Watch Ultra 2')">
                                    <span class="material-symbols-outlined text-sm">edit</span> Edit Review
                                </button>
                                <a class="px-3 py-1.5 rounded-lg bg-card-white hover:bg-surface-container text-on-surface-variant hover:text-slate-authority font-body-small text-body-small font-medium flex items-center gap-1 transition-colors"
                                    href="#">
                                    <span>View Product</span>
                                    <span class="material-symbols-outlined text-sm">arrow_outward</span>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
                <!-- 7. Vault & Escrow Security Authority Banner -->
                <div
                    class="rounded-2xl bg-slate-authority p-8 sm:p-10 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden shadow-lg">
                    <div
                        class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-amber-action/10 blur-3xl pointer-events-none">
                    </div>
                    <div class="relative z-10 flex flex-col gap-2 max-w-2xl">
                        <span
                            class="font-label-micro text-label-micro uppercase tracking-widest text-amber-action font-semibold flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">security</span> Verified Review Integrity
                            Protocol
                        </span>
                        <h3 class="font-headline-section text-headline-section font-bold text-canvas-ivory">
                            Reviews are cryptographically verified by Bazaario Vault
                        </h3>
                        <p class="font-body-regular text-body-regular text-canvas-ivory/80 leading-relaxed">
                            Every review on this profile is tied to Vault #ESC-9041 and smart-contract order
                            fulfillment. Astroturfing, fake accounts, and paid endorsements are strictly impossible
                            under our protocol.
                        </p>
                    </div>
                    <div class="relative z-10 flex flex-col items-start sm:items-end gap-3 shrink-0">
                        <div
                            class="px-4 py-2 rounded-xl bg-card-white/10 backdrop-blur-sm flex items-center gap-2 text-canvas-ivory font-label-micro text-label-micro">
                            <span class="w-2 h-2 rounded-full bg-status-green"></span>
                            Vault Checksum: #0x92b4...88a1
                        </div>
                        <button
                            class="bg-amber-action text-slate-authority px-6 py-3 rounded-xl font-button-text text-button-text font-semibold shadow-sm hover:opacity-95 transition-all active:scale-[0.99] flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">download</span>
                            Export Reputation Audit
                        </button>
                    </div>
                </div>
            </div>
            <!-- 8. Floating AI Assistant Action Dock (Bottom Right) -->
            <aside class="fixed bottom-6 right-6 z-40">
                <button
                    class="flex items-center gap-2.5 px-4 py-3 rounded-full bg-slate-authority text-canvas-ivory shadow-xl hover:shadow-2xl hover:bg-slate-900 transition-all border border-card-white/10 group cursor-pointer">
                    <span
                        class="material-symbols-outlined text-amber-action text-xl animate-spin-slow">auto_awesome</span>
                    <span class="font-body-small text-body-small font-semibold pr-1">
                        Ask Bazaario AI: <span class="text-canvas-ivory/70 font-normal">"Draft a review for my latest
                            order"</span>
                    </span>
                    <span
                        class="material-symbols-outlined text-sm text-canvas-ivory/50 group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </button>
            </aside>
            <!-- Script for Modal Handling & Micro-Interactions -->
            <script>
                function openEditModal(productName) {
                    document.getElementById('modalProductTitle').innerText = productName;
                    document.getElementById('editReviewModal').classList.remove('hidden');
                }
                function closeEditModal() {
                    document.getElementById('editReviewModal').classList.add('hidden');
                }
                function saveReview() {
                    closeEditModal();
                    // Subtle visual feedback confirmation
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-24 right-8 z-50 bg-slate-authority text-canvas-ivory px-4 py-3 rounded-xl shadow-xl flex items-center gap-2 text-body-small font-medium border border-status-green/30';
                    toast.innerHTML = '<span class="material-symbols-outlined text-status-green text-sm">check_circle</span> Feedback updated and verified on Bazaario ledger.';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3500);
                }
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