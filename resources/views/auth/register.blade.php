<!DOCTYPE html>
<html lang="en" style="">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account — Bazaario</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
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

        /* ── OTP digit boxes ── */
        .otp-digit {
            width: 46px;
            height: 56px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            font-family: 'JetBrains Mono', 'Space Grotesk', monospace;
            border: 1.5px solid rgba(15,23,42,0.12);
            border-radius: 12px;
            background: #fafaf9;
            color: #0F172A;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease,
                        background 0.18s ease, transform 0.12s ease;
            caret-color: transparent;
            cursor: text;
            box-shadow: 0 1px 3px rgba(15,23,42,0.06), inset 0 1px 2px rgba(15,23,42,0.04);
            -webkit-appearance: none;
        }
        /* Hover — subtle lift */
        .otp-digit:hover {
            border-color: rgba(245,166,35,0.45);
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(245,166,35,0.12), inset 0 1px 2px rgba(15,23,42,0.03);
        }
        /* Focus — amber glow, no browser outline */
        .otp-digit:focus {
            border-color: #F5A623;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(245,166,35,0.22),
                        0 2px 10px rgba(245,166,35,0.18),
                        inset 0 1px 2px rgba(15,23,42,0.03);
            transform: translateY(-1px) scale(1.04);
        }
        /* Filled — amber tint background with top accent line */
        .otp-digit.filled {
            border-color: #F5A623;
            background: linear-gradient(160deg, #fffdf4 0%, #fff9e8 100%);
            box-shadow: 0 0 0 1.5px rgba(245,166,35,0.30),
                        0 3px 10px rgba(245,166,35,0.14);
            position: relative;
        }
        /* Filled — pop-in animation */
        .otp-digit.filled.just-filled {
            animation: digitPop 0.18s cubic-bezier(0.34,1.56,0.64,1) forwards;
        }
        @keyframes digitPop {
            0%   { transform: scale(0.88); }
            100% { transform: scale(1); }
        }

        /* ── Slide transition for OTP panel ── */
        #otp-panel {
            display: none;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        #otp-panel.visible {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        #reg-form-panel {
            transition: opacity 0.2s ease;
        }
        #reg-form-panel.fading {
            opacity: 0;
            pointer-events: none;
        }

        /* ── Avatar uploader ── */
        #avatar-preview {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
        }
        #avatar-placeholder {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #f5f3ee;
            border: 2px dashed rgba(15,23,42,0.20);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color 0.15s;
        }
        #avatar-placeholder:hover {
            border-color: #F5A623;
        }
        [x-cloak] { display: none !important; }
    </style>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script
        id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { colors: { "surface-dim": "#dbdad5", "card-white": "#FFFFFF", "on-error-container": "#93000a", "on-tertiary-fixed-variant": "#633f00", "primary-alpha-70": "rgba(15, 23, 42, 0.70)", "outline": "#76777d", "on-tertiary-fixed": "#291800", "on-primary-fixed-variant": "#3f465c", "on-secondary-fixed-variant": "#005320", "slate-authority": "#0F172A", "inverse-on-surface": "#f2f1ec", "tertiary": "#000000", "tertiary-fixed": "#ffddb4", "error": "#ba1a1a", "tertiary-fixed-dim": "#ffb955", "surface-variant": "#e4e2de", "on-tertiary-container": "#b57700", "secondary-fixed": "#7ffc97", "ivory-alpha-70": "rgba(255, 253, 248, 0.70)", "on-secondary": "#ffffff", "on-primary": "#ffffff", "amber-action": "#F5A623", "on-primary-container": "#7c839b", "on-secondary-container": "#007230", "surface-container-low": "#f5f3ee", "surface-container": "#efeee9", "tertiary-container": "#291800", "inverse-primary": "#bec6e0", "secondary-container": "#7cf994", "on-secondary-fixed": "#002109", "surface-container-highest": "#e4e2de", "on-background": "#1b1c19", "primary-alpha-20": "rgba(15, 23, 42, 0.20)", "canvas-ivory": "#FFFDF8", "error-container": "#ffdad6", "primary-container": "#131b2e", "on-surface": "#1b1c19", "surface-tint": "#565e74", "on-error": "#ffffff", "surface-container-lowest": "#ffffff", "outline-variant": "#c6c6cd", "on-primary-fixed": "#131b2e", "background": "#fbf9f4", "status-green": "#16A34A", "primary": "#000000", "on-tertiary": "#ffffff", "secondary-fixed-dim": "#62df7d", "surface-bright": "#fbf9f4", "primary-fixed-dim": "#bec6e0", "primary-fixed": "#dae2fd", "inverse-surface": "#30312e", "primary-alpha-10": "rgba(15, 23, 42, 0.10)", "on-surface-variant": "#45464d", "surface": "#fbf9f4", "surface-container-high": "#eae8e3", "secondary": "#006e2d" }, borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, spacing: { "gutter-md": "1.5rem", "section-final-bottom": "4rem", "gutter-sm": "1rem", "section-hero-top": "4rem", "container-max": "72rem", "section-hero-bottom": "2.5rem", "card-padding": "1.25rem", "gutter-lg": "2.5rem", "gutter-xs": "0.5rem", "section-interior-y": "3.5rem", "gutter-xl": "3.5rem", "banner-padding": "2.5rem" }, fontFamily: { "label-eyebrow": ["JetBrains Mono"], "body-regular": ["Inter"], "display-hero": ["Space Grotesk"], "title-card": ["Space Grotesk"], "label-micro": ["JetBrains Mono"], "body-small": ["Inter"], "button-text": ["Inter"], "body-lead": ["Inter"], "headline-section": ["Space Grotesk"], "display-hero-mobile": ["Space Grotesk"] }, fontSize: { "label-eyebrow": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "500" }], "body-regular": ["16px", { "lineHeight": "24px", "fontWeight": "400" }], "display-hero": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }], "title-card": ["18px", { "lineHeight": "24px", "letterSpacing": "-0.01em", "fontWeight": "600" }], "label-micro": ["10px", { "lineHeight": "14px", "letterSpacing": "0.025em", "fontWeight": "500" }], "body-small": ["14px", { "lineHeight": "20px", "fontWeight": "400" }], "button-text": ["16px", { "lineHeight": "24px", "fontWeight": "600" }], "body-lead": ["18px", { "lineHeight": "28px", "fontWeight": "400" }], "headline-section": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700" }], "display-hero-mobile": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700" }] } } } };</script>
