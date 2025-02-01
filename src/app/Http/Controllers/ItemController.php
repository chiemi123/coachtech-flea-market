<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{

    // 商品一覧画面
    public function index()
    {
        // 仮のデータ
        $items = [
            ['id' => 1, 'name' => '商品1', 'price' => 47000, 'image' => '/images/item1.png'],
            ['id' => 2, 'name' => '商品2', 'price' => 32000, 'image' => '/images/item2.png']
        ];

        return view('items.index', compact('items'));
    }

    // 商品詳細画面
    public function show($item_id)
    {
        // 仮のデータ
        $item = [
            'id' => $item_id,
            'name' => '商品名がここに入る',
            'brand' => 'ブランド名',
            'price' => 47000,
            'description' => '商品の状態は良好です。傷ありません。',
            'condition' => '良好',
            'categories' => ['洋服', 'メンズ'],
            'image' => '/images/item1.png',
            'comments' => [
                ['user' => 'admin', 'comment' => 'こちらにコメントが入ります。']
            ]
        ];

        return view('items.show', compact('item'));
    }

    // 商品出品画面の表示
    public function create()
    {
        // カテゴリー一覧の仮データ（本来はデータベースから取得）
        $categories = ['ファッション', '家電', 'キッチン用品', 'レディース', 'メンズ', 'スポーツ', 'コスメ', 'ゲーム', 'アウトドア', 'アクセサリー', 'その他'];

        return view('sell.create', compact('categories'));
    }

    // 商品出品処理
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category' => 'required|string',
            'condition' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:1',
        ]);

        // 画像を保存する処理
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            $validated['image'] = str_replace('public/', 'storage/', $imagePath);
        }

        // 商品情報を保存する処理（仮）
        // 本来はデータベースに保存します。
        // 商品情報を仮にセッションに保存しておく例：
        session()->flash('success', '商品が出品されました！');

        return redirect()->route('items.index');
    }
}
