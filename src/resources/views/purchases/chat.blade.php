@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchases/chat.css') }}">
@endsection

{{-- ヘッダー右側のアクション（購入者だけ表示したい想定） --}}
@section('header-extra')
@if(isset($isSeller) && !$isSeller)
<button type="button" class="finish-btn" id="open-complete-modal">取引を完了する</button>
@endif
@endsection

@section('title', '取引チャット')

@section('content')
<div class="chat-wrap" data-purchase-id="{{ $purchase->id }}">
    {{-- 購入者でも表示：空でなければ出す --}}
    @if($otherPurchases->isNotEmpty())
    <aside class="sidebar" aria-label="その他の取引">
        <div class="title">その他の取引</div>
        <ul class="sidebar-list">
            @foreach($otherPurchases as $p)
            <li class="sidebar-item {{ $p->id === $purchase->id ? 'is-active' : '' }}">
                <a href="{{ route('purchases.chat', $p->id) }}">
                    <span class="sidebar-title">{{ $p->item->name }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </aside>
    @endif

    {{-- 会話パネル --}}
    <main class="chat-main">
        <header class="chat-header">
            {{-- 1行目：購入者プロフィール --}}
            <div class="chat-profile">
                <div class="profile-thumb">
                    <img src="{{ Storage::url($partner->profile_image ?? 'images/default-user.png') }}" alt="{{ $partner->username }}">
                </div>
                <div class="profile-name">「{{ $partner->username }}」 さんとの取引画面</div>
            </div>
            {{-- 2行目：商品情報 --}}
            <div class="chat-item-info">
                <div class="item-thumb">
                    @php $img = $item->item_image; @endphp
                    @if ($img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']))
                    <img src="{{ $img }}" alt="{{ $item->name }}">
                    @else
                    <img src="{{ Storage::url($img) }}" alt="{{ $item->name }}">
                    @endif
                </div>
                <div class="item-detail">
                    <div class="item-name">{{ $item->name }}</div>
                    <div class="item-price">¥{{ number_format($item->price) }}</div>
                </div>
            </div>
        </header>

        @php
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Storage;

        $last = $messages instanceof \Illuminate\Support\Collection ? $messages->last() : null;
        $afterTs = ($last && $last->created_at) ? $last->created_at->timestamp : 0;
        @endphp

        <section id="chat-timeline" class="chat-timeline" data-after="{{ $afterTs }}">
            @forelse ($messages as $m)
            @php
            $mine = isset($me) && $m->user_id === $me->id;

            $u = $m->relationLoaded('user') ? $m->user : ($m->user ?? null);
            $uname = $u ? ($u->username ?? 'ユーザー') : 'ユーザー';
            $time = $m->created_at ? $m->created_at->format('Y/m/d H:i') : '';

            // アバター
            $avatar = null;
            if ($u && !empty($u->profile_image)) {
            $avatar = Str::startsWith($u->profile_image, ['http://','https://'])
            ? $u->profile_image
            : Storage::url($u->profile_image);
            }
            $avatar = $avatar ?: asset('images/default-avatar.png');
            @endphp

            <div class="msg-row {{ $mine ? 'is-me' : 'is-other' }}">
                <div class="msg-head">
                    <img class="avatar" src="{{ $avatar }}" alt="{{ $uname }}">
                    <span class="name">{{ $uname }}</span>
                </div>

                <div class="msg-bubble">
                    @if($time)
                    <div class="msg-meta"><span class="time">{{ $time }}</span></div>
                    @endif
                    <div class="msg-body">{{ $m->body }}</div>

                    @if(!empty($m->image_path))
                    <div class="msg-image">
                        <img src="{{ Storage::url($m->image_path) }}" alt="添付画像">
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <p>最初のメッセージを送ってみましょう。</p>
            @endforelse
        </section>



        {{-- 入力フォーム（既存：クラスだけ合わせる） --}}
        <section class="chat-footer">
            <form class=chat-form id="chat-form" action="{{ url('/purchases/' . $purchase->id . '/messages') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <textarea class="chat-textarea" name="body" placeholder="取引メッセージを記入してください" maxlength="400" aria-label="メッセージ入力"></textarea>
                <div class="input-row">
                    <label for="chat-image">画像を追加</label>
                    <input type="file" id="chat-image" name="image" accept=".png,.jpg">
                    <button class="send-btn" type="submit" aria-label="送信">
                        <img src="{{ asset('images/icons/send.jpg') }}" alt="">
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>
@endsection