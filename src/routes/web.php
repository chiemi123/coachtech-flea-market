<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::middleware(['auth'])->group(function () {
    // プロフィール設定画面
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');
});


// 商品一覧画面
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// 商品のコメント投稿ルート
Route::post('/item/{item}/comment', [ItemController::class, 'addComment'])->name('items.comment');


Route::middleware(['auth', 'profile.complete'])->group(function () {

    // 商品一覧画面
    Route::get('/', [ItemController::class, 'index'])->name('items.index');

    // プロフィール画面
    Route::get('/mypage', [UserController::class, 'index'])->name('profile.index');

    // 商品購入画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');

    // 送付先住所変更画面
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('address.update');

    // 商品出品画面
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');

    // 商品出品処理
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');
});



if (app()->environment('local')) {
    Route::get('/debug-auth', function () {
        $user = Auth::user();

        if ($user) {
            // ユーザー情報を返す
            return response()->json([
                'id' => $user->id,
                'username' => $user->username,
                'postal_code' => $user->postal_code,
                'address' => $user->address,
                'building_name' => $user->building_name,
            ]);
        }


        // 認証されていない場合のメッセージ
        return response()->json(['message' => 'ユーザーが認証されていません'], 401);
    })->middleware('auth');


    Route::get('/debug-session', function () {
        return response()->json(session()->all());
    });
}

Route::post('/logout', function () {
    Auth::logout(); // ログアウト処理
    return redirect('/login'); // ログイン画面にリダイレクト
})->name('logout');
