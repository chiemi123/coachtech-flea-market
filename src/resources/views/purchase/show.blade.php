@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/show.css') }}">
@endsection

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('title', '商品購入')

@section('content')
<div class="purchase-container">
    <!-- 左側の購入情報 -->
    <div class="purchase-info">
        <!-- 商品情報 -->
        <div class="item-box">
            <img src="{{ asset($item->item_image) }}" alt="{{ $item->name }}">
            <div class="item-info">
                <p class="item-name">{{ $item->name }}</p>
                <p class="item-price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <!-- 支払い方法 -->
        <div class="payment-method">
            <label for="payment">支払い方法</label>
            <select id="payment">
                <option value="">選択してください</option>
                <option value="credit">クレジットカード</option>
                <option value="convenience">コンビニ払い</option>
            </select>
        </div>

        <!-- 配送先 -->
        <div class="shipping-info">
            <div class="shipping-address">
                <p>〒 XXX-YYYY</p>
                <p>ここには住所詳細が入ります</p>
            </div>
            <a href="#" class="shipping-edit">変更する</a>
        </div>
    </div>

    <!-- 右側の購入概要 -->
    <div class="purchase-summary">
        <p class="total-price">商品代金　¥{{ number_format($item->price) }}</p>
        <p class="total-payment">支払い方法　コンビニ払い</p>
        <a href="#" class="purchase-button">購入する</a>
    </div>
</div>
@endsection