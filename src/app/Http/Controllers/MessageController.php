<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Purchase $purchase)
    {
        // Day1はとりあえず一覧をJSONで返す or ビューに委譲でもOK
        return $purchase->messages()->with('user:id,name')->orderBy('created_at')->get();
    }

    public function store(Request $request, Purchase $purchase)
    {
        $me = auth()->id();

        // 認可チェック
        abort_unless(
            $purchase->user_id === $me || optional($purchase->item)->user_id === $me,
            403
        );

        // バリデーション
        $validated = $request->validate([
            'body'  => 'nullable|string|max:400',
            'image' => 'nullable|image|max:2048', // 最大2MB
        ]);

        // 画像アップロード
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('messages', 'public');
        }

        // メッセージ保存
        $purchase->messages()->create([
            'user_id'    => $me,
            'body'       => $validated['body'],
            'image_path' => $imagePath,
        ]);

        // 並び順のための更新
        $purchase->forceFill(['last_message_at' => now()])->save();

        return back();
    }
}