</head>

<body
    class="bg-canvas-ivory font-body-regular text-body-regular text-on-surface min-h-screen relative selection:bg-amber-action/30 selection:text-slate-authority overflow-x-hidden">
    <!-- Sleek Floating Pill Top Navigation -->
    @include('components.nav', ['currentRoute' => Route::currentRouteName()])
    <!-- Ambient 3D Background Objects & Isometric Showcase Halo -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <!-- Glowing ambient light orbs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-action/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 -right-20 w-[500px] h-[500px] bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-[600px] h-[600px] bg-amber-action/15 rounded-full blur-3xl"></div>
        <!-- Floating Isometric 3D Visual Accents from Gallery Images -->
    </div>
    <main class="relative z-10 w-full pt-6 sm:pt-8 pb-12 px-4 flex flex-col items-center justify-center min-h-screen">
        <div class="max-w-container-max mx-auto w-full px-4 sm:px-6 py-2">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <!-- LEFT COLUMN: 3D Showcase, Headline, and Value Props -->
                <div class="lg:col-span-7 flex flex-col space-y-6">
                    <!-- Eyebrow & Hero Title -->
                    <div>
                        <div
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-action/15 border border-amber-action/30 mb-4 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-amber-action animate-pulse"></span>
                            <span
                                class="font-label-eyebrow text-label-eyebrow uppercase text-amber-action tracking-wider font-semibold">BAZAARIO
                                MARKETPLACE PLATFORM</span>
                        </div>
                        <h1
                            class="font-display-hero text-3xl sm:text-4xl lg:text-display-hero text-slate-authority tracking-tight leading-tight mb-3">
                            Shop <em class="text-amber-action not-italic font-bold">smarter</em>. Sell <em
                                class="text-amber-action not-italic font-bold">bigger</em>.
                        </h1>
                        <p
                            class="font-body-lead text-[16px] sm:text-[18px] text-primary-alpha-70 leading-relaxed max-w-2xl">
                            Join thousands of verified buyers and independent sellers trading on an authentic, curated
                            digital marketplace built for craftsmen and collectors.
                        </p>
                    </div>
                    <!-- 3D Dimensional Showcase Card with Floating Glass Badges -->
                    <div
                        class="relative rounded-3xl overflow-hidden bg-gradient-to-b from-card-white/80 to-surface-container-low/60 border border-card-white shadow-[0_25px_60px_-15px_rgba(15,23,42,0.18)] p-3 group">
                        <div
                            class="relative aspect-[16/11] w-full rounded-2xl overflow-hidden bg-surface-container-high/40">
                            <img alt="3D Marketplace Showcase"
                                class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
                                src="{{ asset('images/screen.png') }}">
                            <!-- Floating Badge 1: Real-time Latency (Top Left) -->
                            <div
                                class="absolute top-4 left-4 bg-card-white/95 backdrop-blur-md px-3 py-1.5 rounded-full border border-primary-alpha-10 shadow-md flex items-center gap-2 pointer-events-none">
                                <span class="w-2 h-2 rounded-full bg-status-green animate-pulse"></span>
                                <span class="font-label-micro text-[11px] text-slate-authority font-semibold">0.2s
                                    Latency</span>
                                <span class="font-label-micro text-[10px] text-primary-alpha-70 hidden sm:inline">·
                                    Real-time floor engine</span>
                            </div>
                            <!-- Floating Badge 2: Escrow Protection (Top Right) -->
                            <div
                                class="absolute top-4 right-4 bg-card-white/95 backdrop-blur-md px-3 py-1.5 rounded-full border border-primary-alpha-10 shadow-md flex items-center gap-1.5 pointer-events-none">
                                <span
                                    class="material-symbols-outlined text-[15px] text-amber-action">verified_user</span>
                                <span class="font-label-micro text-[11px] text-slate-authority font-semibold">100%
                                    Escrow</span>
                                <span class="font-label-micro text-[10px] text-primary-alpha-70 hidden sm:inline">·
                                    Dispute Protection</span>
                            </div>
                            <!-- Floating Badge 3: Active Stores (Bottom Left) -->
                            <div
                                class="absolute bottom-4 left-4 bg-slate-authority/90 text-canvas-ivory backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/10 shadow-lg flex items-center gap-2 pointer-events-none">
                                <span class="w-2 h-2 rounded-full bg-amber-action"></span>
                                <span class="font-label-micro text-[11px] text-canvas-ivory font-semibold">14k+ Active
                                    Stores</span>
                                <span
                                    class="font-label-micro text-[9px] bg-amber-action text-slate-authority px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">LIVE</span>
                            </div>
                        </div>
                    </div>
                    <!-- Feature Highlights Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-1">
                        <div
                            class="p-4 rounded-2xl bg-card-white/80 backdrop-blur-sm border border-primary-alpha-10 shadow-sm flex flex-col gap-2 hover:border-amber-action/40 transition-colors">
                            <div
                                class="w-9 h-9 rounded-xl bg-amber-action/15 text-amber-action flex items-center justify-center font-bold">
                                <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                            </div>
                            <div>
                                <h4 class="font-title-card text-[15px] font-bold text-slate-authority mb-0.5">Smart
                                    Shopping</h4>
                                <p class="font-body-small text-[12px] text-primary-alpha-70 leading-snug">Curated maker
                                    stalls with verified provenance records.</p>
                            </div>
                        </div>
                        <div
                            class="p-4 rounded-2xl bg-card-white/80 backdrop-blur-sm border border-primary-alpha-10 shadow-sm flex flex-col gap-2 hover:border-amber-action/40 transition-colors">
                            <div
                                class="w-9 h-9 rounded-xl bg-status-green/15 text-status-green flex items-center justify-center font-bold">
                                <span class="material-symbols-outlined text-[20px]">psychology</span>
                            </div>
                            <div>
                                <h4 class="font-title-card text-[15px] font-bold text-slate-authority mb-0.5">AI
                                    Insights</h4>
                                <p class="font-body-small text-[12px] text-primary-alpha-70 leading-snug">Real-time
                                    valuations and market trend discovery.</p>
                            </div>
                        </div>
                        <div
                            class="p-4 rounded-2xl bg-card-white/80 backdrop-blur-sm border border-primary-alpha-10 shadow-sm flex flex-col gap-2 hover:border-amber-action/40 transition-colors">
                            <div
                                class="w-9 h-9 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center font-bold">
                                <span class="material-symbols-outlined text-[20px]">gavel</span>
                            </div>
                            <div>
                                <h4 class="font-title-card text-[15px] font-bold text-slate-authority mb-0.5">Live
                                    Auctions</h4>
                                <p class="font-body-small text-[12px] text-primary-alpha-70 leading-snug">Sub-second
                                    room floor bids with anti-sniping rules.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- RIGHT COLUMN: Elevated Registration Card -->
                <div class="lg:col-span-5">
                    <div
                        class="bg-card-white rounded-3xl p-6 sm:p-8 shadow-[0_25px_60px_-15px_rgba(15,23,42,0.14)] border border-primary-alpha-10 relative">
                        <!-- Card Header -->
                        <div class="flex items-center justify-between mb-2">
                            <span
                                class="font-label-eyebrow text-[11px] uppercase text-amber-action tracking-wider font-semibold">
                                GET STARTED
                            </span>
                            <span
                                class="font-label-micro text-label-micro uppercase bg-status-green/10 text-status-green px-2.5 py-1 rounded-full font-bold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-status-green"></span>
                                Free Membership
                            </span>
                        </div>
                        <h2 class="font-headline-section text-2xl sm:text-3xl text-slate-authority tracking-tight mb-1">
                            Create your account
                        </h2>
                        <p class="font-body-small text-body-small text-primary-alpha-70 mb-5">
                            Trade with verified collectors or launch your digital storefront in seconds.
                        </p>

                        <!-- ── Error banner (shown on JS/validation errors) ── -->
                        <div id="form-error-banner"
                            class="hidden mb-4 flex items-start gap-2 px-3.5 py-2.5 rounded-xl bg-red-50 border border-red-200 text-red-700 font-body-small text-[13px]">
                            <span class="material-symbols-outlined text-[18px] mt-0.5 shrink-0">error</span>
                            <span id="form-error-text"></span>
                        </div>

                        <!-- ══════════════════════════════════════════════════
                             STEP 1 — Registration Form
                        ══════════════════════════════════════════════════ -->
                        <div id="reg-form-panel">
                            <form id="reg-form" class="space-y-3.5" enctype="multipart/form-data" action="{{ route('register.send-otp') }}" method="POST">                                @csrf

                                <!-- ── Profile Image Upload ── -->
                                <div>
                                    <span class="font-label-eyebrow text-[11px] uppercase text-slate-authority/80 mb-2 block">
                                        Profile Photo <span class="text-primary-alpha-70 normal-case font-normal tracking-normal">(optional)</span>
                                    </span>
                                    <div class="flex items-center gap-4">
                                        <!-- Avatar circle -->
                                        <div class="relative shrink-0" id="avatar-wrap">
                                            <!-- Placeholder shown before upload -->
                                            <div id="avatar-placeholder" onclick="document.getElementById('profile_image_input').click()">
                                                <span class="material-symbols-outlined text-[28px] text-slate-authority/30">person</span>
                                            </div>
                                            <!-- Preview shown after selection -->
                                            <img id="avatar-preview" src="" alt=""
                                                style="display:none; width:72px; height:72px; border-radius:9999px; object-fit:cover; border:2px solid rgba(245,166,35,0.4); cursor:pointer;"
                                                onclick="document.getElementById('profile_image_input').click()">
                                            <!-- Edit overlay badge -->
                                            <button type="button"
                                                onclick="document.getElementById('profile_image_input').click()"
                                                class="absolute bottom-0 right-0 w-6 h-6 rounded-full bg-amber-action text-slate-authority flex items-center justify-center shadow-sm border-2 border-card-white hover:brightness-105 transition-all"
                                                title="Upload photo">
                                                <span class="material-symbols-outlined text-[13px]">photo_camera</span>
                                            </button>
                                        </div>
                                        <!-- Helper text -->
                                        <div>
                                            <p class="font-body-small text-[12px] text-slate-authority font-medium mb-0.5">Upload profile photo</p>
                                            <p class="font-label-micro text-[10px] text-primary-alpha-70">JPG, PNG, GIF or WEBP</p>
                                            <button type="button"
                                                onclick="document.getElementById('profile_image_input').click()"
                                                class="mt-1.5 font-label-eyebrow text-[10px] uppercase tracking-wide text-amber-action hover:underline font-semibold">
                                                Browse file
                                            </button>
                                        </div>
                                        <!-- Hidden file input -->
                                        <input type="file" id="profile_image_input" name="profile_image"
                                            accept="image/jpeg,image/png,image/gif,image/webp" class="sr-only">
                                    </div>
                                </div>

                                <!-- Full Name -->
                                <div>
                                    <label
                                        class="font-label-eyebrow text-[11px] uppercase text-slate-authority/80 mb-1 block"
                                        for="reg-fullname">Full Name</label>
                                    <div class="relative">
                                        <input
                                            class="w-full px-3.5 py-2.5 rounded-xl border border-primary-alpha-20 bg-card-white text-slate-authority placeholder:text-slate-authority/40 focus:outline-none focus:border-amber-action focus:ring-1 focus:ring-amber-action font-body-regular text-[13px] transition-colors"
                                            id="reg-fullname" placeholder="Eleanor Vance" required="" type="text"
                                            name="name">
                                        <span
                                            class="material-symbols-outlined absolute right-3 top-2.5 text-slate-authority/40 pointer-events-none text-[18px]">badge</span>
                                    </div>
                                </div>
                                <!-- Email Address -->
                                <div>
                                    <label
                                        class="font-label-eyebrow text-[11px] uppercase text-slate-authority/80 mb-1 block"
                                        for="reg-email">Email Address</label>
                                    <div class="relative">
                                        <input
                                            class="w-full px-3.5 py-2.5 rounded-xl border border-primary-alpha-20 bg-card-white text-slate-authority placeholder:text-slate-authority/40 focus:outline-none focus:border-amber-action focus:ring-1 focus:ring-amber-action font-body-regular text-[13px] transition-colors"
                                            id="reg-email" placeholder="eleanor@vance-atelier.com" required="" type="email"
                                            name="email">
                                        <span
                                            class="material-symbols-outlined absolute right-3 top-2.5 text-slate-authority/40 pointer-events-none text-[18px]">alternate_email</span>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="font-label-eyebrow text-[11px] uppercase text-slate-authority/80 mb-1 block"
                                        for="reg-phone">Phone Number</label>
                                    <div class="relative">
                                        <input
                                            class="w-full px-3.5 py-2.5 rounded-xl border border-primary-alpha-20 bg-card-white text-slate-authority placeholder:text-slate-authority/40 focus:outline-none focus:border-amber-action focus:ring-1 focus:ring-amber-action font-body-regular text-[13px] transition-colors"
                                            id="reg-phone" placeholder="+91 9149285891" type="tel" name="phone">
                                        <span
                                            class="material-symbols-outlined absolute right-3 top-2.5 text-slate-authority/40 pointer-events-none text-[18px]">call</span>
                                    </div>
                                </div>
                                <!-- Password Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                    <div>
                                        <label
                                            class="font-label-eyebrow text-[11px] uppercase text-slate-authority/80 mb-1 block"
                                            for="reg-password">Password</label>
                                        <div class="relative">
                                            <input
                                                class="w-full pl-3.5 pr-9 py-2.5 rounded-xl border border-primary-alpha-20 bg-card-white text-slate-authority placeholder:text-slate-authority/40 focus:outline-none focus:border-amber-action focus:ring-1 focus:ring-amber-action font-body-regular text-[13px] transition-colors"
                                                id="reg-password" placeholder="Min. 8 characters" required=""
                                                type="password" name="password">
                                            <button aria-label="Toggle password visibility"
                                                class="absolute right-2.5 top-3.5 text-slate-authority/50 hover:text-slate-authority focus:outline-none"
                                                onclick="const input = document.getElementById('reg-password'); const icon = document.getElementById('reg-pwd-icon'); if(input.type==='password'){input.type='text'; icon.textContent='visibility_off';}else{input.type='password'; icon.textContent='visibility';}"
                                                type="button">
                                                <span class="material-symbols-outlined text-[18px]"
                                                    id="reg-pwd-icon">visibility</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="font-label-eyebrow text-[11px] uppercase text-slate-authority/80 mb-1 block"
                                            for="reg-password-confirmation">
                                            Confirm Password
                                        </label>

                                        <div class="relative">
                                            <input
                                                class="w-full px-3.5 py-2.5 rounded-xl border border-primary-alpha-20 bg-card-white text-slate-authority placeholder:text-slate-authority/40 focus:outline-none focus:border-amber-action focus:ring-1 focus:ring-amber-action font-body-regular text-[13px]"
                                                id="reg-password-confirmation" name="password_confirmation"
                                                placeholder="Confirm password" required type="password">
                                            <button type="button" aria-label="Toggle confirm password visibility"
                                                class="absolute right-2.5 top-3.5 text-slate-authority/50 hover:text-slate-authority focus:outline-none"
                                                onclick="
                                                        const input = document.getElementById('reg-password-confirmation');
                                                        const icon = document.getElementById('reg-confirm-pwd-icon');

                                                        if (input.type === 'password') {
                                                            input.type = 'text';
                                                            icon.textContent = 'visibility_off';
                                                        } else {
                                                            input.type = 'password';
                                                            icon.textContent = 'visibility';
                                                        }
                                                        ">
                                                <span class="material-symbols-outlined text-[18px]"
                                                    id="reg-confirm-pwd-icon">
                                                    visibility
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Account Type Selector -->
                                <div>
                                    <span
                                        class="font-label-eyebrow text-[11px] uppercase text-slate-authority/80 mb-2 block">Account
                                        Type</span>
                                    <div class="grid grid-cols-2 gap-3">
                                        <!-- Customer Selection Card -->
                                        <label
                                            class="relative border-2 border-amber-action bg-canvas-ivory rounded-xl p-3 cursor-pointer transition-all flex flex-col justify-between select-none shadow-[0_4px_12px_rgba(245,166,35,0.15)] hover:shadow-md"
                                            id="card-type-customer">
                                            <input checked="" class="sr-only" name="role"
                                                onchange="document.getElementById('card-type-customer').className = 'relative border-2 border-amber-action bg-canvas-ivory rounded-xl p-3 cursor-pointer transition-all flex flex-col justify-between select-none shadow-[0_4px_12px_rgba(245,166,35,0.15)] hover:shadow-md'; document.getElementById('card-type-seller').className = 'relative border border-primary-alpha-10 bg-card-white hover:border-amber-action/50 rounded-xl p-3 cursor-pointer transition-all flex flex-col justify-between select-none shadow-sm'; document.getElementById('tag-customer').classList.remove('hidden'); document.getElementById('tag-seller').classList.add('hidden');"
                                                type="radio" value="user">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-6 h-6 rounded-md bg-amber-action text-slate-authority flex items-center justify-center font-bold text-xs shadow-sm">
                                                        <span class="material-symbols-outlined text-[15px]">person</span>
                                                    </div>
                                                    <span
                                                        class="font-title-card text-[14px] font-bold text-slate-authority">Customer</span>
                                                </div>
                                                <span
                                                    class="font-label-micro text-[9px] text-amber-action uppercase tracking-widest font-bold bg-amber-action/15 px-1.5 py-0.5 rounded"
                                                    id="tag-customer">ACTIVE</span>
                                            </div>
                                            <span class="font-label-micro text-[10px] text-primary-alpha-70">Shop, bid &amp;
                                                collect rare works</span>
                                        </label>
                                        <!-- Seller Selection Card -->
                                        <label
                                            class="relative border border-primary-alpha-10 bg-card-white hover:border-amber-action/50 rounded-xl p-3 cursor-pointer transition-all flex flex-col justify-between select-none shadow-sm hover:shadow-md"
                                            id="card-type-seller">
                                            <input class="sr-only" name="role"
                                                onchange="document.getElementById('card-type-seller').className = 'relative border-2 border-amber-action bg-canvas-ivory rounded-xl p-3 cursor-pointer transition-all flex flex-col justify-between select-none shadow-[0_4px_12px_rgba(245,166,35,0.15)] hover:shadow-md'; document.getElementById('card-type-customer').className = 'relative border border-primary-alpha-10 bg-card-white hover:border-amber-action/50 rounded-xl p-3 cursor-pointer transition-all flex flex-col justify-between select-none shadow-sm'; document.getElementById('tag-seller').classList.remove('hidden'); document.getElementById('tag-customer').classList.add('hidden');"
                                                type="radio" value="seller">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-6 h-6 rounded-md bg-slate-authority/15 text-slate-authority flex items-center justify-center font-bold text-xs shadow-sm">
                                                        <span
                                                            class="material-symbols-outlined text-[15px]">storefront</span>
                                                    </div>
                                                    <span
                                                        class="font-title-card text-[14px] font-bold text-slate-authority">Seller</span>
                                                </div>
                                                <span
                                                    class="hidden font-label-micro text-[9px] text-amber-action uppercase tracking-widest font-bold bg-amber-action/15 px-1.5 py-0.5 rounded"
                                                    id="tag-seller">ACTIVE</span>
                                            </div>
                                            <span class="font-label-micro text-[10px] text-primary-alpha-70">Sell, host
                                                auctions &amp; scale stalls</span>
                                        </label>
                                    </div>
                                </div>
                                <!-- Terms Checkbox -->
                                <div class="flex items-start gap-2.5 pt-1">
                                    <input
                                        class="mt-0.5 w-4 h-4 rounded border-primary-alpha-20 text-amber-action focus:ring-amber-action cursor-pointer accent-amber-action"
                                        id="reg-terms" required="" type="checkbox" name="terms">
                                    <label class="font-body-small text-[12px] text-primary-alpha-70 cursor-pointer"
                                        for="reg-terms">
                                        I agree to the
                                        <a class="text-slate-authority font-semibold underline hover:text-amber-action transition-colors"
                                            href="#">Terms of Service</a>
                                        and
                                        <a class="text-slate-authority font-semibold underline hover:text-amber-action transition-colors"
                                            href="#">Privacy Policy</a>.
                                    </label>
                                </div>
                                <!-- Submit Button -->
                                <button id="reg-submit-btn"
                                    class="w-full bg-amber-action text-slate-authority py-3 px-6 rounded-xl font-button-text text-[15px] font-bold hover:brightness-105 active:scale-[0.99] transition-all text-center flex items-center justify-center gap-2 shadow-[0_4px_14px_rgba(245,166,35,0.4)] mt-2"
                                    type="submit">
                                    <span id="reg-submit-label">Create Account</span>
                                    <span id="reg-submit-icon" class="material-symbols-outlined text-[19px]">arrow_forward</span>
                                </button>
                            </form>
                        </div><!-- /#reg-form-panel -->

                        <!-- ══════════════════════════════════════════════════
                             STEP 2 — OTP Verification Panel
                        ══════════════════════════════════════════════════ -->
                        <div id="otp-panel">
                            <!-- Back link -->
                            <button type="button" id="otp-back-btn"
                                class="flex items-center gap-1.5 font-label-eyebrow text-[11px] uppercase tracking-wide text-primary-alpha-70 hover:text-slate-authority mb-6 transition-colors group">
                                <span class="material-symbols-outlined text-[16px] group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
                                Back to form
                            </button>

                            <!-- Progress steps -->
                            <div class="flex items-center gap-2 mb-6">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-status-green flex items-center justify-center shadow-sm">
                                        <span class="material-symbols-outlined text-[13px] text-white">check</span>
                                    </div>
                                    <span class="font-label-micro text-[10px] uppercase tracking-wide text-status-green font-bold">Details</span>
                                </div>
                                <div class="flex-1 h-px bg-amber-action/40 relative">
                                    <div class="absolute inset-0 bg-amber-action rounded-full"></div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-amber-action flex items-center justify-center shadow-sm shadow-amber-action/40">
                                        <span class="material-symbols-outlined text-[13px] text-slate-authority font-bold">mail</span>
                                    </div>
                                    <span class="font-label-micro text-[10px] uppercase tracking-wide text-amber-action font-bold">Verify</span>
                                </div>
                                <div class="flex-1 h-px bg-primary-alpha-10"></div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-primary-alpha-10 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[13px] text-slate-authority/40">person</span>
                                    </div>
                                    <span class="font-label-micro text-[10px] uppercase tracking-wide text-slate-authority/40 font-bold">Account</span>
                                </div>
                            </div>

                            <!-- Icon + heading -->
                            <div class="flex flex-col items-center text-center mb-6">
                                <div class="relative mb-4">
                                    <!-- Glow ring -->
                                    <div class="absolute inset-0 rounded-2xl bg-amber-action/20 blur-md scale-110"></div>
                                    <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-action/20 to-amber-action/5 border border-amber-action/30 flex items-center justify-center shadow-sm">
                                        <span class="material-symbols-outlined text-[32px] text-amber-action">mark_email_read</span>
                                    </div>
                                </div>
                                <h3 class="font-headline-section text-[22px] text-slate-authority font-bold mb-1.5">Check your inbox</h3>
                                <p class="font-body-small text-[13px] text-primary-alpha-70 leading-relaxed">
                                    We sent a 6-digit verification code to
                                </p>
                                <div class="flex items-center gap-1.5 mt-1 px-3 py-1.5 rounded-full bg-surface-container-low border border-primary-alpha-10">
                                    <span class="material-symbols-outlined text-[14px] text-amber-action">alternate_email</span>
                                    <strong id="otp-email-display" class="font-label-eyebrow text-[11px] text-slate-authority tracking-wide"></strong>
                                </div>
                            </div>

                            <!-- OTP digit boxes -->
                            <div class="mb-5">
                                <p class="font-label-micro text-[10px] uppercase tracking-widest text-primary-alpha-70 text-center mb-3">Enter verification code</p>
                                <div class="flex items-center justify-center gap-2" id="otp-boxes">
                                    <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" data-idx="0">
                                    <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" data-idx="1">
                                    <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" data-idx="2">
                                    <div class="w-4 flex items-center justify-center">
                                        <div class="w-1 h-1 rounded-full bg-primary-alpha-20"></div>
                                    </div>
                                    <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" data-idx="3">
                                    <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" data-idx="4">
                                    <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" data-idx="5">
                                </div>
                            </div>

                            <!-- Expiry timer bar -->
                            <div class="mb-5">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="font-label-micro text-[10px] text-primary-alpha-70 uppercase tracking-wide">Code expires in</span>
                                    <span id="otp-timer-text" class="font-label-eyebrow text-[11px] text-amber-action font-bold">10:00</span>
                                </div>
                                <div class="h-1 bg-surface-container-high rounded-full overflow-hidden">
                                    <div id="otp-timer-bar" class="h-full bg-amber-action rounded-full transition-all duration-1000" style="width:100%"></div>
                                </div>
                            </div>

                            <!-- Verify button -->
                            <button id="otp-verify-btn"
                                class="w-full bg-amber-action text-slate-authority py-3.5 px-6 rounded-xl font-button-text text-[15px] font-bold hover:brightness-105 active:scale-[0.99] transition-all text-center flex items-center justify-center gap-2 shadow-[0_6px_20px_rgba(245,166,35,0.45)] mb-4"
                                type="button">
                                <span id="otp-verify-label">Verify &amp; Create Account</span>
                                <span id="otp-verify-icon" class="material-symbols-outlined text-[19px]">verified_user</span>
                            </button>

                            <!-- Resend -->
                            <div class="flex items-center justify-center gap-2">
                                <span class="font-body-small text-[12px] text-primary-alpha-70">Didn't receive it?</span>
                                <button id="resend-btn" type="button" disabled
                                    class="font-body-small text-[12px] font-semibold text-slate-authority/40 cursor-not-allowed transition-colors">
                                    Resend in <span id="resend-countdown">60</span>s
                                </button>
                            </div>
                        </div><!-- /#otp-panel -->

                        <!-- SSL Security Assurance -->
                        <div
                            class="mt-4 pt-3 border-t border-primary-alpha-10 flex items-center justify-center gap-2 text-primary-alpha-70">
                            <span class="material-symbols-outlined text-[15px] text-status-green">verified_user</span>
                            <span class="font-label-micro text-[10px] uppercase tracking-wider">256-BIT SSL ENCRYPTED
                                &amp; COMMUNITY VERIFIED</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-footer />

