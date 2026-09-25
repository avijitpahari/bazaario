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
    public function index()
    {
        $user = Auth::user();
        $cart = $user ? $user->cart()->with(['items.product.images'])->first() : null;

        $subtotal     = 0;
        $shipping     = 0;
        $discount     = 0;
        $appliedCoupon = session('coupon');

        if ($cart) {
            $subtotal = $cart->items->sum(fn ($item) => ($item->product->price ?? 0) * $item->quantity);
            $shipping = $subtotal > 0 ? 99 : 0;

            if ($appliedCoupon) {
                $coupon = Coupon::where('code', $appliedCoupon['code'])->first();
                if ($coupon) {
                    $discount = $coupon->discount_type === 'percentage'
                        ? ($subtotal * $coupon->discount_value / 100)
                        : $coupon->discount_value;
                    $discount = min($discount, $subtotal);
                }
            }
        }

        $total = max(0, $subtotal + $shipping - $discount);

        return view('user.cart.index', compact('user', 'cart', 'subtotal', 'shipping', 'discount', 'total', 'appliedCoupon'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1|max:99',
        ]);

        $user = Auth::user();
        $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;
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

        return redirect()->back()->with('success', 'Item added to cart.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        abort_unless($cartItem->cart->user_id === Auth::id(), 403);

        $cartItem->update(['quantity' => $request->quantity]);

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $cartItem)
    {
        abort_unless($cartItem->cart->user_id === Auth::id(), 403);
        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$coupon) {
            return redirect()->route('cart.index')->with('error', 'Invalid or expired coupon code.');
        }

        session(['coupon' => ['code' => $coupon->code, 'id' => $coupon->id]]);

        return redirect()->route('cart.index')->with('success', 'Coupon "' . $coupon->code . '" applied!');
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return redirect()->route('cart.index')->with('success', 'Coupon removed.');
    }
}
