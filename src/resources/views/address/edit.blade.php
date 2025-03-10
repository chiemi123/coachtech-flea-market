@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address/edit.css') }}">
@endsection

@section('header-extra')
@endsection

@section('title', '住所の変更')

@section('content')
<div class="address__container">
    <h1 class="address__title">住所の変更</h1>
    <form action="{{ route('address.update', ['item_id' => $item_id]) }}" method="POST">
        @csrf

        <div class="address__form-group">
            <label for="postal_code" class="address__label">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="address__input"
                value="{{ old('postal_code', $address->postal_code ?? '') }}" required>
            @error('postal_code')
            <p class="address__error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="address__form-group">
            <label for="address" class="address__label">住所</label>
            <input type="text" name="address" id="address" class="address__input"
                value="{{ old('address', $address->address ?? '') }}" required>
            @error('address')
            <p class="address__error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="address__form-group">
            <label for="building_name" class="address__label">建物名</label>
            <input type="text" name="building_name" id="building_name" class="address__input"
                value="{{ old('building_name', $address->building_name ?? '') }}">
            @error('building_name')
            <p class="address__error-message">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="address__submit-button">更新する</button>
    </form>
</div>
@endsection