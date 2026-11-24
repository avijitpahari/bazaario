<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo ">>> POPULATING AUTHENTIC BAZAARIO MARKETPLACE DATA (NO SEEDERS)..." . PHP_EOL;

// 1. UPDATE / CREATE VERIFIED MERCHANTS
$merchantsData = [
    [
        'email' => 'seller@gmail.com',
        'name' => 'Lillian Blackburn',
        'shop_name' => 'Himalayan & Bengal Organic Orchards',
        'shop_slug' => 'himalayan-bengal-organic',
        'seller_type' => 'Farmer',
        'trust_score' => 98.4,
        'commission_rate' => 8.5,
        'bio' => 'Certified organic heirloom cultivator producing GI-tagged Alphonso mangoes, sun-cured saffron, and Vedic dairy with 100% cold-chain dispatch.',
        'bank_account_number' => '309482710381',
        'bank_ifsc' => 'SBIN0001240',
        'gstin' => '19AAACH7409R1ZZ',
        'pan_number' => 'AAACH7409R',
        'city' => 'Contai',
        'state' => 'West Bengal',
        'country' => 'India',
        'address' => 'Plot 14, Agri-Export Zone, Contai',
        'postal_code' => '721401',
        'latitude' => 21.778100,
        'longitude' => 87.751200,
        'operating_radius_km' => 35,
    ],
    [
        'email' => 'horology@bazaario.com',
        'name' => 'Marcus Vance',
        'shop_name' => 'Geneva Vault & Fine Horology',
        'shop_slug' => 'geneva-vault-horology',
        'seller_type' => 'Wholesaler',
        'trust_score' => 99.2,
        'commission_rate' => 6.0,
        'bio' => 'Curators of verified luxury Swiss timepieces, chronometers, and authenticated horological marvels backed by master watchmaker inspection.',
        'bank_account_number' => '50100293847192',
        'bank_ifsc' => 'HDFC0000452',
        'gstin' => '27AAACG1234M1Z5',
        'pan_number' => 'AAACG1234M',
        'city' => 'Kolkata',
        'state' => 'West Bengal',
        'country' => 'India',
        'address' => 'Floor 4, Diamond Heritage Building, Fairlie Place, BBD Bagh',
        'postal_code' => '700001',
        'latitude' => 22.572600,
        'longitude' => 88.363900,
        'operating_radius_km' => 50,
    ],
    [
        'email' => 'artisan@bazaario.com',
        'name' => 'Ananya Sen',
        'shop_name' => 'Bengal Heritage Guild & Antiques',
        'shop_slug' => 'bengal-heritage-guild',
        'seller_type' => 'Artisan',
        'trust_score' => 96.8,
        'commission_rate' => 7.5,
        'bio' => 'Preserving 19th-century maritime navigation artifacts, master carvers of Staunton chessboards, and authentic Pashmina weavers.',
        'bank_account_number' => '009201938472',
        'bank_ifsc' => 'ICIC0001092',
        'gstin' => '19AAACH5555K1Z1',
        'pan_number' => 'AAACH5555K',
        'city' => 'Kolkata',
        'state' => 'West Bengal',
        'country' => 'India',
        'address' => '24 Craftsmen Lane, Kumartuli Artisans Quarter',
        'postal_code' => '700005',
        'latitude' => 22.580000,
        'longitude' => 88.370000,
        'operating_radius_km' => 40,
    ],
    [
        'email' => 'tech@bazaario.com',
        'name' => 'Karan Mehta',
        'shop_name' => 'SonicCraft Audio & Gadgets Studio',
        'shop_slug' => 'soniccraft-audio-gadgets',
        'seller_type' => 'Manufacturer',
        'trust_score' => 97.5,
        'commission_rate' => 8.0,
        'bio' => 'Boutique custom mechanical keyboards, high-fidelity reference headphones, and artisanal ambient desktop electronics.',
        'bank_account_number' => '91402003948172',
        'bank_ifsc' => 'UTIB0000214',
        'gstin' => '19AAACM9999L1Z2',
        'pan_number' => 'AAACM9999L',
        'city' => 'Kolkata',
        'state' => 'West Bengal',
        'country' => 'India',
        'address' => 'Suite 102, Silicon Square, Sector V, Salt Lake',
        'postal_code' => '700091',
        'latitude' => 22.569700,
        'longitude' => 88.369700,
        'operating_radius_km' => 45,
    ]
];

