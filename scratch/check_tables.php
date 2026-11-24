<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $vals = array_values((array)$table);
    $tableName = $vals[0];
    if (str_contains($tableName, 'bid') || str_contains($tableName, 'auction') || str_contains($tableName, 'product') || str_contains($tableName, 'image')) {
        echo "Table: {$tableName}" . PHP_EOL;
        print_r(Schema::getColumnListing($tableName));
    }
}
