@extends('layouts.app')

@section('content')
<div class="container">
    <h2>購入が完了しました！</h2>
    <p>ご注文ありがとうございました。</p>
    <a href="{{ route('items.index') }}" class="btn btn-primary">トップページへ戻る</a>
</div>
@endsection