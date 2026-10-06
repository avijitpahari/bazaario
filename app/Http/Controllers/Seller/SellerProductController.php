<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Class SellerProductController
 *
 * Comprehensive Seller Product & Inventory Management Controller (Milestone 3).
 *
 * Implements:
 * - Feature 16: Product Catalog Master Table (Search, Category & Status Tabs, Freshness Tags)
 * - Feature 17: Custom Unit Types (UoM) Support ('kg', 'dozen', 'bundle', 'litre', 'piece', 'pack')
 * - Feature 18: Add / Edit Product Workstation (Form validation, slug & SKU auto-generation)
 * - Feature 19: Product Image Upload Dropzone (Multi-image upload, primary flag, public disk storage)
 * - Feature 20: Agronomic Ledger & Freshness Window (Harvest date, shelf-life window, expiry calculation)
 * - Feature 21: Automated Freshness Engine (Auto-flag stale items, auto-hide expired listings)
 * - Feature 22: Inventory Stock Telemetry Table (Depleted stock alerts, health metrics)
 * - Feature 23: Quick Restock / Adjustment Modal ('add', 'reduce', 'set' with audit logging)
 * - Feature 24: Safe Product Deletion Guardrail (Protects against active orders & live auctions)
 * - Feature 25: Live Buyer View Simulation
 *
 * Enforces strict multi-tenant isolation: sellers can only view, edit, adjust, or delete their own products.
 */
class SellerProductController extends Controller
{
    /**
     * Permitted Units of Measurement.
     */
    public const VALID_UNIT_TYPES = ['kg', 'dozen', 'bundle', 'litre', 'piece', 'pack'];

