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

        <!-- 商品画像のアップロード -->
        <div class="image-upload">
            <!-- ファイル選択のラベル -->
            <label for="item_image" class="custom-file-label">画像を選択する</label>

            <!-- ファイル選択ボタン（見えないようにする） -->
            <input type="file" name="item_image" id="item_image" class="custom-file-input" accept="image/*">

            <!-- 選択されたファイル名を表示 -->
            <span id="file-name" class="file-name">選択された画像はありません</span>

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
                        name="category_ids[]"
                        id="category-{{ $category->id }}"
                        value="{{ $category->id }}"
                        {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>

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
        <div class="product-condition">
            <label for="condition">商品の状態</label>
            <!-- カスタムセレクトボックス -->
            <div class="custom-select">
                <div class="selected-option">選択してください</div>
                <ul class="options-list">
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
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <h2 class="section-title">商品名と説明</h2>

        <!-- 商品名 -->
        <div class="product-name">
            <label for="name">商品名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- ブランド名 -->
        <div class="brand-name">
            <label for="brand">ブランド名</label>
            <input type="text" name="brand" id="brand" value="{{ old('brand') }}">
            @error('brand')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <!-- 商品の説明 -->
        <div class="product-description">
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
        <div class="submit-button-wrapper">
            <button type="submit">出品する</button>
        </div>
    </form>

    <script>
        document.getElementById('item_image').addEventListener('change', function() {
            document.getElementById('file-name').textContent = this.files.length ? this.files[0].name : "選択された画像はありません";
        });

        document.addEventListener("DOMContentLoaded", function() {
            const selectBox = document.querySelector(".selected-option");
            const optionsList = document.querySelector(".options-list");
            const options = document.querySelectorAll(".options-list li");
            const hiddenInput = document.getElementById("selected-condition");

            // 初期状態で選択済みの項目を反映
            options.forEach(option => {
                if (option.classList.contains("selected")) {
                    selectBox.textContent = option.textContent.trim();
                }
            });

            // セレクトボックスをクリックでリストを開閉
            selectBox.addEventListener("click", function() {
                optionsList.style.display = optionsList.style.display === "block" ? "none" : "block";
            });

            // 選択肢をクリックしたときの処理
            options.forEach(option => {
                option.addEventListener("click", function() {
                    // すべての選択肢から "selected" クラスを削除
                    options.forEach(opt => opt.classList.remove("selected"));

                    // 選択したものに "selected" クラスを追加（✓マークが表示される）
                    this.classList.add("selected");

                    // 選択したテキストを表示
                    selectBox.textContent = this.textContent.replace("✓", "").trim();

                    // 隠し input に値を設定（フォーム送信可能にする）
                    hiddenInput.value = this.dataset.value;

                    // 選択肢を閉じる
                    optionsList.style.display = "none";
                });
            });

            // 外部クリックで閉じる
            document.addEventListener("click", function(event) {
                if (!selectBox.contains(event.target) && !optionsList.contains(event.target)) {
                    optionsList.style.display = "none";
                }
            });
        });
    </script>

</div>
@endsection