$sellers = [];
foreach ($merchantsData as $data) {
    $user = User::firstOrNew(['email' => $data['email']]);
    if (!$user->exists) {
        $user->name = $data['name'];
        $user->role = 'seller';
        $user->status = 'active';
        $user->password = Hash::make('Password123!');
        $user->save();
    } else {
        $user->role = 'seller';
        $user->status = 'active';
        $user->save();
    }

    $profile = SellerProfile::firstOrNew(['user_id' => $user->id]);
    $profile->shop_name = $data['shop_name'];
    $profile->shop_slug = $data['shop_slug'];
    $profile->seller_type = $data['seller_type'];
    $profile->trust_score = $data['trust_score'];
    $profile->commission_rate = $data['commission_rate'];
    $profile->bio = $data['bio'];
    $profile->bank_account_number = $data['bank_account_number'];
    $profile->bank_ifsc = $data['bank_ifsc'];
    $profile->gstin = $data['gstin'];
    $profile->pan_number = $data['pan_number'];
    $profile->status = 'approved';
    $profile->city = $data['city'];
    $profile->state = $data['state'];
    $profile->country = $data['country'];
    $profile->address = $data['address'];
    $profile->postal_code = $data['postal_code'];
    $profile->latitude = $data['latitude'];
    $profile->longitude = $data['longitude'];
    $profile->operating_radius_km = $data['operating_radius_km'];
    $profile->operating_days = json_encode(['mon', 'tue', 'wed', 'thu', 'fri', 'sat']);
    $profile->verified_at = Carbon::now();
    $profile->save();

    $sellers[$data['email']] = $user;
    echo "✓ Verified Merchant Ready: {$data['shop_name']} ({$data['email']})" . PHP_EOL;
}

// Ensure demo buyer user exists
$buyer = User::firstOrCreate(
    ['email' => 'avijitpahari867@gmail.com'],
    [
        'name' => 'Avijit Pahari',
        'role' => 'user',
        'status' => 'active',
        'password' => Hash::make('Password123!'),
    ]
);

$secondBuyer = User::firstOrCreate(
    ['email' => 'collector.rohit@bazaario.com'],
    [
        'name' => 'Rohit Mukherjee',
        'role' => 'user',
        'status' => 'active',
        'password' => Hash::make('Password123!'),
    ]
);

// 2. UPDATE CATEGORY IMAGES
$categoryImageMap = [
    'organic-produce' => 'images/categories/organic_food.jpg',
    'fresh-vegetables' => 'images/categories/organic_food.jpg',
    'fresh-fruits' => 'images/categories/organic_food.jpg',
    'grains-cereals' => 'images/categories/organic_food.jpg',
    'spices-herbs' => 'images/categories/organic_food.jpg',
    'dairy-farm-items' => 'images/categories/organic_food.jpg',
    'artisan-craft' => 'images/categories/artisan_craft.jpg',
    'books-stationery' => 'images/categories/books_stationery.jpg',
    'electronics' => 'images/categories/electronics.jpg',
    'fashion' => 'images/categories/fashion.jpg',
    'home-living' => 'images/categories/home_living.jpg',
    'luxury-horology' => 'images/categories/collectibles.jpg',
    'collectibles' => 'images/categories/collectibles.jpg',
    'jewelry-gemstones' => 'images/categories/collectibles.jpg',
];

foreach ($categoryImageMap as $slug => $imgPath) {
    Category::where('slug', $slug)->update(['image' => $imgPath, 'status' => 'active']);
}
echo "✓ Category banners synced." . PHP_EOL;

// 3. CATALOG OF REAL PRODUCTS
$catId = fn($slug) => Category::where('slug', $slug)->value('id');

