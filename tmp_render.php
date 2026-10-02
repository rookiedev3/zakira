<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

$request = Request::create('/c/products/create', 'GET');
$response = $kernel->handle($request)->send();
$body = $response->getContent();

echo "STATUS: " . $response->status() . "\n";
echo "HAS 'kategori 2': " . (strpos($body, 'kategori 2') !== false ? 'YES' : 'NO') . "\n";
echo "HAS 'Default Category': " . (strpos($body, 'Default Category') !== false ? 'YES' : 'NO') . "\n";

// Extract the categories block
preg_match('/Kategori<\/label>([\s\S]*?)<!-- Categories -->/u', $body, $m);
echo "\n--- Categories block ---\n";
echo isset($m[1]) ? trim($m[1]) : '(not found)' . "\n";