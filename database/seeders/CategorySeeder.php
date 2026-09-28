<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Điện Thoại', 'description' => 'Flagship smartphones và điện thoại thông minh chính hãng.'],
            ['name' => 'Máy Tính Bảng', 'description' => 'iPad, Galaxy Tab và máy tính bảng cao cấp.'],
            ['name' => 'Phụ Kiện', 'description' => 'Cáp sạc, tai nghe, ốp lưng và phụ kiện công nghệ.'],
            ['name' => 'Âm Thanh', 'description' => 'Tai nghe không dây, loa Bluetooth chất lượng cao.'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description']]
            );
        }
    }
}
