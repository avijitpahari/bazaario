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
            <!-- Subtle Ambient Glow Orbs behind canvas -->
            <div class="relative w-full overflow-hidden">
                <div
                    class="absolute -top-32 left-1/4 w-96 h-96 bg-amber-action/10 rounded-full blur-3xl pointer-events-none -z-10">
                </div>
                <div
                    class="absolute top-80 right-10 w-[28rem] h-[28rem] bg-secondary-fixed/20 rounded-full blur-3xl pointer-events-none -z-10">
                </div>
                <div class="max-w-container-max mx-auto px-gutter-md py-gutter-md flex flex-col gap-6">
                    <!-- Breadcrumb & Top Utility Bar -->
                    <div
                        class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-4 rounded-xl bg-surface-container-lowest/80 backdrop-blur-md shadow-sm">
                        <div class="flex flex-wrap items-center gap-3">
                            <a class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-surface-container-low hover:bg-surface-container text-slate-authority font-button-text text-body-small transition-colors"
                                data-path="user-profile" href="#">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Back to Profile
                            </a>
                            <div class="hidden sm:block w-px h-4 bg-primary-alpha-10"></div>
                            <nav aria-label="Breadcrumbs"
                                class="flex items-center gap-2 font-body-small text-body-small text-on-surface-variant">
                                <a class="hover:text-slate-authority transition-colors" data-path="home"
                                    href="#">Home</a>
                                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                                <a class="hover:text-slate-authority transition-colors" data-path="user-profile"
                                    href="#">Account</a>
                                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                                <span class="font-semibold text-slate-authority">Edit Information</span>
                            </nav>
                        </div>
                        <!-- Escrow & Security Meta Badge -->
                        <div class="flex items-center gap-2.5 self-stretch md:self-auto justify-between md:justify-end">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-status-green/10 text-status-green font-label-micro text-label-micro font-medium">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                                ESCROW VERIFIED • VAULT #ESC-9041 • UID: BZ-891-IN
                            </div>
                            <div
                                class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-authority text-canvas-ivory font-label-micro text-label-micro">
                                <span class="material-symbols-outlined text-[14px] text-amber-action">lock</span>
                                Vault: ₹60,500.00
                            </div>
                        </div>
                    </div>
                    <!-- Page Header Banner -->
                    <div class="flex flex-col gap-2 pt-2 pb-1">
                        <div
                            class="inline-flex items-center gap-2 text-amber-action font-label-eyebrow text-label-eyebrow tracking-wider uppercase">
                            <span class="material-symbols-outlined text-[16px]">shield_person</span>
                            Account Management • Credential Layer
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2">
                            <h1 class="font-display-hero text-display-hero text-slate-authority">Edit Profile</h1>
                            <span
                                class="font-label-micro text-label-micro text-on-surface-variant bg-surface-container-high px-2.5 py-1 rounded-md">
                                Last Synced: Today at 14:22 IST
                            </span>
                        </div>
                        <p class="font-body-regular text-body-regular text-on-surface-variant max-w-2xl">
                            Update your personal credentials, contact endpoints, and marketplace preferences.
                            Cryptographic verification tokens will be prompted upon altering financial endpoints.
                        </p>
                    </div>
                    <!-- Main Form Container: Dimensional Frosted Surface -->
                    <form
                        class="bg-card-white/90 backdrop-blur-xl rounded-2xl shadow-xl shadow-slate-authority/5 p-6 md:p-10 flex flex-col gap-10"
                        id="profileForm"
                        onsubmit="event.preventDefault(); document.getElementById('saveModal').classList.remove('hidden');">
                        <!-- Avatar / Identity Pod Section -->
                        <section
                            class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 pb-8 bg-surface-container-low/60 p-6 rounded-xl">
                            <div class="flex items-center gap-5">
                                <!-- Monogram Avatar Pod with Ambient Amber Ring -->
                                <div class="relative group">
                                    <div
                                        class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-slate-authority via-primary-container to-slate-authority p-1 shadow-lg flex items-center justify-center relative">
                                        <div
                                            class="w-full h-full rounded-[14px] bg-slate-authority flex items-center justify-center text-canvas-ivory font-display-hero text-headline-section font-bold tracking-tight select-none">
                                            AP
                                        </div>
                                        <span
                                            class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-status-green ring-4 ring-card-white flex items-center justify-center text-card-white text-[12px]">
                                            <span class="material-symbols-outlined text-[14px]"
                                                style="font-variation-settings: 'FILL' 1;">check</span>
                                        </span>
                                    </div>
                                    <div
                                        class="absolute inset-0 -z-10 rounded-2xl bg-amber-action/30 blur-md group-hover:blur-lg transition-all">
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <h2 class="font-title-card text-title-card text-slate-authority">Profile Pod
                                            &amp; Avatar</h2>
                                        <span
                                            class="font-label-micro text-label-micro bg-amber-action/20 text-slate-authority px-2 py-0.5 rounded-full font-semibold">Tier
                                            1 Craftsman</span>
                                    </div>
                                    <p class="font-body-small text-body-small text-on-surface-variant">Recommended
                                        400×400px. High-contrast square format (Max 5MB).</p>
                                    <div
                                        class="flex items-center gap-2 text-on-surface-variant font-label-micro text-label-micro mt-0.5">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                        Publicly visible in Live Auctions and Community Stalls
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 w-full md:w-auto">
                                <button
                                    class="flex-1 md:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-card-white shadow-sm hover:bg-surface-container-high font-button-text text-body-small text-slate-authority transition-all"
                                    type="button">
                                    <span
                                        class="material-symbols-outlined text-[20px] text-amber-action">photo_camera</span>
                                    Upload New
                                </button>
                                <button
                                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-error hover:bg-error-container/40 font-body-small text-body-small transition-colors"
                                    type="button">
                                    Remove
                                </button>
                            </div>
                        </section>
                        <!-- Two-Column Structured Inputs -->
                        <section class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <!-- Full Name (KYC Verified) -->
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <label class="font-button-text text-body-small font-semibold text-slate-authority"
                                        for="fullName">
                                        Legal Full Name
                                    </label>
                                    <span
                                        class="inline-flex items-center gap-1 font-label-micro text-label-micro text-status-green bg-status-green/10 px-2 py-0.5 rounded-full font-medium">
                                        <span class="material-symbols-outlined text-[12px]">verified</span>
                                        Official KYC Verified
                                    </span>
                                </div>
                                <div class="relative flex items-center">
                                    <span
                                        class="material-symbols-outlined absolute left-3.5 text-[20px] text-on-surface-variant pointer-events-none">badge</span>
                                    <input
                                        class="w-full bg-surface-container-low pl-11 pr-4 py-3 rounded-xl font-body-regular text-body-regular text-slate-authority placeholder:text-on-surface-variant/60 focus:outline-none focus:bg-card-white shadow-inner focus:shadow-md transition-all"
                                        id="fullName" type="text" value="Avijit Pahari" />
                                </div>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Name matches Indian
                                    PAN / National identity registry.</p>
                            </div>
                            <!-- Marketplace Handle -->
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <label class="font-button-text text-body-small font-semibold text-slate-authority"
                                        for="handle">
                                        Marketplace Handle
                                    </label>
                                    <span class="font-label-micro text-label-micro text-amber-action font-semibold">Live
                                        Auction ID</span>
                                </div>
                                <div class="relative flex items-center">
                                    <span
                                        class="material-symbols-outlined absolute left-3.5 text-[20px] text-on-surface-variant pointer-events-none">alternate_email</span>
                                    <input
                                        class="w-full bg-surface-container-low pl-11 pr-24 py-3 rounded-xl font-body-regular text-body-regular text-slate-authority placeholder:text-on-surface-variant/60 focus:outline-none focus:bg-card-white shadow-inner focus:shadow-md transition-all"
                                        id="handle" type="text" value="avijit_craft" />
                                    <span
                                        class="absolute right-3.5 font-label-micro text-label-micro text-status-green font-medium">Available</span>
                                </div>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Your permanent URL:
                                    bazaario.com/stall/@avijit_craft</p>
                            </div>
                            <!-- Email Address -->
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <label class="font-button-text text-body-small font-semibold text-slate-authority"
                                        for="emailAddr">
                                        Primary Notification Email
                                    </label>
                                    <span
                                        class="inline-flex items-center gap-1 font-label-micro text-label-micro text-status-green bg-status-green/10 px-2 py-0.5 rounded-full font-medium">
                                        <span class="material-symbols-outlined text-[12px]">check_circle</span>
                                        Primary Verified
                                    </span>
                                </div>
                                <div class="relative flex items-center">
                                    <span
                                        class="material-symbols-outlined absolute left-3.5 text-[20px] text-on-surface-variant pointer-events-none">mail</span>
                                    <input
                                        class="w-full bg-surface-container-low pl-11 pr-4 py-3 rounded-xl font-body-regular text-body-regular text-slate-authority placeholder:text-on-surface-variant/60 focus:outline-none focus:bg-card-white shadow-inner focus:shadow-md transition-all"
                                        id="emailAddr" type="email" value="avijit.pahari@example.com" />
                                </div>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Escrow release
                                    tokens and live bidding receipts are dispatched here.</p>
                            </div>
                            <!-- Phone Number with OTP Locked Status -->
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <label class="font-button-text text-body-small font-semibold text-slate-authority"
                                        for="phoneNumber">
                                        Handshake Phone Number
                                    </label>
                                    <span
                                        class="inline-flex items-center gap-1 font-label-micro text-label-micro bg-primary-container text-canvas-ivory px-2 py-0.5 rounded-full font-medium">
                                        <span
                                            class="material-symbols-outlined text-[12px] text-amber-action">lock</span>
                                        2FA &amp; OTP Locked
                                    </span>
                                </div>
                                <div class="relative flex items-center">
                                    <span
                                        class="material-symbols-outlined absolute left-3.5 text-[20px] text-on-surface-variant pointer-events-none">smartphone</span>
                                    <input
                                        class="w-full bg-surface-container-low pl-11 pr-24 py-3 rounded-xl font-body-regular text-body-regular text-slate-authority placeholder:text-on-surface-variant/60 focus:outline-none focus:bg-card-white shadow-inner focus:shadow-md transition-all"
                                        id="phoneNumber" type="tel" value="+91 98200 41829" />
                                    <button
                                        class="absolute right-2.5 px-2.5 py-1 rounded-lg bg-surface-container-high hover:bg-surface-container text-slate-authority font-label-micro text-label-micro font-semibold transition-colors"
                                        type="button">
                                        Change
                                    </button>
                                </div>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Utilized for
                                    delivery verification and unboxing security tokens.</p>
                            </div>
                            <!-- Language & Localization -->
                            <div class="flex flex-col gap-2">
                                <label class="font-button-text text-body-small font-semibold text-slate-authority"
                                    for="langSelect">
                                    Display Language &amp; Locale
                                </label>
                                <div class="relative flex items-center">
                                    <span
                                        class="material-symbols-outlined absolute left-3.5 text-[20px] text-on-surface-variant pointer-events-none">translate</span>
                                    <select
                                        class="w-full appearance-none bg-surface-container-low pl-11 pr-10 py-3 rounded-xl font-body-regular text-body-regular text-slate-authority focus:outline-none focus:bg-card-white shadow-inner cursor-pointer"
                                        id="langSelect">
                                        <option selected="" value="en-IN">English (India) — GMT+5:30</option>
                                        <option value="hi-IN">हिन्दी (Hindi) — GMT+5:30</option>
                                        <option value="bn-IN">বাংলা (Bengali) — GMT+5:30</option>
                                        <option value="en-US">English (United States) — GMT-5:00</option>
                                        <option value="en-GB">English (United Kingdom) — GMT+0:00</option>
                                    </select>
                                    <span
                                        class="material-symbols-outlined absolute right-3.5 text-[20px] text-on-surface-variant pointer-events-none">expand_more</span>
                                </div>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Affects catalog
                                    numerical formats, timestamps, and localized auction countdowns.</p>
                            </div>
                            <!-- Currency Preference -->
                            <div class="flex flex-col gap-2">
                                <label class="font-button-text text-body-small font-semibold text-slate-authority"
                                    for="currSelect">
                                    Settlement &amp; Bidding Currency
                                </label>
                                <div class="relative flex items-center">
                                    <span
                                        class="material-symbols-outlined absolute left-3.5 text-[20px] text-on-surface-variant pointer-events-none">currency_exchange</span>
                                    <select
                                        class="w-full appearance-none bg-surface-container-low pl-11 pr-10 py-3 rounded-xl font-body-regular text-body-regular text-slate-authority focus:outline-none focus:bg-card-white shadow-inner cursor-pointer"
                                        id="currSelect">
                                        <option selected="" value="INR">INR (₹) — Indian Rupee (Default Escrow)</option>
                                        <option value="USD">USD ($) — United States Dollar</option>
                                        <option value="EUR">EUR (€) — Eurozone</option>
                                        <option value="GBP">GBP (£) — British Pound</option>
                                        <option value="AED">AED (د.إ) — UAE Dirham</option>
                                    </select>
                                    <span
                                        class="material-symbols-outlined absolute right-3.5 text-[20px] text-on-surface-variant pointer-events-none">expand_more</span>
                                </div>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Real-time
                                    conversions reference Reserve Bank of India spot rates.</p>
                            </div>
                            <!-- Community Bio & Collector Statement (Span 2) -->
                            <div class="flex flex-col gap-2 md:col-span-2">
                                <div class="flex items-center justify-between">
                                    <label class="font-button-text text-body-small font-semibold text-slate-authority"
                                        for="bio">
                                        Community Bio &amp; Collector Statement
                                    </label>
                                    <span class="font-label-micro text-label-micro text-on-surface-variant"
                                        id="charCount">178 / 500 characters</span>
                                </div>
                                <div class="relative">
                                    <textarea
                                        class="w-full bg-surface-container-low p-4 rounded-xl font-body-regular text-body-regular text-slate-authority placeholder:text-on-surface-variant/60 focus:outline-none focus:bg-card-white shadow-inner focus:shadow-md transition-all leading-relaxed resize-y"
                                        id="bio" maxlength="500"
                                        oninput="document.getElementById('charCount').textContent = `${this.value.length} / 500 characters`"
                                        rows="4">Collector of artisanal keyboards, vintage rangefinders, and industrial audio gear. Active bidder in Bazaario live auctions and supporter of independent craft stalls across South Asia.</textarea>
                                </div>
                                <p class="font-label-micro text-label-micro text-on-surface-variant">Displayed to stall
                                    owners when you negotiate counter-offers or initiate high-value escrow handshakes.
                                </p>
                            </div>
                        </section>
                        <!-- Marketplace Preferences Strip -->
                        <section class="flex flex-col gap-4 pt-4 border-t border-primary-alpha-10">
                            <h3 class="font-title-card text-title-card text-slate-authority">Marketplace Privacy &amp;
                                Live Bid Visage</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Toggle 1 -->
                                <label
                                    class="flex items-start gap-3 p-3.5 rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-colors cursor-pointer">
                                    <input checked=""
                                        class="mt-1 w-4 h-4 text-amber-action rounded focus:ring-amber-action border-outline"
                                        type="checkbox" />
                                    <div class="flex flex-col">
                                        <span
                                            class="font-button-text text-body-small text-slate-authority font-medium">Show
                                            Verified Badge</span>
                                        <span class="font-label-micro text-label-micro text-on-surface-variant">Display
                                            green escrow checkmark on bids.</span>
                                    </div>
                                </label>
                                <!-- Toggle 2 -->
                                <label
                                    class="flex items-start gap-3 p-3.5 rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-colors cursor-pointer">
                                    <input checked=""
                                        class="mt-1 w-4 h-4 text-amber-action rounded focus:ring-amber-action border-outline"
                                        type="checkbox" />
                                    <div class="flex flex-col">
                                        <span
                                            class="font-button-text text-body-small text-slate-authority font-medium">Anonymous
                                            Auction Bidding</span>
                                        <span class="font-label-micro text-label-micro text-on-surface-variant">Mask
                                            handle as "Bidder #891" in public rooms.</span>
                                    </div>
                                </label>
                                <!-- Toggle 3 -->
                                <label
                                    class="flex items-start gap-3 p-3.5 rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-colors cursor-pointer">
                                    <input checked=""
                                        class="mt-1 w-4 h-4 text-amber-action rounded focus:ring-amber-action border-outline"
                                        type="checkbox" />
                                    <div class="flex flex-col">
                                        <span
                                            class="font-button-text text-body-small text-slate-authority font-medium">Stallmaker
                                            Direct Messaging</span>
                                        <span class="font-label-micro text-label-micro text-on-surface-variant">Allow
                                            verified sellers to send special reserved offers.</span>
                                    </div>
                                </label>
                            </div>
                        </section>
                        <!-- Form Action Buttons -->
                        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 pt-6">
                            <div
                                class="flex items-center gap-2 text-on-surface-variant font-label-micro text-label-micro">
                                <span class="material-symbols-outlined text-[16px] text-status-green">lock_clock</span>
                                All state mutations cryptographically logged to Vault #ESC-9041
                            </div>
                            <div class="flex items-center gap-3 w-full sm:w-auto">
                                <a class="flex-1 sm:flex-initial inline-flex items-center justify-center px-6 py-3 rounded-xl bg-surface-container-high hover:bg-surface-container text-slate-authority font-button-text text-button-text transition-colors"
                                    data-path="user-profile" href="#">
                                    Discard
                                </a>
                                <button
                                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl bg-amber-action hover:opacity-95 active:scale-[0.99] text-slate-authority font-button-text text-button-text font-bold shadow-lg shadow-amber-action/20 transition-all"
                                    type="submit">
                                    <span class="material-symbols-outlined text-[20px]">save</span>
                                    Save Credentials
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- Dimensional Trust & Security Assurance Banner -->
                    <section
                        class="bg-slate-authority text-canvas-ivory rounded-2xl p-6 md:p-8 relative overflow-hidden shadow-2xl">
                        <div
                            class="absolute -right-16 -bottom-16 w-64 h-64 bg-amber-action/10 rounded-full blur-2xl pointer-events-none">
                        </div>
                        <div
                            class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 rounded-xl bg-amber-action/15 text-amber-action flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[28px]">enhanced_encryption</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-headline-section text-headline-section text-canvas-ivory">
                                            256-Bit Cryptographic Cold-Vault Security</h3>
                                        <span
                                            class="font-label-micro text-label-micro bg-status-green/20 text-status-green px-2.5 py-0.5 rounded-full">ACTIVE
                                            DEFENSE</span>
                                    </div>
                                    <p class="font-body-small text-body-small text-canvas-ivory/75 max-w-3xl">
                                        Bazaario implements hardware-secured escrow sign-offs. Altering high-risk
                                        parameters (Payout Endpoints, Phone OTP, or Physical Delivery Coordinates)
                                        requires dual-token confirmation before releasing pending bid balances or stall
                                        commissions.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0 w-full lg:w-auto">
                                <button
                                    class="w-full lg:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-canvas-ivory/10 hover:bg-canvas-ivory/20 text-canvas-ivory font-button-text text-body-small transition-colors"
                                    onclick="alert('Viewing active cryptographic sessions: 2 devices connected (macOS Sonoma, iOS 17.5). All keys valid.')"
                                    type="button">
                                    <span class="material-symbols-outlined text-[18px]">key</span>
                                    Manage Security Keys
                                </button>
                            </div>
                        </div>
                    </section>
                    <!-- Floating AI Concierge Pill (Interactive trigger) -->
                    <div class="fixed bottom-6 right-6 z-40">
                        <button
                            class="group flex items-center gap-3 pl-3 pr-4 py-2.5 rounded-full bg-slate-authority text-canvas-ivory shadow-2xl shadow-slate-authority/30 hover:shadow-amber-action/20 border border-card-white/10 transition-all hover:scale-[1.02] active:scale-[0.98]"
                            onclick="const p = document.getElementById('aiPrompt'); p.classList.toggle('hidden');"
                            type="button">
                            <div
                                class="w-7 h-7 rounded-full bg-amber-action text-slate-authority flex items-center justify-center font-bold">
                                <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                            </div>
                            <div class="flex flex-col text-left">
                                <span
                                    class="font-button-text text-body-small font-semibold leading-tight group-hover:text-amber-action transition-colors">Ask
                                    Bazaario AI</span>
                                <span class="font-label-micro text-label-micro text-canvas-ivory/60">Need help
                                    configuring 2FA or currency?</span>
                            </div>
                        </button>
                        <!-- AI Mini Dialogue Bubble -->
                        <div class="hidden absolute bottom-16 right-0 w-80 p-4 rounded-2xl bg-card-white shadow-2xl border border-primary-alpha-10 flex flex-col gap-3"
                            id="aiPrompt">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex items-center gap-1.5 text-slate-authority font-button-text text-body-small font-bold">
                                    <span
                                        class="material-symbols-outlined text-[18px] text-amber-action">smart_toy</span>
                                    Marketplace Concierge
                                </div>
                                <button class="text-on-surface-variant hover:text-slate-authority"
                                    onclick="document.getElementById('aiPrompt').classList.add('hidden')" type="button">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                            <p class="font-body-small text-body-small text-on-surface-variant">
                                Hey Avijit, since your current active balance is ₹60,500.00 in the escrow vault,
                                changing currency to USD will auto-hedge existing live auction commitments at current
                                RBI mid-rates.
                            </p>
                            <button
                                class="w-full py-2 rounded-lg bg-surface-container-high text-slate-authority font-button-text text-body-small hover:bg-surface-container transition-colors"
                                onclick="document.getElementById('aiPrompt').classList.add('hidden')" type="button">
                                Got it, thanks!
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Success Confirmation Modal -->
            <div class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-authority/60 backdrop-blur-sm p-4"
                id="saveModal">
                <div
                    class="bg-card-white rounded-2xl max-w-md w-full p-6 shadow-2xl flex flex-col items-center text-center gap-4 animate-in fade-in zoom-in-95 duration-200">
                    <div
                        class="w-16 h-16 rounded-full bg-status-green/10 text-status-green flex items-center justify-center">
                        <span class="material-symbols-outlined text-[36px]"
                            style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h3 class="font-title-card text-title-card font-bold text-slate-authority">Credentials Updated
                            Successfully</h3>
                        <p class="font-body-small text-body-small text-on-surface-variant">
                            Your profile metadata has been cryptographically published across Bazaario live auctions and
                            seller stalls.
                        </p>
                    </div>
                    <div
                        class="w-full bg-surface-container-low p-3 rounded-xl flex items-center justify-between text-left font-label-micro text-label-micro text-slate-authority">
                        <span>Receipt Hash:</span>
                        <span class="font-mono text-on-surface-variant font-semibold">0x89F4...A904</span>
                    </div>
                    <button
                        class="w-full py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-button-text font-bold hover:opacity-95 transition-all"
                        onclick="document.getElementById('saveModal').classList.add('hidden');" type="button">
                        Return to Workspace
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