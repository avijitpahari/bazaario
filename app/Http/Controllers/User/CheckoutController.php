<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = $user->cart()->with(['items.product.images'])->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $addresses = $user->addresses()->latest()->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $subtotal      = $cart->items->sum(fn ($item) => ($item->product->price ?? 0) * $item->quantity);
        $shipping      = $subtotal > 0 ? 99 : 0;
        $discount      = 0;
        $appliedCoupon = null;

        if (session('coupon')) {
            $coupon = Coupon::where('code', session('coupon.code'))->first();
            if ($coupon) {
                $appliedCoupon = $coupon;
                $discount = $coupon->discount_type === 'percentage'
                    ? ($subtotal * $coupon->discount_value / 100)
                    : $coupon->discount_value;
                $discount = min($discount, $subtotal);
            }
        }

        $total = max(0, $subtotal + $shipping - $discount);

        return view('user.checkout.index', compact(
            'user', 'cart', 'addresses', 'defaultAddress',
            'subtotal', 'shipping', 'discount', 'total', 'appliedCoupon'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address_id'       => 'required|exists:addresses,id',
            'payment_method'   => 'required|in:cod,card,upi,net_banking,wallet',
            'notes'            => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $cart = $user->cart()->with(['items.product'])->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $address = $user->addresses()->findOrFail($data['address_id']);

        $subtotal  = $cart->items->sum(fn ($i) => ($i->product->price ?? 0) * $i->quantity);
        $shipping  = 99;
        $discount  = 0;
        $couponId  = null;

        if (session('coupon')) {
            $coupon = Coupon::where('code', session('coupon.code'))->first();
            if ($coupon) {
                $discount = $coupon->discount_type === 'percentage'
                    ? ($subtotal * $coupon->discount_value / 100)
                    : $coupon->discount_value;
                $discount = min($discount, $subtotal);
                $couponId = $coupon->id;
            }
        }

        $total = max(0, $subtotal + $shipping - $discount);

        $order = DB::transaction(function () use ($user, $cart, $address, $data, $subtotal, $shipping, $discount, $total, $couponId) {
            $order = Order::create([
                'order_number'          => 'BZ-' . date('Y') . '-' . strtoupper(Str::random(6)),
                'user_id'               => $user->id,
                'coupon_id'             => $couponId,
                'order_type'            => 'cart',
                'subtotal'              => $subtotal,
                'discount_amount'       => $discount,
                'shipping_amount'       => $shipping,
                'total_amount'          => $total,
                'payment_method'        => $data['payment_method'],
                'payment_status'        => 'pending',
                'order_status'          => 'pending',
                'delivery_full_name'    => $address->full_name,
                'delivery_phone'        => $address->phone,
                'delivery_address_line_1' => $address->address_line_1,
                'delivery_address_line_2' => $address->address_line_2,
                'delivery_city'         => $address->city,
                'delivery_state'        => $address->state,
                'delivery_country'      => $address->country,
                'delivery_postal_code'  => $address->postal_code,
                'notes'                 => $data['notes'] ?? null,
                'placed_at'             => now(),
            ]);

            // Record coupon usage
            if ($couponId) {
                CouponUsage::create(['order_id' => $order->id, 'user_id' => $user->id, 'coupon_id' => $couponId]);
            }

            // Clear the cart
            $cart->items()->delete();
            session()->forget('coupon');

            return $order;
        });

        return redirect()->route('checkout.success', $order->id)
            ->with('success', 'Order placed successfully!');
    }

    public function success(Order $order)
    {
        $user = Auth::user();
        abort_unless($order->user_id === $user->id, 403);

        $order->load(['sellerOrders.items']);

        return view('user.checkout.success', compact('user', 'order'));
    }
}
