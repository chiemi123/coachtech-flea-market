<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        // 各コンディションの ID を取得
        $conditionGood = Condition::where('name', '良好')->first();
        $conditionNoDamage = Condition::where('name', '目立った傷や汚れなし')->first();
        $conditionSlightDamage = Condition::where('name', 'やや傷や汚れあり')->first();
        $conditionBad = Condition::where('name', '状態が悪い')->first();

        // ダミーデータを一括で追加
        DB::table('items')->insert([
            [
                'user_id' => 1,
                'condition_id' => $conditionGood->id,
                'name' => '腕時計',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditionNoDamage->id,
                'name' => 'HDD',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditionSlightDamage->id,
                'name' => '玉ねぎ3束',
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditionBad->id,
                'name' => '革靴',
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditionGood->id,
                'name' => 'ノートPC',
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditionNoDamage->id,
                'name' => 'マイク',
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditionSlightDamage->id,
                'name' => 'ショルダーバッグ',
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditionBad->id,
                'name' => 'タンブラー',
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditionGood->id,
                'name' => 'コーヒーミル',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],
            [
                'user_id' => 1,
                'condition_id' => $conditionNoDamage->id, // 修正！
                'name' => 'メイクセット',
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'sold_out' => false,
                'likes_count' => 0,
                'comments_count' => 0,
            ],

        ]);
    }
}
