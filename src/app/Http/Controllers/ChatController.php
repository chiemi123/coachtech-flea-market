<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function show(Purchase $purchase)
    {
        $me = Auth::user();

        // 必要な関連を読み込む（出品者は item.user から）
        $purchase->load(['item.user', 'user']);

        // 参加者チェック（任意）: 自分が買い手 or 出品者でなければ403
        abort_unless(
            $purchase->user_id === $me->id || optional($purchase->item)->user_id === $me->id,
            403
        );

        // 相手ユーザーを求める
        $seller  = optional($purchase->item)->user; // 出品者
        $buyer   = $purchase->user;                 // 買い手
        $partner = ($buyer && $buyer->id === $me->id) ? $seller : $buyer;

        // メッセージは user を eager load
        $messages = $purchase->messages()->with('user')->oldest()->get();

        return view('purchases.chat', [
            'purchase'  => $purchase,
            'item'      => $purchase->item,
            'partner'   => $partner,
            'messages'  => $messages,
            'me'        => $me,
        ]);
    }
}
