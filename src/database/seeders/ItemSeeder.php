<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ======= コンディションの ID を一括取得 =======
        $conditions = Condition::pluck('id', 'name');

        // ======= カテゴリの ID を一括取得 =======
        $categories = Category::pluck('id', 'name');

        // 出品者
        $userA = User::where('email', 'demo_a@example.com')->first();
        $userB = User::where('email', 'demo_b@example.com')->first();

        // CO001〜CO010 をA/Bに割当（brandは文字列、descriptionは255以内）
        $map = [
            'CO001' => ['name' => '腕時計', 'brand' => 'ノーブランド', 'price' => 15000, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg', 'cond' => '良好', 'cats' => ['ファッション', 'メンズ'], 'owner' => 'A'],
            'CO002' => ['name' => 'HDD', 'brand' => 'ノーブランド', 'price' => 5000, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg', 'cond' => '目立った傷や汚れなし', 'cats' => ['家電'], 'owner' => 'A'],
            'CO003' => ['name' => '玉ねぎ3束', 'brand' => 'ノーブランド', 'price' => 300, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg', 'cond' => 'やや傷や汚れあり', 'cats' => ['キッチン', 'インテリア'], 'owner' => 'A'],
            'CO004' => ['name' => '革靴', 'brand' => 'ノーブランド', 'price' => 4000, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg', 'cond' => '状態が悪い', 'cats' => ['ファッション', 'メンズ'], 'owner' => 'A'],
            'CO005' => ['name' => 'ノートPC', 'brand' => 'ノーブランド', 'price' => 45000, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg', 'cond' => '良好', 'cats' => ['家電'], 'owner' => 'A'],
            'CO006' => ['name' => 'マイク', 'brand' => 'ノーブランド', 'price' => 8000, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg', 'cond' => '目立った傷や汚れなし', 'cats' => ['家電'], 'owner' => 'B'],
            'CO007' => ['name' => 'ショルダーバッグ', 'brand' => 'ノーブランド', 'price' => 3500, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg', 'cond' => 'やや傷や汚れあり', 'cats' => ['ファッション'], 'owner' => 'B'],
            'CO008' => ['name' => 'タンブラー', 'brand' => 'ノーブランド', 'price' => 500, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg', 'cond' => '状態が悪い', 'cats' => ['キッチン'], 'owner' => 'B'],
            'CO009' => ['name' => 'コーヒーミル', 'brand' => 'ノーブランド', 'price' => 4000, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg', 'cond' => '良好', 'cats' => ['キッチン'], 'owner' => 'B'],
            'CO010' => ['name' => 'メイクセット', 'brand' => 'ノーブランド', 'price' => 2500, 'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg', 'cond' => '目立った傷や汚れなし', 'cats' => ['コスメ'], 'owner' => 'B'],
        ];

        foreach ($map as $code => $d) {
            $ownerId     = $d['owner'] === 'A' ? $userA->id : $userB->id;
            $conditionId = $conditions[$d['cond']] ?? Condition::query()->value('id'); // フォールバック

            // 冪等：code 一意で作成/取得
            $item = Item::firstOrCreate(
                ['code' => $code],
                [
                    'user_id'        => $ownerId,
                    'condition_id'   => $conditionId,
                    'name'           => $d['name'],
                    'brand'          => $d['brand'] ?? 'ノーブランド',
                    'description'    => "デモ: {$d['name']}", // 255以内
                    'price'          => $d['price'],
                    'item_image'     => $d['img'],
                    'sold_out'       => false,
                    'likes_count'    => 0,
                    'comments_count' => 0,
                ]
            );

            // カテゴリ名→ID（存在しない名前はスキップ）
            $catIds = collect($d['cats'])
                ->map(fn($n) => $categories[$n] ?? null)
                ->filter()
                ->values()
                ->all();

            if ($catIds) {
                // 再実行でも重複しない
                $item->categories()->syncWithoutDetaching($catIds);
            }
        }

        if (method_exists($this->command, 'info')) {
            $this->command->info('ItemSeeder: CO001〜CO010 をユーザーA/Bに割り当てて投入しました。');
        }
    }
}