<script>
let countdownTimer = null;
let expiryTimer = null;
/* ════════════════════════════════════════════════════════════════
   Profile Image Preview
════════════════════════════════════════════════════════════════ */
document.getElementById('profile_image_input').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        const preview     = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-placeholder');
        // Set source first, then reveal — avoids broken-image flash
        preview.src              = e.target.result;
        preview.style.display    = 'block';
        placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
});

/* ════════════════════════════════════════════════════════════════
   Step 1 — Submit registration form → send OTP
════════════════════════════════════════════════════════════════ */
document.getElementById('reg-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    hideError();

    const btn   = document.getElementById('reg-submit-btn');
    const label = document.getElementById('reg-submit-label');
    const icon  = document.getElementById('reg-submit-icon');

    // Loading state
    btn.disabled = true;
    label.textContent = 'Sending OTP…';
    icon.textContent  = 'hourglass_empty';

    const formData = new FormData(this);

    try {
        const res  = await fetch('{{ route("register.send-otp") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });
        const data = await res.json();

        if (res.ok && data.success) {
            // Show OTP panel
            const email = document.getElementById('reg-email').value;
            document.getElementById('otp-email-display').textContent = email;
            showOtpPanel();
            startResendCountdown();
            startExpiryTimer(600); // 10 minutes
        } else {
            // Show exact validation errors from backend
            let errorMessage = 'Something went wrong. Please try again.';
            if (data.errors) {
                errorMessage = Object.values(data.errors).flat().join(' ');
            } else if (data.message) {
                errorMessage = data.message;
            }
            showError(errorMessage);
            btn.disabled  = false;
            label.textContent = 'Create Account';
            icon.textContent  = 'arrow_forward';
        }
    } catch (err) {
        showError('Request failed (' + err.message + '). Please try again.');
        btn.disabled  = false;
        label.textContent = 'Create Account';
        icon.textContent  = 'arrow_forward';
    }
});

