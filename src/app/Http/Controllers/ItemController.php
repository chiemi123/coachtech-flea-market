<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Like;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // 商品一覧画面
    public function index(Request $request)
    {

        $search = $request->input('search');

        $query = Item::query();

        // ログインしている場合は自分の商品を除外
        if (auth()->check()) {
            $query->where('user_id', '<>', auth()->id());
        }

        // スペースのみの検索を防ぐ
        if (!empty($search)) {
            $convertedSearch = mb_convert_kana($search, 's'); // 全角スペースを半角に変換
            $trimmedSearch = trim($convertedSearch); // 前後のスペースを削除

            if ($trimmedSearch !== '') {
                // 検索履歴をセッションに保存
                session(['last_search' => $trimmedSearch]);

                // スペース区切りで分割し、各キーワードをAND検索
                $keywords = preg_split('/\s+/u', $trimmedSearch);
                foreach ($keywords as $keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%");
                }
            }
        }

        $items = $query->get();

        return view('items.index', compact('items'));
    }

    // マイリストの取得
    public function myList(Request $request)
    {
        // 🔹 未認証のユーザーは空のリストを返す
        if (!Auth::check()) {
            return view('items.index', ['items' => collect([])]);
        }

        $user = Auth::user();
        $search = mb_convert_kana($request->input('search'), 's'); // 全角スペースを半角に
        $trimmedSearch = trim($search);

        // いいねした商品から「自分が出品した商品」は除外
        $items = Item::whereHas('likes', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('user_id', '!=', $user->id)
            ->when($trimmedSearch, function ($query, $trimmedSearch) {
                if ($trimmedSearch !== '') {
                    $keywords = preg_split('/\s+/u', $trimmedSearch); // スペースで分割
                    foreach ($keywords as $keyword) {
                        $query->where('name', 'LIKE', "%{$keyword}%");
                    }
                }
                return $query;
            })->get();

        return view('items.index', compact('items'));
    }


    // 商品詳細画面
    public function show($item_id)
    {
        // 商品情報をデータベースから取得
        $item = Item::with(['likes', 'categories', 'comments.user'])->findOrFail($item_id);

        return view('items.show', compact('item'));
    }

    // いいね追加・解除
    public function toggleLike(Request $request, $id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($id);

        // いいねが既に存在するかチェック
        $like = Like::where('user_id', $user->id)->where('item_id', $item->id)->first();

        if ($like) {
            // いいねを解除
            $like->delete();
            $item->likes_count--;
        } else {
            // いいねを追加
            Like::create(['user_id' => $user->id, 'item_id' => $item->id]);
            $item->likes_count++;
        }
        $item->save();

        // リダイレクトして画面を更新
        return redirect()->route('items.show', $id);
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
        // brand のバリデーションをここで追加
        $request->validate([
            'brand' => 'nullable|string|max:255', // フォームリクエストに含めず、ここでチェック
        ]);

        // バリデーション済みデータを取得
        $validated = $request->validated();

        // brand が NULL または空文字の場合、デフォルトで "ノーブランド" を設定
        $validated['brand'] = $request->input('brand') ?: 'ノーブランド';

        // 画像を保存
        if ($request->hasFile('item_image')) {
            $imagePath = $request->file('item_image')->store('public/images'); // "public/images" に保存
            $validated['item_image'] = str_replace('public/', '', $imagePath); // "images/ファイル名.jpg" で保存
        }

        // 商品をデータベースに保存
        $validated['user_id'] = Auth::id();
        $item = Item::create($validated);

        // **カテゴリーを紐付ける**
        $item->categories()->attach($validated['category_ids']); // ✅ カテゴリーを登録

        return redirect()->route('profile.index')->with('success', '商品が出品されました！');
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
