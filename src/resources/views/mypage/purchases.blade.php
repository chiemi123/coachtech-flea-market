@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/purchases.css') }}">
@endsection

@section('title', 'マイページ｜取引中')

@section('content')
<div class="mypage">
    <h2>取引中の商品</h2>

    @if ($purchases->isEmpty())
    <p>取引中の商品はありません。</p>
    @else
    <ul class="txn-cards">
        @foreach ($purchases as $p)
        <li class="txn-card">
            <a href="{{ url('/purchases/' . $p->id . '/chat') }}">
                <div class="thumb">
                    @php $img = $p->item->item_image; @endphp
                    @if (Str::startsWith($img, ['http://','https://']))
                    <img src="{{ $img }}" alt="{{ $p->item->name }}">
                    @else
                    <img src="{{ Storage::url($img) }}" alt="{{ $p->item->name }}">
                    @endif
                    @if ($p->unread_count > 0)
                    <span class="unread-dot"></span>
                    @endif
                </div>
                <div class="meta">
                    <div class="title">{{ $p->item->name }}</div>
                    <div class="price">¥{{ number_format($p->item->price) }}</div>
                    <div class="time">{{ optional($p->last_message_at)->diffForHumans() }}</div>
                </div>
                @if ($p->unread_count > 0)
                <span class="unread-badge">{{ $p->unread_count }}</span>
                @endif
            </a>
        </li>
        @endforeach
    </ul>
    @endif
</div>
@endsection