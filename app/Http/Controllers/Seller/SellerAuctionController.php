<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerAuctionController extends Controller
{
    /**
     * Display the seller's master auctions registry.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile) {
            abort(403, 'Seller profile required.');
        }

        $status = $request->query('status', 'all');

        $query = Auction::with(['product.primaryImage', 'product.images', 'product.category', 'bids'])
            ->where('seller_id', $sellerProfile->id);

        if (in_array($status, ['scheduled', 'live', 'ended', 'cancelled'])) {
            $query->where('status', $status);
        }

        $auctions = $query->latest('starts_at')->paginate(15)->withQueryString();

        $counts = [
            'all'       => Auction::where('seller_id', $sellerProfile->id)->count(),
            'scheduled' => Auction::where('seller_id', $sellerProfile->id)->where('status', 'scheduled')->count(),
            'live'      => Auction::where('seller_id', $sellerProfile->id)->where('status', 'live')->count(),
            'ended'     => Auction::where('seller_id', $sellerProfile->id)->where('status', 'ended')->count(),
            'cancelled' => Auction::where('seller_id', $sellerProfile->id)->where('status', 'cancelled')->count(),
        ];

        return view('seller.auctions.index', compact('seller', 'sellerProfile', 'auctions', 'status', 'counts'));
    }

    /**
     * Display the seller's completed and ended auctions history.
     */
    public function history(Request $request): View|RedirectResponse
    {
        $request->merge(['status' => 'ended']);
        return $this->index($request);
    }

    /**
     * Display the form to create a new wholesale auction listing.
     */
    public function create(): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile) {
            abort(403, 'Seller profile required.');
        }

        // Fetch products owned by the authenticated seller with images and category eager loaded
        $products = Product::with(['primaryImage', 'images', 'category'])
            ->where('seller_id', $seller->id)
            ->latest('id')
            ->get();

        // Ensure standard marketplace categories are registered so auctions support all sellers and categories
        $standardCategories = [
            ['name' => 'Artisan & Handmade Crafts', 'slug' => 'artisan-craft'],
            ['name' => 'Books & Fine Stationery', 'slug' => 'books-stationery'],
            ['name' => 'Electronics & Gadgets', 'slug' => 'electronics'],
            ['name' => 'Fashion & Apparel', 'slug' => 'fashion'],
            ['name' => 'Home & Living', 'slug' => 'home-living'],
            ['name' => 'Luxury Horology & Watches', 'slug' => 'luxury-horology'],
            ['name' => 'Rare Collectibles & Antiques', 'slug' => 'collectibles'],
            ['name' => 'Jewelry & Gemstones', 'slug' => 'jewelry-gemstones'],
            ['name' => 'Organic Produce & Food', 'slug' => 'organic-produce'],
            ['name' => 'Fresh Vegetables', 'slug' => 'fresh-vegetables'],
            ['name' => 'Fresh Fruits', 'slug' => 'fresh-fruits'],
            ['name' => 'Grains & Cereals', 'slug' => 'grains-cereals'],
            ['name' => 'Spices & Herbs', 'slug' => 'spices-herbs'],
            ['name' => 'Dairy & Farm Items', 'slug' => 'dairy-farm-items'],
        ];
        foreach ($standardCategories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], ['name' => $cat['name'], 'status' => 'active']);
        }

        // Fetch all active categories across the marketplace sorted alphabetically
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('seller.auctions.create', compact('seller', 'sellerProfile', 'products', 'categories'));
    }

    /**
     * Store a newly created auction in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile) {
            abort(403, 'Seller profile required.');
        }

        if (!$request->has('product_source')) {
            $productSource = $request->filled('manual_name') ? 'manual' : 'existing';
            $request->merge(['product_source' => $productSource]);
        } else {
            $productSource = $request->input('product_source', 'existing');
        }

        $rules = [
            'product_source'    => 'sometimes|in:existing,manual',
            'starting_price'    => 'required|numeric|gt:0',
            'reserve_price'     => 'nullable|numeric|gte:starting_price',
            'minimum_increment' => 'required|numeric|gt:0',
            'starts_at'         => 'required|date',
            'ends_at'           => 'required|date|after:starts_at',
        ];

        if ($productSource === 'manual') {
            $rules['manual_name']        = 'required|string|max:255';
            $rules['manual_category_id'] = 'nullable|exists:categories,id';
            $rules['manual_stock']       = 'required|numeric|min:1';
            $rules['manual_unit_type']   = 'nullable|string|max:50';
            $rules['manual_price']       = 'nullable|numeric|min:0';
            $rules['manual_description'] = 'nullable|string|max:3000';
            $rules['manual_image']       = 'nullable|image|max:10240';
            $rules['manual_farm_origin'] = 'nullable|string|max:255';
            $rules['manual_harvest_date']= 'nullable|date';
        } else {
            $rules['product_id'] = 'required|exists:products,id';
        }

        $request->validate($rules);

        if ($productSource === 'manual') {
            $categoryId = $request->input('manual_category_id');
            if (!$categoryId) {
                $firstCat = Category::where('status', 'active')->orderBy('name')->first()
                    ?? Category::firstOrCreate(
                        ['slug' => 'general-merchandise'],
                        ['name' => 'General Merchandise', 'status' => 'active']
                    );
                $categoryId = $firstCat->id;
            }

            $slug = Str::slug($request->manual_name) . '-' . Str::lower(Str::random(6));
            $sku = 'AUC-' . strtoupper(Str::random(8));

            $product = Product::create([
                'seller_id'           => $seller->id,
                'category_id'         => (int) $categoryId,
                'name'                => $request->manual_name,
                'slug'                => $slug,
                'sku'                 => $sku,
                'short_description'   => Str::limit($request->manual_description, 200),
                'description'         => $request->manual_description,
                'sale_type'           => 'auction',
                'unit_type'           => $request->input('manual_unit_type', 'kg'),
                'price'               => (float) ($request->manual_price ?: $request->starting_price),
                'stock'               => (int) $request->manual_stock,
                'status'              => 'active',
                'farm_origin'         => $request->input('manual_farm_origin'),
                'harvest_date'        => $request->input('manual_harvest_date'),
            ]);

            if ($request->hasFile('manual_image')) {
                $path = $request->file('manual_image')->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }
        } else {
            // Tenancy check: Ensure the selected product belongs to this seller
            $product = Product::where('id', $request->product_id)
                ->where('seller_id', $seller->id)
                ->first();

            if (!$product) {
                return back()->withErrors([
                    'product_id' => 'The selected product does not belong to your store.',
                ])->withInput();
            }
        }

        $startsAt = Carbon::parse($request->starts_at);
        $endsAt = Carbon::parse($request->ends_at);
        $status = (now()->gte($startsAt) && now()->lt($endsAt)) ? 'live' : 'scheduled';

        $auction = Auction::create([
            'seller_id'         => $sellerProfile->id,
            'product_id'        => $product->id,
            'starting_price'    => (float) $request->starting_price,
            'reserve_price'     => $request->filled('reserve_price') ? (float) $request->reserve_price : null,
            'current_price'     => (float) $request->starting_price,
            'minimum_increment' => (float) $request->minimum_increment,
            'starts_at'         => $startsAt,
            'ends_at'           => $endsAt,
            'status'            => $status,
        ]);

        try {
            \App\Models\AdminFeed::record(
                title: "Wholesale Auction Listed: Lot #AUC-{$auction->id}",
                description: "{$product->name} listed by {$sellerProfile->shop_name} (Starting: ₹" . number_format($auction->starting_price, 2) . ").",
                type: 'auction',
                icon: 'gavel',
                badgeText: 'New Auction',
                badgeColor: 'blue',
                actionUrl: route('admin.auctions.show', $auction->id)
            );
        } catch (\Throwable $feedEx) {}

        return redirect()->route('seller.auctions.index')
            ->with('success', "Auction lot #AUC-{$auction->id} for '{$product->name}' was successfully scheduled.");
    }

    /**
     * Display the specified auction lot details and bid ledger.
     */
    public function show(Auction $auction): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile || (int) $auction->seller_id !== (int) $sellerProfile->id) {
            abort(403, 'Unauthorized access to auction lot.');
        }

        $auction->load(['product.primaryImage', 'product.images', 'product.category', 'bids' => fn ($q) => $q->latest('id'), 'bids.user', 'winner']);

        return view('seller.auctions.show', compact('seller', 'sellerProfile', 'auction'));
    }

    /**
     * Display the live wholesale bidding terminal for the current active lot.
     */
    public function liveTerminal(Request $request): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile) {
            abort(403, 'Seller profile required.');
        }

        // Locate active live auction lot
        $auction = Auction::with(['product.primaryImage', 'product.images', 'product.category', 'bids' => fn ($q) => $q->latest('id'), 'bids.user'])
            ->where('seller_id', $sellerProfile->id)
            ->where(function ($q) {
                $q->where('status', 'live')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'scheduled')
                          ->where('starts_at', '<=', now())
                          ->where('ends_at', '>', now());
                  });
            })
            ->latest('starts_at')
            ->first();

        // Fallback to most recent auction if none currently live
        if (!$auction) {
            $auction = Auction::with(['product.primaryImage', 'product.images', 'product.category', 'bids' => fn ($q) => $q->latest('id'), 'bids.user'])
                ->where('seller_id', $sellerProfile->id)
                ->latest('id')
                ->first();
        }

        return view('seller.auctions.live', compact('seller', 'sellerProfile', 'auction'));
    }

    /**
     * Cancel an eligible auction lot if no binding bids have been placed.
     */
    public function cancel(Request $request, Auction $auction): RedirectResponse|JsonResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile || (int) $auction->seller_id !== (int) $sellerProfile->id) {
            abort(403, 'Unauthorized access to auction lot.');
        }

        if (in_array($auction->status, ['ended', 'cancelled'])) {
            $msg = "Policy Guardrail: Auction #AUC-{$auction->id} is already {$auction->status} and cannot be modified.";
            if ($request->expectsJson()) {
                return response()->json(['error' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $bidCount = $auction->bids()->count();
        if ($bidCount > 0) {
            $msg = 'Policy Guardrail: This auction cannot be cancelled because active binding bids have been placed under APMC trading rules.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $auction->update(['status' => 'cancelled']);

        return redirect()->route('seller.auctions.index')
            ->with('success', "Auction lot #AUC-{$auction->id} was successfully cancelled.");
    }
}
