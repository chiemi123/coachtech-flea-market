@extends('layouts.app')

@section('content')
<div class="container">
    <h1>購入完了</h1>
    <p>購入が正常に完了しました。</p>
    <p>商品ID: {{ $purchase->item_id }}</p>
    <p>取引ID: {{ $purchase->transaction_id }}</p>
    <p>支払い方法: {{ $purchase->payment_method }}</p>
    <a href="{{ route('profile.index') }}" class="btn btn-primary">マイページへ</a>
</div>
@endsection