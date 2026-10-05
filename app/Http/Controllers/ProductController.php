<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Feature 9 - 13: Homepage Discovery & Hyperlocal Browsing
     */
    public function home(Request $request)
    {
        $dbCategories = Cache::store('file')->remember('home_categories', 600, function () {
            return Category::where('status', 'active')->withCount('products')->get();
        });

        $trendingProducts = Cache::store('file')->remember('home_trending_products', 300, function () {
            return Product::with(['category', 'seller.sellerProfile', 'primaryImage', 'images'])
                ->where('status', 'active')
                ->latest()
                ->take(10)
                ->get();
        });

        $featuredAuction = Cache::store('file')->remember('home_featured_auction', 120, function () {
            return Auction::with(['product.primaryImage', 'product.images', 'bids'])
                ->withCount('bids')
                ->whereIn('status', ['live', 'active'])
                ->latest()
                ->first();
        });

        $reviews = Cache::store('file')->remember('home_reviews', 600, function () {
            return Review::with(['user', 'product'])
                ->latest()
                ->take(3)
                ->get();
        });

        // Feature 10: Featured Sellers (Verified merchants)
        $featuredSellers = Cache::store('file')->remember('home_featured_sellers', 300, function () {
            return User::where('role', 'seller')
                ->whereHas('sellerProfile', function ($q) {
                    $q->where('status', 'approved');
                })
                ->with(['sellerProfile'])
                ->withCount('products')
                ->latest()
                ->take(4)
                ->get();
        });

        // Feature 11 & Issue P33: Nearby Stalls (Hyperlocal discovery with graceful geolocation fallback)
        $hasCoordinates = $request->filled('lat') && $request->filled('lng');
        $lat = $hasCoordinates ? (float)$request->input('lat') : null;
        $lng = $hasCoordinates ? (float)$request->input('lng') : null;
        $radius = (float)$request->input('radius', 100); // Default 100 km radius

        $allSellers = User::where('role', 'seller')
            ->whereHas('sellerProfile', function ($q) {
                $q->where('status', 'approved');
            })
            ->with(['sellerProfile'])
            ->withCount('products')
            ->get();

        if ($hasCoordinates) {
            $nearbyStalls = $allSellers->map(function ($seller) use ($lat, $lng) {
                $profile = $seller->sellerProfile;
                $dist = $profile ? $profile->distanceTo($lat, $lng) : 0.0;
                $seller->distance_km = $dist;
                return $seller;
            });

            if ($radius > 0) {
                $nearbyStalls = $nearbyStalls->filter(function ($seller) use ($radius) {
                    return $seller->distance_km <= $radius;
                });
            }

            $nearbyStalls = $nearbyStalls->sortBy('distance_km')->values()->take(6);
        } else {
            // Graceful fallback: nationwide verified stalls when user has not shared coordinates
            $nearbyStalls = $allSellers->map(function ($seller) {
                $seller->distance_km = null;
                return $seller;
            })->take(6);
        }

        $stats = [
            'total_products'   => Product::where('status', 'active')->count(),
            'total_sellers'    => max(4, User::where('role', 'seller')->count()),
            'total_categories' => Category::where('status', 'active')->count(),
            'active_auctions'  => Auction::whereIn('status', ['live', 'active'])->count(),
        ];

        return view('index', compact(
            'dbCategories',
            'trendingProducts',
            'featuredAuction',
            'reviews',
            'stats',
            'featuredSellers',
            'nearbyStalls',
            'lat',
            'lng',
            'radius'
        ));
    }

    /**
     * Features 16 - 23: View All Products, Multi-filter Pipeline & Server Pagination
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'seller.sellerProfile', 'images', 'primaryImage'])
            ->where('status', 'active');

        // Feature 21 & 22: Keyword Search (fuzzy search across name, description, category, and seller)
        $rawSearch = $request->input('search', $request->input('q', ''));
        $search = trim(is_array($rawSearch) ? (string)($rawSearch[0] ?? '') : (string)$rawSearch);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('seller.sellerProfile', function ($sq) use ($search) {
                      $sq->where('shop_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Feature 17: Filter by Category (safely handle array inputs like category[]=textiles)
        $rawCategory = $request->input('category');
        $selectedCategory = is_array($rawCategory) ? ($rawCategory[0] ?? null) : $rawCategory;
        if (!empty($selectedCategory) && $selectedCategory !== 'all' && $selectedCategory !== 'All Categories') {
            $query->where(function ($q) use ($selectedCategory) {
                $q->whereHas('category', function ($cq) use ($selectedCategory) {
                    $cq->where('slug', $selectedCategory)
                      ->orWhere('name', $selectedCategory);
                })->orWhere('category_id', $selectedCategory);
            });
        }

        // Feature 18: Filter by Price Range (min and max bounds)
        $minPrice = $request->filled('min_price') ? (float)$request->input('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float)$request->input('max_price') : null;
        if (!is_null($minPrice) && $minPrice > 0) {
            $query->where('price', '>=', $minPrice);
        }
        if (!is_null($maxPrice) && $maxPrice > 0) {
            $query->where('price', '<=', $maxPrice);
        }

        // Feature 19: Filter by Seller Rating & Product Rating
        $minRating = $request->filled('min_rating') ? (float)$request->input('min_rating') : ($request->filled('rating') ? (float)$request->input('rating') : null);
        if (!is_null($minRating) && $minRating > 0) {
            $query->where('average_rating', '>=', $minRating);
        }

        if ($request->filled('seller_rating')) {
            $sellerRating = (float)$request->input('seller_rating');
            $minTrust = $sellerRating <= 5 ? $sellerRating * 20 : $sellerRating;
            $query->whereHas('seller.sellerProfile', function ($sq) use ($minTrust) {
                $sq->where('trust_score', '>=', $minTrust);
            });
        }

        // Feature 20: Filter by Distance / Radius / City
        $city = $request->input('city');
        if (!empty($city)) {
            $query->whereHas('seller.sellerProfile', function ($sq) use ($city) {
                $sq->where('city', $city);
            });
        }

        $radius = $request->filled('radius') ? (float)$request->input('radius') : null;
        $userLat = $request->filled('lat') ? (float)$request->input('lat') : null;
        $userLng = $request->filled('lng') ? (float)$request->input('lng') : null;

        if (!is_null($radius) && $radius > 0) {
            $uLat = $userLat ?? 22.572646; // Default to Kolkata coordinates
            $uLng = $userLng ?? 88.363895;

            // Bounding box approximation (1 deg lat ~ 111 km, 1 deg lon ~ 111 * cos(lat) km)
            $latDelta = $radius / 111.0;
            $lngDelta = $radius / (111.0 * max(0.1, cos(deg2rad($uLat))));

            $query->whereHas('seller.sellerProfile', function ($sq) use ($uLat, $uLng, $latDelta, $lngDelta) {
                $sq->whereBetween('latitude', [$uLat - $latDelta, $uLat + $latDelta])
                   ->whereBetween('longitude', [$uLng - $lngDelta, $uLng + $lngDelta]);
            });
        }

        // Filter by Seller ID if specified
        if ($request->filled('seller')) {
            $query->where('seller_id', $request->input('seller'));
        }

        // Verified seller filter
        if ($request->boolean('verified_only')) {
            $query->whereHas('seller.sellerProfile', function ($sq) {
                $sq->where('status', 'approved');
            });
        }

        // In-stock only filter
        if ($request->boolean('in_stock_only')) {
            $query->where('stock', '>', 0);
        }

        // Feature 23: Sort Products
        $sortBy = $request->input('sort', $request->input('sort_by', 'newest'));
        switch ($sortBy) {
            case 'price_asc':
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'popular':
            case 'popularity':
                $query->orderBy('total_reviews', 'desc');
                break;
            case 'newest':
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        // Feature 16: Server-side pagination (12 items per page)
        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('status', 'active')->get();
        $dbCategories = $categories; // Alias for backward compatibility

        return view('user.products.index', [
            'products'         => $products,
            'dbProducts'       => $products, // Backward compatibility for views reading $dbProducts
            'categories'       => $categories,
            'dbCategories'     => $dbCategories,
            'search'           => $search,
            'selectedCategory' => $selectedCategory ?: 'All Categories',
            'minPrice'         => $minPrice,
            'maxPrice'         => $maxPrice,
            'minRating'        => $minRating ?? 0,
            'radius'           => $radius,
            'userLat'          => $userLat,
            'userLng'          => $userLng,
            'sortBy'           => $sortBy,
            'totalResults'     => $products->total(),
        ]);
    }

    /**
     * Feature 17: Dynamic Category Show Page
     */
    public function category(Request $request, $slug = 'all')
    {
        if (empty($slug) || $slug === 'all') {
            $slug = 'all';
            $category = null;
        } else {
            $category = Category::where('status', 'active')->where('slug', $slug)->first();
        }

        $categories = Category::where('status', 'active')->get();

        $query = Product::with(['category', 'seller.sellerProfile', 'images', 'primaryImage'])
            ->where('status', 'active');

        if ($category) {
            $query->where('category_id', $category->id);
        } elseif ($slug !== 'all') {
            $query->whereHas('category', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        // Optional search within category
        if ($request->filled('search')) {
            $search = trim((string)$request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Optional sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            default:
                $query->latest('id');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('user.products.category', compact('slug', 'category', 'categories', 'products'));
    }

    /**
     * Product Detail Page
     */
    public function show(Request $request, $slug = 'handcrafted-leather-messenger-bag')
    {
        $product = Product::with(['category', 'seller.sellerProfile', 'images', 'reviews.user'])
            ->where('status', 'active')
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::with(['category', 'primaryImage', 'images'])
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        return view('user.products.show', compact('product', 'slug', 'relatedProducts'));
    }

    /**
     * Feature 14: Transparent Pricing Page
     */
    public function feesAndCommission()
    {
        return view('docs.fees-and-commission');
    }

    /**
     * Seller Onboarding Guide
     */
    public function becomeASeller()
    {
        return view('docs.become-a-seller');
    }

    /**
     * Feature 15: How It Works & Platform Documentation
     */
    public function howItWorks()
    {
        return view('pages.how-it-works');
    }

    /**
     * Privacy Policy Page
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Terms of Service Page
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Return Policy Page
     */
    public function returnPolicy()
    {
        return view('pages.return-policy');
    }
}
