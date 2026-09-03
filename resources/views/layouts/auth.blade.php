<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Authentication — Bazaario')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
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
    </style>
</head>
<body class="min-h-full flex flex-col justify-center bg-slate-50 antialiased selection:bg-indigo-500 selection:text-white">

    <div class="min-h-screen flex">
        
        <!-- Left Side Feature Column (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 text-white flex-col justify-between p-12 overflow-hidden">
            <!-- Background Gradient / Image Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 via-slate-900 to-amber-950/40 opacity-90"></div>
            <img src="https://images.unsplash.com/photo-1472851294608-062f824d29cc?auto=format&fit=crop&w=1200&q=80" alt="Marketplace" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-30">

            <div class="relative z-10">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-amber-400 flex items-center justify-center text-white font-black text-xl shadow-lg">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <span class="text-2xl font-bold font-heading text-white tracking-tight">Bazaario</span>
                </a>
            </div>

            <div class="relative z-10 space-y-6 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-amber-300">
                    <i class="fa-solid fa-shield-halved"></i> 100% Protected Marketplace
                </div>
                <h2 class="text-4xl font-bold font-heading leading-tight text-white">
                    Discover authentic goods from independent creators & verified stalls.
                </h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Join thousands of buyers and sellers enjoying transparent pricing, multi-vendor unified cart, and live tracking across the globe.
                </p>

                <!-- Micro Stats Bar -->
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-white/10">
                    <div>
                        <div class="text-2xl font-bold font-heading text-white">450+</div>
                        <div class="text-xs text-slate-400">Verified Stalls</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold font-heading text-white">28,000+</div>
                        <div class="text-xs text-slate-400">Unique Products</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold font-heading text-white">99.4%</div>
                        <div class="text-xs text-slate-400">Fulfillment Rate</div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 flex items-center justify-between text-xs text-slate-400 border-t border-white/10 pt-4">
                <span>&copy; {{ date('Y') }} Bazaario Multi-Vendor Marketplace</span>
                <div class="flex gap-4">
                    <a href="{{ route('home') }}" class="hover:text-white transition">Back to Store</a>
                    <a href="{{ route('seller.register') }}" class="hover:text-white transition">Become a Seller</a>
                </div>
            </div>
        </div>

        <!-- Right Side Form Column -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-white">
            <div class="mx-auto w-full max-w-md">
                @yield('content')
            </div>
        </div>

    </div>

</body>
</html>