/* ════════════════════════════════════════════════════════════════
   OTP digit box logic — auto-advance, backspace, paste
════════════════════════════════════════════════════════════════ */
const otpDigits = document.querySelectorAll('.otp-digit');

otpDigits.forEach((input, i) => {
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace') {
            this.value = '';
            this.classList.remove('filled');
            if (i > 0) otpDigits[i - 1].focus();
            e.preventDefault();
        }
    });

    input.addEventListener('input', function () {
        // Allow digits only
        this.value = this.value.replace(/\D/g, '').slice(-1);
        if (this.value) {
            this.classList.add('filled');
            if (i < otpDigits.length - 1) otpDigits[i + 1].focus();
        } else {
            this.classList.remove('filled');
        }
        // Auto-verify when all 6 filled
        if (getOtpValue().length === 6) {
            document.getElementById('otp-verify-btn').focus();
        }
    });

    // Allow paste of full OTP on any box
    input.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        otpDigits.forEach((box, j) => {
            box.value = pasted[j] || '';
            box.classList.toggle('filled', !!box.value);
        });
        if (pasted.length >= 6) document.getElementById('otp-verify-btn').focus();
        else if (pasted.length > 0) otpDigits[Math.min(pasted.length, 5)].focus();
    });
});

function getOtpValue() {
    return Array.from(otpDigits).map(b => b.value).join('');
}

