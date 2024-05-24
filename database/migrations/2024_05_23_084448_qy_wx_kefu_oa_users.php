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
        Schema::create('qy_wx_kefu_oa_users', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('open_kfid', 120)->nullable()->comment('客服账号ID');
            $table->string('user_id', 60)->nullable()->comment('用户ID');
            $table->string('official_account_openid', 120)->nullable()->comment('公众号openid');
            $table->string('openid', 120)->nullable()->comment('账号名对应的企微openid');
            $table->softDeletes();
            $table->timestamps();
            $table->comment('企业微信客服公众号绑定用户关系');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('qy_wx_kefu_oa_users');
    }
};
