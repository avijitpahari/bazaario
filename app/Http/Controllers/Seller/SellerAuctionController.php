<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerAuctionController extends Controller
{
    /**
     * Display the seller's master auctions registry.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile) {
            abort(403, 'Seller profile required.');
        }

        $status = $request->query('status', 'all');

        $query = Auction::with(['product', 'bids'])
            ->where('seller_id', $sellerProfile->id);

        if (in_array($status, ['scheduled', 'live', 'ended', 'cancelled'])) {
            $query->where('status', $status);
        }

        $auctions = $query->latest('starts_at')->paginate(15)->withQueryString();

        $counts = [
            'all'       => Auction::where('seller_id', $sellerProfile->id)->count(),
            'scheduled' => Auction::where('seller_id', $sellerProfile->id)->where('status', 'scheduled')->count(),
            'live'      => Auction::where('seller_id', $sellerProfile->id)->where('status', 'live')->count(),
            'ended'     => Auction::where('seller_id', $sellerProfile->id)->where('status', 'ended')->count(),
            'cancelled' => Auction::where('seller_id', $sellerProfile->id)->where('status', 'cancelled')->count(),
        ];

        return view('seller.auctions.index', compact('seller', 'sellerProfile', 'auctions', 'status', 'counts'));
    }

    /**
     * Display the seller's completed and ended auctions history.
     */
    public function history(Request $request): View|RedirectResponse
    {
        $request->merge(['status' => 'ended']);
        return $this->index($request);
    }

    /**
     * Display the form to create a new wholesale auction listing.
     */
    public function create(): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile) {
            abort(403, 'Seller profile required.');
        }

        // Fetch products owned by the authenticated seller (Product.seller_id references users.id)
        $products = Product::where('seller_id', $seller->id)->get();

        return view('seller.auctions.create', compact('seller', 'sellerProfile', 'products'));
    }

    /**
     * Store a newly created auction in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile) {
            abort(403, 'Seller profile required.');
        }

        $request->validate([
            'product_id'        => 'required|exists:products,id',
            'starting_price'    => 'required|numeric|gt:0',
            'reserve_price'     => 'nullable|numeric|gte:starting_price',
            'minimum_increment' => 'required|numeric|gt:0',
            'starts_at'         => 'required|date',
            'ends_at'           => 'required|date|after:starts_at',
        ]);

        // Tenancy check: Ensure the selected product belongs to this seller
        $product = Product::where('id', $request->product_id)
            ->where('seller_id', $seller->id)
            ->first();

        if (!$product) {
            return back()->withErrors([
                'product_id' => 'The selected product does not belong to your store.',
            ])->withInput();
        }

        $startsAt = Carbon::parse($request->starts_at);
        $endsAt = Carbon::parse($request->ends_at);
        $status = (now()->gte($startsAt) && now()->lt($endsAt)) ? 'live' : 'scheduled';

        $auction = Auction::create([
            'seller_id'         => $sellerProfile->id,
            'product_id'        => $product->id,
            'starting_price'    => (float) $request->starting_price,
            'reserve_price'     => $request->filled('reserve_price') ? (float) $request->reserve_price : null,
            'current_price'     => (float) $request->starting_price,
            'minimum_increment' => (float) $request->minimum_increment,
            'starts_at'         => $startsAt,
            'ends_at'           => $endsAt,
            'status'            => $status,
        ]);

        return redirect()->route('seller.auctions.index')
            ->with('success', "Auction lot #AUC-{$auction->id} for '{$product->name}' was successfully scheduled.");
    }

    /**
     * Display the specified auction lot details and bid ledger.
     */
    public function show(Auction $auction): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile || (int) $auction->seller_id !== (int) $sellerProfile->id) {
            abort(403, 'Unauthorized access to auction lot.');
        }

        $auction->load(['product', 'bids' => fn ($q) => $q->latest('id'), 'bids.user', 'winner']);

        return view('seller.auctions.show', compact('seller', 'sellerProfile', 'auction'));
    }

    /**
     * Display the live wholesale bidding terminal for the current active lot.
     */
    public function liveTerminal(Request $request): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile) {
            abort(403, 'Seller profile required.');
        }

        // Locate active live auction lot
        $auction = Auction::with(['product', 'bids' => fn ($q) => $q->latest('id'), 'bids.user'])
            ->where('seller_id', $sellerProfile->id)
            ->where(function ($q) {
                $q->where('status', 'live')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'scheduled')
                          ->where('starts_at', '<=', now())
                          ->where('ends_at', '>', now());
                  });
            })
            ->latest('starts_at')
            ->first();

        // Fallback to most recent auction if none currently live
        if (!$auction) {
            $auction = Auction::with(['product', 'bids' => fn ($q) => $q->latest('id'), 'bids.user'])
                ->where('seller_id', $sellerProfile->id)
                ->latest('id')
                ->first();
        }

        return view('seller.auctions.live', compact('seller', 'sellerProfile', 'auction'));
    }

    /**
     * Cancel an eligible auction lot if no binding bids have been placed.
     */
    public function cancel(Request $request, Auction $auction): RedirectResponse|JsonResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $sellerProfile = $seller->sellerProfile;
        if (!$sellerProfile || (int) $auction->seller_id !== (int) $sellerProfile->id) {
            abort(403, 'Unauthorized access to auction lot.');
        }

        if (in_array($auction->status, ['ended', 'cancelled'])) {
            $msg = "Policy Guardrail: Auction #AUC-{$auction->id} is already {$auction->status} and cannot be modified.";
            if ($request->expectsJson()) {
                return response()->json(['error' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $bidCount = $auction->bids()->count();
        if ($bidCount > 0) {
            $msg = 'Policy Guardrail: This auction cannot be cancelled because active binding bids have been placed under APMC trading rules.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $auction->update(['status' => 'cancelled']);

        return redirect()->route('seller.auctions.index')
            ->with('success', "Auction lot #AUC-{$auction->id} was successfully cancelled.");
    }
}