$products = [
    // 1. Organic Fresh Mangoes
    [
        'seller' => $sellers['seller@gmail.com'],
        'category_id' => $catId('organic-produce') ?? $catId('fresh-fruits'),
        'name' => 'GI-Tagged Ratnagiri Alphonso Mangoes (Grade A++ Export Quality)',
        'slug' => 'gi-tagged-ratnagiri-alphonso-mangoes',
        'short_description' => 'Naturally tree-ripened, carbide-free Alphonso mangoes handpicked from coastal orchards in Ratnagiri.',
        'description' => "Celebrated worldwide as the 'King of Mangoes', our certified GI-tagged Ratnagiri Alphonso lot is hand-harvested at peak maturity. Each fruit features vibrant golden skin, zero synthetic chemical ripening, and extraordinary sweetness with 22° Brix richness.\n\n• Harvest Origin: Ratnagiri Coastal Belt, Maharashtra\n• Grade: Grade A++ Export Certified (260g - 280g per fruit)\n• Chemical residue: 0.00% (Certified pesticide-free)\n• Safe Packing: Custom cushioned ventilated carton with ethylene absorbers.",
        'sale_type' => 'fixed_price',
        'unit_type' => 'kg',
        'price' => 1450.00,
        'stock' => 120,
        'sku' => 'BZ-MANGO-ALP01',
        'is_perishable' => true,
        'harvest_date' => Carbon::today()->subDays(1),
        'expiry_days' => 12,
        'farm_origin' => 'Ratnagiri Coastal Groves',
        'harvest_grade' => 'Grade A++',
        'low_stock_threshold' => 15,
        'weight' => 3.5,
        'status' => 'active',
        'average_rating' => 4.9,
        'total_reviews' => 42,
        'images' => ['images/products/alphonso_mangoes_1.jpg'],
    ],

    // 2. GI Kashmiri Saffron
    [
        'seller' => $sellers['seller@gmail.com'],
        'category_id' => $catId('spices-herbs') ?? $catId('organic-produce'),
        'name' => 'GI-Certified Kashmiri Mongra Saffron Grade A++ (Pure Crimson Filaments 10g)',
        'slug' => 'gi-certified-kashmiri-mongra-saffron',
        'short_description' => 'Top-tier Mongra stigma threads cultivated in the highlands of Pampore, Kashmir.',
        'description' => "Our pure Kashmiri Mongra Saffron is hand-separated exclusively from the deepest crimson stigmas of Crocus sativus grown in the nutrient-dense karewa soils of Pampore. Unadulterated, rich in crocin, picrocrocin, and safranal.\n\n• Stigma Type: 100% Pure Mongra (No yellow style threads)\n• Crocin Reading: >250 (Exceeds ISO 3632 Category I standard)\n• Packaging: Hermetically sealed UV-blocking glass jar with security wax seal.",
        'sale_type' => 'fixed_price',
        'unit_type' => 'piece',
        'price' => 3600.00,
        'stock' => 50,
        'sku' => 'BZ-SAFFRON-KSH01',
        'is_perishable' => false,
        'farm_origin' => 'Pampore Highlands, Kashmir',
        'harvest_grade' => 'Grade A++',
        'low_stock_threshold' => 10,
        'weight' => 0.1,
        'status' => 'active',
        'average_rating' => 5.0,
        'total_reviews' => 38,
        'images' => ['images/categories/organic_food.jpg'],
    ],

    // 3. A2 Gir Cow Bilona Ghee
    [
        'seller' => $sellers['seller@gmail.com'],
        'category_id' => $catId('dairy-farm-items') ?? $catId('organic-produce'),
        'name' => 'A2 Gir Cow Cultured Vedic Bilona Ghee (100% Grass-Fed Glass Jar 1L)',
        'slug' => 'a2-gir-cow-cultured-vedic-bilona-ghee',
        'short_description' => 'Traditional bilona churned ghee prepared from the A2 milk of free-grazing indigenous Gir cows.',
        'description' => "Crafted according to ancient Ayurvedic methods: milk from indigenous free-grazing Gir cows is boiled over slow cow-dung fire, fermented into curd overnight, and two-way hand-churned (bilona) to extract cultured makkhan before gentle clarification into golden granular ghee.\n\n• High Smoke Point: 250°C / 485°F\n• Texture: Rich golden granular crystalline Danedar texture\n• Zero preservatives, zero additives, 100% pure glass jar packaging.",
        'sale_type' => 'fixed_price',
        'unit_type' => 'liter',
        'price' => 1950.00,
        'stock' => 65,
        'sku' => 'BZ-GHEE-GIR01',
        'is_perishable' => false,
        'farm_origin' => 'Saurashtra Pastures, Gujarat',
        'harvest_grade' => 'Artisanal Batch',
        'low_stock_threshold' => 12,
        'weight' => 1.2,
        'status' => 'active',
        'average_rating' => 4.85,
        'total_reviews' => 57,
        'images' => ['images/categories/organic_food.jpg'],
    ],

    // 4. Rolex Submariner (Live Auction)
    [
        'seller' => $sellers['horology@bazaario.com'],
        'category_id' => $catId('luxury-horology') ?? $catId('collectibles'),
        'name' => 'Rolex Submariner Date 41mm Oystersteel Cerachrom Bezel (Ref. 126610LN)',
        'slug' => 'rolex-submariner-date-41mm-ref-126610ln',
        'short_description' => 'Unworn complete collector set with original green presentation box, warranty card, and chronometer tags.',
        'description' => "The archetype of the diver watch. Features a 41mm Oystersteel case, unidirectional rotatable bezel with black Cerachrom ceramic disc, intense black dial with Chromalight luminescent display, and Rolex Calibre 3235 automatic movement boasting a 70-hour power reserve.\n\n• Reference: 126610LN\n• Condition: Mint / Unworn 2026 Serial\n• Bracelet: Oystersteel Oyster bracelet with Oysterlock safety clasp and Glidelock extension system\n• Authenticity: Inspected and verified by Geneva Vault Horology with Escrow Guarantee.",
        'sale_type' => 'auction',
        'unit_type' => 'piece',
        'price' => 950000.00,
        'stock' => 1,
        'sku' => 'BZ-WATCH-RLX01',
        'is_perishable' => false,
        'low_stock_threshold' => 1,
        'weight' => 0.8,
        'status' => 'active',
        'average_rating' => 5.0,
        'total_reviews' => 19,
        'images' => ['images/products/watch_1.jpg'],
        'auction' => [
            'starting_price' => 950000.00,
            'reserve_price' => 1100000.00,
            'current_price' => 1025000.00,
            'minimum_increment' => 25000.00,
            'starts_at' => Carbon::now()->subHours(8),
            'ends_at' => Carbon::now()->addHours(18),
            'status' => 'live',
            'bids' => [
                ['user' => $secondBuyer, 'amount' => 975000.00, 'minutes_ago' => 400],
                ['user' => $buyer, 'amount' => 1000000.00, 'minutes_ago' => 250],
                ['user' => $secondBuyer, 'amount' => 1025000.00, 'minutes_ago' => 45],
            ]
        ]
    ],

    // 5. Sennheiser HD 660S2
    [
        'seller' => $sellers['tech@bazaario.com'],
        'category_id' => $catId('electronics'),
        'name' => 'Sennheiser HD 660S2 Open-Back High-Fidelity Reference Headphones',
        'slug' => 'sennheiser-hd-660s2-open-back-headphones',
        'short_description' => 'Audiophile open-back dynamic headphones with upgraded sub-bass response and ultra-light aluminum voice coils.',
        'description' => "Engineered in Germany and crafted in Ireland, the HD 660S2 delivers reference-grade acoustic immersion with revised transducer airflow and sub-bass resonance tuning that captures micro-dynamics and natural timbre with breathtaking resolution.\n\n• Impedance: 300 Ohms\n• Frequency Response: 8 Hz – 41,500 Hz\n• Transducer: 38mm dynamic drivers hand-selected for left/right channel matching\n• Cables Included: 6.3mm stereo plug and 4.4mm balanced Pentaconn cable.",
        'sale_type' => 'fixed_price',
        'unit_type' => 'piece',
        'price' => 44990.00,
        'stock' => 18,
        'sku' => 'BZ-AUDIO-SNN01',
        'is_perishable' => false,
        'low_stock_threshold' => 4,
        'weight' => 1.1,
        'status' => 'active',
        'average_rating' => 4.95,
        'total_reviews' => 24,
        'images' => ['images/products/headphones_1.jpg', 'images/products/headphones_2.jpg'],
    ],

    // 6. Keychron Q1 Pro (Live Auction)
    [
        'seller' => $sellers['tech@bazaario.com'],
        'category_id' => $catId('electronics'),
        'name' => 'Keychron Q1 Pro Wireless Custom Mechanical Keyboard (Gateron Jupiter Banana)',
        'slug' => 'keychron-q1-pro-wireless-custom-keyboard',
        'short_description' => 'CNC aluminum double-gasket custom QMK/VIA mechanical keyboard with hot-swappable switches.',
        'description' => "Full metal body CNC-machined from 6063 aluminum, double-gasket acoustic mounting design, screw-in PCB stabilizers, south-facing RGB, and tri-mode Bluetooth 5.1 / Type-C connectivity.\n\n• Switch: Factory Lubed Gateron Jupiter Banana (tactile 59gf)\n• Keycaps: KSA double-shot PBT keycaps\n• Fully programmable via QMK and VIA web configurator\n• OS Support: Mac, Windows, Linux toggleable hardware switch.",
        'sale_type' => 'auction',
        'unit_type' => 'piece',
        'price' => 14500.00,
        'stock' => 1,
        'sku' => 'BZ-KB-KEY01',
        'is_perishable' => false,
        'low_stock_threshold' => 1,
        'weight' => 2.4,
        'status' => 'active',
        'average_rating' => 4.9,
        'total_reviews' => 15,
        'images' => ['images/products/keyboard_1.jpg', 'images/products/keyboard_2.jpg'],
        'auction' => [
            'starting_price' => 12000.00,
            'reserve_price' => 15000.00,
            'current_price' => 13500.00,
            'minimum_increment' => 500.00,
            'starts_at' => Carbon::now()->subDays(1),
            'ends_at' => Carbon::now()->addDays(2),
            'status' => 'live',
            'bids' => [
                ['user' => $buyer, 'amount' => 12500.00, 'minutes_ago' => 600],
                ['user' => $secondBuyer, 'amount' => 13500.00, 'minutes_ago' => 120],
            ]
        ]
    ],

    // 7. Staunton Sheesham Chess Set
    [
        'seller' => $sellers['artisan@bazaario.com'],
        'category_id' => $catId('collectibles') ?? $catId('artisan-craft'),
        'name' => 'Hand-Carved Staunton Sheesham & Boxwood Heirloom Chess Set (21-Inch Board)',
        'slug' => 'hand-carved-staunton-sheesham-chess-set',
        'short_description' => 'Weighted and felted tournament-scale luxury chessmen with solid inlaid rosewood storage board.',
        'description' => "Exquisitely hand-carved by master woodturners using genuine Indian Sheesham (Golden Dalbergia) and natural Boxwood. Each piece features double-lead weighting and green billiard cloth baize bottoms for crisp tactile feedback.\n\n• King Height: 4.5 Inches with 1.9-inch base\n• Board Dimensions: 21 x 21 Inches inlaid square geometry with satin lacquer polish\n• Includes 34 chess pieces (Includes 2 additional Queen pieces for pawn promotion).",
        'sale_type' => 'fixed_price',
        'unit_type' => 'piece',
        'price' => 8950.00,
        'stock' => 14,
        'sku' => 'BZ-CHESS-STA01',
        'is_perishable' => false,
        'low_stock_threshold' => 3,
        'weight' => 4.2,
        'status' => 'active',
        'average_rating' => 4.9,
        'total_reviews' => 16,
        'images' => ['images/products/chess_1.jpg'],
    ],

    // 8. 1892 Victorian Brass Maritime Compass (Live Auction)
    [
        'seller' => $sellers['artisan@bazaario.com'],
        'category_id' => $catId('collectibles'),
        'name' => '1892 Victorian Antiqued Brass Maritime Pocket Compass & Sextant',
        'slug' => '1892-victorian-antiqued-brass-compass',
        'short_description' => 'Authentic reproduction antique naval prismatic compass in hand-stitched saddle leather case.',
        'description' => "Crafted from solid marine-grade heavy brass with a deep antiqued patina finish. Features a fully functional magnetic jewel bearing compass card, flip-up sighting prism, sun filters, and engraved graduation dial.\n\n• Diameter: 3 inches (7.6 cm)\n• Material: 100% Solid Brass with aged verdigris protective waxing\n• Case: Heavy top-grain cowhide leather storage pouch with embossed compass rose seal.",
        'sale_type' => 'auction',
        'unit_type' => 'piece',
        'price' => 6500.00,
        'stock' => 1,
        'sku' => 'BZ-ANTQ-CMP01',
        'is_perishable' => false,
        'low_stock_threshold' => 1,
        'weight' => 0.6,
        'status' => 'active',
        'average_rating' => 4.8,
        'total_reviews' => 12,
        'images' => ['images/products/compass_1.jpg'],
        'auction' => [
            'starting_price' => 6500.00,
            'reserve_price' => 9500.00,
            'current_price' => 8000.00,
            'minimum_increment' => 500.00,
            'starts_at' => Carbon::now()->subHours(14),
            'ends_at' => Carbon::now()->addHours(34),
            'status' => 'live',
            'bids' => [
                ['user' => $secondBuyer, 'amount' => 7000.00, 'minutes_ago' => 500],
                ['user' => $buyer, 'amount' => 8000.00, 'minutes_ago' => 90],
            ]
        ]
    ],

    // 9. Italian Leather Duffle Bag
    [
        'seller' => $sellers['artisan@bazaario.com'],
        'category_id' => $catId('fashion'),
        'name' => 'Full-Grain Italian Leather Handcrafted Weekender Duffle Bag',
        'slug' => 'full-grain-italian-leather-weekender-bag',
        'short_description' => 'Vegetable-tanned full-grain leather travel bag with YKK antique brass zippers and waterproof cotton lining.',
        'description' => "Constructed from thick 2.2mm vegetable-tanned full-grain leather that develops a distinctive rich patina with every journey. Features reinforced riveted handles, an adjustable padded shoulder strap, and an exterior quick-access passport pocket.\n\n• Capacity: 45 Liters (Airline carry-on approved)\n• Hardware: Solid antique brass buckles and Japanese YKK Excella zippers\n• Lining: Heavy 16oz water-repellent herringbone cotton canvas.",
        'sale_type' => 'fixed_price',
        'unit_type' => 'piece',
        'price' => 18499.00,
        'stock' => 22,
        'sku' => 'BZ-LTH-BAG01',
        'is_perishable' => false,
        'low_stock_threshold' => 5,
        'weight' => 2.6,
        'status' => 'active',
        'average_rating' => 4.95,
        'total_reviews' => 31,
        'images' => ['images/products/leather_bag_1.jpg', 'images/products/leather_bag_2.jpg', 'images/products/leather_bag_3.jpg'],
    ],

    // 10. Kashmiri Pashmina Silk Shawl
    [
        'seller' => $sellers['artisan@bazaario.com'],
        'category_id' => $catId('fashion'),
        'name' => 'Authentic Handwoven Kashmiri Pashmina Silk Shawl (Pure Mulberry Weave)',
        'slug' => 'handwoven-kashmiri-pashmina-silk-shawl',
        'short_description' => 'Ultra-fine gossamer wool gathered from Changthangi mountain goats blended with raw mulberry silk.',
        'description' => "Woven entirely by hand on traditional wooden pit looms by master artisans in the Kashmir valley. The featherweight weave passes smoothly through a finger ring, demonstrating authentic Pashmina purity and softness.\n\n• Blend: 70% Pure Changthangi Pashmina Wool, 30% Mulberry Silk\n• Dimensions: 200 cm x 70 cm\n• Dyeing: Natural vegetable dyes with hand-spun delicate eyelash fringes.",
        'sale_type' => 'fixed_price',
        'unit_type' => 'piece',
        'price' => 14200.00,
        'stock' => 16,
        'sku' => 'BZ-PASH-SHW01',
        'is_perishable' => false,
        'low_stock_threshold' => 3,
        'weight' => 0.25,
        'status' => 'active',
        'average_rating' => 5.0,
        'total_reviews' => 28,
        'images' => ['images/products/silk_scarf_1.jpg'],
    ],

    // 11. Stoneware Ceramic Mug Set
    [
        'seller' => $sellers['artisan@bazaario.com'],
        'category_id' => $catId('home-living') ?? $catId('artisan-craft'),
        'name' => 'Artisanal Speckled Stoneware Ceramic Coffee Mug Set of 4',
        'slug' => 'artisanal-speckled-ceramic-coffee-mug-set',
        'short_description' => 'Wheel-thrown ceramic mugs with reactive matte glaze and ergonomic comfort handles.',
        'description' => "Individually wheel-thrown using high-fire stoneware clay and finished with an earthy reactive speckled glaze fired at 1260°C. Each mug holds a generous 380ml pour and is microwave and dishwasher safe.\n\n• Capacity: 380 ml each\n• Glaze: Non-toxic lead-free food-safe matte mineral glaze\n• Quantity: Set of 4 unique hand-crafted mugs.",
        'sale_type' => 'fixed_price',
        'unit_type' => 'piece',
        'price' => 2200.00,
        'stock' => 35,
        'sku' => 'BZ-CRMC-MUG01',
        'is_perishable' => false,
        'low_stock_threshold' => 6,
        'weight' => 1.8,
        'status' => 'active',
        'average_rating' => 4.75,
        'total_reviews' => 18,
        'images' => ['images/products/ceramic_mug_1.jpg', 'images/products/ceramic_mug_2.jpg'],
    ],

    // 12. Amber Glass Ambient Lamp (Live Auction)
    [
        'seller' => $sellers['tech@bazaario.com'],
        'category_id' => $catId('home-living'),
        'name' => 'Nordic Minimalist Handcrafted Amber Glass Bedside Ambient Lamp',
        'slug' => 'nordic-minimalist-amber-glass-ambient-lamp',
        'short_description' => 'Mouth-blown fluted amber glass shade with solid walnut wood base and brass dimmer switch.',
        'description' => "Combining Scandinavian simplicity with warm nostalgic amber illumination. Features hand-blown fluted optical glass that casts soft radial light patterns across bedroom or study surfaces.\n\n• Base: Hand-turned solid American Walnut wood\n• Switch: Stepless knurled brass rotary dimmer switch\n• Bulb: Includes warm 2200K vintage LED spiral filament bulb (E27).",
        'sale_type' => 'auction',
        'unit_type' => 'piece',
        'price' => 3800.00,
        'stock' => 1,
        'sku' => 'BZ-LGT-LMP01',
        'is_perishable' => false,
        'low_stock_threshold' => 1,
        'weight' => 1.9,
        'status' => 'active',
        'average_rating' => 4.85,
        'total_reviews' => 14,
        'images' => ['images/products/lamp_1.jpg'],
        'auction' => [
            'starting_price' => 3800.00,
            'reserve_price' => 5000.00,
            'current_price' => 4400.00,
            'minimum_increment' => 200.00,
            'starts_at' => Carbon::now()->subHours(6),
            'ends_at' => Carbon::now()->addDays(3),
            'status' => 'live',
            'bids' => [
                ['user' => $secondBuyer, 'amount' => 4000.00, 'minutes_ago' => 200],
                ['user' => $buyer, 'amount' => 4400.00, 'minutes_ago' => 60],
            ]
        ]
    ],

    // 13. Vintage Deckle Edge Leather Journal
    [
        'seller' => $sellers['artisan@bazaario.com'],
        'category_id' => $catId('books-stationery'),
        'name' => 'Handmade Vintage Deckle Edge Leather-Bound Grimoire Journal',
        'slug' => 'vintage-deckle-edge-leather-bound-journal',
        'short_description' => '240 pages of tree-free cotton handmade deckle paper wrapped in distressed antique leather.',
        'description' => "Crafted for calligraphers, writers, and sketch artists. Filled with 240 blank deckle edge pages made from 100% recycled cotton pulp that resists fountain pen ink bleed-through.\n\n• Binding: Hand-stitched Coptic leather binding that lays completely flat (180°)\n• Closure: Vintage brass swing clasp and leather wraparound thong\n• Size: 7 x 5 Inches (A5 compact traveler scale).",
        'sale_type' => 'fixed_price',
        'unit_type' => 'piece',
        'price' => 1850.00,
        'stock' => 40,
        'sku' => 'BZ-BK-JRN01',
        'is_perishable' => false,
        'low_stock_threshold' => 8,
        'weight' => 0.5,
        'status' => 'active',
        'average_rating' => 4.9,
        'total_reviews' => 27,
        'images' => ['images/products/journal_1.jpg'],
    ],

    // 14. Aged Basmati Rice
    [
        'seller' => $sellers['seller@gmail.com'],
        'category_id' => $catId('grains-cereals') ?? $catId('organic-produce'),
        'name' => 'Dehraduni Aged Traditional Basmati Rice (2-Year Barrel Matured 10kg)',
        'slug' => 'dehraduni-aged-traditional-basmati-rice',
        'short_description' => 'Long-grain fragrant Basmati grown in Himalayan foothills and aged for 24 months in wooden silos.',
        'description' => "True heritage Dehraduni Basmati known for its delicate floral aroma and remarkable post-cooking grain elongation exceeding 20mm. Aged for 24 months under controlled humidity to reduce moisture content and maximize fluffiness.\n\n• Grain Length: Average uncooked grain length 8.4mm+\n• Aging: 24 Months Matured for non-sticky, distinct separation\n• Packaging: Eco-friendly breathable natural jute sack.",
        'sale_type' => 'fixed_price',
        'unit_type' => 'kg',
        'price' => 2450.00,
        'stock' => 55,
        'sku' => 'BZ-RICE-BSM01',
        'is_perishable' => false,
        'farm_origin' => 'Doon Valley, Uttarakhand',
        'harvest_grade' => 'Select Aged Lot',
        'low_stock_threshold' => 10,
        'weight' => 10.2,
        'status' => 'active',
        'average_rating' => 4.8,
        'total_reviews' => 33,
        'images' => ['images/categories/organic_food.jpg'],
    ],

    // 15. Ended Auction (Damascus Knife) - Winner is Avijit Pahari!
    [
        'seller' => $sellers['artisan@bazaario.com'],
        'category_id' => $catId('artisan-craft') ?? $catId('home-living'),
        'name' => 'Handcrafted Damascus Steel 8-Inch Chef Knife with Stabilized Burl Handle',
        'slug' => 'handcrafted-damascus-steel-chef-knife',
        'short_description' => '67-layer VG-10 core forged Damascus culinary knife won at live auction.',
        'description' => "Hand-forged by master bladesmiths featuring 67 alternating layers of high-carbon stainless steel over an ultra-hard VG-10 cutting core (61 HRC). Fitted with an octagonal stabilized burl wood and copper spacer handle.\n\n• Blade Length: 8 Inches (20.3 cm)\n• Edge Angle: 12 degrees per side for scalpel-like sharpness\n• Status: Auction Completed & Lot Hammered Down.",
        'sale_type' => 'auction',
        'unit_type' => 'piece',
        'price' => 7500.00,
        'stock' => 0,
        'sku' => 'BZ-KNFE-DMS01',
        'is_perishable' => false,
        'low_stock_threshold' => 0,
        'weight' => 0.45,
        'status' => 'active',
        'average_rating' => 5.0,
        'total_reviews' => 8,
        'images' => ['images/categories/artisan_craft.jpg'],
        'auction' => [
            'starting_price' => 5000.00,
            'reserve_price' => 7000.00,
            'current_price' => 8200.00,
            'minimum_increment' => 200.00,
            'starts_at' => Carbon::now()->subDays(5),
            'ends_at' => Carbon::now()->subDays(1),
            'status' => 'ended',
            'winner_id' => $buyer->id,
            'bids' => [
                ['user' => $secondBuyer, 'amount' => 5500.00, 'minutes_ago' => 5000],
                ['user' => $buyer, 'amount' => 6500.00, 'minutes_ago' => 4000],
                ['user' => $secondBuyer, 'amount' => 7500.00, 'minutes_ago' => 3000],
                ['user' => $buyer, 'amount' => 8200.00, 'minutes_ago' => 1500],
            ]
        ]
    ]
];

