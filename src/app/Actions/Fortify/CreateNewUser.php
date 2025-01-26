<?php

namespace App\Actions\Fortify;

use App\Http\Requests\RegisterRequest; // フォームリクエストをインポート
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    //use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        //Validator::make($input, [
        //    'name' => ['required', 'string', 'max:255'],
        //    'email' => [
        //        'required',
        //        'string',
        //        'email',
        //        'max:255',
        //        Rule::unique(User::class),
        //    ],
        //    'password' => $this->passwordRules(),
        //])->validate();

        // ユーザーを作成
        //return User::create([
        //   'name' => $input['name'],
        //    'email' => $input['email'],
        //    'password' => Hash::make($input['password']),
        //]);

        // フォームリクエストのインスタンスを作成し、バリデーションを実行
        $request = app(RegisterRequest::class);
        $validatedData = $request->validated(); // バリデーション済みデータを取得

        // ユーザーを作成
        return User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
    }
}
