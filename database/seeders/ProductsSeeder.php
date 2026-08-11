<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            'iOS - iPhone 7',
            'iOS - iPhone 8',
            'iOS - iPhone 12',
            'Android - Xiaomi',
            'Android - Redmi',
        ];

        foreach ($cats as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $ios7 = Category::where('name', 'iOS - iPhone 7')->first();
        $ios8 = Category::where('name', 'iOS - iPhone 8')->first();
        $ios12 = Category::where('name', 'iOS - iPhone 12')->first();
        $xiaomi = Category::where('name', 'Android - Xiaomi')->first();
        $redmi = Category::where('name', 'Android - Redmi')->first();

        $samples = [
            [$ios12, 'iPhone 12 Pro', 'Apple', 'iPhone 12 Pro', 'iPhone 12 Pro with A14 Bionic, 6.1-inch display.', 799.00, ['ram' => '6GB', 'storage' => '128GB', 'color' => 'Graphite']],
            [$ios12, 'iPhone 12', 'Apple', 'iPhone 12', 'iPhone 12 6.1-inch.', 699.00, ['ram' => '4GB', 'storage' => '64GB']],
            [$ios8, 'iPhone 8', 'Apple', 'iPhone 8', 'iPhone 8 classic 4.7-inch.', 199.00, ['ram' => '2GB', 'storage' => '64GB']],
            [$ios7, 'iPhone 7', 'Apple', 'iPhone 7', 'iPhone 7 basic model.', 99.00, ['ram' => '2GB', 'storage' => '32GB']],
            [$xiaomi, 'Xiaomi Mi 11', 'Xiaomi', 'Mi 11', 'Xiaomi flagship with Snapdragon.', 699.00, ['ram' => '8GB', 'storage' => '128GB']],
            [$xiaomi, 'Xiaomi Redmi K40', 'Xiaomi', 'Redmi K40', 'Powerful mid-range.', 299.00, ['ram' => '6GB', 'storage' => '128GB']],
            [$redmi, 'Redmi Note 10', 'Redmi', 'Note 10', 'Affordable Redmi Note series.', 149.00, ['ram' => '4GB', 'storage' => '64GB']],
            [$redmi, 'Redmi 9A', 'Redmi', '9A', 'Budget smartphone.', 79.00, ['ram' => '2GB', 'storage' => '32GB']],
        ];

        foreach ($samples as $s) {
            [$cat, $name, $brand, $model, $desc, $price, $specs] = $s;
            Product::firstOrCreate([
                'category_id' => $cat->id,
                'name' => $name,
            ], [
                'brand' => $brand,
                'model' => $model,
                'description' => $desc,
                'price' => $price,
                'specs' => $specs,
            ]);
        }
    }
}
