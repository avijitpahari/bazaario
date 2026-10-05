<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "--- Starting Forensic Controller & Eloquent Verification ---\n";

// Run in a transaction so we don't pollute the test/dev DB
DB::beginTransaction();

try {
    // 1. Create categories
    $catA = Category::create([
        'name' => 'Auditor Woodwork ' . uniqid(),
        'slug' => 'auditor-woodwork-' . uniqid(),
        'status' => 'active',
    ]);
    $catB = Category::create([
        'name' => 'Auditor Pottery ' . uniqid(),
        'slug' => 'auditor-pottery-' . uniqid(),
        'status' => 'active',
    ]);

    // 2. Create sellers
    // Local seller in Kolkata
    $userKolkata = User::create([
        'name' => 'Kolkata Merchant',
        'email' => 'km_' . uniqid() . '@test.com',
        'password' => Hash::make('secret123'),
        'role' => 'seller',
        'status' => 'active',
    ]);
    $profileKolkata = SellerProfile::create([
        'user_id' => $userKolkata->id,
        'shop_name' => 'Kolkata Woodcraft',
        'shop_slug' => 'kolkata-woodcraft-' . uniqid(),
        'status' => 'approved',
        'city' => 'Kolkata',
        'latitude' => 22.572646,
        'longitude' => 88.363895,
    ]);

    // Distant seller in Delhi (28.704060, 77.102493)
    $userDelhi = User::create([
        'name' => 'Delhi Merchant',
        'email' => 'dm_' . uniqid() . '@test.com',
        'password' => Hash::make('secret123'),
        'role' => 'seller',
        'status' => 'active',
    ]);
    $profileDelhi = SellerProfile::create([
        'user_id' => $userDelhi->id,
        'shop_name' => 'Delhi Pottery Works',
        'shop_slug' => 'delhi-pottery-' . uniqid(),
        'status' => 'approved',
        'city' => 'Delhi',
        'latitude' => 28.704060,
        'longitude' => 77.102493,
    ]);

    // 3. Create products
    $prodLocal = Product::create([
        'seller_id' => $userKolkata->id,
        'category_id' => $catA->id,
        'name' => 'Handcrafted Mahogany Chair',
        'slug' => 'mahogany-chair-' . uniqid(),
        'description' => 'Fine mahogany armchair carved by Bengal artisans.',
        'price' => 3500.00,
        'stock' => 10,
        'sku' => 'SKU-MC-' . uniqid(),
        'status' => 'active',
        'average_rating' => 4.9,
        'total_reviews' => 25,
    ]);
    ProductImage::create(['product_id' => $prodLocal->id, 'image_path' => 'chair.jpg', 'is_primary' => true]);

    $prodDistant = Product::create([
        'seller_id' => $userDelhi->id,
        'category_id' => $catB->id,
        'name' => 'Glazed Delhi Ceramic Vase',
        'slug' => 'ceramic-vase-' . uniqid(),
        'description' => 'Traditional blue glazed ceramic vase from Delhi.',
        'price' => 750.00,
        'stock' => 5,
        'sku' => 'SKU-CV-' . uniqid(),
        'status' => 'active',
        'average_rating' => 3.5,
        'total_reviews' => 8,
    ]);
    ProductImage::create(['product_id' => $prodDistant->id, 'image_path' => 'vase.jpg', 'is_primary' => true]);

    $controller = new ProductController();

    // Check A: Keyword Search
    $reqSearch = Request::create('/products', 'GET', ['search' => 'Mahogany']);
    $viewSearch = $controller->index($reqSearch);
    $productsSearch = $viewSearch->getData()['products'];
    echo "Check A (Search): " . ($productsSearch->contains('id', $prodLocal->id) && !$productsSearch->contains('id', $prodDistant->id) ? "PASS" : "FAIL") . "\n";

    // Check B: Category Filter
    $reqCat = Request::create('/products', 'GET', ['category' => $catB->slug]);
    $viewCat = $controller->index($reqCat);
    $productsCat = $viewCat->getData()['products'];
    echo "Check B (Category): " . ($productsCat->contains('id', $prodDistant->id) && !$productsCat->contains('id', $prodLocal->id) ? "PASS" : "FAIL") . "\n";

    // Check C: Price Range Filter (min=1000, max=5000)
    $reqPrice = Request::create('/products', 'GET', ['min_price' => 1000, 'max_price' => 5000]);
    $viewPrice = $controller->index($reqPrice);
    $productsPrice = $viewPrice->getData()['products'];
    echo "Check C (Price Range): " . ($productsPrice->contains('id', $prodLocal->id) && !$productsPrice->contains('id', $prodDistant->id) ? "PASS" : "FAIL") . "\n";

    // Check D: Seller Rating Filter (min_rating = 4.0)
    $reqRating = Request::create('/products', 'GET', ['min_rating' => 4.0]);
    $viewRating = $controller->index($reqRating);
    $productsRating = $viewRating->getData()['products'];
    echo "Check D (Rating): " . ($productsRating->contains('id', $prodLocal->id) && !$productsRating->contains('id', $prodDistant->id) ? "PASS" : "FAIL") . "\n";

    // Check E: Proximity Radius Filter (radius=50km from Kolkata coords)
    // Distance Kolkata to Delhi is ~1300km, so Delhi product should be excluded!
    $reqRadius = Request::create('/products', 'GET', [
        'lat' => 22.572646,
        'lng' => 88.363895,
        'radius' => 50,
    ]);
    $viewRadius = $controller->index($reqRadius);
    $productsRadius = $viewRadius->getData()['products'];
    echo "Check E (Radius 50km): " . ($productsRadius->contains('id', $prodLocal->id) && !$productsRadius->contains('id', $prodDistant->id) ? "PASS" : "FAIL") . "\n";

    // Check F: Proximity Radius Filter with zero matching
    $reqFar = Request::create('/products', 'GET', [
        'lat' => 10.000000,
        'lng' => 10.000000,
        'radius' => 10,
    ]);
    $viewFar = $controller->index($reqFar);
    $productsFar = $viewFar->getData()['products'];
    echo "Check F (Zero proximity): " . ($productsFar->isEmpty() ? "PASS" : "FAIL") . "\n";

    // Check G: Server-Side Pagination
    echo "Check G (Paginator instance): " . ($productsSearch instanceof LengthAwarePaginator ? "PASS" : "FAIL") . "\n";
    echo "Check G (Paginator perPage): " . ($productsSearch->perPage() === 12 ? "PASS" : "FAIL") . "\n";

    // Check H: Category method
    $viewCatMethod = $controller->category(Request::create('/category/' . $catA->slug), $catA->slug);
    $catProducts = $viewCatMethod->getData()['products'];
    echo "Check H (Category Page Products): " . ($catProducts->contains('id', $prodLocal->id) && !$catProducts->contains('id', $prodDistant->id) ? "PASS" : "FAIL") . "\n";
    echo "Check H (Category Page Paginator): " . ($catProducts instanceof LengthAwarePaginator ? "PASS" : "FAIL") . "\n";

} finally {
    DB::rollBack();
}

echo "--- Completed Forensic Verification Script ---\n";
