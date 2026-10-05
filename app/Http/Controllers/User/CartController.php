<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Features 34, 37, 38: Cart Index with Seller Grouping, Seller Subtotals & Coupon Engine
     */
    public function index()
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        $cart = $user ? $user->cart()->with([
            'items.product.images',
            'items.product.primaryImage',
            'items.product.seller.sellerProfile',
        ])->first() : null;

        $subtotal        = 0;
        $shipping        = 0;
        $discount        = 0;
        $appliedCoupon   = session('coupon');
        $groupedItems    = collect();
        $sellerSubtotals = [];

        if ($cart && $cart->items) {
            $subtotal = (float) $cart->items->sum(fn ($item) => ($item->product->price ?? 0) * $item->quantity);
            $shipping = $subtotal > 0 ? 99 : 0;

            // Feature 34: Group cart items by seller
            $groupedItems = $cart->items->groupBy(function ($item) {
                return $item->product->seller_id ?? 0;
            });

            // Feature 37: Seller-wise Subtotals
            foreach ($groupedItems as $sellerId => $items) {
                $sellerSubtotals[$sellerId] = (float) $items->sum(fn ($i) => ($i->product->price ?? 0) * $i->quantity);
            }

            // Feature 38: Apply promo coupon code rules (minimum_order_amount, maximum_discount_amount, usage_limit, expiration)
            if ($appliedCoupon) {
                $coupon = Coupon::where('code', $appliedCoupon['code'])->first();
                if ($coupon && $coupon->status === 'active'
                    && (!$coupon->expires_at || $coupon->expires_at > now())
                    && (!$coupon->starts_at || $coupon->starts_at <= now())
                    && (!$coupon->usage_limit || $coupon->used_count < $coupon->usage_limit)
                ) {
                    if ($coupon->minimum_order_amount && $subtotal < $coupon->minimum_order_amount) {
                        $discount = 0;
                    } else {
                        $discount = $coupon->discount_type === 'percentage'
                            ? ($subtotal * $coupon->discount_value / 100)
                            : (float) $coupon->discount_value;

                        if ($coupon->maximum_discount_amount && $discount > $coupon->maximum_discount_amount) {
                            $discount = (float) $coupon->maximum_discount_amount;
                        }

                        $discount = min($discount, $subtotal);
                    }
                } else {
                    session()->forget('coupon');
                    $appliedCoupon = null;
                }
            }
        }

        $total = max(0, $subtotal + $shipping - $discount);

        return view('user.cart.index', compact(
            'user',
            'cart',
            'subtotal',
            'shipping',
            'discount',
            'total',
            'appliedCoupon',
            'groupedItems',
            'sellerSubtotals'
        ));
    }

    /**
     * Features 30 & 31: Add to Cart & Buy Now
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1|max:99',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to add items to your cart.');
        }

        $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);

        $product = Product::findOrFail($request->product_id);
        $quantity = (int) ($request->quantity ?? 1);

        $existing = $cart->items()->where('product_id', $request->product_id)->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'unit_price' => $product->price,
            ]);
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index')->with('success', 'Proceeding to checkout.');
        }

        return redirect()->back()->with('success', 'Item added to cart.');
    }

    /**
     * Feature 35: Update Item Quantity
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        abort_unless($cartItem->cart->user_id === Auth::id(), 403);

        $cartItem->update(['quantity' => $request->quantity]);

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    /**
     * Feature 36: Remove Item
     */
    public function destroy(CartItem $cartItem)
    {
        abort_unless($cartItem->cart->user_id === Auth::id(), 403);
        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    /**
     * Feature 38: Apply Promo Coupon Code
     */
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon || $coupon->status !== 'active') {
            return redirect()->route('cart.index')->with('error', 'Invalid or inactive coupon code.');
        }

        if ($coupon->expires_at && $coupon->expires_at <= now()) {
            return redirect()->route('cart.index')->with('error', 'Invalid or expired coupon code.');
        }

        if ($coupon->starts_at && $coupon->starts_at > now()) {
            return redirect()->route('cart.index')->with('error', 'This coupon is not active yet.');
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return redirect()->route('cart.index')->with('error', 'Coupon usage limit has been reached.');
        }

        // Validate minimum_order_amount if user has active items in cart
        $user = Auth::user();
        $cart = $user ? $user->cart()->with('items.product')->first() : null;
        if ($cart && $cart->items->isNotEmpty()) {
            $subtotal = $cart->items->sum(fn ($item) => ($item->product->price ?? 0) * $item->quantity);
            if ($coupon->minimum_order_amount && $subtotal < $coupon->minimum_order_amount) {
                return redirect()->route('cart.index')->with('error', 'Minimum order amount of ₹' . number_format($coupon->minimum_order_amount, 2) . ' required.');
            }
        }

        session(['coupon' => ['code' => $coupon->code, 'id' => $coupon->id]]);

        return redirect()->route('cart.index')->with('success', 'Coupon "' . $coupon->code . '" applied!');
    }

    /**
     * Feature 38: Remove Promo Coupon Code
     */
    public function removeCoupon()
    {
        session()->forget('coupon');
        return redirect()->route('cart.index')->with('success', 'Coupon removed.');
    }
}
