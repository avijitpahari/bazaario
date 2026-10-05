<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\SellerOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerPayoutController extends Controller
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
     * Ensure the payout belongs to the authenticated seller.
     */
    protected function authorizePayoutOwnership(User $seller, Payout $payout): void
    {
        if ((int) $payout->seller_id !== (int) $seller->id) {
            abort(403, 'Unauthorized access to this payout record.');
        }
    }

    /**
     * Display seller payouts ledger and financial summary workspace.
     */
    public function index(Request $request): View
    {
        $seller = $this->getAuthenticatedSeller();
        $sellerId = $seller->id;
        $sellerProfile = $seller->sellerProfile;

        $status = $request->query('status', 'all');
        $search = $request->query('search');
        $selectedId = $request->query('payout_id', $request->query('selected'));

        // Status tab counts strictly isolated to authenticated seller
        $allPayouts = Payout::forSeller($sellerId)->get(['id', 'status']);
        $statusCounts = [
            'all'        => $allPayouts->count(),
            'processing' => $allPayouts->where('status', 'processing')->count(),
            'paid'       => $allPayouts->where('status', 'paid')->count(),
            'pending'    => $allPayouts->where('status', 'pending')->count(),
            'failed'     => $allPayouts->where('status', 'failed')->count(),
        ];

        // 4 Summary KPIs
        // Lifetime Revenue: gross amount of all payouts or fulfilled orders
        $payoutGross = (float) Payout::forSeller($sellerId)->sum('gross_amount');
        $soGross = (float) SellerOrder::forSeller($sellerId)->whereIn('status', ['delivered', 'fulfilled'])->sum('subtotal');
        $lifetimeRevenue = max($payoutGross, $soGross);

        // Marketplace Commission (Fixed 10%)
        $payoutComm = (float) Payout::forSeller($sellerId)->sum('commission_amount');
        $soComm = (float) SellerOrder::forSeller($sellerId)->whereIn('status', ['delivered', 'fulfilled'])->sum('commission_amount');
        $lifetimeCommission = max($payoutComm, $soComm);

        // Total Settled (Paid)
        $totalSettled = (float) Payout::forSeller($sellerId)->where('status', 'paid')->sum('net_amount');

        // Pending / Processing Escrow
        $pendingProcessing = (float) Payout::forSeller($sellerId)->whereIn('status', ['pending', 'processing'])->sum('net_amount');
        if ($pendingProcessing == 0 && $allPayouts->isEmpty()) {
            $pendingProcessing = (float) SellerOrder::forSeller($sellerId)
                ->whereIn('status', ['placed', 'pending', 'processing', 'packed', 'ready_for_pickup'])
                ->sum('payout_amount');
        }

        $kpis = [
            'lifetime_revenue'    => $lifetimeRevenue,
            'platform_commission' => $lifetimeCommission,
            'total_settled'       => $totalSettled,
            'pending_processing'  => $pendingProcessing,
        ];

        // Bank details from seller profile
        $rawAccount = $sellerProfile?->bank_account_number;
        $maskedAccount = (!empty($rawAccount) && strlen($rawAccount) >= 4)
            ? '•••• ' . substr($rawAccount, -4)
            : 'Not configured';
        $bankIfsc = $sellerProfile?->bank_ifsc ?: 'Not configured';
        $bankName = !empty($rawAccount) ? 'HDFC Bank' : 'Not configured';

        // Upcoming Settlement details
        $nextSettlementDate = Carbon::now()->next(Carbon::FRIDAY)->format('l, F j, Y');
        $upcomingSettlement = [
            'amount'         => $pendingProcessing,
            'date'           => $nextSettlementDate,
            'bank_name'      => $bankName,
            'masked_account' => $maskedAccount,
            'ifsc'           => $bankIfsc,
            'status'         => 'Batch Processing In Progress',
        ];

        // Base Query
        $query = Payout::forSeller($sellerId)
            ->with(['sellerOrder.order.user', 'sellerOrder.items']);

        // Apply status filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Apply search filter (payout reference, order number)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('payout_reference', 'like', "%{$search}%")
                    ->orWhereHas('sellerOrder', function ($soq) use ($search) {
                        $soq->where('seller_order_number', 'like', "%{$search}%");
                    });
            });
        }

        $payouts = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Selected payout for detail inspector pane
        $selectedPayout = null;
        if (!empty($selectedId)) {
            $selectedPayout = Payout::forSeller($sellerId)
                ->with(['sellerOrder.order.user', 'sellerOrder.items'])
                ->find($selectedId);
        }

        if (!$selectedPayout && $payouts->isNotEmpty()) {
            $selectedPayout = $payouts->first();
        }

        return view('seller.payouts.index', [
            'payouts'            => $payouts,
            'selectedPayout'     => $selectedPayout,
            'upcomingSettlement' => $upcomingSettlement,
            'kpis'               => $kpis,
            'statusCounts'       => $statusCounts,
            'counts'             => $statusCounts,
            'currentStatus'      => $status,
            'searchQuery'        => $search,
            'sellerProfile'      => $sellerProfile,
            'sellerUser'         => $seller,
            'sellerId'           => $sellerId,
        ]);
    }

    /**
     * Display a single payout details page.
     */
    public function show(Request $request, $payout): View|JsonResponse
    {
        $seller = $this->getAuthenticatedSeller();
        $payoutModel = $payout instanceof Payout ? $payout : Payout::findOrFail($payout);

        $this->authorizePayoutOwnership($seller, $payoutModel);

        $payoutModel->load(['sellerOrder.order.user', 'sellerOrder.items', 'seller.sellerProfile']);

        if ($request->wantsJson()) {
            return response()->json($payoutModel);
        }

        return view('seller.payouts.show', [
            'payout'        => $payoutModel,
            'sellerProfile' => $seller->sellerProfile,
        ]);
    }
}
