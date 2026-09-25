<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceDataSeeder extends Seeder
{
    /**
     * Helper to download image from web or create high-quality fallback SVG
     */
    private function saveImageFromUrl(string $url, string $relativeFolder, string $filename, string $label = 'Bazaario'): string
    {
        $publicPath = public_path("images/{$relativeFolder}");
        if (!File::isDirectory($publicPath)) {
            File::makeDirectory($publicPath, 0755, true, true);
        }

        $fullPath = "{$publicPath}/{$filename}";
        $relativePath = "images/{$relativeFolder}/{$filename}";

        // If file already exists and is not empty, reuse it
        if (File::exists($fullPath) && File::size($fullPath) > 500) {
            return $relativePath;
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 8,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);

            $imageContent = @file_get_contents($url, false, $context);
            if ($imageContent && strlen($imageContent) > 1000) {
                File::put($fullPath, $imageContent);
                return $relativePath;
            }
        } catch (\Throwable $e) {
            // Fallback gracefully below
        }

        // Fallback: Generate SVG placeholder if network download fails
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600">
            <rect width="600" height="600" fill="#0F172A"/>
            <circle cx="300" cy="300" r="180" fill="#1E293B"/>
            <text x="50%" y="46%" font-family="Space Grotesk, sans-serif" font-size="28" font-weight="bold" fill="#F5A623" text-anchor="middle" dominant-baseline="middle">BAZAARIO</text>
            <text x="50%" y="54%" font-family="Inter, sans-serif" font-size="18" fill="#CBD5E1" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars($label) . '</text>
        </svg>';

        $svgPath = str_replace(['.jpg', '.jpeg', '.png', '.webp'], '.svg', $fullPath);
        File::put($svgPath, $svg);
        return str_replace(['.jpg', '.jpeg', '.png', '.webp'], '.svg', $relativePath);
    }

    public function run(): void
    {
        $this->command->info('Creating local image storage directories...');
        File::makeDirectory(public_path('images/categories'), 0755, true, true);
        File::makeDirectory(public_path('images/products'), 0755, true, true);
        File::makeDirectory(public_path('images/sellers'), 0755, true, true);

        // ─────────────────────────────────────────────────────────────
        // 1. SEED USERS & SELLERS
        // ─────────────────────────────────────────────────────────────
        $this->command->info('Seeding Users and Verified Sellers...');

        // Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@bazaario.com'],
            [
                'name' => 'Bazaario Super Admin',
                'phone' => '9800000001',
                'password' => Hash::make('Password123!'),
                'role' => 'admin',
                'preferred_language' => 'en',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );

        // Seller 1: Heritage Leatherworks
        $sellerUser1 = User::updateOrCreate(
            ['email' => 'heritage@bazaario.com'],
            [
                'name' => 'Vikram Singhania',
                'phone' => '9800000002',
                'password' => Hash::make('Password123!'),
                'role' => 'seller',
                'preferred_language' => 'en',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $seller1 = SellerProfile::updateOrCreate(
            ['user_id' => $sellerUser1->id],
            [
                'shop_name' => 'Heritage Leatherworks',
                'shop_slug' => 'heritage-leatherworks',
                'bio' => 'Master artisan guild specializing in full-grain genuine buffalo leather goods, messenger bags, and accessories.',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'status' => 'approved',
                'commission_rate' => 8.50,
                'trust_score' => 98.60,
                'verified_at' => now()->subMonths(14),
            ]
        );

        // Seller 2: TechForge Systems
        $sellerUser2 = User::updateOrCreate(
            ['email' => 'techforge@bazaario.com'],
            [
                'name' => 'Rohan Mehta',
                'phone' => '9800000003',
                'password' => Hash::make('Password123!'),
                'role' => 'seller',
                'preferred_language' => 'en',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $seller2 = SellerProfile::updateOrCreate(
            ['user_id' => $sellerUser2->id],
            [
                'shop_name' => 'TechForge Systems',
                'shop_slug' => 'techforge-systems',
                'bio' => 'Curators of premium mechanical keyboards, custom switches, desk ergonomics, and audio gear.',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'country' => 'India',
                'status' => 'approved',
                'commission_rate' => 7.00,
                'trust_score' => 99.20,
                'verified_at' => now()->subMonths(20),
            ]
        );

        // Seller 3: Clay & Kiln Studio
        $sellerUser3 = User::updateOrCreate(
            ['email' => 'claykiln@bazaario.com'],
            [
                'name' => 'Sunita Dasgupta',
                'phone' => '9800000004',
                'password' => Hash::make('Password123!'),
                'role' => 'seller',
                'preferred_language' => 'en',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $seller3 = SellerProfile::updateOrCreate(
            ['user_id' => $sellerUser3->id],
            [
                'shop_name' => 'Clay & Kiln Studio',
                'shop_slug' => 'clay-and-kiln-studio',
                'bio' => 'Handcrafted stoneware ceramics, artisan coffee drippers, tableware, and sculptural pottery.',
                'city' => 'Jaipur',
                'state' => 'Rajasthan',
                'country' => 'India',
                'status' => 'approved',
                'commission_rate' => 9.00,
                'trust_score' => 97.80,
                'verified_at' => now()->subMonths(8),
            ]
        );

        // Seller 4: Varanasi Weavers Guild
        $sellerUser4 = User::updateOrCreate(
            ['email' => 'weavers@bazaario.com'],
            [
                'name' => 'Kabir Ansari',
                'phone' => '9800000005',
                'password' => Hash::make('Password123!'),
                'role' => 'seller',
                'preferred_language' => 'en',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $seller4 = SellerProfile::updateOrCreate(
            ['user_id' => $sellerUser4->id],
            [
                'shop_name' => 'Varanasi Weavers Guild',
                'shop_slug' => 'varanasi-weavers-guild',
                'bio' => 'Traditional handloom weavers preserving organic mulberry silk, zari brocades, and natural vegetable-dyed textiles.',
                'city' => 'Varanasi',
                'state' => 'Uttar Pradesh',
                'country' => 'India',
                'status' => 'approved',
                'commission_rate' => 8.00,
                'trust_score' => 99.50,
                'verified_at' => now()->subMonths(24),
            ]
        );

        // Customer Users
        $buyer1 = User::updateOrCreate(
            ['email' => 'aarav.sharma@example.com'],
            [
                'name' => 'Aarav Sharma',
                'phone' => '9800000011',
                'password' => Hash::make('Password123!'),
                'role' => 'user',
                'preferred_language' => 'en',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );

        $buyer2 = User::updateOrCreate(
            ['email' => 'priya.mukherjee@example.com'],
            [
                'name' => 'Priya Mukherjee',
                'phone' => '9800000012',
                'password' => Hash::make('Password123!'),
                'role' => 'user',
                'preferred_language' => 'en',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );

        // ─────────────────────────────────────────────────────────────
        // 2. SEED CATEGORIES
        // ─────────────────────────────────────────────────────────────
        $this->command->info('Seeding Categories and downloading category images...');

        $catData = [
            [
                'name' => 'Artisan & Handmade Crafts',
                'slug' => 'artisan-craft',
                'description' => 'Authentic hand-made pottery, leatherwork, woodcrafts, and master artisan creations.',
                'imageUrl' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&auto=format&fit=crop&q=80',
                'file' => 'artisan_craft.jpg',
            ],
            [
                'name' => 'Electronics & Gadgets',
                'slug' => 'electronics',
                'description' => 'Custom mechanical keyboards, high-fidelity audio gear, and precision tech accessories.',
                'imageUrl' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&auto=format&fit=crop&q=80',
                'file' => 'electronics.jpg',
            ],
            [
                'name' => 'Fashion & Apparel',
                'slug' => 'fashion',
                'description' => 'Handwoven mulberry silks, designer timepieces, natural fabrics, and bespoke apparel.',
                'imageUrl' => 'https://images.unsplash.com/photo-1601924994987-69e26d50dc26?w=800&auto=format&fit=crop&q=80',
                'file' => 'fashion.jpg',
            ],
            [
                'name' => 'Home & Living',
                'slug' => 'home-living',
                'description' => 'Artisan tableware, solid wood lamps, ceramic planters, and handcrafted decor.',
                'imageUrl' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&auto=format&fit=crop&q=80',
                'file' => 'home_living.jpg',
            ],
            [
                'name' => 'Rare Collectibles & Antiques',
                'slug' => 'collectibles',
                'description' => 'Vintage nautical instruments, antique brass replicas, rare coins, and curated memorabilia.',
                'imageUrl' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop&q=80',
                'file' => 'collectibles.jpg',
            ],
            [
                'name' => 'Books & Fine Stationery',
                'slug' => 'books-stationery',
                'description' => 'Deckle-edge handmade journals, brass fountain pens, calligraphy sets, and collector editions.',
                'imageUrl' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80',
                'file' => 'books_stationery.jpg',
            ]
        ];

        $categories = [];
        foreach ($catData as $c) {
            $localImagePath = $this->saveImageFromUrl($c['imageUrl'], 'categories', $c['file'], $c['name']);

            $catModel = Category::updateOrCreate(
                ['slug' => $c['slug']],
                [
                    'name' => $c['name'],
                    'description' => $c['description'],
                    'image' => $localImagePath,
                    'status' => 'active',
                ]
            );
            $categories[$c['slug']] = $catModel;
        }

        // ─────────────────────────────────────────────────────────────
        // 3. SEED PRODUCTS & IMAGES
        // ─────────────────────────────────────────────────────────────
        $this->command->info('Seeding Products and downloading local image assets...');

        $productsData = [
            [
                'seller_id' => $sellerUser1->id,
                'category_slug' => 'artisan-craft',
                'name' => 'Handcrafted Heritage Leather Messenger Bag',
                'slug' => 'handcrafted-leather-messenger-bag',
                'short_description' => 'Full-grain buffalo leather messenger bag with padded 15.6" laptop sleeve and brass hardware.',
                'description' => 'Individually hand-cut and crafted from top-grade full grain buffalo leather. Designed for modern creators and commuters who appreciate authentic craftsmanship, timeless aesthetics, and uncompromising durability. Features antique brass buckles and high-density laptop partition.',
                'sale_type' => 'fixed_price',
                'price' => 4299.00,
                'stock' => 18,
                'sku' => 'BZ-LTH-8841',
                'weight' => 1.25,
                'length' => 39.0,
                'width' => 11.0,
                'height' => 29.0,
                'processing_time_days' => 1,
                'average_rating' => 4.90,
                'total_reviews' => 342,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&auto=format&fit=crop&q=80', 'file' => 'leather_bag_1.jpg', 'is_primary' => true],
                    ['url' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80', 'file' => 'leather_bag_2.jpg', 'is_primary' => false],
                    ['url' => 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=800&auto=format&fit=crop&q=80', 'file' => 'leather_bag_3.jpg', 'is_primary' => false],
                ]
            ],
            [
                'seller_id' => $sellerUser2->id,
                'category_slug' => 'electronics',
                'name' => 'Lumik V87 Pro Wireless Mechanical Keyboard',
                'slug' => 'lumik-v87-pro-wireless-keyboard',
                'short_description' => 'Gasket mounted tri-mode Bluetooth 5.2 mechanical keyboard with hot-swappable tactile switches.',
                'description' => 'High performance wireless mechanical keyboard engineered for programmers and enthusiasts. Features custom factory-lubed linear switches, sound-dampening silicone gaskets, south-facing RGB, and 4000mAh long-lasting battery.',
                'sale_type' => 'fixed_price',
                'price' => 6499.00,
                'stock' => 25,
                'sku' => 'BZ-TECH-1087',
                'weight' => 0.95,
                'length' => 36.0,
                'width' => 14.0,
                'height' => 4.0,
                'processing_time_days' => 1,
                'average_rating' => 4.80,
                'total_reviews' => 215,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&auto=format&fit=crop&q=80', 'file' => 'keyboard_1.jpg', 'is_primary' => true],
                    ['url' => 'https://images.unsplash.com/photo-1595225476474-87563907a212?w=800&auto=format&fit=crop&q=80', 'file' => 'keyboard_2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'seller_id' => $sellerUser3->id,
                'category_slug' => 'home-living',
                'name' => 'Hand-thrown Ceramic Coffee Dripper & Mug Set',
                'slug' => 'hand-thrown-ceramic-coffee-set',
                'short_description' => 'Artisan stoneware glazed V60 coffee dripper with matching 350ml hand-thrown coffee mug.',
                'description' => 'Handmade on the potter wheel in Jaipur using natural mineral glazes. Fired at 1250°C for exceptional heat retention and durability. Microwave, oven, and dishwasher safe.',
                'sale_type' => 'fixed_price',
                'price' => 1850.00,
                'stock' => 14,
                'sku' => 'BZ-POT-2401',
                'weight' => 0.65,
                'length' => 15.0,
                'width' => 15.0,
                'height' => 18.0,
                'processing_time_days' => 2,
                'average_rating' => 4.90,
                'total_reviews' => 88,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&auto=format&fit=crop&q=80', 'file' => 'ceramic_mug_1.jpg', 'is_primary' => true],
                    ['url' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=800&auto=format&fit=crop&q=80', 'file' => 'ceramic_mug_2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'seller_id' => $sellerUser2->id,
                'category_slug' => 'electronics',
                'name' => 'Acoustic Pro Active Noise-Cancelling Headphones',
                'slug' => 'acoustic-pro-anc-headphones',
                'short_description' => 'High-resolution wireless over-ear headphones with hybrid ANC and 45-hour playback.',
                'description' => 'Immerse in pure audiophile-grade sound with 40mm titanium drivers, adaptive noise cancellation, transparency ambient mode, and memory foam ear cushions for all-day comfort.',
                'sale_type' => 'fixed_price',
                'price' => 12499.00,
                'stock' => 12,
                'sku' => 'BZ-AUD-5520',
                'weight' => 0.28,
                'length' => 20.0,
                'width' => 18.0,
                'height' => 8.0,
                'processing_time_days' => 1,
                'average_rating' => 4.70,
                'total_reviews' => 512,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80', 'file' => 'headphones_1.jpg', 'is_primary' => true],
                    ['url' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&auto=format&fit=crop&q=80', 'file' => 'headphones_2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'seller_id' => $sellerUser4->id,
                'category_slug' => 'fashion',
                'name' => 'Pure Organic Mulberry Silk Scarf (Indigo Dye)',
                'slug' => 'organic-mulberry-silk-scarf',
                'short_description' => '100% natural Mulberry silk handwoven in Varanasi and dyed with natural organic indigo.',
                'description' => 'A testament to centuries-old handloom heritage. Superbly soft, breathable, and finished with delicate hand-rolled edges. Dyed in natural fermented indigo vats without synthetic chemicals.',
                'sale_type' => 'fixed_price',
                'price' => 2499.00,
                'stock' => 30,
                'sku' => 'BZ-SLK-3310',
                'weight' => 0.12,
                'length' => 180.0,
                'width' => 55.0,
                'height' => 0.5,
                'processing_time_days' => 1,
                'average_rating' => 4.90,
                'total_reviews' => 120,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1601924994987-69e26d50dc26?w=800&auto=format&fit=crop&q=80', 'file' => 'silk_scarf_1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'seller_id' => $sellerUser1->id,
                'category_slug' => 'collectibles',
                'name' => 'Vintage Brass Desk Compass & Sundial (1940s Replica)',
                'slug' => 'vintage-brass-desk-compass',
                'short_description' => 'Solid antique brass pocket sundial compass with handcrafted stitched leather case.',
                'description' => 'Precision working nautical sundial and magnetic compass cast in heavyweight solid brass with an authentic antique patina. Includes a hand-stitched leather protective pouch.',
                'sale_type' => 'fixed_price',
                'price' => 3199.00,
                'stock' => 8,
                'sku' => 'BZ-ANT-7120',
                'weight' => 0.45,
                'length' => 8.0,
                'width' => 8.0,
                'height' => 3.0,
                'processing_time_days' => 1,
                'average_rating' => 4.60,
                'total_reviews' => 64,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop&q=80', 'file' => 'compass_1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'seller_id' => $sellerUser1->id,
                'category_slug' => 'books-stationery',
                'name' => 'Handbound Leather Journal with Deckle Edge Cotton Paper',
                'slug' => 'handbound-leather-journal-deckle-paper',
                'short_description' => '240 pages of tree-free handmade recycled cotton paper bound in distressed leather.',
                'description' => 'Crafted for writers, sketchers, and calligraphers. Features 240 blank deckle edge cotton pages that accept fountain pen ink, watercolors, and graphite without bleeding.',
                'sale_type' => 'fixed_price',
                'price' => 1299.00,
                'stock' => 45,
                'sku' => 'BZ-BOK-1190',
                'weight' => 0.50,
                'length' => 22.0,
                'width' => 16.0,
                'height' => 4.0,
                'processing_time_days' => 1,
                'average_rating' => 4.80,
                'total_reviews' => 194,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80', 'file' => 'journal_1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'seller_id' => $sellerUser3->id,
                'category_slug' => 'home-living',
                'name' => 'Nordic Minimalist Oak Wood Table Lamp',
                'slug' => 'nordic-minimalist-oak-table-lamp',
                'short_description' => 'FSC-certified solid oak wood base with dimmable warm LED bulb and linen drum shade.',
                'description' => 'Sleek Scandinavian-inspired bedside desk lamp crafted from natural white oak with touch sensor stepped dimming and textured linen shade for cozy ambient lighting.',
                'sale_type' => 'fixed_price',
                'price' => 2899.00,
                'stock' => 20,
                'sku' => 'BZ-LMP-4402',
                'weight' => 1.10,
                'length' => 18.0,
                'width' => 18.0,
                'height' => 38.0,
                'processing_time_days' => 1,
                'average_rating' => 4.70,
                'total_reviews' => 76,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=800&auto=format&fit=crop&q=80', 'file' => 'lamp_1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'seller_id' => $sellerUser2->id,
                'category_slug' => 'fashion',
                'name' => 'Minimalist Titanium Automatic Chronograph Watch',
                'slug' => 'minimalist-titanium-chronograph-watch',
                'short_description' => 'Grade 5 titanium casing, NH35 automatic movement, sapphire crystal glass, 100m water resistance.',
                'description' => 'Engineered for durability and clean understated aesthetics. Lightweight aerospace-grade titanium case paired with anti-reflective scratch-proof sapphire crystal and self-winding Japanese movement.',
                'sale_type' => 'fixed_price',
                'price' => 18999.00,
                'stock' => 6,
                'sku' => 'BZ-WCH-9901',
                'weight' => 0.08,
                'length' => 24.0,
                'width' => 4.2,
                'height' => 1.2,
                'processing_time_days' => 1,
                'average_rating' => 4.90,
                'total_reviews' => 287,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?w=800&auto=format&fit=crop&q=80', 'file' => 'watch_1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'seller_id' => $sellerUser1->id,
                'category_slug' => 'artisan-craft',
                'name' => 'Hand-Carved Sheesham Wood Chess Set (Weighted Pieces)',
                'slug' => 'hand-carved-sheesham-wood-chess-set',
                'short_description' => '14" folding magnetic Sheesham & boxwood board with felt-bottom weighted Staunton pieces.',
                'description' => 'Every single knight, bishop, and pawn is painstakingly sculpted by master woodworkers in Saharanpur. The board doubles as a plush velvet-lined storage compartment.',
                'sale_type' => 'fixed_price',
                'price' => 3750.00,
                'stock' => 15,
                'sku' => 'BZ-CHS-6012',
                'weight' => 1.40,
                'length' => 35.0,
                'width' => 35.0,
                'height' => 6.0,
                'processing_time_days' => 2,
                'average_rating' => 4.85,
                'total_reviews' => 110,
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1529699211952-734e80c4d42b?w=800&auto=format&fit=crop&q=80', 'file' => 'chess_1.jpg', 'is_primary' => true],
                ]
            ]
        ];

        foreach ($productsData as $pData) {
            $cat = $categories[$pData['category_slug']] ?? null;

            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                [
                    'seller_id' => $pData['seller_id'],
                    'category_id' => $cat ? $cat->id : null,
                    'name' => $pData['name'],
                    'short_description' => $pData['short_description'],
                    'description' => $pData['description'],
                    'sale_type' => $pData['sale_type'],
                    'price' => $pData['price'],
                    'stock' => $pData['stock'],
                    'sku' => $pData['sku'],
                    'weight' => $pData['weight'],
                    'length' => $pData['length'],
                    'width' => $pData['width'],
                    'height' => $pData['height'],
                    'processing_time_days' => $pData['processing_time_days'],
                    'status' => 'active',
                    'average_rating' => $pData['average_rating'],
                    'total_reviews' => $pData['total_reviews'],
                ]
            );

            // Seed Images
            ProductImage::where('product_id', $product->id)->delete();
            $sortOrder = 0;
            foreach ($pData['images'] as $img) {
                $savedPath = $this->saveImageFromUrl($img['url'], 'products', $img['file'], $product->name);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $savedPath,
                    'is_primary' => $img['is_primary'],
                    'sort_order' => $sortOrder++,
                ]);
            }

            // Seed a realistic verified customer review
            Review::updateOrCreate(
                [
                    'user_id' => $buyer1->id,
                    'product_id' => $product->id,
                ],
                [
                    'rating' => 5,
                    'title' => 'Exceptional quality, exactly as described!',
                    'comment' => 'Received the package in pristine condition with 100% escrow buyer protection. The craftsmanship and finish exceeded my expectations. Will definitely shop from this seller again.',
                    'status' => 'approved',
                ]
            );
        }

        // ─────────────────────────────────────────────────────────────
        // 4. SEED COUPONS
        // ─────────────────────────────────────────────────────────────
        $this->command->info('Seeding Promo Coupons...');

        $coupons = [
            [
                'code' => 'WELCOME100',
                'discount_type' => 'fixed',
                'discount_value' => 100.00,
                'minimum_order_amount' => 999.00,
                'maximum_discount_amount' => 100.00,
                'usage_limit' => 500,
                'used_count' => 42,
                'starts_at' => now()->subDays(10),
                'expires_at' => now()->addMonths(6),
                'status' => 'active',
            ],
            [
                'code' => 'BAZAARIO10',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'minimum_order_amount' => 1500.00,
                'maximum_discount_amount' => 1000.00,
                'usage_limit' => 1000,
                'used_count' => 128,
                'starts_at' => now()->subDays(5),
                'expires_at' => now()->addMonths(3),
                'status' => 'active',
            ],
            [
                'code' => 'FESTIVE500',
                'discount_type' => 'fixed',
                'discount_value' => 500.00,
                'minimum_order_amount' => 3500.00,
                'maximum_discount_amount' => 500.00,
                'usage_limit' => 200,
                'used_count' => 19,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
                'status' => 'active',
            ],
        ];

        foreach ($coupons as $cp) {
            Coupon::updateOrCreate(['code' => $cp['code']], $cp);
        }

        $this->command->info('✅ Bazaario marketplace database successfully populated with categories, users, products, local image assets, reviews, and coupons!');
    }
}
