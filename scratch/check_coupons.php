<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Coupons: " . \App\Models\Coupon::count() . "\n";
foreach(\App\Models\Coupon::get() as $c) {
    echo "{$c->code} | {$c->discount_type} | {$c->discount_value} | {$c->status} | Expires: {$c->expires_at}\n";
}
