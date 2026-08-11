<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Str;

$missing = Product::whereNull('slug')->orWhere('slug', '')->get();
foreach ($missing as $product) {
    $product->slug = Str::slug($product->name) ?: 'product-' . $product->id;
    $product->save();
    echo "Updated product {$product->id} => slug={$product->slug}\n";
}

echo 'Done. Total updated: ' . $missing->count() . "\n";
