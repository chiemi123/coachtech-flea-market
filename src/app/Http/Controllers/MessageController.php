<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Purchase;
use App\Models\Message;
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

        return back()->with('success', 'メッセージを送信しました。');
    }

    public function edit(Message $message)
    {
        $purchase = $message->purchase;
        $item = $purchase->item;
        $me = auth()->user();
        $messages = Message::where('purchase_id', $purchase->id)->with('user')->get();
        $editing = $message;
        $isSeller = optional($purchase->item)->user_id === $me->id;

        // 相手ユーザー
        $partner = $purchase->user_id === $me->id
            ? optional($item)->user
            : $purchase->user;

        // 他の取引
        $otherPurchases = Purchase::where('user_id', $me->id)
            ->where('id', '!=', $purchase->id)
            ->with('item')
            ->latest()
            ->get();

        return view('purchases.chat', compact(
            'purchase',
            'item',
            'me',
            'partner',
            'messages',
            'editing',
            'otherPurchases',
            'isSeller'
        ))->with('isChatView', true);
    }

    public function update(UpdateMessageRequest $request, Message $message)
    {
        $message->body = $request->input('body');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('messages', 'public');
            $message->image_path = $path;
        }

        $message->save();

        return redirect()->route('purchases.chat', ['purchase' => $message->purchase_id])
            ->with('success', 'メッセージを更新しました');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->back()->with('success', 'メッセージを削除しました');
    }
}
