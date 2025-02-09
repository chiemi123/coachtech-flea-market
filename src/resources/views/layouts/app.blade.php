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
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <!-- ロゴ画像 -->
                <img src="{{ asset('images/logo.svg') }}" alt="coachtech" class="logo">
                <nav>
                    <ul class="header-nav">
                        @if (Auth::check())
                        <!-- 会員登録画面・ログイン画面を除くページで検索ボックスを表示 -->
                        @if (!in_array(Route::currentRouteName(), ['register', 'login']))
                        <div class="search-box">
                            <form action="" method="GET">
                                <input type="text" name="query" placeholder="なにをお探しですか？">
                            </form>
                        </div>
                        @endif
                        <li class="header-nav__item">
                            <form class="form" action="/logout" method="post">
                                @csrf
                                <button class="header-nav__button">ログアウト</button>
                            </form>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="/mypage">マイページ</a>
                        </li>
                        @yield('header-extra')
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>