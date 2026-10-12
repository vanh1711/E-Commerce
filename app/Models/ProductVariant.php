<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'version_name',
        'color',
        'color_code',
        'storage',
        'ram',
        'price',
        'original_price',
        'stock',
        'weight',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Nhãn hiển thị biến thể: "Pro Max / 512GB / Titan Sa Mạc"
     */
    public function getLabelAttribute(): string
    {
        return collect([
            $this->version_name,
            $this->storage,
            $this->color,
        ])->filter()->implode(' / ');
    }

    /**
     * Tính phần trăm giảm giá so với giá gốc
     */
    public function getDiscountPercentAttribute(): int
    {
        if (!$this->original_price || $this->original_price <= $this->price) {
            return 0;
        }
        return (int) round((1 - $this->price / $this->original_price) * 100);
    }

    /**
     * Kiểm tra còn hàng không
     */
    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    /**
     * URL ảnh biến thể (hỗ trợ cả http URL online và storage)
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return $this->product?->image_url ?? asset('images/products/iphone-16-pro-max.jpg');
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }
        return asset('storage/' . $this->image);
    }
}
