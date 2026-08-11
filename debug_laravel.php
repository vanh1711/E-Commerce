<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$count = Product::whereNull('slug')->orWhere('slug', '')->count();
echo "MISSING_SLUG={$count}\n";
$rows = Product::whereNull('slug')->orWhere('slug', '')->limit(10)->get();
foreach ($rows as $row) {
    echo "ID={$row->id} NAME={$row->name} BRAND_ID={$row->brand_id} SLUG={$row->slug}\n";
}
