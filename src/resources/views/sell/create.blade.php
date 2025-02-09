@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell/create.css') }}">
@endsection

@section('header-extra')
<li class="header-nav__item">
    <a class="header-nav__link" href="{{ route('sell.create') }}">出品</a>
</li>
@endsection

@section('title', '商品出品')

@section('content')
<div class="container">
    <h1>商品の出品</h1>
    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像 -->
        <div>
            <label for="image">商品画像</label>
            <input type="file" name="image" id="image">
            @error('image')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <h2 class="section-title">商品の詳細</h2>

        <div class="category-group">
            <label for="category">カテゴリー</label>
            @foreach ($categories as $category)
            <!-- チェックボックス -->
            <input
                type="checkbox"
                name="category_id[]"
                id="category-{{ $category->id }}"
                value="{{ $category->id }}"
                {{ is_array(old('category_id')) && in_array($category->id, old('category_id')) ? 'checked' : '' }}>

            <!-- ラベル -->
            <label for="category-{{ $category->id }}" class="category-label">
                {{ $category->name }}
            </label>
            @endforeach
            @error('category_id')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品の状態 -->
        <div>
            <label for="condition">商品の状態</label>
            <select name="condition_id" id="condition">
                <option value="" disabled selected>選択してください</option>
                @foreach ($conditions as $condition)
                <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                    {{ $condition->name }}
                </option>
                @endforeach
            </select>
            @error('condition_id')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <h2 class="section-title">商品名と説明</h2>

        <!-- 商品名 -->
        <div>
            <label for="name">商品名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品名 -->
        <div>
            <label for="brand">ブランド名</label>
            <input type="text" name="brand" id="name" value="{{ old('brand') }}">
            @error('brand')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品の説明 -->
        <div>
            <label for="description">商品の説明</label>
            <textarea name="description" id="description" rows="5">{{ old('description') }}</textarea>
            @error('description')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 販売価格 -->
        <div>
            <label for="price">販売価格</label>
            <input type="number" name="price" id="price" value="{{ old('price') }}">
            @error('price')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 出品ボタン -->
        <button type="submit">出品する</button>
    </form>
</div>
@endsection