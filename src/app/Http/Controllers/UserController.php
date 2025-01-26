<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * プロフィール画面
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
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
    public function update(AddressRequest $request)
    {
        /** @var User $user */
        $user = Auth::user(); // 現在ログイン中のユーザーを取得

        // プロフィール情報を更新
        $user->update([
            'profile_image' => $request->file('profile_image')
                ? $request->file('profile_image')->store('profiles', 'public')
                : $user->profile_image,
            'username' => $request->username,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building_name' => $request->building_name,
            'profile_completed' => true, // プロフィールを完了済みにする

        ]);

        return redirect('/'); // 商品一覧画面にリダイレクト
    }
}
