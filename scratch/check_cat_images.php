<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Category::all() as $cat) {
    echo "ID: {$cat->id} | Name: {$cat->name} | Slug: {$cat->slug} | Image: {$cat->image}" . PHP_EOL;
}
