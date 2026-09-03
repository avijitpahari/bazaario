<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Platform Command — Bazaario')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .custom-admin-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-admin-scrollbar::-webkit-scrollbar-track { background: #090d16; }
        .custom-admin-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        .custom-admin-scrollbar::-webkit-scrollbar-thumb:hover { background: #334155; }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-200 bg-slate-950 selection:bg-indigo-500 selection:text-white" 
      x-data="{ 
          sidebarOpen: false, 
          quickSearchOpen: false, 
          notifDrawer: false,
          searchQuery: '',
          systemNotice: true,
          clearCacheToast: false,
          triggerCacheClear() {
              this.clearCacheToast = true;
              setTimeout(() => this.clearCacheToast = false, 3000);
          }
      }"
      @keydown.window.prevent.slash="quickSearchOpen = true"
      @keydown.window.escape="quickSearchOpen = false; notifDrawer = false">

    <!-- Global Toast -->
    <div x-show="clearCacheToast" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-slate-950 px-5 py-3.5 rounded-2xl shadow-2xl font-bold text-xs flex items-center gap-2.5">
        <i class="fa-solid fa-circle-check text-base"></i> Platform cache & route tables flushed successfully!
    </div>

    <!-- Quick Search Command Palette (Triggered with '/' or click) -->
    <div x-show="quickSearchOpen" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-start justify-center pt-20 p-4">
        <div @click.outside="quickSearchOpen = false" class="bg-slate-900 border border-slate-700/80 rounded-3xl p-5 max-w-2xl w-full shadow-2xl space-y-4">
            <div class="relative flex items-center border-b border-slate-800 pb-3">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3.5 text-base"></i>
                <input type="text" x-model="searchQuery" placeholder="Search sellers, orders (#BZR-...), products, or settings..." class="w-full pl-11 pr-10 py-3 bg-transparent text-sm text-white placeholder-slate-500 focus:outline-none font-medium" autofocus>
                <button @click="quickSearchOpen = false" class="text-xs bg-slate-800 text-slate-400 px-2 py-1 rounded-lg border border-slate-700 font-mono">ESC</button>
            </div>
            
            <div class="text-xs space-y-3 max-h-80 overflow-y-auto custom-admin-scrollbar">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-2">Fast Navigation</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <a href="{{ route('admin.sellers.pending') }}" class="p-3 rounded-2xl bg-slate-800/60 hover:bg-indigo-600 hover:text-white transition flex items-center justify-between group">
                        <span class="flex items-center gap-2.5 font-semibold"><i class="fa-solid fa-user-clock text-amber-400 group-hover:text-white"></i> Seller Approvals Queue</span>
                        <span class="text-[10px] bg-amber-500/20 text-amber-300 group-hover:bg-white/20 group-hover:text-white px-2 py-0.5 rounded-full font-bold">7</span>
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="p-3 rounded-2xl bg-slate-800/60 hover:bg-indigo-600 hover:text-white transition flex items-center justify-between group">
                        <span class="flex items-center gap-2.5 font-semibold"><i class="fa-solid fa-triangle-exclamation text-rose-400 group-hover:text-white"></i> Open Disputes</span>
                        <span class="text-[10px] bg-rose-500/20 text-rose-300 group-hover:bg-white/20 group-hover:text-white px-2 py-0.5 rounded-full font-bold">2</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="p-3 rounded-2xl bg-slate-800/60 hover:bg-indigo-600 hover:text-white transition flex items-center gap-2.5 font-semibold">
                        <i class="fa-solid fa-folder-tree text-indigo-400"></i> Category Governance
                    </a>
                    <a href="{{ route('admin.settings.platform') }}" class="p-3 rounded-2xl bg-slate-800/60 hover:bg-indigo-600 hover:text-white transition flex items-center gap-2.5 font-semibold">
                        <i class="fa-solid fa-sliders text-emerald-400"></i> Platform Mode & Fees
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Drawer -->
    <div x-show="notifDrawer" x-cloak class="fixed inset-y-0 right-0 z-50 w-full max-w-sm bg-slate-900 border-l border-slate-800 p-6 shadow-2xl flex flex-col justify-between">
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-bell text-indigo-400"></i>
                    <h3 class="font-bold text-sm font-heading text-white">System & Governance Alerts</h3>
                </div>
                <button @click="notifDrawer = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="p-3 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-200 space-y-1">
                    <span class="font-bold text-amber-400 flex items-center gap-1.5"><i class="fa-solid fa-store"></i> 7 New Stall Applications</span>
                    <p class="text-[11px] text-slate-300">Merchants awaiting tax document review.</p>
                </div>
                <div class="p-3 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-200 space-y-1">
                    <span class="font-bold text-rose-400 flex items-center gap-1.5"><i class="fa-solid fa-shield-halved"></i> Dispute Raised: #BZR-98188</span>
                    <p class="text-[11px] text-slate-300">Buyer claims damaged package from Shady Trinkets Co.</p>
                </div>
                <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-200 space-y-1">
                    <span class="font-bold text-emerald-400 flex items-center gap-1.5"><i class="fa-solid fa-money-bill-transfer"></i> Payout Cycle Ready</span>
                    <p class="text-[11px] text-slate-300">$18,420 ready for bi-weekly merchant disbursement.</p>
                </div>
            </div>
        </div>

        <button @click="notifDrawer = false" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
            Close Alert Center
        </button>
    </div>

    <div class="min-h-full flex">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-black/80 backdrop-blur-sm lg:hidden"></div>

        <!-- Wide Labeled Admin Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 border-r border-slate-800 text-slate-300 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 custom-admin-scrollbar">
            
            <div>
                <!-- Brand / Executive Header -->
                <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950/60">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-rose-600 via-indigo-600 to-indigo-500 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-rose-600/20 group-hover:scale-105 transition">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div class="font-heading font-bold text-lg text-white flex items-center gap-1.5">
                                Bazaario
                                <span class="text-[10px] uppercase font-mono tracking-wider font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 px-1.5 py-0.2 rounded">HQ</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Executive Control Panel</p>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Platform Live Mode Indicator Widget -->
                <div class="p-3.5 mx-4 my-3 rounded-2xl bg-slate-950/70 border border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <div>
                            <span class="text-xs font-bold text-white block leading-tight">Multi-Vendor Mode</span>
                            <span class="text-[10px] text-slate-400 font-mono">Commission: 8.5%</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.settings.platform') }}" class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/20 px-2 py-1 rounded-lg transition">
                        Configure
                    </a>
                </div>

                <!-- Navigation Menu Links -->
                <nav class="px-4 space-y-1 overflow-y-auto max-h-[calc(100vh-280px)] custom-admin-scrollbar">
                    
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 mt-2 mb-1 font-mono">Platform Executive</p>
                    
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-chart-pie w-4 text-center text-sm"></i> Overview & GMV Metrics
                        </div>
                        <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-white/10">Live</span>
                    </a>

                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 mt-4 mb-1 font-mono">Seller Governance</p>
                    
                    <a href="{{ route('admin.sellers.pending') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.sellers.pending') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-user-clock w-4 text-center text-amber-400"></i> Stall Approvals
                        </div>
                        <span class="text-[10px] font-bold font-mono px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">7 pending</span>
                    </a>

                    <a href="{{ route('admin.sellers.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.sellers.index') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-users-gear w-4 text-center"></i> All Merchant Stalls
                        </div>
                        <span class="text-[11px] font-mono text-slate-400">124</span>
                    </a>

                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 mt-4 mb-1 font-mono">Commerce & Catalog</p>

                    <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-folder-tree w-4 text-center"></i> Category Taxonomies
                        </div>
                        <span class="text-[11px] font-mono text-slate-400">8 roots</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-receipt w-4 text-center"></i> Platform Orders & Disputes
                        </div>
                        <span class="text-[10px] font-bold font-mono px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30">2 disputes</span>
                    </a>

                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 mt-4 mb-1 font-mono">System Configuration</p>

                    <a href="{{ route('admin.settings.branding') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.settings.branding') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-palette w-4 text-center"></i> Branding & Visual Setup
                    </a>

                    <a href="{{ route('admin.settings.platform') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.settings.platform') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-sliders w-4 text-center"></i> Platform Mode & Fees
                    </a>
                </nav>
            </div>

            <!-- Bottom Operations Bar -->
            <div class="p-4 border-t border-slate-800 space-y-2 bg-slate-950/40">
                <div class="flex items-center justify-between text-[11px] text-slate-400 px-1">
                    <span>Server: <strong class="text-emerald-400">PHP 8.2 • 32ms</strong></span>
                    <button @click="triggerCacheClear()" class="text-indigo-400 hover:text-white flex items-center gap-1" title="Flush Route & View Cache">
                        <i class="fa-solid fa-arrows-rotate"></i> Flush
                    </button>
                </div>
                <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Open Live Storefront
                </a>
            </div>
        </aside>

        <!-- Admin Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-950 overflow-hidden">
            
            <!-- Admin Top Header Bar -->
            <header class="h-20 bg-slate-950/90 border-b border-slate-800/80 backdrop-blur-md flex items-center justify-between px-4 sm:px-8 z-10">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-400 hover:bg-slate-800">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-bold font-heading text-white">@yield('page-title', 'Platform Management')</h1>
                            <span class="hidden md:inline text-xs text-slate-500 font-mono">v2.4-enterprise</span>
                        </div>
                        <p class="text-xs text-slate-400 hidden sm:block">Unified marketplace supervision, seller compliance, and multi-vendor escrow management</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    
                    <!-- Search Shortcut Button -->
                    <button @click="quickSearchOpen = true" class="hidden md:flex items-center gap-2.5 px-3.5 py-2 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs text-slate-400 transition">
                        <i class="fa-solid fa-magnifying-glass text-slate-500"></i>
                        <span>Search platform...</span>
                        <kbd class="px-1.5 py-0.5 rounded bg-slate-800 text-[10px] font-mono text-slate-400 border border-slate-700">/</kbd>
                    </button>

                    <!-- Alert / Notification Bell -->
                    <button @click="notifDrawer = true" class="p-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white relative transition">
                        <i class="fa-regular fa-bell text-base"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-slate-950"></span>
                    </button>

                    <!-- User Pill -->
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-800">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-rose-500 flex items-center justify-center text-white font-black text-xs shadow-md">
                            AD
                        </div>
                        <div class="hidden xl:block text-left text-xs">
                            <span class="font-bold text-white block leading-tight">Super Admin</span>
                            <span class="text-[10px] text-emerald-400 font-mono">Root Access</span>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Scrollable Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-950 custom-admin-scrollbar">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
