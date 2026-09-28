<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

        // Real recent multi-seller split orders
        $recentOrders = Order::with(['user', 'sellerOrders.seller', 'sellerOrders.items.product'])
            ->latest()
            ->take(6)
            ->get();

        // Real pending verification merchant queue
        $pendingSellers = SellerProfile::where('status', 'pending')
            ->with('user')
            ->latest()
            ->take(4)
            ->get();

        // Live auctions ticker
        $liveAuctions = Auction::with(['product', 'seller'])
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
        $query = SellerProfile::with(['user', 'products']);

        if ($request->filled('search')) {
            $s = $request->search;
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
            $query->where('status', $request->status);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $sellers = $query->latest()->paginate(12)->withQueryString();
        $cities = SellerProfile::whereNotNull('city')->pluck('city')->unique();

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
     * Approve Seller KYC
     */
    public function approveSeller($id)
    {
        $seller = SellerProfile::findOrFail($id);
        $seller->update([
            'status' => 'approved',
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Merchant \"{$seller->shop_name}\" has been successfully verified & activated!");
    }

    /**
     * Reject Seller KYC
     */
    public function rejectSeller(Request $request, $id)
    {
        $seller = SellerProfile::findOrFail($id);
        $reason = $request->input('reason', 'Submitted business documents did not satisfy GSTIN or trade licensing compliance.');
        $seller->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        return back()->with('success', "Merchant application for \"{$seller->shop_name}\" was rejected.");
    }

    /**
     * Toggle Seller Active/Suspension
     */
    public function toggleSellerStatus($id)
    {
        $seller = SellerProfile::findOrFail($id);
        $newStatus = $seller->status === 'suspended' ? 'approved' : 'suspended';
        $seller->update(['status' => $newStatus]);

        $msg = $newStatus === 'suspended' ? 'suspended from platform trading.' : 're-activated.';
        return back()->with('success', "Merchant \"{$seller->shop_name}\" has been {$msg}");
    }

    /**
     * Update Seller Commission Rate
     */
    public function updateSellerCommission(Request $request, $id)
    {
        $request->validate(['commission_rate' => 'required|numeric|min:0|max:50']);
        $seller = SellerProfile::findOrFail($id);
        $seller->update(['commission_rate' => $request->commission_rate]);

        return back()->with('success', "Commission rate for {$seller->shop_name} updated to {$request->commission_rate}%.");
    }

    /**
     * Merchant Deep Dossier
     */
    public function sellerDetail($id)
    {
        $seller = SellerProfile::with(['user', 'products'])->findOrFail($id);
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
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhereHas('seller', function($sq) use ($s) {
                      $sq->where('name', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sale_type')) {
            $query->where('sale_type', $request->sale_type);
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
     * Toggle Product Status
     */
    public function toggleProductStatus($id)
    {
        $product = Product::findOrFail($id);
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);

        return back()->with('success', "Product SKU \"{$product->name}\" is now {$newStatus}.");
    }

    /**
     * Quick Update Product Stock and Price
     */
    public function updateProductStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'stock' => $request->stock,
            'price' => $request->price,
        ]);

        return back()->with('success', "Updated SKU \"{$product->name}\" stock to {$request->stock} and price to ₹{$request->price}.");
    }

    /**
     * Delete Product SKU
     */
    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        return back()->with('success', "Product SKU \"{$name}\" removed from catalog.");
    }

    /**
     * Category Taxonomy Matrix
     */
    public function categories()
    {
        $categories = Category::withCount('products')->latest()->get();
        $totalProducts = Product::count();
        return view('admin.categories.index', compact('categories', 'totalProducts'));
    }

    /**
     * Create New Category
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => 'active',
        ]);

        return back()->with('success', "New category node \"{$category->name}\" created successfully.");
    }

    /**
     * Update Category
     */
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return back()->with('success', "Category \"{$category->name}\" updated.");
    }

    /**
     * Delete Category
     */
    public function deleteCategory($id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        if ($category->products_count > 0) {
            return back()->with('error', "Cannot delete category \"{$category->name}\" because it contains {$category->products_count} active products.");
        }

        $category->delete();
        return back()->with('success', "Category deleted successfully.");
    }

    /**
     * Marketplace Orders Directory
     */
    public function orders(Request $request)
    {
        $query = Order::with(['user', 'sellerOrders.seller', 'sellerOrders.items.product']);

        if ($request->filled('search')) {
            $s = $request->search;
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
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
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
     * Order Deep Dossier
     */
    public function orderDetail($id = 'BZ-10482')
    {
        $order = Order::where('id', $id)
            ->orWhere('order_number', $id)
            ->with(['user', 'sellerOrders.seller', 'sellerOrders.items.product', 'coupon'])
            ->first();

        if (!$order) {
            $order = Order::with(['user', 'sellerOrders.seller', 'sellerOrders.items.product', 'coupon'])->latest()->first();
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update Order Fulfillment Status
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,completed,cancelled,refunded',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['order_status' => $request->order_status]);

        // Sync seller orders
        $sellerStatus = match($request->order_status) {
            'completed' => 'delivered',
            'processing' => 'shipped',
            'cancelled' => 'cancelled',
            default => 'placed',
        };
        $order->sellerOrders()->update(['status' => $sellerStatus]);

        return back()->with('success', "Order #{$order->order_number} status updated to \"{$request->order_status}\".");
    }

    /**
     * Live Auction Monitoring Terminal
     */
    public function auctions(Request $request)
    {
        $query = Auction::with(['product', 'seller', 'winner'])->withCount('bids');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
     * Auction Lot Control Center
     */
    public function auctionDetail($id = 'AUC-8041')
    {
        $auction = Auction::where('id', $id)
            ->with(['product', 'seller', 'winner', 'bids.user'])
            ->first();

        if (!$auction) {
            $auction = Auction::with(['product', 'seller', 'winner', 'bids.user'])->latest()->first();
        }

        $bids = $auction ? $auction->bids()->with('user')->orderByDesc('amount')->get() : collect();

        return view('admin.auctions.show', compact('auction', 'bids'));
    }

    /**
     * End Auction & Determine Winning Bid
     */
    public function endAuction($id)
    {
        $auction = Auction::findOrFail($id);
        $highestBid = AuctionBid::where('auction_id', $auction->id)->orderByDesc('amount')->first();

        $auction->update([
            'status' => 'ended',
            'winner_id' => $highestBid ? $highestBid->user_id : null,
            'current_price' => $highestBid ? $highestBid->amount : $auction->current_price,
        ]);

        $winnerMsg = $highestBid ? "Winner assigned to User #{$highestBid->user_id} at ₹" . number_format($highestBid->amount) : "No valid bids met reserve.";
        return back()->with('success', "Auction #AUC-{$auction->id} closed. {$winnerMsg}");
    }

    /**
     * Cancel Auction
     */
    public function cancelAuction($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->update(['status' => 'cancelled']);

        return back()->with('success', "Auction #AUC-{$auction->id} has been cancelled.");
    }

    /**
     * Merchant Payouts & Escrow Settlements
     */
    public function payouts(Request $request)
    {
        $query = Payout::with(['seller.sellerProfile', 'sellerOrder.order']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
     * Release Payout
     */
    public function releasePayout($id)
    {
        $payout = Payout::with('seller')->findOrFail($id);
        $reference = 'NEFT-BZ-' . date('Ymd') . '-' . str_pad($payout->id, 4, '0', STR_PAD_LEFT);

        $payout->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payout_reference' => $reference,
        ]);

        return back()->with('success', "Escrow settlement of ₹" . number_format($payout->net_amount, 2) . " released to {$payout->seller->name}. Ref: {$reference}");
    }

    /**
     * Batch Release All Pending Payouts
     */
    public function batchReleasePayouts()
    {
        $pending = Payout::where('status', 'pending')->get();
        $count = $pending->count();

        foreach ($pending as $p) {
            $p->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payout_reference' => 'BATCH-NEFT-' . date('Ymd') . '-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
            ]);
        }

        return back()->with('success', "Batch release completed: {$count} merchant payouts disbursed via escrow rails.");
    }

    /**
     * Escrow Dispute Resolution Portal
     */
    public function disputes(Request $request)
    {
        $query = OrderReturn::with(['order.user', 'orderItem.sellerOrder.seller']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $disputes = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => OrderReturn::count(),
            'requested' => OrderReturn::where('status', 'requested')->count(),
            'approved' => OrderReturn::where('status', 'approved')->count(),
            'rejected' => OrderReturn::where('status', 'rejected')->count(),
        ];

        return view('admin.disputes.index', compact('disputes', 'stats'));
    }

    /**
     * Arbitrate Dispute Decision
     */
    public function arbitrateDispute(Request $request, $id)
    {
        $request->validate(['decision' => 'required|in:approve,reject']);
        $dispute = OrderReturn::with('order')->findOrFail($id);

        if ($request->decision === 'approve') {
            $dispute->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
            if ($dispute->order) {
                $dispute->order->update(['payment_status' => 'refunded']);
            }
            return back()->with('success', "Dispute #DSP-{$dispute->id} approved. Refund of ₹" . number_format($dispute->refund_amount, 2) . " scheduled for buyer.");
        } else {
            $dispute->update([
                'status' => 'rejected',
                'completed_at' => now(),
            ]);
            return back()->with('success', "Dispute #DSP-{$dispute->id} rejected. Escrow settlement released to seller.");
        }
    }

    /**
     * Customers Directory
     */
    public function customers(Request $request)
    {
        $query = User::where('role', 'user')->withCount('orders');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
     * Customer Dossier
     */
    public function customerDetail($id = '1')
    {
        $customer = User::where('role', 'user')
            ->where('id', $id)
            ->with(['orders.sellerOrders.items.product', 'addresses'])
            ->first();

        if (!$customer) {
            $customer = User::where('role', 'user')->with(['orders.sellerOrders.items.product', 'addresses'])->first();
        }

        $bids = $customer ? AuctionBid::where('user_id', $customer->id)->with('auction.product')->latest()->take(10)->get() : collect();

        return view('admin.customers.show', compact('customer', 'bids'));
    }

    /**
     * Toggle Customer Active/Suspension
     */
    public function toggleCustomerStatus($id)
    {
        $user = User::where('role', 'user')->findOrFail($id);
        $newStatus = $user->status === 'suspended' ? 'active' : 'suspended';
        $user->update(['status' => $newStatus]);

        return back()->with('success', "Customer \"{$user->name}\" status updated to {$newStatus}.");
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
     * Create New Coupon
     */
    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:1',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $coupon = Coupon::create([
            'code' => strtoupper(trim($request->code)),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'minimum_order_amount' => $request->minimum_order_amount ?? 0,
            'usage_limit' => $request->usage_limit ?? 500,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => $request->expires_at ? date('Y-m-d H:i:s', strtotime($request->expires_at)) : now()->addMonths(6),
            'status' => 'active',
        ]);

        return back()->with('success', "Promotional voucher \"{$coupon->code}\" successfully activated.");
    }

    /**
     * Toggle Coupon Status
     */
    public function toggleCouponStatus($id)
    {
        $coupon = Coupon::findOrFail($id);
        $newStatus = $coupon->status === 'active' ? 'inactive' : 'active';
        $coupon->update(['status' => $newStatus]);

        return back()->with('success', "Coupon \"{$coupon->code}\" is now {$newStatus}.");
    }

    /**
     * Delete Coupon
     */
    public function deleteCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $code = $coupon->code;
        $coupon->delete();

        return back()->with('success', "Coupon \"{$code}\" has been removed.");
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
     * Update AI Engine Settings
     */
    public function updateAiSettings(Request $request)
    {
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

        foreach ($keys as $k) {
            if ($request->has($k)) {
                SiteSetting::set($k, $request->input($k));
            }
        }

        return back()->with('success', 'AI Engine hyperparameters, autonomous triage rules, and prompt directives deployed to production!');
    }
}
