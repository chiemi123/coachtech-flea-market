@extends('layouts.app') <!-- 共通レイアウトを継承 -->

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}">
@endsection

@section('header-extra')

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
    <div class="tab-container">
        <input type="radio" id="tab1" name="tab" class="tab-input" checked>
        <label for="tab1">出品した商品</label>

        <input type="radio" id="tab2" name="tab" class="tab-input">
        <label for="tab2">購入した商品</label>



        <!-- タブコンテンツ -->
        <div class="tab-content-wrapper">
            <div class="tab-content" id="content1">
                <!-- 出品した商品 -->
                @if ($listedItems->isEmpty())
                <p>出品した商品はありません。</p>
                @else
                <ul class="item-list">
                    @foreach ($listedItems as $item)
                    <li class="item-card">
                        <a href="{{ route('items.show', $item->id) }}">
                            <div class="item-image-wrapper">
                                @if (Str::startsWith($item->item_image, ['http://', 'https://']))
                                <!-- 🔹 S3などの外部URLの場合は、そのまま表示 -->
                                <img src="{{ $item->item_image }}" alt="{{ $item->name }}">
                                @else
                                <!-- 🔹 ローカルストレージの場合 -->
                                <img src="{{ Storage::url($item->item_image) }}" alt="{{ $item->name }}">
                                @endif

                                @if ($item->sold_out)
                                <span class="sold-label">sold</span>
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
            <div class="tab-content" id="content2">
                @if ($purchasedItems->isEmpty())
                <p>購入した商品はありません。</p>
                @else
                <ul class="item-list">
                    @foreach ($purchasedItems as $item)
                    <li class="item-card">
                        <a href="{{ route('items.show', $item->id) }}">
                            <div class="item-image-wrapper">
                                @if (Str::startsWith($item->item_image, ['http://', 'https://']))
                                <!-- 🔹 S3などの外部URLの場合は、そのまま表示 -->
                                <img src="{{ $item->item_image }}" alt="{{ $item->name }}">
                                @else
                                <!-- 🔹 ローカルストレージの場合 -->
                                <img src="{{ Storage::url($item->item_image) }}" alt="{{ $item->name }}">
                                @endif

                                @if ($item->sold_out)
                                <span class="sold-label">sold</span>
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