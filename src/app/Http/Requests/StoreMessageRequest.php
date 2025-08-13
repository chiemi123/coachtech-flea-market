<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // 参加者のみ許可（購買者 or 出品者）
        $purchase = $this->route('purchase');
        $me = $this->user();

        return $purchase
            && $me
            && ($purchase->user_id === $me->id || optional($purchase->item)->user_id === $me->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // 本文：必須、最大400文字
            'body'  => ['required', 'string', 'max:400'],

            // 画像：.jpeg / .png のみ、.jpgは不可
            'image' => [
                'nullable',
                'file',
                'mimes:jpeg,png',
                function ($attr, $value, $fail) {
                    if (!$value) return;
                    if (strtolower($value->getClientOriginalExtension()) === 'jpg') {
                        $fail('「.png」または「.jpeg」形式でアップロードしてください');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => '本文を入力してください',
            'body.max'      => '本文は400文字以内で入力してください',
            'image.mimes'   => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
