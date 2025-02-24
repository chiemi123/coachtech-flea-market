<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    /**
     * ログインユーザーのみコメント可能
     */
    public function authorize()
    {
        return Auth::check(); // ユーザーがログインしている場合のみ許可
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    /**
     * バリデーションルール
     */
    public function rules()
    {
        return [
            'content' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * カスタムエラーメッセージ
     */
    public function messages()
    {
        return [
            'content.required' => 'コメントを入力してください。',
            'content.string' => 'コメントは文字列で入力してください。',
            'content.max' => 'コメントは255文字以内で入力してください。',
        ];
    }
}
