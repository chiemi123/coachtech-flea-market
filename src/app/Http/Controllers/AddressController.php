<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    // 送付先住所変更画面
    public function edit($item_id)
    {
        // 仮の住所データ（本来はデータベースから取得）
        $address = [
            'postal_code' => '123-4567',
            'address' => '東京都新宿区西新宿1-1-1',
            'phone' => '090-1234-5678',
        ];

        return view('address.edit', compact('item_id', 'address'));
    }

    // 送付先住所の更新処理
    public function update(Request $request, $item_id)
    {
        // バリデーション
        $validated = $request->validate([
            'postal_code' => 'required|max:8',
            'address' => 'required|max:255',
            'phone' => 'required|max:15',
        ]);

        // データの更新処理（ここでは仮の処理）
        // 本来はデータベースを更新する処理を記述
        // DB::table('addresses')->where('item_id', $item_id)->update($validated);

        return redirect()->route('purchase.show', ['item_id' => $item_id])
            ->with('success', '送付先住所が更新されました。');
    }
}
