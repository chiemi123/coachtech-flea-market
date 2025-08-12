@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/purchases.css') }}">
@endsection

@section('title', 'マイページ｜取引中')

@section('content')
<div class="mypage">
    <h2 class="section-title">取引中の商品</h2>

    @if ($purchases->isEmpty())
    <p class="empty" role="status" aria-live="polite">
        参加中の取引はまだありません。商品ページから取引を開始してみましょう。
    </p>
    @else
    <ul class="txn-cards" role="list" aria-label="取引一覧">
        @foreach ($purchases as $p)
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
                <div class="thumb">
                    <img src="{{ $imgUrl }}" alt="{{ $p->item->name }}">
                    @if ($unread > 0)
                    <span class="unread-dot" aria-hidden="true"></span>
                    @endif
                </div>

                <div class="meta">
                    <div class="title" title="{{ $p->item->name }}">{{ $p->item->name }}</div>
                    <div class="price">¥{{ number_format((int)$p->item->price) }}</div>
                    <div class="time">
                        {{ optional($p->last_message_at)->diffForHumans() ?? 'メッセージなし' }}
                    </div>
                </div>

                @if ($unread > 0)
                <span class="unread-badge" aria-label="未読{{ $unread }}件">
                    {{ $unread > 99 ? '99+' : $unread }}
                </span>
                @endif
            </a>
        </li>
        @endforeach
    </ul>
    @endif
</div>
@endsection