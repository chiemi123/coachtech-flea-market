@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="login">
    <div class="login__heading">
        <h2>ログイン</h2>
    </div>
    <form class="login__form" action="/login" method="post">
        @csrf
        <div class="login__group">
            <label for="email" class="login__label">ユーザー名／メールアドレス</label>
            <div class="login__input">
                <input id="email" type="email" name="email" value="{{ old('email') }}" />
            </div>
            <div class="login__error">
                @error('email')
                {{ $message }}
                @enderror
            </div>
        </div>

        <div class="login__group">
            <label for="password" class="login__label">パスワード</label>
            <div class="login__input">
                <input id="password" type="password" name="password" />
            </div>
            <div class="login__error">
                @error('password')
                {{ $message }}
                @enderror
            </div>
        </div>

        @if (session('error'))
        <span class="text-red-500">{{ session('error') }}</span>
        @endif

        <div class="login-button">
            <button class="login-button__submit" type="submit">ログインする</button>
        </div>
    </form>
    <div class="register-link">
        <a class="register-link__button" href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection
