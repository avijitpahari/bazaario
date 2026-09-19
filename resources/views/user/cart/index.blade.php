<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Cart — 3D Dimensional Glass with Dock Nav | Bazaario</title>
    <!-- Tailwind CSS CDN with forms and container queries -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Space Grotesk & Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Space+Grotesk:wght@500;600;700&amp;family=JetBrains+Mono:wght@500;600&amp;display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        bazaarAmber: {
                            DEFAULT: '#F5A623',
                            hover: '#E09212',
                            light: '#FEF6E9',
                            border: '#FDE4BE',
                        },
                        bazaarDark: '#121316',
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.05)',
                        'card-glass': '0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                        'pill': '0 6px 20px rgba(0, 0, 0, 0.06)',
                        'dock': '0 12px 36px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>
    <!-- Custom Layout & Aesthetic Styles -->
    <style data-purpose="ambient-background">
        body {
            background-color: #FFFDF8;
            background-image:
                radial-gradient(at 10% 15%, rgba(245, 166, 35, 0.05) 0px, transparent 50%),
                radial-gradient(at 90% 25%, rgba(139, 92, 246, 0.04) 0px, transparent 45%),
                radial-gradient(at 50% 85%, rgba(245, 166, 35, 0.03) 0px, transparent 50%);
            background-attachment: fixed;
        }
    </style>
    <style data-purpose="glass-effects">
        .frosted-dock {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .frosted-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.7);
        }

        .frosted-summary {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.9);
        }
    </style>
</head>

