<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerfIndexesForPurchasesAndMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // purchases.last_message_at にインデックス
        Schema::table('purchases', function (Blueprint $table) {
            $table->index('last_message_at', 'purchases_last_message_at_idx');
        });

        // messages (purchase_id, created_at) に複合インデックス
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['purchase_id', 'created_at'], 'messages_purchase_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('purchases_last_message_at_idx');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_purchase_created_idx');
        });
    }
}
