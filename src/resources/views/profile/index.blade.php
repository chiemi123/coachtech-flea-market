@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}">
@endsection

@section('title', 'マイページ')

@section('content')

<div class="profile">
    <!-- プロフィール情報 -->
    <div class="profile__header">
        <!-- プロフィール画像 -->
        <div class="profile__image">
            @if ($user->profile_image)
            <img src="{{ Storage::url($user->profile_image) }}" alt="プロフィール画像">
            @else
            <img src="{{ asset('images/default-avatar.png') }}" alt="デフォルトプロフィール画像">
            @endif
        </div>

        <!-- ユーザー情報 -->
        <div class="profile__info">
            <h2>{{ $user->username }}</h2>
        </div>

        <!-- プロフィール編集ボタン -->
        <div class="profile__edit">
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-danger">プロフィールを編集</a>
        </div>
    </div>

    <!-- タブメニュー -->
    <div class="profile__tabs">
        <input type="radio" id="tab-listed" name="tab" class="profile__tab-input" checked>
        <label for="tab-listed" class="profile__tab-label">出品した商品</label>

        <input type="radio" id="tab-purchased" name="tab" class="profile__tab-input">
        <label for="tab-purchased" class="profile__tab-label">購入した商品</label>

        <!-- タブコンテンツ -->
        <div class="profile__tabs-content">
            <!-- 出品した商品 -->
            <div class="profile__tab-panel" id="tab-listed-content">
                @if ($listedItems->isEmpty())
                <p class="profile__message">出品した商品はありません。</p>
                @else
                <ul class="profile__items">
                    @foreach ($listedItems as $item)
                    <li class="profile__item">
                        <a href="{{ route('items.show', $item->id) }}">
                            <div class="profile__item-image">
                                @if (Str::startsWith($item->item_image, ['http://', 'https://']))
                                <img src="{{ $item->item_image }}" alt="{{ $item->name }}">
                                @else
                                <img src="{{ Storage::url($item->item_image) }}" alt="{{ $item->name }}">
                                @endif

                                @if ($item->is_sold)
                                <span class="profile__item-sold">sold</span>
                                @endif
                            </div>
                            <h3>{{ $item->name }}</h3>
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

            <!-- 購入した商品 -->
            <div class="profile__tab-panel" id="tab-purchased-content">
                @if ($purchasedItems->isEmpty())
                <p class="profile__message">購入した商品はありません。</p>
                @else
                <ul class="profile__items">
                    @foreach ($purchasedItems as $item)
                    <li class="profile__item">
                        <a href="{{ route('items.show', $item->id) }}">
                            <div class="profile__item-image">
                                @if (Str::startsWith($item->item_image, ['http://', 'https://']))
                                <img src="{{ $item->item_image }}" alt="{{ $item->name }}">
                                @else
                                <img src="{{ Storage::url($item->item_image) }}" alt="{{ $item->name }}">
                                @endif

                                @if ($item->is_sold)
                                <span class="profile__item-sold">sold</span>
                                @endif
                            </div>
                            <h3>{{ $item->name }}</h3>
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection