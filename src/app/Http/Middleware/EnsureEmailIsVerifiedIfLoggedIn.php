<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerifiedIfLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * * - ログインしていない（ゲスト）の場合はそのまま次の処理へ渡す。
     * - ログインしているがメール認証が未完了の場合は、強制的に /email/verify にリダイレクトする。
     * - ログインしておりメール認証が完了している場合はそのままリクエストを渡す。
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (is_null($user->email_verified_at)) {
                // ユーザーがログインしているがメール認証が完了していない場合
                return redirect('/email/verify');
            }
        }
        // ゲストユーザー、またはメール認証済みユーザーの場合はそのままリクエストを次へ渡す
        return $next($request);
    }
}
