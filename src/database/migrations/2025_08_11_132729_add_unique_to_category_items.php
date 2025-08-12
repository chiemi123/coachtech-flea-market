<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueToCategoryItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_items', function (Blueprint $table) {
            $table->unique(['item_id', 'category_id'], 'category_items_item_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_items', function (Blueprint $table) {
            $table->dropUnique('category_items_item_category_unique');
        });
    }
}
