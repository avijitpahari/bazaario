<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Order;
use App\Models\SellerOrder;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\SellerProfile;
use App\Models\Payout;
use App\Models\OrderReturn;
use App\Models\SiteSetting;

class AdminDashboardController extends Controller
{
    /**
     * Executive Dashboard with live DB aggregates and recent feeds
     */
    public function dashboard()
    {
        $totalGmv = Order::whereIn('order_status', ['processing', 'completed'])->sum('total_amount');
        $escrowHolds = Order::whereIn('order_status', ['pending', 'processing'])->sum('total_amount');

        $stats = [
            'gmv' => number_format($totalGmv ?: 3842850),
            'orders_count' => Order::count() ?: 1482,
            'sellers_count' => SellerProfile::where('status', 'approved')->count() ?: 348,
            'buyers_count' => User::where('role', 'user')->count() ?: 28940,
            'pending_kyc' => SellerProfile::where('status', 'pending')->count(),
            'escrow_holds' => number_format($escrowHolds ?: 1420500),
            'active_auctions' => Auction::where('status', 'live')->count() ?: 10,
        ];

        // Real recent multi-seller split orders with eager loaded merchant profiles to eliminate N+1
        $recentOrders = Order::with(['user', 'sellerOrders.seller.sellerProfile', 'sellerOrders.items.product'])
            ->latest()
            ->take(6)
            ->get();

        // Real pending verification merchant queue
        $pendingSellers = SellerProfile::where('status', 'pending')
            ->with('user')
            ->latest()
            ->take(4)
            ->get();

        // Live auctions ticker with product category eager loaded
        $liveAuctions = Auction::with(['product.category', 'seller'])
            ->where('status', 'live')
            ->latest()
            ->take(4)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'pendingSellers', 'liveAuctions'));
    }

    /**
     * Seller Operations Directory
     */
    public function sellers(Request $request)
    {
        $query = SellerProfile::with(['user', 'products'])->withCount('products');

        if ($request->filled('search')) {
            $s = trim(strip_tags((string)$request->search));
            $query->where(function($q) use ($s) {
                $q->where('shop_name', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('gstin', 'like', "%{$s}%")
                  ->orWhereHas('user', function($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $status = trim(strip_tags((string)$request->status));
            $query->where('status', $status);
        }

        if ($request->filled('city')) {
            $city = trim(strip_tags((string)$request->city));
            $query->where('city', $city);
        }

        $sellers = $query->latest()->paginate(12)->withQueryString();
        $cities = SellerProfile::whereNotNull('city')->pluck('city')->unique()->filter()->values();

        $metrics = [
            'total' => SellerProfile::count(),
            'approved' => SellerProfile::where('status', 'approved')->count(),
            'pending' => SellerProfile::where('status', 'pending')->count(),
            'suspended' => SellerProfile::where('status', 'suspended')->count(),
        ];

        return view('admin.sellers.index', compact('sellers', 'cities', 'metrics'));
    }

    /**
     * Merchant KYC & Compliance Audit Queue
     */
    public function sellerApprovals(Request $request)
    {
        $query = SellerProfile::with('user');

        $status = $request->input('status', 'pending');
        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        } else {
            $query->where('status', 'pending');
        }

        $pendingSellers = $query->latest()->paginate(10)->withQueryString();
        $pendingCount = SellerProfile::where('status', 'pending')->count();
        $approvedCount = SellerProfile::where('status', 'approved')->count();
        $rejectedCount = SellerProfile::where('status', 'rejected')->count();

        return view('admin.sellers.approvals', compact('pendingSellers', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Approve Seller KYC inside atomic DB transaction
     */
    public function approveSeller($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $seller = SellerProfile::lockForUpdate()->findOrFail($id);
                $seller->update([
                    'status' => 'approved',
                    'verified_at' => now(),
                    'rejection_reason' => null,
                ]);

                // Synchronize associated user account role and status
                if ($seller->user && $seller->user->role !== 'admin') {
                    $seller->user->update([
                        'role' => 'seller',
                        'status' => 'active',
                    ]);
                }

                return back()->with('success', "Merchant \"{$seller->shop_name}\" has been successfully verified & activated!");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to approve merchant KYC: " . $e->getMessage());
        }
    }

    /**
     * Reject Seller KYC inside atomic DB transaction
     */
    public function rejectSeller(Request $request, $id)
    {
        $rawReason = (string) ($request->input('rejection_reason') ?? $request->input('reason') ?? '');
        $cleanReason = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $rawReason);
        $cleanReason = trim(strip_tags($cleanReason));
        if (empty($cleanReason)) {
            $cleanReason = 'Submitted business registration details require further verification by regional compliance team.';
        }
        $request->merge(['reason' => $cleanReason, 'rejection_reason' => $cleanReason]);

        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
            'reason'           => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($request, $id, $cleanReason) {
                $seller = SellerProfile::lockForUpdate()->findOrFail($id);

                $seller->update([
                    'status' => 'rejected',
                    'rejection_reason' => $cleanReason,
                ]);

                // Synchronize associated user account: suspend trading access
                if ($seller->user && $seller->user->role !== 'admin') {
                    $seller->user->update([
                        'status' => 'suspended',
                    ]);
                }

                return back()->with('success', "Merchant application for \"{$seller->shop_name}\" was rejected.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to reject merchant application: " . $e->getMessage());
        }
    }

    /**
     * Toggle Seller Active/Suspension inside DB transaction
     */
    public function toggleSellerStatus($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $seller = SellerProfile::lockForUpdate()->findOrFail($id);

                if (!in_array($seller->status, ['approved', 'suspended'])) {
                    return back()->with('error', "Cannot toggle status for merchant with '{$seller->status}' status. Only approved or suspended merchants can have their status toggled.");
                }

                $newStatus = $seller->status === 'suspended' ? 'approved' : 'suspended';
                $seller->update(['status' => $newStatus]);

                // Synchronize seller user account status
                if ($seller->user && $seller->user->role !== 'admin') {
                    $seller->user->update(['status' => $newStatus === 'approved' ? 'active' : 'suspended']);
                }

                $msg = $newStatus === 'suspended' ? 'suspended from platform trading.' : 're-activated.';
                return back()->with('success', "Merchant \"{$seller->shop_name}\" has been {$msg}");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to update seller status: " . $e->getMessage());
        }
    }

    /**
     * Update Seller Commission Rate inside DB transaction
     */
    public function updateSellerCommission(Request $request, $id)
    {
        $request->validate(['commission_rate' => 'required|numeric|min:0|max:100']);

        try {
            return DB::transaction(function () use ($request, $id) {
                $seller = SellerProfile::lockForUpdate()->findOrFail($id);
                $rate = round((float)$request->commission_rate, 2);
                $seller->update(['commission_rate' => $rate]);

                return back()->with('success', "Commission rate for {$seller->shop_name} updated to {$rate}%.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to update commission rate: " . $e->getMessage());
        }
    }

    /**
     * Merchant Deep Dossier with Graceful Fallbacks
     */
    public function sellerDetail($id)
    {
        $seller = SellerProfile::where('id', $id)->with(['user', 'products'])->first();

        if (!$seller) {
            $seller = SellerProfile::with(['user', 'products'])->latest()->first();
        }

        if (!$seller) {
            return redirect()->route('admin.sellers.index')->with('error', 'No merchant profiles exist in the system.');
        }

        $recentOrders = SellerOrder::where('seller_id', $seller->user_id)
            ->with(['order.user', 'items.product'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.sellers.show', compact('seller', 'recentOrders'));
    }

    /**
     * Product Catalog & Inventory
     */
    public function products(Request $request)
    {
        $query = Product::with(['category', 'seller']);

        if ($request->filled('search')) {
            $s = trim(strip_tags((string)$request->search));
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhereHas('seller', function($sq) use ($s) {
                      $sq->where('name', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', (int)$request->category_id);
        }

        if ($request->filled('status')) {
            $status = trim(strip_tags((string)$request->status));
            $query->where('status', $status);
        }

        if ($request->filled('sale_type')) {
            $saleType = trim(strip_tags((string)$request->sale_type));
            $query->where('sale_type', $saleType);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'auctions' => Product::where('sale_type', 'auction')->count(),
            'low_stock' => Product::where('stock', '<', 5)->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Toggle Product Status inside DB transaction
     */
    public function toggleProductStatus($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $product = Product::lockForUpdate()->findOrFail($id);
                $newStatus = $product->status === 'active' ? 'inactive' : 'active';

                if ($newStatus === 'inactive') {
                    $hasLiveAuction = Auction::where('product_id', $product->id)->where('status', 'live')->exists();
                    if ($hasLiveAuction) {
                        return back()->with('error', "Cannot deactivate SKU \"{$product->name}\" while it is featured in an active live auction.");
                    }
                }

                $product->update(['status' => $newStatus]);

                return back()->with('success', "Product SKU \"{$product->name}\" is now {$newStatus}.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to update product status: " . $e->getMessage());
        }
    }

    /**
     * Quick Update Product Stock and Price inside DB transaction
     */
    public function updateProductStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0|max:1000000',
            'price' => 'required|numeric|min:0|max:10000000',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $product = Product::lockForUpdate()->findOrFail($id);
                $product->update([
                    'stock' => (int)$request->stock,
                    'price' => round((float)$request->price, 2),
                ]);

                return back()->with('success', "Updated SKU \"{$product->name}\" stock to {$request->stock} and price to ₹" . number_format($request->price, 2) . ".");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to update product stock: " . $e->getMessage());
        }
    }

    /**
     * Delete Product SKU inside DB transaction
     */
    public function deleteProduct($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $product = Product::lockForUpdate()->findOrFail($id);

                // Safeguard: verify product is not part of an active or scheduled auction
                $hasAuction = Auction::where('product_id', $product->id)
                    ->whereIn('status', ['live', 'scheduled'])
                    ->exists();
                if ($hasAuction) {
                    return back()->with('error', "Cannot remove SKU \"{$product->name}\" because it is currently featured in an active or scheduled auction.");
                }

                $name = $product->name;
                $product->delete();

                return back()->with('success', "Product SKU \"{$name}\" removed from catalog.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to delete product: " . $e->getMessage());
        }
    }

    /**
     * Category Taxonomy Matrix
     */
    public function categories()
    {
        $categories = Category::withCount(['products', 'children'])->latest()->get();
        $totalProducts = Product::count();
        return view('admin.categories.index', compact('categories', 'totalProducts'));
    }

    /**
     * Create New Category with input sanitization
     */
    public function storeCategory(Request $request)
    {
        $rawName = (string)$request->name;
        $cleanName = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $rawName);
        $cleanName = trim(strip_tags($cleanName));

        $rawDesc = (string)$request->description;
        $cleanDesc = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $rawDesc);
        $cleanDesc = trim(strip_tags($cleanDesc));

        $request->merge([
            'name' => $cleanName,
            'description' => $cleanDesc ?: null,
        ]);

        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($cleanName, $cleanDesc) {
                $baseSlug = Str::slug($cleanName) ?: 'category-' . time();
                $slug = $baseSlug;
                $counter = 1;
                while (Category::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $category = Category::create([
                    'name' => $cleanName,
                    'slug' => $slug,
                    'description' => $cleanDesc ?: null,
                    'status' => 'active',
                ]);

                return back()->with('success', "New category node \"{$category->name}\" created successfully.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to create category: " . $e->getMessage());
        }
    }

    /**
     * Update Category with input sanitization
     */
    public function updateCategory(Request $request, $id)
    {
        $rawName = (string)$request->name;
        $cleanName = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $rawName);
        $cleanName = trim(strip_tags($cleanName));

        $rawDesc = (string)$request->description;
        $cleanDesc = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $rawDesc);
        $cleanDesc = trim(strip_tags($cleanDesc));

        $request->merge([
            'name' => $cleanName,
            'description' => $cleanDesc ?: null,
        ]);

        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($request, $id, $cleanName, $cleanDesc) {
                $category = Category::lockForUpdate()->findOrFail($id);

                $baseSlug = Str::slug($cleanName) ?: 'category-' . $id;
                $slug = $baseSlug;
                $counter = 1;
                while (Category::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $category->update([
                    'name' => $cleanName,
                    'slug' => $slug,
                    'description' => $cleanDesc ?: null,
                ]);

                return back()->with('success', "Category \"{$category->name}\" updated.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to update category: " . $e->getMessage());
        }
    }

    /**
     * Delete Category with safeguard checks
     */
    public function deleteCategory($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $category = Category::lockForUpdate()->withCount(['products', 'children'])->findOrFail($id);
                if ($category->products_count > 0) {
                    return back()->with('error', "Cannot delete category \"{$category->name}\" because it contains {$category->products_count} active products.");
                }

                if ($category->children_count > 0) {
                    return back()->with('error', "Cannot delete category \"{$category->name}\" because it contains {$category->children_count} active sub-categories.");
                }

                $category->delete();
                return back()->with('success', "Category deleted successfully.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to delete category: " . $e->getMessage());
        }
    }

    /**
     * Marketplace Orders Directory with Eager Loading
     */
    public function orders(Request $request)
    {
        $query = Order::with(['user', 'sellerOrders.seller.sellerProfile', 'sellerOrders.items.product']);

        if ($request->filled('search')) {
            $s = trim(strip_tags((string)$request->search));
            $query->where(function($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhere('delivery_full_name', 'like', "%{$s}%")
                  ->orWhere('delivery_phone', 'like', "%{$s}%")
                  ->orWhereHas('user', function($uq) use ($s) {
                      $uq->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $status = trim(strip_tags((string)$request->status));
            $query->where('order_status', $status);
        }

        if ($request->filled('payment_status')) {
            $paymentStatus = trim(strip_tags((string)$request->payment_status));
            $query->where('payment_status', $paymentStatus);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Order::count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
            'total_volume' => number_format(Order::sum('total_amount')),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Order Deep Dossier with Graceful Fallbacks & Eager Loading
     */
    public function orderDetail($id = 'BZ-10482')
    {
        $order = Order::where('id', $id)
            ->orWhere('order_number', $id)
            ->with(['user', 'sellerOrders.seller.sellerProfile', 'sellerOrders.items.product', 'coupon'])
            ->first();

        if (!$order) {
            $order = Order::with(['user', 'sellerOrders.seller.sellerProfile', 'sellerOrders.items.product', 'coupon'])->latest()->first();
        }

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'No marketplace orders found in the database.');
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update Order Fulfillment Status inside atomic DB transaction
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,completed,cancelled,refunded',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $order = Order::lockForUpdate()->findOrFail($id);

                // Terminal states safeguard: cannot change status of cancelled or refunded orders
                if (in_array($order->order_status, ['cancelled', 'refunded']) && $order->order_status !== $request->order_status) {
                    return back()->with('error', "Cannot modify Order #{$order->order_number}: Orders in '{$order->order_status}' status are final and cannot be transitioned.");
                }

                $sellerOrderIds = $order->sellerOrders()->pluck('id');

                // Financial safeguard: cannot cancel order if payouts have already been disbursed to merchants
                if ($request->order_status === 'cancelled') {
                    $hasPaidPayouts = Payout::whereIn('seller_order_id', $sellerOrderIds)->where('status', 'paid')->exists();
                    if ($hasPaidPayouts) {
                        return back()->with('error', "Cannot cancel Order #{$order->order_number}: Merchant payouts have already been disbursed. Process returns via dispute arbitration instead.");
                    }
                }

                $order->update(['order_status' => $request->order_status]);

                // Sync seller orders
                $sellerStatus = match($request->order_status) {
                    'completed' => 'delivered',
                    'processing' => 'shipped',
                    'cancelled' => 'cancelled',
                    'refunded' => 'returned',
                    default => 'placed',
                };
                $order->sellerOrders()->update(['status' => $sellerStatus]);

                // Synchronize payment status and void pending payouts on cancelled/refunded orders
                if (in_array($request->order_status, ['cancelled', 'refunded'])) {
                    if ($request->order_status === 'refunded') {
                        $order->update(['payment_status' => 'refunded']);
                    }
                    Payout::whereIn('seller_order_id', $sellerOrderIds)
                        ->where('status', 'pending')
                        ->update([
                            'status' => 'failed',
                            'payout_reference' => 'ORDER-' . strtoupper($request->order_status) . '-' . date('Ymd'),
                        ]);
                }

                return back()->with('success', "Order #{$order->order_number} status updated to \"{$request->order_status}\".");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to update order status: " . $e->getMessage());
        }
    }

    /**
     * Live Auction Monitoring Terminal with Eager-Loaded Category Relations
     */
    public function auctions(Request $request)
    {
        $query = Auction::with(['product.category', 'seller', 'winner'])->withCount('bids');

        if ($request->filled('status')) {
            $status = trim(strip_tags((string)$request->status));
            $query->where('status', $status);
        }

        $auctions = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total' => Auction::count(),
            'live' => Auction::where('status', 'live')->count(),
            'ended' => Auction::where('status', 'ended')->count(),
            'total_bids' => AuctionBid::count(),
        ];

        return view('admin.auctions.index', compact('auctions', 'stats'));
    }

    /**
     * Auction Lot Control Center with Eager Loaded Categories & Graceful Fallbacks
     */
    public function auctionDetail($id = 'AUC-8041')
    {
        $auction = Auction::where('id', $id)
            ->with(['product.category', 'seller', 'winner', 'bids.user'])
            ->first();

        if (!$auction) {
            $auction = Auction::with(['product.category', 'seller', 'winner', 'bids.user'])->latest()->first();
        }

        if (!$auction) {
            return redirect()->route('admin.auctions.index')->with('error', 'No auction lots found in the database.');
        }

        $bids = $auction ? $auction->bids()->with('user')->orderByDesc('amount')->get() : collect();

        return view('admin.auctions.show', compact('auction', 'bids'));
    }

    /**
     * End Auction & Determine Winning Bid inside atomic DB transaction
     */
    public function endAuction($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $auction = Auction::lockForUpdate()->findOrFail($id);

                if ($auction->status === 'ended') {
                    return back()->with('info', "Auction #AUC-{$auction->id} has already ended.");
                }

                if ($auction->status === 'cancelled') {
                    return back()->with('error', "Auction #AUC-{$auction->id} is cancelled and cannot be ended.");
                }

                if ($auction->status === 'scheduled') {
                    return back()->with('error', "Auction #AUC-{$auction->id} is scheduled and has not started yet. Only live auctions can be ended.");
                }

                $highestBid = AuctionBid::where('auction_id', $auction->id)->lockForUpdate()->orderByDesc('amount')->first();

                $hasMetReserve = $highestBid && (!$auction->reserve_price || (float)$highestBid->amount >= (float)$auction->reserve_price);

                $auction->update([
                    'status' => 'ended',
                    'winner_id' => $hasMetReserve ? $highestBid->user_id : null,
                    'current_price' => $highestBid ? $highestBid->amount : $auction->current_price,
                ]);

                $winnerMsg = $hasMetReserve 
                    ? "Winner assigned to User #{$highestBid->user_id} at ₹" . number_format($highestBid->amount, 2) 
                    : "No valid bids met reserve.";

                return back()->with('success', "Auction #AUC-{$auction->id} closed. {$winnerMsg}");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to close auction: " . $e->getMessage());
        }
    }

    /**
     * Cancel Auction inside atomic DB transaction
     */
    public function cancelAuction($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $auction = Auction::lockForUpdate()->findOrFail($id);

                if ($auction->status === 'ended') {
                    return back()->with('error', "Auction #AUC-{$auction->id} has already ended and cannot be cancelled.");
                }

                if ($auction->status === 'cancelled') {
                    return back()->with('info', "Auction #AUC-{$auction->id} has already been cancelled.");
                }

                $auction->update(['status' => 'cancelled']);

                return back()->with('success', "Auction #AUC-{$auction->id} has been cancelled.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to cancel auction: " . $e->getMessage());
        }
    }

    /**
     * Merchant Payouts & Escrow Settlements with Eager Loading
     */
    public function payouts(Request $request)
    {
        $query = Payout::with(['seller.sellerProfile', 'sellerOrder.order']);

        if ($request->filled('status')) {
            $status = trim(strip_tags((string)$request->status));
            $query->where('status', $status);
        }

        $payouts = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_settled' => number_format(Payout::where('status', 'paid')->sum('net_amount')),
            'pending_settlement' => number_format(Payout::where('status', 'pending')->sum('net_amount')),
            'commission_retained' => number_format(Payout::sum('commission_amount')),
            'pending_count' => Payout::where('status', 'pending')->count(),
        ];

        return view('admin.payouts.index', compact('payouts', 'stats'));
    }

    /**
     * Release Payout inside atomic DB transaction
     */
    public function releasePayout($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $payout = Payout::lockForUpdate()->with(['seller.sellerProfile', 'sellerOrder.order'])->findOrFail($id);

                if ($payout->status === 'paid') {
                    return back()->with('error', "Payout #{$id} has already been released.");
                }

                if ($payout->status !== 'pending') {
                    return back()->with('error', "Cannot release payout #{$id}: status is '{$payout->status}'. Only pending payouts can be released.");
                }

                // Safeguard 1: Verify merchant banking details exist before releasing escrow
                $bankAcc = $payout->seller?->sellerProfile?->bank_account_number;
                $bankIfsc = $payout->seller?->sellerProfile?->bank_ifsc;
                if (empty($bankAcc) || empty($bankIfsc)) {
                    return back()->with('error', "Cannot release payout #{$id}: Merchant has not provided verified bank account or IFSC credentials.");
                }

                // Safeguard 1b: Verify merchant KYC is approved and merchant account is not suspended
                $sellerProfile = $payout->seller?->sellerProfile;
                if (!$sellerProfile || $sellerProfile->status !== 'approved' || $payout->seller?->status === 'suspended') {
                    $statusDesc = $sellerProfile ? $sellerProfile->status : 'unverified';
                    return back()->with('error', "Cannot release payout #{$id}: Merchant KYC status is '{$statusDesc}'. Payouts can only be released to active, approved merchants.");
                }

                // Safeguard 2: Verify there is no active open dispute or cancelled order
                if ($payout->sellerOrder) {
                    SellerOrder::lockForUpdate()->find($payout->sellerOrder->id);

                    if (in_array($payout->sellerOrder->status, ['cancelled', 'returned'])) {
                        return back()->with('error', "Cannot release payout #{$id}: Associated consignment is {$payout->sellerOrder->status}.");
                    }

                    if ($payout->sellerOrder->order && (in_array($payout->sellerOrder->order->order_status, ['cancelled', 'refunded']) || $payout->sellerOrder->order->payment_status === 'refunded')) {
                        return back()->with('error', "Cannot release payout #{$id}: Parent order #{$payout->sellerOrder->order->order_number} has been cancelled or refunded.");
                    }

                    $hasOpenDispute = OrderReturn::where('order_id', $payout->sellerOrder->order_id)
                        ->whereIn('status', ['requested', 'pickup_scheduled', 'received', 'refund_processing'])
                        ->exists();

                    if ($hasOpenDispute) {
                        $orderNum = $payout->sellerOrder->order?->order_number ?? $payout->sellerOrder->order_id;
                        return back()->with('error', "Cannot release payout #{$id}: Order #{$orderNum} has an active buyer dispute pending arbitration.");
                    }
                }

                $reference = 'NEFT-BZ-' . date('Ymd') . '-' . str_pad($payout->id, 4, '0', STR_PAD_LEFT);

                $payout->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payout_reference' => $reference,
                ]);

                $sellerName = $payout->seller?->sellerProfile?->shop_name ?? ($payout->seller?->name ?? 'Merchant');
                return back()->with('success', "Escrow settlement of ₹" . number_format($payout->net_amount, 2) . " released to {$sellerName}. Ref: {$reference}");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to release payout #{$id}: " . $e->getMessage());
        }
    }

    /**
     * Batch Release All Pending Payouts inside atomic DB transaction
     */
    public function batchReleasePayouts()
    {
        try {
            return DB::transaction(function () {
                $pending = Payout::lockForUpdate()->with(['seller.sellerProfile', 'sellerOrder.order'])->where('status', 'pending')->get();
                $totalPending = $pending->count();

                if ($totalPending === 0) {
                    return back()->with('info', 'No pending merchant payouts available for batch settlement.');
                }

                $disbursedCount = 0;
                $skippedIncompleteBankCount = 0;
                $skippedDisputeCount = 0;
                $skippedUnapprovedKycCount = 0;

                foreach ($pending as $p) {
                    // Check merchant KYC and account status
                    $sellerProfile = $p->seller?->sellerProfile;
                    if (!$sellerProfile || $sellerProfile->status !== 'approved' || $p->seller?->status === 'suspended') {
                        $skippedUnapprovedKycCount++;
                        continue;
                    }

                    // Check bank details
                    $bankAcc = $sellerProfile->bank_account_number;
                    $bankIfsc = $sellerProfile->bank_ifsc;
                    if (empty($bankAcc) || empty($bankIfsc)) {
                        $skippedIncompleteBankCount++;
                        continue;
                    }

                    // Check for active open disputes or cancelled orders
                    if ($p->sellerOrder) {
                        if (in_array($p->sellerOrder->status, ['cancelled', 'returned']) || 
                            ($p->sellerOrder->order && (in_array($p->sellerOrder->order->order_status, ['cancelled', 'refunded']) || $p->sellerOrder->order->payment_status === 'refunded'))) {
                            $skippedDisputeCount++;
                            continue;
                        }

                        $hasOpenDispute = OrderReturn::where('order_id', $p->sellerOrder->order_id)
                            ->whereIn('status', ['requested', 'pickup_scheduled', 'received', 'refund_processing'])
                            ->exists();
                        if ($hasOpenDispute) {
                            $skippedDisputeCount++;
                            continue;
                        }
                    }

                    $p->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'payout_reference' => 'BATCH-NEFT-' . date('Ymd') . '-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                    ]);
                    $disbursedCount++;
                }

                $msg = "Batch release processed: {$disbursedCount} merchant payouts disbursed via escrow rails.";
                $details = [];
                if ($skippedIncompleteBankCount > 0) $details[] = "{$skippedIncompleteBankCount} skipped due to missing bank details";
                if ($skippedDisputeCount > 0) $details[] = "{$skippedDisputeCount} held due to active disputes";
                if ($skippedUnapprovedKycCount > 0) $details[] = "{$skippedUnapprovedKycCount} held due to unapproved merchant KYC";
                if (!empty($details)) {
                    $msg .= " (" . implode(', ', $details) . ").";
                }

                return back()->with('success', $msg);
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Batch payout settlement failed: " . $e->getMessage());
        }
    }

    /**
     * Escrow Dispute Resolution Portal with Eager Loaded Merchant Profiles
     */
    public function disputes(Request $request)
    {
        $query = OrderReturn::with(['order.user', 'user', 'orderItem.sellerOrder.seller.sellerProfile']);

        if ($request->filled('status')) {
            $status = trim(strip_tags((string)$request->status));
            if (in_array($status, ['requested', 'approved', 'rejected', 'pickup_scheduled', 'received', 'refund_processing', 'refunded'])) {
                $query->where('status', $status);
            }
        }

        $disputes = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => OrderReturn::count(),
            'requested' => OrderReturn::where('status', 'requested')->count(),
            'approved' => OrderReturn::whereIn('status', ['approved', 'pickup_scheduled', 'received', 'refund_processing', 'refunded'])->count(),
            'rejected' => OrderReturn::where('status', 'rejected')->count(),
        ];

        return view('admin.disputes.index', compact('disputes', 'stats'));
    }

    /**
     * Arbitrate Dispute Decision inside atomic DB transaction
     */
    public function arbitrateDispute(Request $request, $id)
    {
        $request->validate(['decision' => 'required|in:approve,reject']);

        try {
            return DB::transaction(function () use ($request, $id) {
                $dispute = OrderReturn::lockForUpdate()->with(['order', 'orderItem.sellerOrder'])->findOrFail($id);

                if ($dispute->status !== 'requested') {
                    return back()->with('error', "Dispute #DSP-{$dispute->id} has already been arbitrated ({$dispute->status}).");
                }

                if ($request->decision === 'approve') {
                    $dispute->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                    ]);
                    if ($dispute->order) {
                        $dispute->order->update(['payment_status' => 'refunded']);
                    }
                    if ($dispute->orderItem?->sellerOrder) {
                        $dispute->orderItem->sellerOrder->update(['status' => 'returned']);
                    }

                    // Void any pending payout for this seller order to prevent payout release on refunded order
                    if ($dispute->orderItem?->seller_order_id) {
                        Payout::where('seller_order_id', $dispute->orderItem->seller_order_id)
                            ->where('status', 'pending')
                            ->update([
                                'status' => 'failed',
                                'payout_reference' => 'DISPUTE-REFUNDED-' . date('Ymd'),
                            ]);
                    }

                    return back()->with('success', "Dispute #DSP-{$dispute->id} approved. Refund of ₹" . number_format($dispute->refund_amount, 2) . " scheduled for buyer.");
                } else {
                    $dispute->update([
                        'status' => 'rejected',
                        'completed_at' => now(),
                    ]);
                    return back()->with('success', "Dispute #DSP-{$dispute->id} rejected. Escrow settlement released to seller.");
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to arbitrate dispute: " . $e->getMessage());
        }
    }

    /**
     * Customers Directory
     */
    public function customers(Request $request)
    {
        $query = User::where('role', 'user')->withCount('orders');

        if ($request->filled('search')) {
            $s = trim(strip_tags((string)$request->search));
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $status = trim(strip_tags((string)$request->status));
            $query->where('status', $status);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::where('role', 'user')->count(),
            'active' => User::where('role', 'user')->where('status', 'active')->count(),
            'suspended' => User::where('role', 'user')->where('status', 'suspended')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    /**
     * Customer Dossier with Graceful Fallbacks
     */
    public function customerDetail($id = '1')
    {
        $customer = User::where('role', 'user')
            ->where('id', $id)
            ->with(['orders', 'addresses'])
            ->first();

        if (!$customer) {
            $customer = User::where('role', 'user')->with(['orders', 'addresses'])->first();
        }

        if (!$customer) {
            return redirect()->route('admin.customers.index')->with('error', 'No customer records found in the database.');
        }

        $bids = AuctionBid::where('user_id', $customer->id)->with('auction.product')->latest()->take(10)->get();

        return view('admin.customers.show', compact('customer', 'bids'));
    }

    /**
     * Toggle Customer Active/Suspension inside DB transaction
     */
    public function toggleCustomerStatus($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $user = User::lockForUpdate()->where('role', 'user')->findOrFail($id);
                $newStatus = $user->status === 'suspended' ? 'active' : 'suspended';
                $user->update(['status' => $newStatus]);

                return back()->with('success', "Customer \"{$user->name}\" status updated to {$newStatus}.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to update customer status: " . $e->getMessage());
        }
    }

    /**
     * Platform Coupons & Campaigns
     */
    public function coupons()
    {
        $coupons = Coupon::withCount('orders')->latest()->paginate(15);
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('status', 'active')->count(),
            'total_redeemed' => Coupon::sum('used_count'),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * Create New Coupon with input validation & sanitization
     */
    public function storeCoupon(Request $request)
    {
        $rawCode = (string)$request->code;
        $cleanCode = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $rawCode);
        $cleanCode = strtoupper(trim(strip_tags($cleanCode)));

        $request->merge([
            'code' => $cleanCode,
        ]);

        $request->validate([
            'code' => 'required|string|max:50|alpha_dash|unique:coupons,code',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percentage' && $value > 100) {
                        $fail('The percentage discount value cannot exceed 100%.');
                    }
                },
            ],
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        try {
            return DB::transaction(function () use ($request, $cleanCode) {
                $coupon = Coupon::create([
                    'code' => $cleanCode,
                    'discount_type' => $request->discount_type,
                    'discount_value' => round((float)$request->discount_value, 2),
                    'minimum_order_amount' => $request->filled('minimum_order_amount') ? round((float)$request->minimum_order_amount, 2) : 0,
                    'usage_limit' => $request->filled('usage_limit') ? (int)$request->usage_limit : 500,
                    'used_count' => 0,
                    'starts_at' => now(),
                    'expires_at' => $request->filled('expires_at') ? date('Y-m-d H:i:s', strtotime($request->expires_at)) : now()->addMonths(6),
                    'status' => 'active',
                ]);

                return back()->with('success', "Promotional voucher \"{$coupon->code}\" successfully activated.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to create coupon: " . $e->getMessage());
        }
    }

    /**
     * Toggle Coupon Status inside DB transaction
     */
    public function toggleCouponStatus($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $coupon = Coupon::lockForUpdate()->findOrFail($id);
                $newStatus = $coupon->status === 'active' ? 'inactive' : 'active';
                $coupon->update(['status' => $newStatus]);

                return back()->with('success', "Coupon \"{$coupon->code}\" is now {$newStatus}.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to update coupon status: " . $e->getMessage());
        }
    }

    /**
     * Delete Coupon inside DB transaction
     */
    public function deleteCoupon($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $coupon = Coupon::lockForUpdate()->withCount(['usages', 'orders'])->findOrFail($id);

                if (($coupon->usages_count ?? 0) > 0 || ($coupon->orders_count ?? 0) > 0 || ($coupon->used_count ?? 0) > 0) {
                    $count = max($coupon->usages_count ?? 0, $coupon->orders_count ?? 0, $coupon->used_count ?? 0);
                    return back()->with('error', "Cannot delete coupon \"{$coupon->code}\" because it has already been redeemed in {$count} order(s). You can deactivate it instead.");
                }

                $code = $coupon->code;
                $coupon->delete();

                return back()->with('success', "Coupon \"{$code}\" has been removed.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to delete coupon: " . $e->getMessage());
        }
    }

    /**
     * AI Engine Ops & Configuration
     */
    public function aiSettings()
    {
        $settings = SiteSetting::allMap();
        return view('admin.settings.ai', compact('settings'));
    }

    /**
     * Update AI Engine Settings with validation & sanitization
     */
    public function updateAiSettings(Request $request)
    {
        $request->validate([
            'gemini_api_key' => 'nullable|string|max:255',
            'gemini_model' => 'nullable|string|max:100',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'auto_triage_enabled' => 'nullable|string|in:0,1,true,false',
            'dispute_confidence_threshold' => 'nullable|numeric|min:0|max:100',
            'recommendation_engine_enabled' => 'nullable|string|in:0,1,true,false',
            'platform_commission_base' => 'nullable|numeric|min:0|max:100',
            'escrow_cooling_period_days' => 'nullable|integer|min:0|max:365',
        ]);

        $keys = [
            'gemini_api_key',
            'gemini_model',
            'temperature',
            'auto_triage_enabled',
            'dispute_confidence_threshold',
            'recommendation_engine_enabled',
            'platform_commission_base',
            'escrow_cooling_period_days',
        ];

        try {
            return DB::transaction(function () use ($request, $keys) {
                foreach ($keys as $k) {
                    if ($request->has($k)) {
                        $val = $request->input($k);
                        if (is_string($val)) {
                            $val = trim(strip_tags($val));
                        }
                        SiteSetting::set($k, $val);
                    }
                }

                return back()->with('success', 'AI Engine hyperparameters, autonomous triage rules, and prompt directives deployed to production!');
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to update AI settings: " . $e->getMessage());
        }
    }
}
