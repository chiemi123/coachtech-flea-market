<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Condition;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // 商品一覧画面
    public function index()
    {
        $items = Item::with(['user', 'brand', 'condition', 'categories', 'likes', 'comments'])->get();
        return view('items.index', compact('items')); // ビューへデータを渡す
    }

    // 商品詳細画面
    public function show($item_id)
    {
        // 商品情報をデータベースから取得
        $item = Item::with(['categories', 'comments.user'])->findOrFail($item_id);

        return view('items.show', compact('item'));
    }

    // 商品出品画面の表示
    public function create()
    {
        // カテゴリー一覧を取得
        $categories = Category::all();
        //　コンディション一覧を取得
        $conditions = Condition::all();

        return view('sell.create', compact('categories', 'conditions'));
    }

    // 商品出品処理
    public function store(ExhibitionRequest $request)
    {
        // バリデーション済みデータを取得
        $validated = $request->validated();

        // 画像を保存
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            $validated['image'] = str_replace('public/', 'storage/', $imagePath);
        }

        // 商品をデータベースに保存
        $validated['user_id'] = Auth::id();
        Item::create($validated);

        return redirect()->route('items.index')->with('success', '商品が出品されました！');
    }

    // 商品へのコメント投稿
    public function addComment(CommentRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'content' => $request->content,
        ]);

        // コメント数を増加
        $item->increment('comments_count');

        return back()->with('success', 'コメントを投稿しました！');
    }
}
