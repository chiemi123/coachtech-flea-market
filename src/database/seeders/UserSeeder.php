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
        // 既存のテストユーザー（id=1 固定で問題なければこのまま）
        // 再実行時の安全性を高めるため firstOrCreate を推奨
        User::firstOrCreate(
            ['id' => 1],
            [
                'name'          => 'テストユーザー',
                'email'         => 'test@example.com',
                'password'      => Hash::make('password123'),
                'profile_image' => 'https://example.com/default-profile.png',
                'username'      => 'test_user',
                'postal_code'   => '123-4567',
                'address'       => '東京都渋谷区',
                'building_name' => 'サンプルビル 101',
            ]
        );

        // 出品者：ユーザーA
        User::firstOrCreate(
            ['email' => 'demo_a@example.com'],
            [
                'name'          => 'ユーザーA',
                'password'      => Hash::make('password123'),
                'profile_image' => 'https://example.com/default-profile.png',
                'username'      => 'user_a',
                'postal_code'   => '100-0001',
                'address'       => '東京都千代田区千代田1-1',
                'building_name' => 'デモビルA 101',
            ]
        );

        // 出品者：ユーザーB
        User::firstOrCreate(
            ['email' => 'demo_b@example.com'],
            [
                'name'          => 'ユーザーB',
                'password'      => Hash::make('password123'),
                'profile_image' => 'https://example.com/default-profile.png',
                'username'      => 'user_b',
                'postal_code'   => '100-0002',
                'address'       => '東京都千代田区丸の内1-1',
                'building_name' => 'デモビルB 201',
            ]
        );

        // 購入者：ユーザーC（出品はしない想定）
        User::firstOrCreate(
            ['email' => 'demo_c@example.com'],
            [
                'name'          => 'ユーザーC（購入者）',
                'password'      => Hash::make('password123'),
                'profile_image' => 'https://example.com/default-profile.png',
                'username'      => 'user_c',
                'postal_code'   => '100-0003',
                'address'       => '東京都千代田区大手町1-1',
                'building_name' => 'デモビルC 301',
            ]
        );

        // 開発時のログ（任意）
        if (method_exists($this->command, 'info')) {
            $this->command->info('UserSeeder: test(1), demo_a, demo_b, demo_c を用意しました。');
        }
    }
}
