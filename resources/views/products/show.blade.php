@extends('layouts.app')

@section('title', 'Full-Grain Leather Messenger Briefcase — Nordic Leather Atelier | Bazaario')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    activeImg: 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1200&q=80',
    quantity: 1,
    activePolicy: 'shipping',
    addedToCart: false,
    toastMsg: ''
}">

    <!-- Toast Notification for Add-to-Cart -->
    <div x-show="addedToCart" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center font-bold">
            <i class="fa-solid fa-check"></i>
        </div>
        <div>
            <p class="text-xs font-bold" x-text="toastMsg"></p>
            <p class="text-[10px] text-slate-400">Added to your unified cart</p>
        </div>
        <a href="{{ route('cart.index') }}" class="ml-2 text-xs font-bold text-amber-400 hover:underline">View Cart &rarr;</a>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('category.show', 'artisan-crafts') }}" class="hover:text-indigo-600">Artisan & Crafts</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('stall.show', 'nordic-leather') }}" class="hover:text-indigo-600">Nordic Leather Atelier</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="text-slate-900 font-semibold truncate max-w-xs">Full-Grain Leather Messenger Briefcase</span>
    </nav>

    <!-- Product Main Stage (Gallery + Floating Summary Card) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Left: Gallery & Content (Span 7) -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Hero Image Frame -->
            <div class="space-y-4">
                <div class="relative h-[440px] sm:h-[500px] rounded-3xl overflow-hidden bg-slate-100 border border-slate-200 shadow-sm group">
                    <img :src="activeImg" alt="Product Image" class="w-full h-full object-cover transition-all duration-300">
                    <span class="absolute top-4 left-4 bg-amber-500 text-slate-950 text-xs font-black uppercase px-3 py-1 rounded-full shadow-md">
                        Handmade Artisan Piece
                    </span>
                    <button class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/90 backdrop-blur hover:bg-white text-slate-600 hover:text-rose-500 shadow-md flex items-center justify-center transition">
                        <i class="fa-regular fa-heart text-base"></i>
                    </button>
                </div>

                <!-- Thumbnail Dots / Gallery Selector -->
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    <button @click="activeImg = 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1200&q=80'" :class="activeImg.includes('photo-1548036328') ? 'ring-2 ring-indigo-600' : 'opacity-70 hover:opacity-100'" class="w-20 h-20 rounded-2xl overflow-hidden border border-slate-200 shrink-0 transition">
                        <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=200&q=80" class="w-full h-full object-cover">
                    </button>
                    <button @click="activeImg = 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=1200&q=80'" :class="activeImg.includes('photo-1553062407') ? 'ring-2 ring-indigo-600' : 'opacity-70 hover:opacity-100'" class="w-20 h-20 rounded-2xl overflow-hidden border border-slate-200 shrink-0 transition">
                        <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=200&q=80" class="w-full h-full object-cover">
                    </button>
                    <button @click="activeImg = 'https://images.unsplash.com/photo-1473188557897-f95e0524982f?auto=format&fit=crop&w=1200&q=80'" :class="activeImg.includes('photo-1473188557') ? 'ring-2 ring-indigo-600' : 'opacity-70 hover:opacity-100'" class="w-20 h-20 rounded-2xl overflow-hidden border border-slate-200 shrink-0 transition">
                        <img src="https://images.unsplash.com/photo-1473188557897-f95e0524982f?auto=format&fit=crop&w=200&q=80" class="w-full h-full object-cover">
                    </button>
                </div>
            </div>

            <!-- Description Section -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 space-y-6">
                <h3 class="text-xl font-bold font-heading text-slate-900 border-b border-slate-100 pb-3">Product Description & Craftsmanship</h3>
                <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 space-y-4">
                    <p>
                        Engineered from premium vegetable-tanned Scandinavian bovine leather, this handcrafted messenger briefcase will develop a deep, distinctive patina with each year of use. Every stitch is reinforced with waxed braided poly-thread to withstand daily urban commutes or international travels.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <span class="font-bold text-slate-900 flex items-center gap-1.5"><i class="fa-solid fa-laptop text-indigo-600"></i> Dedicated Laptop Sleeve</span>
                            <p class="text-slate-500">Padded compartment accommodates up to 16" MacBook Pro</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <span class="font-bold text-slate-900 flex items-center gap-1.5"><i class="fa-solid fa-feather-pointed text-amber-600"></i> Solid Brass Hardware</span>
                            <p class="text-slate-500">Antique brass buckle closures & YKK heavy-duty zippers</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <span class="font-bold text-slate-900 flex items-center gap-1.5"><i class="fa-solid fa-ruler-combined text-emerald-600"></i> Dimensions</span>
                            <p class="text-slate-500">15.7" W x 11.8" H x 4.3" D (Weight: 1.4 kg)</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <span class="font-bold text-slate-900 flex items-center gap-1.5"><i class="fa-solid fa-earth-americas text-purple-600"></i> Origin</span>
                            <p class="text-slate-500">Independently crafted in Stockholm Atelier</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seller Policies Accordion -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 space-y-4">
                <h3 class="text-lg font-bold font-heading text-slate-900">Stall Guarantees & Policies</h3>
                
                <div class="space-y-2 text-xs">
                    <!-- Policy 1 -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden">
                        <button @click="activePolicy = activePolicy === 'shipping' ? '' : 'shipping'" class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 font-bold text-slate-800 text-left">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-truck-fast text-indigo-600"></i> Direct Stall Dispatch & Shipping</span>
                            <i class="fa-solid" :class="activePolicy === 'shipping' ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                        <div x-show="activePolicy === 'shipping'" class="p-4 bg-white text-slate-600 leading-relaxed border-t border-slate-200">
                            Orders are dispatched directly from Nordic Leather Atelier within 24–48 hours with door-to-door tracking. Because Bazaario connects independent stalls, items purchased from other stalls will ship in separate direct parcels.
                        </div>
                    </div>

                    <!-- Policy 2 -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden">
                        <button @click="activePolicy = activePolicy === 'returns' ? '' : 'returns'" class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 font-bold text-slate-800 text-left">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-rotate-left text-amber-600"></i> 14-Day Buyer Protection & Returns</span>
                            <i class="fa-solid" :class="activePolicy === 'returns' ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                        <div x-show="activePolicy === 'returns'" class="p-4 bg-white text-slate-600 leading-relaxed border-t border-slate-200">
                            If your handcrafted item arrives damaged or fails to match the stall description, Bazaario platform guarantees 100% refund protection or hassle-free replacement.
                        </div>
                    </div>

                    <!-- Policy 3 -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden">
                        <button @click="activePolicy = activePolicy === 'warranty' ? '' : 'warranty'" class="w-full flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 font-bold text-slate-800 text-left">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-shield-halved text-emerald-600"></i> Lifetime Artisan Stitching Guarantee</span>
                            <i class="fa-solid" :class="activePolicy === 'warranty' ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                        <div x-show="activePolicy === 'warranty'" class="p-4 bg-white text-slate-600 leading-relaxed border-t border-slate-200">
                            This stall guarantees all structural leather seams and hardware for the lifetime of the product against manufacturing defects.
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Floating Summary Card (Span 5) -->
        <div class="lg:col-span-5">
            <div class="sticky top-28 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
                
                <!-- Stall Identity Bar with Trust Score -->
                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Stall owner" class="w-12 h-12 rounded-xl object-cover ring-2 ring-amber-400">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h4 class="font-bold text-sm text-slate-900">Nordic Leather Atelier</h4>
                                <i class="fa-solid fa-circle-check text-indigo-600 text-xs" title="Verified Stall"></i>
                            </div>
                            <!-- Trust Score Badge -->
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">
                                    <i class="fa-solid fa-shield-check"></i> 99.2% Trust Score
                                </span>
                                <span class="text-[11px] text-slate-500">1.4k sales</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('stall.show', 'nordic-leather') }}" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-900 hover:text-white border border-amber-300 text-xs font-bold text-slate-800 transition shadow-sm shrink-0">
                        Visit Stall
                    </a>
                </div>

                <!-- Product Title & Rating -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900 leading-snug">
                        Full-Grain Cognac Leather Messenger Briefcase
                    </h1>
                    <div class="flex items-center gap-3 mt-2 text-xs text-slate-600">
                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span class="text-slate-900 ml-1">4.96</span>
                        </div>
                        <span>•</span>
                        <span class="text-slate-500">128 verified customer reviews</span>
                    </div>
                </div>

                <!-- Price & Stock Status -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black font-heading text-slate-900">$149.00</span>
                            <span class="text-sm text-slate-400 line-through">$189.00</span>
                            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md">Save $40</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">Taxes included • Free nationwide dispatch</p>
                    </div>

                    <div class="text-right">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Only 3 left in stock
                        </span>
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Select Quantity:</label>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center border border-slate-200 rounded-2xl bg-white overflow-hidden shadow-sm">
                            <button @click="if (quantity > 1) quantity--" class="px-3.5 py-2.5 text-slate-600 hover:bg-slate-100 transition">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <span class="px-4 py-2 text-sm font-bold text-slate-900 font-heading" x-text="quantity"></span>
                            <button @click="if (quantity < 3) quantity++" class="px-3.5 py-2.5 text-slate-600 hover:bg-slate-100 transition">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                        <span class="text-xs text-slate-400">Max limit per customer: 3</span>
                    </div>
                </div>

                <!-- CTA Actions -->
                <div class="space-y-3 pt-2">
                    <button @click="addedToCart = true; toastMsg = 'Full-Grain Leather Briefcase (' + quantity + 'x) added!'; setTimeout(() => addedToCart = false, 4000)" class="w-full py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/25 flex items-center justify-center gap-2 hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fa-solid fa-cart-plus"></i> Add to Unified Cart
                    </button>
                    
                    <a href="{{ route('checkout.index') }}" class="w-full py-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm flex items-center justify-center gap-2 transition">
                        <i class="fa-solid fa-bolt text-amber-400"></i> Buy Now with 1-Click
                    </a>
                </div>

                <!-- Micro Highlights -->
                <div class="space-y-2 pt-2 text-xs text-slate-600 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave text-emerald-600 w-4"></i>
                        <span>Cash on Delivery (COD) supported for this stall</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-box-open text-indigo-600 w-4"></i>
                        <span>Dispatches in 24–48 hours in eco-padded packaging</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-shield-heart text-amber-600 w-4"></i>
                        <span>Protected by Bazaario Verified Merchant guarantee</span>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Reviews Section (Horizontal Scroll Cards) -->
    <section class="mt-16 pt-12 border-t border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
            <div>
                <h3 class="text-2xl font-bold font-heading text-slate-900">Buyer Reviews & Verified Feedback</h3>
                <div class="flex items-center gap-2 mt-1">
                    <div class="flex text-amber-400 text-sm">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-800">4.9 out of 5</span>
                    <span class="text-xs text-slate-400">(128 reviews)</span>
                </div>
            </div>
            <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-sm transition">
                <i class="fa-solid fa-pen-to-square text-indigo-600"></i> Write a Stall Review
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80&q=80" class="w-8 h-8 rounded-full object-cover">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">Elena Rostova</span>
                                <span class="text-[10px] text-slate-400">Verified Buyer • 4 days ago</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full"><i class="fa-solid fa-check"></i> Verified</span>
                    </div>
                    <div class="flex text-amber-400 text-xs mb-2">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        "The quality of the full-grain leather is breathtaking. The smell upon unboxing was amazing and the brass buckles are robust. Fits my 15-inch laptop with room to spare!"
                    </p>
                </div>
            </div>

            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=80&q=80" class="w-8 h-8 rounded-full object-cover">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">David Chen</span>
                                <span class="text-[10px] text-slate-400">Verified Buyer • 1 week ago</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full"><i class="fa-solid fa-check"></i> Verified</span>
                    </div>
                    <div class="flex text-amber-400 text-xs mb-2">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        "Shipped directly by the maker in Sweden and arrived in California in 3 days. Packed with a handwritten note. Bazaario's multi-vendor checkout is smooth."
                    </p>
                </div>
            </div>

            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80" class="w-8 h-8 rounded-full object-cover">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">Maya Lindqvist</span>
                                <span class="text-[10px] text-slate-400">Verified Buyer • 2 weeks ago</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full"><i class="fa-solid fa-check"></i> Verified</span>
                    </div>
                    <div class="flex text-amber-400 text-xs mb-2">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        "Great craftsmanship! The strap is comfortable even when carrying heavy gear. Love supporting independent workshops directly."
                    </p>
                </div>
            </div>

        </div>
    </section>

    <!-- "More From This Stall" Horizontal Carousel -->
    <section class="mt-16 pt-12 border-t border-slate-200">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold font-heading text-slate-900">More from Nordic Leather Atelier</h3>
                <p class="text-xs text-slate-500 mt-1">Discover other handcrafted goods directly from this stall</p>
            </div>
            <a href="{{ route('stall.show', 'nordic-leather') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                View All Stall Items (28) <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <a href="{{ route('products.show', 'leather-minimalist-wallet') }}" class="p-4 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition flex items-center gap-4 group">
                <img src="https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&w=200&q=80" class="w-20 h-20 rounded-2xl object-cover">
                <div>
                    <h4 class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 transition">Slim Bifold Card Wallet</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Vegetable Tanned Leather</p>
                    <span class="text-sm font-extrabold text-slate-900 mt-1 block font-heading">$45.00</span>
                </div>
            </a>

            <a href="{{ route('products.show', 'leather-desk-mat') }}" class="p-4 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition flex items-center gap-4 group">
                <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=200&q=80" class="w-20 h-20 rounded-2xl object-cover">
                <div>
                    <h4 class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 transition">Executive Leather Desk Pad</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Saddle Brown Stitched</p>
                    <span class="text-sm font-extrabold text-slate-900 mt-1 block font-heading">$79.00</span>
                </div>
            </a>

            <a href="{{ route('products.show', 'leather-keychain-fob') }}" class="p-4 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition flex items-center gap-4 group">
                <img src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=200&q=80" class="w-20 h-20 rounded-2xl object-cover">
                <div>
                    <h4 class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 transition">Solid Brass Leather Key Hook</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Hand-burnished Edge</p>
                    <span class="text-sm font-extrabold text-slate-900 mt-1 block font-heading">$24.00</span>
                </div>
            </a>

        </div>
    </section>

</div>
@endsection
