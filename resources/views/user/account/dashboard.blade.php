<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="web_standard" name="shell-type">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600&amp;family=Space+Grotesk:wght@500;600;700&amp;display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet">
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
                        src="https://lh3.googleusercontent.com/aida/AEtjO1XSJITP3lGfyftjWK-KyQdkr_p-dkjB55PjIN7euT8WuESTgjD3lPkAVAUXxlFIjVJPBz9Oqm2cKZ_x9x3S_amnhGmh900sc5ueTNsDEcNpP93YxqAkt1txHQPaxSo6QEOdfMl0VwK1kInjDJv-PlIFHTQi_ymjzclU6QdqVlntqPIYbPl96vON-MAHiiu67gshPxDEtQbgZyyE8fCJ0BPrIQVNzhnbyuckQwvYyDNRcbUonyAzkarFSyqzC63lc4cgjr8Z0R0_qQ">
                    <div class="flex flex-col text-left"><span
                            class="font-button-text text-body-small font-semibold text-slate-authority leading-tight">Avijit</span><span
                            class="inline-flex items-center gap-1 font-label-micro text-label-micro text-status-green font-medium leading-none"><span
                                class="w-1.5 h-1.5 rounded-full bg-status-green"></span>Verified</span></div>
                </a>
            </div>
        </div>
    </header>
    <main class="w-full pt-20 bg-canvas-ivory flex-1">
        <div class="flex flex-col w-full relative overflow-hidden">
            <!-- Atmospheric Luminous 3D Glass Gradients -->
            <div
                class="pointer-events-none absolute -top-40 -left-20 w-[550px] h-[550px] rounded-full bg-amber-action/15 blur-[120px] mix-blend-multiply">
            </div>
            <div
                class="pointer-events-none absolute top-72 -right-28 w-[600px] h-[600px] rounded-full bg-slate-authority/10 blur-[140px] mix-blend-multiply">
            </div>
            <div
                class="pointer-events-none absolute top-[900px] left-1/3 w-[450px] h-[450px] rounded-full bg-amber-action/10 blur-[130px]">
            </div>
            <div
                class="max-w-container-max mx-auto px-gutter-md w-full relative z-10 py-gutter-lg flex flex-col gap-gutter-lg">
                <!-- 1. Header & Welcome Hero -->
                <section class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 pb-2">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center gap-2 font-label-micro text-label-micro tracking-widest uppercase bg-surface-container-lowest/80 text-amber-action px-3 py-1.5 rounded-full shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                                ESCROW VERIFIED BUYER • VAULT #ESC-9041
                            </span>
                            <span
                                class="hidden sm:inline-flex items-center font-label-micro text-label-micro px-2.5 py-1 rounded-full bg-slate-authority/5 text-slate-authority">
                                UID: BZ-891-IN
                            </span>
                        </div>
                        <h1 class="font-display-hero text-display-hero text-slate-authority tracking-tight">
                            Good morning, Avijit <span
                                class="inline-block transform origin-bottom-right hover:rotate-12 transition-transform cursor-pointer">👋</span>
                        </h1>
                        <p class="font-body-lead text-body-lead text-on-surface-variant max-w-2xl">
                            Here's what's happening with your Bazaario account, orders, and protected vaults today.
                        </p>
                    </div>
                    <!-- Quick Action Pills -->
                    <div class="flex flex-wrap items-center gap-3">
                        <div
                            class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm">
                            <span
                                class="w-7 h-7 rounded-lg bg-amber-action/15 flex items-center justify-center text-amber-action">
                                <span class="material-symbols-outlined text-[18px]">lock</span>
                            </span>
                            <div class="flex flex-col">
                                <span class="font-label-micro text-label-micro text-on-surface-variant uppercase">Escrow
                                    Vault Balance</span>
                                <span
                                    class="font-title-card text-title-card font-bold text-slate-authority leading-tight">₹60,500.00</span>
                            </div>
                        </div>
                        <a class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-lowest/80 backdrop-blur-md hover:bg-surface-container text-slate-authority transition-colors shadow-sm"
                            data-path="account-settings" href="#">
                            <span class="material-symbols-outlined text-[20px] text-on-surface-variant">settings</span>
                            <span class="font-button-text text-body-small font-semibold">Account Settings</span>
                        </a>
                    </div>
                </section>
                <!-- 2. 4 Stat Cards Grid (Modern 3D Dimensional Glass) -->
                <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Card 1: Orders -->
                    <div
                        class="relative group p-5 rounded-2xl bg-surface-container-lowest/85 backdrop-blur-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                        <div
                            class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-amber-action/10 blur-xl group-hover:scale-125 transition-transform">
                        </div>
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-amber-action/15 flex items-center justify-center text-amber-action shadow-inner">
                                <span class="material-symbols-outlined text-[26px]">inventory_2</span>
                            </div>
                            <span
                                class="inline-flex items-center gap-1.5 font-label-micro text-label-micro bg-status-green/10 text-status-green px-2.5 py-1 rounded-full font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-status-green animate-ping"></span>
                                2 in transit
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="font-display-hero text-headline-section font-bold text-slate-authority">24</span>
                            <span class="font-body-small text-body-small text-on-surface-variant mt-0.5">Total
                                Orders</span>
                        </div>
                    </div>
                    <!-- Card 2: Wishlist -->
                    <div
                        class="relative group p-5 rounded-2xl bg-surface-container-lowest/85 backdrop-blur-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                        <div
                            class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-error/10 blur-xl group-hover:scale-125 transition-transform">
                        </div>
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-error/10 flex items-center justify-center text-error shadow-inner">
                                <span class="material-symbols-outlined text-[26px]">favorite</span>
                            </div>
                            <span
                                class="inline-flex items-center font-label-micro text-label-micro bg-error/10 text-error px-2.5 py-1 rounded-full font-medium">
                                3 On Discount
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="font-display-hero text-headline-section font-bold text-slate-authority">12</span>
                            <span class="font-body-small text-body-small text-on-surface-variant mt-0.5">Saved
                                Items</span>
                        </div>
                    </div>
                    <!-- Card 3: Active Bids -->
                    <div
                        class="relative group p-5 rounded-2xl bg-surface-container-lowest/85 backdrop-blur-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                        <div
                            class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-primary-container/10 blur-xl group-hover:scale-125 transition-transform">
                        </div>
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-primary-container/10 flex items-center justify-center text-slate-authority shadow-inner">
                                <span class="material-symbols-outlined text-[26px]">gavel</span>
                            </div>
                            <span
                                class="inline-flex items-center gap-1 font-label-micro text-label-micro bg-amber-action/20 text-slate-authority px-2.5 py-1 rounded-full font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-action animate-pulse"></span>
                                1 Winning • 2h left
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="font-display-hero text-headline-section font-bold text-slate-authority">3</span>
                            <span class="font-body-small text-body-small text-on-surface-variant mt-0.5">Live
                                Auctions</span>
                        </div>
                    </div>
                    <!-- Card 4: Coupons -->
                    <div
                        class="relative group p-5 rounded-2xl bg-surface-container-lowest/85 backdrop-blur-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                        <div
                            class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-status-green/10 blur-xl group-hover:scale-125 transition-transform">
                        </div>
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-status-green/10 flex items-center justify-center text-status-green shadow-inner">
                                <span class="material-symbols-outlined text-[26px]">confirmation_number</span>
                            </div>
                            <span
                                class="inline-flex items-center font-label-micro text-label-micro bg-status-green/10 text-status-green px-2.5 py-1 rounded-full font-medium">
                                Save ₹2,500
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="font-display-hero text-headline-section font-bold text-slate-authority">5</span>
                            <span class="font-body-small text-body-small text-on-surface-variant mt-0.5">Available
                                Perks</span>
                        </div>
                    </div>
                </section>
                <!-- 3. Recent Orders Section -->
                <section class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-headline-section text-headline-section text-slate-authority tracking-tight">
                                Recent Orders</h2>
                            <p class="font-body-small text-body-small text-on-surface-variant">Live escrow state and
                                multi-checkpoint fulfillment</p>
                        </div>
                        <a class="font-button-text text-body-small text-amber-action hover:text-amber-action/80 flex items-center gap-1 font-semibold transition-colors"
                            data-path="all-orders" href="#">
                            View all orders <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>
                    <div class="flex flex-col gap-3">
                        <!-- Order 1 -->
                        <div
                            class="p-4 sm:p-5 rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <div
                                    class="w-20 h-20 rounded-xl overflow-hidden bg-surface-container-high flex-shrink-0">
                                    <img alt="Sony Headphones" class="w-full h-full object-cover"
                                        src="https://lh3.googleusercontent.com/aida/AEtjO1ULHYfZ58DWHb3ovmkHCOx30d2y2KNFJNjSaNeyIekug2Nn45SSGMa1s8vyF89_u8JpyK5DcURAFa2FtF4vEIv2pVZGC2MzAZq1rRjLYQKE-t52YAMdsIJW5prgjmSFsExmX2aiDaBf_jZkASVFGXLFGDmCoTL9fUxPtPDX9hogJvtf9H4TP8MNd42K8QLAuao7kkBFZ2byKJcoFdTnhVs9s7R2FX26LF17V1ZCh9hOhUu4lmibOG9-0g">
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider">#BZ-2026-00128</span>
                                        <span
                                            class="inline-flex items-center gap-1 font-label-micro text-label-micro bg-status-green/10 text-status-green px-2 py-0.5 rounded-full font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span> Delivered ✓
                                        </span>
                                    </div>
                                    <h3
                                        class="font-title-card text-title-card font-semibold text-slate-authority truncate mt-1">
                                        Sony WH-1000XM5 Wireless Noise Cancelling Headphones</h3>
                                    <p class="font-body-small text-body-small text-on-surface-variant truncate">Matte
                                        Black • 1 Item • TechWorld Store • Delivered Apr 14</p>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between md:justify-end gap-6 w-full md:w-auto flex-shrink-0 pt-2 md:pt-0">
                                <div class="flex flex-col md:text-right">
                                    <span class="font-label-micro text-label-micro text-on-surface-variant">Escrow
                                        Settled</span>
                                    <span
                                        class="font-title-card text-title-card font-bold text-slate-authority">₹2,499</span>
                                </div>
                                <a class="px-4 py-2.5 rounded-xl bg-surface-container-high hover:bg-surface-container-highest text-slate-authority font-button-text text-body-small font-semibold transition-colors flex items-center gap-1"
                                    data-path="order-details-128" href="#">
                                    Details <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                </a>
                            </div>
                        </div>
                        <!-- Order 2 -->
                        <div
                            class="p-4 sm:p-5 rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <div
                                    class="w-20 h-20 rounded-xl overflow-hidden bg-surface-container-high flex-shrink-0">
                                    <img alt="Apple Watch Ultra 2" class="w-full h-full object-cover"
                                        src="https://lh3.googleusercontent.com/aida/AEtjO1WCV79P_WvGQN5KQJBCsoSb-BB7bk_bI9raCF39grO__0osmkm6wz8yOcsJDmbNAtpsoOLotAmSTm8MaUvtbCzz2kFK6vXUGpeo78gNOeqO1yyDPtdaIb533Ya-QTC0PN7pTYfSKxV6V-u3H9sok9Nj0YVvjmpsuzEplsjAWJRMufP453b17PWleiI6RG79hsIYDKekSa9mJ0sz3bbZnIjMwEebK0zG_961mM9Bh1cY8T9TmdK9Ce5EeQc">
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider">#BZ-2026-00124</span>
                                        <span
                                            class="inline-flex items-center gap-1 font-label-micro text-label-micro bg-primary-fixed text-slate-authority px-2 py-0.5 rounded-full font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-authority"></span> Shipped 🚚
                                        </span>
                                        <span
                                            class="font-label-micro text-label-micro text-amber-action bg-amber-action/10 px-2 py-0.5 rounded-full font-medium">Out
                                            for delivery today</span>
                                    </div>
                                    <h3
                                        class="font-title-card text-title-card font-semibold text-slate-authority truncate mt-1">
                                        Apple Watch Ultra 2 (Titanium / Orange Ocean Band)</h3>
                                    <p class="font-body-small text-body-small text-on-surface-variant truncate">49mm GPS
                                        • 1 Item • Direct Stall • Telemetry Node Active</p>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between md:justify-end gap-6 w-full md:w-auto flex-shrink-0 pt-2 md:pt-0">
                                <div class="flex flex-col md:text-right">
                                    <span class="font-label-micro text-label-micro text-on-surface-variant">Escrow
                                        Held</span>
                                    <span
                                        class="font-title-card text-title-card font-bold text-slate-authority">₹3,999</span>
                                </div>
                                <a class="px-4 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-90 active:scale-[0.99] transition-all flex items-center gap-1 shadow-sm"
                                    data-path="track-order-124" href="#">
                                    <span class="material-symbols-outlined text-[16px]">near_me</span> Track Live
                                </a>
                            </div>
                        </div>
                        <!-- Order 3 -->
                        <div
                            class="p-4 sm:p-5 rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <div
                                    class="w-20 h-20 rounded-xl overflow-hidden bg-surface-container-high flex-shrink-0">
                                    <img alt="Mechanical Keyboard" class="w-full h-full object-cover"
                                        src="https://lh3.googleusercontent.com/aida/AEtjO1V962eUfdX3W9GnnupcSTLq4Tj4fBcsQVyefenltjqVi57DxdkzzgTDibxjj3Yzbz2nHjzJc4mf14iqX6-ZoBGNkGV2MuMAUjwjBX5bHwb5HsGHHGEGqa8O93vN9qHtPEl-439Y21ZODyv1zHYyXj8PmErpyFrKKLYka9HfSEGbq2va_SX1PLem6YZwjhcJ88Tjmv7w83mQkPJSQYoQYZYPPhtt-jy46KqZisQ9VjvIcBg-CbzOJV9SzQ">
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant uppercase tracking-wider">#BZ-2026-00119</span>
                                        <span
                                            class="inline-flex items-center gap-1 font-label-micro text-label-micro bg-amber-action/20 text-slate-authority px-2 py-0.5 rounded-full font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-action"></span> Processing ⏳
                                        </span>
                                        <span class="font-label-micro text-label-micro text-on-surface-variant">Stall
                                            packaging &amp; QC in progress</span>
                                    </div>
                                    <h3
                                        class="font-title-card text-title-card font-semibold text-slate-authority truncate mt-1">
                                        Retro Cream Wireless Mechanical Keyboard</h3>
                                    <p class="font-body-small text-body-small text-on-surface-variant truncate">Artisan
                                        ANSI PBT • Gateron Oil King Switches</p>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between md:justify-end gap-6 w-full md:w-auto flex-shrink-0 pt-2 md:pt-0">
                                <div class="flex flex-col md:text-right">
                                    <span class="font-label-micro text-label-micro text-on-surface-variant">Escrow
                                        Held</span>
                                    <span
                                        class="font-title-card text-title-card font-bold text-slate-authority">₹1,299</span>
                                </div>
                                <a class="px-4 py-2.5 rounded-xl bg-surface-container-high hover:bg-surface-container-highest text-slate-authority font-button-text text-body-small font-semibold transition-colors flex items-center gap-1"
                                    data-path="order-details-119" href="#">
                                    Details <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- 4. Escrow Guarantee Banner -->
                <section
                    class="p-6 md:p-8 rounded-2xl bg-slate-authority text-canvas-ivory relative overflow-hidden shadow-lg">
                    <div
                        class="pointer-events-none absolute -right-20 -top-20 w-80 h-80 rounded-full bg-amber-action/20 blur-3xl">
                    </div>
                    <div
                        class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 rounded-2xl bg-amber-action/20 text-amber-action flex items-center justify-center flex-shrink-0 shadow-inner">
                                <span class="material-symbols-outlined text-[32px]">shield_with_heart</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow text-amber-action uppercase tracking-wider font-semibold">Tier-1
                                        Smart Protection</span>
                                    <span
                                        class="font-label-micro text-label-micro bg-status-green/20 text-secondary-fixed px-2 py-0.5 rounded-full font-mono">100%
                                        SECURE</span>
                                </div>
                                <h3
                                    class="font-headline-section text-headline-section font-bold text-canvas-ivory leading-snug">
                                    Bazaario Escrow Protocol Protection Active</h3>
                                <p class="font-body-small text-body-small text-canvas-ivory/70 max-w-2xl mt-1">
                                    Your merchant funds are held safely in a multi-signature smart escrow hold until
                                    physical or digital parcel handover is inspected and approved by you.
                                </p>
                            </div>
                        </div>
                        <a class="px-6 py-3 rounded-xl bg-amber-action hover:bg-amber-action/90 text-slate-authority font-button-text text-button-text font-bold transition-transform active:scale-[0.99] whitespace-nowrap shadow-md"
                            data-path="verify-escrow" href="#">
                            Verify Protection Details
                        </a>
                    </div>
                </section>
                <!-- 5. Recommended For You Section -->
                <section class="flex flex-col gap-5 pb-section-final-bottom">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
                        <div>
                            <span
                                class="font-label-micro text-label-micro text-amber-action uppercase tracking-widest font-semibold">AI
                                Curated Picks</span>
                            <h2 class="font-headline-section text-headline-section text-slate-authority tracking-tight">
                                Recommended For You</h2>
                            <p class="font-body-small text-body-small text-on-surface-variant mt-0.5">Matched from your
                                artisan search patterns &amp; high-trust verified stalls</p>
                        </div>
                        <a class="font-button-text text-body-small text-amber-action hover:text-amber-action/80 font-semibold flex items-center gap-1 transition-colors"
                            data-path="personalized-catalog" href="#">
                            See personalized catalog <span
                                class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <!-- Product 1 -->
                        <div
                            class="group flex flex-col rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md p-3.5 shadow-sm hover:shadow-md transition-all duration-300">
                            <div
                                class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container mb-3.5">
                                <img alt="Apple MacBook Pro M3"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1WmVwiFHlpeR7aYWEXJI4MXdP_CMYZQzm9EupPH1KVffNXSiO5PbZE_Tl4V_tHbuNP3hZJ0_nT2oTXbZIQZyfuHDsn592L5uVajacxVQs83-yA9rgzvgTCkCPFH1bEB5Xhg6AZfBoRqtN_xg9zUgydCzGubY78iWLgaQXB-BLQKX-06YRbcoJMVWNDeA3muHHDYcfbSIMMagz19qyd9UFJwbexjK_t_q8IxTxQur18NCgCLnTQ057oWX-E">
                                <span
                                    class="absolute top-2.5 left-2.5 font-label-micro text-label-micro bg-slate-authority/80 backdrop-blur-md text-canvas-ivory px-2.5 py-1 rounded-full font-semibold">
                                    Top Pick
                                </span>
                                <button aria-label="Save item"
                                    class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-surface-container-lowest/80 backdrop-blur-md text-slate-authority hover:text-error flex items-center justify-center transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">favorite</span>
                                </button>
                            </div>
                            <div class="flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-1 text-amber-action mb-1">
                                        <span class="material-symbols-outlined text-[16px]"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span
                                            class="font-label-micro text-label-micro text-slate-authority font-bold">4.9</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant">(142)</span>
                                    </div>
                                    <h4
                                        class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                        Apple MacBook Pro M3 16"</h4>
                                    <p
                                        class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                        Space Black • Liquid Retina XDR</p>
                                </div>
                                <div class="mt-4 pt-3 flex items-center justify-between">
                                    <div>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant block">Price</span>
                                        <span
                                            class="font-title-card text-title-card font-bold text-slate-authority">₹1,69,999</span>
                                    </div>
                                    <button
                                        class="px-3.5 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-90 active:scale-[0.99] transition-all flex items-center gap-1.5 shadow-sm"
                                        type="button">
                                        <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Product 2 -->
                        <div
                            class="group flex flex-col rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md p-3.5 shadow-sm hover:shadow-md transition-all duration-300">
                            <div
                                class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container mb-3.5">
                                <img alt="Vintage Leica M 35mm Brass"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1WtXge9GPbqerXxtDR81FmoO_hfHqYgmqlaYYUwc2Yh_MefBJoICSP9w7Wb0Cqr6tyKGrqQHUndgUWVoT8ggWFORDtiLGGzfQHiKXsLNWSKSi8IniszOYx4fofXjH1O2Fr6JH_kTpLyAmT-GYlGMOKE3SeqInPG0mwsWk2CEDPZEvzeA4FChdtZ_EWJd2NUmm0zgSRZojYH02eszXaZDsXknosVC-J0Vy91v025yswoZN2tqWyLVRPaitw">
                                <span
                                    class="absolute top-2.5 left-2.5 font-label-micro text-label-micro bg-amber-action text-slate-authority px-2.5 py-1 rounded-full font-bold">
                                    Live Auction
                                </span>
                                <button aria-label="Save item"
                                    class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-surface-container-lowest/80 backdrop-blur-md text-slate-authority hover:text-error flex items-center justify-center transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">favorite</span>
                                </button>
                            </div>
                            <div class="flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-1 text-amber-action mb-1">
                                        <span class="material-symbols-outlined text-[16px]"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span
                                            class="font-label-micro text-label-micro text-slate-authority font-bold">4.9</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant">(52)</span>
                                    </div>
                                    <h4
                                        class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                        Vintage Leica M 35mm Brass</h4>
                                    <p
                                        class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                        Mint Patina • Collector Vault Stall</p>
                                </div>
                                <div class="mt-4 pt-3 flex items-center justify-between">
                                    <div>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant block">Current
                                            Bid</span>
                                        <span
                                            class="font-title-card text-title-card font-bold text-slate-authority">₹85,000</span>
                                    </div>
                                    <button
                                        class="px-3.5 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold hover:bg-slate-authority/90 active:scale-[0.99] transition-all flex items-center gap-1.5 shadow-sm"
                                        type="button">
                                        <span class="material-symbols-outlined text-[18px]">gavel</span> Bid
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Product 3 -->
                        <div
                            class="group flex flex-col rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md p-3.5 shadow-sm hover:shadow-md transition-all duration-300">
                            <div
                                class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container mb-3.5">
                                <img alt="Artisan Ceramic Pour-Over Set"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1VEqpC4Hxd6V20c1K4TxM7C3OOrKBkSDxiXru2mbeOpZSPDKBrWf1c9Vrzam0URflXPCbkjybkbYJgwNkHjMA_AXL1G2BzOZzNhkZYqp2uth8lwx4V9vfmM1juZjW7i5WDGX50tg0PFqNK2TUSM56Tt8j68ViDgQAunzYL2xTo3FUK-jsgu35WM9Jk6WgLfHOeQarJzSC7OuADmqpPTNE-Q2SIcnJSOsvQ_7U5DEl5qe15RYChEeiMJdLc">
                                <span
                                    class="absolute top-2.5 left-2.5 font-label-micro text-label-micro bg-surface-container-highest text-slate-authority px-2.5 py-1 rounded-full font-medium">
                                    Handcrafted
                                </span>
                                <button aria-label="Save item"
                                    class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-surface-container-lowest/80 backdrop-blur-md text-slate-authority hover:text-error flex items-center justify-center transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">favorite</span>
                                </button>
                            </div>
                            <div class="flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-1 text-amber-action mb-1">
                                        <span class="material-symbols-outlined text-[16px]"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span
                                            class="font-label-micro text-label-micro text-slate-authority font-bold">4.8</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant">(88)</span>
                                    </div>
                                    <h4
                                        class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                        Artisan Ceramic Pour-Over Set</h4>
                                    <p
                                        class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                        Wood-fired stoneware dripper &amp; mug</p>
                                </div>
                                <div class="mt-4 pt-3 flex items-center justify-between">
                                    <div>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant block">Price</span>
                                        <span
                                            class="font-title-card text-title-card font-bold text-slate-authority">₹1,500</span>
                                    </div>
                                    <button
                                        class="px-3.5 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-90 active:scale-[0.99] transition-all flex items-center gap-1.5 shadow-sm"
                                        type="button">
                                        <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Product 4 -->
                        <div
                            class="group flex flex-col rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md p-3.5 shadow-sm hover:shadow-md transition-all duration-300">
                            <div
                                class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container mb-3.5">
                                <img alt="Nike Air Max 270 Earth Edition"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1Xk-cbYLEd4R-z9TPjwfNw0cyNTEgkAitsgzIeecHJrWOMUdviByMVnbzPQiWzM2Usp0_v_VWuHLZDbULLTv1HMBCucFo_ko2p4IHcKO-okK1e8d-8T7yZmZwJPSrIJlHul1iow5r-2aR9naYy8dRJMbO_As4FK_nrFeTNlTjyX94mzSMC6uw_NPiFHpf4AaLvwANr-gYtJVEx3cuXniJtDkFIHferibqwSr8gg9lKqijejaIKmN_QP-U8">
                                <span
                                    class="absolute top-2.5 left-2.5 font-label-micro text-label-micro bg-status-green/10 text-status-green px-2.5 py-1 rounded-full font-semibold">
                                    Trending
                                </span>
                                <button aria-label="Save item"
                                    class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-surface-container-lowest/80 backdrop-blur-md text-slate-authority hover:text-error flex items-center justify-center transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">favorite</span>
                                </button>
                            </div>
                            <div class="flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-1 text-amber-action mb-1">
                                        <span class="material-symbols-outlined text-[16px]"
                                            style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span
                                            class="font-label-micro text-label-micro text-slate-authority font-bold">4.7</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant">(210)</span>
                                    </div>
                                    <h4
                                        class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-1">
                                        Nike Air Max 270 Earth Edition</h4>
                                    <p
                                        class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">
                                        Neutral earth tones &amp; orange air bubble</p>
                                </div>
                                <div class="mt-4 pt-3 flex items-center justify-between">
                                    <div>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant block">Price</span>
                                        <span
                                            class="font-title-card text-title-card font-bold text-slate-authority">₹7,999</span>
                                    </div>
                                    <button
                                        class="px-3.5 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-90 active:scale-[0.99] transition-all flex items-center gap-1.5 shadow-sm"
                                        type="button">
                                        <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- 6. Interactive Assistant Dock Pill -->
            <aside class="fixed bottom-6 right-6 z-40">
                <button
                    class="flex items-center gap-3 px-4 py-3 rounded-full bg-slate-authority text-canvas-ivory shadow-xl hover:bg-slate-authority/90 transition-all active:scale-[0.98] group"
                    onclick="const p=document.getElementById('ai-bubble'); p.classList.toggle('hidden');" type="button">
                    <span
                        class="w-8 h-8 rounded-full bg-amber-action flex items-center justify-center text-slate-authority">
                        <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                    </span>
                    <span class="hidden sm:inline font-button-text text-body-small font-medium pr-1">
                        ✦ Ask Bazaario AI: <span class="text-canvas-ivory/70">“Track active package or reorder”</span>
                    </span>
                    <span class="material-symbols-outlined text-[20px] text-amber-action">chat</span>
                </button>
                <!-- AI Popup Mock -->
                <div class="hidden absolute bottom-16 right-0 w-80 p-4 rounded-2xl bg-surface-container-lowest/95 backdrop-blur-xl shadow-2xl flex flex-col gap-3"
                    id="ai-bubble">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-status-green"></span>
                            <span class="font-title-card text-body-small font-bold text-slate-authority">Bazaario
                                Assistant</span>
                        </div>
                        <button class="text-on-surface-variant hover:text-slate-authority"
                            onclick="document.getElementById('ai-bubble').classList.add('hidden')" type="button">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                    <p class="font-body-small text-body-small text-on-surface-variant">
                        Your order <strong class="text-slate-authority">#BZ-2026-00124</strong> is out for delivery in
                        Mumbai. Expected delivery: <strong>4:30 PM today</strong>.
                    </p>
                    <div class="flex flex-col gap-1.5 pt-1">
                        <button
                            class="w-full text-left font-label-micro text-label-micro px-2.5 py-1.5 rounded-lg bg-surface-container-high hover:bg-surface-container-highest text-slate-authority transition-colors"
                            type="button">
                            • View driver telemetry route
                        </button>
                        <button
                            class="w-full text-left font-label-micro text-label-micro px-2.5 py-1.5 rounded-lg bg-surface-container-high hover:bg-surface-container-highest text-slate-authority transition-colors"
                            type="button">
                            • Release escrow hold early
                        </button>
                    </div>
                </div>
            </aside>
        </div>
        <script>
            // Micro-interaction for quick interactive feedback
            document.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const ripple = document.createElement('span');
                    ripple.className = 'absolute inset-0 rounded-xl bg-white/20 pointer-events-none transition-opacity duration-300';
                    this.style.position = 'relative';
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 300);
                });
            });
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