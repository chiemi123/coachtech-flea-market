<?php

namespace App\Http\Controllers;
use App\Models\Item;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    // 商品購入画面
    public function show($item_id)
    {
        // 購入する商品情報を取得
        $item = Item::findOrFail($item_id);

        return view('purchase.show', compact('item'));
    }
}
