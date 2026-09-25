<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── Greeting ────────────────────────────────────────────────────────
        $hour = (int) now()->format('G');
        $greeting = match (true) {
            $hour >= 5  && $hour <= 11 => 'Good morning',
            $hour >= 12 && $hour <= 16 => 'Good afternoon',
            $hour >= 17 && $hour <= 20 => 'Good evening',
            default                    => 'Good night',
        };

        // ── Stats ────────────────────────────────────────────────────────────
        $totalOrders  = $user->orders()->count();
        $inTransit    = $user->orders()->where('order_status', 'processing')->count();
        $wishlistCount = $user->wishlist ? $user->wishlist->items()->count() : 0;
        $activeBids   = 0; // Placeholder — extend with AuctionBid model later
        $availableCoupons = DB::table('coupons')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        // ── Recent Orders ────────────────────────────────────────────────────
        $recentOrders = $user->orders()
            ->with(['sellerOrders.items'])
            ->latest('placed_at')
            ->limit(5)
            ->get();

        // ── Total spent ──────────────────────────────────────────────────────
        $totalSpent = $user->orders()->where('payment_status', 'paid')->sum('total_amount');

        return view('user.account.dashboard', compact(
            'user',
            'greeting',
            'totalOrders',
            'inTransit',
            'wishlistCount',
            'activeBids',
            'availableCoupons',
            'recentOrders',
            'totalSpent'
        ));
    }
}