// Insert / Update products and auctions
foreach ($products as $pData) {
    $seller = $pData['seller'];
    $category_id = $pData['category_id'] ?? 1;
    $slug = $pData['slug'];

    $product = Product::firstOrNew(['slug' => $slug]);
    $product->seller_id = $seller->id;
    $product->category_id = $category_id;
    $product->name = $pData['name'];
    $product->short_description = $pData['short_description'];
    $product->description = $pData['description'];
    $product->sale_type = $pData['sale_type'];
    $product->unit_type = $pData['unit_type'];
    $product->price = $pData['price'];
    $product->stock = $pData['stock'];
    $product->sku = $pData['sku'];
    $product->is_perishable = $pData['is_perishable'] ?? false;
    $product->harvest_date = $pData['harvest_date'] ?? null;
    $product->expiry_days = $pData['expiry_days'] ?? null;
    if ($product->is_perishable && $product->harvest_date && $product->expiry_days) {
        $product->expiry_date = Carbon::parse($product->harvest_date)->addDays((int)$product->expiry_days)->toDateString();
    }
    $product->farm_origin = $pData['farm_origin'] ?? null;
    $product->harvest_grade = $pData['harvest_grade'] ?? null;
    $product->low_stock_threshold = $pData['low_stock_threshold'] ?? 5;
    $product->weight = $pData['weight'] ?? 1.0;
    $product->status = $pData['status'] ?? 'active';
    $product->average_rating = $pData['average_rating'] ?? 5.0;
    $product->total_reviews = $pData['total_reviews'] ?? 10;
    $product->save();

    // Attach product images
    ProductImage::where('product_id', $product->id)->delete();
    foreach ($pData['images'] as $idx => $img) {
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $img,
            'is_primary' => ($idx === 0),
            'sort_order' => $idx,
            'created_at' => Carbon::now(),
        ]);
    }

    // Attach Auction if defined
    if (isset($pData['auction'])) {
        $aucData = $pData['auction'];
        $auction = Auction::firstOrNew(['product_id' => $product->id]);
        $auction->seller_id = $seller->sellerProfile->id;
        $auction->starting_price = $aucData['starting_price'];
        $auction->reserve_price = $aucData['reserve_price'];
        $auction->current_price = $aucData['current_price'];
        $auction->minimum_increment = $aucData['minimum_increment'];
        $auction->starts_at = $aucData['starts_at'];
        $auction->ends_at = $aucData['ends_at'];
        $auction->status = $aucData['status'];
        $auction->winner_id = $aucData['winner_id'] ?? null;
        $auction->save();

        // Attach bids
        AuctionBid::where('auction_id', $auction->id)->delete();
        if (isset($aucData['bids'])) {
            foreach ($aucData['bids'] as $bidInfo) {
                AuctionBid::create([
                    'auction_id' => $auction->id,
                    'user_id' => $bidInfo['user']->id,
                    'amount' => $bidInfo['amount'],
                    'created_at' => Carbon::now()->subMinutes($bidInfo['minutes_ago']),
                    'updated_at' => Carbon::now()->subMinutes($bidInfo['minutes_ago']),
                ]);
            }
        }
        echo "✓ Auction Created: {$product->name} (Current: ₹" . number_format($auction->current_price, 2) . ")" . PHP_EOL;
    } else {
        echo "✓ Product Created: {$product->name} (₹" . number_format($product->price, 2) . ")" . PHP_EOL;
    }

    // Add 2 authentic buyer reviews
    Review::where('product_id', $product->id)->delete();
    Review::create([
        'product_id' => $product->id,
        'user_id' => $buyer->id,
        'rating' => 5,
        'comment' => "Remarkable authentic quality! Fast courier dispatch, immaculate packaging, and genuine provenance verification.",
        'created_at' => Carbon::now()->subDays(3),
    ]);
    Review::create([
        'product_id' => $product->id,
        'user_id' => $secondBuyer->id,
        'rating' => 5,
        'comment' => "Exceeded expectations. Exactly as described by the merchant with complete transparency.",
        'created_at' => Carbon::now()->subDays(1),
    ]);
}

echo PHP_EOL . ">>> REAL PRODUCTS & AUCTIONS POPULATED SUCCESSFULLY!" . PHP_EOL;
echo "Products count: " . Product::count() . PHP_EOL;
echo "Auctions count: " . Auction::count() . PHP_EOL;
echo "Bids count: " . AuctionBid::count() . PHP_EOL;
echo "Reviews count: " . Review::count() . PHP_EOL;
