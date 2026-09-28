<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Payout;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminOperationsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Admin Operational Records...');

        // ─────────────────────────────────────────────────────────────
        // 1. SEED PENDING KYC SELLERS
        // ─────────────────────────────────────────────────────────────
        $pendingMerchants = [
            [
                'name' => 'Subhashish Roy',
                'email' => 'bengal.weaves@example.com',
                'phone' => '9830112233',
                'shop_name' => 'Bengal Handloom Co-operative Guild',
                'shop_slug' => 'bengal-handloom-cooperative',
                'bio' => 'Empowering 120 traditional weavers producing certified GI-tagged Jamdani and Tangail textiles in Shantipur.',
                'city' => 'Shantipur',
                'state' => 'West Bengal',
                'country' => 'India',
                'gstin' => '19AAACB1234F1Z5',
                'pan_number' => 'AAACB1234F',
                'trade_license_number' => 'TL-WB-2024-8891',
                'bank_account_number' => '50200012938471',
                'bank_ifsc' => 'HDFC0001234',
                'fssai_number' => null,
                'commission_rate' => 7.50,
                'trust_score' => 94.50,
            ],
            [
                'name' => 'Bikash Mondal',
                'email' => 'sundarban.honey@example.com',
                'phone' => '9830445566',
                'shop_name' => 'Sundarban Mangrove Organic Honey',
                'shop_slug' => 'sundarban-mangrove-honey',
                'bio' => 'Pure unpasteurized wild multi-floral mangrove honey harvested by certified forest collectors in the Sundarbans biosphere.',
                'city' => 'Canning',
                'state' => 'West Bengal',
                'country' => 'India',
                'gstin' => '19BBBCD5678G2Z1',
                'pan_number' => 'BBBCD5678G',
                'trade_license_number' => 'TL-WB-2024-4412',
                'bank_account_number' => '30491827461',
                'bank_ifsc' => 'SBIN0004521',
                'fssai_number' => '12823019000142',
                'commission_rate' => 6.00,
                'trust_score' => 96.00,
            ],
            [
                'name' => 'Ramesh Agrawal',
                'email' => 'kolkata.digital@example.com',
                'phone' => '9830778899',
                'shop_name' => 'Kolkata Digital Peripherals Hub',
                'shop_slug' => 'kolkata-digital-hub',
                'bio' => 'Authorized distributor of gaming components, optical switches, custom cables, and workstation peripherals.',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'gstin' => '19CCCCE9101H3Z4',
                'pan_number' => 'CCCCE9101H',
                'trade_license_number' => 'TL-KMC-2024-0098',
                'bank_account_number' => '098205001294',
                'bank_ifsc' => 'ICIC0000982',
                'fssai_number' => null,
                'commission_rate' => 8.00,
                'trust_score' => 92.00,
            ],
            [
                'name' => 'Tenzing Norbu',
                'email' => 'darjeeling.tea@example.com',
                'phone' => '9830990011',
                'shop_name' => 'Darjeeling Organic High-Grown Tea',
                'shop_slug' => 'darjeeling-organic-tea',
                'bio' => 'Single-estate first and second flush orthodox black and white teas from high elevation micro-gardens in Kurseong.',
                'city' => 'Kurseong',
                'state' => 'West Bengal',
                'country' => 'India',
                'gstin' => '19DDDDJ1121K4Z8',
                'pan_number' => 'DDDDJ1121K',
                'trade_license_number' => 'TL-DAR-2023-7741',
                'bank_account_number' => '919010048172635',
                'bank_ifsc' => 'AXIS0000411',
                'fssai_number' => '10014022002719',
                'commission_rate' => 7.00,
                'trust_score' => 98.20,
            ],
        ];

        foreach ($pendingMerchants as $m) {
            $user = User::updateOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'phone' => $m['phone'],
                    'password' => Hash::make('Password123!'),
                    'role' => 'seller',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            SellerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'shop_name' => $m['shop_name'],
                    'shop_slug' => $m['shop_slug'],
                    'bio' => $m['bio'],
                    'city' => $m['city'],
                    'state' => $m['state'],
                    'country' => $m['country'],
                    'gstin' => $m['gstin'],
                    'pan_number' => $m['pan_number'],
                    'trade_license_number' => $m['trade_license_number'],
                    'bank_account_number' => $m['bank_account_number'],
                    'bank_ifsc' => $m['bank_ifsc'],
                    'fssai_number' => $m['fssai_number'],
                    'commission_rate' => $m['commission_rate'],
                    'trust_score' => $m['trust_score'],
                    'status' => 'pending',
                ]
            );
        }

        // ─────────────────────────────────────────────────────────────
        // 2. SEED MULTI-SELLER CONSIGNMENT ORDERS
        // ─────────────────────────────────────────────────────────────
        $buyers = User::where('role', 'user')->get();
        if ($buyers->isEmpty()) {
            $buyers = collect([
                User::create([
                    'name' => 'Aarav Sharma',
                    'email' => 'aarav.sharma@example.com',
                    'phone' => '9810011223',
                    'password' => Hash::make('Password123!'),
                    'role' => 'user',
                    'status' => 'active',
                ]),
                User::create([
                    'name' => 'Priya Mukherjee',
                    'email' => 'priya.mukherjee@example.com',
                    'phone' => '9820022334',
                    'password' => Hash::make('Password123!'),
                    'role' => 'user',
                    'status' => 'active',
                ]),
            ]);
        }

        $sellerUsers = User::where('role', 'seller')->whereHas('sellerProfile', function($q) {
            $q->where('status', 'approved');
        })->get();

        $allProducts = Product::take(20)->get();

        if ($allProducts->isNotEmpty() && $sellerUsers->isNotEmpty()) {
            $ordersSeedData = [
                [
                    'order_number' => 'BZ-10482',
                    'subtotal' => 14149.00,
                    'shipping_amount' => 100.00,
                    'discount_amount' => 0.00,
                    'total_amount' => 14249.00,
                    'order_status' => 'processing',
                    'payment_method' => 'card',
                    'payment_status' => 'paid',
                    'created_at' => now()->subHours(2),
                    'delivery_full_name' => 'Aarav Sharma',
                    'delivery_phone' => '9810011223',
                    'delivery_address_line_1' => 'Flat 4B, Greenfield Heights, New Town Action Area I',
                    'delivery_city' => 'Kolkata',
                    'delivery_state' => 'West Bengal',
                    'delivery_country' => 'India',
                    'delivery_postal_code' => '700156',
                ],
                [
                    'order_number' => 'BZ-10481',
                    'subtotal' => 4890.00,
                    'shipping_amount' => 80.00,
                    'discount_amount' => 100.00,
                    'total_amount' => 4870.00,
                    'order_status' => 'processing',
                    'payment_method' => 'upi',
                    'payment_status' => 'paid',
                    'created_at' => now()->subHours(6),
                    'delivery_full_name' => 'Priya Mukherjee',
                    'delivery_phone' => '9820022334',
                    'delivery_address_line_1' => '24/1 Ballygunge Circular Road',
                    'delivery_city' => 'Kolkata',
                    'delivery_state' => 'West Bengal',
                    'delivery_country' => 'India',
                    'delivery_postal_code' => '700019',
                ],
                [
                    'order_number' => 'BZ-10480',
                    'subtotal' => 32500.00,
                    'shipping_amount' => 0.00,
                    'discount_amount' => 500.00,
                    'total_amount' => 32000.00,
                    'order_status' => 'completed',
                    'payment_method' => 'net_banking',
                    'payment_status' => 'paid',
                    'created_at' => now()->subDays(2),
                    'delivery_full_name' => 'Avijit Pahari',
                    'delivery_phone' => '9547623150',
                    'delivery_address_line_1' => 'Central Avenue, Contai Municipality',
                    'delivery_city' => 'Contai',
                    'delivery_state' => 'West Bengal',
                    'delivery_country' => 'India',
                    'delivery_postal_code' => '721401',
                ],
                [
                    'order_number' => 'BZ-10479',
                    'subtotal' => 1850.00,
                    'shipping_amount' => 60.00,
                    'discount_amount' => 0.00,
                    'total_amount' => 1910.00,
                    'order_status' => 'completed',
                    'payment_method' => 'upi',
                    'payment_status' => 'paid',
                    'created_at' => now()->subDays(3),
                    'delivery_full_name' => 'Aarav Sharma',
                    'delivery_phone' => '9810011223',
                    'delivery_address_line_1' => 'Park Street Residency, 3rd Floor',
                    'delivery_city' => 'Kolkata',
                    'delivery_state' => 'West Bengal',
                    'delivery_country' => 'India',
                    'delivery_postal_code' => '700016',
                ],
                [
                    'order_number' => 'BZ-10478',
                    'subtotal' => 8720.00,
                    'shipping_amount' => 120.00,
                    'discount_amount' => 200.00,
                    'total_amount' => 8640.00,
                    'order_status' => 'pending',
                    'payment_method' => 'card',
                    'payment_status' => 'paid',
                    'created_at' => now()->subHours(1),
                    'delivery_full_name' => 'Priya Mukherjee',
                    'delivery_phone' => '9820022334',
                    'delivery_address_line_1' => 'Hill Cart Road, Pradhan Nagar',
                    'delivery_city' => 'Siliguri',
                    'delivery_state' => 'West Bengal',
                    'delivery_country' => 'India',
                    'delivery_postal_code' => '734001',
                ],
                [
                    'order_number' => 'BZ-10477',
                    'subtotal' => 18999.00,
                    'shipping_amount' => 0.00,
                    'discount_amount' => 0.00,
                    'total_amount' => 18999.00,
                    'order_status' => 'processing',
                    'payment_method' => 'card',
                    'payment_status' => 'paid',
                    'created_at' => now()->subDay(),
                    'delivery_full_name' => 'Aarav Sharma',
                    'delivery_phone' => '9810011223',
                    'delivery_address_line_1' => 'Salt Lake Sector V, Technopolis Area',
                    'delivery_city' => 'Kolkata',
                    'delivery_state' => 'West Bengal',
                    'delivery_country' => 'India',
                    'delivery_postal_code' => '700091',
                ],
                [
                    'order_number' => 'BZ-10476',
                    'subtotal' => 6450.00,
                    'shipping_amount' => 90.00,
                    'discount_amount' => 0.00,
                    'total_amount' => 6540.00,
                    'order_status' => 'completed',
                    'payment_method' => 'upi',
                    'payment_status' => 'paid',
                    'created_at' => now()->subDays(4),
                    'delivery_full_name' => 'Priya Mukherjee',
                    'delivery_phone' => '9820022334',
                    'delivery_address_line_1' => 'Alipore Park Road',
                    'delivery_city' => 'Kolkata',
                    'delivery_state' => 'West Bengal',
                    'delivery_country' => 'India',
                    'delivery_postal_code' => '700027',
                ],
                [
                    'order_number' => 'BZ-10475',
                    'subtotal' => 12800.00,
                    'shipping_amount' => 150.00,
                    'discount_amount' => 0.00,
                    'total_amount' => 12950.00,
                    'order_status' => 'cancelled',
                    'payment_method' => 'upi',
                    'payment_status' => 'refunded',
                    'created_at' => now()->subDays(5),
                    'delivery_full_name' => 'Aarav Sharma',
                    'delivery_phone' => '9810011223',
                    'delivery_address_line_1' => 'Gariahat Road South',
                    'delivery_city' => 'Kolkata',
                    'delivery_state' => 'West Bengal',
                    'delivery_country' => 'India',
                    'delivery_postal_code' => '700031',
                ],
            ];

            foreach ($ordersSeedData as $oData) {
                $buyer = $buyers->random();
                $order = Order::updateOrCreate(
                    ['order_number' => $oData['order_number']],
                    array_merge($oData, [
                        'user_id' => $buyer->id,
                        'order_type' => 'cart',
                        'placed_at' => $oData['created_at'],
                    ])
                );

                // Add Seller Orders & Items if not present
                if ($order->sellerOrders()->count() === 0) {
                    $sellerUser = $sellerUsers->random();
                    $commissionRate = 8.5;
                    $commissionAmount = round($order->subtotal * ($commissionRate / 100), 2);
                    $payoutAmount = $order->subtotal - $commissionAmount;

                    $sellerStatus = match($order->order_status) {
                        'completed' => 'delivered',
                        'processing' => 'shipped',
                        'cancelled' => 'cancelled',
                        default => 'placed',
                    };

                    $sellerOrder = SellerOrder::create([
                        'order_id' => $order->id,
                        'seller_id' => $sellerUser->id,
                        'seller_order_number' => $order->order_number . '-S1',
                        'subtotal' => $order->subtotal,
                        'shipping_amount' => $order->shipping_amount,
                        'commission_rate' => $commissionRate,
                        'commission_amount' => $commissionAmount,
                        'payout_amount' => $payoutAmount,
                        'status' => $sellerStatus,
                        'tracking_number' => $sellerStatus === 'shipped' ? 'BLUEDART-' . rand(1000000, 9999999) : null,
                    ]);

                    $product = $allProducts->random();
                    OrderItem::create([
                        'seller_order_id' => $sellerOrder->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku ?? ('SKU-' . $product->id),
                        'unit_price' => $order->subtotal,
                        'quantity' => 1,
                        'total_price' => $order->subtotal,
                    ]);
                }
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 3. SEED PAYOUTS
        // ─────────────────────────────────────────────────────────────
        if ($sellerUsers->isNotEmpty()) {
            $payoutsSeed = [
                ['gross' => 14500.00, 'comm' => 1232.50, 'net' => 13267.50, 'status' => 'pending', 'ref' => null, 'paid_at' => null],
                ['gross' => 28400.00, 'comm' => 1988.00, 'net' => 26412.00, 'status' => 'pending', 'ref' => null, 'paid_at' => null],
                ['gross' => 8900.00, 'comm' => 801.00, 'net' => 8099.00, 'status' => 'processing', 'ref' => 'PRC-BZ-' . rand(1000, 9999), 'paid_at' => null],
                ['gross' => 42000.00, 'comm' => 3360.00, 'net' => 38640.00, 'status' => 'paid', 'ref' => 'NEFT-BZ-20260920-041', 'paid_at' => now()->subDays(4)],
                ['gross' => 19800.00, 'comm' => 1683.00, 'net' => 18117.00, 'status' => 'paid', 'ref' => 'IMPS-BZ-20260922-108', 'paid_at' => now()->subDays(2)],
                ['gross' => 31200.00, 'comm' => 2496.00, 'net' => 28704.00, 'status' => 'paid', 'ref' => 'RTGS-BZ-20260923-019', 'paid_at' => now()->subDay()],
                ['gross' => 12600.00, 'comm' => 1008.00, 'net' => 11592.00, 'status' => 'pending', 'ref' => null, 'paid_at' => null],
                ['gross' => 6500.00, 'comm' => 520.00, 'net' => 5980.00, 'status' => 'paid', 'ref' => 'NEFT-BZ-20260924-882', 'paid_at' => now()->subHours(12)],
            ];

            foreach ($payoutsSeed as $p) {
                $seller = $sellerUsers->random();
                Payout::create([
                    'seller_id' => $seller->id,
                    'gross_amount' => $p['gross'],
                    'commission_amount' => $p['comm'],
                    'net_amount' => $p['net'],
                    'status' => $p['status'],
                    'payout_reference' => $p['ref'],
                    'paid_at' => $p['paid_at'],
                ]);
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 4. SEED DISPUTES / RETURNS
        // ─────────────────────────────────────────────────────────────
        $sampleOrder = Order::where('order_status', 'completed')->first();
        if ($sampleOrder && $sampleOrder->sellerOrders()->exists()) {
            $sampleItem = $sampleOrder->sellerOrders()->first()->items()->first();
            if ($sampleItem) {
                $disputesSeed = [
                    [
                        'order_id' => $sampleOrder->id,
                        'order_item_id' => $sampleItem->id,
                        'user_id' => $sampleOrder->user_id,
                        'reason' => 'Defective buckle mechanism on shoulder strap',
                        'description' => 'The quick-release brass swivel hook arrived deformed and fails to clamp securely onto the D-ring attachment. Photos uploaded showing stress fracture.',
                        'status' => 'requested',
                        'refund_amount' => 2499.00,
                        'requested_at' => now()->subHours(8),
                    ],
                    [
                        'order_id' => $sampleOrder->id,
                        'order_item_id' => $sampleItem->id,
                        'user_id' => $sampleOrder->user_id,
                        'reason' => 'Hairline fracture discovered on ceramic dripper',
                        'description' => 'Item arrived in bubble packaging with chipped glaze at base spout. Unusable for hot brewing.',
                        'status' => 'approved',
                        'refund_amount' => 1450.00,
                        'requested_at' => now()->subDays(2),
                        'approved_at' => now()->subDay(),
                    ],
                    [
                        'order_id' => $sampleOrder->id,
                        'order_item_id' => $sampleItem->id,
                        'user_id' => $sampleOrder->user_id,
                        'reason' => 'Color shade variance compared to listing photograph',
                        'description' => 'Natural dye hue is slightly darker than screen swatch. Buyer claims dissatisfaction.',
                        'status' => 'rejected',
                        'refund_amount' => 3200.00,
                        'requested_at' => now()->subDays(4),
                    ],
                ];

                foreach ($disputesSeed as $d) {
                    OrderReturn::create($d);
                }
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 5. SEED SITE SETTINGS & AI ENGINE CONFIG
        // ─────────────────────────────────────────────────────────────
        $settings = [
            'gemini_api_key' => 'AIzaSyBazaarioLiveFlashEngine_Key2026',
            'gemini_model' => 'gemini-1.5-flash',
            'temperature' => '0.7',
            'auto_triage_enabled' => true,
            'dispute_confidence_threshold' => 85,
            'recommendation_engine_enabled' => true,
            'platform_commission_base' => 8.5,
            'escrow_cooling_period_days' => 7,
            'tds_rate' => 1.0,
            'tcs_rate' => 1.0,
            'system_maintenance_mode' => false,
        ];

        foreach ($settings as $key => $val) {
            SiteSetting::set($key, $val);
        }

        $this->command->info('✅ Admin operational dataset populated successfully!');
    }
}
