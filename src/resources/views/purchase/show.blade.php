@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/show.css') }}">
@endsection

@section('header-extra')

@endsection

@section('title', '商品購入')

@section('content')


<div class="purchase-container">
    <form action="{{ route('purchase.confirm', $item->id) }}" method="POST">
        @csrf
        <!-- 左側の購入情報 -->
        <div class="purchase-info">
            <!-- 商品情報 -->
            <div class="item-box">
                @if (Str::startsWith($item->item_image, 'http'))
                <!-- 画像がURLの場合（外部URLやアイテムテーブルに保存されている画像） -->
                <img src="{{ asset($item->item_image) }}" alt="{{ $item->name }}">
                @else
                <!-- 画像がストレージ内に保存されている場合 -->
                <img src="{{ Storage::url($item->item_image) }}" alt="{{ $item->name }}">
                @endif
                <div class="item-info">
                    <p class="item-name">{{ $item->name }}</p>
                    <p class="item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <!-- 支払い方法 -->
            <div class="payment-method">
                <label class="payment-label">支払い方法</label>
                <div class="payment-options">

                    <!-- トグル用チェックボックス（ドロップダウンの開閉に利用） -->
                    <input type="checkbox" id="toggle-dropdown" class="toggle-checkbox">
                    <label class="dropdown-label" for="toggle-dropdown">
                        選択してください
                    </label>

                    <!-- 支払い方法の選択肢 -->
                    <div class="payment-options-dropdown">
                        <div class="option">
                            <input type="radio" id="convenience" name="payment_method" value="コンビニ払い"
                                {{ session('payment_method') === 'コンビニ払い' ? 'checked' : '' }}
                                onchange="submitWithDelay(this.form, 300)">
                            <label for="convenience">コンビニ払い</label>
                        </div>
                        <div class="option">
                            <input type="radio" id="credit_card" name="payment_method" value="クレジットカード"
                                {{ session('payment_method') === 'クレジットカード' ? 'checked' : '' }}
                                onchange="submitWithDelay(this.form, 300)">
                            <label for="credit_card">クレジットカード</label>
                        </div>

                        <script>
                            function submitWithDelay(form, delay) {
                                setTimeout(function() {
                                    form.submit();
                                }, delay);
                            }
                        </script>

                    </div>
                </div>

                <!-- 隠しフィールドで住所IDを渡す -->
                <input type="hidden" name="address_id" value="{{ session('address_id', 'user_table') }}">
            </div>

            <!-- 配送先 -->
            <div class="shipping-info">
                <label class="shipping-label">配送先</label>

                <div class="shipping-address">
                    @auth
                    <p>〒 {{ $address->postal_code ?? 'XXX-YYYY' }}</p>
                    <p>{{ $address->address ?? 'ここには住所が入ります' }}</p>
                    <p>{{ $address->building_name ?? '' }}</p>
                    @else
                    <p>ログインしてください</p>
                    @endauth
                </div>
                <a href="{{ route('address.edit', ['item_id' => $item->id]) }}" class="shipping-edit">変更する</a>
            </div>
        </div>

    </form>


    <!-- 右側の購入概要（表のようなデザイン） -->
    <form action="{{ route('purchase.checkout', $item->id) }}" method="POST">
        @csrf
        <div class="purchase-summary">
            <table class="purchase-table">
                <tr>
                    <th>商品代金</th>
                    <td>¥{{ number_format($item->price) }}</td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td>{{ session('payment_method', '') }}</td> <!-- 選択した支払い方法を表示 -->
                </tr>
            </table>
            <input type="hidden" name="address_id" value="{{ session('address_id', 'user_table') }}">
            <input type="hidden" name="payment_method" value="{{ session('payment_method', '') }}">
            <button type="submit" class="purchase-button">購入する</button>
        </div>
    </form>
</div>
@endsection