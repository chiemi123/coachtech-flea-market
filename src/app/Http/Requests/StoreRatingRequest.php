<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $purchase = $this->route('purchase');
        $me = $this->user();
        return $purchase && $me
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
            'score'    => ['required', 'integer', 'min:1', 'max:5'],
            'ratee_id' => ['required', 'integer', 'exists:users,id', 'different:auth_id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['auth_id' => optional($this->user())->id]);
    }

    public function messages(): array
    {
        return [
            'score.required'   => '評価点を選択してください。',
            'score.min'        => '1〜5の範囲で選択してください。',
            'score.max'        => '1〜5の範囲で選択してください。',
            'ratee_id.different' => '自分自身を評価することはできません。',
        ];
    }
}
