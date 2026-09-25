<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
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
            'body'           => 'nullable|string|max:2000',
        ]);

        $data['user_id'] = Auth::id();

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $data['product_id']],
            $data
        );

        return redirect()->back()->with('success', 'Review submitted successfully.');
    }
}
