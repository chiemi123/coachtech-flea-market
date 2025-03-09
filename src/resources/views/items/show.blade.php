@extends('layouts.app')

@section('title', $item->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/item-show.css') }}">
@endsection

@section('header-extra')

@endsection

@section('content')

<div class="item">
    <div class="item__container">
        <!-- 左側: 商品画像 -->
        <div class="item__image">
            @if (Str::startsWith($item->item_image, 'http'))
            <img src="{{ $item->item_image }}" alt="{{ $item->name }}">
            @else
            <img src="{{ Storage::url($item->item_image) }}" alt="{{ $item->name }}">
            @endif
        </div>

        <!-- 右側: 商品情報 -->
        <div class="item__details">
            <div class="item__summary">
                <h1 class="item__name">{{ $item->name }}</h1>
                <p class="item__brand">{{ $item->brand ?? 'ブランドなし' }}</p>
                <p class="item__price">¥{{ number_format($item->price) }} (税込)</p>

                <!-- いいね数とコメント数 -->
                <div class="item__stats">
                    <div class="item__like">
                        @auth
                        <form action="{{ route('likes.toggle', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="item__like-button {{ Auth::user()->likes->contains('item_id', $item->id) ? 'active' : '' }}">
                                <img src="{{ asset('images/star-outline.png') }}" alt="いいね" class="item__like-icon">
                                <span class="item__like-count">{{ $item->likes_count }}</span>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('login') }}" method="GET">
                            <button type="submit" class="item__like-button">
                                <img src="{{ asset('images/star-outline.png') }}" alt="いいね" class="item__like-icon">
                                <span class="item__like-count">{{ $item->likes_count }}</span>
                            </button>
                        </form>
                        @endauth
                    </div>

                    <!-- コメント数 -->
                    <div class="item__stat">
                        <img src="{{ asset('images/stat-icon.png') }}" alt="コメント" class="item__stat-icon">
                        <span class="item__stat-count">{{ $item->comments_count }}</span>
                    </div>
                </div>
            </div>

            <!-- 購入ボタン -->
            <a href="{{ route('purchase.show', $item->id) }}" class="item__purchase-button">購入手続きへ</a>

            <!-- 商品説明 -->
            <div class="item__description">
                <h2>商品説明</h2>
                <p>{{ $item->description }}</p>
            </div>

            <!-- 商品情報 -->
            <div class="item__info">
                <h2>商品の情報</h2>
                <p class="item__category">カテゴリー:
                    @foreach ($item->categories as $category)
                    <span class="item__category-label">{{ $category->name }}</span>
                    @endforeach
                </p>
                <p class="item__condition">
                    <span class="item__label">商品の状態:</span>
                    <span class="item__value">{{ $item->condition->name }}</span>
                </p>
            </div>

            <!-- コメントセクション -->
            <div class="item__comments">
                <h2 class="item__comments-title">コメント ({{ $item->comments_count }})</h2>
                @foreach ($item->comments as $comment)
                <div class="item__comment">
                    <div class="item__comment-header">
                        <div class="item__comment-user-icon">
                            <img src="{{ $comment->user->profile_image ? asset('storage/' . $comment->user->profile_image) : asset('images/default-avatar.png') }}" alt="ユーザー画像">
                        </div>
                        <div class="item__comment-user">
                            <strong>{{ $comment->user->name }}</strong>
                        </div>
                    </div>
                    <p class="item__comment-content">{{ $comment->content }}</p>
                </div>
                @endforeach

                <!-- コメント投稿フォーム -->
                <h2>商品のコメント</h2>
                @auth
                <form action="{{ route('items.comment', $item->id) }}" method="POST" class="item__comment-form">
                    @csrf
                    <textarea name="content" placeholder="商品のコメントを入力してください"></textarea>
                    <button type="submit" class="item__comment-submit">コメントを送信する</button>
                </form>
                @else
                <form action="{{ route('login') }}" method="GET">
                    <button type="submit" class="item__comment-submit">コメントを投稿する</button>
                </form>
                @endauth
            </div>
        </div>
    </div>
</div>

@endsection