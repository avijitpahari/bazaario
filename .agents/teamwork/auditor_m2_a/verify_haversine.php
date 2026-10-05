<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerProfile;

$profile = new SellerProfile();
$profile->latitude = 22.572646;
$profile->longitude = 88.363895; // Kolkata

// Test 1: Self distance
$d0 = $profile->distanceTo(22.572646, 88.363895);
echo "Self distance: {$d0} km (Expected: 0.0)\n";

// Test 2: Kolkata to Mumbai (19.076090, 72.877426)
$dMumbai = $profile->distanceTo(19.076090, 72.877426);
echo "Kolkata to Mumbai: {$dMumbai} km (Expected ~1654.5 km)\n";

// Test 3: Kolkata to Contai (21.778124, 87.751624)
$dContai = $profile->distanceTo(21.778124, 87.751624);
echo "Kolkata to Contai: {$dContai} km (Expected ~108.9 km)\n";

// Test 4: Null values handling
$dNull = $profile->distanceTo(null, null);
echo "Null lat/lng: {$dNull} km (Expected: 0.0)\n";

// Test 5: Digha to Contai (~21.626600, 87.507400 to 21.778124, 87.751624)
$dighaProfile = new SellerProfile(['latitude' => 21.626600, 'longitude' => 87.507400]);
$dighaToContai = $dighaProfile->distanceTo(21.778124, 87.751624);
echo "Digha to Contai: {$dighaToContai} km (Expected ~30.4 km)\n";
