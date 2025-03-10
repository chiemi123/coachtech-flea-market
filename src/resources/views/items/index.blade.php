@extends('layouts.app')

@section('title', '商品一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/item-index.css') }}">
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
<div class="items">
    @if(request()->routeIs('items.index'))
        <div class="items__list">
            @forelse ($items as $item)
            <div class="items__card">
                @if ($item->is_sold)
                <span class="items__card-sold">sold</span>
                @endif
                <a href="{{ route('items.show', $item->id) }}">
                    @if (Str::startsWith($item->item_image, ['http://', 'https://']))
                    <img src="{{ $item->item_image }}" alt="{{ $item->name }}">
                    @else
                    <img src="{{ Storage::url($item->item_image) }}" alt="{{ $item->name }}">
                    @endif
                    <h3 class="items__card-name">{{ $item->name }}</h3>
                </a>
            </div>
            @empty
            <p class="items__no-items">現在、おすすめの商品はありません。</p>
            @endforelse
        </div>

    @elseif(request()->routeIs('items.mylist'))
        @auth
            @if ($items->isEmpty())
            <p class="mylist__no-items">マイリストに登録された商品はありません。</p>
            @else
            <div class="mylist__list">
                @foreach ($items as $item)
                <div class="mylist__card">
                    @if ($item->is_sold)
                    <span class="mylist__card-sold">sold</span>
                    @endif
                    <a href="{{ route('items.show', $item->id) }}">
                        @php
                        $isStorageImage = !Str::startsWith($item->item_image, ['http://', 'https://']);
                        @endphp
                        <img src="{{ $isStorageImage ? asset('storage/' . $item->item_image) : asset($item->item_image) }}" alt="{{ $item->name }}">
                        <h3 class="mylist__card-name">{{ $item->name }}</h3>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        @else
        <p class="mylist__login-message">マイリストを見るには <a href="{{ route('login') }}">ログイン</a> してください。</p>
        @endauth
    @endif
</div>
@endsection
