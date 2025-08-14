<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'item_id', 'address_id', 'payment_method', 'status', 'transaction_id', 'last_message_at', 'completed_at',];

    protected $casts = [
        'last_message_at' => 'datetime',
        'completed_at'    => 'datetime',
    ];

    // 購入者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // buyerはエイリアスとしてuser()をそのまま返す
    public function buyer()
    {
        return $this->user();
    }

    // 購入した商品
    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    // 配送先住所
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // 取引メッセージ
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // 取引評価

    public function ratingBy($user)
    {
        return Rating::where('purchase_id', $this->id)
            ->where('rater_id', $user->id)
            ->first();
    }


    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * 自分が参加している取引（買い手 or 出品者）に限定
     */
    public function scopeParticipating($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId) // 買い手
                ->orWhereHas('item', function ($iq) use ($userId) { // 出品者
                    $iq->where('user_id', $userId);
                });
        });
    }
}
