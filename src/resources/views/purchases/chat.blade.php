@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchases/chat.css') }}">
@endsection

{{-- ヘッダー右側のアクション（購入者だけ表示したい想定） --}}
@section('header-extra')

@if (!$isSeller && $purchase->status === 'paid')
<form method="POST" action="{{ route('purchases.complete', $purchase) }}" style="display:inline;">
    @csrf
    <button type="submit" class="finish-btn">取引を完了する</button>
</form>
@endif
@endsection

@section('title', '取引チャット')

@section('content')
<div class="chat-wrap with-sidebar" data-purchase-id="{{ $purchase->id }}">
    <aside class="sidebar" aria-label="その他の取引">
        <div class="title">その他の取引</div>
        <ul class="sidebar-list">
            @forelse($otherPurchases as $p)
            <li class="sidebar-item {{ $p->id === $purchase->id ? 'is-active' : '' }}">
                <a href="{{ route('purchases.chat', $p->id) }}">
                    <span class="sidebar-title">{{ $p->item->name }}</span>
                </a>
            </li>
            @empty
            <li class="sidebar-item disabled">他の取引はありません</li>
            @endforelse
        </ul>
    </aside>

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

            <div class="msg-row {{ $mine ? 'is-me' : 'is-other' }} {{ isset($editing) && $editing->id === $m->id ? 'edit-form-wrapper' : '' }}">
                <div class="msg-head">
                    <img class="avatar" src="{{ $avatar }}" alt="{{ $uname }}">
                    <span class="name">{{ $uname }}</span>
                </div>

                <div class="msg-content-wrapper">
                    <div class="msg-bubble">
                        @if($time)
                        <div class="msg-meta"><span class="time">{{ $time }}</span></div>
                        @endif
                        {{-- ✏️ 編集モードかどうか判定 --}}
                        @if(isset($editing) && $editing->id === $m->id)
                        <form action="{{ route('messages.update', $m) }}" method="POST" enctype="multipart/form-data" class="edit-form">
                            @csrf
                            @method('PUT')

                            <textarea name="body" rows="3" class="edit-textarea">{{ old('body', $m->body) }}</textarea>

                            <div class="edit-image-input">
                                <input type="file" name="image">
                            </div>
                    </div>
                    <div class="edit-form-actions">
                        <button type="submit" class="edit-submit">更新</button>
                        <a href="{{ route('purchases.chat', ['purchase' => $m->purchase_id]) }}" class="edit-cancel">キャンセル</a>
                    </div>
                    </form>
                    @else

                    <div class="msg-body">{{ $m->body }}</div>

                    @if(!empty($m->image_path))
                    <div class="msg-image">
                        <img src="{{ Storage::url($m->image_path) }}" alt="添付画像">
                    </div>
                    @endif
                </div>

                {{-- 編集・削除ボタン（自分の投稿のみ） --}}
                @if(Gate::check('update', $m) || Gate::check('delete', $m))
                <div class="msg-actions">
                    @can('update', $m)
                    <a href="{{ route('messages.edit', $m) }}">編集</a>
                    @endcan

                    @can('delete', $m)
                    <form action="{{ route('messages.destroy', $m) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">削除</button>
                    </form>
                    @endcan
                </div>
                @endif
                @endif
            </div>
</div>
@empty
<p>最初のメッセージを送ってみましょう。</p>
@endforelse
</section>

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
            aria-describedby="@error('body') body-error @enderror"></textarea>
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
</main>
</div>
@endsection

@if((session('show_rating_modal') || request('show_rating_modal')) &&
$purchase->status === 'completed' &&
!$purchase->ratingBy($me))

{{-- 評価モーダル --}}
<div class="modal is-open" id="complete-modal" aria-hidden="false">
    <div class="modal__backdrop"></div>
    <div class="modal__panel" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <h3 id="modal-title" class="modal-title">取引が完了しました。</h3>
        <p class="modal__sub">
            @if ($purchase->user_id === $me->id)
            今回の取引はいかがでしたか？
            @elseif ($purchase->item->user_id === $me->id)
            今回の取引相手はどうでしたか？
            @endif
        </p>

        <form method="post" action="{{ route('ratings.store', $purchase) }}">
            @csrf
            <input type="hidden" name="ratee_id" value="{{ $partner->id }}">

            <fieldset class="stars-fieldset">
                @for ($i = 5; $i >= 1; $i--)
                <input type="radio" name="score" id="star{{ $i }}" value="{{ $i }}" required>
                <label for="star{{ $i }}" data-value="{{ $i }}">
                    <img src="/images/ratings/Star9.png" alt="{{ $i }}点" class="star-img">
                </label>
                @endfor
            </fieldset>

            <div class="modal__actions">
                <button type="submit" class="btn-primary" aria-label="評価を送信して取引を完了">
                    送信する
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    document.getElementById('chat-form')?.addEventListener('submit', e => {
        const btn = e.target.querySelector('.send-btn');
        btn?.setAttribute('disabled', 'disabled');

        // 送信時に入力内容をlocalStorageから削除
        const textarea = document.querySelector('textarea[name="body"]');
        if (textarea) {
            localStorage.removeItem('chat_draft_{{ $purchase->id }}');
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        const stars = document.querySelectorAll(".stars-fieldset label");
        const grayStar = "/images/ratings/Star9.png";
        const yellowStar = "/images/ratings/Star6.png";

        function updateStars(selectedValue) {
            stars.forEach((label) => {
                const value = parseInt(label.dataset.value);
                const img = label.querySelector("img");
                img.src = (value <= selectedValue) ? yellowStar : grayStar;
            });
        }

        stars.forEach((label) => {
            const input = document.getElementById(label.getAttribute("for"));
            label.setAttribute("data-value", input.value);

            label.addEventListener("mouseenter", () => {
                updateStars(label.dataset.value);
            });

            label.addEventListener("mouseleave", () => {
                const checked = document.querySelector(".stars-fieldset input:checked");
                updateStars(checked ? checked.value : 0);
            });

            input.addEventListener("change", () => {
                updateStars(input.value);
            });
        });

        const checked = document.querySelector(".stars-fieldset input:checked");
        updateStars(checked ? checked.value : 0);

        const textarea = document.querySelector('textarea[name="body"]');
        const draftKey = 'chat_draft_{{ $purchase->id }}';

        if (textarea) {
            // 入力があるたびに保存
            textarea.addEventListener('input', () => {
                localStorage.setItem(draftKey, textarea.value);
            });

            // 読み込み時にdraftがあれば復元（old() より優先）
            const saved = localStorage.getItem(draftKey);
            if (saved && !textarea.value) {
                textarea.value = saved;
            }
        }
    });

    // 戻る操作などでキャッシュからページを復元したときに、送信ボタンを有効にする
    window.addEventListener('pageshow', function(event) {
        const btn = document.querySelector('.btn-primary');
        if (btn) btn.disabled = false;
    });
</script>