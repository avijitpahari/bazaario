<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuctionController extends Controller
{
    /**
     * Display live auctions catalog / clearance floor.
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $sort   = $request->query('sort', 'ending_soonest');
        $search = $request->query('search');

        // NOTE: DB enum is ('scheduled','live','ended','cancelled') — 'active' does NOT exist
        $query = Auction::with([
            'product.primaryImage',
            'product.category',
            'seller.sellerProfile',
            'bids' => fn ($q) => $q->orderBy('amount', 'desc')->with('user'),
        ])->where('status', 'live');

        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if ($filter === 'ending_soon') {
            $query->where('ends_at', '<=', now()->addMinutes(30));
        } elseif ($filter === 'reserve_met') {
            $query->whereColumn('current_price', '>=', 'reserve_price');
        } elseif ($filter === 'active_bids' && Auth::check()) {
            $userId = Auth::id();
            $query->whereHas('bids', fn ($q) => $q->where('user_id', $userId));
        } elseif ($filter === 'outbid_alerts' && Auth::check()) {
            // Auctions where user bid but is NOT the current highest bidder
            $userId = Auth::id();
            $query->whereHas('bids', fn ($q) => $q->where('user_id', $userId))
                ->whereDoesntHave('bids', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                      ->whereRaw('amount = (SELECT MAX(b2.amount) FROM bids b2 WHERE b2.auction_id = bids.auction_id)');
                });
        } elseif ($filter === 'won' && Auth::check()) {
            $userId = Auth::id();
            $query->where('winner_id', $userId);
        }

        // Apply sorting
        switch ($sort) {
            case 'highest_value':
                $query->orderBy('current_price', 'desc');
                break;
            case 'most_active':
                $query->withCount('bids')->orderBy('bids_count', 'desc');
                break;
            case 'lowest_start':
                $query->orderBy('starting_price', 'asc');
                break;
            case 'ending_soonest':
            default:
                $query->orderBy('ends_at', 'asc');
                break;
        }

        $auctions = $query->paginate(12)->withQueryString();

        // ── Telemetry metrics ────────────────────────────────────────────────
        $allCount      = Auction::where('status', 'live')->count();
        $endingSoon    = Auction::where('status', 'live')
                            ->where('ends_at', '<=', now()->addMinutes(30))
                            ->count();
        $reserveMet    = Auction::where('status', 'live')
                            ->whereColumn('current_price', '>=', 'reserve_price')
                            ->count();
        $clearanceVol  = Auction::where('status', 'live')->sum('current_price');

        $userLeadingCount = 0;
        $userOutbidCount  = 0;
        $userWonCount     = 0;

        if (Auth::check()) {
            $userId = Auth::id();
            $userWonCount = Auction::where('winner_id', $userId)->count();

            // Auctions this user has bid on
            $userBidAuctionIds = AuctionBid::where('user_id', $userId)
                ->pluck('auction_id')
                ->unique()
                ->values();

            if ($userBidAuctionIds->isNotEmpty()) {
                // Leading = user's highest bid equals the overall highest bid for that auction
                $userLeadingCount = Auction::where('status', 'live')
                    ->whereIn('id', $userBidAuctionIds)
                    ->whereHas('bids', function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                          ->whereRaw(
                              'amount = (SELECT MAX(b2.amount) FROM bids b2 WHERE b2.auction_id = bids.auction_id)'
                          );
                    })
                    ->count();

                $userOutbidCount = $userBidAuctionIds->count() - $userLeadingCount;
            }
        }

        $telemetry = [
            'total_lots'          => $allCount,
            'ending_soon_count'   => $endingSoon,
            'user_leading_count'  => $userLeadingCount,
            'user_outbid_count'   => max(0, $userOutbidCount),
            'reserve_met_count'   => $reserveMet,
            'won_count'           => $userWonCount,
            'clearance_vol'       => $clearanceVol > 0 ? $clearanceVol : 4280000,
        ];

        return view('user.account.auctions', compact('auctions', 'telemetry', 'filter', 'sort'));
    }

    /**
     * User's active bids & escrow stakes page.
     */
    public function bids(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please sign in to view your bids.');
        }

        $user   = Auth::user();
        $filter = $request->query('filter', 'active');
        $sort   = $request->query('sort', 'ending_soonest');

        // Fetch all auctions where the user has placed a bid
        $userBidAuctionIds = AuctionBid::where('user_id', $user->id)
            ->pluck('auction_id')
            ->unique()
            ->values();

        // Base query for auctions involving this user
        $baseQuery = Auction::with([
            'product.primaryImage',
            'product.images',
            'product.category',
            'seller.sellerProfile',
            'bids' => fn ($q) => $q->orderBy('amount', 'desc')->with('user'),
        ]);

        // Compute counts across all statuses
        $activeCount = Auction::whereIn('id', $userBidAuctionIds)
            ->where('status', 'live')
            ->where('ends_at', '>', now())
            ->count();

        $wonCount = Auction::where('winner_id', $user->id)->count();

        $allLiveAuctions = Auction::with(['bids' => fn ($q) => $q->orderBy('amount', 'desc')])
            ->whereIn('id', $userBidAuctionIds)
            ->where('status', 'live')
            ->where('ends_at', '>', now())
            ->get();

        $leadCount = 0;
        $outbidCount = 0;
        $totalLockedEscrow = 0;

        foreach ($allLiveAuctions as $auc) {
            $userHighestBid = $auc->bids->where('user_id', $user->id)->first();
            if ($userHighestBid) {
                $totalLockedEscrow += (float) $userHighestBid->amount;
                $highestBid = $auc->bids->first();
                if ($highestBid && $highestBid->user_id === $user->id) {
                    $leadCount++;
                } else {
                    $outbidCount++;
                }
            }
        }

        $refundsCount = Auction::whereIn('id', $userBidAuctionIds)
            ->where(function ($q) use ($user) {
                $q->where('status', 'ended')
                  ->orWhere('ends_at', '<=', now());
            })
            ->where(function ($q) use ($user) {
                $q->where('winner_id', '!=', $user->id)
                  ->orWhereNull('winner_id');
            })
            ->count();

        // Apply tab filter
        $query = clone $baseQuery;
        if ($filter === 'active') {
            $query->whereIn('id', $userBidAuctionIds)
                ->where('status', 'live')
                ->where('ends_at', '>', now());
        } elseif ($filter === 'won') {
            $query->where('winner_id', $user->id);
        } elseif ($filter === 'outbid') {
            $query->whereIn('id', $userBidAuctionIds)
                ->where('status', 'live')
                ->where('ends_at', '>', now())
                ->whereDoesntHave('bids', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->whereRaw('amount = (SELECT MAX(b2.amount) FROM bids b2 WHERE b2.auction_id = bids.auction_id)');
                });
        } elseif ($filter === 'refunds') {
            $query->whereIn('id', $userBidAuctionIds)
                ->where(function ($q) use ($user) {
                    $q->where('status', 'ended')
                      ->orWhere('ends_at', '<=', now());
                })
                ->where(function ($q) use ($user) {
                    $q->where('winner_id', '!=', $user->id)
                      ->orWhereNull('winner_id');
                });
        } else {
            // 'all'
            $query->whereIn('id', $userBidAuctionIds);
        }

        // Apply sorting
        switch ($sort) {
            case 'highest_escrow':
                $query->orderBy('current_price', 'desc');
                break;
            case 'recently_outbid':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'lowest_increment':
                $query->orderBy('minimum_increment', 'asc');
                break;
            case 'ending_soonest':
            default:
                $query->orderBy('ends_at', 'asc');
                break;
        }

        $auctions = $query->get();

        $upcomingAuction = Auction::whereIn('id', $userBidAuctionIds)
            ->where('status', 'live')
            ->where('ends_at', '>', now())
            ->orderBy('ends_at', 'asc')
            ->first();

        $totalBidCount = $userBidAuctionIds->count();
        $winRate       = $totalBidCount > 0 ? round(($wonCount / $totalBidCount) * 100) : 0;

        $stats = [
            'total_locked_escrow' => $totalLockedEscrow,
            'lead_count'          => $leadCount,
            'outbid_count'        => $outbidCount,
            'won_count'           => $wonCount,
            'active_count'        => $activeCount,
            'refunds_count'       => $refundsCount,
            'upcoming_auction'    => $upcomingAuction,
            'win_rate'            => $winRate,
        ];

        return view('user.account.bids', compact('auctions', 'stats', 'user', 'filter', 'sort'));
    }

    /**
     * Place a new bid on an auction.
     */
    public function placeBid(Request $request, Auction $auction)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Please sign in to place a bid on live auctions.'], 401);
            }
            return back()->with('error', 'Please sign in to place a bid on live auctions.');
        }

        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $lockedAuction = null;

        try {
            DB::transaction(function () use ($request, $auction, $user, &$lockedAuction) {
                $lockedAuction = Auction::where('id', $auction->id)->lockForUpdate()->firstOrFail();

                if (!in_array($lockedAuction->status, ['live', 'active']) || now()->greaterThan($lockedAuction->ends_at)) {
                    throw ValidationException::withMessages(['amount' => 'This auction is no longer active.']);
                }

                $minNextBid = (float) $lockedAuction->current_price + (float) $lockedAuction->minimum_increment;
                if ((float) $request->amount < $minNextBid) {
                    throw ValidationException::withMessages(['amount' => 'Bid must be at least ₹' . number_format($minNextBid, 2)]);
                }

                $bidAmount = (float) $request->amount;

                AuctionBid::create([
                    'auction_id' => $lockedAuction->id,
                    'user_id'    => $user->id,
                    'amount'     => $bidAmount,
                ]);

                $lockedAuction->current_price = $bidAmount;

                // Anti-sniping: if remaining time <= 120 seconds, extend by 2 minutes
                $secondsLeft = now()->diffInSeconds($lockedAuction->ends_at, false);
                if ($secondsLeft >= 0 && $secondsLeft <= 120) {
                    $lockedAuction->ends_at = $lockedAuction->ends_at->addMinutes(2);
                }

                $lockedAuction->save();
            });
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors'  => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'       => true,
                'message'       => 'Bid placed successfully!',
                'current_price' => $lockedAuction ? $lockedAuction->current_price : (float) $request->amount,
            ]);
        }

        return back()->with('success', '✅ Bid placed! Vault collateral updated to ₹' . number_format((float) $request->amount, 2));
    }

    /**
     * Quick Bid handler — adds one minimum increment above current price.
     */
    public function quickBid(Request $request, Auction $auction)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Please sign in to place a bid.'], 401);
            }
            return back()->with('error', 'Please sign in to place a bid.');
        }

        $freshAuction = Auction::find($auction->id) ?? $auction;
        $increment = (float) ($request->input('increment') ?: $freshAuction->minimum_increment);
        $newAmount = (float) $freshAuction->current_price + $increment;

        $request->merge(['amount' => $newAmount]);

        return $this->placeBid($request, $freshAuction);
    }

    /**
     * Display dedicated Live Auction Lot Room.
     */
    public function show(Auction $auction)
    {
        $auction->load([
            'product.primaryImage',
            'product.images',
            'product.category',
            'seller.sellerProfile',
            'bids' => fn ($q) => $q->orderBy('amount', 'desc')->with('user'),
        ]);

        $highestBid = $auction->bids->first();
        $userHighestBid = null;
        $isUserLeading = false;
        $isUserOutbid = false;

        if (Auth::check()) {
            $userId = Auth::id();
            $userHighestBid = $auction->bids->where('user_id', $userId)->first();
            if ($userHighestBid) {
                if ($highestBid && $highestBid->user_id === $userId) {
                    $isUserLeading = true;
                } else {
                    $isUserOutbid = true;
                }
            }
        }

        $minNextBid = (float) $auction->current_price + (float) $auction->minimum_increment;
        $isEndingSoon = $auction->ends_at->diffInMinutes(now(), false) >= -30;
        $secondsLeft = max(0, $auction->ends_at->timestamp - now()->timestamp);
        $isReserveMet = $auction->reserve_price ? ($auction->current_price >= $auction->reserve_price) : true;

        // Related live lots
        $relatedAuctions = Auction::with(['product.primaryImage', 'bids'])
            ->where('status', 'live')
            ->where('id', '!=', $auction->id)
            ->take(4)
            ->get();

        return view('user.account.auction-show', compact(
            'auction',
            'highestBid',
            'userHighestBid',
            'isUserLeading',
            'isUserOutbid',
            'minNextBid',
            'isEndingSoon',
            'secondsLeft',
            'isReserveMet',
            'relatedAuctions'
        ));
    }
}
