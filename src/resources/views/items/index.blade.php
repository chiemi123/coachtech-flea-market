@extends('layouts.app')

@section('title', '商品一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/item-index.css') }}">
@endsection

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('content')
<!-- タブ -->
<div class="tabs">
    <div class="tab">おすすめ</div>
    <div class="tab active">マイリスト</div>
</div>

<!-- 商品リスト -->
<div class="item-list">
    @foreach ($items as $item)
    <div class="item-card">
        <img src="{{ asset($item->item_image) }}" alt="{{ $item->name }}">
        <h3>{{ $item->name }}</h3>
        <p>¥{{ number_format($item->price) }}</p>
        <a href="{{ route('items.show', $item->id) }}">詳細を見る</a>
    </div>
    @endforeach
</div>

@endsection