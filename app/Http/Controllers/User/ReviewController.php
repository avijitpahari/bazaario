<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $reviews = $user->reviews()->with(['product'])->latest()->paginate(10);

        return view('user.account.reviews', compact('user', 'reviews'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'     => 'required|exists:products,id',
            'order_item_id'  => 'nullable|exists:order_items,id',
            'rating'         => 'required|integer|min:1|max:5',
            'title'          => 'nullable|string|max:150',
            'comment'        => 'nullable|string|max:2000',
            'body'           => 'nullable|string|max:2000',
        ]);

        $comment = $request->input('comment', $request->input('body'));

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $data['product_id']],
            [
                'order_item_id' => $data['order_item_id'] ?? null,
                'rating'        => $data['rating'],
                'title'         => $data['title'] ?? null,
                'comment'       => $comment,
                'status'        => 'approved',
            ]
        );

        // Recalculate product aggregate ratings (Feature 33)
        $product = Product::find($data['product_id']);
        if ($product) {
            $avg = Review::where('product_id', $product->id)->avg('rating');
            $count = Review::where('product_id', $product->id)->count();
            $product->update([
                'average_rating' => round($avg, 2),
                'total_reviews'  => $count,
            ]);
        }

        return redirect()->back()->with('success', 'Review submitted successfully.');
    }
}
