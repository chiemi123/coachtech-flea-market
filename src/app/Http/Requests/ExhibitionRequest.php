<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'item_image' => ['required', 'image', 'mimes:jpg,png', 'max:2048'],
            'category_ids' => ['required', 'array'], // ✅ 配列として受け取る
            'category_ids.*' => ['exists:categories,id'], // ✅ 各要素が `categories` テーブルに存在するかチェック
            'condition_id' => ['required', 'exists:conditions,id'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください。',
            'description.required' => '商品説明を入力してください。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'item_image.required' => '商品画像をアップロードしてください。',
            'item_image.image' => '画像ファイルをアップロードしてください。',
            'item_image.mimes' => 'アップロードできる画像は JPG または PNG のみです。',
            'item_image.max' => '画像サイズは最大 2MB までです。',
            'category_ids.required' => '商品のカテゴリーを選択してください。',
            'category_ids.exists' => '選択されたカテゴリーが存在しません。',
            'condition_id.required' => '商品の状態を選択してください。',
            'condition_id.exists' => '選択された商品の状態が存在しません。',
            'price.required' => '価格を入力してください。',
            'price.numeric' => '価格は数値で入力してください。',
            'price.min' => '価格は0円以上で入力してください。',
        ];
    }
}
