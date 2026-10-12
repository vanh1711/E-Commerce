<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'iPhone (Apple)',
            'Samsung Galaxy',
            'Xiaomi & Redmi',
            'Google Pixel',
            'Gaming Phone',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
