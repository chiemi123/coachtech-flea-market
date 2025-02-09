@extends('layouts.app')

@section('title', $item->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/item-show.css') }}">
@endsection

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('content')

<div class="item-detail-container">
    <!-- 左右のコンテナ -->
    <div class="item-info-container">
        <!-- 左側: 商品画像 -->
        <div class="item-image">
            <img src="{{ asset($item->item_image) }}" alt="{{ $item->name }}">
        </div>

        <!-- 右側: 商品情報 -->
        <div class="item-details">
            <h1 class="item-name">{{ $item->name }}</h1>
            <p class="brand-name">{{ $item->brand->name ?? 'ブランドなし' }}</p>
            <p class="item-price">¥{{ number_format($item->price) }} (税込)</p>

            <!-- いいね数とコメント数 -->
            <div class="item-stats">
                <div class="stat">
                    <!-- いいね数 -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="stat-icon">
                        <path d="M287.9 0c9.2 0 17.6 5.2 21.6 13.5l68.6 141.3 153.2 22.6c9 1.3 16.5 7.6 19.3 16.3s.5 18.1-5.9 24.5L433.6 328.4l26.2 155.6c1.5 9-2.2 18.1-9.7 23.5s-17.3 6-25.3 1.7l-137-73.2L151 509.1c-8.1 4.3-17.9 3.7-25.3-1.7s-11.2-14.5-9.7-23.5l26.2-155.6L31.1 218.2c-6.5-6.4-8.7-15.9-5.9-24.5s10.3-14.9 19.3-16.3l153.2-22.6L266.3 13.5C270.4 5.2 278.7 0 287.9 0zm0 79L235.4 187.2c-3.5 7.1-10.2 12.1-18.1 13.3L99 217.9 184.9 303c5.5 5.5 8.1 13.3 6.8 21L171.4 443.7l105.2-56.2c7.1-3.8 15.6-3.8 22.6 0l105.2 56.2L384.2 324.1c-1.3-7.7 1.2-15.5 6.8-21l85.9-85.1L358.6 200.5c-7.8-1.2-14.6-6.1-18.1-13.3L287.9 79z" />
                    </svg>
                    <span class="count">{{ $item->likes_count }}</span>
                </div>

                <!-- コメント数 -->
                <div class="stat">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="stat-icon">
                        <path d="M123.6 391.3c12.9-9.4 29.6-11.8 44.6-6.4c26.5 9.6 56.2 15.1 87.8 15.1c124.7 0 208-80.5 208-160s-83.3-160-208-160S48 160.5 48 240c0 32 12.4 62.8 35.7 89.2c8.6 9.7 12.8 22.5 11.8 35.5c-1.4 18.1-5.7 34.7-11.3 49.4c17-7.9 31.1-16.7 39.4-22.7zM21.2 431.9c1.8-2.7 3.5-5.4 5.1-8.1c10-16.6 19.5-38.4 21.4-62.9C17.7 326.8 0 285.1 0 240C0 125.1 114.6 32 256 32s256 93.1 256 208s-114.6 208-256 208c-37.1 0-72.3-6.4-104.1-17.9c-11.9 8.7-31.3 20.6-54.3 30.6c-15.1 6.6-32.3 12.6-50.1 16.1c-.8 .2-1.6 .3-2.4 .5c-4.4 .8-8.7 1.5-13.2 1.9c-.2 0-.5 .1-.7 .1c-5.1 .5-10.2 .8-15.3 .8c-6.5 0-12.3-3.9-14.8-9.9c-2.5-6-1.1-12.8 3.4-17.4c4.1-4.2 7.8-8.7 11.3-13.5c1.7-2.3 3.3-4.6 4.8-6.9l.3-.5z" />
                    </svg>
                    <span class="count">{{ $item->comments_count }}</span>
                </div>
            </div>

            <!-- 購入ボタン -->
            <a href="{{ route('purchase.show', $item->id) }}" class="purchase-button">購入手続きへ</a>

            <!-- 商品説明 -->
            <div class="item-description">
                <h2>商品説明</h2>
                <p>{{ $item->description }}</p>
            </div>

            <!-- 商品情報 -->
            <div class="item-info">
                <h2>商品の情報</h2>
                <p>カテゴリー:
                    @foreach ($item->categories as $category)
                    <span class="category">{{ $category->name }}</span>
                    @endforeach
                </p>
                <p>商品の状態: {{ $item->condition->name }}</p>
            </div>

            <!-- コメントセクション -->
            <div class="comment-section">
                <h2 class="comment-title">コメント ({{ $item->comments_count }})</h2>
                @foreach ($item->comments as $comment)
                <div class="comment">
                    <div class="comment-user">
                        <strong>{{ $comment->user->name }}</strong>
                    </div>
                    <p class="comment-content">{{ $comment->content }}</p>
                </div>
                @endforeach

                <!-- コメント投稿フォーム -->
                @auth
                <form action="{{ route('items.comment', $item->id) }}" method="POST" class="comment-form">
                    @csrf
                    <textarea name="content" placeholder="商品のコメントを入力してください"></textarea>
                    <button type="submit" class="comment-submit">コメントを送信する</button>
                </form>
                @else
                <p class="login-message">コメントを投稿するには <a href="{{ route('login') }}">ログイン</a> してください。</p>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection