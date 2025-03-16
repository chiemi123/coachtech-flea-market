<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id' => 1, // IDを固定
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'), // 適宜変更
            'profile_image' => 'https://example.com/default-profile.png', // デフォルト画像
            'username' => 'test_user', // ユーザー名を指定
            'postal_code' => '123-4567', // 仮の郵便番号
            'address' => '東京都渋谷区', // 仮の住所
            'building_name' => 'サンプルビル 101', // 仮の建物名
        ]);
    }
}
