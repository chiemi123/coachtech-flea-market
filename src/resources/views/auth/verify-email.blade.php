@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="verify">
    <div class="verify__wrapper">
        <div class="verify__message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </div>
        <form method="GET" action="{{ route('verification.notice') }}">
            <button type="submit" class="verify__button">認証はこちらから</button>
        </form>
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="verify__resend-button" aria-label="認証メールを再送する">
                認証メールを再送する
            </button>
        </form>
    </div>
</div>
@endsection
