<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function show(Purchase $purchase)
    {
        $me = Auth::user();

        // 取引の基本情報
        $purchase->load(['item.user', 'user']);

        // アクセス権：買い手 or 出品者のみ
        abort_unless(
            $purchase->user_id === $me->id || optional($purchase->item)->user_id === $me->id,
            403
        );

        // 相手ユーザー
        $seller  = optional($purchase->item)->user; // 出品者
        $buyer   = $purchase->user;                 // 買い手
        $partner = ($buyer && $buyer->id === $me->id) ? $seller : $buyer;

        // 自分が出品者かどうか（ヘッダー右の「取引を完了する」表示制御などに使用）
        $isSeller = optional($purchase->item)->user_id === $me->id;

        $isBuyer = $purchase->user_id === $me->id;


        // メッセージ（発言者をEager Load）
        $messages = $purchase->messages()->with('user')->oldest()->get();

        // --- サイドバー用：その他の取引（購入者・出品者どちらにも表示）---
        // 進行中ステータス（必要に応じて調整）
        $inProgressStatuses = ['pending', 'paid', 'shipping'];

        $otherPurchases = Purchase::participating($me->id)
            ->whereIn('status', $inProgressStatuses)
            ->with([
                'item:id,name,item_image', // サイドバー表示に必要な最低限
            ])
            ->select(['id', 'item_id', 'user_id', 'status', 'last_message_at']) // 軽量化
            ->withCount([
                // 自分が未読のメッセージ（相手発言のみ）をカウント
                'messages as unread_count' => function ($q) use ($me) {
                    $q->where('user_id', '<>', $me->id)
                        ->whereDoesntHave('reads', fn($r) => $r->where('user_id', $me->id));
                },
            ])
            ->orderByDesc('last_message_at')
            ->get();

        // 自分がまだ読んでいない相手のメッセージを既読にする
        $unreadMessages = $purchase->messages()
            ->where('user_id', '!=', $me->id)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $me->id))
            ->get();

        DB::transaction(function () use ($unreadMessages, $me) {
            foreach ($unreadMessages as $message) {
                $message->reads()->create([
                    'user_id' => $me->id,
                    'read_at' => now(),
                ]);
            }
        });

        return view('purchases.chat', [
            'purchase'       => $purchase,
            'item'           => $purchase->item,
            'partner'        => $partner,
            'messages'       => $messages,
            'me'             => $me,
            'isSeller'       => $isSeller,      // ← 追加
            'isBuyer'       => $isBuyer,      // ← 追加
            'otherPurchases' => $otherPurchases, // ← 追加
            'isChatView' => true
        ]);
    }
}
