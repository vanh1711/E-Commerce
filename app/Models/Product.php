<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'model',
        'description',
        'price',
        'weight',
        'specs',
        'image',
        'gallery',
        'tags',
        'stock',
    ];

    protected $casts = [
        'specs' => 'array',
        'gallery' => 'array',
        'tags' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Danh sách các biến thể (phiên bản) của sản phẩm
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Tổng tồn kho cộng dồn từ tất cả biến thể (nếu có biến thể)
     * Fallback về cột stock của products nếu chưa có biến thể
     */
    public function getTotalStockAttribute(): int
    {
        if ($this->variants()->exists()) {
            return (int) $this->variants()->sum('stock');
        }
        return (int) ($this->stock ?? 0);
    }

    /**
     * Giá thấp nhất trong các biến thể
     */
    public function getMinPriceAttribute()
    {
        if ($this->variants()->exists()) {
            return $this->variants()->min('price');
        }
        return $this->price;
    }

    /**
     * Giá cao nhất trong các biến thể
     */
    public function getMaxPriceAttribute()
    {
        if ($this->variants()->exists()) {
            return $this->variants()->max('price');
        }
        return $this->price;
    }

    /**
     * Kiểm tra sản phẩm có biến thể hay không
     */
    public function getHasVariantsAttribute(): bool
    {
        return $this->variants()->exists();
    }

    /**
     * Xác định nhóm nhu cầu sử dụng (Needs-based Tags)
     * gaming: Chơi game khủng
     * camera: Chụp ảnh đỉnh
     * battery: Pin trâu cả ngày
     * compact: Gọn nhẹ sang trọng
     */
    public function getNeedsTagsAttribute(): array
    {
        // 1. Ưu tiên lấy từ mảng tags do Admin chỉ định trực tiếp
        if (is_array($this->tags) && count($this->tags) > 0) {
            return array_values(array_unique($this->tags));
        }

        // 2. Fallback Heuristic tự động phân loại cho sản phẩm cũ chưa chọn tag
        $tags = [];
        $nameLower = mb_strtolower($this->name, 'UTF-8');
        $descLower = mb_strtolower($this->description ?? '', 'UTF-8');
        $specs = is_array($this->specs) ? $this->specs : [];
        $ram = intval(preg_replace('/[^0-9]/', '', $specs['ram'] ?? ''));

        // 1. Gaming: RAM >= 8GB hoặc có từ khóa Pro/Max/Ultra/Gaming/Snapdragon/Dimensity
        if ($ram >= 8 || str_contains($nameLower, 'pro') || str_contains($nameLower, 'ultra') || str_contains($nameLower, 'gaming') || str_contains($nameLower, 'mi 11') || str_contains($nameLower, 's24') || str_contains($nameLower, 'iphone 16') || str_contains($nameLower, 'iphone 15')) {
            $tags[] = 'gaming';
        }

        // 2. Camera: Pro Max, Ultra, iPhone 12/13/14/15/16, S24, Xiaomi Flagship
        if (str_contains($nameLower, 'pro') || str_contains($nameLower, 'ultra') || str_contains($nameLower, 'camera') || str_contains($nameLower, 'iphone') || str_contains($descLower, 'camera')) {
            $tags[] = 'camera';
        }

        // 3. Battery: Plus, Ultra, Max, Pro Max hoặc dòng máy lớn
        if (str_contains($nameLower, 'plus') || str_contains($nameLower, 'max') || str_contains($nameLower, 'ultra') || $ram >= 6 || str_contains($descLower, 'pin')) {
            $tags[] = 'battery';
        }

        // 4. Compact: Màn hình nhỏ, mỏng nhẹ, iPhone mini, iPhone 7, 8, 12, 13, 14, SE
        if (str_contains($nameLower, 'mini') || str_contains($nameLower, 'iphone 7') || str_contains($nameLower, 'iphone 8') || str_contains($nameLower, 'iphone se') || (!str_contains($nameLower, 'max') && !str_contains($nameLower, 'ultra') && !str_contains($nameLower, 'plus'))) {
            $tags[] = 'compact';
        }

        // Mặc định luôn có ít nhất 1 tag
        if (empty($tags)) {
            $tags[] = 'gaming';
        }

        return array_values(array_unique($tags));
    }
}

