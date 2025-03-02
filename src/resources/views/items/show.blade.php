@extends('layouts.app')

@section('title', $item->name)

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/item-show.css') }}">
@endsection

@section('header-extra')

@endsection

@section('content')

<div class="item-detail-container">
    <!-- 左右のコンテナ -->
    <div class="item-info-container">
        <!-- 左側: 商品画像 -->
        <div class="item-image">
            @if (Str::startsWith($item->item_image, 'http'))
            <!-- 画像がURLの場合（外部URLやアイテムテーブルに保存されている画像） -->
            <img src="{{ $item->item_image }}" alt="{{ $item->name }}">
            @else
            <!-- 画像がストレージ内に保存されている場合 -->
            <img src="{{ Storage::url($item->item_image) }}" alt="{{ $item->name }}">
            @endif
        </div>

        <!-- 右側: 商品情報 -->
        <div class="item-details">
            <div class="item-summary">
                <h1 class="item-name">{{ $item->name }}</h1>
                <p class="brand-name">{{ $item->brand ?? 'ブランドなし' }}</p>
                <p class="item-price">¥{{ number_format($item->price) }} (税込)</p>

                <!-- いいね数とコメント数 -->
                <div class="item-stats">
                    <div class="like-section">
                        @auth
                        <form action="{{ route('likes.toggle', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="like-button {{ Auth::user()->likes->contains('item_id', $item->id) ? 'active' : '' }}">
                                <img src="{{ asset('images/star-outline.png') }}" alt="いいね" class="star-icon">
                                <span class="like-count">{{ $item->likes_count }}</span>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('login') }}" method="GET">
                            <button type="submit" class="like-button">
                                <img src="{{ asset('images/star-outline.png') }}" alt="いいね" class="star-icon">
                                <span class="like-count">{{ $item->likes_count }}</span>
                            </button>
                        </form>
                        @endauth
                    </div>

                    <!-- コメント数 -->
                    <div class="stat">
                        <img src="{{ asset('images/stat-icon.png') }}" alt="いいね" class="stat-icon">
                        <span class="stat-count">{{ $item->comments_count }}</span>
                    </div>
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
                <p class="item-condition">商品の状態: {{ $item->condition->name }}</p>
            </div>

            <!-- コメントセクション -->
            <div class="comment-section">
                <h2 class="comment-title">コメント ({{ $item->comments_count }})</h2>
                @foreach ($item->comments as $comment)
                <div class="comment">
                    <div class="comment-header">
                        <!-- ユーザーアイコン -->
                        <div class="comment-user-icon">
                            <img src="{{ $comment->user->profile_image ? asset('storage/' . $comment->user->profile_image) : asset('images/default-avatar.png') }}" alt="ユーザー画像">
                        </div>
                        <!-- ユーザー名 -->
                        <div class="comment-user">
                            <strong>{{ $comment->user->name }}</strong>
                        </div>
                    </div>
                    <!-- コメント内容 -->
                    <p class="comment-content">{{ $comment->content }}</p>
                </div>
                @endforeach

                <!-- コメント投稿フォーム -->
                <h2>商品のコメント</h2>
                @auth
                <form action="{{ route('items.comment', $item->id) }}" method="POST" class="comment-form">
                    @csrf
                    <textarea name="content" placeholder="商品のコメントを入力してください"></textarea>
                    <button type="submit" class="comment-submit">コメントを送信する</button>
                </form>
                @else
                <form action="{{ route('login') }}" method="GET">
                    <button type="submit" class="comment-submit">コメントを投稿する</button>
                </form>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection