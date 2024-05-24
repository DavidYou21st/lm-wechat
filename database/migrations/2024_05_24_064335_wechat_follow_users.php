<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wechat_follow_users', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('user_id', 120)->nullable()->comment('客户联系功能的成员userid');
            $table->string('openid', 120)->nullable()->comment('企业微信openid');
            $table->string('corp_id', 60)->nullable()->comment('企业ID');
            $table->string('kf_id', 120)->nullable()->comment('客服账号ID');
            $table->index('user_id');
            $table->softDeletes();
            $table->timestamps();
            $table->comment('企业微信配置了客户联系功能的成员列表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('wechat_follow_users');
    }
};
