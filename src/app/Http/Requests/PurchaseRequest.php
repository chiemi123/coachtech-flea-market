<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check(); // 認証済みユーザーのみ許可
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => ['required', 'in:コンビニ払い,クレジットカード'],
            'address_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    // 🚀 `user_table` ならバリデーションを通す（`users` の住所を使用）
                    if ($value === 'user_table') {
                        return;
                    }

                    // 🚀 `addresses` テーブルに `address_id` が存在するかチェック
                    if (!\App\Models\Address::where('id', $value)->exists()) {
                        $fail('選択した配送先が存在しません。');
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in' => '選択できる支払い方法は「コンビニ払い」または「クレジットカード」のみです。',
            'address_id.required' => '配送先を選択してください。',
            'address_id.exists' => '選択した配送先が存在しません。',
        ];
    }
}
