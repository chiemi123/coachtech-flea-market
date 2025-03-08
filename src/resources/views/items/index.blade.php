@extends('layouts.app')

@section('title', '商品一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/item-index.css') }}">
@endsection

@section('header-extra')

@endsection

@section('content')
<!-- タブ -->
<div class="tabs">
    <div class="tab {{ request()->routeIs('items.index') ? 'active' : '' }}">
        <a href="{{ route('items.index', ['search' => request('search')]) }}">おすすめ</a>
    </div>
    <div class="tab {{ request()->routeIs('items.mylist') ? 'active' : '' }}">
        <a href="{{ route('items.mylist', ['search' => request('search')]) }}">マイリスト</a>
    </div>
</div>

<!-- 商品リスト -->
<div class="item-list">
    @if(request()->routeIs('items.index'))
    {{-- ここはログイン不要（おすすめ） --}}
    @forelse ($items as $item)
    <div class="item-card">
        @if ($item->is_sold)
        <span class="sold-label">sold</span>
        @endif
        <a href="{{ route('items.show', $item->id) }}">
            @if (Str::startsWith($item->item_image, ['http://', 'https://']))
            <!-- 🔹 S3などの外部URLの場合は、そのまま表示 -->
            <img src="{{ $item->item_image }}" alt="{{ $item->name }}">
            @else
            <!-- 🔹 ローカルストレージの場合 -->
            <img src="{{ Storage::url($item->item_image) }}" alt="{{ $item->name }}">
            @endif
            <h3>{{ $item->name }}</h3>
        </a>
    </div>
    @empty
    <p class="no-items">現在、おすすめの商品はありません。</p>
    @endforelse
    @elseif(request()->routeIs('items.mylist'))
    {{-- ここはログイン必須（マイリスト） --}}
    @auth
    @forelse ($items as $item)
    <div class="item-card">
        @if ($item->sold_out)
        <span class="sold-label">sold</span>
        @endif
        <a href="{{ route('items.show', $item->id) }}">
            @php
            $isStorageImage = !Str::startsWith($item->item_image, ['http://', 'https://']);
            @endphp
            <img src="{{ $isStorageImage ? asset('storage/' . $item->item_image) : asset($item->item_image) }}" alt="{{ $item->name }}">
            <h3>{{ $item->name }}</h3>
        </a>
    </div>
    @empty
    <p class="no-items">マイリストに登録された商品はありません。</p>
    @endforelse
    @else
    <p class="login-message">マイリストを見るには <a href="{{ route('login') }}">ログイン</a> してください。</p>
    @endauth
    @endif
</div>

@endsection