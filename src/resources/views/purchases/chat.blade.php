@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchases/chat.css') }}">
@endsection

{{-- ヘッダー右側のアクション（購入者だけ表示したい想定） --}}
@section('header-extra')
@if(isset($isSeller) && !$isSeller && $purchase->status !== 'completed')
<form method="POST" action="{{ route('purchases.complete', $purchase) }}" style="display:inline;">
    @csrf
    <button type="submit" class="finish-btn">取引を完了する</button>
</form>
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



        {{-- 送信ステータス & 上部に全体エラー（任意） --}}
        @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            入力内容に誤りがあります。各項目をご確認ください。
        </div>
        @endif

        {{-- 入力フォーム --}}
        <section class="chat-footer">
            <form class="chat-form" id="chat-form"
                action="{{ url('/purchases/' . $purchase->id . '/messages') }}"
                method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- 本文 --}}
                <textarea
                    class="chat-textarea @error('body') is-invalid @enderror"
                    name="body"
                    placeholder="取引メッセージを記入してください"
                    maxlength="400"
                    aria-label="メッセージ入力"
                    aria-invalid="@error('body') true @else false @enderror"
                    aria-describedby="@error('body') body-error @enderror">{{ old('body') }}</textarea>
                @error('body')
                <p id="body-error" class="form-error" role="alert">{{ $message }}</p>
                @enderror

                <div class="input-row">
                    <label for="chat-image">画像を追加</label>
                    <input
                        type="file"
                        id="chat-image"
                        name="image"
                        accept=".png,.jpeg"
                        class="@error('image') is-invalid @enderror"
                        aria-invalid="@error('image') true @else false @enderror"
                        aria-describedby="@error('image') image-error @enderror">
                    @error('image')
                    <p id="image-error" class="form-error" role="alert">{{ $message }}</p>
                    @enderror

                    <button class="send-btn" type="submit" aria-label="送信">
                        <img src="{{ asset('images/icons/send.jpg') }}" alt="送信">
                    </button>
                </div>
            </form>
        </section>

        @if($purchase->status === 'completed' && !$purchase->ratingBy($me))
        {{-- 評価モーダル --}}
        <div class="modal is-open" id="complete-modal" aria-hidden="false">
            <div class="modal__backdrop"></div>
            <div class="modal__panel" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                <h3 id="modal-title">取引が完了しました。</h3>
                <p class="modal__sub">今回の取引はいかがでしたか？</p>

                <form method="post" action="{{ route('ratings.store', $purchase) }}">
                    @csrf
                    <input type="hidden" name="ratee_id" value="{{ $partner->id }}">

                    <fieldset class="stars-fieldset">
                        <legend class="sr-only">評価</legend>
                        @for ($i = 5; $i >= 1; $i--)
                        <input id="star{{ $i }}" type="radio" name="score" value="{{ $i }}" required>
                        <label for="star{{ $i }}">
                            <img src="{{ asset('images/ratings/star7.jpg') }}" alt="{{ $i }}点">
                        </label>
                        @endfor
                    </fieldset>

                    <div class="modal__actions">
                        <button type="submit" class="btn-primary">評価して完了</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </main>
</div>
@endsection

<script>
    document.getElementById('chat-form')?.addEventListener('submit', e => {
        const btn = e.target.querySelector('.send-btn');
        btn?.setAttribute('disabled', 'disabled');
    });
</script>