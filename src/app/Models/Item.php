<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id',
        'condition_id',
        'name',
        'brand',
        'description',
        'price',
        'item_image',
        'sold_out',
        'likes_count',
        'comments_count'
    ];

    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    // 状態
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    // カテゴリー（多対多）
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items');
    }

    // いいね（多対多）
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // コメント（1対多）
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * 商品を購入したユーザー
     */
    public function buyers()
    {
        return $this->belongsToMany(User::class, 'purchases', 'item_id', 'user_id')
            ->withTimestamps(); // 中間テーブルを利用
    }

    // 商品が売り切れかどうか判定するアクセサ
    public function getIsSoldAttribute()
    {
        return $this->sold_out == true;
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
