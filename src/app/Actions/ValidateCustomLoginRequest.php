<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ValidateCustomLoginRequest
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function handle(Request $request, \Closure $next)
    {
        try {
            Log::info('ValidateCustomLoginRequest: バリデーション開始', $request->all());

            Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ], [
                'email.required' => 'メールアドレスを入力してください。',
                'email.email' => '正しいメールアドレス形式で入力してください。',
                'password.required' => 'パスワードを入力してください。',
                'password.min' => 'パスワードは8文字以上で入力してください。',
            ])->validate();

            Log::info('ValidateCustomLoginRequest: バリデーション成功');
            // バリデーション成功時、次のアクションへ進む
            return $next($request);

            Log::info('ValidateCustomLoginRequest: バリデーション成功');
        } catch (ValidationException $e) {
            Log::error('ValidateCustomLoginRequest: バリデーション失敗', $e->errors());
            throw $e; // Laravelにエラー処理を委ねる
        }
    }
}
