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
}
