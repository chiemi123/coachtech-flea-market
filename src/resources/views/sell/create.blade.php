@extends('layouts.app')

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('title', '商品出品')

@section('content')
<div style="max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
    <h1 style="text-align: center;">商品の出品</h1>
    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像 -->
        <div style="margin-bottom: 20px;">
            <label for="image" style="display: block; font-weight: bold; margin-bottom: 5px;">商品画像</label>
            <input type="file" name="image" id="image" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            @error('image')
            <p style="color: red; font-size: 12px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- カテゴリー -->
        <div style="margin-bottom: 20px;">
            <label for="category" style="display: block; font-weight: bold; margin-bottom: 5px;">カテゴリー</label>
            <div>
                @foreach ($categories as $category)
                <label style="margin-right: 10px;">
                    <input type="radio" name="category" value="{{ $category }}"> {{ $category }}
                </label>
                @endforeach
            </div>
            @error('category')
            <p style="color: red; font-size: 12px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品の状態 -->
        <div style="margin-bottom: 20px;">
            <label for="condition" style="display: block; font-weight: bold; margin-bottom: 5px;">商品の状態</label>
            <select name="condition" id="condition" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="" disabled selected>選択してください</option>
                <option value="新品">新品</option>
                <option value="中古">中古</option>
                <option value="ジャンク">ジャンク</option>
            </select>
            @error('condition')
            <p style="color: red; font-size: 12px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品名と説明 -->
        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; font-weight: bold; margin-bottom: 5px;">商品名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            @error('name')
            <p style="color: red; font-size: 12px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; font-weight: bold; margin-bottom: 5px;">商品の説明</label>
            <textarea name="description" id="description" rows="5" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">{{ old('description') }}</textarea>
            @error('description')
            <p style="color: red; font-size: 12px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- 販売価格 -->
        <div style="margin-bottom: 20px;">
            <label for="price" style="display: block; font-weight: bold; margin-bottom: 5px;">販売価格</label>
            <input type="number" name="price" id="price" value="{{ old('price') }}" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            @error('price')
            <p style="color: red; font-size: 12px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- 出品ボタン -->
        <button type="submit" style="background-color: red; color: white; padding: 10px 20px; border: none; border-radius: 5px; width: 100%; cursor: pointer;">
            出品する
        </button>
    </form>
</div>
@endsection