<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach(\App\Models\Auction::with('product.images','product.primaryImage')->get() as $a) {
    $p = $a->product;
    echo "Auction #{$a->id} | Product: " . ($p ? $p->name : 'None') . " | Primary: " . ($p && $p->primaryImage ? $p->primaryImage->image_path : 'None') . "\n";
}
