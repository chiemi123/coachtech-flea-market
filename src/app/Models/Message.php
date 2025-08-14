<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'user_id', 'body', 'image_path', 'edited_at',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reads()
    {
        return $this->hasMany(MessageRead::class);
    }

    /**
     * UI用：画像URLを統一的に取得
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path
            ? \Storage::disk('public')->url($this->image_path)
            : null;
    }

    /**
     * 既読を付与（無ければ作成、あれば更新）
     */
    public function markReadBy($userId)
    {
        $this->reads()->updateOrCreate(
            ['user_id' => $userId],
            ['read_at' => now()]
        );
    }
}
