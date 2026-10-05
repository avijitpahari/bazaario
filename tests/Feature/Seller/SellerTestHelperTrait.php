<?php

namespace Tests\Feature\Seller;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payout;
use App\Models\Product;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait SellerTestHelperTrait
{
    /**
     * Create an approved seller User with linked approved SellerProfile.
     */
    protected function createApprovedSeller(array $userAttrs = [], array $profileAttrs = []): User
    {
        $seller = User::factory()->create(array_merge([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ], $userAttrs));

        SellerProfile::create(array_merge([
            'user_id'             => $seller->id,
            'shop_name'           => 'Verified Merchant Shop ' . $seller->id,
            'shop_slug'           => 'verified-merchant-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'country'             => 'India',
            'status'              => 'approved',
            'trust_score'         => 94.00,
            'commission_rate'     => 10.00,
            'address'             => 'Plot 55, Agro Hub, Contai',
            'postal_code'         => '721401',
            'operating_radius_km' => 25,
            'bank_account_number' => '9182374650124092',
            'bank_ifsc'           => 'HDFC0001245',
        ], $profileAttrs));

        return $seller->fresh(['sellerProfile']);
    }

    /**
     * Create an unapproved pending seller User with linked pending SellerProfile.
     */
    protected function createPendingSeller(array $userAttrs = [], array $profileAttrs = []): User
    {
        $seller = User::factory()->create(array_merge([
            'role'     => 'seller',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ], $userAttrs));

        SellerProfile::create(array_merge([
            'user_id'             => $seller->id,
            'shop_name'           => 'Pending Application Shop ' . $seller->id,
            'shop_slug'           => 'pending-application-' . $seller->id,
            'seller_type'         => 'Farmer',
            'city'                => 'Contai',
            'state'               => 'West Bengal',
            'country'             => 'India',
            'status'              => 'pending',
            'trust_score'         => 80.00,
            'commission_rate'     => 10.00,
            'address'             => 'Pending Address, Contai',
            'postal_code'         => '721401',
            'operating_radius_km' => 20,
        ], $profileAttrs));

        return $seller->fresh(['sellerProfile']);
    }

    /**
     * Create a suspended or rejected seller User.
     */
    protected function createSuspendedSeller(array $userAttrs = [], array $profileAttrs = []): User
    {
        return $this->createApprovedSeller($userAttrs, array_merge(['status' => 'suspended'], $profileAttrs));
    }

    /**
     * Create a regular buyer User.
     */
    protected function createBuyer(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role'     => 'user',
            'status'   => 'active',
            'password' => Hash::make('Password123!'),
        ], $attrs));
    }

    /**
     * Create or retrieve a test product category.
     */
    protected function createCategory(string $name = 'Fresh Produce', ?string $slug = null): Category
    {
        $slug = $slug ?? Str::slug($name) . '-' . uniqid();
        return Category::firstOrCreate(['slug' => $slug], [
            'name'   => $name,
            'status' => 'active',
        ]);
    }

    /**
     * Create a product belonging to a seller.
     */
    protected function createProductRecord(
        User $seller,
        string $name = 'Farm Fresh Item',
        float $price = 100.00,
        int $stock = 50,
        string $unitType = 'kg',
        int $threshold = 10,
        string $status = 'active',
        ?Category $category = null
    ): Product {
        $category = $category ?? $this->createCategory();

        return Product::create([
            'seller_id'           => $seller->id,
            'category_id'         => $category->id,
            'name'                => $name,
            'slug'                => Str::slug($name) . '-' . uniqid(),
            'sku'                 => 'BZ-' . strtoupper(Str::random(6)),
            'sale_type'           => 'fixed_price',
            'unit_type'           => $unitType,
            'price'               => $price,
            'stock'               => $stock,
            'low_stock_threshold' => $threshold,
            'status'              => $status,
            'is_perishable'       => false,
        ]);
    }

    /**
     * Create a parent Order record.
     */
    protected function createParentOrder(User $buyer, float $subtotal = 1000.00, array $attrs = []): Order
    {
        return Order::create(array_merge([
            'order_number'            => 'ORD-' . strtoupper(uniqid()),
            'user_id'                 => $buyer->id,
            'subtotal'                => $subtotal,
            'total_amount'            => $subtotal,
            'payment_method'          => 'cod',
            'payment_status'          => 'pending',
            'order_status'            => 'processing',
            'delivery_full_name'      => $buyer->name ?? 'Ananya Sharma',
            'delivery_phone'          => '9876543210',
            'delivery_address_line_1' => 'Flat 402, Palm Grove, Indiranagar',
            'delivery_city'           => 'Bengaluru',
            'delivery_state'          => 'Karnataka',
            'delivery_country'        => 'India',
            'delivery_postal_code'    => '560038',
            'notes'                   => 'Time Slot: Today, 4:00 PM – 6:00 PM | Fragile items',
            'placed_at'               => now(),
        ], $attrs));
    }

    /**
     * Create a SellerOrder record attached to a parent Order and Seller.
     */
    protected function createSellerOrderRecord(
        User $seller,
        User $buyer,
        float $subtotal = 1000.00,
        string $sellerOrderNumber = 'SO-001',
        string $status = 'placed',
        array $attrs = []
    ): SellerOrder {
        $parentOrder = $attrs['parent_order'] ?? $this->createParentOrder($buyer, $subtotal);
        unset($attrs['parent_order']);

        $commissionRate = $attrs['commission_rate'] ?? 10.00;
        $commissionAmount = round($subtotal * ($commissionRate / 100), 2);
        $payoutAmount = max(0, $subtotal - $commissionAmount);

        return SellerOrder::create(array_merge([
            'order_id'            => $parentOrder->id,
            'seller_id'           => $seller->id,
            'seller_order_number' => $sellerOrderNumber,
            'subtotal'            => $subtotal,
            'shipping_amount'     => 0.00,
            'commission_rate'     => $commissionRate,
            'commission_amount'   => $commissionAmount,
            'payout_amount'       => $payoutAmount,
            'status'              => $status,
            'delivery_slot'       => 'Today, 4:00 PM – 6:00 PM',
            'tracking_number'     => 'BZ-TRK-' . strtoupper(Str::random(8)),
        ], $attrs));
    }

    /**
     * Create an OrderItem linking a product to a SellerOrder.
     */
    protected function createOrderItemRecord(
        SellerOrder $sellerOrder,
        Product $product,
        int $quantity = 1,
        ?float $unitPrice = null
    ): OrderItem {
        $unitPrice = $unitPrice ?? (float) $product->price;
        $totalPrice = round($unitPrice * $quantity, 2);

        return OrderItem::create([
            'seller_order_id' => $sellerOrder->id,
            'product_id'      => $product->id,
            'product_name'    => $product->name,
            'sku'             => $product->sku,
            'unit_price'      => $unitPrice,
            'quantity'        => $quantity,
            'total_price'     => $totalPrice,
        ]);
    }

    /**
     * Create a Payout record.
     */
    protected function createPayoutRecord(
        User $seller,
        ?SellerOrder $sellerOrder = null,
        float $gross = 1000.00,
        float $commission = 100.00,
        float $net = 900.00,
        string $status = 'pending',
        array $attrs = []
    ): Payout {
        return Payout::create(array_merge([
            'seller_id'         => $seller->id,
            'seller_order_id'   => $sellerOrder?->id,
            'gross_amount'      => $gross,
            'commission_amount' => $commission,
            'net_amount'        => $net,
            'status'            => $status,
            'payout_reference'  => 'PO-' . strtoupper(uniqid()),
            'paid_at'           => $status === 'paid' ? now() : null,
        ], $attrs));
    }

    /**
     * Create a multi-seller order scenario where a single buyer places an order
     * with products from multiple distinct sellers.
     *
     * @param User $buyer
     * @param array $sellersWithItems [ ['seller' => User, 'items' => [ ['product' => Product, 'qty' => int] ]] ]
     * @return array ['parent_order' => Order, 'seller_orders' => Collection<SellerOrder>]
     */
    protected function createMultiSellerOrder(User $buyer, array $sellersWithItems): array
    {
        $parentSubtotal = 0.0;
        foreach ($sellersWithItems as $entry) {
            foreach ($entry['items'] as $itemData) {
                $parentSubtotal += ($itemData['product']->price * $itemData['qty']);
            }
        }

        $parentOrder = $this->createParentOrder($buyer, $parentSubtotal);
        $createdSellerOrders = collect();

        foreach ($sellersWithItems as $index => $entry) {
            $seller = $entry['seller'];
            $sellerSubtotal = 0.0;
            foreach ($entry['items'] as $itemData) {
                $sellerSubtotal += ($itemData['product']->price * $itemData['qty']);
            }

            $sellerOrderNumber = 'SO-' . $parentOrder->id . '-' . ($index + 1) . '-' . strtoupper(Str::random(4));
            $sellerOrder = $this->createSellerOrderRecord(
                $seller,
                $buyer,
                $sellerSubtotal,
                $sellerOrderNumber,
                'placed',
                ['parent_order' => $parentOrder]
            );

            foreach ($entry['items'] as $itemData) {
                $this->createOrderItemRecord($sellerOrder, $itemData['product'], $itemData['qty'], $itemData['product']->price);
            }

            $createdSellerOrders->push($sellerOrder);
        }

        return [
            'parent_order'  => $parentOrder,
            'seller_orders' => $createdSellerOrders,
        ];
    }

    /**
     * Create an Auction record attached to a Seller and Product.
     *
     * Note: Auction foreign key seller_id references seller_profiles.id (not users.id).
     */
    protected function createAuctionRecord(
        User $seller,
        Product $product,
        array $attrs = []
    ): Auction {
        $sellerProfile = $seller->sellerProfile 
            ?? SellerProfile::where('user_id', $seller->id)->first();

        $sellerProfileId = $sellerProfile ? $sellerProfile->id : $seller->id;

        return Auction::create(array_merge([
            'seller_id'         => $sellerProfileId,
            'product_id'        => $product->id,
            'starting_price'    => 1500.00,
            'reserve_price'     => 2500.00,
            'current_price'     => 1500.00,
            'minimum_increment' => 100.00,
            'starts_at'         => now()->subMinutes(10),
            'ends_at'           => now()->addMinutes(50),
            'status'            => 'live',
        ], $attrs));
    }

    /**
     * Create an AuctionBid record attached to an Auction and Bidder User.
     * Automatically updates the auction's current_price if the bid amount is higher.
     */
    protected function createAuctionBid(
        Auction $auction,
        User $bidder,
        float $amount,
        array $attrs = []
    ): AuctionBid {
        $bid = AuctionBid::create(array_merge([
            'auction_id' => $auction->id,
            'user_id'    => $bidder->id,
            'amount'     => $amount,
        ], $attrs));

        if ($amount > (float) $auction->current_price) {
            $auction->update(['current_price' => $amount]);
        }

        return $bid;
    }
}
