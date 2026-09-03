@extends('layouts.app')

@section('title', 'Nordic Leather Atelier — Verified Stall Storefront | Bazaario')

@section('content')
<div class="space-y-12 pb-16" x-data="{
    following: false,
    stallTab: 'products',
    stallCategory: 'all',
    searchStall: ''
}">

    <!-- 1. Seller Hero Banner & Storefront Header -->
    <div class="relative bg-slate-900 text-white">
        <!-- Banner Background -->
        <div class="h-64 sm:h-80 w-full relative overflow-hidden">
            <img src="https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=1600&q=80" alt="Stall Cover" class="w-full h-full object-cover opacity-60">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
        </div>

        <!-- Storefront Identity Overlay -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-10 pb-8">
            <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
                
                <div class="flex flex-col sm:flex-row items-start sm:items-end gap-5">
                    <!-- Shop Logo -->
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl bg-white p-1.5 shadow-2xl border-4 border-white/90 overflow-hidden shrink-0">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80" alt="Stall Logo" class="w-full h-full object-cover rounded-2xl">
                    </div>

                    <!-- Shop Info -->
                    <div class="space-y-1 text-slate-100">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">Nordic Leather Atelier</h1>
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-300 bg-amber-500/20 border border-amber-500/30 px-2.5 py-0.5 rounded-full">
                                <i class="fa-solid fa-circle-check"></i> Verified Stall
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-xl">
                            Handcrafting bespoke vegetable-tanned leather goods, messenger bags, and minimal accessories in Stockholm since 2018.
                        </p>
                        
                        <!-- Trust Metrics Bar -->
                        <div class="flex flex-wrap items-center gap-4 text-xs pt-1 text-slate-300">
                            <span class="flex items-center gap-1.5 font-bold text-emerald-400">
                                <i class="fa-solid fa-shield-check text-emerald-400"></i> 99.2% Trust Score
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1 text-amber-400 font-bold">
                                <i class="fa-solid fa-star"></i> 4.9 (128 reviews)
                            </span>
                            <span>•</span>
                            <span><i class="fa-solid fa-location-dot text-slate-400"></i> Stockholm, Sweden</span>
                            <span>•</span>
                            <span><i class="fa-solid fa-clock text-slate-400"></i> Replies in &lt; 2 hours</span>
                        </div>
                    </div>
                </div>

                <!-- Stall Actions (Follow / Message) -->
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button @click="following = !following" :class="following ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-900 border-white hover:bg-slate-100'" class="flex-1 sm:flex-initial px-6 py-3 rounded-2xl font-bold text-xs shadow-lg transition flex items-center justify-center gap-2">
                        <i class="fa-solid" :class="following ? 'fa-check' : 'fa-plus'"></i>
                        <span x-text="following ? 'Following Stall (1.2k)' : 'Follow Stall (1.2k)'"></span>
                    </button>
                    <button class="px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white backdrop-blur border border-white/20 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-regular fa-paper-plane"></i> Contact
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- 2. Storefront Navigation Tabs -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex border-b border-slate-200">
            <button @click="stallTab = 'products'" :class="stallTab === 'products' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3.5 px-6 border-b-2 text-sm transition flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked"></i> All Products (28)
            </button>
            <button @click="stallTab = 'reviews'" :class="stallTab === 'reviews' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3.5 px-6 border-b-2 text-sm transition flex items-center gap-2">
                <i class="fa-solid fa-star"></i> Stall Reviews (128)
            </button>
            <button @click="stallTab = 'about'" :class="stallTab === 'about' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3.5 px-6 border-b-2 text-sm transition flex items-center gap-2">
                <i class="fa-solid fa-circle-info"></i> Atelier Story & Policies
            </button>
        </div>
    </div>

    <!-- 3. Tab Content: Products Grid -->
    <div x-show="stallTab === 'products'" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Filter Bar inside Stall -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-white border border-slate-200">
            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
                <button @click="stallCategory = 'all'" :class="stallCategory === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-full text-xs font-bold transition">All Items (28)</button>
                <button @click="stallCategory = 'bags'" :class="stallCategory === 'bags' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-full text-xs font-bold transition">Bags & Briefcases (12)</button>
                <button @click="stallCategory = 'wallets'" :class="stallCategory === 'wallets' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-full text-xs font-bold transition">Wallets & Cardholders (9)</button>
                <button @click="stallCategory = 'accessories'" :class="stallCategory === 'accessories' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-full text-xs font-bold transition">Desk Accessories (7)</button>
            </div>

            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3 top-3 text-xs"></i>
                <input type="text" x-model="searchStall" placeholder="Search this stall..." class="w-full py-2 pl-8 pr-3 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-500">
            </div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Item 1 -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition group">
                <div class="relative h-60 overflow-hidden bg-slate-100">
                    <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=600&q=80" alt="Product" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <span class="absolute top-3 left-3 bg-amber-500 text-slate-950 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full">Bestseller</span>
                </div>
                <div class="p-5">
                    <a href="{{ route('products.show', 'handcrafted-leather-messenger-bag') }}" class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 line-clamp-1 block transition">
                        Full-Grain Leather Messenger Briefcase
                    </a>
                    <div class="flex items-center gap-1 text-amber-500 text-xs mt-1">
                        <i class="fa-solid fa-star text-[10px]"></i> 4.9 <span class="text-slate-400 text-[10px]">(128)</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-base font-extrabold font-heading text-slate-900">$149.00</span>
                        <a href="{{ route('cart.index') }}" class="p-2 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white transition">
                            <i class="fa-solid fa-cart-plus text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition group">
                <div class="relative h-60 overflow-hidden bg-slate-100">
                    <img src="https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&w=600&q=80" alt="Product" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <a href="{{ route('products.show', 'leather-minimalist-wallet') }}" class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 line-clamp-1 block transition">
                        Slim Bifold Vegetable-Tanned Card Wallet
                    </a>
                    <div class="flex items-center gap-1 text-amber-500 text-xs mt-1">
                        <i class="fa-solid fa-star text-[10px]"></i> 5.0 <span class="text-slate-400 text-[10px]">(48)</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-base font-extrabold font-heading text-slate-900">$45.00</span>
                        <a href="{{ route('cart.index') }}" class="p-2 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white transition">
                            <i class="fa-solid fa-cart-plus text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 3 -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition group">
                <div class="relative h-60 overflow-hidden bg-slate-100">
                    <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80" alt="Product" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <a href="{{ route('products.show', 'leather-desk-mat') }}" class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 line-clamp-1 block transition">
                        Executive Leather Desk Pad & Writing Blotter
                    </a>
                    <div class="flex items-center gap-1 text-amber-500 text-xs mt-1">
                        <i class="fa-solid fa-star text-[10px]"></i> 4.8 <span class="text-slate-400 text-[10px]">(32)</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-base font-extrabold font-heading text-slate-900">$79.00</span>
                        <a href="{{ route('cart.index') }}" class="p-2 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white transition">
                            <i class="fa-solid fa-cart-plus text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition group">
                <div class="relative h-60 overflow-hidden bg-slate-100">
                    <img src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=600&q=80" alt="Product" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <a href="{{ route('products.show', 'leather-keychain-fob') }}" class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 line-clamp-1 block transition">
                        Solid Brass Heavy-Duty Leather Key Hook
                    </a>
                    <div class="flex items-center gap-1 text-amber-500 text-xs mt-1">
                        <i class="fa-solid fa-star text-[10px]"></i> 4.9 <span class="text-slate-400 text-[10px]">(86)</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-base font-extrabold font-heading text-slate-900">$24.00</span>
                        <a href="{{ route('cart.index') }}" class="p-2 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white transition">
                            <i class="fa-solid fa-cart-plus text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 4. Tab Content: Reviews -->
    <div x-show="stallTab === 'reviews'" x-cloak class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl p-8 border border-slate-200 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold font-heading text-slate-900">All Reviews for Nordic Leather Atelier</h3>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">99.2% Positive Seller Feedback</span>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-900">Marcus Wright</span>
                        <span class="text-slate-400">3 days ago</span>
                    </div>
                    <div class="flex text-amber-400 text-xs"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p class="text-xs text-slate-600">"Seller responded immediately when I requested custom embossing. Stellar packaging and fast courier delivery."</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-900">Sophie Dupont</span>
                        <span class="text-slate-400">2 weeks ago</span>
                    </div>
                    <div class="flex text-amber-400 text-xs"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p class="text-xs text-slate-600">"Bought two wallets as anniversary gifts. The leather grain and burnishing is of luxury standard."</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Tab Content: About -->
    <div x-show="stallTab === 'about'" x-cloak class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl p-8 border border-slate-200 space-y-6 text-sm text-slate-600 leading-relaxed">
            <h3 class="text-xl font-bold font-heading text-slate-900">About the Maker & Workshop</h3>
            <p>
                Founded by leathercraftsman Erik Lindgren, Nordic Leather Atelier preserves traditional Scandinavian hand-stitching techniques. All hides are sourced from certified ethical tanneries in Sweden and Italy, using 100% plant-based bark tannins.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-xs">
                <div class="p-4 rounded-2xl bg-slate-50">
                    <h4 class="font-bold text-slate-900 mb-1">Direct Dispatch</h4>
                    <p class="text-slate-500">Dispatched from Stockholm within 24-48 hours with DHL Express</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50">
                    <h4 class="font-bold text-slate-900 mb-1">Returns Accepted</h4>
                    <p class="text-slate-500">14-day hassle-free returns under Bazaario Buyer Protection</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50">
                    <h4 class="font-bold text-slate-900 mb-1">Lifetime Warranty</h4>
                    <p class="text-slate-500">Free hardware & stitching repairs for the lifetime of your item</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
