<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'payment_method' => ['required', 'in:コンビニ払い,クレジットカード'],
            'address_id' => ['required', 'exists:addresses,id'],
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
