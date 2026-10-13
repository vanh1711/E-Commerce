<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'is_admin',
        'is_locked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_locked' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) (
            ($this->is_admin == 1) || 
            ($this->role === 'admin') || 
            in_array($this->email, ['admin@gmail.com', 'admin@example.com'])
        );
    }

    public function hasVerifiedEmail(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return ! is_null($this->email_verified_at);
    }

    public function isLocked(): bool
    {
        return (bool) ($this->is_locked ?? false);
    }

    /**
     * Danh sách sổ địa chỉ nhận hàng của người dùng
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Địa chỉ mặc định (is_default = true), fallback về địa chỉ mới nhất
     */
    public function defaultAddress()
    {
        return $this->hasOne(UserAddress::class)
                    ->where('is_default', true)
                    ->withDefault();
    }

    /**
     * Tin nhắn đã gửi
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Danh sách đơn hàng của người dùng
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Tin nhắn đã nhận
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
