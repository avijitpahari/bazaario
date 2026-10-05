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

$sellerDir = $viewsDir . '/seller';
if (is_dir($sellerDir)) {
    scanDirRecursive($sellerDir, $files);
}
$files[] = $viewsDir . '/layouts/seller.blade.php';

$tokens = [];
foreach ($files as $f) {
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    preg_match_all('/\b((?:bg|text|border|ring|shadow|rounded|font)-(?:brand|surface|on-surface|primary|secondary|heading|outline|error|custom)[^\s\"\'>]*)/', $c, $m);
    foreach ($m[1] as $t) {
        $tokens[$t] = ($tokens[$t] ?? 0) + 1;
    }
}
ksort($tokens);
print_r($tokens);
