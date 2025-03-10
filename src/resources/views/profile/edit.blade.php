@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection

@section('header-extra')

@endsection

@section('content')
<div class="profile__edit">
    <h1 class="profile__edit-title">プロフィール設定</h1>

    <form class="profile__edit-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- プロフィール画像のグループ -->
        <div class="profile__edit-image-container">
            <div class="profile__edit-image-wrapper">
                @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="profile__edit-image">
                @else
                <img src="{{ asset('images/default-avatar.png') }}" alt="デフォルトプロフィール画像" class="profile__edit-image">
                @endif
            </div>
            <label for="profile_image" class="profile__edit-button profile__edit-file-button">画像を選択する</label>
            <input type="file" name="profile_image" id="profile_image" class="profile__edit-input profile__edit-input--file">
        </div>

        <!-- ユーザー名 -->
        <div class="profile__edit-group">
            <label for="username" class="profile__edit-label">ユーザー名</label>
            <input type="text" name="username" id="username" value="{{ old('username', $user->name ?? '') }}" class="profile__edit-input" required>
        </div>

        <!-- 郵便番号 -->
        <div class="profile__edit-group">
            <label for="postal_code" class="profile__edit-label">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="profile__edit-input" required>
        </div>

        <!-- 住所 -->
        <div class="profile__edit-group">
            <label for="address" class="profile__edit-label">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" class="profile__edit-input" required>
        </div>

        <!-- 建物名 -->
        <div class="profile__edit-group">
            <label for="building_name" class="profile__edit-label">建物名</label>
            <input type="text" name="building_name" id="building_name" value="{{ old('building_name', $user->building_name) }}" class="profile__edit-input">
        </div>

        <!-- 更新ボタン -->
        <button type="submit" class="profile__edit-submit-button">更新する</button>
    </form>
</div>
@endsection