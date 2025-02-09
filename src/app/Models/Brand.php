<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // ブランドに属するアイテム
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
