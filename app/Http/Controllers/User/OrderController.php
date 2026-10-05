<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $status = $request->query('status', 'all');

        $query = $user->orders()->with(['sellerOrders.items.product', 'sellerOrders.seller.sellerProfile'])->latest('placed_at');

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

        $order->load(['sellerOrders.items.product.images', 'sellerOrders.seller.sellerProfile', 'coupon', 'payments']);

        return view('user.account.orders.show', compact('user', 'order'));
    }

    public function cancel(Request $request, Order $order)
    {
        $user = Auth::user();

        abort_unless($order->user_id === $user->id, 403);

        if (!in_array($order->order_status, ['pending', 'processing'])) {
            return back()->with('error', 'Orders in ' . ucfirst($order->order_status) . ' status cannot be cancelled.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['order_status' => 'cancelled']);
            $order->sellerOrders()->update(['status' => 'cancelled']);

            // Restore product stock for all cancelled line items
            $order->load(['sellerOrders.items']);
            foreach ($order->sellerOrders as $sellerOrder) {
                foreach ($sellerOrder->items as $item) {
                    if ($item->product_id) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }

            // Update payment record if applicable
            $order->payments()->where('status', 'pending')->update(['status' => 'failed']);
            $order->payments()->where('status', 'paid')->update(['status' => 'refunded']);
        });

        return back()->with('success', 'Order #' . $order->order_number . ' has been cancelled and product stock has been restored.');
    }

    public function reorder(Request $request, Order $order)
    {
        $user = Auth::user();

        abort_unless($order->user_id === $user->id, 403);

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $order->load(['sellerOrders.items.product']);

        $addedCount = 0;
        foreach ($order->sellerOrders as $sellerOrder) {
            foreach ($sellerOrder->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product && $product->status === 'active' && $product->stock > 0) {
                        $qty = min($item->quantity, $product->stock);
                        $cartItem = $cart->items()->where('product_id', $product->id)->first();
                        if ($cartItem) {
                            $newQty = min($cartItem->quantity + $qty, $product->stock);
                            $cartItem->update([
                                'quantity'   => $newQty,
                                'unit_price' => $product->price,
                            ]);
                        } else {
                            $cart->items()->create([
                                'product_id' => $product->id,
                                'quantity'   => $qty,
                                'unit_price' => $product->price,
                            ]);
                        }
                        $addedCount++;
                    }
                }
            }
        }

        if ($addedCount > 0) {
            return redirect()->route('cart.index')->with('success', "{$addedCount} item(s) from past order added to your cart!");
        }

        return redirect()->route('cart.index')->with('error', 'None of the items from this order are currently available in stock.');
    }
}
