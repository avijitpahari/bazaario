<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\SellerOrder;
use App\Models\SiteSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
     * Compute comprehensive payout summary metrics for the seller.
     */
    protected function computePayoutSummary(User $seller): array
    {
        $sellerId = $seller->id;
        $sellerProfile = $seller->sellerProfile;

        // Status tab counts strictly isolated to authenticated seller
        $allPayouts = Payout::forSeller($sellerId)->get();
        $paidPayouts = $allPayouts->where('status', 'paid');
        $processingPayouts = $allPayouts->where('status', 'processing');
        $pendingPayouts = $allPayouts->where('status', 'pending');
        $failedPayouts = $allPayouts->where('status', 'failed');

        $statusCounts = [
            'all'        => $allPayouts->count(),
            'processing' => $processingPayouts->count(),
            'paid'       => $paidPayouts->count(),
            'pending'    => $pendingPayouts->count(),
            'failed'     => $failedPayouts->count(),
        ];

        // 4 Summary KPIs
        // Lifetime Revenue: gross amount of all payouts or fulfilled orders
        $payoutGross = (float) $allPayouts->sum('gross_amount');
        $soGross = (float) SellerOrder::forSeller($sellerId)->whereIn('status', ['delivered', 'fulfilled'])->sum('subtotal');
        $lifetimeRevenue = max($payoutGross, $soGross);

        // Marketplace Commission (Fixed 10%)
        $payoutComm = (float) $allPayouts->sum('commission_amount');
        $soComm = (float) SellerOrder::forSeller($sellerId)->whereIn('status', ['delivered', 'fulfilled'])->sum('commission_amount');
        $lifetimeCommission = max($payoutComm, $soComm);

        // Total Settled (Paid)
        $totalSettled = (float) $paidPayouts->sum('net_amount');

        // Pending / Processing Escrow from existing payouts
        $pendingProcessing = (float) $allPayouts->whereIn('status', ['pending', 'processing'])->sum('net_amount');

        // Also check if there are fulfilled/delivered orders that have not yet had a paid payout
        $unsettledFulfilled = SellerOrder::forSeller($sellerId)
            ->whereIn('status', ['fulfilled', 'delivered'])
            ->where(function ($q) {
                $q->whereDoesntHave('payout')
                  ->orWhereHas('payout', function ($sub) {
                      $sub->where('status', '!=', 'paid');
                  });
            })
            ->get();

        foreach ($unsettledFulfilled as $ord) {
            $hasPayoutInAll = $allPayouts->contains(function ($p) use ($ord) {
                return $p->seller_order_id == $ord->id && in_array($p->status, ['pending', 'processing']);
            });
            if (!$hasPayoutInAll) {
                $pendingProcessing += (float) ($ord->payout_amount > 0 ? $ord->payout_amount : $ord->net_payout_calculated);
            }
        }

        $totalCess = (float) $allPayouts->sum('apmc_cess');
        $avgPayout = $paidPayouts->count() > 0 ? (float) $paidPayouts->avg('net_amount') : 0.0;

        // Bank details from seller profile
        $rawAccount = $sellerProfile?->bank_account_number;
        $hasBankDetails = (!empty($rawAccount) && strlen($rawAccount) >= 4);
        $maskedAccount = $hasBankDetails
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

        $kpis = [
            'lifetime_revenue'    => $lifetimeRevenue,
            'platform_commission' => $lifetimeCommission,
            'total_settled'       => $totalSettled,
            'pending_processing'  => $pendingProcessing,
        ];

        $effectiveRate = $sellerProfile ? $sellerProfile->getEffectiveCommissionRate() : SiteSetting::getCommissionRate(10.0);
        $isCustomRate = ($sellerProfile && $sellerProfile->commission_rate !== null);

        $tierLabel = $isCustomRate
            ? 'Custom Agreement'
            : ($sellerProfile?->seller_type === 'farmer' ? 'Tier 1 Prime Farmer' : 'Tier 1 Prime Merchant');

        $tierSubtitle = $isCustomRate
            ? 'Negotiated rate • Custom fee structure'
            : ($sellerProfile?->seller_type === 'farmer' ? 'Prime Farmer rate • No hidden fees' : 'Platform Standard rate • No hidden fees');

        $summary = [
            'lifetime_revenue'         => $lifetimeRevenue,
            'platform_commission'      => $lifetimeCommission,
            'total_settled'            => $totalSettled,
            'pending_processing'       => $pendingProcessing,
            'total_batches'            => $allPayouts->count(),
            'paid_count'               => $paidPayouts->count(),
            'processing_count'         => $processingPayouts->count(),
            'pending_count'            => $pendingPayouts->count(),
            'failed_count'             => $failedPayouts->count(),
            'avg_payout'               => $avgPayout,
            'total_apmc_cess'          => $totalCess,
            'commission_tier'          => $tierLabel,
            'commission_tier_subtitle' => $tierSubtitle,
            'commission_rate'          => $effectiveRate,
            'commission_rate_string'   => number_format($effectiveRate, 1) . '%',
            'is_custom_commission'     => $isCustomRate,
            'gst_exempt'               => true,
            'next_cycle_date'          => $nextSettlementDate,
            'bank_name'                => $bankName,
            'masked_account'           => $maskedAccount,
            'bank_ifsc'                => $bankIfsc,
            'has_bank_details'         => $hasBankDetails,
        ];

        return [
            'kpis'               => $kpis,
            'statusCounts'       => $statusCounts,
            'upcomingSettlement' => $upcomingSettlement,
            'summary'            => $summary,
        ];
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

        $summaryData = $this->computePayoutSummary($seller);
        $statusCounts = $summaryData['statusCounts'];
        $kpis = $summaryData['kpis'];
        $upcomingSettlement = $summaryData['upcomingSettlement'];
        $payoutSummary = $summaryData['summary'];

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
            'payoutSummary'      => $payoutSummary,
            'summary'            => $payoutSummary,
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
     * Display dedicated seller payout summary statement workspace.
     */
    public function summary(Request $request): View|JsonResponse
    {
        $seller = $this->getAuthenticatedSeller();
        $summaryData = $this->computePayoutSummary($seller);

        if ($request->wantsJson()) {
            return response()->json($summaryData);
        }

        $recentPayouts = Payout::forSeller($seller->id)
            ->with(['sellerOrder.order.user', 'sellerOrder.items'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('seller.payouts.summary', [
            'seller'             => $seller,
            'sellerUser'         => $seller,
            'sellerProfile'      => $seller->sellerProfile,
            'sellerId'           => $seller->id,
            'kpis'               => $summaryData['kpis'],
            'summary'            => $summaryData['summary'],
            'payoutSummary'      => $summaryData['summary'],
            'statusCounts'       => $summaryData['statusCounts'],
            'counts'             => $summaryData['statusCounts'],
            'upcomingSettlement' => $summaryData['upcomingSettlement'],
            'recentPayouts'      => $recentPayouts,
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

    /**
     * Demo Action: Instantly settle pending payout amounts to the merchant's bank account.
     */
    public function demoSettle(Request $request): RedirectResponse
    {
        $seller = $this->getAuthenticatedSeller();
        $sellerId = $seller->id;
        $profile = $seller->sellerProfile;

        // Auto-link test bank if missing so demo always succeeds seamlessly
        if ($profile && empty($profile->bank_account_number)) {
            $profile->bank_account_number = '918010023456789';
            $profile->bank_ifsc = 'HDFC0001234';
            $docStatuses = $profile->document_statuses ?? [];
            $docStatuses['bank'] = 'uploaded';
            $docStatuses['bank_verified'] = true;
            $profile->document_statuses = $docStatuses;
            $profile->save();
        }

        // 1. Check if any pending or processing payouts exist for this seller
        $pendingPayouts = Payout::forSeller($sellerId)->whereIn('status', ['pending', 'processing'])->get();

        // 2. If no pending payout record exists, check if there are fulfilled seller orders without paid payouts
        if ($pendingPayouts->isEmpty()) {
            $unsettledOrders = SellerOrder::forSeller($sellerId)
                ->whereIn('status', ['fulfilled', 'delivered'])
                ->where(function ($q) {
                    $q->whereDoesntHave('payout')
                      ->orWhereHas('payout', function ($sub) {
                          $sub->where('status', '!=', 'paid');
                      });
                })
                ->get();

            foreach ($unsettledOrders as $order) {
                $subtotal = (float) $order->subtotal;
                $comm = (float) $order->commission_amount;
                $cess = (float) $order->apmc_cess;
                $net = (float) ($order->payout_amount > 0 ? $order->payout_amount : $order->net_payout_calculated);

                $payout = Payout::firstOrNew(['seller_order_id' => $order->id]);
                $payout->seller_id = $sellerId;
                $payout->gross_amount = $subtotal;
                $payout->commission_amount = $comm;
                $payout->apmc_cess = $cess;
                $payout->net_amount = $net;
                if ($payout->status !== 'paid') {
                    $payout->status = 'pending';
                    $payout->payout_reference = $payout->payout_reference ?: ('PO-' . strtoupper(Str::random(10)));
                    $payout->save();
                    $pendingPayouts->push($payout);
                }
            }
        }

        // 3. If there are NO unsettled payouts or orders, stop here and do not create repeated fake vouchers
        if ($pendingPayouts->isEmpty()) {
            return redirect()->back(fallback: route('seller.payouts.index'))
                ->with('info', 'No unsettled amount available. All earnings have already been settled to your bank account.');
        }

        $totalSettledAmount = 0;
        $primaryRef = '';

        foreach ($pendingPayouts as $payout) {
            $ref = 'DEMO-NEFT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            if (empty($primaryRef)) {
                $primaryRef = $ref;
            }
            $payout->update([
                'status'           => 'paid',
                'paid_at'          => now(),
                'payout_reference' => $ref,
            ]);
            $totalSettledAmount += (float) $payout->net_amount;
        }

        $destAccount = $profile?->bank_account_number ? '•••• ' . substr($profile->bank_account_number, -4) : '•••• 6789';
        $bankName = $profile?->bank_name ?: 'HDFC Bank';

        return redirect()->back(fallback: route('seller.payouts.index'))->with('success', "🎉 Demo Settlement Completed! Successfully disbursed ₹" . number_format($totalSettledAmount, 2) . " to {$bankName} ({$destAccount}). Transfer Ref: {$primaryRef}");
    }

    /**
     * Demo Action: Settle an individual pending payout.
     */
    public function demoSettleSingle(Request $request, $payout): RedirectResponse
    {
        $seller = $this->getAuthenticatedSeller();
        $payoutRecord = $payout instanceof Payout ? $payout : Payout::findOrFail($payout);
        $this->authorizePayoutOwnership($seller, $payoutRecord);

        if ($payoutRecord->status === 'paid') {
            return redirect()->back(fallback: route('seller.payouts.index'))
                ->with('info', "This payout ({$payoutRecord->payout_number}) has already been settled to your bank account.");
        }

        $ref = 'DEMO-NEFT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $payoutRecord->update([
            'status'           => 'paid',
            'paid_at'          => now(),
            'payout_reference' => $ref,
        ]);

        return redirect()->back(fallback: route('seller.payouts.index'))->with('success', "🎉 Demo Settlement of ₹" . number_format($payoutRecord->net_amount, 2) . " released! Transfer Ref: {$ref}");
    }
}
