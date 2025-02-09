<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 出品者（ユーザー削除時に商品も削除）
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete(); // ブランド（削除時はNULL）
            $table->foreignId('condition_id')->constrained()->cascadeOnDelete(); // 商品の状態（削除時は商品も削除）

            $table->string('name'); // 商品名
            $table->string('description', 255); // 説明（最大255文字）
            $table->unsignedInteger('price'); // 価格（マイナスはあり得ないので `unsignedInteger`）
            $table->string('item_image')->nullable(); // 商品画像（保存パス）

            $table->boolean('sold_out')->default(false); // 売却済みフラグ
            $table->unsignedInteger('likes_count')->default(0); // いいね数（キャッシュ）
            $table->unsignedInteger('comments_count')->default(0); // コメント数（キャッシュ）

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
