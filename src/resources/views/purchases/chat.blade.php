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
                    @if (Str::startsWith($img, ['http://','https://']))
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
        <section id="chat-timeline" class="chat-timeline" data-after="{{ optional($messages->last())->created_at?->timestamp }}">
            @forelse ($messages as $m)
            <div class="msg {{ $m->user_id === $me->id ? 'is-me' : 'is-other' }}" data-ts="{{ $m->created_at->timestamp }}">
                <div class="msg-meta">
                    <span class="user">{{ $m->user->username }}</span>
                    <span class="time">{{ $m->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <div class="msg-body">{{ $m->body }}</div>
                @if($m->image_path)
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
                <textarea name="body" rows="2" placeholder="取引メッセージを記入してください" maxlength="400"></textarea>
                <div class="input-row">
                    <label for="chat-image">画像を追加</label>
                    <input type="file" id="chat-image" name="image" accept=".png,.jpeg">
                    <button type="submit">送信</button>
                </div>
            </form>
        </section>
    </main>
</div>
@endsection