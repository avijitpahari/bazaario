<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <title>Saved Wishlist — Bazaario</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols Outlined -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "slate-authority": "#0F172A",
                        "amber-action": "#F5A623",
                        "status-green": "#16A34A",
                        "canvas-ivory": "#FFFDF8"
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        };
    </script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        * { -webkit-tap-highlight-color: transparent; }
    </style>
</head>

<body class="bg-[#FFFDF8] font-sans text-slate-800 antialiased min-h-screen flex flex-col selection:bg-amber-500 selection:text-slate-950"
      x-data="wishlistPage()">

    <!-- Customer Top Navbar Header Dock -->
    @include('components.nav-user')

    <!-- Toast Notification Banner -->
    <div x-cloak x-show="toast.visible" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
         class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 left-4 sm:left-auto z-50 flex items-center gap-3 px-4 sm:px-5 py-3 rounded-2xl shadow-2xl border text-xs sm:text-sm font-medium bg-slate-900 text-white border-slate-800">
        <span class="material-symbols-outlined text-lg sm:text-xl shrink-0" x-text="toast.icon"></span>
        <span class="truncate" x-text="toast.message"></span>
    </div>

    <!-- Main Container -->
    <main class="flex-1 w-full pt-20 sm:pt-28 pb-24 md:pb-16">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-1.5 text-[11px] sm:text-xs font-mono text-slate-500 mb-3 sm:mb-6">
                <a href="{{ url('/') }}" class="hover:text-amber-600 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('account.index') }}" class="hover:text-amber-600 transition-colors">Account</a>
                <span>/</span>
                <span class="text-slate-900 font-semibold">Saved Wishlist</span>
            </nav>

            <div class="flex items-center justify-between gap-3 pb-4 sm:pb-6 border-b border-slate-200/80 mb-6">
                <div>
                    <h1 class="text-xl sm:text-3xl font-display font-bold text-slate-900">My Saved Wishlist</h1>
                    <p class="text-xs text-slate-500 font-mono mt-0.5" x-text="items.length + ' saved items'"></p>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-mono font-semibold text-amber-600 hover:underline">
                    ← Explore More Items
                </a>
            </div>

            <!-- Empty Wishlist State -->
            <div x-show="items.length === 0" class="bg-white rounded-3xl p-10 sm:p-16 text-center border border-slate-200/80 shadow-xs max-w-xl mx-auto">
                <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">favorite</span>
                </div>
                <h2 class="text-lg sm:text-xl font-display font-bold text-slate-900 mb-1">Your wishlist is empty</h2>
                <p class="text-xs sm:text-sm text-slate-500 mb-6">Save products you love so you can easily purchase them later.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition-all shadow-md">
                    <span>Browse Marketplace</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <!-- Wishlist Products Grid (2 columns on mobile, 4 on desktop) -->
            <div x-show="items.length > 0" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
                <template x-for="(item, idx) in items" :key="item.id">
                    <div class="group bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-xl hover:border-slate-300 transition-all flex flex-col justify-between relative">
                        
                        <!-- Image Container -->
                        <div class="relative aspect-square w-full bg-slate-100 overflow-hidden">
                            <a :href="'/product/' + item.slug" class="block w-full h-full">
                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>

                            <!-- Remove Wishlist Button -->
                            <button @click="removeItem(idx)" 
                                    class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 backdrop-blur-md flex items-center justify-center text-rose-500 hover:scale-110 shadow-sm transition-all"
                                    title="Remove from wishlist">
                                <span class="material-symbols-outlined text-base sm:text-lg">close</span>
                            </button>
                        </div>

                        <!-- Card Details -->
                        <div class="p-2.5 sm:p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[9px] sm:text-[10px] font-mono text-slate-400 uppercase truncate block" x-text="item.category"></span>
                                <h3 class="font-display font-semibold text-slate-900 text-xs sm:text-sm leading-snug line-clamp-2 mt-0.5 hover:text-amber-600 transition-colors">
                                    <a :href="'/product/' + item.slug" x-text="item.name"></a>
                                </h3>
                                <div class="text-xs font-mono font-bold text-slate-900 mt-2" x-text="'₹' + Number(item.price).toLocaleString('en-IN')"></div>
                            </div>

                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center gap-1.5">
                                <button @click="moveToCart(item, idx)" 
                                        class="w-full py-2 bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-1 shadow-xs active:scale-95">
                                    <span class="material-symbols-outlined text-sm">shopping_cart</span>
                                    <span>Move to Cart</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

        </div>
    </main>

    <!-- ── NATIVE MOBILE BOTTOM NAVIGATION DOCK ── -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-slate-200/90 px-3 py-2 shadow-2xl flex items-center justify-around text-[10px] font-sans font-medium text-slate-600">
        <a href="{{ url('/') }}" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl transition-colors hover:text-slate-900">
            <span class="material-symbols-outlined text-[20px]">home</span>
            <span>Home</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl transition-colors hover:text-slate-900">
            <span class="material-symbols-outlined text-[20px]">storefront</span>
            <span>Shop</span>
        </a>
        <a href="{{ url('/user/account/wishlist') }}" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl text-slate-900 font-bold">
            <span class="material-symbols-outlined text-[20px] text-rose-500 fill-current">favorite</span>
            <span>Wishlist</span>
        </a>
        <a href="{{ url('/cart') }}" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl transition-colors hover:text-slate-900">
            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
            <span>Cart</span>
        </a>
        <a href="{{ url('/account') }}" class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-xl transition-colors hover:text-slate-900">
            <span class="material-symbols-outlined text-[20px]">account_circle</span>
            <span>Account</span>
        </a>
    </nav>

    <!-- Global Footer -->
    <footer class="bg-slate-900 text-slate-400 text-xs pt-8 pb-28 md:py-10 mt-auto border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div class="flex items-center gap-2 justify-center sm:justify-start">
                <span class="font-display font-bold text-white tracking-wider">BAZAARIO</span>
                <span>— Multi-Vendor Verified Escrow Marketplace</span>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 font-mono text-[11px]">
                <a href="{{ url('/privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="{{ url('/terms') }}" class="hover:text-white transition-colors">Terms of Service</a>
                <a href="{{ url('/support') }}" class="hover:text-white transition-colors">Customer Support</a>
            </div>
        </div>
    </footer>

    <!-- Alpine.js Wishlist Logic -->
    <script>
        function wishlistPage() {
            return {
                toast: { visible: false, message: '', icon: 'check' },
                items: [
                    {
                        id: 1,
                        name: 'Handcrafted Heritage Leather Messenger Bag',
                        slug: 'handcrafted-leather-messenger-bag',
                        category: 'Artisan Craft',
                        price: 4299,
                        image: 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&auto=format&fit=crop&q=80'
                    },
                    {
                        id: 4,
                        name: 'Acoustic Pro Active Noise-Cancelling Headphones',
                        slug: 'acoustic-pro-anc-headphones',
                        category: 'Electronics',
                        price: 12499,
                        image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&auto=format&fit=crop&q=80'
                    }
                ],

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.showToast('Item removed from wishlist');
                },

                moveToCart(item, index) {
                    this.items.splice(index, 1);
                    this.showToast(`Moved "${item.name.substring(0, 18)}..." to cart!`, 'shopping_cart');
                },

                showToast(msg, icon = 'check') {
                    this.toast.message = msg;
                    this.toast.icon = icon;
                    this.toast.visible = true;
                    setTimeout(() => { this.toast.visible = false; }, 3000);
                }
            };
        }
    </script>
</body>
</html>
