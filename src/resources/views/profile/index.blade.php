@extends('layouts.app') <!-- 共通レイアウトを継承 -->

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}">
@endsection

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('content')
<div class="profile-container">
    <!-- プロフィール情報 -->
    <div class="profile-header">
        <!-- プロフィール画像 -->
        <div class="profile-image">
            @if ($user->profile_image)
            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像">
            @else
            <img src="{{ asset('images/default-avatar.png') }}" alt="デフォルトプロフィール画像">
            @endif
        </div>

        <!-- ユーザー情報 -->
        <div class="profile-info">
            <h2>{{ $user->username }}</h2>
        </div>

        <!-- プロフィール編集ボタン -->
        <div class="profile-edit">
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-danger">プロフィールを編集</a>
        </div>
    </div>

    <!-- タブメニュー -->
    <ul class="nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#listed-items" data-bs-toggle="tab">出品した商品</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#purchased-items" data-bs-toggle="tab">購入した商品</a>
        </li>
    </ul>

    <!-- タブコンテンツ -->
    <div class="tab-content">
        <!-- 出品した商品 -->
        <div class="tab-pane fade show active" id="listed-items">
            @if ($listedItems->isEmpty())
            <p>出品した商品はありません。</p>
            @else
            <ul class="item-list">
                @foreach ($listedItems as $item)
                <li class="item-card">
                    <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
                    <h3>{{ $item->name }}</h3>
                    <p>¥{{ number_format($item->price) }}</p>
                    <a href="{{ route('items.show', $item->id) }}">詳細を見る</a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>

        <!-- 購入した商品 -->
        <div class="tab-pane fade" id="purchased-items">
            @if ($purchasedItems->isEmpty())
            <p>購入した商品はありません。</p>
            @else
            <ul class="item-list">
                @foreach ($purchasedItems as $item)
                <li class="item-card">
                    <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
                    <h3>{{ $item->name }}</h3>
                    <p>¥{{ number_format($item->price) }}</p>
                    <a href="{{ route('items.show', $item->id) }}">詳細を見る</a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection