<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;

echo "--- Starting Blade Template Rendering Forensic Check ---\n";

// 1. Render user.products.category with empty products
$paginatorEmpty = new LengthAwarePaginator([], 0, 12, 1);
$renderedEmpty = View::make('user.products.category', [
    'slug' => 'all',
    'category' => null,
    'categories' => Category::all(),
    'products' => $paginatorEmpty,
])->render();

echo "Category view (empty) length: " . strlen($renderedEmpty) . " bytes\n";
echo "Category view (empty) has 'No products found': " . (str_contains($renderedEmpty, 'No products found') ? "PASS" : "FAIL") . "\n";

// 2. Render user.products.category with actual DB products if any exist
$dbProducts = Product::with(['category', 'seller.sellerProfile', 'images', 'primaryImage'])
    ->where('status', 'active')
    ->paginate(12);

$renderedPopulated = View::make('user.products.category', [
    'slug' => 'all',
    'category' => null,
    'categories' => Category::all(),
    'products' => $dbProducts,
])->render();

echo "Category view (populated) length: " . strlen($renderedPopulated) . " bytes\n";
echo "Category view (populated) rendered without errors: PASS\n";

// 3. Render pages.how-it-works
$renderedHowItWorks = View::make('pages.how-it-works')->render();
echo "pages.how-it-works length: " . strlen($renderedHowItWorks) . " bytes: PASS\n";

echo "--- Blade Template Rendering Forensic Check Completed ---\n";
