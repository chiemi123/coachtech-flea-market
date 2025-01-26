@extends('layouts.app')

@section('content')
<div class="container">
    <h1>プロフィール設定</h1>

    @if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form class="form" action="/mypage/profile" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="profile_image">プロフィール画像</label>
            <input type="file" name="profile_image">
        </div>

        <div>
            <label for="username">ユーザー名</label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}" required>
        </div>

        <div>
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" required>
        </div>

        <div>
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}" required>
        </div>

        <div>
            <label for="building_name">建物名</label>
            <input type="text" name="building_name" value="{{ old('building_name', $user->building_name) }}" required>
        </div>

        <button type="submit">保存</button>
    </form>
</div>
@endsection