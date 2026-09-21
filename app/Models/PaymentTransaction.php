<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_order_id',
        'transaction_id',
        'amount',
        'status',
        'result_code',
        'message',
        'request_payload',
        'response_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'request_payload'  => 'array',
            'response_payload' => 'array',
            'paid_at'          => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'paid'      => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'initiated' => 'bg-blue-50 text-blue-700 border-blue-200',
            'failed'    => 'bg-rose-50 text-rose-700 border-rose-200',
            'cancelled' => 'bg-slate-50 text-slate-700 border-slate-200',
            default     => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'paid'      => 'Thành công',
            'initiated' => 'Đang chờ xử lý',
            'failed'    => 'Thất bại',
            'cancelled' => 'Đã hủy',
            default     => 'Chờ thanh toán',
        };
    }
}
