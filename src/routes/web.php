<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;


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


Route::get('/', function () {
    return view('index'); // トップ画面
})->name('home');


Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::get('/', function () {
        return view('index'); // トップ画面
    })->name('home');

    // プロフィール画面
    Route::get('/mypage', [UserController::class, 'index'])->name('profile.index');
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