/* ════════════════════════════════════════════════════════════════
   Step 2 — Verify OTP
════════════════════════════════════════════════════════════════ */
document.getElementById('otp-verify-btn').addEventListener('click', async function () {
    const otp = getOtpValue();
    if (otp.length !== 6) {
        showError('Please enter all 6 digits of your verification code.');
        return;
    }
    hideError();

    const btn   = this;
    const label = document.getElementById('otp-verify-label');
    const icon  = document.getElementById('otp-verify-icon');

    btn.disabled  = true;
    label.textContent = 'Verifying…';
    icon.textContent  = 'hourglass_empty';

    try {
        const res = await fetch('{{ route("register.verify-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ otp }),
        });
        const data = await res.json();

        if (res.ok && data.success) {
            label.textContent = 'Account Created!';
            icon.textContent  = 'check_circle';
            btn.classList.add('opacity-80');
            // Redirect
            setTimeout(() => { window.location.href = data.redirect; }, 600);
        } else {
            showError(data.message || 'Verification failed. Please try again.');
            btn.disabled  = false;
            label.textContent = 'Verify & Create Account';
            icon.textContent  = 'verified_user';
        }
    } catch (err) {
        showError('Network error. Please try again.');
        btn.disabled  = false;
        label.textContent = 'Verify & Create Account';
        icon.textContent  = 'verified';
    }
});

