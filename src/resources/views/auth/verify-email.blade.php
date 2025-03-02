@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-container">
    <div class="verify-wrapper">
        <div class="verify-message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </div>
        <a href="{{ route('verification.notice') }}" class="verify-button">認証はこちらから</a>
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="resend-link">認証メールを再送する</button>
        </form>
    </div>
</div>
@endsection