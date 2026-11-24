<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display the authenticated user's wishlist with filtering, sorting, and telemetry.
     */
    public function index(Request $request): View
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        $wishlist = $user->wishlist()->firstOrCreate(['user_id' => $user->id]);

        $query = $wishlist->items()->with([
            'product.images',
            'product.primaryImage',
            'product.category',
            'product.seller.sellerProfile',
        ]);

        // Search within saved items
        if ($search = trim($request->input('q', ''))) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($categoryId = $request->input('category_id')) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->join('products', 'wishlist_items.product_id', '=', 'products.id')
                      ->orderBy('products.price', 'asc')
                      ->select('wishlist_items.*');
                break;
            case 'price_desc':
                $query->join('products', 'wishlist_items.product_id', '=', 'products.id')
                      ->orderBy('products.price', 'desc')
                      ->select('wishlist_items.*');
                break;
            case 'name':
                $query->join('products', 'wishlist_items.product_id', '=', 'products.id')
                      ->orderBy('products.name', 'asc')
                      ->select('wishlist_items.*');
                break;
            case 'stock':
                $query->join('products', 'wishlist_items.product_id', '=', 'products.id')
                      ->orderBy('products.stock', 'desc')
                      ->select('wishlist_items.*');
                break;
            case 'latest':
            default:
                $query->orderBy('wishlist_items.id', 'desc');
                break;
        }

        $items = $query->get();

        // Calculate telemetry metrics
        $allItems = $wishlist->items()->with('product')->get();
        $totalCount = $allItems->count();
        $totalValue = $allItems->sum(fn ($item) => (float) ($item->product->price ?? 0));
        $inStockCount = $allItems->filter(fn ($item) => ($item->product->stock ?? 0) > 0)->count();
        $outOfStockCount = $totalCount - $inStockCount;

        // Categories of saved items for quick filter pills
        $savedCategoryIds = $allItems->pluck('product.category_id')->filter()->unique();
        $savedCategories = Category::whereIn('id', $savedCategoryIds)->get();

        // Curated recommendations for empty state or discovery footer
        $recommendations = Product::where('status', 'active')
            ->where('sale_type', 'fixed_price')
            ->whereNotIn('id', $allItems->pluck('product_id')->filter())
            ->with(['images', 'primaryImage', 'seller.sellerProfile'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('user.account.wishlist', compact(
            'user',
            'wishlist',
            'items',
            'totalCount',
            'totalValue',
            'inStockCount',
            'outOfStockCount',
            'savedCategories',
            'recommendations',
            'search',
            'sort'
        ));
    }

    /**
     * Add or remove a product from the wishlist (AJAX or Standard Form POST).
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $productId = (int) $request->product_id;
        $product = Product::findOrFail($productId);
        $user = Auth::guard('user')->user() ?? Auth::user();

        if ($user) {
            $wishlist = $user->wishlist()->firstOrCreate(['user_id' => $user->id]);
            $existing = $wishlist->items()->where('product_id', $productId)->first();

            if ($existing) {
                $existing->delete();
                $inWishlist = false;
                $message = "Removed \"{$product->name}\" from your wishlist.";
            } else {
                $wishlist->items()->create(['product_id' => $productId]);
                $inWishlist = true;
                $message = "Added \"{$product->name}\" to your wishlist!";
            }

            $count = $wishlist->items()->count();
        } else {
            // Guest session wishlist
            $sessionWishlist = session('wishlist', []);
            if (in_array($productId, $sessionWishlist)) {
                $sessionWishlist = array_values(array_diff($sessionWishlist, [$productId]));
                $inWishlist = false;
                $message = "Removed \"{$product->name}\" from your wishlist.";
            } else {
                $sessionWishlist[] = $productId;
                $inWishlist = true;
                $message = "Added \"{$product->name}\" to your wishlist!";
            }
            session(['wishlist' => $sessionWishlist]);
            $count = count($sessionWishlist);
        }

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist,
                'count' => $count,
                'message' => $message,
                'product_id' => $productId,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove an item from the wishlist.
     */
    public function destroy(Request $request, WishlistItem $wishlistItem): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        abort_unless($user && $wishlistItem->wishlist->user_id === $user->id, 403);

        $productName = $wishlistItem->product?->name ?? 'Item';
        $wishlist = $wishlistItem->wishlist;
        $wishlistItem->delete();

        $count = $wishlist->items()->count();

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "Removed \"{$productName}\" from your wishlist.",
            ]);
        }

        return redirect()->route('user.wishlist.index')->with('success', "Removed \"{$productName}\" from your wishlist.");
    }

    /**
     * Move a single wishlist item to cart.
     */
    public function moveToCart(Request $request, WishlistItem $wishlistItem): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        abort_unless($user && $wishlistItem->wishlist->user_id === $user->id, 403);

        $product = $wishlistItem->product;

        if (!$product || $product->stock <= 0) {
            $msg = 'Sorry, this product is currently out of stock.';
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);
        $existing = $cart->items()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->increment('quantity');
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->price,
            ]);
        }

        // Remove from wishlist
        $wishlistItem->delete();

        $cartCount = (int) $cart->items->sum('quantity');
        $wishlistCount = $user->wishlist?->items()->count() ?? 0;
        $msg = "Moved \"{$product->name}\" to your cart!";

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'cart_count' => $cartCount,
                'wishlist_count' => $wishlistCount,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Move all available (in-stock) wishlist items to cart.
     */
    public function moveAllToCart(Request $request): RedirectResponse
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        abort_unless($user, 403);

        $wishlist = $user->wishlist;
        if (!$wishlist || $wishlist->items->isEmpty()) {
            return redirect()->back()->with('info', 'Your wishlist is currently empty.');
        }

        $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);
        $movedCount = 0;
        $outOfStockCount = 0;

        foreach ($wishlist->items()->with('product')->get() as $item) {
            $product = $item->product;
            if (!$product || $product->stock <= 0) {
                $outOfStockCount++;
                continue;
            }

            $existing = $cart->items()->where('product_id', $product->id)->first();
            if ($existing) {
                $existing->increment('quantity');
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => $product->price,
                ]);
            }

            $item->delete();
            $movedCount++;
        }

        if ($movedCount === 0 && $outOfStockCount > 0) {
            return redirect()->back()->with('warning', 'All items in your wishlist are currently out of stock.');
        }

        $msg = "Successfully moved {$movedCount} item(s) to your cart!";
        if ($outOfStockCount > 0) {
            $msg .= " ({$outOfStockCount} out-of-stock item(s) remained saved).";
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Clear all items from the wishlist.
     */
    public function clear(Request $request): RedirectResponse
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        abort_unless($user, 403);

        if ($wishlist = $user->wishlist) {
            $wishlist->items()->delete();
        }

        return redirect()->route('user.wishlist.index')->with('success', 'Wishlist cleared successfully.');
    }

    /**
     * Update a personal note on a wishlist item.
     */
    public function updateNote(Request $request, WishlistItem $wishlistItem): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        abort_unless($user && $wishlistItem->wishlist->user_id === $user->id, 403);

        $request->validate(['note' => 'nullable|string|max:255']);

        $wishlistItem->update(['note' => $request->input('note')]);

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Note saved successfully.',
                'note' => $wishlistItem->note,
            ]);
        }

        return redirect()->back()->with('success', 'Note saved.');
    }

    /**
     * Generate or fetch the public sharing link for the user's wishlist.
     */
    public function generateShareLink(Request $request): JsonResponse
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        abort_unless($user, 403);

        $wishlist = $user->wishlist()->firstOrCreate(['user_id' => $user->id]);
        $token = $wishlist->getOrCreateSharingToken();
        $shareUrl = route('wishlist.public', $token);

        return response()->json([
            'success' => true,
            'sharing_token' => $token,
            'share_url' => $shareUrl,
            'message' => 'Public wishlist link generated!',
        ]);
    }

    /**
     * Display a shared public wishlist view.
     */
    public function publicShow(string $token): View
    {
        $wishlist = Wishlist::where('sharing_token', $token)
            ->where('is_public', true)
            ->with([
                'user',
                'items.product.images',
                'items.product.primaryImage',
                'items.product.category',
                'items.product.seller.sellerProfile',
            ])
            ->firstOrFail();

        $items = $wishlist->items;
        $totalValue = $items->sum(fn ($i) => (float) ($i->product->price ?? 0));

        return view('user.account.wishlist-shared', compact('wishlist', 'items', 'totalValue'));
    }
}
