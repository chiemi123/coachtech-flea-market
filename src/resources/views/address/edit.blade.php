@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address/edit.css') }}">
@endsection

@section('header-extra')
@endsection

@section('title', '住所の変更')

@section('content')
<div class="address-container">
    <h1 class="address-title">住所の変更</h1>
    <form action="{{ route('address.update', ['item_id' => $item_id]) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}">
            @error('postal_code')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $address->address ?? '') }}">
            @error('address')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" name="building_name" id="building_name" value="{{ old('building_name', $address->building_name ?? '') }}">
            @error('building_name')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="submit-button">更新する</button>
    </form>
</div>
@endsection