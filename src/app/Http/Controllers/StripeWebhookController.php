<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase;
use Stripe\Webhook;
use Stripe\Stripe;
use Stripe\Event;


class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook error'], 400);
        }

        // Stripeの支払い完了イベント
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $transaction_id = $session->id; // StripeのCheckout Session ID
            $customer_email = $session->customer_details->email ?? null;

            Log::info("決済完了: transaction_id={$transaction_id}");

            // DBに該当する購入データがあるか確認（session_idを元に検索）
            $purchase = Purchase::where('transaction_id', $transaction_id)->first();

            if ($purchase) {
                // 購入ステータスを「completed」に更新
                $purchase->update([
                    'status' => 'completed',
                ]);
            } else {
                // もし購入データが見つからない場合、ログに記録
                Log::warning("購入データが見つかりません: transaction_id={$transaction_id}");
            }
        }

        return response()->json(['status' => 'success']);
    }
}
