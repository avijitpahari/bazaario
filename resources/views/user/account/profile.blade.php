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
            <!-- Interactive Ambient Atmosphere Overlay -->
            <div class="relative w-full max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">
                <!-- Top Breadcrumbs & Escrow Vault Ribbon -->
                <div
                    class="flex flex-wrap items-center justify-between gap-4 p-3 rounded-xl bg-card-white/80 backdrop-blur-md border border-white/80 shadow-[0_4px_24px_rgba(15,23,42,0.04)]">
                    <nav class="flex items-center gap-2 font-label-micro text-label-micro text-on-surface-variant">
                        <a class="hover:text-amber-action transition-colors" href="#">Home</a>
                        <span class="text-on-surface-variant/40">/</span>
                        <a class="hover:text-amber-action transition-colors" href="#">Account</a>
                        <span class="text-on-surface-variant/40">/</span>
                        <span class="text-slate-authority font-semibold">My Profile</span>
                    </nav>
                    <div class="flex flex-wrap items-center gap-3 font-label-micro text-label-micro">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-status-green/10 text-status-green font-medium border border-status-green/20">
                            <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                            <span>ESCROW VERIFIED BUYER • VAULT #ESC-9041 • UID: BZ-891-IN</span>
                        </div>
                        <div
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-authority text-canvas-ivory shadow-sm">
                            <span class="material-symbols-outlined text-[15px] text-amber-action">lock</span>
                            <span>Escrow Vault: <strong
                                    class="text-amber-action tracking-wide">₹60,500.00</strong></span>
                            <a class="text-canvas-ivory/60 hover:text-canvas-ivory underline ml-1" href="#">Settings</a>
                        </div>
                    </div>
                </div>
                <!-- Hero Header with 3D Specular Dimension Backdrop -->
                <div
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-8 shadow-[0_12px_36px_rgba(15,23,42,0.05)] border border-white/90">
                    <div
                        class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-amber-action/10 blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute right-32 -bottom-20 w-64 h-64 rounded-full bg-primary-alpha-10 blur-2xl pointer-events-none">
                    </div>
                    <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div class="flex flex-col gap-2 max-w-2xl">
                            <span
                                class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">verified</span>
                                Account Management
                            </span>
                            <h1
                                class="font-display-hero text-headline-section md:text-display-hero text-slate-authority leading-tight">
                                My Profile
                            </h1>
                            <p class="font-body-regular text-body-regular text-on-surface-variant mt-1">
                                Manage your personal credentials, contact endpoints, delivery preferences, and
                                cryptographic escrow signatures.
                            </p>
                        </div>
                        <!-- Right Quick Action Hub -->
                        <div class="flex items-center gap-3">
                            <button
                                class="px-4 py-2.5 rounded-xl bg-card-white/90 backdrop-blur border border-slate-authority/10 font-button-text text-body-small text-slate-authority hover:border-amber-action shadow-sm transition-all flex items-center gap-2"
                                id="viewVaultBtn">
                                <span class="material-symbols-outlined text-[18px] text-amber-action">token</span>
                                Escrow Keys
                            </button>
                            <a class="px-5 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-[0_4px_14px_rgba(245,166,35,0.35)] hover:opacity-95 active:scale-[0.99] transition-all flex items-center gap-2"
                                href="#">
                                <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                Edit Details
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Main Grid: 2-Column Responsive Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <!-- LEFT COLUMN: Profile & Avatar 3D Glass Node (5 Cols) -->
                    <div class="lg:col-span-4 flex flex-col gap-6">
                        <!-- Avatar Glass Card -->
                        <div
                            class="relative overflow-hidden rounded-2xl bg-card-white/85 backdrop-blur-xl border border-white/90 p-6 shadow-[0_12px_32px_rgba(15,23,42,0.06)] flex flex-col items-center text-center">
                            <!-- 3D Specular Avatar Halo -->
                            <div class="relative mb-4 mt-2">
                                <div
                                    class="absolute -inset-2 rounded-full bg-gradient-to-tr from-amber-action via-amber-200 to-amber-action/20 opacity-70 blur-md animate-pulse">
                                </div>
                                <div
                                    class="relative w-28 h-28 rounded-full p-1 bg-gradient-to-b from-amber-action/80 to-slate-authority/30 shadow-[0_8px_20px_rgba(245,166,35,0.25)] flex items-center justify-center">
                                    <div
                                        class="w-full h-full rounded-full bg-slate-authority flex flex-col items-center justify-center text-card-white relative overflow-hidden">
                                        <span
                                            class="font-display-hero text-headline-section font-bold tracking-tight text-canvas-ivory">AP</span>
                                        <span
                                            class="absolute bottom-1 font-label-micro text-[9px] uppercase tracking-wider text-amber-action/90 font-mono">Bazaario
                                            ID</span>
                                    </div>
                                </div>
                                <!-- Verification Tag -->
                                <div class="absolute bottom-0 right-0 p-1.5 bg-card-white rounded-full shadow-md">
                                    <span class="material-symbols-outlined text-[18px] text-status-green block"
                                        style="font-variation-settings: 'FILL' 1;">verified</span>
                                </div>
                            </div>
                            <h2 class="font-title-card text-title-card font-bold text-slate-authority">Avijit Pahari
                            </h2>
                            <span
                                class="font-label-micro text-label-micro text-on-surface-variant mt-0.5">@avijit_craft</span>
                            <div
                                class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-action/15 border border-amber-action/30 text-slate-authority font-label-micro text-label-micro font-semibold">
                                <span class="material-symbols-outlined text-[14px] text-amber-action">award_star</span>
                                Active Buyer • Tier-1 Verified
                            </div>
                            <!-- Change Photo Button -->
                            <button
                                class="mt-5 w-full py-2.5 px-4 rounded-xl bg-surface-container/70 hover:bg-surface-container border border-slate-authority/10 font-button-text text-body-small font-medium text-slate-authority hover:border-amber-action transition-all flex items-center justify-center gap-2">
                                <span
                                    class="material-symbols-outlined text-[18px] text-on-surface-variant">photo_camera</span>
                                Change Profile Photo
                            </button>
                            <!-- Divider line softly represented by tonal shift -->
                            <div class="w-full h-px bg-surface-container-high my-5"></div>
                            <!-- Meta Key-Value List -->
                            <div class="w-full flex flex-col gap-3 font-body-small text-body-small text-left">
                                <div class="flex items-center justify-between py-1">
                                    <span class="text-on-surface-variant flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                                        Member Since
                                    </span>
                                    <span
                                        class="font-label-micro text-label-micro font-semibold text-slate-authority">March
                                        2024</span>
                                </div>
                                <div class="flex items-center justify-between py-1">
                                    <span class="text-on-surface-variant flex items-center gap-2">
                                        <span
                                            class="material-symbols-outlined text-[16px] text-status-green">verified_user</span>
                                        Account Status
                                    </span>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green bg-status-green/10 px-2 py-0.5 rounded-full font-semibold">KYC
                                        Level 3 ✓</span>
                                </div>
                                <div class="flex items-center justify-between py-1">
                                    <span class="text-on-surface-variant flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">currency_rupee</span>
                                        Default Currency
                                    </span>
                                    <span
                                        class="font-label-micro text-label-micro font-semibold text-slate-authority">INR
                                        (₹)</span>
                                </div>
                                <div class="flex items-center justify-between py-1">
                                    <span class="text-on-surface-variant flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">devices</span>
                                        Primary Session
                                    </span>
                                    <span
                                        class="font-label-micro text-label-micro font-semibold text-slate-authority">macOS
                                        • Chrome</span>
                                </div>
                            </div>
                        </div>
                        <!-- Quick Escrow Security Metrics Pill Container -->
                        <div
                            class="rounded-2xl bg-card-white/80 backdrop-blur-md border border-white/80 p-5 shadow-[0_8px_24px_rgba(15,23,42,0.04)] flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <h3
                                    class="font-title-card text-title-card font-semibold text-slate-authority flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-action text-[20px]">shield</span>
                                    Escrow Safeguards
                                </h3>
                                <span
                                    class="font-label-micro text-label-micro bg-status-green/10 text-status-green font-semibold px-2 py-0.5 rounded-full">Secure</span>
                            </div>
                            <div class="flex flex-col gap-2.5 pt-1">
                                <div
                                    class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low/80 border border-slate-authority/5">
                                    <div class="flex items-center gap-2.5">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-status-green">phonelink_lock</span>
                                        <span
                                            class="font-body-small text-body-small text-slate-authority font-medium">2FA
                                            Authenticator</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green font-semibold">Active</span>
                                </div>
                                <div
                                    class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low/80 border border-slate-authority/5">
                                    <div class="flex items-center gap-2.5">
                                        <span class="material-symbols-outlined text-[18px] text-status-green">sms</span>
                                        <span
                                            class="font-body-small text-body-small text-slate-authority font-medium">Escrow
                                            Release OTP</span>
                                    </div>
                                    <span
                                        class="font-label-micro text-label-micro text-status-green font-semibold">Enabled</span>
                                </div>
                            </div>
                            <a class="font-label-micro text-label-micro text-amber-action font-semibold text-center hover:underline pt-1"
                                href="#">
                                Audit Escrow Security Log →
                            </a>
                        </div>
                    </div>
                    <!-- RIGHT COLUMN: Personal Info & Glass Nodes (8 Cols) -->
                    <div class="lg:col-span-8 flex flex-col gap-6">
                        <!-- Personal & Security Information Card -->
                        <div
                            class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 md:p-8 shadow-[0_12px_36px_rgba(15,23,42,0.05)] flex flex-col gap-6">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-surface-container">
                                <div>
                                    <span
                                        class="font-label-eyebrow text-label-eyebrow uppercase tracking-wider text-amber-action font-semibold">Credential
                                        Layer</span>
                                    <h2
                                        class="font-title-card text-title-card md:text-headline-section font-bold text-slate-authority mt-0.5">
                                        Personal Credentials</h2>
                                </div>
                                <button
                                    class="self-start sm:self-auto px-4 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm hover:opacity-95 active:scale-[0.99] transition-all flex items-center gap-2"
                                    id="toggleEditForm">
                                    <span class="material-symbols-outlined text-[17px]">edit</span>
                                    Edit Profile
                                </button>
                            </div>
                            <!-- Glass Form Container -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Full Name Node -->
                                <div
                                    class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 transition-all hover:border-amber-action">
                                    <div
                                        class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                                        <span>FULL NAME</span>
                                        <span class="material-symbols-outlined text-[15px]">person</span>
                                    </div>
                                    <p class="font-title-card text-title-card font-semibold text-slate-authority">Avijit
                                        Pahari</p>
                                    <span class="text-[11px] text-on-surface-variant font-mono">Official identity per
                                        KYC verification</span>
                                </div>
                                <!-- Username Node -->
                                <div
                                    class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 transition-all hover:border-amber-action">
                                    <div
                                        class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                                        <span>MARKETPLACE HANDLE</span>
                                        <span class="material-symbols-outlined text-[15px]">alternate_email</span>
                                    </div>
                                    <p class="font-title-card text-title-card font-semibold text-slate-authority">
                                        @avijit_craft</p>
                                    <span class="text-[11px] text-on-surface-variant font-mono">Public auction &amp;
                                        shop identifier</span>
                                </div>
                                <!-- Email Address Node -->
                                <div
                                    class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 transition-all hover:border-amber-action">
                                    <div
                                        class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                                        <span>PRIMARY EMAIL</span>
                                        <span
                                            class="inline-flex items-center gap-1 font-label-micro text-[11px] text-status-green font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            Verified
                                        </span>
                                    </div>
                                    <p
                                        class="font-body-regular text-body-regular font-semibold text-slate-authority truncate">
                                        avijit@example.com</p>
                                    <span class="text-[11px] text-on-surface-variant font-mono">Escrow receipts &amp;
                                        dispatch notifications</span>
                                </div>
                                <!-- Phone Number Node -->
                                <div
                                    class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 transition-all hover:border-amber-action">
                                    <div
                                        class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                                        <span>REGISTERED PHONE</span>
                                        <span
                                            class="inline-flex items-center gap-1 font-label-micro text-[11px] text-status-green font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">lock</span>
                                            OTP Locked
                                        </span>
                                    </div>
                                    <p
                                        class="font-body-regular text-body-regular font-semibold text-slate-authority font-mono">
                                        +91 98200 •••••</p>
                                    <span class="text-[11px] text-on-surface-variant font-mono">Masked for privacy •
                                        Active for delivery PIN</span>
                                </div>
                                <!-- Language & Region Node -->
                                <div
                                    class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 md:col-span-2 transition-all hover:border-amber-action">
                                    <div
                                        class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                                        <span>REGIONAL PREFERENCES</span>
                                        <span class="material-symbols-outlined text-[15px]">globe_asia</span>
                                    </div>
                                    <p class="font-body-regular text-body-regular font-medium text-slate-authority">
                                        English (India) • GMT+5:30 (IST) • Metric Units</p>
                                    <span class="text-[11px] text-on-surface-variant font-mono">Determines auction
                                        countdown clocks and currency conversions</span>
                                </div>
                                <!-- Bio / About Node -->
                                <div
                                    class="p-4 rounded-xl bg-surface-container-low/90 border border-slate-authority/10 flex flex-col gap-1 md:col-span-2 transition-all hover:border-amber-action">
                                    <div
                                        class="flex items-center justify-between text-on-surface-variant font-label-micro text-label-micro">
                                        <span>COMMUNITY BIO &amp; COLLECTOR STATEMENT</span>
                                        <span class="material-symbols-outlined text-[15px]">format_quote</span>
                                    </div>
                                    <p class="font-body-regular text-body-regular text-slate-authority leading-relaxed">
                                        "Collector of artisanal keyboards, vintage rangefinders, and industrial audio
                                        gear. Active bidder in Bazaario live auctions and supporter of independent craft
                                        stalls across South Asia."
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Saved Delivery Addresses Section -->
                        <div
                            class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 md:p-8 shadow-[0_12px_36px_rgba(15,23,42,0.05)] flex flex-col gap-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-amber-action/10 flex items-center justify-center text-amber-action">
                                        <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                                    </div>
                                    <div>
                                        <h3 class="font-title-card text-title-card font-bold text-slate-authority">Saved
                                            Delivery Addresses</h3>
                                        <p class="font-body-small text-body-small text-on-surface-variant">Validated
                                            postal drop-points for insured physical goods.</p>
                                    </div>
                                </div>
                                <a class="hidden sm:inline-flex items-center gap-1 font-button-text text-body-small font-semibold text-slate-authority hover:text-amber-action transition-colors"
                                    href="#">
                                    Manage All 3 Addresses
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                <!-- Address 1: Primary Home -->
                                <div
                                    class="relative p-4 rounded-xl bg-surface-container-low/70 border border-amber-action/40 shadow-sm flex flex-col justify-between gap-3">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-amber-action">home</span>
                                            <span
                                                class="font-title-card text-body-regular font-bold text-slate-authority">Primary
                                                Residence</span>
                                        </div>
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-amber-action text-slate-authority font-label-micro text-label-micro font-bold">DEFAULT</span>
                                    </div>
                                    <p class="font-body-small text-body-small text-on-surface-variant leading-relaxed">
                                        Contai, Purba Medinipur,<br />
                                        West Bengal — <span
                                            class="font-mono text-slate-authority font-semibold">721401</span>, India
                                    </p>
                                    <div
                                        class="flex items-center justify-between pt-2 border-t border-slate-authority/5 font-label-micro text-label-micro text-on-surface-variant">
                                        <span>Receiver: Avijit Pahari</span>
                                        <span class="text-status-green font-medium">Geo-verified ✓</span>
                                    </div>
                                </div>
                                <!-- Address 2: Work -->
                                <div
                                    class="relative p-4 rounded-xl bg-surface-container-low/70 border border-slate-authority/10 flex flex-col justify-between gap-3 hover:border-amber-action transition-all">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-on-surface-variant">apartment</span>
                                            <span
                                                class="font-title-card text-body-regular font-bold text-slate-authority">Work
                                                Studio</span>
                                        </div>
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-micro text-label-micro">OFFICE</span>
                                    </div>
                                    <p class="font-body-small text-body-small text-on-surface-variant leading-relaxed">
                                        Sector V, Salt Lake Electronics Complex,<br />
                                        Kolkata — <span
                                            class="font-mono text-slate-authority font-semibold">700091</span>, India
                                    </p>
                                    <div
                                        class="flex items-center justify-between pt-2 border-t border-slate-authority/5 font-label-micro text-label-micro text-on-surface-variant">
                                        <span>Receiver: Avijit (Studio 4B)</span>
                                        <span class="text-on-surface-variant/70">Mon–Fri Drop</span>
                                    </div>
                                </div>
                            </div>
                            <div class="sm:hidden text-center pt-2">
                                <a class="font-button-text text-body-small font-semibold text-slate-authority hover:text-amber-action"
                                    href="#">
                                    Manage All 3 Addresses →
                                </a>
                            </div>
                        </div>
                        <!-- Decentralized Escrow Security & Smart Vault Assurance -->
                        <div
                            class="rounded-2xl bg-slate-authority text-canvas-ivory p-6 md:p-8 shadow-[0_16px_40px_rgba(15,23,42,0.18)] flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                            <!-- Background glow -->
                            <div
                                class="absolute -left-10 -bottom-10 w-48 h-48 rounded-full bg-amber-action/10 blur-2xl pointer-events-none">
                            </div>
                            <div class="flex items-start gap-4 relative z-10">
                                <div
                                    class="w-12 h-12 rounded-xl bg-canvas-ivory/10 border border-white/10 flex items-center justify-center text-amber-action shrink-0">
                                    <span class="material-symbols-outlined text-[28px]">shield_with_heart</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="font-label-eyebrow text-label-eyebrow uppercase tracking-wider text-amber-action font-semibold">Decentralized
                                            Security</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-status-green/20 text-status-green font-label-micro text-label-micro font-medium border border-status-green/30">Active
                                            Shield</span>
                                    </div>
                                    <h3
                                        class="font-title-card text-title-card md:text-headline-section font-bold text-canvas-ivory">
                                        256-Bit Cryptographic Cold-Vault Security</h3>
                                    <p class="font-body-small text-body-small text-canvas-ivory/70 max-w-lg">
                                        Your Bazaario signature keys authenticate buyer releases directly into seller
                                        custody only upon mutual delivery verification.
                                    </p>
                                </div>
                            </div>
                            <div class="relative z-10 shrink-0 w-full md:w-auto">
                                <button
                                    class="w-full md:w-auto px-5 py-3 rounded-xl bg-card-white/10 hover:bg-card-white/20 border border-white/20 text-canvas-ivory font-button-text text-body-small font-medium shadow-sm transition-all flex items-center justify-center gap-2">
                                    <span
                                        class="material-symbols-outlined text-[18px] text-amber-action">download_for_offline</span>
                                    Download Security Certificate (PDF)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Floating AI Assistant Prompt Trigger Glass Dock -->
                <div class="fixed bottom-6 right-6 z-40">
                    <div class="group relative flex items-center">
                        <button
                            class="flex items-center gap-3 pl-4 pr-5 py-3 rounded-full bg-slate-authority/90 backdrop-blur-xl border border-white/20 text-canvas-ivory shadow-[0_8px_30px_rgba(15,23,42,0.3)] hover:border-amber-action transition-all transform hover:-translate-y-0.5"
                            id="aiAssistBtn">
                            <span
                                class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-action to-yellow-300 flex items-center justify-center text-slate-authority shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                            </span>
                            <span class="font-button-text text-body-small font-semibold tracking-wide">
                                ✦ Ask Bazaario AI: <span class="font-normal text-canvas-ivory/80">"Update notification
                                    preferences or email"</span>
                            </span>
                        </button>
                    </div>
                </div>
                <!-- Modal for Quick Profile Edit (Vanilla JS Toggleable) -->
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-authority/60 backdrop-blur-sm hidden p-4"
                    id="profileEditModal">
                    <div
                        class="bg-card-white rounded-2xl max-w-lg w-full p-6 md:p-8 shadow-[0_24px_48px_rgba(15,23,42,0.2)] border border-white flex flex-col gap-5 animate-in fade-in zoom-in duration-150">
                        <div class="flex items-center justify-between border-b border-surface-container pb-3">
                            <div class="flex items-center gap-2">
                                <span
                                    class="material-symbols-outlined text-amber-action text-[22px]">manage_accounts</span>
                                <h3 class="font-title-card text-title-card font-bold text-slate-authority">Quick Edit
                                    Profile</h3>
                            </div>
                            <button
                                class="p-1 rounded-full hover:bg-surface-container text-on-surface-variant transition-colors"
                                id="closeModalBtn">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>
                        <form class="flex flex-col gap-4 font-body-small text-body-small" id="profileForm"
                            onsubmit="event.preventDefault(); document.getElementById('profileEditModal').classList.add('hidden');">
                            <div class="flex flex-col gap-1.5">
                                <label class="font-semibold text-slate-authority">Display Full Name</label>
                                <input
                                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-2.5 text-slate-authority focus:outline-none focus:border-amber-action"
                                    type="text" value="Avijit Pahari" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="font-semibold text-slate-authority">Marketplace Handle</label>
                                <input
                                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-2.5 text-slate-authority focus:outline-none focus:border-amber-action"
                                    type="text" value="@avijit_craft" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="font-semibold text-slate-authority">Bio Statement</label>
                                <textarea
                                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl p-3 text-slate-authority focus:outline-none focus:border-amber-action resize-none"
                                    rows="3">Collector of artisanal keyboards, vintage rangefinders, and industrial audio gear. Active bidder in Bazaario live auctions.</textarea>
                            </div>
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button
                                    class="px-4 py-2 rounded-xl border border-slate-authority/20 text-slate-authority font-button-text hover:bg-surface-container transition-colors"
                                    id="cancelModalBtn" type="button">
                                    Cancel
                                </button>
                                <button
                                    class="px-5 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text font-semibold shadow-sm hover:opacity-95 transition-opacity"
                                    type="submit">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                (function initProfileInteractions() {
                    const toggleBtn = document.getElementById('toggleEditForm');
                    const modal = document.getElementById('profileEditModal');
                    const closeBtn = document.getElementById('closeModalBtn');
                    const cancelBtn = document.getElementById('cancelModalBtn');
                    const aiBtn = document.getElementById('aiAssistBtn');

                    if (toggleBtn && modal) {
                        toggleBtn.addEventListener('click', () => {
                            modal.classList.remove('hidden');
                        });
                    }

                    const closeModal = () => {
                        if (modal) modal.classList.add('hidden');
                    };

                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

                    if (aiBtn) {
                        aiBtn.addEventListener('click', () => {
                            alert('Bazaario AI: "Hello Avijit! You can prompt me to update contact channels, adjust escrow release timeouts, or switch default currencies anytime."');
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