@extends('layouts.app')

@section('content')
<div class="container">
    <h2>決済がキャンセルされました</h2>
    <p>購入を完了できませんでした。もう一度お試しください。</p>
    <a href="{{ route('purchase.show', ['item_id' => session('last_item_id') ?? 1]) }}" class="btn btn-warning">購入画面へ戻る</a>
</div>
@endsection