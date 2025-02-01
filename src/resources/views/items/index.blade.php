@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
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
<div class="product-list">
    <div class="product-item">
        <img src="" alt="商品画像" class="product-item">
        <p>商品名</p>
    </div>
</div>

@endsection