/* ════════════════════════════════════════════════════════════════
   Resend OTP
════════════════════════════════════════════════════════════════ */
function startResendCountdown(seconds = 60) {
    const btn       = document.getElementById('resend-btn');
    if (!btn) return;
    btn.disabled = true;
    btn.className = 'font-body-small text-[12px] font-semibold text-slate-authority/40 cursor-not-allowed transition-colors';
    btn.innerHTML = 'Resend in <span id="resend-countdown">' + seconds + '</span>s';

    let remaining = seconds;
    clearInterval(countdownTimer);
    countdownTimer = setInterval(() => {
        remaining--;
        const countdown = document.getElementById('resend-countdown');
        if (countdown) {
            countdown.textContent = remaining;
        }
        if (remaining <= 0) {
            clearInterval(countdownTimer);
            btn.disabled  = false;
            btn.className = 'font-body-small text-[12px] font-semibold text-amber-action hover:underline transition-colors';
            btn.innerHTML = 'Resend OTP';
        }
    }, 1000);
}

/* ════════════════════════════════════════════════════════════════
   Expiry Timer Bar (10 min countdown)
════════════════════════════════════════════════════════════════ */

function startExpiryTimer(totalSeconds = 600) {
    let remaining = totalSeconds;
    const bar  = document.getElementById('otp-timer-bar');
    const text = document.getElementById('otp-timer-text');

    clearInterval(expiryTimer);
    expiryTimer = setInterval(() => {
        remaining--;
        const pct = Math.max(0, (remaining / totalSeconds) * 100);
        bar.style.width = pct + '%';

        // Change bar colour when under 2 minutes
        if (remaining <= 120) {
            bar.style.background = '#ba1a1a';
            text.style.color     = '#ba1a1a';
        }

        const mins = String(Math.floor(remaining / 60)).padStart(2, '0');
        const secs = String(remaining % 60).padStart(2, '0');
        text.textContent = mins + ':' + secs;

        if (remaining <= 0) {
            clearInterval(expiryTimer);
            text.textContent = 'Expired';
        }
    }, 1000);
}

