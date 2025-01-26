<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email', // メールアドレスは必須で、正しい形式
            'password' => 'required|string|min:8', // パスワードは必須、文字列、8文字以上
        ];
    }

    /**
     * カスタムエラーメッセージを設定する
     */
    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '正しいメールアドレス形式で入力してください。',
            'password.required' => 'パスワードを入力してください。',
        ];
    }
}
