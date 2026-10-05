<?php
$viewsDir = __DIR__ . '/../../../resources/views';
$files = [];

function scanDirRecursive($dir, &$results) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            scanDirRecursive($path, $results);
        } elseif (str_ends_with($item, '.blade.php')) {
            $results[] = $path;
        }
    }
}

$sellerFiles = [];
scanDirRecursive($viewsDir . '/seller', $sellerFiles);
$sellerFiles[] = $viewsDir . '/layouts/seller.blade.php';

// Check which views extend layouts.seller or are layouts/seller.blade.php
$extendsSeller = [$viewsDir . '/layouts/seller.blade.php'];
foreach ($sellerFiles as $sf) {
    $c = file_get_contents($sf);
    if (str_contains($c, "extends('layouts.seller')") || str_contains($c, 'extends("layouts.seller")')) {
        $extendsSeller[] = $sf;
    }
}

echo "Found " . count($extendsSeller) . " files extending layouts.seller\n";

// Check all tokens in these files
$tokens = [];
foreach ($extendsSeller as $f) {
    $c = file_get_contents($f);
    preg_match_all('/\b((?:bg|text|border|ring|shadow|rounded|font)-(?:brand|surface|on-surface|primary|secondary|heading|outline|error|custom)[^\s\"\'>]*)/', $c, $m);
    foreach ($m[1] as $t) {
        $tokens[$t] = ($tokens[$t] ?? 0) + 1;
    }
}
ksort($tokens);
print_r($tokens);
