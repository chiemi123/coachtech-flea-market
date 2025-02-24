@extends('layouts.app')

@section('content')
<div class="container">
    <h2>メール認証誘導画面</h2>
    <div class="card">
        <div class="card-header">COACHTECH</div>
        <div class="card-body">
            <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p>メール認証を完了してください。</p>
            <a href="{{ route('verification.notice') }}" class="btn btn-primary">認証はこちらから</a>
            <form method="POST" action="{{ route('verification.resend') }}" style="margin-top: 10px;">
                @csrf
                <button type="submit" class="btn btn-link">認証メールを再送する</button>
            </form>
        </div>
    </div>
</div>
@endsection