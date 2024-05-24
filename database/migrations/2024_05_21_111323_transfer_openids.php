<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfer_openids', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('b_qd_openid', 120)->nullable()->comment('内部联系人（b侧）的企点openid');
            $table->string('c_qw_openid', 120)->nullable()->comment('外部联系人（c侧）的企微openid');
            $table->string('c_qd_openid', 120)->nullable()->comment('外部联系人（c侧）的企点openid');
            $table->softDeletes();
            $table->timestamps();
            $table->comment('企微openid换取企点openid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('transfer_openids');
    }
};
