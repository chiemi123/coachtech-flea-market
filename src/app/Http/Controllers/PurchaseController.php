<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
use App\Http\Requests\PurchaseRequest;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['success', 'cancel']);
    }

    //  商品購入画面
    public function show($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        //  `addresses` に登録がなければ `users` の住所を `addresses` に保存
        if (!Address::where('user_id', $user->id)->exists()) {
            $newAddress = Address::create([
                'user_id' => $user->id,
                'postal_code' => $user->postal_code,
                'address' => $user->address,
                'building_name' => $user->building_name ?? null,
            ]);
        }

        //  ユーザーの最新の住所（`addresses` にあればそれを取得）
        $address = $user->latestAddress ?? Address::where('user_id', $user->id)->first();

        //  `session('address_id')` に `addresses.id` をセット
        if (!session()->has('address_id') || session('address_id') === null) {
            session(['address_id' => optional($address)->id]);
        }

        //  支払い方法のセッションを取得
        $payment_method = session('payment_method', null);

        return view('purchases.show', compact('item', 'address', 'payment_method'));
    }


    //  住所と支払い方法を確認・保存
    public function confirm(PurchaseRequest $request, $item_id)
    {
        $user = auth()->user();
        $addressId = $request->input('address_id', session('address_id'));

        //  `session('address_id')` に `user_id` または `address_id` を保存
        session(['address_id' => $addressId, 'payment_method' => $request->payment_method]);
        session()->save();

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    //  Stripe 決済のチェックアウト
    public function checkout(Request $request, $item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        //  住所情報を取得
        $addressId = session('address_id') ?? $request->input('address_id');
        if (!$addressId) {
            return redirect()->route('purchase.show', $item_id)->withErrors(['address_id' => '配送先を選択してください。']);
        }

        //  支払い方法を取得
        $paymentMethod = session('payment_method', '');
        if (!$paymentMethod) {
            return redirect()->route('purchase.show', $item_id)->withErrors(['payment_method' => '支払い方法を選択してください。']);
        }

        $paymentMethod = in_array($paymentMethod, ['クレジットカード', 'credit_card', 'card']) ? 'card' : 'konbini';


        //  `address_id` を `integer` にキャスト
        $addressId = (int) $addressId;

        //  Stripe APIキー設定 & Checkout セッション作成
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $checkoutSession = StripeSession::create([
                'payment_method_types' => [$paymentMethod],
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
                    'user_id' => auth()->id(),
                    'address_id' => $addressId,
                    'payment_method' => $paymentMethod,
                ],
                'success_url' => url('/purchase/success') . '?session_id={CHECKOUT_SESSION_ID}', // ✅ ここは修正
                'cancel_url' => route('purchase.cancel'),
            ]);
        } catch (\Exception $e) {
            Log::error("Stripe Checkout Session の作成に失敗しました", [
                'user_id' => auth()->id(),
                'item_id' => $item->id,
                'address_id' => $addressId,
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('purchase.show', $item_id)->withErrors(['error' => '決済の開始に失敗しました。']);
        }

        return redirect($checkoutSession->url);
    }

    //  決済成功後の処理（DBを更新）
    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        //  セッションIDを取得
        $session_id = $request->query('session_id');
        if (!$session_id) {
            return redirect()->route('items.index')->withErrors(['error' => '決済情報が取得できませんでした。']);
        }

        //  Checkout セッション情報を取得
        try {
            $checkoutSession = \Stripe\Checkout\Session::retrieve($session_id);
        } catch (\Exception $e) {
            Log::error("success(): Checkout Session の取得に失敗: " . $e->getMessage());
            return redirect()->route('items.index')->withErrors(['error' => '決済情報が見つかりませんでした。']);
        }

        //  `metadata` から `item_id`、`address_id`、`user_id` を取得
        $item_id = $checkoutSession->metadata->item_id ?? null;
        $address_id = $checkoutSession->metadata->address_id ?? null;
        $user_id = $checkoutSession->metadata->user_id ?? null;

        Log::info("success() メソッドで取得したデータ", [
            'user_id' => $user_id,
            'item_id' => $item_id,
            'address_id' => $address_id,
            'session_id' => $session_id,
        ]);

        //  `metadata` に必要な情報が含まれていない場合
        if (!$item_id || !$user_id || !$address_id) {
            Log::error("success(): metadata が不足しています");
            return redirect()->route('items.index')->withErrors(['error' => '決済情報に不備があります。']);
        }

        //  Webhook がすでに処理したか確認
        $existingPurchase = Purchase::where('transaction_id', $session_id)->first();
        if (!$existingPurchase) {
            Log::warning("success(): Webhook による購入データが見つかりません。");
            return redirect()->route('items.index')->withErrors(['error' => '決済データが確認できません。数分後に再度ご確認ください。']);
        }

        //  セッションデータをクリア
        session()->forget('payment_method');

        return redirect()->route('profile.index')->with('success', '決済が完了しました。購入ありがとうございます！');
    }

    public function cancel()
    {
        return redirect()->route('profile.index')->withErrors([
            'error' => '決済がキャンセルされました。購入を完了できませんでした。もう一度お試しください。'
        ]);
    }
}
