<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class PhoneStoreSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['Apple', 'apple', 'https://via.placeholder.com/120x120?text=Apple'],
            ['Samsung', 'samsung', 'https://via.placeholder.com/120x120?text=Samsung'],
            ['Xiaomi', 'xiaomi', 'https://via.placeholder.com/120x120?text=Xiaomi'],
            ['OPPO', 'oppo', 'https://via.placeholder.com/120x120?text=OPPO'],
            ['vivo', 'vivo', 'https://via.placeholder.com/120x120?text=vivo'],
            ['realme', 'realme', 'https://via.placeholder.com/120x120?text=realme'],
            ['Google Pixel', 'pixel', 'https://via.placeholder.com/120x120?text=Pixel'],
            ['ASUS', 'asus', 'https://via.placeholder.com/120x120?text=ASUS'],
        ];

        foreach ($brands as [$name, $slug, $logo]) {
            Brand::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'logo' => $logo,
                'description' => "$name - điện thoại cao cấp, phong cách hiện đại.",
                'status' => 'active',
                'featured' => true,
            ]);
        }

        $categories = ['Flagship', 'Gaming', 'Camera', 'New Arrival'];
        foreach ($categories as $categoryName) {
            Category::firstOrCreate(['name' => $categoryName], ['slug' => Str::slug($categoryName)]);
        }

        $samples = [
            ['iPhone 17 Pro Max', 'apple', 'Flagship', 'iPhone 17 Pro Max', 'Siêu phẩm iPhone mới nhất với chip A20, camera 48MP và màn hình ProMotion.', 37000000, 32900000, 4.9, 12, 'Mới', '8GB', '256GB', 'iOS 18'],
            ['Galaxy S26 Ultra', 'samsung', 'Flagship', 'Galaxy S26 Ultra', 'Màn hình Infinity-O, camera 200MP và pin trâu dành cho người dùng cao cấp.', 32000000, 28900000, 4.8, 20, 'Giảm giá', '12GB', '256GB', 'Android 14'],
            ['Galaxy Z Fold Ultra', 'samsung', 'Gaming', 'Galaxy Z Fold Ultra', 'Thiết kế gập màn hình lớn, trải nghiệm mobile tuyệt đỉnh.', 54900000, 51900000, 4.7, 10, 'Trả góp 0%', '16GB', '512GB', 'Android 14'],
            ['Xiaomi 16 Ultra', 'xiaomi', 'Camera', 'Xiaomi 16 Ultra', 'Camera Leica, sạc 120W và cấu hình flagship trong tầm giá.', 19990000, 17990000, 4.7, 25, 'Giảm giá', '12GB', '256GB', 'Android 14'],
            ['OPPO Find X9 Pro', 'oppo', 'Flagship', 'Find X9 Pro', 'Thiết kế cao cấp, hiệu năng mạnh mẽ và màn hình AMOLED 120Hz.', 22990000, 21990000, 4.6, 18, 'Mới', '12GB', '256GB', 'Android 14'],
            ['vivo X300 Pro', 'vivo', 'Gaming', 'X300 Pro', 'Hiệu năng mạnh, pin trâu và sạc siêu nhanh cho game thủ.', 16990000, 15990000, 4.5, 30, 'Trả góp 0%', '12GB', '256GB', 'Android 14'],
            ['realme GT 8 Pro', 'realme', 'New Arrival', 'GT 8 Pro', 'Thiết kế thể thao, chip Snapdragon và camera chất lượng.', 12990000, 11990000, 4.4, 22, 'Giảm giá', '12GB', '256GB', 'Android 14'],
        ];

        foreach ($samples as $item) {
            [$name, $brandSlug, $categoryName, $model, $desc, $price, $salePrice, $rating, $stock, $badge, $ram, $storage, $os] = $item;
            $brand = Brand::where('slug', $brandSlug)->first();
            $category = Category::where('name', $categoryName)->first();

            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'model' => $model,
                    'description' => $desc,
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'rating' => $rating,
                    'stock' => $stock,
                    'featured' => true,
                    'badge' => $badge,
                    'ram' => $ram,
                    'storage' => $storage,
                    'specs' => [
                        'display' => '6.7" OLED',
                        'refresh_rate' => '120Hz',
                        'chip' => 'Snapdragon 8 Gen 4',
                        'front_camera' => '32MP',
                        'rear_camera' => '50MP + 48MP',
                        'battery' => '5000mAh',
                        'fast_charging' => '120W',
                        'os' => $os,
                        'connectivity' => '5G',
                        'water_resistance' => 'IP68',
                    ],
                    'image' => 'https://via.placeholder.com/720x720?text=' . urlencode($name),
                ]
            );
        }
    }
}
