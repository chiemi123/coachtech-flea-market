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

        // 変更履歴があれば最新の住所を取得、なければ `users` テーブルの住所を使う
        $address = Address::where('user_id', $user->id)->latest()->first() ?? $user;

        // セッションに保存された支払い方法を取得（デフォルトを null にする）
        $payment_method = session()->has('payment_method') ? session('payment_method') : null;

        return view('purchase.show', compact('item', 'address', 'payment_method'));
    }

    public function confirm(PurchaseRequest $request, $item_id)
    {

        $user = auth()->user();

        // `address_id` がリクエストに無い場合は `users` テーブルの住所を使う
        $addressId = $request->input('address_id', session('address_id'));

        // ✅ ここで `address_id` の値を確認！
        //dd($addressId);

        if (!$addressId) {
            $defaultAddress = $user->address ?? null;

            if ($defaultAddress) {
                $addressId = $defaultAddress->id;
                session(['address_id' => $addressId]); // セッションに保存
            } else {
                return redirect()->route('purchase.show', ['item_id' => $item_id])
                    ->withErrors(['address_id' => '配送先を選択してください。']);
            }
        }


        // セッションに支払い方法を保存
        session()->put('payment_method', $request->payment_method);
        session()->save();

        // ✅ `session('address_id')` に保存
        session(['address_id' => $addressId]);
        session()->save();  // ← 明示的にセッションを保存

        // ✅ セッションの `address_id` をデバッグ
        //dd(session('address_id')); // 🎯 セッションに正しく保存されたか確認！

        // 商品詳細ページにリダイレクト（選択内容を反映）
        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    public function checkout(Request $request, $item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        // 住所情報を取得
        $addressId = session('address_id') ?? $request->input('address_id');

        if (!$addressId) {
            return redirect()->route('purchase.show', ['item_id' => $item_id])
                ->withErrors(['address_id' => '配送先を選択してください。']);
        }

        // 支払い方法を取得
        $paymentMethod = session('payment_method');

        if (!$paymentMethod) {
            return redirect()->route('purchase.show', ['item_id' => $item_id])
                ->withErrors(['payment_method' => '支払い方法を選択してください。']);
        }

        // Stripe APIキー設定
        Stripe::setApiKey(config('services.stripe.secret'));

        // 支払い方法の設定
        $payment_methods = ($paymentMethod === 'クレジットカード') ? ['card'] : ['konbini'];

        // Stripe Checkout セッション作成
        $checkoutSession = StripeSession::create([
            'payment_method_types' => $payment_methods,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [ // ✅ ここに `item_id` を追加！
                'item_id' => $item->id,
                'address_id' => session('address_id'),
            ],
            'success_url' => url('/purchase/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.cancel'),
        ]);

        // ✅ ここで Checkout の URL を確認
        //dd($checkoutSession->url);

        // StripeのCheckoutページにリダイレクト
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
        //dd($item_id, $address_id); // 🎯 ここで取得した値を確認！

        // ✅ `item_id` が取得できない場合はエラー
        if (!$item_id || !$address_id) {
            return redirect()->route('items.index')->withErrors(['error' => '商品情報が見つかりませんでした。']);
        }

        $payment_intent_id = $checkoutSession->payment_intent;
        $payment_status = $checkoutSession->payment_status;

        if ($payment_intent_id) {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
            $payment_status = $paymentIntent->status;
        }

        if ($payment_status === 'succeeded') {
            // 購入データをDBに保存
            Purchase::create([
                'user_id' => auth()->id(),
                'item_id' => $checkoutSession->metadata->item_id,
                'address_id' => session('address_id'),
                'payment_method' => session('payment_method'),
                'status' => 'completed', // 決済成功！
                'transaction_id' => $session_id,
            ]);

            return redirect()->route('profile.index')->with('success', '購入が完了しました！');
        } else {
            return redirect()->route('items.index')->withErrors(['error' => '決済が完了していません。']);
        }
    }

    public function cancel()
    {
        return view('purchase.cancel');
    }

    // 商品購入処理
    public function complete(Request $request, $item_id) {}
}
