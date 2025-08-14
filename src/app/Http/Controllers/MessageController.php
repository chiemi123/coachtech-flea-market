<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Purchase;
use App\Notifications\MessageReceived;

class MessageController extends Controller
{
    public function index(Purchase $purchase)
    {
        return $purchase->messages()->with('user:id,name')->orderBy('created_at')->get();
    }

    public function store(StoreMessageRequest $request, Purchase $purchase)
    {
        $me = $request->user();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('message_images', 'public');
        }

        // メッセージを作成
        $message = $purchase->messages()->create([
            'user_id'    => $me->id,
            'body'       => $request->input('body'),
            'image_path' => $imagePath,
        ]);

        // 最終メッセージ日時を更新
        $purchase->forceFill([
            'last_message_at' => now(),
        ])->save();

        // 相手ユーザーを特定
        $receiver = ($purchase->user_id === $me->id)
            ? optional($purchase->item)->user  // 自分が購入者 → 出品者に送る
            : $purchase->user;                 // 自分が出品者 → 購入者に送る

        // 通知を送信（Mailhog上で確認可能）
        if ($receiver) {
            $receiver->notify(new MessageReceived($message));
        }

        return back()->with('status', 'メッセージを送信しました。');
    }
}