<body
    class="text-slate-800 antialiased min-h-screen flex flex-col justify-between selection:bg-amber-200 selection:text-amber-950 font-sans">
    <!-- BEGIN: TopDockNavigation -->
    <header class="sticky top-4 z-50 px-4 sm:px-6 w-full max-w-6xl mx-auto" data-purpose="floating-dock-nav">
        <nav
            class="frosted-dock shadow-dock rounded-full py-2 px-3 sm:px-5 flex items-center justify-between transition-all duration-300">
            <!-- Left: Logo & Core Hub Link -->
            <div class="flex items-center gap-6">
                <a class="flex items-center gap-2 group focus:outline-none" href="#">
                    <div
                        class="h-8 w-8 rounded-lg bg-gradient-to-tr from-amber-500 via-amber-400 to-indigo-600 p-[1.5px] shadow-sm transition-transform group-hover:scale-105">
                        <div class="w-full h-full bg-white rounded-[7px] flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-900" fill="currentColor" viewbox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                            </svg>
                        </div>
                    </div>
                    <span class="font-display text-xl font-bold tracking-tight text-slate-950">Bazaario<span
                            class="text-amber-500">.</span></span>
                </a>
                <!-- Middle Navigation Links Pill -->
                <div
                    class="hidden md:flex items-center gap-1 text-xs font-medium text-slate-600 bg-slate-100/70 p-1 rounded-full border border-slate-200/50">
                    <a class="px-3.5 py-1.5 rounded-full text-slate-700 hover:text-slate-950 hover:bg-white/80 transition-colors"
                        href="#">Shop</a><a
                        class="px-3.5 py-1.5 rounded-full text-slate-700 hover:text-slate-950 hover:bg-white/80 transition-colors"
                        href="#">Categories</a><a
                        class="px-3.5 py-1.5 rounded-full text-slate-700 hover:text-slate-950 hover:bg-white/80 transition-colors"
                        href="#">Auctions</a><a
                        class="px-3.5 py-1.5 rounded-full text-slate-700 hover:text-slate-950 hover:bg-white/80 transition-colors"
                        href="#">AI Compare</a><a
                        class="px-3.5 py-1.5 rounded-full bg-white text-slate-950 font-semibold shadow-xs flex items-center gap-1.5 transition-colors"
                        href="#"><svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor"
                            stroke-width="2" viewbox="0 0 24 24">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>Cart <span
                            class="w-4 h-4 rounded-full bg-amber-500 text-slate-950 text-[10px] font-bold flex items-center justify-center font-mono">2</span></a>
                </div>
            </div>
            <!-- Right Utility Section: Notification & User Pill -->
            <div class="flex items-center gap-3">
                <!-- Notification Button -->
                <button aria-label="Notifications"
                    class="relative w-9 h-9 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:border-slate-300 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
                        <path
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-500 rounded-full ring-2 ring-white"></span>
                </button>
                <!-- User Profile Pill -->
                <div
                    class="flex items-center gap-2 pl-1 pr-3 py-1 bg-white border border-slate-200/90 rounded-full shadow-sm hover:border-slate-300 transition-all cursor-pointer">
                    <div
                        class="w-7 h-7 rounded-full bg-slate-900 text-amber-400 font-semibold text-xs flex items-center justify-center tracking-tight">
                        A
                    </div>
                    <span class="text-xs font-semibold text-slate-800">Avijit</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 ml-0.5" fill="none" stroke="currentColor" stroke-width="2"
                        viewbox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
            </div>
        </nav>
    </header>
    <!-- END: TopDockNavigation -->
    <!-- BEGIN: MainContent -->
    <main class="w-full max-w-6xl mx-auto px-4 sm:px-6 py-8 flex-grow" data-purpose="cart-interface">
        <!-- Header Block: Eyebrow, Title & Subtitle -->
        <section class="mb-8" data-purpose="page-heading">
            <!-- Eyebrow Tag -->
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/80 text-amber-800 text-[11px] font-mono tracking-wider font-semibold uppercase mb-3 shadow-xs">
                <span class="text-amber-500">✦</span> VERIFIED SECURE CHECKOUT &amp; ESCROW <span
                    class="text-amber-500">✦</span>
            </div>
            <div class="flex flex-col md:flex-row md:items-baseline justify-between gap-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl sm:text-4xl font-display font-bold text-slate-950 tracking-tight">Shopping Cart
                    </h1>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">2
                        Items</span>
                </div>
                <p class="text-sm text-slate-500 max-w-lg">
                    Review your selected items, apply promotional perks, and proceed with 100% escrow buyer protection.
                </p>
            </div>
        </section>
        <!-- Grid Container: Items List (Left 8 Cols) & Order Summary (Right 4 Cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- BEGIN: CartItemsColumn -->
            <div class="lg:col-span-8 space-y-4" data-purpose="cart-items-list">
                <article
                    class="backdrop-blur-2xl bg-white/80 border border-white/70 shadow-[0_16px_36px_rgba(15,23,42,0.05),0_1px_2px_rgba(255,255,255,0.9)_inset] rounded-2xl p-5 sm:p-6 transition-all duration-200 hover:shadow-lg hover:border-amber-200/60">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 sm:gap-6">
                        <div
                            class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden bg-slate-100 border border-slate-200/80 flex-shrink-0 shadow-inner group">
                            <img alt="Sony WH-1000XM5 Wireless Headphones"
                                class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1VHIOUaIHnEHXj6aAZR4Ssh03jnYrUls7pm_emn5hHL2orC7ViQ-wxHcYe-_OtQ4l7DFZJtmf9sVuU9gusW4XgqcW0bn92muRJsSPTObn0awSbspcMJr7VIFOyHd974SJiQ_R0tqH09fkmekCH06QhNvFA8G_Azm4NodqGuMIHPfhtk9Z3DeVZT5bgVvTgUIHqlnWVHYBzuChGGQ3NqtT_B5CoyX1mWpCicCbGqDmUt56gau_yghNUuBQ" /><span
                                class="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-md bg-slate-950/75 backdrop-blur-md text-[9px] font-mono text-amber-300 font-semibold tracking-wider border border-white/10">STUDIO
                                AUDIO</span></div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center gap-2 mb-1.5"><span
                                    class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 flex items-center gap-1.5"><span
                                        class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> In
                                    Stock</span><span class="text-xs text-slate-500 font-medium">Instant Buy
                                    Verified</span></div>
                            <h2 class="text-base sm:text-lg font-display font-bold text-slate-900 truncate">Sony
                                WH-1000XM5 Premium Headphones</h2>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 flex-wrap"><span>Sony Audio
                                    • Matte Black</span><span>•</span><span
                                    class="inline-flex items-center gap-1 text-slate-700 font-medium"><svg
                                        class="w-3 h-3 text-emerald-600" fill="currentColor" viewbox="0 0 20 20">
                                        <path clip-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            fill-rule="evenodd"></path>
                                    </svg> TechWorld Electronics</span></p>
                            <div class="mt-3 flex items-center gap-4 text-xs font-medium text-slate-500"><button
                                    class="hover:text-slate-900 transition-colors flex items-center gap-1"><svg
                                        class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewbox="0 0 24 24">
                                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg> Save for Later</button><span class="text-slate-300">|</span><button
                                    class="hover:text-red-600 flex items-center gap-1 transition-colors"><svg
                                        class="w-3.5 h-3.5 text-slate-400 hover:text-red-600" fill="none"
                                        stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg> Remove</button></div>
                        </div>
                        <div
                            class="flex sm:flex-col items-end justify-between w-full sm:w-auto gap-4 sm:gap-3 pt-3 sm:pt-0 border-t sm:border-0 border-slate-100">
                            <div class="text-right">
                                <div class="font-display font-bold text-xl text-slate-900">₹25,000</div>
                                <div class="text-xs text-slate-400 font-mono">₹25,000 × 1</div>
                            </div>
                            <div
                                class="flex items-center gap-1 border border-slate-200/90 rounded-xl bg-slate-50/90 p-1 shadow-xs">
                                <button aria-label="Decrease quantity"
                                    class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 active:scale-95 text-slate-700 flex items-center justify-center font-bold text-sm border border-slate-200/70 transition-all shadow-xs">−</button><span
                                    class="w-8 text-center text-sm font-bold text-slate-900 font-mono">1</span><button
                                    aria-label="Increase quantity"
                                    class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 active:scale-95 text-slate-700 flex items-center justify-center font-bold text-sm border border-slate-200/70 transition-all shadow-xs">+</button>
                            </div>
                        </div>
                    </div>
                </article>
                <article
                    class="backdrop-blur-2xl bg-white/80 border border-white/70 shadow-[0_16px_36px_rgba(15,23,42,0.05),0_1px_2px_rgba(255,255,255,0.9)_inset] rounded-2xl p-5 sm:p-6 transition-all duration-200 hover:shadow-lg hover:border-amber-200/60">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 sm:gap-6">
                        <div
                            class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden bg-slate-100 border border-slate-200/80 flex-shrink-0 shadow-inner group">
                            <img alt="Retro Cream Mechanical Keyboard"
                                class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1Vuz-e4ii6gOV2jAQgMpB1N9MIzgysUeNPdcwjYPzkYHga3UfjHTFkS8fV47RmoFUipezX_vsHo5VUXgpGvYfuiLYWCL6KwVgK2YofEOcjvrD7zgENNTjPH6_9ab9Wbd8R3aWmYg1Asip4dJ1bqyLdJS_B9Q_v2_CWdT3Z7b7lW8j6T4qpgzDUUdMYSEG2cj1zvGiQWjGEYf78vpvUhqvVPVQD5Cd-OnQNGRa_cTNAjtQjwoMCHPba5ag" /><span
                                class="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-md bg-slate-950/75 backdrop-blur-md text-[9px] font-mono text-amber-300 font-semibold tracking-wider border border-white/10">ARTISAN</span>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center gap-2 mb-1.5"><span
                                    class="text-xs font-semibold text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200/80">Custom
                                    Craft Batch #48</span><span class="text-xs text-slate-500 font-medium">Limited
                                    Run</span></div>
                            <h2 class="text-base sm:text-lg font-display font-bold text-slate-900 truncate">Retro Cream
                                Mechanical Keyboard</h2>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 flex-wrap"><span>Gateron
                                    Yellow Switches • Qty: 2</span><span>•</span><span
                                    class="inline-flex items-center gap-1 text-slate-700 font-medium">Craft Desk Studio
                                    • Artisan Stall</span></p>
                            <div class="mt-3 flex items-center gap-4 text-xs font-medium text-slate-500"><button
                                    class="hover:text-slate-900 transition-colors flex items-center gap-1"><svg
                                        class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewbox="0 0 24 24">
                                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg> Save for Later</button><span class="text-slate-300">|</span><button
                                    class="hover:text-red-600 flex items-center gap-1 transition-colors"><svg
                                        class="w-3.5 h-3.5 text-slate-400 hover:text-red-600" fill="none"
                                        stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg> Remove</button></div>
                        </div>
                        <div
                            class="flex sm:flex-col items-end justify-between w-full sm:w-auto gap-4 sm:gap-3 pt-3 sm:pt-0 border-t sm:border-0 border-slate-100">
                            <div class="text-right">
                                <div class="font-display font-bold text-xl text-slate-900">₹25,000</div>
                                <div class="text-xs text-slate-400 font-mono">₹12,500 each</div>
                            </div>
                            <div
                                class="flex items-center gap-1 border border-slate-200/90 rounded-xl bg-slate-50/90 p-1 shadow-xs">
                                <button aria-label="Decrease quantity"
                                    class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 active:scale-95 text-slate-700 flex items-center justify-center font-bold text-sm border border-slate-200/70 transition-all shadow-xs">−</button><span
                                    class="w-8 text-center text-sm font-bold text-slate-900 font-mono">2</span><button
                                    aria-label="Increase quantity"
                                    class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 active:scale-95 text-slate-700 flex items-center justify-center font-bold text-sm border border-slate-200/70 transition-all shadow-xs">+</button>
                            </div>
                        </div>
                    </div>
                </article>
                <div class="flex items-center justify-between pt-2 px-1 text-sm font-medium"><a
                        class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-950 transition-colors font-medium"
                        href="#"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                            viewbox="0 0 24 24">
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg> Continue Shopping</a><button
                        class="text-slate-400 hover:text-red-600 text-xs transition-colors">Clear Entire Cart</button>
                </div>
                <div
                    class="mt-6 rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white p-5 sm:p-6 shadow-xl border border-slate-700/60 relative overflow-hidden">
                    <div class="relative z-10 flex items-start gap-4">
                        <div
                            class="w-11 h-11 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center flex-shrink-0 text-amber-400 text-xl shadow-inner">
                            🛡️</div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-display font-semibold text-sm tracking-wide text-white">Bazaario Escrow
                                    Hold Guarantee</h3><span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/40">100%
                                    SECURED</span>
                            </div>
                            <p class="text-xs text-slate-300 leading-relaxed max-w-xl">Your payment is held in an
                                encrypted digital trust escrow. Sellers do not receive payout until you receive, unbox,
                                and verify your orders within 48 hours.</p>
                        </div>
                    </div>
                    <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-amber-500/10 rounded-full blur-3xl"></div>
                </div>
            </div>
            <!-- END: CartItemsColumn -->
            <!-- BEGIN: OrderSummaryColumn -->
            <aside class="lg:col-span-4" data-purpose="sticky-order-summary">
                <div
                    class="backdrop-blur-2xl bg-white/90 border border-white/80 shadow-[0_20px_50px_rgba(15,23,42,0.06),0_1px_2px_rgba(255,255,255,0.9)_inset] rounded-2xl p-6 sticky top-24">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
                        <h2 class="font-display font-bold text-xl text-slate-950">Order Summary</h2><span
                            class="text-xs font-mono font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">2
                            Items</span>
                    </div>
                    <div class="my-5" data-purpose="promo-code"><label
                            class="block text-xs font-medium text-slate-600 mb-1.5" for="promo">Promotion or Gift
                            Card</label>
                        <div class="flex gap-2"><input
                                class="block w-full text-xs font-mono uppercase rounded-xl border-slate-200 bg-white shadow-xs focus:border-amber-500 focus:ring-amber-500 px-3 py-2.5 text-slate-800 font-semibold"
                                id="promo" placeholder="ENTER CODE" type="text" value="BAZAARIOFIRST" /><button
                                class="px-4 py-2.5 bg-slate-950 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors flex-shrink-0 active:scale-95">Apply</button>
                        </div>
                        <div
                            class="mt-2.5 flex items-center justify-between text-xs bg-amber-50/90 border border-amber-200/80 rounded-xl px-3 py-2 text-amber-900 shadow-xs">
                            <span class="font-mono text-[11px] font-semibold flex items-center gap-1.5"><span
                                    class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">✓</span>
                                BAZAARIOFIRST (-₹1,000)</span><button
                                class="text-slate-400 hover:text-amber-800 text-[10px] font-bold uppercase transition-colors">Remove</button>
                        </div>
                    </div>
                    <div class="space-y-3.5 text-xs text-slate-600 pb-5 border-b border-slate-200/80 font-medium">
                        <div class="flex justify-between items-center"><span>Cart Subtotal</span><span
                                class="text-slate-950 font-mono font-semibold text-sm">₹50,000</span></div>
                        <div class="flex justify-between items-center"><span>Estimated Delivery</span><span
                                class="text-slate-950 font-mono font-semibold text-sm">₹99</span></div>
                        <div class="flex justify-between items-center text-amber-700 font-semibold"><span>Promotional
                                Discount</span><span class="font-mono text-sm">-₹1,000</span></div>
                        <div class="flex justify-between items-center text-slate-500"><span
                                class="flex items-center gap-1">Escrow Protection Fee <svg
                                    class="w-3 h-3 text-slate-400" fill="currentColor" viewbox="0 0 20 20">
                                    <path clip-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        fill-rule="evenodd"></path>
                                </svg></span><span class="text-emerald-600 font-semibold font-mono">₹0 <span
                                    class="text-[10px] font-normal text-slate-400">(Waived)</span></span></div>
                    </div>
                    <div class="pt-4 pb-6">
                        <div class="flex items-baseline justify-between">
                            <div><span class="text-sm font-semibold text-slate-900">Total Payable</span>
                                <p class="text-[11px] text-slate-400 mt-0.5">Incl. ₹99 shipping &amp; ₹1,000 discount
                                </p>
                            </div>
                            <div class="text-right"><span
                                    class="font-display font-bold text-3xl text-slate-950 tracking-tight">₹49,099</span>
                            </div>
                        </div>
                    </div><button
                        class="w-full py-4 px-6 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-display font-bold text-base shadow-lg shadow-amber-500/20 hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 group active:scale-[0.99]"><span>Proceed
                            to Checkout</span><svg class="w-4 h-4 transition-transform group-hover:translate-x-1"
                            fill="none" stroke="currentColor" stroke-width="2.5" viewbox="0 0 24 24">
                            <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg></button>
                    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col items-center text-center">
                        <div class="flex items-center justify-center gap-2 text-slate-500 text-[11px] font-mono mb-2.5">
                            <span
                                class="px-2 py-0.5 rounded border border-slate-200/80 bg-white font-semibold shadow-xs">UPI</span><span
                                class="px-2 py-0.5 rounded border border-slate-200/80 bg-white font-semibold shadow-xs">VISA</span><span
                                class="px-2 py-0.5 rounded border border-slate-200/80 bg-white font-semibold shadow-xs">Mastercard</span><span
                                class="px-2 py-0.5 rounded border border-slate-200/80 bg-white font-semibold shadow-xs">NetBanking</span>
                        </div>
                        <p class="text-[11px] text-slate-500 flex items-center justify-center gap-1.5 font-medium"><svg
                                class="w-3.5 h-3.5 text-emerald-600 inline" fill="currentColor" viewbox="0 0 20 20">
                                <path clip-rule="evenodd"
                                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    fill-rule="evenodd"></path>
                            </svg> 256-Bit Cryptographic SSL • 100% Refund Guarantee</p>
                    </div>
                </div>
            </aside>
            <!-- END: OrderSummaryColumn -->
        </div>
    </main>
    <!-- END: MainContent -->
    <!-- BEGIN: FloatingAssistantTrigger -->
    <!-- Docked floating Ask Bazaario AI button -->
    <aside class="fixed bottom-5 right-5 z-40" data-purpose="floating-ai-assistant">
        <button
            class="flex items-center gap-2.5 px-4 py-2.5 rounded-full bg-slate-900 text-white hover:bg-slate-800 shadow-dock border border-slate-700/60 transition-all duration-300 hover:scale-105 active:scale-95 group focus:outline-none">
            <span class="text-amber-400 text-sm">✦</span>
            <span class="text-xs font-display font-semibold tracking-wide">Ask Bazaario AI</span>
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse ml-0.5"></span>
        </button>
    </aside>
    <!-- END: FloatingAssistantTrigger -->
    <!-- BEGIN: Footer -->
    <footer class="w-full border-t border-slate-200/80 bg-white/70 backdrop-blur-md mt-16 py-6 px-4 sm:px-6"
        data-purpose="page-footer">
        <div
            class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-medium">
            <!-- Legal Text -->
            <div class="flex items-center gap-2">
                <span class="font-mono text-slate-700">© 2025 BAZAARIO MARKETPLACE. ALL RIGHTS RESERVED.</span>
            </div>
            <!-- Links -->
            <div class="flex items-center gap-6">
                <a class="hover:text-slate-900 transition-colors" href="#">Privacy</a>
                <a class="hover:text-slate-900 transition-colors" href="#">Terms</a>
                <a class="hover:text-slate-900 transition-colors" href="#">Escrow Guarantee</a>
                <a class="hover:text-slate-900 transition-colors" href="#">Help</a>
            </div>
        </div>
    </footer>
    <!-- END: Footer -->
</body>

</html>