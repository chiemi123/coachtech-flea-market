<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    /**
     * public/images（リポジトリ同梱）→ storage/app/public にコピーして相対パスを返す
     */
    private function copyPublicToStorage(string $publicRelPath, string $destPath): string
    {
        $src = public_path($publicRelPath); // 例: public/images/seed/avatars/default-profile.png
        if (!file_exists($src)) {
            throw new \RuntimeException("Seed image not found: {$src}");
        }

        if (!Storage::disk('public')->exists($destPath)) {
            Storage::disk('public')->put($destPath, file_get_contents($src));
        }

        return $destPath; // ← DBに保存する 'avatars/xxxx.png'
    }

    public function run(): void
    {
        // 1) リポジトリに同梱した画像（例）
        //    public/images/seed/avatars/default-profile.png を用意しておく
        $defaultSrc = 'images/seed/avatars/default-profile.png';

        // 2) 各ユーザーの保存先ファイル名（storage側）
        $testAvatar = $this->copyPublicToStorage($defaultSrc, 'avatars/test_user.png');
        $aAvatar    = $this->copyPublicToStorage($defaultSrc, 'avatars/demo_a.png');
        $bAvatar    = $this->copyPublicToStorage($defaultSrc, 'avatars/demo_b.png');
        $cAvatar    = $this->copyPublicToStorage($defaultSrc, 'avatars/demo_c.png');

        // 3) 作成（email をキーに updateOrCreate 推奨）
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'          => 'テストユーザー',
                'password'      => Hash::make('password123'),
                'profile_image' => $testAvatar,      // ← 'avatars/test_user.png'
                'username'      => 'test_user',
                'postal_code'   => '123-4567',
                'address'       => '東京都渋谷区',
                'building_name' => 'サンプルビル 101',
            ]
        );

        User::updateOrCreate(
            ['email' => 'demo_a@example.com'],
            [
                'name'          => 'ユーザーA',
                'password'      => Hash::make('password123'),
                'profile_image' => $aAvatar,         // ← 'avatars/demo_a.png'
                'username'      => 'user_a',
                'postal_code'   => '100-0001',
                'address'       => '東京都千代田区千代田1-1',
                'building_name' => 'デモビルA 101',
            ]
        );

        User::updateOrCreate(
            ['email' => 'demo_b@example.com'],
            [
                'name'          => 'ユーザーB',
                'password'      => Hash::make('password123'),
                'profile_image' => $bAvatar,
                'username'      => 'user_b',
                'postal_code'   => '100-0002',
                'address'       => '東京都千代田区丸の内1-1',
                'building_name' => 'デモビルB 201',
            ]
        );

        User::updateOrCreate(
            ['email' => 'demo_c@example.com'],
            [
                'name'          => 'ユーザーC（購入者）',
                'password'      => Hash::make('password123'),
                'profile_image' => $cAvatar,
                'username'      => 'user_c',
                'postal_code'   => '100-0003',
                'address'       => '東京都千代田区大手町1-1',
                'building_name' => 'デモビルC 301',
            ]
        );

        if (method_exists($this->command, 'info')) {
            $this->command->info('UserSeeder: test, demo_a, demo_b, demo_c を用意しました。');
        }
    }
}
