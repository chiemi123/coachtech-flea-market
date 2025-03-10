@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell/create.css') }}">
@endsection

@section('header-extra')

@endsection

@section('title', '商品出品')

@section('content')
<div class="sell__container">
    <h1 class="sell__title">商品の出品</h1>
    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像のアップロード -->
        <div class="sell__image-upload">
            <label for="item_image" class="sell__image-label">画像を選択する</label>
            <input type="file" name="item_image" id="item_image" class="sell__image-input" accept="image/*">
            <span id="file-name" class="sell__image-file-name">選択された画像はありません</span>

            @error('item_image')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <h2 class="sell__section-title">商品の詳細</h2>

        <!-- カテゴリー選択 -->
        <div class="sell__category-group">
            <p class="sell__category-title">カテゴリー</p>
            <div class="sell__category-options">
                @foreach ($categories as $category)
                <div class="sell__category-item">
                    <input type="checkbox" name="category_ids[]" id="category-{{ $category->id }}" value="{{ $category->id }}"
                        {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                    <label for="category-{{ $category->id }}" class="sell__category-label">{{ $category->name }}</label>
                </div>
                @endforeach
            </div>

            @error('category_id')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品の状態 -->
        <div class="sell__condition">
            <label for="condition" class="sell__condition-label">商品の状態</label>
            <div class="sell__condition-select">
                <div class="sell__condition-selected">選択してください</div>
                <ul class="sell__condition-options">
                    @foreach ($conditions as $condition)
                    <li data-value="{{ $condition->id }}"
                        class="{{ old('condition_id') == $condition->id ? 'selected' : '' }}">
                        {{ $condition->name }}
                    </li>
                    @endforeach
                </ul>
                <input type="hidden" name="condition_id" id="selected-condition" value="{{ old('condition_id') }}">
            </div>

            @error('condition_id')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <h2 class="sell__section-title">商品名と説明</h2>

        <!-- 商品名 -->
        <div class="sell__product-name">
            <label for="name" class="sell__label">商品名</label>
            <input type="text" name="name" id="name" class="sell__input" value="{{ old('name') }}">
            @error('name')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- ブランド名 -->
        <div class="sell__brand-name">
            <label for="brand" class="sell__label">ブランド名</label>
            <input type="text" name="brand" id="brand" class="sell__input" value="{{ old('brand') }}">
            @error('brand')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品の説明 -->
        <div class="sell__description">
            <label for="description" class="sell__label">商品の説明</label>
            <textarea name="description" id="description" class="sell__textarea" rows="5">{{ old('description') }}</textarea>
            @error('description')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 販売価格 -->
        <div class="sell__price">
            <label for="price" class="sell__label">販売価格</label>
            <input type="number" name="price" id="price" class="sell__input" value="{{ old('price') }}">
            @error('price')
            <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 出品ボタン -->
        <div class="sell__submit">
            <button type="submit" class="sell__submit-button">出品する</button>
        </div>
    </form>

    <script>
        document.getElementById('item_image').addEventListener('change', function() {
            document.getElementById('file-name').textContent = this.files.length ? this.files[0].name : "選択された画像はありません";
        });

        document.addEventListener("DOMContentLoaded", function() {
            const selectBox = document.querySelector(".sell__condition-selected");
            const optionsList = document.querySelector(".sell__condition-options");
            const options = document.querySelectorAll(".sell__condition-options li");
            const hiddenInput = document.getElementById("selected-condition");

            options.forEach(option => {
                if (option.classList.contains("selected")) {
                    selectBox.textContent = option.textContent.trim();
                }
            });

            selectBox.addEventListener("click", function() {
                optionsList.style.display = optionsList.style.display === "block" ? "none" : "block";
            });

            options.forEach(option => {
                option.addEventListener("click", function() {
                    options.forEach(opt => opt.classList.remove("selected"));
                    this.classList.add("selected");
                    selectBox.textContent = this.textContent.trim();
                    hiddenInput.value = this.dataset.value;
                    optionsList.style.display = "none";
                });
            });

            document.addEventListener("click", function(event) {
                if (!selectBox.contains(event.target) && !optionsList.contains(event.target)) {
                    optionsList.style.display = "none";
                }
            });
        });
    </script>

</div>
@endsection