<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ======= 修正前（個別に `where('name', ...)->first()` で取得）=======
        // $conditionGood = Condition::where('name', '良好')->first();
        // $conditionNoDamage = Condition::where('name', '目立った傷や汚れなし')->first();
        // $conditionSlightDamage = Condition::where('name', 'やや傷や汚れあり')->first();
        // $conditionBad = Condition::where('name', '状態が悪い')->first();

        // ======= 修正後（`pluck('id', 'name')` を使って一括取得）=======
        // 各コンディションの ID を取得
        $conditions = Condition::whereIn('name', [
            '良好',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '状態が悪い'
        ])->pluck('id', 'name');

        // ======= 各カテゴリの ID を一括取得 =======
        $categories = Category::whereIn('name', [
            'ファッション',
            '家電',
            'インテリア',
            'レディース',
            'メンズ',
            'コスメ',
            '本',
            'ゲーム',
            'スポーツ',
            'キッチン',
            'ハンドメイド',
            'アクセサリー',
            'おもちゃ',
            'ベビー・キッズ'
        ])->pluck('id', 'name');


        /* ======= 修正前（商品データを直接挿入していたコード）======= */
        // ダミーデータを一括で追加
        /*    DB::table('items')->insert([
            [
                'user_id' => 1,
                'condition_id' => $conditions['良好'], // ← 修正後
                'name' => '腕時計',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditions['目立った傷や汚れなし'], // ← 修正後
                'name' => 'HDD',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditions['やや傷や汚れあり'], // ← 修正後
                'name' => '玉ねぎ3束',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditions['状態が悪い'], // ← 修正後
                'name' => '革靴',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditions['良好'], // ← 修正後
                'name' => 'ノートPC',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditions['目立った傷や汚れなし'], // ← 修正後
                'name' => 'マイク',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditions['やや傷や汚れあり'], // ← 修正後
                'name' => 'ショルダーバッグ',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditions['状態が悪い'], // ← 修正後
                'name' => 'タンブラー',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditions['良好'], // ← 修正後
                'name' => 'コーヒーミル',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditions['目立った傷や汚れなし'], // ← 修正後
                'name' => 'メイクセット',
                'brand' => 'ノーブランド', // ブランドなし
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],

        ]);
        */

        // 既存のアイテムにカテゴリを追加
        $itemsWithCategories = [
            '腕時計' => ['ファッション', 'メンズ'],
            'HDD' => ['家電'],
            '玉ねぎ3束' => ['キッチン', 'インテリア'],
            '革靴' => ['ファッション', 'メンズ'],
            'ノートPC' => ['家電'],
            'マイク' => ['家電'],
            'ショルダーバッグ' => ['ファッション'],
            'タンブラー' => ['キッチン'],
            'コーヒーミル' => ['キッチン'],
            'メイクセット' => ['コスメ'],
        ];

        foreach ($itemsWithCategories as $itemName => $categoryNames) {
            // 既存のアイテムを取得
            $item = Item::where('name', $itemName)->first();

            if ($item) {
                // カテゴリIDを取得
                $categoryIds = [];
                foreach ($categoryNames as $categoryName) {
                    if (isset($categories[$categoryName])) {
                        $categoryIds[] = $categories[$categoryName];
                    }
                }

                // カテゴリを追加（既存カテゴリは維持）
                $item->categories()->syncWithoutDetaching($categoryIds);
            }
        }
    }
}
