<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\MessageRead;

class ConversationDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // === 0) ユーザー取得（A=出品者, B=出品者, C=購入者） ===
            $buyer   = User::where('email', 'demo_c@example.com')->firstOrFail();
            $sellerA = User::where('email', 'demo_a@example.com')->firstOrFail();
            $sellerB = User::where('email', 'demo_b@example.com')->firstOrFail();

            // 配送先（latestAddress が無ければ作る）
            $address = $buyer->latestAddress()->first();
            if (!$address) {
                $address = $buyer->addresses()->create([
                    'postal_code'   => $buyer->postal_code ?: '100-0003',
                    'address'       => $buyer->address ?: '東京都千代田区大手町1-1',
                    'building_name' => $buyer->building_name ?: '（未設定）',
                ]);
            }

            // ヘルパ：会話を投入（最後の投稿は相手側を未読に）
            $seedThread = function (Purchase $purchase, User $firstSender, User $secondSender, string $body1, string $body2, string $body3) {
                if ($purchase->messages()->exists()) {
                    // 既に会話があるなら流さない（冪等）
                    return;
                }

                $t1 = Carbon::now()->subMinutes(9);
                $t2 = Carbon::now()->subMinutes(6);
                $t3 = Carbon::now()->subMinutes(3);

                // 1通目（firstSender）
                $m1 = $purchase->messages()->create([
                    'user_id'    => $firstSender->id,
                    'body'       => $body1,
                    'created_at' => $t1,
                    'updated_at' => $t1,
                ]);
                MessageRead::firstOrCreate(
                    ['message_id' => $m1->id, 'user_id' => $firstSender->id],
                    ['read_at' => $t1]
                );

                // 2通目（secondSender）
                $m2 = $purchase->messages()->create([
                    'user_id'    => $secondSender->id,
                    'body'       => $body2,
                    'created_at' => $t2,
                    'updated_at' => $t2,
                ]);
                MessageRead::firstOrCreate(
                    ['message_id' => $m2->id, 'user_id' => $secondSender->id],
                    ['read_at' => $t2]
                );

                // 3通目（firstSender）→ 相手（secondSender）を未読にしておく
                $m3 = $purchase->messages()->create([
                    'user_id'    => $firstSender->id,
                    'body'       => $body3,
                    'created_at' => $t3,
                    'updated_at' => $t3,
                ]);
                MessageRead::firstOrCreate(
                    ['message_id' => $m3->id, 'user_id' => $firstSender->id],
                    ['read_at' => $t3]
                );

                // 並び替え/未読数用に last_message_at を更新
                $purchase->update(['last_message_at' => $t3]);
            };

            // === 1) Aのアイテム（CO001）をCが購入：最後は buyer の発言 → sellerA 未読 ===
            $itemA = Item::where('code', 'CO001')->firstOrFail();
            $purchaseA = Purchase::firstOrCreate(
                ['user_id' => $buyer->id, 'item_id' => $itemA->id],
                [
                    'address_id'      => $address->id,
                    'payment_method'  => 'card',
                    'status'          => 'paid',
                    'last_message_at' => now()->subMinutes(10),
                ]
            );

            $itemA->update(['sold_out' => 1]);

            $seedThread(
                $purchaseA,
                $buyer,    // firstSender
                $sellerA,  // secondSender
                'はじめまして、購入希望です。',
                'ありがとうございます！本日中に発送できます。',
                '助かります、よろしくお願いします。' // ← sellerA 側が未読になる
            );

            // === 2) Bのアイテム（CO006）をCが購入：最後は sellerB の発言 → buyer 未読 ===
            $itemB = Item::where('code', 'CO006')->firstOrFail();
            $purchaseB = Purchase::firstOrCreate(
                ['user_id' => $buyer->id, 'item_id' => $itemB->id],
                [
                    'address_id'      => $address->id,
                    'payment_method'  => 'card',
                    'status'          => 'paid',
                    'last_message_at' => now()->subMinutes(4),
                ]
            );

            $itemB->update(['sold_out' => 1]);

            // ここは順番を反転（最後が sellerB になる）
            if (!$purchaseB->messages()->exists()) {
                $t1 = Carbon::now()->subMinutes(3);
                $t2 = Carbon::now()->subMinutes(2);
                $t3 = Carbon::now()->subMinute();

                $n1 = $purchaseB->messages()->create([
                    'user_id'    => $sellerB->id,
                    'body'       => 'ご購入ありがとうございます。明日発送予定です。',
                    'created_at' => $t1,
                    'updated_at' => $t1,
                ]);
                MessageRead::firstOrCreate(
                    ['message_id' => $n1->id, 'user_id' => $sellerB->id],
                    ['read_at' => $t1]
                );

                $n2 = $purchaseB->messages()->create([
                    'user_id'    => $buyer->id,
                    'body'       => '承知しました、よろしくお願いします！',
                    'created_at' => $t2,
                    'updated_at' => $t2,
                ]);
                MessageRead::firstOrCreate(
                    ['message_id' => $n2->id, 'user_id' => $buyer->id],
                    ['read_at' => $t2]
                );

                $n3 = $purchaseB->messages()->create([
                    'user_id'    => $sellerB->id,
                    'body'       => '伝票番号は準備でき次第お送りします。',
                    'created_at' => $t3,
                    'updated_at' => $t3,
                ]);
                MessageRead::firstOrCreate(
                    ['message_id' => $n3->id, 'user_id' => $sellerB->id],
                    ['read_at' => $t3]
                );
                // buyer 側は n3 未読のまま

                $purchaseB->update(['last_message_at' => $t3]);
            }

            if (method_exists($this->command, 'info')) {
                $this->command->info('ConversationDemoSeeder: CO001/CO006 に購入＋会話を投入。未読は sellerA 側・buyer 側でそれぞれ発生。');
            }
        });
    }
}
