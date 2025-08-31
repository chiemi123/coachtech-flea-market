<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech</title>
    <!-- Alpine.js の読み込み（CDN版） -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <!-- ロゴ画像 -->
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" class="logo">
                </a>

                <!-- 検索フォーム（商品一覧画面、商品詳細画面、マイリスト画面のみ表示） -->
                @if (
                request()->routeIs('items.index') ||
                request()->routeIs('items.show') ||
                request()->routeIs('items.mylist') ||
                request()->routeIs('profile.index')
                )
                <form action="{{ request()->routeIs('items.mylist') ? route('items.mylist') : route('items.index') }}"
                    method="GET" class="search-form"
                    x-data="{ search: '{{ request('search') }}', timeout: null }">

                    <input type="text" name="search" id="searchInput"
                        placeholder="なにをお探しですか？"
                        x-model="search"
                        @input.debounce.500ms="clearTimeout(timeout); timeout = setTimeout(() => $event.target.form.submit(), 500)">
                </form>
                @endif

                <!-- 右側: ナビゲーション -->
                @if (!request()->routeIs('verification.notice'))
                <nav class="header-nav">
                    {{-- チャット画面で購入者のみ「取引を完了する」ボタン表示 --}}
                    @yield('header-extra')

                    @auth
                    @if (empty($isChatView))
                    <form class="form" action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="header-nav__button">ログアウト</button>
                    </form>
                    <a href="{{ route('profile.index') }}">マイページ</a>
                    <a href="{{ route('sell.create') }}" class="sell-button">出品</a>
                    @endif
                    @else
                    @if (!request()->routeIs('login') && !request()->routeIs('register'))
                    <a href="{{ route('login') }}">ログイン</a>
                    <a href="{{ route('profile.index') }}">マイページ</a>
                    <a href="{{ route('sell.create') }}" class="sell-button">出品</a>
                    @endif
                    @endauth
                </nav>
                @endif
            </div>
        </div>
    </header>

    <main>
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if (
        !request()->routeIs('register') &&
        !request()->routeIs('login') &&
        !request()->routeIs('purchases.chat') &&
        !request()->routeIs('messages.edit'))
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @endif
        @yield('content')
    </main>

    @yield('scripts')

</body>

</html>