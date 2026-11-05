@extends('layouts.user')

@section('title', 'AI Product Comparison — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="relative z-10 max-w-xl">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[17px]">compare_arrows</span> Side-by-Side Intelligence
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">AI Product Comparison</h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">
                Evaluate specifications, price-to-value ratios, farm freshness, and autonomous pros & cons recommendations.
            </p>
        </div>
        <div class="relative z-10 shrink-0 flex items-center gap-3">
            @if(isset($comparison['source']))
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $comparison['source'] === 'gemini' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                    <span class="material-symbols-outlined text-[15px] {{ $comparison['source'] === 'gemini' ? 'text-emerald-600' : 'text-amber-500' }}">auto_awesome</span>
                    <span>{{ $comparison['source'] === 'gemini' ? 'Gemini 1.5 Live AI' : 'Deterministic AI Fallback' }}</span>
                </span>
            @endif
            <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold hover:bg-slate-authority/90 transition-all flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">storefront</span> Browse Catalog
            </a>
        </div>
    </div>

    @php
        $comparedProducts = $comparison['products'] ?? [];
        $hasComparison = count($comparedProducts) >= 2;
        $currentIds = collect($comparedProducts)->pluck('id')->all();
    @endphp

    @if($hasComparison)
        {{-- AI Verdict & Recommendation Banner --}}
        <div class="rounded-2xl bg-card-white border border-slate-authority/10 p-6 shadow-xs flex flex-col gap-4">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">verified</span>
                </span>
                <div>
                    <h2 class="font-title-card text-base font-bold text-slate-authority">AI Verdict & Buying Advice</h2>
                    <p class="text-xs text-on-surface-variant">Synthesized from customer ratings, price economics, and verified producer attributes.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                @if(!empty($comparison['verdict']))
                    <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-100">
                        <span class="font-label-eyebrow text-[11px] uppercase tracking-wider text-emerald-800 font-bold block mb-1">
                            ✦ Top Recommendation
                        </span>
                        <p class="text-xs text-emerald-950 font-medium leading-relaxed">{{ $comparison['verdict'] }}</p>
                    </div>
                @endif

                @if(!empty($comparison['recommendation']))
                    <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-100">
                        <span class="font-label-eyebrow text-[11px] uppercase tracking-wider text-amber-800 font-bold block mb-1">
                            ✦ Persona Use-Case
                        </span>
                        <p class="text-xs text-amber-950 font-medium leading-relaxed">{{ $comparison['recommendation'] }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Side-by-Side Product Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-{{ min(4, max(2, count($comparedProducts))) }} gap-5">
            @foreach($comparedProducts as $prod)
                @php
                    $prodId = (string) $prod['id'];
                    $otherIds = array_diff($currentIds, [$prod['id']]);
                    $removeUrl = route('user.compare', ['products' => implode(',', $otherIds)]);
                @endphp
                <div class="rounded-2xl bg-card-white border border-slate-authority/10 shadow-xs overflow-hidden flex flex-col group relative">
                    <a href="{{ $removeUrl }}" class="absolute top-2.5 right-2.5 z-10 w-7 h-7 rounded-full bg-slate-900/60 hover:bg-rose-600 text-white flex items-center justify-center transition-colors" title="Remove from comparison">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </a>

                    <div class="h-44 w-full bg-surface-container overflow-hidden relative">
                        <img src="{{ $prod['image_url'] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600' }}" alt="{{ $prod['name'] }}" class="w-full h-full object-cover">
                        <span class="absolute bottom-2.5 left-2.5 px-2.5 py-0.5 rounded-full bg-slate-authority/80 backdrop-blur text-canvas-ivory font-label-micro text-label-micro font-semibold">
                            {{ $prod['category'] ?? 'General' }}
                        </span>
                    </div>

                    <div class="p-4 flex flex-col flex-1 justify-between gap-4">
                        <div>
                            <div class="font-label-micro text-label-micro text-on-surface-variant flex items-center justify-between">
                                <span>{{ $prod['seller_name'] ?? 'Verified Merchant' }}</span>
                                <span class="text-amber-action flex items-center gap-0.5 font-bold">
                                    <span class="material-symbols-outlined text-[13px]">star</span> {{ number_format($prod['average_rating'] ?? 4.8, 1) }}
                                </span>
                            </div>
                            <h3 class="font-title-card text-sm font-bold text-slate-authority mt-1 line-clamp-2">
                                {{ $prod['name'] }}
                            </h3>
                        </div>

                        <div class="pt-3 border-t border-slate-authority/5 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-slate-400 block uppercase font-semibold">Unit Price</span>
                                <span class="font-mono font-bold text-base text-slate-authority">₹{{ number_format($prod['price'], 2) }}</span>
                                <span class="text-[11px] text-slate-500">/ {{ $prod['unit_type'] ?? 'item' }}</span>
                            </div>
                            <a href="{{ route('products.show', $prod['slug'] ?? $prod['id']) }}" class="px-3.5 py-2 rounded-xl bg-slate-authority hover:bg-amber-action hover:text-slate-authority text-canvas-ivory text-xs font-semibold transition-all">
                                View Item
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Side-by-side Attribute Matrix Table --}}
        <div class="rounded-2xl bg-card-white border border-slate-authority/10 shadow-xs overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-authority/10 flex items-center justify-between">
                <h3 class="font-title-card text-sm font-bold text-slate-authority flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-action text-[18px]">table_chart</span>
                    Technical & Commercial Specification Matrix
                </h3>
                <span class="text-xs text-on-surface-variant font-mono">{{ count($comparedProducts) }} products compared</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-white">
                            <th class="p-4 text-slate-400 uppercase font-semibold text-[11px] w-1/4">Specification</th>
                            @foreach($comparedProducts as $prod)
                                <th class="p-4 font-bold text-slate-authority">{{ $prod['name'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-4 font-semibold text-slate-600 bg-slate-50/50">Price</td>
                            @foreach($comparedProducts as $prod)
                                <td class="p-4 font-mono font-bold text-slate-900">₹{{ number_format($prod['price'], 2) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-600 bg-slate-50/50">Unit Packaging</td>
                            @foreach($comparedProducts as $prod)
                                <td class="p-4 capitalize text-slate-800">{{ $prod['unit_type'] ?? 'Standard Item' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-600 bg-slate-50/50">Inventory Level</td>
                            @foreach($comparedProducts as $prod)
                                <td class="p-4">
                                    @if(($prod['stock'] ?? 0) > 10)
                                        <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> In Stock ({{ $prod['stock'] }})
                                        </span>
                                    @elseif(($prod['stock'] ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1 text-amber-700 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Low Stock ({{ $prod['stock'] }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-rose-700 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Out of Stock
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-600 bg-slate-50/50">Customer Rating</td>
                            @foreach($comparedProducts as $prod)
                                <td class="p-4 font-semibold text-slate-800 flex items-center gap-1">
                                    <span class="text-amber-500 material-symbols-outlined text-[15px]">star</span>
                                    {{ number_format($prod['average_rating'] ?? 4.8, 1) }}★ ({{ $prod['total_reviews'] ?? 0 }} reviews)
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-600 bg-slate-50/50">Category Taxonomy</td>
                            @foreach($comparedProducts as $prod)
                                <td class="p-4 text-slate-800">{{ $prod['category'] ?? 'Marketplace' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-600 bg-slate-50/50">Verified Seller</td>
                            @foreach($comparedProducts as $prod)
                                <td class="p-4 text-slate-800 font-medium">{{ $prod['seller_name'] ?? 'Verified Store' }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pros & Cons Breakdown --}}
        @if(!empty($comparison['pros_cons']))
            <div class="grid grid-cols-1 md:grid-cols-{{ min(4, max(2, count($comparedProducts))) }} gap-5">
                @foreach($comparedProducts as $prod)
                    @php
                        $pc = $comparison['pros_cons'][(string) $prod['id']] ?? $comparison['pros_cons'][$prod['id']] ?? null;
                    @endphp
                    <div class="rounded-2xl bg-card-white border border-slate-authority/10 p-5 shadow-xs flex flex-col gap-4">
                        <div class="border-b border-slate-100 pb-2">
                            <h4 class="font-bold text-xs text-slate-authority truncate">{{ $prod['name'] }}</h4>
                            <span class="text-[10px] text-slate-400">Pros & Cons Evaluation</span>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div>
                                <span class="font-label-eyebrow text-[10px] uppercase font-bold text-emerald-800 flex items-center gap-1 mb-2">
                                    <span class="material-symbols-outlined text-[14px] text-emerald-600">thumb_up</span> Strengths
                                </span>
                                <ul class="space-y-1.5">
                                    @forelse($pc['pros'] ?? ['Competitive value', 'Fast shipping'] as $pro)
                                        <li class="flex items-start gap-1.5 text-xs text-slate-700">
                                            <span class="material-symbols-outlined text-emerald-500 text-[14px] shrink-0 mt-0.5">check_circle</span>
                                            <span>{{ $pro }}</span>
                                        </li>
                                    @empty
                                        <li class="text-xs text-slate-400">No specific strengths recorded.</li>
                                    @endforelse
                                </ul>
                            </div>

                            <div class="pt-2 border-t border-slate-100">
                                <span class="font-label-eyebrow text-[10px] uppercase font-bold text-rose-800 flex items-center gap-1 mb-2">
                                    <span class="material-symbols-outlined text-[14px] text-rose-600">thumb_down</span> Trade-offs
                                </span>
                                <ul class="space-y-1.5">
                                    @forelse($pc['cons'] ?? ['Limited stock availability'] as $con)
                                        <li class="flex items-start gap-1.5 text-xs text-slate-700">
                                            <span class="material-symbols-outlined text-amber-500 text-[14px] shrink-0 mt-0.5">info</span>
                                            <span>{{ $con }}</span>
                                        </li>
                                    @empty
                                        <li class="text-xs text-slate-400">Standard market trade-offs apply.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    @else
        {{-- Empty or Insufficient Products State with Interactive Picker --}}
        <div class="flex flex-col items-center justify-center py-12 px-6 gap-4 text-center bg-card-white rounded-2xl border border-slate-authority/10 shadow-xs">
            <div class="w-16 h-16 rounded-full bg-amber-500/15 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[36px]">compare_arrows</span>
            </div>
            <div>
                <h2 class="font-title-card text-lg font-bold text-slate-authority">Choose products to compare</h2>
                <p class="font-body-small text-body-small text-on-surface-variant max-w-md mt-1">
                    Select 2 or more products from the catalog below to activate the side-by-side AI matrix, pros & cons pills, and persona verdicts.
                </p>
            </div>
        </div>
    @endif

    {{-- Interactive Product Picker Shelf --}}
    @if(isset($selectableProducts) && $selectableProducts->isNotEmpty())
        <div class="rounded-2xl bg-card-white border border-slate-authority/10 p-6 shadow-xs flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-title-card text-sm font-bold text-slate-authority flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-action text-[18px]">add_circle</span>
                        {{ $hasComparison ? 'Add More Products to Comparison' : 'Select Products to Start Comparing' }}
                    </h3>
                    <p class="text-xs text-on-surface-variant">Click to add listings into the comparison matrix.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                @foreach($selectableProducts as $sp)
                    @php
                        $newIds = array_unique(array_merge($currentIds, [$sp->id]));
                        $addUrl = route('user.compare', ['products' => implode(',', $newIds)]);
                        $imgPath = $sp->primaryImage?->image_path;
                        $imgUrl = $imgPath ? asset($imgPath) : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600';
                    @endphp
                    <div class="p-3 rounded-xl border border-slate-200 hover:border-amber-400 bg-slate-50/50 flex items-center justify-between gap-3 transition">
                        <img src="{{ $imgUrl }}" alt="{{ $sp->name }}" class="w-12 h-12 rounded-lg object-cover bg-white shrink-0">
                        <div class="flex-1 min-w-0">
                            <h5 class="text-xs font-bold text-slate-800 truncate">{{ $sp->name }}</h5>
                            <span class="font-mono text-xs font-semibold text-slate-600 block">₹{{ number_format($sp->price, 2) }}</span>
                        </div>
                        <a href="{{ $addUrl }}" class="px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-[11px] shrink-0 transition flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">add</span> Add
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection