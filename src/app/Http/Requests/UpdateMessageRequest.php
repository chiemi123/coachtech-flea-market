<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $message = $this->route('message');
        return $message && $this->user()->id === $message->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'body'  => ['required', 'string', 'max:400'],
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
