<?php
$content = file_get_contents(__DIR__ . '/../../../resources/views/layouts/seller.blade.php');
preg_match_all('/class="([^"]+)"/', $content, $m);
$classes = [];
foreach ($m[1] as $classStr) {
    // split by space and blade expressions
    $parts = preg_split('/\s+/', $classStr);
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p !== '' && !str_starts_with($p, '{{') && !str_ends_with($p, '}}') && !str_contains($p, '?') && !str_contains($p, ':')) {
            $classes[$p] = true;
        }
    }
}
ksort($classes);

$appCss = file_get_contents(__DIR__ . '/../../../resources/css/app.css');
echo "Checking classes in layouts/seller.blade.php:\n";
foreach (array_keys($classes) as $cls) {
    if (preg_match('/^(bg|text|border|ring|shadow|font|rounded)-(.*)$/', $cls, $cm)) {
        $prefix = $cm[1];
        $token = $cm[2];
        // Check if token or arbitrary
        if (str_starts_with($token, '[') || in_array($token, ['white', 'black', 'transparent', 'current', 'inherit', 'xs', 'sm', 'base', 'lg', 'xl', '2xl', '3xl', '4xl', 'mono', 'sans', 'serif', 'normal', 'medium', 'semibold', 'bold', 'extrabold', 'full', 'none', 'md', 'amber-300', 'blue-200', 'blue-600'])) {
            continue;
        }
        // Check if in appCss
        if (!str_contains($appCss, $token)) {
            echo "Custom token not in app.css? class: $cls (token: $token)\n";
        }
    }
}
echo "Done checking layouts/seller.blade.php\n";
