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
        Schema::create('wechat_kefus', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('corp_id', 60)->nullable()->comment('企业ID');
            $table->string('kf_id', 120)->nullable()->comment('客服账号ID');
            $table->string('name', 120)->nullable()->comment('客服名称');
            $table->string('openid', 120)->nullable()->comment('账号名对应的企业微信openid');
            $table->index('kf_id');
            $table->softDeletes();
            $table->timestamps();
            $table->comment('企业微信客服账号信息');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('wechat_kefus');
    }
};
