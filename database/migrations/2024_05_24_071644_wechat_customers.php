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
        Schema::create('wechat_customers', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('external_userid', 120)->nullable()->comment('微信客户的external_userid');
            $table->string('nickname', 120)->nullable()->comment('微信昵称');
            $table->string('unionid', 120)->nullable()->comment('unionid，需要绑定微信开发者账号才能获取到');
            $table->string('openid', 120)->nullable()->comment('openid');
            $table->string('corp_id', 60)->nullable()->comment('企业ID');
            $table->index(['external_userid', 'corp_id']);
            $table->softDeletes();
            $table->timestamps();
            $table->comment('企业微信客户');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('wechat_customers');
    }
};
