@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchases/chat.css') }}">
@endsection

@section('title', '取引チャット')

@section('content')
<div class="chat-layout" data-purchase-id="{{ $purchase->id }}">
    {{-- 会話パネル --}}
    <main class="chat-main">
        {{-- ヘッダー --}}
        <header class="chat-header">
            <div class="item-hero">
                <div class="item-thumb">
                    @php $img = $item->item_image; @endphp
                    @if ($img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']))
                    <img src="{{ $img }}" alt="{{ $item->name }}">
                    @else
                    <img src="{{ Storage::url($img) }}" alt="{{ $item->name }}">
                    @endif
                </div>
                <div class="item-info">
                    <div class="partner">「{{ $partner->username }}」さんとの取引</div>
                    <div class="name">{{ $item->name }}</div>
                    <div class="price">¥{{ number_format($item->price) }}</div>
                </div>
            </div>
        </header>

        {{-- メッセージタイムライン --}}
        @php
        $last = $messages->last();
        $afterTs = ($last && $last->created_at) ? $last->created_at->timestamp : 0;
        @endphp
        <section id="chat-timeline" class="chat-timeline" data-after="{{ $afterTs }}">
            @forelse ($messages as $m)
            @php
            $ts = $m->created_at ? $m->created_at->timestamp : 0;
            $time = $m->created_at ? $m->created_at->format('Y/m/d H:i') : '';
            @endphp
            <div class="msg {{ $m->user_id === $me->id ? 'is-me' : 'is-other' }}" data-ts="{{ $ts }}">
                <div class="msg-meta">
                    <span class="user">{{ $m->user->username }}</span>
                    <span class="time">{{ $time }}</span>
                </div>
                <div class="msg-body">{{ $m->body }}</div>
                @if (!empty($m->image_path))
                <div class="msg-image">
                    <img src="{{ Storage::url($m->image_path) }}" alt="添付画像">
                </div>
                @endif
            </div>
            @empty
            <p>最初のメッセージを送ってみましょう。</p>
            @endforelse
        </section>

        {{-- 入力フォーム --}}
        <section class="chat-input">
            <form id="chat-form" action="{{ url('/purchases/' . $purchase->id . '/messages') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <textarea class="chat-input" name="body" placeholder="取引メッセージを記入してください" maxlength="400" aria-label="メッセージ入力"></textarea>

                <div class="input-row">
                    <label for="chat-image">画像を追加</label>
                    <input type="file" id="chat-image" name="image" accept=".png,.jpg">
                    {{-- 送信ボタン（JPGアイコン） --}}
                    <button class="send-btn" type="submit" aria-label="送信">
                        <img src="{{ asset('images/icons/send.jpg') }}" alt="送信">
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>
@endsection