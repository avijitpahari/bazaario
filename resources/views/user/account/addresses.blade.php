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
            <!-- Subtle Ambient Glow Orbs -->
            <div class="relative w-full overflow-hidden">
                <div
                    class="pointer-events-none absolute -top-24 left-1/4 w-96 h-96 bg-amber-action/10 rounded-full blur-3xl -z-10">
                </div>
                <div
                    class="pointer-events-none absolute top-40 right-10 w-80 h-80 bg-primary-container/5 rounded-full blur-2xl -z-10">
                </div>
                <div class="max-w-container-max mx-auto px-gutter-md pt-4 pb-section-hero-bottom">
                    <!-- Breadcrumbs & Status Bar -->
                    <div
                        class="flex flex-wrap items-center justify-between gap-4 py-3 px-4 rounded-xl bg-card-white/80 backdrop-blur-md shadow-sm mb-8">
                        <div class="flex items-center gap-3 flex-wrap">
                            <a class="inline-flex items-center gap-1.5 font-button-text text-body-small text-slate-authority hover:text-amber-action transition-colors"
                                href="#">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Back to Account
                            </a>
                            <span class="text-on-surface-variant/40 text-xs">/</span>
                            <nav aria-label="Breadcrumb"
                                class="flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant">
                                <a class="hover:text-slate-authority transition-colors" href="#">Home</a>
                                <span>/</span>
                                <a class="hover:text-slate-authority transition-colors" href="#">Account</a>
                                <span>/</span>
                                <span class="text-slate-authority font-bold">Addresses</span>
                            </nav>
                        </div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-status-green animate-pulse"></span>
                                ESCROW VERIFIED BUYER • VAULT #ESC-9041 • UID: BZ-891-IN
                            </div>
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-authority text-canvas-ivory font-label-micro text-label-micro shadow-sm">
                                <span class="material-symbols-outlined text-[14px] text-amber-action">lock</span>
                                <span>Vault: ₹60,500.00</span>
                                <a class="text-amber-action hover:underline ml-1" href="#">Settings</a>
                            </div>
                        </div>
                    </div>
                    <!-- Main Page Header -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8">
                        <div>
                            <div
                                class="inline-flex items-center gap-2 font-label-eyebrow text-label-eyebrow text-amber-action uppercase tracking-widest mb-2 font-semibold">
                                <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                                Account Management • Logistics Layer
                            </div>
                            <h1 class="font-display-hero text-display-hero text-slate-authority tracking-tight">
                                My Addresses
                            </h1>
                            <p class="font-body-regular text-body-regular text-on-surface-variant max-w-2xl mt-2">
                                Manage your validated delivery locations and dispatch endpoints for physical escrow
                                handshakes.
                            </p>
                        </div>
                        <!-- Add Address Button (Triggers Modal) -->
                        <button
                            class="inline-flex items-center justify-center gap-2 bg-amber-action text-slate-authority px-6 py-3 rounded-xl font-button-text text-button-text font-bold hover:opacity-95 active:scale-[0.99] transition shadow-sm"
                            id="open-address-modal">
                            <span class="material-symbols-outlined text-[20px]">add_location_alt</span>
                            Add New Address
                        </button>
                    </div>
                    <!-- Address Matrix Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Card 1: Primary Residence (Default) -->
                        <div
                            class="flex flex-col justify-between bg-card-white/90 backdrop-blur-md rounded-2xl p-card-padding shadow-sm relative transition hover:shadow-md">
                            <div class="flex flex-col">
                                <div class="flex items-center justify-between gap-2 pb-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-action/15 text-amber-action font-label-micro text-label-micro font-bold">
                                        <span class="material-symbols-outlined text-[14px]">home</span>
                                        HOME
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-status-green/10 text-status-green font-label-micro text-label-micro font-semibold">
                                        <span class="material-symbols-outlined text-[12px]">verified</span>
                                        Default Delivery Point
                                    </span>
                                </div>
                                <div class="pt-1">
                                    <div class="flex items-center gap-2">
                                        <h2 class="font-title-card text-title-card text-slate-authority font-bold">
                                            Avijit Pahari</h2>
                                        <span
                                            class="inline-flex items-center gap-0.5 text-status-green font-label-micro text-label-micro"
                                            title="Geo-location matched with municipal cadastral grid">
                                            <span class="material-symbols-outlined text-[14px]">my_location</span>
                                            Geo-verified
                                        </span>
                                    </div>
                                    <div class="mt-3 font-body-small text-body-small text-on-surface-variant space-y-1">
                                        <p class="text-slate-authority font-medium">Station Road, Contai</p>
                                        <p>Purba Medinipur, West Bengal</p>
                                        <p class="font-mono text-xs">PIN: 721401 — India</p>
                                        <p
                                            class="flex items-center gap-1.5 pt-1 text-slate-authority font-mono text-xs">
                                            <span
                                                class="material-symbols-outlined text-[14px] text-status-green">phone</span>
                                            +91 98200 41829
                                            <span
                                                class="material-symbols-outlined text-[14px] text-status-green">check_circle</span>
                                        </p>
                                    </div>
                                </div>
                                <!-- Handshake Note -->
                                <div
                                    class="mt-4 p-3 rounded-lg bg-surface-container-low text-on-surface-variant flex items-start gap-2">
                                    <span
                                        class="material-symbols-outlined text-[16px] text-amber-action shrink-0 mt-0.5">shield_person</span>
                                    <p class="font-body-small text-body-small text-xs">
                                        Safe drop with building security or biometric OTP escrow handshake at gate.
                                    </p>
                                </div>
                            </div>
                            <!-- Card Footer -->
                            <div
                                class="mt-6 pt-4 flex items-center justify-between bg-surface-container-low/40 -mx-card-padding -mb-card-padding px-card-padding py-3 rounded-b-2xl">
                                <span
                                    class="font-label-micro text-label-micro text-status-green font-semibold inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                    Active Default
                                </span>
                                <div class="flex items-center gap-2">
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-slate-authority font-button-text text-body-small text-xs transition">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                        Edit
                                    </button>
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container-low text-on-surface-variant/40 cursor-not-allowed text-xs font-button-text"
                                        title="Default delivery point cannot be removed">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Card 2: Work Studio -->
                        <div
                            class="flex flex-col justify-between bg-card-white/90 backdrop-blur-md rounded-2xl p-card-padding shadow-sm relative transition hover:shadow-md">
                            <div class="flex flex-col">
                                <div class="flex items-center justify-between gap-2 pb-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-high text-slate-authority font-label-micro text-label-micro font-bold">
                                        <span class="material-symbols-outlined text-[14px]">apartment</span>
                                        WORK / STUDIO
                                    </span>
                                    <button
                                        class="set-default-btn inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-action/10 hover:bg-amber-action hover:text-slate-authority text-amber-action font-label-micro text-label-micro font-semibold transition">
                                        Set as Default
                                    </button>
                                </div>
                                <div class="pt-1">
                                    <div class="flex items-center gap-2">
                                        <h2 class="font-title-card text-title-card text-slate-authority font-bold">
                                            Avijit Pahari (Studio 4B)</h2>
                                    </div>
                                    <div class="mt-3 font-body-small text-body-small text-on-surface-variant space-y-1">
                                        <p class="text-slate-authority font-medium">Sector V, Salt Lake Electronics
                                            Complex</p>
                                        <p>Near Webel Bhavan, Bidhannagar</p>
                                        <p>Kolkata, West Bengal — 700091, India</p>
                                        <p
                                            class="flex items-center gap-1.5 pt-1 text-slate-authority font-mono text-xs">
                                            <span
                                                class="material-symbols-outlined text-[14px] text-on-surface-variant">phone</span>
                                            +91 98200 41829 (Ext. 402)
                                        </p>
                                    </div>
                                </div>
                                <!-- Delivery Instruction Note -->
                                <div
                                    class="mt-4 p-3 rounded-lg bg-surface-container-low text-on-surface-variant flex items-start gap-2">
                                    <span
                                        class="material-symbols-outlined text-[16px] text-slate-authority shrink-0 mt-0.5">schedule</span>
                                    <p class="font-body-small text-body-small text-xs">
                                        Deliver between 10:00 AM – 6:00 PM (Mon–Fri only). Reception sign-in available.
                                    </p>
                                </div>
                            </div>
                            <!-- Card Footer -->
                            <div
                                class="mt-6 pt-4 flex items-center justify-between bg-surface-container-low/40 -mx-card-padding -mb-card-padding px-card-padding py-3 rounded-b-2xl">
                                <span class="font-label-micro text-label-micro text-on-surface-variant">Commercial
                                    Hub</span>
                                <div class="flex items-center gap-2">
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-slate-authority font-button-text text-body-small text-xs transition">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                        Edit
                                    </button>
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-error-container/40 hover:bg-error-container text-error font-button-text text-body-small text-xs transition">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Card 3: Artisan Workshop / Warehouse -->
                        <div
                            class="flex flex-col justify-between bg-card-white/90 backdrop-blur-md rounded-2xl p-card-padding shadow-sm relative transition hover:shadow-md">
                            <div class="flex flex-col">
                                <div class="flex items-center justify-between gap-2 pb-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-highest text-slate-authority font-label-micro text-label-micro font-bold">
                                        <span class="material-symbols-outlined text-[14px]">warehouse</span>
                                        ARTISAN VAULT
                                    </span>
                                    <button
                                        class="set-default-btn inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-action/10 hover:bg-amber-action hover:text-slate-authority text-amber-action font-label-micro text-label-micro font-semibold transition">
                                        Set as Default
                                    </button>
                                </div>
                                <div class="pt-1">
                                    <div class="flex items-center gap-2">
                                        <h2 class="font-title-card text-title-card text-slate-authority font-bold">
                                            Avijit Pahari (Logistics Bay)</h2>
                                    </div>
                                    <div class="mt-3 font-body-small text-body-small text-on-surface-variant space-y-1">
                                        <p class="text-slate-authority font-medium">NH-116 Bypass, Depot Cluster 12</p>
                                        <p>Kanthi Bypass Road, Industrial Sector</p>
                                        <p>Contai, West Bengal — 721402, India</p>
                                        <p
                                            class="flex items-center gap-1.5 pt-1 text-slate-authority font-mono text-xs">
                                            <span
                                                class="material-symbols-outlined text-[14px] text-on-surface-variant">phone</span>
                                            +91 98200 41829
                                        </p>
                                    </div>
                                </div>
                                <!-- Heavy Freight Note -->
                                <div
                                    class="mt-4 p-3 rounded-lg bg-surface-container-low text-on-surface-variant flex items-start gap-2">
                                    <span
                                        class="material-symbols-outlined text-[16px] text-amber-action shrink-0 mt-0.5">forklift</span>
                                    <p class="font-body-small text-body-small text-xs">
                                        Heavy parcel dock with hydraulic ramp &amp; forklift access for bulk craft
                                        equipment.
                                    </p>
                                </div>
                            </div>
                            <!-- Card Footer -->
                            <div
                                class="mt-6 pt-4 flex items-center justify-between bg-surface-container-low/40 -mx-card-padding -mb-card-padding px-card-padding py-3 rounded-b-2xl">
                                <span class="font-label-micro text-label-micro text-on-surface-variant">Freight / High
                                    Capacity</span>
                                <div class="flex items-center gap-2">
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-slate-authority font-button-text text-body-small text-xs transition">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                        Edit
                                    </button>
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-error-container/40 hover:bg-error-container text-error font-button-text text-body-small text-xs transition">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Card 4: Interactive Add New Delivery Endpoint Kiosk Card -->
                        <button
                            class="group flex flex-col items-center justify-center text-center p-8 rounded-2xl bg-card-white/60 hover:bg-card-white backdrop-blur-md shadow-sm transition min-h-[290px] cursor-pointer"
                            id="add-kiosk-btn">
                            <div
                                class="w-14 h-14 rounded-2xl bg-amber-action/10 group-hover:bg-amber-action flex items-center justify-center text-amber-action group-hover:text-slate-authority transition mb-4 shadow-sm">
                                <span class="material-symbols-outlined text-[28px]">add_location</span>
                            </div>
                            <h3
                                class="font-title-card text-title-card font-bold text-slate-authority group-hover:text-amber-action transition-colors">
                                Add New Delivery Address
                            </h3>
                            <p class="font-body-small text-body-small text-on-surface-variant max-w-xs mt-2 mb-4">
                                Add an office, secondary home, or verified locker node for insured escrow dispatches.
                            </p>
                            <span
                                class="inline-flex items-center gap-1.5 font-button-text text-body-small font-bold text-slate-authority group-hover:underline">
                                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                                Add Endpoint
                            </span>
                        </button>
                        <!-- Card 5: Real-time Dispatch Radius Info Card -->
                        <div
                            class="flex flex-col justify-between rounded-2xl p-card-padding bg-card-white/80 backdrop-blur-md shadow-sm">
                            <div>
                                <div class="flex items-center justify-between pb-3">
                                    <span
                                        class="font-label-micro text-label-micro text-status-green font-bold uppercase tracking-wider flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">radar</span>
                                        Escrow Dispatch Grid
                                    </span>
                                    <span
                                        class="font-label-micro text-label-micro bg-status-green/10 text-status-green px-2 py-0.5 rounded-full font-medium">99.8%
                                        SLA</span>
                                </div>
                                <h3 class="font-title-card text-title-card font-bold text-slate-authority">Coverage In
                                    West Bengal</h3>
                                <p class="font-body-small text-body-small text-on-surface-variant mt-1.5">
                                    All 3 addresses sit inside the premium 24-hour insured escrow delivery corridor with
                                    live GPS telematics.
                                </p>
                                <!-- Mini Sparkline / Metrics Visualization -->
                                <div
                                    class="mt-4 p-3 rounded-xl bg-surface-container-low flex items-center justify-between">
                                    <div>
                                        <span class="font-label-micro text-label-micro text-on-surface-variant">Average
                                            Handshake Speed</span>
                                        <p
                                            class="font-title-card text-title-card font-bold text-slate-authority mt-0.5">
                                            18.4 hrs</p>
                                    </div>
                                    <svg class="w-24 h-8 text-status-green" fill="none" stroke="currentColor"
                                        viewbox="0 0 100 30">
                                        <path d="M0 24 Q 25 10, 45 18 T 75 8 T 100 4" fill="none" stroke-linecap="round"
                                            stroke-width="2.5"></path>
                                        <circle cx="100" cy="4" fill="currentColor" r="3"></circle>
                                    </svg>
                                </div>
                            </div>
                            <div class="pt-4 flex items-center gap-2">
                                <span
                                    class="material-symbols-outlined text-[16px] text-amber-action">support_agent</span>
                                <span class="font-label-micro text-label-micro text-on-surface-variant">Priority 24/7
                                    Dispatch Dispatcher Assigned</span>
                            </div>
                        </div>
                        <!-- Card 6: Smart Locker / Drop-off Node Selector -->
                        <div
                            class="flex flex-col justify-between rounded-2xl p-card-padding bg-surface-container-high/60 backdrop-blur-md shadow-sm">
                            <div>
                                <div class="flex items-center justify-between pb-3">
                                    <span
                                        class="font-label-micro text-label-micro text-amber-action font-bold uppercase tracking-wider flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">lock_clock</span>
                                        Locker Network
                                    </span>
                                    <span
                                        class="font-label-micro text-label-micro bg-card-white px-2 py-0.5 rounded-full text-slate-authority font-medium">New
                                        Feature</span>
                                </div>
                                <h3 class="font-title-card text-title-card font-bold text-slate-authority">Collect from
                                    24/7 Bazaario Locker</h3>
                                <p class="font-body-small text-body-small text-on-surface-variant mt-1.5">
                                    Not home? Direct parcels to a temperature-controlled biometric locker near Contai
                                    Central Station.
                                </p>
                            </div>
                            <div class="pt-4">
                                <button
                                    class="w-full py-2.5 px-4 rounded-xl bg-card-white hover:bg-surface-container-lowest text-slate-authority font-button-text text-body-small font-semibold shadow-sm transition flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-amber-action">near_me</span>
                                    Locate Nearest Locker
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Decentralized Encryption Security Notice (Authority Dark Module) -->
                    <div
                        class="mt-10 rounded-2xl bg-slate-authority text-canvas-ivory p-6 md:p-8 shadow-xl flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative overflow-hidden">
                        <div
                            class="pointer-events-none absolute -right-16 -bottom-16 w-64 h-64 bg-amber-action/10 rounded-full blur-2xl">
                        </div>
                        <div class="flex items-start gap-4 max-w-3xl">
                            <div
                                class="w-12 h-12 rounded-xl bg-amber-action/20 text-amber-action flex items-center justify-center shrink-0 mt-1">
                                <span class="material-symbols-outlined text-[28px]">enhanced_encryption</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-headline-section text-headline-section font-bold text-canvas-ivory">
                                        Decentralized Address Encryption
                                    </h3>
                                    <span
                                        class="font-label-micro text-label-micro px-2.5 py-0.5 rounded-full bg-status-green/20 text-status-green font-medium">
                                        AES-256 Enabled
                                    </span>
                                </div>
                                <p class="font-body-regular text-body-small text-canvas-ivory/70 mt-1">
                                    All delivery coordinates and contact telephone numbers are encrypted at rest.
                                    Courier personnel receive single-use, ephemeral decryption keys only after biometric
                                    physical handoff confirmation.
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                            <button
                                class="flex-1 lg:flex-none px-4 py-2.5 rounded-xl bg-canvas-ivory/10 hover:bg-canvas-ivory/20 text-canvas-ivory font-button-text text-body-small font-semibold transition flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                                View Security Log
                            </button>
                            <button
                                class="flex-1 lg:flex-none px-5 py-2.5 rounded-xl bg-amber-action text-slate-authority hover:opacity-95 font-button-text text-body-small font-bold transition flex items-center justify-center gap-2 shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                                Ask AI Logistics
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Interactive Add Address Modal Dialog (Hidden by Default) -->
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-authority/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200"
                id="address-modal">
                <div
                    class="bg-card-white rounded-2xl max-w-lg w-full p-6 shadow-2xl transition-transform duration-200 scale-95 flex flex-col max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between pb-4">
                        <div class="flex items-center gap-2">
                            <span
                                class="w-8 h-8 rounded-lg bg-amber-action/20 text-amber-action flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">add_location_alt</span>
                            </span>
                            <h2 class="font-headline-section text-headline-section font-bold text-slate-authority">Add
                                Delivery Endpoint</h2>
                        </div>
                        <button class="p-1.5 rounded-full hover:bg-surface-container text-on-surface-variant transition"
                            id="close-address-modal">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    <form class="space-y-4 pt-2" id="new-address-form" onsubmit="event.preventDefault();">
                        <div>
                            <label
                                class="block font-label-micro text-label-micro font-bold text-slate-authority uppercase tracking-wider mb-1">Address
                                Label</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label
                                    class="flex items-center justify-center gap-1.5 p-2 rounded-xl bg-surface-container-low hover:bg-surface-container font-label-micro text-label-micro cursor-pointer">
                                    <input checked="" class="text-amber-action focus:ring-amber-action"
                                        name="label_type" type="radio" value="HOME" />
                                    <span>🏠 Home</span>
                                </label>
                                <label
                                    class="flex items-center justify-center gap-1.5 p-2 rounded-xl bg-surface-container-low hover:bg-surface-container font-label-micro text-label-micro cursor-pointer">
                                    <input class="text-amber-action focus:ring-amber-action" name="label_type"
                                        type="radio" value="WORK" />
                                    <span>💼 Work</span>
                                </label>
                                <label
                                    class="flex items-center justify-center gap-1.5 p-2 rounded-xl bg-surface-container-low hover:bg-surface-container font-label-micro text-label-micro cursor-pointer">
                                    <input class="text-amber-action focus:ring-amber-action" name="label_type"
                                        type="radio" value="VAULT" />
                                    <span>📦 Studio</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block font-label-micro text-label-micro font-bold text-slate-authority uppercase tracking-wider mb-1">Full
                                Recipient Name</label>
                            <input
                                class="w-full bg-surface-container-low rounded-xl px-4 py-2.5 text-body-small font-body-small text-slate-authority focus:bg-card-white focus:outline-none focus:ring-2 focus:ring-amber-action"
                                placeholder="Name or Department" type="text" value="Avijit Pahari" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block font-label-micro text-label-micro font-bold text-slate-authority uppercase tracking-wider mb-1">Contact
                                    Phone</label>
                                <input
                                    class="w-full bg-surface-container-low rounded-xl px-4 py-2.5 text-body-small font-body-small text-slate-authority focus:bg-card-white focus:outline-none focus:ring-2 focus:ring-amber-action"
                                    placeholder="+91 00000 00000" type="tel" value="+91 98200 41829" />
                            </div>
                            <div>
                                <label
                                    class="block font-label-micro text-label-micro font-bold text-slate-authority uppercase tracking-wider mb-1">Postal
                                    Pincode</label>
                                <input
                                    class="w-full bg-surface-container-low rounded-xl px-4 py-2.5 text-body-small font-body-small text-slate-authority focus:bg-card-white focus:outline-none focus:ring-2 focus:ring-amber-action"
                                    placeholder="6-digit PIN" type="text" value="721401" />
                            </div>
                        </div>
                        <div>
                            <label
                                class="block font-label-micro text-label-micro font-bold text-slate-authority uppercase tracking-wider mb-1">Street
                                Address / Locality</label>
                            <input
                                class="w-full bg-surface-container-low rounded-xl px-4 py-2.5 text-body-small font-body-small text-slate-authority focus:bg-card-white focus:outline-none focus:ring-2 focus:ring-amber-action"
                                placeholder="Plot, building, apartment, street name" type="text" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block font-label-micro text-label-micro font-bold text-slate-authority uppercase tracking-wider mb-1">City
                                    / District</label>
                                <input
                                    class="w-full bg-surface-container-low rounded-xl px-4 py-2.5 text-body-small font-body-small text-slate-authority focus:bg-card-white focus:outline-none focus:ring-2 focus:ring-amber-action"
                                    type="text" value="Contai, Purba Medinipur" />
                            </div>
                            <div>
                                <label
                                    class="block font-label-micro text-label-micro font-bold text-slate-authority uppercase tracking-wider mb-1">State</label>
                                <select
                                    class="w-full bg-surface-container-low rounded-xl px-4 py-2.5 text-body-small font-body-small text-slate-authority focus:bg-card-white focus:outline-none focus:ring-2 focus:ring-amber-action cursor-pointer">
                                    <option>West Bengal</option>
                                    <option>Maharashtra</option>
                                    <option>Karnataka</option>
                                    <option>Delhi NCR</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block font-label-micro text-label-micro font-bold text-slate-authority uppercase tracking-wider mb-1">Escrow
                                Delivery Instructions</label>
                            <textarea
                                class="w-full bg-surface-container-low rounded-xl px-4 py-2 text-body-small font-body-small text-slate-authority focus:bg-card-white focus:outline-none focus:ring-2 focus:ring-amber-action"
                                placeholder="e.g., Call before arrival, leave with security guard, or forklift dock access required"
                                rows="2"></textarea>
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <input class="w-4 h-4 rounded text-amber-action focus:ring-amber-action" id="make_default"
                                type="checkbox" />
                            <label
                                class="font-body-small text-body-small text-slate-authority cursor-pointer select-none"
                                for="make_default">
                                Set as default escrow shipping and dispatch point
                            </label>
                        </div>
                        <div class="pt-4 flex items-center justify-end gap-3">
                            <button
                                class="px-5 py-2.5 rounded-xl bg-surface-container text-slate-authority font-button-text text-body-small font-semibold hover:bg-surface-container-high transition"
                                id="cancel-address-btn" type="button">
                                Cancel
                            </button>
                            <button
                                class="px-6 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-bold hover:opacity-95 transition shadow-sm"
                                id="save-address-btn" type="submit">
                                Save Endpoint
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Floating AI Concierge Dock Trigger -->
            <aside class="fixed bottom-6 right-6 z-40 max-w-sm hidden sm:block">
                <button
                    class="group flex items-center gap-3 px-4 py-3 rounded-full bg-slate-authority text-canvas-ivory shadow-xl hover:bg-black transition-all hover:pr-5">
                    <span
                        class="w-7 h-7 rounded-full bg-amber-action text-slate-authority flex items-center justify-center font-bold text-sm shrink-0">
                        ✦
                    </span>
                    <div class="text-left font-body-small text-xs leading-tight">
                        <span class="text-amber-action font-semibold block">Ask Bazaario AI</span>
                        <span class="text-canvas-ivory/80 truncate block max-w-[240px]">"What is courier cutoff time for
                            Contai (721401)?"</span>
                    </div>
                    <span
                        class="material-symbols-outlined text-[18px] text-amber-action opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
                </button>
            </aside>
            <!-- Interactive Scripts -->
            <script>
                (function () {
                    const modal = document.getElementById('address-modal');
                    const openBtn = document.getElementById('open-address-modal');
                    const kioskBtn = document.getElementById('add-kiosk-btn');
                    const closeBtn = document.getElementById('close-address-modal');
                    const cancelBtn = document.getElementById('cancel-address-btn');
                    const saveBtn = document.getElementById('save-address-btn');

                    function openModal() {
                        if (!modal) return;
                        modal.classList.remove('opacity-0', 'pointer-events-none');
                        modal.classList.add('opacity-100', 'pointer-events-auto');
                        const inner = modal.querySelector('div');
                        if (inner) {
                            inner.classList.remove('scale-95');
                            inner.classList.add('scale-100');
                        }
                    }

                    function closeModal() {
                        if (!modal) return;
                        modal.classList.remove('opacity-100', 'pointer-events-auto');
                        modal.classList.add('opacity-0', 'pointer-events-none');
                        const inner = modal.querySelector('div');
                        if (inner) {
                            inner.classList.remove('scale-100');
                            inner.classList.add('scale-95');
                        }
                    }

                    if (openBtn) openBtn.addEventListener('click', openModal);
                    if (kioskBtn) kioskBtn.addEventListener('click', openModal);
                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

                    if (saveBtn) {
                        saveBtn.addEventListener('click', function () {
                            closeModal();
                        });
                    }

                    // Default switcher feedback
                    const defaultBtns = document.querySelectorAll('.set-default-btn');
                    defaultBtns.forEach(btn => {
                        btn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            const originalText = this.innerHTML;
                            this.innerHTML = '<span class="material-symbols-outlined text-[12px]">check</span> Default Set';
                            this.classList.remove('bg-amber-action/10', 'text-amber-action');
                            this.classList.add('bg-status-green/10', 'text-status-green');
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.classList.add('bg-amber-action/10', 'text-amber-action');
                                this.classList.remove('bg-status-green/10', 'text-status-green');
                            }, 1800);
                        });
                    });
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