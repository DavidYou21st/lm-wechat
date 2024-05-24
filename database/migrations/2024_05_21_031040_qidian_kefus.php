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
        Schema::create('qidian_kefus', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('account', 60)->unique()->comment('账号名');
            $table->string('openid', 120)->nullable()->comment('账号名对应的企点openid');
            $table->softDeletes();
            $table->timestamps();
            $table->comment('企点客服账号信息');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('qidian_kefus');
    }
};
