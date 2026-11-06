<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CloseExpiredAuctions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close expired live auctions, select winning bidders, and generate winning order/invoice records.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredAuctions = Auction::where('status', 'live')
            ->where('ends_at', '<=', now())
            ->get();

        $closedCount = 0;
        $winningOrderCount = 0;

        foreach ($expiredAuctions as $auction) {
            DB::transaction(function () use ($auction, &$closedCount, &$winningOrderCount) {
                // Concurrency lock and idempotency check
                $lockedAuction = Auction::where('id', $auction->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedAuction || $lockedAuction->status !== 'live') {
                    return;
                }

                $highestBid = AuctionBid::where('auction_id', $lockedAuction->id)
                    ->orderByDesc('amount')
                    ->first();

                $hasMetReserve = $highestBid && (
                    !$lockedAuction->reserve_price ||
                    (float) $highestBid->amount >= (float) $lockedAuction->reserve_price
                );

                $lockedAuction->update([
                    'status'        => 'ended',
                    'winner_id'     => $hasMetReserve ? $highestBid->user_id : null,
                    'current_price' => $highestBid ? $highestBid->amount : $lockedAuction->current_price,
                ]);

                $closedCount++;

                if ($hasMetReserve) {
                    $winner = User::with('addresses')->find($highestBid->user_id);
                    $address = $winner?->addresses?->where('is_default', 1)->first() ?? $winner?->addresses?->first();

                    $order = Order::create([
                        'user_id'                 => $highestBid->user_id,
                        'order_number'            => 'ORD-AUC-' . strtoupper(Str::random(8)),
                        'order_type'              => 'auction',
                        'subtotal'                => $highestBid->amount,
                        'discount_amount'         => 0.00,
                        'shipping_amount'         => 0.00,
                        'total_amount'            => $highestBid->amount,
                        'payment_method'          => 'cod',
                        'payment_status'          => 'pending',
                        'order_status'            => 'processing',
                        'delivery_full_name'      => $address?->full_name ?? $winner?->name ?? 'Auction Winner',
                        'delivery_phone'          => $address?->phone ?? $winner?->phone ?? '9999999999',
                        'delivery_address_line_1' => $address?->address_line_1 ?? 'Auction Lot Delivery Address',
                        'delivery_address_line_2' => $address?->address_line_2,
                        'delivery_city'           => $address?->city ?? 'Kolkata',
                        'delivery_state'          => $address?->state ?? 'West Bengal',
                        'delivery_country'        => $address?->country ?? 'India',
                        'delivery_postal_code'    => $address?->postal_code ?? '700001',
                        'notes'                   => 'Won auction lot #AUC-' . $lockedAuction->id,
                        'placed_at'               => now(),
                    ]);

                    $sellerProfile = $lockedAuction->sellerProfile ?? SellerProfile::find($lockedAuction->seller_id);
                    $sellerUserId = $sellerProfile?->user_id ?? $lockedAuction->seller_id;
                    $commissionRate = $sellerProfile ? $sellerProfile->getEffectiveCommissionRate() : SiteSetting::getCommissionRate(10.00);
                    $commissionAmount = round((float) $highestBid->amount * ($commissionRate / 100), 2);
                    $payoutAmount = max(0, (float) $highestBid->amount - $commissionAmount);

                    $sellerOrder = SellerOrder::create([
                        'order_id'            => $order->id,
                        'seller_id'           => $sellerUserId,
                        'seller_order_number' => 'SO-AUC-' . strtoupper(Str::random(8)),
                        'subtotal'            => $highestBid->amount,
                        'shipping_amount'     => 0.00,
                        'commission_rate'     => $commissionRate,
                        'commission_amount'   => $commissionAmount,
                        'payout_amount'       => $payoutAmount,
                        'status'              => 'processing',
                        'tracking_number'     => 'BZ-TRK-AUC-' . strtoupper(Str::random(8)),
                    ]);

                    $product = $lockedAuction->product;

                    OrderItem::create([
                        'seller_order_id' => $sellerOrder->id,
                        'product_id'      => $lockedAuction->product_id,
                        'product_name'    => $product?->name ?? 'Auction Product',
                        'product_image'   => $product?->primaryImage?->image_path ?? $product?->main_image_url ?? null,
                        'sku'             => $product?->sku ?? ('AUC-LOT-' . $lockedAuction->id),
                        'unit_price'      => $highestBid->amount,
                        'quantity'        => 1,
                        'total_price'     => $highestBid->amount,
                    ]);

                    Invoice::create([
                        'order_id'       => $order->id,
                        'invoice_number' => 'INV-AUC-' . strtoupper(Str::random(8)),
                        'subtotal'       => $highestBid->amount,
                        'discount'       => 0.00,
                        'shipping_fee'   => 0.00,
                        'tax'            => 0.00,
                        'total'          => $highestBid->amount,
                        'issued_at'      => now(),
                    ]);

                    try {
                        Notification::create([
                            'user_id' => $highestBid->user_id,
                            'type'    => 'auction_won',
                            'title'   => 'Congratulations! You won the auction! 🏆',
                            'message' => 'You won the auction lot for ' . ($product?->name ?? 'the auction lot') . ' with a bid of ₹' . number_format($highestBid->amount, 2) . '. Order #' . $order->order_number . ' has been generated.',
                            'data'    => [
                                'auction_id'   => $lockedAuction->id,
                                'order_id'     => $order->id,
                                'order_number' => $order->order_number,
                                'amount'       => $highestBid->amount,
                            ],
                        ]);
                    } catch (\Throwable $e) {
                        Log::warning('Auction winner notification failed: ' . $e->getMessage());
                    }

                    $winningOrderCount++;
                }
            });
        }

        $this->info("Closed {$closedCount} expired auction(s). {$winningOrderCount} winning order(s) generated.");

        return Command::SUCCESS;
    }
}
