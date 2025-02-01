@extends('layouts.app')

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('title', '住所の変更')

@section('content')
<div style="max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
    <h1 style="text-align: center;">住所の変更</h1>
    <form action="{{ route('address.update', ['item_id' => $item_id]) }}" method="POST">
        @csrf
        <div style="margin-bottom: 20px;">
            <label for="postal_code" style="display: block; font-weight: bold; margin-bottom: 5px;">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address['postal_code']) }}"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            @error('postal_code')
            <p style="color: red; font-size: 12px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="address" style="display: block; font-weight: bold; margin-bottom: 5px;">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $address['address']) }}"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            @error('address')
            <p style="color: red; font-size: 12px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="phone" style="display: block; font-weight: bold; margin-bottom: 5px;">電話番号</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $address['phone']) }}"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            @error('phone')
            <p style="color: red; font-size: 12px;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; width: 100%; cursor: pointer;">
            更新する
        </button>
    </form>
</div>
@endsection