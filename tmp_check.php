<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

echo "=== Category::all() ===\n";
foreach (Category::all() as $c) {
    echo $c->id . ' - ' . $c->name . ' - is_active=' . var_export($c->is_active, true) . "\n";
}

echo "\n=== Category::active() ===\n";
foreach (Category::active()->get() as $c) {
    echo $c->id . ' - ' . $c->name . ' - is_active=' . var_export($c->is_active, true) . "\n";
}
echo "active count: " . Category::active()->count() . "\n";

echo "\n=== Category::active()->orderBy('name')->get(['id','name']) ===\n";
foreach (Category::active()->orderBy('name')->get(['id', 'name']) as $c) {
    echo $c->id . ' - ' . $c->name . "\n";
}

echo "\n=== raw query ===\n";
$pdo = DB::connection()->getPdo();
foreach ($pdo->query("SELECT id, name, is_active FROM categories ORDER BY id") as $r) {
    echo $r['id'] . ' - ' . $r['name'] . ' - ' . $r['is_active'] . "\n";
}