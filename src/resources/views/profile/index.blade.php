@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage/purchases.css') }}">
@endsection

@section('title', 'マイページ')

@section('content')

<div class="profile">
    <!-- プロフィール情報 -->
    <div class="profile__header">
        <!-- プロフィール画像 -->
        <div class="profile__image">
            @php $img = $user->profile_image; @endphp

            @if ($img)
            @if (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://']))
            <img src="{{ $img }}" alt="プロフィール画像">
            @else
            <img src="{{ Storage::url($img) }}" alt="プロフィール画像">
            @endif
            @else
            <img src="{{ asset('images/default-avatar.png') }}" alt="デフォルトプロフィール画像">
            @endif
        </div>

        <!-- ユーザー情報 -->
        <div class="profile__info">
            <h2>{{ $user->username }}</h2>

            @php
            $score = $user->average_rating ?? 0;
            @endphp

            <div class="stars" role="img" aria-label="評価 {{ $score }} / 5">
                @for ($i = 1; $i <= 5; $i++)
                    <img
                    src="{{ asset('images/ratings/' . ($i <= $score ? 'Star1.png' : 'Star4.png')) }}"
                    alt=""
                    aria-hidden="true">
                    @endfor
            </div>
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

        <input type="radio" id="tab-inprogress" name="tab" class="profile__tab-input">
        <label for="tab-inprogress" class="profile__tab-label">
            取引中の商品
            @isset($inProgressUnreadTotal)
            @if($inProgressUnreadTotal > 0)
            <span class="badge" aria-label="取引中の未読合計">{{ $inProgressUnreadTotal }}</span>
            @endif
            @endisset
        </label>

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
                    @foreach ($purchasedItems as $purchase)
                    @php $item = $purchase->item; @endphp
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

            <!-- 取引中の商品 -->
            <div class="profile__tab-panel" id="tab-inprogress-content">
                @if ($purchases->isEmpty())
                <p class="empty" role="status" aria-live="polite">
                    参加中の取引はまだありません。商品ページから取引を開始してみましょう。
                </p>
                @else
                <ul class="txn-cards" role="list" aria-label="取引一覧">
                    @foreach ($purchases as $p)
                    @if (!$p->ratingBy($me))
                    @php
                    $unread = (int)($p->unread_count ?? 0);
                    $img = $p->item->item_image ?? '';
                    $isRemote = \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']);
                    $imgUrl = $img
                    ? ($isRemote ? $img : \Illuminate\Support\Facades\Storage::url($img))
                    : asset('images/placeholder.png'); // 画像なし用のフォールバック
                    @endphp

                    <li class="txn-card" role="listitem">
                        <a
                            href="{{ url('/purchases/' . $p->id . '/chat') }}"
                            class="purchase-card"
                            aria-label="『{{ $p->item->name }}』の取引{{ $unread > 0 ? '、未読'.$unread.'件' : '' }}">
                            <div class="txn-thumb">
                                <img src="{{ $imgUrl }}" alt="{{ $p->item->name }}">
                                @if ($unread > 0)
                                <span class="unread-badge" aria-label="未読{{ $unread }}件">
                                    {{ $unread > 99 ? '99+' : $unread }}
                                </span>
                                @endif
                            </div>
                            <h3 class="txn-item-name">{{ $p->item->name }}</h3>
                        </a>
                    </li>
                    @endif
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection