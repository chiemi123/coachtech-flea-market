@extends('layouts.app') <!-- 共通レイアウトを継承 -->

@section('content')
<div class="container">
    <h1 class="text-center">プロフィール画面</h1>

    <div class="profile-header d-flex align-items-center justify-content-between">
        <!-- プロフィール画像 -->
        <div class="profile-image">
            @if ($user->profile_image)
            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
            @else
            <img src="{{ asset('images/default-avatar.png') }}" alt="デフォルトプロフィール画像" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
            @endif
        </div>

        <!-- ユーザー名 -->
        <div class="profile-info">
            <h2>{{ $user->username }}</h2>
        </div>

        <!-- プロフィール編集ボタン -->
        <div>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-danger">プロフィールを編集</a>
        </div>
    </div>

    <!-- タブメニュー-->
    <ul class="nav nav-tabs mt-4">
        <li class="nav-item">
            <a class="nav-link active text-danger" href="#listed-items" data-bs-toggle="tab">出品した商品</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#purchased-items" data-bs-toggle="tab">購入した商品</a>
        </li>
    </ul>


</div>

@endsection