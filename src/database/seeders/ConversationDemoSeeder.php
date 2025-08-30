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
            $buyerC = User::where('email', 'demo_c@example.com')->firstOrFail();
            $buyerD = User::where('email', 'demo_d@example.com')->firstOrFail();

            $getAddress = function (User $buyer) {
                return $buyer->latestAddress()->first()
                    ?? $buyer->addresses()->create([
                        'postal_code'   => $buyer->postal_code ?? '000-0000',
                        'address'       => $buyer->address ?? '東京都',
                        'building_name' => $buyer->building_name ?? '（未設定）',
                    ]);
            };

            $seedThread = function (
                Purchase $purchase,
                User $firstSender,
                User $secondSender,
                string $body1,
                string $body2,
                string $body3,
                bool $isFirstSenderLast = true
            ) {
                if ($purchase->messages()->exists()) return;

                $t1 = Carbon::now()->subMinutes(9);
                $t2 = Carbon::now()->subMinutes(6);
                $t3 = Carbon::now()->subMinutes(3);

                // 1通目
                $m1 = $purchase->messages()->create([
                    'user_id' => $firstSender->id,
                    'body' => $body1,
                    'created_at' => $t1,
                    'updated_at' => $t1,
                ]);
                MessageRead::firstOrCreate(['message_id' => $m1->id, 'user_id' => $firstSender->id], ['read_at' => $t1]);
                MessageRead::firstOrCreate(['message_id' => $m1->id, 'user_id' => $secondSender->id], ['read_at' => $t1]);

                // 2通目
                $m2 = $purchase->messages()->create([
                    'user_id' => $secondSender->id,
                    'body' => $body2,
                    'created_at' => $t2,
                    'updated_at' => $t2,
                ]);
                MessageRead::firstOrCreate(['message_id' => $m2->id, 'user_id' => $secondSender->id], ['read_at' => $t2]);
                MessageRead::firstOrCreate(['message_id' => $m2->id, 'user_id' => $firstSender->id], ['read_at' => $t2]);

                // 3通目（未読にしたい側を lastReceiver として外す）
                $lastSender = $isFirstSenderLast ? $firstSender : $secondSender;
                $lastReceiver = $isFirstSenderLast ? $secondSender : $firstSender;

                $m3 = $purchase->messages()->create([
                    'user_id' => $lastSender->id,
                    'body' => $body3,
                    'created_at' => $t3,
                    'updated_at' => $t3,
                ]);
                MessageRead::firstOrCreate(['message_id' => $m3->id, 'user_id' => $lastSender->id], ['read_at' => $t3]);
                // lastReceiver は未読のまま

                $purchase->update(['last_message_at' => $t3]);
            };

            foreach (range(1, 5) as $i) {
                $code = sprintf("CO%03d", $i);
                $item = Item::where('code', $code)->firstOrFail();
                $purchase = Purchase::firstOrCreate(
                    ['user_id' => $buyerC->id, 'item_id' => $item->id],
                    [
                        'address_id' => $getAddress($buyerC)->id,
                        'payment_method' => 'card',
                        'status' => 'paid',
                        'last_message_at' => now()
                    ]
                );
                $item->update(['sold_out' => 1]);

                if (in_array($code, ['CO001', 'CO002'])) {
                    $isFirstSenderLast = $code === 'CO002'; // CO001→出品者未読, CO002→購入者未読
                    $seedThread(
                        $purchase,
                        $buyerC,
                        $item->user,
                        '購入しました！',
                        'ありがとうございます！',
                        'よろしくお願いします！',
                        $isFirstSenderLast
                    );
                }
            }

            foreach (range(6, 10) as $i) {
                $code = sprintf("CO%03d", $i);
                $item = Item::where('code', $code)->firstOrFail();
                $purchase = Purchase::firstOrCreate(
                    ['user_id' => $buyerD->id, 'item_id' => $item->id],
                    [
                        'address_id' => $getAddress($buyerD)->id,
                        'payment_method' => 'card',
                        'status' => 'paid',
                        'last_message_at' => now()
                    ]
                );
                $item->update(['sold_out' => 1]);

                if (in_array($code, ['CO006', 'CO007'])) {
                    $isFirstSenderLast = $code === 'CO007'; // CO006→出品者未読, CO007→購入者未読
                    $seedThread(
                        $purchase,
                        $buyerD,
                        $item->user,
                        '購入させていただきました。',
                        '発送準備いたします！',
                        'よろしくお願いします！',
                        $isFirstSenderLast
                    );
                }
            }

            if (method_exists($this->command, 'info')) {
                $this->command->info('ConversationDemoSeeder: A/B/C/D に各2件の未読がある状態を作成しました。');
            }
        });
    }
}
