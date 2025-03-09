@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<div class="register">
    <div class="register__heading">
        <h2>会員登録</h2>
    </div>
    <form class="register__form" action="/register" method="post">
        @csrf
        <div class="register__group">
            <div class="register__group-title">
                <label for="name" class="register__label">ユーザー名</label>
            </div>
            <div class="register__input">
                <input id="name" type="text" name="name" value="{{ old('name') }}" />
                <div class="register__error">
                    @error('name') {{ $message }} @enderror
                </div>
            </div>
        </div>

        <div class="register__group">
            <div class="register__group-title">
                <label for="email" class="register__label">メールアドレス</label>
            </div>
            <div class="register__input">
                <input id="email" type="email" name="email" value="{{ old('email') }}" />
                <div class="register__error">
                    @error('email') {{ $message }} @enderror
                </div>
            </div>
        </div>

        <div class="register__group">
            <div class="register__group-title">
                <label for="password" class="register__label">パスワード</label>
            </div>
            <div class="register__input">
                <input id="password" type="password" name="password" />
                <div class="register__error">
                    @error('password') {{ $message }} @enderror
                </div>
            </div>
        </div>

        <div class="register__group">
            <div class="register__group-title">
                <label for="password_confirmation" class="register__label">確認用パスワード</label>
            </div>
            <div class="register__input">
                <input id="password_confirmation" type="password" name="password_confirmation" />
                <div class="register__error">
                    @error('password_confirmation') {{ $message }} @enderror
                </div>
            </div>
        </div>

        <div class="register__button">
            <button class="register__button-submit" type="submit">登録する</button>
        </div>
    </form>
    
    <div class="register__login-link">
        <a class="register__login-button" href="/login">ログインはこちら</a>
    </div>
</div>
@endsection
