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
            <div class="w-full max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">
                <!-- Sub-navigation & Escrow Telemetry Bar -->
                <div class="flex flex-wrap items-center justify-between gap-3 text-on-surface">
                    <div class="flex items-center gap-2">
                        <a class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-card-white shadow-sm hover:bg-surface-container transition-colors text-slate-authority font-body-small text-body-small font-medium"
                            href="#">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            <span>Back to Account</span>
                        </a>
                        <span class="text-on-surface-variant/40 text-body-small">/</span>
                        <span class="font-body-small text-body-small text-on-surface-variant">Account</span>
                        <span class="text-on-surface-variant/40 text-body-small">/</span>
                        <span class="font-body-small text-body-small font-semibold text-slate-authority">Wishlist</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-card-white shadow-sm font-label-micro text-label-micro text-on-surface-variant">
                            <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                            <span class="font-bold text-slate-authority">ESCROW VERIFIED BUYER</span>
                            <span class="text-on-surface-variant/40">•</span>
                            <span>VAULT #ESC-9041</span>
                            <span class="text-on-surface-variant/40">•</span>
                            <span>UID: BZ-891-IN</span>
                        </div>
                        <div
                            class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-card-white shadow-sm font-body-small text-body-small text-slate-authority font-medium">
                            <span class="material-symbols-outlined text-[18px] text-amber-action">lock</span>
                            <span>Vault: <span class="font-semibold text-slate-authority">₹60,500.00</span></span>
                            <a class="text-amber-action hover:underline font-label-micro text-label-micro ml-1"
                                href="#">Settings</a>
                        </div>
                    </div>
                </div>
                <!-- Header Block -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pt-2">
                    <div class="flex flex-col gap-1">
                        <span
                            class="font-label-eyebrow text-label-eyebrow text-amber-action font-bold uppercase tracking-wider">
                            Account Management • Curated Saved Vault
                        </span>
                        <h1
                            class="font-display-hero text-display-hero text-slate-authority font-bold tracking-tight leading-none">
                            My Wishlist
                        </h1>
                        <p
                            class="font-body-regular text-body-regular text-on-surface-variant flex items-center gap-2 mt-1">
                            <span>12 products saved for later</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-action"></span>
                            <span class="text-amber-action font-medium">3 currently on time-sensitive price drop</span>
                        </p>
                    </div>
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-card-white hover:bg-surface-container text-slate-authority font-button-text text-body-small font-semibold shadow-sm transition-all"
                            id="move-all-btn" type="button">
                            <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                            <span>Move All to Cart</span>
                        </button>
                        <button
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-card-white hover:bg-surface-container text-slate-authority font-button-text text-body-small font-semibold shadow-sm transition-all"
                            type="button">
                            <span class="material-symbols-outlined text-[20px]">share</span>
                            <span>Share Wishlist</span>
                        </button>
                    </div>
                </div>
                <!-- Segmented Tabs & Sort Bar -->
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-2 bg-card-white rounded-xl shadow-sm">
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none"
                        id="wishlist-filter-tabs">
                        <button
                            class="px-4 py-2 rounded-lg font-body-small text-body-small font-semibold bg-slate-authority text-canvas-ivory transition-colors whitespace-nowrap"
                            data-filter="all">
                            All Saved (12)
                        </button>
                        <button
                            class="px-4 py-2 rounded-lg font-body-small text-body-small font-medium text-on-surface-variant hover:text-slate-authority hover:bg-surface-container transition-colors whitespace-nowrap flex items-center gap-1.5"
                            data-filter="pricedrop">
                            <span class="w-2 h-2 rounded-full bg-amber-action"></span>
                            Price Drops (3)
                        </button>
                        <button
                            class="px-4 py-2 rounded-lg font-body-small text-body-small font-medium text-on-surface-variant hover:text-slate-authority hover:bg-surface-container transition-colors whitespace-nowrap"
                            data-filter="electronics">
                            Electronics (6)
                        </button>
                        <button
                            class="px-4 py-2 rounded-lg font-body-small text-body-small font-medium text-on-surface-variant hover:text-slate-authority hover:bg-surface-container transition-colors whitespace-nowrap"
                            data-filter="crafts">
                            Artisan Crafts (4)
                        </button>
                        <button
                            class="px-4 py-2 rounded-lg font-body-small text-body-small font-medium text-on-surface-variant hover:text-slate-authority hover:bg-surface-container transition-colors whitespace-nowrap"
                            data-filter="auctions">
                            Live Auctions (2)
                        </button>
                    </div>
                    <div class="flex items-center gap-3 justify-between sm:justify-end px-2 sm:px-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">sort</span>
                            <label
                                class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider"
                                for="sort-select">Sort by:</label>
                            <select
                                class="bg-surface-container rounded-lg px-3 py-1.5 font-body-small text-body-small font-semibold text-slate-authority focus:outline-none cursor-pointer"
                                id="sort-select">
                                <option value="recent">Recently Added</option>
                                <option value="price-asc">Price: Low to High</option>
                                <option value="price-desc">Price: High to Low</option>
                                <option value="discount">Highest Savings</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Product Grid (4 Columns) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" id="wishlist-grid">
                    <!-- Product 1: Sony Headphones -->
                    <article
                        class="group flex flex-col bg-card-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md"
                        data-category="electronics" data-pricedrop="true">
                        <div class="relative w-full aspect-square bg-surface-container overflow-hidden">
                            <img alt="Sony WH-1000XM5 Wireless Headphones"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1ULHYfZ58DWHb3ovmkHCOx30d2y2KNFJNjSaNeyIekug2Nn45SSGMa1s8vyF89_u8JpyK5DcURAFa2FtF4vEIv2pVZGC2MzAZq1rRjLYQKE-t52YAMdsIJW5prgjmSFsExmX2aiDaBf_jZkASVFGXLFGDmCoTL9fUxPtPDX9hogJvtf9H4TP8MNd42K8QLAuao7kkBFZ2byKJcoFdTnhVs9s7R2FX26LF17V1ZCh9hOhUu4lmibOG9-0g" />
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full font-label-micro text-label-micro font-bold bg-amber-action text-slate-authority shadow-sm">
                                    PRICE DROP -24%
                                </span>
                            </div>
                            <button aria-label="Remove item"
                                class="wishlist-remove-btn absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-md text-error flex items-center justify-center shadow-sm hover:scale-110 active:scale-95 transition-all"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </div>
                        <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold uppercase tracking-wider text-on-surface-variant">AUDIO
                                        • STUDIO</span>
                                    <div
                                        class="flex items-center gap-1 font-label-micro text-label-micro text-slate-authority font-semibold">
                                        <span class="material-symbols-outlined text-[16px] text-amber-action"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>4.8</span>
                                        <span class="text-on-surface-variant font-normal">(1,420)</span>
                                    </div>
                                </div>
                                <h3
                                    class="font-title-card text-title-card font-bold text-slate-authority group-hover:text-amber-action transition-colors line-clamp-1">
                                    Sony WH-1000XM5 Headphones
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1">
                                    Solid walnut stand included • Noise canceling
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 pt-2 border-t border-primary-alpha-10">
                                <div class="flex items-baseline justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹2,499</span>
                                        <span
                                            class="font-body-small text-body-small text-on-surface-variant line-through">₹3,299</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 px-1.5 py-0.5 rounded">Save
                                        ₹800</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-status-green font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                    <span>In Stock • Dispatches today</span>
                                </div>
                                <button
                                    class="add-cart-btn w-full mt-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- Product 2: Apple Watch Ultra 2 -->
                    <article
                        class="group flex flex-col bg-card-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md"
                        data-category="electronics" data-pricedrop="true">
                        <div class="relative w-full aspect-square bg-surface-container overflow-hidden">
                            <img alt="Apple Watch Ultra 2 Titanium"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1WCV79P_WvGQN5KQJBCsoSb-BB7bk_bI9raCF39grO__0osmkm6wz8yOcsJDmbNAtpsoOLotAmSTm8MaUvtbCzz2kFK6vXUGpeo78gNOeqO1yyDPtdaIb533Ya-QTC0PN7pTYfSKxV6V-u3H9sok9Nj0YVvjmpsuzEplsjAWJRMufP453b17PWleiI6RG79hsIYDKekSa9mJ0sz3bbZnIjMwEebK0zG_961mM9Bh1cY8T9TmdK9Ce5EeQc" />
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full font-label-micro text-label-micro font-bold bg-amber-action text-slate-authority shadow-sm">
                                    PRICE DROP -20%
                                </span>
                            </div>
                            <button aria-label="Remove item"
                                class="wishlist-remove-btn absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-md text-error flex items-center justify-center shadow-sm hover:scale-110 active:scale-95 transition-all"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </div>
                        <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold uppercase tracking-wider text-on-surface-variant">WEARABLE
                                        • TITANIUM</span>
                                    <div
                                        class="flex items-center gap-1 font-label-micro text-label-micro text-slate-authority font-semibold">
                                        <span class="material-symbols-outlined text-[16px] text-amber-action"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>4.7</span>
                                        <span class="text-on-surface-variant font-normal">(892)</span>
                                    </div>
                                </div>
                                <h3
                                    class="font-title-card text-title-card font-bold text-slate-authority group-hover:text-amber-action transition-colors line-clamp-1">
                                    Apple Watch Ultra 2 Titanium
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1">
                                    Ocean orange silicone band • 49mm case
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 pt-2 border-t border-primary-alpha-10">
                                <div class="flex items-baseline justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹3,999</span>
                                        <span
                                            class="font-body-small text-body-small text-on-surface-variant line-through">₹4,999</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 px-1.5 py-0.5 rounded">Save
                                        ₹1,000</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-status-green font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                    <span>In Stock • Ready to dispatch</span>
                                </div>
                                <button
                                    class="add-cart-btn w-full mt-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- Product 3: Mechanical Keyboard -->
                    <article
                        class="group flex flex-col bg-card-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md"
                        data-category="electronics" data-pricedrop="true">
                        <div class="relative w-full aspect-square bg-surface-container overflow-hidden">
                            <img alt="Retro Cream Mechanical Keyboard"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1V962eUfdX3W9GnnupcSTLq4Tj4fBcsQVyefenltjqVi57DxdkzzgTDibxjj3Yzbz2nHjzJc4mf14iqX6-ZoBGNkGV2MuMAUjwjBX5bHwb5HsGHHGEGqa8O93vN9qHtPEl-439Y21ZODyv1zHYyXj8PmErpyFrKKLYka9HfSEGbq2va_SX1PLem6YZwjhcJ88Tjmv7w83mQkPJSQYoQYZYPPhtt-jy46KqZisQ9VjvIcBg-CbzOJV9SzQ" />
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full font-label-micro text-label-micro font-bold bg-amber-action text-slate-authority shadow-sm">
                                    PRICE DROP -28%
                                </span>
                            </div>
                            <button aria-label="Remove item"
                                class="wishlist-remove-btn absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-md text-error flex items-center justify-center shadow-sm hover:scale-110 active:scale-95 transition-all"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </div>
                        <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold uppercase tracking-wider text-on-surface-variant">PERIPHERALS
                                        • DESK</span>
                                    <div
                                        class="flex items-center gap-1 font-label-micro text-label-micro text-slate-authority font-semibold">
                                        <span class="material-symbols-outlined text-[16px] text-amber-action"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>4.6</span>
                                        <span class="text-on-surface-variant font-normal">(630)</span>
                                    </div>
                                </div>
                                <h3
                                    class="font-title-card text-title-card font-bold text-slate-authority group-hover:text-amber-action transition-colors line-clamp-1">
                                    Retro Cream Mechanical Keyboard
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1">
                                    Custom coiled aviator cable • Warm backlight
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 pt-2 border-t border-primary-alpha-10">
                                <div class="flex items-baseline justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹1,299</span>
                                        <span
                                            class="font-body-small text-body-small text-on-surface-variant line-through">₹1,799</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 px-1.5 py-0.5 rounded">Save
                                        ₹500</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-status-green font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                    <span>Only 4 left in stock</span>
                                </div>
                                <button
                                    class="add-cart-btn w-full mt-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- Product 4: Apple MacBook Pro -->
                    <article
                        class="group flex flex-col bg-card-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md"
                        data-category="electronics" data-pricedrop="false">
                        <div class="relative w-full aspect-square bg-surface-container overflow-hidden">
                            <img alt="Apple MacBook Pro M3 16 Inch"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1WmVwiFHlpeR7aYWEXJI4MXdP_CMYZQzm9EupPH1KVffNXSiO5PbZE_Tl4V_tHbuNP3hZJ0_nT2oTXbZIQZyfuHDsn592L5uVajacxVQs83-yA9rgzvgTCkCPFH1bEB5Xhg6AZfBoRqtN_xg9zUgydCzGubY78iWLgaQXB-BLQKX-06YRbcoJMVWNDeA3muHHDYcfbSIMMagz19qyd9UFJwbexjK_t_q8IxTxQur18NCgCLnTQ057oWX-E" />
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full font-label-micro text-label-micro font-bold bg-slate-authority text-canvas-ivory shadow-sm">
                                    ESCROW PROTECTED
                                </span>
                            </div>
                            <button aria-label="Remove item"
                                class="wishlist-remove-btn absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-md text-error flex items-center justify-center shadow-sm hover:scale-110 active:scale-95 transition-all"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </div>
                        <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold uppercase tracking-wider text-on-surface-variant">COMPUTING
                                        • PRO</span>
                                    <div
                                        class="flex items-center gap-1 font-label-micro text-label-micro text-slate-authority font-semibold">
                                        <span class="material-symbols-outlined text-[16px] text-amber-action"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>4.9</span>
                                        <span class="text-on-surface-variant font-normal">(2,110)</span>
                                    </div>
                                </div>
                                <h3
                                    class="font-title-card text-title-card font-bold text-slate-authority group-hover:text-amber-action transition-colors line-clamp-1">
                                    Apple MacBook Pro M3 16"
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1">
                                    Space Black • 36GB Unified RAM • 1TB SSD
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 pt-2 border-t border-primary-alpha-10">
                                <div class="flex items-baseline justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹59,999</span>
                                        <span
                                            class="font-body-small text-body-small text-on-surface-variant line-through">₹69,999</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 px-1.5 py-0.5 rounded">Save
                                        ₹10,000</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-status-green font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                    <span>Verified Seller • Immediate dispatch</span>
                                </div>
                                <button
                                    class="add-cart-btn w-full mt-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- Product 5: Vintage Leica Camera -->
                    <article
                        class="group flex flex-col bg-card-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md"
                        data-category="auctions" data-pricedrop="false">
                        <div class="relative w-full aspect-square bg-surface-container overflow-hidden">
                            <img alt="Vintage Leica M 35mm Rangefinder Camera"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1WtXge9GPbqerXxtDR81FmoO_hfHqYgmqlaYYUwc2Yh_MefBJoICSP9w7Wb0Cqr6tyKGrqQHUndgUWVoT8ggWFORDtiLGGzfQHiKXsLNWSKSi8IniszOYx4fofXjH1O2Fr6JH_kTpLyAmT-GYlGMOKE3SeqInPG0mwsWk2CEDPZEvzeA4FChdtZ_EWJd2NUmm0zgSRZojYH02eszXaZDsXknosVC-J0Vy91v025yswoZN2tqWyLVRPaitw" />
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full font-label-micro text-label-micro font-bold bg-slate-authority text-amber-action shadow-sm">
                                    AUCTION ENDING IN 2H
                                </span>
                            </div>
                            <button aria-label="Remove item"
                                class="wishlist-remove-btn absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-md text-error flex items-center justify-center shadow-sm hover:scale-110 active:scale-95 transition-all"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </div>
                        <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold uppercase tracking-wider text-on-surface-variant">COLLECTIBLE
                                        • OPTICS</span>
                                    <div
                                        class="flex items-center gap-1 font-label-micro text-label-micro text-slate-authority font-semibold">
                                        <span class="material-symbols-outlined text-[16px] text-amber-action"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>4.9</span>
                                        <span class="text-on-surface-variant font-normal">(184)</span>
                                    </div>
                                </div>
                                <h3
                                    class="font-title-card text-title-card font-bold text-slate-authority group-hover:text-amber-action transition-colors line-clamp-1">
                                    Vintage Leica M 35mm Rangefinder
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1">
                                    Brass patina • Fully serviced shutter
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 pt-2 border-t border-primary-alpha-10">
                                <div class="flex items-baseline justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹85,000</span>
                                        <span class="font-label-micro text-label-micro text-on-surface-variant">Current
                                            Bid</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro font-semibold text-slate-authority bg-surface-container px-1.5 py-0.5 rounded">14
                                        Bids</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-amber-action font-medium">
                                    <span class="material-symbols-outlined text-[14px]">timer</span>
                                    <span>Reserve price unlocked</span>
                                </div>
                                <button
                                    class="w-full mt-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">gavel</span>
                                    <span>Place Live Bid</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- Product 6: Ceramic Pour-Over -->
                    <article
                        class="group flex flex-col bg-card-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md"
                        data-category="crafts" data-pricedrop="false">
                        <div class="relative w-full aspect-square bg-surface-container overflow-hidden">
                            <img alt="Artisan Ceramic Pour-Over Set"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1VEqpC4Hxd6V20c1K4TxM7C3OOrKBkSDxiXru2mbeOpZSPDKBrWf1c9Vrzam0URflXPCbkjybkbYJgwNkHjMA_AXL1G2BzOZzNhkZYqp2uth8lwx4V9vfmM1juZjW7i5WDGX50tg0PFqNK2TUSM56Tt8j68ViDgQAunzYL2xTo3FUK-jsgu35WM9Jk6WgLfHOeQarJzSC7OuADmqpPTNE-Q2SIcnJSOsvQ_7U5DEl5qe15RYChEeiMJdLc" />
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full font-label-micro text-label-micro font-bold bg-card-white text-slate-authority shadow-sm">
                                    HANDCRAFTED
                                </span>
                            </div>
                            <button aria-label="Remove item"
                                class="wishlist-remove-btn absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-md text-error flex items-center justify-center shadow-sm hover:scale-110 active:scale-95 transition-all"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </div>
                        <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold uppercase tracking-wider text-on-surface-variant">KITCHEN
                                        • ARTISAN</span>
                                    <div
                                        class="flex items-center gap-1 font-label-micro text-label-micro text-slate-authority font-semibold">
                                        <span class="material-symbols-outlined text-[16px] text-amber-action"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>4.8</span>
                                        <span class="text-on-surface-variant font-normal">(420)</span>
                                    </div>
                                </div>
                                <h3
                                    class="font-title-card text-title-card font-bold text-slate-authority group-hover:text-amber-action transition-colors line-clamp-1">
                                    Artisan Ceramic Pour-Over Set
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1">
                                    Earth stoneware dripper &amp; 350ml mug
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 pt-2 border-t border-primary-alpha-10">
                                <div class="flex items-baseline justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹1,500</span>
                                        <span
                                            class="font-body-small text-body-small text-on-surface-variant line-through">₹1,850</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 px-1.5 py-0.5 rounded">Save
                                        ₹350</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-status-green font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                    <span>Direct craft studio batch</span>
                                </div>
                                <button
                                    class="add-cart-btn w-full mt-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- Product 7: Nike Air Max 270 -->
                    <article
                        class="group flex flex-col bg-card-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md"
                        data-category="crafts" data-pricedrop="false">
                        <div class="relative w-full aspect-square bg-surface-container overflow-hidden">
                            <img alt="Nike Air Max 270 Earth Edition"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1Xk-cbYLEd4R-z9TPjwfNw0cyNTEgkAitsgzIeecHJrWOMUdviByMVnbzPQiWzM2Usp0_v_VWuHLZDbULLTv1HMBCucFo_ko2p4IHcKO-okK1e8d-8T7yZmZwJPSrIJlHul1iow5r-2aR9naYy8dRJMbO_As4FK_nrFeTNlTjyX94mzSMC6uw_NPiFHpf4AaLvwANr-gYtJVEx3cuXniJtDkFIHferibqwSr8gg9lKqijejaIKmN_QP-U8" />
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full font-label-micro text-label-micro font-bold bg-surface-container-high text-slate-authority shadow-sm">
                                    LIMITED RUN
                                </span>
                            </div>
                            <button aria-label="Remove item"
                                class="wishlist-remove-btn absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-md text-error flex items-center justify-center shadow-sm hover:scale-110 active:scale-95 transition-all"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </div>
                        <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold uppercase tracking-wider text-on-surface-variant">FOOTWEAR
                                        • STREET</span>
                                    <div
                                        class="flex items-center gap-1 font-label-micro text-label-micro text-slate-authority font-semibold">
                                        <span class="material-symbols-outlined text-[16px] text-amber-action"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>4.7</span>
                                        <span class="text-on-surface-variant font-normal">(512)</span>
                                    </div>
                                </div>
                                <h3
                                    class="font-title-card text-title-card font-bold text-slate-authority group-hover:text-amber-action transition-colors line-clamp-1">
                                    Nike Air Max 270 Earth Edition
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1">
                                    Neutral earth tone upper with amber accent
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 pt-2 border-t border-primary-alpha-10">
                                <div class="flex items-baseline justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹7,999</span>
                                        <span
                                            class="font-body-small text-body-small text-on-surface-variant line-through">₹9,499</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 px-1.5 py-0.5 rounded">Save
                                        ₹1,500</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-status-green font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                    <span>Sizes 8, 9, 10 in stock</span>
                                </div>
                                <button
                                    class="add-cart-btn w-full mt-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- Product 8: Technical Backpack -->
                    <article
                        class="group flex flex-col bg-card-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md"
                        data-category="crafts" data-pricedrop="false">
                        <div class="relative w-full aspect-square bg-surface-container overflow-hidden">
                            <img alt="Technical Outdoor Travel Backpack"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1W2BDkBFYipW9qmjyIcLQgLjryi6WxFqYmFvfS8dgPXCIv7ZMlFsKoTVonJf0z4hOy14uaOfbEQjlDSS7Met7Fxszg0MwDBydbakzwvEVJulOTe0fhh-gjGqqhJIqbWVmgpd0ZXFoA60rJCu-XL9cIrF4Ud7TGU3-FtkTl7zhZIqgeGOh-vAtbxTIzhFUS9QuNI2-1iJx9S5Qh5cnTUwHXVYvneimc2oNPV1h-H3kt8LjJ6g8YLSR4xJw" />
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full font-label-micro text-label-micro font-bold bg-card-white text-slate-authority shadow-sm">
                                    CORDURA 500D
                                </span>
                            </div>
                            <button aria-label="Remove item"
                                class="wishlist-remove-btn absolute top-3 right-3 w-8 h-8 rounded-full bg-card-white/90 backdrop-blur-md text-error flex items-center justify-center shadow-sm hover:scale-110 active:scale-95 transition-all"
                                type="button">
                                <span class="material-symbols-outlined text-[18px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </div>
                        <div class="p-card-padding flex flex-col flex-1 justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold uppercase tracking-wider text-on-surface-variant">TRAVEL
                                        • GEAR</span>
                                    <div
                                        class="flex items-center gap-1 font-label-micro text-label-micro text-slate-authority font-semibold">
                                        <span class="material-symbols-outlined text-[16px] text-amber-action"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>4.5</span>
                                        <span class="text-on-surface-variant font-normal">(340)</span>
                                    </div>
                                </div>
                                <h3
                                    class="font-title-card text-title-card font-bold text-slate-authority group-hover:text-amber-action transition-colors line-clamp-1">
                                    Technical Travel Backpack 30L
                                </h3>
                                <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1">
                                    Matte black weatherproof shell • TSA lay-flat
                                </p>
                            </div>
                            <div class="flex flex-col gap-2 pt-2 border-t border-primary-alpha-10">
                                <div class="flex items-baseline justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span
                                            class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹4,299</span>
                                        <span
                                            class="font-body-small text-body-small text-on-surface-variant line-through">₹4,999</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro font-bold text-status-green bg-status-green/10 px-1.5 py-0.5 rounded">Save
                                        ₹700</span>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 font-label-micro text-label-micro text-status-green font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                    <span>Lifetime warranty included</span>
                                </div>
                                <button
                                    class="add-cart-btn w-full mt-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold transition-all shadow-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
                <!-- Pagination & Bulk Summary -->
                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-card-white rounded-xl shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="font-body-small text-body-small text-on-surface-variant">
                            Showing <span class="font-bold text-slate-authority" id="showing-count">8</span> of 12 items
                        </span>
                        <span class="text-on-surface-variant/40">•</span>
                        <span
                            class="font-label-micro text-label-micro bg-surface-container px-2 py-0.5 rounded text-slate-authority font-medium">
                            Vault Value: ₹1,66,594
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button
                            class="w-9 h-9 rounded-lg bg-surface-container hover:bg-surface-container-high flex items-center justify-center text-slate-authority transition-colors disabled:opacity-40"
                            disabled="">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </button>
                        <button
                            class="w-9 h-9 rounded-lg bg-slate-authority text-canvas-ivory font-body-small text-body-small font-bold flex items-center justify-center shadow-sm">
                            1
                        </button>
                        <button
                            class="w-9 h-9 rounded-lg bg-surface-container hover:bg-surface-container-high text-slate-authority font-body-small text-body-small font-semibold flex items-center justify-center transition-colors">
                            2
                        </button>
                        <button
                            class="w-9 h-9 rounded-lg bg-surface-container hover:bg-surface-container-high flex items-center justify-center text-slate-authority transition-colors">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>
                </div>
                <!-- Telemetry & Price Alert Banner (Slate Authority) -->
                <div
                    class="bg-slate-authority text-canvas-ivory rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-md relative overflow-hidden">
                    <div class="flex items-start gap-4 z-10">
                        <div
                            class="w-12 h-12 rounded-xl bg-amber-action/20 text-amber-action flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-[28px]">notifications_active</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-title-card text-title-card font-bold text-canvas-ivory">
                                    Price Drop Telemetry Active
                                </h3>
                                <span
                                    class="font-label-micro text-label-micro bg-status-green/20 text-status-green px-2 py-0.5 rounded-full font-semibold">
                                    Live Monitor
                                </span>
                            </div>
                            <p class="font-body-small text-body-small text-canvas-ivory/70 max-w-xl">
                                Bazaario monitors all saved market items every 15 minutes. We notify you via SMS &amp;
                                Push notification when seller prices decrease by 5% or more.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto z-10">
                        <button
                            class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-body-small font-bold transition-all shadow-sm whitespace-nowrap"
                            type="button">
                            Configure Price Alerts
                        </button>
                    </div>
                    <!-- Decorative subtle radial background circle -->
                    <div
                        class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-amber-action/10 blur-3xl pointer-events-none">
                    </div>
                </div>
                <!-- Floating AI Concierge Dock -->
                <div class="fixed bottom-6 right-6 z-40">
                    <button
                        class="flex items-center gap-3 px-4 py-3 rounded-full bg-slate-authority text-canvas-ivory shadow-xl hover:bg-slate-authority/90 active:scale-95 transition-all group"
                        type="button">
                        <span
                            class="material-symbols-outlined text-[20px] text-amber-action group-hover:rotate-12 transition-transform">auto_awesome</span>
                        <div class="flex flex-col text-left">
                            <span
                                class="font-label-micro text-label-micro uppercase tracking-wider text-amber-action font-bold leading-none">Bazaario
                                AI</span>
                            <span
                                class="font-body-small text-body-small font-semibold text-canvas-ivory leading-tight">Compare
                                saved items</span>
                        </div>
                        <span class="material-symbols-outlined text-[18px] text-canvas-ivory/60">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>
        <script>
            (function () {
                // Filter Tabs
                const tabs = document.querySelectorAll('#wishlist-filter-tabs button');
                const articles = document.querySelectorAll('#wishlist-grid article');
                const showingCount = document.getElementById('showing-count');

                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        tabs.forEach(t => {
                            t.classList.remove('bg-slate-authority', 'text-canvas-ivory');
                            t.classList.add('text-on-surface-variant');
                        });
                        tab.classList.add('bg-slate-authority', 'text-canvas-ivory');
                        tab.classList.remove('text-on-surface-variant');

                        const filter = tab.getAttribute('data-filter');
                        let visible = 0;

                        articles.forEach(art => {
                            if (filter === 'all') {
                                art.style.display = 'flex';
                                visible++;
                            } else if (filter === 'pricedrop') {
                                const isDrop = art.getAttribute('data-pricedrop') === 'true';
                                art.style.display = isDrop ? 'flex' : 'none';
                                if (isDrop) visible++;
                            } else {
                                const cat = art.getAttribute('data-category');
                                const match = cat === filter;
                                art.style.display = match ? 'flex' : 'none';
                                if (match) visible++;
                            }
                        });

                        if (showingCount) {
                            showingCount.textContent = visible.toString();
                        }
                    });
                });

                // Remove item interaction
                const removeButtons = document.querySelectorAll('.wishlist-remove-btn');
                removeButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const card = btn.closest('article');
                        if (card) {
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                card.remove();
                                const remaining = document.querySelectorAll('#wishlist-grid article').length;
                                if (showingCount) showingCount.textContent = remaining.toString();
                            }, 200);
                        }
                    });
                });

                // Add to cart feedback
                const addCartButtons = document.querySelectorAll('.add-cart-btn');
                addCartButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const origHtml = btn.innerHTML;
                        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">check</span><span>Added!</span>';
                        btn.classList.remove('bg-amber-action');
                        btn.classList.add('bg-status-green', 'text-white');
                        setTimeout(() => {
                            btn.innerHTML = origHtml;
                            btn.classList.remove('bg-status-green', 'text-white');
                            btn.classList.add('bg-amber-action');
                        }, 1500);
                    });
                });

                // Move all to cart
                const moveAllBtn = document.getElementById('move-all-btn');
                if (moveAllBtn) {
                    moveAllBtn.addEventListener('click', () => {
                        addCartButtons.forEach(btn => btn.click());
                    });
                }
            })();
        </script>
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