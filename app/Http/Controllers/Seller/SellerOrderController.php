<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payout;
use App\Models\SellerOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerOrderController extends Controller
{
    /**
     * Get the authenticated seller user.
     */
    protected function getAuthenticatedSeller(): User
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        return $user;
    }

    /**
     * Ensure the order belongs to the authenticated seller.
     */
    protected function authorizeOrderOwnership(User $seller, SellerOrder $order): void
    {
        if ((int) $order->seller_id !== (int) $seller->id) {
            abort(403, 'Unauthorized access to this order.');
        }
    }

    /**
     * Display a listing of seller orders (2-column workspace).
     */
    public function index(Request $request): View
    {
        $seller = $this->getAuthenticatedSeller();
        $sellerId = $seller->id;
        $sellerProfile = $seller->sellerProfile;

        $status = $request->query('status', 'all');
        $search = $request->query('search');
        $dateFilter = $request->query('date', 'all');
        $selectedId = $request->query('order_id', $request->query('selected'));

        // Precompute status tab counts strictly within tenancy boundary
        $allOrdersForCounts = SellerOrder::forSeller($sellerId)->get(['id', 'status']);
        $statusCounts = [
            'all'              => $allOrdersForCounts->count(),
            'pending'          => $allOrdersForCounts->whereIn('status', ['placed', 'pending'])->count(),
            'confirmed'        => $allOrdersForCounts->where('status', 'confirmed')->count(),
            'processing'       => $allOrdersForCounts->where('status', 'processing')->count(),
            'ready_for_pickup' => $allOrdersForCounts->whereIn('status', ['ready_for_pickup', 'packed'])->count(),
            'fulfilled'        => $allOrdersForCounts->whereIn('status', ['fulfilled', 'delivered'])->count(),
            'cancelled'        => $allOrdersForCounts->whereIn('status', ['cancelled', 'returned'])->count(),
        ];

        // Base query with eager loaded relationships
        $query = SellerOrder::forSeller($sellerId)
            ->with(['order.user', 'items.product', 'payouts', 'payout']);

        // Apply status tab filtering
        if ($status !== 'all') {
            if (in_array($status, ['placed', 'pending'])) {
                $query->whereIn('status', ['placed', 'pending']);
            } elseif (in_array($status, ['ready_for_pickup', 'packed'])) {
                $query->whereIn('status', ['ready_for_pickup', 'packed']);
            } elseif (in_array($status, ['fulfilled', 'delivered'])) {
                $query->whereIn('status', ['fulfilled', 'delivered']);
            } elseif (in_array($status, ['cancelled', 'returned'])) {
                $query->whereIn('status', ['cancelled', 'returned']);
            } else {
                $query->where('status', $status);
            }
        }

        // Apply search query filter (order number, customer name, phone, item name)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('seller_order_number', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_number', 'like', "%{$search}%")
                            ->orWhere('delivery_full_name', 'like', "%{$search}%")
                            ->orWhere('delivery_phone', 'like', "%{$search}%")
                            ->orWhere('delivery_city', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('product_name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply date preset filter
        if ($dateFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateFilter === 'yesterday') {
            $query->whereDate('created_at', Carbon::yesterday());
        } elseif ($dateFilter === '7d' || $dateFilter === 'last_7_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Resolve focused order for the right-hand inspector
        $focusedOrder = null;
        if (!empty($selectedId)) {
            $focusedOrder = SellerOrder::forSeller($sellerId)
                ->with(['order.user', 'items.product', 'payouts', 'payout'])
                ->find($selectedId);
        }

        if (!$focusedOrder && $orders->isNotEmpty()) {
            $focusedOrder = $orders->first();
        }

        // Operational stats & telemetry
        $pendingAccrual = (float) SellerOrder::forSeller($sellerId)
            ->whereIn('status', ['placed', 'pending', 'processing', 'packed', 'ready_for_pickup'])
            ->sum('payout_amount');

        $stats = [
            'dispatch_capacity' => 82,
            'on_time_sla'       => 99.4,
            'pending_accrual'   => $pendingAccrual,
        ];

        return view('seller.orders.index', [
            'orders'          => $orders,
            'focusedOrder'    => $focusedOrder,
            'selectedOrder'   => $focusedOrder,
            'statusCounts'    => $statusCounts,
            'counts'          => $statusCounts,
            'stats'           => $stats,
            'currentStatus'   => $status,
            'searchQuery'     => $search,
            'dateFilter'      => $dateFilter,
            'sellerProfile'   => $sellerProfile,
            'sellerUser'      => $seller,
            'sellerId'        => $sellerId,
        ]);
    }

    /**
     * Display a single order details page.
     */
    public function show(Request $request, $order): View|JsonResponse
    {
        $seller = $this->getAuthenticatedSeller();
        $sellerOrder = $order instanceof SellerOrder ? $order : SellerOrder::findOrFail($order);

        $this->authorizeOrderOwnership($seller, $sellerOrder);

        $sellerOrder->load(['order.user', 'items.product', 'payouts', 'payout']);

        if ($request->wantsJson()) {
            return response()->json($sellerOrder);
        }

        return view('seller.orders.show', [
            'order'         => $sellerOrder,
            'sellerOrder'   => $sellerOrder,
            'sellerProfile' => $seller->sellerProfile,
        ]);
    }

    /**
     * Update order status with progression and transition validation.
     */
    public function updateStatus(Request $request, $order): RedirectResponse|JsonResponse
    {
        $seller = $this->getAuthenticatedSeller();
        $sellerOrder = $order instanceof SellerOrder ? $order : SellerOrder::findOrFail($order);

        $this->authorizeOrderOwnership($seller, $sellerOrder);

        $validated = $request->validate([
            'status'           => 'required|string|in:placed,pending,confirmed,processing,packed,ready_for_pickup,shipped,delivered,fulfilled,cancelled',
            'courier_name'     => 'nullable|string|max:150',
            'courier_bay'      => 'nullable|string|max:150',
            'courier_verified' => 'nullable|boolean',
        ]);

        $currentStatus = $sellerOrder->status;
        $targetStatus = $validated['status'];

        // Terminal state guards
        if (in_array($currentStatus, ['delivered', 'fulfilled'])) {
            return back()->with('error', 'Fulfilled orders cannot be modified.');
        }

        if ($currentStatus === 'cancelled') {
            return back()->with('error', 'Cancelled orders cannot be modified.');
        }

        // Progression validation: Cannot jump directly from placed/pending to fulfilled/delivered or ready_for_pickup
        if (in_array($currentStatus, ['placed', 'pending'])) {
            if (in_array($targetStatus, ['fulfilled', 'delivered', 'ready_for_pickup', 'packed', 'shipped'])) {
                return back()->with('error', 'Order must be processed before packaging or fulfillment.');
            }
        }

        // If target status is fulfillment, delegate to the atomic fulfillment routine
        if (in_array($targetStatus, ['fulfilled', 'delivered'])) {
            return $this->executeOrderFulfillment($request, $sellerOrder);
        }

        // Standard status update inside transaction
        DB::transaction(function () use ($sellerOrder, $targetStatus, $request) {
            $updates = [
                'status' => $targetStatus,
            ];

            if ($targetStatus === 'ready_for_pickup' || $targetStatus === 'packed') {
                if ($request->filled('courier_name')) {
                    $updates['courier_name'] = $request->input('courier_name');
                }
            }

            if ($targetStatus === 'shipped') {
                $updates['shipped_at'] = now();
            }

            $sellerOrder->update($updates);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated to ' . $targetStatus,
                'order'   => $sellerOrder->fresh(),
            ]);
        }

        return back()->with('success', 'Order status updated to ' . str_replace('_', ' ', $targetStatus));
    }

    /**
     * Handover verification protocol / mark order as fulfilled.
     */
    public function fulfill(Request $request, $order): RedirectResponse|JsonResponse
    {
        $seller = $this->getAuthenticatedSeller();
        $sellerOrder = $order instanceof SellerOrder ? $order : SellerOrder::findOrFail($order);

        $this->authorizeOrderOwnership($seller, $sellerOrder);

        return $this->executeOrderFulfillment($request, $sellerOrder);
    }

    /**
     * Handover alias endpoint for courier physical custody transfer.
     */
    public function handover(Request $request, $order): RedirectResponse|JsonResponse
    {
        return $this->fulfill($request, $order);
    }

    /**
     * Execute atomic order fulfillment, timestamp update, parent order sync, and payout generation.
     */
    protected function executeOrderFulfillment(Request $request, SellerOrder $sellerOrder): RedirectResponse|JsonResponse
    {
        $currentStatus = $sellerOrder->status;

        if (in_array($currentStatus, ['delivered', 'fulfilled'])) {
            return back()->with('error', 'Order is already marked as fulfilled.');
        }

        if ($currentStatus === 'cancelled') {
            return back()->with('error', 'Cannot fulfill a cancelled order.');
        }

        // Ensure order is at least processing or ready_for_pickup
        if (in_array($currentStatus, ['placed', 'pending'])) {
            return back()->with('error', 'Order must be processed and packed before fulfillment handover.');
        }

        DB::transaction(function () use ($sellerOrder, $request) {
            $now = now();
            $courier = $request->input('courier_name', $request->input('courier_bay', $sellerOrder->courier_name));

            // Financial breakdown
            $subtotal = (float) $sellerOrder->subtotal;
            $commissionRate = (float) ($sellerOrder->commission_rate ?: 10.00);
            $commissionAmount = round($subtotal * ($commissionRate / 100), 2);

            // Net payout calculation: respect existing payout_amount or compute standard
            if ((float) $sellerOrder->payout_amount > 0) {
                $netAmount = (float) $sellerOrder->payout_amount;
                $apmcCess = max(0, round($subtotal - $commissionAmount - $netAmount, 2));
            } else {
                $apmcCess = round($subtotal * 0.015, 2);
                $netAmount = max(0, round($subtotal - $commissionAmount - $apmcCess, 2));
            }

            // 1. Update SellerOrder
            $sellerOrder->update([
                'status'                => 'fulfilled',
                'delivered_at'          => $sellerOrder->delivered_at ?: $now,
                'handover_confirmed_at' => $sellerOrder->handover_confirmed_at ?: $now,
                'courier_name'          => $courier,
                'commission_amount'     => $commissionAmount,
                'payout_amount'         => $netAmount,
            ]);

            // 2. Generate or update Payout record
            $payout = Payout::firstOrNew(['seller_order_id' => $sellerOrder->id]);
            $payout->seller_id = $sellerOrder->seller_id;
            $payout->gross_amount = $subtotal;
            $payout->commission_amount = $commissionAmount;
            $payout->apmc_cess = $apmcCess;
            $payout->net_amount = $netAmount;
            if (!$payout->exists) {
                $payout->status = 'pending';
                $payout->payout_reference = 'PO-' . strtoupper(Str::random(10));
            }
            $payout->save();

            // 3. Parent Order status synchronization: if all sub-orders fulfilled, mark parent completed
            if ($sellerOrder->order_id) {
                $parentOrder = Order::find($sellerOrder->order_id);
                if ($parentOrder) {
                    $siblingOrders = SellerOrder::where('order_id', $parentOrder->id)->get();
                    $allFulfilled = $siblingOrders->every(function ($so) {
                        return in_array($so->status, ['fulfilled', 'delivered']);
                    });

                    if ($allFulfilled && !in_array($parentOrder->order_status, ['cancelled', 'completed'])) {
                        $parentOrder->update(['order_status' => 'completed']);
                    }
                }
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order fulfilled and handed over to courier successfully.',
                'order'   => $sellerOrder->fresh(['payout']),
            ]);
        }

        return back()->with('success', 'Order fulfilled and handed over to courier successfully.');
    }
}
