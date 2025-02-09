<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
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
        return view('profile.edit', compact('user'));
    }

    /**
     * プロフィール更新処理
     */
    public function update(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        /** @var User $user */
        $user = Auth::user(); // 現在ログイン中のユーザーを取得

        // プロフィール画像の保存処理
        if ($profileRequest->hasFile('profile_image')) {
            $imagePath = $profileRequest->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $imagePath; // 保存した画像のパスをユーザー情報に保存
        }

        // その他のプロフィール情報を更新
        $user->update([
            'username' => $addressRequest->username,
            'postal_code' => $addressRequest->postal_code,
            'address' => $addressRequest->address,
            'building_name' => $addressRequest->building_name,
        ]);

        // プロフィール画面にリダイレクト
        return redirect()->route('profile.edit')->with('success', 'プロフィールが更新されました');
    }
}
