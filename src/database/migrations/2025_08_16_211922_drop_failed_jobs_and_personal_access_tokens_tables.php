<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropFailedJobsAndPersonalAccessTokensTables extends Migration
{
    public function up()
    {
        // 存在するときだけ安全に削除
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('personal_access_tokens');
    }

    public function down()
    {
        // いつでも復活できるように、元のスキーマを定義

        // failed_jobs
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // personal_access_tokens（Sanctum）
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable'); // tokenable_type + tokenable_id
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }
}
