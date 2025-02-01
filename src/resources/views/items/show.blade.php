@extends('layouts.app')

@section('title', $item['name'])

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('content')
<div class="item-detail">
    <div style="display: flex; gap: 20px;">
        <!-- 左側：商品画像 -->
        <div>
            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width:300px; height:300px; background-color:#eee;">
        </div>

        <!-- 右側：商品情報 -->
        <div style="flex-grow: 1;">
            <h1>{{ $item['name'] }}</h1>
            <p>ブランド名: {{ $item['brand'] }}</p>
            <p style="font-size: 24px; color: red;">¥{{ number_format($item['price']) }} (税込)</p>
            <button style="background-color: red; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                購入手続きへ
            </button>

            <h2>商品説明</h2>
            <p>{{ $item['description'] }}</p>

            <h2>商品の情報</h2>
            <p>カテゴリー:
                @foreach ($item['categories'] as $category)
                <span style="background-color: #eee; padding: 5px 10px; margin-right: 5px;">{{ $category }}</span>
                @endforeach
            </p>
            <p>商品の状態: {{ $item['condition'] }}</p>
        </div>
    </div>

    <!-- コメントセクション -->
    <div style="margin-top: 30px;">
        <h2>コメント({{ count($item['comments']) }})</h2>
        @foreach ($item['comments'] as $comment)
        <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
            <strong>{{ $comment['user'] }}</strong>
            <p>{{ $comment['comment'] }}</p>
        </div>
        @endforeach

        <form action="#" method="POST" style="margin-top: 20px;">
            @csrf
            <textarea name="comment" placeholder="商品のコメントを入力してください" style="width: 100%; height: 100px; margin-bottom: 10px;"></textarea>
            <button type="submit" style="background-color: red; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                コメントを送信する
            </button>
        </form>
    </div>
</div>
@endsection