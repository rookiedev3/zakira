<?php
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;

$controller = app(ProductController::class);
$request = Request::create('/c/products/create', 'GET');

$response = $controller->create($request);
$factory = app('view');
$data = $response->gatherData();
$data['errors'] = new Illuminate\Support\ViewErrorBag();
$body = $factory->make('products.create', $data)->render();

echo "HAS 'kategori 2': " . (strpos($body, 'kategori 2') !== false ? 'YES' : 'NO') . "\n";
echo "HAS 'Default Category': " . (strpos($body, 'Default Category') !== false ? 'YES' : 'NO') . "\n";
preg_match('/Kategori<\/label>([\s\S]*?)<!-- Categories -->/u', $body, $m);
echo "\n--- Categories block ---\n";
echo isset($m[1]) ? trim($m[1]) : '(not found)' . "\n";