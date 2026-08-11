<?php

namespace App\Models;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'slug',
        'name',
        'model',
        'description',
        'price',
        'sale_price',
        'rating',
        'stock',
        'badge',
        'featured',
        'status',
        'ram',
        'storage',
        'specs',
        'image',
    ];

    protected $casts = [
        'specs' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getBrandSlugAttribute(): string
    {
        $brand = $this->brand;

        if ($brand instanceof Brand && $brand->slug) {
            return $brand->slug;
        }

        if (is_string($brand) && trim($brand) !== '') {
            return \Illuminate\Support\Str::slug($brand);
        }

        return 'phone';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
