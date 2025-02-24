@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell/create.css') }}">
@endsection

@section('header-extra')

@endsection

@section('title', '商品出品')

@section('content')
<div class="container">
    <h1>商品の出品</h1>
    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像 -->
        <div class="image-upload">
            <label for="item_image" class="custom-file-label">画像を選択する</label>
            <input type="file" name="item_image" id="item_image" class="custom-file-input" accept="image/*">

            @error('item_image')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <h2 class="section-title">商品の詳細</h2>

        <div class="category-group">
            <p class="category-title">カテゴリー</p> <!-- ✅ `for="category"` の代わりに `p` で表記 -->

            <div class="category-options">
                @foreach ($categories as $category)
                <div class="category-item">
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
                </div>
                @endforeach
            </div>

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

        <!-- ブランド名 -->
        <div>
            <label for="brand">ブランド名</label>
            <input type="text" name="brand" id="brand" value="{{ old('brand') }}">
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
        <div class="price-input-wrapper">
            <label for="price" class="price-label">販売価格</label>
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