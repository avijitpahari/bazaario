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
                        data-path="auctions" href="#">Auctions</a><a aria-current="page"
                        class="px-3 py-2 transition-colors bg-surface-container text-on-surface font-semibold rounded-lg"
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
            <div
                class="max-w-container-max mx-auto px-gutter-md w-full pt-6 pb-section-final-bottom flex flex-col gap-6">
                <!-- Sub-Header & Breadcrumb Bar -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 py-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <a class="bg-card-white/90 backdrop-blur-md shadow-sm hover:bg-surface-container text-slate-authority font-body-small text-body-small font-medium px-3.5 py-1.5 rounded-full inline-flex items-center gap-1.5 transition-colors"
                            data-path="user-account" href="#">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                            <span>Back to Account</span>
                        </a>
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-authority/20"></span>
                        <nav
                            class="flex items-center gap-2 font-label-micro text-label-micro uppercase text-on-surface-variant tracking-wider">
                            <a class="hover:text-slate-authority transition-colors" data-path="home" href="#">Home</a>
                            <span>/</span>
                            <a class="hover:text-slate-authority transition-colors" data-path="user-account"
                                href="#">Account</a>
                            <span>/</span>
                            <span class="text-slate-authority font-semibold">Live Auctions</span>
                        </nav>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <div
                            class="bg-card-white/90 backdrop-blur-md shadow-sm px-3.5 py-1.5 rounded-full inline-flex items-center gap-2 font-label-micro text-label-micro text-slate-authority font-medium">
                            <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                            <span class="tracking-wide">ESCROW VERIFIED BUYER</span>
                            <span class="text-on-surface-variant/40">•</span>
                            <span class="text-on-surface-variant font-mono">VAULT #ESC-9041</span>
                            <span class="text-on-surface-variant/40">•</span>
                            <span class="bg-surface-container px-1.5 py-0.5 rounded text-slate-authority">UID:
                                BZ-891-IN</span>
                        </div>
                        <div
                            class="bg-slate-authority text-canvas-ivory px-3.5 py-1.5 rounded-full shadow-sm inline-flex items-center gap-2 font-label-micro text-label-micro font-medium">
                            <span class="material-symbols-outlined text-[15px] text-amber-action">lock</span>
                            <span>Vault: <span class="font-mono font-bold text-card-white">₹60,500.00</span></span>
                            <button aria-label="Vault Settings"
                                class="hover:text-amber-action transition-colors ml-0.5 flex items-center">
                                <span class="material-symbols-outlined text-[14px]">settings</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Page Header -->
                <div
                    class="bg-card-white/70 backdrop-blur-md rounded-2xl p-6 md:p-8 shadow-sm flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="flex flex-col gap-2 max-w-3xl">
                        <div
                            class="flex items-center gap-2 font-label-eyebrow text-label-eyebrow uppercase text-amber-action font-semibold tracking-wider">
                            <span class="material-symbols-outlined text-[16px]">gavel</span>
                            <span>Account Management • Real-Time Floor Auctions &amp; Escrow Bidding</span>
                        </div>
                        <h1
                            class="font-display-hero text-display-hero md:text-display-hero text-slate-authority tracking-tight leading-none">
                            Live Auctions</h1>
                        <p class="font-body-regular text-body-regular text-on-surface-variant max-w-2xl mt-1">
                            Bid on authenticated market rarities before the final block closes. Every bid is secured in
                            cryptographic escrow with instant, zero-penalty unlock upon outbid.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            class="bg-card-white shadow-sm hover:bg-surface-container-low text-slate-authority font-button-text text-body-small px-4 py-2.5 rounded-xl inline-flex items-center gap-2 transition-all"
                            type="button">
                            <span class="material-symbols-outlined text-[18px] text-amber-action">bolt</span>
                            <span>Auto-Bid Settings</span>
                        </button>
                        <button
                            class="bg-card-white shadow-sm hover:bg-surface-container-low text-slate-authority font-button-text text-body-small px-4 py-2.5 rounded-xl inline-flex items-center gap-2 transition-all"
                            type="button">
                            <span
                                class="material-symbols-outlined text-[18px] text-on-surface-variant">history_edu</span>
                            <span>My Bid History (14)</span>
                        </button>
                    </div>
                </div>
                <!-- Bidding Stats Highlight Strip -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-card-white/80 backdrop-blur-md rounded-xl p-4 shadow-sm flex items-center gap-4">
                        <div
                            class="w-11 h-11 rounded-lg bg-surface-container flex items-center justify-center text-slate-authority">
                            <span class="material-symbols-outlined text-[24px]">front_hand</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span
                                class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Active
                                Floor Bids</span>
                            <div class="flex items-baseline gap-2 mt-0.5">
                                <span class="font-headline-section text-title-card font-bold text-slate-authority">4
                                    Items</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant font-mono">|
                                    Locked: ₹42,500</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-card-white/80 backdrop-blur-md rounded-xl p-4 shadow-sm flex items-center gap-4">
                        <div
                            class="w-11 h-11 rounded-lg bg-status-green/10 flex items-center justify-center text-status-green">
                            <span class="material-symbols-outlined text-[24px]">workspace_premium</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span
                                class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Highest
                                Position</span>
                            <div class="flex items-baseline gap-2 mt-0.5">
                                <span class="font-headline-section text-title-card font-bold text-status-green">2
                                    Leading</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant truncate">Leica
                                    M3 &amp; Rolex</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-card-white/80 backdrop-blur-md rounded-xl p-4 shadow-sm flex items-center gap-4">
                        <div
                            class="w-11 h-11 rounded-lg bg-amber-action/15 flex items-center justify-center text-slate-authority">
                            <span
                                class="material-symbols-outlined text-[24px] text-amber-action animate-bounce">alarm</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span
                                class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Next
                                Ending Lot</span>
                            <div class="flex items-baseline gap-2 mt-0.5">
                                <span
                                    class="font-headline-section text-title-card font-bold text-error font-mono">00:45:12</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant truncate">Leica
                                    35mm f/2</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Live Floor Filter & Status Tabs -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            class="bg-slate-authority text-canvas-ivory shadow-md font-button-text text-body-small px-4 py-2 rounded-full flex items-center gap-2 transition-transform active:scale-95">
                            <span class="w-2 h-2 rounded-full bg-error animate-ping"></span>
                            <span>Live Now (8)</span>
                        </button>
                        <button
                            class="bg-card-white hover:bg-surface-container-low text-slate-authority shadow-sm font-button-text text-body-small px-4 py-2 rounded-full flex items-center gap-1.5 transition-colors">
                            <span class="material-symbols-outlined text-[16px] text-amber-action">schedule</span>
                            <span>Ending Soon (3)</span>
                        </button>
                        <button
                            class="bg-card-white hover:bg-surface-container-low text-slate-authority shadow-sm font-button-text text-body-small px-4 py-2 rounded-full flex items-center gap-1.5 transition-colors">
                            <span
                                class="material-symbols-outlined text-[16px] text-on-surface-variant">auto_awesome</span>
                            <span>Newly Listed (5)</span>
                        </button>
                        <button
                            class="bg-card-white hover:bg-surface-container-low text-slate-authority shadow-sm font-button-text text-body-small px-4 py-2 rounded-full flex items-center gap-1.5 transition-colors">
                            <span
                                class="material-symbols-outlined text-[16px] text-on-surface-variant">my_location</span>
                            <span>My Active Bids (4)</span>
                        </button>
                        <button
                            class="bg-card-white hover:bg-surface-container-low text-slate-authority shadow-sm font-button-text text-body-small px-4 py-2 rounded-full flex items-center gap-1.5 transition-colors">
                            <span
                                class="material-symbols-outlined text-[16px] text-on-surface-variant">emoji_events</span>
                            <span>Won Auctions (2)</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-3 self-end md:self-auto">
                        <div
                            class="flex items-center gap-1.5 font-label-micro text-label-micro text-status-green bg-status-green/10 px-2.5 py-1 rounded-full font-mono">
                            <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                            <span>Live Socket: 0.2s sync</span>
                        </div>
                        <div class="relative bg-card-white shadow-sm rounded-full px-3 py-1.5 flex items-center">
                            <span
                                class="material-symbols-outlined text-[16px] text-on-surface-variant mr-1.5">sort</span>
                            <select aria-label="Sort auctions"
                                class="bg-transparent font-body-small text-body-small font-medium text-slate-authority focus:outline-none cursor-pointer pr-3">
                                <option>Ending Soonest</option>
                                <option>Highest Value</option>
                                <option>Most Active (Bids)</option>
                                <option>Lowest Starting</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- LIVE AUCTION CARDS GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Card 1: Vintage Submariner Watch -->
                    <article
                        class="group bg-card-white/95 backdrop-blur-md rounded-2xl p-5 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Media Showcase Container -->
                            <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-authority/5">
                                <img alt="Vintage Rolex Submariner luxury mechanical watch on velvet cushion"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1VJoPjRTEqfbGAzcHNyPgWbDYTZKUrZ-rzqc9TdrjmxYIbj5RJborlQzjSqkNdh-2ILz6xU8UyRDtQOGJN42cZrrKG27JZfH89-GTnDeam4oBk0oNQ0a-EUIAh70N9PVOdnw2vMtKlOP4-1AcrnfMMU7Tn8a70ght1BYiqY3u4gNWF0zHI7mJbu-7q7P6lOJ2hgaczp37cmFaccLsP__8tRqdMpllWz3N44LPV8gEpyvlhtaqn1vBuKRBY" />
                                <!-- Badges Overlay -->
                                <div
                                    class="absolute top-3 left-3 flex items-center gap-1.5 bg-slate-authority/80 backdrop-blur-md text-canvas-ivory px-2.5 py-1 rounded-full font-label-micro text-label-micro font-semibold shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error animate-ping"></span>
                                    <span>LIVE FLOOR</span>
                                </div>
                                <div class="absolute top-3 right-3 flex items-center gap-1.5">
                                    <span
                                        class="bg-card-white/90 backdrop-blur-md text-slate-authority font-label-micro text-label-micro font-bold px-2 py-1 rounded-md shadow-sm">12
                                        Bids</span>
                                    <button aria-label="Save to Wishlist"
                                        class="w-7 h-7 rounded-full bg-card-white/90 backdrop-blur-md flex items-center justify-center text-slate-authority hover:text-amber-action shadow-sm transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Product Details -->
                            <div class="mt-4 flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold text-on-surface-variant uppercase tracking-wider">Horology
                                        • Certified Authentic</span>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green bg-status-green/10 px-2 py-0.5 rounded font-mono">Lot
                                        #HZ-402</span>
                                </div>
                                <h2
                                    class="font-title-card text-title-card text-slate-authority group-hover:text-amber-action transition-colors truncate">
                                    Vintage Submariner Mechanical Watch
                                </h2>
                                <div
                                    class="flex items-center gap-1.5 font-body-small text-body-small text-on-surface-variant">
                                    <span>Timepiece Atelier</span>
                                    <span class="text-on-surface-variant/40">•</span>
                                    <span
                                        class="inline-flex items-center text-amber-action font-semibold text-label-micro">
                                        <span class="material-symbols-outlined text-[14px]">star</span> 4.9
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Bidding Matrix -->
                        <div class="mt-5 pt-4 bg-surface-container-low/70 rounded-xl p-3.5 flex flex-col gap-3">
                            <div class="flex items-end justify-between">
                                <div class="flex flex-col">
                                    <span
                                        class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Current
                                        Escrow Bid</span>
                                    <span
                                        class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹8,500</span>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green inline-flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[12px]">check_circle</span> Reserve
                                        Met
                                    </span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Closes
                                        In</span>
                                    <div
                                        class="font-mono text-label-eyebrow font-bold text-error bg-error-container/60 text-on-error-container px-2.5 py-1 rounded-md inline-flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[14px]">timer</span>
                                        <span>02:31:44</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro bg-card-white px-2.5 py-1 rounded-lg">
                                <span>Next increment:</span>
                                <span class="font-mono font-semibold text-slate-authority">Min ₹8,750 (+₹250)</span>
                            </div>
                            <button
                                class="w-full bg-amber-action hover:opacity-95 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.99]"
                                type="button">
                                <span>Place Bid</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </button>
                        </div>
                    </article>
                    <!-- Card 2: GeForce RTX 4090 OC GPU -->
                    <article
                        class="group bg-card-white/95 backdrop-blur-md rounded-2xl p-5 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Media Showcase Container -->
                            <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-authority/5">
                                <img alt="High-end graphics card Nvidia GeForce RTX 4090 gaming GPU with glowing RGB accents"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDuyZYkr6bvudqoQx19Vft_TBKEJ27nGwZBcz0VES9gl170zWfXcwUyRacOfV08msqXVqvZWc_0Y2pdiKo8he2nfHr-RkcRvoDf0Mz59cR9YDz_k3TcEdRr1vlewOnXgspxpfb2_IfrXRhv2NmlF2M1KLlizLH9_Vl9YMFH-XIXBtd7-gLeFn1RmRWboFRDvucfcKLa7fGUpS1gADS3_kM6J66E8rMfYvKXz76LrB9B5AX6wQbLfMFQ" />
                                <!-- Badges Overlay -->
                                <div
                                    class="absolute top-3 left-3 flex items-center gap-1.5 bg-slate-authority/80 backdrop-blur-md text-canvas-ivory px-2.5 py-1 rounded-full font-label-micro text-label-micro font-semibold shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-error animate-ping"></span>
                                    <span>LIVE FLOOR</span>
                                </div>
                                <div class="absolute top-3 right-3 flex items-center gap-1.5">
                                    <span
                                        class="bg-card-white/90 backdrop-blur-md text-error font-label-micro text-label-micro font-bold px-2 py-1 rounded-md shadow-sm">28
                                        Bids (Hot 🔥)</span>
                                    <button aria-label="Save to Wishlist"
                                        class="w-7 h-7 rounded-full bg-card-white/90 backdrop-blur-md flex items-center justify-center text-slate-authority hover:text-amber-action shadow-sm transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Product Details -->
                            <div class="mt-4 flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold text-on-surface-variant uppercase tracking-wider">Hardware
                                        • Factory Sealed</span>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green bg-status-green/10 px-2 py-0.5 rounded font-mono">Lot
                                        #GPU-910</span>
                                </div>
                                <h2
                                    class="font-title-card text-title-card text-slate-authority group-hover:text-amber-action transition-colors truncate">
                                    GeForce RTX 4090 OC 24GB Gaming GPU
                                </h2>
                                <div
                                    class="flex items-center gap-1.5 font-body-small text-body-small text-on-surface-variant">
                                    <span>SiliconVault Official</span>
                                    <span class="text-on-surface-variant/40">•</span>
                                    <span
                                        class="inline-flex items-center text-amber-action font-semibold text-label-micro">
                                        <span class="material-symbols-outlined text-[14px]">star</span> 5.0
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Bidding Matrix -->
                        <div class="mt-5 pt-4 bg-surface-container-low/70 rounded-xl p-3.5 flex flex-col gap-3">
                            <div class="flex items-end justify-between">
                                <div class="flex flex-col">
                                    <span
                                        class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Current
                                        Escrow Bid</span>
                                    <span
                                        class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹32,000</span>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green inline-flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[12px]">check_circle</span> Reserve
                                        Met
                                    </span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Closes
                                        In</span>
                                    <div
                                        class="font-mono text-label-eyebrow font-bold text-slate-authority bg-surface-container px-2.5 py-1 rounded-md inline-flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[14px]">timer</span>
                                        <span>05:12:20</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro bg-card-white px-2.5 py-1 rounded-lg">
                                <span>Next increment:</span>
                                <span class="font-mono font-semibold text-slate-authority">Min ₹32,500 (+₹500)</span>
                            </div>
                            <button
                                class="w-full bg-amber-action hover:opacity-95 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.99]"
                                type="button">
                                <span>Place Bid</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </button>
                        </div>
                    </article>
                    <!-- Card 3: Vintage Leica M3 Camera -->
                    <article
                        class="group bg-card-white/95 backdrop-blur-md rounded-2xl p-5 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Media Showcase Container -->
                            <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-authority/5">
                                <img alt="Vintage Leica 35mm film rangefinder camera with brass patina"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida/AEtjO1WtXge9GPbqerXxtDR81FmoO_hfHqYgmqlaYYUwc2Yh_MefBJoICSP9w7Wb0Cqr6tyKGrqQHUndgUWVoT8ggWFORDtiLGGzfQHiKXsLNWSKSi8IniszOYx4fofXjH1O2Fr6JH_kTpLyAmT-GYlGMOKE3SeqInPG0mwsWk2CEDPZEvzeA4FChdtZ_EWJd2NUmm0zgSRZojYH02eszXaZDsXknosVC-J0Vy91v025yswoZN2tqWyLVRPaitw" />
                                <!-- Badges Overlay -->
                                <div
                                    class="absolute top-3 left-3 flex items-center gap-1.5 bg-error text-card-white px-2.5 py-1 rounded-full font-label-micro text-label-micro font-semibold shadow-sm animate-pulse">
                                    <span class="material-symbols-outlined text-[12px]">flash_on</span>
                                    <span>ENDING SOON</span>
                                </div>
                                <div class="absolute top-3 right-3 flex items-center gap-1.5">
                                    <span
                                        class="bg-card-white/90 backdrop-blur-md text-slate-authority font-label-micro text-label-micro font-bold px-2 py-1 rounded-md shadow-sm">7
                                        Bids</span>
                                    <button aria-label="Save to Wishlist"
                                        class="w-7 h-7 rounded-full bg-card-white/90 backdrop-blur-md flex items-center justify-center text-slate-authority hover:text-amber-action shadow-sm transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Product Details -->
                            <div class="mt-4 flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-label-micro text-label-micro font-semibold text-on-surface-variant uppercase tracking-wider">Collectible
                                        Optics • Mint Archival</span>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green bg-status-green/10 px-2 py-0.5 rounded font-mono">Lot
                                        #LC-183</span>
                                </div>
                                <h2
                                    class="font-title-card text-title-card text-slate-authority group-hover:text-amber-action transition-colors truncate">
                                    Vintage Leica M 35mm f/2 Summicron
                                </h2>
                                <div
                                    class="flex items-center gap-1.5 font-body-small text-body-small text-on-surface-variant">
                                    <span>Optical Rarities</span>
                                    <span class="text-on-surface-variant/40">•</span>
                                    <span
                                        class="inline-flex items-center text-amber-action font-semibold text-label-micro">
                                        <span class="material-symbols-outlined text-[14px]">star</span> 4.8
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Bidding Matrix -->
                        <div class="mt-5 pt-4 bg-surface-container-low/70 rounded-xl p-3.5 flex flex-col gap-3">
                            <div class="flex items-end justify-between">
                                <div class="flex flex-col">
                                    <span
                                        class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Current
                                        Escrow Bid</span>
                                    <span
                                        class="font-display-hero text-headline-section font-bold text-slate-authority leading-tight">₹18,000</span>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green inline-flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[12px]">check_circle</span> Reserve
                                        Met
                                    </span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="font-label-micro text-label-micro uppercase tracking-wider text-on-surface-variant">Closes
                                        In</span>
                                    <div
                                        class="font-mono text-label-eyebrow font-bold text-error bg-error-container/60 text-on-error-container px-2.5 py-1 rounded-md inline-flex items-center gap-1 mt-1 animate-pulse">
                                        <span class="material-symbols-outlined text-[14px]">alarm</span>
                                        <span>00:45:12</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro bg-card-white px-2.5 py-1 rounded-lg">
                                <span>Next increment:</span>
                                <span class="font-mono font-semibold text-slate-authority">Min ₹18,500 (+₹500)</span>
                            </div>
                            <button
                                class="w-full bg-amber-action hover:opacity-95 text-slate-authority font-button-text text-body-small font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.99]"
                                type="button">
                                <span>Place Bid</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </button>
                        </div>
                    </article>
                </div>
                <!-- Quick Escrow Bid Drawer Bar -->
                <div
                    class="bg-card-white/90 backdrop-blur-md rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-full bg-amber-action/20 text-slate-authority flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px] text-amber-action">flash_auto</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-button-text text-body-small font-semibold text-slate-authority">Quick
                                Escrow Bidding Enabled</span>
                            <span class="font-body-small text-body-small text-on-surface-variant">Vault #ESC-9041 ready
                                for 1-click biometric authorization with instant outbid return.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-stretch sm:self-auto justify-end">
                        <span
                            class="font-label-micro text-label-micro font-mono text-status-green bg-status-green/10 px-2 py-1 rounded">SSL
                            256-bit Encrypted</span>
                    </div>
                </div>
                <!-- Cryptographic Floor Security & Escrow Guarantee Banner -->
                <div
                    class="bg-slate-authority rounded-2xl p-6 md:p-8 text-canvas-ivory shadow-xl flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-amber-action/20 text-amber-action flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[28px]">shield</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-micro text-label-micro uppercase font-mono tracking-wider text-amber-action font-semibold">Cryptographic
                                    Live Auction Floor</span>
                                <span class="w-1 h-1 rounded-full bg-canvas-ivory/40"></span>
                                <span class="font-label-micro text-label-micro text-status-green font-mono">Zero Shill
                                    Guarantee</span>
                            </div>
                            <h3 class="font-headline-section text-title-card font-bold text-card-white">Authentic
                                Community Auction Ledger</h3>
                            <p class="font-body-small text-body-small text-canvas-ivory/70 max-w-xl">
                                All floor bids are backed by real escrow deposit locked into smart multi-signature
                                vaults. If outbid, your capital auto-unlocks to your spending balance in under 200ms.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                        <button
                            class="bg-transparent hover:bg-card-white/10 text-canvas-ivory font-button-text text-body-small px-5 py-3 rounded-xl transition-colors"
                            type="button">
                            View Escrow Protocol
                        </button>
                        <button
                            class="bg-amber-action hover:opacity-95 text-slate-authority font-button-text text-body-small font-bold px-6 py-3 rounded-xl flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.99]"
                            type="button">
                            <span class="material-symbols-outlined text-[18px]">lock_open</span>
                            <span>Enable 1-Click Vault Bidding</span>
                        </button>
                    </div>
                </div>
                <!-- AI Concierge Interactive Prompt Pill -->
                <div class="self-center">
                    <button
                        class="bg-card-white/90 backdrop-blur-md shadow-md hover:shadow-lg text-slate-authority px-5 py-2.5 rounded-full inline-flex items-center gap-2.5 font-body-small text-body-small font-medium transition-all group"
                        type="button">
                        <span
                            class="material-symbols-outlined text-[20px] text-amber-action group-hover:rotate-12 transition-transform">smart_toy</span>
                        <span>Ask Bazaario AI: <span class="text-on-surface-variant italic">“What is the optimal snipe
                                strategy for Leica M?”</span></span>
                        <span
                            class="material-symbols-outlined text-[16px] text-on-surface-variant group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
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