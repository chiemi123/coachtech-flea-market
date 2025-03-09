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
        // ======= コンディションの ID を一括取得 =======
        $conditions = Condition::pluck('id', 'name');

        // ======= カテゴリの ID を一括取得 =======
        $categories = Category::pluck('id', 'name');

        // ======= アイテムデータを作成（コンディション & カテゴリ付き） =======
        $items = [
            [
                'name' => '腕時計',
                'brand' => 'ノーブランド',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition_name' => '良好',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'HDD',
                'brand' => 'ノーブランド',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition_name' => '目立った傷や汚れなし',
                'categories' => ['家電'],
            ],
            [
                'name' => '玉ねぎ3束',
                'brand' => 'ノーブランド',
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition_name' => 'やや傷や汚れあり',
                'categories' => ['キッチン', 'インテリア'],
            ],
            [
                'name' => '革靴',
                'brand' => 'ノーブランド',
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition_name' => '状態が悪い',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'ノートPC',
                'brand' => 'ノーブランド',
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition_name' => '良好',
                'categories' => ['家電'],
            ],
            [
                'name' => 'マイク',
                'brand' => 'ノーブランド',
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition_name' => '目立った傷や汚れなし',
                'categories' => ['家電'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'brand' => 'ノーブランド',
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition_name' => 'やや傷や汚れあり',
                'categories' => ['ファッション'],
            ],
            [
                'name' => 'タンブラー',
                'brand' => 'ノーブランド',
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition_name' => '状態が悪い',
                'categories' => ['キッチン'],
            ],
            [
                'name' => 'コーヒーミル',
                'brand' => 'ノーブランド',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition_name' => '良好',
                'categories' => ['キッチン'],
            ],
            [
                'name' => 'メイクセット',
                'brand' => 'ノーブランド',
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition_name' => '目立った傷や汚れなし',
                'categories' => ['コスメ'],
            ],
        ];

        foreach ($items as $itemData) {
            // アイテムを作成
            $item = Item::create([
                'user_id' => 1,
                'condition_id' => $conditions[$itemData['condition_name']],
                'name' => $itemData['name'],
                'brand' => $itemData['brand'],
                'description' => $itemData['description'],
                'price' => $itemData['price'],
                'item_image' => $itemData['item_image'],
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ]);

            // カテゴリを紐付け
            $categoryIds = [];
            foreach ($itemData['categories'] as $categoryName) {
                if (isset($categories[$categoryName])) {
                    $categoryIds[] = $categories[$categoryName];
                }
            }
            $item->categories()->attach($categoryIds);
        }
    }
}