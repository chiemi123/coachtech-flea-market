<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // カテゴリーに属するアイテム（多対多）
    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_items');
    }
}
