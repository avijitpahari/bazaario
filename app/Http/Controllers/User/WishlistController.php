<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $wishlist = $user->wishlist()->with(['items.product.images'])->first();
        $items    = $wishlist ? $wishlist->items : collect();

        return view('user.account.wishlist', compact('user', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $user     = Auth::user();
        $wishlist = $user->wishlist()->firstOrCreate(['user_id' => $user->id]);

        $wishlist->items()->firstOrCreate(['product_id' => $request->product_id]);

        return redirect()->back()->with('success', 'Added to wishlist!');
    }

    public function destroy(WishlistItem $wishlistItem)
    {
        abort_unless($wishlistItem->wishlist->user_id === Auth::id(), 403);
        $wishlistItem->delete();

        return redirect()->route('user.wishlist.index')->with('success', 'Removed from wishlist.');
    }
}
