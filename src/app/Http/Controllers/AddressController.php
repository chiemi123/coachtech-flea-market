<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    // 送付先住所変更画面
    public function edit($item_id)
    {
        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->latest()->first() ?? $user;


        return view('address.edit', compact('item_id', 'address'));
    }

    // 送付先住所の更新処理
    public function update(AddressRequest $request, $item_id)
    {

        $user = Auth::user();

        // フォームリクエストでバリデーション済みのデータを取得
        $validated = $request->validated();

        // 新しい住所を `addresses` テーブルに保存
        Address::create([
            'user_id' => $user->id,
            'postal_code' => $validated['postal_code'],
            'address' => $validated['address'],
            'building_name' => $validated['building_name'] ?? null,
        ]);



        return redirect()->route('purchase.show', ['item_id' => $item_id])
            ->with('success', '配送先を更新しました');
    }
}
