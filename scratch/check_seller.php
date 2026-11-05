<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

$u = \App\Models\User::find(42);
\Illuminate\Support\Facades\Auth::login($u);

echo "Auth::user()->id: " . \Illuminate\Support\Facades\Auth::user()?->id . "\n";
echo "Auth::user()->role: " . \Illuminate\Support\Facades\Auth::user()?->role . "\n";
echo "Auth::guard('seller')->check(): " . (\Illuminate\Support\Facades\Auth::guard('seller')->check() ? 'true' : 'false') . "\n";

$view = view('layouts.seller')->render();
$pos = strpos($view, 'Merchant Store Quick Card');
if ($pos !== false) {
    echo substr($view, $pos, 600) . "\n";
} else {
    echo "Not found in layouts.seller\n";
}
