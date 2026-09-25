<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $status = $request->query('status', 'all');

        $query = $user->orders()->with(['sellerOrders.items'])->latest('placed_at');

        if ($status !== 'all') {
            $query->where('order_status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        $statusCounts = [
            'all'        => $user->orders()->count(),
            'pending'    => $user->orders()->where('order_status', 'pending')->count(),
            'processing' => $user->orders()->where('order_status', 'processing')->count(),
            'completed'  => $user->orders()->where('order_status', 'completed')->count(),
            'cancelled'  => $user->orders()->where('order_status', 'cancelled')->count(),
        ];

        return view('user.account.orders.index', compact('user', 'orders', 'status', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $user = Auth::user();

        // Ensure the order belongs to the authenticated user
        abort_unless($order->user_id === $user->id, 403);

        $order->load(['sellerOrders.items', 'coupon']);

        return view('user.account.orders.show', compact('user', 'order'));
    }
}
