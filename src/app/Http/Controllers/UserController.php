<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * プロフィール画面
     */
    public function index()
    {
        // 現在ログイン中のユーザーを取得
        $user = Auth::user();

        // 出品した商品を取得
        $listedItems = Item::where('user_id', $user->id)->get();

        // 購入した商品を取得
        $purchasedItems = $user->purchasedItems; // ユーザーが購入した商品（リレーション経由で取得）



        return view('profile.index', compact('user', 'listedItems', 'purchasedItems'));
    }

    /**
     * プロフィール設定画面
     */
    public function edit()
    {
        $user = Auth::user();

        // メール認証が完了していない場合は /email/verify にリダイレクト
        if ($user instanceof \App\Models\User && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return view('profile.edit', compact('user'));
    }

    /**
     * プロフィール更新処理
     */
    public function update(AddressRequest $addressRequest, ProfileRequest $request)
    {
        /** @var User $user */
        $user = Auth::user(); // 現在ログイン中のユーザーを取得

        if ($request->hasFile('profile_image')) {
            // 古い画像を削除（ファイルが存在するかチェック）
            if ($user->profile_image && Storage::exists('public/' . $user->profile_image)) {
                Storage::delete('public/' . $user->profile_image);
            }

            // 新しい画像を `storage/app/public/profile_images/` に保存
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // その他のプロフィール情報を更新
        $user->update([
            'username' => $addressRequest->username,
            'postal_code' => $addressRequest->postal_code,
            'address' => $addressRequest->address,
            'building_name' => $addressRequest->building_name,
            'profile_completed' => 1,
        ]);

        // プロフィール画面にリダイレクト
        return redirect()->route('profile.edit')->with('success', 'プロフィールが更新されました');
    }
}
