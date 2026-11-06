<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Auction;
use App\Models\AuctionBid;

$bid24 = AuctionBid::where('created_at', '>=', now()->subDay())->sum('amount');
$liveCurr = Auction::where('status', 'live')->sum('current_price');
$ended24 = Auction::where('status', 'ended')->where('updated_at', '>=', now()->subDay())->sum('current_price');
echo "Bid 24h: {$bid24}\n";
echo "Live current_price: {$liveCurr}\n";
echo "Ended 24h current_price: {$ended24}\n";
