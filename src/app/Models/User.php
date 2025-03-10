<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image', // プロフィール画像
        'username',      // ユーザー名
        'postal_code',   // 郵便番号
        'address',       // 住所
        'building_name', // 建物名
        'profile_completed', // プロフィール完了状態

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_completed' => 'boolean', // プロフィール完了状態をboolean型にキャスト
    ];

    /**
     * ユーザーが出品した商品
     */
    public function listedItems()
    {
        return $this->hasMany(Item::class, 'user_id');
    }

    /**
     * ユーザーが購入した商品
     */
    public function purchasedItems()
    {
        return $this->belongsToMany(Item::class, 'purchases', 'user_id', 'item_id')
            ->withTimestamps(); // 中間テーブルを利用
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // 最新の住所を取得
    public function latestAddress()
    {
        return $this->hasOne(Address::class)->latestOfMany('created_at'); // addresses テーブルの最新の住所を取得
    }

    // ユーザー登録時に `addresses` に自動で住所を保存
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // `postal_code` や `address` が登録されている場合のみ `addresses` に保存
            if (!empty($user->postal_code) && !empty($user->address)) {
                $user->addresses()->create([
                    'user_id' => $user->id,
                    'postal_code' => $user->postal_code,
                    'address' => $user->address,
                    'building_name' => $user->building_name ?? null,
                ]);
            }
        });
    }
}
