<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\Item;


class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $event = $request->all();
        Log::info("Webhook received:", ['event_type' => $event['type']]);

        // ✅ コンビニ支払いの受付完了（この時点ではまだ支払い完了ではない）
        if ($event['type'] === 'checkout.session.completed') {
            $session = $event['data']['object'];
            $item_id = $session['metadata']['item_id'] ?? null;
            $address_id = $session['metadata']['address_id'] ?? null;
            $user_id = $session['metadata']['user_id'] ?? null;
            $session_id = $session['id'];
            // ✅ 支払い方法を取得
            $payment_method = $session['payment_method_types'][0] ?? 'unknown'; // 'card' or 'konbini'

            Log::info("Webhook metadata:", [
                'user_id' => $user_id,
                'item_id' => $item_id,
                'address_id' => $address_id,
                'session_id' => $session_id,
                'payment_method' => $payment_method
            ]);

            // ✅ メタデータが不足している場合は処理を中止
            if (!$item_id || !$user_id || !$address_id) {
                Log::error("Webhook: metadata が null のため処理を中断しました");
                return response()->json(['status' => 'error', 'message' => 'Metadata missing'], 400);
            }

            // 🚀 **すでに購入データがある場合はスキップ**
            if (Purchase::where('transaction_id', $session_id)->exists()) {
                Log::info("Webhook: すでに購入データが存在するため処理をスキップ");
                return response()->json(['status' => 'success']);
            }

            // ✅ クレジットカード決済の場合は即 `completed` にする
            $status = ($payment_method === 'card') ? 'completed' : 'pending';


            // ✅ 購入データを `pending` 状態で保存
            DB::beginTransaction();
            try {
                // 🚀 **ロックをかけて重複登録を防ぐ**
                $existingPurchase = Purchase::where('transaction_id', $session_id)->lockForUpdate()->first();
                if ($existingPurchase) {
                    Log::info("Webhook: 購入データが既にあるためスキップ");
                    DB::rollBack();
                    return response()->json(['status' => 'success']);
                }


                Purchase::create([
                    'user_id' => $user_id,
                    'item_id' => $item_id,
                    'address_id' => $address_id,
                    'payment_method' => ($payment_method === 'card') ? 'クレジットカード' : 'コンビニ払い',
                    'status' => $status,
                    'transaction_id' => $session_id,
                ]);

                // ✅ クレジットカード決済の場合は `sold_out` を更新
                if ($payment_method === 'card') {
                    Item::where('id', $item_id)->update(['sold_out' => 1]);
                    Log::info("Webhook: クレジットカード決済のため、商品を `sold_out` に更新しました item_id: $item_id");
                }

                DB::commit();
                Log::info("Webhook: 購入データが保存されました（pending）");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Webhook: 購入処理エラー: " . $e->getMessage());
            }
        }

        // ✅ コンビニ支払いの完了を検知 → `status` を `completed` に更新
        if ($event['type'] === 'checkout.session.async_payment_succeeded') {
            $session = $event['data']['object'];
            $session_id = $session['id'];

            Log::info("Webhook: async_payment_succeeded を受信", ['session_id' => $session_id]);

            // 🚀 `transaction_id` に対応する購入データを探す
            $purchase = Purchase::where('transaction_id', $session_id)->first();
            if (!$purchase) {
                Log::error("Webhook: 購入データが見つかりませんでした session_id: $session_id ");
                return response()->json(['status' => 'error', 'message' => 'Purchase not found'], 404);
            }

            // 🚀 既に `completed` ならスキップ
            if ($purchase->status === 'completed') {
                Log::info("Webhook: すでに `completed` のため処理をスキップ");
                return response()->json(['status' => 'success']);
            }

            // 🚀 `status` を `completed` に更新し、商品を `sold_out` にする
            DB::beginTransaction();
            try {
                $purchase->update(['status' => 'completed']);

                // ✅ 商品の `sold_out` フラグを更新
                Item::where('id', $purchase->item_id)->update(['sold_out' => 1]);

                DB::commit();
                Log::info("Webhook: 購入データが `completed` に更新されました session_id: $session_id ");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Webhook: 購入完了処理エラー: " . $e->getMessage());
            }
        }

        return response()->json(['status' => 'success']);
    }
}
