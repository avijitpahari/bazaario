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
            <!-- Sub-Header / Account Breadcrumb Strip -->
            <section
                class="w-full bg-surface-container-low/70 backdrop-blur-md border-b border-primary-alpha-10 py-3.5">
                <div
                    class="max-w-container-max mx-auto px-gutter-md flex flex-wrap items-center justify-between gap-3 text-slate-authority">
                    <div class="flex items-center gap-3">
                        <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-card-white shadow-sm hover:bg-surface transition-colors font-button-text text-body-small text-slate-authority"
                            href="#">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            <span>Back to Account</span>
                        </a>
                        <div
                            class="hidden sm:flex items-center gap-2 font-label-eyebrow text-label-eyebrow text-on-surface-variant">
                            <span>Home</span>
                            <span>/</span>
                            <span>Account</span>
                            <span>/</span>
                            <span class="text-slate-authority font-semibold">Compare Products</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <div
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-card-white shadow-sm font-label-micro text-label-micro text-on-surface-variant">
                            <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                            <span>ESCROW VERIFIED BUYER</span>
                            <span class="text-on-surface-variant/40">•</span>
                            <span class="font-mono text-slate-authority">VAULT #ESC-9041</span>
                            <span class="text-on-surface-variant/40">•</span>
                            <span class="font-mono text-slate-authority">UID: BZ-891-IN</span>
                        </div>
                        <div
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-action/15 text-slate-authority font-label-micro text-label-micro font-bold">
                            <span class="material-symbols-outlined text-[14px]">lock</span>
                            <span>Vault: ₹60,500.00</span>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Main Comparison Stage -->
            <main class="w-full max-w-container-max mx-auto px-gutter-md pt-8 pb-section-final-bottom">
                <!-- Header Block -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6">
                    <div class="space-y-2 max-w-3xl">
                        <div
                            class="inline-flex items-center gap-2 font-label-eyebrow text-label-eyebrow uppercase text-amber-action tracking-wider">
                            <span class="material-symbols-outlined text-[15px]">difference</span>
                            <span>Account Management • Hardware &amp; Spec Matrix • Smart Diff Highlight</span>
                        </div>
                        <h1 class="font-headline-section text-display-hero text-slate-authority tracking-tight">Compare
                            Products</h1>
                        <p class="font-body-regular text-body-regular text-on-surface-variant">
                            Compare up to 4 products side by side with real-time merchant escrow ratings, verified audio
                            telemetry, and AI value scoring.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <button
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-card-white hover:bg-surface-container-high transition-colors shadow-sm font-button-text text-body-small text-slate-authority"
                            id="clearBtn" type="button">
                            <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
                            <span>Clear All</span>
                        </button>
                        <button
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-card-white hover:bg-surface-container-high transition-colors shadow-sm font-button-text text-body-small text-slate-authority"
                            type="button">
                            <span class="material-symbols-outlined text-[18px] text-amber-action">auto_awesome</span>
                            <span>AI Smart Match</span>
                        </button>
                        <button
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-authority text-canvas-ivory hover:bg-slate-authority/90 transition-colors shadow-sm font-button-text text-body-small"
                            type="button">
                            <span class="material-symbols-outlined text-[18px]">add_circle</span>
                            <span>+ Add 4th Product</span>
                        </button>
                    </div>
                </div>
                <!-- Quick Tabs / Difference Filters Strip -->
                <div
                    class="bg-card-white/90 backdrop-blur-md rounded-2xl p-2.5 shadow-sm mb-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none"
                        id="filterPillGroup">
                        <button
                            class="filter-pill px-4 py-2 rounded-xl font-button-text text-body-small bg-slate-authority text-canvas-ivory whitespace-nowrap shadow-sm"
                            data-filter="all">
                            All Features (18)
                        </button>
                        <button
                            class="filter-pill px-4 py-2 rounded-xl font-button-text text-body-small bg-surface-container-low text-on-surface-variant hover:text-slate-authority whitespace-nowrap flex items-center gap-1.5"
                            data-filter="diff">
                            <span class="w-2 h-2 rounded-full bg-amber-action"></span>
                            <span>Differences Only (7)</span>
                        </button>
                        <button
                            class="filter-pill px-4 py-2 rounded-xl font-button-text text-body-small bg-surface-container-low text-on-surface-variant hover:text-slate-authority whitespace-nowrap"
                            data-filter="audio">
                            Audio Specs
                        </button>
                        <button
                            class="filter-pill px-4 py-2 rounded-xl font-button-text text-body-small bg-surface-container-low text-on-surface-variant hover:text-slate-authority whitespace-nowrap"
                            data-filter="battery">
                            Battery &amp; Charging
                        </button>
                        <button
                            class="filter-pill px-4 py-2 rounded-xl font-button-text text-body-small bg-surface-container-low text-on-surface-variant hover:text-slate-authority whitespace-nowrap"
                            data-filter="warranty">
                            Warranty &amp; Escrow
                        </button>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-3 pl-2 shrink-0">
                        <label class="flex items-center gap-2 cursor-pointer select-none" for="winnerToggle">
                            <span
                                class="font-label-eyebrow text-label-eyebrow text-on-surface-variant uppercase">Highlight
                                Winner:</span>
                            <div
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-container-low font-label-micro text-label-micro font-semibold text-slate-authority">
                                <span class="material-symbols-outlined text-[16px] text-amber-action">bolt</span>
                                <span id="winnerStateLabel">ON / Auto</span>
                            </div>
                        </label>
                    </div>
                </div>
                <!-- Comparison Matrix Shell -->
                <div class="bg-card-white/95 rounded-2xl shadow-[0_4px_24px_rgba(15,23,42,0.05)] overflow-hidden">
                    <!-- Responsive Scroll Container -->
                    <div class="overflow-x-auto">
                        <div class="min-w-[980px] w-full flex flex-col">
                            <!-- Sticky Column Header -->
                            <div
                                class="grid grid-cols-12 bg-surface-container-low/80 backdrop-blur-md p-4 sticky top-20 z-20 shadow-sm items-stretch gap-3">
                                <!-- Col 1: Metric Label -->
                                <div class="col-span-3 flex flex-col justify-end p-2">
                                    <span
                                        class="font-label-micro text-label-micro uppercase text-on-surface-variant tracking-wider font-semibold">Comparison
                                        Matrix</span>
                                    <p
                                        class="font-headline-section text-title-card text-slate-authority font-bold mt-1">
                                        Telemetry &amp; Benchmarks</p>
                                    <span
                                        class="font-body-small text-label-micro text-on-surface-variant mt-0.5">Updated
                                        4 mins ago via Bazaario Core</span>
                                </div>
                                <!-- Col 2: Product A -->
                                <div
                                    class="col-span-3 bg-card-white rounded-xl p-3.5 shadow-sm relative flex flex-col justify-between group transition-all duration-200 hover:shadow-md">
                                    <button
                                        class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-surface-container-high hover:bg-error-container hover:text-error flex items-center justify-center text-on-surface-variant transition-colors"
                                        title="Remove Product">
                                        <span class="material-symbols-outlined text-[15px]">close</span>
                                    </button>
                                    <div>
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-action/20 text-slate-authority font-label-micro text-label-micro font-bold mb-2">
                                            <span>BEST VALUE</span>
                                            <span>🏆</span>
                                        </span>
                                        <div
                                            class="h-32 w-full rounded-lg bg-surface-container overflow-hidden mb-3 relative">
                                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                data-alt="Sony WH-1000XM5 wireless noise cancelling headphones resting on a sculpted walnut wooden stand on a warm modern oak desk bathed in soft afternoon sunlight"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBnowdmXMypFWyBq4JQGuv-nO8P6gQLF0a5l14gbVseUeZFGh804aKn3CAg5q32fajYu1jJoAPlEiIA_I721vZFlvQvyrWc-ekB9_PmeG3bidvGtlTlxkHXOp8okbA9V7phAvNQv_rcX7AWvNhRPUpfXvQBh8XSBA2XIa2bpDL32o0Ij3mZdY0EkGfoXYc5r9Ii_ok0fsb0iPEgqGsVAQP2Yr7uUmbiyjPQDwZwQf7eXEGKMZIM_9xX" />
                                            <span
                                                class="absolute bottom-2 left-2 px-2 py-0.5 rounded bg-slate-authority/80 text-canvas-ivory font-label-micro text-label-micro">Over-Ear</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card text-slate-authority font-bold leading-snug line-clamp-2">
                                            Sony WH-1000XM5 Wireless Headphones</h3>
                                        <div class="flex items-center gap-1.5 mt-1.5">
                                            <span class="inline-flex items-center text-amber-action text-[14px]">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                            </span>
                                            <span
                                                class="font-body-small text-body-small font-bold text-slate-authority">4.8</span>
                                            <span
                                                class="font-body-small text-label-micro text-on-surface-variant">(1,420
                                                verified)</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-primary-alpha-10">
                                        <div class="flex items-baseline gap-2 mb-2">
                                            <span
                                                class="font-headline-section text-title-card font-bold text-slate-authority">₹2,499</span>
                                            <span
                                                class="font-body-small text-label-micro text-on-surface-variant line-through">₹3,299</span>
                                            <span
                                                class="font-label-micro text-label-micro font-bold text-status-green">Save
                                                24%</span>
                                        </div>
                                        <button
                                            class="w-full py-2 px-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold flex items-center justify-center gap-1.5 shadow-sm hover:opacity-95 active:scale-[0.99] transition-all"
                                            type="button">
                                            <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                            <span>Add to Cart</span>
                                        </button>
                                    </div>
                                </div>
                                <!-- Col 3: Product B -->
                                <div
                                    class="col-span-3 bg-card-white rounded-xl p-3.5 shadow-sm relative flex flex-col justify-between group transition-all duration-200 hover:shadow-md">
                                    <button
                                        class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-surface-container-high hover:bg-error-container hover:text-error flex items-center justify-center text-on-surface-variant transition-colors"
                                        title="Remove Product">
                                        <span class="material-symbols-outlined text-[15px]">close</span>
                                    </button>
                                    <div>
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-authority/10 text-slate-authority font-label-micro text-label-micro font-bold mb-2">
                                            <span>PORTABILITY PICK</span>
                                            <span>⚡</span>
                                        </span>
                                        <div
                                            class="h-32 w-full rounded-lg bg-surface-container overflow-hidden mb-3 relative">
                                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                data-alt="High precision wireless titanium earbuds resting on a smooth dark reflective stone display surface next to an ambient glowing smartphone"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBZCY7CeJhSpEuGdlniXykXyPebU2FB-SU2Se2b_W4Pt0hu5TBv8jQ4wFbNvFe4cSsabxzKJE_GnxzvQlIfWFfv1fEEv9i4DS1snDyOYXFFeOYbZ5zT3wxtqPmLwLzoP4qXGgZLR9SH7nSrBHo9uF15UYk3kN92zR97xmfY4Ejqro1mSk6H5_x7JvcCgG-9RhL-3DbafFsxw8idSA2ysXiN7qws0UeKUkkmCzx_stMPoIfarzvcTIgd" />
                                            <span
                                                class="absolute bottom-2 left-2 px-2 py-0.5 rounded bg-slate-authority/80 text-canvas-ivory font-label-micro text-label-micro">In-Ear
                                                ANC</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card text-slate-authority font-bold leading-snug line-clamp-2">
                                            Sony WF-1000XM5 ANC Wireless Earbuds</h3>
                                        <div class="flex items-center gap-1.5 mt-1.5">
                                            <span class="inline-flex items-center text-amber-action text-[14px]">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                            </span>
                                            <span
                                                class="font-body-small text-body-small font-bold text-slate-authority">4.7</span>
                                            <span class="font-body-small text-label-micro text-on-surface-variant">(890
                                                reviews)</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-primary-alpha-10">
                                        <div class="flex items-baseline gap-2 mb-2">
                                            <span
                                                class="font-headline-section text-title-card font-bold text-slate-authority">₹3,499</span>
                                            <span
                                                class="font-body-small text-label-micro text-on-surface-variant line-through">₹4,199</span>
                                            <span
                                                class="font-label-micro text-label-micro font-semibold text-status-green">Fast
                                                Dispatch</span>
                                        </div>
                                        <button
                                            class="w-full py-2 px-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold flex items-center justify-center gap-1.5 shadow-sm hover:opacity-95 active:scale-[0.99] transition-all"
                                            type="button">
                                            <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                            <span>Add to Cart</span>
                                        </button>
                                    </div>
                                </div>
                                <!-- Col 4: Product C (airpods / smartwatch companion) -->
                                <div
                                    class="col-span-3 bg-card-white rounded-xl p-3.5 shadow-sm relative flex flex-col justify-between group transition-all duration-200 hover:shadow-md">
                                    <button
                                        class="absolute top-2.5 right-2.5 w-6 h-6 rounded-full bg-surface-container-high hover:bg-error-container hover:text-error flex items-center justify-center text-on-surface-variant transition-colors"
                                        title="Remove Product">
                                        <span class="material-symbols-outlined text-[15px]">close</span>
                                    </button>
                                    <div>
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container-highest text-slate-authority font-label-micro text-label-micro font-bold mb-2">
                                            <span>PREMIUM FLAGSHIP</span>
                                            <span>✦</span>
                                        </span>
                                        <div
                                            class="h-32 w-full rounded-lg bg-surface-container overflow-hidden mb-3 relative">
                                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                data-alt="Apple AirPods Max space gray with breathable acoustic mesh headband and anodized aluminum ear cups on warm clean desk surface"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBJyLkq-M8MMXlunTrTSoiN0nEU5wQke5ZVQZF691IFpGtev2WZyoco2HhXqwZ9yN18KF2pqZJnzCCH4-F1w_4sFTo_vajO2STEmapdM7QM1PCsS0Lla9mIhT5k7GyR5lZ-jcgk2loKAN5rgsUQjXkKvOE5Bd2dKTEcXTuI-Tae2qdOyjDTYSENFjPUK5vM5hEdA9nn4J5PwS4y8SUhfZ9pmb7mDhS6iNnLtH79yZOhSunPm5qMK5ui" />
                                            <span
                                                class="absolute bottom-2 left-2 px-2 py-0.5 rounded bg-slate-authority/80 text-canvas-ivory font-label-micro text-label-micro">Studio
                                                Mesh</span>
                                        </div>
                                        <h3
                                            class="font-title-card text-title-card text-slate-authority font-bold leading-snug line-clamp-2">
                                            Apple AirPods Max (Space Gray Mesh)</h3>
                                        <div class="flex items-center gap-1.5 mt-1.5">
                                            <span class="inline-flex items-center text-amber-action text-[14px]">
                                                <span class="material-symbols-outlined text-[16px]"
                                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                            </span>
                                            <span
                                                class="font-body-small text-body-small font-bold text-slate-authority">4.9</span>
                                            <span
                                                class="font-body-small text-label-micro text-on-surface-variant">(2,110
                                                reviews)</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-primary-alpha-10">
                                        <div class="flex items-baseline gap-2 mb-2">
                                            <span
                                                class="font-headline-section text-title-card font-bold text-slate-authority">₹12,999</span>
                                            <span
                                                class="font-body-small text-label-micro text-on-surface-variant line-through">₹14,900</span>
                                            <span
                                                class="font-label-micro text-label-micro font-semibold text-status-green">Escrow</span>
                                        </div>
                                        <button
                                            class="w-full py-2 px-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold flex items-center justify-center gap-1.5 shadow-sm hover:opacity-95 active:scale-[0.99] transition-all"
                                            type="button">
                                            <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                            <span>Add to Cart</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- SECTION 1: CORE ESSENTIALS -->
                            <div class="section-group" data-category="all essentials">
                                <div class="bg-surface-container-low px-6 py-2.5 flex items-center justify-between">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow uppercase font-bold text-slate-authority tracking-wider flex items-center gap-2">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-amber-action">layers</span>
                                        <span>Group 01: Core Essentials</span>
                                    </span>
                                    <span class="font-label-micro text-label-micro text-on-surface-variant font-mono">4
                                        Metrics Monitored</span>
                                </div>
                                <!-- Row: Brand -->
                                <div
                                    class="spec-row grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">verified</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority">Brand
                                            Heritage</span>
                                    </div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-semibold">
                                        Sony Audio Japan</div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-semibold">
                                        Sony Audio Japan</div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-semibold">
                                        Apple Inc. Cupertino</div>
                                </div>
                                <!-- Row: Form Factor (Diff) -->
                                <div
                                    class="spec-row is-diff grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10 bg-amber-action/[0.03]">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">headphones</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority flex items-center gap-1.5">
                                            <span>Form Factor</span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-action"
                                                title="Specification Difference"></span>
                                        </span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        Over-Ear Circumaural (Soft Fit Leatherette)</div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">In-Ear
                                        Canalphone (Polyurethane Foam Tips)</div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        Over-Ear Acoustic Knit Mesh Canopy</div>
                                </div>
                                <!-- Row: Karma Rating -->
                                <div
                                    class="spec-row grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">social_leaderboard</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority">Seller
                                            Rating &amp; Karma</span>
                                    </div>
                                    <div class="col-span-3 flex items-center gap-2">
                                        <span class="font-body-small text-body-small font-bold text-slate-authority">4.8
                                            ★</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium">98%
                                            Karma</span>
                                    </div>
                                    <div class="col-span-3 flex items-center gap-2">
                                        <span class="font-body-small text-body-small font-bold text-slate-authority">4.7
                                            ★</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium">94%
                                            Karma</span>
                                    </div>
                                    <div class="col-span-3 flex items-center gap-2">
                                        <span class="font-body-small text-body-small font-bold text-slate-authority">4.9
                                            ★</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium">99%
                                            Karma</span>
                                    </div>
                                </div>
                                <!-- Row: Price Tier (Diff) -->
                                <div
                                    class="spec-row is-diff grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10 bg-amber-action/[0.03]">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">payments</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority flex items-center gap-1.5">
                                            <span>Market Price &amp; Index</span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-action"></span>
                                        </span>
                                    </div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority flex items-center gap-1.5">
                                        <span class="font-bold text-status-green">₹2,499</span>
                                        <span
                                            class="px-2 py-0.5 rounded-md bg-status-green/15 text-status-green font-label-micro text-label-micro font-bold">Lowest
                                            Entry</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span class="font-bold text-slate-authority">₹3,499</span>
                                        <span class="text-on-surface-variant text-label-micro"> (Mid-Tier)</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span class="font-bold text-slate-authority">₹12,999</span>
                                        <span class="text-on-surface-variant text-label-micro"> (Luxury Tier)</span>
                                    </div>
                                </div>
                            </div>
                            <!-- SECTION 2: AUDIO & CONNECTIVITY -->
                            <div class="section-group" data-category="all audio">
                                <div class="bg-surface-container-low px-6 py-2.5 flex items-center justify-between">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow uppercase font-bold text-slate-authority tracking-wider flex items-center gap-2">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-amber-action">equalizer</span>
                                        <span>Group 02: Audio Telemetry &amp; Connectivity</span>
                                    </span>
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant font-mono">Verified
                                        Lab Tests</span>
                                </div>
                                <!-- Row: Active Noise Cancellation (Diff) -->
                                <div
                                    class="spec-row is-diff grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10 bg-amber-action/[0.03]">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">noise_aware</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority flex items-center gap-1.5">
                                            <span>ANC Telemetry</span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-action"></span>
                                        </span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <div class="font-semibold text-slate-authority">Dual V1 + QN1 Processor</div>
                                        <div
                                            class="font-label-micro text-label-micro text-status-green flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            <span>99.4% Low Frequency Ambient Block</span>
                                        </div>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <div class="font-semibold text-slate-authority">Integrated V2 Chip</div>
                                        <div
                                            class="font-label-micro text-label-micro text-on-surface-variant flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[14px]">check</span>
                                            <span>Dual Feedback Microphones</span>
                                        </div>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <div class="font-semibold text-slate-authority">Apple H1 Chip (x2)</div>
                                        <div
                                            class="font-label-micro text-label-micro text-on-surface-variant flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[14px]">check</span>
                                            <span>8 ANC Mics + Computational Audio</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row: Transparency Mode -->
                                <div
                                    class="spec-row grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">graphic_eq</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority">Ambient
                                            / Transparency</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span>Adaptive Sound Control (Auto GPS)</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span>Speak-to-Chat &amp; Quick Attention</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span>Computational Natural Ambient Mesh</span>
                                    </div>
                                </div>
                                <!-- Row: Driver Construction (Diff) -->
                                <div
                                    class="spec-row is-diff grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10 bg-amber-action/[0.03]">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">album</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority flex items-center gap-1.5">
                                            <span>Transducer Driver</span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-action"></span>
                                        </span>
                                    </div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-mono">
                                        30mm Carbon Fiber Composite Dome
                                    </div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-mono">
                                        8.4mm Dynamic Driver X
                                    </div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-mono">
                                        40mm Dual Neodymium Ring Driver
                                    </div>
                                </div>
                                <!-- Row: Codecs -->
                                <div
                                    class="spec-row grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">memory</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority">Codecs
                                            &amp; Audio Formats</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">LDAC
                                        (990kbps), AAC, SBC, DSEE Extreme</div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">LDAC,
                                        LC3 Next-Gen, AAC, SBC</div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">AAC,
                                        SBC, Apple Spatial Audio Head Tracking</div>
                                </div>
                            </div>
                            <!-- SECTION 3: BATTERY & HARDWARE -->
                            <div class="section-group" data-category="all battery">
                                <div class="bg-surface-container-low px-6 py-2.5 flex items-center justify-between">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow uppercase font-bold text-slate-authority tracking-wider flex items-center gap-2">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-amber-action">battery_charging_full</span>
                                        <span>Group 03: Battery Endurance &amp; Ergonomics</span>
                                    </span>
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant font-mono">Continuous
                                        Playback</span>
                                </div>
                                <!-- Row: Battery Runtime (Diff Winner) -->
                                <div
                                    class="spec-row is-diff grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10 bg-amber-action/[0.03]">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">timer</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority flex items-center gap-1.5">
                                            <span>Battery (ANC Active)</span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-action"></span>
                                        </span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small">
                                        <div
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-action/20 text-slate-authority font-bold font-mono">
                                            <span>30 Hours Continuous</span>
                                            <span
                                                class="material-symbols-outlined text-[16px] text-amber-action">bolt</span>
                                        </div>
                                        <div class="font-label-micro text-label-micro text-on-surface-variant mt-1">40
                                            Hours with ANC Disabled</div>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <div class="font-mono font-semibold">8 Hours Standalone</div>
                                        <div class="font-label-micro text-label-micro text-on-surface-variant mt-1">24
                                            Hours combined with Qi Case</div>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <div class="font-mono font-semibold">20 Hours Standalone</div>
                                        <div class="font-label-micro text-label-micro text-on-surface-variant mt-1">
                                            Smart Case ultra-low power standby</div>
                                    </div>
                                </div>
                                <!-- Row: Quick Charge -->
                                <div
                                    class="spec-row grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">speed</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority">Quick
                                            Burst Charge</span>
                                    </div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-mono">
                                        3 mins = 3 hours playback</div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-mono">
                                        3 mins = 60 mins playback</div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-mono">
                                        5 mins = 1.5 hours playback</div>
                                </div>
                                <!-- Row: Charging Interface -->
                                <div
                                    class="spec-row grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">usb</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority">Interface
                                            &amp; Wireless</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">USB
                                        Type-C Fast PD</div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">USB-C +
                                        Qi Wireless Charging</div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        Lightning / USB-C Reversible</div>
                                </div>
                                <!-- Row: Net Weight (Diff) -->
                                <div
                                    class="spec-row is-diff grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10 bg-amber-action/[0.03]">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">scale</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority flex items-center gap-1.5">
                                            <span>Hardware Weight</span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-action"></span>
                                        </span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span class="font-bold text-slate-authority font-mono">250g</span>
                                        <span
                                            class="font-label-micro text-label-micro text-status-green ml-1 font-medium">(Comfort
                                            Tuned)</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span class="font-bold text-slate-authority font-mono">5.9g</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant ml-1">(Per
                                            earpiece)</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span class="font-bold text-slate-authority font-mono">384.8g</span>
                                        <span
                                            class="font-label-micro text-label-micro text-on-surface-variant ml-1">(Stainless
                                            &amp; Mesh)</span>
                                    </div>
                                </div>
                            </div>
                            <!-- SECTION 4: ECOSYSTEM & WARRANTY -->
                            <div class="section-group" data-category="all warranty">
                                <div class="bg-surface-container-low px-6 py-2.5 flex items-center justify-between">
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow uppercase font-bold text-slate-authority tracking-wider flex items-center gap-2">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-amber-action">security</span>
                                        <span>Group 04: Ecosystem &amp; Escrow Guarantee</span>
                                    </span>
                                    <span
                                        class="font-label-micro text-label-micro text-on-surface-variant font-mono">Bazaario
                                        Buyer Shield</span>
                                </div>
                                <!-- Row: Multipoint -->
                                <div
                                    class="spec-row grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">devices</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority">Multi-Device
                                            Pairing</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">Dual
                                        Device Multipoint Seamless Switch</div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        Bluetooth 5.3 Multipoint Sync</div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">iCloud
                                        Instant Switch + Audio Sharing</div>
                                </div>
                                <!-- Row: Ingress Protection (Diff) -->
                                <div
                                    class="spec-row is-diff grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10 bg-amber-action/[0.03]">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">water_drop</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority flex items-center gap-1.5">
                                            <span>Ingress Rating</span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-action"></span>
                                        </span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">Desk /
                                        Indoor Weather Shielded</div>
                                    <div
                                        class="col-span-3 font-body-small text-body-small text-slate-authority font-semibold text-status-green flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px]">verified</span>
                                        <span>IPX4 Sweat &amp; Rain Resistant</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        Acoustic Mesh Moisture Gasket</div>
                                </div>
                                <!-- Row: Escrow Warranty -->
                                <div
                                    class="spec-row grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">gavel</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority">Bazaario
                                            Escrow Warranty</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span class="font-semibold">1 Year Merchant Replacement</span>
                                        <span
                                            class="block font-label-micro text-label-micro text-on-surface-variant">Funds
                                            locked until 48hr burn-in passed</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span class="font-semibold">1 Year Brand Warranty</span>
                                        <span
                                            class="block font-label-micro text-label-micro text-on-surface-variant">Official
                                            Sony India Service Centers</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <span class="font-semibold">1 Year AppleCare Eligible</span>
                                        <span
                                            class="block font-label-micro text-label-micro text-on-surface-variant">Escrow
                                            Serial Verification Passed</span>
                                    </div>
                                </div>
                                <!-- Row: Seller Provenance -->
                                <div
                                    class="spec-row grid grid-cols-12 items-center p-4 hover:bg-surface-container-low/40 transition-colors border-b border-primary-alpha-10">
                                    <div class="col-span-3 flex items-center gap-2 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[18px]">storefront</span>
                                        <span
                                            class="font-body-small text-body-small font-medium text-slate-authority">Merchant
                                            Provenance</span>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <div class="font-semibold text-slate-authority">AudioCraft Official</div>
                                        <div
                                            class="font-label-micro text-label-micro text-status-green flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[14px]">shield</span>
                                            <span>★ 4.97 • 100% Escrow Clean</span>
                                        </div>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <div class="font-semibold text-slate-authority">SonicWave Labs</div>
                                        <div
                                            class="font-label-micro text-label-micro text-on-surface-variant flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[14px]">shield</span>
                                            <span>★ 4.85 • Top Rated Stall</span>
                                        </div>
                                    </div>
                                    <div class="col-span-3 font-body-small text-body-small text-slate-authority">
                                        <div class="font-semibold text-slate-authority">Apex Cellular Collective</div>
                                        <div
                                            class="font-label-micro text-label-micro text-status-green flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[14px]">shield</span>
                                            <span>★ 4.99 • Platinum Merchant</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Bottom Sticky Action Strip -->
                            <div
                                class="grid grid-cols-12 items-center p-4 bg-surface-container-low/90 backdrop-blur-md">
                                <div class="col-span-3 flex flex-col justify-center pr-4">
                                    <span
                                        class="font-label-micro text-label-micro font-mono uppercase text-on-surface-variant">Instant
                                        Checkout</span>
                                    <span class="font-title-card text-title-card font-bold text-slate-authority">Make
                                        Your Decision</span>
                                </div>
                                <div class="col-span-3 pr-2">
                                    <button
                                        class="w-full py-2.5 px-4 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold flex items-center justify-center gap-2 shadow-sm hover:opacity-95 transition-all"
                                        type="button">
                                        <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                        <span>Get XM5 (₹2,499)</span>
                                    </button>
                                </div>
                                <div class="col-span-3 pr-2">
                                    <button
                                        class="w-full py-2.5 px-4 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold flex items-center justify-center gap-2 shadow-sm hover:opacity-95 transition-all"
                                        type="button">
                                        <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                        <span>Get Buds (₹3,499)</span>
                                    </button>
                                </div>
                                <div class="col-span-3">
                                    <button
                                        class="w-full py-2.5 px-4 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold flex items-center justify-center gap-2 shadow-sm hover:opacity-95 transition-all"
                                        type="button">
                                        <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                        <span>Get Max (₹12,999)</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 4th Slot Placeholder CTA Banner -->
                <div
                    class="mt-6 p-6 rounded-2xl bg-card-white shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-slate-authority">
                            <span class="material-symbols-outlined text-[26px]">add_circle_outline</span>
                        </div>
                        <div>
                            <h2 class="font-title-card text-title-card font-bold text-slate-authority">Compare a 4th
                                device side-by-side</h2>
                            <p class="font-body-small text-body-small text-on-surface-variant">Add smartwatches,
                                alternative flagships, or studio monitors to this active session.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="relative flex-1 md:w-72">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">search</span>
                            <input
                                class="w-full pl-9 pr-3 py-2 rounded-xl bg-surface-container-low text-body-small font-body-small text-slate-authority placeholder:text-on-surface-variant/60 focus:outline-none focus:ring-1 focus:ring-amber-action"
                                placeholder="Search Sony, Bose, Apple..." type="text" />
                        </div>
                        <button
                            class="px-4 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small hover:bg-slate-authority/90 whitespace-nowrap"
                            type="button">
                            Add Slot
                        </button>
                    </div>
                </div>
                <!-- AI Smart Verdict Authority Container (3D Dimensional Dark Glass) -->
                <section
                    class="mt-10 rounded-2xl bg-slate-authority text-canvas-ivory p-8 sm:p-10 shadow-[0_12px_40px_rgba(15,23,42,0.25)] relative overflow-hidden">
                    <!-- Ambient Glow Decorator -->
                    <div
                        class="absolute top-0 right-0 w-96 h-96 bg-amber-action/10 rounded-full blur-3xl pointer-events-none -mr-20 -mt-20">
                    </div>
                    <div
                        class="absolute bottom-0 left-1/3 w-64 h-64 bg-status-green/10 rounded-full blur-2xl pointer-events-none -mb-20">
                    </div>
                    <div class="relative z-10 space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div
                                    class="inline-flex items-center gap-2 font-label-micro text-label-micro uppercase font-mono tracking-widest text-amber-action">
                                    <span class="material-symbols-outlined text-[16px]">psychology</span>
                                    <span>Bazaario AI Hardware Verdict • Concierge Engine 4.2</span>
                                </div>
                                <h2
                                    class="font-headline-section text-display-hero-mobile font-bold tracking-tight mt-1 text-canvas-ivory">
                                    Smart Analysis: Which product matches your audio profile?
                                </h2>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-card-white/10 text-canvas-ivory font-label-micro text-label-micro backdrop-blur-sm">
                                    <span class="w-2 h-2 rounded-full bg-status-green"></span>
                                    <span>Escrow Model Synced</span>
                                </span>
                            </div>
                        </div>
                        <!-- 3 Quick Verdict Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                            <!-- Card 1 -->
                            <div
                                class="bg-card-white/5 backdrop-blur-md rounded-xl p-5 hover:bg-card-white/10 transition-colors flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <span
                                            class="font-label-micro text-label-micro text-amber-action font-mono uppercase font-bold">Best
                                            Everyday Value</span>
                                        <span
                                            class="material-symbols-outlined text-amber-action text-[20px]">recommend</span>
                                    </div>
                                    <h3 class="font-title-card text-title-card font-bold text-canvas-ivory mb-1.5">Sony
                                        WH-1000XM5</h3>
                                    <p class="font-body-small text-body-small text-canvas-ivory/80 leading-relaxed">
                                        Unmatched 30-hour battery life and class-leading noise cancellation. At ₹2,499,
                                        this is the definitive price-to-performance champion on the market today.
                                    </p>
                                </div>
                                <div
                                    class="mt-4 pt-3 border-t border-canvas-ivory/10 flex items-center justify-between">
                                    <span class="font-label-micro text-label-micro text-canvas-ivory/60">Value
                                        Score</span>
                                    <span class="font-mono text-body-small font-bold text-status-green">9.8 / 10</span>
                                </div>
                            </div>
                            <!-- Card 2 -->
                            <div
                                class="bg-card-white/5 backdrop-blur-md rounded-xl p-5 hover:bg-card-white/10 transition-colors flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <span
                                            class="font-label-micro text-label-micro text-amber-action font-mono uppercase font-bold">Best
                                            On-The-Go</span>
                                        <span
                                            class="material-symbols-outlined text-amber-action text-[20px]">directions_run</span>
                                    </div>
                                    <h3 class="font-title-card text-title-card font-bold text-canvas-ivory mb-1.5">Sony
                                        Buds WF-1000XM5</h3>
                                    <p class="font-body-small text-body-small text-canvas-ivory/80 leading-relaxed">
                                        Ultra-compact IPX4 build for active workouts and intense commutes. Features
                                        seamless Qi wireless charging and superior wind-reduction microphones.
                                    </p>
                                </div>
                                <div
                                    class="mt-4 pt-3 border-t border-canvas-ivory/10 flex items-center justify-between">
                                    <span class="font-label-micro text-label-micro text-canvas-ivory/60">Portability
                                        Score</span>
                                    <span class="font-mono text-body-small font-bold text-amber-action">9.5 / 10</span>
                                </div>
                            </div>
                            <!-- Card 3 -->
                            <div
                                class="bg-card-white/5 backdrop-blur-md rounded-xl p-5 hover:bg-card-white/10 transition-colors flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <span
                                            class="font-label-micro text-label-micro text-amber-action font-mono uppercase font-bold">Ecosystem
                                            Immersion</span>
                                        <span
                                            class="material-symbols-outlined text-amber-action text-[20px]">auto_awesome</span>
                                    </div>
                                    <h3 class="font-title-card text-title-card font-bold text-canvas-ivory mb-1.5">Apple
                                        AirPods Max</h3>
                                    <p class="font-body-small text-body-small text-canvas-ivory/80 leading-relaxed">
                                        Industry benchmark spatial tracking and premium aluminum acoustic chambers.
                                        Ideal if you exist primarily within the Apple macOS &amp; iOS hardware workflow.
                                    </p>
                                </div>
                                <div
                                    class="mt-4 pt-3 border-t border-canvas-ivory/10 flex items-center justify-between">
                                    <span class="font-label-micro text-label-micro text-canvas-ivory/60">Craftsmanship
                                        Score</span>
                                    <span class="font-mono text-body-small font-bold text-canvas-ivory">9.9 / 10</span>
                                </div>
                            </div>
                        </div>
                        <!-- AI Actions -->
                        <div
                            class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-canvas-ivory/10">
                            <div class="flex items-center gap-2">
                                <span
                                    class="material-symbols-outlined text-[20px] text-amber-action">verified_user</span>
                                <span class="font-body-small text-body-small text-canvas-ivory/80">All AI telemetry
                                    corroborated by verified Bazaario buyers and lab microphones.</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <button
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-card-white/10 hover:bg-card-white/20 text-canvas-ivory font-button-text text-body-small transition-colors backdrop-blur-sm"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                                    <span>Export Spec PDF</span>
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold hover:opacity-95 active:scale-[0.99] transition-all"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                                    <span>Ask AI to Personalize Recommendation</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
            <script>
                (function () {
                    // Interactive filter buttons
                    const pills = document.querySelectorAll('.filter-pill');
                    const specRows = document.querySelectorAll('.spec-row');
                    const sectionGroups = document.querySelectorAll('.section-group');

                    pills.forEach(pill => {
                        pill.addEventListener('click', () => {
                            pills.forEach(p => {
                                p.classList.remove('bg-slate-authority', 'text-canvas-ivory');
                                p.classList.add('bg-surface-container-low', 'text-on-surface-variant');
                            });
                            pill.classList.remove('bg-surface-container-low', 'text-on-surface-variant');
                            pill.classList.add('bg-slate-authority', 'text-canvas-ivory');

                            const filter = pill.getAttribute('data-filter');

                            if (filter === 'all') {
                                specRows.forEach(row => row.classList.remove('hidden'));
                                sectionGroups.forEach(grp => grp.classList.remove('hidden'));
                            } else if (filter === 'diff') {
                                specRows.forEach(row => {
                                    if (row.classList.contains('is-diff')) {
                                        row.classList.remove('hidden');
                                    } else {
                                        row.classList.add('hidden');
                                    }
                                });
                                sectionGroups.forEach(grp => grp.classList.remove('hidden'));
                            } else {
                                // Category filter
                                sectionGroups.forEach(grp => {
                                    const cat = grp.getAttribute('data-category');
                                    if (cat && cat.includes(filter)) {
                                        grp.classList.remove('hidden');
                                    } else {
                                        grp.classList.add('hidden');
                                    }
                                });
                                specRows.forEach(row => row.classList.remove('hidden'));
                            }
                        });
                    });

                    // Clear button feedback
                    const clearBtn = document.getElementById('clearBtn');
                    if (clearBtn) {
                        clearBtn.addEventListener('click', () => {
                            if (confirm('Are you sure you want to clear this comparison matrix?')) {
                                window.location.reload();
                            }
                        });
                    }
                })();
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