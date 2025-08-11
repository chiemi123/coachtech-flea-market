<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastMessageAndCompletedToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('purchases', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('last_message_at');
            }
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
            if (Schema::hasColumn('purchases', 'last_message_at')) {
                $table->dropColumn('last_message_at');
            }
            if (Schema::hasColumn('purchases', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });
    }
}
