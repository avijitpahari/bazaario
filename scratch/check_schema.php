<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== PRODUCTS COLUMNS ===" . PHP_EOL;
print_r(Schema::getColumnListing('products'));

echo "=== AUCTIONS COLUMNS ===" . PHP_EOL;
print_r(Schema::getColumnListing('auctions'));

echo "=== AUCTION_BIDS COLUMNS ===" . PHP_EOL;
print_r(Schema::getColumnListing('auction_bids'));

echo "=== SELLER_PROFILES COLUMNS ===" . PHP_EOL;
print_r(Schema::getColumnListing('seller_profiles'));
