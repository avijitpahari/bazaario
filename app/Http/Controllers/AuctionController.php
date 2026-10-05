<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuctionController extends Controller
{
    /**
     * Place a new bid on an auction with pessimistic concurrency locking.
     */
    public function placeBid(Request $request, $auction)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Please sign in to place a bid on live auctions.'], 401);
            }
            return back()->with('error', 'Please sign in to place a bid on live auctions.');
        }

        $user = Auth::user();
        $targetId = $auction instanceof Auction ? $auction->id : (int) $auction;

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $lockedAuction = null;

        try {
            DB::transaction(function () use ($request, $targetId, $user, &$lockedAuction) {
                $lockedAuction = Auction::where('id', $targetId)->lockForUpdate()->firstOrFail();

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
    public function quickBid(Request $request, $auction)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Please sign in to place a bid.'], 401);
            }
            return back()->with('error', 'Please sign in to place a bid.');
        }

        $targetId = $auction instanceof Auction ? $auction->id : (int) $auction;
        $freshAuction = Auction::find($targetId);
        if (!$freshAuction) {
            return back()->with('error', 'Auction not found.');
        }

        $increment = (float) ($request->input('increment') ?: $freshAuction->minimum_increment);
        $newAmount = (float) $freshAuction->current_price + $increment;

        $request->merge(['amount' => $newAmount]);

        return $this->placeBid($request, $freshAuction);
    }
}
