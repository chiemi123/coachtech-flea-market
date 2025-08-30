<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
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
        /** @var User $me */
        $me = Auth::user();
        $user = $me;

        // 出品した商品
        $listedItems = Item::where('user_id', $me->id)->latest()->get();

        // 購入した商品
        $purchasedItems = $me->purchases() // ← 自分が購入したデータ
            ->where('status', 'completed')
            ->with(['item' => fn($q) => $q->withTrashed()])
            ->get()
            ->filter(fn($purchase) => $purchase->ratingBy($me));


        // 取引中 + 未読数（評価未完了のcompletedも含める）
        $purchases = Purchase::participating($me->id)
            ->where(function ($q) use ($me) {
                $q->whereIn('status', ['pending', 'paid', 'shipping']) // 通常の進行中
                    ->orWhere(function ($q2) use ($me) {
                        $q2->where('status', 'completed')
                            ->whereDoesntHave('ratings', fn($r) => $r->where('rater_id', $me->id)); // 評価未完了のcompleted
                    });
            })
            ->with([
                'item:id,name,item_image,price,user_id',
            ])
            ->withCount([
                'messages as unread_count' => function ($q) use ($me) {
                    $q->where('user_id', '<>', $me->id)
                        ->whereDoesntHave('reads', fn($r) => $r->where('user_id', $me->id));
                },
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        // 未読合計（評価済のcompletedは含まれない）
        $inProgressUnreadTotal = $purchases->sum('unread_count');

        return view('profile.index', compact(
            'user',
            'listedItems',
            'purchasedItems',
            'purchases',
            'inProgressUnreadTotal',
            'me'
        ));
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