document.getElementById('resend-btn').addEventListener('click', async function () {
    if (this.disabled) return;
    hideError();
    this.textContent = 'Sending…';
    this.disabled    = true;

    try {
        const res  = await fetch('{{ route("register.resend-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });
        const data = await res.json();

        if (res.ok && data.success) {
            // Clear boxes
            otpDigits.forEach(b => { b.value = ''; b.classList.remove('filled'); });
            otpDigits[0].focus();
            startResendCountdown();
            // Reset timer bar
            const bar  = document.getElementById('otp-timer-bar');
            const text = document.getElementById('otp-timer-text');
            if (bar) bar.style.background = '';
            if (text) text.style.color    = '';
            startExpiryTimer(600);
        } else {
            showError(data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Could not resend OTP. Please try again.'));
            this.textContent = 'Resend OTP';
            this.disabled    = false;
        }
    } catch (err) {
        showError('Network error (' + err.message + '). Please try again.');
        this.textContent = 'Resend OTP';
        this.disabled    = false;
    }
});

/* ════════════════════════════════════════════════════════════════
   Back button — return to form
════════════════════════════════════════════════════════════════ */
document.getElementById('otp-back-btn').addEventListener('click', function () {
    hideOtpPanel();
    clearInterval(countdownTimer);

    // Reset submit button
    const btn   = document.getElementById('reg-submit-btn');
    btn.disabled = false;
    document.getElementById('reg-submit-label').textContent = 'Create Account';
    document.getElementById('reg-submit-icon').textContent  = 'arrow_forward';
    hideError();
});

/* ════════════════════════════════════════════════════════════════
   Panel transition helpers
════════════════════════════════════════════════════════════════ */
function showOtpPanel() {
    const formPanel = document.getElementById('reg-form-panel');
    const otpPanel  = document.getElementById('otp-panel');

    formPanel.classList.add('fading');
    setTimeout(() => {
        formPanel.style.display = 'none';
        otpPanel.style.display  = 'block';
        // Trigger reflow for transition
        otpPanel.offsetHeight;
        otpPanel.classList.add('visible');
        otpDigits[0].focus();
    }, 200);
}

function hideOtpPanel() {
    const formPanel = document.getElementById('reg-form-panel');
    const otpPanel  = document.getElementById('otp-panel');

    otpPanel.classList.remove('visible');
    setTimeout(() => {
        otpPanel.style.display  = 'none';
        formPanel.style.display = 'block';
        formPanel.classList.remove('fading');
    }, 250);
}

/* ════════════════════════════════════════════════════════════════
   Error banner helpers
════════════════════════════════════════════════════════════════ */
function showError(msg) {
    const banner = document.getElementById('form-error-banner');
    document.getElementById('form-error-text').textContent = msg;
    banner.classList.remove('hidden');
    banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function hideError() {
    document.getElementById('form-error-banner').classList.add('hidden');
}
</script>

</body>

</html>