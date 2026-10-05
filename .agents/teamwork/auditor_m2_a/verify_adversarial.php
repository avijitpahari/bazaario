<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;

echo "--- Starting Adversarial Stress Testing ---\n";

$controller = new ProductController();

// 1. SQL Injection in search parameter
$sqlInjections = [
    "' OR 1=1 --",
    "'; DROP TABLE products; --",
    "admin' --",
    "1' UNION SELECT * FROM users --",
];

foreach ($sqlInjections as $sqli) {
    try {
        $req = Request::create('/products', 'GET', ['search' => $sqli]);
        $res = $controller->index($req);
        echo "SQLi search test [{$sqli}]: PASS (no error, returned view)\n";
    } catch (\Throwable $e) {
        echo "SQLi search test [{$sqli}]: FAIL (" . $e->getMessage() . ")\n";
    }
}

// 2. SQL Injection in category and sort
$sortInjections = [
    "price asc; DROP TABLE users;",
    "id desc, (SELECT sleep(5))",
    "invalid_sort_key",
];

foreach ($sortInjections as $sort) {
    try {
        $req = Request::create('/products', 'GET', ['sort' => $sort]);
        $res = $controller->index($req);
        echo "Sort test [{$sort}]: PASS (defaulted or executed safely)\n";
    } catch (\Throwable $e) {
        echo "Sort test [{$sort}]: FAIL (" . $e->getMessage() . ")\n";
    }
}

// 3. Coordinate extremes & Non-numeric inputs
$coordinateTests = [
    ['lat' => 90.0, 'lng' => 0.0, 'radius' => 100, 'label' => 'North Pole'],
    ['lat' => -90.0, 'lng' => 0.0, 'radius' => 100, 'label' => 'South Pole'],
    ['lat' => 0.0, 'lng' => 0.0, 'radius' => 100, 'label' => 'Null Island'],
    ['lat' => 'not-a-number', 'lng' => 'also-not', 'radius' => 'bad', 'label' => 'String coords'],
    ['lat' => 22.57, 'lng' => 88.36, 'radius' => -50, 'label' => 'Negative radius'],
    ['lat' => 22.57, 'lng' => 88.36, 'radius' => 99999999, 'label' => 'Extreme radius'],
];

foreach ($coordinateTests as $tc) {
    try {
        $req = Request::create('/products', 'GET', [
            'lat' => $tc['lat'],
            'lng' => $tc['lng'],
            'radius' => $tc['radius'],
        ]);
        $res = $controller->index($req);
        echo "Coordinate test [{$tc['label']}]: PASS\n";
    } catch (\Throwable $e) {
        echo "Coordinate test [{$tc['label']}]: FAIL (" . $e->getMessage() . ")\n";
    }
}

// 4. XSS in search string rendered in index view
$xssPayload = '<script>alert("XSS")</script>';
$reqXss = Request::create('/products', 'GET', ['search' => $xssPayload]);
$resXss = $controller->index($reqXss);
$viewOutput = $resXss->render();
if (str_contains($viewOutput, '<script>alert("XSS")</script>')) {
    echo "XSS in view output: VULNERABILITY (Raw unescaped script tag found!)\n";
} else {
    echo "XSS in view output: PASS (Properly escaped or sanitized by Blade)\n";
}

// 5. Special characters in category slug
$categorySlugs = [
    'non-existent-category-slug-12345',
    '../../etc/passwd',
    '"><img src=x onerror=alert(1)>',
    'all',
];

foreach ($categorySlugs as $catSlug) {
    try {
        $req = Request::create('/category/' . urlencode($catSlug), 'GET');
        $res = $controller->category($req, $catSlug);
        echo "Category slug [{$catSlug}]: PASS (Handled without 500)\n";
    } catch (\Throwable $e) {
        echo "Category slug [{$catSlug}]: FAIL (" . $e->getMessage() . ")\n";
    }
}

echo "--- Completed Adversarial Stress Testing ---\n";
