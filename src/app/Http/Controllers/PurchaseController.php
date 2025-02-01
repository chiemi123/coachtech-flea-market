<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    // 商品購入画面
    public function show($item_id)
    {
        // 仮のデータ（本来はデータベースから取得）
        $item = [
            'id' => $item_id,
            'name' => '商品名',
            'price' => 47000,
            'image' => '/images/item1.png',
        ];

        // 購入者情報（仮のデータ）
        $userAddress = [
            'postal_code' => 'XXX-YYYY',
            'address' => 'ここには住所情報が入ります',
        ];

        return view('purchase.show', compact('item', 'userAddress'));
    }
}
