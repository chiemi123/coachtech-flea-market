<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check(); // 認証済みである場合のみ許可
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|string|max:255',
            'postal_code' => 'required|string|regex:/^\d{4}-\d{4}$/|max:10',
            'address' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
        ];
    }
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'profile_image.image' => 'アップロードできるのは画像ファイルのみです。',
            'profile_image.mimes' => '許可されている画像形式はjpeg, png, jpg, gifのみです。',
            'profile_image.max' => '画像ファイルのサイズは2MB以下にしてください。',
            'username.required' => 'ユーザー名を入力してください。',
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください。',
            'address.required' => '住所を入力してください。',
            'building_name.string' => '建物名は文字列で入力してください。',
        ];
    }
}
