<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'profile_image' => ['nullable', 'image', 'mimes:jpg,png', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'profile_image.required' => 'プロフィール画像をアップロードしてください。',
            'profile_image.image' => '画像ファイルをアップロードしてください。',
            'profile_image.mimes' => 'アップロードできる画像は JPG または PNG のみです。',
            'profile_image.max' => '画像サイズは最大 2MB までです。',
        ];
    }
}
