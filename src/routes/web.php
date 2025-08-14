<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RatingController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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


// ===========================
//  ログイン不要でアクセス可能なルート
// ===========================

// 商品一覧画面
Route::get('/', [ItemController::class, 'index'])->name('items.index')->middleware('verified.if.loggedin');

// マイリスト表示
Route::get('/mylist', [ItemController::class, 'myList'])->name('items.mylist');

// 商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// 商品のコメント投稿ルート
Route::post('/item/{item}/comment', [ItemController::class, 'addComment'])->name('items.comment');

Route::post('/webhook/stripe', [StripeWebhookController::class, 'handleWebhook']);

Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');

Route::get('/purchase/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');



// ===========================
//  ログイン必須のルート
// ===========================

// メール認証通知画面（ユーザーに「認証メールを送信しました」などを表示）
Route::get('/email/verify', function () {
    if (!Auth::check()) { // ユーザーが未ログインなら
        return redirect('/login'); // ログインページへリダイレクト
    }
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール認証リンクをクリックしたときのルート
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // 認証完了
    return redirect('/mypage/profile'); // 認証後のリダイレクト先
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メールの再送信ルート
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// ===========================
//  認証済みユーザー向けのルート
// ===========================

Route::middleware(['auth', 'verified'])->group(function () {
    // プロフィール設定画面
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');
    Route::put('/mypage/profile', [UserController::class, 'update'])->name('profile.update.put');
});

// ===========================
//  プロフィール完了済みユーザー向けのルート
// ===========================


Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {

    // プロフィール画面
    Route::get('/mypage', [UserController::class, 'index'])->name('profile.index');

    //いいね機能
    Route::post('/item/{id}/like', [ItemController::class, 'toggleLike'])->name('likes.toggle');

    // 商品購入画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');

    // 支払方法選択の反映
    Route::post('/purchase/{item_id}/confirm', [PurchaseController::class, 'confirm'])->name('purchase.confirm');

    // Stripeの購入決済
    Route::post('/purchase/checkout/{id}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');

    // 購入処理
    Route::post('/purchases/{purchase}/complete', [PurchaseController::class, 'complete'])->name('purchases.complete');

    // 送付先住所変更画面item_
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('address.update');

    // 商品出品画面
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');

    // 商品出品処理
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');

    // 追加機能を実装用
    Route::get('/purchases/{purchase}/chat', [ChatController::class, 'show'])->name('purchases.chat');
    Route::get('/purchases/{purchase}/messages', [MessageController::class, 'index']);
    Route::post('/purchases/{purchase}/messages', [MessageController::class, 'store']);
    Route::post('/purchases/{purchase}/ratings', [RatingController::class, 'store'])->name('ratings.store');

});

// ===========================
//  ログアウト処理
// ===========================

Route::post('/logout', function () {
    Auth::logout(); // ログアウト処理

    request()->session()->invalidate(); // セッション無効化（セキュリティ強化）
    request()->session()->regenerateToken(); // CSRFトークンを再生成

    return redirect('/login'); // ログイン画面にリダイレクト
})->name('logout');
