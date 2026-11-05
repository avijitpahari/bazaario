<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Payout;
use App\Models\Product;
use App\Models\SellerOrder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerAnalyticsController extends Controller
{
    /**
     * Display the seller store analytics dashboard.
     * Enforces strict multi-tenant isolation scoped to the authenticated seller.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        $profile = $user->sellerProfile;
        if ($profile && $profile->status !== 'approved') {
            return redirect()->route('seller.pending')
                ->with('warning', 'Your seller account is awaiting verification approval.');
        }

        $sellerId = $user->id;
        $range = $request->get('range', '30d');
        [$startDate, $endDate, $periodDays] = $this->resolveDateRange($range);

        // Previous equivalent timeframe for comparative trend calculation
        $prevStartDate = $startDate ? (clone $startDate)->subDays($periodDays) : null;
        $prevEndDate = $startDate ? (clone $startDate)->subSecond() : null;

        // Base Query Scopes
        $orderQuery = SellerOrder::where('seller_id', $sellerId);
        if ($startDate) {
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $sellerOrders = $orderQuery->get();

        $activeOrders = $sellerOrders->reject(fn($so) => in_array($so->status, ['cancelled', 'returned']));

        // Core Financial & Order Metrics
        $storeGrossSales = (float) $activeOrders->sum('subtotal');
        $netEarnings = (float) $activeOrders->sum('payout_amount');
        $commissionIncurred = (float) $activeOrders->sum('commission_amount');

        $totalOrdersCount = $sellerOrders->count();
        $deliveredCount = $sellerOrders->where('status', 'delivered')->count();
        $shippedCount = $sellerOrders->where('status', 'shipped')->count();
        $processingCount = $sellerOrders->whereIn('status', ['placed', 'pending', 'confirmed', 'packed'])->count();
        $cancelledCount = $sellerOrders->whereIn('status', ['cancelled', 'returned'])->count();

        $validCompletedOrders = $activeOrders->count();
        $fulfillmentRate = ($totalOrdersCount - $cancelledCount) > 0
            ? round(($deliveredCount / ($totalOrdersCount - $cancelledCount)) * 100, 1)
            : 0;

        $aov = $validCompletedOrders > 0 ? round($storeGrossSales / $validCompletedOrders, 2) : 0;

        // Calculate Units Sold and ASP (Average Selling Price)
        $orderItemQuery = OrderItem::whereIn('seller_order_id', $activeOrders->pluck('id'));
        $totalUnitsSold = (int) $orderItemQuery->sum('quantity');
        $asp = $totalUnitsSold > 0 ? round($storeGrossSales / $totalUnitsSold, 2) : 0;

        // Previous Period Aggregates for % comparison
        $prevGrossSales = 0;
        $prevNetEarnings = 0;
        $prevOrdersCount = 0;
        if ($prevStartDate && $prevEndDate) {
            $prevOrders = SellerOrder::where('seller_id', $sellerId)
                ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->whereNotIn('status', ['cancelled', 'returned'])
                ->get();
            $prevGrossSales = (float) $prevOrders->sum('subtotal');
            $prevNetEarnings = (float) $prevOrders->sum('payout_amount');
            $prevOrdersCount = $prevOrders->count();
        }

        $salesGrowth = $prevGrossSales > 0 ? round((($storeGrossSales - $prevGrossSales) / $prevGrossSales) * 100, 1) : null;
        $earningsGrowth = $prevNetEarnings > 0 ? round((($netEarnings - $prevNetEarnings) / $prevNetEarnings) * 100, 1) : null;
        $ordersGrowth = $prevOrdersCount > 0 ? round((($totalOrdersCount - $prevOrdersCount) / $prevOrdersCount) * 100, 1) : null;

        // Payout Ledger Health
        $readyEscrowPayout = (float) SellerOrder::where('seller_id', $sellerId)
            ->where('status', 'delivered')
            ->whereDoesntHave('payout', fn($q) => $q->where('status', 'completed'))
            ->sum('payout_amount');
        $clearedPayouts = (float) Payout::where('seller_id', $sellerId)
            ->where('status', 'completed')
            ->sum('net_amount');

        // Top 5 Performing Products
        $topProducts = $this->calculateTopProducts($sellerId, $startDate, $endDate);

        // Timeline Daily Chart Data
        $chartData = $this->generateTimelineChartData($startDate, $endDate, $sellerOrders);

        // Pipeline Status Distribution
        $pipeline = [
            'delivered' => $deliveredCount,
            'shipped' => $shippedCount,
            'processing' => $processingCount,
            'cancelled' => $cancelledCount,
        ];

        return view('seller.analytics.index', compact(
            'range',
            'storeGrossSales',
            'netEarnings',
            'commissionIncurred',
            'totalOrdersCount',
            'deliveredCount',
            'processingCount',
            'cancelledCount',
            'fulfillmentRate',
            'aov',
            'asp',
            'totalUnitsSold',
            'salesGrowth',
            'earningsGrowth',
            'ordersGrowth',
            'readyEscrowPayout',
            'clearedPayouts',
            'topProducts',
            'pipeline',
            'chartData',
            'profile'
        ));
    }

    /**
     * Export seller store analytics manifest to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();
        abort_unless($user && $user->role === 'seller', 403);

        $sellerId = $user->id;
        $range = $request->get('range', '30d');
        [$startDate, $endDate] = $this->resolveDateRange($range);

        $filename = 'bazaario-seller-analytics-' . $range . '-' . now()->format('Y-m-d-His') . '.csv';

        $orderQuery = SellerOrder::where('seller_id', $sellerId);
        if ($startDate) {
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $sellerOrders = $orderQuery->get();

        $dailyStats = $sellerOrders->groupBy(function($so) {
            return $so->created_at->format('Y-m-d');
        })->map(function($dayOrders, $date) {
            $valid = $dayOrders->reject(fn($so) => in_array($so->status, ['cancelled', 'returned']));
            return [
                'date' => $date,
                'total_orders' => $dayOrders->count(),
                'delivered' => $dayOrders->where('status', 'delivered')->count(),
                'gross_sales' => (float) $valid->sum('subtotal'),
                'commission_fees' => (float) $valid->sum('commission_amount'),
                'net_earnings' => (float) $valid->sum('payout_amount'),
            ];
        })->sortKeysDesc();

        return response()->streamDownload(function() use ($dailyStats, $range, $user) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Bazaario Merchant Store Analytics Report']);
            fputcsv($handle, ['Merchant Name', $user->name]);
            fputcsv($handle, ['Timeframe Presets', strtoupper($range)]);
            fputcsv($handle, ['Export Timestamp', now()->toIso8601String()]);
            fputcsv($handle, []);
            fputcsv($handle, ['Date', 'Orders Count', 'Delivered Orders', 'Gross Sales (INR)', 'Commission Fees (INR)', 'Net Earnings (INR)']);

            foreach ($dailyStats as $stat) {
                fputcsv($handle, [
                    $stat['date'],
                    $stat['total_orders'],
                    $stat['delivered'],
                    number_format($stat['gross_sales'], 2, '.', ''),
                    number_format($stat['commission_fees'], 2, '.', ''),
                    number_format($stat['net_earnings'], 2, '.', ''),
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
     * Calculate top 5 performing products for this seller.
     */
    private function calculateTopProducts(int $sellerId, ?Carbon $startDate, Carbon $endDate): array
    {
        $query = OrderItem::query()
            ->join('seller_orders', 'order_items.seller_order_id', '=', 'seller_orders.id')
            ->where('seller_orders.seller_id', $sellerId)
            ->whereNotIn('seller_orders.status', ['cancelled', 'returned']);

        if ($startDate) {
            $query->whereBetween('seller_orders.created_at', [$startDate, $endDate]);
        }

        $items = $query->select(
            'order_items.product_id',
            'order_items.product_name',
            'order_items.product_image',
            DB::raw('SUM(order_items.quantity) as total_units'),
            DB::raw('SUM(order_items.total_price) as total_revenue')
        )
        ->groupBy('order_items.product_id', 'order_items.product_name', 'order_items.product_image')
        ->orderByDesc('total_revenue')
        ->take(5)
        ->get();

        $top = [];
        foreach ($items as $item) {
            $product = Product::find($item->product_id);
            $top[] = [
                'id' => $item->product_id,
                'name' => $item->product_name,
                'image' => $item->product_image ? (str_starts_with($item->product_image, 'http') ? $item->product_image : asset('storage/' . $item->product_image)) : asset('images/products/keyboard_1.jpg'),
                'units' => (int) $item->total_units,
                'revenue' => (float) $item->total_revenue,
                'current_stock' => $product?->stock_quantity ?? 0,
                'is_low_stock' => ($product?->stock_quantity ?? 0) <= 5,
            ];
        }

        return $top;
    }

    /**
     * Generate daily continuous timeline data for Chart.js.
     */
    private function generateTimelineChartData(?Carbon $startDate, Carbon $endDate, $sellerOrders): array
    {
        $start = $startDate ? $startDate->copy() : Carbon::now()->subDays(29)->startOfDay();
        $period = CarbonPeriod::create($start, '1 day', $endDate);

        $dailySales = [];
        $dailyEarnings = [];
        $dailyOrders = [];
        $labels = [];

        $grouped = $sellerOrders->reject(fn($so) => in_array($so->status, ['cancelled', 'returned']))
            ->groupBy(fn($so) => $so->created_at->format('Y-m-d'));

        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $label = $date->format('M d');
            $labels[] = $label;

            $group = $grouped->get($key);

            $dailySales[] = $group ? round((float) $group->sum('subtotal'), 2) : 0;
            $dailyEarnings[] = $group ? round((float) $group->sum('payout_amount'), 2) : 0;
            $dailyOrders[] = $group ? $group->count() : 0;
        }

        return [
            'labels' => $labels,
            'sales' => $dailySales,
            'earnings' => $dailyEarnings,
            'orders' => $dailyOrders,
        ];
    }
}
