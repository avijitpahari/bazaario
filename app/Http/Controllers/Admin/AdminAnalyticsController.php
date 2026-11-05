<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Payout;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAnalyticsController extends Controller
{
    /**
     * Display the site-wide analytics dashboard.
     */
    public function index(Request $request)
    {
        $range = $request->get('range', '30d');
        [$startDate, $endDate, $periodDays] = $this->resolveDateRange($range);

        // Previous equivalent timeframe for comparative trend calculation
        $prevStartDate = $startDate ? (clone $startDate)->subDays($periodDays) : null;
        $prevEndDate = $startDate ? (clone $startDate)->subSecond() : null;

        // Base Query Scopes
        $orderQuery = Order::query();
        $sellerOrderQuery = SellerOrder::query();

        if ($startDate) {
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
            $sellerOrderQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Current Period Aggregates
        $sellerOrders = $sellerOrderQuery->with('seller.sellerProfile')->get();
        $orders = $orderQuery->get();

        $activeSellerOrders = $sellerOrders->reject(fn($so) => in_array($so->status, ['cancelled', 'returned']));

        $totalGmv = (float) $activeSellerOrders->sum('subtotal');
        $platformCommission = (float) $activeSellerOrders->sum('commission_amount');
        $netSellerPayouts = (float) $activeSellerOrders->sum('payout_amount');

        $totalOrdersCount = $orders->count();
        $deliveredOrdersCount = $sellerOrders->where('status', 'delivered')->count();
        $activeOrdersCount = $sellerOrders->whereIn('status', ['placed', 'pending', 'confirmed', 'packed', 'shipped'])->count();
        $cancelledOrdersCount = $sellerOrders->whereIn('status', ['cancelled', 'returned'])->count();

        $fulfillmentRate = ($totalOrdersCount - $cancelledOrdersCount) > 0 
            ? round(($deliveredOrdersCount / ($totalOrdersCount - $cancelledOrdersCount)) * 100, 1) 
            : 0;

        $completedOrdersCount = $activeSellerOrders->count();
        $aov = $completedOrdersCount > 0 ? round($totalGmv / $completedOrdersCount, 2) : 0;
        $takeRate = $totalGmv > 0 ? round(($platformCommission / $totalGmv) * 100, 2) : 8.5;

        // Previous Period Aggregates for % comparison
        $prevGmv = 0;
        $prevCommission = 0;
        $prevOrdersCount = 0;
        if ($prevStartDate && $prevEndDate) {
            $prevSellerOrders = SellerOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->whereNotIn('status', ['cancelled', 'returned'])
                ->get();
            $prevGmv = (float) $prevSellerOrders->sum('subtotal');
            $prevCommission = (float) $prevSellerOrders->sum('commission_amount');
            $prevOrdersCount = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        }

        $gmvGrowth = $prevGmv > 0 ? round((($totalGmv - $prevGmv) / $prevGmv) * 100, 1) : null;
        $commissionGrowth = $prevCommission > 0 ? round((($platformCommission - $prevCommission) / $prevCommission) * 100, 1) : null;
        $ordersGrowth = $prevOrdersCount > 0 ? round((($totalOrdersCount - $prevOrdersCount) / $prevOrdersCount) * 100, 1) : null;

        // Direct Retail vs. Live Auction Split
        $auctionOrders = $orders->where('order_type', 'auction');
        $directOrders = $orders->where('order_type', '!=', 'auction');

        $directCount = $directOrders->count();
        $auctionCount = $auctionOrders->count();
        $directGmv = (float) $directOrders->sum('total_amount');
        $auctionGmv = (float) $auctionOrders->sum('total_amount');

        $directShare = ($totalOrdersCount > 0) ? round(($directCount / $totalOrdersCount) * 100, 1) : 0;
        $auctionShare = ($totalOrdersCount > 0) ? round(($auctionCount / $totalOrdersCount) * 100, 1) : 0;

        // Category Revenue Contribution
        $categoryBreakdown = $this->calculateCategoryBreakdown($startDate, $endDate, $totalGmv);

        // Top Merchant Leaderboard
        $merchantLeaderboard = $this->calculateMerchantLeaderboard($sellerOrders);

        // Regional Fulfillment Hubs
        $regionalHubs = $orders->groupBy(function($order) {
            return !empty($order->delivery_state) ? trim($order->delivery_state) : (!empty($order->delivery_city) ? trim($order->delivery_city) : 'Direct Hub');
        })->map(function($group, $region) {
            return [
                'region' => $region,
                'orders_count' => $group->count(),
                'total_volume' => (float) $group->sum('total_amount'),
            ];
        })->sortByDesc('orders_count')->take(5)->values();

        // Escrow & Dispute Integrity Metrics
        $totalEscrowHeld = (float) SellerOrder::whereIn('status', ['placed', 'pending', 'confirmed', 'packed', 'shipped'])->sum('subtotal');
        $totalPayoutsReleased = (float) Payout::where('status', 'completed')->sum('net_amount');
        $disputeCount = OrderReturn::count();

        // Timeline Daily Chart Data
        $chartData = $this->generateTimelineChartData($startDate, $endDate, $sellerOrders, $orders);

        return view('admin.analytics.index', compact(
            'range',
            'totalGmv',
            'platformCommission',
            'netSellerPayouts',
            'totalOrdersCount',
            'deliveredOrdersCount',
            'activeOrdersCount',
            'cancelledOrdersCount',
            'fulfillmentRate',
            'aov',
            'takeRate',
            'gmvGrowth',
            'commissionGrowth',
            'ordersGrowth',
            'directCount',
            'auctionCount',
            'directGmv',
            'auctionGmv',
            'directShare',
            'auctionShare',
            'categoryBreakdown',
            'merchantLeaderboard',
            'regionalHubs',
            'totalEscrowHeld',
            'totalPayoutsReleased',
            'disputeCount',
            'chartData'
        ));
    }

    /**
     * Export platform analytics manifest to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $range = $request->get('range', '30d');
        [$startDate, $endDate] = $this->resolveDateRange($range);

        $filename = 'bazaario-platform-analytics-' . $range . '-' . now()->format('Y-m-d-His') . '.csv';

        $sellerOrderQuery = SellerOrder::query();
        if ($startDate) {
            $sellerOrderQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $sellerOrders = $sellerOrderQuery->get();

        $dailyStats = $sellerOrders->groupBy(function($so) {
            return $so->created_at->format('Y-m-d');
        })->map(function($dayOrders, $date) {
            $valid = $dayOrders->reject(fn($so) => in_array($so->status, ['cancelled', 'returned']));
            return [
                'date' => $date,
                'total_orders' => $dayOrders->count(),
                'fulfilled_orders' => $dayOrders->where('status', 'delivered')->count(),
                'gross_volume' => (float) $valid->sum('subtotal'),
                'platform_commission' => (float) $valid->sum('commission_amount'),
                'seller_payout' => (float) $valid->sum('payout_amount'),
            ];
        })->sortKeysDesc();

        return response()->streamDownload(function() use ($dailyStats, $range) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Bazaario Platform Analytics Manifest']);
            fputcsv($handle, ['Timeframe Presets', strtoupper($range)]);
            fputcsv($handle, ['Export Timestamp', now()->toIso8601String()]);
            fputcsv($handle, []);
            fputcsv($handle, ['Date', 'Total Orders', 'Fulfilled Orders', 'Gross GMV (INR)', 'Platform Commission (INR)', 'Net Seller Payout (INR)']);

            foreach ($dailyStats as $stat) {
                fputcsv($handle, [
                    $stat['date'],
                    $stat['total_orders'],
                    $stat['fulfilled_orders'],
                    number_format($stat['gross_volume'], 2, '.', ''),
                    number_format($stat['platform_commission'], 2, '.', ''),
                    number_format($stat['seller_payout'], 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Resolve date range presets.
     */
    private function resolveDateRange(string $range): array
    {
        $now = Carbon::now();
        return match ($range) {
            '7d' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay(), 7],
            '90d' => [$now->copy()->subDays(89)->startOfDay(), $now->copy()->endOfDay(), 90],
            '1y' => [$now->copy()->subDays(364)->startOfDay(), $now->copy()->endOfDay(), 365],
            'all' => [null, $now->copy()->endOfDay(), 365],
            default => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay(), 30], // '30d'
        };
    }

    /**
     * Calculate category revenue contribution.
     */
    private function calculateCategoryBreakdown(?Carbon $startDate, Carbon $endDate, float $totalGmv): array
    {
        $itemQuery = OrderItem::query()
            ->join('seller_orders', 'order_items.seller_order_id', '=', 'seller_orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereNotIn('seller_orders.status', ['cancelled', 'returned']);

        if ($startDate) {
            $itemQuery->whereBetween('seller_orders.created_at', [$startDate, $endDate]);
        }

        $categories = $itemQuery->select(
            'categories.id',
            'categories.name',
            DB::raw('SUM(order_items.total_price) as category_revenue'),
            DB::raw('SUM(order_items.quantity) as total_units')
        )
        ->groupBy('categories.id', 'categories.name')
        ->orderByDesc('category_revenue')
        ->take(6)
        ->get();

        $breakdown = [];
        foreach ($categories as $cat) {
            $rev = (float) $cat->category_revenue;
            $share = $totalGmv > 0 ? round(($rev / $totalGmv) * 100, 1) : 0;
            $breakdown[] = [
                'name' => $cat->name,
                'revenue' => $rev,
                'units' => (int) $cat->total_units,
                'share' => $share,
            ];
        }

        return $breakdown;
    }

    /**
     * Calculate top merchant leaderboard.
     */
    private function calculateMerchantLeaderboard($sellerOrders): array
    {
        return $sellerOrders->groupBy('seller_id')
            ->map(function($merchantOrders, $sellerId) {
                $seller = $merchantOrders->first()->seller;
                $profile = $seller?->sellerProfile;
                $storeName = $profile?->shop_name ?? ($profile?->business_name ?? ($seller?->name ?? 'Merchant #' . $sellerId));
                $avatar = $seller?->avatar;

                $validOrders = $merchantOrders->reject(fn($so) => in_array($so->status, ['cancelled', 'returned']));
                $grossSales = (float) $validOrders->sum('subtotal');
                $commission = (float) $validOrders->sum('commission_amount');
                $deliveredCount = $merchantOrders->where('status', 'delivered')->count();

                return [
                    'seller_id' => $sellerId,
                    'store_name' => $storeName,
                    'seller_name' => $seller?->name ?? 'N/A',
                    'avatar' => $avatar,
                    'rating' => (float) ($profile?->trust_rating ?? 4.8),
                    'total_orders' => $merchantOrders->count(),
                    'delivered_orders' => $deliveredCount,
                    'gross_sales' => $grossSales,
                    'commission_paid' => $commission,
                ];
            })
            ->sortByDesc('gross_sales')
            ->take(8)
            ->values()
            ->toArray();
    }

    /**
     * Generate daily continuous timeline data for Chart.js.
     */
    private function generateTimelineChartData(?Carbon $startDate, Carbon $endDate, $sellerOrders, $orders): array
    {
        $start = $startDate ? $startDate->copy() : Carbon::now()->subDays(29)->startOfDay();
        $period = CarbonPeriod::create($start, '1 day', $endDate);

        $dailyGmv = [];
        $dailyCommission = [];
        $dailyOrders = [];
        $labels = [];

        // Pre-group by Y-m-d
        $groupedSellerOrders = $sellerOrders->reject(fn($so) => in_array($so->status, ['cancelled', 'returned']))
            ->groupBy(fn($so) => $so->created_at->format('Y-m-d'));
        $groupedOrders = $orders->groupBy(fn($o) => $o->created_at->format('Y-m-d'));

        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $label = $date->format('M d');
            $labels[] = $label;

            $soGroup = $groupedSellerOrders->get($key);
            $oGroup = $groupedOrders->get($key);

            $dailyGmv[] = $soGroup ? round((float) $soGroup->sum('subtotal'), 2) : 0;
            $dailyCommission[] = $soGroup ? round((float) $soGroup->sum('commission_amount'), 2) : 0;
            $dailyOrders[] = $oGroup ? $oGroup->count() : 0;
        }

        return [
            'labels' => $labels,
            'gmv' => $dailyGmv,
            'commission' => $dailyCommission,
            'orders' => $dailyOrders,
        ];
    }
}
