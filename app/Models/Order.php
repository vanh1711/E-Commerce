<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'to_province_id',
        'to_district_id',
        'to_ward_code',
        'province_name',
        'district_name',
        'ward_name',
        'latitude',
        'longitude',
        'distance_km',
        'shipping_fee',
        'ghn_total_fee',
        'subtotal',
        'total_amount',
        'payment_method',
        'payment_status',
        'shipping_status',
        'ghn_order_code',
        'shipper_name',
        'shipper_phone',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function latestTransaction()
    {
        return $this->hasOne(PaymentTransaction::class)->latestOfMany();
    }

    // Aliases để tương thích 100% với các tên biến trong Lab06
    public function getTotalPriceAttribute(): float
    {
        return (float) ($this->attributes['total_amount'] ?? 0);
    }

    public function setTotalPriceAttribute($value): void
    {
        $this->attributes['total_amount'] = $value;
    }

    public function getNameAttribute(): ?string
    {
        return $this->customer_name ?? null;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->customer_phone ?? null;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->shipping_address ?? null;
    }

    public function canPayWithMomo(): bool
    {
        return $this->payment_status !== 'paid' && $this->shipping_status !== 'cancelled';
    }

    public function getPaymentMethodTextAttribute(): string
    {
        return match ($this->payment_method) {
            'cod'           => 'Tiền mặt khi nhận hàng (COD)',
            'momo'          => 'Ví điện tử MoMo (ATM / QR Code)',
            'bank_transfer' => 'Chuyển khoản VietQR Napas 24/7',
            'card'          => 'Thẻ tín dụng / Ghi nợ quốc tế',
            default         => ucfirst($this->payment_method),
        };
    }

    public function getPaymentStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'      => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
            'pending'   => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
            'failed'    => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800',
            'refunded'  => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800',
            default     => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300',
        };
    }

    public function getPaymentStatusTextAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'      => 'Đã thanh toán',
            'pending'   => 'Chờ thanh toán',
            'failed'    => 'Thanh toán thất bại',
            'refunded'  => 'Đã hoàn tiền',
            default     => ucfirst($this->payment_status),
        };
    }

    public function getShippingStatusBadgeAttribute(): string
    {
        return match ($this->shipping_status) {
            'pending'       => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
            'preparing'     => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800',
            'ready_to_pick' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800',
            'shipping'      => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800',
            'delivered'     => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
            'cancelled'     => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800',
            default         => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300',
        };
    }

    public function getShippingStatusTextAttribute(): string
    {
        return match ($this->shipping_status) {
            'pending'       => 'Chờ duyệt đơn',
            'preparing'     => 'Đang đóng gói',
            'ready_to_pick' => 'GHN chờ lấy hàng 📦',
            'shipping'      => 'Đang giao hàng 🚀',
            'delivered'     => 'Đã giao thành công ✨',
            'cancelled'     => 'Đã hủy',
            default         => ucfirst($this->shipping_status),
        };
    }
}
