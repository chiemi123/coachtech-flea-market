<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 購入者のID
            $table->foreignId('item_id')->constrained()->cascadeOnDelete(); // 購入した商品ID
            $table->foreignId('address_id')->constrained()->cascadeOnDelete(); // 送付先住所ID
            $table->string('payment_method'); // 支払い方法（コンビニ払い / クレジットカード）
            $table->string('status')->default('pending'); // 購入ステータス
            $table->string('transaction_id')->nullable(); // Stripeの決済ID
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
        Schema::dropIfExists('purchases');
    }
}