    /**
     * Resolve authenticated seller with role and approval verification.
     */
    protected function getAuthenticatedSeller(): ?User
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return null;
        }

        return $user;
    }

    /**
     * Check if seller profile is approved.
     */
    protected function isSellerApproved(User $user): bool
    {
        $profile = $user->sellerProfile;
        return $profile && $profile->status === 'approved';
    }

    /**
     * Authorize product ownership for strict multi-tenant isolation.
     */
    protected function authorizeProductOwnership(User $user, Product $product): void
    {
        if ((int) $product->seller_id !== (int) $user->id) {
            abort(403, 'Unauthorized. You do not have permission to manage this product.');
        }
    }

    /**
     * Display the seller's product catalog with search, category filtering, and status tabs.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->isSellerApproved($user)) {
            return redirect()->route('seller.pending')
                ->with('warning', 'Your seller account is waiting for admin approval.');
        }

        $sellerId = $user->id;
        $sellerProfile = $user->sellerProfile;

        // Base query strictly scoped to authenticated seller
        $query = Product::where('seller_id', $sellerId)
            ->with(['category', 'primaryImage', 'images', 'auction']);

        // Search query filter (Name, SKU, Farm Origin, Description)
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('farm_origin', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id') && is_numeric($request->query('category_id'))) {
            $query->where('category_id', (int) $request->query('category_id'));
        }

        // Status Tabs Filtering
        $tab = $request->query('tab', 'all');
        switch ($tab) {
            case 'active':
                $query->where('status', 'active');
                break;
            case 'draft':
                $query->where('status', 'draft');
                break;
            case 'low':
            case 'low_stock':
                $query->lowStock();
                break;
            case 'out':
            case 'out_of_stock':
                $query->where('stock', '<=', 0);
                break;
            case 'stale':
                $query->stale();
                break;
            case 'all':
            default:
                // Return all seller products
                break;
        }

        // Sorting options
        $sort = $request->query('sort', 'recent');
        switch ($sort) {
            case 'recent':
                $query->latest();
                break;
            case 'stock-asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock-desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'price-asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name-asc':
                $query->orderBy('name', 'asc');
                break;
            case 'revenue':
            case 'velocity':
                $query->orderByDesc('average_rating')->latest();
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(15)->withQueryString();

        // Catalog Status Metrics Counts for Tabs
        $baseCountQuery = Product::where('seller_id', $sellerId);
        $totalCount = (clone $baseCountQuery)->count();
        $activeCount = (clone $baseCountQuery)->where('status', 'active')->count();
        $draftCount = (clone $baseCountQuery)->where('status', 'draft')->count();
        $lowStockCount = (clone $baseCountQuery)->lowStock()->count();
        $outOfStockCount = (clone $baseCountQuery)->where('stock', '<=', 0)->count();
        $staleCount = (clone $baseCountQuery)->stale()->count();

        // Items requiring urgent attention: perishable items within 48h of expiry or already stale
        $freshnessAttentionCount = (clone $baseCountQuery)
            ->where('is_perishable', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::now()->addDays(2)->toDateString())
            ->count();

        $categories = Category::active()->orderBy('name')->get();

        return view('seller.products.index', compact(
            'user',
            'sellerProfile',
            'products',
            'categories',
            'tab',
            'sort',
            'totalCount',
            'activeCount',
            'draftCount',
            'lowStockCount',
            'outOfStockCount',
            'staleCount',
            'freshnessAttentionCount'
        ));
    }

    /**
     * Show the workstation form for creating a new product.
     *
     * @return View|RedirectResponse
     */
    public function create(): View|RedirectResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->isSellerApproved($user)) {
            return redirect()->route('seller.pending')
                ->with('warning', 'Your seller account is waiting for admin approval.');
        }

        $categories = Category::active()->orderBy('name')->get();
        $unitTypes = self::VALID_UNIT_TYPES;
        $sellerProfile = $user->sellerProfile;

        return view('seller.products.create', compact(
            'user',
            'sellerProfile',
            'categories',
            'unitTypes'
        ));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if (!$this->isSellerApproved($user)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Seller account pending approval.'], 403);
            }
            return redirect()->route('seller.pending')
                ->with('warning', 'Your seller account is waiting for admin approval.');
        }

        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'category_id'          => ['required', 'integer', 'exists:categories,id'],
            'price'                => ['required', 'numeric', 'gt:0'],
            'unit_type'            => ['required', 'string', 'in:' . implode(',', self::VALID_UNIT_TYPES)],
            'stock'                => ['required', 'integer', 'min:0'],
            'low_stock_threshold'  => ['nullable', 'integer', 'min:0'],
            'short_description'    => ['nullable', 'string', 'max:500'],
            'description'          => ['nullable', 'string'],
            'sale_type'            => ['nullable', 'string', 'in:fixed_price,auction'],
            'discount_pct'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'farm_origin'          => ['nullable', 'string', 'max:255'],
            'harvest_grade'        => ['nullable', 'string', 'max:100'],
            'harvest_date'         => ['nullable', 'date'],
            'expiry_days'          => ['nullable', 'integer', 'min:1'],
            'expiry_date'          => ['nullable', 'date'],
            'is_perishable'        => ['nullable', 'boolean'],
            'auto_hide_expired'    => ['nullable', 'boolean'],
            'status'               => ['nullable', 'string', 'in:draft,active,inactive,archived'],
            'sku'                  => ['nullable', 'string', 'max:100'],
            'images'               => ['nullable', 'array', 'max:6'],
            'images.*'             => ['file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'image'                => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'primary_image_index'  => ['nullable', 'integer', 'min:0'],
        ]);

        return DB::transaction(function () use ($request, $validated, $user) {
            // 1. Slug auto-generation with collision avoidance
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug ?: 'product';
            if (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . strtolower(Str::random(6));
            }

            // 2. SKU auto-generation if not supplied
            $sku = $request->input('sku');
            if (empty($sku)) {
                $sanitized = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']));
                $prefix = 'BZ-' . (strlen($sanitized) >= 4 ? substr($sanitized, 0, 4) : 'PRD');
                $sku = $prefix . '-' . strtoupper(Str::random(4));
                while (Product::where('sku', $sku)->exists()) {
                    $sku = $prefix . '-' . strtoupper(Str::random(4));
                }
            }

            // 3. Agronomic Ledger & Freshness Engine Calculations
            $isPerishable = $request->boolean('is_perishable');
            $harvestDate = $request->input('harvest_date');
            $expiryDays = $request->filled('expiry_days') ? (int) $request->input('expiry_days') : null;
            $expiryDate = $request->input('expiry_date');

            if ($harvestDate && $expiryDays) {
                $isPerishable = true;
                if (empty($expiryDate)) {
                    $expiryDate = Carbon::parse($harvestDate)->addDays($expiryDays)->toDateString();
                }
            } elseif ($isPerishable && $harvestDate && $expiryDays && empty($expiryDate)) {
                $expiryDate = Carbon::parse($harvestDate)->addDays($expiryDays)->toDateString();
            }

            // Auto-hide expired listings: defaults to true for perishables if not explicitly specified
            $autoHideExpired = $request->has('auto_hide_expired')
                ? $request->boolean('auto_hide_expired')
                : ($isPerishable ? true : false);

            $status = $request->input('status', 'active');
            $saleType = $request->input('sale_type', 'fixed_price');
            $lowStockThreshold = $request->filled('low_stock_threshold')
                ? (int) $request->input('low_stock_threshold')
                : 10;

            // 4. Create Product Record
            $product = Product::create([
                'seller_id'           => $user->id,
                'category_id'         => (int) $validated['category_id'],
                'name'                => $validated['name'],
                'slug'                => $slug,
                'sku'                 => $sku,
                'short_description'   => $request->input('short_description'),
                'description'         => $request->input('description'),
                'sale_type'           => $saleType,
                'unit_type'           => $validated['unit_type'],
                'price'               => (float) $validated['price'],
                'stock'               => (int) $validated['stock'],
                'low_stock_threshold' => $lowStockThreshold,
                'status'              => $status,
                'farm_origin'         => $request->input('farm_origin'),
                'harvest_grade'       => $request->input('harvest_grade'),
                'harvest_date'        => $harvestDate,
                'expiry_days'         => $expiryDays,
                'expiry_date'         => $expiryDate,
                'is_perishable'       => $isPerishable,
                'auto_hide_expired'   => $autoHideExpired,
            ]);

            // 5. Image Uploads Processing
            $primaryIndex = (int) $request->input('primary_image_index', 0);
            $hasPrimary = false;

            if ($request->hasFile('images')) {
                $files = is_array($request->file('images')) ? $request->file('images') : [$request->file('images')];
                foreach ($files as $index => $file) {
                    $path = $file->store('products', 'public');
                    $isPrimary = ($index === $primaryIndex);
                    if ($isPrimary) {
                        $hasPrimary = true;
                    }
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $isPrimary,
                        'sort_order' => $index,
                    ]);
                }
            } elseif ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
                $hasPrimary = true;
            }

            // Ensure at least one image is primary if images were uploaded
            if ($product->images()->exists() && !$hasPrimary) {
                $firstImage = $product->images()->first();
                if ($firstImage) {
                    $firstImage->update(['is_primary' => true]);
                }
            }

            Log::info("Product created: #{$product->id} ({$product->sku}) by seller #{$user->id}");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Product '{$product->name}' created successfully.",
                    'product' => $product->load(['category', 'images']),
                ], 201);
            }

            return redirect()->route('seller.products.index')
                ->with('success', "Product '{$product->name}' created successfully.");
        });
    }

    /**
     * Display the specified product.
     *
     * @param Product $product
     * @return View|RedirectResponse
     */
    public function show(Product $product): View|RedirectResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorizeProductOwnership($user, $product);

        $product->load(['category', 'images', 'primaryImage', 'auction', 'reviews']);
        $sellerProfile = $user->sellerProfile;

        return view('seller.products.show', compact('user', 'sellerProfile', 'product'));
    }

    /**
     * Show the workstation form for editing an existing product.
     *
     * @param Product $product
     * @return View|RedirectResponse
     */
    public function edit(Product $product): View|RedirectResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->isSellerApproved($user)) {
            return redirect()->route('seller.pending')
                ->with('warning', 'Your seller account is waiting for admin approval.');
        }

        $this->authorizeProductOwnership($user, $product);

        $product->load(['category', 'images', 'primaryImage']);
        $categories = Category::active()->orderBy('name')->get();
        $unitTypes = self::VALID_UNIT_TYPES;
        $sellerProfile = $user->sellerProfile;

        return view('seller.products.edit', compact(
            'user',
            'sellerProfile',
            'product',
            'categories',
            'unitTypes'
        ));
    }

    /**
     * Update the specified product in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse|JsonResponse
     */
    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $this->authorizeProductOwnership($user, $product);

        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'category_id'          => ['required', 'integer', 'exists:categories,id'],
            'price'                => ['required', 'numeric', 'gt:0'],
            'unit_type'            => ['required', 'string', 'in:' . implode(',', self::VALID_UNIT_TYPES)],
            'stock'                => ['required', 'integer', 'min:0'],
            'low_stock_threshold'  => ['nullable', 'integer', 'min:0'],
            'short_description'    => ['nullable', 'string', 'max:500'],
            'description'          => ['nullable', 'string'],
            'sale_type'            => ['nullable', 'string', 'in:fixed_price,auction'],
            'discount_pct'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'farm_origin'          => ['nullable', 'string', 'max:255'],
            'harvest_grade'        => ['nullable', 'string', 'max:100'],
            'harvest_date'         => ['nullable', 'date'],
            'expiry_days'          => ['nullable', 'integer', 'min:1'],
            'expiry_date'          => ['nullable', 'date'],
            'is_perishable'        => ['nullable', 'boolean'],
            'auto_hide_expired'    => ['nullable', 'boolean'],
            'status'               => ['nullable', 'string', 'in:draft,active,inactive,archived'],
            'images'               => ['nullable', 'array', 'max:6'],
            'images.*'             => ['file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'image'                => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'primary_image_index'  => ['nullable', 'integer', 'min:0'],
        ]);

        return DB::transaction(function () use ($request, $validated, $product, $user) {
            $isPerishable = $request->boolean('is_perishable');
            $harvestDate = $request->input('harvest_date');
            $expiryDays = $request->filled('expiry_days') ? (int) $request->input('expiry_days') : null;
            $expiryDate = $request->input('expiry_date');

            if ($harvestDate && $expiryDays) {
                $isPerishable = true;
                $expiryDate = Carbon::parse($harvestDate)->addDays($expiryDays)->toDateString();
            }

            $autoHideExpired = $request->has('auto_hide_expired')
                ? $request->boolean('auto_hide_expired')
                : ($isPerishable ? $product->auto_hide_expired : false);

            $lowStockThreshold = $request->filled('low_stock_threshold')
                ? (int) $request->input('low_stock_threshold')
                : ($product->low_stock_threshold ?? 10);

            $product->update([
                'category_id'         => (int) $validated['category_id'],
                'name'                => $validated['name'],
                'short_description'   => $request->input('short_description', $product->short_description),
                'description'         => $request->input('description', $product->description),
                'sale_type'           => $request->input('sale_type', $product->sale_type),
                'unit_type'           => $validated['unit_type'],
                'price'               => (float) $validated['price'],
                'stock'               => (int) $validated['stock'],
                'low_stock_threshold' => $lowStockThreshold,
                'status'              => $request->input('status', $product->status),
                'farm_origin'         => $request->input('farm_origin', $product->farm_origin),
                'harvest_grade'       => $request->input('harvest_grade', $product->harvest_grade),
                'harvest_date'        => $harvestDate,
                'expiry_days'         => $expiryDays,
                'expiry_date'         => $expiryDate,
                'is_perishable'       => $isPerishable,
                'auto_hide_expired'   => $autoHideExpired,
            ]);

            // Handle optional new images
            if ($request->hasFile('images')) {
                $currentMaxOrder = (int) $product->images()->max('sort_order') ?? 0;
                $files = is_array($request->file('images')) ? $request->file('images') : [$request->file('images')];
                foreach ($files as $index => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => false,
                        'sort_order' => $currentMaxOrder + $index + 1,
                    ]);
                }
            } elseif ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => ((int) $product->images()->max('sort_order') ?? 0) + 1,
                ]);
            }

            Log::info("Product updated: #{$product->id} ({$product->sku}) by seller #{$user->id}");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Product '{$product->name}' updated successfully.",
                    'product' => $product->fresh(['category', 'images']),
                ]);
            }

            return redirect()->route('seller.products.index')
                ->with('success', "Product '{$product->name}' updated successfully.");
        });
    }

    /**
     * Remove the specified product from storage (Safe Deletion Guardrail).
     *
     * Protected against deletion if:
     * 1. Product is associated with active unfulfilled orders (status: placed, processing, packed, shipped)
     * 2. Product is currently listed in an active or scheduled auction (status: scheduled, live, active)
     *
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse|JsonResponse
     */
    public function destroy(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $this->authorizeProductOwnership($user, $product);

        return DB::transaction(function () use ($request, $product, $user) {
            $lockedProduct = Product::lockForUpdate()->findOrFail($product->id);

            // Guardrail 1: Active unfulfilled orders check
            $hasActiveOrders = OrderItem::where('product_id', $lockedProduct->id)
                ->whereHas('sellerOrder', function ($q) {
                    $q->whereNotIn('status', ['delivered', 'cancelled', 'returned']);
                })
                ->exists();

            if ($hasActiveOrders) {
                $errorMessage = "Cannot delete product '{$lockedProduct->name}' because it is associated with active unfulfilled orders.";
                if ($request->expectsJson()) {
                    return response()->json(['message' => $errorMessage], 422);
                }
                return back()->with('error', $errorMessage);
            }

            // Guardrail 2: Active or scheduled auctions check
            $hasActiveAuction = Auction::where('product_id', $lockedProduct->id)
                ->whereIn('status', ['live', 'scheduled', 'active'])
                ->exists();

            if ($hasActiveAuction) {
                $errorMessage = "Cannot delete product '{$lockedProduct->name}' because it has an active or scheduled auction lot.";
                if ($request->expectsJson()) {
                    return response()->json(['message' => $errorMessage], 422);
                }
                return back()->with('error', $errorMessage);
            }

            $productName = $lockedProduct->name;
            $lockedProduct->delete();

            Log::info("Product #{$lockedProduct->id} ({$lockedProduct->sku}) safely deleted by seller #{$user->id}");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Product '{$productName}' deleted successfully.",
                ]);
            }

            return redirect()->route('seller.products.index')
                ->with('success', "Product '{$productName}' deleted successfully.");
        });
    }

    /**
     * Display the warehouse inventory & stock telemetry table.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function inventory(Request $request): View|RedirectResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->isSellerApproved($user)) {
            return redirect()->route('seller.pending')
                ->with('warning', 'Your seller account is waiting for admin approval.');
        }

        $sellerId = $user->id;
        $sellerProfile = $user->sellerProfile;

        $query = Product::where('seller_id', $sellerId)
            ->with(['category', 'primaryImage', 'images']);

        // Search query
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('farm_origin', 'like', "%{$search}%");
            });
        }

        // Tab filter
        $tab = $request->query('tab', 'all');
        switch ($tab) {
            case 'low':
            case 'low_stock':
                $query->lowStock();
                break;
            case 'out':
            case 'out_of_stock':
                $query->where('stock', '<=', 0);
                break;
            case 'stale':
            case 'freshness_alert':
                $query->stale();
                break;
            default:
                break;
        }

        $products = $query->orderBy('stock', 'asc')->paginate(15)->withQueryString();

        // 4 KPI Aggregations for Warehouse Telemetry
        $totalTracked = Product::where('seller_id', $sellerId)->count();
        $inStockHealthy = Product::where('seller_id', $sellerId)
            ->where('stock', '>', DB::raw('COALESCE(low_stock_threshold, 10)'))
            ->count();
        $lowStockCount = Product::where('seller_id', $sellerId)->lowStock()->count();
        $outOfStockCount = Product::where('seller_id', $sellerId)->where('stock', '<=', 0)->count();
        $freshnessAlertCount = Product::where('seller_id', $sellerId)->stale()->count();

        $healthyPercent = $totalTracked > 0 ? (int) round(($inStockHealthy / $totalTracked) * 100) : 100;

        return view('seller.products.inventory', compact(
            'user',
            'sellerProfile',
            'products',
            'tab',
            'totalTracked',
            'inStockHealthy',
            'lowStockCount',
            'outOfStockCount',
            'freshnessAlertCount',
            'healthyPercent'
        ));
    }

    /**
     * Adjust stock level for a product (Add, Reduce, or Set exact) with audit logging.
     *
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse|JsonResponse
     */
    public function adjustStock(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $this->authorizeProductOwnership($user, $product);

        $validated = $request->validate([
            'action'   => ['required', 'string', 'in:add,reduce,set'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reason'   => ['nullable', 'string', 'max:255'],
        ]);

        $qty = (int) $validated['quantity'];
        $oldStock = (int) $product->stock;

        switch ($validated['action']) {
            case 'add':
                $newStock = $oldStock + $qty;
                break;
            case 'reduce':
                $newStock = max(0, $oldStock - $qty);
                break;
            case 'set':
                $newStock = max(0, $qty);
                break;
            default:
                $newStock = $oldStock;
                break;
        }

        $product->update(['stock' => $newStock]);

        $reason = $validated['reason'] ?? 'Routine warehouse adjustment';
        Log::info("Stock adjusted for Product #{$product->id} ({$product->sku}): {$oldStock} -> {$newStock} by Seller #{$user->id}. Action: {$validated['action']}. Reason: {$reason}");

        $successMsg = "Stock for '{$product->name}' updated to {$newStock} {$product->unit_type}.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => $successMsg,
                'old_stock'  => $oldStock,
                'new_stock'  => $newStock,
                'product_id' => $product->id,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Handle bulk actions for selected products (P24: Activate, Deactivate, Archive, Delete).
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkAction(Request $request): RedirectResponse|JsonResponse
    {
        $user = $this->getAuthenticatedSeller();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'action' => 'required|string|in:activate,deactivate,draft,archive,delete',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $productIds = $validated['product_ids'];
        $action = $validated['action'];

        // Strict multi-tenant isolation: only manage seller's own products
        $sellerProducts = Product::where('seller_id', $user->id)
            ->whereIn('id', $productIds)
            ->get();

        if ($sellerProducts->isEmpty()) {
            return redirect()->route('seller.products.index')
                ->with('warning', 'No matching products found under your merchant account.');
        }

        $count = $sellerProducts->count();

        switch ($action) {
            case 'activate':
                Product::where('seller_id', $user->id)
                    ->whereIn('id', $sellerProducts->pluck('id'))
                    ->update(['status' => 'active']);
                $message = "Successfully activated {$count} product(s).";
                break;

            case 'deactivate':
            case 'draft':
                Product::where('seller_id', $user->id)
                    ->whereIn('id', $sellerProducts->pluck('id'))
                    ->update(['status' => 'draft']);
                $message = "Successfully updated {$count} product(s) to draft/inactive.";
                break;

            case 'archive':
                Product::where('seller_id', $user->id)
                    ->whereIn('id', $sellerProducts->pluck('id'))
                    ->update(['status' => 'archived']);
                $message = "Successfully archived {$count} product(s).";
                break;

            case 'delete':
                $deletedCount = 0;
                $blockedCount = 0;

                foreach ($sellerProducts as $prod) {
                    $hasActiveOrders = OrderItem::where('product_id', $prod->id)
                        ->whereHas('sellerOrder', function ($q) {
                            $q->whereNotIn('status', ['delivered', 'cancelled', 'returned']);
                        })
                        ->exists();

                    $hasActiveAuctions = Auction::where('product_id', $prod->id)
                        ->whereIn('status', ['scheduled', 'live', 'active'])
                        ->exists();

                    if ($hasActiveOrders || $hasActiveAuctions) {
                        $blockedCount++;
                        continue;
                    }

                    $prod->delete();
                    $deletedCount++;
                }

                if ($blockedCount > 0 && $deletedCount > 0) {
                    $message = "Deleted {$deletedCount} product(s). {$blockedCount} product(s) could not be deleted due to active orders or auctions.";
                } elseif ($blockedCount > 0 && $deletedCount === 0) {
                    return redirect()->route('seller.products.index')
                        ->with('error', "All {$blockedCount} selected product(s) have active orders or auctions and cannot be safely deleted.");
                } else {
                    $message = "Successfully deleted {$deletedCount} product(s).";
                }
                break;

            default:
                $message = "Bulk action completed for {$count} product(s).";
                break;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
            ]);
        }

        return redirect()->route('seller.products.index')->with('success', $message);
    }
}
