<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['success', 'cancel']);
    }

    // 商品購入画面
    public function show($item_id)
    {

        $user = Auth::user();
        // 購入する商品情報を取得
        $item = Item::findOrFail($item_id);

        // ✅ ユーザーの最新の住所を取得
        $address = $user->latestAddress ?? ($user->postal_code ? $user : null);

        // 🚀 `session('address_id')` に `user_table` をセット（デフォルトの住所を識別）
        if (!session()->has('address_id')) {
            session(['address_id' => optional($address)->id ?? 'user_table']);
        }

        // 🚀 `session('address_id')` が `user_table` の場合、`users` の住所を使用
        $addressId = session('address_id');
        $address = ($addressId === 'user_table') ? $user : $address;

        // ✅ セッションに保存された支払い方法を取得
        $payment_method = session('payment_method', null);

        return view('purchase.show', compact('item', 'address', 'payment_method'));
    }

    public function confirm(PurchaseRequest $request, $item_id)
    {

        $user = auth()->user();

        $addressId = $request->input('address_id', session('address_id'));


        // 🚀 `address_id` が `null` なら `users` テーブルの住所を使用
        if (!$addressId) {
            $addressId = 'user_table'; // ユーザーのデフォルト住所を識別
        }

        // 🚨 住所がない場合はエラー
        if (!$addressId) {
            return redirect()->route('purchase.show', ['item_id' => $item_id])
                ->withErrors(['address_id' => '配送先を選択してください。']);
        }


        // ✅ セッションに `address_id` と `payment_method` を保存
        session(['address_id' => $addressId, 'payment_method' => $request->payment_method]);
        session()->save(); // 明示的にセッションを保存

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    public function checkout(Request $request, $item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        // ✅ 住所情報 & 支払い方法を取得
        if (!$addressId = session('address_id') ?: $request->input('address_id')) {
            return redirect()->route('purchase.show', $item_id)->withErrors(['address_id' => '配送先を選択してください。']);
        }

        if (!$paymentMethod = session('payment_method')) {
            return redirect()->route('purchase.show', $item_id)->withErrors(['payment_method' => '支払い方法を選択してください。']);
        }

        // ✅ Stripe APIキー設定 & Checkout セッション作成
        Stripe::setApiKey(config('services.stripe.secret'));
        $checkoutSession = StripeSession::create([
            'payment_method_types' => [$paymentMethod === 'クレジットカード' ? 'card' : 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'item_id' => $item->id,
                'address_id' => $addressId,
            ],
            'success_url' => url('/purchase/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.cancel'),
        ]);

        return redirect($checkoutSession->url);
    }

    // ✅ 決済成功後の処理（DBを更新）
    public function success(Request $request)
    {

        Stripe::setApiKey(config('services.stripe.secret'));

        // セッションIDを取得
        $session_id = $request->query('session_id');
        if (!$session_id) {
            return redirect()->route('items.index')->withErrors(['error' => '決済情報が取得できませんでした。']);
        }

        // ✅ Checkout セッション情報を取得
        $checkoutSession = \Stripe\Checkout\Session::retrieve($session_id);

        // ✅ `metadata` から `item_id` と `address_id` を取得
        $item_id = $checkoutSession->metadata->item_id ?? null;
        $address_id = $checkoutSession->metadata->address_id ?? null;

        // ✅ デバッグ
        dd($item_id, $address_id); // 🎯 ここで取得した値を確認！

        // ✅ `item_id` が取得できない場合はエラー
        if (!$item_id || !$address_id) {
            return redirect()->route('items.index')->withErrors(['error' => '商品情報が見つかりませんでした。']);
        }

        // ✅ 支払いステータスを取得
        $payment_status = $checkoutSession->payment_status;
        if ($checkoutSession->payment_intent) {
            $payment_status = \Stripe\PaymentIntent::retrieve($checkoutSession->payment_intent)->status;
        }

        // ✅ 決済成功時に購入情報を保存
        if ($payment_status === 'succeeded') {
            $purchase = Purchase::create([
                'user_id' => auth()->id(),
                'item_id' => $item_id,
                'address_id' => $address_id,
                'payment_method' => session('payment_method'),
                'status' => 'completed',
                'transaction_id' => $session_id,
            ]);

            // ✅ `purchase.success` ビューを表示
            return view('purchase.success', compact('purchase'));
        }

        return redirect()->route('items.index')->withErrors(['error' => '決済が完了していません。']);
    }



    public function cancel()
    {
        return view('purchase.cancel');
    }
}
