@extends('layouts.app')

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('title', '商品購入')

@section('content')
<div style="display: flex; justify-content: space-between; margin-top: 30px;">
    <!-- 左側: 商品情報 -->
    <div style="flex: 2; margin-right: 20px;">
        <!-- 商品情報 -->
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 150px; height: 150px; background-color: #eee; margin-right: 20px;">
            <div>
                <h2>{{ $item['name'] }}</h2>
                <p style="font-size: 24px; color: red;">¥{{ number_format($item['price']) }}</p>
            </div>
        </div>

        <!-- 支払い方法 -->
        <h3>支払い方法</h3>
        <select name="payment_method" style="width: 100%; padding: 10px; margin-bottom: 20px;">
            <option value="" disabled selected>選択してください</option>
            <option value="credit_card">クレジットカード</option>
            <option value="convenience_store">コンビニ払い</option>
        </select>

        <!-- 配送先 -->
        <h3>配送先</h3>
        <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;">
            <p>〒{{ $userAddress['postal_code'] }}</p>
            <p>{{ $userAddress['address'] }}</p>
            <a href="{{ route('address.edit', ['item_id' => $item['id']]) }}" style="color: blue;">変更する</a>
        </div>
    </div>

    <!-- 右側: 購入情報 -->
    <div style="flex: 1; padding: 20px; border: 1px solid #ddd;">
        <h3>購入情報</h3>
        <p>商品代金: <span style="float: right;">¥{{ number_format($item['price']) }}</span></p>
        <p>支払い方法: <span style="float: right;">コンビニ払い</span></p>
        <button style="background-color: red; color: white; padding: 10px 20px; border: none; width: 100%; cursor: pointer; margin-top: 20px;">
            購入する
        </button>
    </div>
</div>
@endsection