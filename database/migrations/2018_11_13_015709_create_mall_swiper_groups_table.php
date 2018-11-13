<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMallSwiperGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mall_swiper_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',20)->comment('轮播图名');
            $table->tinyInteger('display')->default(0)->comment('是否启用，1表启用0表关闭');
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
        Schema::dropIfExists('mall_swiper_groups');
    }
}
