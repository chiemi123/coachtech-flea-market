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
                    // `address_id` が `users` の `id` である場合
                    if (\App\Models\User::where('id', $value)->exists()) {
                        return; // `user_id` が存在する場合はバリデーションをスキップ
                    }

                    //  `addresses` にデータが存在しない場合はエラー
                    if (!is_numeric($value) || !\App\Models\Address::where('id', $value)->exists()) {
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
