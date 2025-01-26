<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // ログイン中のユーザーを取得
        $user = Auth::user();

        if ($user) {
            Log::info('profile_completed: ' . $user->profile_completed); // 値をログに出力
        }

        // プロフィールが未完了（profile_completedがfalse）の場合
        if ($user && !$user->profile_completed) {
            return redirect('/mypage/profile'); // プロフィール設定画面にリダイレクト
        }

        // 問題なければ次の処理へ
        return $next($request);
    }
}
