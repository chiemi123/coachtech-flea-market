<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('email'); // プロフィール画像
            $table->string('username')->nullable()->unique()->after('profile_image'); // ユーザー名
            $table->string('postal_code')->nullable()->after('username'); // 郵便番号
            $table->string('address')->nullable()->after('postal_code'); // 住所
            $table->string('building_name')->nullable()->after('address'); // 建物名
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'username', 'postal_code', 'address', 'building_name']);
        });
    }
}
