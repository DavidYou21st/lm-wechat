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
        Schema::create('qidian_customers', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('cust_id', 120)->unique()->comment('企点客户唯一凭证');
            $table->string('openid', 120)->nullable()->comment('账号名对应的企点openid');
            $table->json('qq')->nullable()->comment('客户QQ的openid');
            $table->tinyInteger('qq_official_account_openid')->default(0)->comment('有没有关注qq公众号,1:是，0：否');
            $table->json('wx_openid', 120)->nullable()->comment('企点客户的微信openid');
            $table->tinyInteger('wx_official_account_openid')->default(0)->comment('有没有关注微信公众号,1:是，0：否');
            $table->json('wx_social')->nullable()->comment('微信公众号appid，openid');
            $table->json('mini_social')->nullable()->comment('微信小程序appid，openid');
            $table->json('wecom')->nullable()->comment('企业微信cid');
            $table->json('wxkf')->nullable()->comment('微信客服cid');
            $table->json('visitor_id')->nullable()->comment('webim通路的cid');
            $table->string('wxaccount', 120)->nullable()->comment('微信');
            $table->json('wx_account_info')->nullable()->comment('微信信息');
            $table->softDeletes();
            $table->timestamps();
            $table->comment('企点客户信息');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('qidian_customers');
    }
};
