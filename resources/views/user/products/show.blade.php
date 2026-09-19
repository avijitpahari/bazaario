<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Apple iPhone 15 Pro Max — Bazaario</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600;700&amp;family=Space+Grotesk:wght@500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "slate-authority": "#0F172A",
                        "canvas-ivory": "#FFFDF8",
                        "amber-action": "#F5A623",
                        "status-green": "#16A34A"
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    borderRadius: {
                        'card': '1rem'
                    },
                    boxShadow: {
                        'dock': '0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(255, 255, 255, 0.9) inset',
                        'card-elevated': '0 12px 32px -8px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04)'
                    }
                }
            }
        };
    </script>
    <style>
        body {
            background-color: #FFFDF8;
            background-image:
                radial-gradient(circle at 12% 10%, rgba(245, 166, 35, 0.06) 0%, transparent 45%),
                radial-gradient(circle at 88% 18%, rgba(15, 23, 42, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 50% 80%, rgba(245, 166, 35, 0.04) 0%, transparent 50%);
            background-attachment: fixed;
        }

        .glass-pill {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .ai-gradient-border {
            background: linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, #F5A623 0%, #8B5CF6 50%, #3B82F6 100%) border-box;
            border: 2px solid transparent;
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body
    class="bg-[#FFFDF8] text-slate-800 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-amber-100 selection:text-amber-900 pb-16 relative">
    <!-- 1. TOP NAVIGATION & FLOATING PILL DOCK -->
    <header class="sticky top-0 z-50 w-full px-4 pt-4 pb-2">
        <nav aria-label="Main Navigation"
            class="max-w-6xl mx-auto glass-pill px-5 py-2.5 rounded-full shadow-dock flex items-center justify-between transition-all">
            <!-- Left: Logo -->
            <a aria-label="Bazaario Home" class="flex items-center space-x-2 group shrink-0" href="#">
                <img alt="Bazaario Logo" class="h-7 md:h-8 w-auto object-contain"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAuwlU-di3HEHvvXlgPdnw9rh9FBoYa5PQfUnKZsKGM_iyCpkMsTo3-xxlIWiKy_Syou2w8YfruCCwjrG-S6XZ2BZ_pnsMUSaU2h9I5n6IFjuk-eW-ClLsCxkqdDVWT1ZXz2oibvcqO84h6AeP2HPPv7B0omnP1FABWo3gzR_YlMH0x3rmZs1tRJ00gyakhiI1RiVK67IJiPKfP6T5zVxWM8QYyp138Mqi_dqk1blDWnqc_em3mBRfNGIHM_j2D-6LsuQ" />
            </a>
            <!-- Center Links Pill -->
            <div
                class="hidden md:flex items-center gap-1 bg-[#0F172A]/5 p-1 rounded-full text-xs font-semibold text-slate-700">
                <a class="bg-[#0F172A] text-white px-4 py-1.5 rounded-full shadow-sm flex items-center gap-1.5"
                    href="#">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#F5A623]"></span>
                    Shop
                </a>
                <a class="px-3.5 py-1.5 rounded-full hover:text-[#0F172A] hover:bg-white/70 transition-colors"
                    href="#">Categories</a>
                <a class="px-3.5 py-1.5 rounded-full hover:text-[#0F172A] hover:bg-white/70 transition-colors flex items-center gap-1.5"
                    href="#">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Auctions
                </a>
                <a class="px-3.5 py-1.5 rounded-full hover:text-[#0F172A] hover:bg-white/70 transition-colors flex items-center gap-1 text-purple-700 font-medium"
                    href="#">
                    <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                    AI Compare
                </a>
            </div>
            <!-- Right Dock Controls -->
            <div class="flex items-center space-x-2.5">
                <!-- Search shortcut button -->
                <button aria-label="Search items"
                    class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 border border-slate-200/80 text-xs text-slate-500 hover:text-slate-900 transition shadow-xs">
                    <span class="material-symbols-outlined text-[15px]">search</span>
                    <span class="font-mono text-[11px]">⌘K</span>
                </button>
                <!-- Notification Bell with Dot -->
                <button aria-label="Notifications"
                    class="relative p-2 text-slate-600 hover:text-[#0F172A] hover:bg-white/70 rounded-full transition">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#F5A623] rounded-full ring-2 ring-white"></span>
                </button>
                <!-- Cart Pill with count (3) -->
                <a class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/25 text-xs font-semibold text-slate-800 transition"
                    href="#">
                    <span class="material-symbols-outlined text-[16px] text-amber-600">shopping_bag</span>
                    <span>Cart</span>
                    <span
                        class="w-4 h-4 rounded-full bg-[#F5A623] text-[#0F172A] text-[10px] font-bold flex items-center justify-center font-mono">3</span>
                </a>
                <!-- Avijit's Verified Buyer Avatar Pill -->
                <div class="flex items-center gap-2 pl-2 border-l border-slate-200/90">
                    <div
                        class="w-8 h-8 rounded-full bg-[#0F172A] text-white font-display font-bold text-xs flex items-center justify-center shadow-xs">
                        A
                    </div>
                    <div class="hidden lg:flex flex-col text-left">
                        <p class="text-xs font-bold text-[#0F172A] leading-tight">Avijit</p>
                        <p class="text-[10px] font-mono text-[#16A34A] font-medium flex items-center gap-0.5">
                            <span>Verified Buyer</span>
                            <span class="material-symbols-outlined text-[11px]">verified</span>
                        </p>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <!-- MAIN WRAPPER -->
    <main class="max-w-6xl mx-auto px-4 md:px-6 py-4 w-full space-y-8 flex-1">
        <!-- BREADCRUMBS & ESCROW BADGE -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
            <nav aria-label="Breadcrumb"
                class="flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-slate-500 flex-wrap">
                <a class="hover:text-slate-900 transition-colors" href="#">Home</a>
                <span>/</span>
                <a class="hover:text-slate-900 transition-colors" href="#">Electronics</a>
                <span>/</span>
                <a class="hover:text-slate-900 transition-colors" href="#">Smartphones</a>
                <span>/</span>
                <span class="text-[#0F172A] font-bold">iPhone 15 Pro Max</span>
            </nav>
            <div class="flex items-center gap-3 font-mono text-xs">
                <span
                    class="bg-emerald-500/10 text-[#16A34A] border border-emerald-500/25 rounded-full px-3 py-1 inline-flex items-center gap-1.5 font-bold tracking-wide text-[11px]">
                    <span class="w-2 h-2 rounded-full bg-[#16A34A] animate-pulse"></span>
                    ● BAZAARIO ESCROW SECURED
                </span>
                <span class="text-slate-400 font-mono text-[11px] hidden md:inline">ITEM ID: BZ-89104-APL</span>
            </div>
        </div>
        <!-- 2 & 3: MAIN PRODUCT OVERVIEW (LEFT GALLERY 50% / RIGHT BUY BOX 50%) -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- LEFT COLUMN: PRODUCT GALLERY (lg:col-span-6 = 50%) -->
            <div class="lg:col-span-6 flex flex-col gap-4">
                <!-- Main Showcase Card -->
                <div
                    class="bg-white rounded-card p-6 border border-slate-900/10 shadow-card-elevated relative overflow-hidden group">
                    <!-- Top Badges -->
                    <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
                        <span
                            class="font-mono text-[11px] bg-[#0F172A]/85 backdrop-blur-md text-white rounded-full px-3 py-1 inline-flex items-center gap-1.5 shadow-sm border border-white/20 font-semibold">
                            <span class="text-[#F5A623]">⚡</span> 100% Escrow Verified
                        </span>
                        <span
                            class="font-mono text-[11px] bg-emerald-100/90 backdrop-blur-md text-emerald-800 rounded-full px-3 py-1 inline-flex items-center gap-1 font-semibold border border-emerald-200">
                            <span class="text-[#16A34A]">⚡</span> Express Dispatch
                        </span>
                    </div>
                    <!-- Top-Right Controls: Expand + Wishlist Heart -->
                    <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
                        <button
                            class="w-9 h-9 rounded-full bg-white/90 backdrop-blur-md border border-slate-200 flex items-center justify-center text-slate-700 hover:text-[#0F172A] shadow-sm transition active:scale-95"
                            onclick="alert('Viewing high-resolution gallery full-screen.')" title="Expand Fullscreen">
                            <span class="material-symbols-outlined text-[18px]">open_in_full</span>
                        </button>
                        <button
                            class="w-9 h-9 rounded-full bg-white/90 backdrop-blur-md border border-slate-200 flex items-center justify-center text-slate-700 hover:text-red-500 shadow-sm transition active:scale-95"
                            id="gallery-wishlist-btn" onclick="toggleWishlist(this)" title="Add to Wishlist">
                            <span class="material-symbols-outlined text-[19px]">favorite_border</span>
                        </button>
                    </div>
                    <!-- Centered Product Image with Hover Zoom -->
                    <div
                        class="w-full min-h-[390px] md:min-h-[420px] flex items-center justify-center p-2 overflow-hidden cursor-crosshair">
                        <img alt="iPhone 15 Pro Max Natural Titanium"
                            class="w-full max-h-[410px] object-contain mx-auto transition-transform duration-500 ease-out group-hover:scale-110"
                            id="main-product-image"
                            src="https://lh3.googleusercontent.com/aida/AEtjO1U3XME0NHjyC0yTKDtPI-ikX0uXSsK46fZWbEaNRHHh3unKIDvt926bWFjY23XI1c5GTBPfAs4A1fHM-2MKmEAcn266rdb_5aiHgb4OI9OdXVLs1zjaghPZsIBGNIXAEJZw-Yl91LswyLLPZpKiukxNkkqjmxe5RpeUyLb_TpaY5LV6_vyqZcvQWn_5K_BDw8a2JEJUCAerh9mvrt4wZU0oatV1V4JCINW2MuphqazsPhOTaIZpo9ayAW4" />
                    </div>
                    <!-- Interactive Nav Arrows (< >) -->
                    <button
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-[#0F172A] flex items-center justify-center border border-slate-200 shadow-sm transition opacity-0 group-hover:opacity-100"
                        onclick="cycleGallery(-1)" title="Previous Image">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <button
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-[#0F172A] flex items-center justify-center border border-slate-200 shadow-sm transition opacity-0 group-hover:opacity-100"
                        onclick="cycleGallery(1)" title="Next Image">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                    <!-- 360° Studio View Pill Button -->
                    <div class="absolute bottom-4 right-4 z-10">
                        <button
                            class="bg-[#0F172A]/85 hover:bg-[#0F172A] backdrop-blur-md text-white px-3.5 py-1.5 rounded-full flex items-center gap-1.5 font-mono text-xs shadow-sm border border-white/10 transition active:scale-95"
                            onclick="alert('Launching Interactive 360° Titanium Model.')">
                            <span class="material-symbols-outlined text-[16px] text-[#F5A623]">360</span>
                            <span>360° Studio View</span>
                        </button>
                    </div>
                </div>
                <!-- Thumbnails Rail (4 thumbnails with active amber ring) -->
                <div class="grid grid-cols-4 gap-3">
                    <!-- Thumb 1: Front screen view -->
                    <button
                        class="thumb-btn border border-slate-200 p-1.5 rounded-card bg-white shadow-xs transition hover:scale-[1.02] flex items-center justify-center aspect-square"
                        onclick="selectThumb(0, 'https://lh3.googleusercontent.com/aida/AEtjO1VBfMU_UJLdgyOwT14nG-AylyaKuOAGG2ZZqje9PxPmYECnNoJDh68Krv5X9qxiHEE91OaXp_EDCt15rQRy_d6wSZHmWvc-2WwjfxCyRKyXBqCJPKu-L3Tx9mxC9REuVmYucvAwPFfvDxod-gvguMPMEZv9fzJacAwGBT0Vtodc45GjrxPp1X-1LwYIRLUBL90UBCw54dIQBvsEzT7RHrJbneZrTHTldD4c-cC0eUZdQ_fBpU6yrnNlKvI', this)">
                        <img alt="Front OLED display with thin bezels" class="w-full h-16 object-contain"
                            src="https://lh3.googleusercontent.com/aida/AEtjO1VBfMU_UJLdgyOwT14nG-AylyaKuOAGG2ZZqje9PxPmYECnNoJDh68Krv5X9qxiHEE91OaXp_EDCt15rQRy_d6wSZHmWvc-2WwjfxCyRKyXBqCJPKu-L3Tx9mxC9REuVmYucvAwPFfvDxod-gvguMPMEZv9fzJacAwGBT0Vtodc45GjrxPp1X-1LwYIRLUBL90UBCw54dIQBvsEzT7RHrJbneZrTHTldD4c-cC0eUZdQ_fBpU6yrnNlKvI" />
                    </button>
                    <!-- Thumb 2: Back titanium angle (Initial Active) -->
                    <button
                        class="thumb-btn border-2 border-[#F5A623] p-1.5 rounded-card bg-white shadow-xs transition hover:scale-[1.02] flex items-center justify-center aspect-square"
                        onclick="selectThumb(1, 'https://lh3.googleusercontent.com/aida/AEtjO1U3XME0NHjyC0yTKDtPI-ikX0uXSsK46fZWbEaNRHHh3unKIDvt926bWFjY23XI1c5GTBPfAs4A1fHM-2MKmEAcn266rdb_5aiHgb4OI9OdXVLs1zjaghPZsIBGNIXAEJZw-Yl91LswyLLPZpKiukxNkkqjmxe5RpeUyLb_TpaY5LV6_vyqZcvQWn_5K_BDw8a2JEJUCAerh9mvrt4wZU0oatV1V4JCINW2MuphqazsPhOTaIZpo9ayAW4', this)">
                        <img alt="Natural Titanium rear angle" class="w-full h-16 object-contain"
                            src="https://lh3.googleusercontent.com/aida/AEtjO1U3XME0NHjyC0yTKDtPI-ikX0uXSsK46fZWbEaNRHHh3unKIDvt926bWFjY23XI1c5GTBPfAs4A1fHM-2MKmEAcn266rdb_5aiHgb4OI9OdXVLs1zjaghPZsIBGNIXAEJZw-Yl91LswyLLPZpKiukxNkkqjmxe5RpeUyLb_TpaY5LV6_vyqZcvQWn_5K_BDw8a2JEJUCAerh9mvrt4wZU0oatV1V4JCINW2MuphqazsPhOTaIZpo9ayAW4" />
                    </button>
                    <!-- Thumb 3: Reflective finish -->
                    <button
                        class="thumb-btn border border-slate-200 p-1.5 rounded-card bg-white shadow-xs transition hover:scale-[1.02] flex items-center justify-center aspect-square"
                        onclick="selectThumb(2, 'https://lh3.googleusercontent.com/aida/AEtjO1Wzt2qenVqQnp5b3WNrnmK9j8KZawCAt2uvf_-imd9ZPFeKz8NyGF_E0gE0047zxBPF__D5Jc_-tWxglSpDeAWXGT0QYKYMa8azKDyT3LpO-K2UFFteyMo7oKiwO6-NoFLmgTRYub_z4VvYkls48KdYB308dMR2mAyoCkLCz1CSZ-V2gSMW_ElybIVeVyM691WVLcfvRXORt8QLylnSBTVibdSfXVHv1EZmNzOCwcKHq-qcSMCPAhfSZjQ', this)">
                        <img alt="Reflective finish angle" class="w-full h-16 object-contain"
                            src="https://lh3.googleusercontent.com/aida/AEtjO1Wzt2qenVqQnp5b3WNrnmK9j8KZawCAt2uvf_-imd9ZPFeKz8NyGF_E0gE0047zxBPF__D5Jc_-tWxglSpDeAWXGT0QYKYMa8azKDyT3LpO-K2UFFteyMo7oKiwO6-NoFLmgTRYub_z4VvYkls48KdYB308dMR2mAyoCkLCz1CSZ-V2gSMW_ElybIVeVyM691WVLcfvRXORt8QLylnSBTVibdSfXVHv1EZmNzOCwcKHq-qcSMCPAhfSZjQ" />
                    </button>
                    <!-- Thumb 4: Lifestyle companion -->
                    <button
                        class="thumb-btn border border-slate-200 p-1.5 rounded-card bg-white shadow-xs transition hover:scale-[1.02] flex items-center justify-center aspect-square"
                        onclick="selectThumb(3, 'https://lh3.googleusercontent.com/aida/AEtjO1VdXEmh6VIHBeEKpbhBsJla5YRF2HZfN_yobBtuYB29VcpXPUhJ3bF_9h6T1v-aBFf9RjmXiP7SMtLWz8tL24YlWmvXBrRiy3Of39ExP85BvzYHoDoL_GGGbxXSqWdi9lnp-Q3RWE50x-h3nmfIdDsYuRa9jK19Gf1OXdvLjPu6tY-kxH7lRQL1qFgdx-WbwlmpFOp8_eww2HlqsF-1x2YrEWXQgabb7057tBdbEK6THq4cbJIPvONL01M', this)">
                        <img alt="Apple ecosystem companion" class="w-full h-16 object-contain"
                            src="https://lh3.googleusercontent.com/aida/AEtjO1VdXEmh6VIHBeEKpbhBsJla5YRF2HZfN_yobBtuYB29VcpXPUhJ3bF_9h6T1v-aBFf9RjmXiP7SMtLWz8tL24YlWmvXBrRiy3Of39ExP85BvzYHoDoL_GGGbxXSqWdi9lnp-Q3RWE50x-h3nmfIdDsYuRa9jK19Gf1OXdvLjPu6tY-kxH7lRQL1qFgdx-WbwlmpFOp8_eww2HlqsF-1x2YrEWXQgabb7057tBdbEK6THq4cbJIPvONL01M" />
                    </button>
                </div>
            </div>
            <!-- RIGHT COLUMN: PRODUCT INFORMATION & BUY BOX (lg:col-span-6 = 50%) -->
            <div class="lg:col-span-6 flex flex-col gap-5">
                <div class="bg-white rounded-card p-6 md:p-7 border border-slate-900/10 shadow-card-elevated space-y-5">
                    <!-- Brand & Stock Urgency -->
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <span class="font-mono text-xs uppercase tracking-wider text-slate-500 font-semibold">
                            Apple
                        </span>
                        <div
                            class="flex items-center gap-1.5 font-mono text-xs font-semibold text-[#16A34A] bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-[#16A34A] animate-pulse"></span>
                            <span>✓ In Stock</span>
                            <span class="text-emerald-700 font-medium">• Only 8 units left in stock!</span>
                        </div>
                    </div>
                    <!-- Product Title & Review Meta -->
                    <div>
                        <h1
                            class="font-display font-bold text-3xl md:text-4xl text-[#0F172A] tracking-tight leading-tight">
                            iPhone 15 Pro Max
                        </h1>
                        <div class="flex items-center gap-2.5 pt-2 flex-wrap">
                            <span
                                class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-900 px-2.5 py-0.5 rounded-full font-mono text-xs font-bold">
                                <span class="material-symbols-outlined text-[15px] text-[#F5A623]"
                                    style="font-variation-settings: 'FILL' 1;">star</span>
                                ★ 4.8
                            </span>
                            <span class="text-slate-300">•</span>
                            <a class="text-xs font-mono text-slate-600 hover:text-[#0F172A] underline underline-offset-2"
                                href="#customer-reviews">
                                248 reviews
                            </a>
                            <span class="text-slate-300">•</span>
                            <span class="text-[11px] font-mono text-slate-500" id="selected-spec-label">256 GB • Natural
                                Titanium</span>
                        </div>
                    </div>
                    <!-- Pricing Section -->
                    <div class="pt-2 border-t border-slate-100 flex items-baseline gap-3 flex-wrap">
                        <span class="font-display font-bold text-3xl md:text-4xl text-[#0F172A] tracking-tight"
                            id="active-price">
                            ₹1,19,999
                        </span>
                        <span class="text-lg text-slate-400 line-through font-medium" id="mrp-price">
                            MRP ₹1,34,999
                        </span>
                        <span class="rounded-card px-2.5 py-1 bg-amber-100 text-amber-800 font-mono text-xs font-bold">
                            11% OFF
                        </span>
                    </div>
                    <!-- Color & Storage Selectors -->
                    <div class="space-y-4 pt-1 border-t border-slate-100">
                        <!-- Color Selection -->
                        <div>
                            <div class="flex justify-between items-center text-xs mb-2">
                                <span class="font-mono text-slate-500 uppercase tracking-wider font-semibold">Finish:
                                    <span class="text-slate-900" id="color-name">Natural Titanium</span></span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button
                                    class="color-btn w-8 h-8 rounded-full bg-[#B8B3A8] ring-2 ring-offset-2 ring-[#0F172A] transition shadow-xs"
                                    onclick="setColor('Natural Titanium', this)" title="Natural Titanium"></button>
                                <button
                                    class="color-btn w-8 h-8 rounded-full bg-[#3B4454] ring-1 ring-slate-300 hover:ring-2 hover:ring-slate-400 transition shadow-xs"
                                    onclick="setColor('Blue Titanium', this)" title="Blue Titanium"></button>
                                <button
                                    class="color-btn w-8 h-8 rounded-full bg-[#E5E5EA] ring-1 ring-slate-300 hover:ring-2 hover:ring-slate-400 transition shadow-xs"
                                    onclick="setColor('White Titanium', this)" title="White Titanium"></button>
                                <button
                                    class="color-btn w-8 h-8 rounded-full bg-[#1C1C1E] ring-1 ring-slate-300 hover:ring-2 hover:ring-slate-400 transition shadow-xs"
                                    onclick="setColor('Space Black', this)" title="Space Black"></button>
                            </div>
                        </div>
                        <!-- Storage Selection -->
                        <div>
                            <div class="flex justify-between items-center text-xs mb-2">
                                <span class="font-mono text-slate-500 uppercase tracking-wider font-semibold">Storage:
                                    <span class="text-slate-900" id="storage-name">256 GB</span></span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <button
                                    class="storage-btn p-2.5 rounded-xl border-2 border-[#0F172A] bg-slate-900/5 font-semibold text-[#0F172A] flex flex-col items-center justify-center transition"
                                    onclick="setStorage('256 GB', '₹1,19,999', 'MRP ₹1,34,999', this)">
                                    <span>256 GB</span>
                                    <span
                                        class="text-[10px] text-slate-500 font-mono font-normal mt-0.5">Included</span>
                                </button>
                                <button
                                    class="storage-btn p-2.5 rounded-xl border border-slate-200 hover:border-slate-400 bg-white font-semibold text-slate-700 flex flex-col items-center justify-center transition"
                                    onclick="setStorage('512 GB', '₹1,39,999', 'MRP ₹1,54,999', this)">
                                    <span>512 GB</span>
                                    <span
                                        class="text-[10px] text-slate-500 font-mono font-normal mt-0.5">+₹20,000</span>
                                </button>
                                <button
                                    class="storage-btn p-2.5 rounded-xl border border-slate-200 hover:border-slate-400 bg-white font-semibold text-slate-700 flex flex-col items-center justify-center transition"
                                    onclick="setStorage('1 TB', '₹1,59,999', 'MRP ₹1,74,999', this)">
                                    <span>1 TB</span>
                                    <span
                                        class="text-[10px] text-slate-500 font-mono font-normal mt-0.5">+₹40,000</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Quantity Stepper & Main CTA Buttons -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-3">
                            <!-- Tactile Stepper: [ - ] 1 [ + ] -->
                            <div
                                class="bg-slate-100 rounded-card p-1 flex items-center border border-slate-200/90 shadow-inner">
                                <button
                                    class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-700 hover:bg-white hover:text-slate-900 transition text-base font-bold active:scale-95"
                                    onclick="adjustQty(-1)">
                                    −
                                </button>
                                <span class="w-10 text-center font-mono text-sm font-bold text-[#0F172A]"
                                    id="stepper-count">
                                    1
                                </span>
                                <button
                                    class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-700 hover:bg-white hover:text-slate-900 transition text-base font-bold active:scale-95"
                                    onclick="adjustQty(1)">
                                    +
                                </button>
                            </div>
                            <!-- Primary Add to Cart Button -->
                            <button
                                class="flex-1 py-3.5 px-6 bg-[#F5A623] hover:brightness-105 text-[#0F172A] rounded-card font-semibold text-sm shadow-sm flex items-center justify-center gap-2 transition active:scale-[0.98]"
                                id="add-to-cart-btn" onclick="triggerCartAdd(this)">
                                <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                <span>🛒 Add to Cart</span>
                            </button>
                        </div>
                        <!-- Instant Escrow Buy Now Button + Wishlist Heart -->
                        <div class="flex items-center gap-3">
                            <button
                                class="flex-1 py-3.5 px-6 bg-[#0F172A] hover:bg-slate-800 text-white rounded-card font-semibold text-sm shadow-sm flex items-center justify-center gap-2 transition active:scale-[0.98]"
                                onclick="alert('Proceeding to instant secure smart escrow checkout for ' + document.getElementById('active-price').innerText + '.')">
                                <span class="text-[#F5A623]">⚡</span>
                                <span>⚡ Instant Escrow Buy Now</span>
                            </button>
                            <button
                                class="w-12 h-12 rounded-card border border-slate-200 bg-slate-50 hover:bg-white text-slate-700 hover:text-red-500 flex items-center justify-center transition active:scale-95 shadow-xs"
                                onclick="toggleWishlist(this)" title="Save to Wishlist">
                                <span class="material-symbols-outlined text-[22px]">favorite_border</span>
                            </button>
                        </div>
                    </div>
                    <!-- Product Benefits Cards (Grid of 4 Clean Cards) -->
                    <div class="grid grid-cols-2 gap-2.5 pt-2">
                        <div
                            class="p-3 rounded-card bg-slate-50/80 border border-slate-200/70 flex items-center gap-2.5">
                            <span class="text-lg">🚚</span>
                            <div>
                                <p class="font-display font-bold text-xs text-[#0F172A]">Free Delivery</p>
                                <p class="font-mono text-[10px] text-slate-500">Express in 24h</p>
                            </div>
                        </div>
                        <div
                            class="p-3 rounded-card bg-slate-50/80 border border-slate-200/70 flex items-center gap-2.5">
                            <span class="text-lg">↩</span>
                            <div>
                                <p class="font-display font-bold text-xs text-[#0F172A]">7 Day Returns</p>
                                <p class="font-mono text-[10px] text-slate-500">Hassle-free guarantee</p>
                            </div>
                        </div>
                        <div
                            class="p-3 rounded-card bg-slate-50/80 border border-slate-200/70 flex items-center gap-2.5">
                            <span class="text-lg">🔒</span>
                            <div>
                                <p class="font-display font-bold text-xs text-[#0F172A]">Secure Payment</p>
                                <p class="font-mono text-[10px] text-slate-500">100% Escrow Vault</p>
                            </div>
                        </div>
                        <div
                            class="p-3 rounded-card bg-slate-50/80 border border-slate-200/70 flex items-center gap-2.5">
                            <span class="text-lg">✓</span>
                            <div>
                                <p class="font-display font-bold text-xs text-[#0F172A]">Genuine Product</p>
                                <p class="font-mono text-[10px] text-slate-500">Brand Authenticity Verified</p>
                            </div>
                        </div>
                    </div>
                    <!-- Multi-vendor Seller Information Card -->
                    <div class="border-t border-slate-100 pt-4 space-y-2">
                        <span
                            class="font-mono text-[11px] text-slate-400 uppercase tracking-wider block font-semibold">Sold
                            by</span>
                        <div
                            class="p-3.5 rounded-card bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-display font-bold text-sm text-[#0F172A]">TechWorld Store</span>
                                    <span
                                        class="bg-emerald-100 text-emerald-800 font-mono text-[10px] font-bold px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                                        Trusted Seller ✓
                                    </span>
                                    <span class="text-xs font-mono font-semibold text-amber-700">★ 4.8 (98% positive
                                        rating)</span>
                                </div>
                                <p class="font-mono text-[11px] text-slate-500">
                                    4,912 Products • Mumbai, India • Seller since 2021 • 99.8% Trust Score
                                </p>
                            </div>
                            <button
                                class="self-start sm:self-center font-mono text-xs font-bold text-[#0F172A] hover:text-amber-600 bg-white border border-slate-200 px-3 py-1.5 rounded-lg transition shrink-0">
                                [ Visit Store → ]
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- 4. BAZAARIO AI PRODUCT INSIGHT (GEMINI AI SECTION) -->
        <section class="w-full">
            <div class="ai-gradient-border bg-white rounded-card p-6 md:p-7 shadow-card-elevated space-y-5">
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#F5A623] text-[22px]">auto_awesome</span>
                            <h3 class="font-display font-bold text-xl text-[#0F172A]">✨ Bazaario AI Insight</h3>
                            <span
                                class="bg-purple-100 text-purple-800 font-mono text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                                Powered by Gemini AI Engine v4.2
                            </span>
                        </div>
                        <p class="font-display font-semibold text-sm text-slate-800">Is this product right for you?</p>
                        <p class="font-mono text-xs text-slate-500">Based on your browsing and purchase preferences:</p>
                    </div>
                    <a class="self-start md:self-center font-display font-semibold text-xs text-[#0F172A] bg-amber-100 hover:bg-amber-200 text-amber-950 px-4 py-2 rounded-xl transition flex items-center gap-1.5 shadow-xs"
                        href="#">
                        [ Compare with Similar Products → ]
                    </a>
                </div>
                <!-- AI Bullet checks & Metric Bars -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <!-- Checks (7 cols) -->
                    <div class="lg:col-span-7 space-y-2.5 text-xs text-slate-700 leading-relaxed">
                        <div class="flex items-start gap-2">
                            <span class="text-[#16A34A] font-bold text-sm">✓</span>
                            <p><strong class="text-[#0F172A]">Great performance:</strong> A17 Pro benchmarks surpass 98%
                                of flagship phones on market compute loads.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-[#16A34A] font-bold text-sm">✓</span>
                            <p><strong class="text-[#0F172A]">Good camera quality:</strong> 5x optical periscope zoom
                                &amp; 48MP ProRAW for professional studio captures.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-[#16A34A] font-bold text-sm">✓</span>
                            <p><strong class="text-[#0F172A]">Suitable for everyday use:</strong> All-day endurance
                                paired with aerospace titanium chassis.</p>
                        </div>
                    </div>
                    <!-- Progress Bars (5 cols) -->
                    <div
                        class="lg:col-span-5 bg-slate-50/90 rounded-xl p-4 border border-slate-200/80 space-y-3 font-mono text-xs">
                        <div class="space-y-1.5">
                            <div class="flex justify-between font-medium">
                                <span class="text-slate-700">Value for Money</span>
                                <span class="text-[#0F172A] font-bold">8.7 / 10</span>
                            </div>
                            <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-[#F5A623] rounded-full" style="width: 87%;"></div>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex justify-between font-medium">
                                <span class="text-slate-700">Performance</span>
                                <span class="text-[#0F172A] font-bold">9.2 / 10</span>
                            </div>
                            <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-600 rounded-full" style="width: 92%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- 5. ABOUT THIS PRODUCT & DESCRIPTION -->
        <section class="w-full">
            <div class="glass-card rounded-card p-6 md:p-8 space-y-6 shadow-sm">
                <div>
                    <h2 class="font-display font-bold text-2xl text-[#0F172A]">About this product</h2>
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed mt-2">
                        The iPhone 15 Pro Max delivers exceptional performance with a powerful processor, advanced
                        camera system, and long-lasting battery. Forged in aerospace-grade titanium, it brings unmatched
                        computational photography and next-generation gaming to the Bazaario ecosystem.
                    </p>
                </div>
                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <h3 class="font-display font-bold text-sm text-[#0F172A] uppercase tracking-wide">Key Features</h3>
                    <ul class="space-y-2 text-xs md:text-sm text-slate-700 font-sans">
                        <li class="flex items-center gap-2">
                            <span class="text-[#16A34A] font-bold">✓</span>
                            <span>6.7-inch Super Retina XDR display with ProMotion 120Hz</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-[#16A34A] font-bold">✓</span>
                            <span>256GB NVMe high-speed storage</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-[#16A34A] font-bold">✓</span>
                            <span>Advanced 48MP triple-lens camera system with 5x optical zoom</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-[#16A34A] font-bold">✓</span>
                            <span>Long battery life (up to 29 hours video playback)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-[#16A34A] font-bold">✓</span>
                            <span>Premium titanium design with customizable Action button</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- 6. PRODUCT SPECIFICATIONS (CLEAN 2-COLUMN TABLE) -->
        <section class="w-full space-y-3">
            <h2 class="font-display font-bold text-2xl text-[#0F172A]">Product Specifications</h2>
            <div class="bg-white rounded-card overflow-hidden border border-slate-900/10 shadow-sm">
                <table class="w-full text-left text-xs md:text-sm border-collapse">
                    <tbody class="divide-y divide-slate-200/80 font-sans">
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Brand</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">Apple</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Model</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">iPhone 15 Pro Max</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Storage</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">256 GB</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">RAM</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">8 GB Unified</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Display</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">6.7 inch Super Retina XDR OLED</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Processor
                            </td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">Apple A17 Pro (3nm)</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Main Camera
                            </td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">48 MP + 12 MP + 12 MP (5x
                                Telephoto)</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Operating
                                System</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">iOS 17 (Upgradable)</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Weight</td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">221 grams</td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="w-1/3 py-3 px-4 font-mono font-medium text-slate-500 bg-slate-50/50">Warranty
                            </td>
                            <td class="w-2/3 py-3 px-4 font-semibold text-[#0F172A]">1 Year Official Manufacturer
                                Warranty</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <!-- 7. CUSTOMER REVIEWS SECTION -->
        <section class="space-y-6 pt-2" id="customer-reviews">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <span class="font-mono text-xs font-bold text-amber-700 uppercase tracking-wider block">VERIFIED
                        RATINGS</span>
                    <h2 class="font-display font-bold text-2xl md:text-3xl text-[#0F172A]">⭐ Customer Reviews</h2>
                </div>
                <!-- Filter pills -->
                <div class="flex items-center gap-1.5 flex-wrap text-xs font-mono">
                    <button class="bg-[#0F172A] text-white px-3 py-1.5 rounded-full font-bold">All (248)</button>
                    <button
                        class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-3 py-1.5 rounded-full">With
                        Photos (64)</button>
                    <button
                        class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-3 py-1.5 rounded-full">5
                        Stars (190)</button>
                    <button
                        class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-1.5 rounded-full font-semibold">Escrow
                        Verified (248)</button>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Rating summary card (5 cols) -->
                <div class="lg:col-span-5 glass-card rounded-card p-6 shadow-sm space-y-5">
                    <div class="flex items-baseline gap-3">
                        <span class="font-display font-bold text-5xl text-[#0F172A]">4.8</span>
                        <div class="space-y-1">
                            <span class="text-sm font-mono text-slate-500">/ 5.0</span>
                            <div class="flex text-[#F5A623] text-lg">
                                ★★★★★
                            </div>
                        </div>
                    </div>
                    <p class="font-mono text-xs text-slate-500">Based on 248 verified reviews</p>
                    <!-- Horizontal distribution bars matching requested specs -->
                    <div class="space-y-2 font-mono text-xs pt-2 border-t border-slate-100">
                        <!-- 5 Star -->
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 text-right font-medium">5 ★</span>
                            <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bg-[#F5A623] h-full rounded-full" style="width: 76%;"></div>
                            </div>
                            <span class="w-16 text-right text-slate-600 font-semibold">190 (76%)</span>
                        </div>
                        <!-- 4 Star -->
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 text-right font-medium">4 ★</span>
                            <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bg-[#F5A623] h-full rounded-full" style="width: 15%;"></div>
                            </div>
                            <span class="w-16 text-right text-slate-600 font-semibold">38 (15%)</span>
                        </div>
                        <!-- 3 Star -->
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 text-right font-medium">3 ★</span>
                            <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bg-[#F5A623] h-full rounded-full" style="width: 5%;"></div>
                            </div>
                            <span class="w-16 text-right text-slate-600 font-semibold">12 (5%)</span>
                        </div>
                        <!-- 2 Star -->
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 text-right font-medium">2 ★</span>
                            <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bg-[#F5A623] h-full rounded-full" style="width: 2%;"></div>
                            </div>
                            <span class="w-16 text-right text-slate-600 font-semibold">5 (2%)</span>
                        </div>
                        <!-- 1 Star -->
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 text-right font-medium">1 ★</span>
                            <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bg-[#F5A623] h-full rounded-full" style="width: 1%;"></div>
                            </div>
                            <span class="w-16 text-right text-slate-600 font-semibold">3 (1%)</span>
                        </div>
                    </div>
                </div>
                <!-- Review Cards (7 cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- Review 1: Rahul V. -->
                    <div class="glass-card rounded-card p-5 shadow-sm space-y-2.5">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-8 h-8 rounded-full bg-[#0F172A] text-white font-display font-bold text-xs flex items-center justify-center">
                                    RV
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-display font-bold text-sm text-[#0F172A]">Rahul V.</span>
                                        <span
                                            class="bg-emerald-100 text-emerald-800 font-mono text-[10px] px-2 py-0.5 rounded-full font-semibold">
                                            Verified Purchase ✓
                                        </span>
                                    </div>
                                    <span class="font-mono text-[10px] text-slate-400">2 days ago</span>
                                </div>
                            </div>
                            <div class="text-[#F5A623] text-sm">★★★★★</div>
                        </div>
                        <p class="text-xs md:text-sm text-slate-700 leading-relaxed">
                            Excellent product. Delivery was very fast. The natural titanium finish feels unbelievable in
                            hand and escrow protection gave me total peace of mind.
                        </p>
                    </div>
                    <!-- Review 2: Priya Sharma -->
                    <div class="glass-card rounded-card p-5 shadow-sm space-y-2.5">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-8 h-8 rounded-full bg-slate-700 text-white font-display font-bold text-xs flex items-center justify-center">
                                    PS
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-display font-bold text-sm text-[#0F172A]">Priya Sharma</span>
                                        <span
                                            class="bg-emerald-100 text-emerald-800 font-mono text-[10px] px-2 py-0.5 rounded-full font-semibold">
                                            Verified Purchase ✓
                                        </span>
                                    </div>
                                    <span class="font-mono text-[10px] text-slate-400">1 week ago</span>
                                </div>
                            </div>
                            <div class="text-[#F5A623] text-sm">★★★★★</div>
                        </div>
                        <p class="text-xs md:text-sm text-slate-700 leading-relaxed">
                            Battery easily lasts two full days. 120Hz screen is gorgeous and camera in low light is top
                            tier.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- 8. YOU MAY ALSO LIKE -->
        <section class="space-y-5 pt-4 pb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display font-bold text-2xl text-[#0F172A]">✨ You may also like</h2>
                </div>
                <a class="text-xs font-mono font-bold text-[#0F172A] hover:text-amber-600 transition" href="#">
                    Browse catalog →
                </a>
            </div>
            <!-- 4 product cards grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Sony WH-1000XM5 -->
                <article
                    class="bg-white rounded-card p-4 border border-slate-900/10 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div
                            class="w-full aspect-square bg-slate-50 rounded-lg overflow-hidden flex items-center justify-center p-3">
                            <img alt="Sony WH-1000XM5"
                                class="w-full h-full object-contain group-hover:scale-105 transition duration-300"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1VHIOUaIHnEHXj6aAZR4Ssh03jnYrUls7pm_emn5hHL2orC7ViQ-wxHcYe-_OtQ4l7DFZJtmf9sVuU9gusW4XgqcW0bn92muRJsSPTObn0awSbspcMJr7VIFOyHd974SJiQ_R0tqH09fkmekCH06QhNvFA8G_Azm4NodqGuMIHPfhtk9Z3DeVZT5bgVvTgUIHqlnWVHYBzuChGGQ3NqtT_B5CoyX1mWpCicCbGqDmUt56gau_yghNUuBQ" />
                        </div>
                        <div>
                            <div class="flex items-center gap-1 text-[#F5A623] text-xs font-mono">
                                <span>★ 4.9</span>
                            </div>
                            <h3 class="font-display font-bold text-sm text-[#0F172A] truncate">Sony WH-1000XM5</h3>
                            <p class="font-mono font-bold text-base text-[#0F172A] mt-1">₹25,000</p>
                        </div>
                    </div>
                    <button
                        class="mt-3 w-full bg-slate-100 hover:bg-[#0F172A] hover:text-white text-[#0F172A] font-semibold text-xs py-2 rounded-lg transition"
                        onclick="triggerMiniAdd(this)">
                        🛒 Add to Cart
                    </button>
                </article>
                <!-- Card 2: Retro Cream Keyboard -->
                <article
                    class="bg-white rounded-card p-4 border border-slate-900/10 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div
                            class="w-full aspect-square bg-slate-50 rounded-lg overflow-hidden flex items-center justify-center p-3">
                            <img alt="Retro Cream Keyboard"
                                class="w-full h-full object-contain group-hover:scale-105 transition duration-300"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1Vuz-e4ii6gOV2jAQgMpB1N9MIzgysUeNPdcwjYPzkYHga3UfjHTFkS8fV47RmoFUipezX_vsHo5VUXgpGvYfuiLYWCL6KwVgK2YofEOcjvrD7zgENNTjPH6_9ab9Wbd8R3aWmYg1Asip4dJ1bqyLdJS_B9Q_v2_CWdT3Z7b7lW8j6T4qpgzDUUdMYSEG2cj1zvGiQWjGEYf78vpvUhqvVPVQD5Cd-OnQNGRa_cTNAjtQjwoMCHPba5ag" />
                        </div>
                        <div>
                            <div class="flex items-center gap-1 text-[#F5A623] text-xs font-mono">
                                <span>★ 4.7</span>
                            </div>
                            <h3 class="font-display font-bold text-sm text-[#0F172A] truncate">Retro Cream Keyboard</h3>
                            <p class="font-mono font-bold text-base text-[#0F172A] mt-1">₹18,500</p>
                        </div>
                    </div>
                    <button
                        class="mt-3 w-full bg-slate-100 hover:bg-[#0F172A] hover:text-white text-[#0F172A] font-semibold text-xs py-2 rounded-lg transition"
                        onclick="triggerMiniAdd(this)">
                        🛒 Add to Cart
                    </button>
                </article>
                <!-- Card 3: Nike Air Max Earth -->
                <article
                    class="bg-white rounded-card p-4 border border-slate-900/10 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div
                            class="w-full aspect-square bg-slate-50 rounded-lg overflow-hidden flex items-center justify-center p-3">
                            <img alt="Nike Air Max Earth"
                                class="w-full h-full object-contain group-hover:scale-105 transition duration-300"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1Xep3-dlB7z3YoAD_OUxL9JusNyYl-fYu4q6eKlOSOZ6br_ALauCKVM6uFISE7m5spV00agB9G9sqJ_dNw2FedqwACnW1yGZQ6MQJAVPcHepLbD640I33clDsP90jYqsRZxxibHRC9if3kpfkSsoPQFouW7OCh6An1cW2TfHK9q5KXusLSjn1EsJ9hBupZcW99lKn7SmOSXwtjbGJwC2PS-cHJGkK-lVnnrd9FD3uGDWokcvfbNdxQ9mgg" />
                        </div>
                        <div>
                            <div class="flex items-center gap-1 text-[#F5A623] text-xs font-mono">
                                <span>★ 4.8</span>
                            </div>
                            <h3 class="font-display font-bold text-sm text-[#0F172A] truncate">Nike Air Max Earth</h3>
                            <p class="font-mono font-bold text-base text-[#0F172A] mt-1">₹31,000</p>
                        </div>
                    </div>
                    <button
                        class="mt-3 w-full bg-slate-100 hover:bg-[#0F172A] hover:text-white text-[#0F172A] font-semibold text-xs py-2 rounded-lg transition"
                        onclick="triggerMiniAdd(this)">
                        🛒 Add to Cart
                    </button>
                </article>
                <!-- Card 4: Apple Watch Ultra -->
                <article
                    class="bg-white rounded-card p-4 border border-slate-900/10 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div
                            class="w-full aspect-square bg-slate-50 rounded-lg overflow-hidden flex items-center justify-center p-3">
                            <img alt="Apple Watch Ultra"
                                class="w-full h-full object-contain group-hover:scale-105 transition duration-300"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1VdXEmh6VIHBeEKpbhBsJla5YRF2HZfN_yobBtuYB29VcpXPUhJ3bF_9h6T1v-aBFf9RjmXiP7SMtLWz8tL24YlWmvXBrRiy3Of39ExP85BvzYHoDoL_GGGbxXSqWdi9lnp-Q3RWE50x-h3nmfIdDsYuRa9jK19Gf1OXdvLjPu6tY-kxH7lRQL1qFgdx-WbwlmpFOp8_eww2HlqsF-1x2YrEWXQgabb7057tBdbEK6THq4cbJIPvONL01M" />
                        </div>
                        <div>
                            <div class="flex items-center gap-1 text-[#F5A623] text-xs font-mono">
                                <span>★ 4.9</span>
                            </div>
                            <h3 class="font-display font-bold text-sm text-[#0F172A] truncate">Apple Watch Ultra</h3>
                            <p class="font-mono font-bold text-base text-[#0F172A] mt-1">₹42,000</p>
                        </div>
                    </div>
                    <button
                        class="mt-3 w-full bg-slate-100 hover:bg-[#0F172A] hover:text-white text-[#0F172A] font-semibold text-xs py-2 rounded-lg transition"
                        onclick="triggerMiniAdd(this)">
                        🛒 Add to Cart
                    </button>
                </article>
            </div>
        </section>
    </main>
    <!-- 8. FLOATING AI ASSISTANT PILL (FIXED BOTTOM-RIGHT) -->
    <aside class="fixed bottom-5 right-5 z-40">
        <button
            class="bg-[#0F172A] hover:bg-slate-800 text-white px-4 py-2.5 rounded-full shadow-2xl border border-slate-700/80 flex items-center gap-2.5 transition active:scale-95 group"
            onclick="alert('Bazaario AI Assistant: Comparing specs, finding coupons, or initiating seller escrow price negotiation...')">
            <span
                class="material-symbols-outlined text-[#F5A623] text-[18px] group-hover:rotate-12 transition-transform">auto_awesome</span>
            <span class="font-display font-medium text-xs tracking-tight">Ask Bazaario AI: Compare specs or negotiate
                price</span>
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse ml-0.5"></span>
        </button>
    </aside>
    <!-- 9. FOOTER -->
    <footer class="w-full border-t border-slate-200 bg-white/70 backdrop-blur-md py-8 text-xs text-slate-500 font-mono">
        <div class="max-w-6xl mx-auto px-4 md:px-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3 flex-wrap">
                <img alt="Bazaario Logo" class="h-5 w-auto object-contain opacity-85"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuB6N8xXYkJuSE5X-CR6WEhK9PUa5-2rkboZlHagx8IBbmecvbjJWr3mRbh9nw4Tf2EyurhHLJKOv7I6TpOH_kTJv-Mz2qotBSSqMN6OteRo0kgP1gwtNnJ2AoPfCQu0dBlavrJaqFR3842QyVI-CnPDHEUfrYcRGKDkeF8YoDBq6egY6CSLELCFW5wBzp7DaZiL_GRe6kdZ54M_yUS6TJ94-1aJx4OsIQzaGlG-fbf-LuE7O9cuReNoqLY6N4A5BDeBoQ" />
                <span class="text-slate-300">|</span>
                <p>© 2026 Bazaario Inc. All rights reserved. 100% Escrow Guarantee Protected Marketplace.</p>
            </div>
            <div class="flex items-center space-x-5 flex-wrap">
                <a class="hover:text-[#0F172A] transition" href="#">Privacy Policy</a>
                <a class="hover:text-[#0F172A] transition" href="#">Terms of Service</a>
                <a class="hover:text-[#0F172A] transition" href="#">Escrow Guarantee</a>
                <a class="hover:text-[#0F172A] transition" href="#">Help Center</a>
            </div>
        </div>
    </footer>
    <!-- SCRIPT FOR INTERACTION -->
    <script>
        const galleryList = [
            "https://lh3.googleusercontent.com/aida/AEtjO1VBfMU_UJLdgyOwT14nG-AylyaKuOAGG2ZZqje9PxPmYECnNoJDh68Krv5X9qxiHEE91OaXp_EDCt15rQRy_d6wSZHmWvc-2WwjfxCyRKyXBqCJPKu-L3Tx9mxC9REuVmYucvAwPFfvDxod-gvguMPMEZv9fzJacAwGBT0Vtodc45GjrxPp1X-1LwYIRLUBL90UBCw54dIQBvsEzT7RHrJbneZrTHTldD4c-cC0eUZdQ_fBpU6yrnNlKvI",
            "https://lh3.googleusercontent.com/aida/AEtjO1U3XME0NHjyC0yTKDtPI-ikX0uXSsK46fZWbEaNRHHh3unKIDvt926bWFjY23XI1c5GTBPfAs4A1fHM-2MKmEAcn266rdb_5aiHgb4OI9OdXVLs1zjaghPZsIBGNIXAEJZw-Yl91LswyLLPZpKiukxNkkqjmxe5RpeUyLb_TpaY5LV6_vyqZcvQWn_5K_BDw8a2JEJUCAerh9mvrt4wZU0oatV1V4JCINW2MuphqazsPhOTaIZpo9ayAW4",
            "https://lh3.googleusercontent.com/aida/AEtjO1Wzt2qenVqQnp5b3WNrnmK9j8KZawCAt2uvf_-imd9ZPFeKz8NyGF_E0gE0047zxBPF__D5Jc_-tWxglSpDeAWXGT0QYKYMa8azKDyT3LpO-K2UFFteyMo7oKiwO6-NoFLmgTRYub_z4VvYkls48KdYB308dMR2mAyoCkLCz1CSZ-V2gSMW_ElybIVeVyM691WVLcfvRXORt8QLylnSBTVibdSfXVHv1EZmNzOCwcKHq-qcSMCPAhfSZjQ",
            "https://lh3.googleusercontent.com/aida/AEtjO1VdXEmh6VIHBeEKpbhBsJla5YRF2HZfN_yobBtuYB29VcpXPUhJ3bF_9h6T1v-aBFf9RjmXiP7SMtLWz8tL24YlWmvXBrRiy3Of39ExP85BvzYHoDoL_GGGbxXSqWdi9lnp-Q3RWE50x-h3nmfIdDsYuRa9jK19Gf1OXdvLjPu6tY-kxH7lRQL1qFgdx-WbwlmpFOp8_eww2HlqsF-1x2YrEWXQgabb7057tBdbEK6THq4cbJIPvONL01M"
        ];
        let activeGalleryIdx = 1; // Default to main Titanium back image

        function selectThumb(idx, src, btn) {
            activeGalleryIdx = idx;
            const img = document.getElementById('main-product-image');
            if (img) {
                img.style.opacity = '0.3';
                setTimeout(() => {
                    img.src = src;
                    img.style.opacity = '1';
                }, 120);
            }
            document.querySelectorAll('.thumb-btn').forEach(b => {
                b.className = 'thumb-btn border border-slate-200 p-1.5 rounded-card bg-white shadow-xs transition hover:scale-[1.02] flex items-center justify-center aspect-square';
            });
            btn.className = 'thumb-btn border-2 border-[#F5A623] p-1.5 rounded-card bg-white shadow-xs transition hover:scale-[1.02] flex items-center justify-center aspect-square';
        }

        function cycleGallery(delta) {
            activeGalleryIdx = (activeGalleryIdx + delta + galleryList.length) % galleryList.length;
            const thumbs = document.querySelectorAll('.thumb-btn');
            if (thumbs[activeGalleryIdx]) {
                selectThumb(activeGalleryIdx, galleryList[activeGalleryIdx], thumbs[activeGalleryIdx]);
            }
        }

        let currentColor = 'Natural Titanium';
        let currentStorage = '256 GB';

        function updateSpecLabel() {
            const label = document.getElementById('selected-spec-label');
            if (label) {
                label.innerText = `${currentStorage} • ${currentColor}`;
            }
        }

        function setColor(name, btn) {
            currentColor = name;
            document.getElementById('color-name').innerText = name;
            document.querySelectorAll('.color-btn').forEach(b => {
                b.className = 'color-btn w-8 h-8 rounded-full ring-1 ring-slate-300 hover:ring-2 hover:ring-slate-400 transition shadow-xs ' + b.className.split(' ').filter(c => c.startsWith('bg-')).join(' ');
            });
            btn.className = 'color-btn w-8 h-8 rounded-full ring-2 ring-offset-2 ring-[#0F172A] transition shadow-xs ' + btn.className.split(' ').filter(c => c.startsWith('bg-')).join(' ');
            updateSpecLabel();
        }

        function setStorage(size, price, mrp, btn) {
            currentStorage = size;
            document.getElementById('storage-name').innerText = size;
            document.getElementById('active-price').innerText = price;
            document.getElementById('mrp-price').innerText = mrp;
            document.querySelectorAll('.storage-btn').forEach(b => {
                b.className = 'storage-btn p-2.5 rounded-xl border border-slate-200 hover:border-slate-400 bg-white font-semibold text-slate-700 flex flex-col items-center justify-center transition';
            });
            btn.className = 'storage-btn p-2.5 rounded-xl border-2 border-[#0F172A] bg-slate-900/5 font-semibold text-[#0F172A] flex flex-col items-center justify-center transition';
            updateSpecLabel();
        }

        let qty = 1;
        function adjustQty(delta) {
            qty = Math.max(1, qty + delta);
            document.getElementById('stepper-count').innerText = qty;
        }

        function triggerCartAdd(btn) {
            const original = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">done</span> Added to Cart!';
            btn.classList.add('bg-emerald-500', 'text-white');
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('bg-emerald-500', 'text-white');
            }, 1800);
        }

        function triggerMiniAdd(btn) {
            const original = btn.innerHTML;
            btn.innerHTML = '✓ Added';
            btn.classList.add('bg-emerald-600', 'text-white');
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('bg-emerald-600', 'text-white');
            }, 1500);
        }

        function toggleWishlist(btn) {
            const icon = btn.querySelector('.material-symbols-outlined');
            if (icon) {
                if (icon.innerText === 'favorite_border') {
                    icon.innerText = 'favorite';
                    btn.classList.add('text-red-500');
                } else {
                    icon.innerText = 'favorite_border';
                    btn.classList.remove('text-red-500');
                }
            }
        }
    </script>
</body>

</html>