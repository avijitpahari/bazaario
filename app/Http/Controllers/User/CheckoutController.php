<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = $user->cart()->with(['items.product.images', 'items.product.seller.sellerProfile'])->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $addresses = $user->addresses()->latest()->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $subtotal      = $cart->items->sum(fn ($item) => ($item->product->price ?? 0) * $item->quantity);
        $shipping      = $subtotal > 0 ? 99.00 : 0.00;
        $discount      = 0.00;
        $appliedCoupon = null;

        if (session('coupon')) {
            $coupon = Coupon::where('code', session('coupon.code'))->first();
            if ($coupon && $coupon->status === 'active') {
                $appliedCoupon = $coupon;
                $discount = $coupon->discount_type === 'percentage'
                    ? ($subtotal * $coupon->discount_value / 100)
                    : $coupon->discount_value;
                if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
                    $discount = $coupon->max_discount_amount;
                }
                $discount = min($discount, $subtotal);
            }
        }

        $total = max(0, $subtotal + $shipping - $discount);

        $timeSlots = [
            'Morning: 8 AM - 12 PM',
            'Afternoon: 12 PM - 4 PM',
            'Evening: 4 PM - 8 PM',
        ];

        return view('user.checkout.index', compact(
            'user', 'cart', 'addresses', 'defaultAddress',
            'subtotal', 'shipping', 'discount', 'total', 'appliedCoupon', 'timeSlots'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'payment_method'     => 'required|in:cod,card,upi,net_banking,wallet',
            'notes'              => 'nullable|string|max:500',
            'delivery_time_slot' => 'nullable|string|max:100',
        ];

        if ($request->input('address_id') === 'new') {
            $rules['new_full_name']      = 'required|string|max:150';
            $rules['new_phone']          = 'required|string|max:30';
            $rules['new_address_line_1'] = 'required|string|max:255';
            $rules['new_address_line_2'] = 'nullable|string|max:255';
            $rules['new_city']           = 'required|string|max:100';
            $rules['new_state']          = 'required|string|max:100';
            $rules['new_postal_code']    = 'required|string|max:20';
            $rules['new_type']           = 'nullable|in:home,work,other';
        } else {
            $rules['address_id'] = 'required|exists:addresses,id';
        }

        $data = $request->validate($rules);

        $user = Auth::user();
        $cart = $user->cart()->with(['items.product'])->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if ($request->input('address_id') === 'new') {
            $address = Address::create([
                'user_id'        => $user->id,
                'type'           => $request->input('new_type', 'home'),
                'full_name'      => $request->input('new_full_name'),
                'phone'          => $request->input('new_phone'),
                'address_line_1' => $request->input('new_address_line_1'),
                'address_line_2' => $request->input('new_address_line_2'),
                'city'           => $request->input('new_city'),
                'state'          => $request->input('new_state'),
                'postal_code'    => $request->input('new_postal_code'),
                'country'        => $request->input('new_country', 'India'),
                'is_default'     => $user->addresses()->count() === 0,
            ]);
        } else {
            $address = $user->addresses()->find($data['address_id']);
            if (!$address) {
                return back()->withErrors(['address_id' => 'The selected address does not belong to you.']);
            }
        }

        try {
            $order = DB::transaction(function () use ($user, $cart, $address, $data, $request) {
                // Pessimistic locking on products to prevent stock race conditions
                $productIds = $cart->items->pluck('product_id')->filter()->all();
                $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

                foreach ($cart->items as $item) {
                    $prod = $products->get($item->product_id);
                    if (!$prod || $prod->stock < $item->quantity) {
                        throw new \Exception("Product '" . ($prod ? $prod->name : 'Item') . "' does not have sufficient stock.");
                    }
                }

                $subtotal = $cart->items->sum(function ($item) use ($products) {
                    $price = $products[$item->product_id]->price ?? $item->unit_price;
                    return $price * $item->quantity;
                });
                $shipping = $subtotal > 0 ? 99.00 : 0.00;
                $discount = 0.00;
                $couponId = null;

                if (session('coupon')) {
                    $coupon = Coupon::where('code', session('coupon.code'))->first();
                    if ($coupon && $coupon->status === 'active') {
                        $discount = $coupon->discount_type === 'percentage'
                            ? ($subtotal * $coupon->discount_value / 100)
                            : $coupon->discount_value;
                        if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
                            $discount = $coupon->max_discount_amount;
                        }
                        $discount = min($discount, $subtotal);
                        $couponId = $coupon->id;
                    }
                }

                $total = max(0, $subtotal + $shipping - $discount);

                $timeSlot = $request->input('delivery_time_slot');
                $notes = $data['notes'] ?? null;
                if ($timeSlot) {
                    $notes = $notes ? ($notes . " | Time Slot: " . $timeSlot) : ("Time Slot: " . $timeSlot);
                }

                // 1. Create parent Order
                $order = Order::create([
                    'order_number'            => 'BZ-' . date('Y') . '-' . strtoupper(Str::random(8)),
                    'user_id'                 => $user->id,
                    'coupon_id'               => $couponId,
                    'order_type'              => 'cart',
                    'subtotal'                => $subtotal,
                    'discount_amount'         => $discount,
                    'shipping_amount'         => $shipping,
                    'total_amount'            => $total,
                    'payment_method'          => $data['payment_method'],
                    'payment_status'          => 'pending',
                    'order_status'            => 'pending',
                    'delivery_full_name'      => $address->full_name,
                    'delivery_phone'          => $address->phone,
                    'delivery_address_line_1' => $address->address_line_1,
                    'delivery_address_line_2' => $address->address_line_2,
                    'delivery_city'           => $address->city,
                    'delivery_state'          => $address->state,
                    'delivery_country'        => $address->country ?? 'India',
                    'delivery_postal_code'    => $address->postal_code,
                    'notes'                   => $notes,
                    'placed_at'               => now(),
                ]);

                // 2. Group items by seller_id and create SellerOrders
                $itemsBySeller = $cart->items->groupBy(function ($item) use ($products) {
                    $prod = $products->get($item->product_id);
                    return $prod?->seller_id ?: 0;
                });

                $sellerCount = $itemsBySeller->count();
                $sellerShipping = $sellerCount > 0 ? round($shipping / $sellerCount, 2) : 0.00;

                $sellerIndex = 1;
                foreach ($itemsBySeller as $sellerId => $sellerItems) {
                    $sellerSubtotal = $sellerItems->sum(function ($item) use ($products) {
                        $price = $products[$item->product_id]->price ?? $item->unit_price;
                        return $price * $item->quantity;
                    });

                    $seller = $sellerId ? User::with('sellerProfile')->find($sellerId) : null;
                    $commissionRate = $seller?->sellerProfile?->commission_rate ?? 10.00;
                    $commissionAmount = round($sellerSubtotal * ($commissionRate / 100), 2);
                    $payoutAmount = max(0, $sellerSubtotal - $commissionAmount);

                    $sellerOrderNumber = 'SO-' . $order->id . '-' . $sellerIndex . '-' . strtoupper(Str::random(4));

                    $sellerOrder = SellerOrder::create([
                        'order_id'            => $order->id,
                        'seller_id'           => $sellerId ?: null,
                        'seller_order_number' => $sellerOrderNumber,
                        'subtotal'            => $sellerSubtotal,
                        'shipping_amount'     => $sellerShipping,
                        'commission_rate'     => $commissionRate,
                        'commission_amount'   => $commissionAmount,
                        'payout_amount'       => $payoutAmount,
                        'status'              => 'placed',
                        'tracking_number'     => 'BZ-TRK-' . strtoupper(Str::random(8)),
                    ]);

                    // 3. Create OrderItems & decrement product stock
                    foreach ($sellerItems as $item) {
                        $prod = $products->get($item->product_id);
                        $unitPrice = $prod ? $prod->price : $item->unit_price;
                        $lineTotal = $unitPrice * $item->quantity;

                        OrderItem::create([
                            'seller_order_id' => $sellerOrder->id,
                            'product_id'      => $item->product_id,
                            'product_name'    => $prod ? $prod->name : 'Product',
                            'product_image'   => $prod?->images?->first()?->image_path ?? null,
                            'sku'             => $prod?->sku ?? null,
                            'unit_price'      => $unitPrice,
                            'quantity'        => $item->quantity,
                            'total_price'     => $lineTotal,
                        ]);

                        if ($prod) {
                            $prod->decrement('stock', $item->quantity);
                        }
                    }

                    $sellerIndex++;
                }

                // 4. Create Payment record
                Payment::create([
                    'order_id'       => $order->id,
                    'user_id'        => $user->id,
                    'payment_method' => $data['payment_method'],
                    'gateway'        => $data['payment_method'] === 'cod' ? null : $data['payment_method'],
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
                    'amount'         => $total,
                    'status'         => 'pending',
                ]);

                // 5. Record coupon usage
                if ($couponId) {
                    CouponUsage::create([
                        'order_id'  => $order->id,
                        'user_id'   => $user->id,
                        'coupon_id' => $couponId,
                    ]);
                }

                // 6. Empty the cart and forget coupon
                $cart->items()->delete();
                session()->forget('coupon');

                return $order;
            });
        } catch (\Throwable $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        return redirect()->route('checkout.success', $order->id)
            ->with('success', 'Order placed successfully!');
    }

    public function success(Order $order)
    {
        $user = Auth::user();
        abort_unless($order->user_id === $user->id, 403);

        $order->load(['sellerOrders.items.product', 'sellerOrders.seller.sellerProfile', 'payments']);

        return view('user.checkout.success', compact('user', 'order'));
    }
}
