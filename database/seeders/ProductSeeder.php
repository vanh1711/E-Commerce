<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $phoneCategory = Category::firstOrCreate(['name' => 'Điện Thoại'], ['description' => 'Flagship smartphones chính hãng.']);
        $tabletCategory = Category::firstOrCreate(['name' => 'Máy Tính Bảng'], ['description' => 'iPad và máy tính bảng.']);
        $accCategory = Category::firstOrCreate(['name' => 'Phụ Kiện'], ['description' => 'Phụ kiện công nghệ.']);

        $products = [
            [
                'category_id' => $phoneCategory->id,
                'name'        => 'iPhone 16 Pro Max 256GB',
                'brand'       => 'Apple',
                'model'       => 'A3296',
                'description' => 'Flagship cao cấp nhất của Apple trang bị chip A18 Pro, khung titan, nút Camera Control thế hệ mới.',
                'price'       => 34990000,
                'stock'       => 50,
                'weight'      => 227,
                'tags'        => ['gaming', 'camera', 'battery'],
            ],
            [
                'category_id' => $phoneCategory->id,
                'name'        => 'Samsung Galaxy S24 Ultra 5G',
                'brand'       => 'Samsung',
                'model'       => 'SM-S928B',
                'description' => 'Quyền năng Galaxy AI đỉnh cao, màn hình phẳng Dynamic AMOLED 2X 120Hz và bút S-Pen tích hợp.',
                'price'       => 29990000,
                'stock'       => 45,
                'weight'      => 232,
                'tags'        => ['gaming', 'camera', 'battery'],
            ],
            [
                'category_id' => $phoneCategory->id,
                'name'        => 'iPhone 15 Pro 128GB',
                'brand'       => 'Apple',
                'model'       => 'A3102',
                'description' => 'Khung viền titan siêu nhẹ, chip Apple A17 Pro và cổng kết nối USB-C chuẩn tốc độ cao.',
                'price'       => 24990000,
                'stock'       => 30,
                'weight'      => 187,
                'tags'        => ['camera', 'compact', 'gaming'],
            ],
            [
                'category_id' => $phoneCategory->id,
                'name'        => 'Samsung Galaxy Z Fold6 5G',
                'brand'       => 'Samsung',
                'model'       => 'SM-F956B',
                'description' => 'Thiết kế gập siêu mỏng nhẹ đột phá, màn hình ngoài tỷ lệ mới cùng sức mạnh AI đa nhiệm.',
                'price'       => 41990000,
                'stock'       => 20,
                'weight'      => 239,
                'tags'        => ['gaming', 'battery'],
            ],
            [
                'category_id' => $phoneCategory->id,
                'name'        => 'Xiaomi 14 Ultra 512GB',
                'brand'       => 'Xiaomi',
                'model'       => '24030PN60G',
                'description' => 'Hợp tác cùng huyền thoại Leica với cụm 4 camera 50MP cảm biến 1-inch biến thiên khẩu độ.',
                'price'       => 28990000,
                'stock'       => 25,
                'weight'      => 220,
                'tags'        => ['camera', 'gaming'],
            ],
            [
                'category_id' => $phoneCategory->id,
                'name'        => 'Google Pixel 9 Pro 128GB',
                'brand'       => 'Google',
                'model'       => 'GEC77',
                'description' => 'Trải nghiệm Android thuần khiết với chip Google Tensor G4 và trí tuệ nhân tạo Gemini Nano.',
                'price'       => 26500000,
                'stock'       => 15,
                'weight'      => 199,
                'tags'        => ['camera', 'compact'],
            ],
            [
                'category_id' => $tabletCategory->id,
                'name'        => 'iPad Pro 11 inch M4 Wi-Fi 256GB',
                'brand'       => 'Apple',
                'model'       => 'A2836',
                'description' => 'Độ mỏng kỷ lục 5.3mm, màn hình Ultra Retina XDR OLED kép cùng sức mạnh vượt trội từ chip M4.',
                'price'       => 28990000,
                'stock'       => 35,
                'weight'      => 444,
                'tags'        => ['gaming', 'battery'],
            ],
            [
                'category_id' => $tabletCategory->id,
                'name'        => 'Samsung Galaxy Tab S9 Ultra',
                'brand'       => 'Samsung',
                'model'       => 'SM-X910',
                'description' => 'Màn hình khổng lồ 14.6 inch Dynamic AMOLED 2X, chuẩn kháng nước IP68 đầu tiên trên tablet.',
                'price'       => 27490000,
                'stock'       => 20,
                'weight'      => 732,
                'tags'        => ['gaming', 'battery'],
            ],
            [
                'category_id' => $accCategory->id,
                'name'        => 'Apple AirPods Pro 2 (USB-C)',
                'brand'       => 'Apple',
                'model'       => 'A3048',
                'description' => 'Chống ồn chủ động ANC gấp 2 lần, âm thanh không gian cá nhân hóa và sạc qua chuẩn USB-C.',
                'price'       => 5690000,
                'stock'       => 80,
                'weight'      => 50,
                'tags'        => ['compact'],
            ],
            [
                'category_id' => $accCategory->id,
                'name'        => 'Củ Sạc Nhanh Anker 65W GaNPrime',
                'brand'       => 'Anker',
                'model'       => 'A2668',
                'description' => 'Công nghệ GaNPrime độc quyền, hỗ trợ 3 cổng ra sạc đồng thời cho laptop, tablet và điện thoại.',
                'price'       => 1290000,
                'stock'       => 100,
                'weight'      => 130,
                'tags'        => ['compact'],
            ],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['name' => $p['name']],
                [
                    'category_id' => $p['category_id'],
                    'brand'       => $p['brand'],
                    'model'       => $p['model'],
                    'description' => $p['description'],
                    'price'       => $p['price'],
                    'stock'       => $p['stock'],
                    'weight'      => $p['weight'],
                    'tags'        => $p['tags'],
                ]
            );
        }
    }
}
