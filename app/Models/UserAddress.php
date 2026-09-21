<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone',
        'address_line',
        'to_province_id',
        'to_district_id',
        'to_ward_code',
        'province_name',
        'district_name',
        'ward_name',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy địa chỉ đầy đủ chuẩn GHN: "Số nhà, Phường/Xã, Quận/Huyện, Tỉnh/Thành"
     */
    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line,
            $this->ward_name,
            $this->district_name,
            $this->province_name,
        ])->filter()->implode(', ');
    }
}
