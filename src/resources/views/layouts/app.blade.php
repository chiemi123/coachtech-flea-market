<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    @yield('css')
</head>

<body>
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <!-- ロゴ画像 -->
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" class="logo">
                </a>

                <!-- 中央: 検索フォーム（商品一覧画面と商品詳細画面のみ表示） -->
                @if (request()->routeIs('items.index') || request()->routeIs('items.show') || request()->routeIs('items.mylist'))
                <form action="{{ request()->routeIs('items.mylist') ? route('items.mylist') : route('items.index') }}" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="なにをお探しですか？" value="{{ request('search') }}" oninput="this.form.submit()">
                </form>
                @endif

                <!-- 右側: ナビゲーション -->
                <nav class="header-nav">
                    @auth
                    <form class="form" action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="header-nav__button">ログアウト</button>
                    </form>
                    <a href="{{ route('profile.index') }}">マイページ</a>
                    <a href="{{ route('sell.create') }}" class="sell-button">出品</a>
                    @yield('header-extra') <!-- 他のページで追加メニューを入れられる -->
                    @else
                    <!-- ログインページと会員登録ページでは非表示 -->
                    @if (!request()->routeIs('login') && !request()->routeIs('register'))
                    <a href="{{ route('login') }}">ログイン</a>
                    <a href="{{ route('profile.index') }}">マイページ</a>
                    <a href="{{ route('sell.create') }}" class="sell-button">出品</a>
                    @endif
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>