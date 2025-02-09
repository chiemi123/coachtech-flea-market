@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('content')
<div class="profile-edit-container">
    <h1 class="profile-edit-title">プロフィール設定</h1>

    @if ($errors->any())
    <div class="error-messages">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form class="profile-edit-form" action="/mypage/profile" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- プロフィール画像 -->
        <div class="form-group profile-image-group">
            <!-- プロフィール画像プレビュー -->
            <div class="image-preview">
                @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像">
                @else
                <img src="{{ asset('images/default-avatar.png') }}" alt="デフォルトプロフィール画像">
                @endif
            </div>

            <!-- ファイル選択ボタン -->
            <input type="file" name="profile_image" id="profile_image">
            <label for="profile_image">画像を選択する</label>
        </div>

        <!-- ユーザー名 -->
        <div class="form-group">
            <label for="username" class="form-label">ユーザー名</label>
            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="form-input" required>
        </div>

        <!-- 郵便番号 -->
        <div class="form-group">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="form-input" required>
        </div>

        <!-- 住所 -->
        <div class="form-group">
            <label for="address" class="form-label">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" class="form-input" required>
        </div>

        <!-- 建物名 -->
        <div class="form-group">
            <label for="building_name" class="form-label">建物名</label>
            <input type="text" name="building_name" id="building_name" value="{{ old('building_name', $user->building_name) }}" class="form-input" required>
        </div>

        <!-- 保存ボタン -->
        <button type="submit" class="btn-submit">更新する</button>

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

    </form>
</div>
@endsection