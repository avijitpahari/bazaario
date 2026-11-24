<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USERS ===" . PHP_EOL;
foreach (\App\Models\User::all() as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: {$user->role}" . PHP_EOL;
}

echo PHP_EOL . "=== SELLER PROFILES ===" . PHP_EOL;
foreach (\App\Models\SellerProfile::all() as $sp) {
    echo "ID: {$sp->id} | User ID: {$sp->user_id} | Shop: {$sp->shop_name} | Status: {$sp->status} | Slug: {$sp->shop_slug}" . PHP_EOL;
}

echo PHP_EOL . "=== CATEGORIES ===" . PHP_EOL;
foreach (\App\Models\Category::all() as $cat) {
    echo "ID: {$cat->id} | Name: {$cat->name} | Slug: {$cat->slug} | Parent: " . ($cat->parent_id ?? 'null') . PHP_EOL;
}
