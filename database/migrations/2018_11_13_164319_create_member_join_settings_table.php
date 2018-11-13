<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberJoinSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_join_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('month_num')->default(0)->comment('开通月份');
            $table->decimal('price', 10, 2)->default(0)->comment('现价');
            $table->decimal('oprice', 10, 2)->default(0)->comment('原价');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_join_settings');
    }
}
